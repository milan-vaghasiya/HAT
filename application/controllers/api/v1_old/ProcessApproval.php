<?php
class ProcessApproval extends MY_Apicontroller{
    public function __construct() {
        parent:: __construct();        
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

        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function getJobWorkOrderNoList(){
        $data = $this->input->post();
        $resultData = $this->processApprove->getJobWorkOrderNoList($data)['result'];
        $jobWorkOrders = array();
        foreach($resultData as $row):
            $jobWorkOrders[] = ['id'=>$row->id,'job_work_no'=>getPrefixNumber($row->jwo_prefix,$row->jwo_no).' [ Ord. Qty. : '.$row->qty.' ]'];
        endforeach;
        $this->data['jobWorkOrders'] = $jobWorkOrders;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function getJobWorkOrderProcessList(){
        $data = $this->input->post();
        $this->data['jobWorkProcessList'] = $this->processApprove->getJobWorkOrderProcessList($data)['jobWorkProcessList'];
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function getMaterialBatch(){
        $id = $this->input->post('id');
        $outwardData = $this->processApprove->getOutward($id);

        if(empty($outwardData->in_process_id)):
            $materialBatch = $this->processApprove->getBatchStock($outwardData->job_card_id,$outwardData->out_process_id);
        else:
            $materialBatch = $this->processApprove->getBatchStockOnProductionTrans($outwardData->job_card_id,$outwardData->in_process_id,$outwardData->out_process_id,$id);
        endif;

        $batchData = array();
        foreach($materialBatch as $row):
            $pending_qty = $row->issue_qty - $row->used_qty;
            if( $pending_qty > 0):
                $batchData[] = [
                    'id' => $row->id,
                    'batch_no' => $row->batch_no,
                    'issue_qty' => $row->issue_qty,
                    'used_qty' => $row->used_qty,
                    'wp_qty' => $row->wp_qty
                ];
            endif;
        endforeach;

        $this->data['materialBatch'] = $batchData;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

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
			if(empty($data['batch_no'])):
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
            $data['created_by'] = $this->loginId;
            $result = $this->processApprove->save($data);
            $result['outwardTrans'] = $this->processApprove->getOutwardTrans($data['ref_id'],$data['out_process_id'])['outwardTrans'];
            $result['materialBatch'] = $this->getMaterialBatch($data['ref_id']);
            $this->printJson($result);
        endif;
    }
}
?>