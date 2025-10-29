	
<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getMaintenanceDtHeader($page)
{	   
    /* Machine Header */
    $data['machines'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['machines'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['machines'][] = ["name"=>"Description <br>of Machine"];
    $data['machines'][] = ["name"=>"Code No."];
    $data['machines'][] = ["name"=>"Make/Model"];
    $data['machines'][] = ["name"=>"Capacity"];
    $data['machines'][] = ["name"=>"Installation Year"];
    $data['machines'][] = ["name"=>"Location"];
    $data['machines'][] = ["name"=>"Preventive <br>Maintanance?"];
    $data['machines'][] = ["name"=>"Process"];

    /* Machine Ticket Header */
    $data['machineTicket'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['machineTicket'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['machineTicket'][] = ["name"=>"Machine No."];
	$data['machineTicket'][] = ["name"=>"Trans. No."];
	$data['machineTicket'][] = ["name"=>"Problem Title"];
	$data['machineTicket'][] = ["name"=>"Problem Date"];
	$data['machineTicket'][] = ["name"=>"Solution"];
	$data['machineTicket'][] = ["name"=>"Solution Date"];

    
    /* Machine Activities Header */
    $data['machineActivities'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['machineActivities'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['machineActivities'][] = ["name"=>"Machine Activities"];

    

    return tableHeader($data[$page]);
}

/* Machine Activities Data  */
function getMachineActivitieseData($data){
    $deleteParam = $data->id.",'Machine Activities'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editMachineActivities', 'title' : 'Update Machine Activities'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->activities];
}
/* Machine Table Data */
function getMachineData($data){
    $deleteParam = $data->id.",'Machine'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editMachine', 'title' : 'Update Machine'}";
    $activityParam = "{'machine_id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'machine_activity', 'title' : 'Preventive Maintanance Checklist'}";

    $activityButton = '';
    if($data->prev_maint_req == 'Yes')
        $activityButton = '<a class="btn btn-info btn-activity permission-modify" href="javascript:void(0)" datatip="Machine Activity" flow="down" onclick="setActivity('.$activityParam.');"><i class="fa fa-check-square"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $reportButton = '<a href="'.base_url('machines/machineReport/'.$data->id).'" type="button" class="btn btn-primary" datatip="Report" flow="down"><i class="icon-Receipt-3"></i></a>';
    
    $pdfButton = '<a href="'.base_url('machines/machineReport_pdf/'.$data->id).'" type="button" class="btn btn-facebook" datatip="Machine Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';

	$action = getActionButton($activityButton.$reportButton.$pdfButton.$editButton.$deleteButton);
	
    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->make_brand,$data->size,$data->install_year,$data->location,$data->prev_maint_req,$data->process_name];
}

/* Machine Ticket Data */
function getMachineTicketData($data){
    $deleteParam = $data->id.",'Machine Ticket'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editMachineTicket', 'title' : 'Update Machine Ticket'}";
    $solutionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'machineSolution', 'title' : 'Machine Solution', 'fnEdit' : 'getMachineSolution', 'fnsave' : 'saveMachineSolution'}";

    $solutionButton = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Machine Solution" flow="down" onclick="edit('.$solutionParam.');"><i class="fa fa-check"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($solutionButton.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->item_name,$data->trans_prefix.$data->trans_no,$data->problem_title,formatDate($data->problem_date),$data->solution_detail,formatDate($data->solution_date)];
}

/* Machine Activities Data  */
function getMachineActivitiesData($data){
    $deleteParam = $data->id.",'Machine Activities'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editMachineActivities', 'title' : 'Update Machine Activities'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->activities];
}

?>