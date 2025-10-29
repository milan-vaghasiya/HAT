<?php
class ItemCategory extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
    
/* Create By : Avruti @30-11-2021 10:10 AM
     update by : 
     note :
*/

    public function ItemCategoryList(){
        $total_rows = $this->itemCategory->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/itemCategory/itemCategoryList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['itemCategoryList'] = $this->itemCategory->getItemCategoryList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addItemCategory(){
        $this->data['itemGroup'] = $this->item->getItemGroup();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['category_name']))
            $errorMessage['category_name'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->itemCategory->save($data));
        endif;
    }

    public function view(){
        $this->data['itemGroup'] = $this->item->getItemGroup();
        $this->data['dataRow'] = $this->itemCategory->getCategory($this->input->post('id'));
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemCategory->delete($id));
        endif;
    }
}
?>