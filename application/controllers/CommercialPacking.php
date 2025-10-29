<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CommercialPacking extends MY_Controller{
    private $index = "commercial_packing/index";
    private $form = "commercial_packing/form";

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
		$this->data['headData']->pageTitle = "Commercial Packing";
		$this->data['headData']->controller = "commercialPacking";
		$this->data['headData']->pageUrl = "commercialPacking";
    }

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->index,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); 
        $data['entry_type'] = 19;
        $data['status'] = $status;
        $result = $this->commercialPacking->getDTRows($data);
        $sendData = array();
        foreach($result['data'] as $row):
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCommercialPackingData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->form,$this->data);
    }

    public function getPackingNoList(){
        $party_id = $this->input->post('party_id');
        $result = $this->commercialPacking->getPackingNoList($party_id);
        $packingList = '<option value="">Select Packing No.</option>';
        foreach($result as $row):
            $packingList .= '<option value="'.$row->id.'">'.$row->trans_number.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'packingList'=>$packingList]);
    }

    public function getPackingItemList(){
        $packing_id = $this->input->post('packing_id');
        $result = $this->commercialPacking->getPackingItemList($packing_id);
        $this->printJson(['status'=>1,'itemList'=>$result]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party name is required.";
        
        if(empty($data['ref_id']))
            $errorMessage['ref_id'] = "Packing No. is required.";

        if(empty($data['extra_fields']['pre_carriage_by']))
            $errorMessage['pre_carriage_by'] = "Pre-Carriage by is required.";

        if(empty($data['extra_fields']['place_of_rec_by_pre_carrier']))
            $errorMessage['place_of_rec_by_pre_carrier'] = "Place of receipt by Pre-Carrier is required."; 
            
        if(empty($data['extra_fields']['vessel_flight']))
            $errorMessage['vessel_flight'] = "Vessel / Flight is required."; 

        if(empty($data['extra_fields']['port_of_loading']))
            $errorMessage['port_of_loading'] = "Port Of Loading is required."; 

        if(empty($data['extra_fields']['port_of_discharge']))
            $errorMessage['port_of_discharge'] = "Port Of Discharge is required."; 

        if(empty($data['extra_fields']['place_of_delivery']))
            $errorMessage['place_of_delivery'] = "Place Of Delivery is required.";
            
        if(empty($data['extra_fields']['country_of_final_destonation']))
            $errorMessage['country_of_final_destonation'] = "Country of Final Destination is required.";

        if(empty($data['terms_conditions']))
            $errorMessage['terms_conditions'] = "Terms of Delivery and Payment is required.";

        if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Item is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $data['item_data'];
            unset($data['item_data']);

            $data['ref_by'] = $data['ref_id'];
            $data['doc_date'] = (!empty($data['doc_no']))?$data['doc_date']:NULL;
            $data['entry_type'] = 19;
            $data['created_by'] = $this->loginId;
            $data['extra_fields']['packing_trans_id'] = implode(",",array_column($itemData,'ref_id'));            

            $data['extra_fields']['no_of_wooden_box'] = 0;
            foreach($itemData as $row):
                if($row['taxable_amount'] > 0):
                    $data['extra_fields']['no_of_wooden_box'] += 1;
                endif;
            endforeach;

            $customerPOData = $this->commercialPacking->getCustomerPOData($data['extra_fields']['packing_trans_id']);
            $cust_po_no=array();$cust_po_date=array();$so_id=array();
            foreach($customerPOData as $row):
                if(!in_array($row->id,$so_id)):
                    $cust_po_no[] = $row->doc_no;
                    $cust_po_date[] = date("d-m-Y",strtotime($row->doc_date));
                    $so_id[] = $row->id;
                endif;
            endforeach;
            $data['extra_fields']['so_id'] = implode(",",$so_id);
            $data['extra_fields']['cust_po_no'] = implode(",",$cust_po_no);
            $data['extra_fields']['cust_po_date'] = implode(",",$cust_po_date);
            $data['extra_fields']['total_net_weight'] = $data['total_amount'];
            $data['extra_fields']['total_gross_weight'] = $data['net_amount'];

            $data['extra_fields'] = json_encode($data['extra_fields']);
            $masterData = $data;
            $this->printJson($this->commercialPacking->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['customerData'] = $this->party->getCustomerList();
        $result = $this->commercialPacking->getCommercialPackingData($id);
        $packingData = $this->commercialPacking->getPackingItemList($result->ref_id);
        $itemData = $result->itemData;
        unset($result->itemData);

        $dataRows = array();
        foreach($packingData as $row):
            if(in_array($row->id,array_column($itemData,'ref_id'))):
                $itemKey = array_search($row->id,array_column($itemData,'ref_id'));
                $row->id = $itemData[$itemKey]->id;
                $row->ref_id = $itemData[$itemKey]->ref_id;
                $row->hsn_code = $itemData[$itemKey]->hsn_code;
                $row->hsn_desc = $itemData[$itemKey]->hsn_desc;
            else:
                $row->ref_id = $row->id;
                $row->id = "";
            endif;
            $dataRows[] = $row;
        endforeach;
        $result->itemData = $dataRows;

        $packingListData = $this->commercialPacking->getPackingNoList($result->party_id);
        $packingList = '<option value="">Select Packing No.</option>';
        foreach($packingListData as $row):
            $selected = ($row->id == $result->ref_id)?"selected":"";
            $packingList .= '<option value="'.$row->id.'" '.$selected.'>'.$row->trans_number.'</option>';
        endforeach;
        $result->packing_no_list = $packingList;
        $this->data['dataRow'] = $result;
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->commercialPacking->delete($id));
		endif;
	}

    //Created By Avruti @19/04/2022
	public function commercialPackingPdf1($id){	
		$result = $this->commercialPacking->getCommercialPackingData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        //print_r(count($result->itemData));exit;
		$pdfData = $this->load->view('commercial_packing/commercial_packing_pdf',$this->data,true);
		$mpdf = $this->m_pdf->load();
		$pdfFileName='PACKING-LIST-'.$result->ref_by.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	//Created By Avruti @19/04/2022
	public function commercialPackingPdf($id,$file_type='PDF'){	
		$result = $this->commercialPacking->getCommercialPackingData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        //print_r(count($result->itemData));exit;
        $this->data['packageNum'] = $this->commercialPacking->getCommercialPackingItems($id);
        $pdfData = $this->load->view('commercial_packing/commercial_packing_pdf',$this->data,true);

        if($file_type=='PDF'){

            $mpdf = $this->m_pdf->load();
            $pdfFileName=preg_replace('/[^A-Za-z0-9]/',"_",$result->trans_number).'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->setTitle($result->trans_number);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));
            
            if(empty($result->doc_no)):
                $mpdf->SetWatermarkText('TENTATIVE',0.05);
                $mpdf->showWatermarkText = true;
            else:
                $logo=base_url('assets/images/logo.png?v='.time());
    		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
    		    $mpdf->showWatermarkImage = true;
    		endif;
            
            $mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        else{
            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];

            // Sales Sheet
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->loadFromString($pdfData);
            $spreadsheet->getSheet(0)->setTitle('Sales');
            $packingSheet = $spreadsheet->getSheet(0);
            $pack_hcol = $packingSheet->getHighestColumn();
            $pack_hrow = $packingSheet->getHighestRow();
            $packFullRange = 'A1:' . $pack_hcol . $pack_hrow;

            foreach (range('A', $pack_hcol) as $col) {
                $packingSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $packingSheet->getStyle('A1:' . $pack_hcol . '3')->applyFromArray($styleArray);
            $packingSheet->getStyle('A' . $pack_hrow . ':' . $pack_hcol . $pack_hrow)->applyFromArray($fontBold);
            $packingSheet->getStyle('A1')->applyFromArray($alignLeft);
            $packingSheet->getStyle('A' . $pack_hrow)->applyFromArray($alignRight);
            $packingSheet->getStyle('J1')->applyFromArray($alignRight);
            $packingSheet->getStyle($packFullRange)->applyFromArray($borderStyle);

            $fileDirectory = realpath(APPPATH . '../assets/uploads/export_inv_excel');
            $fileName = '/packing' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/export_inv_excel') . $fileName);
        }
	}

}
?>