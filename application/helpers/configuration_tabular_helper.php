<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */
function getConfigDtHeader($page)
{
    /* terms header */
    $data['terms'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['terms'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['terms'][] = ["name"=>"Title"];
    $data['terms'][] = ["name"=>"Category"];
    $data['terms'][] = ["name"=>"Type"];
    $data['terms'][] = ["name"=>"Conditions"];
    /* Shift Header */
    $data['shift'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['shift'][] = ["name"=>"Shift Name"];
	$data['shift'][] = ["name"=>"Start Time"];
	$data['shift'][] = ["name"=>"End Time"];
	$data['shift'][] = ["name"=>"Production Time"];
	$data['shift'][] = ["name"=>"Lunch Time"];
	$data['shift'][] = ["name"=>"Shift Hour"];
    /* Holidays Header */
    $data['holidays'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['holidays'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['holidays'][] = ["name"=>"Holiday Date"];
    $data['holidays'][] = ["name"=>"Holiday Type"];
    $data['holidays'][] = ["name"=>"Title"];
    /* category Header */
    $data['category'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['category'][] = ["name"=>"Category Name"];
    $data['category'][] = ["name"=>"Over Time"];
    /* Currency Header*/
    $data['currency'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['currency'][] = ["name"=>"Currency Name"];
    $data['currency'][] = ["name"=>"Code"];
    $data['currency'][] = ["name"=>"Symbol"];
    $data['currency'][] = ["name"=>"Rate in INR"];
    
    /* Material Grade header */
    $data['materialGrade'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['materialGrade'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['materialGrade'][] = ["name"=>"Material Grade"];
    $data['materialGrade'][] = ["name"=>"Standard"];
    $data['materialGrade'][] = ["name"=>"Scrap Group"];
    $data['materialGrade'][] = ["name"=>"Colour Code"];
    /* Attendance Policy Header */
    $data['attendancePolicy'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['attendancePolicy'][] = ["name"=>"Policy Name"];
    $data['attendancePolicy'][] = ["name"=>"Early In"];
    $data['attendancePolicy'][] = ["name"=>"No. Early In"];
    $data['attendancePolicy'][] = ["name"=>"Early Out"];
    $data['attendancePolicy'][] = ["name"=>"No. Early Out"];
    $data['attendancePolicy'][] = ["name"=>"Short Leave Hour"];
    $data['attendancePolicy'][] = ["name"=>"No. Short Leave"];
    /* Main Menu Header */
    $data['mainMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['mainMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['mainMenuConf'][] = ["name"=>"Menu Icon"];
    $data['mainMenuConf'][] = ["name"=>"Menu Name"];
    $data['mainMenuConf'][] = ["name"=>"Menu Sequence"];
    $data['mainMenuConf'][] = ["name"=>"Is Master"];
    /* Sub Menu Header */
    $data['subMenuConf'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['subMenuConf'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Sequence"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Icon"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Name"];
    $data['subMenuConf'][] = ["name"=>"Sub Menu Contoller Name"];
    $data['subMenuConf'][] = ["name"=>"Main Menu"];
    $data['subMenuConf'][] = ["name"=>"Is Report"];
    /* Tax Master Header */
    $data['taxMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['taxMaster'][] = ["name" => "Tax Name"];
    $data['taxMaster'][] = ["name" => "Tax Type"];
    $data['taxMaster'][] = ["name" => "Calcu. Type"];
    $data['taxMaster'][] = ["name" => "Ledger Name"];
    $data['taxMaster'][] = ["name" => "Is Active"];
    $data['taxMaster'][] = ["name" => "Add/Deduct"];
    /* Expense Master Header */
    $data['expenseMaster'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "#", "style" => "width:5%;"];
    $data['expenseMaster'][] = ["name" => "Exp. Name"];
    $data['expenseMaster'][] = ["name" => "Entry Name"];
    $data['expenseMaster'][] = ["name" => "Sequence"];
    $data['expenseMaster'][] = ["name" => "Calcu. Type"];
    $data['expenseMaster'][] = ["name" => "Ledger Name"];
    $data['expenseMaster'][] = ["name" => "Is Active"];
    $data['expenseMaster'][] = ["name" => "Add/Deduct"];
    /* terms header */
   $data['contactDirectory'][] = ["name"=>"Action","style"=>"width:5%;"];
   $data['contactDirectory'][] = ["name"=>"#","style"=>"width:5%;"]; 
   $data['contactDirectory'][] = ["name"=>"Company Name"];
   $data['contactDirectory'][] = ["name"=>"Contact Person"];
   $data['contactDirectory'][] = ["name"=>"Contact No."];
   $data['contactDirectory'][] = ["name"=>"Email"];   
   $data['contactDirectory'][] = ["name"=>"Service"];
   $data['contactDirectory'][] = ["name"=>"Remark"];
    /* HSN Master header */
	$data['hsnMaster'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['hsnMaster'][] = ["name"=>"#","style"=>"width:5%;"]; 
	$data['hsnMaster'][] = ["name"=>"HSN Code"];
	$data['hsnMaster'][] = ["name"=>"GST Per."];
	$data['hsnMaster'][] = ["name"=>"Description"]; 
	
	/* Transport Header*/
    $data['transport'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['transport'][] = ["name"=>"Transport Name"];
    $data['transport'][] = ["name"=>"Transport ID"];
    $data['transport'][] = ["name"=>"Address"];
    /* Banking Header*/
    $data['banking'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['banking'][] = ["name"=>"Bank Name"];
    $data['banking'][] = ["name"=>"Branch Name"];
    $data['banking'][] = ["name"=>"IFSC Code"];
    $data['banking'][] = ["name"=>"Address"];
    
    /*Grade Master header */
    $data['gradeMaster'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['gradeMaster'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['gradeMaster'][] = ["name"=>"Material Grade"]; 
    $data['gradeMaster'][] = ["name"=>"Standard"]; 
    
    /*Feed Back header */
    $data['feedbackPoint'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['feedbackPoint'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['feedbackPoint'][] = ["name"=>"Discription"]; 
    
    
    /* Scrap Group Header*/
    $data['scrapGroup'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['scrapGroup'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['scrapGroup'][] = ["name"=>"Scrap Group Name"];
    /*$data['scrapGroup'][] = ["name"=>"Category Name"];*/
    $data['scrapGroup'][] = ["name"=>"Unit Name"];
    
    /* Master Detail Header*/
    $data['masterDetail'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['masterDetail'][] = ["name"=>"#","style"=>"width:5%;"];
    //$data['masterDetail'][] = ["name"=>"Code"];
    $data['masterDetail'][] = ["name"=>"Title"];
    
	return tableHeader($data[$page]);
}
/* Terms Table Data */
function getTermsData($data){
    $deleteParam = $data->id.",'Terms'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
    $category = (!empty($data->default_terms))? 'General':'Commercial';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title,$category,str_replace(',',', ',$data->type),$data->conditions];
}
/* get Shift Data */
function getShiftData($data){
    $deleteParam = $data->id.",'Shift'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Shift'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->shift_name,$data->shift_start,$data->shift_end,$data->production_hour,$data->total_lunch_time,$data->total_shift_time];
}
/* Currency Data */
function getCurrencyData($data){
    return [$data->sr_no,$data->currency_name,$data->currency,$data->code2000,$data->inrinput];
}
  
/* Material Grade Table Data */
function getMaterialData($data){
    $deleteParam = $data->id.",'Material Grade'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Material Grade'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $insParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'TC Parameter', 'fnEdit' : 'getInspectionParam', 'fnsave' : 'saveInspectionParam'}";
    $insParamButton = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="edit('.$insParam.');"><i class="fa fa-file" ></i></a>';
	$action = getActionButton($insParamButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,'['.$data->metal_code.'] '.$data->material_grade,$data->standard,$data->group_name,$data->color_code];
}
/* get Attendance Policy Data */
function getAttendancePolicyData($data){
    $deleteParam = $data->id.",'Attendance Policy'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAttendancePolicy', 'title' : 'Update Attendance Policy'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->policy_name,$data->early_in,$data->no_early_in,$data->early_out,$data->no_early_out,$data->short_leave_hour,$data->no_short_leave];
}
/* Main Menu Table Data */
function getMainMenuConfData($data){
    $deleteParam = $data->id.",'MainMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editmainMenuConf', 'title' : 'Update MainMenuConf'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->menu_icon,$data->menu_name,$data->menu_seq,$data->is_master];
}
/* Sub Menu Table Data */
function getSubMenuConfData($data){
    $deleteParam = $data->id.",'SubMenuConf'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editsubMenuConf', 'title' : 'Update SubMenuConf'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$isReport=($data->is_report == 0)?"No":"Yes";
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->sub_menu_seq,$data->sub_menu_icon,$data->sub_menu_name,$data->sub_controller_name,$data->menu_name,$isReport];
}
/* Expense Master Table Data */
function getExpenseMasterData($data){
    $deleteParam = $data->id.",'Expense'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editExpense', 'title' : 'Update Expense'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->exp_name,$data->entry_name,$data->seq,$data->calc_type_name,$data->party_name,$data->is_active_name,$data->add_or_deduct_name];
}
function getTaxMasterData($data){
    $deleteParam = $data->id.",'Tax'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editTax', 'title' : 'Update Tax'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->name,$data->tax_type_name,$data->calc_type_name,$data->acc_name,$data->is_active_name,$data->add_or_deduct_name];
}
function getContactDirectoryData($data){
    $deleteParam = $data->id.",'Contact'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editTerms', 'title' : 'Update Terms'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->comapny_name,$data->contact_person,$data->contact_number,$data->email,$data->service,$data->remark];
}
/* HSN Master Table Data */
function getHSNMasterData($data){
    $deleteParam = $data->id.",'HSN Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editHsnMaster', 'title' : 'HSN Master'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->hsn_code,$data->gst_per,$data->description];
}
/* Transport Data */
function getTransportData($data){
	$deleteParam = $data->id.",'Transport'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Transport'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->transport_name,$data->transport_id,$data->address];
}
/* Banking Data */
function getBankingData($data){
	$deleteParam = $data->id.",'Banking'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editShift', 'title' : 'Update Banking Details'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->bank_name,$data->branch_name,$data->ifsc_code,$data->address];
}
/* get Category Data */
function getCategoryData($data){
    $deleteParam = $data->id.",'Category'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editCategory', 'title' : 'Update Employee Category'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->category,$data->overtime];
}
/* Grade Master Table Data */
function getGradeMasterData($data){
    $deleteParam = $data->id.",'Grade Master'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'edit', 'title' : 'Update Grade Master'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->material_grade,$data->standard];
}

/* Feedback Point Table Data */
function getFeedbackPointData($data){
    $deleteParam = $data->id.",'Feedback Point'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Feedback Point'}";
    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
   
    return [$action,$data->sr_no,$data->parameter];
}

/* Scrap Group Data */
function getScrapGroupData($data){
	$deleteParam = $data->id.",'Scrap Group'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editScrap', 'title' : 'Update Scrap Group'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_name,$data->unit_name];
}

/* Master Detail Data */
function getMasterDetailData($data){
	$deleteParam = $data->id.",'Master Detail'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editMasterDetail', 'title' : 'Update Master Detail'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->title];
}
?>