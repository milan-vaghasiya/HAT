<?php
class SkillMaster extends MY_Controller{
    private $indexPage = "hr/skill_master/index";
    private $skillMasterForm = "hr/skill_master/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Skill Master";
		$this->data['headData']->controller = "hr/skillMaster";
		$this->data['headData']->pageUrl = "hr/skillMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('skillMaster');   
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->skillMaster->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSkillMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addskillMaster(){
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->load->view($this->skillMasterForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";
        if(empty($data['designation_id']))
            $errorMessage['designation_id'] = "Designation is required.";
        if(empty($data['skill']))
            $errorMessage['skill'] = "Skill is required.";
        if(empty($data['req_per']))
            $errorMessage['req_per'] = "Req. Per(%) is required.";
       if($data['req_per'] <= 0 || $data['req_per'] > 100)
            $errorMessage['req_per'] = "Req. Per(%) Invalid.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->skillMaster->save($data));
        endif;
    }

    public function edit(){        
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['dataRow'] = $this->skillMaster->getSkillMaster($this->input->post('id'));
        $this->load->view($this->skillMasterForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->skillMaster->delete($id));
        endif;
    }
    
}
?>