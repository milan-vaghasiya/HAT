<?php
class RejectionLog extends MY_Controller
{
    private $indexPage = "rejection_log/index";
    private $formPage = "rejection_log/form";
    private $machine_log = "log_sheet/machine_log";

    private $reworkManage="rejection_log/rework_manage";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Rejection Log";
        $this->data['headData']->controller = "rejectionLog";
        $this->data['headData']->pageUrl = "rejectionLog";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($prod_type = 1)
    {
        $data = $this->input->post();
        $data['prod_type'] = $prod_type;
        $result = $this->rejectionLog->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getRejectionLogData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addLog()
    {
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2, '0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->load->view($this->formPage, $this->data);
    }

    public function getProcess()
    {
        $job_id = $this->input->post('job_card_id');
        $jobData = $this->jobcard->getJobcard($job_id);
        $processList = explode(',', $jobData->process);
        $options = '<option value="">Select Process</option>';
        if (isset($processList)) :
            foreach ($processList as $key => $value) :
                $JobApproveData = $this->jobcard_v2->getJobApprovalDetail($job_id, $value);
                $logData = $this->rejectionLog->getPrdLogOnProcessNJob($job_id, $value);
                $pending_qty = $JobApproveData->in_qty - $logData->production_qty;
                if ($value != 42) :
                    $pdata = $this->process->getProcess($value);
                    $options .= '<option value="' . $pdata->id . '" data-pending_qty="' . $pending_qty . '">' . $pdata->process_name . '  [Pending Qty : ' . $pending_qty . ']</option>';
                endif;
            endforeach;
        endif;
        $this->printJson(['status' => 1, 'options' => $options, 'job_date' => $jobData->job_date]);
    }

    public function getJobWorkOrder()
    {
        $data = $this->input->post();
        $vendorData = $this->rejectionLog->getJobWorkVendorTransData($data);
        $rejOption = '<option value="0" data-party_name="In House">In House</option>';
        $rewOption = '<option value="0" data-party_name="In House">In House</option>';
        if (!empty($vendorData)) :
            foreach ($vendorData as $row) :
                $rejOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . $row->party_name . '">' . $row->party_name . '</option>';
                $rewOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . $row->party_name . '">' . $row->party_name . '</option>';
            endforeach;
        endif;
        $this->printJson(['status' => 1, 'rejOption' => $rejOption, 'rewOption' => $rewOption]);
    }

    public function getMachine()
    {
        $process_id = $this->input->post('process_id');
        $machineData = $this->item->getProcessWiseMachine($process_id);
        $options = '<option value="" >Select Machine</option>';
        foreach ($machineData as $row) :
            $options .= '<option value="' . $row->id . '" >[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
        endforeach;
        $this->printJson(['status' => 1, 'options' => $options]);
    }

    /**
     * Updated By Mansee
     * Note : Remove Start time End Time & Add load Unload time and Cycle time
     * Date : 12-02-2022
     */
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
        if (empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine is required.";

        if (empty($data['rejection_reason']) && empty($data['rework_reason']))
            $errorMessage['general_error'] = "Rejection Or Rework  required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

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
                // if ($data['rej_qty'] > ($pending_qty)) {
                //     $errorMessage['general_error'] = "Rej Qty is Not Valid";
                //     $this->printJson(['status' => 0, 'message' => $errorMessage]);
                // }
            endif;
            if (!empty($data['rework_reason'])) :
                $data['rw_qty'] = array_sum(array_column($data['rework_reason'], 'rw_qty'));
            endif;
            $data['created_by'] = $this->session->userdata('loginId');

            $this->printJson($this->rejectionLog->save($data));
        endif;
    }

    public function edit()
    {
        $this->data['jobCardData'] = $this->jobcard->getJobcardListByVersion(2, '0,1,2,4');
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['reworkComments'] = $this->comment->getReworkCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();


        $dataRow = $this->rejectionLog->getLogs($this->input->post('id'));
        $this->data['rejRwData'] = $this->rejectionLog->getRejRwData($this->input->post('id'));

        //Process
        $jobData = $this->jobcard->getJobcard($dataRow->job_card_id);
        $processList = explode(',', $jobData->process);
        $dataRow->processOpt = '';
        if (isset($processList)) :
            foreach ($processList as $key => $value) :
                $pdata = $this->process->getProcess($value);
                $JobApproveData = $this->jobcard_v2->getJobApprovalDetail($dataRow->job_card_id, $value);
                $logData = $this->rejectionLog->getPrdLogOnProcessNJob($dataRow->job_card_id, $value);
                $pending_qty = $JobApproveData->in_qty - $logData->production_qty;
                $selectPro = (!empty($dataRow->process_id) && $dataRow->process_id == $pdata->id) ? "selected" : "disabled";
                $dataRow->processOpt .= '<option value="' . $pdata->id . '" ' . $selectPro . ' data-pending_qty=' . $pending_qty . '>' . $pdata->process_name . '  [Pending Qty : ' . $pending_qty . ']</option>';
            endforeach;
        endif;

        //Machine
        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);
        $dataRow->machineOpt = '<option value="" >Select Machine</option>';
        foreach ($machineData as $row) :
            $selectMac = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
            $dataRow->machineOpt .= '<option value="' . $row->id . '" ' . $selectMac . '>[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
        endforeach;

        //Rej & Rew Form
        $in_process_key = array_keys($processList, $dataRow->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($processList as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $dataRow->stage = $html;

        $this->data['dataRow'] = $dataRow;
        $this->load->view($this->formPage, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->rejectionLog->delete($id));
        endif;
    }

    /**
     * Created By Mansee
     * Note : Machine Log
     * Date : 12-02-2022
     */

    public function machineLog()
    {
        $this->data['machineLogData'] = $this->rejectionLog->getMachineLogDtRows($this->input->post());
        $this->load->view($this->machine_log, $this->data);
    }

    /**
     * Created By Mansee
     * Note : Master Cycle
     * Date : 10-03-2022
     */
    public function getMasterCycleTime()
    {
        $data = $this->input->post();
        $result = $this->rejectionLog->getMasterCycleTime($data);
        $parsed = date_parse($result->cycle_time);
        $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
        $this->printJson(['status' => 1, 'cycle_time' => $seconds]);
    }

    public function getRejectionBelongs()
    {
        $data = $this->input->post();
        $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
        $jobProcess = explode(",", $jobCardData->process);
        $in_process_key = array_keys($jobProcess, $data['process_id'])[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($jobProcess as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $this->printJson(['status' => 1, 'rejOption' => $html, 'rewOption' => $html]);
    }

    public function saveReworkQty()
    {
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->rejectionLog->saveReworkQty($data));
    }

    public function reworkmanagement($id)
    {
        // $data = $this->input->post();
        $this->data['rejRwData']=$this->rejectionLog->getRejectionData($id,2);
        $this->data['dataRow'] = $this->rejectionLog->getLogs($id);
        $this->data['rejectionComments'] = $this->comment->getCommentList();
      
        $jobCardData = $this->jobcard->getJobcard($this->data['dataRow']->job_card_id);
        $jobProcess = explode(",", $jobCardData->process);
        $in_process_key = array_keys($jobProcess, $this->data['dataRow']->process_id)[0];
        $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
        foreach ($jobProcess as $key => $value) :
            if ($key <= $in_process_key) :
                $processData = $this->process->getProcess($value);
                $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
            endif;
        endforeach;
        $this->data['dataRow']->stage=$html;
        $this->load->view($this->reworkManage, $this->data);
    }

    public function saveRejectionQty(){
        $data=$this->input->post();
        
        $logData=$this->rejectionLog->getRejectionData($data['id']);
        $data['rejection_reason']=[];
        $qty=0;
        if(!empty($logData)){
            foreach($logData as $row){
                $data['rejection_reason'][]=[
                    'trans_id'=>$row->id,
                    'rej_reason'=>$row->reason,
                    'rej_from'=>$row->vendor_id,
                    'rej_remark'=>$row->remark,
                    'rej_party_name'=>$row->vendor_name,
                    'rej_stage'=>$row->belongs_to,
                    'rej_stage_name'=>$row->belongs_to_name,
                    'rej_ref_id'=>$row->ref_id,
                    'rej_type'=>$row->rej_type,
                    'rej_qty'=>$row->qty,
                    'rejection_reason'=>$row->reason_name,
                ];
                $qty+=$row->qty;
            }
        }
        $data['rejection_reason'][]=[
            'trans_id'=>'',
            'rej_qty'=>$data['rej_qty'],
            'rej_reason'=>$data['rej_reason'],
            'rejection_reason'=>$data['rejection_reason_name'],
            'rej_from'=>$data['rej_from'],
            'rej_remark'=>$data['rej_remark'],
            'rej_party_name'=>$data['rej_party_name'],
            'rej_stage'=>$data['rej_stage'],
            'rej_stage_name'=>$data['rej_stage_name'],
            'rej_ref_id'=>$data['rej_ref_id'],
            'rej_type'=>$data['rej_type']
        ];

        // For minus qty from rework log and production log
        $reworkData=[
            'id'=>$data['rej_ref_id'],
            'rej_qty'=>$data['rej_qty']
        ];
        $data['rej_qty']=$data['rej_qty']+$qty;
        $data['created_by'] = $this->session->userdata('loginId');
        unset($data['trans_id'],$data['rej_reason'],$data['rejection_reason_name'],$data['rej_from'],$data['rej_remark'],$data['rej_party_name'],$data['rej_stage'],$data['rej_stage_name'],$data['rej_ref_id'],$data['rej_type']);
        
        $this->printJson($this->rejectionLog->save($data,$reworkData));
    }

    public function saveReworkmanagement(){
        $data=$this->input->post();
        print_r($data);exit;
    }
}
