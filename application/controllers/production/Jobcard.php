<?php
class Jobcard extends MY_Controller{
    private $indexPage = "production/jobcard/index";
    private $jobcardForm = "production/jobcard/form";
    private $jobcardDetail = "production/jobcard/jobcard_detail";
    private $jobDetail = "production/jobcard/jobcard_detail1";
    private $material_return_form = "production/jobcard/material_return_form";
    private $updateJobForm = "production/jobcard/update_job";
    private $settingParamForm = "production/jobcard/setting_param_form";
    private $change_job_stage = "production/jobcard/change_job_stage"; 

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Jobcard";
        $this->data['headData']->controller = "production/jobcard";
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "production/jobcard";
        $this->data['tableHeader'] = getProductionHeader("jobcard");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->jobcard->getDTRows($data, 0);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name)) ? $row->party_name : "Self Stock";
            $row->party_code = (!empty($row->party_code)) ? $row->party_code : "Self Stock";
            if ($row->order_status == 0) :
                $row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            elseif ($row->order_status == 1) :
                $row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif ($row->order_status == 2) :
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif ($row->order_status == 3) :
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif ($row->order_status == 4) :
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            else :
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;

            $lastLog = $this->jobcard->getLastTrans($row->id);
            $row->last_activity = (!empty($lastLog)) ? $lastLog->updated_at : "";

            $pendingdata = $this->jobcard->getJobPendingQty($row->id);
            if (!empty($pendingdata)) {
                $row->pendingQty = $pendingdata->in_qty - $pendingdata->ok_qty;
            } else {
                $row->pendingQty = 0;
            }
            
            $row->material_status = $this->jobcard->getMaterialStatus($row->id);

            $row->controller = $this->data['headData']->controller;
            $row->loginID = $this->session->userdata('loginId');
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJobcard()
    {
        $this->data['jobPrefix'] = "JC";
        $this->data['jobNo'] = sprintf('%04d', $this->jobcard->getNextJobNo(0));
        $this->data['jobwPrefix'] = "JCW";
        $this->data['jobwNo'] = sprintf('%04d', $this->jobcard->getNextJobNo(1));
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['productData'] = $this->item->getItemList(1);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->load->view($this->jobcardForm, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if(!empty($data['error_msg']))
            //$errorMessage['error_msg'] = "Some Details are missing...";
        if ($data['party_id'] == ""){
            $errorMessage['party_id'] = "Customer is required.";
        }    
        if (empty($data['product_id'])){
            $errorMessage['product_id'] = "Product is required.";
        }    
        if (empty($data['qty']) || $data['qty'] == "0.000"){
            $errorMessage['qty'] = "Quantity is required.";
        }    
        if (empty($data['process'])){
            $errorMessage['process'] = "Product Process is required.";
        }
        if (empty($data['job_date'])) :
            $errorMessage['job_date'] = "Date is required.";
        else :
            if (($data['job_date'] < $this->startYearDate) or ($data['job_date'] > $this->endYearDate))
                $errorMessage['job_date'] = "Invalid Date";
        endif;
        /*if(!empty($data['so_trans_id'])){
            $soData = $this->salesOrder->getSoByTransId(['so_trans_id'=>$data['so_trans_id']]);
            $old_qty = 0;
            if(!empty($data['id'])){
                $jobData = $this->jobcard->getJobcard($data['id']);
                $old_qty = $jobData->qty;
            }
            $plusQty = (10*$soData->qty)/100;
            $totalSOQty = $soData->qty+round($plusQty);
            $total_production_qty = $data['qty'] + ($soData->production_qty-$old_qty);
            if($total_production_qty > $totalSOQty){
                $errorMessage['qty'] = "Quantity is invalid.";
            }

        }*/
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard->save($data));
        endif;
    }
 
    public function edit()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->jobcard->getJobcard($id); // print_r($this->data['dataRow']);exit;
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['customerSalesOrder'] = $this->jobcard->getCustomerSalesOrder($this->data['dataRow']->party_id);

        $productPostData = ['sales_order_id' => $this->data['dataRow']->sales_order_id, 'product_id' => $this->data['dataRow']->product_id];
        $this->data['productData'] = $this->jobcard->getProductList($productPostData);

        $productProcessData = ['product_id' => $this->data['dataRow']->product_id];
        $this->data['productProcessAndRaw'] = $this->jobcard->getProductProcess($productProcessData, $id);

        $this->data['allocatedMaterial'] = $this->jobcard->getAllocatedMaterial($id);

        $this->load->view($this->jobcardForm, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->jobcard->delete($id));
        endif;
    }

    public function customerSalesOrderList()
    {
        $orderData = $this->jobcard->getCustomerSalesOrder($this->input->post('party_id'));
        $options = "<option value=''>Select Order No.</option>";
        foreach ($orderData as $row) :
            $options .= '<option value="' . $row->id . '">' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</option>';
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function getProductList()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->getProductList($data));
    }

    public function getProductProcess()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->getProductProcess($data));
    }

    public function materialRequest(){
        $id = $this->input->get_post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard->getJobcard($id);
        $this->data['allocatedMaterial'] = $this->jobcard->getAllocatedMaterial($id);
        $this->data['machineList'] = $this->machine->getMachineList();
        $this->load->view('production/jobcard/material_request', $this->data);
    }

    public function getBatchNo()
    {
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id, $location_id);

        $options = '<option value="">Select Batch No.</option>';
        foreach ($batchData as $row) :
            if ($row->qty > 0) :
                $options .= '<option value="' . $row->batch_no . '"  data-stock="' . $row->qty . '">' . $row->batch_no . '</option>';
            endif;
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    public function saveMaterialRequest()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if($data['used_at'] == "")
            $errorMessage['used_at'] = "Dispatch To is required.";
        if(empty($data['handover_to']))
            $errorMessage['handover_to'] = "Dispatch Location is required.";

        $i = 1;
        if(!empty($data['item'])):
            foreach($data['item'] as $row):
                if(empty($row['req_qty'])):
                    $errorMessage['request_qty_'.$i] = "Req. Qty. is required.";
                endif;
                if(!empty($row['req_qty']) && $row['req_qty'] > $row['pending_qty']):
                    $errorMessage['request_qty_'.$i] = "Invalid Qty.";
                endif;$i++;
            endforeach;
        else:
            $errorMessage['general_error'] = "Material is required.";
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobcard->saveMaterialRequest($data));
        endif;
    }

    public function materialReceived()
    {
        $data = $this->input->post();
        $this->printJson($this->jobcard->materialReceived($data));
    }

    public function changeJobStatus()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->jobcard->changeJobStatus($data));
        endif;
    }

    public function view($id){
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
        $this->load->view($this->jobcardDetail, $this->data);
    }


    public function jobDetail($id)
    {
        $jobCardData = $this->jobcard->getJobcard($id);

        if (empty($jobCardData->party_name)) {$jobCardData->party_name = "Self Stock";}

		$process = explode(",",$jobCardData->process);
		$jobApprovalData = Array();
		if($jobCardData->order_status == 0) // If Job card has not Started
		{
			foreach ($process as $process_id)
			{
				$prData = New Stdclass();
				if(!empty($process_id)):
					$prData = $this->process->getProcess($process_id);
				endif;
				$jobApprovalData['in_process_id'] = $process_id;
				$jobApprovalData['process_name'] = ((!empty($prData)) ? $prData->process_name : "Raw Material");
			}
		}
		else // If Job card Started
		{
			$jobApprovalData = $this->processMovement->getApprovalDataByJob($id,1);
		}	
		
        $jobCardData->first_process_id = $process[1];
        $dataRows = array();

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty)) ? ($totalCompleteQty * 100 / (($jobCardData->qty * count($process)))) : "0";
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
		
		$jobApprovalData1 = $jobApprovalData;
		$jobApprovalData = Array();
        $rej_belongs = $this->processMovement->getRejBelongsToStages($id);
		foreach($jobApprovalData1 as $jaRow)
		{
			foreach($rej_belongs as $rjRow)
			{
				if(!empty($rjRow->process_id) AND $jaRow->in_process_id==$rjRow->process_id)
				{
					$jaRow->total_rej_belongs = $rjRow->total_rej_belongs;
				}else{$jaRow->total_rej_belongs = 0;}
			}
			$jobApprovalData[] = $jaRow;
		}

        $jobCardData->processData = $dataRows;
        $this->data['dataRow'] = $jobCardData;
        $this->data['jobApprovalData'] = $jobApprovalData;
        $this->data['jobBom'] = $this->jobcard->getProcessWiseRequiredMaterials($jobCardData)['result'];
        $reqMaterials = $this->jobcard->getMaterialIssueDataNew($jobCardData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData']:'';
        $this->data['reqMaterialsRows'] = (!empty($reqMaterials['result']))?$reqMaterials['result']:'';
		//print_r($jobApprovalData);exit;
        $this->load->view($this->jobDetail, $this->data);
    }

    public function getLastActivitLog()
    {
        $trans_id = $this->input->post('trans_id');
        $transData = $this->jobcard->getLastActivitLog($trans_id);

        $tbody = '';
        $i = 1;
        $activity = '';
        if (!empty($transData)) {
            foreach ($transData as $row) :
                $created_at = date("Y-m-d H:i", strtotime($row->created_at));
                $updated_at = date("Y-m-d H:i", strtotime($row->updated_at));
                if ($created_at == $updated_at) {
                    $activity = 'Created';
                } else {
                    $activity = 'Updated';
                }
                $empData = $this->employee->getEmp($row->created_by);
                $tbody .= '<tr>
                    <td>' . $i . '</td>
                    <td>' . formatDate($row->entry_date) . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . $row->production_time . '</td>
                    <td>' . $empData->emp_name . '</td>
                    <td>' . $activity . '</td>
                </tr>';
                $i++;
            endforeach;
        } else {
            $tbody .= '<tr>
                <td class="text-center" colspan="8">No Data Found</td>
            </tr>';
        }

        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function updateJobProcessSequance()
    {
        if (empty($this->input->post('id')) or empty($this->input->post('process_id'))) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $stageRows = $this->jobcard->updateJobProcessSequance($this->input->post());
            $this->printJson(['status' => 1, 'stageRows' => $stageRows[0], 'pOptions' => $stageRows[1]]);
        endif;
    }

    public function removeJobStage()
    {
        if (empty($this->input->post('id')) or empty($this->input->post('process_id'))) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $stageRows = $this->jobcard->removeJobStage($this->input->post());
            $this->printJson(['status' => 1, 'stageRows' => $stageRows[0], 'pOptions' => $stageRows[1]]);
        endif;
    }
   

    /* Process identification tag Print Data */
    public function printProcessIdentification($id)
    {
        $jobData = $this->processMovement->getOutwardTransPrint($id);
        $partyName = (!empty($jobData->party_name)) ? $jobData->party_name : "In House";
        $title = (!empty($jobData->party_name)) ? "(Vendor Awaiting Inspection)" : "";
        $process_name = (!empty($jobData->process_name)) ? $jobData->process_name : "Raw Material";
        $next_process = (!empty($jobData->next_process)) ? $jobData->next_process : "Packing";
        if (!empty($jobData->next_process)) {
            $mtitle = 'Process Tag';
            $revno = date('d.m.Y <br> h:i:s A');
        } else {
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }

        $logo = base_url('assets/images/logo.png');


        $topSectionO = '<table class="table">
                                <tr>
                                    <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                                    <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $mtitle . ' <br><small><span class="text-dark">' . $title . '</span></small></td>
                                    <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '</td>
                                </tr>
                            </table>';
							
		$itemList = '<table class="table table-bordered vendor_challan_table">
					<tr>
						<td style="font-size:0.7rem;"><b>Job No</b></td>
						<td style="font-size:0.7rem;">' . $jobData->job_number . '</td>
						<td style="font-size:0.7rem;"><b>Date</b></td>
						<td style="font-size:0.7rem;">' . formatDate($jobData->entry_date) . '</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>W.O. No.</b></td>
						<td style="font-size:0.7rem;">' . $jobData->wo_no . '</td>
						<td style="font-size:0.7rem;"><b>Part</b></td>
						<td style="font-size:0.7rem;">' . $jobData->item_code . '</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Job Qty</b></td>
						<td style="font-size:0.7rem;">' . $jobData->qty . '</td>
						<td style="font-size:0.7rem;"><b>Batch No.</b></td>
						<td style="font-size:0.7rem;">' . $jobData->batch_no . '</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Completed Process</b></td>
						<td style="font-size:0.7rem;" colspan="3">' . $process_name . '</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Completed Qty</b></td>
						<td style="font-size:0.7rem;">' . $jobData->qty . '</td>
						<td style="font-size:0.7rem;"><b>M/c / Vendor:</b></td>
						<td style="font-size:0.7rem;">' . $partyName . '</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Next Process</b></td>
						<td style="font-size:0.7rem;">' . $next_process . '</td>
						<td style="font-size:0.7rem;"><b>Next M/c No.</b></td>
						<td style="font-size:0.7rem;">' . $partyName . '</td>
					</tr>
				</table>';
        $originalCopy = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSectionO . $itemList . '</div>';
		
        $pdfData = $originalCopy;
        // print_r($pdfData);exit; 
        //$mpdf = $this->m_pdf->load();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = $mtitle . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 2, 2, 2, 2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }
    
    public function printTag($tag_type,$id){
        $tagData = $this->jobcard->getTagData($id);   
        $rejData = $this->processMovement->getRejCFTData(['job_trans_id'=>$tagData->id,'entry_type'=>1]);
        
        $vendorName = (!empty($tagData->party_name)) ? $tagData->party_name : $tagData->operator_name;
        $title = "";$mtitle = "";$revno = "";$qtyLabel = "";$qty = 0;
        if($tag_type == "REJ"):
            $mtitle = 'Rejection at M/c';
            $revno = 'R-QC-65 (00/01.10.22)';
            $qtyLabel = "Rej Qty";
            $qty = $rejData->rej_qty;
        elseif($tag_type == "REW"):
            $mtitle = 'Rework at M/c';
            $revno = 'R-QC-66 (00/01.10.22)';
            $qtyLabel = "RW Qty";
            $qty = $rejData->rw_qty;
        elseif($tag_type == "SUSP"):
            $mtitle = 'Suspected At M/c';
            $revno = 'R-QC-67 (00/01.10.22)';
            $qtyLabel = "Susp. Qty";
            $qty = $rejData->hold_qty;
        endif;

        $logo = base_url('assets/images/logo.png');


        $topSection = '<table class="table">
            <tr>
                <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $mtitle . ' <br><small><span class="text-dark">' . $title . '</span></small></td>
                <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '</td>
            </tr>
        </table>';
        
        $itemList = '<table class="table table-bordered vendor_challan_table">
			<tr>
				<td style="font-size:0.7rem;"><b>Job No</b></td>
				<td style="font-size:0.7rem;">' . $tagData->job_number . '</td>
				<td style="font-size:0.7rem;"><b>Date</b></td>
				<td style="font-size:0.7rem;">' . formatDate($tagData->entry_date) . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>W.O. No.</b></td>
				<td style="font-size:0.7rem;">' . $tagData->wo_no . '</td>
				<td style="font-size:0.7rem;"><b>Part</b></td>
				<td style="font-size:0.7rem;">' . $tagData->item_code . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Prod Qty</b></td>
				<td style="font-size:0.7rem;">' . ($tagData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty) . '</td>
				<td style="font-size:0.7rem;"><b>'.$qtyLabel.'</b></td>
				<td style="font-size:0.7rem;">'.$qty.'</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Vendor/Ope.</b></td>
				<td style="font-size:0.7rem;">' . $vendorName . '</td>
				<td style="font-size:0.7rem;"><b>M/c No</b></td>
				<td style="font-size:0.7rem;">[' .$tagData->machine_code.'] '. $tagData->machine_name . '</td>
			</tr>
			<tr>
				<td style="font-size:0.7rem;"><b>Issue By</b></td>
				<td style="font-size:0.7rem;" colspan="3">' . $tagData->emp_name . '</td>
			</tr>
		</table>';
		$pdfData = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSection . $itemList . '</div>';
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 60]]);
        $pdfFileName = str_replace(" ","_",str_replace("/"," ",$mtitle)) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 2, 2, 2, 2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    function printDetailedRouteCard($id)
    {
        $this->data['jobData'] = $this->jobcard->getJobcard($id);
        $jobCardData = $this->data['jobData'];
        $this->data['companyData'] = $this->jobcard->getCompanyInfo();
        $this->data['userDetail'] = $userDetail = $this->employee->getEmp($jobCardData->created_by);
        
        $reqMaterials = $this->jobcard->getMaterialIssueData($jobCardData); 
        $this->data['materialDetail'] =  (!empty($reqMaterials['result']))?$reqMaterials['result']:'';
        $this->data['inhouseProduction'] = $this->processMovement->getMovementTransactions($id,0);
		$this->data['vendorProduction'] = $this->processMovement->getMovementTransactions($id,4);
        $response = "";
        $logo = base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$this->data['letter_footer']=base_url('assets/images/lh-footer.png');

        
        $process = explode(",", $jobCardData->process);
        $dataRows = array();
        $totalCompleteQty = 0;
        $totalRejectQty = 0;

        foreach ($process as $key => $value) :
            $row = new stdClass;
            $jobProcessData = $this->processMovement->getProcessWiseApprovalData($id, $value);
            $processData = $this->process->getProcess($value);
            $row->process_name = (!empty($processData->process_name))?$processData->process_name:''; 
            //$prdProcess=$this->item->getPrdProcessDataProductProcessWise(['process_id'=>$value,'item_id'=>$jobCardData->product_id]);
          
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->total_ok_qty = (!empty($jobProcessData->ok_qty)) ? $jobProcessData->ok_qty : 0;
            $row->rework_qty = (!empty($jobProcessData->total_rework_qty)) ? $jobProcessData->total_rework_qty : 0;
            $row->rejection_qty = (!empty($jobProcessData->total_rejection_qty)) ? $jobProcessData->total_rejection_qty : 0;
            $row->ok_qty = (!empty($jobProcessData->ok_qty)) ? $jobProcessData->ok_qty : 0;
            $completeQty = $row->rework_qty + $row->rejection_qty + $row->ok_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->rework_qty + $row->rejection_qty;
            $dataRows[] = $row;
        endforeach;
        $this->data['processDetail'] = $dataRows;
        
        $pdfData = $this->load->view('production/jobcard/view', $this->data, true);
        $printedBy = $this->employee->getEmp($this->loginId);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img" style="border-bottom:1px solid #acacac">';
        $htmlFooter = '
			<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
				<tr>
					<td style="width:50%;">
					    Created By & Date : '.$userDetail->emp_name.' ('.formatDate($jobCardData->created_at, 'd-m-Y H:s:i').')<br>
					    Printed By & Date : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:s:i'), 'd-m-Y H:s:i').')
					</td>
					<td style="width:50%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';

        $mpdf = $this->m_pdf->load();
        $pdfFileName = 'DC-REG-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 45, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    /* Material Return , Scrap , Used in Job */
    public function materialReturn()
    {
        $data = $this->input->post();
        $this->data['locationList'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $data;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['batchData'] = $this->jobcard->getBatchNoForReturnMaterial($data['job_card_id'], $data['id'])['options']; 
        $issueMtrData = $this->jobcard->getJobBomRawMaterialData($data['job_card_id']); //print_r($issueMtrData);exit;
        $this->data['dataRow']['pendingQty'] = $issueMtrData->issue_qty - abs($issueMtrData->used_qty+$issueMtrData->return_qty);
        $this->data['jobData'] = $this->jobcard->getJobcard($data['job_card_id']);
        $this->data['transData'] = $this->jobcard->getMaterialReturnTrans(['job_card_id' => $data['job_card_id'], 'item_id' => $data['item_id']])['resultHtml'];
        $this->load->view($this->material_return_form, $this->data);
    }

    public function saveMaterialReturn()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        if (empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";
        if (empty($data['ref_type']))
            $errorMessage['ref_type'] = "Return Type is required.";
        if ($data['ref_type'] == 10) {
            if (empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
            if (empty($data['batch_no']))
                $errorMessage['batch_no'] = "Batch No is required.";
        }
        if ($data['ref_type'] == 18) {
            if (empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
        }
        if (!empty($errorMessage)) {
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        } else {
            $result = $this->jobcard->saveMaterialReturn($data);
            $this->printJson($result);
        }
    }

    public function deleteMaterialReturn()
    {
        $data = $this->input->post();
        $result = $this->jobcard->deleteMaterialReturn($data['id']);
        $this->printJson($result);
    }

    public function updateJobQty()
    {
        $this->data['job_card_id'] = $this->input->post('id');
        $this->data['logData'] = $this->jobcard->getJobLogData($this->data['job_card_id']);
        $this->load->view($this->updateJobForm, $this->data);
    }

    public function saveJobQty()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['log_type'] == -1) :
            $jobdata = $this->jobcard->getJobPendingQty($data['job_card_id']);
            $pendingQty = $jobdata->in_qty - $jobdata->total_out_qty;
            if ($pendingQty < $data['qty']) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->jobcard->saveJobQty($data);
            $tbody = '';
            $i = 1;
            if (!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1) ? '(+) Add' : '(-) Reduce';
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->log_date) . '</td>
                        <td>' . $logType . '</td>
                        <td>' . $row->qty . '</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }

    public function deleteJobUpdateQty()
    {
        $id = $this->input->post('id');
        $logdata = $this->jobcard->getJobLog($id);
        $errorMessage = '';
        if ($logdata->log_type == 1) :
            $jobdata = $this->jobcard->getJobPendingQty($logdata->job_card_id);
            $pendingQty = $jobdata->in_qty - $jobdata->total_out_qty;
            if ($pendingQty < $logdata->qty) :
                $errorMessage = "Sorry...! You can't delete this jobcard log because This Qty. moved to next process.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->jobcard->deleteJobUpdateQty($id);

            $tbody = '';
            $i = 1;
            if (!empty($result)) :
                foreach ($result as $row) :
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1) ? '(+) Add' : '(-) Reduce';
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->log_date) . '</td>
                        <td>' . $logType . '</td>
                        <td>' . $row->qty . '</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }

    public function getHandoverData(){
        $used_at = $this->input->post('used_at');
        $handover = '<option value="">Select</option>';
        if (empty($used_at)) :
            $empData = $this->machine->getMachineList();
            foreach ($empData as $row) :
                $handover .= "<option value='" . $row->id . "'   data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
            endforeach;
        else :
            $partyData = $this->party->getVendorList();
            foreach ($partyData as $row) :
                $handover .= "<option value='" . $row->id . "' data-row='" . json_encode($row) . "'>[" . $row->party_code . "] " . $row->party_name . " </option>";
            endforeach;
        endif;

        $this->printJson(['status' => 1, 'handover' => $handover]);
    }

    function printDetailedRouteCardNew($id)
    { 
        $this->data['jobData'] = $this->jobcard->getJobcard($id);
        $jobCardData = $this->data['jobData'];
        $this->data['companyData'] = $this->jobcard->getCompanyInfo();
        //$this->data['userDetail'] = $userDetail = $this->employee->getEmp($jobCardData->created_by);
        
        $issueMaterials = $this->jobcard->getMaterialIssueData($jobCardData); 
        $this->data['issueMaterialsDetail'] =  (!empty($issueMaterials['result']))?$issueMaterials['result']:''; 
        $reqMaterials = $this->jobcard->getJobBomData($id); 
        $this->data['materialDetail'] = $materialDetail = (!empty($reqMaterials))?$reqMaterials:[];
		$batchNo = array_column($materialDetail,'batch_no');
        $this->data['batch_no'] = (!empty($batchNo)?(implode(',',$batchNo)):'');
        $response = "";
        $logo = base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		$this->data['letter_footer']=base_url('assets/images/lh-footer.png');

        
        $process = explode(",", $jobCardData->process);
        $dataRows = array();
        $totalCompleteQty = 0;
        $totalRejectQty = 0;

        foreach ($process as $key => $value) :
            $row = new stdClass;
            //$jobProcessData = $this->processMovement->getProcessWiseApprovalData($id, $value);
            $processData = $this->process->getProcess($value);
            $row->process_name = (!empty($processData->process_name))?$processData->process_name:''; 
            /*$prdProcess=$this->item->getPrdProcessDataProductProcessWise(['process_id'=>$value,'item_id'=>$jobCardData->product_id]);
          
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->in_qty = (!empty($jobProcessData->in_qty)) ? $jobProcessData->in_qty : 0;
            $row->total_ok_qty = (!empty($jobProcessData->ok_qty)) ? $jobProcessData->ok_qty : 0;
            $row->rework_qty = (!empty($jobProcessData->total_rework_qty)) ? $jobProcessData->total_rework_qty : 0;
            $row->rejection_qty = (!empty($jobProcessData->total_rejection_qty)) ? $jobProcessData->total_rejection_qty : 0;
            $row->ok_qty = (!empty($jobProcessData->ok_qty)) ? $jobProcessData->ok_qty : 0;
            $completeQty = $row->rework_qty + $row->rejection_qty + $row->ok_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->rework_qty + $row->rejection_qty;*/
            $dataRows[] = $row;
        endforeach;
        $this->data['processDetail'] = $dataRows;
        
        $pdfData = $this->load->view('production/jobcard/viewNew', $this->data, true);
       
        $printedBy = $this->employee->getEmp($this->loginId);
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img" style="border-bottom:1px solid #acacac">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #000000;">
				<tr>
					<td>
					    Created By & Date : '.$jobCardData->created_name.' ('.formatDate($jobCardData->created_at, 'd-m-Y H:s:i').') <br>
						Printed By & Date : '.$printedBy->emp_name.' ('.formatDate(date('Y-m-d H:s:i'), 'd-m-Y H:s:i').')
					</td>
					<td style="text-align:right;">Page No. {PAGENO}/{nbpg}</td>
				</tr>
			</table>';
			
        $mpdf = $this->m_pdf->load(); //new \Mpdf\Mpdf();
        $pdfFileName = 'DC-REG-' . $id . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;

        $mpdf->AddPage('P', '', '', '', '', 5, 5, 45, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }
	
	public function addSettingParameter(){
        $data = $this->input->post();
        $this->data['job_approval_id'] = $data['id'];
        $this->data['insertData'] = $this->item->getItemDataList(['category_id'=>44]);
        $this->data['htmlData'] = $this->getSettingParamHtml($data['id']);
        $this->load->view($this->settingParamForm, $this->data);
    }

    public function saveSettingParameter()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['tool_no'])) :
            $errorMessage['tool_no'] = "Tool No is required";
        endif;
        if (empty($data['insert_id'])) :
            $errorMessage['insert_id'] = "Insert is required";
        endif;
        if (empty($data['entry_date'])) :
            $errorMessage['entry_date'] = "Date is required";
        endif;
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $approvalData = $this->processMovement->getApprovalData($data['job_approval_id']);
            $data['job_card_id'] = $approvalData->job_card_id;
            $data['product_id'] = $approvalData->product_id;
            $data['process_id'] = $approvalData->in_process_id;
            $result = $this->jobcard->saveSettingParameter($data);
            $result['htmlData'] = $this->getSettingParamHtml($data['job_approval_id']);
            $this->printJson($result);
        endif;
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

    public function deleteSettingParam(){
        $id = $this->input->post('id');
        $job_approval_id = $this->input->post('job_approval_id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->jobcard->deleteSettingParam($id);
            $result['htmlData'] = $this->getSettingParamHtml($job_approval_id);
            $this->printJson($result);
        endif; 
    }

    public function approveRM()
    {
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->jobcard->approveRM($data));
        endif;
    }


    /***  * Created By Mansee [26-07-2023]  * Change ,Remove,Add Job  Stages */
    
    public function changeJobStage(){
        $this->data['jobList'] = $this->jobcard->getJobcardList(3);
        $this->load->view($this->change_job_stage, $this->data);
    }

    public function getJobStages(){
        $data = $this->input->post();
        $result = $this->getJobStagesHtml($data);;
        
        $this->printJson(['status' => 1, 'stageRows' => $result['stageRows'],'processOptions'=>$result['processOptions']]);
    }

    public function getJobStagesHtml($data){
        $stageData = $this->processMovement->getApprovalDataByJob($data['job_card_id'],1);
        $jobData = $this->jobcard->getJobcard($data['job_card_id']);
        $stageRows = '';
        if (!empty($stageData)) :
            $i = 1;
            foreach ($stageData as $row) :
                if(($row->inward_qty+$row->outward_qty) <= 0 ){
                    $stageRows .= '<tr id="' . $row->in_process_id . '">
                    <td class="text-center">' . $i. '</td>
                    <td>' . $row->process_name .'| '.$row->in_process_id . '* .'.$row->out_process_id.'</td>
                    <td class="text-center">

                        <input type="hidden" name="in_process_id[]" value="' . $row->in_process_id . '">
                        <button type="button" data-pid="' . $row->in_process_id . '" class="btn btn-outline-danger waves-effect waves-light permission-remove " onclick="removeJobStage(this)"><i class="ti-trash"></i></button>
                    </td>
                    </tr>';
                    $i++;
                }
               
            endforeach;
        else :
            echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        
        $processData = $this->process->getProcessList();
        $processOptions ='<option value="">Select Stage</option>';
        $productProcess = explode(",",  $jobData->process);
        foreach ($processData as $row) :
            if (!empty($productProcess) && (!in_array($row->id, $productProcess))) :
                $processOptions .= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
            endif;
        endforeach;
        return (['status' => 1, 'stageRows' => $stageRows,'processOptions'=>$processOptions]);
    }


    public function saveJobProcessSequence()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id'])){ $errorMessage['job_card_id'] = "Job Number is required."; }    
        if (empty($data['in_process_id'][0])){ $errorMessage['stage_error'] = "Job Process is required."; }
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result = $this->jobcard->saveJobProcessSequence($data);
            $this->printJson($result);
        endif;
    }

    public function addJobStage()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id'])){ $errorMessage['job_card_id'] = "Job Number is required."; }    
        if (empty($data['process_id'])){ $errorMessage['stage_id'] = "Process is required."; }
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $result =  $this->jobcard->addJobStage($data);
            $jobStageData = $this->getJobStagesHtml($data);
            $result['stageRows'] = $jobStageData['stageRows'];
            $result['processOptions'] = $jobStageData['processOptions'];
            $this->printJson($result);
        endif;

    }

}
