<?php
class SalesReport extends MY_Controller
{
    private $indexPage = "report/sales_report/index";
    private $order_monitor = "report/sales_report/order_monitor";
    private $dispatch_plan = "report/sales_report/dispatch_plan";
    private $packing_report = "report/sales_report/packing_report";
    private $dispatch_summary = "report/sales_report/dispatch_summary";
    private $item_history = "report/sales_report/item_history";
    private $sales_enquiry = "report/sales_report/sales_enquiry";
    private $monthlySales = "report/sales_report/monthly_sales";
    private $dispatch_plan_summary = "report/sales_report/dispatch_plan_summary";
    private $enquiry_monitoring = "report/sales_report/enquiry_monitoring";
    private $sales_target = "report/sales_report/sales_target";
    private $sales_order_summary = "report/sales_report/sales_order_summary";
    private $custom_enquiry_register = "report/sales_report/custom_enquiry_register";
    private $quotation_monitoring = "report/sales_report/quotation_monitoring";
    private $packing_history = "report/sales_report/packing_history";
    private $satisfaction_feedback_summary = "report/sales_report/satisfaction_feedback_summary";
    private$customer_complaints = "report/sales_report/customer_complaints";
    private $list_of_customers  = "report/sales_report/list_of_customers";
    private $approved_product = "report/sales_report/approved_product";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Report";
		$this->data['headData']->controller = "reports/salesReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/sales_report/floating_menu',[],true);    
        $this->data['monthData'] = $this->getMonthListFY();
	}
	
	public function index(){
		$this->data['pageHeader'] = 'SALES REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* Customer's Order Monitoring */
	public function orderMonitor(){
        $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REGISTER';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->order_monitor,$this->data);
    }

    public function getOrderMonitor($jsonData=""){
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
            $orderData = $this->salesReportModel->getOrderMonitorNew($data);
            // print_r($this->db->last_query());exit;
            $tbody="";$thead="";$i=1;$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            $total=0;
            $thead =  '<tr class="text-center">
                            <th colspan="15"><h4>CUSTOMER ORDER MONITORING REGISTER</h4></th>
                            <th colspan="3">F/MKT/03 (00/01.01.16)</th>
                        </tr>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">OA Date</th>
                            <th rowspan="2">P.O. Date</th>
                            <th rowspan="2">P.O. No</th>
                            <th rowspan="2">Customer Code</th>
                            <th rowspan="2">Part No.</th>
                            <th rowspan="2">W.O. No.</th>
                            <th rowspan="2">Metal</th>
                            <th rowspan="2">Actual Order Qty.</th>
                            <th rowspan="2">Exp. Delivery Date</th>
                            <th rowspan="2">RM Size</th>
                            <th rowspan="2">Wtg./Pc</th>
                            <th rowspan="2">Reqd. Wtg.</th>
                            <th colspan="3">Disp. Details</th>
                            <th rowspan="2">Total</th>
                            <th rowspan="2">Disp. Wtg</th>
                        </tr>
                        <tr>
                            <th>Actual Delivery Date</th>
                            <th>Actual Delivered Qty.</th>
                            <th>Total Qty. Delivered</th>										
                        </tr>';
            $trans_id = "";
            foreach($orderData as $row):
                $data['id'] = $row->id;
                $data['sales_type'] = $row->sales_type;
                $rp = 0;
                $total = $row->qty * $row->wt_pcs;
                $tbody .= '<tr>';
                if($trans_id != $row->id){
                    $tbody .= '
                        <td style="width:10px;">'.$i++.'</td>
                        <td style="width:120px;">'.formatDate($row->trans_date).'</td>
                        <td style="width:120px;">'.formatDate($row->doc_date).'</td>
                        <td style="width:100px;">'.$row->doc_no.'/'.$row->id.'</td>
                        <td style="width:100px;">'.$row->party_code.'</td>
                        <td style="width:350px;">['.$row->item_code.']<br>'.$row->item_name.'</td>
                        <td style="width:100px;">'.$row->grn_data.'</td>
                        <td style="width:100px;">'.$row->material_grade.'</td>
                        <td style="width:100px;">'.floatVal($row->qty).'</td>
                        <td style="width:100px;">'.formatDate($row->cod_date).'</td>
                        <td style="width:100px;">'.$row->size.'</td>
                        <td style="width:100px;">'.$row->wt_pcs.'</td>
                        <td style="width:100px;">'.$row->wt_pcs.'</td>';
                }else{
                    $tbody .= $blankInTd;
                }
                $trans_id = $row->id;
                $j=1;$dqty=0; $totalQty=0; $dPr=0; $disp_wtg=0;
                $dqty = (!empty($row->dqty)) ? $row->dqty : 0;
                $totalQty += (!empty($dqty)) ? $dqty : 0;
                $qtyD = ($row->qty) - $totalQty;
                $disp_wtg = $dqty * $row->wt_pcs;
                $dPr = (( $qtyD * 100 ) / $row->qty);
                $tbody.='<td style="width:100px;">'.formatDate($row->dc_delivery_date).'</td>
                        <td style="width:100px;">'.floatval($dqty).'</td>
                        <td style="width:100px;">'.floatval($totalQty).'</td>';
                $tbody .= '<td style="width:100px;">'.floatval($total).'</td> <td>'.floatval($disp_wtg).'</td>';
                 
                $tbody .= '</tr>';
            endforeach;
            if($data['type'] == 1){

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
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
            //$this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
        endif;
    }

    /* Customer's Order Monitoring */
	public function orderMonitor_old(){
        $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REGISTER';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->order_monitor,$this->data);
    }

    public function getOrderMonitor_old($jsonData=""){
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
            $orderData = $this->salesReportModel->getOrderMonitor($data);
            $tbody="";$i=1;$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($orderData as $row):
                $data['id'] = $row->id;
                $data['sales_type'] = $row->sales_type;
                $invoiceData = $this->salesReportModel->getInvoiceData($data);
                $invoiceCount = count($invoiceData);
                $rp = 0;
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                    <td>'.formatDate($row->doc_date).'</td>
                    <td>'.$row->doc_no.'</td>
                    <td>'.$row->party_code.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.formatDate($row->cod_date).'</td>
                    <td>'.$row->drg_rev_no.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->emp_name.'</td>';

                    if($invoiceCount > 0):
                        $j=1;$dqty=0; $totalQty=0; $dPr=0;
                        foreach($invoiceData as $invRow):
                            $dqty = $this->salesReportModel->getDeliveredQty($row->item_id,$invRow->trans_main_id)->dqty;
                            $totalQty += $dqty;
                            $qtyD = ($row->qty) - $totalQty;
                            $dPr = (( $qtyD * 100 ) / $row->qty);
                            $schInvNo = (!empty($invRow->inv_no))?getPrefixNumber($invRow->inv_prefix,$invRow->inv_no):getPrefixNumber($invRow->trans_prefix,$invRow->trans_no);
                            $tbody.='<td>'.$schInvNo.'</td>
                                    <td>'.formatDate($invRow->trans_date).'</td>
                                    <td>'.floatval($dqty).'</td>
                                    <td>'.floatval($totalQty).'</td>
                                    <td>'.$qtyD.'</td>
                                    <td>'.number_format($dPr,2).'%</td>';
                            if($j != $invoiceCount){$tbody.='</tr><tr>'.$blankInTd; }
                            //if($j != $invoiceCount){$tbody.='</tr><tr>'; }
                            $j++;
                        endforeach;
                    else:
                        $tbody.='<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>';
                    endif;
                $tbody .= '</tr>';
            endforeach;
            if($data['type'] == 1){
                $thead .= '<tr>
                            <th >#</th>
                            <th >OA No.</th>
                            <th >P.O. Date</th>
                            <th >P.O. No</th>
                            <th >Customer Code</th>
                            <th>Part No.</th>
                            <th >Actual Order Qty.</th>
                            <th style="min-width:80px;">Exp. Delivery Date</th>
                            <th >PPAP Level</th>
                            <th style="min-width:80px;">Entry Date</th>
                            <th >Entry by</th>
                            <th >Invoice No.</th>
                            <th style="min-width:80px;">Actual Delivery Date</th>
                            <th >Actual Delivered Qty.</th>
                            <th >Total Qty. Delivered</th>
                            <th >Qty. Deviation</th>
                            <th >Deviation PR(%)</th>
                        </tr>';
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-center'>CUSTOMER ORDER MONITORING REGISTER</th>
                                        <th class='text-right'>F/MKT/03 (00/01.01.16)</th>
                                    </tr>
                                </table> <hr>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
                $this->printJson(['status'=>1, 'tbody'=>$tbody]);
            }
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /*   Dispatch Plan Report    */
    public function dispatchPlan()
    {
        $this->data['pageHeader'] = 'DISPATCH PLAN REPORT';
        $this->load->view($this->dispatch_plan, $this->data);
    }

    public function getDispatchPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $orderData = $this->salesReportModel->getDispatchPlan($data);
            $tbody = "";$i = 1;$toq=0;$tov=0;$wipq=0;$tpq=0;$tpv=0;$tdq=0;$tdv=0;$tpckq=0;$tpackv=0;$pq=0;$pv=0;
            $used_qty=Array();
            foreach ($orderData as $row) :
                $data['trans_main_id'] = $row->trans_main_id;
                $data['item_id'] = $row->item_id;
                //$itmData = $this->item->getItem($row->item_id);
                $price=0;
                if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$price=$inr[0]->inrrate*$row->price;}
                }
                else{$price=$row->price;}
				
				if(!isset($used_qty[$row->item_id])){$used_qty[$row->item_id] = 0;}
                $pendingQty = $row->qty - $row->dispatch_qty;
				$pckQty=0;$packingQty = 0;
				
				$pckQty = $row->packingQty - $used_qty[$row->item_id];
				if($pckQty > 0){if($pckQty > $pendingQty){$pckQty=$pendingQty;}}else{$pckQty=0;}
				
				if($pckQty > 0):
					$packingQty = $pckQty;
					$used_qty[$row->item_id] += $pckQty;
				endif;
                $wipQty = $this->salesReportModel->getWIPQtyForDispatchPlan($data);
                
                $planQty = $row->qty - $row->dispatch_qty - $packingQty;
				if($planQty < 0 ){$planQty = 0;}
                
				$jobData = new StdClass;
				$jobData = $this->salesReportModel->getJobcardBySO($row->so_id,$row->item_id);
				$del_date = formatDate($row->trans_date);
				if(!empty($jobData)){$del_date = formatDate($jobData->delivery_date);}
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td>' . $del_date . '</td>
                    <td>' . floatVal($price) . '</td>
                    <td>' . floatVal($row->qty) . '</td>
                    <td>' . floatVal($row->qty * $price) . '</td>
                    <td>' . floatVal($wipQty[0]->qty) . '</td>
                    <td>' . floatVal($planQty) . '</td>
                    <td>' . floatVal($planQty * $price) . '</td>
                    <td>' . floatVal($row->dispatch_qty) . '</td>
                    <td>' . floatVal($row->dispatch_qty * $price) . '</td>
                    <td>' . floatVal($packingQty) . '</td>
                    <td>' . floatVal($packingQty * $price) . '</td>
                    <td>' . floatVal($pendingQty) . '</td>
                    <td>' . floatval($pendingQty * $price) . '</td>';
                $tbody .= '</tr>';
				$toq+=floatVal($row->qty);$tov+=floatVal($row->qty * $price);$wipq+=floatVal($wipQty[0]->qty);
				$tpq+=floatVal($planQty);$tpv+=floatVal($planQty * $price);
				$tdq+=floatVal($row->dispatch_qty);$tdv+=floatVal($row->dispatch_qty * $price);
				$tpckq+=floatVal($packingQty);$tpackv+=floatVal($packingQty * $price);
				$pq+=floatVal($pendingQty);$pv+=floatval($pendingQty * $price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="7">TOTAL</th>
						<th>' . $toq . '</th>
						<th>' . $tov . '</th>
						<th>' . $wipq . '</th>
						<th>' . $tpq . '</th>
						<th>' . $tpv . '</th>
						<th>' . $tdq . '</th>
						<th>' . $tdv . '</th>
						<th>' . $tpckq . '</th>
						<th>' . $tpackv . '</th>
						<th>' . $pq . '</th>
						<th>' . $pv . '</th>
					</tr>';

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }


    /*   Dispatch Summary Report */
    public function dispatchSummary()
    {
        $this->data['pageHeader'] = 'Customer wise Dispatch Report';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_summary, $this->data);
    }
    
    public function getPartyItems(){
        $party_id = $this->input->post('party_id');
        $itemData = $this->item->getPartyItemList($party_id);
        $partyItems='';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        $this->printJson(['status'=>1,'partyItems'=>$partyItems]);
    }
    
    public function getDispatchSummary($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $dispatchData = $this->salesReportModel->getDispatchSummary($data);
        $i=1; $tbody =""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            $schInvNo = (!empty($row->inv_no))?getPrefixNumber($row->inv_prefix,$row->inv_no):getPrefixNumber($row->trans_prefix,$row->trans_no);
            $row->price = (!empty($row->inv_price))?$row->inv_price:$row->price;
            $amt = floatVal(round($row->qty * $row->price,2));
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>' . (!empty($row->so_prefix && $row->so_no) ? (getPrefixNumber($row->so_prefix,$row->so_no)) : "") . '</td>
                <td>' . (!empty($row->cust_po_no) ? ($row->cust_po_no) : "") . '</td>
                <td>[' . $row->party_code.']' .$row->party_name. '</td>
                <td>[' . $row->item_code . '] '.$row->item_name.'</td>
                <td>' . $schInvNo . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>'.floatVal($row->qty).'</td>
                <td>'.floatVal($row->price).'</td>
                <td>'.$amt.'</td>
            </tr>';
            $tqty += $row->qty; $tamt += $amt;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="7">Total</th>
                <th>' .floatVal($tqty). '</th>
                <th></th>
                <th>' .moneyFormatIndia($tamt). '</th>
            </tr>';

        if($data['type'] == 1){
            $thead .= '<tr>
                            <th style="min-width:25px;">#</th>
                            <th style="min-width:100px;">S.O. No.</th>
                            <th style="min-width:100px;">Cust P.O. No.</th>
                            <th style="min-width:100px;">Customer</th>
                            <th style="min-width:100px;">Part</th>
                            <th style="min-width:100px;">Inv./Ch. No.</th>
                            <th style="min-width:100px;">Dispatch date</th>
                            <th style="min-width:50px;">Quantity</th>
                            <th style="min-width:50px;">Price</th>
                            <th style="min-width:50px;">Total Amount</th>
                       </tr>';
            $pdfData = '<table id="reportTable" class="table item-list-bb">
            <thead class="thead-info" id="theadData">'.$thead.'</thead>
            <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
            <tfoot class="tfoot-info" id="tfootData">'.$tfoot.'</tfoot>
            </table>';

            $htmlHeader = "<table>
                                <tr>
                                    <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                    <th class='text-left'>CUSTOMER WISE DISPATCH REPORT</th>
                                </tr>
                            </table> <hr>";
            $mpdf = $this->m_pdf->load();
            $pdfFileName='CustOrdReg'.time().'.pdf';
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
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot' => $tfoot]);
        }
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot' => $tfoot]);
    }
    
    /* ITEM HISTORY Report */
    public function itemHistory()
    {
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
            $item.= '<option value="'.$row->id.'">'.(!empty($row->item_code)? $row->item_code:$row->item_name).'</option>';
        endforeach;
        $this->printJson(['status' => 1, 'itemData' => $item]);
    }

    public function getItemHistory(){
        $data = $this->input->post();

        $itemData = $this->salesReportModel->getItemHistory($data['item_id'], $data['location_id']);

        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            $credit=0;$debit=0;
            $transType = ($row->ref_type >= 0)?$this->data['stockTypes'][$row->ref_type] : "Opening Stock";
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
    
    public function salesEnquiry()
    {
        $this->data['pageHeader'] = 'Regreated Inquiry';
        $this->data['resonData'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->sales_enquiry, $this->data);
    }
	
    public function getSalesEnquiry($jsonData=""){
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
            $enquiryData = $this->salesReportModel->getSalesEnquiry($data);
            $tbody=''; $i=1;
            if(!empty($enquiryData)):
                foreach($enquiryData as $row):
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                        <td>' . formatDate($row->trans_date) . '</td>
                        <td>[' . $row->party_code.']' .$row->party_name. '</td>
                        <td>['.$row->item_code.'] ' . $row->item_name . '</td>
                        <td>' . $row->reason . '</td>
                        <td>'.floatVal($row->qty).'</td>
                    </tr>';
                endforeach;
            else:

            endif;

            if($data['type'] == 1){
                $thead .= '<tr>
                            <th>#</th>
                            <th>Enq. No.</th>
                            <th>Enq. Date</th>
                            <th>Customer Name</th>
                            <th>Item Name</th>
                            <th>Reason</th>
                            <th>Qty.</th>
                          </tr>';
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-left' style='width:55%'>REGREATED INQUIRY</th>
                                    </tr>
                                </table> <hr>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
                $this->printJson(['status'=>1, 'tbody'=>$tbody]);
            }
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Monthly Sales Reports */
    public function monthlySales()
    {
        $this->data['pageHeader'] = 'MONTHLY SALES';
        $this->data['partyList'] = $this->party->getCustomerList();  
        $this->data['productList']=$this->item->getItemLists(1);
        $this->load->view($this->monthlySales, $this->data);
    }

    public function getMonthlySalesData($jsonData="")
    {
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $salesData = $this->salesReportModel->getSalesData($data);
            $tbody=""; $i=1; $tfoot=""; $totalTaxAmt=0; $totalGstAmt=0; $totalDiscAmt=0; $TotalNetAmt=0;
            
            foreach ($salesData as $row) :
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . $row->party_name . '</td>
                    <td>' . floatVal($row->taxable_amount) . '</td>
                    <td>' . floatVal($row->gst_amount) . '</td>
                    <td>' . floatVal($row->disc_amount) . '</td>
                    <td>' . floatVal($row->net_amount) . '</td>
                </tr>';
                $totalTaxAmt += floatVal($row->taxable_amount);
                $totalGstAmt += floatVal($row->gst_amount);
                $totalDiscAmt += floatVal($row->disc_amount);
                $TotalNetAmt += floatVal($row->net_amount);
            endforeach;
            $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal($totalTaxAmt). '</th>
                <th>' .floatVal($totalGstAmt). '</th>
                <th>' .floatVal($totalDiscAmt). '</th>
                <th>' .floatVal($TotalNetAmt). '</th>
            </tr>';

            if($data['type'] == 1){
                $thead .= '<tr>
                            <th style="min-width:25px;">#</th>
                            <th style="min-width:80px;">Invoice Date</th>
                            <th style="min-width:50px;">Invoice No</th>
                            <th style="min-width:100px;">Party Name</th>
                            <th style="min-width:100px;">Taxable Amount</th>
                            <th style="min-width:50px;">GST</th>
                            <th style="min-width:80px;">Discount</th>
                            <th style="min-width:50px;">Net Amount</th>
                           </tr>';
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                <tfoot class="tfoot-info" id="tfootData">'.$tfoot.'</tfoot>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-left' style='width:55%'>MONTHLY SALES</th>
                                    </tr>
                                </table> <hr>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
                $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
            }
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function dispatchPlanSummary(){
        $this->data['pageHeader'] = 'Monthly Order Summary';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_plan_summary, $this->data);
    }

    public function getDispatchPlanSummary(){
        $data = $this->input->post();
        
        $dispatchData = $this->salesReportModel->getDispatchPlanSummary($data);
        $i=1; $tbody =""; $thead=""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            //if($row->qty >= $row->dispatch_qty):
                $qty = $row->qty;$item_price=0;
                if($row->currency != 'INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                $amt = round(($qty * $item_price),2);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>[' . $row->party_code.']' .$row->party_name. '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->doc_no . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$qty).'</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$item_price).'</td>
                    <td class="text-right">'.sprintf("%1\$.2f",$amt).'</td>
                </tr>';
                $tqty += $qty; $tamt += $amt;
            //endif;
        endforeach;
        $thead .= '<tr class="thead-info">
                <th colspan="6">Monthly Order Summary</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tqty). '</th>
                <th></th>
                <th class="text-right">' .sprintf("%1\$.2f",$tamt). '</th>
            </tr>
            <tr>
                <th style="min-width:25px;">#</th>
                <th style="min-width:100px;">Customer</th>
                <th style="min-width:100px;">Part</th>
                <th style="min-width:100px;">Cust. PO. No.</th>
                <th style="min-width:100px;">SO. No.</th>
                <th style="min-width:100px;">Delivery date</th>
                <th class="text-right" style="min-width:50px;">Quantity</th>
                <th class="text-right" style="min-width:50px;">Price</th>
                <th class="text-right" style="min-width:50px;">Total Amount</th>
            </tr>';
        $tfoot .= '<tr class="thead-info">
                <th colspan="6">Total</th>
                <th class="text-right">' .sprintf("%1\$.2f",$tqty). '</th>
                <th></th>
                <th class="text-right">' .sprintf("%1\$.2f",$tamt). '</th>
            </tr>';

            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead, 'tfoot' => $tfoot]);
    }
    /*   Packing Report    */
    public function packingReport()
    {
        $this->data['pageHeader'] = 'PACKING REPORT';
        $this->load->view($this->packing_report, $this->data);
    }

    public function getPackingPlan_old()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
				if(!isset($used_qty[$row->item_id]))
				{
					$used_qty[$row->item_id] = 0;$data['item_id'] = $row->item_id;
					$dispatchData=$this->salesReportModel->getDispatchMaterial($data);
					if(!empty($dispatchData)){$used_qty[$row->item_id]=$q=$dispatchData->dispatch_qty;}
				}
                $item_price=0;$stockQty = 0; $dispatch_qty=$used_qty[$row->item_id];
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				if($dispatch_qty > 0):
					if($dispatch_qty > $row->packing_qty){$dispatch_qty = $row->packing_qty;}
					$used_qty[$row->item_id] -= $dispatch_qty;
				endif;
				
                $stockQty = $row->packing_qty - $dispatch_qty;
                $withoutPacking = $row->totalStock - $row->packing_qty;
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->packing_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty + $stockQty)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal(floatVal($dispatch_qty + $stockQty) * $item_price)) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($dispatch_qty + $stockQty);$tpackv+=floatVal(floatVal($dispatch_qty + $stockQty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $item_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="5">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function getPackingPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
                $item_price=0;$stockQty = 0; $dispatch_qty=0;$dispatch_price=0;$data['item_id']=$row->item_id;$disc_amt=0;
				$dispatchData=$this->salesReportModel->getDispatchOnPacking($data);
				//print_r($dispatchData);
				if(!empty($dispatchData))
				{
					$dispatch_qty=$dispatchData->dispatch_qty;
					//$dispatch_price=(!empty($dispatchData->dispatch_price)) ? round(($dispatch_qty/$dispatchData->dispatch_price),2) : 0;
					$dispatch_price = round($dispatchData->dispatch_price,2);
					$disc_amt=$dispatchData->disc_amt;
				}
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				
                //$stockQty = $row->packing_qty - $dispatch_qty;
                $stockQty = 0;
                $stockData = $this->salesReportModel->getRFDStock($row->item_id);
                if(!empty($stockData)){$stockQty = $stockData->rfd_qty;}
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <!--<td>' . formatDate($row->packing_date) . '</td>-->
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . floatVal($dispatch_price) . '</td>
                    <td class="text-right">' . formatDecimal($dispatch_qty). '</td>
                    <td class="text-right">' . formatDecimal($stockQty). '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty) . '</td>
                    <td class="text-right">' . formatDecimal(($dispatch_qty * $dispatch_price)-$disc_amt) . '</td>
                    <td class="text-right">' . formatDecimal($stockQty * $item_price) . '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty * $item_price) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($row->packing_qty);$tpackv+=floatVal(floatVal($row->packing_qty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $dispatch_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="4">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : Mansee @ 13-12-2021 [Party wise filter]
    * Note : 
    */
    public function enquiryMonitoring(){
        $this->data['pageHeader'] = 'Inquiry v/s order';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->enquiry_monitoring, $this->data);
    }
    
    /*
  
    * Updated By : Sweta @ 06-05-2023 [Party wise filter]
    * Note : 
    */
    public function getEnquiryMonitoring($jsonData="")
    {
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $EnqMonitorData = $this->salesReportModel->getEnquiryMonitoring($data);
        $i = 1;
        $tbody = "";
        $tfoot = "";
        $thead ="";

        if (empty($data['party_id'])) :
            foreach ($EnqMonitorData as $row) :
                $data['party_id'] = $row->party_id;
                $countData = $this->salesReportModel->getEnquiryCount($data);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->party_name . '</td>
                    <td>' . $countData->totalEnquiry . '</td>
                    <td>' . $countData->quoted . '</td>
                    <td>' . $countData->pending . '</td>
                    <td>' . $countData->confirmSo . '</td>
                    <td>' . $countData->pendingSo . '</td>
                </tr>';
            endforeach;
            $tfoot .='<tr>
                <th colspan="7"></th>
            </tr>';

            $thead .= '<tr>
                        <th style="min-width:25px;">#</th>
                        <th style="min-width:100px;">Customer</th>
                        <th style="min-width:100px;">Total Enquiry</th>
                        <th style="min-width:100px;">Quotation Send</th>
                        <th style="min-width:100px;">Pending Quotation</th>
                        <th style="min-width:50px;">Sales Order Confirm</th>
                        <th style="min-width:50px;">Pending Enquiry</th>
                    </tr>';

        else :
            $total = 0;
            foreach($data['party_id'] as $key=>$value):
                $qryData['from_date'] = $data['from_date'];
                $qryData['to_date'] = $data['to_date'];
                $qryData['party_id'] = $value;
                $EnqMonitorData = $this->salesReportModel->getSalesEnquiryByParty($qryData);
                if(!empty($EnqMonitorData)):
                    foreach ($EnqMonitorData as $enqData) :
                        $quoteData = $this->salesReportModel->getSalesQuotation($enqData->id);
                        $total_amount = array();
                        $quoteNo = array();
                        $quotedt = array();
                        $quoteDays=array();
                        $transCount = $this->salesEnquiry->getFisiblityCount($enqData->id);
                        $itm = $this->salesEnquiry->getTransChild($enqData->id);
                        
                        $orderNo = array();
                        $orderdt = array();
                        $orderDays=array();
                        foreach ($quoteData as $quote) :
                            $total_amount[] = ($quote->total_amount * $quote->inrrate);
                            $total += ($quote->total_amount * $quote->inrrate);
                            $quoteNo[] = getPrefixNumber($quote->trans_prefix, $quote->trans_no);
                            $quotedt[] = formatDate($quote->trans_date) ;

                            $date2 = strtotime($enqData->trans_date);
                            $date1 = strtotime($quote->trans_date);
                            $datediff = $date1 - $date2;

                            $quoteDays[] =  (floor($datediff / (60 * 60 * 24)));
                        
                        
                            $orderData = $this->salesReportModel->getSalesOrder($quote->id);
                            if (!empty($orderData)) :
                                foreach ($orderData as $order) :

                                    $orderNo[] = getPrefixNumber($order->trans_prefix, $order->trans_no);
                                    $orderdt[] = formatDate($order->trans_date) ;

                                    $orderDate1 = strtotime($order->trans_date);
                                    $orderDate2 = strtotime($quote->trans_date);
                                    $orderDateDiff = $orderDate1 - $orderDate2;

                                    $orderDays[] =  (floor($orderDateDiff / (60 * 60 * 24)));

                                endforeach;
                            endif;
                        endforeach;
                        $quoteprefix = (!empty($quoteNo) ? implode('<hr>', $quoteNo) : '');
                        $quoteDate = (!empty($quotedt) ? implode('<hr>', $quotedt) : '');
                        $quoteTotalDays = (!empty($quoteDays) ? implode('<hr>', $quoteDays) : '');
                        $quotetotal_amount = (!empty($total_amount) ? implode('<hr>', $total_amount) : '');

                        $orderprefix = (!empty($orderNo) ? implode('<hr>', $orderNo) : '');
                        $orderDate = (!empty($orderdt) ? implode('<hr>', $orderdt) : '');
                        $orderTotalDays = (!empty($orderDays) ? implode('<hr>', $orderDays) : '');

                        $tbody .= '<tr>
                                        <td style="min-width:25px;">' . $i++ . '</td>
                                        <td style="min-width:100px;">' . $enqData->party_name . '</td>
                                        <td style="min-width:100px;">' .formatDate($enqData->trans_date)  . '</td>
                                        <td style="min-width:100px;">' . getPrefixNumber($enqData->trans_prefix, $enqData->trans_no)  . '</td>
                                        <td style="min-width:100px;">' . $quoteDate . '</td>
                                        <td style="min-width:100px;">' . $quoteprefix . '</td>
                                        <td style="min-width:50px;">' . $transCount->quoted . '</td>
                                        <td style="min-width:50px;">' . (count($itm) - $transCount->quoted) . '</td>
                                        <td style="min-width:100px;">' .$quotetotal_amount . '</td>
                                        <td style="min-width:100px;">'.$quoteTotalDays.'</td>
                                        <td style="min-width:100px;">' . $orderDate . '</td>
                                        <td style="min-width:100px;">' . $orderprefix . '</td>
                                        <td style="min-width:100px;">'.$orderTotalDays.'</td>
                                </tr>';

                       
                    endforeach;
                endif;
            endforeach;
            $tfoot .='<tr>
                <th colspan="8" style="text-align: center">Total</th>
                <th class="text-right">'.number_format($total).'</th>
                <th colspan="4"></th>
            </tr>';

            $thead .= '<tr>
                        <th style="min-width:25px;">#</th>
                        <th style="min-width:100px;">Customer</th>
                        <th style="min-width:100px;">Enquiry Date</th>
                        <th style="min-width:100px;">Enquiry No.</th>
                        <th style="min-width:100px;">Quotation Date</th>
                        <th style="min-width:100px;">Quotation No.</th>
                        <th style="min-width:50px;">Quoted</th>
                        <th style="min-width:50px;">Un Quote</th>
                        <th clas="text-right" style="min-width:100px;">Quotation Amount</th>
                        <th style="min-width:100px;">Day</th>
                        <th style="min-width:100px;">Sales order Date</th>
                        <th style="min-width:100px;">Sales order No.</th>
                        <th style="min-width:100px;">Day/Month</th>
                    </tr>';
        endif;
        if($data['type'] == 1){
            
            $pdfData = '<table id="reportTable" class="table item-list-bb">
            <thead class="thead-info" id="theadData">'.$thead.'</thead>
            <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
            <tfoot class="tfoot-info" id="tfootData">'.$tfoot.'</tfoot>
            </table>';

            $htmlHeader = "<table>
                                <tr>
                                    <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                    <th class='text-left' style='width:55%'>INQUIRY V/S ORDER</th>
                                </tr>
                            </table> <hr>";
            $mpdf = $this->m_pdf->load();
            $pdfFileName='CustOrdReg'.time().'.pdf';
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
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        }
    }
    
    /* 
        Created By Avruti @ 30-12-2021
    */
    public function salesTarget(){
        $this->data['pageHeader'] = 'TARGET V/S SALES';
        $this->load->view($this->sales_target, $this->data);
    }

    public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$partyData = $this->employee->getTargetRows($postData);

			$hiddenInputs = '<input type="hidden" id="sexecutive" name="executive" value="'.$postData['sales_executive'].'" />';
			$hiddenInputs .= '<input type="hidden" id="smonth" name="smonth" value="'.$postData['month'].'" />';
			$targetData = ''; $tfoot='';$i=1; $performance=0; $totalInv = 0; $totalOrder = 0; $totalTarget = 0;
			if(!empty($partyData)):
				foreach($partyData as $row):
                    if($row->business_target > 0):
    				    $postData['party_id']=$row->id;$salesTargetORD = 0;$salesTargetINV = 0 ;
        			    $salesTargetDataORD = $this->salesReportModel->getSalesOrderTarget($postData);
                        if(!empty($salesTargetDataORD->totalOrderAmt)){$salesTargetORD = $salesTargetDataORD->totalOrderAmt;}
                        
        			    $salesTargetDataINV = $this->salesReportModel->getSalesInvoiceTarget($postData);
                        if(!empty($salesTargetDataINV->totalInvoiceAmt)){$salesTargetINV = $salesTargetDataINV->totalInvoiceAmt;}
                        
                        $performance = 0;
                        if($salesTargetORD > 0 && $row->business_target >0){$performance = ($salesTargetORD * 100) / ($row->business_target);}
                        
    					$targetData .= '<tr>';
    						$targetData .= '<td>'.$i++.'</td>';
    						$targetData .= '<td>'.$row->party_name.'</td>';
    						$targetData .= '<td>'.$row->contact_person.'</td>';
    						$targetData .= '<td>'.$row->business_target.'</td>';
    						$targetData .= '<td>'.$salesTargetORD.'</td>';
    						$targetData .= '<td>'.$salesTargetINV.'</td>';
    						$targetData .= '<td>'.round($performance,2).'%</td>';
    					$targetData .= '</tr>';
                        $totalTarget += $row->business_target;$totalOrder += $salesTargetORD; $totalInv += $salesTargetINV;
                    endif;
				endforeach;
                $tfoot .='<tr class="thead-info">
                    <th colspan="3" style="text-align: left">Total</th>
                    <th class="text-right">'.number_format($totalTarget).'</th>
                    <th class="text-right">'.number_format($totalOrder).'</th>
                    <th class="text-right">'.number_format($totalInv).'</th>
                    <th></th>
                </tr>';
				$this->printJson(['status'=>1,'targetData'=>$targetData,'hiddenInputs'=>$hiddenInputs,'tfoot'=>$tfoot]);
			endif;
		endif;
    }
    
    /* Sales Order Summary */
	public function orderSummary(){
        $this->data['pageHeader'] = 'SALES ORDER SUMMARY REPORT';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->sales_order_summary, $this->data);
    }

    public function getOrderSummary($jsonData=""){
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
            $orderData = $this->salesReportModel->getOrderSummary($data);
            
            if(!empty($data['financial']))
            {
                $thead = '<tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">S.O. No.</th>
                            <th rowspan="2">P.O. No.</th>
                            <th rowspan="2">S.O. Date</th>
                            <th rowspan="2">WO. No.</th>
                            <th rowspan="2">Del. Date</th>
                            <th rowspan="2">Cust. Code</th>
                            <th rowspan="2">Item Name</th>
                            <th colspan="3">Order</th>
                            <th colspan="3">Dispatch</th>
                            <th colspan="3">Pending</th>
                        </tr>
                        <tr>
                            <th>Qty.</th><th>Rate</th><th>Amount</th>
                            <th>Qty.</th><th>Rate</th><th>Amount</th>
                            <th>Qty.</th><th>Rate</th><th>Amount</th>
                        </tr>';
            }
            else
            {
                $thead = '<tr>
                            <th>#</th>
                            <th>S.O. No.</th>
                            <th>P.O. No.</th>
                            <th>S.O. Date</th>
                            <th>WO. No.</th>
                            <th>Del. Date</th>
                            <th>Cust. Code</th>
                            <th>Item Name</th>
                            <th>Order Qty</th>
                            <th>Dispatch Qty</th>
                            <th>Pending Qty</th>
                        </tr>';
            }
            
            $tbody="";$i=1;
            foreach($orderData as $row):
                //$dispatch = $this->salesReportModel->getOrderWiseDispatch($row->id,$row->sales_type);
                
                $item_price=0;
                if($row->currency != 'INR')
                {         
                    //$inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($row->inrrate)){$item_price=$row->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                
                
                $dispatch_qty = ''; $dispatch_amt = ''; $dispatch_rate = ''; $pending_qty = ''; $pending_amt = ''; $pending_rate = '';
                if(!empty($row->dispatch_qty)){
                    $dispatch_qty = floatval($row->dispatch_qty);
                    $dispatch_amt = round(($row->dispatch_qty * $item_price), 2);
                    $dispatch_rate = round($item_price, 2);

                    $pending_qty = $row->qty - $row->dispatch_qty;
                    if($pending_qty > 0){
                        $pending_qty = floatval(($row->qty - $row->dispatch_qty));
                        $pending_amt = round(($pending_qty * $item_price), 2);
                        $pending_rate = round($item_price, 2);
                    } else { $pending_qty=0; $pending_amt=0; $pending_rate=0; }
                }
                
                if(!empty($data['financial']))
                {
                    $tbody .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                                    <td>'.$row->doc_no.'</td>
                                    <td>'.formatDate($row->trans_date).'</td>
                                    <td>'.$row->grn_data.'</td>
                                    <td>'.formatDate($row->cod_date).'</td>
                                    <td>'.$row->party_name.'</td>
                                    <td class="text-left">['.$row->item_code.'] '.$row->item_name.'</td>
                                    <td>'.floatval($row->qty).'</td>
                                    <td>'.round($item_price, 2).'</td>
                                    <td>'.round($row->amount, 2).'</td>
                                    <td>'.$dispatch_qty.'</td>
                                    <td>'.$dispatch_rate.'</td>
                                    <td>'.$dispatch_amt.'</td>
                                    <td>'.$pending_qty.'</td>
                                    <td>'.$pending_rate.'</td>
                                    <td>'.$pending_amt.'</td>
                               </tr>';
                }
                else
                {
                    $tbody .= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                                    <td>'.$row->doc_no.'</td>
                                    <td>'.formatDate($row->trans_date).'</td>
                                    <td>'.$row->grn_data.'</td>
                                    <td>'.formatDate($row->cod_date).'</td>
                                    <td>'.$row->party_name.'</td>
                                    <td class="text-left">['.$row->item_code.'] '.$row->item_name.'</td>
                                    <td>'.floatval($row->qty).'</td>
                                    <td>'.$dispatch_qty.'</td>
                                    <td>'.$pending_qty.'</td>
                               </tr>';
                }
            endforeach;
            if($data['type'] == 1){
                
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-left'>SALES ORDER SUMMARY REPORT</th>
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
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
        endif;
    }
    
    public function customerEnquiryRegister(){
        $this->data['pageHeader'] = "CUSTOMER'S INQUIRY MONITORING REGISTER";
        $this->load->view($this->custom_enquiry_register, $this->data);
    }
	
    public function getCustomerEnquiryRegister($jsonData=""){
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
            $enquiryData = $this->salesReportModel->getCustomerEnquiryRegister($data);
            $tbody=''; $thead=''; $i=1;
            $thead =  '<tr class="text-center" id="theadData">
                                        <th colspan="11">CUSTOMER INQUIRY MONITORING REGISTER</th>
                                        <th style="width:170px">F/MKT/01 (00/01.01.16)</th>
                                    </tr>
									<tr>
										<th rowspan="2">#</th>
										<th rowspan="2" style="width:85px">Receipt Date</th>
										<th rowspan="2" style="width:120px">Customer Name</th>
										<th rowspan="2">Customer Inquiry Reference</th>
										<th rowspan="2">Item Description</th>
										<th rowspan="2">Required Qty.</th>
										<th rowspan="2">Specification Reference</th>
										<th rowspan="2">Feasible(Yes/No)</th>
										<th rowspan="2">Our Quotation Reference</th>
										<th colspan="2" style="width:200px">Conversion to Order</th>
										<th rowspan="2">Remarks<br>(if any)</th>
									</tr>
									<tr>
										<th>(Yes / No)</th>
										<th>Order Date</th>
									</tr>';
            if(!empty($enquiryData)):
                foreach($enquiryData as $row):
                    $partyName=(!empty($row->party_code)?'['.$row->party_code.']':'').$row->party_name;
                    $tbody .= '<tr>
                        <td>'. $i++.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td class="text-left">'.$partyName.'</td>
                        <td>'.$row->ref_by.'</td>
                        <td class="text-left">'. $row->item_name.'</td>
                        <td>'.floatVal($row->qty).'</td>
                        <td></td>  
                        <td>'.$row->feasible.'</td>                        
                        <td>'.((!empty($row->quote_no))?getPrefixNumber($row->quote_prefix,$row->quote_no):'').'<br>'.formatDate($row->quote_date).'</td>
                        <td>'.(!empty($row->so_date)?'Yes':'No').'</td>                        
                        <td>'.formatDate($row->so_date).'</td>                        
                        <td >'.$row->reason .'</td>
                    </tr>';
                endforeach;
            endif;
            if($data['type'] == 1){

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
                $pdfFileName='CustEnqReg'.time().'.pdf';
                $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
                $stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
                $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
                $mpdf->WriteHTML($stylesheet,1);
                $mpdf->SetDisplayMode('fullpage');
                $mpdf->SetProtection(array('print'));
                $mpdf->SetHTMLHeader($htmlHeader);
                $mpdf->SetHTMLFooter("");
                $mpdf->AddPage('L','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-L');
                $mpdf->WriteHTML($pdfData);
                $mpdf->Output($pdfFileName,'I');
            }else{
                $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
            }
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'thead'=>$thead]);
        endif;
    }

    /* Sales Quotation Monitoring ---Karmi @05/07/2022 */
	public function salesQuotationMonitoring(){
        $this->data['pageHeader'] = 'SALES QUOTATION MONITORING REPORT';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->quotation_monitoring, $this->data);
    }

    public function getSalesQuotationMonitoring($jsonData=""){
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
            $quotationData = $this->salesReportModel->getSalesQuotationMonitoring($data);

            $tbody=''; $i=1; $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td>';
            if(!empty($quotationData)):
                foreach($quotationData as $row):
                    $followCount = 0; 
                    $followData = (!empty($row->extra_fields))?json_decode($row->extra_fields):array();
                    $followCount = count($followData);
                    $tbody .= '<tr>
                        <td class="text-center">'. $i++.'</td>
                        <td class="text-center"><a href="'.base_url('reports/salesReport/printQuotation/'.$row->trans_main_id).'" target="_blank" datatip="Print" flow="down">'.getPrefixNumber($row->trans_prefix, $row->trans_no).'</a></td>
                        <td class="text-center">'.formatDate($row->trans_date).'</td>
                        <td class="text-center">'.$row->party_name.'</td>';
                        
                    if($followCount > 0):
                        $j=1;
                        foreach($followData as $fup):
							$tbody .= '<td class="text-center">'.formatDate($fup->trans_date).'</td>
                            <td class="text-center">'.$fup->sales_executiveName.'</td>
                            <td class="text-center">'.$fup->f_note.'</td>';
                            if($j != $followCount){$tbody.='</tr><tr>'.$blankInTd; }
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
            if($data['type'] == 1){
                $thead .= '<tr>
                                <th rowspan="2" style="text-align:center">#</th>
                                <th rowspan="2" style="text-align:center">Quote No.</th>
                                <th rowspan="2" style="text-align:center">Quote Date</th>
                                <th rowspan="2" style="text-align:center">Customer Name</th>
                                <th colspan="3" style="text-align:center">Follow Up Detail</th>										
                           </tr>
                           <tr>
                                <th style="text-align:center">Date</th>
                                <th style="text-align:center">Sales Excutive</th>
                                <th style="text-align:center">Note</th>
                           </tr>';
                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
                </table>';

                $htmlHeader = "<table>
                                    <tr>
                                        <td><img src='assets/images/logo.png' class='img' style='height:60px'></td>
                                        <th class='text-left'>SALES QUOTATION MONITORING REPORT</th>
                                    </tr>
                                </table> <hr>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
                $this->printJson(['status'=>1, 'tbody'=>$tbody]);
            }
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    //Created By Karmi @12/08/2022
    public function packingHistory(){
        $this->data['pageHeader'] = 'PACKING HISTORY';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->packing_history,$this->data);
    }

    public function getPackingHistory(){
        $data = $this->input->post();
        $packingData = $this->salesReportModel->getPackingHistory($data); 
        $tbody=''; $i=1;  $soNo = ""; $pending_qty = 0; $oldDays =0;
        if(!empty($packingData)):
            foreach($packingData as $row):
                if(!empty($data['to_date'])){ 
                    $toDate = new DateTime(date('Y-m-d',strtotime($data['to_date'])));
                    $packingDate = new DateTime(date('Y-m-d',strtotime($row->entry_date)));
                    $dueDays = date_diff($packingDate,$toDate);
                    $day = $dueDays->format('%a');
                    $oldDays = ($day + 1).' Days'; 
                }
                $cls= ($oldDays > 30) ? 'text-danger font-bold' : '';
                $soNo = (!empty($row->so_no))? getPrefixNumber($row->so_prefix,$row->so_no) :""; 
                $pending_qty = $row->total_qty - $row->dispatch_qty; 
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->entry_date) . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . $soNo . '</td>
                    <td>' . $row->party_code.'</td>
                    <td>' . $row->item_code . '</td>
                    <td>'.floatVal($row->total_qty).'</td>
                    <td>'.floatVal($row->dispatch_qty).'</td>
                    <td>'.floatVal($pending_qty).'</td>
                    <td class="'.$cls.'">'.$oldDays.'</td>
                   
                </tr>';
            endforeach;
        endif;
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
    
    /* Print of Sales Quotation Monitoring Report */
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
		$pdfData = $this->load->view('report/sales_report/quotation_monitoring_print',$this->data,true);

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
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		//$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,41,45,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	 /* Customer Satisfaction Feedback Summary */
	public function customerSatisfactionFeedback(){
        $data = $this->input->post();
        $this->data['pageHeader'] = 'CUSTOMER SATISFACTION FEEDBACK SUMMARY';
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->load->view($this->satisfaction_feedback_summary,$this->data);
    }

    public function getCustomerSatisfactionFeedback($jsonData=""){
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
            $custData = $this->salesReportModel->getCustomerSatisfactionFeedback($data);
            $thead=""; $tbody=""; $tfoot=""; $i=1; $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            $totalValue=10; $total=0; $observerdVal=0; $csi=0; $csiTotal=0;
            
            $thead .= '<tr class="text-center">
                            <th colspan="2">CUSTOMER SATISFACTION FEEDBACK SUMMARY</th>
                            <th colspan="3">F/MKT/05 (00/01.01.16)</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-left" style="font-size:13px;"><b>Customer Name: '.(!empty($custData[0]->party_name)?$custData[0]->party_name:'').'</b></th>
                        </tr>
                        <tr>
                            <th colspan="2" class="text-left" style="font-size:13px;"><b>Survey Period: '.(!empty($custData[0]->survey_from)?$custData[0]->survey_from:'').' - '.(!empty($custData[0]->survey_to)?$custData[0]->survey_to:'').'</b></th>
                            <th colspan="3" class="text-left" style="font-size:13px;"><b>Date: '.(!empty($custData[0]->feedback_at)?formatdate($custData[0]->feedback_at):'').'</b></th>
                        </tr>
                        <tr>
                            <th>Sr.</th>
                            <th style="width:696px;">Description</th>
                            <th>Total Value</th>
                            <th>Observed Value</th>
                            <th>CSI %</th>
                    </tr>';
            foreach($custData as $row):
                if($row->grade == 1) { $row->grade = 5; }
                if($row->grade == 2) { $row->grade = 8; }
                if($row->grade == 3) { $row->grade = 10; }
                $total += $totalValue;
                $observerdVal += $row->grade;
                $csi = ($row->grade / $totalValue) * 100;
                $csiTotal = ($observerdVal / $total) * 100;
                $data['id'] = $row->id;
                $tbody .= '<tr>
                            <td class="text-center">'.$i++.'</td>
                            <td>'.$row->parameter.'</td>
                            <td class="text-center">'.$totalValue.'</td>
                            <td class="text-center">'.$row->grade.'</td>
                            <td class="text-center">'.$csi.' %'.'</td>
                          </tr>';
            endforeach;
            $tfoot .= '<tr class="thead-info">
                <th colspan="2" class="text-right">Total</th>
                <th>' .floatVal($total). '</th>
                <th>'.floatval($observerdVal).'</th>
                <th>'.floatval(round($csiTotal)).' %'.'</th>
            </tr>';
            if($data['type'] == 1){

                $pdfData = '<table id="reportTable" class="table item-list-bb">
                <thead class="thead-info" id="theadData">'.$thead.'</thead>
                <tbody id="receivableData">'.$tbody.'</tbody>
                <tfoot class="tfoot-info" id="tfootData">'.$tfoot.'</tfoot>
                </table>';

                $htmlHeader = "<table class='table item-list-bb'>
                                    <tr>
                                        <th class='text-center' style='width:70%;font-size:20px;'>AKSHAR ENGINEERS</th>
                                        <th><img src='assets/images/logo.png' class='img' style='height:60px'></th>
                                    </tr>   
                                </table>";
                $mpdf = $this->m_pdf->load();
                $pdfFileName='CustOrdReg'.time().'.pdf';
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
                $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody, 'tfoot' => $tfoot]);
            }
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function customerComplaints(){
        $this->load->view($this->customer_complaints, $this->data);
    }

    public function salesCustomerComplaints(){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;
       
        $customerComplaints = $this->customerComplaints->getCustomercomplaintsData($data); //print_r( $customerComplaints); exit;
        $i=1; $tbody = ""; 
        if(!empty($customerComplaints)):
            foreach($customerComplaints as $row): 
                $tbody .= '<tr>
                    <td>'.$i++.'</td> 
                    <td>'.formatDate($row->trans_date).'</td> 
                    <td>'.$row->party_id.'</td>
                    <td>'.$row->so_number.'</td>
                    <td>'.$row->item_id.'</td>
                    <td>'.$row->complaint.'</td>	
                    <td>'.($row->product_returned == 1 ? 'No':'Yes').'</td>
                    <td>'.$row->action_taken.'</td>
                    <td>'.$row->ref_feedback.'</td>
                    <td>'.$row->remark.'</td>  
                </tr>';
            endforeach;
        endif;
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);  
    }

	/* List of Customers */
    public function listOfCustomers(){
        $this->data['customerData'] = $this->salesReportModel->getListOfCustomerData();
        $this->load->view($this->list_of_customers, $this->data);
    }

    /* List of Customers Print */
    public function printListOfCustomers(){
        $customerData = $this->salesReportModel->getListOfCustomerData();         
        $logo=base_url('assets/images/logo.png');		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%"> LIST OF CUSTOMERS</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">D/MKT/01 (00/01.01.16)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th>Sr. No.</th>
                                        <th>Customers Name & Address</th>
                                        <th>Contact Details</th>
                                        <th>Remarks</th>	
									</tr>
								</thead>';                                
        $itemList.='<tbody id="tbodyData">';
                        $i=1;
                        foreach($customerData as $row)
                        {
                            $itemList.='<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->party_name.'<br>'.$row->party_address.'</td>
                                            <td>
                                                <b>Person : </b>'.$row->contact_person.'<br>
                                                <b>Phone : </b>'.$row->party_phone.'<br>
                                                <b>Mobile : </b>'.$row->party_mobile.'
                                            </td>
                                            <td></td>
                                        </tr>';
                        }
        $itemList.='</tbody>
                </table>';
	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';		
		$pdfData = $originalCopy;		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='CUST-LIST-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    /* List of Customers Approved Product & Drawing */
    public function listOfCustomersApprovedProductDrawing(){
        $this->data['customerData'] = $this->salesReportModel->getListOfCustomerData();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->approved_product, $this->data);
    }

    /* Get Data of List of Customers Approved Product & Drawing On Load Data */
    public function getlistOfCustomersApprovedProductDrawing($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $customerData = $this->salesReportModel->getlistOfCustomersApprovedProductDrawing($data);
        $logo=base_url('assets/images/logo.png');

        $tbody =""; 
        if(!empty($customerData))
        {
            $i=1;       
            foreach($customerData as $row):
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td class="text-left">' . $row->description . '</td>
                    <td class="text-left">' . $row->item_name . '</td>
                    <td>' . $row->drawing_no . '</td>
                    <td>' . $row->rev_no .'</td>
                    <td>' . formatDate($row->rev_date) . '</td>
                    <td></td>
                    <td>' . $row->size . '</td>
                    <td></td>
                    <td>'. $row->wt_pcs .'</td>
                    <td>'. $row->material_grade .'</td>
                    <td></td>
                    <td></td>
                </tr>';
            endforeach;    
        }
        else
        {
            $tbody .= '<tr><td colspan="13">No data available in table</td></tr>';
        }  

        if($data['type'] == 1)
        {                
            $topSectionO ='<table class="table">
                            <tr>
                                <td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
                                <td class="org_title text-center" style="font-size:1rem;width:60%">LIST OF CUSTOMER APPROVED  PRODUCT & DRAWING</td>
                                <td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">D/MKT/02 (00/01.01.16)</td>
                            </tr>
                        </table>';
            $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
                                    <thead class="thead-info" id="theadData">
                                        <tr>
                                            <th rowspan="2">Sr. No.</th>
                                            <th rowspan="2">Product Description</th>
                                            <th rowspan="2">Part Name</th>
                                            <th rowspan="2">Drawing No.</th>
                                            <th colspan="2">Revision</th>
                                            <th rowspan="2">Received Date</th>
                                            <th rowspan="2">RM Size</th>
                                            <th rowspan="2">RM Weight</th>
                                            <th rowspan="2">Finish Weight</th>
                                            <th rowspan="2">Material Grade</th>
                                            <th rowspan="2">Distributed To</th>
                                            <th rowspan="2">Remarks</th>
                                        </tr>
                                        <tr>
                                            <th>No.</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>';                                
            $itemList.='<tbody id="tbodyData">';
                            $i=1;
                            foreach($customerData as $row)
                            {
                                $itemList.='<tr>
                                                <td>' . $i++ . '</td>
                                                <td class="text-left">' . $row->description . '</td>
                                                <td class="text-left">' . $row->item_name . '</td>
                                                <td>' . $row->drawing_no . '</td>
                                                <td>' . $row->rev_no .'</td>
                                                <td>' . formatDate($row->rev_date) . '</td>
                                                <td></td>
                                                <td>' . $row->size . '</td>
                                                <td></td>
                                                <td>'. $row->wt_pcs .'</td>
                                                <td>'. $row->material_grade .'</td>
                                                <td></td>
                                                <td></td>
                                            </tr>';
                            }
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
        }
        else
        {
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        }
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }
}
?>