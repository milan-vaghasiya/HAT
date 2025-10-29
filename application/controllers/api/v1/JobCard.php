<?php
class JobCard extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $job_category = (isset($_REQUEST['job_category']) && !empty($_REQUEST['job_category']))?$_REQUEST['job_category']:"";
        $order_status = (isset($_REQUEST['order_status']) && !empty($_REQUEST['order_status']))?$_REQUEST['order_status']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'job_category'=>$job_category,'order_status'=>$order_status];
        $this->data['jobCardList'] = $this->jobcard_v2->getJobCardListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function customerList(){
        $this->data['customerData'] = $this->jobcard_v2->getCustomerList();
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function customerSalesOrderList(){
        $orderData = $this->jobcard_v2->getCustomerSalesOrder($this->input->post('party_id'));
        $dataRow = array();
        foreach($orderData as $row):
            $dataRow[] = [
                'id' => $row->id,
                'order_no' => getPrefixNumber($row->trans_prefix,$row->trans_no)
            ];
        endforeach;
        $this->data['orderData'] = $dataRow;
		$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function productList(){
        $data = $this->input->post();
        $productData = $this->jobcard_v2->getProductList($data)['productData'];
        $dataRow = array();
        if(empty($data['sales_order_id'])):
            foreach($productData as $row):
                $dataRow[] = [
                    'id' => $row->id,
                    'delivery_date' => date("Y-m-d"),
                    'job_category' => 0,
                    'item_code' => $row->item_code
                ];
            endforeach;
        else:
            foreach($productData as $row):
                $dataRow[] = [
                    'id' => $row->item_id,
                    'delivery_date' => (!empty($row->cod_date))?$row->cod_date:date("Y-m-d"),
                    'job_category' => ($row->order_type == 1)?0:1,
                    'item_code' => $row->item_code.' (Ord. Qty. : '.$row->qty.')'
                ];
            endforeach;
        endif;
        $this->data['productList'] = $dataRow;
		$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function jobCategory(){
        $this->data['jobCategory'] = [
            ['id'=>0,'name'=>'Manufacturing'],
            ['id'=>1,'name'=>'Job Work']
        ];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function productProcess(){
        $data = $this->input->post();
        $this->data['productProcess'] = $this->jobcard_v2->getProductProcess($data)['processData'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['job_date']))
            $errorMessage['job_date'] = "Job Card date is required.";
        if($data['party_id'] == "")
			$errorMessage['party_id'] = "Customer is required.";
		if(empty($data['product_id']))
			$errorMessage['product_id'] = "Product is required.";
		if(empty($data['qty']) || $data['qty'] == "0.000")
			$errorMessage['qty'] = "Quantity is required.";
        if(!empty($data['delivery_date']) && !empty($data['job_date'])):
            if($data['job_date'] > $data['delivery_date']):
                $errorMessage['delivery_date'] = "Invalid delivery date.";
            endif;
        endif;
		if(empty($data['process'])):
			$errorMessage['process'] = "Product Process is required.";
        else:
            $data['process'] = explode(",",$data['process']);
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobcard_v2->save($data));
        endif;
    }

    public function edit($id){
        $jobData = $this->jobcard_v2->getJobcard($id);
        $this->data['jobCardData'] = $jobData;
        $this->data['customerData'] = $this->jobcard_v2->getCustomerList();

        $dataRow = array();
        if(!empty($jobData->sales_order_id)):
            $orderData = $this->jobcard_v2->getCustomerSalesOrder($jobData->party_id);
            foreach($orderData as $row):
                $dataRow[] = [
                    'id' => $row->id,
                    'order_no' => getPrefixNumber($row->trans_prefix,$row->trans_no)
                ];
            endforeach;
        endif;
        $this->data['orderData'] = $dataRow;

        $productData = $this->jobcard_v2->getProductList($jobData->sales_order_id)['productData'];
        $dataRow = array();
        if(empty($jobData->sales_order_id)):
            foreach($productData as $row):
                $dataRow[] = [
                    'id' => $row->id,
                    'delivery_date' => date("Y-m-d"),
                    'job_category' => 0,
                    'item_code' => $row->item_code
                ];
            endforeach;
        else:
            foreach($productData as $row):
                $dataRow[] = [
                    'id' => $row->item_id,
                    'delivery_date' => (!empty($row->cod_date))?$row->cod_date:date("Y-m-d"),
                    'job_category' => ($row->order_type == 1)?0:1,
                    'item_code' => $row->item_code.' (Ord. Qty. : '.$row->qty.')'
                ];
            endforeach;
        endif;
        $this->data['productList'] = $dataRow;

        $this->data['productProcess'] = $this->jobcard_v2->getProductProcess(['product_id'=>$jobData->product_id])['processData'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function view($id){
        $jobCardData = $this->jobcard_v2->getJobcard($id);
        if(empty($jobCardData->party_name)){$jobCardData->party_name ="Self";}
        if(empty($jobCardData->party_code)){$jobCardData->party_code ="Self Stock";}
        $process = explode(",","0,".$jobCardData->process);
        $jobCardData->first_process_id = $process[1];
        $dataRows = array(); $totalCompleteQty=0; $totalRejectQty=0; $stages = array(); $stg = array(); $s=0;$runningStages = array(); $totalScrapQty=0;
        foreach($process as $process_id):
            $row = new stdClass;
            $jobApprovalData = $this->processApprove_v2->getProcessWiseApprovalData($id,$process_id);
            $row->process_id = $process_id;
            $row->process_name = (!empty($jobApprovalData))?$jobApprovalData->in_process_name:((!empty($process_id))?$this->process->getProcess($process_id)->process_name:"Initial Stage");

            $row->job_id = $id;
            $row->id = (!empty($jobApprovalData))?$jobApprovalData->id:0;
            $row->product_id = $jobCardData->product_id;
            $row->product_code = $jobCardData->product_code;
            $row->vendor = (!empty($jobApprovalData))?$jobApprovalData->vendor:"";
            $row->inward_qty = (!empty($jobApprovalData->inward_qty))?$jobApprovalData->inward_qty:0;
            $row->in_qty = (!empty($jobApprovalData))?$jobApprovalData->in_qty:0;
            $row->out_qty = (!empty($jobApprovalData))?$jobApprovalData->out_qty:0;
            $row->total_ok_qty = (!empty($jobApprovalData))?$jobApprovalData->total_ok_qty:0;
            $row->total_rejection_qty = (!empty($jobApprovalData))?$jobApprovalData->total_rejection_qty:0;
            $row->total_rework_qty = (!empty($jobApprovalData))?$jobApprovalData->total_rework_qty:0;

            $completeQty = $row->total_ok_qty + $row->total_rejection_qty + $row->total_rework_qty;
            $row->pending_qty = $row->in_qty - $completeQty;
            $row->scrap_qty = (!empty($jobApprovalData))?round(($jobApprovalData->pre_finished_weight - $jobApprovalData->finished_weight) * $row->in_qty,2):0;

            $totalScrapQty += $row->scrap_qty;
            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->in_qty):0;
            $row->status = round($processPer,2);
            
            $row->process_approvel_data = $jobApprovalData;
            $dataRows[] = $row;
            $totalCompleteQty += $completeQty;
            $totalRejectQty += $row->total_rejection_qty;
            if($row->inward_qty == 0 and $row->in_qty == 0 and $s > 0):
				$stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s-1)];
			else:
                if(!empty($row->process_id)):
				    $runningStages[] = $row->process_id;
                endif;
			endif;
			$s++;
        endforeach;
        
        $jobProcessPer = (!empty($totalCompleteQty))?($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty)) ):"0";
		$jobCardData->jobPer = round($jobProcessPer,2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if($jobCardData->order_status == 0):
            $jobCardData->order_status = 'Pending - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 1):
            $jobCardData->order_status = 'Start - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 2):
            $jobCardData->order_status = 'In-Process - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 3):
            $jobCardData->order_status = 'On-Hold - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 4):
            $jobCardData->order_status = 'Complete - '.round($jobProcessPer,2).'%';
        else:
            $jobCardData->order_status = 'Closed';
        endif;
        $stages['stages'] = $stg;
		$stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows;
        $this->data['dataRow'] = $jobCardData;
        $this->data['reqMaterial'] = $this->jobcard_v2->getProcessWiseRequiredMaterial($jobCardData)['resultData'];        
        $this->data['stageData'] = $stages;
        $this->data['totalScrapQty'] = $totalScrapQty;
		$this->data['processDataList'] = $this->process->getProcessList();
        $this->data['jobBom']=$this->jobcard_v2->getJobBomQty($jobCardData->id,$jobCardData->product_id);
        $this->data['rawMaterial'] = $this->item->getItemLists("3");

        $reqMaterials = $this->jobcard_v2->getProcessWiseRequiredMaterials($jobCardData);
        $this->data['reqMaterials'] = $reqMaterials['resultData'][0];

        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function delete($id){
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard_v2->delete($id));
        endif;
    }

    public function materialRequest($id){
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcard($id);
        $this->data['disptachData'] = $this->jobcard_v2->getRequestItemData($id);
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['kitData'] = $this->item->getProductKitData($id);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]); 
    }

    public function saveMaterialRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Date is required.";

        if(empty($data['bom_item_id'])):
            $errorMessage['general_error'] = "Items is required.";
        else:
            $i=1;
            foreach($data['bom_item_id'] as $key=>$value):
                if(empty($data['request_qty'][$key])):
                    $errorMessage['request_qty'.$i] = "Request Qty is required.";
                endif;
                $i++;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobcard_v2->saveMaterialRequest($data));
        endif;        
    }

    public function materialReceived(){
        $data = $this->input->post();
        $data['mr_at'] = date("Y-m-d H:i:s");
        $this->printJson($this->jobcard_v2->materialReceived($data));
    }

    public function changeJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard_v2->changeJobStatus($data));
        endif;
    }

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $dataRow = array();
        foreach($batchData as $row):
			if($row->qty > 0):
                $dataRow[] = [
                    'batch_no' => $row->batch_no,
                    'stock_qty' => $row->qty
                ];
			endif;
        endforeach;
        $this->data['batchData'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]); 
    }
}
?>