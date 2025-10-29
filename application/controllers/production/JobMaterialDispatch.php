<?php 
class JobMaterialDispatch extends MY_Controller{
    private $indexPage = "production/job_material_dispatch/index";
    private $dispatchForm = "production/job_material_dispatch/form";
    private $job_material_issue = "production/job_material_dispatch/job_material_issue";
    private $requestForm = "production/job_material_dispatch/purchase_request";
    private $toolConsumption = "production/job_material_dispatch/tool_consumption";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Jobcard Material Dispatch";
		$this->data['headData']->controller = "production/jobMaterialDispatch";
		$this->data['headData']->pageUrl = "production/jobMaterialDispatch";
	}
	
	public function index($status = 0){
        $header = ($status == 2)?"allocatedMaterial" : "jobMaterialDispatch";
        $this->data['status'] = $status;
        $this->data['tableHeader'] = getStoreDtHeader($header);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobMaterial->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++; 
            $row->tab_status = $status;
            $sendData[] = getJobMaterialIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
	public function addDispatch(){
        $this->data['jobCardData'] = $this->jobcard->getJobcardList([0,2]);
        $this->load->view($this->dispatchForm,$this->data);
    }
    
	public function dispatch(){
        $id = $this->input->post('id');
        $dispatchData = $this->jobMaterial->getJobMaterial($id);
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $locationData = $this->store->getItemStockBatchWise(['item_id'=>$dispatchData->ref_item_id,'stock_required'=>1,'group_by'=>'location_id']);
        $storeGroupedArray = array();
        foreach($locationData as $store){
            $storeGroupedArray[$store->store_name][] = $store;
        }
        $this->data['locationData'] = $storeGroupedArray;
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->data['batchTrans'] = $this->issueBatchHtml(['job_bom_id'=>$id,'job_card_id'=>$dispatchData->job_card_id]);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();

        $dispatchData->job_bom_id = $dispatchData->id;
        unset($dispatchData->id);
        $this->data['dataRow'] = $dispatchData; 
        $this->load->view($this->dispatchForm,$this->data);
    }

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->store->getItemStockBatchWise(['item_id'=>$item_id,'location_id'=>$location_id,'group_by'=>'item_id,location_id,batch_no,tc_no']);
        
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
                $tc_no = (!empty($row->tc_no)) ? '-'.$row->tc_no : '';
                $tc_no_lbl = (!empty($row->tc_no)) ? '-'.$row->tc_no : 'TC NOT FOUND..!';
				$options .= '<option value="'.$row->batch_no.'" data-tc_no="'.$row->tc_no.'" data-stock="'.$row->qty.'">'.$tc_no_lbl.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
	public function getLocation(){
        $item_id = $this->input->post('item_id');
        $locationData = $this->store->getItemStockBatchWise(['item_id'=>$item_id,'stock_required'=>1]);
        $storeGroupedArray = array();
        foreach($locationData as $store){
            $storeGroupedArray[$store->store_name][] = $store;
        }
        $options = '<option value="">Select Location</option>';
        if(!empty($storeGroupedArray)):
            foreach($storeGroupedArray as $key=>$option): 
                $options .=  '<optgroup label="'.$key.'">';
                    foreach($option as $val):
                        $options .= '<option value="'.$val->location_id.'" data-store_name="'.$val->store_name.'">'.$val->location.'</option>';
                    endforeach; 
                $options .=  '</optgroup>';
            endforeach; 
        endif;
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
	public function getJobBomItems(){
        $job_card_id = $this->input->post('job_card_id');
        $bomData = $this->jobcard->getJobBomData($job_card_id);

        $options = '<option value="">Select Item</option>';
        if(!empty($bomData)){
            foreach($bomData as $row):
				$options .= '<option value="'.$row->ref_item_id.'" data-job_bom_id="'.$row->id.'" data-req_qty="'.$row->req_qty.'" data-issue_qty="'.$row->issue_qty.'"> '.(!empty($row->item_code)?'['.$row->item_code.']':'').$row->item_name.'</option>';
            endforeach;
        }
        
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
      
        if ($data['job_card_id'] == ""){
            $errorMessage['job_card_id'] = "Jobcard is required.";
        }   
        if (empty($data['location_id'])){
            $errorMessage['location_id'] = " Location is required";
        }
        if (empty($data['batch_no'])){
            $errorMessage['batch_no'] = "Heat No required.";
        }
        if (empty($data['tc_no'])){
            $errorMessage['batch_no'] = "TC No required.";
        }
        if (empty($data['qty'])){
            $errorMessage['qty'] = " Issue Qty is required";
        }else{
           /* $tcCount=0;$tc_no="";
            $prevIssueData = $this->jobMaterial->getIssueBatchTrans($data['job_bom_id'],$data['job_card_id']);
            if(!empty($prevIssueData)){
                $tc_no=$prevIssueData[0]->tc_no;
                if($prevIssueData[0]->tc_no != $data['tc_no']){
                    $errorMessage['batch_no'] = "Multiple TC no allowded.";
                }   
            } */
            
            $stockData = $this->store->getItemStockBatchWise(['location_id'=>$data['location_id'],'batch_no'=>$data['batch_no'],'tc_no'=>$data['tc_no'],'item_id'=>$data['dispatch_item_id'],'single_row'=>1]);
            if($data['qty'] > $stockData->qty){
                $errorMessage['qty'] = " Stock not available.";
            }
            
        }
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            unset($data['error_msg']);
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->jobMaterial->save($data);
            $result['tbodyData'] = $this->issueBatchHtml(['job_bom_id'=>$data['job_bom_id'],'job_card_id'=>$data['job_card_id']]);
            $this->printJson($result);
        endif;
        
    }
    
	public function delete(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->jobMaterial->delete($data['id']);
            $result['tbodyData'] = $this->issueBatchHtml(['job_bom_id'=>$data['job_bom_id'],'job_card_id'=>$data['job_card_id']]);
            $this->printJson($result);
        endif;
    }
    
	public function issueBatchHtml($postData){
        $batchTrans = $this->jobMaterial->getIssueBatchTrans($postData['job_bom_id'],$postData['job_card_id']);
        $tbodyData = '';$i=1;
        if(!empty($batchTrans)){
            foreach($batchTrans as $row){
                $delBtn = '';
                if($row->qty <= $row->pending_receive ){
                    $delBtn ='<button type="button" onclick="trashMaterial('.$row->id.','.$row->trans_ref_id.','.$row->ref_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>';
                }
                $tbodyData .='<tr>
                                <td>'.$i.'</td>
                                <td>'.formatDate($row->ref_date).'</td>
                                <td>['.$row->location.'] '.$row->store_name.'</td>
                                <td>'.$row->batch_no.'</td>
                                <td>'.$row->tc_no.'</td>
                                <td>'.abs($row->qty).'</td>
                                <td>'.$delBtn.'</td>
                            </tr>';
            }
        }else{
            $tbodyData= '<tr>
                            <td colspan="7"> No Data available. </td>
                        </tr>';
        }
        return $tbodyData;

    }
}
?>