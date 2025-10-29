<?php
class Templates extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function templatesTypeList(){
        $this->data['typeList'] = [
            ['id' => 1,'name' => 'Rejection'],
            ['id' => 2,'name' => 'Idle Reason'],
            ['id' => 3,'name' => 'Item Feasibility'],
            ['id' => 4,'name' => 'Rework'],
            ['id' => 5,'name' => 'Customer Feedback']
        ];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $type = (isset($_REQUEST['type']) && !empty($_REQUEST['type']))?$_REQUEST['type']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'type'=>$type];
        $this->data['templateList'] = $this->comment->getTemplateListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['remark']))
             $errorMessage['remark'] = "Reason is required.";

        if($data['type'] == 2): 
            if(empty($data['code']))
                $errorMessage['code'] = "Code is required.";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $result = $this->comment->save($data);
            $this->printJson($result);
        endif;
    }

    public function edit($id){
        $this->data['templateData'] = $this->comment->getComment($id);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function delete($id){
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->comment->delete($id));
        endif;
    }
}
?>