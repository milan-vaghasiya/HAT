

<?php
class MachineActivities extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  
    public function machineActivitiesList(){
        $total_rows = $this->activities->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/machineActivities/machineActivitiesList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['machineActivitiesList'] = $this->activities->getMachineActivitiesList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addMachineActivities(){
        $this->printJson(['status'=>1,'message'=>'Record found']);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['activities']))
            $errorMessage['activities'] = "Activities is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->activities->save($data));
        endif;
    }

    public function view(){
        $this->data['dataRow'] = $this->activities->getActivities($this->input->post('id'));
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->activities->delete($id));
        endif;
    }
}
?>