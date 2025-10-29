<?php
class Process extends MY_Controller
{
    private $indexPage = "process/index";
    private $processForm = "process/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Process";
		$this->data['headData']->controller = "process";
		$this->data['headData']->pageUrl = "process";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->process->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getProcessData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProcess(){
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->processForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['process_name']))
            $errorMessage['process_name'] = "Process name is required.";
        if(empty($data['dept_id']))
            $errorMessage['dept_id'] = "Department is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->process->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->process->getProcess($this->input->post('id'));
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->processForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->process->delete($id));
        endif;
    }

    public function processMigration()
    {
        $this->printJson($this->process->processMigration());
    }
    
}
?>