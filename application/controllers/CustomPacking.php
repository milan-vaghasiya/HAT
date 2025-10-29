<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class CustomPacking extends MY_Controller{	
	private $indexPage = "custom_packing/index";
    private $invoiceForm = "custom_packing/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Custom Packing";
		$this->data['headData']->controller = "customPacking";
		$this->data['headData']->pageUrl = "customPacking";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
		$data = $this->input->post(); 
		$data['entry_type'] = 20;
		$data['status'] = $status;
        $result = $this->customPacking->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCustomPackingData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
		$this->data['comPackingNoList'] = $this->customPacking->getCommercialPackingNoList();
        $this->load->view($this->invoiceForm,$this->data);
    }

    public function getCommercialPackingData(){
        $id = $this->input->post('id');
		$result = $this->commercialPacking->getCommercialPackingData($id);
		$itemData = $result->itemData;
		
		$result->id = "";
        $result->doc_date = (!empty($result->doc_date))?date("d-m-Y",strtotime($result->doc_date)):"";
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 20;
		unset($result->itemData,$result->ref_id);
		
		$itemList = array();
		foreach($itemData as $row):
			if($row->packing_status == 1):
				$row->ref_id = $row->id;
				$row->id = "";
				$row->from_entry_type = $row->entry_type;
				$row->entry_type = 20;
				$itemList[] = $row;
			endif;
		endforeach;

		$this->printJson(['status'=>1,'masterData'=>$result,'itemData'=>$itemList]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['ref_id']))
            $errorMessage['ref_id'] = "Com. Pac. No is required.";
        if(empty($data['doc_no']))
            $errorMessage['doc_no'] = "Inv. No. is required.";
        if(empty($data['extra_fields']['export_type']))
            $errorMessage['export_type'] = "Export Type is required.";
        if(empty($data['extra_fields']['buyer_address']))
            $errorMessage['buyer_address'] = "Buyer Address is required.";
        if(empty($data['extra_fields']['consignee_name']))
            $errorMessage['consignee_name'] = "Consignee Name is required.";
        if(empty($data['extra_fields']['consignee_address']))
            $errorMessage['consignee_address'] = "Consignee Address is required.";
        if(empty($data['extra_fields']['country_of_final_destonation']))
            $errorMessage['country_of_final_destonation'] = "Country of Final Destination is required.";
        if(empty($data['extra_fields']['applicable_prefrential_agreement']))
            $errorMessage['applicable_prefrential_agreement'] = "Applicable Prefrential Agreement is required.";
        if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Item Details is required.";

		if(!empty($data['ref_by'])):
			$packingData = $this->packings->getPackingData($data['ref_by']);
			if(!empty($packingData) && $packingData->is_final == 0):
				$errorMessage['item_name_error'] = "Final Packing has not been done for dispatch.";
			endif;
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $data['item_data'];
            unset($data['item_data']);
			$data['doc_date'] = (!empty($data['doc_no']))?date("Y-m-d",strtotime($data['doc_date'])):NULL;
            $data['entry_type'] = 20;
            $data['created_by'] = $this->loginId;

			$data['extra_fields'] = json_encode($data['extra_fields']);
            $masterData = $data;
            $this->printJson($this->customPacking->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['comPackingNoList'] = $this->customPacking->getCommercialPackingNoList();
        $packingData = $this->customPacking->getCustomPackingData($id);
        $packingItemData = $packingData->itemData;
		$result = $this->commercialPacking->getCommercialPackingData($packingData->ref_id);
		$itemData = $result->itemData;

		$result->id = $packingData->id;
		$result->ref_id = $packingData->ref_id;
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 20;
        $result->export_type = $packingData->export_type;
        $result->lut_no = $packingData->lut_no;
        $result->buyer_address = $packingData->buyer_address;
        $result->consignee_name = $packingData->consignee_name;
        $result->consignee_address = $packingData->consignee_address;
        $result->country_of_final_destonation = $packingData->country_of_final_destonation;
        $result->applicable_prefrential_agreement = $packingData->applicable_prefrential_agreement;
        $result->remark = $packingData->remark;

		unset($result->itemData);

		$itemList = array();
		foreach($itemData as $row):
			if(in_array($row->id,array_column($packingItemData,'ref_id'))):
				$itemKey = array_search($row->id,array_column($packingItemData,'ref_id'));
				$row->id = $packingItemData[$itemKey]->id;
                $row->ref_id = $packingItemData[$itemKey]->ref_id;
				$row->from_entry_type = $packingItemData[$itemKey]->from_entry_type;
                $row->hsn_code = $packingItemData[$itemKey]->hsn_code;
                $row->hsn_desc = $packingItemData[$itemKey]->hsn_desc;
				$row->price = $packingItemData[$itemKey]->price;
			else:
				$row->ref_id = $row->id;
				$row->id = "";
				$row->from_entry_type = $row->entry_type;
			endif;			
			$row->entry_type = 20;			
			$itemList[] = $row;
		endforeach;

		$result->itemData = $itemList;
		$this->data['dataRow'] = $result;
        $this->load->view($this->invoiceForm,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->customPacking->delete($id));
		endif;
	}

    public function customPackingPdf($id){		
		$result = $this->customPacking->getCustomPackingData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['packageNum'] = $this->customPacking->getCustomPackingItems($id);
		$pdfData = $this->load->view('custom_packing/custom_packing_pdf',$this->data,true);		
		$mpdf = $this->m_pdf->load();
		$pdfFileName=preg_replace('/[^A-Za-z0-9]/',"_",$result->trans_number).'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->setTitle($result->trans_number);
		$mpdf->SetDisplayMode('fullpage');
		
		if(empty($result->doc_no)):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
            $logo=base_url('assets/images/logo.png?v='.time());
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHtmlFooter('<div class="text-center fs-13" style="border-top:1px solid #696969;border-bottom:1px solid #696969;">Page No. {PAGENO} / {nb}</div>');
		$mpdf->AddPage('P','','','','',5,5,5,5,5,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
}
?>