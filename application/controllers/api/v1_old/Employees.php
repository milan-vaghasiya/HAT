<?php
class Employees extends MY_Apicontroller{

    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];
    private $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
    private $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setup Inspector",4=>"Process Setter"];

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @30-11-2021 10:10 AM
     update by : 
     note :
*/

    public function EmployeesList(){
        $total_rows = $this->employee->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/employee/employeesList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['employeesList'] = $this->employee->getEmployeesList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addEmployee(){
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_role']))
            $errorMessage['emp_role'] = "Role is required.";
        // if(empty($data['emp_contact']))
        //     $errorMessage['emp_contact'] = "Contact No. is required.";
        if(empty($data['emp_dept_id']))
            $errorMessage['emp_dept_id'] = "Department is required.";
        if(empty($data['emp_designation']))
        {
            if(empty($data['designationTitle']))
                $errorMessage['emp_designation'] = "Designation is required.";
            else
                $data['emp_designation'] = $this->employee->saveDesignation($data['designationTitle'],$data['emp_dept_id']);
        }
        unset($data['designationTitle']);
        if(empty($data['id'])):
            /* if(empty($data['emp_password']))
                $errorMessage['emp_password'] = "Password is required.";
            if(!empty($data['emp_password']) && $data['emp_password'] != $data['emp_password_c'])
                $errorMessage['emp_password_c'] = "Confirm Password not match."; */
            $data['emp_password'] = "123456";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['emp_name'] = ucwords($data['emp_name']);
            $data['created_by'] = $this->loginId;            
            $this->printJson($this->employee->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $result = $this->employee->getEmp($id);
        //$result->designation = $this->employee->getDesignation($result->emp_dept_id,$result->emp_designation)['result'];
        $this->data['dataRow'] = $result;
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }
}
?>