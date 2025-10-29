<?php
class Designation extends MY_Controller
{
    private $indexPage = "hr/designation/index";
    private $designationForm = "hr/designation/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Designation";
		$this->data['headData']->controller = "hr/designation";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('designation');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->designation->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):
			$row->sr_no = $i++;       
			$sendData[] = getDesignationData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addDesignation(){
        // $this->data['deptData'] = $this->designation->getDepartments($this->input->post());
        $this->load->view($this->designationForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['title']))
            $errorMessage['title'] = "Designation name is required.";
        /*if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department name is required.";*/

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->designation->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->designation->getDesignation($id);
        // $this->data['deptData'] = $this->designation->getDepartments($this->input->post());
        $this->load->view($this->designationForm,$this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->designation->delete($id));
        endif;
    }

}
?>