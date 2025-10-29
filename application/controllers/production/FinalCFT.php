<?php
class FinalCFT extends MY_Controller
{
    private $indexPage = "production/final_cft/index";
    private $cft_ok_form = "production/final_cft/cft_ok_form";
    private $cft_rej_form = "production/final_cft/cft_rej_form";
    private $cft_rw_form = "production/final_cft/cft_rw_form";    
    private $cft_ud_form = "production/primary_cft/cft_ud_form";

    
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Final CFT";
        $this->data['headData']->controller = "production/finalCFT";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("finalCFT");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($entry_type =2,$operation_type = 4)
    {
        $data = $this->input->post();
        $data['entry_type'] = $entry_type;
        $data['operation_type'] = $operation_type;
        $result = $this->finalCFT->getDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getFinalCFTData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function convertToOk(){
        $data = $this->input->post();
        $this->data['entry_type'] = 3;
        $this->data['operation_type'] =4;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form,$this->data);
    }

    public function saveCFTQty(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        $i = 1;
        if (empty($data['qty'])):
            $errorMessage['qty'] = "Qty is required.";
        else:
            $cftData=$this->primaryCFT->getRejMovementData($data['ref_id']);
            if($data['qty'] > ($cftData->qty - $cftData->cft_qty)){
                $errorMessage['qty'] = "Qty is Invalid.";
            }
        endif;
      
        if ($data['operation_type'] == 1  && empty($data['rej_type']))
            $errorMessage['rej_type'] = "Rejection Type is required.";
        if (($data['operation_type'] == 1 || $data['operation_type'] == 2) && empty($data['rr_reason']))
            $errorMessage['rr_reason'] = "Reason is required.";

        if (($data['operation_type'] == 1 || $data['operation_type'] == 2) && $data['rr_stage'] =='')
            $errorMessage['rr_stage'] = "Rej Stage is required.";

        if (($data['operation_type'] == 1 || $data['operation_type'] == 2) && $data['rr_by'] == '')
            $errorMessage['rr_by'] = "Rejection By is required.";

        if (($data['operation_type'] == 1 || $data['operation_type'] == 2 || $data['operation_type'] == 5) && empty($data['remark']))
            $errorMessage['remark'] = "Description is required.";

        if ($data['operation_type'] == 2 && empty($data['rw_process_id']))
            $errorMessage['rework_process'] = "Rework Process is required.";
        

        if ($data['operation_type'] == 5 && empty($data['rw_process_id']))
            $errorMessage['rw_process_id'] = "Special Marking is required.";

        if ($data['operation_type'] == 5 && empty($data['rr_stage']))
            $errorMessage['rr_stage'] = "Deviation Reason is required.";
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['entry_date'] = date("Y-m-d");
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->finalCFT->saveCFTQty($data));
        endif;
    }

    public function convertToRej(){
        $data = $this->input->post();
        $this->data['dataRow']=$dataRow = $this->primaryCFT->getRejMovementData($data['id']);
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 1;
        $this->data['rejectionComments'] = $this->comment->getCommentList();  
        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_id="Raw Material" data-process_name="Raw Material">Raw Material</option>';
        $jobProcess = explode(",", $dataRow->process);
        if (!empty($dataRow->process_id)) {
            $in_process_key = array_keys($jobProcess, $dataRow->process_id)[0];
            foreach ($jobProcess as $key => $value) {
                if ($key <= $in_process_key) {
                    $processData = $this->process->getProcess($value);
                    if(!empty($this->controlPlanEnable)){
                        $prdProcessData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $dataRow->product_id, 'process_id' => $value]);
                        $pfcData = (!empty($prdProcessData->pfc_process)) ? $this->controlPlan->getPfcForProcess($prdProcessData->pfc_process) : array();
                        
                        if (!empty($pfcData)) {
                            foreach ($pfcData as $pfc) {
                                $stageHtml .= '<option value="' . $pfc->id . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">[' . $pfc->process_no . '] ' . $pfc->parameter . '</option>';
                            }
                        }
                    }else{
                        $stageHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                    }
                }
            }
        }
        $this->data['dataRow']->stage = $stageHtml;
       
        $this->load->view($this->cft_rej_form,$this->data);
    }

    public function getRRByOptions()
    {
        $data = $this->input->post();
        $vendorData = $this->processMovement->getRejRWBy($data);
        $rejOption = '<option value="" data-party_name="In House">Select </option>';
        if($data['process_id'] == 'Raw Material'){
            $jobData = new stdClass();
            $jobData->id = $data['job_card_id'];
            $jobData->product_id = $data['part_id'];
            $rmData = $this->jobcard->getMaterialIssueData($jobData)['result'];
            if (!empty($rmData)) :
                foreach ($rmData as $row) :
                    $rejOption .= '<option value="' . $row->party_id . '" data-party_name="' . (!empty($row->party_name)?$row->party_name:'In House') . '">' . (!empty($row->party_name)?$row->party_name:'In House') . '</option>';
                endforeach;
            endif;
        }else{
            $vendorData = $this->processMovement->getRejRWBy($data);
            if (!empty($vendorData)) :
                foreach ($vendorData as $row) :
                    $rejOption .= '<option value="' . $row->vendor_id . '" data-party_name="' . (!empty($row->party_name)?$row->party_name:'In House') . '">' . (!empty($row->party_name)?$row->party_name:'In House') . '</option>';
                endforeach;
            endif;
        }

        $dimOptions='<option value="">Select</option>';
        if(!empty($this->controlPlanEnable)){
            $fmeData = $this->controlPlan->getCPDimenstion(['pfc_id'=>$data['pfc_id'],'item_id'=>$data['part_id'],'rmd'=>1]);
            if(!empty($fmeData)){
                foreach($fmeData as $fme){
                    if($fme->parameter_type ==1){
                        $range ='';
                        if($fme->requirement==1){ $range = $fme->min_req.'/'.$fme->max_req ; }
                        if($fme->requirement==2){ $range = $fme->min_req.' '.$fme->other_req ; }
                        if($fme->requirement==3){ $range = $fme->max_req.' '.$fme->other_req ; }
                        if($fme->requirement==4){ $range = $fme->other_req ; }
                        $dimOptions .= '<option value="' . $fme->id . '" >'.$fme->parameter.' ['.$range.']</option>';
                    }
                }
            }
        }
        
        $this->printJson(['status' => 1, 'rejOption' => $rejOption,'dimOptions'=>$dimOptions]);
    }

    public function convertToRw(){
        $data = $this->input->post();
        $this->data['dataRow']=$dataRow = $this->primaryCFT->getRejMovementData($data['id']);
        $this->data['entry_type'] = 3;
        $this->data['operation_type'] = 2;
        $this->data['reworkComment'] = $this->comment->getReworkCommentList();  
        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_name="Raw Material" data-process_id="Raw Material">Row Material</option>';
        $reworkProcessHtml='<option value="">Select Rework Process</option>';
        $jobProcess = explode(",", $dataRow->process);
        if (!empty($dataRow->process_id)) {
            $in_process_key = array_keys($jobProcess, $dataRow->process_id)[0];
            foreach ($jobProcess as $key => $value) {
                $processData = $this->process->getProcess($value);
                if(!empty($this->controlPlanEnable)){
                    $prdProcessData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $dataRow->product_id, 'process_id' => $value]);
                    $pfcData = (!empty($prdProcessData->pfc_process)) ? $this->controlPlan->getPfcForProcess($prdProcessData->pfc_process) : array();
                    
                    if (!empty($pfcData)) {
                        foreach ($pfcData as $pfc) {
                            $stageHtml .= '<option value="' . $pfc->id . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">[' . $pfc->process_no . '] ' . $pfc->parameter . '</option>';
                        }
                    }
                }else{
                    $stageHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                }
                    
                if ($key <= $in_process_key) {
                    $reworkProcessHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                }
            }
        }
        $this->data['dataRow']->stage = $stageHtml;
        $this->data['dataRow']->reworkProcess = $reworkProcessHtml;
        $this->load->view($this->cft_rw_form,$this->data);
    }

    public function confirmCft(){
        $data = $this->input->post();
        $primaryCFTData = $this->primaryCFT->getRejMovementData($data['id']);

        $finalCFTData = [
            'id'=>'',
            'ref_id'=>$data['id'],
            'entry_type'=>3,
            'operation_type'=>$primaryCFTData->operation_type,
            'ref_type'=>$primaryCFTData->entry_type,
            'job_trans_id'=>$primaryCFTData->job_trans_id,
            'job_card_id'=>$primaryCFTData->job_card_id,
            'qty'=>$primaryCFTData->qty,
            'rr_reason'=>$primaryCFTData->rr_reason,
            'rej_type'=>$primaryCFTData->rej_type,
            'rr_stage'=>$primaryCFTData->rr_stage,
            'dimension_range'=>$primaryCFTData->dimension_range,
            'rr_by'=>$primaryCFTData->rr_by,
            'rw_process_id'=>$primaryCFTData->rw_process_id,
            'remark'=>$primaryCFTData->remark,
            'entry_date'=> date("Y-m-d"),
            'opr_mt'=>$primaryCFTData->opr_mt,
            'created_by'=> $this->session->userdata('loginId')
        ];
        $this->printJson($this->finalCFT->saveCFTQty($finalCFTData));
    }

    public function convertToUD(){
        $data = $this->input->post();
        $this->data['entry_type'] = 3;
        $this->data['operation_type'] =5;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ud_form,$this->data);
    }

    public function convertToUdOk(){
        $data = $this->input->post();
        $this->data['entry_type'] = 4;
        $this->data['operation_type']= 4;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form,$this->data);
    }
    public function convertToUdRej(){
        $data = $this->input->post();
        $this->data['entry_type'] = 4;
        $this->data['operation_type']= 1;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form,$this->data);
    }
}
?>