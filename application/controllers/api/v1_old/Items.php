<?php
class Items extends MY_Apicontroller{

    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);


    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @30-11-2021 10:10 AM
     update by : 
     note :
*/

    public function RawMaterialList(){
        $total_rows = $this->item->getCount(3);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/item/rawMaterialList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['rawMaterialList'] = $this->item->getItemList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function CapitalGoodsList(){
        $total_rows = $this->item->getCount(4);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/item/capitalGoodsList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['CapitalGoodsList'] = $this->item->getItemList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function ConsumableList(){
        $total_rows = $this->item->getCount(2);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/item/consumableList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['consumableList'] = $this->item->getItemList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addItem($item_type){
        
        $this->data['item_type'] = $item_type;
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['categoryList'] = $this->item->getCategoryList($item_type);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
		
		if($data['item_type'] == 3){
			if(empty($data['itmsize']) AND empty($data['itmshape']) AND empty($data['itmbartype']) AND empty($data['itmmaterialtype']))
				$errorMessage['item_name'] = "Item Name is required.";
		}else{
			if(empty($data['item_name']))
				$errorMessage['item_name'] = "Item Name is required.";
		}
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			if($data['item_type'] == 3):
				$data['item_name'] = $data['itmsize'].' ';
				$data['item_name'] .= $data['itmshape'].' ';
				$data['item_name'] .= $data['itmbartype'].' ';
				$data['item_name'] .= $data['itmmaterialtype'];
				$data['item_image'] =  $data['itmsize'] . '~@' . $data['itmshape'] . '~@' . $data['itmbartype'] . '~@' . $data['itmmaterialtype'];
				unset($data['itmsize'],$data['itmshape'],$data['itmbartype'],$data['itmmaterialtype']);
			else:
				
			endif;
            $data['created_by'] = $this->loginId;
            
            $this->printJson($this->item->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['categoryList'] = $this->item->getCategoryList($this->data['dataRow']->item_type);
        $this->data['familyGroup'] = $this->item->getfamilyGroupList();

        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

}
?>