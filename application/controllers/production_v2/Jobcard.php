<?php
class Jobcard extends MY_Controller{

    private $indexPage = "production_v2/jobcard/index";
    private $customerJobwork = "production_v2/jobcard/customer_jobwork";
    private $jobcardForm = "production_v2/jobcard/form";
    private $requiementForm = "production_v2/jobcard/required_test";
    private $jobcardDetail = "production_v2/jobcard/jobcard_detail";
    private $jobScrape = "production_v2/jobcard/generate_scrape";
    private $productionScrap = "production_v2/jobcard/scrap_form";
    private $updateJobForm = "production_v2/jobcard/update_job";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobcard";
		$this->data['headData']->controller = "production_v2/jobcard";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "production_v2/jobcard";
        $this->data['tableHeader'] = getProductionHeader("jobcard");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobcard_v2->getDTRows($data,0);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name))?$row->party_name:"Self Stock";
            $row->party_code = (!empty($row->party_code))?$row->party_code:"Self Stock";
			if($row->order_status == 0):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->order_status == 1):
				$row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif($row->order_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif($row->order_status == 3):
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif($row->order_status == 4):
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			else:
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
			endif;
			
			$lastLog = $this->jobcard_v2->getLastTrans($row->id);
            $row->last_activity = (!empty($lastLog))? $lastLog->updated_at : "";
			
			$pendingdata = $this->jobcard_v2->getJobPendingQty($row->id);
			if(!empty($pendingdata)){ $row->pendingQty = $pendingdata->in_qty - $pendingdata->out_qty; }else{ $row->pendingQty = 0; }
			
            $row->controller = $this->data['headData']->controller;
            $row->loginID = $this->session->userdata('loginId');
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function customerJobWork(){
        $this->data['headData']->pageUrl = "production_v2/jobcard/customerJobWork";
        $this->data['tableHeader'] = getProductionHeader("jobcard");
        $this->load->view($this->customerJobwork,$this->data);
    }

    public function customerJobWorkList($status=0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobcard_v2->getDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->party_name = (!empty($row->party_name))?$row->party_name:"Self Stock";
            $row->party_code = (!empty($row->party_code))?$row->party_code:"Self Stock";
			if($row->order_status == 0):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->order_status == 1):
				$row->order_status_label = '<span class="badge badge-pill badge-primary m-1">Start</span>';
            elseif($row->order_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-warning m-1">In-Process</span>';
            elseif($row->order_status == 3):
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">On-Hold</span>';
            elseif($row->order_status == 4):
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			else:
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
			endif;
            $row->controller = $this->data['headData']->controller;
            
			$lastLog = $this->jobcard_v2->getLastTrans($row->id);
            $row->last_activity = (!empty($lastLog))? $lastLog->updated_at : "";
            $row->trans_id = (!empty($lastLog))? $lastLog->id : "";
            
            $pendingdata = $this->jobcard_v2->getJobPendingQty($row->id);
			if(!empty($pendingdata)){ $row->pendingQty = $pendingdata->in_qty - $pendingdata->out_qty; }else{ $row->pendingQty = 0; }
            
            $row->loginID = $this->session->userdata('loginId');
            $sendData[] = getJobcardData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJobcard(){
        $this->data['jobPrefix'] = "JOB/".$this->shortYear.'/';
        $this->data['jobNo'] = $this->jobcard_v2->getNextJobNo(0);
        $this->data['jobwPrefix'] = "JOBW/".$this->shortYear.'/';
        $this->data['jobwNo'] = $this->jobcard_v2->getNextJobNo(1);
        $this->data['customerData'] = $this->jobcard_v2->getCustomerList();
        $this->data['productData'] = $this->item->getItemList(1);
        $this->load->view($this->jobcardForm,$this->data);
    }

    public function customerSalesOrderList(){
        $orderData = $this->jobcard_v2->getCustomerSalesOrder($this->input->post('party_id'));
        $options = "<option value=''>Select Order No.</option>";
        foreach($orderData as $row):
            $options .= '<option value="'.$row->id.'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getProductList(){
		$data = $this->input->post();
		$this->printJson($this->jobcard_v2->getProductList($data));
	}

    public function getProductProcess(){
        $data = $this->input->post();
        $this->printJson($this->jobcard_v2->getProductProcess($data));
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == "")
			$errorMessage['party_id'] = "Customer is required.";
		if(empty($data['product_id']))
			$errorMessage['product_id'] = "Product is required.";
		if(empty($data['qty']) || $data['qty'] == "0.000")
			$errorMessage['qty'] = "Quantity is required.";
		if(empty($data['process']))
			$errorMessage['process'] = "Product Process is required.";
		if(empty($data['job_date'])):
			$errorMessage['job_date'] = "Date is required.";
		else:
		    if(($data['job_date'] < $this->startYearDate) OR ($data['job_date'] > $this->endYearDate) )
			    $errorMessage['job_date'] = "Invalid Date";
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard_v2->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->jobcard_v2->getJobcard($id);
        $this->data['customerData'] = $this->jobcard_v2->getCustomerList();
        $this->data['customerSalesOrder'] = $this->jobcard_v2->getCustomerSalesOrder($this->data['dataRow']->party_id);

        $productPostData = ['sales_order_id'=>$this->data['dataRow']->sales_order_id,'product_id'=>$this->data['dataRow']->product_id];
        $this->data['productData'] = $this->jobcard_v2->getProductList($productPostData);

        $productProcessData = ['product_id'=>$this->data['dataRow']->product_id];
        $this->data['productProcessAndRaw'] = $this->jobcard_v2->getProductProcess($productProcessData,$id);

        $this->load->view($this->jobcardForm,$this->data);
    }

    public function view($id){
        $this->data['headData']->pageUrl = "production_v2/jobcard";
        $jobCardData = $this->jobcard_v2->getJobcard($id);
		
        if(empty($jobCardData->party_name)){$jobCardData->party_name ="Self";}
        if(empty($jobCardData->party_code)){$jobCardData->party_code ="Self Stock";}        
        
        $process = explode(",","0,".$jobCardData->process);
        $jobCardData->first_process_id = $process[1];
        $dataRows = array(); $totalCompleteQty=0; $totalRejectQty=0; $stages = array(); $stg = array(); $s=0;$runningStages = array(); $totalScrapQty=0;

        foreach($process as $process_id):
            $row = new stdClass;
            $jobApprovalData = $this->processApprove_v2->getProcessWiseApprovalData($id,$process_id);
            $logData=$this->logSheet->getPrdLogOnProcessNJob($id,$process_id);
            $rej_belongs=$this->logSheet->getRejBelongsTo($id,$process_id);

            $row->process_id = $process_id;
            $row->process_name = (!empty($jobApprovalData))?$jobApprovalData->in_process_name:((!empty($process_id))?$this->process->getProcess($process_id)->process_name:"Raw Material");
            $row->job_id = $id;
            $row->id = (!empty($jobApprovalData))?$jobApprovalData->id:0;
            $row->product_id = $jobCardData->product_id;
            $row->product_code = $jobCardData->product_code;
            $row->vendor = (!empty($jobApprovalData))?$jobApprovalData->vendor:"";
            $row->inward_qty = (!empty($jobApprovalData->inward_qty))?$jobApprovalData->inward_qty:0;
            $row->in_qty = (!empty($jobApprovalData))?$jobApprovalData->in_qty:0;
            $row->out_qty = (!empty($jobApprovalData))?$jobApprovalData->out_qty:0;
            $row->total_ok_qty = (!empty($jobApprovalData))?$jobApprovalData->total_ok_qty:0;
            $row->total_rejection_qty = (!empty($logData->rejection_qty))?$logData->rejection_qty:0;
            $row->total_rej_belongs = (!empty($rej_belongs))?$rej_belongs:0;
            $row->total_rework_qty =(!empty($logData->rework_qty))?$logData->rework_qty:0;;

            $completeQty = $row->out_qty + $row->total_rejection_qty /* + $row->total_rework_qty */;
            $row->pending_qty = $row->in_qty - $completeQty;

            $row->scrap_qty = (!empty($jobApprovalData))?round(($jobApprovalData->pre_finished_weight - $jobApprovalData->finished_weight) * $row->in_qty,2):0;
            $totalScrapQty += $row->scrap_qty;

            $processPer = ($completeQty > 0 && $row->in_qty > 0)?($completeQty * 100 / $row->in_qty):"0";
            if($completeQty == 0):
                $row->status = '<span class="badge badge-pill badge-danger m-1">'.round($processPer,2).'%</span>';
            elseif($row->in_qty > $completeQty):
                $row->status = '<span class="badge badge-pill badge-warning m-1">'.round($processPer,2).'%</span>';
            elseif($row->in_qty == $completeQty):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.round($processPer,2).'%</span>';
            else:
                $row->status = '<span class="badge badge-pill badge-dark m-1">'.round($processPer,2).'%</span>';;
            endif; 

            $row->process_approvel_data = $jobApprovalData;
            $dataRows[] = $row;

            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->total_rejection_qty;

            if($row->inward_qty == 0 and $row->in_qty == 0 and $s > 0):
				$stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s-1)];
			else:
                if(!empty($row->process_id)):
				    $runningStages[] = $row->process_id;
                endif;
			endif;
			$s++;
        endforeach;

        //$reworkData = $this->processApprove_v2->getReworkData($id);
        $reworkDataRows=array();
        $i=1;$prevProcessId=0;$completeQty=0;$processPer=0;$totalReworkRejectionQty = 0;
        /* foreach($reworkData as $row):
            $row->process_name = $this->process->getProcess($row->in_process_id)->process_name;
            $row->process_id = $row->in_process_id;
            $row->job_id = $id;
            if($i!=1):
                $prevProcessData = $this->production->getProcessWiseProduction($id,$prevProcessId,1);
                $row->inward_qty = (!empty($prevProcessData))?$prevProcessData->out_qty:0;
            else:
                $row->inward_qty = $row->in_qty;
            endif;
            $prevProcessId = $row->in_process_id;
            $row->rework_qty = $row->total_rework_qty;
            $row->rejection_qty = $row->total_rejection_qty;
            $row->out_qty = $row->out_qty;

            $completeQty = $row->total_rework_qty + $row->total_rejection_qty + $row->in_qty;

            $row->pending_qty = $row->inward_qty - $completeQty;

            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->inward_qty):"0";

            if($completeQty == 0):
                $row->status = '<span class="badge badge-pill badge-danger m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty > $completeQty):
                $row->status = '<span class="badge badge-pill badge-warning m-1">'.round($processPer,2).'%</span>';
            elseif($row->inward_qty == $completeQty):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.round($processPer,2).'%</span>';
            else:
                $row->status = round($processPer,2);
            endif;            

            $row->process_approvel_data = $row;
            $reworkDataRows[] = $row;
            $totalReworkRejectionQty += $row->total_rework_qty + $row->total_rejection_qty;
            $i++;
        endforeach; */

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty))?($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty + $totalReworkRejectionQty)) ):"0";
		$jobCardData->jobPer = round($jobProcessPer,2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if($jobCardData->order_status == 0):
            $jobCardData->order_status = '<span class="badge badge-pill badge-danger m-1">Pending - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 1):
            $jobCardData->order_status = '<span class="badge badge-pill badge-primary m-1">Start - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 2):
			$jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = '<span class="badge badge-pill badge-warning m-1">In-Process - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 3):
            $jobCardData->order_status = '<span class="badge badge-pill badge-info m-1">On-Hold - '.round($jobProcessPer,2).'%</span>';
        elseif($jobCardData->order_status == 4):
			$jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = '<span class="badge badge-pill badge-success m-1">Complete - '.round($jobProcessPer,2).'%</span>';
        else:
			$jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
        endif;
        
        $stages['stages'] = $stg;
		$stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows; 
        $jobCardData->reworkData = $reworkDataRows; 
        $this->data['reqMaterial'] = $this->jobcard_v2->getProcessWiseRequiredMaterial($jobCardData);
        $this->data['dataRow'] = $jobCardData;
        $this->data['stageData'] = $stages;
        $this->data['totalScrapQty'] = $totalScrapQty;
		$this->data['processDataList'] = $this->process->getProcessList();
        $this->data['jobBom']=$this->jobcard_v2->getJobBomQty($jobCardData->id,$jobCardData->product_id);
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        // $reqMaterials = $this->jobcard_v2->getProcessWiseRequiredMaterials($jobCardData); 
        $reqMaterials = $this->jobcard_v2->getMaterialIssueData($jobCardData); 
        $this->data['reqMaterials'] = (!empty($reqMaterials['resultData']))?$reqMaterials['resultData'][0]:'';
        $this->load->view($this->jobcardDetail,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard_v2->delete($id));
        endif;
    }

    public function materialRequest(){
        $id = $this->input->get_post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcard($id);     
        $this->data['disptachData'] = $this->jobcard_v2->getRequestItemData($id);   
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['kitData'] = $this->item->getProductKitData($id);
        $this->load->view('jobcard/material_request',$this->data);
    }

    public function saveMaterialRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        $i=1;
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Date is required.";
        if(empty($data['bom_item_id'])):
            $errorMessage['general_error'] = "Items is required.";
        else:
            foreach($data['bom_item_id'] as $key=>$value):
                if(empty($data['request_qty'][$key])):
                    $errorMessage['request_qty'.$i] = "Request Qty is required.";
                endif;
            $i++;
            endforeach;
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard_v2->saveMaterialRequest($data));
        endif;        
    }

    public function changeJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard_v2->changeJobStatus($data));
        endif;
    }

    public function saveJobBomItem(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['bom_item_id']))
            $errorMessage['bom_item_id'] = "Item Name is required.";
        if(empty($data['bom_qty']))
            $errorMessage['bom_qty'] = "Weight/Pcs is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $postData = [
                'id' => '',
                'job_card_id' => $data['bom_job_card_id'],
                'item_id' => $data['bom_product_id'],
                'ref_item_id' => $data['bom_item_id'],
                'qty' => $data['bom_qty'],
                'process_id' => $data['bom_process_id'],
                'created_by' => $this->loginId
            ];
            $this->printJson($this->jobcard_v2->saveJobBomItem($postData));
        endif;
    }

    public function deleteBomItem(){
        $id = $this->input->post('id');
        $job_card_id = $this->input->post('job_card_id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard_v2->deleteBomItem($id,$job_card_id));
        endif;
    }

    public function updateJobProcessSequance(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            //print_r($this->input->post());exit;
			$stageRows = $this->jobcard_v2->updateJobProcessSequance($this->input->post());
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }

    public function removeJobStage(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
			$stageRows = $this->jobcard_v2->removeJobStage($this->input->post());
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }

    public function getStoreLocation(){
        $locationData = $this->store->getStoreLocationList();
        $options = '<option value=""  data-store_name="">Select Location</option>';
        foreach($locationData as $lData):                            
            $options .= '<optgroup label="'.$lData['store_name'].'">';
            foreach($lData['location'] as $row):
                $options .= '<option value="'.$row->id.'" data-store_name="'.$lData['store_name'].'">'.$row->location.' </option>';
            endforeach;
            $options .= '</optgroup>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getBatchNoForReturnMaterial(){
        $job_id = $this->input->post('job_id');
        $item_id = $this->input->post('item_id');
        $this->printJson($this->jobcard_v2->getBatchNoForReturnMaterial($job_id,$item_id));
    }

    public function materialReceived(){
        $data = $this->input->post();
        $data['mr_at'] = date("Y-m-d H:i:s");
        $this->printJson($this->jobcard_v2->materialReceived($data));
    }

    public function generateScrap(){
        $data=$this->input->post();
        $this->data['job_card_id']=$data['job_card_id'];
        $this->data['totalScrapQty']=$data['scrap_qty'];
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->data['locationList'] = $this->store->getStoreLocationList();
        $this->data['reqMaterial'] = $this->jobcard_v2->getJobcardRowMaterial($data['job_card_id']);
        
        $this->load->view($this->productionScrap, $this->data);
    }

    public function saveProductionScrape(){
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobcard_v2->saveProductionScrape($data));
        endif;
    }

    public function getLastActivitLog(){
        $trans_id = $this->input->post('trans_id');
        $transData = $this->jobcard_v2->getLastActivitLog($trans_id);

        $tbody = ''; $i=1; $activity='';
        if(!empty($transData)){
            foreach($transData as $row):
                $created_at = date("Y-m-d H:i",strtotime($row->created_at));
                $updated_at = date("Y-m-d H:i",strtotime($row->updated_at));
                if($created_at == $updated_at){
                    $activity = 'Created';
                } else {
                    $activity = 'Updated';
                }
                $empData = $this->employee->getEmp($row->created_by); 
                
                $tbody .= '<tr>
                    <td>'.$i.'</td>
                    <td>'.formatDate($row->entry_date).'</td>
                    <td>'.$row->out_qty.'</td>
                    <td>'.$row->production_time.'</td>
                    <td>'.$empData->emp_name.'</td>
                    <td>'.$activity.'</td>
                </tr>';
                $i++;
            endforeach;
        } else { 
            $tbody .= '<tr>
                <td class="text-center" colspan="8">No Data Found</td>
            </tr>';
        }

        $this->printJson(['status'=>1,'tbody'=>$tbody]);
    }

    /* function printDetailedRouteCard($id){
		$this->data['jobData'] = $this->jobcard_v2->getJobcardDetail($id);
		$this->data['companyData'] = $this->jobcard_v2->getCompanyInfo();
		$this->data['userDetail'] = $this->employee->getEmp($this->data['jobData']->created_by);
		$this->data['materialDetail'] = $this->jobcard_v2->getMaterilIssueDetail($this->data['jobData']->id,$this->data['jobData']->product_id);
		$this->data['inhouseProduction'] = $this->production->getJobTransactions($this->data['jobData']->id,false);
		$this->data['vendorProduction'] = $this->production->getJobTransactions($this->data['jobData']->id,true);
		
		
		$jobCardData = $this->data['jobData'];
		$process = explode(",",$jobCardData->process);
        $dataRows = array(); $totalCompleteQty=0;  $totalRejectQty=0;
        foreach($process as $key=>$value):
            $row = new stdClass;
            $jobProcessData = $this->production->getProcessWiseProduction($id,$value);
            $row->process_name = $this->process->getProcess($value)->process_name;
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->rework_qty = (!empty($jobProcessData->total_rework_qty))?$jobProcessData->total_rework_qty:0;
            $row->rejection_qty = (!empty($jobProcessData->total_rejection_qty))?$jobProcessData->total_rejection_qty:0;
            $row->out_qty = (!empty($jobProcessData->out_qty))?$jobProcessData->out_qty:0;
            $completeQty = $row->rework_qty + $row->rejection_qty + $row->out_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $processPer = ($completeQty != "0.000")?($completeQty * 100 / $jobProcessData->in_qty):"0";
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->rework_qty + $row->rejection_qty;
            $dataRows[] = $row;
        endforeach; 
        $this->data['processDetail'] = $dataRows;
		
		$pdfData = $this->load->view('jobcard/view',$this->data,true);
		
		//$mpdf = $this->m_pdf->load();
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,3,3,0,0,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	} */
	/** Created By Mansee @ 19-02-22 */

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);

        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'"  data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    public function addJobStage(){
        if(empty($this->input->post('id')) OR empty($this->input->post('process_id'))):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $data = $this->input->post();
            $data['created_by'] = $this->session->userdata('loginId');
			$stageRows = $this->jobcard_v2->addJobStage($data);			
            $this->printJson(['status'=>1,'stageRows'=> $stageRows[0],'pOptions'=> $stageRows[1]]);
        endif;
    }
    
    /*function printDetailedRouteCard($id){
		$this->data['jobData'] = $this->jobcard->getJobcardDetail($id);
		$this->data['companyData'] = $this->jobcard->getCompanyInfo();
		$this->data['userDetail'] = $this->employee->getEmp($this->data['jobData']->created_by);
		$this->data['materialDetail'] = $this->jobcard->getMaterilIssueDetail($this->data['jobData']->id,$this->data['jobData']->product_id);
		$this->data['inhouseProduction'] = $this->production->getLogTransactions($this->data['jobData']->id,1);
		$this->data['vendorProduction'] = $this->production->getLogTransactions($this->data['jobData']->id,2);
		$response="";$logo=base_url('assets/images/logo.png');
		
		$jobCardData = $this->data['jobData'];
		$process = explode(",",$jobCardData->process);
        $dataRows = array(); $totalCompleteQty=0;  $totalRejectQty=0;   $stages = array();$stg = array();$s=0;$runningStages = Array();
        foreach($process as $key=>$value):
            $row = new stdClass();
            $jobProcessData = $this->production->getProcessWiseProduction($id,$value); 
            //print_r($jobProcessData);exit;
            $logData=$this->logSheet->getPrdLogOnProcessNJob($id,$value);
           
            $row->process_name = $this->process->getProcess($value)->process_name;
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->rework_qty = (!empty($logData->rework_qty))?$logData->rework_qty:0;
            $row->rejection_qty = (!empty($logData->rejection_qty))?$logData->rejection_qty:0;
            $row->out_qty = (!empty($jobProcessData->ok_qty))?$logData->ok_qty:0;
            
            $completeQty = $logData->rework_qty + $logData->rejection_qty + $row->out_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $logData->rework_qty + $logData->rejection_qty;
            $dataRows[] = $row;
        endforeach; 
        $this->data['processDetail'] = $dataRows;
		// /print_r($this->data);exit;

		$pdfData = $this->load->view('production_v2/jobcard/view',$this->data,true);
		//echo $pdfData;exit;
		//$mpdf = $this->m_pdf->load();
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,3,3,0,0,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}*/
	
	 function printDetailedRouteCard($id){
		$this->data['jobData'] = $this->jobcard->getJobcardDetail($id);
		$this->data['companyData'] = $this->jobcard->getCompanyInfo();
		$this->data['userDetail'] = $this->employee->getEmp($this->data['jobData']->created_by);
		$this->data['materialDetail'] = $this->jobcard->getMaterilIssueDetail($this->data['jobData']->id,$this->data['jobData']->product_id);
		$this->data['inhouseProduction'] = $this->production->getLogTransactions($this->data['jobData']->id,1);
		$this->data['vendorProduction'] = $this->production->getLogTransactions($this->data['jobData']->id,2);
		$response="";$logo=base_url('assets/images/logo.png');
		
		$jobCardData = $this->data['jobData'];
		$process = explode(",",$jobCardData->process);
        $dataRows = array(); $totalCompleteQty=0;  $totalRejectQty=0;   $stages = array();$stg = array();$s=0;$runningStages = Array();
        foreach($process as $key=>$value):
            $row = new stdClass;
            $jobProcessData = $this->processApprove_v2->getProcessWiseApprovalData($id,$value); //print_r($jobProcessData);exit;
            $logData=$this->logSheet->getPrdLogOnProcessNJob($id,$value);
        //    print_r($jobProcessData);exit;
            $row->process_name = $this->process->getProcess($value)->process_name;
            $row->process_id = $value;
            $row->job_id = $id;
            $row->regular_in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->in_qty = (!empty($jobProcessData->in_qty))?$jobProcessData->in_qty:0;
            $row->total_ok_qty = (!empty($jobProcessData->total_ok_qty))?$jobProcessData->total_ok_qty:0;
            $row->rework_qty = (!empty($logData->rework_qty))?$logData->rework_qty:0;
            $row->rejection_qty = (!empty($logData->rejection_qty))?$logData->rejection_qty:0;
            $row->out_qty = (!empty($logData->ok_qty))?$logData->ok_qty:0;
            $completeQty = $logData->rework_qty + $logData->rejection_qty + $row->out_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $processPer = ($completeQty != "0.000")?($completeQty * 100 / $jobProcessData->in_qty):"0";
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $logData->rework_qty + $logData->rejection_qty;
            $dataRows[] = $row;
        endforeach; 
        $this->data['processDetail'] = $dataRows;
		// /print_r($this->data);exit;

		$pdfData = $this->load->view('production_v2/jobcard/view',$this->data,true);
		
		//$mpdf = $this->m_pdf->load();
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,3,3,0,0,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	/* Created By avruti  @ 11/04/2022 */
    /* Process identification tag Print Data */
    public function printProcessIdentification($id){
        $jobData = $this->jobcard_v2->getOutwardTransPrint($id);      
        $partyName = (!empty($jobData->party_name))?$jobData->party_name:"In House";
        $title = (!empty($jobData->party_name))? "(Vendor Awaiting Inspection)":"";
        $process_name = (!empty($jobData->process_name))?$jobData->process_name:"Raw Material";
        $next_process = (!empty($jobData->next_process))? $jobData->next_process:"Packing";
        if(!empty($jobData->next_process)){
            $mtitle = 'Process Identification Tag';
            $revno = 'F ST 12<br>(00/01.08.2021)';
        }else{
            $mtitle = 'Final Inspection	OK Material';
            $revno = 'F QA 25<br>(01/01.10.2021)';
        }
        
        $logo=base_url('assets/images/logo.png');
		

            $topSectionO ='<table class="table">
                            <tr>
                                <td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
                                <td class="org_title text-center" style="font-size:1rem;width:60%">'.$mtitle.' <br><small><span class="text-dark">'.$title.'</span></small></td>
                                <td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">'.$revno.'</td>
                            </tr>
                        </table>';
            
            $itemList='<table class="table table-bordered recieveTag">
                    <tr>
						<td style="font-size:0.7rem;"><b>Vendor:</b></td>
                        <td style="font-size:0.7rem;">'.$partyName.'</td>
                        <td style="font-size:0.7rem;"><b>Job No:</b></td>
                        <td style="font-size:0.7rem;">'.getPrefixNumber($jobData->job_prefix, $jobData->job_no).'</td>
					</tr>
					<tr>
                        <td style="font-size:0.7rem;"><b>Date:</b></td>
                        <td style="font-size:0.7rem;">'.formatDate($jobData->entry_date).'</td>
                        <td style="font-size:0.7rem;"><b>Challan No : </b></td>
                        <td style="font-size:0.7rem;">'.$jobData->in_challan_no.'</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Part Code:</b></td>
                        <td style="font-size:0.7rem;">'.$jobData->item_code.'</td>
                        <td style="font-size:0.7rem;"><b>Department:</b></td>
                        <td style="font-size:0.7rem;">'.$jobData->dept_name.'</td>
					</tr>
					<tr>
						<td style="font-size:0.7rem;"><b>Qty:</b></td>
                        <td style="font-size:0.7rem;">'.$jobData->in_qty.'</td>
                        <td style="font-size:0.7rem;"><b>Batch/Heat <br> Code:</b></td>
                        <td style="font-size:0.7rem;">'.$jobData->job_batch_no.'</td>
					</tr>
                    <tr>
						<td style="font-size:0.7rem;"><b>Current Process:</b></td>
                        <td style="font-size:0.7rem;">'.$process_name.'</td>
                        <td style="font-size:0.7rem;"><b>Next Process:</b></td>
                        <td style="font-size:0.7rem;">'.$next_process.'</td>
					</tr>
                </table>';
        $originalCopy = '<div style="width:97mm;height:50mm;text-align:center;float:left;padding:0mm 0.7mm;">'.$topSectionO.$itemList.'</div>';
		
        $pdfData = $originalCopy;
		//print_r($pdfData);exit;
		//$mpdf = $this->m_pdf->load();
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 50]]);
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',0,0,2,2,2,2);
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    public function returnOrScrapeSave(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $result = $this->jobcard_v2->returnOrScrapeSave($data);
        $this->printJson($result);
    }

    public function deleteRetuenOrScrapeItem(){
        $data = $this->input->post();
        $result = $this->jobcard_v2->deleteRetuenOrScrapeItem($data['id']);
        $this->printJson($result);
    }
    
        public function updateJobQty(){
        $this->data['job_card_id'] = $this->input->post('id');
        $this->data['logData'] = $this->jobcard_v2->getJobLogData($this->data['job_card_id']);
        $this->load->view($this->updateJobForm,$this->data);
    }

    public function saveJobQty(){
        $data = $this->input->post();
        $errorMessage = array();
        if($data['log_type'] == -1):
            $jobdata = $this->jobcard_v2->getJobPendingQty($data['job_card_id']);
            $pendingQty = $jobdata->in_qty - $jobdata->out_qty; 
            if($pendingQty < $data['qty']):
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->jobcard_v2->saveJobQty($data);
            $tbody='';$i=1;
            if(!empty($result)):
                foreach($result as $row):
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->log_date).'</td>
                        <td>'.$logType.'</td>
                        <td>'.$row->qty.'</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }

    public function deleteJobUpdateQty(){
        $id = $this->input->post('id'); 
        $logdata = $this->jobcard_v2->getJobLog($id); 
        $errorMessage = '';
        if($logdata->log_type == 1):
            $jobdata = $this->jobcard_v2->getJobPendingQty($logdata->job_card_id);
            $pendingQty = $jobdata->in_qty - $jobdata->out_qty; 
            if($pendingQty < $logdata->qty):
                $errorMessage = "Sorry...! You can't delete this jobcard log because This Qty. moved to next process.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $result = $this->jobcard_v2->deleteJobUpdateQty($id);

            $tbody='';$i=1;
            if(!empty($result)):
                foreach($result as $row):
                    $deleteParam = $row->id . ",'Jobcard Log'";
                    $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->log_date).'</td>
                        <td>'.$logType.'</td>
                        <td>'.$row->qty.'</td>
                        <td><a class="btn btn-sm btn-outline-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                    </tr>';
                endforeach;
            endif;
            $this->printJson(['status'=>1,'tbody'=>$tbody]);
        endif;
    }
    
    /*****************************************/
    /* Material Return , Scrap , Used in Job */
    public function materialReturn(){
        $data=$this->input->post();
        $this->data['locationList'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $data;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['batchData']=$this->jobcard_v2->getBatchNoForReturnMaterial($data['job_card_id'],$data['item_id'])['options'];
        $this->data['jobData']=$this->jobcard_v2->getJobcard($data['job_card_id']);
        $this->data['transData']=$this->jobcard_v2->getScrapeTrans(['job_card_id'=>$data['job_card_id']])['resultHtml'];
        $this->load->view('production_v2/jobcard/material_return_form',$this->data);
    }
    
    public function saveMaterialScrap(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty is required.";
        if(empty($data['ref_type']))
            $errorMessage['qty'] = "Return Type is required.";
        if($data['ref_type']==10){
            if(empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
            if(empty($data['batch_no']))
                $errorMessage['batch_no'] = "Batch No is required.";
            
        }
        if($data['ref_type']==18){
            if(empty($data['location_id']))
                $errorMessage['location_id'] = "Location is required.";
        }
        if(!empty($errorMessage)){
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        }
        else{
            $result = $this->jobcard_v2->saveMaterialScrap($data);
            $this->printJson($result);
        }   
    }
    
    public function deleteScrap(){
        $data = $this->input->post();
        $result = $this->jobcard_v2->deleteScrap($data['id']);
        $this->printJson($result);
    }
}
?>