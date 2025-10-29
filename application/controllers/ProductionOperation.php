<?php
class ProductionOperation extends MY_Controller
{
    private $indexPage = "production_operation/index";
    private $operationForm = "production_operation/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Production Operation";
		$this->data['headData']->controller = "productionOperation";
		$this->data['headData']->pageUrl = "productionOperation";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->operation->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProductionOperationData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProductionOperation(){
        $this->load->view($this->operationForm);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['operation_name']))
            $errorMessage['operation_name'] = "Operation Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->operation->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->operation->getOperation($this->input->post('id'));
        $this->load->view($this->operationForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->operation->delete($id));
        endif;
    }
    
}
?>