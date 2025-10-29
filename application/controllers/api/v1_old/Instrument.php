

<?php
class Instrument extends MY_Apicontroller{

    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/  
    public function instrumentList(){
        $type = ($this->input->post('type'))?$this->input->post('type'):6;

        $total_rows = $this->instrument->getCount($type);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/instrument/instrumentList";
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

    public function addInstrument(){
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['item_name']))
            $errorMessage['item_name'] = "Insrtument Name is required.";

        if ($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['item_image']['name'];
            $_FILES['userfile']['type']     = $_FILES['item_image']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['item_image']['error'];
            $_FILES['userfile']['size']     = $_FILES['item_image']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/instrument/');
            $config = ['file_name' => time() . "_order_item_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path'    => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['item_image'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $data['item_image'] = $uploadData['file_name'];
            endif;
        else :
            unset($data['item_image']);
        endif;

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['item_type'] = 6;
            $data['created_by'] = $this->loginId;
            $this->printJson($this->instrument->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->item->getCategoryList(6);
        $this->data['gstPercentage'] = $this->gstPercentage;
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