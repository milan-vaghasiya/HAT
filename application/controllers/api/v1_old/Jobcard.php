<?php
class Jobcard extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

   
    
    public function customerJobWorkList(){
        $status = ($this->input->post('status'))?$this->input->post('status'):0;
        $type = ($this->input->post('type'))?$this->input->post('type'):1;
        $total_rows = $this->jobcard->getCount($status,$type);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/jobcard/customerJobWorkList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['jobCardList'] = $this->jobcard->getJobCardList_api($config["per_page"], $page,$status,$type);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function createJobCard(){
        $this->data['jobPrefix'] = "JOB/".$this->shortYear.'/';
        $this->data['jobNo'] = $this->jobcard->getNextJobNo(0);
        $this->data['jobwPrefix'] = "JOBW/".$this->shortYear.'/';
        $this->data['jobwNo'] = $this->jobcard->getNextJobNo(1);
        $this->data['customerData'] = $this->jobcard->getCustomerList();
        $this->data['productData'] = $this->item->getItemList(1);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function customerSalesOrderList(){
        $orderData = $this->jobcard->getCustomerSalesOrder($this->input->post('party_id'));
        $options = array();
        foreach($orderData as $row):
            $options[] = ['id'=>$row->id,'order_no'=>getPrefixNumber($row->trans_prefix,$row->trans_no)];
        endforeach;
        $result['salesOrderList'] = $options;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$result]);
    }

    public function getProductList(){
        $data = $this->input->post();
		$resultData = $this->jobcard->getProductList($data);
        $productData = $resultData['productData'];
        $transDate = $resultData['trans_date'];
		
        $productList = array();
        if(empty($data['sales_order_id'])):
            if(!empty($productData)):
                foreach($productData as $row):
                    $productList[] = [
                        'id' => $row->id,
                        'delivery_date' => date("Y-m-d"),
                        'order_type' => 0,
                        'item_code' => $row->item_code
                    ];
                endforeach;
            endif;
        else:
            if(!empty($productData)):
                foreach($productData as $row):
                    $jobType = ($row->order_type == 1)?0:1;

                    $productList[] = [
                        'id' => $row->item_id,
                        'delivery_date' => ((!empty($row->cod_date))?$row->cod_date:date("Y-m-d")),
                        'order_type' => $jobType,
                        'item_code' => $row->item_code.'(Ord. Qty. : '.$row->qty.')'
                    ];
                endforeach;
            endif;
        endif;
        $result['productList'] = $productList;
        $result['transDate'] = $transDate;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$result]);
	}

    public function getProductProcess(){
        $data = $this->input->post();
        $resultData = $this->jobcard->getProductProcess($data);
        $result['processData'] = $resultData['processData'];
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$result]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['party_id'] == "")
			$errorMessage['party_id'] = "Customer is required.";
		if(empty($data['product_id']))
			$errorMessage['product_id'] = "Product is required.";
		if(empty($data['qty']) || $data['qty'] == "0.000")
			$errorMessage['qty'] = "Quantity is required.";
		if(empty($data['process']))
			$errorMessage['process'] = "Product Process is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobcard->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $jobCardData = $this->jobcard->getJobcard($id);
        $this->data['jobCardData'] = $jobCardData;
        $this->data['customerData'] = $this->jobcard->getCustomerList();

        $orderData = $this->jobcard->getCustomerSalesOrder($jobCardData->party_id);
        $orderLost = array();
        foreach($orderData as $row):
            $orderLost[] = ['id'=>$row->id,'order_no'=>getPrefixNumber($row->trans_prefix,$row->trans_no)];
        endforeach;
        $this->data['salesOrderList'] = $orderLost;

        $productPostData = ['sales_order_id'=>$jobCardData->sales_order_id,'product_id'=>$jobCardData->product_id];
        $resultData = $this->jobcard->getProductList($productPostData);
        $productData = $resultData['productData'];
        $transDate = $resultData['trans_date'];
		
        $productList = array();
        if(empty($jobCardData->sales_order_id)):
            if(!empty($productData)):
                foreach($productData as $row):
                    $productList[] = [
                        'id' => $row->id,
                        'delivery_date' => date("Y-m-d"),
                        'order_type' => 0,
                        'item_code' => $row->item_code
                    ];
                endforeach;
            endif;
        else:
            if(!empty($productData)):
                foreach($productData as $row):
                    $jobType = ($row->order_type == 1)?0:1;

                    $productList[] = [
                        'id' => $row->item_id,
                        'delivery_date' => ((!empty($row->cod_date))?$row->cod_date:date("Y-m-d")),
                        'order_type' => $jobType,
                        'item_code' => $row->item_code.'(Ord. Qty. : '.$row->qty.')'
                    ];
                endforeach;
            endif;
        endif;
        $this->data['productList'] = $productList;
        $this->data['transDate'] = $transDate;

        $processData = $this->jobcard->getProductProcess(['product_id'=>$jobCardData->product_id]);
        $this->data['processData'] = $processData['processData'];

        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function view(){
        $id = $this->input->post('id');
        $jobCardData = $this->jobcard->getJobcard($id);
        $jobCardData->party_code = (!empty($jobCardData->party_id))?$this->party->getParty($jobCardData->party_id)->party_code:"Self Stock";
        
        $itmData = $this->item->getItem($jobCardData->product_id);
        $jobCardData->product_name = $itmData->item_name;
        $jobCardData->product_code = $itmData->item_code;
        $jobCardData->unit_name = (!empty($itmData->unit_name)) ? $itmData->unit_name : '';
        $process = explode(",","0,".$jobCardData->process);
        $jobCardData->first_process_id = $process[1];
        $dataRows = array(); $totalCompleteQty=0;  $totalRejectQty=0;   $stages = array();$stg = array();$s=0;$runningStages = Array();$prevProcessId = 0;
        foreach($process as $key=>$value):
            $row = new stdClass;
            $jobProcessData = $this->production->getProcessWiseProduction($id,$value);

            $row->process_name = (!empty($value))?$this->process->getProcess($value)->process_name:"Initial Stage";
            $row->process_id = $value;
            $row->job_id = $id;
            $row->in_qty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
            if(!empty($value)):
                $prevProcessData = $this->production->getProcessWiseProduction($id,$prevProcessId);
                $row->inward_qty = (!empty($prevProcessData))?$prevProcessData->out_qty:0;
            else:
                $row->inward_qty = (!empty($jobProcessData))?$jobProcessData->in_qty:0;
            endif;
            $prevProcessId = $value;
            $row->rework_qty = (!empty($jobProcessData))?$jobProcessData->total_rework_qty:0;
            $row->rejection_qty = (!empty($jobProcessData))?$jobProcessData->total_rejection_qty:0;
            $row->out_qty = (!empty($jobProcessData))?$jobProcessData->out_qty:0;
            $row->vendor = (!empty($jobProcessData))?$jobProcessData->vendor:'';

            $completeQty = (!empty($jobProcessData))?($jobProcessData->total_rework_qty + $jobProcessData->total_rejection_qty + $jobProcessData->in_qty):0;

            $row->pending_qty = (!empty($jobProcessData))?($row->inward_qty - $completeQty):0;

            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->inward_qty):"0";

            if($completeQty == 0):
                $row->status = round($processPer,2).'%';
            elseif($row->inward_qty > $completeQty):
                $row->status = round($processPer,2).'%';
            elseif($row->inward_qty == $completeQty):
                $row->status = round($processPer,2).'%';
            else:
                $row->status = round($processPer,2);
            endif;            

            $row->process_approvel_data = $jobProcessData;
            $dataRows[] = $row;
			
            $totalCompleteQty += $completeQty;
            $totalRejectQty += (!empty($jobProcessData))?($jobProcessData->total_rework_qty + $jobProcessData->total_rejection_qty):0;

			if($row->in_qty == 0 and $s > 1):
				$stg[] = ['process_id' => $row->process_id, 'process_name' => $row->process_name, 'sequence' => ($s-1)];
			else:
                if(!empty($row->process_id)):
				    $runningStages[] = $row->process_id;
                endif;
			endif;
			$s++;
        endforeach; 

        $reworkData = $this->production->getReworkData($id);
        $reworkDataRows=array();
        $i=1;$prevProcessId=0;$completeQty=0;$processPer=0;$totalReworkRejectionQty = 0;
        foreach($reworkData as $row):
            $row->process_name = $this->process->getProcess($row->in_process_id)->process_name;
            $row->process_id = $row->in_process_id;
            $row->job_id = $id;
            if($i!=1):
                $prevProcessData = $this->production->getProcessWiseProduction($id,$prevProcessId,1);
                $row->inward_qty = (!empty($prevProcessData))?$prevProcessData->out_qty:0;
            else:
                $row->inward_qty = $row->in_qty;
            endif;
            $prevProcessId = $row->in_process_id;
            $row->rework_qty = $row->total_rework_qty;
            $row->rejection_qty = $row->total_rejection_qty;
            $row->out_qty = $row->out_qty;

            $completeQty = $row->total_rework_qty + $row->total_rejection_qty + $row->in_qty;

            $row->pending_qty = $row->inward_qty - $completeQty;

            $processPer = ($completeQty > 0)?($completeQty * 100 / $row->inward_qty):"0";

            if($completeQty == 0):
                $row->status = round($processPer,2).'%';
            elseif($row->inward_qty > $completeQty):
                $row->status = round($processPer,2).'%';
            elseif($row->inward_qty == $completeQty):
                $row->status = round($processPer,2).'%';
            else:
                $row->status = round($processPer,2);
            endif;            

            $row->process_approvel_data = $row;
            $reworkDataRows[] = $row;
            $totalReworkRejectionQty += $row->total_rework_qty + $row->total_rejection_qty;
            $i++;
        endforeach;

        $jobCardData->tblId = "jobStages";
        $jobProcessPer = (!empty($totalCompleteQty))?($totalCompleteQty * 100 / (($jobCardData->qty * count($process)) - ($totalRejectQty + $totalReworkRejectionQty)) ):"0";
		$jobCardData->jobPer = round($jobProcessPer,2);
        $jobCardData->job_order_status = $jobCardData->order_status;
        if($jobCardData->order_status == 0):
            $jobCardData->order_status = 'Pending - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 1):
            $jobCardData->order_status = 'Start - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 2):
			$jobCardData->tblId = "jobStages2";
            $jobCardData->order_status = 'In-Process - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 3):
            $jobCardData->order_status = 'On-Hold - '.round($jobProcessPer,2).'%';
        elseif($jobCardData->order_status == 4):
			$jobCardData->tblId = "jobStages4";
            $jobCardData->order_status = 'Complete - '.round($jobProcessPer,2).'%';
        else:
			$jobCardData->tblId = "jobStages5";
            $jobCardData->order_status = 'Closed';
        endif;
        
        $stages['stages'] = $stg;
		$stages['rnStages'] = $runningStages;
        $jobCardData->processData = $dataRows; 
        $jobCardData->reworkData = $reworkDataRows; 
        $this->data['reqMaterial'] = $this->jobcard->getProcessWiseRequiredMaterial($jobCardData)['resultData'];
        $this->data['dataRow'] = $jobCardData;
        $this->data['stageData'] = $stages;
		$this->data['processDataList'] = $this->process->getProcessList();
        $this->data['jobBom']=$this->jobcard->getJobBomQty($jobCardData->id,$jobCardData->product_id);

        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard->delete($id));
        endif;
    }

    public function materialRequest(){
        $id = $this->input->post('id');
        $this->data['job_id'] = $id;
        $this->data['jobCardData'] = $this->jobcard->getJobcard($id);     
        $this->data['disptachData'] = $this->jobcard->getRequestItemData($id);   
        $this->data['machineData'] = $this->machine->getMachineList();
        $this->data['kitData'] = $this->item->getProductKitData($id);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function saveMaterialRequest(){
        $data = $this->input->post();
        $errorMessage = array();
        $i=1;
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Date is required.";
        if(empty($data['bom_item_id'])):
            $errorMessage['general_error'] = "Items is required.";
        else:
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
            $this->printJson($this->jobcard->saveMaterialRequest($data));
        endif;        
    }

    public function changeJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobcard->changeJobStatus($data));
        endif;
    }
}
?>