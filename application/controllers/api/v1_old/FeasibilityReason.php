<?php
class FeasibilityReason extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 03:00 PM
     update by : 
     note :
*/  
    public function feasibilityReasonList(){
        $total_rows = $this->feasibilityReason->getCount(3);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/feasibilityReason/feasibilityReasonList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['feasibilityReasonList'] = $this->feasibilityReason->getFeasibilityReasonList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addFeasibilityReason(){
        
        $this->printJson(['status'=>1,'message'=>'Record found']);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['type']))
            $errorMessage['type'] = "Type is required.";
        if(empty($data['remark']))
            $errorMessage['remark'] = "Remark is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->feasibilityReason->save($data));
        endif;
    }

    public function view(){
        $this->data['dataRow'] = $this->feasibilityReason->getFeasibility($this->input->post('id'));
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->feasibilityReason->delete($id));
        endif;
    }
   
}
?>