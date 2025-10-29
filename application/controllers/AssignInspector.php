<?php
class AssignInspector extends MY_Controller{
    private $indexPage = "assign_inspector/index";
    private $assignInspectorForm = "assign_inspector/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Assign Inspector";
		$this->data['headData']->controller = "assignInspector";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->assignInspector->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->machine_no = (!empty($row->machine_code) || !empty($row->machine_name))?"[ ".$row->machine_code." ] ".$row->machine_name:"";
            $row->assign_status = "";
            if($row->status == 0):
				$row->assign_status = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->status == 1):
				$row->assign_status = '<span class="badge badge-pill badge-warning m-1">In Process</span>';
            elseif($row->status == 2):
                $row->assign_status = '<span class="badge badge-pill badge-info m-1">Finish By Setter</span>';
            elseif($row->status == 3):
                $row->assign_status = '<span class="badge badge-pill badge-success m-1">Approved</span>';
            elseif($row->status == 4):
                $row->assign_status = '<span class="badge badge-pill badge-primary m-1">Resetup</span>';
			elseif($row->status == 5):
				$row->assign_status = '<span class="badge badge-pill badge-dark m-1">On Hold</span>';
            elseif($row->status == 6):
                $row->assign_status = '<span class="badge badge-pill badge-info m-1">Accept By Inspector</span>';
			endif;
            $sendData[] = getAssignInspectorData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function assignInspector(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->assignInspector->getRequestData($id);
        $this->data['employeeData'] = $this->employee->getSetterInspectorList();
        $this->load->view($this->assignInspectorForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['assign_date']))
            $errorMessage['assign_date'] = "Assign Date is required.";
        if(empty($data['qci_id']))
            $errorMessage['qci_id'] = "Inspector Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:   
            $data['assign_by'] = $this->session->userdata('loginId');
            $this->printJson($this->assignInspector->save($data));
        endif;
    }
}
?>