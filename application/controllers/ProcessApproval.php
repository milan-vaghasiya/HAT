<?php
class ProcessApproval extends MY_Controller{
    private $indexPage = "process_approval/index";
    private $approvalForm = "process_approval/form";
    private $approvalList = "process_approval/process_wise_approve";
    private $storeLocation = "process_approval/store_location";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Process Approval";
		$this->data['headData']->controller = "processApproval";
		$this->data['headData']->pageUrl = "processApproval";
	}

    public function processApproved(){
        $id = $this->input->post('id');
        $outwardData = $this->processApprove->getOutward($id);

        $outwardData->in_process_name = (!empty($outwardData->in_process_id))?$this->process->getProcess($outwardData->in_process_id)->process_name:"Initial Stage";
        $outwardData->out_process_name = (!empty($outwardData->out_process_id))?$this->process->getProcess($outwardData->out_process_id)->process_name:"";
		$outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
        $outwardData->pqty = $outwardData->in_qty - $outwardData->out_qty;
        $this->data['dataRow'] = $outwardData;
        if(empty($outwardData->in_process_id)):
            $this->data['materialBatch'] = $this->processApprove->getBatchStock($outwardData->job_card_id,$outwardData->out_process_id);
        else:
            $this->data['materialBatch'] = $this->processApprove->getBatchStockOnProductionTrans($outwardData->job_card_id,$outwardData->in_process_id,$outwardData->out_process_id,$id);
        endif;
        $this->data['machineData'] = $this->machine->getProcessWiseMachine($outwardData->out_process_id);
        $this->data['vendorData'] = $this->party->getVendorList();
        $this->data['consumableData'] = $this->item->getProductKitOnProcessData($outwardData->product_id,$outwardData->out_process_id);
        $this->data['outwardTrans'] = $this->processApprove->getOutwardTrans($outwardData->id,$outwardData->out_process_id)['outwardTrans'];
        $this->data['employeeData'] = $this->employee->getSetterList();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->load->view($this->approvalForm,$this->data);
    }
    
    public function getJobWorkOrderNoList(){
        $data = $this->input->post();
        $this->printJson($this->processApprove->getJobWorkOrderNoList($data));
    }
    
    public function getJobWorkOrderProcessList(){
        $data = $this->input->post();
        $this->printJson($this->processApprove->getJobWorkOrderProcessList($data)); 
    }

    public function getMaterialBatch($id){
        $outwardData = $this->processApprove->getOutward($id);

        if(empty($outwardData->in_process_id)):
            $materialBatch = $this->processApprove->getBatchStock($outwardData->job_card_id,$outwardData->out_process_id);
        else:
            $materialBatch = $this->processApprove->getBatchStockOnProductionTrans($outwardData->job_card_id,$outwardData->in_process_id,$outwardData->out_process_id,$id);
        endif;

        $html = '<option value="">Material Batch</option>';
        foreach($materialBatch as $row):
            $pending_qty = $row->issue_qty - $row->used_qty;
            if( $pending_qty > 0):
                $html .=  '<option value="'.$row->id.'" data-batch_no="'.$row->batch_no.'" data-issue_qty="'.$row->issue_qty.'" data-used_qty="'.$row->used_qty.'" data-wp_qty="'.$row->wp_qty.'">'.$row->batch_no.'</option>';
            endif;
        endforeach;
        return $html;
    }

    /* Save Outward Trans */
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card No. is required.";
        if(empty($data['product_id']))
            $errorMessage['product_id'] = "Product Name is required.";
        if(empty($data['out_process_id']))
            $errorMessage['out_process_id'] = "Out To Process is required.";
        if($data['vendor_id'] == "")
            $errorMessage['vendor_id'] = "Vendor Name is required.";
        if(!empty($data['vendor_id'])):
            if(empty($data['job_process_ids']))
                $errorMessage['job_process_ids'] = "Job Order Process is required.";
        endif;
        if(empty($data['out_qty']))
            $errorMessage['out_qty'] = "Out Qty. is required.";

        if(empty($data['setup_status'])):
            if(empty($data['setter_id'])):
                $errorMessage['setter_id'] = "Setter Name is required.";
            endif;
            if(empty($data['machine_id'])):
                $errorMessage['machine_id'] = "Machine No. is required.";
            endif;
        endif;
        if(empty($data['entry_date']) OR $data['entry_date'] == null OR $data['entry_date'] == ""):
            $errorMessage['entry_date'] = "Date is required.";
		else:
		    if(empty($data['material_used_id'])):
		        $errorMessage['material_used_id'] = "Batch No. is required.";
			elseif(empty($data['batch_no'])):
				$errorMessage['material_used_id'] = "Batch No. is required.";
			else:
				$data['req_qty'] = $data['out_qty'] * $data['wp_qty'];
				$stockQty = $data['issue_qty'] - $data['used_qty'];
				if(intVal($stockQty) < intVal($data['req_qty']))
					$errorMessage['material_used_id'] = "Stock Not Available";
			endif;
		endif;
		
        $outwardData = $this->processApprove->getOutward($data['ref_id']);
        if(!empty($data['out_qty'])):            
            $pendingQty = $outwardData->in_qty - $outwardData->out_qty;
            if($pendingQty < $data['out_qty'])
                $errorMessage['out_qty'] = "Qty not available for approval.";
        endif;

        if(!empty($data['job_process_ids'])):
            $processList = explode(",",$data['job_process_ids']);

            $jobProcess = $this->jobcard->getJobcard($data['job_card_id'])->process;
            $jobProcess = explode(",",$jobProcess);

            $a=0;$jwoProcessIds=array();
            foreach($jobProcess as $key=>$value):                
                if(isset($processList[$a])):
                    $processKey = array_search($processList[$a],$jobProcess);
                    $jwoProcessIds[$processKey] = $processList[$a];
                    $a++;
                endif;
            endforeach;
            ksort($jwoProcessIds);

            $processList = array();
            foreach($jwoProcessIds as $key=>$value):
                $processList[] = $value;
            endforeach;
        
            $in_process_key = array_search($data['out_process_id'],$jobProcess);
            $i=0;$error = false;       
            foreach($jobProcess as $key=>$value):
                if($key >= $in_process_key):
                    if(isset($processList[$i])):
                        //print_r($processList[$i]."=".$value);
                        if($processList[$i] != $value):
                            $error = true;
                            break;
                        endif;
                        $i++;
                    endif;
                endif;
            endforeach;
            if($error == true):
                $errorMessage['job_process_ids'] = "Invalid Process Sequence.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			unset($data['wp_qty'],$data['issue_qty']);           
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->processApprove->save($data);
            $result['materialBatch'] = $this->getMaterialBatch($data['ref_id']);
            $this->printJson($result);
        endif;
    }

    /* Delete Outward Trans */
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->processApprove->delete($id);
            $result['materialBatch'] = $this->getMaterialBatch($result['job_approval_id']);
            $this->printJson($result);
        endif;
    }

    public function storeLocation(){
        $id = $this->input->post('id');
        $transid = $this->input->post('transid');
        $jobcardData = $this->jobcard->getJobCard($id);
        $outwardData = $this->processApprove->getOutward($transid);
		$outwardData->minDate = (!empty($outwardData->entry_date)) ? $outwardData->entry_date : $outwardData->job_date;
		$this->data['dataRow'] = $outwardData;
		
        $this->data['job_id'] = $id;
        $this->data['ref_id'] = $transid;
        $this->data['jobNo'] = getPrefixNumber($jobcardData->job_prefix, $jobcardData->job_no);
        $this->data['qty'] = $jobcardData->unstored_qty;
        $this->data['pending_qty'] = $jobcardData->unstored_qty;
        $this->data['product_name'] = $this->item->getItem($jobcardData->product_id)->item_code;
        $this->data['locationData'] = $this->store->getStoreLocationListWithoutProcess();
        $this->data['transactionData'] = $this->processApprove->getStoreLocationTrans($id);
        $this->load->view($this->storeLocation,$this->data);
    }

    public function saveStoreLocation(){
        $data = $this->input->post();
        $errorMessage = array();
        $jobcardData = $this->jobcard->getJobCard($data['job_id']);

        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if(!empty($data['qty']) && $data['qty'] != "0.000"):            
            if($data['qty'] > $jobcardData->unstored_qty):
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        else:
            $errorMessage['qty'] = "Qty is required.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['trans_date'] = formatDate($data['trans_date'],'Y-m-d');
            $data['product_id'] = $jobcardData->product_id;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->processApprove->saveStoreLocation($data));
        endif;
    }

    public function deleteStoreLocationTrans(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->processApprove->deleteStoreLocationTrans($id));
        endif;
    }
}
?>