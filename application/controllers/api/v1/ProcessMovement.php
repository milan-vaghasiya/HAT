<?php
class ProcessMovement extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function processApproved($id,$vp_trans_id=0){
        $outwardData = $this->processApprove_v2->getApprovalData($id);
		$outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $logData = $this->logSheet->getPrdLogOnProcessNJob($outwardData->job_card_id, $outwardData->in_process_id);
        $rework_qty = (!empty($logData)) ? $logData->rework_qty : 0;
        $rejection_qty = (!empty($logData)) ? $logData->rejection_qty : 0;

        if(!empty($vp_trans_id)):
            $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($vp_trans_id); 
            $outwardData->pending_qty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
        else:
            $outwardData->pending_qty = $outwardData->in_qty - $outwardData->out_qty - $rejection_qty;// - $rework_qty;
        endif;
        
        $outwardData->vp_trans_id = $vp_trans_id;
        $this->data['dataRow'] = $outwardData;
        if (!empty($vp_trans_id)):
            $jobCardData = $this->jobcard_v2->getJobcard($outwardData->job_card_id);
            $jobProcess = explode(",", $jobCardData->process);
            $in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
            $dataRow = array();
            foreach ($jobProcess as $key => $value) :
                if ($key <= $in_process_key) :
                    $processData = $this->process->getProcess($value);
                    $dataRow[] = [
                        'id' => $processData->id,
                        'process_name' => $processData->process_name
                    ];
                endif;
            endforeach;
            $this->data['dataRow']->stage = $dataRow;
        endif;

