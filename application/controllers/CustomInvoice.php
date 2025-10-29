<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class CustomInvoice extends MY_Controller{
    private $indexPage = "custom_invoice/index";
    private $invoiceForm = "custom_invoice/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Custom Invoice";
		$this->data['headData']->controller = "customInvoice";
		$this->data['headData']->pageUrl = "customInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
		$data = $this->input->post(); 
		$data['entry_type'] = 11;
		$data['status'] = $status;
        $result = $this->customInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCustomInvocieData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addInvoice(){
		$this->data['cumPackingNoList'] = $this->customInvoice->getCustomPackingNoList();
        $this->load->view($this->invoiceForm,$this->data);
    }

	public function getCustomPackingData(){
		$id = $this->input->post('id');
		$result = $this->customPacking->getCustomPackingData($id);
		$itemData = $result->itemData;
		
		$result->id = "";
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 11;
		unset($result->itemData,$result->ref_id);
		
		$itemList = array();
		foreach($itemData as $row):
			$row->ref_id = $row->id;
			$row->id = "";
			$row->from_entry_type = $row->entry_type;
			$row->entry_type = 11;
			$row->price = $row->so_price;
			$row->amount = round($row->qty * $row->price,4);
			$row->taxable_amount = $row->amount;
			$row->net_amount = $row->amount;
			$itemList[] = $row;
		endforeach;

		$result->total_amount = array_sum(array_column($itemList,'amount'));
		$result->net_amount = array_sum(array_column($itemList,'net_amount'));

		$this->printJson(['status'=>1,'masterData'=>$result,'itemData'=>$itemList]);
	}

	public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['ref_id'] = "Cum. Pac. No is required.";

		if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Item is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$itemData = $data['item_data'];
            unset($data['item_data']);
			$data['doc_date'] = (!empty($data['doc_no']))?$data['doc_date']:NULL;
            $data['entry_type'] = 11;
            $data['created_by'] = $this->loginId;

			$data['extra_fields'] = json_encode($data['extra_fields']);
            $masterData = $data;
            $this->printJson($this->customInvoice->save($masterData,$itemData));
		endif;
	}

	public function edit($id){
		$this->data['cumPackingNoList'] = $this->customInvoice->getCustomPackingNoList();
		$invData = $this->customInvoice->getCustomInvoiceData($id);
		$invItemData = $invData->itemData;
		$result = $this->customPacking->getCustomPackingData($invData->ref_id);
		$itemData = $result->itemData;

		$result->id = $invData->id;
		$result->ref_id = $invData->ref_id;
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 11;
		$result->total_amount = 0;
		$result->freight_amount = $invData->freight_amount;
		$result->other_amount = $invData->other_amount;
		$result->net_amount = 0;
		unset($result->itemData);

		$itemList = array();
		foreach($itemData as $row):
			if(in_array($row->id,array_column($invItemData,'ref_id'))):
				$itemKey = array_search($row->id,array_column($invItemData,'ref_id'));
				$row->id = $invItemData[$itemKey]->id;
                $row->ref_id = $invItemData[$itemKey]->ref_id;
				$row->from_entry_type = $invItemData[$itemKey]->from_entry_type;
				$row->price = $invItemData[$itemKey]->price;
			else:
				$row->ref_id = $row->id;
				$row->id = "";
				$row->from_entry_type = $row->entry_type;
				$row->price = $row->so_price;
			endif;			
			$row->entry_type = 11;			
			$row->amount = round($row->qty * $row->price,4);
			$row->taxable_amount = $row->amount;
			$row->net_amount = $row->amount;
			$itemList[] = $row;
		endforeach;

		$result->total_amount = array_sum(array_column($itemList,'amount'));
		$result->net_amount = array_sum(array_column($itemList,'net_amount'));
		$result->net_amount += ($result->freight_amount > 0)?$result->freight_amount:0;
		$result->net_amount += ($result->other_amount > 0)?$result->other_amount:0;

		$result->itemData = $itemList;
		$this->data['dataRow'] = $result;
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->customInvoice->delete($id));
		endif;
	}

	public function getPartyCustomInvocie(){
		$party_id = $this->input->post('party_id');
		$this->printJson($this->customInvoice->getPartyCustomInvocie($party_id));
	}

	public function customInvoicePdf($id){
		$result = $this->customInvoice->getCustomInvoiceData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['packageNum'] = $this->customInvoice->getCustomInvoiceItems($id);
		$pdfData = $this->load->view('custom_invoice/custom_invoice_pdf',$this->data,true);
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName=preg_replace('/[^A-Za-z0-9]/',"_",$result->trans_number).'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->setTitle($result->trans_number);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHtmlFooter('<div class="text-center fs-13" style="border-top:1px solid #696969;border-bottom:1px solid #696969;">Page No. {PAGENO} / {nb}</div>');
	
		if(empty($result->doc_no)):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
            $logo=base_url('assets/images/logo.png?v='.time());
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetProtection(array('print'));
		
	
		$mpdf->AddPage('P','','','','',5,5,5,8,5,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
	
	public function evdPdf($id){
		$result = $this->customInvoice->getCustomInvoiceData($id,1);
        $this->data['dataRow'] = $result;
		$this->data['companyData'] = $this->salesOrder->getCompanyInfo();
		$logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');

		$pdfData = $this->load->view('custom_invoice/evd_pdf',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='EVD_'.$result->trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->setTitle('EVD_'.$result->trans_number);
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,41,10,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

	//Created By Karmi @11/07/2022
	public function scometPrint(){
		$data=$this->input->post();
        $id=$data['id'];
        $shipment=$data['shipment'];
		$result = $this->customInvoice->getCustomInvoiceData($id,1);
        $this->data['dataRow'] = $result; 
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
		$this->data['extraField'] = json_decode($result->extra_fields);
		$this->data['shipment'] = $shipment;
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['country'] = (!empty($this->data['partyData']->country_id)) ? $this->party->getCountryById($this->data['partyData']->country_id) : '';

		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());

		$pdfData = $this->load->view('custom_invoice/custom_scomet_pdf',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$mpdf = $this->m_pdf->load();
		$pdfFileName='SCOMET_'.$result->trans_number.'.pdf';
		
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->setTitle('SCOMET_'.$result->trans_number);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHTMLHeader($htmlHeader);
		//$mpdf->SetHtmlFooter('<div class="text-center fs-13" style="border-top:1px solid #696969;border-bottom:1px solid #696969;">Page No. {PAGENO} / {nb}</div>');
	
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
	
		$mpdf->AddPage('P','','','','',10,5,40,8,5,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');	

	}
	
	public function dbkPdf($id){
		$result = $this->customInvoice->getCustomInvoiceData($id,1);
		$this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['dataRow'] = $result;
		$this->data['companyData'] = $this->salesOrder->getCompanyInfo();
		$logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');

		$pdfData = $this->load->view('custom_invoice/dbk_pdf',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DBK_'.$result->trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->setTitle('DBK_'.$result->trans_number);
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,41,10,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>