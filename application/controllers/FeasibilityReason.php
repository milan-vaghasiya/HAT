<?php
class FeasibilityReason extends MY_Controller
{
    private $indexPage = "feasibility_reason/index";
    private $freasonForm = "feasibility_reason/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Rejected Reason";
		$this->data['headData']->controller = "feasibilityReason";
		$this->data['headData']->pageUrl = "feasibilityReason";
	}
	
	public function index(){
        $this->data['tableHeader'] = getsalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->feasibilityReason->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getFeasibilityReasonData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFeasibilityReason(){
        
        $this->load->view($this->freasonForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['type']))
            $errorMessage['type'] = "Type is required.";
        if(empty($data['remark']))
            $errorMessage['remark'] = "Remark is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->feasibilityReason->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->feasibilityReason->getFeasibility($this->input->post('id'));
        $this->load->view($this->freasonForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->feasibilityReason->delete($id));
        endif;
    }
    
}
?>