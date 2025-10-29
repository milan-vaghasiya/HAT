<?php
class Banking extends MY_Controller
{
    private $indexPage = "banking/index";
    private $formPage = "banking/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Banking";
		$this->data['headData']->controller = "banking";
		$this->data['headData']->pageUrl = "banking";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->banking->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getbankingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function addBanking(){
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['bank_name']))
            $errorMessage['bank_name'] = "Bank Name is required.";
		if (empty($data['branch_name']))
            $errorMessage['branch_name'] = "Branch Name is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->banking->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->banking->getBankingDetails($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->banking->delete($id));
        endif;
    }
    
}
?>