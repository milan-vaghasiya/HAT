<?php
class Store extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @30-11-2021 12:10 AM
     update by : 
     note :
*/
    public function StoreLocationList(){
        $total_rows = $this->store->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/store/storeLocationList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['storeLocationList'] = $this->store->getStoreLocationList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function StockLedgerList(){
        $total_rows = $this->item->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/store/stockLedgerList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['stockLedgerList'] = $this->item->getItemList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addStoreLocation(){
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
       
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['store_name']))
            if(empty($data['storename']))
			    $errorMessage['store_name'] = "Store Name is required.";
            else
            $data['store_name'] = $data['storename'];
        unset($data['storename']);
        if(empty($data['location']))
			$errorMessage['location'] = "Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->store->save($data));
        endif;
    }

    public function view(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->store->getStoreLocation($id);
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->store->delete($id));
        endif;
    }

}
?>