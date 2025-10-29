<?php
class PurchaseEnquiry extends MY_Controller{
    private $indexPage = 'purchase_enquiry/index';
    private $enquiryForm = "purchase_enquiry/form";
    private $confirmForm = "purchase_enquiry/enquiry_confirm";
    
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Enquiries";
		$this->data['headData']->controller = "purchaseEnquiry";
		$this->data['headData']->pageUrl = "purchaseEnquiry";
    }

    public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->purchaseEnquiry->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($row->confirm_status == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1">Approved</span>';
			endif;	
            $row->controller = "purchaseEnquiry";
            $sendData[] = getPurchaseEnquiryData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEnquiry(){
        $year = str_replace("-","",$this->shortYear);
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['enqPrefix'] = "ENQ".$year."/";
        $this->data['nextEnqNo'] = $this->purchaseEnquiry->nextEnqNo();
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['supplier_id']))
            $errorMessage['supplier_id'] = "Supplier Name is required.";
        if(empty($data['enq_no']))
            $errorMessage['enq_no'] = 'Enquiry No. is required.';
        if(empty($data['item_name'][0]))
            $errorMessage['item_name'] = 'Item Detail is required.';
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = 'Unit is required.';

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:	
            
            $masterData = [ 
                'id' => $data['enq_id'],
                'enq_prefix' => $data['enq_prefix'],
                'enq_no'=>$data['enq_no'], 
                'enq_date' => date('Y-m-d',strtotime($data['enq_date'])), 
                'supplier_id' => $data['supplier_id'],
                'supplier_name' => $data['supplier_name'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId'),
                'req_id' => $data['req_id']
            ];
                            
            $itemData = [
                'id' => $data['trans_id'],
                'item_name' => $data['item_name'],
                'item_type' => $data['item_type'],
                'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
                'unit_id' => $data['unit_id'],
                'qty' => $data['qty'],
                'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];
            $this->printJson($this->purchaseEnquiry->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['dataRow'] = $this->purchaseEnquiry->getEnquiry($id);
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->enquiryForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->deleteEnquiry($id));
		endif;
    }

    public function getEnquiryData(){
        $enq_id = $this->input->post('enq_id');
        $this->data['enquiryItems'] = $this->purchaseEnquiry->getEnquiryData($enq_id);
        $this->load->view($this->confirmForm,$this->data);
    }

    public function enquiryConfirmed(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_name'][0])):
            $errorMessage['item_name_error'] = "Please select Item.";
        else:
            foreach($data['qty'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['qty'.$data['trans_id'][$key]] = "Qty is required.";
                endif;
            endforeach;

            foreach($data['rate'] as $key=>$value):
                if(empty($value)):
                    $errorMessage['rate'.$data['trans_id'][$key]] = "Price is required.";
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->purchaseEnquiry->enquiryConfirmed($data));
        endif;
    }

    public function closeEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->closeEnquiry($id));
		endif;
    }

    public function reopenEnquiry(){
        $id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseEnquiry->reopenEnquiry($id));
		endif;
    }

	public function itemSearch(){
		$this->printJson($this->purchaseEnquiry->itemSearch());
	}
	
    /* NYN */
    public function addEnqFromRequest($id){
		$this->data['req_id'] = $id;
        $year = (date('m') > 3)?date('y').(date('y') +1):(date('y')-1).date('y');
        $this->data['partyData'] = $this->party->getPartyList();
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['enqPrefix'] = "ENQ".$year."/";
        $this->data['nextEnqNo'] = $this->purchaseEnquiry->nextEnqNo();
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->data['reqItem'] = $this->purchaseRequest->getPurchaseReqForEnq($id);
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->enquiryForm,$this->data);
    }
	
	function printQuotation($id){
		$this->data['enqData'] = $this->purchaseEnquiry->getPurchaseEnqForPrint($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());
				
		$pdfData = $this->load->view('purchase_enquiry/print_enquiry',$this->data,true);
				
		$enqData = $this->data['enqData'];
		
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
							<td style="width:25%;">Qtn. No. & Date : '.getPrefixNumber($enqData->enq_prefix,$enqData->enq_no).'-'.formatDate($enqData->enq_date).'</td>
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
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,41,45,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>