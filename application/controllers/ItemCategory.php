<?php
class ItemCategory extends MY_Controller
{
    private $indexPage = "item_category/index";
    private $itemCategoryForm = "item_category/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Item Category";
		$this->data['headData']->controller = "itemCategory";
		$this->data['headData']->pageUrl = "itemCategory";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->itemCategory->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getItemCategoryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addItemCategory(){
        $this->data['itemGroup'] = $this->item->getItemGroup();
        $this->load->view($this->itemCategoryForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['category_name']))
            $errorMessage['category_name'] = "Category is required.";
        if(empty($data['category_name']))
            $errorMessage['category_code'] = "Code is required.";
		if(empty($data['category_type']))
            $errorMessage['category_type'] = "Item Group is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->itemCategory->save($data));
        endif;
    }

    public function edit(){
        $this->data['itemGroup'] = $this->item->getItemGroup();
        $this->data['dataRow'] = $this->itemCategory->getCategory($this->input->post('id'));
        $this->load->view($this->itemCategoryForm,$this->data);
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