<?php
class LogSheet extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search];
        $this->data['logSheetList'] = $this->logSheet->getLogSheetListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function addLog(){
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2);
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function getJobCardProcessList($job_id){
        $jobData = $this->jobcard->getJobcard($job_id);
        $processList = explode(',' , $jobData->process);
        $dataRow = array();
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                $dataRow[] = [
                    'id' => $pdata->id,
                    'process_name' => $pdata->process_name
                ];
            endforeach; 
        endif;
        $this->data['jobProcessList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function machineListOnProcess($process_id){
        $machineData = $this->item->getProcessWiseMachine($process_id);
        $dataRow = array();
        foreach($machineData as $row):
            $dataRow[] = [
                'id' => $row->id,
                'process_name' => '[ '.$row->item_code.' ] '.$row->item_name
            ];
        endforeach;
        $this->data['machineList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function getMasterCycleTime(){
		$data=$this->input->post();
		$result=$this->logSheet->getMasterCycleTime($data);
		$parsed = date_parse($result->cycle_time);
		$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
        $this->data['cycle_time'] = $seconds;
		$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
	}

    public function rejectionBelongs(){
        $data=$this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];

        $dataRow = array();
        $dataRow[] = [
            'id' => '',
            'party_name' => 'Select Stage'
        ];
        $dataRow[] = [
            'id' => '0',
            'party_name' => 'Row Material'
        ];

        foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
                $dataRow[] = [
                    'id' => $processData->id,
                    'process_name' => $processData->process_name
                ];
			endif;
		endforeach;

        $this->data['rejectionBelongs'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }   
    
    public function reworkBelongs(){
        $data=$this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];

        $dataRow = array();
        $dataRow[] = [
            'id' => '',
            'party_name' => 'Select Stage'
        ];
        $dataRow[] = [
            'id' => '0',
            'party_name' => 'Row Material'
        ];

        foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
                $dataRow[] = [
                    'id' => $processData->id,
                    'process_name' => $processData->process_name
                ];
			endif;
		endforeach;

        $this->data['reworkBelongs'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    } 

    public function rejectionFromList(){
        $data = $this->input->post();
        $vendorData = $this->logSheet->getJobWorkVendorTransData($data);
        $dataRow = array();
        $dataRow[] = [
            'id' => 0,
            'party_name' => 'In House'
        ];

        foreach($vendorData as $row):
            $dataRow[] = [
                'id' => $row->vendor_id,
                'party_name' => $row->party_name
            ]; 
        endforeach;
        $this->data['rejectionFromList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function reworkFromList(){
        $data = $this->input->post();
        $vendorData = $this->logSheet->getJobWorkVendorTransData($data);
        $dataRow = array();
        $dataRow[] = [
            'id' => 0,
            'party_name' => 'In House'
        ];

        foreach($vendorData as $row):
            $dataRow[] = [
                'id' => $row->vendor_id,
                'party_name' => $row->party_name
            ]; 
        endforeach;
        $this->data['reworkFromList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['log_date']))
        $errorMessage['log_date'] = "Date is required.";
        if(empty($data['load_unload_time']))
            $data['load_unload_time'] = 0;

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
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:           
            if(!empty($data['idle_reason'])):
                $data['idle_reason'] = json_decode($data['idle_reason']);
                $data['idle_time'] = array_sum(array_column($data['idle_reason'],'idle_time'));
                $data['idle_reason'] = json_encode($data['idle_reason']);
            endif;

            if(!empty($data['rej_reason'])):
                $data['rej_reason'] = json_decode($data['rej_reason']);
                $data['rej_qty'] = array_sum(array_column($data['rej_reason'],'rej_qty'));
                $data['rej_reason'] = json_encode($data['rej_reason']);
            endif;

            if(!empty($data['rw_reason'])):
                $data['rw_reason'] = json_decode($data['rw_reason']);
                $data['rw_qty'] = array_sum(array_column($data['rw_reason'],'rw_qty'));
                $data['rw_reason'] = json_encode($data['rw_reason']);
            endif;
            $data['created_by'] = $this->loginId;  
                    
            $this->printJson($this->logSheet->save($data));
        endif;
    }

    public function edit($id){       
        
        $logData = $this->logSheet->getLogs($id);

        $this->data['dataRow'] = $logData;
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2);
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();

        //Process
        $jobData = $this->jobcard->getJobcard($logData->job_card_id);
        $processList = explode(',' , $jobData->process);
        $dataRow = array();
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                $dataRow[] = [
                    'id' => $pdata->id,
                    'process_name' => $pdata->process_name
                ];
            endforeach; 
        endif;
        $this->data['jobProcessList'] = $dataRow;

        //Machine
        $machineData = $this->item->getProcessWiseMachine($logData->process_id);
        $dataRow = array();
        foreach($machineData as $row):
            $dataRow[] = [
                'id' => $row->id,
                'process_name' => '[ '.$row->item_code.' ] '.$row->item_name
            ];
        endforeach;
        $this->data['machineList'] = $dataRow;

        //Rej & Rew Belongs
        $jobProcess = explode(",",$jobData->process);
        $in_process_key = array_keys($jobProcess,$logData->process_id)[0];

        $dataRow = array();
        $dataRow[] = [
            'id' => '',
            'party_name' => 'Select Stage'
        ];
        $dataRow[] = [
            'id' => '0',
            'party_name' => 'Row Material'
        ];

        foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
                $dataRow[] = [
                    'id' => $processData->id,
                    'process_name' => $processData->process_name
                ];
			endif;
		endforeach;

        $this->data['rejectionBelongs'] = $dataRow;
        $this->data['reworkBelongs'] = $dataRow;

        //Rej & Rew Form
        $data = ['process_id'=>$logData->process_id,'part_id'=>$jobData->product_id,'job_card_id'=>$jobData->id];
        $vendorData = $this->logSheet->getJobWorkVendorTransData($data);
        $dataRow = array();
        $dataRow[] = [
            'id' => 0,
            'party_name' => 'In House'
        ];

        foreach($vendorData as $row):
            $dataRow[] = [
                'id' => $row->vendor_id,
                'party_name' => $row->party_name
            ]; 
        endforeach;
        $this->data['rejectionFromList'] = $dataRow;
        $this->data['reworkFromList'] = $dataRow;

        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function delete($id){
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->logSheet->delete($id));
        endif;
    }
}
?>