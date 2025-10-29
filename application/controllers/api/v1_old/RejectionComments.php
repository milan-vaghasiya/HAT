
    
<?php
class RejectionComments extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
    
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  

    public function idleReasonList(){
        $type = ($this->input->post('type'))?$this->input->post('type'):2;

        $total_rows = $this->comment->getCount($type);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/rejectionComments/idleReasonList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['idleReasonList'] = $this->comment->getIdleReasonList_api($config["per_page"], $page,$type);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function rejectionCommentsList(){
        $type = ($this->input->post('type'))?$this->input->post('type'):"1,4";

        $total_rows = $this->comment->getCount($type);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/rejectionComments/rejectionCommentsList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['idleReasonList'] = $this->comment->getIdleReasonList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addRejectionComment(){
        $this->printJson(['status'=>1,'message'=>'Record found']);

    }
	
	// public function addIdleReason(){
    //     $this->load->view($this->idleForm);
    // }
	
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
            $this->printJson($this->comment->save($data));
        endif;
    }

    public function view(){
        $this->data['dataRow'] = $this->comment->getComment($this->input->post('id'));
		if($this->data['dataRow']->type == 1): 
            $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

        else:
            $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->comment->delete($id));
        endif;
    }
}
?>