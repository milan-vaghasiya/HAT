<?php
class StoreReport extends MY_Controller
{
    private $indexPage = "report/store_report/index";
    private $issue_register = "report/store_report/issue_register";
    private $stock_register = "report/store_report/stock_register";
    private $inventory_monitor = "report/store_report/inventory_monitor";
    private $consumable_report = "report/store_report/consumable_report";
    private $fgstock_report = "report/store_report/fgstock_report";
    private $tool_issue_register = "report/store_report/tool_issue_register";
    private $scrap_book = "report/store_report/scrap_book";
    private $item_history = "report/sales_report/item_history";
    private $stock_wise_report = "report/store_report/stock_wise_report";
    private $misplaced_item="report/store_report/misplaced_item";    
    private $rm_specification="report/store_report/rm_specification";
    private $rm_stock_register = "report/store_report/rm_stock_register";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/store_report/floating_menu',[],true);
        $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product','Rejection Scrap','Production Scrap','Credit Note','Debit Note','General Issue', 'Stock Verification', 'Process Movement', 'Production Rejection', 'Production Rejection Scrap', 'Move to Allocation RM Store', 'Move To Received RM Store', 'Move To Packing Area', 'RM Process', 'Short Closed');
    }
	
	public function index(){
		$this->data['pageHeader'] = 'STORE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }
 
    /* ISSUE REGISTER (CONSUMABLE) REPORT */
    public function issueRegister(){
        $this->data['pageHeader'] = 'ISSUE REGISTER RAW MATERIAL REPORT';
        //$this->data['deptData'] = $this->department->getDepartmentList();
        $this->load->view($this->issue_register,$this->data);
    }

    public function getIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getIssueRegister($data);
            $tbody="";$i=1; $tfoot=""; $totalQty=0;

            foreach($issueData as $row):
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->job_number.'</td>
                    <td>'.$row->wo_no.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$row->emp_name.'</td>
                </tr>';
                $totalQty+=abs($row->qty);
            endforeach;
            $tfoot = '<tr>
                    <th colspan="5">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th></th>
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    /* STOCK REGISTER (CONSUMABLE) REPORT */
    public function stockRegister(){
        $this->data['pageHeader'] = 'STOCK REGISTER (CONSUMABLE) REPORT';
        $this->data['item_type'] = 2;
        $this->load->view($this->stock_register,$this->data);
    }

    /* STOCK REGISTER (RAW MATERIAL) REPORT */
    public function stockRegisterRawMaterial(){
        $this->data['pageHeader'] = 'STOCK REGISTER (RAW MATERIAL) REPORT';
        $this->data['item_type'] = 3;
        $this->load->view($this->stock_register,$this->data);
    }

    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if(!empty($data['to_date']))
			$errorMessage['toDate'] = "Required date.";

        if(empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            //$itemData = $this->storeReportModel->getStockRegister($data['item_type']);
            $itemData = $this->accountingReport->getStockRegister($data);
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;
            
            if(!empty($itemData)):
                $type = ($data['item_type'] == 2)? "Consumable":"Raw Material";
                $formate = ($data['item_type'] == 2)? "F ST 05":"F ST 02";
                $thead = '<tr class="text-center"><th colspan="4">Stock Register ('.$type.')</th><th>'.$formate.'(00/01.06.20)</th></tr><tr><th>#</th><th>Item Description</th><th>Receipt Qty.</th><th>Issued Qty.</th><th>Balance Qty.</th></tr>';
                foreach($itemData as $row):
                    $data['item_id'] = $row->id;
                    $receiptQty = $row->rqty; $issuedQty = $row->iqty;
                    $balanceQty = 0;
                    if(!empty($issuedQty) AND !empty($receiptQty)){ $balanceQty = $receiptQty - abs($issuedQty); }
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td class="text-right">'.floatVal($receiptQty).'</td>
                        <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                        <td class="text-right">'.sprintf('%0.2f',$balanceQty).'</td>
                    </tr>';
                endforeach;
            else:
                $tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
            endif;
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody]);
        endif;
    }

    /* INVENTORY MONITORING REPORT */
    public function inventoryMonitor(){
        $this->data['pageHeader'] = 'INVENTORY MONITORING REPORT';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->load->view($this->inventory_monitor,$this->data);
    }

    public function getInventoryMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['to_date']))
			$errorMessage['toDate'] = "Date is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getInventoryMonitor($data);
            $tbody="";$i=1;$opningStock=0;$closingStock=0;$fyOpeningStock=0;$totalOpeningStock=0;$monthlyInward=0;$monthlyCons=0;$inventory=0;$amount=0;$total=0;$totalInventory=0;$totalValue=0;$totalUP=0;
            foreach($itemData as $row):
                if($row->item_type != 1){
                    $lastPurchase = $this->storeReportModel->getLastPurchasePrice($row->id);
					$row->price = $lastPurchase->price;
                }
                
                $data['item_id'] = $row->id;
                $fyOSData = Array();
                $opningStock = (!empty($row->opening_qty)) ? $row->opening_qty : 0;
                $monthlyInward = $row->rqty;
                $monthlyCons = abs($row->iqty);
                $totalOpeningStock = floatval($opningStock);
                $closingStock = ($totalOpeningStock + $monthlyInward - $monthlyCons);
                $total = round(($closingStock * $row->price), 2);
                
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</td>
                    <td>'.floatVal($totalOpeningStock).'</td>
                    <td>'.floatVal(round($monthlyInward,2)).'</td>
                    <td>'.floatVal(round($monthlyCons,2)).'</td>
                    <td>'.floatVal(round($closingStock,2)).'</td>
                    <td>'.number_format($row->price, 2).'</td>
                    <td>'.number_format($total, 2).'</td>
                </tr>';
                $totalInventory += round($row->price,2);
                $totalValue += $total;
                
            endforeach;
            
            $totalUP = (!empty($totalInventory)) ? round(($totalValue / $totalInventory),2) : 0;
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'totalInventory'=>number_format($totalInventory,2), 'totalUP'=>number_format($totalUP,2), 'totalValue'=>number_format($totalValue,2)]);
        endif;
    }
	
   /* Consumable Report UPDATED BY MEGHAVI @23/06/2023*/
    public function consumableReport(){
        $this->data['pageHeader'] = 'CONSUMABLES REPORT';
        $consumableData = $this->storeReportModel->getConsumable();

        $i=1;  $this->data['tbody']='';
        if(!empty($consumableData)){
            foreach($consumableData as $row):        
                $size = (!empty($row->size))? ' / '.$row->size : "";
                $this->data['tbody'] .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->material_grade.$size.'</td>
                    <td>'.$row->make_brand.'</td>
                    <td>'.$row->min_qty.'</td>
                    <td>'.$row->location.'</td>
                    <td>'.$row->description.'</td>
                </tr>';
            endforeach;
        }
        $this->load->view($this->consumable_report,$this->data);
    }

    /* Stock Statement finish producct */
    public function fgStockReport(){
        $this->data['pageHeader'] = 'STOCK STATEMENT REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->fgstock_report,$this->data);
    }
    
	//UPDATED BY MEGHAVI @23/06/2023
    public function getFgStockReport($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post(); //print_r($data);exit;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_type'] = 1;$stockType = '';
            $itemData = $this->accountingReport->getStockRegister($data,$stockType);
            $itemData = $this->accountingReport->getStockRegister($data);
            // if($data['stock_type'] == '0'){$stockType = 'stockQty = 0';}
            // if($data['stock_type'] == 1){$stockType = 'stockQty > 0';}
           
            $thead="";$tbody="";$tfoot='';$i=1;$receiptQty=0;$issuedQty=0;$totalAmt=0; $treceiptQty = 0; $tissuedQty = 0; $tbalanceQty = 0;
            
            if(!empty($itemData)):
                foreach($itemData as $row):  
                    $data['item_id'] = $row->id;
                    $bQty = 0;
                    $receiptQty = $row->rqty; 
                    $issuedQty = $row->iqty;
                    $itmStock = $row->stockQty;
                    if(!empty($row->stockQty)){$bQty = $row->stockQty;}
                    
                    $balanceQty=0;
                    if($row->item_type == 1){ $balanceQty = round($bQty,3); } 
                    else { $balanceQty = round($receiptQty - abs($issuedQty),3); } 
                    
                    //$balanceQty = round($bQty,3);
                    
                    $price = (!empty($row->inrrate))? ($row->price * $row->inrrate) : $row->price;
                    $tamt = ($balanceQty > 0)? round($balanceQty * $price, 2) : 0;
                    $tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->item_code.'</td>
                                <td>'.$row->item_name.'</td>
                                <td>'.$row->party_name.'</td>
                                <td>'.$row->drawing_no.'</td>
                                <td>'.$row->size.'</td>
                                <td>'.$row->material_grade.'</td>
                                <td>'.$row->rev_no.'</td>
                                <td>'.floatVal($balanceQty).'</td>
                                <td>'.$row->description.'</td>

                            </tr>';
                    $totalAmt += $tamt;
                    $treceiptQty += $receiptQty; 
                    $tissuedQty += $issuedQty;
                    $tbalanceQty += $balanceQty;
                endforeach;


                $tfoot = '<tr>
                        <th colspan="6">Total</th>
                        <th class="text-right">' .number_format($tbalanceQty,2). '</th>
                    </tr>';            
            else:
                $tfoot = '<tr>
                        <th colspan="2">Total</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                        <th class="text-right">0</th>
                    </tr>';     
            endif;
            if($data['type'] == 1){

                $thead .= '<tr>
                            <th style="min-width:25px;">#</th>
                            <th style="min-width:80px;">Part No.</th>
                            <th style="min-width:50px;">Part Name</th>
                            <th style="min-width:100px;">Customers Name</th>
                            <th style="min-width:100px;">Drg. No.</th>
                            <th style="min-width:50px;">Size</th>
                            <th style="min-width:80px;">Metal</th>
                            <th style="min-width:50px;">Rev. No.</th>
                            <th style="min-width:50px;">Stock Qty.</th>
                            <th style="min-width:50px;">Remark</th>
                        </tr>';
                
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-left'>STOCK REGISTER - FINISH PRODUCT</th>
                                    </tr>
                               </table> <hr>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='SalesOrderSummary'.time().'.pdf';
                $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
                $stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
                $mpdf->WriteHTML($stylesheet,1);
                $mpdf->SetDisplayMode('fullpage');
                //$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
               
                $mpdf->SetProtection(array('print'));
                
                $mpdf->SetHTMLHeader($htmlHeader);
                $mpdf->SetHTMLFooter("");
                $mpdf->AddPage('L','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-L');
                $mpdf->WriteHTML($pdfData);
                $mpdf->Output($pdfFileName,'I');
            }else{
                $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
            }
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }
	
    /*TOOL ISSUE REGISTER (CONSUMABLE) REPORT */  
    public function toolissueRegister(){
        $this->data['pageHeader'] = 'TOOL ISSUE REGISTER REPORT';
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['jobCardData'] = $this->storeReportModel->getJobcardList();
        $this->load->view($this->tool_issue_register,$this->data);
        
    } 
    
	public function getToolIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getToolIssueRegister($data); 
			
            $tbody=""; $i=1; $total_amount = 0;$total_qty = 0;
            foreach($issueData as $row):
                $data['item_id']=$row->dispatch_item_id;
                $prs=$this->purchaseReport->getItemLastPurchasePrice($data);
                $price=$row->price;
                if(!empty($prs))
                {
                    $price=$prs->price;
                }
                $amount = round((floatVal($row->dispatch_qty) * floatval($price)),2);
				$partCode=''; $jobNo = '';
				if(!empty($row->job_no)){
                    $jobNo = getPrefixNumber($row->job_prefix,$row->job_no);
                    $pcode = $this->item->getItem($row->product_id)->item_code;
                    $partCode = '['.$pcode.'] ';
                }
                
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.formatDate($row->dispatch_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->name.'</td>
                    <td>'.$partCode.$jobNo.'</td>
                    <td>'.floatVal($row->dispatch_qty).'</td>
                    <td>'.floatval($price).(!empty($prs)?' (P) ':' (D) ').'</td>
                    <td>'.$amount.'</td>
                </tr>';
				$total_amount += $amount;$total_qty += $row->dispatch_qty;
            endforeach;
			$avgPrice = ($total_qty > 0) ? round((floatVal($total_amount / $total_qty)),2) : 0;
			$thead = '<tr class="text-center" id="theadData">
                    <th colspan="5">Tool Issue Register (F PR 13 (00/01.06.2020))</th>
                    <th>'.floatVal($total_qty).'</th>
                    <th>'.$avgPrice.'</th>
                    <th>'.moneyFormatIndia($total_amount).'</th>
                </tr>
				<tr>  
					<th>#</th>
					<th>Date</th>
					<th>Product</th>
					<th>Department</th>
					<th>Job Card</th>
					<th>QTY</th>
					<th>Price</th>
					<th>Amount</th>
				</tr>';
			$tfoot = '<tr>
                    <th colspan="5">Total</th>
                    <th>'.floatVal($total_qty).'</th>
                    <th>'.$avgPrice.'</th>
                    <th>'.moneyFormatIndia($total_amount).'</th>
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot, 'thead'=>$thead]);
        endif;
    }

    /**
     * Created By Mansee @ 09-12-2021
     */
    public function scrapBook(){
        $this->data['pageHeader'] = 'SCRAP BOOK REPORT';
        $this->data['scrapGroup']=$this->materialGrade->getScrapList();
        $this->data['materialGrade']=$this->materialGrade->getMaterialGrades();        
        $this->load->view($this->scrap_book,$this->data);
    }

    public function getScrapReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getRowMaterialScrapQty($data);
            $tbody="";$i=1;
            $totalQty = 0;
            $totalPrice = 0;
            $netValuation = 0;
            $avgPrice = 0;
            if(!empty($itemData)):
                foreach($itemData as $row):
                    $totalQty += $row->scrap_qty;
                    $totalPrice += $row->price;
                                    
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->scrap_qty.'</td>
                        <td>'.$row->price.'</td>
                        <td>'.($row->price*$row->scrap_qty).'</td>                    
                    </tr>';
                    $netValuation += round(($row->price*$row->scrap_qty),2);
                endforeach;
                $avgPrice = (!empty($netValuation))?round(($netValuation / $totalQty),2):0;
            endif;           
            
            $this->printJson(['status'=>1, 'tbody'=>$tbody,'avg_price'=>$avgPrice,'net_valuation'=>$netValuation,'total_qty'=>$totalQty]);
        endif;
    }
    
    /* ITEM HISTORY Report */
    public function itemHistory(){
        $this->data['pageHeader'] = 'ITEM HISTORY REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->data['locationData'] = $this->store->getProcessStoreLocationList();
        $this->load->view($this->item_history, $this->data);
    }

    public function getItemList(){
        $item_type = $this->input->post('item_type');
        $itemData = $this->item->getItemListForSelect($item_type);

        $item="";
        $item.='<option value="">Select Item</option>';
        foreach($itemData as $row):
            $item.= '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
        endforeach;
        $this->printJson(['status' => 1, 'itemData' => $item]);
    }

    public function getItemHistory(){
        $data = $this->input->post();
        //print_r(count($this->data['stockTypes']));exit;
        $itemData = $this->salesReportModel->getItemHistory($data['item_id'], $data['location_id']);

        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            $credit=0;$debit=0;
            $transType = ($row->ref_type >= 0)? $this->data['stockTypes'][$row->ref_type] : "Opening Stock";
            if($row->trans_type == 1){ $credit = abs($row->qty);$tbalance +=abs($row->qty); } else { $debit = abs($row->qty);$tbalance -=abs($row->qty); }
            if($transType == 'Material Issue'){$row->ref_no = $row->batch_no;}
            
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$transType.' [ '.$row->location.' ]</td>
                <td>'.$row->ref_no.'</td>
                <td>'.formatDate($row->ref_date).'</td>
                <td>'.floatVal(round($credit)).'</td>
                <td>'.floatVal(round($debit)).'</td>
                <td>'.floatVal(round($tbalance)).'</td>
            </tr>';
            $tcredit += $credit; $tdebit += $debit;// $tbalance += abs($tcredit) - abs($tdebit);
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,2)). '</th>
                <th>' .floatVal(round($tdebit,2)). '</th>
                <th>' .floatVal(round($tbalance,2)). '</th>
            </tr>';

        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    /* Store Wise Stock Statement 
        Avruti @21-04-2022
    */
    public function storeWiseStockReport(){
        $this->data['pageHeader'] = 'STORE WISE STOCK STATEMENT REPORT';
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->load->view($this->stock_wise_report,$this->data);
    }

    public function getStoreWiseStockReport(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $fgData = $this->storeReportModel->getStoreWiseStockReport($data); 
            $tbody="";$i=1;
            foreach($fgData as $row):
                if($row->qty > 0):
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->qty.'</td>
                    </tr>';
                endif;
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* MISPLACED ITEM HISTORY Report */
    public function misplacedItem(){
        $this->data['pageHeader'] = 'MISPLACED ITEM HISTORY REPORT';
		$this->data['headData']->pageTitle = "MISPLACED ITEM HISTORY REPORT";
        $this->data['itemData'] = $this->storeReportModel->getMisplacedItemList();
        $this->load->view($this->misplaced_item, $this->data);
    }

    public function getMisplacedItemHistory(){
        $data = $this->input->post();
        $itemData = $this->storeReportModel->getMisplacedItemHistory($data);
        $i=1; $tbody =""; 
        foreach($itemData as $row):

            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$row->item_name.'</td>
                <td>'.date("d-m-Y H:i:s",strtotime($row->created_at)).'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->batch_no.'</td>
                <td>'.$row->ref_batch.'</td>
                <td>'.floatVal($row->qty).'</td>
            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }
    
    /*RAW MATEREAL SPECIFICATION*/ 
    public function rawMaterialSpecification(){
        $this->data['pageHeader'] = 'RAW MATERIAL SPECIFICATION REPORT';   
        $materialData = $this->materialGrade->getMaterialGrades(); 
        $i=1;  $tbody = '';

            if(!empty($materialData)){
                foreach($materialData as $row):  
                    $tbody .= '<tr>
                    <td style="min-width:5px;">'.$i++.'</td>
                    <td style="min-width:150px;">'.$row->material_grade.'</td>
                    <td style="min-width:150px;">'.$row->standard.'</td>
                    <td style="min-width:10px;">Min<hr>Max</td>';
                    $specifiData = $this->materialGrade->getMaterialSpecification($row->id);
                    foreach($specifiData as $sd):
                        $tbody .= '<td style="width:30px;">'.$sd->min_value.'<hr style="padding:0rem;">'.$sd->max_value.'</td>';
                    endforeach;
                    $tbody .= '<td style="min-width:150px;">'.$row->remark.'</td>
                    </tr>';
                endforeach;
            }
        $this->data['tbody'] = $tbody;
        $this->load->view($this->rm_specification,$this->data);
    }

    /* Raw Material Stock Register */
    public function rawMaterialStockRegister(){
        $this->data['pageHeader'] = 'RAW MATERIAL STOCK REGISTER';
        $this->data['itemList'] = $this->item->getItemListForSelect(3);
        $this->load->view($this->rm_stock_register,$this->data);
    }

    public function getRmStockRegisterData($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;
        $itemData = $this->item->getItem($data['item_id']);
        $stockData = $this->storeReportModel->getRmStockRegisterData($data);
        $resultData = $stockData['resultData'];
        $opQty = $stockData['op_qty'];

        $thead = "";

        if(!empty($jsonData)):
            $thead .= '<tr>
                <th colspan="6" class="text-center" >RAW MATERIAL STOCK REGISTER</th>
                <th colspan="2" class="text-center">F/STR/01 (00/01.01.16)</th>
            </tr>';
            $thead .= '<tr>
                <th colspan="6" class="text-center" >Item : ['.$itemData->item_code.'] '.$itemData->item_name.'</th>
                <th colspan="2" class="text-center">Month : '.(date("M-Y",strtotime($data['month']))).'</th>
            </tr>';
        else:
            $thead .= '<tr>
                <th colspan="5" class="text-center" >Item : ['.$itemData->item_code.'] '.$itemData->item_name.'</th>
                <th class="text-center">Month : '.(date("M-Y",strtotime($data['month']))).'</th>
            </tr>';
        endif;
        $thead .= '<tr>
            <th class="text-center">#</th>
            <th class="text-center">Date</th>
            <th class="text-right">Opening</th>
            <th class="text-right">Recd. Qty</th>
            <th class="text-right">Issue Qty</th>
            <th class="text-right">Closing Qty</th>';
        if(!empty($jsonData)):
            $thead .= '<th class="text-center">Remarks</th>';
            $thead .= '<th class="text-center">Sign.</th>';
        endif;
        $thead .= '</tr>';

        $html = "";$closingQty = 0; $i=1; $firstRow = $lastRow = false; 
        foreach($resultData as $row):
            $closingQty += ($i==1)?($opQty + $row->in_qty - $row->out_qty):($row->in_qty - $row->out_qty);
            if($row->ref_date == date("Y-m-01",strtotime($data['month']))):
                $firstRow = true;
                $opq = floatVal($opQty);
            else:
                $i = $i + 1;
                $opq = '';
            endif;

            if($row->ref_date == date("Y-m-t",strtotime($data['month']))):
                $lastRow = true;
            endif;

            $html .= '<tr>
                <td class="text-center">'.++$i.'</td>
                <td class="text-center">'.date("d-m-Y",strtotime($row->ref_date)).'</td>
                <td class="text-right">'.$opq.'</td>
                <td class="text-right">'.floatVal($row->in_qty).'</td>
                <td class="text-right">'.floatVal($row->out_qty).'</td>
                <td class="text-right">'.floatVal($closingQty).'</td>';
            if(!empty($jsonData)):
                $html .= '<td class="text-center"></td>';
                $html .= '<td class="text-center"></td>';
            endif;
            $html .= '</tr>';
        endforeach;

        $tbody = '';
        if($firstRow == false):
            $tbody .= '<tr>
                <td class="text-center">1</td>
                <td class="text-center">'.date("01-m-Y",strtotime($data['month'])).'</td>
                <td class="text-right">'.floatVal($opQty).'</td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right">'.floatVal($opQty).'</td>';
            if(!empty($jsonData)):
                $tbody .= '<td class="text-center"></td>';
                $tbody .= '<td class="text-center"></td>';
            endif;
            $tbody .= '</tr>';
        endif;

        $tbody .= $html;

        if($lastRow == false):
            $closingQty = (empty($closingQty))?$opQty:$closingQty;
            $tbody .= '<tr>
                <td class="text-center">'.++$i.'</td>
                <td class="text-center">'.date("t-m-Y",strtotime($data['month'])).'</td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right"></td>
                <td class="text-right">'.floatVal($closingQty).'</td>';
            if(!empty($jsonData)):
                $tbody .= '<td class="text-center"></td>';
                $tbody .= '<td class="text-center"></td>';
            endif;
            $tbody .= '</tr>';
        endif;

        if(!empty($jsonData)):
            $pdfData = '<table id="reportTable" class="table item-list-bb">
            <thead class="thead-info" id="theadData">'.$thead.'</thead>
            <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
            </table>';

            $htmlHeader = "<table class='table item-list-bb' style='margin-bottom:0px'>
                                <tr>
                                    <th class='text-center' style='width:85%'><h4>AKSHAR ENGINEERS</h4></th>
                                    <th><img src='assets/images/logo.png' class='img' style='height:60px'></th>
                                </tr>
                            </table>";
            $mpdf = $this->m_pdf->load();
            $pdfFileName='rm-register-'.time().'.pdf';
            $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
            $stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');
            //$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
            
            $mpdf->SetProtection(array('print'));
            
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter("");
            $mpdf->AddPage('L','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        else:
            $this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody]);
        endif;
    }
}
?>