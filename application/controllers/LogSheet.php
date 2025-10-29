<?php
class LogSheet extends MY_Controller
{
    private $indexPage = "log_sheet/index";
    private $logForm = "log_sheet/log_form";
    private $machine_log = "log_sheet/machine_log";
    private $rejectionForm = "log_sheet/rej_rw_form";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Log Sheet";
		$this->data['headData']->controller = "logSheet";
		$this->data['headData']->pageUrl = "logSheet";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($prod_type=1){
        $data = $this->input->post(); $data['prod_type'] = $prod_type;
        $result = $this->logSheet->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getLogSheetData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLog(){
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2,'0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
        $this->load->view($this->logForm,$this->data);
    }

    public function getProcess(){
        $job_id = $this->input->post('job_card_id');
        $jobData = $this->jobcard->getJobcard($job_id);
        $processList = explode(',' , $jobData->process);
        $options = '<option value="">Select Process</option>';
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $JobApproveData=$this->jobcard_v2->getJobApprovalDetail($job_id,$value);
                $logData=$this->logSheet->getPrdLogOnProcessNJob($job_id,$value);
                $pending_qty=$JobApproveData->in_qty-$logData->production_qty;
                if($value!=42):
                    $pdata = $this->process->getProcess($value);
                    $options .= '<option value="'.$pdata->id.'" >'.$pdata->process_name.'  [Pending Qty : '.$pending_qty.']</option>';
                endif;
            endforeach; 
        endif;
        $this->printJson(['status'=>1,'options'=>$options,'job_date'=>$jobData->job_date]);
    }

    public function getJobWorkOrder(){
        $data = $this->input->post();
        $vendorData = $this->logSheet->getJobWorkVendorTransData($data);
        $rejOption = '<option value="0" data-party_name="In House">In House</option>';
        $rewOption = '<option value="0" data-party_name="In House">In House</option>';
        if(!empty($vendorData)):
            foreach($vendorData as $row):
                $rejOption.= '<option value="'.$row->vendor_id.'" data-party_name="'.$row->party_name.'">'.$row->party_name.'</option>'; 
                $rewOption.= '<option value="'.$row->vendor_id.'" data-party_name="'.$row->party_name.'">'.$row->party_name.'</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'rejOption'=>$rejOption, 'rewOption'=>$rewOption]);
    }

	 /**
	 * Updated By Mansee
	 * Note : Remove Start time End Time & Add load Unload time and Cycle time
	 * Date : 12-02-2022
	 */
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['log_date']))
        $errorMessage['log_date'] = "Date is required.";
        if(empty($data['load_unload_time']))
            $data['load_unload_time'] = 0;
        //$errorMessage['load_unload_time'] = "Load Unload Time is required.";
        if(empty($data['cycle_time']))
            $errorMessage['cycle_time'] = "Cycle Time is required."; 
       
        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card is required.";
        if(empty($data['process_id']))
            $errorMessage['process_id'] = "Process is required.";
        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";

        if(!empty($data['part_count'])):
            if(empty($data['qty']))
                $errorMessage['qty'] = "Production Qty is required.";
        endif;
        /* if(!empty($data['idle_time'])):
            if(empty($data['idle_reason']))
                $errorMessage['idle_reason'] = "Idle Reason Qty is required.";
        endif; */
        // if(!empty($data['rej_qty'])):
        //     $data['rej_stage'] = $data['process_id'];
        //     if(empty($data['rej_reason']))
        //         $errorMessage['rej_reason'] = "Rejection Reason Qty is required.";
        // endif;
        // if(!empty($data['rw_qty'])):
        //     $data['rw_stage'] = $data['process_id'];
        //     if(empty($data['rw_reason']))
        //         $errorMessage['rw_reason'] = "Rework Reason Qty is required.";
        // endif;
   
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $oldQty=0;
            if(!empty($data['id'])){
                $oldQty = $this->ncReport->getLogs($data['id'])->production_qty;
            }
            $JobApproveData=$this->jobcard_v2->getJobApprovalDetail($data['job_card_id'],$data['process_id']);
            $logData=$this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'],$data['process_id']);
            $pending_qty=$JobApproveData->in_qty-$logData->production_qty+ $oldQty;
            // print_r($pending_qty);
            if($data['production_qty'] > ($pending_qty))
            {
                //$errorMessage['production_qty'] = "Production Qty is Not Valid";
                //$this->printJson(['status'=>0,'message'=>$errorMessage]);
            }
            $data['rw_reason']='';$data['rej_reason']='';
           
            if(!empty($data['idle_reason'])):
                $data['idle_time'] = array_sum(array_column($data['idle_reason'],'idle_time'));
                $data['idle_reason'] = json_encode($data['idle_reason']);
            endif;

            if(!empty($data['rejection_reason'])):
                $data['rej_qty'] = array_sum(array_column($data['rejection_reason'],'rej_qty'));
                $data['rej_reason'] = json_encode($data['rejection_reason']);
            endif;

            if(!empty($data['rework_reason'])):
                $data['rw_qty'] = array_sum(array_column($data['rework_reason'],'rw_qty'));
                $data['rw_reason'] = json_encode($data['rework_reason']);
            endif;
            unset($data['rework_reason'],$data['rejection_reason']);
            $data['created_by'] = $this->session->userdata('loginId');  
                    
            $this->printJson($this->logSheet->save($data));
        endif;
    }
	
    public function edit(){     
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2,'0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
        
        $dataRow = $this->logSheet->getLogs($this->input->post('id'));

        //Process
        $jobData = $this->jobcard->getJobcard($dataRow->job_card_id);
        $processList = explode(',' , $jobData->process);
        $dataRow->processOpt = '';
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                $JobApproveData=$this->jobcard_v2->getJobApprovalDetail($dataRow->job_card_id,$value);
                $logData=$this->logSheet->getPrdLogOnProcessNJob($dataRow->job_card_id,$value);
                $pending_qty=$JobApproveData->in_qty-$logData->production_qty;
                $selectPro = (!empty($dataRow->process_id) && $dataRow->process_id == $pdata->id)?"selected":"disabled";
                $dataRow->processOpt .= '<option value="'.$pdata->id.'" '.$selectPro.'>'.$pdata->process_name.'  [Pending Qty : '.$pending_qty.']</option>';
            endforeach; 
        endif;

        //Machine
        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach($machineData as $row):
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id)?"selected":"";
            $dataRow->machineOpt .= '<option value="'.$row->id.'" '.$selectMac.'>[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;

        //Rej & Rew Form
        $in_process_key = array_keys($processList,$dataRow->process_id)[0];
		$html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';		
		foreach($processList as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'" data-process_name="'.$processData->process_name.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
        $dataRow->stage=$html;

        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->logForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->logSheet->delete($id));
        endif;
    }
    
	/**
     * Created By Mansee
     * Note : Machine Log
     * Date : 12-02-2022
     */

    public function machineLog(){
        $this->data['machineLogData'] = $this->logSheet->getMachineLogDtRows($this->input->post());
        $this->load->view($this->machine_log,$this->data);
    }

    public function getMachine(){
        $process_id = $this->input->post('process_id');
        $machineData = $this->item->getProcessWiseMachine($process_id);
        $options = '<option value="" >Select Machine</option>';
        foreach($machineData as $row):
            $options .= '<option value="'.$row->id.'" >[ '.$row->item_code.' ] '.$row->item_name.'</option>';
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    /**
     * Created By Mansee
     * Note : Master Cycle
     * Date : 10-03-2022
     */
	public function getMasterCycleTime(){
		$data=$this->input->post();
		$result=$this->logSheet->getMasterCycleTime($data);
		$parsed = date_parse($result->cycle_time);
		$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
		$this->printJson(['status'=>1,'cycle_time'=>$seconds]);
	}

    public function getRejectionBelongs(){
        $data=$this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];
		$html = '<option value="">Select Stage</option>
                    <option value="0" data-process_name="Row Material">Row Material</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'" data-process_name="'.$processData->process_name.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
		$ctData=$this->process->getProductProcess(['process_id'=>$data['process_id'],'item_id'=>$data['part_id']]);
		$cycle_time = (!empty($ctData)) ? $ctData->master_ct : 0 ;
		
        
        
        $opOptions = '';$mcOptions = '<option value="" >Select Machine</option>';
        if(!empty($data['entry_type']) AND $data['entry_type']=='REJ')
        {
            $machineData = $this->logSheet->getPrdLogMachines($data);
            $opOptions = '<option value="" >Select Operator</option>';
            $operatorData = $this->logSheet->getPrdLogOperators($data);
            if(!empty($operatorData))
            {
                foreach($operatorData as $row):
                    if(!empty($row->id)){$opOptions .= '<option value="'.$row->id.'" >[ '.$row->emp_code.' ] '.$row->emp_name.'</option>';}
                endforeach;
            }
        }else{$machineData = $this->item->getProcessWiseMachine($data['process_id']);}
        if(!empty($machineData))
        {
            foreach($machineData as $row):
                $mcOptions .= '<option value="'.$row->id.'" >[ '.$row->item_code.' ] '.$row->item_name.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'rejOption'=>$html, 'rewOption'=>$html,'cycle_time'=>$cycle_time,'mcOptions'=>$mcOptions,'opOptions'=>$opOptions]);
    }
}
?>