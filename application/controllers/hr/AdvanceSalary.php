<?php
class AdvanceSalary extends MY_Controller
{
	private $indexpage = "hr/advance_salary/index";
    private $form = "hr/advance_salary/form";
    private $sanctionForm = "hr/advance_salary/sanction_form";
	private $indexPenalty = "hr/advance_salary/indexPenalty";
    private $penalty_form = "hr/advance_salary/penalty_form";
    private $indexFacility = "hr/advance_salary/indexFacility";
    private $facility_form = "hr/advance_salary/facility_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Salary";
		$this->data['headData']->controller = "hr/advanceSalary";
        $this->data['headData']->pageUrl = "hr/advanceSalary";
	}

	public function index(){    
        $this->data['tableHeader'] = getHrDtHeader('advanceSalary');
        $this->load->view($this->indexpage,$this->data);
    }

    public function getDTRows($status = 0,$type=1){
        $data = $this->input->post(); 
        $data['status'] = $status; $data['type'] = $type;
        $result = $this->advanceSalary->getDTRows($data);
       
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getAdvanceSalaryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addAdvance(){
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(!isset($data['form_type'])){
            if(empty($data['emp_id'])){$errorMessage['emp_id'] = "Employee is required.";}
            if(empty($data['amount'])){$errorMessage['amount'] = "Amount is required.";}
            if(empty($data['reason'])){$errorMessage['reason'] = "Reason is required.";}
            
            /*if(!empty($data['emp_id'])):
                $salaryData=$this->employee->getActiveSalaryStructure($data['emp_id']);
                if(!empty($salaryData)){
                    $ctcAmount = ($salaryData->salary_duration == "H")?round(((($salaryData->ctc_amount / 8) * 11) * 26),2):round($salaryData->ctc_amount,2);
                    if($data['amount'] > (round(($ctcAmount / 2),2))){$errorMessage['amount'] = "Amount is bigger than basic salary.";}
                }
                else
                {$errorMessage['amount'] = "Salary Structure Not Defined.";}
            endif;*/
        }else{
            unset($data['form_type']);
            if(empty($data['sanctioned_at']))
                $errorMessage['sanctioned_at'] = "Date and Time is required.";
            if(empty($data['sanctioned_amount']))
                $errorMessage['sanctioned_amount'] = "Sanction Amount is required.";
            $data['sanctioned_by'] = $this->loginId;
        }

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['empSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->advanceSalary->save($data));
        endif;
    }
    
    public function edit(){
        $id = $this->input->post('id'); 
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->data['empData'] = $this->employee->getEmpList();
        $this->load->view($this->form,$this->data);
    }

    public function sanctionAdvance(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->load->view($this->sanctionForm,$this->data);
    }
    
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->advanceSalary->delete($id));
        endif;
    }
    
    // **************************** PENALTY ***************************** //
    public function indexPenalty(){
        $this->data['tableHeader'] = getHrDtHeader("penalty");
        $this->data['type'] = 2;
        $this->load->view($this->indexPenalty,$this->data);
    }

    public function addPenalty(){
        $this->data['type'] = 2;
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->penalty_form,$this->data);
    }
   
	public function savePenalty(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['amount']))
            $errorMessage['amount'] = "Amount is required.";
        if(empty($data['reason']))
            $errorMessage['reason'] = "Reason is required.";
    
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['empSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->advanceSalary->savePenalty($data));
        endif;
    }

    public function editPenalty(){
        $id = $this->input->post('id'); 
        $this->data['type'] = 2;
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->penalty_form,$this->data);
    }
	
	// ********************************* FACILITY *******************************//
    public function indexFacility(){
        $this->data['tableHeader'] = getHrDtHeader("facility");
        $this->data['type'] = 3;
        $this->load->view($this->indexFacility,$this->data);
    }

    public function getDTRowsForFacility($status=0,$type=3){  
        $data = $this->input->post();$data['type'] = $type;
        $result = $this->advanceSalary->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getFacilityData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFacility(){
        $this->data['type'] = 3;
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['typeData'] = $this->employee->getTypeList();
        $this->load->view($this->facility_form,$this->data);
    }

    public function saveFacility(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['facility_id']))
            $errorMessage['facility_id'] = "Facility Type is required.";
      
        if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['empSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->advanceSalary->saveFacility($data));
        endif;
    }

    public function editFacility(){
        $id = $this->input->post('id'); 
        $this->data['type'] = 3;
        $this->data['dataRow'] = $this->advanceSalary->getAdvanceSalary($id);
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->data['typeData'] = $this->employee->getTypeList();
        $this->load->view($this->facility_form,$this->data);
    }
}
?>