<?php
class Transport extends MY_Controller
{
    private $indexPage = "transport/index";
    private $formPage = "transport/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Transport";
		$this->data['headData']->controller = "transport";
		$this->data['headData']->pageUrl = "transport";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
		$data['searchCol'][] = "transport_name";
        $data['searchCol'][] = "transport_id";
        $data['searchCol'][] = "address";
		
		$columns =array('','','transport_name','transport_id','address');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
		$result = $this->transport->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getTransportData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTransport(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['transport_name']))
            $errorMessage['transport_name'] = "Transport Name is required.";
		if (empty($data['transport_id']))
            $errorMessage['transport_id'] = "Transport ID is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->transport->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->transport->getTransport($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->transport->delete($id));
        endif;
    }
    
}
?>