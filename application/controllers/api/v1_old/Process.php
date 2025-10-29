<?php
class Process extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

    public function ProcessList(){
        $total_rows = $this->process->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/process/processList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['processList'] = $this->process->getProcessList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }
}
?>