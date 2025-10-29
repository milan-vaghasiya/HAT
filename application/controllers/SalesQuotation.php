<?php
class SalesQuotation extends MY_Controller{
    private $indexPage = 'sales_quotation/index';
    private $quoteForm = "sales_quotation/form";
    private $quotationForm = "sales_quotation/sales_quotation";
    private $confirmQuotation = "sales_quotation/confirm_quotation";
    private $followUp = "sales_quotation/followup";
	private $automotiveArray = ["2"=>"No","1"=>'Yes'];
    
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Quotation";
		$this->data['headData']->controller = "salesQuotation";
		$this->data['headData']->pageUrl = "salesQuotation";
    }

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->salesQuotation->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->ref_no = '';
            if(!empty($row->from_entry_type)):
               $refData = $this->salesQuotation->getSalesQuotation($row->ref_id);
               $row->ref_no = getPrefixNumber($refData->trans_prefix,$refData->trans_no);
            endif;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getSalesQuotationData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createQuotation($id){
        $this->data['from_entry_type'] = 1;
        $this->data['ref_id'] = $id;
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(2);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(2);
        $this->data['quotationData'] = $this->salesEnquiry->getSalesEnquiry($id);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->data['currencyData'] = $this->party->getCurrency();       
		$this->data['devCharge'] = $this->grnModel->getMasterOptions()->dev_charge;       
        $this->load->view($this->quoteForm,$this->data);
    }

    public function addSalesQuotation(){
        $this->data['from_entry_type'] = 0;
        $this->data['ref_id'] = "";
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(2);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(2);
		$this->data['unitData'] = $this->item->itemUnits();     
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->data['currencyData'] = $this->party->getCurrency();       
		$this->data['devCharge'] = $this->grnModel->getMasterOptions()->dev_charge;       
        $this->load->view($this->quoteForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        $data['currency'] = '';$data['inrrate'] = 0;
        if(empty($data['party_name'])):
            $errorMessage['party_id'] = "Customer Name is required.";
		else:
			if(!empty($data['party_id'])):
                $partyData = $this->party->getParty($data['party_id']); 
                if(floatval($partyData->inrrate) <= 0):
                    $errorMessage['party_id'] = "Currency not set.";
                else:
                    $data['currency'] = $partyData->currency;
                    $data['inrrate'] = $partyData->inrrate;
                endif;
            else:
                if(empty($data['currency'])):
                    
                    $data['currency'] = 'INR';
                    $data['inrrate'] = 1;
                else:
                    $currencyData = $this->party->getCurrencyRate($data['currency']); 
                    $data['inrrate'] = $currencyData->inrrate;
                endif;
            endif;
		endif;
        if(empty($data['quote_no']))
            $errorMessage['quote_no'] = 'Quotation No. is required.';
        if(empty($data['item_name'][0]))
            $errorMessage['item_name_error'] = 'Item Name is required.';
        if(empty($data['unit_id'][0]))
            $errorMessage['unit_id'] = 'Unit is required.';
        if(empty($data['price'][0]))
            $errorMessage['price'] = 'Price is required.';
        if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            $data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			
            $revision_date = "";
            if(!empty($data['doc_date'])):
                $revision_date = formatDate($data['doc_date'],'Y-m-d');
            else:
                $revision_date = formatDate($data['trans_date'],'Y-m-d');
            endif;
             
            $masterData = [ 
                'id' => $data['quote_id'],
                'from_entry_type' => $data['reference_entry_type'],
                'ref_id' => $data['reference_id'],
                'entry_type' => $data['form_entry_type'],
                'trans_prefix' => $data['quote_prefix'],
                'trans_no'=>$data['quote_no'], 
                'trans_date' => formatDate($data['trans_date'],'Y-m-d'), 
                'doc_date' => $revision_date, 
                'party_id' => $data['party_id'],
                'party_name' => $data['party_name'],
                'contact_person' => $data['contact_person'],
				'contact_no'=>$data['contact_no'],
				'contact_email'=>$data['contact_email'],
				'party_phone'=>$data['party_phone'],
				'party_email'=>$data['party_email'],
				'party_address'=>$data['party_address'],
				'party_pincode'=>$data['party_pincode'],
                'lr_no'=>$data['lr_no'],
				'quote_rev_no'=>$data['quote_rev_no'],
				'ref_by'=>$data['ref_by'],
				'sales_executive'=>$data['sales_executive'],
                'gst_type' => $data['gst_type'], 
				'gst_applicable' => $data['gst_applicable'], 
                'total_amount' => $data['amount_total'] + $data['disc_amt_total'],
				'taxable_amount' => $data['amount_total'],
				'gst_amount' => $data['igst_amt_total'],
				'freight_amount' => $data['freight_amt'],
				'igst_amount' => $data['igst_amt_total'], 
				'cgst_amount' => $data['cgst_amt_total'], 
				'sgst_amount' => $data['sgst_amt_total'], 
				'disc_amount' => $data['disc_amt_total'],
				'apply_round' => $data['apply_round'], 
				'round_off_amount' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'terms_conditions' => $data['terms_conditions'],
				'challan_no' => $data['challan_no'],
				'net_weight' => $data['dev_charge'],
                'remark' => $data['remark'],
                'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
                'created_by' => $this->session->userdata('loginId')
            ]; 
            $itemData = [
                'id' => $data['trans_id'],
                'from_entry_type' => $data['from_entry_type'],
                'ref_id' => $data['ref_id'],
                'item_id' => $data['item_id'],
                'item_name' => $data['item_name'],
                'item_type' => $data['item_type'],
                'item_code' => $data['item_code'],
                'item_desc' => $data['item_desc'],
                'hsn_code' => $data['hsn_code'],
                'qty' => $data['qty'],
                'unit_id' => $data['unit_id'],
                'unit_name' => $data['unit_name'],
                'price' => $data['price'],
                'item_remark' => $data['item_remark'],
                'drg_rev_no' => $data['drg_rev_no'],
                'rev_no' => $data['rev_no'],
                'batch_no' => $data['batch_no'],
                'grn_data' => $data['grn_data'],
                'amount' => $data['amount'] + $data['disc_amt'],
				'taxable_amount' => $data['amount'],
                'gst_per' => $data['gst_per'],
                'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'net_amount' => $data['net_amount'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->printJson($this->salesQuotation->save($masterData,$itemData,$data['is_revision']));
        endif;
    }

    public function edit($id){
        $this->data['from_entry_type'] = 0;
        $this->data['ref_id'] = "";
		$this->data['unitData'] = $this->item->itemUnits();     
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $this->salesQuotation->getSalesQuotation($id);
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->data['currencyData'] = $this->party->getCurrency();       
		$this->data['devCharge'] = $this->grnModel->getMasterOptions()->dev_charge;       
        $this->load->view($this->quoteForm,$this->data);
    }

    public function reviseQuotation($id){
        $this->data['from_entry_type'] = 0;
        $this->data['ref_id'] = "";
		$this->data['unitData'] = $this->item->itemUnits();     
		$this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $this->salesQuotation->getSalesQuotation($id);
        $this->data['dataRow']->quote_rev_no =  (intVal($this->data['dataRow']->quote_rev_no) + 1);
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->data['currencyData'] = $this->party->getCurrency();
		$this->data['is_revision'] =  1;
		$this->data['devCharge'] = $this->grnModel->getMasterOptions()->dev_charge;  
        $this->load->view($this->quoteForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesQuotation->deleteQuotation($id));
		endif;
    }

	public function itemSearch(){
		$this->printJson($this->salesQuotation->itemSearch());
	}

    public function getEnquiryData(){
        $enq_id = $this->input->post('enq_id');
        $this->data['dataRow'] = $this->salesQuotation->getSalesEnquiryById($enq_id);
        $this->data['enquiryItems'] = $this->salesQuotation->getEnquiryData($enq_id);
        $this->data['quote_prefix'] = 'SQ/'.$this->shortYear.'/';
        $this->data['quote_no'] = $this->salesQuotation->nextQuoteNo();
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(2);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(2);
        $this->load->view($this->quotationForm,$this->data);
    }

    public function saveQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_name'][0])):
            $errorMessage['item_name_error'] = "Please select Items.";
        else:
            foreach($data['qty'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['qty'.$data['trans_id'][$key]] = "Qty is required.";
                endif;
            endforeach;

            foreach($data['price'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['price'.$data['trans_id'][$key]] = "Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesQuotation->saveQuotation($data));
        endif;
    }

    public function getQuotationItems(){
        $quote_id = $this->input->post('quote_id');
        $this->data['quotationItems'] = $this->salesQuotation->getQuotationItems($quote_id);
        $this->load->view($this->confirmQuotation,$this->data);
    }

    public function saveConfirmQuotation(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['trans_id'][0])):
            $errorMessage['item_name_error'] = "Please select Items.";
        else:
            foreach($data['confirm_price'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['confirm_price'.$data['trans_id'][$key]] = "Confirm Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['confirm_by'] = $this->session->userdata('loginId');
            $this->printJson($this->salesQuotation->saveConfirmQuotation($data));
        endif;
    }

	function printQuotation($id){
		$this->data['sqData'] = $this->salesQuotation->getSalesQuotationForPrint($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
		
		$qrn = str_pad($this->data['sqData']->quote_rev_no, 2, '0', STR_PAD_LEFT);
        $this->data['qrn'] = 'Rev No. '.$qrn.' / '.formatDate($this->data['sqData']->doc_date);
		
        $sqData = $this->data['sqData'];
        if($sqData->entry_type == 2){
            $this->data['sqData']->trans_no = getPrefixNumber($sqData->trans_prefix,$sqData->trans_no);
            $this->data['sqData']->refNo = getPrefixNumber($sqData->ref_prefix,$sqData->ref_no);
            $transf = getPrefixNumber($sqData->trans_prefix,$sqData->trans_no).'-'.formatDate($sqData->trans_date);
        }else{
            $refData = $this->salesQuotation->getSalesQuotationForPrint($sqData->ref_id);
            $this->data['sqData']->trans_no = getPrefixNumber($sqData->ref_prefix,$sqData->ref_no);
            $this->data['sqData']->refNo = getPrefixNumber($refData->ref_prefix,$refData->ref_no);
            $transf = getPrefixNumber($sqData->ref_prefix,$sqData->ref_no).'-'.formatDate($sqData->ref_date);
        }	
		$pdfData = $this->load->view('sales_quotation/print_quotation',$this->data,true);

		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['companyData']->company_name.'</th>
						</tr>
						<tr>
							<td colspan="3" height="70"></td>
						</tr>
						<tr>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Authorised By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;">Qtn. No. & Date : '.$transf.'</td>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		//$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,45,45,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	public function getFollowUp(){
        $id = $this->input->post('id');
        $this->data['id'] = $id;
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['dataRow'] = $this->salesQuotation->getSalesQuotation($id);
        $this->load->view($this->followUp,$this->data);
    }

    public function saveFollowUp(){
        $data  = $this->input->post();
        $errorMessage = array();
        if(empty($data['id']))
            $errorMessage['id'] = "Trans Id is required.";
        if(empty($data['trans_date'][0]))
            $errorMessage['generalError'] = 'Follow Up is required.';
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            $data['extra_fields'] = "";$termsArray = array();
            if(isset($data['trans_date']) && !empty($data['trans_date'])):
                foreach($data['trans_date'] as $key=>$value):
                    $termsArray[] = [
                        'trans_date' => $value,
                        'sales_executive' => $data['sales_executive'][$key],
                        'sales_executiveName' => $data['sales_executiveName'][$key],
                        'f_note' => $data['f_note'][$key]
                    ];
                endforeach;
                $sdata['extra_fields'] = json_encode($termsArray);
            endif;
            $sdata['id'] = $data['id'];
            $sdata['trans_status'] = $data['trans_status'];

            $this->printJson($this->salesQuotation->saveFollowUp($sdata));
        endif;
    }

    public function approveQuotation(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesQuotation->approveQuotation($data));
		endif;
	}
	
	function viewRevisionQuotation(){
        $id=$this->input->post('id');
		$this->data['sqData'] = $this->salesQuotation->getSalesQuotationList($id);
      
		$html="";	
        foreach($this->data['sqData'] as $row):
            $html.='<tr>
                <td><a href="'.base_url($this->data['headData']->controller.'/printQuotation/'.$row->id).'" target="_blank"><i class="fa fa-print"></i></a></td>
                <!-- <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td> -->
                <td>'.str_pad($row->quote_rev_no, 2, '0', STR_PAD_LEFT).'</td>
                <td>'.formatDate($row->doc_date).'</td>
            </tr>';
        endforeach;
        $this->printJson($html);
	}
	    //Created By Karmi @26/03/2022
    public function getPartyOrders(){
		$this->printJson($this->salesQuotation->getPartyOrders($this->input->post('party_id')));
	}

}
?>