<?php
class Terms extends MY_Apicontroller{

    private $typeArray = ["Purchase","Sales","Vendor"];

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/
    public function termsList(){
        $total_rows = $this->terms->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/terms/termsList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['termsList'] = $this->terms->getTermsList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addTerms(){
        $this->data['typeArray'] = $this->typeArray;
       $this->printJson(['status'=>1,'message'=>'recoreds found','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['title']))
			$errorMessage['title'] = "Title is required.";
        if(empty($data['conditions']))
			$errorMessage['conditions'] = "Conditions is required.";
        if(empty($data['type']))
			$errorMessage['type'] = "Type is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->terms->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->terms->getTerms($id);
        $this->data['typeArray'] = $this->typeArray;
        $this->printJson(['status'=>1,'message'=>'recoreds found','data'=>$this->data]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->terms->delete($id));
        endif;
    }
}
?>