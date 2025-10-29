<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class ProductInspection extends MY_Controller
{
	private $indexPage = "product_inspection/index";
	private $inspectionForm = "product_inspection/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Product Inspection";
		$this->data['headData']->controller = "productInspection";
	}
	
	public function index(){
		$this->data['tableHeader'] = getDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

    public function getDTRows(){        
        $result = $this->productInspection->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;            
            $sendData[] = getProductInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addInspection(){
        $this->data['productData'] = $this->item->getItemList(1);
        $this->load->view($this->inspectionForm,$this->data);
    }

	public function getItemData(){
		$id = $this->input->post('id');
		$itemData = $this->item->getItem($id);
		$this->printJson($itemData);
	}
	
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['item_id']))
			$errorMessage['item_id'] = "Product Name is required.";
		if(empty($data['type']))
			$errorMessage['type'] = "Inspection Type is required.";
		if(empty($data['qty']))
			$errorMessage['qty'] = "Qty. is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->productInspection->save($data));
		endif;
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->productInspection->delete($id));
		endif;
	}
    
}
?>
	