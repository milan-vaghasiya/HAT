<?php 
class JobMaterialDispatch extends MY_Controller{
    private $indexPage = "job_material_dispatch/index";
    private $dispatchForm = "job_material_dispatch/form";
    private $requestForm = "job_material_dispatch/purchase_request";
    private $toolConsumption = "job_material_dispatch/tool_consumption";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobcard Material Dispatch";
		$this->data['headData']->controller = "jobMaterialDispatch";
		$this->data['headData']->pageUrl = "jobMaterialDispatch";
	}
	
	public function index(){
//	    echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>This module is under Maintenance</h1><hr>';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobMaterial->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->order_status_label='';
			if(($row->req_qty - $row->dispatch_qty) > 1):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif(($row->req_qty - $row->dispatch_qty) <= 1):
				$row->order_status_label = '<span class="badge badge-pill badge-success m-1">Completed</span>';
            endif;
            if($row->req_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
            endif;
            $row->req_item_stock = (!empty($row->req_item_id))?$this->item->getItem($row->req_item_id)->qty:"";
            $row->dispatch_item_stock = (!empty($row->dispatch_item_id))?$this->item->getItem($row->dispatch_item_id)->qty:"";
            $row->req_item_name = (!empty($row->req_item_id))?$this->item->getItem($row->req_item_id)->item_name:"";
            $row->dispatch_item_name = (!empty($row->dispatch_item_id))?$this->item->getItem($row->dispatch_item_id)->item_name:"";
            $row->req_unit_name = (!empty($row->req_item_id))?$this->item->itemUnit($this->item->getItem($row->req_item_id)->unit_id)->unit_name:"";
            $row->dis_unit_name = (!empty($row->dispatch_item_id))?$this->item->itemUnit($this->item->getItem($row->dispatch_item_id)->unit_id)->unit_name:"";
            $sendData[] = getJobMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDispatch(){
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->load->view($this->dispatchForm,$this->data);
    }
    
    /** Updated By Mansee @ 19-02-22 */

    public function dispatch(){
        $id = $this->input->post('id');
        $dispatchData = $this->jobMaterial->getJobMaterial($id);
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->data['batchTrans'] = $this->jobMaterial->getIssueBatchTrans($id);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['dataRow'] = $dispatchData; 
        if(!empty($dispatchData->location_id))     
        {
            $this->data['batchData']=$this->item->locationWiseBatchStock($dispatchData->item_id,$dispatchData->location_id);
        }  
        $this->load->view($this->dispatchForm,$this->data);
    }

    public function getItemOptions(){
        $type = $this->input->post('material_type');
        $itemData = $this->item->getItemList(0);
        $options = '<option value="">Select Item Name</option>';
        foreach($itemData as $row):
			if($row->item_type == $type):             
				$options .= '<option value="'.$row->id.'">'.$row->item_name.'</option>';     
			endif;
        endforeach;
        $this->printJson(['status'=>1,'item_options'=>$options]);
    }

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if($data['dispatch_qty'] != "0.000" && empty($data['dispatch_qty']))
            $errorMessage['dispatch_qty'] = "Qty is required.";
        if(empty($data['batch_no'][0]))
            $errorMessage['general_batch_no'] = "Location and Batch No. is required.";
        
        if(!empty($data['ref_id']) && $data['ref_id'] != -1 && !empty($data['batch_no'])):
            $jobBom = $this->jobMaterial->getJobBomQty($data['ref_id'],$data['item_id']);
            $qtyError  = array();
            foreach($data['batch_no'] as $key=>$value):
                $checkQty = $data['batch_qty'][$key] / $jobBom->qty;
                //print_r($checkQty.'***');
                if(strpos($checkQty,'.') !== false):
                    $qtyError[] = $value;
                endif;
            endforeach;
            if(!empty($qtyError)):
                $errorMessage['general_batch_no'] = "Invalid qty. for this batch No. : ".implode(",",$qtyError);
            endif;
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['dispatch_by'] = $this->session->userdata('loginId');
            if(empty($data['id']))
                $data['created_by'] = $this->session->userdata('loginId');
            
            $this->printJson($this->jobMaterial->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobMaterial->delete($id));
        endif;
    }

    public function purchaseRequest()
	{
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['dispatch_id']))
            $errorMessage['dispatch_id'] = "Matirial Dispatch id is required.";
        
        $dispatchData = $this->jobMaterial->getJobMaterial($data['dispatch_id']);
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->jobMaterial->savePurchaseRequest($dispatchData));
        endif;
    }

    public function addPurchaseRequest(){
        $this->data['itemData'] = $this->item->getItemLists('2,3');
        $this->data['nextReqNo'] = $this->jobMaretialRequest->nextReqNo();
        $this->load->view($this->requestForm,$this->data);
    }

    //Change By Avruti @16/08/2022
    public function savePurchaseRequest(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = [
				'id' => $data['id'],
				'req_item_id' => $data['req_item_id'],
				'req_qty' => $data['req_qty'],
				'req_item_name' => $data['req_item_name']
			];
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaterial->savePurchaseRequest($data,$itemData));
        endif;
    }

    public function consumption(){
        $data = $this->input->post();
        $this->data['toolData'] = $this->jobMaterial->getToolConsumption($data['product_id']);
        $this->data['tblData'] = '';
        if(!empty($this->data['toolData'])):
            $i=1;$expCon=0;$expCost=0;
            foreach($this->data['toolData'] as $row):
                $jobData = $this->jobcard_v2->getJobCard($data['job_card_id']);
                $expCon = floatVal($jobData->qty) / floatVal($row->tool_life);
                $expCost = floatval($expCon) * floatVal($row->price);
                $this->data['tblData'] .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>
                                '.$row->item_name.'
                            </td>
                            <td>
                                '.$expCon.'
                            </td>
                            <td>
                                '.$expCost.'
                            </td>
                            <td></td>
                            <td></td>
                        </tr>';
            endforeach;
        endif;
        $this->load->view($this->toolConsumption,$this->data);
    }
    
    //created by Meghavi 01-12-2021
    public function closeMaterialRequest(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobMaterial->closeMaterialRequest($data));
        endif;
    }

}
?>