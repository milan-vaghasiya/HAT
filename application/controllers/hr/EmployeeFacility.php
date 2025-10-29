<?php
class EmployeeFacility extends MY_Controller
{
    private $indexPage = "hr/employee_facility/index";
    private $FormPage = "hr/employee_facility/form";

	public function __construct()
    {
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employee Facility";
		$this->data['headData']->controller = "hr/employeeFacility";
        $this->data['headData']->pageUrl = "hr/employeeFacility";
	}
	
	public function index()
    {
        $this->data['tableHeader'] = getHrDtHeader('employeeFacility');
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows()
    {
        $result = $this->employeefacility->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getEmployeeFacilityData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployeeFacility()
    {
        $this->load->view($this->FormPage, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['ficility_type']))
			$errorMessage['ficility_type'] = "Facility is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employeefacility->save($data));
        endif;
    }

    public function edit()
    {     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->employeefacility->getEmployeeFacility($id);
        $this->load->view($this->FormPage, $this->data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->employeefacility->delete($id));
        endif;
    }
}
?>