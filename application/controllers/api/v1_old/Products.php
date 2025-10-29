<?php
class Products extends MY_Apicontroller{

    private $automotiveArray = ["1"=>'Yes',"2"=>"No"];
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @29-11-2021 01:00 PM
     update by : 
     note :
*/  
    public function finidhGoodsList(){

        $total_rows = $this->item->getCount(1);

        $config = array();
        $config["base_url"] = base_url() . "api/v1/products/finidhGoodsList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['itemList'] = $this->item->getItemList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addProduct(){
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
		$this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        $this->data['categoryList'] = $this->item->getCategoryList(1);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_name']))
            $errorMessage['item_name'] = "Part Name is required.";
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['material_grade']))
        {
            if(!empty($data['gradeName']))
                $data['material_grade'] = $this->masterOption->saveGradeName($data['gradeName']);
        }
        unset($data['gradeName']);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:

            if($_FILES['drawing_file']['name'] != null || !empty($_FILES['drawing_file']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['drawing_file']['name'];
				$_FILES['userfile']['type']     = $_FILES['drawing_file']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['drawing_file']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['drawing_file']['error'];
				$_FILES['userfile']['size']     = $_FILES['drawing_file']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/items/drawings');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['drawing_file'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['drawing_file'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['drawing_file']);
			endif;
			
			if($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['item_image']['name'];
				$_FILES['userfile']['type']     = $_FILES['item_image']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['item_image']['error'];
				$_FILES['userfile']['size']     = $_FILES['item_image']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/items/');
				$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['item_image'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['item_image']);
			endif;

            //unset($data['processSelect']);
            $data['created_by'] = $this->loginId;
            $this->printJson($this->item->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['categoryList'] = $this->item->getCategoryList(1);
		$this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
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