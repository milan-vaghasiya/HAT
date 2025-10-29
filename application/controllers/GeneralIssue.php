<?php 
class GeneralIssue extends MY_Controller{
    private $indexPage = "general_issue/index";
    private $form = "general_issue/form";
    private $viewPage = "general_issue/trans_view";
    private $pendingRequestPage = "general_issue/pending_request";
    private $material_return_form = "general_issue/material_return_form";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "General Issue";
		$this->data['headData']->controller = "generalIssue";
		$this->data['headData']->pageUrl = "generalIssue";
	}
	
    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->generalIssue->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->total_item=0;
           
            $sendData[] = getGeneralIssueData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function issueMaterial()
    {
        $this->data['jobCardData'] = $this->jobcard->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->load->view($this->form,$this->data);
    }

    public function getItemLocationList()
    {
        $data=$this->input->post();
        $locationData = $this->item->itemWiseStock($data['item_id']);
        $options = '<option value="">Select Location</option>';
        foreach ($locationData as $row) :
            $options.='<option value="' . $row->id . '" data-store_name="' . $row->store_name . '" >' . $row->location . ' </option>';
        endforeach;
           
        $this->printJson(['status'=>1,'options'=>$options]);
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

        if(empty($data['item_id'][0]))
            $errorMessage['general_batch_no'] = "Item Name is required.";
       
        if(empty($data['batch_no'][0]))
            $errorMessage['general_batch_no'] = "Location and Batch No. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['dispatch_by'] = $this->loginId;
            $data['created_by'] = $this->loginId;
            $this->printJson($this->generalIssue->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $dispatchData = $this->generalIssue->getIssueData($id);
        
        $dataRow = new stdClass();
        $dataRow->req_prefix = $dispatchData[0]->req_prefix;
        $dataRow->req_no = $dispatchData[0]->req_no;
        $dataRow->dispatch_date = $dispatchData[0]->dispatch_date;
        $dataRow->req_date = $dispatchData[0]->dispatch_date;
        $dataRow->collected_by = $dispatchData[0]->collected_by;
        $dataRow->dept_id = $dispatchData[0]->dept_id;
        $dataRow->remark = $dispatchData[0]->remark;
        $dataRow->trans_data = $dispatchData;

        $this->data['dataRow'] = $dataRow;
        $this->data['jobCardData'] = $this->jobcard->getJobcardList();
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->generalIssue->delete($id));
        endif;
    }

    public function materialReturn(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->generalIssue->getIssueItemData($id);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->material_return_form,$this->data);
    }

    public function saveReturnMaterial(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Location is required.";
        if(empty($data['return_qty']))
            $errorMessage['return_qty'] = "Return Qty is required.";

        if(!empty($data['return_qty'])):
            $issueItemData = $this->generalIssue->getIssueItemData($data['id']);
            $pending_qty = $issueItemData->dispatch_qty - $issueItemData->return_qty;
            if($data['return_qty'] > $pending_qty):
                $errorMessage['return_qty'] = "Invalid Qty.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->generalIssue->saveReturnMaterial($data));
        endif;
    }
}
?>