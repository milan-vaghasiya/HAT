<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class SalesInvoice extends MY_Controller{	
	private $indexPage = "sales_invoice/index";
	private $itemwiseInvoice = "sales_invoice/itemwise_invoice";
    private $invoiceForm = "sales_invoice/form";
	private $tc_index = "sales_invoice/tc_index";
	private $packingSlipForm = "sales_invoice/packing_slip_form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.1,"val"=>'0.1%'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){ 
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Invoice";
		$this->data['headData']->controller = "salesInvoice";
		$this->data['headData']->pageUrl = "salesInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'LISTING';
        $result = $this->salesInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
			$existPackingSlipData = $this->salesInvoice->getPackingSlipItems($row->id);

            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'BILLWISE';
			$row->listType = 'LISTING';
			$row->is_available_packing_slip = (!empty($existPackingSlipData) ? true : false);
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function itemwiseInvoice(){
		//$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->itemwiseInvoice,$this->data);
    }

	public function getItemWiseDTRows($sales_type=""){
		$data = $this->input->post(); $data['sales_type'] = $sales_type;$data['list_type'] = 'LISTING';
        $result = $this->salesInvoice->getItemWiseDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            /*if(!empty($row->from_entry_type)):
               $refData = $this->salesInvoice->getInvoice($row->ref_id);
               $row->po_no = $refData->doc_no;
            endif;*/
            $row->controller = $this->data['headData']->controller;
			$row->tp = 'ITEMWISE';
			$row->listType = 'LISTING';
            $sendData[] = getSalesInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	//  Create By : JP @24-05-2022 11:25 AM
	public function getInvoiceSummary($jsonData=''){
		if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        //print_r($postData);exit;
        $result = $this->salesInvoice->getInvoiceSummary($postData);
		$reportTitle = 'BILL WISE SALES REGISTER';
		$report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));

		$companyData = $this->salesInvoice->getCompanyInfo();
		$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo = base_url('assets/images/' . $logoFile);
		$letter_head = base_url('assets/images/letterhead_top.png');
		$InvData = $this->salesInvoice->getSalesInvDataBillWise($postData); //print_r($InvData);exit;

		$tbody="";$thead=""; $i=1;

		$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
		$thead .= '<tr>
						<th>#</th>
						<th>Invoice No.</th>
						<th>Invoice Date</th>
						<th>Customer Name</th>
						<th>Taxable Amount</th>
						<th>Net Amount</th>							
				</tr>';
		foreach($InvData as $row):
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->trans_number.'</td>
				<td>'.date("d-m-Y",strtotime($row->trans_date)).'</td>
				<td>'.$row->party_name.'</td>
				<td class="text-right">'.$row->taxable_amount.'</td>
				<td class="text-right">'.$row->net_amount.'</td>
				</tr>';
		endforeach;
			
		$pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">'.$thead.'</thead>
							<tbody id="receivableData">'.$tbody.'</tbody>
						</table>';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">
						    '.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'
						</td>
					</tr>
				</table>';
				
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';
		
		$custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId,$postData);
        if(!empty($customerList)){
            foreach($customerList as $row){
                $select = (!empty($postData['party_id']) AND $postData['party_id'] == $row->id) ? 'selected' : '';
	        	$custOption .= '<option value="'.$row->id.'" '.$select.'>'.$row->party_name.' | '.$row->city_name.'</option>';
            }
        }
		$custOption .= '</select>';
		if(!empty($postData['pdf']))
		{
    		$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
    		$pdfFileName = $filePath.'/SalesRegister.pdf';
    		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
    		$mpdf->WriteHTML($stylesheet, 1);
    		$mpdf->SetDisplayMode('fullpage');
    		$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
    		$mpdf->showWatermarkImage = true;
    		$mpdf->SetTitle($reportTitle);
    		$mpdf->SetHTMLHeader($htmlHeader);
    		$mpdf->SetHTMLFooter($htmlFooter);
    		$mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
    		$mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else{$this->printJson(['taxable_amount'=>$result->taxable_amount,'gst_amount'=>$result->gst_amount,'net_amount'=>$result->net_amount,'custOption'=>$custOption]);}

		
    }

	//  Create By : Karmi @26-05-2022
	public function getInvoiceSummarybillWise($jsonData=''){
		if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        $result = $this->salesInvoice->getInvoiceSummary($postData);
		$reportTitle = 'ITEM WISE SALES REGISTER';
		$report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));

		$companyData = $this->salesInvoice->getCompanyInfo();
		$logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo = base_url('assets/images/' . $logoFile);
		$letter_head = base_url('assets/images/letterhead_top.png');
		$InvData = $this->salesInvoice->getSalesInvDataItemWise($postData); //print_r($InvData);exit;

		$tbody="";$thead=""; $i=1;

		$thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
		$thead .= '<tr>
					<th class="text-center">#</th>
					<th class="text-center">Invoice No.</th>
					<th class="text-center">Invoice Date</th>
					<th class="text-left">Customer Name</th>
					<th class="text-right">Item Name</th>
					<th class="text-right">Qty</th>
					<th class="text-right">Rate</th>
					<th class="text-right">Discount</th>
					<th class="text-right">Amount</th>
				</tr>';
		foreach($InvData as $row):
			$tbody .= '<tr>
				<td>'.$i++.'</td>
				<td>'.$row->trans_number.'</td>
				<td>'.date("d-m-Y",strtotime($row->trans_date)).'</td>
				<td>'.$row->party_name.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.$row->qty.'</td>
				<td>'.$row->price.'</td>
				<td  class="text-right">'.$row->disc_amount.'</td>
				<td  class="text-right">'.$row->amount.'</td>
				</tr>';
		endforeach;
			
		$pdfData = '<table id="commanTable" class="table table-bordered">
							<thead class="thead-info" id="theadData">'.$thead.'</thead>
							<tbody id="receivableData">'.$tbody.'</tbody>
						</table>';
		$htmlHeader = '<img src="' . $letter_head . '">';
		$htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%"></td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%"></td>
					</tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
					<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
				</table>
				<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:10px;">
					<tr>
						<td class="org_title text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
						<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$reportTitle.'</td>
						<td class="org_title text-uppercase text-right" style="font-size:1rem;width:30%">'.$result->taxable_amount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$result->net_amount.'</td>
					</tr>
				</table>';
				
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
					<tr>
						<td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
						<td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
					</tr>
				</table>';
		
		/*** Custome Select Box ***/
		$custOption = '<select name="party_id" id="party_id" class="form-control single-select cstfilter" style="width:35%;"><option value="">All Customer</option>';
        $customerList = $this->salesInvoice->getCustomerListOnlySales($this->loginId,$postData);
        if(!empty($customerList)){
            foreach($customerList as $row){
                $select = (!empty($postData['party_id']) AND $postData['party_id'] == $row->id) ? 'selected' : '';
	        	$custOption .= '<option value="'.$row->id.'" '.$select.'>'.$row->party_name.' | '.$row->city_name.'</option>';
            }
        }
		$custOption .= '</select>';
		
		if(!empty($postData['pdf']))
		{
    		$mpdf = new \Mpdf\Mpdf();
    		$filePath = realpath(APPPATH . '../assets/uploads/');
    		$pdfFileName = $filePath.'/SalesRegister.pdf';
    		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
    		$mpdf->WriteHTML($stylesheet, 1);
    		$mpdf->SetDisplayMode('fullpage');
    		$mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
    		$mpdf->showWatermarkImage = true;
    		$mpdf->SetTitle($reportTitle);
    		$mpdf->SetHTMLHeader($htmlHeader);
    		$mpdf->SetHTMLFooter($htmlFooter);
    		$mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
    		$mpdf->WriteHTML($pdfData);
    		
    		ob_clean();
    		$mpdf->Output($pdfFileName, 'I');
		}
		else{$this->printJson(['taxable_amount'=>$result->taxable_amount,'net_amount'=>$result->net_amount,'custOption'=>$custOption]);}

		
    }

	public function createInvoice(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$soData = $this->salesOrder->getOrderByRefid($data['ref_id']);
		$soTransNo= ''; $challanNo= ''; $i=1;
		foreach($soData as $row):
			if($data['from_entry_type'] == 5){
				if(!empty($row->ref_no)){
					if($i==1){$soTransNo .= getPrefixNumber($row->ref_prefix,$row->ref_no); }else{ $soTransNo .= ', '.getPrefixNumber($row->ref_prefix,$row->ref_no); }}
				if($i==1){$challanNo .= getPrefixNumber($row->trans_prefix,$row->trans_no); }else{ $challanNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no); }
			}else{
				if($i==1){ $soTransNo .= getPrefixNumber($row->trans_prefix,$row->trans_no); }else{ $soTransNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no); }
			} $i++;
		endforeach; 
		if(!empty($data['inv_id'])):
			$invData = $this->salesInvoice->getInvoice($data['inv_id']);unset($invData->itemData);
			$this->data['invoiceData'] = $invData;
		endif;
		$this->data['soTransNo'] = $soTransNo;
		$this->data['challanNo'] = $challanNo;
		$this->data['invMaster'] = $invMaster;
		$this->data['invItems'] = ($data['from_entry_type'] == 5)?$this->challan->getChallanItems($data['ref_id']):$this->salesOrder->getOrderItems($data['ref_id']);
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function createInvoiceOnCustomInv(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$customInvoiceData = $this->customInvoice->getCustomInvoiceData($data['ref_id'][0]);
		$invNo = explode("/",$customInvoiceData->doc_no)[1];
		$gst_applicable = ($customInvoiceData->export_type == "(Supply Meant For Export With Payment Of IGST)")?1:0;
		$sp_acc_id = ($customInvoiceData->export_type == "(Supply Meant For Export With Payment Of IGST)")?532:533;
		$this->data['entry_type'] = 8;
		$this->data['sales_type'] = 2;
		$this->data['gst_type']  = 2;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$this->data['invMaster'] = $invMaster;
		$this->data['invItems'] = $this->customInvoice->getCustomInvoiceItems($data['ref_id']);
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(8);
        $this->data['nextTransNo'] = $invNo;//$this->transModel->nextTransNo(8);
		$this->data['trans_date'] = $customInvoiceData->doc_date;
		$this->data['gst_applicable'] = $gst_applicable;
		$this->data['sp_acc_id'] = $sp_acc_id;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
	}

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->invoiceForm,$this->data);
    }

	public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

	public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
	
	public function save(){
		$data = $this->input->post();
		//print_r($data);exit;
		$errorMessage = array();
		if($data['sales_type'] == 2 && empty($data['inrrate']))
			$errorMessage['inrrate'] = "INR Rate is required.";
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = (!empty($data['currency']))?$data['currency']:$partyData->currency;
				$data['inrrate'] = (!empty($data['inrrate']))?$data['inrrate']:$partyData->inrrate;
			endif;
			
			$data['credit_period'] = $partyData->credit_days;
		endif;
		if(empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Sales A/c. is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
		if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				elseif($data['stock_eff'][$key] == 1):
					/* $cStock = $this->store->getItemCurrentStock($value,$this->RTD_STORE->id);
					$currentStock = (!empty($cStock)) ? $cStock->qty : 0;
					$old_qty = 0;
					if(!empty($data['trans_id'][$key])):
						$transData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
						if(!empty($transData)){$old_qty = $transData->qty;}
					endif;
					if(($currentStock + $old_qty) < $data['qty'][$key]):
						$errorMessage["qty".$i] = "Stock not available.";
					endif;

					if(empty($data['batch_no'][$key])):
						$errorMessage['batch_no'.$i] = "Batch Details is required.";
					endif; */
					$qty_error=Array();
					foreach(explode(',',$data['location_id'][$key]) as $lkey=>$lid){
						$stockQ = Array();
						$stockQ['item_id'] = $value;$stockQ['location_id'] = $lid;$stockQ['batch_no'] = explode(',',$data['batch_no'][$key])[$lkey]; $stockQ['tc_no'] = explode(',',$data['tc_no'][$key])[$lkey]; $stockQ['ref_batch'] = explode(',',$data['ref_batch'][$key])[$lkey];
						$stockData = $this->store->getItemStockGeneral($stockQ);
						$packing_qty = (!empty($stockData)) ? $stockData->qty : 0;
						$old_qty = 0;
						if(!empty($data['trans_id'][$key])):
							$oldCHData = $this->salesInvoice->salesTransRow($data['trans_id'][$key]);
							$oldBatches = explode(',',$oldCHData->batch_no);$oldLocations = explode(',',$oldCHData->location_id);
							if(in_array($stockQ['batch_no'],$oldBatches)){
								$batchQtyKey = array_search($stockQ['batch_no'],$oldBatches);
								$old_qty = explode(',',$oldCHData->batch_qty)[$batchQtyKey];
							}
						endif;

						if(($packing_qty + $old_qty) < explode(',',$data['batch_qty'][$key])[$lkey]):
							$qty_error[]= $stockQ['batch_no'];
						endif;
					}
					if(!empty($qty_error)){$errorMessage["qty".$i] = "Stock not available. Batch No. = ".implode(', ',$qty_error);}
		
					if(empty($data['batch_no'][$key])):
						$errorMessage['batch'.$i] = "Batch Details is required.";
					endif;
				endif;
				$i++;
			endforeach;
		endif;
		
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

			$gstAmount = 0;
			if($data['gst_type'] == 1):
				if(isset($data['cgst_amount'])):
					$gstAmount = $data['cgst_amount'] + $data['sgst_amount'];
				endif;	
			elseif($data['gst_type'] == 2):
				if(isset($data['igst_amount'])):
					$gstAmount = $data['igst_amount'];
				endif;
			endif;
			
			$masterData = [ 
				'id' => $data['sales_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_number' => $data['inv_prefix'].$data['inv_no'],
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'credit_period' => $data['credit_period'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sp_acc_id' => $data['sp_acc_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gstin' => $data['gstin'],
				'gst_applicable' => $data['gst_applicable'],
				'gst_type' => $data['gst_type'],
				'sales_type' => $data['sales_type'], 
				'challan_no' => $data['challan_no'], 
				'doc_no'=>$data['so_no'],
				'doc_date'=>date('Y-m-d',strtotime($data['inv_date'])),
				'gross_weight' => $data['gross_weight'],
				'total_packet' => $data['total_packet'],
				'eway_bill_no' => $data['eway_bill_no'],
				'lr_no' => $data['lrno'],
				'transport_name' => $data['transport'],
				'shipping_address' => $data['supply_place'],
				'total_amount' => array_sum($data['amount']) + array_sum($data['disc_amt']),
				'taxable_amount' => $data['taxable_amount'],
				'gst_amount' => $gstAmount,
				'igst_acc_id' => (isset($data['igst_acc_id']))?$data['igst_acc_id']:0,
				'igst_per' => (isset($data['igst_per']))?$data['igst_per']:0,
				'igst_amount' => (isset($data['igst_amount']))?$data['igst_amount']:0,
				'sgst_acc_id' => (isset($data['sgst_acc_id']))?$data['sgst_acc_id']:0,
				'sgst_per' => (isset($data['sgst_per']))?$data['sgst_per']:0,
				'sgst_amount' => (isset($data['sgst_amount']))?$data['sgst_amount']:0,
				'cgst_acc_id' => (isset($data['cgst_acc_id']))?$data['cgst_acc_id']:0,
				'cgst_per' => (isset($data['cgst_per']))?$data['cgst_per']:0,
				'cgst_amount' => (isset($data['cgst_amount']))?$data['cgst_amount']:0,
				'cess_acc_id' => (isset($data['cess_acc_id']))?$data['cess_acc_id']:0,
				'cess_per' => (isset($data['cess_per']))?$data['cess_per']:0,
				'cess_amount' => (isset($data['cess_amount']))?$data['cess_amount']:0,
				'cess_qty_acc_id' => (isset($data['cess_qty_acc_id']))?$data['cess_qty_acc_id']:0,
				'cess_qty' => (isset($data['cess_qty']))?$data['cess_qty']:0,
				'cess_qty_amount' => (isset($data['cess_qty_amount']))?$data['cess_qty_amount']:0,
				'tcs_acc_id' => (isset($data['tcs_acc_id']))?$data['tcs_acc_id']:0,
				'tcs_per' => (isset($data['tcs_per']))?$data['tcs_per']:0,
				'tcs_amount' => (isset($data['tcs_amount']))?$data['tcs_amount']:0,
				'tds_acc_id' => (isset($data['tds_acc_id']))?$data['tds_acc_id']:0,
				'tds_per' => (isset($data['tds_per']))?$data['tds_per']:0,
				'tds_amount' => (isset($data['tds_amount']))?$data['tds_amount']:0,
				'disc_amount' => array_sum($data['disc_amt']),
				'apply_round' => $data['apply_round'], 
				'round_off_acc_id'  => (isset($data['roff_acc_id']))?$data['roff_acc_id']:0,
				'round_off_amount' => (isset($data['roff_amount']))?$data['roff_amount']:0, 
				'net_amount' => $data['net_inv_amount'],
				'terms_conditions' => $data['terms_conditions'],
                'remark' => $data['remark'],
                'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
				'ledger_eff' => 1,
				'created_by' => $this->session->userdata('loginId')
			];

			$transExp = getExpArrayMap($data);
			$expAmount = $transExp['exp_amount'];
			$expenseData = array();
            if($expAmount > 0):
				unset($transExp['exp_amount']);    
				$expenseData = $transExp;
			endif;

			$accType = getSystemCode($data['entry_type'],false);
            if(!empty($accType)):
				$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
                $masterData['vou_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
            else:
                $masterData['vou_acc_id'] = 0;
            endif;
			
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'so_ref_id' => $data['so_ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'tc_no' => $data['tc_no'],
				'ref_batch' => $data['ref_batch'],
				'batch_qty' => $data['batch_qty'],
				'packing_trans_id' => $data['packing_trans_id'],
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'org_price' => $data['org_price'],
				'amount' => $data['amount'],
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
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount']
			];

			$this->printJson($this->salesInvoice->save($masterData,$itemData,$expenseData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->salesInvoice->getInvoice($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemLists("1,10");
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
		$this->data['invMaster'] = $this->party->getParty($this->data['invoiceData']->party_id);  
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'SA'"]); 
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'","'ED'","'EI'","'ID'","'II'"]);
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesInvoice->deleteInv($id));
		endif;
	}

	public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->challan->batchWiseItemStock($data);
        $this->printJson($result);
	}

	public function getInvoiceNo(){
		$type = $this->input->post('sales_type');
		if($type == "1"):
			$trans_prefix = $this->transModel->getTransPrefix(6);
        	$nextTransNo = $this->transModel->nextTransNo(6);
			$entry_type = 6;
		elseif($type == "2"):
			$trans_prefix = $this->transModel->getTransPrefix(8);
        	$nextTransNo = $this->transModel->nextTransNo(8);
			$entry_type = 8;
		elseif($type == "3"):
			$trans_prefix = $this->transModel->getTransPrefix(7);
        	$nextTransNo = $this->transModel->nextTransNo(7);
			$entry_type = 7;
		endif;

		$this->printJson(['status'=>1,'trans_prefix'=>$trans_prefix,'nextTransNo'=>$nextTransNo,'entry_type'=>$entry_type]);
	}

	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}
	
	public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->salesInvoice->getInvoice($sales_id);
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');
		
		$gstHCol='';$gstCol='';$blankTD='';$bottomCols=2;$GSTAMT=$salesData->igst_amount;
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">Order No</th>
						<th style="width:10%;">Batch No</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Material</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate<br><small>('.$partyData->currency.')</small></th>
						<th style="width:11%;">Amount<br><small>('.$partyData->currency.')</small></th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		
		$blankLines=10;if(!empty($header_footer)){$blankLines=10;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;		
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);
			$blankLines=12 - count($tc);if(!empty($header_footer)){$blankLines=12 - count($tc);}
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>
										<!--<img src="'.$auth_sign.'" style="width:120px;">-->
									</th>
							</tr>';
				else:
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="font-size:12px;">: '.$trms->condition.'</td>
							</tr>';
				endif;$t++;
			endforeach;
		}
		else
		{
			$tc = array();
			$terms .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		
		$terms .= '</table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();$totalPage = 0;
		$totalItems = count($salesData->itemData);
		
		$lpr = $blankLines ;$pr1 = $blankLines + 6 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;
			$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
			$tempData = $this->salesInvoice->salesTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				$batchNo=''; $materialGrade=''; $orderNo='';
				foreach ($tempData as $row)
				{
					if($row->entry_type == 6)
					{
						if($row->from_entry_type == 0)
						{
							$batchNo = $row->batch_no;
							$materialGrade = $row->material_grade;
						}
						elseif($row->from_entry_type == 4)
						{
							$batchNo = $row->batch_no;
							$soData = $this->salesInvoice->getSalesOrderMasterData($row->ref_id);
							$materialGrade = (!empty($soData->material_grade)) ? $soData->material_grade : '';
							$orderNo = (!empty($soData->doc_no)) ? $soData->doc_no.'<br>'.formatDate($soData->trans_date) : '';
						}
						elseif($row->from_entry_type == 5)
						{
							$dcData = $this->salesInvoice->getDeliveryChallanData($row->ref_id);
							$batchNo = $dcData->batch_no;
							$materialGrade = $dcData->material_grade;
							$orderNo = $dcData->doc_no.'<br>'.formatDate($dcData->trans_date);
						}
					}
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.(!empty($orderNo) ? $orderNo : '').'</td>';
						$pageItems.='<td class="text-center">'.(!empty($batchNo) ? $batchNo : '').'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.(!empty($materialGrade) ? $materialGrade : '').'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount;$subTotal += $row->amount;$i++;
				}
			}
			if($x==$totalPage)
			{
				$pageData[$x]= '';
				$lastPageItems = $pageItems;
			}
			else
			{
				/*$pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="5">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>';*/
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}
		$taxableAmt= $subTotal + $salesData->freight_amount;
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->sgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';

		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;
			}
		}
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="6" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $subTotal).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="6" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			<b>Branch Name : </b>'.$companyData->company_bank_branch.'<br>
			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">P & F</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->freight_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>';
		$itemList.='</tr>';
		
		$itemList.=$gstRow;

		$igstAmt=0;
		$igstAmt = $salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst;
		$itemList.='<tr>';
			$itemList.='<td colspan="6" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>GST Amount ('.$partyData->currency.') : </b>'.numToWordEnglish($igstAmt).'</i></td>';
			$itemList.='<td colspan="2" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
		$itemList.='<td colspan="6" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words ('.$partyData->currency.') : </b>'.numToWordEnglish($salesData->net_amount).'</i></td>';
			$itemList.='<th colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-15" >GSTIN : '.$companyData->company_gst_no.'<br>PAN NO :'.$companyData->company_pan_no.'</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">TAX INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right">'.$it.'</th>
									</tr>
								</table>';
		}
		$gstJson=json_decode($partyData->json_data);
		$partyAddress=(!empty($gstJson->{$salesData->gstin})?$gstJson->{$salesData->gstin}:'');
		$dcData = $this->salesInvoice->getDeliveryChallanData($row->ref_id);
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:40%;" rowspan="5">
								<table>
									<tr><td style="vertical-align:top;"><b>M/s.</b></td><td>: <b>'.$salesData->party_name.'</b></td></tr>
									<tr><td style="vertical-align:top;"></td><td style="padding-left:8px;">'.(!empty($partyAddress->party_address)?$partyAddress->party_address:'').'</td></tr>
									<tr><td style="vertical-align:top;"><b>Contact No.</b></td><td>: '.$partyData->party_mobile.'</td></tr>
									<tr><td style="vertical-align:top;"><b>GSTIN No.</b></td><td>: <b>'.$salesData->gstin.'</b></td></tr>
									<tr><td style="vertical-align:top;"><b>State Code</b></td><td>: '.$salesData->party_state_code.'</td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Invoice No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : '.date('d/m/Y', strtotime($salesData->trans_date)).'</b>
							</td>
						</tr>
						<tr>
							<td style="width:60%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>P.O. No.</b></td><td>: '.$dcData->doc_no.'</td></tr>
									<tr>
										<td style="vertical-align:top;"><b>Challan No</b></td><td>: '.$salesData->challan_no.'</td>
										<td style="vertical-align:top;padding-left: 10px;"><b>Challan Date</b></td><td>: '.formatDate($salesData->challan_date).'</td>
									</tr>
									<tr>
										<td style="vertical-align:top;"><b>Mode of Desp.</b></td><td>: '.$salesData->trans_mode.'</td>
										<td style="vertical-align:top;padding-left: 10px;"><b>No. of Packages</b></td><td>: '.$salesData->total_packet.'</td>
									</tr>
									<tr>
										<td style="vertical-align:top;"><b>Transport</b></td><td>: '.$salesData->transport_name.'</td>
										<td style="vertical-align:top;padding-left: 10px;"><b>L. R. No.</b></td><td style="vertical-align:top;">: '.$salesData->lr_no.'</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		//exit;
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer))
		{
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}
		
		if(!empty($original))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,45,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($duplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($triplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		for($x=0;$x<$extra_copy;$x++)
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		// $mpdf->Output(FCPATH.$fpath,'F');
		
		$mpdf->Output($pdfFileName,'I');
	}

	public function getItemList(){
        $this->printJson($this->salesInvoice->getItemList($this->input->post('id')));
    }

	public function printTestReport($id){
		$invData1 = $this->salesInvoice->getInvoiceTransForTc($id);
		$invData = $invData1[0];
		if($invData->from_entry_type == 5):
			$transIdArr = array_column($invData1,'ref_id');
			
			// $dcData = $this->challan->getChallanForTc($invData->ref_id);
			// $docNoArr = array_column($dcData,'doc_no');
			// $invData->doc_no = implode(',',$docNoArr);
		else:
			$transIdArr = array_column($invData1,'id');
		endif;
		$poArray = array_unique(array_column($invData1,'po_no'));
		$invData->doc_no=$cust_po_no = implode(",",$poArray);
		$stockTrans = $this->salesInvoice->getStockTransForInv(implode(',',$transIdArr),$invData->from_entry_type);
		// print_r($stockTrans);exit;
		$itemID = array_column($stockTrans,'item_id');
		$tcArr = array_column($stockTrans,'tc_no');
		$batchNo = array_column($stockTrans,'batch_no');
		$color_code = array_column($stockTrans,'color_code');
		$stock_gradeid = array_column($stockTrans,'grade_id');
		$rmItemIds = array_column($stockTrans,'rm_item_id');
		$tcNoArr = array();$htNoArr = array();$stockData = [];
		foreach($tcArr as $key=>$tc_no):
			if(!empty(strpos($tc_no,'~'))):
				$tc = explode('~', $tc_no);
				$htNoArr[] = $tc[0];
				$tcNoArr[] = $tc[1];
				if(!empty($stock_gradeid[$key]))
				{
					$stockData[$stock_gradeid[$key]]['heat_no'][] = $tc[0];
					$stockData[$stock_gradeid[$key]]['tc_no'][] = $tc[1];
					$stockData[$stock_gradeid[$key]]['itemID'][] = $itemID[$key];
					$stockData[$stock_gradeid[$key]]['batch_no'][] = $batchNo[$key];
					$stockData[$stock_gradeid[$key]]['color_code'][] = $color_code[$key];
					$stockData[$stock_gradeid[$key]]['rm_item_id'][] = $rmItemIds[$key];
				}
			endif;
		endforeach;

		$tcNos = "'" . implode ( "', '", array_unique($tcNoArr) ) . "'";
		$htNos = "'" . implode ( "', '", array_unique($htNoArr) ) . "'";
		
		// Get TC Param Heading
		//$tcGrades = $this->salesInvoice->getTcGrades($tcNos);
		$gradesArr = [];$p=0;
		
		if(!empty($stockData))
		{
			foreach($stockData as $grade)
			{
				
				$tcNos = "'" . implode ( "', '", $grade['tc_no'] ) . "'";
				$htNos = "'" . implode ( "', '", $grade['heat_no'] ) . "'";
				$rm_item_id = "'" . implode ( "', '", $grade['rm_item_id'] ) . "'";
				$tc_number = $grade['tc_no'][0];//$grade->tc_prefix.$grade->tc_no;
				// $tcHeading = $this->salesInvoice->getTcParamByTcNo($tc_number);
				// if(empty($tcHeading) && !empty($grade['tc_no'][1])){
				// 	$tcHeading = $this->salesInvoice->getTcParamByTcNo($grade['tc_no'][1]);
				// }
				$tcHead=[];
				$tcHead[0]['param_name'] = '';$tcHead[0]['min_value'] = '';$tcHead[0]['max_value'] = '';
				$tcHead[1]['param_name'] = '';$tcHead[1]['min_value'] = '';$tcHead[1]['max_value'] = '';
				/*if(!empty($tcHeading))
				{
					foreach($tcHeading as $row)
					{
						if($row->spec_type == 1)
						{
							$tcHead[0]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[0]['min_value'] .='<th>'.$row->min_value.'</th>';
							$tcHead[0]['max_value'] .='<th>'.$row->max_value.'</th>';
						}
						if($row->spec_type == 2)
						{
							$tcHead[1]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[1]['min_value'] .='<th>'.$row->min_value.'</th>';
							$tcHead[1]['max_value'] .='<th>'.$row->max_value.'</th>';
						}
						if($row->spec_type == 6)
						{
							$tcHead[1]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[1]['min_value'] .='<th>'.$row->min_value.'</th>';
							$tcHead[1]['max_value'] .='<th>'.$row->max_value.'</th>';
						}
					}
				}*/
				
				$tcTransData = $this->salesInvoice->getTcParameter($tcNos,$htNos,$rm_item_id); 
				
				$tcTrans = Array();$material_type = '';
				$i=0;$c=0;$m=0;$l=0;
				$tcTrans[$i]['chemParam']='';$tcTrans[$i]['chemMinData']='';$tcTrans[$i]['chemMaxData']='';$tcTrans[$i]['chemResult']=[];
				$tcTrans[$i]['mechParam']='';$tcTrans[$i]['mechMinData']='';$tcTrans[$i]['mechMaxData']='';$tcTrans[$i]['mechResult']=[];
				$prevHeat='';$prevSize='';
				
				if(!empty($tcTransData)){ 
					$catNameArr = array_column($tcTransData,'category_name');
					$catNameUniq = array_unique($catNameArr); 
					$this->data['material_type'] = $material_type = implode(",",$catNameUniq);
				}
				$recLimitC=1;$recLimitM=1;$chemHead=[]; $chHeadCount = 1;$mechHeadCount = 1;$hrdHeadCount = 1;
				foreach($tcTransData as $row)
				{
					if($prevHeat!=$row->heat_no)
					{
						if($l > 0){$c++;$m++;}
						$tcTrans[$i]['chemResult'][$c]=[];$tcTrans[$i]['chemBatchNo'][$c]=[];$tcTrans[$i]['chemRefTc'][$c]=[];$tcTrans[$i]['chemSize'][$c]=[];
						$tcTrans[$i]['mechResult'][$m]=[];$tcTrans[$i]['mechBatchNo'][$m]=[];$tcTrans[$i]['mechRefTc'][$m]=[];$tcTrans[$i]['mechSize'][$c]=[];
						
						$batchNo =(!empty($row->heat_no)) ? $row->heat_no : "";
						$refTcNo =(!empty($row->ref_tc_no)) ? $row->ref_tc_no : "";
						$size =(!empty($row->size)) ? $row->size : "";

						$tcTrans[$i]['chemResult'][$c][]=$batchNo;$tcTrans[$i]['chemResult'][$c][]=$refTcNo;$tcTrans[$i]['chemResult'][$c][]=(!empty($row->size)) ? $row->size : 'N/A';
						$tcTrans[$i]['mechResult'][$m][]=$batchNo;$tcTrans[$i]['mechResult'][$m][]=$refTcNo;
					}
					
						
					if($row->spec_type == 1){
						$tcTrans[$i]['chemResult'][$c][] = (!empty($row->result) && $row->result > 0) ? number_format($row->result,3) : "-";
						if($chHeadCount <=10){
							$tcHead[0]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[0]['min_value'] .='<th>'.(($row->min_value > 0)?number_format($row->min_value,2):'-').'</th>';
							$tcHead[0]['max_value'] .='<th>'.(($row->max_value > 0)?number_format($row->max_value,3):'-').'</th>';
							$chHeadCount++;
						}
						
					}
					if($row->spec_type == 2){
						$tcTrans[$i]['mechResult'][$m][] = (!empty($row->result) && $row->result > 0) ? number_format($row->result,3) : "-";
						if($mechHeadCount <=4){
							$tcHead[1]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[1]['min_value'] .='<th>'.(($row->min_value > 0)?number_format($row->min_value,3):'-').'</th>';
							$tcHead[1]['max_value'] .='<th>'.(($row->max_value > 0)?number_format($row->max_value,3):'-').'</th>';
							$mechHeadCount++;
						}
						
						
					}
					if($row->spec_type == 6){

						$tcTrans[$i]['mechResult'][$m][] = (!empty($row->result) && $row->result > 0) ? number_format($row->result,3) : "-";

						if($hrdHeadCount <=1){
							$tcHead[1]['param_name'] .='<th>'.$row->param_name.'</th>';
							$tcHead[1]['min_value'] .='<th>'.(($row->min_value > 0)?number_format($row->min_value,3):'-').'</th>';
							$tcHead[1]['max_value'] .='<th>'.(($row->max_value > 0)?number_format($row->max_value,3):'-').'</th>';
							$hrdHeadCount++;
						}
						
					}
					
					$prevHeat=$row->heat_no;$prevSize=$size;
					$l++;
				}
				
				$this->data['testReportData'] = $testReportData = $tcTrans;
				$this->data['tcMasterData'] = $tcMasterData = (!empty($tcTransData)) ? $tcTransData[0] : array();
				$this->data['invData'] = $invData;
				
				/*** CHEMICAL COMPOSITION ***/
				$chemData = Array();$maxLineRecords = [6,12,18,24];
				$chemData[0] = '<table class="table item-list-bb" style="margin-top:8px;"><tr><th><h1 style="color: red;">Test Certificate Not Found...!</h1></th></tr></table>';
				if(!empty($testReportData))
				{
					$cParam = '';$totalCPARAM = count($testReportData[0]['chemResult']);
					$cParamArr = [];$x=0;$j=1;
					foreach($testReportData[0]['chemResult'] as $row)
					{
						$i=1;
						$cParam .= '<tr class="text-center">';
						foreach($row as $val){if($i<=13){$cParam .= '<td>'.$val.'</td>';}$i++;}
						$cParam .= '</tr>';
						if(in_array($j,$maxLineRecords)){$cParamArr[$x++] = $cParam;$cParam='';}if($j==6){$j=0;}$j++;
					}
					if(!empty($cParam)){$cParamArr[$x] = $cParam;}
					if(isset($cParamArr[$x]))
					{
						if($j<5){for($l=$j;$l<=5;$l++){$cParamArr[$x] .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}}
					}
					foreach($cParamArr as $key=>$cp)
					{
						$chemData[$key] = '<table class="table item-list-bb" style="margin-top:8px;">
										<tr>
											<th colspan="13">We hereby certify that material found results as per '.((!empty($tcMasterData->material_grade))?$tcMasterData->material_grade:"").'</th>
										</tr>
										<tr class="bg-grey"><th colspan="13">CHEMICAL COMPOSITION</th></tr>
										<tr class="text-center bg-grey"><th></th><th>Ref. TC No.</th><th>Size</th>'.$tcHead[0]['param_name'].'</tr>
										<tr class="bg-light-grey"><th>Min.</th><th></th><th></th>'.$tcHead[0]['min_value'].'</tr>
										<tr class="bg-light-grey"><th>Max.</th><th></th><th></th>'.$tcHead[0]['max_value'].'</tr>
										'.$cp.'
									</table>';
					}
				}
				/*** MECHANICAL COMPOSITION ***/
				$mechData[0] = '<table class="table item-list-bb" style="margin-top:8px;"><tr><th><h1 style="color: red;">Test Certificate Not Found...!</h1></th></tr></table>';
				$mParamArr = [];$x=0;$j=1;$mParam ='';
				if(!empty($testReportData))
				{
					foreach($testReportData[0]['mechResult'] as $row)
					{
						$i=1;
						$mParam .= '<tr class="text-center">';
						foreach($row as $val){if($i<=7){$mParam .= '<td>'.$val.'</td>';}$i++;}
						$mParam .= '</tr>';
						if(in_array($j,$maxLineRecords)){$mParamArr[$x++] = $mParam;$mParam='';}if($j==6){$j=0;}$j++;
					}
					if(!empty($mParam)){$mParamArr[$x] = $mParam;}
					if(isset($mParamArr[$x]))
					{
						if($j<5){for($l=$j;$l<=5;$l++){$mParamArr[$x] .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}}
					}
					foreach($mParamArr as $key=>$mp)
					{
						$mechData[$key] = '<table class="table item-list-bb" style="margin-top:8px;">
											<tr class="bg-grey"><th colspan="7">MECHANICAL PROPERTIES</th></tr>
											<tr class="text-center bg-grey"><th></th><th>Ref. TC No.</th>'.$tcHead[1]['param_name'].'</tr>
											<tr class="text-center bg-light-grey"><th>Min.</th><th></th>'.$tcHead[1]['min_value'].'</tr>
											<tr class="text-center bg-light-grey"><th>Max.</th><th></th>'.$tcHead[1]['max_value'].'</tr>
											'.$mp.'
										</table>';
					}
				}
				
				/*** Description Of Goods ***/
				
				$fData='';$fgDataArr = [];$i=1;$x=0;$j=1;$fgData = [];
				foreach($invData1 as $row)
				{
					$interBatchNo = array_intersect($grade['batch_no'], explode(',',$row->batchNo));
					if(in_array($row->item_id,$grade['itemID']) AND !empty($interBatchNo))
					{
						$fData .= '<tr class="text-center">
										<td>'.$i++.'</td>
										<td>'.$row->trans_number.'</td>
										<td>'.$row->item_name.'</td>
										<td>'.$row->drawing_no.'</td>
										<td>'.floatVal($row->qty).'</td>
									</tr>';
						if(in_array($j,$maxLineRecords)){$fgDataArr[$x++] = $fData;$fData='';}if($j==6){$j=0;}$j++;
						
						$htmlHeader = '<table class="table top-table-border"> 
											<tr>
												<th colspan="5">MATERIAL TEST CERTIFICATE <br> As Per EN 10204:2004 - Type 3.1</th>
												<th>F/QC/03(00/01.01.16)</th>
											</tr>
											<tr>
												<tr>
													<th class="text-left">Customer Name</th> <td>'.$row->party_name.'</td>
													<th class="text-left">T.C. No.</th> <td>'.((!empty($row->trans_no))?'AE/'.$row->trans_no:"").'</td>
													<th class="text-left">P.O. No.</th> <td>'.((!empty($invData->doc_no))?$invData->doc_no:"").'</td>
												</tr>
												<tr>
													<th class="text-left">Type Of Material</th> <td>'.((!empty($material_type))?$material_type:"").'</td>
													<th class="text-left">Date</th> <td>'.((!empty($row->trans_date))?formatDate($row->trans_date):"").'</td>
													<th class="text-left">Colour Code</th> <td>'.((!empty($tcMasterData->color_code))?$tcMasterData->color_code:"").'</td>
												</tr>
												<tr>
													<th class="text-left">Material Grade</th> <td colspan="5">'.((!empty($tcMasterData->material_grade))?$tcMasterData->material_grade:"").'</td>
												</tr>
											</tr>
										</table>';
					}
				}
				if(!empty($fData)){$fgDataArr[$x] = $fData;}
				if(isset($fgDataArr[$x]))
				{
					if($j<5){for($l=$j;$l<=5;$l++){$fgDataArr[$x] .= '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>';}}
				}
				foreach($fgDataArr as $key=>$fd)
				{
					$fgData[$key] = '<table class="table item-list-bb" style="margin-top:8px;">
									<tr>
										<td colspan="5">Heat Tretment:- Solution Annealed at 1050°C±20°C Soaked for 2 Hr/Inch Of Thickness Then Water Quenched.</td>
									</tr>
									<tr>
										<td colspan="5">Above material Manufactured, Sampled, Tested, Inspected & Confirming to the Requirements of Material Std. Specification and Purchase Order Requirements.</td>
									</tr>
									<tr>
										<th colspan="5" class="bg-grey">DESCRIPTION OF GOODS</th>
									</tr>
									<tr class="text-center bg-light-grey">
										<th>Sr. No.</th>
										<th>Invoice No.</th>
										<th>Item</th>
										<th>Drawing No.</th>
										<th>Qty.</th>
									</tr>
									'.$fd.'
								</table>';
				}
				
				$gradesArr[$p] = ['chemData'=>$chemData, 'mechData'=>$mechData, 'fgData'=>$fgData, 'htmlHeader'=>$htmlHeader];
				$p++;
			}
			
		}
		//print_r($gradesArr);exit;
		
		$companyInfo = $this->salesInvoice->getCompanyInfo();
		$letter_head = base_url('assets/images/letterhead_top.png');
		$prepare = $this->employee->getEmp($this->loginId);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate(date("Y-m-d")).')'; 

		$htmlHeader = '<img src="'.$letter_head.'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:60%;"></td>
							<th style="width:40%;" class="text-center">For, '.$companyInfo->company_name.'</th>
						</tr>
						<tr><td height="30" class="text-center">'.$prepareBy.'</td><td></td></tr>
						<tr>
							<td class="text-center"><b>Prepared By</b></td>
							<th class="text-center"><b>Authorised By</b></th>
						</tr>
					</table>';
		
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
	    $mpdf->SetWatermarkImage($logo,0.05,array(120,60)); 
	    $mpdf->showWatermarkImage = true;
		//$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLFooter($htmlFooter);
		if(!empty($gradesArr)){
			foreach($gradesArr as $grades)
			{
				for($x=0;$x<count($grades['chemData']);$x++)
				{
					$pdfData = (!empty($grades['chemData'][$x])) ? $grades['chemData'][$x] : '';
					$pdfData .= (!empty($grades['mechData'][$x])) ? $grades['mechData'][$x] : '';
					$pdfData .= (!empty($grades['fgData'][$x])) ? $grades['fgData'][$x] : '';
					$mpdf->SetHTMLHeader($htmlHeader.$grades['htmlHeader']);
					$mpdf->AddPage('P','','','','',5,5,80,20,5,5,'','','','','','','','','','A4-P');
					$mpdf->WriteHTML($pdfData);
				}
			}
		}else{
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->AddPage('P','','','','',5,5,60,20,5,5,'','','','','','','','','','A4-P');
			$mpdf->WriteHTML('<h1 style="color: red;" class="text-center">Test Certificate Not Found...!</h1>');
		}
		
		$mpdf->Output($pdfFileName,'I');
	}

	/* Created By :- Sweta @27-07-2023 */
	public function testCertificate(){
		$this->data['headData']->pageUrl = "salesInvoice/testCertificate";
        $this->data['tableHeader'] = getQualityDtHeader("testCertificate");
        $this->load->view($this->tc_index,$this->data);
    }

	/* Created By :- Sweta @27-07-2023 */
	public function testCertificateDTRows(){
		$data = $this->input->post();
        $result = $this->salesInvoice->testCertificateDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getTestCertificateData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	/* Created by milan v 30-10-2025 */
	public function addPackingSlip(){
		$data = $this->input->post();
		
		$this->data['invoice_id'] = $data['id'];
		$this->data['itemData'] = $this->salesInvoice->salesTransactions($data['id']);

        $this->load->view($this->packingSlipForm,$this->data);
    }

	public function savePackingSlip(){
		$data = $this->input->post();	

		$errorMessage = array();
		if(empty($data['wooden_box_no'])){
			$errorMessage['wooden_box_no'] = "Box No. is required.";
		}
		if(empty($data['item_id'])){
			$errorMessage['item_id'] = "Item is required.";
		}
		if(empty($data['qty'])){
			$errorMessage['qty'] = "Qty is required.";
		}
		if(!empty($errorMessage)){
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
		}
		else{
			$data['id'] = '';
			$data['created_by'] = $this->session->userdata('loginId');

			$this->printJson($this->salesInvoice->savePackingSlipItem($data));
		}
    }

	public function getPackingSlipItemList(){
		$this->printJson($this->salesInvoice->getPackingSlipItemList($this->input->post('inv_id')));
    }

	public function deletePackingSlipItem(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->salesInvoice->deletePackingSlipItem($id));
		endif;
	}

	public function packing_slip_pdf($id = ''){		
		$sales_id = $id;
		$salesData = $this->salesInvoice->getPackingSlip($id);
		$companyData = $this->challan->getCompanyInfo();

		$letter_head=base_url('assets/images/letterhead_top.png');

		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		
		$itemList='<table class="table item-list-bb">
					<thead><tr class="text-center">
						<th style="width:6%;">Wooden Box No.</th>
						<th class="text-left">H.A.T. Product No.</th>
						<th style="width:10%;">Invoice No. </th>
						<th style="width:10%;">Invoice Date </th>
						<th style="width:10%;">P.O NO.</th>
						<th style="width:10%;">P.O Dt.</th>

						<th style="width:10%;">Item </th>
						<th style="width:10%;">Drawing No.</th>
						<th style="width:15%;">Description</th>
						<th style="width:10%;">Grade</th>
						<th style="width:10%;">Qty</th>
						
					</tr></thead><tbody>';
		
		// Terms & Conditions		
		$blankLines=10;
		$terms = '<table class="table ">';$tc=new StdClass;	
		$tc = array();
		$terms .= '<tr>
					<td style="width:65%;font-size:12px;"></td>
					<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
						For, '.$companyData->company_name.'<br>
					</th>
			</tr>';
		
		$terms .= '</table>';
		
		$lastPageItems = '';$pageCount = 0;
		$i=1;$total_qty=0;
		$pageData = array();$totalPage = 0;
		$totalItems = !empty($salesData) ? count($salesData->itemData) : 0;
		
		$lpr = $blankLines ;
		$pr1 = $blankLines + 1 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		
		$x = 0;
		$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
		$tempData = $this->salesInvoice->getPackingSlipItems($sales_id);
		
		if(!empty($tempData))
		{
			$item_records = [];
			foreach ($tempData as $row)   
			{	
				$item_records[$row->wooden_box_no][] = $row;
			}
			if (!empty($item_records)) {
				foreach ($item_records as $key => $main_row) {
					$rowspan = count($main_row); 
					$firstRow = true; 

					foreach ($main_row as $row) {
						$pageItems .= '<tr>';

						if ($firstRow) {
							$pageItems .= '<td class="text-center" height="37" rowspan="'.$rowspan.'">'.$key.'</td>';
							$firstRow = false; 
						}

						$pageItems .= '<td class="text-left">'.$row->item_code.'</td>';
						$pageItems .= '<td class="text-left">'.$salesData->trans_number.'</td>';
						$pageItems .= '<td class="text-center">'.formatDate($salesData->trans_date).'</td>';
						$pageItems .= '<td class="text-center">'.$salesData->doc_no.'</td>';
						$pageItems .= '<td class="text-center">'.formatDate($salesData->doc_date).'</td>';
						$pageItems .= '<td class="text-left">'.$row->item_name.'</td>';
						$pageItems .= '<td class="text-left">'.$row->drawing_no.'</td>';
						$pageItems .= '<td class="text-center">'.$row->description.'</td>';
						$pageItems .= '<td class="text-center">'.$row->material_grade.'</td>';
						$pageItems .= '<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';

						$pageItems .= '</tr>';

						$total_qty += $row->qty;
					}
				}
			}
		}
		
		if($x==$totalPage)
		{
			$pageData[$x]= '';
			$lastPageItems = $pageItems;
		}
		else
		{
			$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
		}
		$pageCount += $pageRow;

		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';
			}
		}

		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="10" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><br>'.$terms.'';

		$invoiceType = '<br><br><table style="margin-bottom:5px;">
								<tr>
									<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" >GSTIN: '.$companyData->company_gst_no.'</th>
									<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">PACKING DETAILS</th>
									<th style="width:35%;letter-spacing:2px;" class="text-right"></th>
								</tr>
							</table>';
		
		$baseDetail='<table class="table top-table" style="margin-bottom:5px;border:1px solid">
						<tr>
							<td rowspan="7" style="width:50%;border-right:1px solid; vertical-align: top;">
								<b>INVOICE TO</b><br>
								<b>'.$salesData->party_name.'</b><br>
								'.$salesData->billing_address.'<br>
								<b>GSTIN : '.$party_gstin.'</b>
							</td>
							<td style="width:50%;">
								<b>Invoice No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
							</td>
						</tr>
						<tr>
							<td><b>Date : '.date('d/m/Y').'</b></td>
						</tr>
						<tr><td style="vertical-align:top;"><b>P.O. No.</b>: '.$salesData->doc_no.'</td></tr>
									
						<tr><td style="vertical-align:top;"><b>Transport</b>: '.$salesData->transport_name.'</td></tr>
						<tr><td style="vertical-align:top;"><b>Lr. No.</b>: '.$salesData->lr_no.'</td></tr>
						<tr><td style="vertical-align:top;"><b>No. of Packages</b>: '.$salesData->total_packet.'</td></tr>
						<tr><td style="vertical-align:top;"><b>Total Weight</b>: '.$salesData->net_weight.'</td></tr>
					</table>';
				
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table-bordered" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INVOICE No. & Date : '.$salesData->doc_no.' '.formatDate($salesData->doc_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;

		$pdfFileName = 'Packing-slip.pdf';
		
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);

		foreach($pageData as $pg)
		{
			$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
			$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType.$baseDetail.$pg.'</div></div>');
		}
		
		$mpdf->Output($pdfFileName,'I');
	}
}
?>