<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class CommercialInvoice extends MY_Controller{	
	private $indexPage = "commercial_invoice/index";
    private $invoiceForm = "commercial_invoice/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Commercial Invoice";
		$this->data['headData']->controller = "commercialInvoice";
		$this->data['headData']->pageUrl = "commercialInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
		$data = $this->input->post(); 
		$data['entry_type'] = "10";
		$data['status'] = $status;
        $result = $this->commercialInvoice->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getCommercialInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInvoice(){
		$this->data['comPackingNoList'] = $this->commercialInvoice->getCommercialPackingNoList();
        $this->load->view($this->invoiceForm,$this->data);
    }

	public function getCommercialPackingData(){
		$id = $this->input->post('id');
		$result = $this->commercialPacking->getCommercialPackingData($id);
		$itemData = $result->itemData;
		
		$result->id = "";
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 10;
		unset($result->itemData,$result->ref_id);
		
		$itemList = array();
		foreach($itemData as $row):
			$row->ref_id = $row->id;
			$row->id = "";
			$row->from_entry_type = $row->entry_type;
			$row->entry_type = 10;
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
			$errorMessage['ref_id'] = "Com. Pac. No is required.";

		if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Item is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$itemData = $data['item_data'];
            unset($data['item_data']);
			$data['doc_date'] = (!empty($data['doc_no']))?$data['doc_date']:NULL;
            $data['entry_type'] = 10;
            $data['created_by'] = $this->loginId;

			$data['extra_fields'] = json_encode($data['extra_fields']);
            $masterData = $data;
            $this->printJson($this->commercialInvoice->save($masterData,$itemData));
		endif;
	}

	public function edit($id){
		$this->data['comPackingNoList'] = $this->commercialInvoice->getCommercialPackingNoList();
		$invData = $this->commercialInvoice->getCommercialInvocieData($id);
		$invItemData = $invData->itemData;
		$result = $this->commercialPacking->getCommercialPackingData($invData->ref_id);
		$itemData = $result->itemData;

		$result->id = $invData->id;
		$result->ref_id = $invData->ref_id;
		$result->from_entry_type = $result->entry_type;
		$result->entry_type = 10;
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
			$row->entry_type = 10;			
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
			$this->printJson($this->commercialInvoice->delete($id));
		endif;
	}

	public function commercialInvoicePdf1($id){
		$result = $this->commercialInvoice->getCommercialInvocieData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
		$pdfData = $this->load->view('commercial_invoice/commercial_invoice_pdf',$this->data,true);
		$mpdf = $this->m_pdf->load();
		$pdfFileName='COMINV-'.$result->trans_no.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		//$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	public function commercialInvoicePdf($id,$file_type='PDF'){
		$result = $this->commercialInvoice->getCommercialInvocieData($id,1);
        $this->data['dataRow'] = $result;
        $this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['partyData'] = $this->party->getParty($result->party_id);
        $this->data['packageNum'] = $this->commercialInvoice->getCommercialInvoiceItems($id);
		$pdfData = $this->load->view('commercial_invoice/commercial_invoice_pdf',$this->data,true);
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
            $fileName = '/commercial_inv_' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/export_inv_excel') . $fileName);
		}
		
	}

}
?>