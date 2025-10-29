<?php
class ProductionReport extends MY_Controller
{
    private $production_report_page = "report/production/index";
    private $job_wise_production = "report/production/job_production";
    private $jobwork_register = "report/production/jobwork_register";
    private $production_analysis = "report/production/production_analysis";
    private $stage_production = "report/production/stage_production";
    private $jobcard_register = "report/production/jobcard_register";
    private $machinewise_production = "report/production/machinewise_production";
    private $general_oee = "report/production/general_oee";
    private $operator_monitor = "report/production/operator_monitor";
    private $rejection_monitoring = "report/production/rejection_monitoring";
    private $operator_performance = "report/production/operator_performance";
    private $production_bom = "report/production/production_bom";
    private $rm_planing = "report/production/rm_planing";
    private $fg_tracking = "report/production/fg_tracking";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Report";
		$this->data['headData']->controller = "reports/productionReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/production/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PRODUCTION REPORT';
        $this->load->view($this->production_report_page,$this->data);
    }

    /* Job Wise Production */    
    public function jobProduction($item_id=""){
        $this->data['pageHeader'] = 'JOB WISE PRODUCTION';
		$this->data['jobcardData'] = $this->productionReports->getJobcardList();
		$this->data['itemId'] = $item_id;
        $this->load->view($this->job_wise_production,$this->data);
    }

    public function getJobWiseProduction()
	{
		$data = $this->input->post();
        $result = $this->productionReports->getJobWiseProduction($data);
        $this->printJson($result);
    }

    /* Jobwork Register */
    public function jobworkRegister(){
        $this->data['pageHeader'] = 'JOB WORK OUTWARD-INWARD REGISTER';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->load->view($this->jobwork_register,$this->data);
    }

    public function getJobworkRegister(){
        $data = $this->input->post();
        $jobOutData = $this->productionReports->getJobworkRegister($data);

        $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
        $i=1; $tblData = "";
        foreach($jobOutData as $row): 
            $pids = explode(',',$row->process);$idx=0;$md="";
            foreach($pids as $key=>$pid):
                if($pid == $row->process_id):
                    $idx=$key;
                endif;
            endforeach;
            $uname = $row->punit;$outQty = 0;$rmFlag=false;
            $umData = $this->productionReports->getUsedMaterial($row->material_used_id);
            if($idx == 0):
                $outQty = $row->issue_material_qty;
                $md = $umData->item_name;
                $uname = $umData->unit_name;    $rmFlag=true;            
            else:
                $outQty = $row->in_qty;
                $md = $this->process->getProcess($pids[$idx-1])->process_name.' Ok';
                $uname = $row->punit;
            endif;
            
            //Caret Details @AAVRUTI
            $jobChallanData = $this->productionReports->getJobChallan($row->id);
            $materialData = (!empty($jobChallanData[0]->material_data)? json_decode($jobChallanData[0]->material_data) : "");
            $outwardDetails=""; $inwardDetails="";
            if(!empty($materialData)): $i=1; 
                foreach($materialData as $chData):
                    $item_name = $this->item->getItem($chData->item_id)->item_name;
                    if($i==1){
                        $outwardDetails .= $item_name.' (Qty:. '.$chData->out_qty.')';
                        $inwardDetails .= $item_name.' (Qty:. '.$chData->in_qty.')';
                    }else{
                        $outwardDetails .= '<br> '.$item_name.' (Qty:. '.$chData->out_qty.')';
                        $inwardDetails .= '<br> '.$item_name.' (Qty:. '.$chData->in_qty.')';
                    }
                    $i++;
                endforeach;
            endif;
            
            $outData = $this->productionReports->getJobOutwardData($row->id);
            $rejectQty = 0;
			$rejectData = $this->productionReports->getVendorRejectionSum($row->id);
            if(!empty($rejectData)){$rejectQty = $rejectData->rejectQty;}
            $outCount = count($outData); //print_r($outData);exit;
            $tblData.='<tr>
                            <td>'.$i++.'</td>
                            <td>'.formatDate($row->entry_date).'</td>
                            <td>'.$row->jwo_no.'</td>
                            <td>'.$row->id.'</td>
                            <td>'.$row->item_code.'</td>
                            <td>'.$md.'</td>
                            <td>'.$row->process_name.'</td>
                            <td>'.$outQty.'</td>
                            <td>'.$uname.'</td>
                            <td>'.$row->issue_batch_no.'</td>
                            <td>'.$outwardDetails.'</td>
                            <td>'.$row->remark.'</td>';
            if($outCount > 0):
                $usedQty = 0;$j=1;
                foreach($outData as $outRow):
                        if($rmFlag):
                            $usedQty += ($umData->wp_qty * $outRow->in_qty) + ($umData->wp_qty * $rejectQty);
                        else:
                            $usedQty += $outRow->in_qty + $rejectQty;
                        endif;
                        $balQty = floatVal($outQty) - floatVal($usedQty);
                        
                        $tblData.='<td>'.formatDate($outRow->entry_date).'</td>
                                    <td>'.$outRow->item_code.'</td>
									<td>'.$row->id.'</td>
                                    <td>'.$outRow->challan_no.'</td>
                                    <td>'.floatVal($outRow->in_qty).'</td>
                                    <td>'.$row->punit.'</td>
                                    <td>'.$balQty.'</td>
                                    <td>'.floatVal($outRow->ud_qty + $rejectQty).'</td>
                                    <td>'.$row->issue_batch_no.'</td>
                                    <td>'.$inwardDetails.'</td>
                                    <td>'.$outRow->remark.'</td>';
                        if($j != $outCount){$tblData.='</tr><tr><td>'.$i++.'</td>'.$blankInTd;}
                        $j++;
                endforeach;      
            else:
                $tblData.='<td>&nbsp;</td>
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
            $tblData.='</tr>';
        endforeach;
        $this->printJson(['status'=>1,"tblData"=>$tblData]);
    }

    /* Production Analysis */
    public function productionAnalysis(){
        $this->data['pageHeader'] = 'PRODUCTION ANALYSIS';
        $this->load->view($this->production_analysis,$this->data);
    }

    public function getProductionAnalysis(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $productionData = $this->productionReports->getProductionAnalysis($data);
            $this->printJson($productionData);
        endif;
    }

    /* stage wise Production */
    public function stageProduction(){
        $this->data['pageHeader'] = 'STAGE WISE PRODUCTION';
        $this->data['itemList'] = $this->productionReports->getProductList(1);
        $this->data['processList'] = $this->process->getProcessList();
        $this->load->view($this->stage_production,$this->data);
    }

    public function getStageWiseProduction(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $stageData = $this->productionReports->getStageWiseProduction($data);
			$jobData = $stageData['jobData'];$processList = $stageData['processList'];

            //print_r($stageData);exit;
            $thead='';$tbody="";
			if(!empty($processList)):
				$thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th>';
				$l=0; $p=0;
				foreach($jobData as $row):
					$qtyTD = '';$qty = 0;
					foreach($processList as $pid):
                        $pcData = $this->process->getProcessDetail($pid);
                        $process_name = (!empty($pcData))?$pcData->process_name:"";
						if($l==0){$thead .= '<th>'.$process_name.'<br>(Ok Qty.)</th>';}
						if(in_array($pid,explode(',',$row->process))):							
							$qty = $this->productionReports->getProductionQty($row->id,$pid)->qty;
						endif;
						$qtyTD .= (!empty($qty)) ? '<td>'.floatVal($qty).'</td>' : '<td>-</td>';
					endforeach;
                    //Packed Qty.
                    $stockQry=[
                        'item_id'=>$row->product_id,
                        'from_location_id'=>$this->PROD_STORE->id,
                        'batch_no'=>getPrefixNumber($row->job_prefix,$row->job_no),
                        'ref_type'=>16 //16=Packing
                    ];
                    $stock = $this->store->checkBatchWiseStock($stockQry);
                    if($l==0){$thead .='<th>Packed <br>(Ok Qty.)</th>';}
                    $qtyTD .='<td>'.((!empty($stock) && !empty($stock->qty))?abs($stock->qty):'-').'</td>';
					$tbody .= '<tr class="text-center">
								<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
								<td>'.$row->item_code.'</td>
                                <td>'.floatVal($row->job_qty).'</td>
								'.$qtyTD.'
								<!--<th>'.floatVal($row->total_out_qty).'</th>-->
							</tr>';
					$l++;
				endforeach;
				// $thead .= '<th>Total<br>(Ok Qty.)</th></tr>';
			else:
				$thead = '<tr><th style="min-width:100px;">Job No.</th><th style="min-width:100px">Part No.</th><th style="min-width:100px">Job Card Qty.</th><th style="min-width:100px;">Process List</th></tr>';
			endif;
            

            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody]);
        
        endif;
    }
    
    /* Jobcard Register  */
    public function jobcardRegister(){
        $this->data['pageHeader'] = 'JOB CARD REGISTER';
        $this->data['jobCardData'] = $this->productionReports->getJobcardRegister();
        $this->load->view($this->jobcard_register,$this->data);
    }
    	
	/* Machine Wise Production */
    public function machineWise(){
        $this->data['pageHeader'] = 'MACHINE WISE OEE REGISTER';
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->load->view($this->machinewise_production,$this->data);
    }

    public function getMachineData(){
        $id = $this->input->post('dept_id');
        $machineData = $this->productionReports->getDepartmentWiseMachine($id);
        $option = '<option value="">Select Machine</option>';
        foreach ($machineData as $row):
            $option .= '<option value="' . $row->id . '" >['.$row->item_code.'] ' . $row->item_name . '</option>';
        endforeach;
        
        $this->printJson(['status'=>1, 'option'=>$option]);
    }

    public function getMachineWiseProduction(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $productionData = $this->productionReports->getMachineWiseProduction($data,$data['dept_id']);
            $this->printJson($productionData);
        endif;
    }

 	/* OEE Register */
    public function oeeRegister(){
        $this->data['pageHeader'] = 'GENERAL OEE REGISTER';
        $this->load->view($this->general_oee,$this->data);
    }


    /* Operator Monitoring */
    public function operatorMonitoring(){
        $this->data['pageHeader'] = 'OPERATOR MONITORING';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_monitor,$this->data);
    }

    public function getOperatorMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $productionData = $this->productionReports->getOperatorMonitoring($data);
            $this->printJson($productionData);
        endif;
    }
    
    /* Rejection Monitoring  avruti*/
    public function rejectionMonitoring(){
        $this->data['pageHeader'] = 'REJECTION & REWORK MONITORING REPORT';
        $this->data['itemDataList'] = $this->item->getItemList(1);
        $this->load->view($this->rejection_monitoring,$this->data);
    }

    public function getRejectionMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rejectionData = $this->productionReports->getRejectionMonitoring($data);
            $this->printJson($rejectionData);
        endif;
    }
    
    public function operatorPerformance(){
        $this->data['pageHeader'] = 'OPERATOR PERFORMANCE';
        $this->data['empData'] = $this->productionReports->getOperatorList();
        $this->load->view($this->operator_performance,$this->data);
    }

    public function getOperatorPerformance(){
        $data = $this->input->post();
        $performance = $this->productionReports->getOperatorPerformance($data);
        $this->printJson($performance);
    }
    
    /*Production Bom Created By Meghavi 1-1-22*/
    public function productionBom(){
        $this->data['pageHeader'] = 'Production Bom Report';
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['refItemData'] = $this->item->getItemList(3);
        $this->load->view($this->production_bom,$this->data);
    }

    public function getItemBomData(){
		$data = $this->input->post();
        $result = $this->productionReports->getItemWiseBom($data);
        $this->printJson($result);
    }

    public function getProductionBomData(){
        $data = $this->input->post();
        $result = $this->productionReports->getProductionBomData($data);
        $this->printJson($result);
    }

    // Avruti @3-2-2022
    public function rmPlaning()
    {
        $this->data['pageHeader'] = 'RM PLANING';
        $this->data['rmData'] = $this->item->getItemLists(3);
        $this->load->view($this->rm_planing, $this->data);
    }

    public function getRmPlaning(){
        $data = $this->input->post();
        $result = $this->productionReports->getRmPlaning($data);       

        $i=1; $tbody="";$theadVal = '';
        if(!empty($result)):
            foreach($result as $row):
                $theadVal = $row->ref_qty.' ('.$row->uname.')';
                $tbody .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>'.$row->item_code.' ['.$row->item_name.']</td>
                            <td>'.$row->qty.' ('.$row->uname.')</td>
                            <td>'.floor($row->ref_qty /$row->qty) .'  ('.$row->unit_name.')</td>
                        </tr>';
            endforeach;
        endif;
        $thead = '<tr>
            <th colspan="4">RM Stock : '.$theadVal.'</th>
        </tr>
        <tr>
            <th>#</th>
            <th>Finish Goods</th>
            <th>Bom Qty.</th>
            <th>Expected Ready Qty.</th>
        </tr>';
        $this->printJson(['status'=>1,'thead'=>$thead, 'tbody'=>$tbody]);
    }

    // Mansee @ 08-02-2022
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
}
?>