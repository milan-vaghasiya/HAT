<?php 
class ToolsIssue extends MY_Controller{
    private $indexPage = "tools_issue/index";
    private $dispatchForm = "tools_issue/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();        
		$this->data['headData']->pageTitle = "Tools Issue";
		$this->data['headData']->controller = "toolsIssue";
		$this->data['headData']->pageUrl = "toolsIssue";
        $this->checkGrants("toolsIssue");
	}
	
	public function index(){
	    //echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>This module is under Maintenance</h1><hr>';
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->toolsIssue->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;           
            $sendData[] = getToolsIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDispatch(){
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcardList();
        //$this->data['processList'] = $this->process->getProcessList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->load->view($this->dispatchForm,$this->data);
    }

    public function getProcessData(){
        $id = $this->input->post('dept_id');
        $processData = $this->process->getDepartmentWiseProcess($id);
        $option = "<option value=''>Select Process Name</option>";
        foreach ($processData as $row):
            $option .= '<option value="' . $row->id . '" >' . $row->process_name . '</option>';
        endforeach;
        
        $this->printJson(['status'=>1, 'option'=>$option]);
    }

    public function dispatch(){
        $id = $this->input->post('id');
        $dispatchData = $this->toolsIssue->getToolsIssue($id);
        $this->data['jobCardData'] = $this->jobcard_v2->getJobcardList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->data['batchTrans'] = $this->toolsIssue->getIssueBatchTrans($id);
        $this->data['dataRow'] = $dispatchData;        
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        //$this->data['processList'] = $this->process->getDepartmentWiseProcess($dispatchData->dept_id);
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
        $options = '';//'<option value="">Select Batch No.</option>';
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

        if(!isset($data['item_data']) || empty($data['item_data']))
            $errorMessage['general_error'] = "Item is required.";        

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:  
            $data['created_by'] = $data['dispatch_by'] = $this->session->userdata('loginId');            
            $this->printJson($this->toolsIssue->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->toolsIssue->delete($id));
        endif;
    }
}
?>