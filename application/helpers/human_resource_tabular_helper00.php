<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getHrDtHeader($page){
    /* Department Header */
    $data['departments'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
	$data['departments'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['departments'][] = ["name"=>"Department Name"];
    $data['departments'][] = ["name"=>"Remark"];

    /* Designation Header */
    $data['designation'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
	$data['designation'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['designation'][] = ["name"=>"Designation Name"];
    $data['designation'][] = ["name"=>"Remark"];

    /* Employee Header */
    $data['employees'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['employees'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>'center']; 
    $data['employees'][] = ["name"=>"Employee Name"];
    $data['employees'][] = ["name"=>"Emp Code","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Department"];
    $data['employees'][] = ["name"=>"Designation"];
    $data['employees'][] = ["name"=>"Category","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Shift","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Contact No.","textAlign"=>'center'];

    /* Manual Attendance Header */
    $data['manualAttendance'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['manualAttendance'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['manualAttendance'][] = ["name"=>"Emp Code"];
    $data['manualAttendance'][] = ["name"=>"Employee"];
    $data['manualAttendance'][] = ["name"=>"Punch Time"];
    $data['manualAttendance'][] = ["name"=>"Reason"];
    
    /* Extra Hours Header */
    $data['extraHours'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['extraHours'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['extraHours'][] = ["name"=>"Employee"];
	$data['extraHours'][] = ["name"=>"Emp Code"];
	$data['extraHours'][] = ["name"=>"Date"];
    $data['extraHours'][] = ["name"=>"Extra Hours"];
    $data['extraHours'][] = ["name"=>"Reason"];

    /* Leave Setting Header */
    $data['leaveSetting'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['leaveSetting'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['leaveSetting'][] = ["name"=>"Leave Type"];
    $data['leaveSetting'][] = ["name"=>"Remark"];
    
    /* Leave Header */
    $data['leave'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['leave'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['leave'][] = ["name"=>"Employee"];
    $data['leave'][] = ["name"=>"Emp Code"];
    $data['leave'][] = ["name"=>"Leave Type"];
    $data['leave'][] = ["name"=>"From"];
    $data['leave'][] = ["name"=>"To"];
    $data['leave'][] = ["name"=>"Leave Days"];
    $data['leave'][] = ["name"=>"Reason"];
    $data['leave'][] = ["name"=>"Status"];
    
    /* Leave Approve Header */
    $data['leaveApprove'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['leaveApprove'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['leaveApprove'][] = ["name"=>"Employee"];
    $data['leaveApprove'][] = ["name"=>"Emp Code"];
    $data['leaveApprove'][] = ["name"=>"Leave Type"];
    $data['leaveApprove'][] = ["name"=>"From"];
    $data['leaveApprove'][] = ["name"=>"To"];
    $data['leaveApprove'][] = ["name"=>"Leave Days"];
    $data['leaveApprove'][] = ["name"=>"Reason"];
    $data['leaveApprove'][] = ["name"=>"Status"];

    /* HR Payroll*/
    $data['payroll'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['payroll'][] = ["name"=>"Month"];
    $data['payroll'][] = ["name"=>"Total Employees"];
    $data['payroll'][] = ["name"=>"Salary Amount"];
    
    /* Advance Salary Meghavi */
    $data['advanceSalary'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['advanceSalary'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['advanceSalary'][] = ["name"=>"Name"];
    $data['advanceSalary'][] = ["name"=>"Date"];
    $data['advanceSalary'][] = ["name"=>"Amount"];
    $data['advanceSalary'][] = ["name"=>"reason"];
    
    
    /* Relieved Employee Header */
    $data['relievedEmployee'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['relievedEmployee'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['relievedEmployee'][] = ["name"=>"Employee Name"];
    $data['relievedEmployee'][] = ["name"=>"Emp Code."];
    $data['relievedEmployee'][] = ["name"=>"Contact No."];
    $data['relievedEmployee'][] = ["name"=>"Department"];

    /* Skill Master Header */
    $data['skillMaster'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
    $data['skillMaster'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['skillMaster'][] = ["name"=>"Department"];
    $data['skillMaster'][] = ["name"=>"Skill"];
    
    /* Salary Structure Header */
    $data['salaryStructure'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
    $data['salaryStructure'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['salaryStructure'][] = ["name"=>"Format Name"];
    $data['salaryStructure'][] = ["name"=>"Format No"];
    $data['salaryStructure'][] = ["name"=>"Salary Duration"];
    $data['salaryStructure'][] = ["name"=>"PF Status"];
    $data['salaryStructure'][] = ["name"=>"PF(%)"];
    $data['salaryStructure'][] = ["name"=>"Gratuity Days"];
    $data['salaryStructure'][] = ["name"=>"Gratuity(%)"];
    $data['salaryStructure'][] = ["name"=>"Effect From"];

    /* Employee Facility Header */
    $data['employeeFacility'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['employeeFacility'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['employeeFacility'][] = ["name"=>"Facility Type"];
    $data['employeeFacility'][] = ["name"=>"Returnable"];
    
    return tableHeader($data[$page]);
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = $data->id.",'Department'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Department'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->description];
}

/* Designation Table Data */
function getDesignationData($data){
    $deleteParam = $data->id.",'Designation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDesignation', 'title' : 'Update Designation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$data->description];
}

/* Employee Table Data */
function getEmployeeData($data){
    $deleteParam = $data->id.",'Employee'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editEmployee', 'title' : 'Update Employee'}";
    $emprelieveParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'empEdu', 'title' : 'Employee Relieve', 'fnEdit' : 'empRelive', 'fnsave' : 'saveEmpRelieve' ,'button' : 'both'}";
    $addInDevice = '';$activeButton = '';$empRelieveBtn = '';$editButton = '';$deleteButton = '';
    
    $empRelieveBtn = '<a class="btn btn-dark btn-edit permission-remove" href="javascript:void(0)" datatip="Relieve" flow="down" onclick="edit('.$emprelieveParam.');"><i class="ti-close" ></i></a>';
    
    if($data->is_active == 1){
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 0}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="changeActiveStatus('.$data->id.',0);"><i class="fa fa-ban"></i></a>';    
        $addInDevice = '<a class="btn btn-dark addInDevice permission-modify" href="javascript:void(0)" datatip="Device" data-id="'.$data->id.'" data-button="close" data-modal_id="modal-lg" data-function="addEmployeeInDevice" data-form_title="Add Employee In Device" flow="down"><i class="fa fa-desktop"></i></a>';
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $empName = '<a href="'.base_url("hr/employees/empProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';
    }else{
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 1}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="changeActiveStatus('.$data->id.',1);"><i class="fa fa-check"></i></a>';    
        $empName = $data->emp_name;
    }
    
    $resetPsw='';
    if($data->loginId == 281):
        $resetParam = $data->id.",'".$data->emp_name."'";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="changeEmpPsw('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    $addInDevice = '';
    $action = getActionButton($resetPsw.$addInDevice.$activeButton.$empRelieveBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$empName,$data->emp_code,$data->dept_name,$data->emp_designation,$data->emp_category,$data->shift_name,$data->emp_contact];
}

/* Manual Attendance Table Data */
function getManualAttendanceData($data){
    $deleteParam = $data->id.",'Manual Attendance'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'button' : 'close', 'form_id' : 'addManualAttendance', 'title' : 'Manual Attendance'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    //$punchin = (!empty($data->punch_in)) ? formatDate($data->punch_in, 'd-m-Y H:i:s') : "";
    return [$action,$data->sr_no, $data->emp_code, $data->emp_name ,formatDate($data->punch_date, 'd-m-Y H:i:s'),$data->remark];
}

function getExtraHoursData($data){
    $deleteParam = $data->id.",'Extra Hours'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editExtraHours', 'title' : 'Extra Hours'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    $punch_date = str_pad($data->ex_hours,2,"0",STR_PAD_LEFT).":".str_pad($data->ex_mins,2,"0",STR_PAD_LEFT);
    $punch_date = ($data->xtype < 0 ) ? '<strong class="text-danger">'.$punch_date.'</strong>' : $punch_date;
    return [$action,$data->sr_no,$data->emp_name ,$data->emp_code,date('d-m-Y',strtotime($data->punch_date)),$punch_date,$data->remark];
}

/* Leave Setting Table Data */
function getLeaveSettingData($data){
    $deleteParam = $data->id.",'Leave Type'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editLeaveType', 'title' : 'Update Leave Type'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->leave_type,$data->remark];
}

/* Leave Table Data */
function getLeaveData($data){
    $deleteParam = $data->id.",'Leave'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editLeave', 'title' : 'Update Leave'}";
    $editButton = '';$deleteButton = '';$approveButton = '';
    //if($data->approve_status == 0 AND strtotime($data->end_date) >= strtotime(date('Y-m-d'))){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    //}
    if($data->showLeaveAction){
        $approveButton = '<a class="btn btn-warning btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" datatip="Leave Action" flow="down"><i class="ti-direction-alt"></i></a>';
    }
    
    $start_date = date('d-m-Y',strtotime($data->start_date));
    $end_date = date('d-m-Y',strtotime($data->end_date));
    $total_days = $data->total_days.' Days';
    
    if(!empty($data->type_leave) && $data->type_leave == 'SL'){
        $start_date = date('d-m-Y H:i',strtotime($data->start_date));
        $end_date = date('d-m-Y H:i',strtotime($data->end_date));
        $hours = intval($data->total_days/60);
        $mins = intval($data->total_days%60);
        $total_days = sprintf('%02d',$hours).':'.sprintf('%02d',$mins).' Hours';
    }
    
	$action = getActionButton( $approveButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,$start_date,$end_date,$total_days,$data->leave_reason,$data->status];
}

/* Leave Approve Table Data */
function getLeaveApproveData($data){
    $approveButton='';
    if($data->approval_type == 1)
    {
        if($data->approve_status == 0 AND (in_array($data->loginId,explode(',',$data->fla_id))))
        {
            $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-emp_id="'.$data->emp_id.'" data-type_leave="'.$data->type_leave.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" data-created_at="'.date("Y-m-d",strtotime($data->created_at)).'" data-approve_status="'.$data->approve_status.'" datatip="Leave Action" flow="down"><i class="ti-check"></i></a>';
        }
    }
    /*else
    {
        if($data->approve_status == 0 AND (in_array($data->loginId,explode(',',$data->pla_id))))
        {
            $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-emp_id="'.$data->emp_id.'" data-type_leave="'.$data->type_leave.'" data-la="'.$data->leave_authority.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" data-created_at="'.date("Y-m-d",strtotime($data->created_at)).'" data-approve_status="'.$data->approve_status.'" datatip="Leave Action" flow="down"><i class="ti-loop"></i></a>';
        }
        if($data->approve_status == 1 AND (in_array($data->loginId,explode(',',$data->fla_id))))
        {
            $approveButton = '<a class="btn btn-success btn-leaveAction permission-modify" href="javascript:void(0)" data-id="'.$data->id.'" data-emp_id="'.$data->emp_id.'" data-type_leave="'.$data->type_leave.'" data-la="'.$data->leave_authority.'" data-min_date="'.date("Y-m-d",strtotime($data->start_date)).'" data-created_at="'.date("Y-m-d",strtotime($data->created_at)).'" data-approve_status="'.$data->approve_status.'" datatip="Leave Action" flow="down"><i class="ti-loop"></i></a>';
        }
    }*/
	
	$start_date = date('d-m-Y',strtotime($data->start_date));
    $end_date = date('d-m-Y',strtotime($data->end_date));
    $total_days = $data->total_days.' Days';
    
    if(!empty($data->type_leave) && $data->type_leave == 'SL'){
        $start_date = date('d-m-Y H:i',strtotime($data->start_date));
        $end_date = date('d-m-Y H:i',strtotime($data->end_date));
        $hours = intval($data->total_days/60);
        $mins = intval($data->total_days%60);
        $total_days = sprintf('%02d',$hours).':'.sprintf('%02d',$mins).' Hours';
    }
    
	$action = getActionButton($approveButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->leave_type,$start_date,$end_date,$total_days,$data->leave_reason,$data->status];
}

/* Payroll Table Data */
function getPayrollData($data){
    $deleteParam = $data->id.",'Payroll'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Payroll'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
	$mnth = '<a href="'.base_url('hr/payroll/getPayrollData/'.$data->month).'" target="_blank">'.date("F-Y",strtotime($data->month)).'</a>';
    return [$action,$data->sr_no,$mnth,$data->salary_sum];
}

// meghavi
function getAdvanceSalaryData($data){
    $deleteParam = $data->id.",'Student'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editStudent', 'title' : 'Update Student'}";
	
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    $action = getActionButton($editButton.$deleteButton);
    
    return [$action,$data->sr_no,'['.$data->emp_code.'] '.$data->emp_name,formatDate($data->date),$data->amount,$data->reason];
}

function getEmpRelievedData($data){
    $deleteParam = $data->id.",'Employee'";
    
    $emprejoinParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'empEdu', 'title' : 'Employee Rejoin', 'fnedit' : 'empRejoin', 'fnsave' : 'saveEmpRelieve' ,'button' : 'both'}";

    
    $empRejoinBtn='<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Rejoin" flow="down" onclick="edit('.$emprejoinParam.');"><i class="ti-reload" ></i></a>';
   
   
    $empName = '<a href="'.base_url("hr/employees/empProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';

    $action = getActionButton($empRejoinBtn);
    return [$action,$data->sr_no,$empName,$data->emp_code,$data->emp_contact,$data->name,$data->title];
}

/* Skill Master Table Data */
function getSkillMasterData($data){
    $deleteParam = $data->id.",'Skill Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Skill Master '}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->skill];
}

function getSalaryStructureData($data){
    $deleteParam = $data->id.",'CTC Format'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'edit', 'title' : 'Update CTC Format'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	
    $salaryHeadParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'salaryHeads', 'title' : 'Salary Heads', 'fnedit' : 'getSalaryheads', 'button' : 'close'}";
    $salaryButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Salary Heads" flow="down" onclick="edit('.$salaryHeadParam.');"><i class="sl-icon-bag"></i></a>';
	
	$salary_duration = ($data->salary_duration == "M")?"Monthly":"Hourly";
	$pf_status = ($data->pf_status == 1)?"Applicable":"Not Applicable";
	
	$action = getActionButton($salaryButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->format_name,$data->format_no,$salary_duration,$pf_status,$data->pf_per,$data->gratuity_days,$data->gratuity_per,formatDate($data->effect_from)];
}

/* Employee Facility Table Data */
function getEmployeeFacilityData($data){ 
    $deleteParam = $data->id.",'EmployeeFacility'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editEmployeeFacility', 'title' : 'Update EmployeeFacility'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $is_returnable ="";
    if($data->is_returnable == 1) { $is_returnable = "Yes"; }
    else { $is_returnable =  "No"; }
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->ficility_type,$is_returnable];
}
?>