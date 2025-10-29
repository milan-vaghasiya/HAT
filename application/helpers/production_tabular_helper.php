<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getProductionHeader($page){
    /* Vendor Header */
    $data['vendor'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['vendor'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['vendor'][] = ["name"=>"Company Name"];
	$data['vendor'][] = ["name"=>"Contact Person"];
    $data['vendor'][] = ["name"=>"Contact No."];
    $data['vendor'][] = ["name"=>"Address"];
    
    /* Process Header */
    $data['process'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['process'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Department"];
    $data['process'][] = ["name"=>"Remark"];

    /* Job Card Header */
    $data['jobcard'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Category","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Total OK Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Total Rej Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Remark","style"=>"width:15%;"];
    $data['jobcard'][] = ["name"=>"Last Activity"];

    /* Material Request */
    $data['materialRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['materialRequest'][] = ["name"=>"Request Date"];
    $data['materialRequest'][] = ["name"=>"Finish Good"];
    $data['materialRequest'][] = ["name"=>"Request Item Name"];
    $data['materialRequest'][] = ["name"=>"Request Item Qty"];
    $data['materialRequest'][] = ["name"=>"Status"];

    /* Jobwork Order Header */
    $data['jobWorkOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkOrder'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Order Date"];
    $data['jobWorkOrder'][] = ["name"=>"Order No."];
    $data['jobWorkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobWorkOrder'][] = ["name"=>"Product"];
    $data['jobWorkOrder'][] = ["name"=>"Qty"];
    $data['jobWorkOrder'][] = ["name"=>"Rate"];
    $data['jobWorkOrder'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Process"];
    $data['jobWorkOrder'][] = ["name"=>"Remark"];

    /* Job Work Header */
    $data['jobWork'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Vendor"];
    $data['jobWork'][] = ["name" => "Product", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Process"];
    $data['jobWork'][] = ["name" => "Status", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Reject Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Rework Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Pending Qty", "textAlign" => "center"]; 

    /* Job Work Vendor Header */
    $data['jobWorkVendor'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Job Date", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Vendor"];
    $data['jobWorkVendor'][] = ["name" => "Product"];
    $data['jobWorkVendor'][] = ["name" => "Process"];
    $data['jobWorkVendor'][] = ["name" => "Status", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Return Qty", "textAlign" => "center"];
    $data['jobWorkVendor'][] = ["name" => "Challan Date", "textAlign" => "center"];

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];

	/* Production Operation Header */
    $data['productionOperation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"Operation Name"];

    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['productOption'][] = ["name"=>"Part Name"]; 
    $data['productOption'][] = ["name"=>"Size"];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"BOM","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Screp","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","style"=>"width:10%;","textAlign"=>"center"];
    //$data['productOption'][] = ["name"=>"Tool","style"=>"width:10%;","textAlign"=>"center"];
    //$data['productOption'][] = ["name"=>"Inspection","style"=>"width:10%;","textAlign"=>"center"];
    //$data['productOption'][] = ["name"=>"In-Inspection","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Idle Reason Header */
    $data['idleReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['idleReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['idleReason'][] = ["name"=>"Idle Code","style"=>"width:10%;","textAlign"=>"center"];
    $data['idleReason'][] = ["name"=>"Idle Reason"];

    /* Process Setup Header */
    $data['processSetup'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['processSetup'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['processSetup'][] = ["name"=>"Req. Date"];
    $data['processSetup'][] = ["name"=>"Status"];
    $data['processSetup'][] = ["name"=>"Setup Type"];
    $data['processSetup'][] = ["name"=>"Setter Name"];
    $data['processSetup'][] = ["name"=>"Setup Note"];
    $data['processSetup'][] = ["name"=>"Job No"];
    $data['processSetup'][] = ["name"=>"Part Name"];
    $data['processSetup'][] = ["name"=>"Process Name"];
    $data['processSetup'][] = ["name"=>"Machine"];
    $data['processSetup'][] = ["name"=>"Inspector Name"];
    $data['processSetup'][] = ["name"=>"Start Time"];
    $data['processSetup'][] = ["name"=>"End Time"];
    $data['processSetup'][] = ["name"=>"Duration"];
    $data['processSetup'][] = ["name"=>"Remark"];

    /* Line Inspection Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkLineInspection" value=""><label for="masterSelect">ALL</label>';
    $data['lineInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['lineInspection'][] = ["name"=>"#","style"=>"width:10%;","textAlign"=>"center"];
    $data['lineInspection'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['lineInspection'][] = ["name"=>"Jobcard No."];
    $data['lineInspection'][] = ["name"=>"Process Name"];
    $data['lineInspection'][] = ["name"=>"Product Code"];
    $data['lineInspection'][] = ["name"=>"Vendor Name"];
    $data['lineInspection'][] = ["name"=>"In Qty."];
    $data['lineInspection'][] = ["name"=>"Out Qty."];
    $data['lineInspection'][] = ["name"=>"Rej. Qty."];
    $data['lineInspection'][] = ["name"=>"ReW. Qty."];
    $data['lineInspection'][] = ["name"=>"Status"];    
    
    /* vendor Challan Header */
    $data['vendorChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['vendorChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['vendorChallan'][] = ["name"=>"Challan Date"];
    $data['vendorChallan'][] = ["name"=>"Challan No."];
    $data['vendorChallan'][] = ["name"=>"Vendor"];
    $data['vendorChallan'][] = ["name"=>"Product"];
    $data['vendorChallan'][] = ["name"=>"Qty"];

    /* Process Approval */
    /* $data['processApproval'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['processApproval'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['processApproval'][] = ["name"=>"Job Date"];
    $data['processApproval'][] = ["name"=>"Delivery Date"];
    $data['processApproval'][] = ["name"=>"Job Type"];
    $data['processApproval'][] = ["name"=>"Customer"];
    $data['processApproval'][] = ["name"=>"Challan No."];
    $data['processApproval'][] = ["name"=>"Product"];
    $data['processApproval'][] = ["name"=>"Order Qty."];
    $data['processApproval'][] = ["name"=>"Status"];
    $data['processApproval'][] = ["name"=>"Remark"]; */

    /* Scrap Header */
    $data['scrap'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"Date.","style"=>"width:9%;","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Jobcard","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Scrap Qty","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Ok Qty","textAlign"=>"center"];
    
    /* Log Sheet  Header */
    $data['logSheet'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['logSheet'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Date","textAlign"=>"center"];
	$data['logSheet'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];
    $data['logSheet'][] = ["name"=>"Rework Qty","textAlign"=>"center"];
    
    /* RM Process  Header */
    $data['rmProcess'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Jobwork Order","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Item Name","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['rmProcess'][] = ["name"=>"Recived Item","textAlign"=>"center"];
    
    /* Hold Area  Header */
    $data['holdAreaMovement'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Ok Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Rejection Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Pending Qty","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdAreaMovement'][] = ["name"=>"Pending Days","style"=>"width:9%;","textAlign"=>"center"];
    
    /* Hold Area  Header */
    $data['holdToOk'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Date.","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Vendor","style"=>"width:9%;","textAlign"=>"center"];
    $data['holdToOk'][] = ["name"=>"Qty","style"=>"width:9%;","textAlign"=>"center"];

    /* Gerenate Scrap */
    $data['generateScrap'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Item Name","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Qty.","textAlign"=>"center"];
    $data['generateScrap'][] = ["name"=>"Remark","textAlign"=>"center"];
    
    /* Costing Header */
    $data['costing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['costing'][] = ["name"=>"Part Code"];
    $data['costing'][] = ["name"=>"Costing","style"=>"width:10%;","textAlign"=>"center"];
    $data['costing'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Job Work Header */
    $data['outSource'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Challan Date", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Challan No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Wo. No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Vendor"];
    $data['outSource'][] = ["name" => "Product"];
    $data['outSource'][] = ["name" => "Process"];
    $data['outSource'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    $data['outSource'][] = ["name" => "Return Qty", "textAlign" => "center"];

    /* Rework */
    $data['rework'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['rework'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Tag No","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw Reason","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw Stage","textAlign"=>"center"];
    $data['rework'][] = ["name"=>"Rej/Rw By","textAlign"=>"center"];

    /* Primary CFT */
    $data['primaryCFT'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['primaryCFT'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Job No.","textAlign"=>"center"];
	$data['primaryCFT'][] = ["name"=>"W.O. No.","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['primaryCFT'][] = ["name"=>"Reason","textAlign"=>"center"];

    /* Final CFT */
    $data['finalCFT'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['finalCFT'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Job No.","textAlign"=>"center"];
	$data['finalCFT'][] = ["name"=>"W.O. No.","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Decision","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw Reason","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw Stage","textAlign"=>"center"];
    $data['finalCFT'][] = ["name"=>"Rej/Rw By","textAlign"=>"center"];

    /* UD CFT */
    $data['underDeviation'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['underDeviation'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Qty","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Pending Qty","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Description","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Reason","textAlign"=>"center"];
    $data['underDeviation'][] = ["name"=>"Special Marking","textAlign"=>"center"];

    /* Customer Rework Header */
    $data['prodCustomerRework'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['prodCustomerRework'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['prodCustomerRework'][] = ["name"=>"Debit. No.","style"=>"width:10%;","textAlign"=>"center"];
    $data['prodCustomerRework'][] = ["name"=>"Date"];
    $data['prodCustomerRework'][] = ["name"=>"Customer Name"];
    $data['prodCustomerRework'][] = ["name"=>"Inv No"];
    $data['prodCustomerRework'][] = ["name"=>"Item Description"];
    $data['prodCustomerRework'][] = ["name"=>"Qty"];
    $data['prodCustomerRework'][] = ["name"=>"Remarks"];
    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteParam = $data->id.",'Process'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcess', 'title' : 'Update Process'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->dept_name,$data->remark];
}

/* Job Card Table Data */
function getJobcardData($data){
    $deleteParam = $data->id.",'Jobcard'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editJobcard', 'title' : 'Update Jobcard'}";
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'requiredTest', 'title' : 'Requirement'}";

    $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = '';$closeOrder="";$reopenOrder = "";$dispatchBtn = ''; $shortClose = ''; $updateJob = '';

    //if($data->loginID == 1):
        $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="6" data-id="'.$data->id.'"><i class="sl-icon-close"></i></a>';
    //endif;
    
    if((floatVal($data->issue_qty) - floatVal($data->received_qty)) > 0){
        $startOrder = '<a class="btn btn-success btn-start materialReceived permission-modify" href="javascript:void(0)" datatip="Material Received" flow="down" data-val="3" data-id="'.$data->id.'"><i class="fa fa-check" ></i></a>';
    }
    
   if($data->order_status == 2):
        $holdOrder = '<a class="btn btn-danger btn-hold changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" data-val="3" data-id="'.$data->id.'"><i class="ti-control-pause" ></i></a>';
        if(isset($data->pendingQty)):
            $updateQtyParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'updateJobQty', 'title' : 'Update Jobcard Qty [".$data->job_number."] Pending Qty.: ".$data->pendingQty."', 'fnEdit':'updateJobQty','button':'close'}";
            $updateJob = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Update Job Qty." flow="down" onclick="edit(' . $updateQtyParam . ');"><i class="ti-exchange-vertical"></i></a>';
        endif;
    elseif($data->order_status == 3):
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    elseif($data->order_status == 4):
        $shortClose = '';
        $closeOrder = '<a class="btn btn-dark btn-close changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-val="5" data-id="'.$data->id.'"><i class="ti-close" ></i></a>';
    elseif($data->order_status == 5):
        $shortClose = '';
        $reopenOrder = '<a class="btn btn-primary btn-reoprn changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Reopen" flow="down" data-val="4" data-id="'.$data->id.'"><i class="ti-reload" ></i></a>';
    endif;

    //Regular Order
    if($data->issue_qty == 0 AND empty($data->order_status)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';        
    endif;

	$jobNo = '<a href="'.base_url($data->controller."/view/".$data->id).'">'.$data->job_number.'</a>';
	
	// last activity
    $firstdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
    $seconddate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
    $thirdate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
    $lastAdate = date('Y-m-d', strtotime($data->last_activity)); 

    $color='';
    if($lastAdate >= $firstdate) { $color="text-primary"; } 
	elseif($lastAdate == $seconddate) { $color="text-dark"; } 
	else { $color="text-danger"; }

    $last_activity = '<a href="javascript:void(0);" class="'.$color.' viewLastActivity" data-trans_id="'.$data->id.'" data-job_no="'.$data->job_number.'" datatip="View Last Activity" flow="down"><b>'.$data->last_activity.'</b></a>';

    $type = ($data->job_category == 0) ? 'Manufacturing' : 'Jobwork';

    $generateScrape = "";
    if($data->order_status == 5):
        $generateScrapeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'generateScrape', 'title' : 'Generate Scrape' , 'fnEdit' : 'generateScrape' , 'fnsave' : 'saveScrape' }";

        $generateScrape = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Generate Scrape" flow="down" onclick="edit('.$generateScrapeParam.');"><i class="icon-Trash-withMen" style="font-size:18px;font-weight: 900;" ></i></a>';
    endif;
    $rmApprovalBtn='';
    if($data->used_as == 2 AND $data->approved_by == 0){
        $rmApprovalBtn='<a class="btn btn-primary btn-reoprn approveRM permission-approve" href="javascript:void(0)" datatip="Approve Raw Material" flow="down" data-id="'.$data->id.'"><i class="fa fa-check" ></i></a>';
    }
    $action = getActionButton($rmApprovalBtn.$updateJob.$dispatchBtn.$startOrder.$holdOrder.$restartOrder.$closeOrder.$shortClose.$reopenOrder.$generateScrape.$editButton.$deleteButton);
    return [$action,$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),$data->party_code,$data->item_code,$data->category_name,floatVal($data->qty),floatVal($data->total_out_qty),floatVal($data->total_rej_qty),$data->order_status_label,$data->remark,$last_activity];
}


/* Material Request Data */
function getMaterialRequest($data){
    $deleteParam = $data->id.",'Request'"; $editButton=''; $deleteButton='';
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'materialRequest', 'title' : 'Material Request'}";
    if($data->order_status != 2):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    endif;    

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->fg_name,$data->req_item_name,$data->req_qty,$data->order_status_label];
}

/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $deleteParam = $data->id.",'Job Work Order'"; $approve = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobOrder', 'title' : 'Update Job Work Order'}";
    $editButton='';$deleteButton='';
    if(empty($data->is_approve)){
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrderView permission-approve" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve Job Work Order" flow="down" ><i class="fa fa-check" ></i></a>';

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    } else {
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrder permission-approve" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject Job Work Order" flow="down" ><i class="fa fa-ban" ></i></a>';
    }
    

    //$printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallan/'.$data->id).'" target="_blank" datatip="Regular Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printBtnFull = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallanFull/'.$data->id).'" target="_blank" datatip="Full Page Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	if(empty($data->is_close)){
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-close"></i></a>';
        $action = getActionButton($approve.$shortClose.$printBtnFull.$editButton.$deleteButton);
    }else{
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Re-open" flow="down" data-val="0" data-id="'.$data->id.'"><i class="ti-loop"></i></a>';
        $action = getActionButton($shortClose);
    }
	
	$qty = ($data->rate_per == 1) ? $data->qty : $data->qty_kg ;
	$productName ="";
    if($data->item_type == 1){
        $productName = $data->item_code;
    }else{
        $productName = $data->item_name;
    }
    return [$action,$data->sr_no,formatDate($data->jwo_date),getPrefixNumber($data->jwo_prefix,$data->jwo_no),$data->party_name,$productName,floatVal($qty),sprintf('%0.2f',$data->rate),$data->approve_status,$data->process,$data->remark];
    
}

/* Job Work Table Data */
function getJobWorkData($data){
    $returnBtn=""; $printBtn="";
    //$printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    if(empty($data->accepted_by)):         
        $button = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>';        
    else:
        $dataRow = ['product_name'=>$data->item_code,'ref_id'=>$data->id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'issue_batch_no'=>$data->issue_batch_no,'issue_material_qty'=>$data->issue_material_qty,'material_used_id'=>$data->material_used_id,'minDate'=>$data->minDate];

        $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Receive Material' flow='down' data-row='".json_encode($dataRow)."' data-toggle='modal' data-target='#outwardModal'><i class='fas fa-paper-plane' ></i></a>";

        if(!empty($data->pending_qty)):
            $returnParams = ['product_name'=>$data->item_code,'job_trans_id'=>$data->id,'job_approval_id'=>$data->job_approval_id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'minDate'=>$data->minDate,'job_process_ids'=>$data->job_process_ids,'fnEdit'=>"jobWorkReturn",'fnsave'=>"jobWorkReturnSave",'modal_id'=>"modal-lg",'title'=>"Return",'form_id'=>"jobWorkReturnSave"];
    
            $returnBtn = "<a class='btn btn-info btn-edit ' href='javascript:void(0)' datatip='Return' flow='down' onclick='jobWorkReturn(".json_encode($returnParams).");'><i class='fas fa-reply'></i></a>";
        endif;
    endif;
    $action = getActionButton($returnBtn.$button.$printBtn);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->process_name,$data->status,floatVal($data->in_qty),floatVal($data->out_qty),floatVal($data->rejection_qty),floatVal($data->rework_qty),floatVal($data->pending_qty)];
}

/* Job Work Table Data */
function getJobWorkVendorData($data){
    $returnBtn="";$moveBtn="";  

    if(!empty($data->pending_qty)):
        $returnParams = ['product_name'=>$data->item_code,'id'=>$data->id,'product_id'=>$data->product_id,'process_id'=>$data->process_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'job_card_id'=>$data->job_card_id,'production_approval_id'=>$data->production_approval_id,'production_trans_id'=>$data->ref_id,'fnEdit'=>"jobWorkReturn",'fnsave'=>"jobWorkReturnSave",'modal_id'=>"modal-lg",'title'=>"Return",'form_id'=>"jobWorkReturnSave"];

        $returnBtn = "<a class='btn btn-info btn-edit ' href='javascript:void(0)' datatip='Return' flow='down' onclick='jobWorkReturn(".json_encode($returnParams).");'><i class='fas fa-reply'></i></a>";
    endif;

    $outParam = "{'id' : " . $data->production_approval_id . ", 'vp_trans_id' : ".$data->id." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Receive Material" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $action = getActionButton($returnBtn.$moveBtn);

    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->job_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->process_name,$data->status,floatVal($data->out_qty),floatVal($data->in_qty),floatVal($data->pending_qty),floatVal($data->return_qty),$data->challan_date];
}

/* Production Opration Data */
function getProductionOperationData($data){
    $deleteParam = $data->id.",'Production Operation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProductionOperation', 'title' : 'Update Production Operation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->operation_name];
}

/* Product Option Data */
function getProductOptionData($data){

	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-twitter productKit permission-modify printbtn" data-id="'.$data->id.'" data-product_name="'.$data->item_name .'-'.$data->size.'" data-button="close" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Create Material BOM" datatip="BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>
				
				<button type="button" class="btn btn-info viewItemProcess permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="Set Product Process" datatip="View Process" flow="down"><i class="fa fa-list"></i></button>
				
				<button type="button" class="btn btn-twitter addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-fnsave="saveCT" data-form_title="Set Cycle Time" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>
            </div>';

    return [$data->sr_no,$data->item_name,$data->size,$data->item_code,$data->bom,$data->process,$data->cycleTime,$btn];
}

/* Process Setup Data */
function getProcessSetupData($data){
    $acceptBtn = "";$editButton = "";
    if(empty($data->setup_start_time)):
        $acceptBtn = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; 
    else:
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcessSetup', 'title' : 'Process Setup', 'fnEdit' : 'processSetup'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Finish Setup" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;    

    $action = getActionButton($acceptBtn.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setup_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->setup_start_time))?date("d-m-Y h:i:s A",strtotime($data->setup_start_time)):"",(!empty($data->setup_end_time))?date("d-m-Y h:i:s A",strtotime($data->setup_end_time)):"",$data->duration,$data->setter_note];
}

/* Line Inspection Data */
function getLineInspectionData($data){
    $btnParam = ['ref_id'=>$data->id,'product_id'=>$data->product_id,'process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'product_name'=>$data->product_code,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'mindate'=>$data->minDate,'modal_id'=>'modal-xxl','form_id'=>'lineInspectionFrom','title'=>'Line Inspection'];

    $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Forward' flow='down' onclick='lineInspection(".json_encode($btnParam).");'><i class='fas fa-paper-plane' ></i></a>";

    $action = getActionButton($button);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkLineInspection" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    if($data->inspected_qty >= $data->in_qty):
        $selectBox = "";
    endif;
    return [$action,$data->sr_no,$selectBox,getPrefixNumber($data->job_prefix,$data->job_no),$data->process_name,$data->product_code,(!empty($data->party_name))?$data->party_name:"In House",$data->in_qty,$data->out_qty,$data->rejection_qty,$data->rework_qty,$data->status];
}

/* Vendor Challan Data */
function getVendorChallanData($data){
    $deleteParam = $data->id.",'Vendor Challan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    
    $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editVendorChallan', 'title' : 'Return Vendor Material', 'fnEdit':'returnVendorMaterial','fnsave':'saveReturnMaterial'}";
    $returnBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Return Vendor Material" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply"></i></a>';

	$action = getActionButton($returnBtn.$printBtn.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->challan_date),getPrefixNumber($data->challan_prefix,$data->challan_no),$data->party_name,$data->item_code,$data->qty];
}

///--------------------
/* Process Approval Table Data */
/* function getProcessApprovalData($data){
    $jobNo = '<a href="'.base_url($data->controller.'/list/'.$data->id).'">'.$data->job_prefix.$data->job_no.'</a>';

    $type = (empty($data->ref_id))?'Regular':'Rework';

    return [$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),date("d-m-Y",strtotime($data->delivery_date)),$type,$data->party_code,$data->challan_no,$data->item_code,$data->qty,$data->order_status,$data->remark];
} */

/* Scrap Table Data */
function getScrapData($data){
    $deleteParam = $data->id.",'Rejection Scrap'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $transList = '<a href="javascript:void(0)" class="btn btn-primary createTransList permission-read" data-id="'.$data->id.'"  datatip="Transaction List" flow="down"><i class="fa fa-list" ></i></a>';
    $action = getActionButton($transList.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->trans_date),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_name,$data->scrap_qty,$data->ok_qty];
}

// Created By Karmi @02/02/2022
function getLogSheetData($data){
    $action = "";
    $deleteParam = $data->id.",'Production Log'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'productionLog', 'title' : 'Update Production Log'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->production_qty,$data->rej_qty,$data->rw_qty];
}

// Created By Avruti @23/04/2022
function getRmProcessData($data){
    $action = "";  $deleteParam = $data->ref_batch.",'RM Process'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'rmProcess', 'title' : 'Update RM Process'}";
    $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'rmProcess', 'title' : 'Return Rm Process [".$data->item_name." ]','button':'close','fnEdit':'returnRmProcess','fnsave':'saveReturnRM'}";

    $returnRMButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply" ></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $jwoNo = (!empty($data->jwo_prefix) && !empty($data->jwo_no))?getPrefixNumber($data->jwo_prefix,$data->jwo_no):'';

    $action = getActionButton($returnRMButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->ref_date),$data->party_name,$jwoNo,$data->item_name,$data->qty,$data->return_itm];
}

/* Created By Mansee @ 14-06-2022 */
function getHoldAreaMovementData($data){
    $pending_qty=$data->in_qty-$data->ok_qty-$data->rej_qty;
    $outParam = "{'id' : " . $data->production_approval_id . ", 'entry_type' : ".$data->entry_type.", 'trans_ref_id' : ".$data->id.", 'pending_qty' : ".$pending_qty." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $action = getActionButton($moveBtn);
    
    $dateDiff = time() - strtotime($data->entry_date);
    $pendingDays = round((time() - strtotime($data->entry_date)) / (60 * 60 * 24));
    
    return [$action,$data->sr_no,formatDate($data->entry_date),getPrefixNumber($data->job_prefix, $data->job_no),$data->item_name,$data->process_name,$data->vendor_name,$data->in_qty,$data->ok_qty,$data->rej_qty,$pending_qty,$pendingDays.' Days'];
}

function getHoldToOkMovementData($data){
    $pending_qty=$data->in_qty;
    $outParam = "{'id' : " . $data->id . " , 'pending_qty' : ".$pending_qty." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="down" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $action = getActionButton($moveBtn);

    return [$action,$data->sr_no,formatDate($data->entry_date),getPrefixNumber($data->job_prefix, $data->job_no),$data->item_name,$data->process_name,$data->vendor_name,$data->in_qty];
}

function getGenerateScrapData($data){
    $deleteParam = $data->id.",'Scrape'";    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editScrap', 'title' : 'Update Scrap'}";

    $editButton = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->ref_date,$data->item_name,$data->qty,$data->remark];
}

/* Costing Data * Created By Meghavi @10/08/2022 */
function getCostingData($data){
    $btn = '<button type="button" class="btn btn-info addCosting permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-button="both" data-modal_id="modal-md" data-function="viewProductProcess" data-fnsave="saveCosting" data-form_title="Product Costing" datatip="Product Costing" flow="down"><i class="fa fa-list"></i></button>';
    return [$data->sr_no,$data->item_code,$data->process,$btn];
}

/* Outsource Table Data */
function getOutsourceData($data){ 
    $returnBtn="";$inwardButton="";  $deleteButton ="";
    if($data->outsource_qty == 0):
        $deleteParam = $data->challan_id.",'Vendor Challan'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
       
    endif;

    $outParam = "{'job_approval_id' : " . $data->job_approval_id . ", 'job_trans_id' : ".$data->id." ,'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";  
    $moveBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Receive Material" flow="down" onclick="vendorMaterialReturn(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
    $printChallanBtn = '<a class="btn btn-primary" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->challan_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($printChallanBtn.$returnBtn.$moveBtn.$deleteButton);

    return [$action,$data->sr_no,date('d-m-Y',strtotime($data->trans_date)),$data->trans_number,$data->job_number,$data->wo_no,$data->party_name,(!empty($data->item_code)?'['.$data->item_code.'] '.$data->item_name:$data->item_name),$data->process_name,floatVal($data->qty),floatVal($data->outsource_qty),floatVal($data->pending_qty),''];
}

/** Get Primary CFT Data */
function getPrimaryCFTData($data){
    $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'Ok ','button' : 'both','fnEdit' : 'convertToOk','fnsave' : 'saveCFTQty'}";
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnEdit' : 'convertToRej','fnsave' : 'saveCFTQty'}";
    $rwBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rwOutWard', 'title' : 'Rework ','button' : 'both','fnEdit' : 'convertToRw','fnsave' : 'saveCFTQty'}";
    $hldBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'suspOutWard', 'title' : 'Hold ','button' : 'both','fnEdit' : 'convertToHold','fnsave' : 'saveCFTQty'}";
    $udBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'udOutWard', 'title' : 'Under Deviation ','button' : 'both','fnEdit' : 'convertToUD','fnsave' : 'saveCFTQty'}";

    $operation_type ='';
    if($data->operation_type == 1){ $operation_type = 'Rejection'; }
    elseif($data->operation_type == 2){ $operation_type = 'Rework'; }
    elseif($data->operation_type == 3){ $operation_type = 'Hold'; }
    elseif($data->operation_type == 4){  $operation_type = 'OK'; }
    elseif($data->operation_type == 5){ $operation_type = 'Under Deviation';  }

	$okBtn='';$rejBtn='';$rwBtn='';$hldBtn='';$printBtn = "";$udBtn = '';
    if($data->pending_qty > 0){
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
        $rwBtn = '<a  onclick="edit('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';
        $hldBtn='<a onclick="edit(' . $hldBtnParam . ')"  class="btn btn-primary" datatip="Hold" flow="down"><i class="fas fa-pause"></i></a>';
        $udBtn='<a onclick="edit(' . $udBtnParam . ')"  class="btn btn-warning" datatip="Under Deviation" flow="down"><i class="fab fa-dochub"></i></a>';
    }else{
        if($data->operation_type == 1):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/REJ/'. $data->id) . '" target="_blank" class="btn btn-dark waves-effect waves-light" datatip="Rejection Tag" flow="down"><i class="fas fa-print"></i></a>';
        elseif($data->operation_type == 2):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/REW/'. $data->id) . '" target="_blank" class="btn btn-info waves-effect waves-light" datatip="Rework Tag" flow="down"><i class="fas fa-print"></i></a>';
        elseif($data->operation_type == 3):
            $printBtn = '<a href="' . base_url('production/primaryCFT/printTag/SUSP/'. $data->id) . '" target="_blank" class="btn btn-warning waves-effect waves-light" datatip="Suspected Tag" flow="down"><i class="fas fa-print"></i></a>';
        endif;
    }
    $action = getActionButton($okBtn.$rejBtn.$rwBtn.$hldBtn.$udBtn.$printBtn);
    $color=''; if($data->entry_type == 4 || $data->ref_type==4){ $color='text-danger font-weight-bold'; }
    $refType = '<span class="'.$color.'" >'.(($data->entry_type == 4  || $data->ref_type==4)?'Under Deviation':'Reguler').'</span>';
    return [$action,$data->sr_no,$data->job_number,$data->wo_no,$data->product_code,formatDate($data->entry_date),$data->process_name,$data->item_code,$data->emp_name,$data->qty,$data->pending_qty,$operation_type,$data->rejection_reason];
}

/** Get Final CFT Data */
function getFinalCFTData($data){
    $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'Ok ','button' : 'both','fnEdit' : 'convertToOk','fnsave' : 'saveCFTQty'}";
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnEdit' : 'convertToRej','fnsave' : 'saveCFTQty'}";
    $rwBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-lg', 'form_id' : 'rwOutWard', 'title' : 'Rework ','button' : 'both','fnEdit' : 'convertToRw','fnsave' : 'saveCFTQty'}";
    $udBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'udOutWard', 'title' : 'Under Deviation ','button' : 'both','fnEdit' : 'convertToUD','fnsave' : 'saveCFTQty'}";

    $okBtn='';$rejBtn='';$rwBtn=''; $confirmBtn='';$udBtn='';
    if($data->entry_type == 2 || $data->entry_type == 4){
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
        $rwBtn = '<a  onclick="edit('. $rwBtnParam . ')"  class="btn btn-info btn-edit permission-modify" datatip="Rework" flow="down"><i class=" fas fa-retweet"></i></a>';
        $confirmBtn = '<a class="btn btn-primary btn-approve permission-approve" href="javascript:void(0)" onclick="confirmCft('.$data->id.');" datatip="Confirm" flow="down"><i class=" fas fa-thumbs-up
        "></i></a>';
        $udBtn='<a onclick="edit(' . $udBtnParam . ')"  class="btn btn-warning" datatip="Under Deviation" flow="down"><i class="fab fa-dochub"></i></a>';

    }
	$operation_type ='';
    if($data->operation_type == 1){ $operation_type = 'Rejection';
    }elseif($data->operation_type == 2){ $operation_type = 'Rework'; }
    elseif($data->operation_type == 3){ $operation_type = 'Hold'; }
    elseif($data->operation_type == 4){ $operation_type = 'OK'; }
    elseif($data->operation_type == 5){ $operation_type = 'Under Deviation'; }
	
    $action = getActionButton($confirmBtn.$okBtn.$rejBtn.$rwBtn.$udBtn);
    $color=''; if($data->ref_type == 4 || $data->ref_type==3){ $color='text-danger font-weight-bold'; }
    $refType = '<span class="'.$color.'" >'.(($data->ref_type == 4 || $data->ref_type==3)?'Under Deviation':'Reguler').'</span>';
    return [ $action,$data->sr_no,$data->job_number,$data->wo_no,$data->product_code,formatDate($data->entry_date),$data->process_name,$data->item_code,$data->emp_name,$data->qty,$data->pending_qty,$operation_type,$data->rejection_reason,$data->parameter,(!empty($data->party_name)?$data->party_name:'In House')];
}

/** Get Rework Data */
function getReworkData($data){
    
	$reworkBtn ='<a  href="'.base_url($data->controller."/reworkDetail/".$data->id).'"  class="btn btn-success btn-edit permission-modify" datatip="Rework Movement" flow="down"><i class=" fas fa-eye"></i></a>'; 
    $action = getActionButton($reworkBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),($data->tag_prefix.sprintf("%04d",$data->tag_no)),$data->job_number,'['.$data->item_code.'] '.$data->item_name,$data->process_name,$data->qty,$data->rejection_reason,$data->parameter,(!empty($data->party_name)?$data->party_name:'In House')];
}

/** Get Under Deviation CFT Data */
function getUdCFTData($data){
    
    $rejBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'rejOutWard', 'title' : 'Rejection ','button' : 'both','fnEdit' : 'convertToUdRej','fnsave' : 'saveCFTQty'}";
   

    $okBtn='';$rejBtn='';$rwBtn=''; $confirmBtn='';
    if($data->entry_type != 4){
        $okBtnParam="{'id' : " . $data->id . " ,'modal_id' : 'modal-md', 'form_id' : 'okOutWard', 'title' : 'UD Ok ','button' : 'both','fnEdit' : 'convertToUdOk','fnsave' : 'saveCFTQty'}";
        $okBtn='<a  onclick="edit('. $okBtnParam . ')"  class="btn btn-success btn-edit permission-modify" datatip="Ok" flow="down"><i class="ti-check"></i></a>';
        $rejBtn = '<a onclick="edit(' . $rejBtnParam . ')"  class="btn btn-danger btn-edit permission-modify" datatip="Rejection" flow="down"><i class="ti-close"></i></a>';
    }

    $action = getActionButton($confirmBtn.$okBtn.$rejBtn.$rwBtn);
    return [ $action,$data->sr_no,formatDate($data->entry_date),$data->job_number,'['.$data->item_code.'] '.$data->item_name,$data->process_name,floatval($data->qty),floatval($data->pending_qty),$data->remark,$data->rr_stage,$data->rw_process_id];
}

/* Customer Complaints Table Data*/
function getProdCustomerReworkData($data){ 
    $editButton="";$acceptBtn=""; $completeBtn ="";
    if(empty($data->status)){
        $acceptBtn = '<a class="btn btn-success btn-delete" href="javascript:void(0)" onclick="acceptRework('.$data->id.');" datatip="Accept" flow="down"><i class="fas fa-check"></i></a>';
    }elseif($data->status == 1){
        
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editcustomerComplaints', 'title' : 'Complete Rework','fnEdit':'addRWRejQty','fnsave':'saveRwRejQty'}";
    
        $completeBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Complete Rework" flow="down" onclick="edit('.$editParam.');"><i class=" fas fa-paper-plane" ></i></a>';
    
    }
    
    
    $action = getActionButton($acceptBtn.$completeBtn);
   
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->inv_no,$data->full_name,$data->qty,$data->remark];
}
?>