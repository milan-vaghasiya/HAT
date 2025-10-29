<?php
class CustomInvoice extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 01:00 PM
     update by : 
     note :
*/  
    public function customInvoiceList(){

        $total_rows = $this->salesInvoice->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/customInvoice/customInvoiceList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['salesInvoiceList'] = $this->salesInvoice->getSalesInvoiceList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

   
}
?>