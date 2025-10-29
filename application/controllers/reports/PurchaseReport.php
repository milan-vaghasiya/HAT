<?php
class PurchaseReport extends MY_Controller
{
    private $indexPage = "report/purchase_report/index";
    private $raw_material = "report/purchase_report/raw_material";
    private $purchase_monitoring = "report/purchase_report/purchase_monitoring";
    private $purchase_inward = "report/purchase_report/purchase_inward";
    private $supplier_wise_item = "report/purchase_report/supplier_wise_item";
    private $grn_tracking = "report/purchase_report/grn_tracking";
    private $supplier_rating = "report/purchase_report/supplier_rating";
    private $pending_po = "report/purchase_report/pending_po";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Report";
		$this->data['headData']->controller = "reports/purchaseReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/purchase_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* RawMaterial Report */
	public function rawMaterialReport(){
        $this->data['pageHeader'] = 'RAW MATERIAL REPORT';
        $this->data['rawMaterialData'] = $this->storeReportModel->getrawMaterialReport();
        $this->load->view($this->raw_material,$this->data);
    }

    /* Purchase Monitoring Report */
    public function purchaseMonitoring(){
        $this->data['pageHeader'] = 'PURCHASE ORDER MONITORING REGISTER';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_monitoring,$this->data);
    }

    public function getPurchaseMonitoring($jsonData=""){
         if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPurchaseMonitoring($data);
            $tbody="";$i=1;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($purchaseData as $row):
                    $data['item_id'] = $row->item_id;$data['grn_trans_id'] = $row->id;
                    $receiptData = $this->purchaseReport->getPurchaseReceipt($data);
                    $receiptCount = count($receiptData);
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.formatDate($row->po_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->reference_by.'</td>
                        <td>'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                        <td>'.$row->wo_no.'</td>
                        <td>'.floatval($row->qty).' ('.$row->unit_name.')</td>
                        <td>'.$row->price.'</td>
                        <td>'.formatDate($row->delivery_date).'</td>';
                        if($receiptCount > 0):
                            $j=1;
                            foreach($receiptData as $recRow):
                                $totalAmt = $recRow->qty * $recRow->price;
                                $tbody.='<td>'.$recRow->challan_no.'</td>
                                            <!-- <td>'.getPrefixNumber($recRow->grn_prefix,$recRow->grn_no).'</td> -->
                                            <td>'.formatDate($recRow->grn_date).'</td>
                                            <td>'.floatval($recRow->qty).'</td>';
                                if($j != $receiptCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>';
                        endif;
                        $tbody.='</tr>';
            endforeach;
		endif;
        if($data['type'] == 1):                
            $topSectionO ='<table class="table">
                            <tr>
                                <td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
                                <td class="org_title text-center" style="font-size:1rem;width:60%">PURCHASE ORDER MONITORING REGISTER</td>
                                <td style="width:20%;" class="text-right"><span style="font-size:0.8rem;"></td>
                            </tr>
                        </table>';
            $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
						<thead class="thead-info" id="theadData">
							<tr class="text-center">
								<th rowspan="2">#</th>
								<th rowspan="2" style="min-width:80px;">Date</th>
								<th rowspan="2" style="min-width:100px;">External Provider Name</th>
								<th rowspan="2" style="min-width:100px;">Item Description</th>
								<th rowspan="2" style="min-width:100px;">Mode of Purchase.</th>
								<th rowspan="2" style="min-width:80px;">Order No.</th>
								<th rowspan="2" style="min-width:50px;">Order Qty.</th>
								<th rowspan="2" style="min-width:50px;">Price</th>
								<th rowspan="2" style="min-width:80px;">Delivery Date</th>
								<th colspan="3">Receipt Details</th>
							</tr>
							<tr class="text-center">
								<th style="min-width:100px;">Challan/Invoice No.</th>
								<th style="min-width:80px;">Date</th>
								<th style="min-width:50px;">Qty.</th>
							</tr>
						</thead>';                                
			$itemList.='<tbody id="tbodyData">';
			$itemList.=$tbody;
			$itemList.='</tbody></table>';
				
            $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';		
            $pdfData = $originalCopy;            
            $mpdf = $this->m_pdf->load();
            $pdfFileName='CUST-LIST-PRODUCT'.time().'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');            
            $mpdf->SetProtection(array('print'));            
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter("");
            $mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        else:
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Purchase Inward Report */
    public function purchaseInward(){
        $this->data['pageHeader'] = 'PURCHASE INWARD REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_inward,$this->data);
    }

    public function getPurchaseInward(){
        $data = $this->input->post();
        $inwardData = $this->purchaseReport->getPurchaseInward($data);
        $i=1; $tbody=''; $totalAmt=0; $poNo=''; $tfoot = ''; $totalQty=0; $totalItemPrice=0; $total=0;
        if(!empty($inwardData)){
            foreach($inwardData as $row):
                $totalAmt = ($row->qty * $row->price);
                if(!empty($row->po_prefix) && !empty($row->po_no)){
                    $poNo = getPrefixNumber($row->po_prefix,$row->po_no);
                }
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->grn_date).'</td>
                    <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                    <td>'.$poNo.'</td>
                    <td>'.formatDate($row->po_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->price.'</td>
                    <td>'.$totalAmt.'</td>
                </tr>';
                $totalQty += $row->qty; $totalItemPrice += $row->price; $total += $totalAmt;
            endforeach;
            $tfoot = '<tr>
                    <th colspan="7">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th>'.round($totalItemPrice, 2).'</th>
                    <th>'.round($total, 2).'</th>
                </tr>';
        } else {
            $tbody .= '<tr><td colspan="10">No Data Found</td></tr>';
        }
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }
    
    /* Supplier Wise Item Report  Created By Avruti @08/08/2022 */
    public function supplierWiseItem(){
        $this->data['pageHeader'] = 'SUPPLIER WISE ITEM & ITEM WISE SUPPLIER REPORT';      
        $this->data['partyData'] = $this->party->getSupplierList();
        $this->data['itemData'] = $this->item->getItemLists('2,3');
        $this->load->view($this->supplier_wise_item,$this->data);
    }

 	//Created By Avruti @08/08/2022
    public function getSupplierWiseItem(){
        $data = $this->input->post();

        $purchaseData = $this->purchaseReport->getSupplierWiseItem($data);
        $tbody="";$i=1;
        foreach($purchaseData as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>' . (!empty($row->item_code) ? '[' . $row->item_code . '] ' . $row->item_name : $row->item_name) . '</td>';
                $tbody.='</tr>';
        endforeach;
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
    /* GRN Tracking Report  Created By Avruti @09/08/2022*/
    public function grnTracking(){
        $this->data['pageHeader'] = 'GRN TRACKING REPORT';       
        $this->data['partyData'] = $this->party->getSupplierList();
        $this->load->view($this->grn_tracking,$this->data);
    }

 	//Created By Avruti @09/08/2022
    public function getGrnTracking(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $grnData = $this->purchaseReport->getGrnTracking($data);
            $tbody="";$i=1;
            foreach($grnData as $row):
                $inqDate =""; $approveDate="";
                if(!empty($row->approve_date)){ 
                    $grn_date = new DateTime(date('Y-m-d',strtotime($row->grn_date)));
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $dueDays = $grn_date->diff($appDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $approveDate = $daysDiff.'<br><small>('.formatDate($row->approve_date).')</small>'; 
                }

                if(!empty($row->inspection_date)){
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $qcDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $dueDays = $appDate->diff($qcDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $inqDate = $daysDiff.'<br><small>('.formatDate($row->inspection_date).')</small>'; 
                }
                
                
                
                
                
                /*
                if(!empty($row->inspection_date)){ 
                    $inspectionDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $grnDate = new DateTime(date('Y-m-d',strtotime($row->grn_date)));
                    $dueDays = $inspectionDate->diff($grnDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $inqDate = formatDate($row->inspection_date).'<br>('.$daysDiff.')'; 
                }

                if(!empty($row->approve_date)){
                    $inspectionDate = new DateTime(date('Y-m-d',strtotime($row->inspection_date)));
                    $appDate = new DateTime(date('Y-m-d',strtotime($row->approve_date)));
                    $dueDays = $inspectionDate->diff($appDate);
                    $day = $dueDays->format('%d');
                    $daysDiff = ($day > 0) ? $day.' Days' : 'Same Day';
                    $approveDate = formatDate($row->approve_date).'<br>('.$daysDiff.')'; 
                }*/
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.formatDate($row->grn_date).'</td>
                        <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.floatval($row->qty).'</td>
                        <td>'.$approveDate.'</td>
                        <td>'.$inqDate.'</td>';
                    $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }

	//Created By NYN 01/07/2023
	public function supplierRating(){
		$this->data['pageHeader'] = 'EXTERNAL PROVIDER RATING REPORT';   
        $this->load->view($this->supplier_rating,$this->data);
	}
	
	//Created By NYN 01/07/2023
	public function getSupplierRating(){
		$data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $grnData = $this->purchaseReport->getSupplierRating($data);
			
            $tbody="";$i=1;
            foreach($grnData as $row):
					$data['party_id'] = $row->id;
					$itemData = $this->purchaseReport->getSupplierItemData($data);
				
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
						<td class="text-center">'.$row->party_name.'</td>
						<td class="text-center">'.sprintf('%0.2f',$itemData->recive_qty).'</td>
						<td class="text-center">'.sprintf('%0.2f',$itemData->recive_qty).'</td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center">'.(($itemData->in_recive_qty > 0)?sprintf('%0.2f',$itemData->in_recive_qty):'').'</td>
						<td class="text-center">'.(($itemData->late_recive_qty > 0)?sprintf('%0.2f',$itemData->late_recive_qty):'').'</td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center"></td>';
                    $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
	}

    /* Created By :- Sweta @29-07-2023 */
    public function pendingPO(){
        $this->data['pageHeader'] = 'PENDING PURCHASE ORDER REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->pending_po,$this->data);
    }

    /* Created By :- Sweta @29-07-2023 */
    public function getPendingPO($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData); 
        else:
            $data = $this->input->post(); 
        endif;
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPendingPO($data);
            $tbody="";$i=1;
            foreach($purchaseData as $row):
                    $tbody .= '<tr>
                            <td class="text-center">'.$i++.'</td>
                            <td>'.formatDate($row->po_date).'</td>
                            <td>'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                            <td>'.$row->party_name.'</td>
                            <td>'.$row->item_name.'</td>
                            <td>'.$row->reference_by.'</td>
                            <td>'.floatval($row->qty).' ('.$row->unit_name.')</td>
                            <td>'.$row->price.'</td>
                            <td>'.formatDate($row->delivery_date).'</td>
                        </tr>';
            endforeach;
        endif;
		$logo = base_url('assets/images/logo.png');
		if($data['type'] == 1):             
			$topSectionO ='<table class="table">
							<tr>
								<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:60%">PENDING PURCHASE ORDER REPORT</td>
								<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;"></td>
							</tr>
						</table>';
			$itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
									<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th>#</th>
										<th style="min-width:80px;">Date</th>
										<th style="min-width:80px;">Order No.</th>
										<th style="min-width:100px;">External Provider Name</th>
										<th style="min-width:100px;">Item Description</th>
										<th style="min-width:100px;">Mode of Purchase.</th>
										<th style="min-width:50px;">Order Qty.</th>
										<th style="min-width:50px;">Price</th>
										<th style="min-width:80px;">Delivery Date</th>
									</tr>
									   
									</thead>';                                
			$itemList.='<tbody id="tbodyData">';
			$itemList.=$tbody;
			$itemList.='</tbody>
					</table>';
			$originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';		
			$pdfData = $originalCopy;            
			$mpdf = $this->m_pdf->load();
			$pdfFileName='CUST-LIST-PRODUCT'.time().'.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');            
			$mpdf->SetProtection(array('print'));            
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter("");
			$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
			$mpdf->WriteHTML($pdfData);
			$mpdf->Output($pdfFileName,'I');
		else:
			$this->printJson(['status'=>1, 'tbody'=>$tbody]);
		endif;
    }
}
?>