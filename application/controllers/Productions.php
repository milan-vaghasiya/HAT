<?php
class Productions extends MY_Controller{
    private $indexPage = "production/index";
    private $productionForm = "production/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Productions";
		$this->data['headData']->controller = "productions";
	}
	
	public function index($id){
        $data = $this->production->getProductionList($id);
        $this->data['productionData'] = $this->getProductionTableHtml($data);
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['jobData'] = $this->jobcard->getJobCard($id);
        $this->data['job_card_process'] = $this->data['jobData']->process;
        $this->data['job_id'] = $id;
        $this->data['comments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->production->getShift();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getProcessWiseProduction(){
        $processId = $this->input->post('process_id');
        $jobCardId = $this->input->post('job_id');
        $data = $this->production->getProductionList($jobCardId,$processId); 
        $this->printJson(['html'=>$this->getProductionTableHtml($data)]);
    }

    public function getProductionTableHtml($data){
        $html = "";$i=1;$jobCardNo = '';$productCode = '';
        if(!empty($data)):
            foreach($data as $row):
                $row->vendor_name = (!empty($row->vendor_id))?$this->party->getParty($row->vendor_id)->party_name:"In House";
                $row->process_type = "Regualr";
                if($row->trans_type == 1):
                    $checkProcess = $this->production->getJobInwardDataById($row->ref_id)->process_id;
                    if($row->process_id != $checkProcess):
                        $row->process_type = "Rework";
                    endif;
                endif;
                /* $machineName = array();
                if((!empty($row->machine_id))):
                    $machineIds = explode(',',$row->machine_id);
                    foreach($machineIds as $key=>$value):
                        $machineData = $this->machine->getMachine($value);
                        $machineName[] = '[ '.$machineData->machine_no.' ] '.$machineData->machine_description;
                    endforeach;
                endif;
                $machineName = implode(",",$machineName); */
                $machineName = "";
                if(!empty($row->machine_id)):
                    $machineData = $this->machine->getMachine($row->machine_id);
                    $machineName = "[ ".$machineData->item_code." ] ".$machineData->item_name;
                endif;
				$jobInData = $this->production->getJobInwardDataById($row->id);
				
                $pendingQty = $row->in_qty - ($row->out_qty + $row->rework_qty + $row->rejection_qty);
                $row->process_name = $this->process->getProcess($row->process_id)->process_name;
				
				$minDate = (!empty($jobInData->entry_date)) ? $jobInData->entry_date : "";
                $button = "";
                if(!empty($row->setup_status)):
                    if(empty($row->accepted_by)):         
                        $button = '<a class="btn btn-success" onclick="acceptJob('.$row->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>';        
                    else:
                        $button = '<a class="btn btn-warning getForward" href="javascript:void(0)" datatip="Forward" flow="down" data-ref_id="'.$row->id.'" data-product_id="'.$row->product_id.'" data-in_process_id="'.$row->process_id.'" data-job_card_id="'.$row->job_card_id.'" data-product_name="'.$row->product_code.'" data-process_name="'.$row->process_name.'" data-pending_qty="'.$pendingQty.'"  data-issue_batch_no="'.$jobInData->issue_batch_no.'" data-issue_material_qty="'.$jobInData->issue_material_qty.'" data-material_used_id="'.$jobInData->material_used_id.'" data-mindate="'.$minDate.'" data-machine_id="'.$row->machine_id.'"  data-toggle="modal" data-target="#outwardModal"><i class="fas fa-paper-plane" ></i></a>';
                    endif;
                endif;
                
                $html .= '<tr class="text-center">
                            <td>
                                <div class="actionWrapper" style="position:relative;">
                                    <div class="actionButtons actionButtonsRight">
                                        <a class="mainButton btn-instagram" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
                                        <div class="btnDiv" style="left:75%;">
                                            '.$button.'
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>'.$i++.'</td>
                            <!--<td>'.$row->job_prefix.$row->job_no.'</td>-->
                            <td class="text-left">'.$row->process_name.'</td>
                            <!--<td>'.$row->product_code.'</td>-->
                            <!--<td>'.$row->vendor_name.'</td>-->
                            <td>'.$row->process_type.'</td>
                            <!--<td>'.$machineName.'</td>-->
                            <td>'.$row->in_qty.'</td>
                            <td>'.$row->out_qty.'</td>
                            <td>'.$row->rework_qty.'</td>
                            <td>'.$row->rejection_qty.'</td>
                            <td>'.$pendingQty.'</td>
                            <td>'.$row->issue_batch_no.'</td>
                        </tr>';
            endforeach;
        endif;
        return $html;
    }

    public function acceptJob(){
        $id = $this->input->post('id');
        $processId = $this->input->post('process_id');
        $jobCardId = $this->input->post('job_id');   
        $emp_id = $this->session->userdata('loginId');     
        $result = $this->production->acceptJob($id,$emp_id);
        $data = $this->production->getProductionList($jobCardId,$processId); 
        $result['html'] = $this->getProductionTableHtml($data);
        $this->printJson($result);
    }

    public function getProductProcessRow(){
		$data = $this->input->post();
		$ppData = $this->production->getProductProcessRow($data);
		if(empty($ppData->cycle_time)){$ppData->cycle_time = "00:00:00";}
		$cttime = explode(':',$ppData->cycle_time);
		$hs = $cttime[0] * 3600;
		$ms = $cttime[1] * 60;
		$ss = $cttime[2];
		$seconds = $hs + $ms + $ss;
		$totalSeconds = $seconds * $data['outQty'];
		$ppData->ptHours = sprintf("%02d", (int)($totalSeconds / 3600));
		$ppData->ptMinutes = sprintf("%02d", (int)($totalSeconds/ 60 % 60));
		$ppData->ptSeconds = sprintf("%02d", (int)($totalSeconds % 60));
		if($totalSeconds >= 3600):
			$ppData->ptLabel = $ppData->ptHours . ' Hours ' . $ppData->ptMinutes . ' Minutes ' . $ppData->ptSeconds . ' Seconds';
            $ppData->productionTime = $ppData->ptHours.':'.$ppData->ptMinutes;
		elseif($totalSeconds >= 60):
			$ppData->ptLabel = $ppData->ptMinutes . ' Minutes ' . $ppData->ptSeconds . ' Seconds';
            $ppData->productionTime = '00:'.$ppData->ptMinutes;
		else:
			$ppData->ptLabel = $ppData->ptSeconds . ' Seconds';
            $ppData->productionTime = '00:00';
		endif;
        $this->printJson(['status' => 1, 'ppData' => $ppData]);
    }

    public function getOpretors(){
        $operatorData = $this->employee->getMachineOperatorList();
        $options = '<option value="">Select Operator</option>';
        foreach($operatorData as $row):
            $options .= '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    public function getSupervisors(){
        $supervisorData = $this->employee->getSupervisorList();
        $options = '<option value="">Select SuperVisor</option>';
        foreach($supervisorData as $row):
            $options .= '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getMachines(){
        $process_id = $this->input->post('process_id');
        $machine_id = $this->input->post('machine_id');
        $machineData = $this->item->getProcessWiseMachine($process_id);
        $disabledDefualt = "";/* (!empty($machine_id))?"disabled":""; */
        $options = '<option value="" '.$disabledDefualt.'>Select Machine</option>';
        foreach($machineData as $row):
            $disabled = (!empty($machine_id) && $machine_id != $row->id)?"disabled":"";
            $selected = (!empty($machine_id) && $machine_id == $row->id)?"selected":"";
            $options .= '<option value="'.$row->id.'" '.$selected.' '.$disabled.'>[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getOutwordTrans(){
        $data = $this->input->post();//print_r($data);exit;
        $result = $this->production->getOutwardTrans($data);
        $productionData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($productionData);

        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];
		$html = '<option value="">Select Stage</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
        $result['processOptions'] = $html;
		
		$rejectionReason = $this->comment->getCommentList();
		$rrOptions = '<option value="">Select Reason</option>';		
		foreach($rejectionReason as $row):
			$rrOptions .= '<option value="'.$row->id.'">'.$row->remark.'</option>';
		endforeach;
        $result['rrOptions'] = $rrOptions;

        $reworkComments = $this->comment->getReworkCommentList();
        $reworkOptions = '<option value="">Select Reason</option>';		
		foreach($reworkComments as $row):
			$reworkOptions .= '<option value="'.$row->id.'">'.$row->remark.'</option>';
		endforeach;
        $result['reworkOptions'] = $reworkOptions;

        $process_html = '<option value="">Select Stage</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$process_html .= '<option value="'. $processData->id.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
        $result['process'] = $process_html;

        $this->printJson($result);
    }

    public function rejectionReason(){
        $stageId = $this->input->post('stage_id');
        $rejectionReason = $this->comment->getCommentList();
		$rrOptions = '<option value="">Select Reason</option>';		
		foreach($rejectionReason as $row):
			$rrOptions .= '<option value="'.$row->id.'">'.$row->remark.'</option>';
		endforeach;
        $result['status'] = 1;
        $result['rrOptions'] = $rrOptions;
        $this->printJson($result);
    }

    public function saveProductionTrans(){
        $data = $this->input->post();
       
		$errorMessage = array();
		$h=0;$m=0;$ts=0;
		if(!empty($data['machine_id'])):
			$mtrans = $this->production->getMachineTrans($data);
			$muDetail='<table class="table table-bordered muDetail"><tr class="bg-light"><th colspan="3">Machine Usage Detail</th></tr><tr class="bg-light"><th>Job Card</th><th>Process</th><th>Time</th></tr>';$flag=false;
			
			if(!empty($mtrans)):
				foreach($mtrans as $mt)
				{
					if(!empty($mt->production_time) and $mt->production_time != '00:00')
					{
						$ex = explode(':',$mt->production_time);
						$h += intVal($ex[0]);
						$m += intVal($ex[1]);
						$ts += ($h * 3600) + ($m * 60);
						$muDetail .= '<tr>';
							$muDetail .= '<td>'.$mt->job_prefix.$mt->job_no.'</td>';
							$muDetail .= '<td>'.$mt->process_name.'</td>';
							$muDetail .= '<td>'.$mt->production_time.'</td>';
						$muDetail .= '</tr>';
						$flag=true;
					}
				}
				$muDetail .= '<tr>';
					$muDetail .= '<th colspan="2" class="text-right">Total Usage Time</th>';
					$muDetail .= '<th>'.$h.':'.$m.'</th>';
				$muDetail .= '</tr></table>';
				$h += intVal(explode(':',$data['production_time'])[0]);
				$m += intVal(explode(':',$data['production_time'])[1]);
				
			endif;
			
			$muContent = '';
			if($flag):
				$muContent = '&nbsp;&nbsp;<a href="javascript:void(0)" id="muDetail" class="text-primary" data-htmlcontent="'.htmlentities($muDetail).'"><i class="fa fa-question-circle"></i></a>';
			endif;
			
			if($h > 10){$errorMessage['production_time'] = "Invalid Time".$muContent;}
			elseif($h == 10 and $m > 30){$errorMessage['production_time'] = "Invalid Time".$muContent;}
			
		endif;
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $page_proces_id = $data['page_process_id'];
            unset($data['page_process_id']);
            $data['created_by'] = $this->session->userdata('loginId');
			$result = $this->production->saveProductionTrans($data);

			$pageData = $this->production->getProductionList($data['job_card_id'],$page_proces_id); 
			$result['html'] = $this->getProductionTableHtml($pageData);
			$this->printJson($result);
        endif;
    }

    public function deleteProductionTrans(){
        $data = $this->input->post();
        $result = $this->production->deleteProductionTrans($data['id']);

        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    public function getReturnOrScrapeTrans(){
        $data = $this->input->post();
        $result = $this->production->getReturnOrScrapeTrans($data);
        /* $data = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($data); */
        $this->printJson($result);
    }

    public function getItemData(){
        $id = $this->input->post('id');
		$result = $this->item->getItem($id);
		$result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
    }

    public function returnOrScrapeSave(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $result = $this->production->returnOrScrapeSave($data);
        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    public function deleteRetuenOrScrapeItem(){
        $data = $this->input->post();
        $result = $this->production->deleteRetuenOrScrapeItem($data['id']);

        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

//-----------------------------------------------------------------------------------------

    /* public function saveOutTrans(){
        $data = $this->input->post();
		$errorMessage = array();
		$h=0;$m=0;$ts=0;
		if(!empty($data['machine_id'])):
			$mtrans = $this->production->getMachineTrans($data);
			// $idleTimeData = $this->production->getIdleTimeByMachineDate($data);
			$muDetail='<table class="table table-bordered muDetail"><tr class="bg-light"><th colspan="3">Machine Usage Detail</th></tr><tr class="bg-light"><th>Job Card</th><th>Process</th><th>Time</th></tr>';$flag=false;
			
			if(!empty($mtrans)):
				foreach($mtrans as $mt)
				{
					if(!empty($mt->production_time) and $mt->production_time != '00:00')
					{
						$ex = explode(':',$mt->production_time);
						$h += intVal($ex[0]);
						$m += intVal($ex[1]);
						$ts += ($h * 3600) + ($m * 60);
						$muDetail .= '<tr>';
							$muDetail .= '<td>'.$mt->job_prefix.$mt->job_no.'</td>';
							$muDetail .= '<td>'.$mt->process_name.'</td>';
							$muDetail .= '<td>'.$mt->production_time.'</td>';
						$muDetail .= '</tr>';
						$flag=true;
					}
				}
				$muDetail .= '<tr>';
					$muDetail .= '<th colspan="2" class="text-right">Total Usage Time</th>';
					$muDetail .= '<th>'.$h.':'.$m.'</th>';
				$muDetail .= '</tr></table>';
				$h += intVal(explode(':',$data['production_time'])[0]);
				$m += intVal(explode(':',$data['production_time'])[1]);
				
			endif;
			
			$muContent = '';
			if($flag):
				$muContent = '&nbsp;&nbsp;<a href="javascript:void(0)" id="muDetail" class="text-primary" data-htmlcontent="'.htmlentities($muDetail).'"><i class="fa fa-question-circle"></i></a>';
			endif;
			
			if($h > 10){$errorMessage['production_time'] = "Invalid Time".$muContent;}
			elseif($h == 10 and $m > 30){$errorMessage['production_time'] = "Invalid Time".$muContent;}
			
		endif;
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
			$result = $this->production->saveOutTrans($data);
			$pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
			$result['html'] = $this->getProductionTableHtml($pageData);
			$this->printJson($result);
        endif;
        
    } */

    /* public function deleteOutward(){
        $data = $this->input->post();
        $result = $this->production->deleteOutward($data['id']);

        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    } */

    public function getRejectionTrans(){
        $data = $this->input->post();

        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['in_process_id'])[0];
		$html = '<option value="">Select Type</option><option value="-1">Material Fault</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
		
		$outDates = $this->production->getOutwardDates($data);
		$dateOptions = '<option value="">Select Date</option>';	
		if(!empty($outDates)):
			foreach($outDates as $row):
				$dateOptions .= '<option value="'. $row->entry_date.'">'.formatDate($row->entry_date).'</option>';
			endforeach;
		endif;

        $result = $this->production->getRejectionTrans($data);
        $data = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($data);
        $result['processOptions'] = $html;
		
		$rejectionReason = $this->comment->getCommentList();
		$rrOptions = '<option value="">Select Reason</option><option value="-1">Material Fault</option>';		
		foreach($rejectionReason as $row):
			$rrOptions .= '<option value="'.$row->id.'">'.$row->remark.'</option>';
		endforeach;
        $result['rrOptions'] = $rrOptions;
        $result['dateOptions'] = $dateOptions;
        $this->printJson($result);
    }

    public function saveRejection(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $result = $this->production->saveRejection($data);
        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    public function deleteRejection(){
        $data = $this->input->post();
        $result = $this->production->deleteRejection($data['id']);

        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    public function getReworkTrans(){
        $data = $this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['in_process_id'])[0];
		$html = '';		
		foreach($jobProcess as $key=>$value):
			//if($value < $data['in_process_id']):
            if($key < $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;

        $result = $this->production->getReworkTrans($data);
        $data = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($data);
        $result['process'] = $html;
        $this->printJson($result);
    }

    public function saveRework(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $result = $this->production->saveRework($data);
        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    public function deleteRework(){
        $data = $this->input->post();
        $result = $this->production->deleteRework($data['id']);

        $pageData = $this->production->getProductionList($data['job_card_id'],$data['page_process_id']); 
        $result['html'] = $this->getProductionTableHtml($pageData);
        $this->printJson($result);
    }

    

    
		
	public function getIdleTime(){
        $data = $this->input->post();
        //$idleReasonData = explode(',', $this->item->getMasterOptions()->machine_idle_reason);
        $idleReasonData = $this->comment->getIdleReason();
        $idleReason = '<option value="">Select Idle Reason</option>';
        foreach($idleReasonData as $row):
            $idleReason .= '<option value="'.$row->id.'">['.$row->code.'] - '.$row->remark.'</option>';
        endforeach;
	    $result['idletblData'] = $this->production->getIdleTimeList($data)['sendData']; 
        $result['idleReason'] = $idleReason;
        $this->printJson($result);
    }

    public function saveIdleTime(){
        $data = $this->input->post();
		$errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['id'] = "";
            $data['start_time'] = date("Y-m-d H:i:s",strtotime($data['start_time']));
            $data['end_time'] = date("Y-m-d H:i:s",strtotime($data['end_time']));
            $data['created_by'] = $this->session->userdata('loginId');
			$result = $this->production->saveIdleTime($data);
            $this->printJson($result);
        endif;
    }

    public function deleteIdleTime(){
        $data = $this->input->post();
        $result = $this->production->deleteIdleTime($data);

        //$idleReasonData = explode(',', $this->item->getMasterOptions()->machine_idle_reason);
        $idleReasonData = $this->comment->getIdleReason();
        $idleReason = '<option value="">Select Idle Reason</option>';
        foreach($idleReasonData as $row):
            $idleReason .= '<option value="'.$row->id.'">['.$row->code.'] - '.$row->remark.'</option>';
        endforeach;
        
        $result['idleReason'] = $idleReason;
        $this->printJson($result);
    }
    
    /* Updated By NYN  @ 27/01/2022 */
    /* Process identification tag Print Data */
    public function printProcessIdentification($id){
        $jobData = $this->production->getOutwardPrintData($id); //print_r($jobData);exit;
        $partyName = (!empty($jobData->party_name))?$jobData->party_name:"In House";
        $title = (!empty($jobData->party_name))? "(Vendor Awaiting Inspection)":"";
        $next_process = (!empty($jobData->next_process))? $jobData->next_process:"Packing";
        
        $logo=base_url('assets/images/logo.png');
		
        if(!empty($jobData->next_process)):
            $topSectionO ='<table class="table">
                            <tr>
                                <td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
                                <td class="org_title text-center" style="font-size:1rem;width:60%">Process Identification Tag <br><small><span class="text-dark">'.$title.'</span></small></td>
                                <td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F ST 12<br>(00/01.08.2021)</td>
                            </tr>
                        </table>';
            
            $itemList='<table class="table table-bordered recieveTag">
                    <tr>
						<td><b>Vendor Name : </b></td>
                        <td>'.$partyName.'</td>
                        <td><b>Job No : </b></td>
                        <td>'.getPrefixNumber($jobData->job_prefix, $jobData->job_no).'</td>
					</tr>
					<tr>
                        <td><b>Date : </b></td>
                        <td>'.formatDate($jobData->entry_date).'</td>
                        <td><b>Challan No : </b></td>
                        <td>'.$jobData->challan_no.'</td>
					</tr>
					<tr>
						<td><b>Part Code : </b></td>
                        <td>'.$jobData->item_code.'</td>
                        <td><b>Department : </b></td>
                        <td>'.$jobData->dept_name.'</td>
					</tr>
					
					<tr>
						<td><b>Qty : </b></td>
                        <td>'.$jobData->in_qty.'</td>
                        <td><b>Batch/Heat <br> Code : </b></td>
                        <td>'.$jobData->batch_no.'</td>
					</tr>
                    <tr>
						<td><b>Current Process : </b></td>
                        <td>'.$jobData->process_name.'</td>
                        <td><b>Next Process : </b></td>
                        <td>'.$next_process.'</td>
					</tr>
                </table>';
		else:
            $topSectionO ='<table class="table">
                <tr>
                    <td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
                    <td class="org_title text-center" style="font-size:1rem;width:60%">Process Identification Tag <br><small><span class="text-dark">Final Inspection Ok Material</span></small></td>
                    <td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F QA 25<br>(01/01.10.2021)</td>
                </tr>
            </table>';

            $itemList='<table class="table table-bordered recieveTag">
                    <tr>
                        <td colspan="2"><b>Job No : </b></td>
                        <td colspan="2">'.getPrefixNumber($jobData->job_prefix, $jobData->job_no).'</td>
                    </tr>
                    <tr>
                        <td><b>Date : </b></td>
                        <td>'.formatDate($jobData->entry_date).'</td>
                        <td><b>Batch/Heat <br> Code : </b></td>
                        <td>'.$jobData->batch_no.'</td>
                    </tr>
                    <tr>
                        <td><b>Part Code : </b></td>
                        <td>'.$jobData->item_code.'</td>
                        <td><b>Qty : </b></td>
                        <td>'.$jobData->in_qty.'</td>
                    </tr>
                   
                    <tr>
                        <td><b>Inspector : </b></td>
                        <td>'.$jobData->emp_name.'</td>
                        <td><b>Supervisor : </b></td>
                        <td>'.$jobData->supervisor_name.'</td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <b>Note (Any) : </b>
                            <br>    '.$jobData->remark.'
                        </td>
                    </tr>
                </table>';

        endif;
        $originalCopy = '<div style="width:100mm;">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
    
    public function getFinishedWeight(){
        $data=$this->input->post();
        $data=$this->item->getProductProcessData($data['item_id'],$data['process_id']);
        $this->printJson($data->finished_weight);
    }
    
    public function getVendorForRework(){
        $data=$this->input->post();
        $options='<option value="">Select Vendor</option>';
        $vndrResult=$this->production->getVendorForRework($data);
        foreach($vndrResult as $row) {
           $options.='<option value="'.$row->vendor_id.'">'.(empty($row->party_name)?'In House':$row->party_name).'</option>'; 
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
}
?>