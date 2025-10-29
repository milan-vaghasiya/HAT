<?php
class FinalInspection extends MY_Controller{
    private $indexPage = "final_inspection/index";
    private $formPage = "final_inspection/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Final Inspection";
		$this->data['headData']->controller = "finalInspection";
		$this->data['headData']->pageUrl = "finalInspection";
	}

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->data['jobCardList'] = $this->jobcard->jobCardNoList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->finalInspection->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getFinalInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function save(){
        $data = $this->input->post();
        $data['created_by'] = $this->session->userdata('loginId');
        $this->printJson($this->finalInspection->save($data));
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['inspectionData'] = $data;
        $this->data['inspectionTrans'] = $this->finalInspection->getInspectionTrans($data['id']);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->finalInspection->delete($data));
        endif;
    }
}
?>