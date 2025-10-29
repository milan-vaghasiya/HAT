<?php
class Employees extends MY_Controller
{
    private $indexPage = "hr/employee/index";
    private $employeeForm = "hr/employee/form";
    private $empSalary = "hr/employee/emp_Salary";
    private $empDocs = "hr/employee/emp_Docs";
    private $empNom = "hr/employee/emp_Nom";
    private $empEdu = "hr/employee/emp_Edu";
    private $profile = "hr/employee/emp_profile";
    private $leaveAuthority = "hr/employee/leave_authority";
    private $employeeDevice="hr/employee/employee_device";
    private $empDocuments = "hr/employee/emp_Documents";
    private $empCtc = "hr/employee/emp_ctc";
    private $empRelieveRejoin="hr/employee/emp_relive_rejoin";
    private $relieved_emp_list="hr/employee/relieved_emp_list";
    private $icard="hr/employee/icard";

    private $modualPermission = "hr/employee/emp_permission";
    private $reportPermission = "hr/employee/emp_permission_report";
    private $copyPermission = "hr/employee/copy_permission";

    private $empRole = ["0"=>"","1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee","7"=>"HR"];
    private $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
    private $systemDesignation = [1=>"Machine Operator",2=>"Line Inspector",3=>"Setter Inspector",4=>"Process Setter",5=>"FQC Inspector"];

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employees";
		$this->data['headData']->controller = "hr/employees";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "hr/employees";
        $this->data['tableHeader'] = getHrDtHeader('employees');
        $this->load->view($this->indexPage,$this->data);
    }

    /* new employee */
    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->employee->getDTRows($data);
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):
			$row->sr_no = $i++; 
			$row->emp_role = $this->empRole[$row->emp_role];  
			$row->loginId = $this->loginId;
			$sendData[] = getEmployeeData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmployee(){
        $this->data['gradeData'] = explode(',', $this->employee->getMasterOptions()->emp_grade);
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList(); 
        $this->data['shiftData'] = $this->shiftModel->getShiftList(); 
        $this->load->view($this->employeeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_code']))
            $errorMessage['emp_code'] = "Employee Code is required.";
        if(empty($data['emp_dept_id']))
            $errorMessage['emp_dept_id'] = "Department is required.";
        if(empty($data['emp_designation']))
            $errorMessage['emp_designation'] = "Designation is required.";
		 if(empty($data['shift_id']))
            $errorMessage['shift_id'] = "Shift is required.";
        //if(empty($data['emp_type']))
        //    $errorMessage['emp_type'] = "Employee Type is required.";
            
        if(empty($data['id'])):
            /*if(empty($data['emp_password']))
                $errorMessage['emp_password'] = "Password is required.";
            if(!empty($data['emp_password']) && $data['emp_password'] != $data['emp_password_c'])
                $errorMessage['emp_password_c'] = "Confirm Password not match.";*/
                $data['emp_password'] = "123456";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['sysdescSelect']);
            $data['emp_name'] = ucwords($data['emp_name']);
            $data['emp_role'] = 6;
            $data['created_by'] = $this->session->userdata('loginId');
            unset($data['emp_password_c']);
            $this->printJson($this->employee->save($data));
        endif;
    }
    
    public function editProfile(){
        $data = $this->input->post();  
        $errorMessage = array();
        if($data['form_type'] == 'personalDetail'):
            if(empty($data['emp_name']))
                $errorMessage['emp_name'] = "Employee name is required.";
            if(empty($data['emp_code']))
                $errorMessage['emp_code'] = "Employee Code is required.";
            $data['emp_name'] = ucwords($data['emp_name']);
        endif;       
        if($data['form_type'] == 'workProfile'):
            if(empty($data['emp_dept_id']))
                $errorMessage['emp_dept_id'] = "Department is required.";
            if(empty($data['emp_designation']))
                $errorMessage['emp_designation'] = "Designation is required.";
            if(empty($data['emp_type']))
                $errorMessage['emp_type'] = "Employee Type is required.";
            if(empty($data['salary_code']))
                $errorMessage['salary_code'] = "Salary Code is required.";
        endif;  
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['sysdescSelect']);
            $data['emp_role'] = 6;
            $this->printJson($this->employee->editProfile($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['dataRow'] = $this->employee->getEmp($id);
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['gradeData'] = explode(',', $this->employee->getMasterOptions()->emp_grade);
        $this->load->view($this->employeeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }
    
    public function addEmployeeInDevice(){
        $id = $this->input->post('id');
       
        $this->data['deviceList']=$this->employee->getDeviceForEmployee();
        $this->data['dataRow'] = $this->employee->getEmp($id);
        $this->data['emp_id'] = $id;

        $this->load->view($this->employeeDevice,$this->data);
        // if(empty($id)):
        //     $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        // else:
        //     $this->printJson($this->employee->addEmployeeInDevice($id));
        // endif;
    }

    /* employee active/inactive */
    public function activeInactive(){
        $id = $this->input->post('id');
        $value = $this->input->post('is_active');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($id,$value));
        endif;
    }
    
    /* change password */
    public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $id = $this->session->userdata('loginId');
			$result =  $this->employee->changePassword($id,$data);
			$this->printJson($result);
		endif;
    }

    /* employee Designation  */
    public function getDesignation(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->getDesignation($id));
        endif;
    }

    /* employee salary */
    public function getEmpSalary(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpSalary($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empSalary,$this->data);
    }

    public function updateEmpSalary(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['salary_basis']))
            $errorMessage['salary_basis'] = "Salary Basis is required.";
        if(empty($data['basic_salary']))
            $errorMessage['basic_salary'] = "Basic Salary is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpSalary($data));
        endif;
    }

    /* employee documents */
    public function getEmpDocs(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpDocs($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empDocs,$this->data);
    }

    public function updateEmpDocs(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee id is required.";
        if($_FILES['basic_ruls']['name'] != null || !empty($_FILES['basic_ruls']['name'])):
                    $this->load->library('upload');
                    $_FILES['userfile']['name']     = $_FILES['basic_ruls']['name'];
                    $_FILES['userfile']['type']     = $_FILES['basic_ruls']['type'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['basic_ruls']['tmp_name'];
                    $_FILES['userfile']['error']    = $_FILES['basic_ruls']['error'];
                    $_FILES['userfile']['size']     = $_FILES['basic_ruls']['size'];
                    
                    $imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/basic rules & regulation/');
                    $config = ['file_name' => time()."_basic_rules_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];
        
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload()):
                        $errorMessage['basic_ruls'] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $data['basic_ruls'] = $uploadData['file_name'];
                    endif;
                else:
                    unset($data['basic_ruls']);
         endif; 

         if($_FILES['aadhar_docs']['name'] != null || !empty($_FILES['aadhar_docs']['name'])):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['aadhar_docs']['name'];
			$_FILES['userfile']['type']     = $_FILES['aadhar_docs']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['aadhar_docs']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['aadhar_docs']['error'];
			$_FILES['userfile']['size']     = $_FILES['aadhar_docs']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/aadhar_docs/');
			$config = ['file_name' => time()."_aadhar_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['aadhar_docs'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$data['aadhar_docs'] = $uploadData['file_name'];
			endif;
		else:
			unset($data['aadhar_docs']);
		endif; 

        if($_FILES['pan_docs']['name'] != null || !empty($_FILES['pan_docs']['name'])):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['pan_docs']['name'];
			$_FILES['userfile']['type']     = $_FILES['pan_docs']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['pan_docs']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['pan_docs']['error'];
			$_FILES['userfile']['size']     = $_FILES['pan_docs']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/pan_docs/');
			$config = ['file_name' => time()."_pan_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['pan_docs'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$data['pan_docs'] = $uploadData['file_name'];
			endif;
		else:
			unset($data['pan_docs']);
		endif; 

        if($_FILES['confirm_letter_docs']['name'] != null || !empty($_FILES['confirm_letter_docs']['name'])):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['confirm_letter_docs']['name'];
			$_FILES['userfile']['type']     = $_FILES['confirm_letter_docs']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['confirm_letter_docs']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['confirm_letter_docs']['error'];
			$_FILES['userfile']['size']     = $_FILES['confirm_letter_docs']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/confirm_letter_docs/');
			$config = ['file_name' => time()."_confirm_letter_docs_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['confirm_letter_docs'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$data['confirm_letter_docs'] = $uploadData['file_name'];
			endif;
		else:
			unset($data['confirm_letter_docs']);
		endif; 

        if($_FILES['emp_detail_docs']['name'] != null || !empty($_FILES['emp_detail_docs']['name'])):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['emp_detail_docs']['name'];
			$_FILES['userfile']['type']     = $_FILES['emp_detail_docs']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['emp_detail_docs']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['emp_detail_docs']['error'];
			$_FILES['userfile']['size']     = $_FILES['emp_detail_docs']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/emp_detail_docs/');
			$config = ['file_name' => time()."_emp_detail_docs_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['emp_detail_docs'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$data['emp_detail_docs'] = $uploadData['file_name'];
			endif;
		else:
			unset($data['emp_detail_docs']);
		endif; 

        if($_FILES['pf_agreement_docs']['name'] != null || !empty($_FILES['pf_agreement_docs']['name'])):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['pf_agreement_docs']['name'];
			$_FILES['userfile']['type']     = $_FILES['pf_agreement_docs']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['pf_agreement_docs']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['pf_agreement_docs']['error'];
			$_FILES['userfile']['size']     = $_FILES['pf_agreement_docs']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/emp_doc/pf_agreement_docs/');
			$config = ['file_name' => time()."_pf_agreement_docs_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['pf_agreement_docs'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$data['pf_agreement_docs'] = $uploadData['file_name'];
			endif;
		else:
			unset($data['pf_agreement_docs']);
		endif;
                

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpDocs($data));
        endif;
    }
    
    
    /*** Update Emp Profile Picture ***/ 
    public function updateProfilePic(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee id is required.";
        
        if($_FILES['emp_profile']['name'] != null || !empty($_FILES['emp_profile']['name'])):
            //
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['emp_profile']['name'];
            $_FILES['userfile']['type']     = $_FILES['emp_profile']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['emp_profile']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['emp_profile']['error'];
            $_FILES['userfile']['size']     = $_FILES['emp_profile']['size'];
            
            $imagePath = realpath(APPPATH . '../assets/uploads/emp_profile/');
            $ext = pathinfo($_FILES['emp_profile']['name'], PATHINFO_EXTENSION);
            $config = ['file_name' => $data['emp_id'].'.'.$ext,'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' => $imagePath];
            if(file_exists($config['upload_path'].'/'.$config['file_name'])) unlink($config['upload_path'].'/'.$config['file_name']);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $errorMessage['emp_profile'] = $this->upload->display_errors();
                $this->printJson(["status"=>0,"message"=>$errorMessage]);
            else:
                $uploadData = $this->upload->data();
                $data['emp_profile'] = $uploadData['file_name'];
            endif;
        else:
            $data['emp_profile'] = '';
            $errorMessage['emp_id'] = "Image Not Found.";
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->updateProfilePic($data));
        endif;
    }

    /* employee nomination */
    public function getEmpNom(){
        $emp_id = $this->input->post('id');
        $this->data['nomData'] = $this->employee->getNominationData($emp_id);
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empNom,$this->data);
    }

    public function updateEmpNom(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['nom_name'][0])){
			$errorMessage['nom_name'] = "Name is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpNom($data));
		endif;
    }

    /* employee education detail */
    public function getEmpEdu(){
        $emp_id = $this->input->post('id');
        $this->data['eduData'] = $this->employee->getEducationData($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empEdu,$this->data);
    }

    public function updateEmpEdu(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['course'][0])){
			$errorMessage['course'] = "Course is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpEdu($data));
		endif;
    }

    /* employee profile */
    public function empProfile($emp_id){
        $employeeData = $this->employee->getEmployee($emp_id);
        $this->data['empData'] = $employeeData;
        $this->data['empSalary'] = $this->employee->getEmpSalary($emp_id);
        $this->data['empDocs'] = $this->employee->getEmpDocs($emp_id);
        $this->data['empNom'] = $this->employee->getNominationData($emp_id);
        $this->data['empEdu'] = $this->employee->getEducationData($emp_id);
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->data['companyInfo'] = $this->employee->getCompanyInfo();
        $this->data['docData'] = $this->employee->getEmpDocuments($emp_id);
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->data['gradeData'] = explode(',', $this->employee->getMasterOptions()->emp_grade);
        $this->data['dataRow'] = $this->employee->getEmp($emp_id);
        $this->data['staffData'] = $this->employee->getStaffSkill($emp_id);
        $this->data['skillData'] = $this->skillMaster->getDeptWiseSkill($this->data['dataRow']->emp_dept_id);
        
        $postData = ['salary_duration' => (($employeeData->emp_type == 1)?"M":"H")];
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats($postData);
        
        $this->data['empFacility'] = $this->employee->getFacilityData($emp_id);
        $this->data['typeData'] = $this->employee->getTypeList();
        $this->data['attendancePolicies'] = $this->policy->getAttendancePolicies();
        $this->load->view($this->profile,$this->data);
    }

    /* LeaveAuthority */
    public function getEmpLeaveAuthority(){
        $emp_id = $this->input->post('id');
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['leaveData'] = $this->employee->getLeaveHierarchy($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->leaveAuthority,$this->data);
    }

    public function saveLeaveAuthority(){
        $data = $this->input->post();
        $errorMessage = Array();
        if(empty($data['dept_id']))
            $errorMessage['emp_dept_id'] = " Pelase select department.";
        if(empty($data['desi_id']))
            $errorMessage['emp_designation'] = " Pelase select designation.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $response = $this->employee->saveLeaveAuthority($data);
			if($response['status'] == 0):
				$this->printJson($response);
			else:
				$this->printJson($this->setLeaveAuthorityView($data['emp_id']));
			endif;
        endif;
    }

    public function setLeaveAuthorityView($emp_id){
        $leaveHierarchyData = $this->employee->getLeaveHierarchy($emp_id);
        $leaveHtml = '';$i=1;
        if (!empty($leaveHierarchyData)) :
            foreach ($leaveHierarchyData as $row) :
				$deleteParam = $row->id.','.$row->emp_id;
				$deleteButton = '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="deleteLeaveAuthority('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                $leaveHtml .= '<tr id="'.$row->id.'">
                            <td class="text-center">'.$i++.'</td>
                            <td>'.$row->name.'</td>
                            <td>'.$row->title.'</td>
                            <td class="text-center">'.$row->priority.'</td>
                            <td>'.$deleteButton.'</td>
                        </tr>';
            endforeach;
        else :
            $leaveHtml .= '<tr><td colspan="5" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "leaveHtml" => $leaveHtml];
    }

    public function setLeaveApprovalPriority(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->employee->setLeaveApprovalPriority($data));			
		endif;
    }

    public function deleteLeaveAuthority(){
        $data = $this->input->post();
        $errorMessage = Array();
        if(empty($data['id']))
            $errorMessage['general']= " ID is Required";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            $response = $this->employee->deleteLeaveAuthority($data);
            $this->printJson($this->setLeaveAuthorityView($data['emp_id']));
        endif;
    }

    public function empPermission(){
        $this->data['headData']->pageUrl = "hr/employees/empPermission";
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['permission'] = $this->permission->getPermission(0);
        $this->load->view($this->modualPermission,$this->data);
    }

    public function empPermissionReport(){
        $this->data['headData']->pageUrl = "hr/employees/empPermission";
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['permission'] = $this->permission->getPermission(1);
        $this->load->view($this->reportPermission,$this->data);
    }

    public function savePermission(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['emp_id']))
            $errorMessage['emp_id'] = "Employee name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->permission->save($data));
        endif;
    }

    public function editPermission(){
        $emp_id = $this->input->post('emp_id');
        $this->printJson($this->permission->editPermission($emp_id));
    }

    public function copyPermission(){
        $this->data['fromList'] = $this->employee->getEmpList();
        $this->data['toList'] = $this->employee->getEmpList();
        $this->load->view($this->copyPermission,$this->data);
    }

    public function saveCopyPermission(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['from_id']))
            $errorMessage['from_id'] = "From User is required.";
        if(empty($data['to_id']))
            $errorMessage['to_id'] = "To User is required.";
        
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $fromData = $this->permission->getEmployeePermission($data['from_id']);            
            $this->printJson($this->permission->saveCopyPermission($data,$fromData['mainPermission'],$fromData['subMenuPermission']));
        endif;
    }

    public function saveEmployeeInDevice()
    {
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->addEmployeeInDevice($data['id'],$data['emp_id']));
        endif;
    }
    
    public function removeEmployeeInDevice()
    {
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->removeEmployeeInDevice($data['id'],$data['emp_id']));
        endif;
    }
    
    public function transferEmpCode()
    {
        ini_set('max_execution_time', 0);
        $data=$this->input->post();
        if(empty($data['emp_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again1.']);
        else:
            $this->printJson($this->employee->transferEmpCode($data));
        endif;
    }
    
    // Created By Karmi @08/01/2022
    /* New employee documents */
    public function getEmpDocuments(){
        $emp_id = $this->input->post('id');
        $this->data['docData'] = $this->employee->getEmpDocuments($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empDocuments,$this->data);
    }
    
    // Created By Karmi @08/01/2022
    public function saveEmpDocumentsParam(){
        $data = $this->input->post();
        //print_r($data);exit;
		$errorMessage = array();
        if(empty($data['doc_name']))
            $errorMessage['doc_name'] = "Document Name is required.";
        if(empty($data['doc_no']))
			$errorMessage['doc_no'] = "Document No is required.";
        if(empty($data['doc_type']))
			$errorMessage['doc_type'] = "Document Type is required.";

        if($_FILES['doc_file']['name'] != null || !empty($_FILES['doc_file']['name'])):
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['doc_file']['name'];
            $_FILES['userfile']['type']     = $_FILES['doc_file']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['doc_file']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['doc_file']['error'];
            $_FILES['userfile']['size']     = $_FILES['doc_file']['size'];
            
            $imagePath = realpath(APPPATH . '../assets/uploads/emp_documents/');
            $config = ['file_name' => time()."_doc_file_".$data['emp_id']."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()):
                $errorMessage['doc_file'] = $this->upload->display_errors();
                $this->printJson(["status"=>0,"message"=>$errorMessage]);
            else:
                $uploadData = $this->upload->data();
                $data['doc_file'] = $uploadData['file_name'];
            endif;
        else:
            unset($data['doc_file']);
        endif; 

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpDocumentsParam($data);
            $docData = $this->employee->getEmpDocuments($data['emp_id']);
            //print_r($docData);
            $tbodyData="";$i=1; 
            if(!empty($docData)):
                $i=1;
                foreach($docData as $row):
                    $tbodyData.= '<tr>
                                <td class="text-center">'.$i++.'</td>
                                <td class="text-center">'.$row->doc_name.'</td>
                                <td class="text-center">'.$row->doc_no.'</td>
                                <td class="text-center">'.$row->doc_type_name.'</td>
                                <td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deletePreInspection(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpDocuments($data['id']);
            $docData = $this->employee->getEmpDocuments($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($docData)):
                $i=1;
                foreach($docData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->doc_name.'</td>
                                <td>'.$row->doc_no.'</td>
                                <td>'.$row->doc_type.'</td>
                                <td>'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    //Created By Karmi @12/01/2022 
    function printExperienceCertificate(){
		$data=$this->input->post();
        $id=$data['id'];
        $responsibility=$data['new_exp_responsibilities'];
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$this->data['empData'] = $this->employee->getEmpforExpCertificate($id);
        $this->data['empRes'] = $responsibility;
        $this->data['empResMain'] = $this->masterOption->getMasterOptions();

		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $this->data['letter_footer']=base_url('assets/images/lh_footer.png');
		$pdfData = $this->load->view('hr/employee/experience_certificate',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<img src="'.$this->data['letter_footer'].'" class="img">';
		
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,41,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	public function getCtc(){
        $emp_id = $this->input->post('id');
        $this->data['dataRow'] = $this->employee->getEmpSalary($emp_id);
        $this->data['emp_id'] = $emp_id;
        $this->load->view($this->empCtc,$this->data);
    }

    public function calculateCtc1(){
        $data = $this->input->post();
        $salaryData = $this->salaryStructure->getsalaryStructure();
        $basicSalary = 0;
        //$bs = (($data['basic_salary'] * $salaryData->basic_salary) / 100);
        //if($bs > 9000){ $basicSalary = $bs; } else { $basicSalary = 9000; }
        $basicSalary = $data['basic_salary'];
        $hra = (($basicSalary * $salaryData->hra) / 100);
        $ca = (($basicSalary * $salaryData->ca) / 100);
        $bonus = 750; //UP TO 20,000 --> CTC

        $gross = ($basicSalary + $hra + $ca + $data['sa']);
        $pf = (($salaryData->pf * ($basicSalary + $data['sa'] + $ca)) / 100);
        $this->printJson(['status'=>1, 'basicSalary'=>$basicSalary, 'hra'=>$hra, 'ca'=>round($ca,0), 'sa'=>$data['sa'], 'pf'=>round($pf,0), 'bonus'=>$bonus, 'gross'=>round($gross,0), 'variable_pay'=> $salaryData->variable_pay, 'gratuity'=>$salaryData->gratuity, 'pf_per'=>$salaryData->pf]);
    }

    public function updateCtc(){
        $data = $this->input->post();
        $errorMessage = array();
		if(empty($data['ctc']))
			$errorMessage['ctc'] = "C.T.C. is required.";
        if(empty($data['basic_salary']))
			$errorMessage['basic_salary'] = "Basic Salary is required.";
        if(empty($data['effect_start']))
			$errorMessage['effect_start'] = "Effect Start is required.";
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpCtc($data));
        endif;
    }
    
    public function empRelive(){
        $data=$this->input->post();
        $this->data['dataRow']=new stdClass();
        $this->data['dataRow']->id=$data['id'];
        $this->data['dataRow']->is_delete=2;
        $this->load->view($this->empRelieveRejoin,$this->data);
    }

    public function saveEmpRelieve(){ 
        $data = $this->input->post();
		$errorMessage = array();
        if($data['is_delete']==2){
            if(empty($data['emp_relieve_date'])){
                $errorMessage['emp_relieve_date'] = "Relieve Date is required.";
            }
            if(empty($data['reason'])){
                $errorMessage['reason'] = "Reason is required.";
            }
        }else{
            if(empty($data['emp_joining_date'])){
                $errorMessage['emp_joining_date'] = "ReJoining Date is required.";
            }
        }
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->saveEmpReliveJoinData($data));
		endif;
    }

   
    public function getRelievedEmpDTRows(){
        $result = $this->employee->getRelievedEmpDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):              
			$value = ($row->is_active == 1)?0:1;
			$checked = ($row->is_active == 1)?"checked":"";
			if($row->emp_role!=1):
				$count = 1;
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive permission-modify" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" data-row_id="'.$i.'" '.$checked.'>';
			else:
				$row->active_html = '<input type="checkbox" id="activeInactive'.$i.'" class="bt-switch activeInactive permission-modify" data-on-color="success"  data-off-color="danger" data-on-text="Active" data-off-text="Inactive" data-id="'.$row->id.'" data-val="'.$value.'" data-row_id="'.$i.'" '.$checked.'>';
			endif;
			$row->sr_no = $i++; 
			
			//Meghavi
			$optionStatus = $this->employee->checkEmployeeStatus($row->id);
			$row->salary = (!empty($optionStatus->salary)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->document = (!empty($optionStatus->document)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->nomination = (!empty($optionStatus->nomination)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->education = (!empty($optionStatus->education)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->leave = (!empty($optionStatus->leave)) ? '<i class="fa fa-check text-primary"></i>' : '';
			
			//$row->emp_role = $this->empRole[$row->emp_role];         
			$sendData[] = getEmpRelievedData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function empRelievedList(){
        $this->data['tableHeader'] = getHrDtHeader('relievedEmployee');
        $this->load->view($this->relieved_emp_list,$this->data);
    }
    
    public function empRejoin(){
        $data=$this->input->post();
        $this->data['dataRow']=new stdClass();
        $this->data['dataRow']->id=$data['id'];
        $this->data['dataRow']->is_delete=0;
        $this->load->view($this->empRelieveRejoin,$this->data);
    }
	
    /* employee I-Card */
    public function icard($emp_id){
        $this->data['empData'] = $this->employee->getEmployee($emp_id);
        $this->data['empSalary'] = $this->employee->getEmpSalary($emp_id);
        $this->data['empDocs'] = $this->employee->getEmpDocs($emp_id);
        $this->data['empNom'] = $this->employee->getNominationData($emp_id);
        $this->data['empEdu'] = $this->employee->getEducationData($emp_id);
        $this->data['roleData'] = $this->empRole;
        $this->data['genderData'] = $this->gender;
        $this->data['emp_id'] = $emp_id;
        $this->data['docData'] = $this->employee->getEmpDocuments($emp_id);
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['companyInfo'] = $this->employee->getCompanyInfo();
        $this->data['systemDesignation'] = $this->systemDesignation;
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->data['gradeData'] = explode(',', $this->employee->getMasterOptions()->emp_grade);
        $this->data['dataRow'] = $this->employee->getEmp($emp_id);
        $this->load->view($this->icard,$this->data);
    }
    public function updateStaffSkill(){
        $data = $this->input->post(); 
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->employee->updateStaffSkill($data));
        endif;
    }
	
    // Save Salary in Emp MAster Actual/On Papaer
    public function saveSalary(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['ctc_emp_id']))
            $errorMessage['ctc_emp_id'] = "Employee is required.";
        if(empty($data['salary_actual']))
            $errorMessage['salary_actual'] = "Actual Salary is required.";
     
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveSalary($data));
        endif;
    }
    
	/*** Created By JP@22-11-2022 
    Updated By Milan@23-11-2022 
    ****/
	public function calculateCTC11(){
        $postData = $this->input->post(); 
        $errorMessage = array();
		if(empty($postData['ctc_emp_id']))
            $errorMessage['ctc_emp_id'] = "Emp ID Required";
		if(empty($postData['ctc_format']))
            $errorMessage['ctc_format'] = "CTC Format Required";
		if(empty($postData['ctc_amount']))
            $errorMessage['ctc_amount'] = "Amount Required";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $ctcData = $this->salaryStructure->getCtcFromat($postData['ctc_format']);
			$salaryHeadData = $this->salaryStructure->getSalaryHeadsOnCtcFormat($postData['ctc_format']);

            $postData['salary_duration'] = $ctcData->salary_duration;
            $empData = [
                'id'=>$postData['ctc_emp_id'],
                'ctc_format' => $postData['ctc_format'],
                'ctc_amount' => $postData['ctc_amount'],
                'salary_duration' => $postData['salary_duration'],
                //'month_hours' => $postData['month_hours'],
                'updated_by' => $this->loginId
            ];
            $this->employee->saveEmpSalary($empData);
            
            $ctcStructure = '<table class="table table-bordered align-items-center">';
			$ctcStructure .= '<thead class="thead-info"><tr>';
				$ctcStructure .= '<th>Fixed Components</th>';
            if($postData['salary_duration'] == "M"):
				$ctcStructure .= '<th>Annual</th>';
            endif;
				$ctcStructure .= '<th>Monthly </th>';
				//$ctcStructure .= '<th>Hourly </th>';
			$ctcStructure .= '</tr></thead><tbody>';
			
			$ctcAmt = $postData['ctc_amount'];
			$grossEarn = '';$genEarn = '';$grossDed = '';
			$grossEarnTotal = [0,0,0];$genEarnTotal = [0,0,0];$grossDedTotal = [0,0,0];
            $headAmount = array();$calculationAmt = array();
			if(!empty($salaryHeadData)){               

				foreach($salaryHeadData as $row){
                        $monthlyAmt = 0;
                        if(!empty($row->cal_on)):
                            $calOn = explode(",",$row->cal_on); $calculationAmt = array();   
                            $calculationAmt[$row->id] = 0;                       
                            foreach($calOn as $key=>$headId):
                                if($headId == -1):
                                    $calculationAmt[$row->id] = $ctcAmt;
                                else:                                       
                                    $calculationAmt[$row->id] = (isset($headAmount[$headId]))?($headAmount[$headId] + $calculationAmt[$row->id]):$calculationAmt[$row->id];                                    
                                endif;
                            endforeach;
                        endif;
                        
                        //if($row->id == 3): print_r($headAmount); print_r("<hr>"); print_r($calculationAmt);exit; endif;

                        $monthlyAmt = 0;
                        if($row->cal_method == 1):
                            $monthlyAmt = round((($row->cal_value * $calculationAmt[$row->id])/100),2);
                            if($row->min_val > 0 AND $monthlyAmt < $row->min_val):
                                $monthlyAmt = $row->min_val;
                            endif;
                            //if($row->id == 3): print_r($monthlyAmt);exit; endif;
                        elseif($row->cal_method == 2):
                            $monthlyAmt = round($calculationAmt[$row->id],2);
                            if((!empty($row->min_val)) AND $monthlyAmt > $row->min_val):
                                $monthlyAmt = $row->cal_value;
                            else:
                                $monthlyAmt = 0;
                            endif;
                        endif;
                        $headAmount[$row->id] = ($monthlyAmt * $row->type);

                        //if($row->id == 2): print_r($headAmount);exit; endif;

                    switch($row->parent_head){
                        case 1:
                            $grossEarn .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $grossEarn .= '<td>'.round(round($headAmount[$row->id]) * 12).'</td>';
                            endif;
                            $grossEarn .= '<td>'.round($headAmount[$row->id]).'</td></tr>';
                            $grossEarnTotal[0] += round(round($headAmount[$row->id]) * 12);
                            $grossEarnTotal[1] += round($headAmount[$row->id]);break;
                        case 2:
                            $genEarn .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $genEarn .= '<td>'.round(round($headAmount[$row->id]) * 12).'</td>';
                            endif;
                            $genEarn .= '<td>'.round($headAmount[$row->id]).'</td></tr>';
                            $genEarnTotal[0] += round(round($headAmount[$row->id]) * 12);
                            $genEarnTotal[1] += round($headAmount[$row->id]);break;
                        case 3:
                            $grossDed .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $grossDed .= '<td>'.round(abs(round($headAmount[$row->id]) * 12)).'</td>';
                            endif;
                            $grossDed .= '<td>'.round(abs($headAmount[$row->id])).'</td></tr>';
                            $grossDedTotal[0] += round(round($headAmount[$row->id]) * 12);
                            $grossDedTotal[1] += round($headAmount[$row->id]);break;
                    }
				}
			}  
            
            // Calculate Employer Provident Fund
            if($ctcData->pf_status > 0):
                if(!empty($ctcData->pf_on)):
                    $pfHeadIds = explode(",",$ctcData->pf_on);
                    $calculationAmt = 0;
                    foreach($pfHeadIds as $key=>$headId):
                        $calculationAmt = (isset($headAmount[$headId]))?($headAmount[$headId] + $calculationAmt):$calculationAmt; 
                    endforeach;

                    $pfAmount = round(($calculationAmt * $ctcData->pf_per)/100);

                    $genEarn .= '<tr><td>Employer Provident Fund @ 12%</td>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<td>'.round($pfAmount * 12).'</td>';
                    endif;
                    $genEarn .= '<td>'.round($pfAmount).'</td></tr>';
                    $genEarnTotal[0] += round($pfAmount * 12);
                    $genEarnTotal[1] += round($pfAmount);

                    $grossDed .= '<tr><td>Employer Provident Fund @ 12%</td>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<td>'.round($pfAmount * 12).'</td>';
                    endif;
                    $grossDed .= '<td>'.round($pfAmount).'</td></tr>';
                    $grossDedTotal[0] += round(($pfAmount * -1) * 12);
                    $grossDedTotal[1] += round(($pfAmount * -1));
                endif;
            endif;

            // Calculate Gratuity
            if($ctcData->gratuity_days > 0):
                if(!empty($ctcData->gratuity_on)):
                    $gratuityHeadIds = explode(",",$ctcData->gratuity_on);
                    $calculationAmt = 0;
                    foreach($gratuityHeadIds as $key=>$headId):
                        $calculationAmt = (isset($headAmount[$headId]))?($headAmount[$headId] + $calculationAmt):$calculationAmt; 
                    endforeach;
                    $gratuityAmount = (!empty($calculationAmt))?round((($calculationAmt * $ctcData->gratuity_per)/$ctcData->gratuity_days)/12,-1):0;

                    if(!empty($gratuityAmount)):
                        $gratuityAmount = (ceil($gratuityAmount/50) * 50);
                        $genEarn .= '<tr><td>Gratuity</td>';
                        if($postData['salary_duration'] == "M"):
                            $genEarn .= '<td>'.round($gratuityAmount * 12).'</td>';
                        endif;
                        $genEarn .= '<td>'.round($gratuityAmount).'</td></tr>';
                        $genEarnTotal[0] += round($gratuityAmount * 12);
                        $genEarnTotal[1] += round($gratuityAmount);
                    endif;
                endif;
            endif;

            // Calculate Profession Tax
            $professionTax = $this->salaryStructure->getProfessionTaxBaseOnGrossSalary($grossEarnTotal[1]);
            if(!empty($professionTax)){
                $grossDed .= '<tr><td>Profession Tax</td>';
                if($postData['salary_duration'] == "M"):
                    $grossDed .= '<td>'.round($professionTax->amount * 12).'</td>';
                endif;
                $grossDed .= '<td>'.round($professionTax->amount).'</td></tr>';
                $grossDedTotal[0] += round(($professionTax->amount * -1) * 12);
                $grossDedTotal[1] += round(($professionTax->amount * -1));
            }

            $netPayTotal[0] = round($grossEarnTotal[0]+$grossDedTotal[0]);
            $netPayTotal[1] = round($grossEarnTotal[1]+$grossDedTotal[1]);
            //print_r($headAmount);exit;

            if($grossEarnTotal[1] > 0):
                $grossEarn .= '<tr class="bg-light">';
                    $grossEarn .= '<th>Gross Salary Total</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossEarn .= '<th>'.$grossEarnTotal[0].'</th>';
                    endif;
                    $grossEarn .= '<th>'.$grossEarnTotal[1].'</th>';
                $grossEarn .= '</tr>';
            endif;
            if($genEarnTotal[1] > 0):
                $genEarn .= '<tr class="bg-light">';
                    $genEarn .= '<th>Grand Total - CTC</th>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<th>'.($genEarnTotal[0]+$grossEarnTotal[0]).'</th>';
                    endif;
                    $genEarn .= '<th>'.($genEarnTotal[1]+$grossEarnTotal[1]).'</th>';
                $genEarn .= '</tr>';
            endif;
            if($grossDedTotal[1] > 0):
                $grossDed .= '<tr class="bg-light">';
                    $grossDed .= '<th>Gross Deduction</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<th>'.abs($grossDedTotal[0]).'</th>';
                    endif;
                    $grossDed .= '<th>'.abs($grossDedTotal[1]).'</th>';
                $grossDed .= '</tr>';
            endif;
			$netPay = '<tr class="bg-light-info">';
				$netPay .= '<th>On Hand Salary</th>';
                if($postData['salary_duration'] == "M"):
                    $netPay .= '<th>'.$netPayTotal[0].'</th>';
                endif;
                $netPay .= '<th>'.$netPayTotal[1].'</th>';
			$netPay .= '</tr>';
			$ctcStructure .= $grossEarn.$genEarn.$grossDed.$netPay.'</tbody></table>';
            $this->printJson(['status'=>1,'ctcStructure'=>$ctcStructure]);
        endif;
    }

	/*** Created By JP@27-11-2022 
    Updated By Milan@23-11-2022 
    ****/
	public function calculateCTC_1(){
        $postData = $this->input->post(); 
        $errorMessage = array();
		if(empty($postData['ctc_emp_id']))
            $errorMessage['ctc_emp_id'] = "Emp ID Required";
		if(empty($postData['ctc_format']))
            $errorMessage['ctc_format'] = "CTC Format Required";
		if(empty($postData['ctc_amount']))
            $errorMessage['ctc_amount'] = "Amount Required";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $ctcData = $this->salaryStructure->getCtcFromat($postData['ctc_format']);
			$salaryHeadData = $this->salaryStructure->getSalaryHeadsOnCtcFormat($postData['ctc_format']);
			
			$ctcAmt = $postData['ctc_amount'];$gross_salary=0;
			$basic_salary = round((($ctcAmt * $ctcData->basic_da)/100));
			if($basic_salary < $ctcData->min_wages){$basic_salary = $ctcData->min_wages;}
			$hra = ($ctcData->hra > 0) ? round((($basic_salary * $ctcData->hra)/100)) : 0;
					
            $postData['salary_duration'] = $ctcData->salary_duration;
            $ctcStructure = '<table class="table table-bordered align-items-center">';
			$ctcStructure .= '<thead class="thead-info"><tr>';
				$ctcStructure .= '<th>Fixed Components</th>';
            if($postData['salary_duration'] == "M"):
				$ctcStructure .= '<th>Annual</th>';
            endif;
				$ctcStructure .= '<th>Monthly </th>';
				//$ctcStructure .= '<th>Hourly </th>';
			$ctcStructure .= '</tr></thead><tbody>';
			
			$ctcAmt = $postData['ctc_amount'];
			$grossEarn = '';$genEarn = '';$grossDed = '';
			$grossEarnTotal = $basic_salary + $hra;$genEarnTotal = 0;$grossDedTotal = 0;
            $headAmount = array();$calculationAmt = array();
			
			$grossEarn .= '<tr><td>Basic + DA</td>';
			if($postData['salary_duration'] == "M"):
				$grossEarn .= '<td>'.round($basic_salary * 12).'</td>';
			endif;
			$grossEarn .= '<td>'.round($basic_salary).'</td></tr>';
			if($hra > 0 )
			{
				$grossEarn .= '<tr><td>HRA</td>';
				if($postData['salary_duration'] == "M"):
					$grossEarn .= '<td>'.round($hra * 12).'</td>';
				endif;
				$grossEarn .= '<td>'.round($hra).'</td></tr>';
			}
			if(!empty($salaryHeadData)){               

				foreach($salaryHeadData as $row){
					$monthlyAmt = 0;
					if(!empty($row->cal_on)):
						$calON = 0;
						if($row->cal_on == 1){$calON = $ctcAmt;} // ON CTC
						if($row->cal_on == 2){$calON = $basic_salary;} // ON BASIC SALARY
						if($row->cal_on == 3){$calON = $grossEarnTotal;} // ON GROSS SALARY
					
						if($row->cal_method == 1):
							$monthlyAmt = round((($row->cal_value * $calON)/100),2);
						elseif($row->cal_method == 2):
							$monthlyAmt = round($row->cal_value,2);
						endif;
						$headAmount[$row->id] = ($monthlyAmt * $row->type);
					else:
						$headAmount[$row->id] = 0;
					endif;
					
                    switch($row->parent_head){
                        case 1:
                            $grossEarn .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $grossEarn .= '<td>'.round(round($headAmount[$row->id]) * 12).'</td>';
                            endif;
                            $grossEarn .= '<td>'.round($headAmount[$row->id]).'</td></tr>';
                            $grossEarnTotal += round($headAmount[$row->id]);break;
                        case 2:
                            $genEarn .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $genEarn .= '<td>'.round(round($headAmount[$row->id]) * 12).'</td>';
                            endif;
                            $genEarn .= '<td>'.round($headAmount[$row->id]).'</td></tr>';
                            $genEarnTotal += round($headAmount[$row->id]);break;
                        case 3:
                            $grossDed .= '<tr><td>'.$row->head_name.'</td>';
                            if($postData['salary_duration'] == "M"):
                                $grossDed .= '<td>'.round(abs(round($headAmount[$row->id]) * 12)).'</td>';
                            endif;
                            $grossDed .= '<td>'.round(abs($headAmount[$row->id])).'</td></tr>';
                            $grossDedTotal += round($headAmount[$row->id]);break;
                    }
				}
			}  
            
            // Calculate Employer Provident Fund
            if($ctcData->pf_status > 0):
                if(!empty($ctcData->pf_on)):
					$pfON = $grossEarnTotal - $hra;
                    $pfAmount = round(($pfON * $ctcData->pf_per)/100);

                    $genEarn .= '<tr><td>Employer Provident Fund @ 12%</td>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<td>'.round($pfAmount * 12).'</td>';
                    endif;
                    $genEarn .= '<td>'.round($pfAmount).'</td></tr>';
                    $genEarnTotal += round($pfAmount);

                    $grossDed .= '<tr><td>Employer Provident Fund @ 12%</td>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<td>'.round($pfAmount * 12).'</td>';
                    endif;
                    $grossDed .= '<td>'.round($pfAmount).'</td></tr>';
                    $grossDedTotal += round(($pfAmount * -1));
                endif;
            endif;

            // Calculate Gratuity
            if($ctcData->gratuity_days > 0):
				$gratuityAmount = round((($basic_salary * $ctcData->gratuity_per)/$ctcData->gratuity_days)/12,-1);

				if(!empty($gratuityAmount)):
					$gratuityAmount = (ceil($gratuityAmount/50) * 50);
					$genEarn .= '<tr><td>Gratuity</td>';
					if($postData['salary_duration'] == "M"):
						$genEarn .= '<td>'.round($gratuityAmount * 12).'</td>';
					endif;
					$genEarn .= '<td>'.round($gratuityAmount).'</td></tr>';
					$genEarnTotal += round($gratuityAmount);
				endif;
            endif;

            // Calculate Profession Tax
            $professionTax = $this->salaryStructure->getProfessionTaxBaseOnGrossSalary($grossEarnTotal);
            if(!empty($professionTax)){
                $grossDed .= '<tr><td>Profession Tax</td>';
                if($postData['salary_duration'] == "M"):
                    $grossDed .= '<td>'.round($professionTax->amount * 12).'</td>';
                endif;
                $grossDed .= '<td>'.round($professionTax->amount).'</td></tr>';
                $grossDedTotal += round(($professionTax->amount * -1));
            }

            $netPayTotal = round($grossEarnTotal+$grossDedTotal);
            //print_r($headAmount);exit;

            if($grossEarnTotal > 0):
                $grossEarn .= '<tr class="bg-light">';
                    $grossEarn .= '<th>Gross Salary Total</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossEarn .= '<th>'.($grossEarnTotal * 12).'</th>';
                    endif;
                    $grossEarn .= '<th>'.$grossEarnTotal.'</th>';
                $grossEarn .= '</tr>';
            endif;
            if($genEarnTotal > 0):
                $genEarn .= '<tr class="bg-light">';
                    $genEarn .= '<th>Grand Total - CTC</th>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<th>'.(($genEarnTotal+$grossEarnTotal) * 12).'</th>';
                    endif;
                    $genEarn .= '<th>'.($genEarnTotal+$grossEarnTotal).'</th>';
                $genEarn .= '</tr>';
            endif;
            if($grossDedTotal > 0):
                $grossDed .= '<tr class="bg-light">';
                    $grossDed .= '<th>Gross Deduction</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<th>'.(abs($grossDedTotal) * 12).'</th>';
                    endif;
                    $grossDed .= '<th>'.abs($grossDedTotal).'</th>';
                $grossDed .= '</tr>';
            endif;
			$netPay = '<tr class="bg-light-info">';
				$netPay .= '<th>On Hand Salary</th>';
                if($postData['salary_duration'] == "M"):
                    $netPay .= '<th>'.($netPayTotal * 12).'</th>';
                endif;
                $netPay .= '<th>'.$netPayTotal.'</th>';
			$netPay .= '</tr>';
			$ctcStructure .= $grossEarn.$genEarn.$grossDed.$netPay.'</tbody></table>';
		
			// Save CTC to Employee Master
			
			$empData = [
				'id'=>$postData['ctc_emp_id'],
				'ctc_format' => $postData['ctc_format'],
				'ctc_amount' => $postData['ctc_amount'],
				'salary_duration' => $postData['salary_duration'],
				//'month_hours' => $postData['month_hours'],
				'updated_by' => $this->loginId
			];
			$this->employee->saveEmpSalary($empData);
			
            $this->printJson(['status'=>1,'ctcStructure'=>$ctcStructure]);
        endif;
    }
	
    public function calculateCTC(){
        $postData = $this->input->post(); 
        $errorMessage = array();
		if(empty($postData['ctc_emp_id']))
            $errorMessage['ctc_emp_id'] = "Emp ID Required";
		if(empty($postData['ctc_format']))
            $errorMessage['ctc_format'] = "CTC Format Required";
		if(empty($postData['ctc_amount']))
            $errorMessage['ctc_amount'] = "Amount Required";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $ctcData = $this->salaryStructure->getCtcFromat($postData['ctc_format']);
			$salaryHeadData = $this->salaryStructure->getSalaryHeadsOnCtcFormat($postData['ctc_format']);
			
			$ctcAmt = $postData['ctc_amount'];$gross_salary=0;
			$basic_salary = round((($ctcAmt * $ctcData->basic_da)/100));
			if($basic_salary < $ctcData->min_wages){$basic_salary = $ctcData->min_wages;}
			$hra = ($ctcData->hra > 0) ? round((($basic_salary * $ctcData->hra)/100)) : 0;
					
            $postData['salary_duration'] = $ctcData->salary_duration;
            $ctcStructure = '<table class="table table-bordered align-items-center">';
			$ctcStructure .= '<thead class="thead-info"><tr>';
				$ctcStructure .= '<th>Fixed Components</th>';
            if($postData['salary_duration'] == "M"):
				$ctcStructure .= '<th>Annual</th>';
            endif;
				$ctcStructure .= '<th>Monthly </th>';
				//$ctcStructure .= '<th>Hourly </th>';
			$ctcStructure .= '</tr></thead><tbody>';
			
			$ctcAmt = $postData['ctc_amount'];
			$grossEarn = '';$genEarn = '';$grossDed = '';
			$grossEarnTotal = $basic_salary + $hra;$genEarnTotal = 0;$grossDedTotal = 0;
            $headAmount = array();$calculationAmt = array();
			
			$grossEarn .= '<tr><td>Basic + DA</td>';
			if($postData['salary_duration'] == "M"):
				$grossEarn .= '<td>'.round($basic_salary * 12).'</td>';
			endif;
			$grossEarn .= '<td>'.round($basic_salary).'</td></tr>';
			if($hra > 0 )
			{
				$grossEarn .= '<tr><td>HRA</td>';
				if($postData['salary_duration'] == "M"):
					$grossEarn .= '<td>'.round($hra * 12).'</td>';
				endif;
				$grossEarn .= '<td>'.round($hra).'</td></tr>';
			}
			
			$grossCTC = $grossEarnTotal; $autoHeads = array(); $dataRow = array();
			if(!empty($salaryHeadData)){  
				foreach($salaryHeadData as $row){
					$monthlyAmt = 0;
					if(!empty($row->cal_on)):
						$calON = 0;
						if($row->cal_on == 1){$calON = $ctcAmt;} // ON CTC
						if($row->cal_on == 2){$calON = $basic_salary;} // ON BASIC SALARY
						if($row->cal_on == 3){$calON = $grossCTC;} // ON GROSS SALARY
					
						if($row->cal_method == 1):
							$monthlyAmt = round((($row->cal_value * $calON)/100),2);
						elseif($row->cal_method == 2):
							$monthlyAmt = round($row->cal_value,2);
						endif;
						$headAmount[$row->id] = ($monthlyAmt * $row->type);
					else:
					    $autoHeads[$row->id] = $row->type;
					    $headAmount[$row->id] = 0;
					endif;
					
					$grossCTC += ($row->parent_head == 1 || $row->parent_head == 2)?round($headAmount[$row->id]):0;

                    switch($row->parent_head){
                        case 1:
                            $grossEarnTotal += round($headAmount[$row->id]);break;
                        case 2:
                            $genEarnTotal += round($headAmount[$row->id]);break;
                        case 3:
                            $grossDedTotal += round($headAmount[$row->id]);break;
                    }
					
					$dataRow[$row->id] = [
					    'head_name' => $row->head_name,
					    'parent_head' => $row->parent_head,
					    'head_amount' => round($headAmount[$row->id])
					];
				}
			}
            
            // Calculate Gratuity
            if($ctcData->gratuity_days > 0):
				$gratuityAmount = round((($basic_salary * $ctcData->gratuity_per)/$ctcData->gratuity_days)/12,-1);

				if(!empty($gratuityAmount)):
					$gratuityAmount = (ceil($gratuityAmount/50) * 50);
					
					$dataRow['gratuity'] = [
					    'head_name' => "Gratuity",
					    'parent_head' => 2,
					    'head_amount' => $gratuityAmount
					];
					$grossCTC += $gratuityAmount;
					$genEarnTotal += round($gratuityAmount);
				endif;
            endif;
			
			// Calculate Employer Provident Fund
			$pfAmount = 0;$npf = 0;$pfAmt = 0;
            if($ctcData->pf_status > 0):
				$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
				$pfAmt = $this->calcPF($pfON,$ctcData->pf_per);
				while($npf != $pfAmt){					
					$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
					$pfAmt = $this->calcPF($pfON,$ctcData->pf_per);
					
					$grossCTC += $pfAmt;
					$genEarnTotal += round($pfAmt);
					$grossDedTotal += round(($pfAmt * -1));
					$diffAmt = $ctcAmt - $grossCTC;
					$grossEarnTotal += $diffAmt;
					//$pfON = ($grossEarnTotal + ($ctcAmt - $grossCTC)) - $hra;
					$npf = round(($pfON * $ctcData->pf_per)/100);
				}
				$pfAmount = $pfAmt;
            endif;
            
            $diffAmount = $ctcAmt - $grossCTC;
			
			foreach($autoHeads as $headId => $type):
			    $dataRow[$headId]['head_amount'] = round(($diffAmount / count($autoHeads)) * $type); 
			endforeach;
            $grossEarnTotal += $diffAmount;
			
			// Final PF
			if($pfAmount > 0):				
				$dataRow['pfe'] = [
					'head_name' => "Employer Provident Fund @ 12%",
					'parent_head' => 2,
					'head_amount' => $pfAmount
				];
				
				$dataRow['pfd'] = [
					'head_name' => "Employer Provident Fund @ 12%",
					'parent_head' => 3,
					'head_amount' => round($pfAmount)
				];
				
            endif;
            
            // Calculate Profession Tax
            $professionTax = $this->salaryStructure->getProfessionTaxBaseOnGrossSalary($grossEarnTotal);
            if(!empty($professionTax)){
                $dataRow['pt'] = [
				    'head_name' => "Profession Tax",
				    'parent_head' => 3,
				    'head_amount' => $professionTax->amount
				];
                $grossDedTotal += round(($professionTax->amount * -1));
            }	
            
			foreach($dataRow as $key => $row):
			    switch($row['parent_head']){
                    case 1:
                        $grossEarn .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $grossEarn .= '<td>'.round($row['head_amount'] * 12).'</td>';
                        endif;
                        $grossEarn .= '<td>'.round($row['head_amount']).'</td></tr>';
                        break;
                    case 2:
                        $genEarn .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $genEarn .= '<td>'.round($row['head_amount'] * 12).'</td>';
                        endif;
                        $genEarn .= '<td>'.round($row['head_amount']).'</td></tr>';
                        break;
                    case 3:
                        $grossDed .= '<tr><td>'.$row['head_name'].'</td>';
                        if($postData['salary_duration'] == "M"):
                            $grossDed .= '<td>'.round(abs($row['head_amount'] * 12)).'</td>';
                        endif;
                        $grossDed .= '<td>'.round(abs($row['head_amount'])).'</td></tr>';
                        break;
                }
			endforeach;

            $netPayTotal = round($genEarnTotal+$grossEarnTotal+$grossDedTotal);
            //print_r($headAmount);exit;

            if($grossEarnTotal > 0):
                $grossEarn .= '<tr class="bg-light">';
                    $grossEarn .= '<th>Gross Salary Total</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossEarn .= '<th>'.($grossEarnTotal * 12).'</th>';
                    endif;
                    $grossEarn .= '<th>'.$grossEarnTotal.'</th>';
                $grossEarn .= '</tr>';
            endif;
            if($genEarnTotal > 0):
                $genEarn .= '<tr class="bg-light">';
                    $genEarn .= '<th>Grand Total - CTC</th>';
                    if($postData['salary_duration'] == "M"):
                        $genEarn .= '<th>'.(($genEarnTotal+$grossEarnTotal) * 12).'</th>';
                    endif;
                    $genEarn .= '<th>'.($genEarnTotal+$grossEarnTotal).'</th>';
                $genEarn .= '</tr>';
            endif;
            if($grossDedTotal > 0):
                $grossDed .= '<tr class="bg-light">';
                    $grossDed .= '<th>Gross Deduction</th>';
                    if($postData['salary_duration'] == "M"):
                        $grossDed .= '<th>'.(abs($grossDedTotal) * 12).'</th>';
                    endif;
                    $grossDed .= '<th>'.abs($grossDedTotal).'</th>';
                $grossDed .= '</tr>';
            endif;
			$netPay = '<tr class="bg-light-info">';
				$netPay .= '<th>On Hand Salary</th>';
                if($postData['salary_duration'] == "M"):
                    $netPay .= '<th>'.($netPayTotal * 12).'</th>';
                endif;
                $netPay .= '<th>'.$netPayTotal.'</th>';
			$netPay .= '</tr>';
			$ctcStructure .= $grossEarn.$genEarn.$grossDed.$netPay.'</tbody></table>';
		
			// Save CTC to Employee Master
			
			$empData = [
				'id'=>$postData['ctc_emp_id'],
				'ctc_format' => $postData['ctc_format'],
				'ctc_amount' => $postData['ctc_amount'],
				'salary_duration' => $postData['salary_duration'],
				//'month_hours' => $postData['month_hours'],
				'updated_by' => $this->loginId
			];
			$this->employee->saveEmpSalary($empData);
			
            $this->printJson(['status'=>1,'ctcStructure'=>$ctcStructure]);
        endif;
    }
	
	public function calcPF($pfON,$pf_per){
		return round(($pfON * $pf_per)/100);
	}
	
	
    // Emp Facility *Created By Meghavi @22/12/22*
    public function updateEmpFacility(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['issue_date'])){
            $errorMessage['issue_date'] = "Issue Date is required.";
        }
        if(empty($data['type'])){
            $errorMessage['type'] = "Type is required.";
        }
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->employee->saveEmpFacility($data);

            $empFacility = $this->employee->getFacilityData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empFacility)):
                $i=1;
                foreach($empFacility as $row):
                 
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' .(formatDate($row->issue_date)). '</td>
                                <td>' . $row->remark . '</td>
                                <td>' . $row->specs . '</td>
                                <td>' . $row->description . ' </td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpFacility('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }                 
    
    // Emp Facility *Created By Meghavi @22/12/22*
    public function deleteEmpFacility(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->employee->deleteEmpFacility($data['id']);
            $empFacility = $this->employee->getFacilityData($data['emp_id']);
            $tbodyData="";$i=1; 
            if(!empty($empFacility)):
                $i=1;
                foreach($empFacility as $row):
                    $tbodyData.= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . (formatDate($row->issue_date)) . '</td>
                                <td>' . $row->type . '</td>
                                <td>' . $row->specs . '</td>
                                <td>' . $row->description . ' </td>
                                <td class="text-center">
                                    <button type="button" onclick="trashEmpFacility('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function updateEmpChargeableFacility(){
        $data = $this->input->post();
        $this->printJson($this->employee->updateEmpChargeableFacility($data));
    }
    
    public function changeEmpPsw(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->changeEmpPsw($id));
        endif;
    }
    
    public function saveEmpSalaryStructure(){
        $data = $this->input->post();
        $errorMessage = array();

        if($data['ctc_format'] > 0):
            if(empty($data['ctc_amount']))
                $errorMessage['ctc_amount'] = "Amount is required.";
            /* if(empty($data['effect_start']))
                $errorMessage['effect_start'] = "Effective Date is required."; */
        endif;

        if(empty($data['salary_head_json']))
            $errorMessage['salary_head'] = "Salary Heads is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->employee->saveEmpSalaryStructure($data));
        endif;
    }
    
    public function getEmpActiveSalaryStructure(){
        $data = $this->input->post();

        $empData = $this->employee->getEmp($data['emp_id']);        
        $activeStructure = $this->employee->getActiveSalaryStructure($data['emp_id']);
        $format_id = (!empty($activeStructure))?$activeStructure->format_id:$data['format_id'];
        
        $html = '';$ctc_amount = 0;$effect_start = "";$summaryHtml = '';$sumTh='';$sumTd = '';$sumdTh='';$sumdTd = '';$sumOrgETd="";$sumOrgDTd="";
        if(!empty($format_id)):  
            $ctcFormat = $this->salaryStructure->getCtcFromat($format_id);
            $earningHeads = $this->salaryStructure->getSalaryHeadList(['ids'=>$ctcFormat->eh_ids,'type'=>1]);
            $deductionHeads = $this->salaryStructure->getSalaryHeadList(['ids'=>$ctcFormat->dh_ids,'type'=>-1]);
            $canteenCharges = $this->masterModel->getMasterOptions();
            $cl_charge = $canteenCharges->cl_charge;
            $cd_charge = $canteenCharges->cd_charge;
            $tc_charge = $empData->traveling_charge;

            $empSalarayHeads = (!empty($activeStructure->salary_head_json))?json_decode($activeStructure->salary_head_json):array();            

            $calMethodArray = ['1'=>"Amount",'2'=>"Percentage"];
            $html .= '<input type="hidden" name="salary_duration" value="'.$ctcFormat->salary_duration.'">';
            $html .= '<table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th>Earning</th>
                        <th style="width: 150px;">Type</th>
                        <th style="width: 150px;">Actual</th>
                        <th style="width: 150px;">On Paper</th>
                    </tr>
                </thead>
                <tbody>';

            foreach($earningHeads as $row):
                $readonly = ($ctcFormat->salary_duration == "H" && !empty($row->system_code))?"readonly":"";
                $html .= '<tr>
                    <td>'.$row->head_name.'</td>
                    <td>
                        <select name="salary_head_json['.$row->id.'][cal_method]" id="'.$row->id.'_cal_method" class="form-control">';
                            foreach($calMethodArray as $key => $value):
                                $selected = ((!empty($empSalarayHeads->{$row->id}->cal_method) && $empSalarayHeads->{$row->id}->cal_method == $key)?"selected":((!empty($row->system_code) && $row->system_code == "basic" && $key == 2)?"disabled":""));
                                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            endforeach;
                $html .= '</select>
                    </td>
                    <td>
                        <input type="hidden" name="salary_head_json['.$row->id.'][type]" id="'.$row->id.'_type" value="'.$row->type.'">
                        <input type="hidden" id="'.$row->id.'_system_code" value="'.$row->system_code.'">
                        <input type="text" name="salary_head_json['.$row->id.'][org_cal_value]" id="'.$row->id.'_org_cal_value" class="form-control claculateSummary orgEarningHead _org_'.$row->system_code.' floatOnly" data-id="'.$row->id.'" data-col="_org_" data-calculate="earn" value="'.((!empty($empSalarayHeads->{$row->id}->org_cal_value) && in_array($row->effect_in , [2,3]))?$empSalarayHeads->{$row->id}->org_cal_value:"").'" '.(($row->effect_in == 1)?"readonly":"").' '.$readonly.'>                        
                    </td>
                    <td>
                        <input type="text" name="salary_head_json['.$row->id.'][cal_value]" id="'.$row->id.'_cal_value" class="form-control claculateSummary earningHead _'.$row->system_code.' floatOnly" data-id="'.$row->id.'" data-col="_" data-calculate="earn" value="'.((!empty($empSalarayHeads->{$row->id}->cal_value) && in_array($row->effect_in , [1,3]))?$empSalarayHeads->{$row->id}->cal_value:"").'" '.(($row->effect_in == 2)?"readonly":"").'>
                    </td>
                </tr>';

                $sumTh .= '<th>'.$row->head_name.'</th>';
                $sumTd .= '<td class="summaryAmount" id="'.$row->id.'_summary_value">'.((!empty($empSalarayHeads->{$row->id}->cal_value))?$empSalarayHeads->{$row->id}->cal_value:"0").'</td>';
                $sumOrgETd .= '<td class="summaryOrgEarnAmount" id="'.$row->id.'_org_summary_value">0</td>';
            endforeach;

            $html .= '<tr class="thead-info">
                <th>Deduction</th>
                <th style="width: 150px;">Type</th>
                <th style="width: 150px;">Actual</th>
                <th style="width: 150px;">On Paper</th>
            </tr>';

            foreach($deductionHeads as $row):
                $readonly = ($ctcFormat->salary_duration == "H" && !empty($row->system_code))?"readonly":"";
                $displayNone = (in_array($row->system_code ,["ccl","ccd"]))?"display:none;":"";
                
                $amount = "";$orgAmount = "";
                if(in_array($row->system_code,["ccl","ccd"])):
                    if($row->system_code == "ccl" && $row->effect_in == 1): $amount = $cl_charge; 
                    elseif($row->system_code == "ccl" && $row->effect_in == 2): $orgAmount = $cl_charge; 
                    else: $amount = $orgAmount = $cl_charge; endif;

                    if($row->system_code == "ccd" && $row->effect_in == 1): $amount = $cd_charge; 
                    elseif($row->system_code == "ccd" && $row->effect_in == 2): $orgAmount = $cd_charge; 
                    else: $amount = $orgAmount = $cd_charge; endif;
                else:
                    if(!empty($empSalarayHeads->{$row->id}->cal_value) && $row->effect_in == 1):
                        $amount = $empSalarayHeads->{$row->id}->cal_value;
                    elseif(!empty($empSalarayHeads->{$row->id}->cal_value) && $row->effect_in == 2):
                        $orgAmount = (!empty( $empSalarayHeads->{$row->id}->org_cal_value))?$empSalarayHeads->{$row->id}->org_cal_value:"";
                    else:
                        $amount = (!empty( $empSalarayHeads->{$row->id}->cal_value))?$empSalarayHeads->{$row->id}->cal_value:"";
                        $orgAmount = (!empty( $empSalarayHeads->{$row->id}->org_cal_value))?$empSalarayHeads->{$row->id}->org_cal_value:"";
                    endif;
                endif;
                
                $html .= '<tr style="'.$displayNone.'">
                    <td>'.$row->head_name.'</td>
                    <td>
                        <select name="salary_head_json['.$row->id.'][cal_method]" id="'.$row->id.'_cal_method" class="form-control">';
                            foreach($calMethodArray as $key => $value):
                                $selected = ((!empty($empSalarayHeads->{$row->id}->cal_method) && $empSalarayHeads->{$row->id}->cal_method == $key)?"selected":(($row->system_code == "pf" && $key == 1)?"disabled":""));
                                $html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            endforeach;
                $html .= '</select>
                    </td>
                    <td>
                        <input type="hidden" name="salary_head_json['.$row->id.'][type]" id="'.$row->id.'_type" value="'.$row->type.'">
                        <input type="hidden" id="'.$row->id.'_system_code" value="'.$row->system_code.'">
                        <input type="text" name="salary_head_json['.$row->id.'][org_cal_value]" id="'.$row->id.'_org_cal_value" class="form-control claculateSummary orgDeductionHead _org_'.$row->system_code.' floatOnly" data-id="'.$row->id.'" data-col="_org_" data-calculate="deduct" value="'.$orgAmount.'" '.(($row->effect_in == 1)?"readonly":"").' '.$readonly.'>
                    </td>
                    <td>                        
                        <input type="text" name="salary_head_json['.$row->id.'][cal_value]" id="'.$row->id.'_cal_value" class="form-control claculateSummary deductionHead _'.$row->system_code.' floatOnly" data-id="'.$row->id.'" data-col="_" data-calculate="deduct" value="'.$amount.'" '.(($row->effect_in == 2)?"readonly":"").'>
                    </td>
                </tr>';
                
                
                $sumdTh .= '<th>'.$row->head_name.'</th>';
                $sumdTd .= '<td class="summaryDeductionAmount '.$row->system_code.'_amt" id="'.$row->id.'_summary_value">'.((!empty($empSalarayHeads->{$row->id}->cal_value))?$empSalarayHeads->{$row->id}->cal_value:"0").'</td>';
                $sumOrgDTd .= '<td class="summaryOrgDeductAmount '.$row->system_code.'_org_amt" id="'.$row->id.'_org_summary_value">0</td>';
            endforeach;

            $html .= '</tbody>
            </table>';

            $ctc_amount = (!empty($activeStructure->ctc_amount))?$activeStructure->ctc_amount:0;
            $org_ctc_amount = (!empty($activeStructure->org_ctc_amount))?$activeStructure->org_ctc_amount:0;
            $html .= '<div class="table table-responsive"><table class="table table-bordered align-items-center mt-2">
                <thead class="thead-info">
                    <tr>
                        <th>Discription</th>
                        '.$sumTh.'
                        <th>Gross Salary</th>
                        '.$sumdTh.'
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Actual</td>
                        '.$sumOrgETd.'
                        <td id="_org_gross_salary">0</td>
                        '.$sumOrgDTd.'
                        <td id="org_net_salary">0</td>
                    </tr>
                    <tr>
                        <td>On Paper</td>
                        '.$sumTd.'
                        <td id="_gross_salary">0</td>
                        '.$sumdTd.'
                        <td id="net_salary">0</td>
                    </tr>
                    <tr>
                        <th colspan="'.(count($earningHeads)+1).'">Gross Difference</th>
                        <th id="gross_diff">0</th>
                        <th colspan="'.(count($deductionHeads)).'">Net Difference</th>
                        <th id="diff">0</th>
                    </tr>
                </tbody>
            </table></div>';
        endif;

        $this->printJson(['html'=>$html,'format_id'=>$format_id,'ctc_amount'=>$ctc_amount,'org_ctc_amount'=>$org_ctc_amount]);
    }
}
?>