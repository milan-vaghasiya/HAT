<?php
class JobTracking extends MY_Controller
{
	private $indexPage = "app/job_tracking";
	private $jobIndexPage = "app/job_card_index";
	private $job_detail_view = "app/job_detail_view";
	private $productionLogFrom = "app/production_log_form";
	private $movementForm = "app/movement_form";
    private $idleTimeForm = "app/idle_time_form";
    private $settingParamForm = "app/setting_param_form";
    private $storeLocation = "app/store_location";

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Tracking";
		$this->data['headData']->controller = "app/JobTracking";
	}

	
	public function index($dispatch_status =0)
	{
        $this->data['dispatch_status'] = $dispatch_status;
		$this->data['bottomMenuName'] ='JobTracking';
		$this->data['soTransData'] = $this->salesOrder->getPendingSoTrans();	
			$this->load->view($this->indexPage, $this->data);
	}

    
	// Created By JP @ 06.07.2023
	public function trackOrderByTransId()
    {
        $postData = $this->input->post();
		$liHTml='';
		$html = '<h4 class="bg-white" style="font-size:2rem;text-align:center;color:#A90000;min-height:50vh">Sorry...!<br> No Data Found...!</h4>';
		$salesData = $this->salesOrder->getSoByTransId($postData);
		//print_r($salesData);exit;
		if(!empty($salesData))
		{
			$html = '<li>
						<div class="listItem item transition "  data-category="transition">
							<div class="in">
								<div>
									<a href="#" class="text-bold-500 text-dark">
									<label class="text-bold" >['.$salesData->item_code.'] '.$salesData->item_name.'</label>
									<div class="text-small  text-secondary "><label class="text-bold" >Cust.Po. No. : '.$salesData->doc_no.'</label></div>
									
									<div class="text-small  text-secondary "><label class="text-bold"><i class="fas fa-clock"></i> '.formatDate($salesData->trans_date).' | #'.getPrefixNumber($salesData->trans_prefix,$salesData->trans_no).'</label></div>
									</a>
									<div class="row text-center mt-2">
										<div class="col bg-info p-2">Order Qty <br>'.floatVal($salesData->qty).'</div>
										<div class="col bg-danger p-2">Dispatch Qty <br>'.floatVal($salesData->dispatch_qty).'</div>
										<div class="col bg-warning p-2">Pending Qty <br>'.floatVal(($salesData->qty - $salesData->dispatch_qty)).'</div>
										<div class="col bg-primary p-2">Stock Qty<br>'.floatVal($salesData->stock_qty).'</div>
									</div>
								</div>
								
							</div>
						</div></li>';
		}
		$prodData = '';
		// $prodData .= '<li>
							
		// 				</li>';
		if(!empty($salesData))
		{
			$jobData = $this->jobcard->trackOrderByTransId($postData);
			//print_r($jobData);exit;
			if(!empty($jobData))
			{
				$prodData .= '<li><div class="timeline timed ms-1 me-2">';
				foreach($jobData as $row)
				{
					$prodQty = $row->fq + $row->rq;
					$pendingQty = $row->oq - $prodQty;
					$workDone = 0;
					if(!empty($row->oq) AND $row->oq>0){$workDone = round((($prodQty * 100)/$row->oq),2);}
					$prodData .= '<div class="item">
										<span class="time">'.$workDone.'%</span>
										<div class="dot bg-primary"></div>
										<div class="content">
											<h4 class="title">'.$row->process_name.'</h4>
											<div class="text">
												<span class="badge badge-info">OQ : '.floatVal($row->oq).'</span>
												<span class="badge badge-success">FQ : '.floatVal($row->fq).'</span>
												<span class="badge badge-warning">RQ : '.floatVal($row->rq).'</span>
												<span class="badge badge-danger">PQ : '.floatVal($pendingQty).'</span>
											</div>
										</div>
									</div>';
					
				}
				$prodData .= '</div></li>';
			}
			else
			{
				$prodData .= '<h4 class="bg-white" style="font-size:2rem;text-align:center;color:#A90000;min-height:50vh">No Running<br>Production Found...!</h4>';
			}
		}
		$html .= $prodData;
		$htmlData = $liHTml.$html;
        $this->printJson(['html'=>$htmlData]);
    }

	public function jobCard($status = 0){
		$this->data['headData']->pageTitle = "Job Card";
		$this->data['status'] = $status;
		$this->data['bottomMenuName'] ='jobCard';
        $this->data['jobData'] = $this->jobcard->getJobListForApp(['status'=>$status]);;
		$this->load->view($this->jobIndexPage, $this->data);
	}

	public function jobDetail($id){
		$jobCardData = $this->jobcard->getJobcard($id);

        if (empty($jobCardData->party_name)){$jobCardData->party_name = "Self";}
        if (empty($jobCardData->party_code)){$jobCardData->party_code = "Self Stock";}

        $process = explode(",","0,".$jobCardData->process);
        $jobCardData->first_process_id = $process[1];
        $dataRows = array();
        $totalCompleteQty = 0;
        $totalRejectQty = 0;
        $stages = array();
        $stg = array();
        $s = 0;
        $runningStages = array();
        $totalScrapQty = 0;

        foreach ($process as $process_id) :
            $row = new stdClass;
            $jobApprovalData = $this->processMovement->getProcessWiseApprovalData($id, $process_id);
            $rej_belongs = $this->processMovement->getRejBelongsTo($id, $process_id);
           
            $row->process_id = $process_id;
            $row->process_name = (!empty($jobApprovalData->in_process_name)) ? $jobApprovalData->in_process_name : ((!empty($process_id)) ? $this->process->getProcess($process_id)->process_name : "Raw Material");
            $row->job_id = $id;
            $row->id = (!empty($jobApprovalData->id)) ? $jobApprovalData->id : 0;
            $row->product_id = $jobCardData->product_id;
            $row->product_code = $jobCardData->product_code;
            $row->vendor = (!empty($jobApprovalData->vendor)) ? $jobApprovalData->vendor : "";
            $row->inward_qty = (!empty($jobApprovalData->inward_qty)) ? $jobApprovalData->inward_qty : 0;
            $row->outward_qty = (!empty($jobApprovalData->outward_qty)) ? $jobApprovalData->outward_qty : 0;
            $row->in_qty = (!empty($jobApprovalData->in_qty)) ? $jobApprovalData->in_qty : 0;
            $row->ok_qty = (!empty($jobApprovalData->ok_qty)) ? $jobApprovalData->ok_qty : 0;
            $row->total_rejection_qty = (!empty($jobApprovalData->total_rejection_qty)) ? $jobApprovalData->total_rejection_qty : 0;
            $row->total_rej_belongs = (!empty($rej_belongs)) ? $rej_belongs : 0;
            $row->total_rework_qty = (!empty($jobApprovalData->total_rework_qty))?$jobApprovalData->total_rework_qty:0;
            $row->total_hold_qty = (!empty($jobApprovalData->total_hold_qty)) ? $jobApprovalData->total_hold_qty : 0;
            $row->v_prod_qty = (!empty($jobApprovalData->v_prod_qty)) ? $jobApprovalData->v_prod_qty : 0;
            $row->ih_prod_qty = (!empty($jobApprovalData->ih_prod_qty)) ? $jobApprovalData->ih_prod_qty : 0;
            $row->total_out_qty = (!empty($jobApprovalData->total_out_qty)) ? $jobApprovalData->total_out_qty : 0;

            // $row->unaccepted_qty = ($row->inward_qty + $row->outward_qty) - $row->in_qty;
            // $row->accepted_qty = $row->in_qty - $row->outward_qty;
            
            $row->ch_qty = (!empty($jobApprovalData->ch_qty)) ? $jobApprovalData->ch_qty : 0;
            $row->unaccepted_qty = ($row->inward_qty ) - ($row->in_qty-$row->ch_qty);
            $row->accepted_qty = ($row->in_qty + $row->outward_qty) - $row->ch_qty;
            $row->inhouse_prod_pending = $row->inward_qty - $row->ih_prod_qty - $row->unaccepted_qty;
            $row->vendor_prod_pending = $row->outward_qty - $row->v_prod_qty;
            $row->pending_movement = $row->ok_qty - $row->total_out_qty;

            $completeQty = $row->ok_qty + $row->total_rejection_qty;
            $row->pending_qty = $row->inhouse_prod_pending +  $row->vendor_prod_pending;

            $row->scrap_qty = (!empty($jobApprovalData->pre_finished_weight)) ? round(($jobApprovalData->pre_finished_weight - $jobApprovalData->finished_weight) * $row->in_qty, 2) : 0;
            $totalScrapQty += $row->scrap_qty;

            $processPer = ($completeQty > 0 && $row->in_qty > 0) ? ($completeQty * 100 / $row->in_qty) : "0";
            if ($completeQty == 0) :
                $row->status = '<span class="badge badge-pill badge-danger m-1">' . round($processPer, 2) . '%</span>';
            elseif ($row->in_qty > $completeQty) :
                $row->status = '<span class="badge badge-pill badge-warning m-1">' . round($processPer, 2) . '%</span>';
            elseif ($row->in_qty == $completeQty) :
                $row->status = '<span class="badge badge-pill badge-success m-1">' . round($processPer, 2) . '%</span>';
            else :
                $row->status = '<span class="badge badge-pill badge-dark m-1">' . round($processPer, 2) . '%</span>';;
            endif;

            $row->process_approvel_data = $jobApprovalData;
            $dataRows[] = $row;

            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->total_rejection_qty;

            if ($row->inward_qty == 0 and $row->in_qty == 0 and $s > 0) :
                $stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s - 1)];
            else :
                if(!empty($row->process_id)) :
                    $runningStages[] = $row->process_id;
                endif;
            endif;
            $s++;
        endforeach;
        $completeQty = 0;
        $processPer = 0;

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty)) ? ($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - $totalRejectQty )) : "0";
        $jobCardData->jobPer = round($jobProcessPer, 2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if ($jobCardData->order_status == 0) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
        elseif ($jobCardData->order_status == 1) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-primary m-1">Start</span>';
        elseif ($jobCardData->order_status == 2) :
            $jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
        elseif ($jobCardData->order_status == 3) :
            $jobCardData->order_status = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
        elseif ($jobCardData->order_status == 4) :
            $jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = '<span class="badge badge-pill badge-success m-1">Complete</span>';
        else :
            $jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
        endif;

        $stages['stages'] = $stg;
        $stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows;
        $this->data['dataRow'] = $jobCardData;
        $this->data['stageData'] = $stages;
        $this->data['totalScrapQty'] = $totalScrapQty;
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['jobBom'] = $this->jobcard->getProcessWiseRequiredMaterials($jobCardData)['result'];
        $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData']:'';
        $this->data['rmData'] = $this->jobcard->getJobBomRawMaterialData($id);
        $this->load->view($this->job_detail_view, $this->data);
	}

	public function processLog($id){
        $outwardData = $this->processMovement->getApprovalData($id);
        $outwardData->pqty = ($outwardData->in_qty * $outwardData->output_qty)-($outwardData->ch_qty * $outwardData->output_qty) - ($outwardData->ih_prod_qty);
        $this->data['dataRow'] = $outwardData;
        $this->data['dataRow']->ref_id = '';
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['outwardTrans'] = $this->processMovement->getOutwardTrans($outwardData->id)['htmlData'];
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComment'] = $this->comment->getReworkCommentList();

        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['masterOption'] = $this->processMovement->getMasterOptions(); 
        $prdPrsData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $outwardData->product_id, 'process_id' => $outwardData->in_process_id]);
        $this->data['cycle_time'] = (!empty($prdPrsData->cycle_time))?timeToSeconds($prdPrsData->cycle_time):0;
        $this->data['machineData'] = $this->item->getMachineTypeWiseMachine();
        $jobCardData = $this->jobcard->getJobcard($outwardData->job_card_id);
        $jobProcess = explode(",", $jobCardData->process);

        $reworkProcessHtml = '<option value="">Select Rework Process</option>';
        $stageHtml = '<option value="">Select Stage</option>
                    <option value="0" data-process_name="Row Material">Row Material</option>';
        if (!empty($outwardData->in_process_id)) {
            $in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
            foreach ($jobProcess as $key => $value) :
                if ($key <= $in_process_key) :
                    $processData = $this->process->getProcess($value);
                    $stageHtml .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
                endif;
                if ($key <= $in_process_key) {
                    $reworkProcessHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                }
            endforeach;
        }
        $this->data['dataRow']->stage = $stageHtml;
        $this->data['dataRow']->reworkProcess = $reworkProcessHtml;
        $this->load->view($this->productionLogFrom, $this->data);
	}

    public function processMovement($id){
        $approvalData = $this->processMovement->getApprovalData($id);
        $handover = '<option value="">Select</option>';$send_to = 0;
        if(empty($approvalData->in_process_id)):
            $mevedTo = $this->processMovement->getSendTo($approvalData->job_card_id); 
            $send_to = 0;           
            if (empty($mevedTo->used_at)) :
                $empData = $this->machine->getMachineList();
                foreach ($empData as $row) :
                    $selected = (0 == $row->id)?"selected":"";
                    $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "' ".$selected.">[" . $row->item_code . "] " . $row->item_name . " </option>";
                endforeach;
            else :
                $partyData = $this->party->getVendorList();
                foreach ($partyData as $row) :
                    $selected = (0 == $row->id)?"selected":"";
                    $handover .= "<option value='" . $row->id . "' data-row='" . json_encode($row) . "' ".$selected.">[" . $row->party_code . "] " . $row->party_name . " </option>";
                endforeach;
            endif;
        else:
            $empData = $this->machine->getMachineList();
            foreach ($empData as $row) :
                $selected = "";
                $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "' ".$selected.">[" . $row->item_code . "] " . $row->item_name . " </option>";
            endforeach;
        endif;

        $this->data['approvalData'] = $approvalData;
        $this->data['send_to'] = $send_to;
        $this->data['handover_to'] = $handover;
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_approval_id'=>$id]);
        $this->data['transHtml'] = $this->getProcessMovementTransHtml($id);

        $this->load->view($this->movementForm,$this->data);
    }

    public function getProcessMovementTransHtml($approval_id){
        $transData = $this->processMovement->getProcessMovementTrans($approval_id);
        $html = '';$i=1;
        if(!empty($transData)):
            foreach($transData as $row):
                $printBtn = '<a href="' . base_url('production/jobcard/printProcessIdentification/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->entry_date)).'</td>
                    <td>'.(($row->send_to == 0)?"In House":"Vendor").'</td>
                    <td>'.(($row->send_to == 0)?"[".$row->item_code."] ".$row->item_name:"[".$row->party_code."] ".$row->party_name).'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center">
                        '.$printBtn.'
                        <button type="button" onclick="trashMovement('.$row->id.','.$row->qty.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
            endforeach;
        else:
            $html = '<tr><td colspan="8" class="text-center">No Data Found.</td></tr>';
        endif;
        return $html;
    }

    public function idleTime($id){
        $approvalData = $this->processMovement->getApprovalData($id);
        $machineList = $this->machine->getProcessWiseMachine($approvalData->in_process_id);
        $this->data['dataRow'] = $approvalData;
        $this->data['machineList'] = $machineList;
        $this->data['idleReason'] = $this->comment->getIdleReason();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['transHtml'] = $this->getIdleTimeTransHtml($id);
        $this->load->view($this->idleTimeForm,$this->data);
    }

    public function getIdleTimeTransHtml($id){
        $transData = $this->processMovement->getIdleTimeData($id);
        $html = '';$i=1;
        if(!empty($transData)):
            foreach($transData as $row):
                $deleteBtn = '<button type="button" onclick="trashIdleTime('.$row->id.');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td class="text-center">'.formatDate($row->entry_date).'</td>
                    <td class="text-center">'.$row->production_time.'</td>
                    <td>'.((!empty($row->machine_code))?"[".$row->machine_code."] ":"").$row->machine_name.'</td>
                    <td>'.$row->shift_name.'</td>
                    <td>'.((!empty($row->operator_code))?"[".$row->operator_code."] ":"").$row->operator_name.'</td>
                    <td>'.((!empty($row->reason_code))?"[".$row->reason_code."] ":"").$row->reason.'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center">'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        else:
            $html = '<tr><td colspan="9" class="text-center">No data available in table</td></tr>';
        endif;

        return $html;
    }

    public function addSettingParameter($id){
        $this->data['job_approval_id'] = $id;
        $this->data['insertData'] = $this->item->getItemDataList(['category_id'=>44]);
        $this->data['htmlData'] = $this->getSettingParamHtml($id);
        $this->data['dataRow'] = $this->processMovement->getApprovalData($id);
        
        $this->load->view($this->settingParamForm, $this->data);
    }

    public function getSettingParamHtml($job_approval_id){
        $paramData = $this->jobcard->getSettingParamData(['job_approval_id'=>$job_approval_id]);
        $htmlData = "";
        if(!empty($paramData)){
            $i = 1;
            foreach($paramData as $row){

                $editBtn = "<button type='button' onclick='editSettingParam(".json_encode($row).",this);' class='btn btn-outline-warning waves-effect waves-light'><i class='ti-pencil-alt'></i></button>";
                $deleteBtn = ' <button type="button" onclick="removeSettingParam('.$row->id.','.$row->job_approval_id.',this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>';
                    										   
                $htmlData .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.formatDate($row->entry_date).'</td>
                    <td>'.$row->tool_no.'</td>
                    <td>'.$row->insert_name.'</td>
                    <td>'.$row->corner_radius.'</td>
                    <td>'.$row->grade.'</td>
                    <td>'.$row->make.'</td>
                    <td>'.$row->cutting_speed.'</td>
                    <td>'.$row->feed.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.$editBtn.$deleteBtn.'</td>
                </tr>';
                $i++;
            }
        }else{
            $htmlData .= '<tr><th colspan="11">No data available.</th></tr>';
        }
        return $htmlData;
    }

    public function storeLocation($id,$transid)
    {
      
        $jobcardData = $this->jobcard->getJobCard($id);
        $outwardData = $this->processMovement->getProcessMovementLastTrans($transid);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $this->data['dataRow'] = $outwardData;

        $this->data['job_id'] = $id;
        $this->data['ref_id'] = $transid;
        $this->data['jobNo'] = $jobcardData->job_number;
        $this->data['qty'] = $jobcardData->unstored_qty;
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['transactionData'] = $this->processMovement->getStoreLocationTrans($id);
        $this->data['heatData'] = $this->processMovement->getHeatData(['job_approval_id'=>$transid]);
        $this->load->view($this->storeLocation, $this->data);
    }
}
?>