        if(empty($outwardData->in_process_id)):
            $this->data['materialBatch'] = $this->processApprove_v2->getBatchStock($outwardData->job_card_id,$outwardData->out_process_id);
        else:
            $this->data['materialBatch'] = $this->processApprove_v2->getBatchStockOnProductionTrans($outwardData->job_card_id,$outwardData->in_process_id,$outwardData->out_process_id,$id);
        endif;
        $this->data['machineData'] = $this->machine->getProcessWiseMachine($outwardData->out_process_id);
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['consumableData'] = $this->item->getProductKitOnProcessData($outwardData->product_id,$outwardData->out_process_id);
        $this->data['outwardTrans'] = $this->processApprove_v2->getOutwardTrans($outwardData->id,$outwardData->in_process_id)['outwardTrans'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function getJobWorkOrderNoList(){
        $data = $this->input->post();
        $result = $this->processApprove_v2->getJobWorkOrderNoList($data)['result'];
        $dataRow = array();
        foreach($result as $row):
            $dataRow[] = [
                'id' => $row->id,
                'order_no' => getPrefixNumber($row->jwo_prefix,$row->jwo_no).' [ Ord. Qty. : '.$row->qty.' ]'
            ];
        endforeach;
        $this->data['jobWorkOrders'] = $dataRow;
        $this->data['jobWorkOrderProcess'] = $this->processApprove_v2->getJobWorkOrderProcessList($data)['jobWorkProcessList'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function getJobWorkOrderProcessList(){
        $data = $this->input->post();
        $this->data['jobWorkOrderProcess'] = $this->processApprove_v2->getJobWorkOrderProcessList($data)['jobWorkProcessList'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]); 
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card No. is required.";
        if (empty($data['product_id']))
            $errorMessage['product_id'] = "Product Name is required.";
        if ($data['vendor_id'] == "")
            $errorMessage['vendor_id'] = "Vendor Name is required.";
        if (!empty($data['vendor_id'])) :
            if (empty($data['job_process_ids']))
                $errorMessage['job_process_ids'] = "Job Order Process is required.";
        endif;

        if (empty($data['production_qty']) && !empty($data['vp_trans_id']))
            $errorMessage['production_qty'] = "Production Qty. is required.";

        if (empty($data['out_qty']) && empty($data['vp_trans_id']))
            $errorMessage['out_qty'] = "Out Qty. is required.";

        if (empty($data['entry_date']) or $data['entry_date'] == null or $data['entry_date'] == "") :
            $errorMessage['entry_date'] = "Date is required.";
        endif;

        $outwardData = $this->processApprove_v2->getApprovalData($data['ref_id']);
        if (!empty($data['out_qty'])):
            $logData = $this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'],$data['in_process_id']);
            $rejectionQty = (!empty($logData))?$logData->rejection_qty:0;
            if(empty($data['vp_trans_id'])):                
                $pendingQty = $outwardData->in_qty - ($outwardData->out_qty + $rejectionQty);                
            else:
                $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($data['vp_trans_id']); 
                $pendingQty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
            endif;

            if($pendingQty < $data['out_qty']):
                $errorMessage['out_qty'] = "Qty not available.";
            endif;

        endif;

        if (!empty($data['job_process_ids'])):
            $processList = explode(",", $data['job_process_ids']);
            $jobProcess = $this->jobcard_v2->getJobcard($data['job_card_id'])->process;
            $jobProcess = explode(",", $jobProcess);
            $a = 0;
            $jwoProcessIds = array();
            foreach ($jobProcess as $key => $value) :
                if (isset($processList[$a])) :
                    $processKey = array_search($processList[$a], $jobProcess);
                    $jwoProcessIds[$processKey] = $processList[$a];
                    $a++;
                endif;
            endforeach;
            ksort($jwoProcessIds);
            $processList = array();
            foreach ($jwoProcessIds as $key => $value) :
                $processList[] = $value;
            endforeach;

            $in_process_key = array_search($data['out_process_id'], $jobProcess);
            $i = 0;
            $error = false;
            foreach ($jobProcess as $key => $value) :
                if ($key >= $in_process_key) :
                    if (isset($processList[$i])) :
                        if ($processList[$i] != $value) :
                            $error = true;
                            break;
                        endif;
                        $i++;
                    endif;
                endif;
            endforeach;
            if ($error == true) :
                $errorMessage['job_process_ids'] = "Invalid Process Sequence.";
            endif;
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->loginId;
            if(isset($data['rejection_reason']) && !empty($data['rejection_reason'])):
                $data['rejection_reason'] = json_decode($data['rejection_reason']);
            endif;
            $result = $this->processApprove_v2->save($data);
            unset($result['outwardTrans']);
            $result['outwardTrans'] = $this->processApprove_v2->getOutwardTrans($data['ref_id'], $data['in_process_id'])['outwardTrans'];
            $this->printJson($result);
        endif;
    }

    public function delete($id){
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processApprove_v2->delete($id);
            if($result['status'] == 1):
                unset($result['outwardTrans']);
                $result['outwardTrans'] = $this->processApprove_v2->getOutwardTrans($result['job_approval_id'], $result['process_id'])['outwardTrans'];
                unset($result['job_approval_id'], $result['process_id']);
            endif;
            $this->printJson($result);
        endif;
    }

    public function storeLocation(){
        $job_id = $this->input->post('job_id');
        $ref_id = $this->input->post('ref_id');
        $jobcardData = $this->jobcard_v2->getJobCard($job_id);
        $outwardData = $this->processApprove_v2->getOutward($ref_id);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $this->data['dataRow'] = $outwardData;

        $this->data['job_id'] = $job_id;
        $this->data['ref_id'] = $ref_id;
        $this->data['job_no'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        //$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['transactionData'] = $this->processApprove_v2->getStoreLocationTrans($job_id)['result'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]); 
    }

    public function saveStoreLocation(){
        $data = $this->input->post();
        $errorMessage = array();        
        $jobcardData = $this->jobcard_v2->getJobCard($data['job_id']);

        $data['location_id'] = $this->PROD_STORE->id;
        $data['batch_no'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);

        if (empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if (!empty($data['qty']) && $data['qty'] != "0.000") :
            if ($data['qty'] > $jobcardData->unstored_qty) :
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        else :
            $errorMessage['qty'] = "Qty is required.";
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
            $data['product_id'] = $jobcardData->product_id;
            $data['created_by'] = $this->loginId;
            $result = $this->processApprove_v2->saveStoreLocation($data);
            if($result['status'] == 1):                
                $result['transactionData'] = $this->processApprove_v2->getStoreLocationTrans($data['job_id'])['result'];
                unset($result['htmlData']);
            endif;
            $this->printJson($result);
        endif;
    }

    public function deleteStoreLocationTrans($id){
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processApprove_v2->deleteStoreLocationTrans($id);
            if($result['status'] == 1):                
                $result['transactionData'] = $this->processApprove_v2->getStoreLocationTrans($result['ref_id'])['result'];
                unset($result['htmlData'],$result['ref_id']);
            endif;
            $this->printJson($result);
        endif;
    }
}
?>