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
    $data['employees'][] = ["name"=>"Employee Code","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Department"];
    $data['employees'][] = ["name"=>"Designation"];
    //$data['employees'][] = ["name"=>"Category","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Shift","textAlign"=>'center'];
    $data['employees'][] = ["name"=>"Contact No.","textAlign"=>'center'];
    //$data['employees'][] = ["name"=>"Attnd. Status","textAlign"=>'center'];
    
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
	$data['extraHours'][] = ["name"=>"Emp Code"];
	$data['extraHours'][] = ["name"=>"Employee"];
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
    $data['advanceSalary'][] = ["name"=>"Date"];
    $data['advanceSalary'][] = ["name"=>"Employee Name"];
    $data['advanceSalary'][] = ["name"=>"Demand Amount"];
    $data['advanceSalary'][] = ["name"=>"Demand Reason"];
    $data['advanceSalary'][] = ["name"=>"Sanctioned By"];
    $data['advanceSalary'][] = ["name"=>"Sanctioned At"];
    $data['advanceSalary'][] = ["name"=>"Sanctioned Amount"];
    $data['advanceSalary'][] = ["name"=>"Deposit Amount"];
    $data['advanceSalary'][] = ["name"=>"Pending Amount"];
    
    /* Employee Loan Karmi */
    $data['empLoan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['empLoan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['empLoan'][] = ["name"=>"Loan Date"];
    $data['empLoan'][] = ["name"=>"Loan No."];
    $data['empLoan'][] = ["name"=>"Employee Name"];
    $data['empLoan'][] = ["name"=>"Amount"];
    $data['empLoan'][] = ["name"=>"reason"];
    
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
    $data['skillMaster'][] = ["name"=>"Skill"];
    $data['skillMaster'][] = ["name"=>"Department"];
    $data['skillMaster'][] = ["name"=>"Designation"];
    $data['skillMaster'][] = ["name"=>"Req. Per(%)"];
    
    /* CTC Format Header */
    $data['ctcFormat'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE"];
    $data['ctcFormat'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['ctcFormat'][] = ["name"=>"Format Name"];
    $data['ctcFormat'][] = ["name"=>"Format No"];
    $data['ctcFormat'][] = ["name"=>"Salary Duration"];
    $data['ctcFormat'][] = ["name"=>"Gratuity Days"];
    $data['ctcFormat'][] = ["name"=>"Gratuity(%)"];
    $data['ctcFormat'][] = ["name"=>"Effect From"];

    /* Salary Heads Header */
    $data['salaryHead'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['salaryHead'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['salaryHead'][] = ["name"=>"Salary Head"];
    $data['salaryHead'][] = ["name"=>"Type"];

    /* Employee Facility Header */
    $data['employeeFacility'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['employeeFacility'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['employeeFacility'][] = ["name"=>"Facility Type"];
    $data['employeeFacility'][] = ["name"=>"Returnable"];
    
    /* Panelty Meghavi */
    $data['penalty'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['penalty'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['penalty'][] = ["name"=>"Name"];
    $data['penalty'][] = ["name"=>"Date"];
    $data['penalty'][] = ["name"=>"Amount"];
    $data['penalty'][] = ["name"=>"reason"];

    /* Facility Meghavi */
    $data['facility'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['facility'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['facility'][] = ["name"=>"Date"];
    $data['facility'][] = ["name"=>"Name"];
    $data['facility'][] = ["name"=>"Facility Type"];
    $data['facility'][] = ["name"=>"Amount"];
    $data['facility'][] = ["name"=>"Size"];
    
    /* Employee Recruitment */
    $data['empRecruitment'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['empRecruitment'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>'center']; 
    $data['empRecruitment'][] = ["name"=>"Employee Name"];
    $data['empRecruitment'][] = ["name"=>"Category"];
    $data['empRecruitment'][] = ["name"=>"Department"];
    $data['empRecruitment'][] = ["name"=>"Designation"];
    $data['empRecruitment'][] = ["name"=>"Education","textAlign"=>'center'];
    $data['empRecruitment'][] = ["name"=>"Experience","textAlign"=>'center'];
    $data['empRecruitment'][] = ["name"=>"Contact No.","textAlign"=>'center'];
    
    return tableHeader($data[$page]);
}

/* Department Table Data */
function getDepartmentData($data){
    $deleteParam = $data->id.",'Department'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Department'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->name,$data->remark];
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
    
    if($data->is_active == 1)
    {
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 0}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="changeActiveStatus('.$data->id.',0);"><i class="fa fa-ban"></i></a>';    
        $addInDevice = '<a class="btn btn-dark addInDevice permission-modify" href="javascript:void(0)" datatip="Device" data-id="'.$data->id.'" data-button="close" data-modal_id="modal-lg" data-function="addEmployeeInDevice" data-form_title="Add Employee In Device" flow="down"><i class="fa fa-desktop"></i></a>';
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $empName = '<a href="'.base_url("hr/employees/empProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';
    }
    else{
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 1}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="changeActiveStatus('.$data->id.',1);"><i class="fa fa-check"></i></a>';    
        $empName = $data->emp_name;
    }
    
    $resetPsw='';
    if($data->loginId == 1):
        $resetParam = $data->id.",'".$data->emp_name."'";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="changeEmpPsw('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    $addInDevice=$empRelieveBtn='';
    $action = getActionButton($resetPsw.$addInDevice.$activeButton.$empRelieveBtn.$editButton);
    $attStatus = (!empty($data->attendance_type)) ? 'Yes' : 'No';
    return [$action,$data->sr_no,$data->emp_name,$data->emp_code,$data->dept_name,$data->emp_designation,$data->shift_name,$data->emp_contact];
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
    $deleteParam = $data->id.",'Extra Hours'";$approveButton='';$editButton='';$deleteButton='';
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editExtraHours', 'title' : 'Extra Hours'}";
    
    if($data->approved_by == 0):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        
        if($data->approvalAuth == 1 OR $data->loginID == 1):
            $approveButton = '<a class="btn btn-info permission-modify approveXHRS" href="javascript:void(0)" data-id="'.$data->id.'" datatip="Approve EX. Hours" flow="down"><i class="ti-check"></i></a>';
    	endif;
    	
    endif;
    
    
	$action = getActionButton($approveButton.$editButton.$deleteButton);
    $punch_date = str_pad($data->ex_hours,2,"0",STR_PAD_LEFT).":".str_pad($data->ex_mins,2,"0",STR_PAD_LEFT);
    $punch_date = ($data->xtype < 0 ) ? '<strong class="text-danger">'.$punch_date.'</strong>' : $punch_date;
    return [$action,$data->sr_no,$data->emp_code,$data->emp_name,date('d-m-Y',strtotime($data->punch_date)),$punch_date,$data->remark];
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
    $deleteParam = "'".$data->month."','Payroll'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editDepartment', 'title' : 'Update Payroll'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('hr/payroll/edit/'.$data->month).'" datatip="Edit" flow="down"><i class="ti-pencil-alt" ></i></a>';
    //$editButton = '';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
	$mnth = '<a href="'.base_url('hr/payroll/getPayrollData/'.$data->month).'" target="_blank">'.date("F-Y",strtotime($data->month)).'</a>';
    return [$action,$data->sr_no,date("F-Y",strtotime($data->month)),$data->salary_sum];
}

// meghavi
function getAdvanceSalaryData($data){
    $deleteParam = $data->id.",'Student'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editAdvanceSalary', 'title' : 'Update Advance Salary'}";
    $sanctionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'sanctionAdvance', 'title' : 'Sanction Advance','fnEdit':'sanctionAdvance'}";

    $editButton = '';$deleteButton = '';$sanction = '';
    if(empty($data->sanctioned_by)):
        $editButton = '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $sanction = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Sanction Advance" flow="down" onclick="edit('.$sanctionParam.');"><i class="ti-check" ></i></a>';
    endif;
	
    $action = getActionButton($sanction.$editButton.$deleteButton);
    
    return [$action,$data->sr_no,formatDate($data->entry_date),'['.$data->emp_code.'] '.$data->emp_name,floatVal($data->amount),$data->reason,$data->sanctioned_by_name,((!empty($data->sanctioned_at))?formatDate($data->sanctioned_at):""),floatVal($data->sanctioned_amount),floatVal($data->deposit_amount),floatVal($data->pending_amount)];
}


function getEmpLoanData($data){
    $deleteParam = $data->id.",'Emp Loan'";$editButton ="";$deleteButton =""; $printBtn =""; $approveButton="";$senctionBtn="";
    if(empty($data->trans_status)){
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editLoan', 'title' : 'Update Loan'}";
        $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $approveParam = "{'id' : " . $data->id . ",'approve_type' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'loanApproval', 'title' : 'Loan Approval','fnsave' : 'saveLoanApproval', 'savebtn_text':'Approve'}";  
        $approveButton = '<a class="btn btn-warning approveLoan permission-approve1" href="javascript:void(0)" data-modal_id="modal-lg"  datatip="Approve" flow="down" onclick="edit('.$approveParam.')"><i class="fas fa-check"></i></a>';
    
    }else{
        $printBtn = '<a class="btn btn-success btn-edit permission-approve1" href="'.base_url('hr/empLoan/printLoan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    }
    if($data->trans_status == 1){
        $senctionParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'loanApproval', 'title' : 'Senction','fnsave' : 'saveLoanSenction', 'savebtn_text':'Approve','fnEdit':'loanSenction'}";  
        $senctionBtn = '<a class="btn btn-warning permission-approve" href="javascript:void(0)" data-modal_id="modal-lg"  datatip="Senction" flow="down" onclick="edit('.$senctionParam.')"><i class="fas fa-check"></i></a>';

       
    }
    
    $action = getActionButton($senctionBtn.$approveButton.$printBtn.$editButton.$deleteButton);
    
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->entry_date),'['.$data->emp_code.'] '.$data->emp_name,$data->demand_amount,$data->reason];
}

function getEmpRelievedData($data){
    $deleteParam = $data->id.",'Employee'";
    
    $emprejoinParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'empEdu', 'title' : 'Employee Rejoin', 'fnEdit' : 'empRejoin', 'fnsave' : 'saveEmpRelieve' ,'button' : 'both'}";

    
    $empRejoinBtn='<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Rejoin" flow="down" onclick="edit('.$emprejoinParam.');"><i class="ti-reload" ></i></a>';
   
   
    $empName = '<a href="'.base_url("hr/employees/empProfile/".$data->id).'" datatip="View Profile" flow="down">'.$data->emp_name.'</a>';

    $action = getActionButton($empRejoinBtn);
    return [$action,$data->sr_no,$empName,$data->emp_code,$data->emp_contact,$data->name,$data->title];
}

/* Skill Master Table Data */
function getSkillMasterData($data){
    $deleteParam = $data->id.",'Skill Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'edit', 'title' : 'Update Skill Master '}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->skill,$data->name,$data->title,$data->req_per];
}

function getCtcFormatData($data){
    $deleteParam = $data->id.",'CTC Format'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'edit', 'title' : 'Update CTC Format'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	
    /* $salaryHeadParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'salaryHeads', 'title' : 'Salary Heads', 'fnEdit' : 'getSalaryheads', 'button' : 'close'}";
    $salaryButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Salary Heads" flow="down" onclick="edit('.$salaryHeadParam.');"><i class="sl-icon-bag"></i></a>'; */
	
	
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->format_name,$data->format_no,$data->salary_duration_text,$data->gratuity_days,$data->gratuity_per,formatDate($data->effect_from)];
}

function getSalaryHeadData($data){
    $deleteParam = $data->id.",'Salary Head','deleteSalaryHead'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSalaryHead', 'title' : 'Update Salary Head','fnEdit':'editSalaryHead','fnsave':'saveSalaryHead'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '';
    if($data->is_system == 0):
        $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->head_name,$data->type_text];
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

// meghavi
function getPenaltyData($data){ 
    $deleteParam = $data->id.",'Penalty'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPenalty', 'title' : 'Update Penalty','fnsave' : 'savePenalty','fnEdit' : 'editPenalty'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    $action = getActionButton($editButton.$deleteButton);
    
    return [$action,$data->sr_no,'['.$data->emp_code.'] '.$data->emp_name,formatDate($data->entry_date),$data->amount,$data->reason];
}

// meghavi
function getFacilityData($data){ 
    $deleteParam = $data->id.",'Facility'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editFacility', 'title' : 'Update Facility','fnsave' : 'saveFacility','fnEdit' : 'editFacility'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    $action = getActionButton($editButton.$deleteButton);
    
    return [$action,$data->sr_no,formatDate($data->entry_date),'['.$data->emp_code.'] '.$data->emp_name,$data->ficility_type,$data->amount,$data->reason];
}

/* Employee Recruitment Table Data */
function getEmpRecruitmenteData($data){
    $deleteParam = $data->id.",'Employee'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editEmployee', 'title' : 'Update Employee'}";
    if($data->status == 1)
    {
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 0}";
        $activeButton = '<a class="btn btn-youtube permission-modify" href="javascript:void(0)" datatip="De-Active" flow="down" onclick="changeActiveStatus('.$data->id.',0);"><i class="fa fa-ban"></i></a>';    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    else{
        $activeParam = "{'id' : ".$data->id.", 'is_active' : 1}";
        $activeButton = '<a class="btn btn-success permission-remove" href="javascript:void(0)" datatip="Active" flow="down" onclick="changeActiveStatus('.$data->id.',1);"><i class="fa fa-check"></i></a>';    
    }
    
    $resetPsw='';
    if($data->loginId == 1):
        $resetParam = $data->id.",'".$data->emp_name."'";
        $resetPsw='<a class="btn btn-danger" href="javascript:void(0)" onclick="changeEmpPsw('.$resetParam.');" datatip="Reset Password" flow="down"><i class="fa fa-key"></i></a>';
    endif;
    
    $experience = (!empty($data->emp_experience)) ? $data->emp_experience.' <small>Months</small>' : '';
    
    $action = getActionButton($activeButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->emp_name,$data->emp_category,$data->dept_name,$data->emp_designation,$data->emp_education,$experience,$data->emp_contact];
}

?>