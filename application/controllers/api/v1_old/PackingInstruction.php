<?php
class PackingInstruction extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 01:00 PM
     update by : 
     note :
*/  
    public function packingInstructionList(){
        $status = ($this->input->post('status'))?$this->input->post('status'):0;

        $total_rows = $this->packingInstruction->getCount($status);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/packingInstruction/packingInstructionList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['packingInstructionList'] = $this->packingInstruction->getPackingInstructionList_api($config["per_page"], $page, $status);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addPacking(){
        $this->data['itemData'] = $this->packingInstruction->getItemList();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->packingInstruction->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['itemData'] = $this->packingInstruction->getItemList();
        $this->data['dataRow'] = $this->packingInstruction->getPackingData($id);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }
   
}
?>