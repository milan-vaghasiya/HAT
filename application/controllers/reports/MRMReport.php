<?php
class MRMReport extends MY_Controller
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

        $blankInTd = '<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i = 1;
        $tblData = "";
        foreach ($jobOutData as $row) :
            
            $pids = explode(',', $row->process);
            $idx = 0;
            $md = "";
            foreach ($pids as $key => $pid) :
                if ($pid == $row->process_id) :
                    $idx = $key;
                endif;
            endforeach;
            $uname = $row->punit;
            $outQty = 0;
            $rmFlag = false;
            $umData = $this->productionReportsNew->getUsedMaterialByJobCardId($row->job_card_id);
            //print_r($umData); exit;
            if ($idx == 0) :
                $outQty = $umData->issue_qty;
                $md = "Initial";
                $uname = $umData->unit_name;
                $rmFlag = true;
                $batch_no = $umData->batch_no;
            else :
                $outQty = $row->out_qty;
                $md = $this->process->getProcess($pids[$idx - 1])->process_name . ' Ok';
                $uname = $row->punit;
                $batch_no = $umData->batch_no;
            endif;

            //Caret Details @AAVRUTI
            $jobChallanData = $this->productionReportsNew->getJobChallan($row->id);
            
            $materialData = (!empty($jobChallanData[0]->material_data) ? json_decode($jobChallanData[0]->material_data) : "");
            $outwardDetails = "";
            $inwardDetails = "";
            if (!empty($materialData)) : $i = 1;
                foreach ($materialData as $chData) :
                    $item_name = $this->item->getItem($chData->item_id)->item_name;
                    if ($i == 1) {
                        $outwardDetails .= 'Plastic Crate (Qty:. ' . $chData->out_qty . ')';
                        $inwardDetails .= 'Plastic Crate (Qty:. ' . $chData->in_qty . ')';
                    } else {
                        $outwardDetails .= '<br> Plastic Crate (Qty:. ' . $chData->out_qty . ')';
                        $inwardDetails .= '<br> Plastic Crate (Qty:. ' . $chData->in_qty . ')';
                    }
                    $i++;
                endforeach;
            endif;

            $outData = $this->productionReportsNew->getJobOutwardData($row->id);
            
            $outCount = count($outData); 
            $tblData .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . formatDate($row->created_at) . '</td>
                            <td>' . (!empty($row->jwo_no)?(getPrefixNumber($row->jwo_prefix,$row->jwo_no)):''). '</td>
                            <td>' . (!empty($jobChallanData[0]->challan_no)?(getPrefixNumber($jobChallanData[0]->challan_prefix,$jobChallanData[0]->challan_no)):''). '</td>
                            <td>' . $row->item_code . '</td>
                            <td>' . $md . '</td>
                            <td>' . $row->process_name . '</td>
                            <td>' . $row->out_qty . '</td>
                            <td>' . $row->punit . '</td>
                            <td>' . $batch_no . '</td>
                            <td>' . $outwardDetails . '</td>';
            if ($outCount > 0) :
                $usedQty = 0;
                $j = 1;
                foreach ($outData as $outRow) :
                    $rejectQty = 0; $balQty = 0; 
                    $rejectData = $this->productionReportsNew->getVendorRejectionSum($outRow->id);
                    if (!empty($rejectData)) {
                        $rejectQty = $rejectData->rejectQty;
                    }
                    
                    if ($rmFlag) :
                        $outQty = ($umData->wp_qty * $row->out_qty);
                        $usedQty += ($umData->wp_qty * $outRow->in_qty);// + ($umData->wp_qty * $rejectQty); print_r($umData->wp_qty); exit;
                    else :
                        $usedQty += $outRow->in_qty + $rejectQty;
                    endif; 
                    $balQty = floatVal($outQty) - floatVal($usedQty);
                    $vendor = (!empty($outRow->party_name)) ? $outRow->party_name : 'In House';
                    $tblData .= '<td>' . formatDate($outRow->entry_date) . '</td>
                                    <td>' . $vendor . '</td>
                                    <td>' . $outRow->item_code . '</td>
									<td>' .(!empty($jobChallanData[0]->challan_no)?(getPrefixNumber($jobChallanData[0]->challan_prefix,$jobChallanData[0]->challan_no)):'') . '</td>
                                    <td>' . $outRow->in_challan_no . '</td>
                                    <td>' . floatVal($outRow->in_qty) . '</td>
                                    <td>' . $row->punit . '</td>
                                    <td>' . $balQty . '</td>
                                    <td>' . floatVal($rejectQty) . '</td>
                                    <td>' . $batch_no . '</td>';
                    if($j==1){
                        $tblData.='<td>' . $inwardDetails . '</td>';
                    }else{
                        $tblData.='<td>-</td>';
                    }             
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
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
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
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
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
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getMachineWiseProduction($data, $data['dept_id']);
            $i = 1;
            $tbody = "";
            $mcData = $this->item->getItem($data['machine_id']);
            foreach ($productionData as $row) :
                $plan_time = ($row->shift_hour);
                $ct = (!empty($row->m_ct)) ? ($row->m_ct) : 0;
                $runTime = round(($plan_time - $row->idle_time-(($row->load_unload_time*$row->ok_qty)/60)),2);
                $plan_qty = (!empty($runTime) && !empty($ct)) ? (($runTime*60) / $ct) : 0;
                $availability = (!empty($plan_time))?($runTime * 100) / $plan_time:0;
                $performance = (!empty($plan_qty)) ? (($row->ok_qty * 100) / $plan_qty) : 0;
                $oee = ($availability + $performance) / 2;
                $td = $this->productionReportsNew->getIdleTimeReasonMachineWise($row->log_date, $row->shift_id, $data['machine_id']);
                $tbody .= '<tr class="text-center">
										<td>' . $i++ . '</td>
										<td>' . formatDate($row->log_date) . '</td>
										<td>' . $row->shift_name . '</td>
										<td>' . $mcData->item_code . '</td>
										<td>' . $plan_time . '</td>
										<td>' . (int)$plan_qty . '</td>
										<td>' . $runTime . '</td>
										<td>' . $row->ok_qty . '</td>
										<td>' . $row->idle_time . '</td>
                                        ' . $td . '
                                        <td>' . number_format($availability) . '%</td>
										<td>' . number_format($performance) . '%</td>
										<td>' . number_format($oee) . '%</td>
								</tr>';
            endforeach;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }
    /* Operator Wise OEE */
    public function operatorWiseOee()
    {
        $this->data['pageHeader'] = 'Operator WISE OEE REGISTER';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_wise_production, $this->data);
    }

    public function getOperatorWiseProduction()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $productionData = $this->productionReportsNew->getOperatorWiseProduction($data);
            $i = 1;$tbody = "";
            $operator = (!empty($data['operator_id'])) ? $this->employee->getEmp($data['operator_id'])->emp_name : 'NO OPERATOR';
            foreach ($productionData as $row) :
                $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                //changed by mansee
				$performanceTime = $plan_time - $row->idle_time;
                $ct = (!empty($row->m_ct)) ? ($row->m_ct) : 0;
                $total_load_unload_time=($row->load_unload_time*$row->production_qty)/60;//changed by mansee
                $runTime = $plan_time - $row->idle_time-$total_load_unload_time;//changed by mansee
                
                $plan_qty = (!empty($runTime) && !empty($ct)) ? (($runTime*60) / $ct) : 0;
                $availability = (!empty($plan_time))?($runTime * 100) / $plan_time:0;
                // $performance = (!empty($plan_qty)) ? (($row->ok_qty * 100) / $plan_qty) : 0;
                //changed by mansee
                if(!empty($performanceTime))
				{
					$performance = (!empty($row->cycle_time)) ? (((($row->cycle_time+$row->load_unload_time)*$row->ok_qty)/($performanceTime))/60)*100 : 0;                    
                }
				else
				{
					$performance = 0;
				}
                // $oee = ($availability + $performance) / 2;//changed by mansee $opData->emp_name
                
                $oee = (($availability/100) * ($performance/100))*100;
                $tbody .= '<tr class="text-center">
										<td>' . $i++ . '</td>
										<td>' . formatDate($row->log_date) . '</td>
										<td>' . $row->shift_name . '</td>
										<td>' . $operator . '</td>
										<td>' . $plan_time . '</td>
										<td>' . (int)$plan_qty . '</td>
										<td>' . number_format($runTime,2) . '</td>
										<td>' . $row->ok_qty . '</td>
										<td>' . $row->idle_time . '</td>
                                        <td>' . number_format($availability,2) . '%</td>
										<td>' . number_format($performance,2) . '%</td>
										<td>' . number_format($oee,2) . '%</td>
								</tr>';
            endforeach;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
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
         $this->data['itemList'] = $this->productionReports->getProductList(1);
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
                 $thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th>';
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
                     //Packed Qty.
                    //  $stockQry = [
                    //      'item_id' => $row->product_id,
                    //      'from_location_id' => $this->RTD_STORE->id,
                    //      'batch_no' => getPrefixNumber($row->job_prefix, $row->job_no),
                    //      'ref_type' => 16 //16=Packing
                    //  ];
                     $stock = $this->store->getItemCurrentStock($row->product_id,$this->RTD_STORE->id);
                     if ($l == 0) {
                         $thead .= '<th>Packed <br>(Ok Qty.)</th>';
                     }
                     $qtyTD .= '<td>' . ((!empty($stock) && !empty($stock->qty)) ? abs($stock->qty) : '-') . '</td>';
                     $tbody .= '<tr class="text-center">
                                 <td>' . getPrefixNumber($row->job_prefix, $row->job_no) . '</td>
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
                 <td>' . getPrefixNumber($row->job_prefix, $row->job_no) . '</td>
                 <td>' . formatDate($row->job_date) . '</td>
                 <td>' . $row->item_code . '</td>
                 <td>' . $cname . '</td>
                 <td>' . $row->challan_no . '</td>
                 <td>' . $row->heat_no . '</td>
                 <td>' . $row->total_weight . '</td>
                 <td>' . floatVal($row->qty) . '</td>
                 <td>' . floatVal($qtyData->ok_qty) . '</td>
                 <td>' . floatVal($qtyData->rejection_qty) . '</td>
                 <td>' . floatVal($row->qty - $qtyData->ok_qty) . '</td>
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
                                <td>' . formatDate($row->log_date) . '</td>
                                <td>' . $row->shift_name . '</td>
                                <td>' . $row->machine_no . '</td>
                                <td>' . $row->item_code . '</td>
                                <td>' . $row->process_name . '</td>';
                    if($data['type'] == 1):
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->ok_qty . '</td>';
                    elseif($data['type'] == 2):
                        $tbody .= '<td>' . $row->rw_qty . '</td>
                                <td>' . $row->rej_qty . '</td>';
                    else:
                        $tbody .= '<td>' . $row->cycle_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td>' . $row->ok_qty . '</td>
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
            $deptData = $this->department->getMachiningDepartment(8);
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
}
