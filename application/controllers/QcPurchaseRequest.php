<?php
class QcPurchaseRequest extends MY_Controller
{
    private $indexPage = "qc_purchase/pr_index";
    private $purchaseRequestForm = "qc_purchase/purchase_request";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "QcPurchaseRequest";
		$this->data['headData']->controller = "qcPurchaseRequest";
		$this->data['headData']->pageUrl = "qcPurchaseRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		
		$result = $this->qcPRModel->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = "qcPurchaseRequest";
            $sendData[] = getQCPRData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPurchaseRequest(){
        $this->data['categoryList'] = $this->itemCategory->getCategoryList('6,7');
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->load->view($this->purchaseRequestForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if (empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if(empty($data['size']))
            $errorMessage['size'] = "Size is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->qcPRModel->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['categoryList'] = $this->itemCategory->getCategoryList('6,7');
        $this->data['threadType'] = explode(',', $this->item->getMasterOptions()->thread_types);
        $this->data['dataRow'] = $this->qcPRModel->getQCPR($id);
        $this->load->view($this->purchaseRequestForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->qcPRModel->delete($id));
        endif;
    }
}
?>