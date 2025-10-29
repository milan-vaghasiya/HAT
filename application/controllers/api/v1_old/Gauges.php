
<?php
class Gauges extends MY_Apicontroller{

    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  

    public function gaugesList(){
        $type = ($this->input->post('type'))?$this->input->post('type'):7;

        $total_rows = $this->instrument->getCount($type);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/gauges/gaugesList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['gaugesList'] = $this->instrument->getGaugesList_api($config["per_page"], $page,$type);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }
    
    public function addGauge(){
        $this->data['categoryList'] = $this->item->getCategoryList(7);
        $this->data['gstPercentage'] = $this->gstPercentage;

        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['size']))
            $errorMessage['size'] = "Thread Size is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['item_type'] = 7;
            $data['created_by'] = $this->loginId;
			if(containsWord($data['cat_name'], 'thread')){}else{$data['thread_type']=NULL;}unset($data['cat_name']);
            $data['item_name'] = $data['size'];
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList(7);
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['dataRow'] = $this->instrument->getItem($id);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->instrument->delete($id));
        endif;
    }
   
}
?>