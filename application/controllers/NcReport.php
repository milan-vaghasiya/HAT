<?php
class NcReport extends MY_Controller
{
    private $indexPage = "nc_report/index";
    private $form = "nc_report/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Nc Report";
		$this->data['headData']->controller = "ncReport";
		$this->data['headData']->pageUrl = "ncReport";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->ncReport->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getNCReportData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addNCReport(){
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2,'0,1,2,4');
        $this->data['operatorList'] = $this->employee->getFinalInspectorList();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
        $this->data['inspectionTypeList']=$this->inspectionType->getInspectionTypeList();
        $this->load->view($this->form,$this->data);
    }

    public function getProcess(){
        $job_id = $this->input->post('job_card_id');
        $jobData = $this->jobcard->getJobcard($job_id);
        $processList = explode(',' , $jobData->process);
        $options = '<option value="">Select Process</option>';
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                if($pdata->dept_id==7){
                    $JobApproveData=$this->jobcard_v2->getJobApprovalDetail($job_id,$value);
                    $logData=$this->logSheet->getPrdLogOnProcessNJob($job_id,$value);
                    $pending_qty=$JobApproveData->in_qty-$logData->production_qty;
                    $options .= '<option value="'.$pdata->id.'" selected>'.$pdata->process_name.'  [Pending Qty : '.$pending_qty.']</option>';
                }
            endforeach; 
        endif;
        $this->printJson(['status'=>1,'options'=>$options,'job_date'=>$jobData->job_date]);
    }

    public function getJobWorkOrder(){
        $data = $this->input->post();
        $vendorData = $this->logSheet->getJobWorkOrder($data);
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

   
    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();

        if (empty($data['log_date']))
            $errorMessage['log_date'] = "Date is required.";

        if (empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card is required.";
        if (empty($data['process_id']))
            $errorMessage['process_id'] = "Process is required.";
        if (empty($data['operator_id']))
            $errorMessage['operator_id'] = "Inspector is required.";



        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $oldQty = 0;
            if (!empty($data['id'])) {
                $oldQty = $this->ncReport->getLogs($data['id'])->production_qty;
            }
            $JobApproveData = $this->jobcard_v2->getJobApprovalDetail($data['job_card_id'], $data['process_id']);
            $logData = $this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'], $data['process_id']);
            $pending_qty = $JobApproveData->in_qty - $logData->production_qty + $oldQty;
            if ($data['production_qty'] > ($pending_qty)) {
                $errorMessage['production_qty'] = "Inspection Qty is Not Valid";
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            }
            $data['rw_reason'] = '';
            $data['rej_reason'] = '';

            $data['rw_reason'] = '';
            $data['rej_reason'] = '';
            if (!empty($data['rejection_reason'])) :
                $data['rej_qty'] = array_sum(array_column($data['rejection_reason'], 'rej_qty'));
                $oldQty = 0;
                if (!empty($data['id'])) {
                    $oldQty = $this->ncReport->getLogs($data['id'])->production_qty;
                }
                $JobApproveData = $this->jobcard_v2->getJobApprovalDetail($data['job_card_id'], $data['process_id']);
                $logData = $this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'], $data['process_id']);
                $pending_qty = $JobApproveData->in_qty - $logData->production_qty + $oldQty;
                // print_r($pending_qty);
                if ($data['rej_qty'] > ($pending_qty)) {
                    $errorMessage['general_error'] = "Rej Qty is Not Valid";
                    $this->printJson(['status' => 0, 'message' => $errorMessage]);
                }
            endif;
            if (!empty($data['rework_reason'])) :
                $data['rw_qty'] = array_sum(array_column($data['rework_reason'], 'rw_qty'));
            endif;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->rejectionLog->save($data));
        endif;
    }

    
    public function edit(){     
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2,'0,1,2,4');
        $this->data['operatorList'] = $this->employee->getFinalInspectorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData']=$this->shiftModel->getShiftList();
        $this->data['inspectionTypeList']=$this->inspectionType->getInspectionTypeList();
        $dataRow = $this->ncReport->getLogs($this->input->post('id'));
        $this->data['rejRwData'] = $this->rejectionLog->getRejRwData($this->input->post('id'));

        //Process
        $jobData = $this->jobcard->getJobcard($dataRow->job_card_id);
        
        $processList = explode(',' , $jobData->process);
        $dataRow->processOpt = '';
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                if($pdata->dept_id==7){
                    $JobApproveData=$this->jobcard_v2->getJobApprovalDetail($dataRow->job_card_id,$value);
                    $logData=$this->logSheet->getPrdLogOnProcessNJob($dataRow->job_card_id,$value);
                    $pending_qty=$JobApproveData->in_qty-$logData->production_qty;
                    $selectPro = (!empty($dataRow->process_id) && $dataRow->process_id == $pdata->id)?"selected":"disabled";
                    $dataRow->processOpt .= '<option value="'.$pdata->id.'" '.$selectPro.'>'.$pdata->process_name.'  [Pending Qty : '.$pending_qty.']</option>';
                }
            endforeach; 
        endif;


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
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ncReport->delete($id));
        endif;
    }
    public function getRejectionBelongs()
    {
        $data=$this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
		$jobProcess = explode(",",$jobCardData->process);
        $in_process_key = array_keys($jobProcess,$data['process_id'])[0];
		$html = '<option value="">Select Stage</option>
                 <option value="-1" data-process_name="Handling Movement">Handling Movement</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';		
		foreach($jobProcess as $key=>$value):
            if($key <= $in_process_key):
				$processData = $this->process->getProcess($value);
				$html .= '<option value="'. $processData->id.'" data-process_name="'.$processData->process_name.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
        $this->printJson(['status'=>1,'rejOption'=>$html, 'rewOption'=>$html]);
    }

}
?>