<?php

class ProcessApproval extends MY_Controller
{
    private $approvalForm = "production_v2/process_approval/form";
    private $storeLocation = "production_v2/process_approval/store_location";
    private $holdToOKForm = "production_v2/job_work_vendor/hold_ok_form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Process Approval";
        $this->data['headData']->controller = "production_v2/processApproval";
        $this->data['headData']->pageUrl = "production_v2/processApproval";
    }

    public function processApproved()
    {
        $data=$this->input->post();
        $id =  $data['id'];
        $vp_trans_id = ($this->input->post('vp_trans_id')) ? $this->input->post('vp_trans_id') : 0;
        $outwardData = $this->processApprove_v2->getApprovalData($id);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        // $outwardData->pqty = $outwardData->in_qty - $outwardData->out_qty;
        $logData = $this->logSheet->getPrdLogOnProcessNJob($outwardData->job_card_id, $outwardData->in_process_id);
        $rework_qty = (!empty($logData)) ? $logData->rework_qty : 0;
        $rejection_qty = (!empty($logData)) ? $logData->rejection_qty : 0;

        if(!empty($data['pending_qty'])){
            $outwardData->pqty =$data['pending_qty'];
        }
        else{
            if(!empty($vp_trans_id)):
                $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($vp_trans_id); 
                $outwardData->pqty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
             else:
                $outwardData->pqty = $outwardData->in_qty - $outwardData->out_qty - $rejection_qty;// - $rework_qty;
             endif;
        }
        
       
        $outwardData->vp_trans_id = $vp_trans_id;
        $this->data['dataRow'] = $outwardData;
        if (!empty($vp_trans_id) || !empty($data['from_entry_type'])) {
            $jobCardData = $this->jobcard_v2->getJobcard($outwardData->job_card_id);
            $jobProcess = explode(",", $jobCardData->process);
            $in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
            $html = '<option value="">Select Stage</option>
                 <option value="0" data-process_name="Row Material">Row Material</option>';
            foreach ($jobProcess as $key => $value) :
                if ($key <= $in_process_key) :
                    $processData = $this->process->getProcess($value);
                    $html .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
                endif;
            endforeach;
            $this->data['dataRow']->stage = $html;
        }
        if (empty($outwardData->in_process_id)) :
            $this->data['materialBatch'] = $this->processApprove_v2->getBatchStock($outwardData->job_card_id, $outwardData->out_process_id);
        else :
            $this->data['materialBatch'] = $this->processApprove_v2->getBatchStockOnProductionTrans($outwardData->job_card_id, $outwardData->in_process_id, $outwardData->out_process_id, $id);
        endif;
        $this->data['machineData'] = $this->machine->getProcessWiseMachine($outwardData->out_process_id);
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['consumableData'] = $this->item->getProductKitOnProcessData($outwardData->product_id, $outwardData->out_process_id);
        $entryType=1;
        if(!empty($data['from_entry_type']) && $data['from_entry_type']==2 && !empty($data['trans_ref_id'])){ $entryType=4; }
        if(!empty($vp_trans_id)){
            $this->data['outwardTrans'] = $this->processApprove_v2->getOutwardTrans($outwardData->id, $outwardData->in_process_id, (!empty($data['trans_ref_id'])?$data['trans_ref_id']:0) , (!empty($data['from_entry_type'])?$data['from_entry_type']:'1,2'), $vp_trans_id)['outwardTrans'];
        }
        else{
            $this->data['outwardTrans'] = $this->processApprove_v2->getOutwardTrans($outwardData->id, $outwardData->in_process_id,(!empty($data['trans_ref_id'])?$data['trans_ref_id']:0),$entryType)['outwardTrans'];
        }
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['from_entry_type'] =!empty($data['from_entry_type'])?$data['from_entry_type']:'';
        $this->data['trans_ref_id'] =!empty($data['trans_ref_id'])?$data['trans_ref_id']:'';
        
        //Changed By Karmi @02/08/2022
        if(!empty($data['vp_trans_id'])){
            $this->data['returnVendorMaterial'] = $this->jobWorkVendor_v2->getChallanData($data['vp_trans_id']);
            if(!empty($this->data['returnVendorMaterial']))
            {
                $this->data['challanDataRow'] = $this->jobWorkVendor_v2->getVendorChallan($this->data['returnVendorMaterial']->id);
                if(!empty($this->data['challanDataRow'])){
    		        $this->data['packingData'] = json_decode($this->data['challanDataRow']->material_data);
                }
            }
        }
        $this->load->view($this->approvalForm, $this->data);
    }

    public function getJobWorkOrderNoList()
    {
        $data = $this->input->post();
        $this->printJson($this->processApprove_v2->getJobWorkOrderNoList($data));
    }

    public function getJobWorkOrderProcessList()
    {
        $data = $this->input->post();
        $this->printJson($this->processApprove_v2->getJobWorkOrderProcessList($data));
    }

    public function getMaterialBatch($id)
    {
        $outwardData = $this->processApprove_v2->getOutward($id);
        if (empty($outwardData->in_process_id)) :
            $materialBatch = $this->processApprove_v2->getBatchStock($outwardData->job_card_id, $outwardData->out_process_id);
        else :
            $materialBatch = $this->processApprove_v2->getBatchStockOnProductionTrans($outwardData->job_card_id, $outwardData->in_process_id, $outwardData->out_process_id, $id);
        endif;
        $html = '<option value="">Material Batch</option>';
        foreach ($materialBatch as $row) :
            $pending_qty = $row->issue_qty - $row->used_qty;
            if ($pending_qty > 0) :
                $html .=  '<option value="' . $row->id . '" data-batch_no="' . $row->batch_no . '" data-issue_qty="' . $row->issue_qty . '" data-used_qty="' . $row->used_qty . '" data-wp_qty="' . $row->wp_qty . '">' . $row->batch_no . '</option>';
            endif;
        endforeach;
        return $html;
    }

    /* Save Outward Trans */
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
            if (empty($data['job_process_ids'])){
                $errorMessage['job_process_ids'] = "Job Order Process is required.";
            }
            if (empty($data['in_qty_kg'])){
                $errorMessage['in_qty_kg'] = "Out Qty.(Kg) is required.";
            }
        endif;
        if (empty($data['production_qty']) && !empty($data['vp_trans_id']))
            $errorMessage['production_qty'] = "Production Qty. is required.";

        if (empty($data['production_qty']) && !empty($data['trans_ref_id']))
            $errorMessage['production_qty'] = "Production Qty. is required.";

        if (empty($data['out_qty']) && empty($data['vp_trans_id']) && empty($data['trans_ref_id']))
            $errorMessage['out_qty'] = "Out Qty. is required.";

        if (!empty($data['out_qty']) && $data['out_qty'] < 0)
            $errorMessage['out_qty'] = "Out Qty. is invalid.";

        if (empty($data['entry_date']) or $data['entry_date'] == null or $data['entry_date'] == "") :
            $errorMessage['entry_date'] = "Date is required.";
        endif;
        
        if (!empty($data['return_item_id'][0])) :
            if (empty($data['return_in_qty'][0])){
                $errorMessage['return_in_qty'] = "Qty is required.";
            }
        endif;

        $outwardData = $this->processApprove_v2->getApprovalData($data['ref_id']);
        if (!empty($data['out_qty']) || !empty($data['production_qty'])):
            $logData = $this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'],$data['in_process_id']);
            $rejectionQty = (!empty($logData))?$logData->rejection_qty:0;
            if(empty($data['vp_trans_id'])):                
                $pendingQty = $outwardData->in_qty - ($outwardData->out_qty + $rejectionQty);                
            else:
                $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($data['vp_trans_id']); 
                $pendingQty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
            endif;

            if($pendingQty < $data['out_qty'] && empty($data['trans_ref_id'])):
                $errorMessage['out_qty'] = "Qty not available.";
            endif;
            if(!empty($data['trans_ref_id'])):
                $hldData=$this->holdArea->getHoldAreaTRans($data['trans_ref_id']);
                $qtyData=$this->holdArea->getOutwardQtyFrmHLD($data['trans_ref_id'],1);
                $pendingQty=$hldData->in_qty-$qtyData->ok_qty-$qtyData->rej_qty;
                // print_r($pendingQty);exit;
                if($pendingQty<$data['production_qty']){
                    $errorMessage['production_qty'] = "Qty not available.";
                }
            endif;

        endif;

        if (!empty($data['job_process_ids'])) :
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
        
        $data['material_data']=""; $materialArray = array();
		if(isset($data['return_item_id']) && !empty($data['return_item_id'])):
			foreach($data['return_item_id'] as $key=>$value):
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $data['return_out_qty'][$key],
					'in_qty' => $data['returnQty'][$key] + $data['return_in_qty'][$key]
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->processApprove_v2->save($data);
            $this->printJson($result);
        endif;
    }

    /* Delete Outward Trans */
    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $result = $this->processApprove_v2->delete($id);
            $this->printJson($result);
        endif;
    }

    public function storeLocation()
    {
        $id = $this->input->post('id');
        $transid = $this->input->post('transid');
        $jobcardData = $this->jobcard_v2->getJobCard($id);
        $outwardData = $this->processApprove_v2->getOutward($transid);
        $outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $this->data['dataRow'] = $outwardData;

        $this->data['job_id'] = $id;
        $this->data['ref_id'] = $transid;
        $this->data['jobNo'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);
        $this->data['qty'] = $jobcardData->unstored_qty;
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['transactionData'] = $this->processApprove_v2->getStoreLocationTrans($id);
        $this->load->view($this->storeLocation, $this->data);
    }

    public function saveStoreLocation()
    {
        $data = $this->input->post();
        $errorMessage = array();
        $jobcardData = $this->jobcard_v2->getJobCard($data['job_id']);
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
            $data['trans_date'] = formatDate($data['trans_date'], 'Y-m-d');
            $data['product_id'] = $jobcardData->product_id;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->processApprove_v2->saveStoreLocation($data));
        endif;
    }

    public function deleteStoreLocationTrans()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->processApprove_v2->deleteStoreLocationTrans($id));
        endif;
    }

    public function productionTransaction(){
        $data=$this->input->post();
        $id =  $data['id'];
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['dataRow']=$this->processApprove_v2->getOutwardTransDetail($id);
        $outwardData = $this->processApprove_v2->getApprovalData($this->data['dataRow']->production_approval_id);
        $this->data['machineData'] = $this->machine->getProcessWiseMachine($outwardData->out_process_id);
        $this->load->view($this->holdToOKForm, $this->data);
    }
}
