<?php
class StockJournal extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @30-11-2021 12:10 AM
     update by : 
     note :
*/
    public function StockJournalList(){
        $total_rows = $this->stockJournal->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/stockJournal/stockJournalList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['stockJournalList'] = $this->stockJournal->getStockJournalList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addStockJournal(){
        $this->data['rmData'] = $this->item->getItemList(3);
        $this->data['fgData'] = $this->item->getItemList(1);
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		 
		if(empty($data['date']))
            $errorMessage['date'] = "Date  is required.";
        if(empty($data['rm_item_id']))
			$errorMessage['rm_item_id'] = "Raw Material  is required.";
        if(empty($data['rm_qty']))
			$errorMessage['rm_qty'] = "RM Qty.";
        if(empty($data['rm_location_id']))
			$errorMessage['rm_location_id'] = "RM Location is required.";
        if(empty($data['fg_item_id']))
			$errorMessage['fg_item_id'] = "Finish Goods is required.";
        if(empty($data['fg_qty']))
			$errorMessage['fg_qty'] = "FG Qty.";
        if(empty($data['fg_location_id']))
			$errorMessage['fg_location_id'] = "FG Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['date'] = formatDate('Y-m-d', $data['date']);
            $data['created_by'] = $this->loginId;
            $this->printJson($this->stockJournal->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->stockJournal->delete($id));
        endif;
    }
}
?>