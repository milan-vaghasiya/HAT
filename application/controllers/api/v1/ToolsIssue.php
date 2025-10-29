<?php
class ToolsIssue extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search];
        $this->data['toolsIssueList'] = $this->toolsIssue->getToolsIssueListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function jobCardNoList(){
        $result = $this->jobcard->getJobcardList();
        $dataRow = array();
        $dataRow[] = [
            'id' => -1,
            'job_no' => 'General Issue'
        ];

        foreach($result as $row):
            $dataRow[] = [
                'id' => $row->id,
                'job_no' => "[".$row->item_code."] ".getPrefixNumber($row->job_prefix,$row->job_no)
            ];
        endforeach;
        $this->data['jobCardNoList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function departmentList(){
        $this->data['departmentList'] = $this->department->getMachiningDepartment(8);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function processListOnDepartment($id){
        $processData = $this->process->getDepartmentWiseProcess($id);
        $dataRow = array();
        foreach ($processData as $row):
            $dataRow[] = [
                'id' => $row->id,
                'process_name' => $row->process_name
            ];
        endforeach;
        $this->data['processList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function batchNoList(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $dataRow = array();
        foreach($batchData as $row):
			if($row->qty > 0):
                $dataRow[] = [
                    'batch_no' => $row->batch_no,
                    'stock_qty' => $row->qty,
                ];
			endif;
        endforeach;
        $this->data['batchNoList'] = $dataRow;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!isset($data['item_data']) || empty($data['item_data']))
            $errorMessage['general_error'] = "Item is required.";        

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:  
            $data['created_by'] = $data['dispatch_by'] = $this->loginId;            
            $this->printJson($this->toolsIssue->save($data));
        endif;
    }

    public function edit($id){
        $this->data['issueData'] = $this->toolsIssue->getToolsIssue($id);
        $this->data['batchTrans'] = $this->toolsIssue->getIssueBatchTrans($id);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function delete($id){
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->toolsIssue->delete($id));
        endif;
    }
}
?>