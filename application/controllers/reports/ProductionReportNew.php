<?php
class ProductionReportNew extends MY_Controller
{
    private $production_report_page = "report/production_new/index";
    private $job_wise_production = "report/production_new/job_production";
    private $jobwork_register = "report/production_new/jobwork_register";
    private $production_analysis = "report/production_new/production_analysis";
    private $stage_production = "report/production_new/stage_production";
    private $jobcard_register = "report/production_new/jobcard_register";
    private $machinewise_production = "report/production_new/machinewise_production";
    private $general_oee = "report/production_new/general_oee";
    private $operator_monitor = "report/production_new/operator_monitor";
    private $rejection_monitoring = "report/production_new/rejection_monitoring";
    private $operator_performance = "report/production_new/operator_performance";
    private $production_bom = "report/production_new/production_bom";
    private $rm_planing = "report/production_new/rm_planing";
    private $fg_tracking = "report/production_new/fg_tracking";
    private $operator_wise_production = "report/production_new/operator_wise_production";
    private $daily_oee = "report/production_new/daily_oee";
    private $vendor_tracking = "report/production_new/vendor_tracking";
    private $fg_planing = "report/production_new/fg_planing";
    private $job_costing = "report/production_new/job_costing";
    private $jobcard_wise_costing = "report/production_new/jobcard_wise_costing";
	private $tooling_device = "report/production_new/tooling_device";
    private $workholding_device = "report/production_new/workholding_device";
    private $mrp_report = "report/production_new/mrp_report";
    private $setup_summary = "report/production_new/setup_summary";
	
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Production Report";
        $this->data['headData']->controller = "reports/productionReportNew";
        $this->data['floatingMenu'] = '';//$this->load->view('report/production_new/floating_menu', [], true);
    }

    public function index()
    {
        $this->data['pageHeader'] = 'PRODUCTION REPORT';
        $this->load->view($this->production_report_page, $this->data);
    }

    /* Job Wise Production */
    public function jobProduction($item_id = "")
    {
        $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
        $this->data['jobcardData'] = $this->productionReportsNew->getJobcardList();
        $this->data['itemId'] = $item_id;
        $this->load->view($this->job_wise_production, $this->data);
    }

    public function getJobWiseProduction()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getJobWiseProduction($data);
        $this->printJson($result);
    }

    /* Jobwork Register */
    public function jobworkRegister()
    {
        $this->data['pageHeader'] = 'JOB WORK OUTWARD-INWARD REGISTER';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->jobwork_register, $this->data);
    }

    public function getJobworkRegister(){
        $data = $this->input->post();
        $jobOutData = $this->productionReportsNew->getJobworkRegister($data);

        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i = 1;
        $tblData = "";
        foreach ($jobOutData as $row) :
            $outData = $this->productionReportsNew->getJobOutwardData($row->id);
            
            $outCount = count($outData); 
            $tblData .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . formatDate($row->entry_date) . '</td>
                            <td>' . $row->party_name. '</td>
                            <td>' . $row->challan_no. '</td>
                            <td>' . $row->job_number. '</td>
                            <td>' . $row->wo_no . '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $row->process_name . '</td>
                            <td>' . $row->qty . '</td>';
            if ($outCount > 0) :
                $usedQty = 0; $j=1;
                foreach ($outData as $outRow) :
                    
					$outQty = $row->qty;
					$usedQty += $outRow->qty;
                    
					$balQty = floatVal($outQty) - floatVal($usedQty);
                    
					$tblData .= '<td>' . formatDate($outRow->entry_date) . '</td>
								<td>' . $row->party_name. '</td>
								<td>' . $outRow->in_challan_no . '</td>
								<td>' . $outRow->qty . '</td>
								<td>' . $balQty . '</td>';           
                    if ($j != $outCount) {
                        $tblData .= '</tr><tr><td>' . $i++ . '</td>' . $blankInTd;
                    }
                    $j++;
                endforeach;
            else :
                $tblData .= '<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>';
            endif;
            $tblData .= '</tr>';
        endforeach;
        $this->printJson(['status' => 1, "tblData" => $tblData]);
    }
    
    /* Machine Wise Production OEE Report */
    public function machineWise()
    {
        $this->data['pageHeader'] = 'MACHINE WISE OEE REGISTER';
        $this->data['machineList'] = $this->item->getItemList(5);
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->machinewise_production, $this->data);
    }

    public function getMachineData()
    {
        $id = $this->input->post('dept_id');
        $machineData = $this->productionReports->getDepartmentWiseMachine($id);
        $option = '<option value="">Select Machine</option>';
        foreach ($machineData as $row) :
            $option .= '<option value="' . $row->id . '" >[' . $row->item_code . '] ' . $row->item_name . '</option>';
        endforeach;

        $this->printJson(['status' => 1, 'option' => $option]);
    }

    public function getMachineWiseProduction()
    {
        $data = $this->input->post();

        $productionData = $this->productionReportsNew->getMachineWiseProduction($data);
        $i = 1;
        $tbody = "";
        foreach ($productionData as $row) :
            $row->m_ct = round($row->m_ct);
            $plan_time = ($row->total_production_time*60) + ($row->idle_time * 60);
            $runTime = ($row->total_production_time*60);
            $plan_qty = (!empty($runTime) && !empty($row->m_ct)) ? (($runTime) / $row->m_ct) : 0;
            $availability = (!empty($plan_time))?($runTime * 100) / $plan_time:0;
            $production_qty = $row->ok_qty+$row->rej_qty;
            $performance = (!empty($plan_qty)) ? (($production_qty * 100) / $plan_qty) : 0;
            $td = $this->productionReportsNew->getIdleTimeReasonMachineWise($row->entry_date, $row->shift_id,$row->machine_id);
            $tbody .= '<tr class="text-center">
                                    <td>' . $i++ . '</td>
                                    <td>' . formatDate($row->entry_date) . '</td>
                                    <td>' . $row->shift_name . '</td>
                                    <td>' . $row->mc_code . '</td>
                                    <td>' . $row->process_name . '</td>
                                    <td>' . $row->item_code . '</td>
                                    <td>' . (($plan_time > 0)?$plan_time/60:0) . '</td>
                                    <td>' . $row->m_ct . '</td>
                                    <td>' . (int)$plan_qty . '</td>
                                    <td>' . (($runTime>0)?$runTime/60:0) . '</td>
                                    <td>' . (int)$production_qty . '</td>
                                    <td>' . $row->idle_time . '</td>
                                    ' . $td . '
                                    <td>' . number_format($availability) . '%</td>
                                    <td>' . number_format($performance) . '%</td>
                            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
       
    }
    
	/* Operator Wise OEE */
    public function operatorWiseOee()
    {
        $this->data['pageHeader'] = 'Operator WISE OEE REGISTER';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->operator_wise_production, $this->data);
    }

    
    public function getOperatorWiseProduction()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $productionData = $this->productionReportsNew->getOperatorWiseProduction($data);
        $i = 1;$tbody = "";
        foreach ($productionData as $row) :
            $row->m_ct = round($row->m_ct);
            $plan_time = !empty($row->total_production_time) ? ($row->total_production_time*60) : 0;
            $plan_qty = (!empty($row->m_ct) && $row->m_ct > 0)?$plan_time/$row->m_ct:0;
            $production_qty = $row->ok_qty + $row->cft_ok_qty + $row->rej_qty + $row->pend_rej_qty +$row->pend_rw_qty +$row->pend_hold_qty;
            $rej_ratio = (!empty($production_qty) && $production_qty > 0)?($row->rej_qty * 100)/$production_qty:0;
            $performance = (!empty($plan_qty) && $plan_qty > 0)?($production_qty * 100)/$plan_qty:0;
            $oee = (!empty($plan_qty) && $plan_qty > 0)?($row->ok_qty * 100)/$plan_qty:0;
            $tbody .= '<tr class="text-center">
                                    <td>' . $i++ . '</td>
                                    <td>' . formatDate($row->entry_date) . '</td>
                                    <td>' . $row->shift_name . '</td>
                                    <td>' . $row->emp_name . '</td>
                                    <td>' . $row->process_name . '</td>
                                    <td>' . $row->item_code . '</td>
                                    <td>' . $row->total_production_time . '</td>
                                    <td>' . $row->m_ct . '</td>
                                    <td>' . (int)$plan_qty . '</td>
                                    <td>' . (int)$production_qty . '</td>
                                    <td>' . ($row->ok_qty+$row->cft_ok_qty) . '</td>
                                    <td>' . $row->pend_rej_qty . '</td>
                                    <td>' . $row->pend_rw_qty . '</td>
                                    <td>' . $row->pend_hold_qty . '</td>
                                    <td>' . $row->rej_qty . '</td>
                                    <td>' . round($performance,2) . '%</td>
                                    <td>' . round($rej_ratio,2) . '%</td>
                                    <td>' . number_format($oee,2) . '%</td>
                            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
       
    }


    /* OEE Register */
    public function oeeRegister()
    {
        $this->data['pageHeader'] = 'OEE REGISTER';
        $this->data['idleReasonList'] = $this->comment->getIdleReason();

        $this->load->view($this->general_oee, $this->data);
    }

    public function getOeeData()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOeeData($data);
            $i = 1;
            $tbody = "";
            foreach ($productionData as $row) :
                $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
				$performanceTime = $plan_time - $row->idle_time;
                $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                $total_load_unload_time=($row->total_load_unload_time*$row->production_qty)/60;
                $runTime = $plan_time - $row->idle_time-$total_load_unload_time;
                $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                $availability = ($plan_time > 0 && !empty($runTime) && !empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                if(!empty($performanceTime))
				{
					$performance = (!empty($row->cycle_time)) ? (((($row->cycle_time+$row->total_load_unload_time)*$row->production_qty)/($performanceTime))/60)*100 : 0;
				}
				else
				{
					$performance = 0;
				}
				$row->ok_qty = $row->ok_qty - $row->rej_qty;
                $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time+$row->total_load_unload_time)/60)*$row->production_qty) / $plan_time))*100 : 0;
                $quality_rate=($row->production_qty > 0) ? $row->ok_qty*100/$row->production_qty : 0;
                $oee = (($availability/100) * ($performance/100) * ($quality_rate/100))*100;
                $td = $this->productionReportsNew->getIdleTimeReasonForOee($row->log_date, $row->shift_id, $row->machine_id,$row->process_id,$row->operator_id,$row->product_id);
                $tbody .= '<tr class="text-center">
										<td>' . $i++ . '</td>
										<td>' . formatDate($row->log_date) . '</td>
										<td>' . $row->shift_name . '</td>
										<td>' . $row->item_code . '</td>
										<td>' . $row->machine_code . '</td>
										<td>' . $row->emp_name . '</td>
										<td>' . $row->process_name . '</td>
										<td>' . $row->cycle_time . '</td>
										<td>' . $row->total_load_unload_time . '</td>
										<td>' . $row->production_qty . '</td>
										<td>' . $row->rej_qty . '</td>
										<td>' . $row->rw_qty . '</td>
										<td>' . $row->idle_time . '</td>
                                        ' . $td . '
										<td>' . $plan_time . '</td>
										<td>' . number_format($runTime,2) . '</td>
										<td>' . (int)$plan_qty . '</td>
										<td>' . $row->ok_qty . '</td>
										<td>' . number_format($total_load_unload_time,2) . '</td>
                                        <td>' . number_format($availability,2) . '%</td>
                                        <td>' . number_format($overall_performance,2) . '%</td>
										<td>' . number_format($performance,2) . '%</td>
										<td>' . number_format($quality_rate,2) . '%</td>
										<td>' . number_format($oee,2) . '%</td>
								</tr>';
            endforeach;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }

     /* stage wise Production */
     public function stageProduction()
     {
         $this->data['pageHeader'] = 'STAGE WISE PRODUCTION';
         $this->data['partyList'] = $this->party->getCustomerList();
         $this->data['processList'] = $this->process->getProcessList();
         $this->load->view($this->stage_production, $this->data);
     }
 
     public function getStageWiseProduction()
     {
         $data = $this->input->post();
         $errorMessage = array();
         if ($data['to_date'] < $data['from_date'])
             $errorMessage['toDate'] = "Invalid date.";
 
         if (!empty($errorMessage)) :
             $this->printJson(['status' => 0, 'message' => $errorMessage]);
         else :
             $stageData = $this->productionReportsNew->getStageWiseProduction($data);
             $jobData = $stageData['jobData'];
             $processList = $stageData['processList'];
 
             $thead = '';
             $tbody = "";
             if (!empty($processList)) :
                 $thead = '<tr><th style="min-width:100px;">Party Code</th><th style="min-width:100px;">Job No.</th><th style="min-width:100px">W.O No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th>';
                 $l = 0;
                 $p = 0;
                 foreach ($jobData as $row) :
                     $qtyTD = '';
                     $qty = 0;
                     foreach ($processList as $pid) :
                         $pcData = $this->process->getProcessDetail($pid);
                         $process_name = (!empty($pcData)) ? $pcData->process_name : "";
                         if ($l == 0) {
                             $thead .= '<th>' . $process_name . '<br>(Ok Qty.)</th>';
                         }
                         if (in_array($pid, explode(',', $row->process))) :
                             $qty = $this->productionReportsNew->getProductionQty($row->id, $pid)->qty;
                         endif;
                         $qtyTD .= (!empty($qty)) ? '<td>' . floatVal($qty) . '</td>' : '<td>-</td>';
                     endforeach;
                     $tbody .= '<tr class="text-center">
                                 <td>' . $row->party_code . '</td>
                                 <td>' . $row->job_number . '</td>
                                 <td>' . $row->wo_no . '</td>
                                 <td>' . $row->item_code . '</td>
                                 <td>' . floatVal($row->job_qty) . '</td>
                                 ' . $qtyTD . '
                                 <!--<th>' . floatVal($row->total_out_qty) . '</th>-->
                             </tr>';
                     $l++;
                 endforeach;
             else :
                 $thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th><th style="min-width:100px;">Process List</th></tr>';
             endif;
 
 
             $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody]);
 
         endif;
     }
 
     /* Jobcard Register  */
     public function jobcardRegister()
     {
         $this->data['pageHeader'] = 'JOB CARD REGISTER';
         $jobCardData = $this->productionReportsNew->getJobcardRegister();
         $html = '';
         $i = 1;
         foreach ($jobCardData as $row) :
             $cname = !empty($row->party_code) ? $row->party_code : "Self Stock";
             $qtyData = $this->productionReportsNew->getPrdLogOnJob($row->id);
             $html .= '<tr>
                 <td>' . $i++ . '</td>
                 <td>' . $row->job_number . '</td>
                 <td>' . $row->wo_no . '</td>
                 <td>' . formatDate($row->job_date) . '</td>
                 <td>' . $row->party_name . '</td>
                 <td>' . $row->item_code . '</td>
                 <td>' . $row->batch_no . '</td>
                 <td>' . floatVal($row->qty) . '</td>
                 <td>' . floatVal($row->total_out_qty) . '</td>
                 <td>' . floatVal($row->total_rej_qty) . '</td>
                 <td>' . $row->emp_name . '</td>
                 <td>' . $row->remark . '</td>
             </tr>';
         endforeach;
         $this->data['jobRegHtml'] = $html;
         $this->load->view($this->jobcard_register, $this->data);
     }

     /* Operator Monitoring */
    public function operatorMonitoring()
    {
        $this->data['pageHeader'] = 'OPERATOR MONITORING';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_monitor, $this->data);
    }

    public function getOperatorMonitoring()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOperatorMonitoring($data);
            $i = 1; $tbody = ""; $thead = "";
            $thead .= '<tr><th style="min-width:50px;">#</th>
                            <th style="min-width:100px;">Date</th>
                            <th style="min-width:80px;">Shift</th>
                            <th style="min-width:80px;">M/C No.</th>
                            <th style="min-width:100px;">Part No.</th>
                            <th style="min-width:150px;">Setup</th>';
            if($data['type'] == 1):
                $thead .= '<th style="min-width:50px;">Cycle Time(m:s)</th>
                            <th style="min-width:50px;">Production Time(h:m)</th>
                            <th style="min-width:100px;">Ok Qty.</th>';                
            elseif($data['type'] == 2):
                $thead .= ' <th style="min-width:50px;">R/w. Qty.</th>
                            <th style="min-width:50px;">Rej. Qty.</th>';
            else:
                $thead .= ' <th style="min-width:50px;">Cycle Time(m:s)</th>
                            <th style="min-width:50px;">Production Time(h:m)</th>
                            <th style="min-width:100px;">Ok Qty.</th>
                            <th style="min-width:50px;">R/w. Qty.</th>
                            <th style="min-width:50px;">Rej. Qty.</th>';
            endif;

            foreach ($productionData as $row) :
                    $tbody .= '<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . formatDate($row->entry_date) . '</td>
                                <td>' . $row->shift_name . '</td>
                                <td>' . $row->machine_no . '</td>
                                <td>' . $row->item_code . '</td>
                                <td>' . $row->process_name . '</td>';
                    if($data['type'] == 1):
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->qty . '</td>';
                    elseif($data['type'] == 2):
                        $tbody .= '<td>' . $row->rw_qty . '</td>
                                <td>' . $row->rej_qty . '</td>';
                    else:
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->qty . '</td>
                                <td>' . $row->rw_qty . '</td>
                                <td>' . $row->rej_qty . '</td>';
                    endif;
                    $tbody .= '</tr>';     
            endforeach;
            $thead .= '</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'thead' => $thead]);
        endif;
    }

    /* Rejection Monitoring  avruti*/
    public function rejectionMonitoring()
    {
        $this->data['pageHeader'] = 'REJECTION & REWORK MONITORING REPORT';
        $this->data['itemDataList'] = $this->item->getItemList(1);
        $this->load->view($this->rejection_monitoring, $this->data);
    }

    public function getRejectionMonitoring()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $rejectionData = $this->productionReports->getRejectionMonitoring($data);
            $this->printJson($rejectionData);
        endif;
    }



    /*Production Bom Created By Meghavi 1-1-22*/
    public function productionBom()
    {
        $this->data['pageHeader'] = 'Production Bom Report';
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['refItemData'] = $this->item->getItemList(3);
        $this->load->view($this->production_bom, $this->data);
    }

    public function getItemBomData()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getItemWiseBom($data);
        $this->printJson($result);
    }

    public function getProductionBomData()
    {
        $data = $this->input->post();
        $result = $this->productionReports->getProductionBomData($data);
        $this->printJson($result);
    }

    public function rmPlaning()
    {
        $this->data['pageHeader'] = 'RM PLANING';
        $this->data['rmData'] = $this->item->getItemLists(3);
        $this->load->view($this->rm_planing, $this->data);
    }

    public function getRmPlaning()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getRmPlaning($data);

        $i = 1;
        $tbody = "";
        $theadVal = '';
        if (!empty($result)) :
            foreach ($result as $row) :
                $theadVal = $row->ref_qty . ' (' . $row->uname . ')';
                $tbody .= '<tr class="text-center">
                            <td>' . $i++ . '</td>
                            <td>' . $row->item_code . ' [' . $row->item_name . ']</td>
                            <td>' . $row->qty . ' (' . $row->uname . ')</td>
                            <td>' . floor($row->ref_qty / $row->qty) . '  (' . $row->unit_name . ')</td>
                        </tr>';
            endforeach;
        endif;
        $thead = '<tr>
            <th colspan="4">RM Stock : ' . $theadVal . '</th>
        </tr>
        <tr>
            <th>#</th>
            <th>Finish Goods</th>
            <th>Bom Qty.</th>
            <th>Expected Ready Qty.</th>
        </tr>';
        $this->printJson(['status' => 1, 'thead' => $thead, 'tbody' => $tbody]);
    }

    public function fgTracking()
    {
        $this->data['pageHeader'] = 'FG Tracking';
        $this->data['fgData'] = $this->item->getItemList(1);
        $this->load->view($this->fg_tracking, $this->data);
    }

    public function getFGStockDetail()
    {
        $data = $this->input->post();
        $i = 1;
        $tbody = "";
        $locationData = $this->store->getStoreLocationList();
        if (!empty($locationData)) {
            $prd_qty = 0;
            foreach ($locationData as $lData) {
                foreach ($lData['location'] as $batch) :
                    $result = $this->productionReports->getStockTrans($data['item_id'], $batch->id);
                    if (!empty($result)) {
                        foreach ($result as $row) {
                            if ($row->stock_qty > 0) :
                                if ($row->location_id == $this->PROD_STORE->id && $row->ref_type == 7) {
                                    $prd_qty += $row->stock_qty;
                                }
                                $tbody .= '<tr>';
                                $tbody .= '<td class="text-center">' . $i . '</td>';
                                $tbody .= '<td>[' . $lData['store_name'] . '] ' . $batch->location . '</td>';
                                $tbody .= '<td>' . $row->batch_no . '</td>';
                                $tbody .= '<td>' . floatVal($row->stock_qty) . '</td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
                        }
                    }
                endforeach;
            }
            $jobData = $this->productionReports->getJobcardWIPQty($data['item_id']);
            $wipQty = (!empty($jobData->qty) ? $jobData->qty : 0) -  $prd_qty;
            $tbody .= '<tr>';
            $tbody .= '<td class="text-center">' . $i . '</td>';
            $tbody .= '<td> WIP </td>';
            $tbody .= '<td> - </td>';
            $tbody .= '<td>' . floatVal($wipQty) . '</td>';
            $tbody .= '</tr>';
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    /* Daily OEE Register */
    public function dailyOeeRegister()
    {
        $this->data['pageHeader'] = 'Daily OEE REGISTER';
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view($this->daily_oee, $this->data);
    }

    public function getDailyOeeData()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['fromDate']))
            $errorMessage['fromDate'] = "From Date is required.";
        if (empty($data['date']))
            $errorMessage['date'] = "Date is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $deptData = $this->department->getDepartmentList();
            $tbody = "";
            $i = 1;
            foreach ($deptData as $dept) {
                $data['dept_id'] = $dept->id;
                $productionData = $this->productionReportsNew->getDepartmentWiseOee($data);


                $total_availability = 0;
                $total_performance = 0;
                $total_overall_performance = 0;
                $total_oee = 0;
                $total_production_qty = 0;
                $total_load_unload_time = 0;
                $total_quality_rate = 0;
                $idleTime = 0;
                $count = 0;
                if (!empty($productionData)) {
                    $td = $this->productionReportsNew->getIdleTimeReasonForDailyOee($data['fromDate'],$data['date'],$dept->id);
                    foreach ($productionData as $row) {
                        $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
						$performanceTime = $plan_time - $row->idle_time;
                        $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                        $total_load_unload_time += ($row->total_load_unload_time * $row->production_qty) / 60;
                        $runTime = $plan_time - $row->idle_time - (($row->total_load_unload_time * $row->production_qty) / 60);
                        $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                        $availability = (!empty($runTime) && !empty($plan_time))?($runTime * 100) / $plan_time:0;
                        $total_availability += $availability;
                        if(!empty($performanceTime))
						{
							$performance = (!empty($row->cycle_time) && !empty($plan_time)) ? (((($row->cycle_time + $row->load_unload_time) * $row->production_qty) / ($performanceTime)) / 60) * 100 : 0;
						}
						else
						{
							$performance = 0;
						}
                        $total_performance += $performance;
                        $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time + $row->load_unload_time) / 60) * $row->production_qty) / $plan_time)) * 100 : 0;
                        $total_overall_performance += $overall_performance;
                        $quality_rate = (!empty($row->production_qty))?$row->ok_qty * 100 / $row->production_qty:0;
                        $total_quality_rate += $quality_rate;
                        $oee = (($availability / 100) * ($performance / 100) * ($quality_rate / 100)) * 100;
                        $total_oee += $oee;
                        $total_production_qty += $row->production_qty;
                        $idleTime += $row->idle_time;
                        $count++;
                    }
                    $deptName = (!empty($dept->alias_name)) ? $dept->alias_name : $dept->name;
                    $tbody .= '<tr class="text-center">
                    <td>' . $i++ . '</td>
                    <td>' . $deptName . '</td>
                    <td>' . number_format(($total_availability) / $count, 2) . '%</td>
                    <td>' . number_format(($total_overall_performance) / $count, 2) . '%</td>
                    <td>' . number_format(($total_performance) / $count, 2) . '%</td>
                    <td>' . number_format(($total_quality_rate) / $count, 2) . '%</td>
                    <td>' . number_format(($total_oee) / $count) . '%</td>
                    <td>' . number_format(($idleTime/60),2) . '</td>
                    <td>' . number_format(($idleTime/60)*$row->machine_hrcost,2) . '</td>
                    <td>' . number_format($total_load_unload_time/60, 2) . '</td>
                   
                    ' . $td . '
                    <td>' . $total_production_qty . '</td>
                   
                    
            </tr>';
                }
            }
        endif;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }
    
    public function vendorTracking(){
        $this->data['pageHeader'] = 'VENDOR GOODS TRACKING';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->vendor_tracking, $this->data);
    }

    public function getVendorTracking(){
        $data = $this->input->post();
        $errorMessage = array();
		if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $trackingData = $this->productionReportsNew->getVendorTrackingData($data);
            $tbody=''; $i=1;$materialData  = array();
            $totalOut=0;$totalIn=0;$totalPend=0;
            if(!empty($trackingData)):
                foreach($trackingData as $row): 
                    $materialData = json_decode($row->material_data);
                    $z=0;$item="";$out_qty='';$in_qty='';$pending_qty='';
                    
                    if(!empty($materialData)):
                        foreach($materialData as $mdata):
                            $itemData = $this->item->getItem($mdata->item_id);
                            if($z==0) {
                                $item .= $itemData->item_name;
                                $out_qty .= $mdata->out_qty;
                                $in_qty .= $mdata->in_qty;
                                $pending_qty .= ($out_qty - $in_qty);
                            }else{
                                    $item .= ',<br>'.$itemData->item_name;
                                    $out_qty .= ',<br>'.$mdata->out_qty;
                                    $in_qty .= ',<br>'.$mdata->in_qty;
                                    $pending_qty .= ',<br>'.($out_qty - $in_qty);
                            } $z++;
                        endforeach;
                    endif;
                    if($pending_qty > 0):
                        $tbody .= '<tr>
                            <td>'. $i++.'</td>
                            <td>'.formatDate($row->challan_date).'</td>
                            <td>'.getPrefixNumber($row->challan_prefix, $row->challan_no).'</td>
                            <td>'.$row->party_name.'</td>
                            <td>'.$item.'</td>
                            <td>'.$out_qty.'</td>
                            <td>'.$in_qty.'</td>
                            <td>'.$pending_qty.'</td>
                        </tr>';
                        $totalOut+=$out_qty; $totalIn+=$in_qty; $totalPend+=$pending_qty;
                    endif;
                endforeach;
            endif;
            $tfoot = '<tr class="thead-info">
						<th colspan="5">TOTAL</th>
						<th>' . $totalOut . '</th>
						<th>' . $totalIn . '</th>
						<th>' . $totalPend . '</th>
					</tr>';

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    //Created By Karmi @10/08/2022
    public function fgPlaning()
    {
        $this->data['pageHeader'] = 'FG PLANNING';
        $this->data['itemDataList'] = $this->item->getItemList(1);        
        $this->load->view($this->fg_planing, $this->data);
    }

    public function getFGPlaning()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getFGPlaning($data); //print_r($data);exit;

        $i = 1;
        $tbody = "";
        $totalReqQty = 0; $stock =0;
        if (!empty($result)) :
            foreach ($result as $row) :
                $qtyTD = '';
                $totalReqQty = $row->qty * $data['fg_qty'];
                $stock = $this->store->getItemStock($row->ref_item_id);
                $qtyTD .= '<td>' . ((!empty($stock) && !empty($stock->qty)) ? abs($stock->qty) : '-') . '</td>';
                $tbody .= '<tr class="text-center">
                            <td>' . $i++ . '</td>
                            <td>' . $row->item_code . ' [' . $row->item_name . ']</td>
                            <td>' . $row->qty . ' (' . $row->unit_name . ')</td>
                            <td>' . floatVal($totalReqQty) . '  (' . $row->unit_name . ')</td>
                            ' . $qtyTD . '
                        </tr>';
            endforeach;
        endif;
        
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }
    
    /* Job Wise Costing */
    public function jobCosting($item_id = "")
    {
        $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
        $this->data['jobcardData'] = $this->productionReportsNew->getJobcardList();
        $this->data['itemId'] = $item_id;
        $this->load->view($this->job_costing, $this->data);
    }

    public function getJobCosting()
    {
        $data = $this->input->post();
        $result = $this->productionReportsNew->getJobCosting($data);
        $this->printJson($result);
    }
    
    //Crerated By Karmi @14/08/2022
    public function jobcardWiseCosting(){
        $this->data['pageHeader'] = 'JOBCARD WISE COSTING';
        $this->data['jobcardData'] = $this->productionReportsNew->getCompletedJobcardList();
        $this->load->view($this->jobcard_wise_costing, $this->data);
    }

    public function getJobCardWiseCosting(){
        $data = $this->input->post();
        $jobCardData = $this->jobcard_v2->getJobCard($data['job_id']);
        
        $process = explode(",",$jobCardData->process);
        $i=1; $tbody=""; $totalCosting=0; $stock=0;
        foreach($process as $process_id):
            $result = $this->productionReportsNew->getJobCardWiseCosting($data,$process_id,$jobCardData->product_id);
            if(!empty($result)) :
                foreach ($result as $row) :
                    $costing = (!empty($row->costing) && !empty($row->outQty))? $row->costing * $row->outQty : 0;
                    $tbody .= '<tr class="text-center">
                                <td>'.$i++.'</td>
                                <td>'.$row->process_name.'</td>
                                <td>'.$row->outQty.'</td>
                                <td>'.floatVal($row->costing).'</td>
                                <td>'.floatVal($costing).'</td>
                            </tr>';
                    $totalCosting += $costing;
                endforeach;
            endif;
        endforeach;
        $tfoot = '<tr class="thead-info">
					<th colspan="4">TOTAL</th>
					<th>' . $totalCosting . '</th>
				</tr>';
        $this->printJson(['status' => 1, 'tbody' => $tbody,'tfoot' => $tfoot]);
    }
    
    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function productionMonitoring(){
        $this->data['pageHeader'] = 'PRODUCTION MONITORING REPORT';
        $this->data['jobcardData'] = $this->productionReportsNew->getJobcardList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->load->view("report/production_new/production_monitoring", $this->data);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProcessListOnJobCard(){
        $job_id = $this->input->post('job_id');
        $jobData = $this->jobcard->getJobcard($job_id);
        $jobProcess = explode(',',$jobData->process);
        $processData = $this->process->getProcessList(['process_ids'=>$jobProcess]);

        $html = '<option value="">Select Process</option>';
        foreach($processData as $row):
            $html .= '<option value="'.$row->id.'">'.$row->process_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'process_list'=>$html]);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProcessWiseMachineList(){
        $process_id = $this->input->post('process_id');
        $machineData = $this->machine->getProcessWiseMachine($process_id);

        $html = '<option value="0">Select Machine</option>';
        foreach($machineData as $row):
            $html .= '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
        endforeach;

        $this->printJson(['status'=>1,'machine_list'=>$html]);
    }

    /* Created At: 03-12-2022 [ Milan Chauhan ] */
    public function getProductionMonitoringData($job_id="",$process_id="",$machine_id=""){
        if(!empty($job_id)):    
            $data['job_id'] = $job_id;
            $data['process_id'] = $process_id;
            $data['machine_id'] = $machine_id;
            $data['is_pdf'] = 1;
        else:
            $data = $this->input->post();
        endif;
        $productionData = $this->productionReportsNew->getProductionMonitoringData($data);
        $this->data['sapData'] = $this->controlPlan->getSarIprData(['job_card_id'=>$job_id,'process_id'=>$process_id,'machine_id'=>$machine_id,'limit'=>1,'single_row'=>1]);
        // print_r($this->db->last_query());exit;
        if($data['is_pdf'] == 0):
            $i = 1;
            $tbody = "";
            foreach ($productionData as $row) :
                if(!empty($row->job_card_id)):
                    $idleTimeData = $this->productionReportsNew->getIdleTimeReasonForOee(['entry_date' => $row->entry_date, 'shift_id' => $row->shift_id, 'machine_id' => $row->machine_id, 'process_id' => $row->process_id, 'operator_id' => $row->operator_id, 'product_id' => $row->product_id, 'job_card_id' => $row->job_card_id ]);
                    $td = $idleTimeData['td'];
                    $row->idle_time = $idleTimeData['total_idle_time'];
    
                    $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                    $performanceTime = $plan_time - $row->idle_time;
                    $ct = (!empty($row->m_ct)) ? ($row->m_ct / 60) : 0;
                    $total_load_unload_time=($row->total_load_unload_time*$row->production_qty)/60;
                    $runTime = $plan_time - $row->idle_time-$total_load_unload_time;
                    $plan_qty = (!empty($runTime) && !empty($ct)) ? ($runTime / $ct) : 0;
                    $availability = ($plan_time > 0 && !empty($runTime) && !empty($plan_time)) ? ($runTime * 100) / $plan_time : 0;
                    if(!empty($performanceTime)){
                        $performance = (!empty($row->cycle_time)) ? (((($row->cycle_time+$row->total_load_unload_time)*$row->production_qty)/($performanceTime))/60)*100 : 0;
                    }else{
                        $performance = 0;
                    }
                    $overall_performance = (!empty($row->cycle_time) && !empty($plan_time)) ? ((((($row->cycle_time+$row->total_load_unload_time)/60)*$row->production_qty) / $plan_time))*100 : 0;
                    $quality_rate=($row->production_qty > 0) ? $row->ok_qty*100/$row->production_qty : 0;
                    $oee = (($availability/100) * ($performance/100) * ($quality_rate/100))*100;
                    
                    $tbody .= '<tr class="text-center">
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->entry_date) . '</td>
                        <td>' . $row->shift_name . '</td>
                        <td>' . $row->operator_name . '</td>
                        <td>' . $row->cycle_time . '</td>
                        <td>' . $row->total_load_unload_time . '</td>
                        <td>' . $row->production_qty . '</td>
                        <td>' . $row->rej_qty . '</td>
                        <td>' . $row->rw_qty . '</td>                    
                        <td>' . $plan_time . '</td>
                        <td>' . number_format($runTime,2) . '</td>
                        <td>' . (int)$plan_qty . '</td>
                        <td>' . $row->ok_qty . '</td>
                        <td>' . number_format($total_load_unload_time,2) . '</td>
                        <td>' . $row->idle_time . '</td>
                        ' . $td . '
                        <td>' . number_format($availability,2) . '%</td>
                        <td>' . number_format($overall_performance,2) . '%</td>
                        <td>' . number_format($performance,2) . '%</td>
                        <td>' . number_format($quality_rate,2) . '%</td>
                        <td>' . number_format($oee,2) . '%</td>
                    </tr>';
                endif;
            endforeach;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        else:
            $this->data['pageHeader'] = 'PRODUCTION MONITORING REPORT';
            $this->data['productionData'] = $productionData;
            $this->data['jobData'] = $jobCardData = $this->jobcard->getJobcard($data['job_id']);
            $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData); 
            $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData']:'';
            $this->data['machineData'] = (!empty($data['machine_id']))?$this->machine->getMachine($data['machine_id']):"";
            $this->data['processData'] = $this->process->getProcess($data['process_id']);
            $this->data['idleReasonList'] = $this->comment->getIdleReason();
            $this->data['jobApprovalData'] =$jobApprovalData= $this->processMovement->getProcessWiseApprovalData($job_id,$process_id);
            $this->data['settingData'] = $this->jobcard->getSettingParamData(['job_approval_id'=>$jobApprovalData->id]);
            $pdfData = $this->load->view('report/production_new/production_monitoring_pdf', $this->data, true);
        
            //$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
            $htmlFooter = '';//'<img src="'.$this->data['letter_footer'].'" class="img">';

            $mpdf = new \Mpdf\Mpdf();
            $pdfFileName = $jobCardData->job_number . '.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));
            //$mpdf->SetHTMLHeader($htmlHeader);
            //$mpdf->SetHTMLFooter($htmlFooter);

            $mpdf->AddPage('L', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName, 'I');
        endif;
    }
    
    /* Created At: 04-12-2022 [ Milan Chauhan ] */
    public function dailyProductionLogSheet(){
        $this->data['pageHeader'] = 'DAILY PRODUCTION LOG SHEET';
        $this->load->view("report/production_new/production_log_sheet", $this->data);
    }

    /* Created At: 04-12-2022 [ Milan Chauhan ] */
    public function getDailyProductionLogSheet($log_date = ""){
        if(!empty($log_date)):
            $data['log_date'] = $log_date;
            $data['is_pdf'] = 1;
        else:
            $data = $this->input->post();
            $data['is_pdf'] = 0;
        endif;

        $productionData = $this->productionReportsNew->getDailyProductionLogSheet($data['log_date']);

        $tbody = '';
        foreach($productionData as $row):
            $tbody .= '<tr class="text-center">
                <td class="text-left">
                    '.$row->emp_name.'
                </td>
                <td class="text-left">
                    '.$row->machine_name.'
                </td>
                <td>
                    '.$row->shift_name.'
                </td>
                <td>
                    '.$row->product_name.'
                </td>
                <td>
                    '.$row->rm_grade.'
                </td>
                <td>
                    '.$row->job_number.'
                </td>
                <td>
                    '.$row->process_name.'
                </td>
                <td>
                    '.$row->cycle_time.'
                </td>
                <td>
                    '.$row->total_production_time.'
                </td>
                <td>
                    '.$row->total_ok_qty.'
                </td>
                <td>
                    '.$row->pre_finished_weight.'
                </td>
                <td>
                    '.$row->finished_weight.'
                </td>
                <td>
                    '.$row->total_rej_qty.'
                </td>
                <td>
                    '.$row->rej_reason.'
                </td>
                <td>
                    '.$row->total_rw_qty.'
                </td>
                <td>
                    '.$row->rw_reason.'
                </td>
                <td>
                    '.$row->total_idle_time.'
                </td>
                <td>
                    '.$row->idle_reason.'
                </td>
                <td>
                    '.$row->effecincy_per.'
                </td>
            </tr>';
        endforeach;

        if($data['is_pdf'] == 0):
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        else:
            $pdfData = '';
			$pdfData .= '<html>
				<head>
					<title>
                        DAILY PRODUCTION LOG SHEET
					</title>
				</head>
				<body style="padding:10px;">
					<table class="table table-bordered itemList">                        
						<tr>
							<th style="width:70%;">AKSHAR ENGINEERS</th>
							<th style="width:30%;">
								<img src="'.base_url("assets/images/logo_text.png").'" alt="logo" style="width:20%;">
							</th>
						</tr>
						<tr>
							<th>DAILY PRODUCTION LOG SHEET</th>
							<th>F/PRD/05 (00/01.01.16)</th>
						</tr>
						<tr>
							<th class="text-right" colspan="2">Date : '.formatDate($data['log_date']).'</th>
						</tr>
					</table>
					<table class="table table-bordered itemList">
						<thead class="thead-info" id="theadData">
                            <tr>
                                <th>Operator Name</th>
                                <th>M/C NO.</th>
                                <th>Day/ Night</th>
                                <th>Part Name</th>
                                <th>Metal</th>
                                <th>WO No</th>
                                <th>Set up</th>
                                <th>Cycle time<br>(Sec.)</th>
                                <th>Total time<br>(Min.)</th>
                                <th>Qty</th>
                                <th>Before weight</th>
                                <th>After weight</th>
                                <th>Rejection qty.</th>
                                <th>Rejection reason</th>
                                <th>Rework qty.</th>
                                <th>Rework reason</th>
                                <th>Down time</th>
                                <th>Down time reason</th>
                                <th>Effciency (%)</th>
                            </tr>
						</thead>
						<tbody>
							'.$tbody.'
						</tbody>
					</table>
				</body>
			</html>';

			$mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'DAILY_PRODUCTION_LOG_SHEET_'.date("d_m_Y",strtotime($data['log_date'])).'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));

            $mpdf->AddPage('L', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-L');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName, 'I');
        endif;
    }

	//Created By Meghavi @22/06/2023
    public function toolingDevice(){
        $this->data['pageHeader'] = 'List Of Tooling Device';   
        $this->data['toolingData'] = $this->productionReportsNew->getToolDataForReport(44); 
        $this->load->view($this->tooling_device, $this->data);
    }

    //Created By Meghavi @22/06/2023
    public function workHoldingDevice(){
        $this->data['pageHeader'] = 'List Of Work Holding Device';   
        $this->data['workHoldingData'] = $this->productionReportsNew->getToolDataForReport(45);
        $this->load->view($this->workholding_device, $this->data);
    }

    /*CREATED BY MEGHAVI @22/06/23*/ 
    public function printToolingDevice(){
        $toolingData = $this->productionReportsNew->getToolDataForReport(44);       
        $logo=base_url('assets/images/logo.png');		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%"> LIST OF TOOLING DEVICES</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">D/PRD/02 (00/01.01.16)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th>Sr. No.</th>
                                        <th>Description of Tooling</th>
                                        <th>Make</th>
                                        <th>Size / Specification</th>	
                                        <th>Qty.</th>	
                                        <th>Location</th>	
                                        <th>Remarks</th>	
									</tr>
								</thead>';                                
        $itemList.='<tbody id="tbodyData">';
                        $i=1;
                        foreach($toolingData as $row)
                        {
                            $size = (!empty($row->size)) ? $row->size : '' ;
                            $itemList.='<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->item_name.'</td>
                                            <td>'.$row->make_brand.'</td>
                                            <td>'.$size.'</td>
                                            <td>'.$row->stock_qty.'</td>
                                            <td>'.$row->location.'</td>
                                            <td>'.$row->description.'</td>
                                        </tr>';
                        }
        $itemList.='</tbody>
                </table>';
	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';		
		$pdfData = $originalCopy;	//print_r($pdfData);exit;	
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

    /*CREATED BY MEGHAVI @22/06/23*/ 
    public function printWorkHoldingDevice(){
        $workHoldingData = $this->productionReportsNew->getToolDataForReport(45);     
        $logo=base_url('assets/images/logo.png');		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%"> LIST OF WORKHOLDING DEVICES</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">D/PRD/03 (00/01.01.16)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th>Sr. No.</th>
                                        <th>Description of Workholding</th>
                                        <th>Make</th>
                                        <th>Size / Specification</th>	
                                        <th>Qty.</th>	
                                        <th>Location</th>	
                                        <th>Remarks</th>	
									</tr>
								</thead>';                                
        $itemList.='<tbody id="tbodyData">';
                        $i=1;
                        foreach($workHoldingData as $row)
                        {
                            $size = (!empty($row->size)) ? $row->size : '' ;
                            $itemList.='<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$row->item_name.'</td>
                                            <td>'.$row->make_brand.'</td>
                                            <td>'.$size.'</td>
                                            <td>'.$row->stock_qty.'</td>
                                            <td>'.$row->location.'</td>
                                            <td>'.$row->description.'</td>
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


    /* Created By :- Sweta @08-08-2023 */
    public function materialReqPlan(){
        $this->data['pageHeader'] = 'MRP (Material Requirement Planning)';
        $this->data['itemDataList'] = $this->item->getItemList(1);        
        $this->load->view($this->mrp_report, $this->data);
    }

    /* Created By :- Sweta @08-08-2023 */
    public function getMaterialReqPlan($jsonData=""){
        if(!empty($jsonData)):
            $data = (Array) decodeURL($jsonData);
        else:
            $data = $this->input->post();
        endif;

        $rmData = $this->productionReportsNew->getMaterialReqPlan($data);
		$toolData = $this->item->getToolConsumption($data['fg_item_id']);
		
        $i=1; $tbody=""; $totalReqQty=0; $stock=0;
		
        if (!empty($rmData)) :
            foreach ($rmData as $row) : 
                $totalReqQty = $row->qty * $data['fg_qty'];
				
                $tbody .= '<tr class="text-center">
                            <td>' . $i++ . '</td>
                            <td>' . $row->item_code . '</td> 
							<td>' . $row->item_name . '</td>
							<td> Raw Material </td>
							<td>' . $row->unit_name . '</td>
                            <td>' . $totalReqQty . ' </td>
                        </tr>';
            endforeach;
        endif;
		
		if(!empty($toolData)):	
			$processName = '';
			foreach($toolData as $row):
				$item_name=''; $material_type='';
				if(!empty($row->sub_group)){
					$item_code = (!empty($row->item_code)? $row->item_code:'');
					$item_name = $row->item_name;
					$material_type = $row->material_type;
				}else{
					$item_code = (!empty($row->inst_code)? $row->inst_code:'');
					$item_name = $row->inst_name;
					$material_type = 'Instrument & Gauges';
				}
				
				if($processName != $row->process_name):
					$tbody.= '<tr class="text-center thead-gray">
						<th colspan="4">'.$row->process_name.'</th>
						<th>CT : '.$row->cycle_time.'</th>
						<th>WT : '.$row->finished_weight.'</th>
					</tr>';
				endif;
				
				$req_qty = $row->req_qty;
				if($row->used_for == 1):
					$req_qty = $row->req_qty * $data['fg_qty'];
				endif;
				
				$tbody.= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.$item_code.'</td>
						<td>'.$item_name.'</td>
						<td>'.$material_type.'</td>
						<td>'.$row->tool_unit.'</td>
						<td>'.floatVal($req_qty).'</td>
					</tr>';
				$processName = $row->process_name;
			endforeach;
		endif;

        $thead='';
        if($data['type'] == 1)
        {
            $itemData = $this->item->getItem($data['fg_item_id']);

            $thead = '<h4>Item Detail:</h4>
                    <table class="text-left table item-list-bb">                        
                        <tr>
                            <th>Part Name </th>
                            <td>'.$itemData->item_name.'</td>
                            <th>Part Size </th>
                            <td>'.$itemData->size.'</td>
                        </tr>
                        <tr>
                            <th>Part Code </th>
                            <td>'.$itemData->item_code.'</td>
                            <th>HSN Code </th>
                            <td>'.$itemData->hsn_code.'</td>
                        </tr>
                        <tr>
                            <th>Cust. Part No </th>
                            <td>'.$itemData->part_no.'</td>
                            <th>Drawing No </th>
                            <td>'.$itemData->drawing_no.'</td>
                        </tr>
                        <tr>
                            <th>Revision No & Date </th>
                            <td>'.$itemData->rev_no.' | '.formatDate($itemData->rev_date).'</td>
                            <th>FG Weight </th>
                            <td>'.$itemData->wt_pcs.'</td>
                        </tr>
                        <tr>
                            <th>Remark </th>
                            <td colspan="3">'.$itemData->description.'</td>
                        </tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #000000;margin-top:20px;">
                        <tr>
                            <td class="text-center" style="font-size:1.3rem;font-weight:bold;">MRP (Material Requirement Planning)</td>
                        </tr>
                    </table>';

            $pdfData = $thead.
            '<table id="reportTable" class="table item-list-bb" style="margin-top:20px;">
                <thead>
                    <tr class="thead-gray">
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Material Type</th>
                        <th>Unit</th>
                        <th>Req. Qty.</th>
                    </tr>
                </thead>
                <tbody class="text-center" id="receivableData">'.$tbody.'</tbody>
            </table>';

            $this->data['letter_head']=base_url('assets/images/letterhead_top.png');
            $htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';

            $mpdf = $this->m_pdf->load();
            $pdfFileName='MRP'.time().'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetDisplayMode('fullpage');           
            $mpdf->SetProtection(array('print'));
            
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter("");
            $mpdf->AddPage('P', '', '', '', '', 5, 5, 30 , 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName,'I');
        }
        else
        {
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        }        
    }

    
	/* Setup Summary */
    public function setupSummary()
    {
        $this->data['pageHeader'] = 'Setup Summary';
        $this->data['empData'] = $this->employee->getSetterList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->setup_summary, $this->data);
    }

    
    public function getSetupSummaryData()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $productionData = $this->productionReportsNew->getSetupSummaryData($data);
        $i = 1;$tbody = "";
        foreach ($productionData as $row) :
            
            $tbody .= '<tr class="text-center">
                                    <td>' . $i++ . '</td>
                                    <td>' . formatDate($row->insp_date) . '</td>
                                    <td>' . $row->emp_name . '</td>
                                    <td>' . $row->operator_name . '</td>
                                    <td>' . $row->process_name . '</td>
                                    <td>[' . $row->mc_code . '] '.$row->mc_name.'</td>
                                    <td>' . $row->item_code . '</td>
                                    <td>' . $row->total_setup . '</td>
                                    <td>' . $row->setting_time . '</td>
                            </tr>';
        endforeach;
        $this->printJson(['status' => 1, 'tbody' => $tbody]);
       
    }

}
