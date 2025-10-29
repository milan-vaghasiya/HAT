<?php
class Items extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function itemTypeList(){
        $this->data['itemTypeList'] = $this->item->getItemGroup();
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function itemList(){
        $item_type = ($this->input->post('item_type'))?$this->input->post('item_type'):"";
        $this->data['itemList'] = $this->item->getItemLists($item_type);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function unitList(){
        $this->data['unitList'] = $this->item->itemUnits();
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function viewItem($id){
        $result = $this->item->getItem($id);
        $result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
        $this->data['itemData'] = $result;
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }
}
?>