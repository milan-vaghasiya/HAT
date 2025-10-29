<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getQualityDtHeader($page)
{	   
	//avruti 14-9-21
	/* Purchase Material Inspection Header */
    $data['materialInspection'][] = ["name"=>"Action","style"=>"width:5%;"]; 
	$data['materialInspection'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"Inv No."];
    $data['materialInspection'][] = ["name"=>"Inv Date"];
	$data['materialInspection'][] = ["name"=>"Challan No."];
    $data['materialInspection'][] = ["name"=>"Order No."];
    $data['materialInspection'][] = ["name"=>"Supplier/Customer"];
    $data['materialInspection'][] = ["name"=>"Item Name"];
    $data['materialInspection'][] = ["name"=>"Finish Goods"];
    $data['materialInspection'][] = ["name"=>"Received Qty"];
    $data['materialInspection'][] = ["name"=>"Batch/Heat No."];
    $data['materialInspection'][] = ["name"=>"Color Code"];
    $data['materialInspection'][] = ["name"=>"Status"];

    /* Final Inspection Header */
    $data['finalInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"Rejection Type"];
    $data['finalInspection'][] = ["name"=>"Item Name"];
    $data['finalInspection'][] = ["name"=>"Qty."];
    $data['finalInspection'][] = ["name"=>"Pending Qty."];
    
    /* Job Work Inpection Header */
    $data['jobWorkInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkInspection'][] = ["name"=>"Date"];
    $data['jobWorkInspection'][] = ["name"=>"Challan No."];
    $data['jobWorkInspection'][] = ["name"=>"Job No."];
    $data['jobWorkInspection'][] = ["name"=>"Vendor"];
    $data['jobWorkInspection'][] = ["name"=>"Part Code"];
    $data['jobWorkInspection'][] = ["name"=>"Charge No."];
    $data['jobWorkInspection'][] = ["name"=>"Process"];
    $data['jobWorkInspection'][] = ["name"=>"OK Qty."];
    $data['jobWorkInspection'][] = ["name"=>"UD Qty."];

	/* RM Inspection Data */
	$data['inspectionParam'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['inspectionParam'][] = ["name"=>"Part Name"];
	$data['inspectionParam'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];
    
	/* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];

	/* Pre Dispatch Inspect Header */
	$data['preDispatchInspect'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['preDispatchInspect'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['preDispatchInspect'][] = ["name"=>"Report No."];
	$data['preDispatchInspect'][] = ["name"=>"Report Date"];
	$data['preDispatchInspect'][] = ["name"=>"Part Code"];
	$data['preDispatchInspect'][] = ["name"=>"Part Name"];
   
    /* In Challan Header */
    $data['inChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"Challan No."];
    $data['inChallan'][] = ["name"=>"Challan Date"];
    $data['inChallan'][] = ["name"=>"Party Name"];
    $data['inChallan'][] = ["name"=>"Item Name"];
    $data['inChallan'][] = ["name"=>"Qty."];
    $data['inChallan'][] = ["name"=>"Remark"];

    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Collected By"];
    $data['outChallan'][] = ["name"=>"Process Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty."];
    $data['outChallan'][] = ["name"=>"Remark"];

    /* Assign Inspector Header */
    $data['assignInspector'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"Req. Date"];
    $data['assignInspector'][] = ["name"=>"Job Card No."];
    $data['assignInspector'][] = ["name"=>"Product Name"];
    $data['assignInspector'][] = ["name"=>"Process Name"];    
    $data['assignInspector'][] = ["name"=>"Machine No."];
    $data['assignInspector'][] = ["name"=>"Setter Name"];
    $data['assignInspector'][] = ["name"=>"Inspector Name"];
    $data['assignInspector'][] = ["name"=>"Status"];
    $data['assignInspector'][] = ["name"=>"Note"];

    /* Setup Inspection Header */
    $data['setupInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['setupInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['setupInspection'][] = ["name"=>"Req. Date"];
    $data['setupInspection'][] = ["name"=>"Status"];
    $data['setupInspection'][] = ["name"=>"Setup Type"];
    $data['setupInspection'][] = ["name"=>"Setter Name"];
    $data['setupInspection'][] = ["name"=>"Setter Note"];
    $data['setupInspection'][] = ["name"=>"Job No"];
    $data['setupInspection'][] = ["name"=>"Part Name"];
    $data['setupInspection'][] = ["name"=>"Process Name"];
    $data['setupInspection'][] = ["name"=>"Machine"];
    $data['setupInspection'][] = ["name"=>"Inspector Name"];
    $data['setupInspection'][] = ["name"=>"Start Date"];
    $data['setupInspection'][] = ["name"=>"End Date"];
    $data['setupInspection'][] = ["name"=>"Duration"];
    $data['setupInspection'][] = ["name"=>"Remark"];
    $data['setupInspection'][] = ["name"=>"Attachment","textAlign"=>"center"];

     /* Inspection Type Header */
    $data['inspectionType'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['inspectionType'][] = ["name" => "#", "style" => "width:5%;"];
    $data['inspectionType'][] = ["name" => "Inspection Type"];

	/* NC Report  Header */
	$data['ncReport'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Job No.","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Date","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Operator","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Qty.","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];
	$data['ncReport'][] = ["name"=>"Rework Qty","textAlign"=>"center"];
	
    /* Log Header */
    $data['rejectionLog'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Part Code","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Process","style"=>"width:9%;","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Machine","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Operator","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];
    $data['rejectionLog'][] = ["name"=>"Rework Qty","textAlign"=>"center"];
    
	/* Control Plan Data */
	$data['controlPlan'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['controlPlan'][] = ["name"=>"Part Code"];
	$data['controlPlan'][] = ["name"=>"Part Name"];
	$data['controlPlan'][] = ["name"=>"Part Size"];
	$data['controlPlan'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];

    /* Line Inspection Data */
    $data['lineInspection'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['lineInspection'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['lineInspection'][] = ["name"=>"Date"];
	$data['lineInspection'][] = ["name"=>"Job Card"];
    $data['lineInspection'][] = ["name"=>"Part Code"];
    $data['lineInspection'][] = ["name"=>"Part Name"];
	$data['lineInspection'][] = ["name"=>"Process"];
	$data['lineInspection'][] = ["name"=>"Sample Qty."];
	$data['lineInspection'][] = ["name"=>"Inspector"];
	
	/* Gauge Header */
    $data['gauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"Gauge Size"];
    $data['gauges'][] = ["name"=>"Inst. Code No."];
    $data['gauges'][] = ["name"=>"Make"];
    $data['gauges'][] = ["name"=>"Thread Type"];
    $data['gauges'][] = ["name"=>"Required"];
    $data['gauges'][] = ["name"=>"Frequency <small>(In months)</small>"];
    $data['gauges'][] = ["name"=>"Location"];
    $data['gauges'][] = ["name"=>"Inhouse/Outside"];
	$data['gauges'][] = ["name"=>"Cal Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Due Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Plan Date","style"=>"width:80px;"];
	$data['gauges'][] = ["name"=>"Remark"];

    /* Instrument Header */
	$data['instrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"Description of Instrument","style"=>"width:200px !important;"];
	$data['instrument'][] = ["name"=>"Inst. Code No."];
	$data['instrument'][] = ["name"=>"Make"];
	$data['instrument'][] = ["name"=>"Range (mm)"];
	$data['instrument'][] = ["name"=>"Least Count"];
	$data['instrument'][] = ["name"=>"Permissible Error"];
	$data['instrument'][] = ["name"=>"Required"];
	$data['instrument'][] = ["name"=>"Frequency <small>(In months)</small>"];
	$data['instrument'][] = ["name"=>"Inhouse/Outside"];
	$data['instrument'][] = ["name"=>"Cal Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Due Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Plan Date","style"=>"width:20%;"];
	$data['instrument'][] = ["name"=>"Remark"];
	
	/* Test Certificate Header */
    $data['testCertificate'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['testCertificate'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['testCertificate'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['testCertificate'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['testCertificate'][] = ["name"=>"Customer Name"]; 

    return tableHeader($data[$page]);
}

/* RM Inspection Data */
function getInspectionParamData($data){
    $btn = '<button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Product Inspection" data-srposition="0" datatip="Inspection" flow="left"><i class="fas fa-info"></i></button>';

    return [$data->sr_no,$data->item_name,$btn];
}

function getJobWorkInspectionData($data)
{
    $reportButton = '<a href="'.base_url('jobWorkInspection/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>';
    $pdfButton = '<a href="'.base_url('jobWorkInspection/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($reportButton.$pdfButton);
    return [$action,$data->sr_no,formatDate($data->entry_date),$data->challan_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->charge_no,$data->process_name,$data->in_qty,$data->out_qty];
} 

/*
* Create By : 
* Updated By : NYN @04-11-2021 12:48 AM 
* Note : Reject BTN
*/
/* Purchase Material Inspection Table Data */
function getPurchaseMaterialInspectionData($data){
    $inspection = ''; $tcBtn = ''; $editTcBtn = ''; $order_no = '';
    if(!empty($data->tc_no)):
		if($data->inspected_qty == '0.000'):
			$inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success  getInspectedMaterial permission-modify" data-grn_id="'.$data->grn_id.'" data-trans_id="'.$data->id.'" data-grn_prefix="'.$data->grn_prefix.'" data-grn_no="'.$data->grn_no.'" data-grn_date="'.date("d-m-Y",strtotime($data->grn_date)).'" data-item_name="'.$data->item_name.'" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';
			
			//$editTcParam = "{'grn_trans_id' : ".$data->id.",'grn_id' : ".$data->grn_id.", 'type':'2', 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'Update TC Parameter', 'fnedit' : 'getTcInspectionParam', 'fnsave' : 'saveTcInspectionParam'}";
			//$editTcBtn = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="Update TC Parameter" flow="down" onclick="tcInspe('.$editTcParam.');"><i class="fa fa-edit"></i></a>';
		endif;
		$editTcParam = "{'grn_trans_id' : ".$data->id.",'grn_id' : ".$data->grn_id.", 'type':'2', 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'Update TC Parameter', 'fnedit' : 'getTcInspectionParam', 'fnsave' : 'saveTcInspectionParam'}";
		$editTcBtn = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="Update TC Parameter" flow="down" onclick="tcInspe('.$editTcParam.');"><i class="fa fa-edit"></i></a>';
	else:
        $tcParam = "{'grn_trans_id' : ".$data->id.",'grn_id' : ".$data->grn_id.",'type':'1', 'modal_id' : 'modal-xl', 'form_id' : 'tcParameter', 'title' : 'TC Parameter', 'fnedit' : 'getTcInspectionParam', 'fnsave' : 'saveTcInspectionParam'}";
        $tcBtn = '<a class="btn btn-info btn-approval permission-modify" href="javascript:void(0)" datatip="TC Parameter" flow="down" onclick="tcInspe('.$tcParam.');"><i class="ti-files" ></i></a>';
    endif;

	$iirButton = '<a href="'.base_url('grn/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 
    $iirPdf = '<a href="'.base_url('grn/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    
    $tcpdf = '<a href="'.base_url('grn/printTc/'.$data->id).'" type="button" class="btn btn-primary" datatip="TC Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>'; 

    if(!empty($data->po_no) and !empty($data->po_prefix)):
		$order_no = getPrefixNumber($data->po_prefix,$data->po_no);
	endif;

    $action = getActionButton($inspection.$editTcBtn.$tcBtn.$tcpdf.$iirButton.$iirPdf);
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),date("d-m-Y",strtotime($data->grn_date)),$data->challan_no,$order_no,$data->party_name,$data->item_name,$data->product_code,$data->qty,$data->batch_no,$data->color_code, $data->status_label];
}

/* get PreDispatch Inspect Data */
function getPreDispatchInspectData($data){
    $deleteParam = $data->id.",'PreDispatch Inspection'";
    $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('preDispatchInspect/printFinalInspection/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

	$action = getActionButton($printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->report_number,date("d-m-Y",strtotime($data->date)),$data->item_code,$data->item_name];
}

function getOutChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>getPrefixNumber($data->challan_prefix,$data->challan_no),'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Receive' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-reply'></i></a>";
    endif;

    $collected_name = (!empty($data->collected_code))? "[".$data->collected_code."] ".$data->collected_name : "";

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->challan_prefix,$data->challan_no),formatDate($data->challan_date),$data->party_name,$collected_name,$data->process_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Get In Challan Data */
function getInChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>$data->doc_no,'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Return' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-share'></i></a>";
    endif;

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->doc_no,formatDate($data->challan_date),$data->party_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Instrument Data */
function getInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editInstrument', 'title' : 'Update Instrument', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('instrument/printInstrumentData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnSave' : 'saveCalibration'}";
    $calibrationButton = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';

    $action = getActionButton($printBtn.$calibrationButton.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date."-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date."-".($data->cal_reminder + 1)." days")) : '';
	
	if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->make_brand,$data->instrument_range,$data->least_count,$data->permissible_error,$data->cal_required,$data->cal_freq,$data->cal_agency,$lcd,$ncd,$pdate,$data->description];
} 

/* Gauge Data */
function getGaugeData($data){ //print_r($data);exit;
    $deleteParam = $data->id.",'Gauge'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnSave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url('gauges/printGaugesData/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        
    $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnSave' : 'saveCalibration'}";
    $calibrationButton = '<a class="btn btn-info btn-contact permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';

    $action = getActionButton($printBtn.$calibrationButton.$editButton.$deleteButton);     

    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
    return [$action,$data->sr_no,$data->size,$data->item_code,$data->make_brand,$data->thread_type,$data->cal_required,$data->cal_freq,$data->location,$data->cal_agency,$lcd,$ncd,$pdate,$data->description];
}

function getFinalInspectionData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'finalInspection', 'title' : 'Final Inspection', 'product_name': '".trimQuotes($data->item_name)."' , 'pending_qty' : '".$data->pending_qty."','rejection_type_id': '".$data->rejection_type_id."', 'product_id': '".$data->product_id."', 'job_card_id' : '".$data->job_card_id."', 'job_inward_id' : '".$data->job_inward_id."', 'operator_id':'".$data->operator_id."', 'machine_id' : '".$data->machine_id."',  'button':'close'}";

    $edParam = [
        'id' => $data->id, 'modal_id' => 'modal-lg', 'form_id' => 'finalInspection', 'title' => 'Final Inspection', 'product_name'=> $data->item_name , 'pending_qty' => $data->pending_qty,'rejection_type_id'=> $data->rejection_type_id, 'product_id'=> $data->product_id, 'job_card_id' => $data->job_card_id, 'job_inward_id' => $data->job_inward_id, 'operator_id'=>$data->operator_id, 'machine_id' => $data->machine_id,  'button'=>'close'        
    ];

    $editButton = "<a class='btn btn-success btn-edit permission-modify' href='javascript:void(0)' datatip='Edit' flow='down' onclick='inspection(".json_encode($edParam).");'><i class='ti-pencil-alt' ></i></a>";

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,(!empty($data->process_name))?$data->process_name:"Material Fault",$data->item_name,$data->qty,$data->pending_qty];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    if($data->type == 1 || $data->type == 4):
        $rejection_type = ($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));

        $deleteParam = $data->id.",".($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Rejection/Rework Comment'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->remark,$rejection_type];
    elseif($data->type == 2):
        $deleteParam = $data->id.",'Idle Reason'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Idle Reason'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->code,$data->remark];
    endif;
}


/* Assign Inspector Data */
function getAssignInspectorData($data){
    $editButton = "";
    if($data->status != 3):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAssignInspector', 'title' : 'Assign Inspector', 'fnEdit' : 'assignInspector'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Assign Inspector" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->machine_no,$data->setter_name,$data->inspector_name,$data->assign_status,$data->remark];
}

/* Setup Inspector Data */
function getSetupInspectionData($data){
    $editButton = "";$attachmentLink = "";$acceptInspection = "";

    if(!empty($data->inspection_start_date)):
        if(!empty($data->setup_end_time) && !empty($data->qci_id)):
            $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSetupInspection', 'title' : 'Setup Inspection', 'fnEdit' : 'setupInspection'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Setup Inspection" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        endif;
        
        if(!empty($data->attachment)):
            $attachmentLink = '<a href="'.base_url('assets/uploads/setup_ins_report/'.$data->attachment).'" class="btn btn-outline-info waves-effect waves-light"><i class="fa fa-arrow-down"> Download</a>';
        endif;
    else:
        if(!empty($data->qci_id)):
            $acceptInspection = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="acceptInspection('.$data->id.');"><i class="fas fa-check" ></i></a>';
        endif;
    endif;

    $action = getActionButton($acceptInspection.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setter_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->inspection_start_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_start_date)):"",(!empty($data->inspection_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_date)):"",$data->duration,$data->qci_note,$attachmentLink];
}

function getInspectionTypeData($data)
{
    $deleteParam = $data->id . ",'Inspection type'";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'editInspectionType', 'title' : 'Update Inspection Type'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit(' . $editParam . ');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton . $deleteButton);

    return [$action, $data->sr_no, $data->inspection_type];
}

function getNCReportData($data){
    $action = "";
    $deleteParam = $data->id.",'Production Log'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'ncReport', 'title' : 'Update NC Report'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),formatDate($data->log_date),$data->process_name,$data->emp_name,$data->production_qty,$data->rej_qty,$data->rw_qty];
}

// Created By Avruti @22/04/2022
function getRejectionLogData($data){
    $action = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'rejectionLog', 'title' : 'Update Rejection Log'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteParam = $data->id.",'Rejection Log'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
	$rwQty = '<a href="'.base_url('rejectionLog/reworkmanagement/'.$data->id).'" target="_blank">'.$data->rw_qty.'</a>';
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix, $data->job_no),$data->product_name,formatDate($data->log_date),$data->process_name,$data->item_code,$data->emp_name,$data->rej_qty,$rwQty];
}

/* Control Plan Data */
function getControlPlanData($data){
    $btn = '<div class="btn-group" role="group" aria-label="Basic example">
		<button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'"  data-button="both" data-modal_id="modal-lg" data-function="getInspectionParameter" data-form_title="Inspection Parameter" datatip="Inspection Parameter" flow="left">Add Parameter</button>
	</div>';
    // <button type="button" class="btn btn-info addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_code.'" data-param_type="2"  data-button="both" data-modal_id="modal-lg" data-function="getInspectionParameter" data-form_title="Final Inspection" datatip="Final Inspection" flow="left">FI</button>

    return [$data->sr_no,$data->item_code,$data->item_name,$data->size,$btn];
}

/* Line Inspect Data */
function getLineInspectData($data){
    $deleteParam = $data->id.",'Line Inspection'";
    $editButton = '<a href="'.base_url('controlPlan/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $printBtn = '<a class="btn btn-info btn-edit" href="'.base_url('controlPlan/printLineInspection/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
   
    $action = getActionButton($printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->insp_date),$data->job_number,$data->item_code,$data->item_name,$data->process_name,$data->sampling_qty,$data->emp_name];
}



/* Instrument Data */
function getQcInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Instrument', 'fnedit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Instrument" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Instrument', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
    
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkInstChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $deleteButton='';
    $action = getActionButton($reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
	if(in_array($data->status,[1,5]))
	{
        return [$action,$data->sr_no,$selectBox,$data->item_code,$data->mfg_sr,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
	else
	{
        return [$action,$data->sr_no,$data->item_code,$data->mfg_sr,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
}

/* QC Purchase Table Data */
function getQCPurchaseData($data){
    $deleteParam = $data->order_id.",'QC Purchase'";
    $grn = "";$edit = "";$delete = ""; $receive = "";
    /** Updated By Karmi */
    if($data->order_status == 0 && $data->rec_qty < $data->qty):       
        //$grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Create GIR" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';
        
        $receive = '<a href="javascript:void(0)" class="btn btn-primary purchaseReceive permission-modify" data-po_id="'.$data->order_id.'" datatip="Receive" flow="down"><i class="fas fa-reply" ></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	//$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

	$printBtn = '<a class="btn btn-info btn-edit permission-approve" href="'.base_url($data->controller.'/printQP/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($printBtn.$receive.$grn.$edit.$delete);
		
    return [$action,$data->sr_no,$data->po_prefix.$data->po_no,formatDate($data->po_date),$data->party_name,'['.$data->category_code.'] '.$data->category_name,$data->price,$data->qty,$data->rec_qty,$data->pending_qty,formatDate($data->delivery_date)];
}

/* Qc Indent Data  */
function getQCIndentData($data){
    $rejParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'fnsave' : 'rejectQCPR', 'title' : 'Reject QC PR'}";
    $rejectBtn="";
    if($data->status == 0):       
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        //$rejectBtn = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Reject QC PR" flow="down" onclick="edit('.$rejParam.');"><i class="ti-na"></i></a>';
    endif;
    $action = getActionButton($rejectBtn);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkQcRequest" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
	$desciption = '['.$data->req_itm_code.'] '.$data->category_name.' '.$data->size;
	if($data->item_type == 2 AND !empty($data->least_count)){$desciption .= ' '.$data->least_count;}
	
    return [$action,$data->sr_no,$selectBox,$data->req_date,$data->req_number,$desciption,$data->make,$data->qty,formatDate($data->delivery_date)];
}

/* QC Purchase Table Data */
function getQCPRData($data){
    $deleteParam = $data->id.",'QC Purchase Request'";
    
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editQCPR', 'title' : 'Update QC PR'}";
    $edit = "";$delete = "";
    
    if($data->status == 0):       
        //$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $edit = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
	
	$action = getActionButton($edit.$delete);
    
	$desciption = '['.$data->req_itm_code.'] '.$data->category_name.' '.$data->size;
	if($data->item_type == 2 AND !empty($data->least_count)){$desciption .= ' ('.$data->least_count.')';}
		
    return [$action,$data->sr_no,$data->req_date,$data->req_number,$desciption,$data->make,$data->qty,formatDate($data->delivery_date),$data->reject_reason];
}

/* QcChallan Data */
function getQcChallanData($data){
    $returnBtn=''; $caliBtn=''; $edit=''; $delete='';
    
    if(empty($data->receive_by)){
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->challan_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $deleteParam = $data->challan_id.",'Challan'";
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trashQcChallan('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        if($data->challan_type != 3){
            $rtnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'button':'close', 'form_id' : 'returnChallan', 'title' : 'Return Challan', 'fnedit' : 'returnChallan'}";
            $returnBtn = '<a href="javascript:void(0)" class="btn btn-info permission-modify" onclick="returnQcChallan('.$rtnParam.');" datatip="Return" flow="down"><i class="fas fa-reply"></i></a>';
        }else{
            $calParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'calibration', 'title' : 'Calibration ', 'fnEdit' : 'getCalibration', 'fnsave' : 'saveCalibration'}";
            $caliBtn = '<a class="btn btn-info permission-modify" href="javascript:void(0)" datatip="Calibration" flow="down" onclick="edit('.$calParam.');"><i class="fas fa-tachometer-alt"></i></a>';
        }
    }else{
        $caliBtn = '<a class="btn btn-info confirmChallan permission-modify" data-id="'.$data->id.'" data-challan_id="'.$data->challan_id.'" data-item_id="'.$data->item_id.'" href="javascript:void(0)" datatip="Confirm" flow="down"><i class="ti-check"></i></a>';
    }

    $data->party_name = (!empty($data->party_name))?$data->party_name:'IN-HOUSE';
    $data->challan_type = (($data->challan_type==1)? 'IN-House Issue': (($data->challan_type==2) ? 'Vendor Issue':'Calibration'));
    
    $action = getActionButton($caliBtn.$returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->trans_prefix.$data->trans_no,formatDate($data->trans_date),$data->challan_type,$data->party_name,$data->item_name,$data->item_remark];
}

/* Gauge Data */
function getQcGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="right"><i class="ti-trash"></i></a>';

    $inward=''; $reject=''; $editButton = '';
    if(empty($data->status)){
        $inwardParam = "{'id' : ".$data->id.", 'status' : '1', 'modal_id' : 'modal-lg', 'form_id' : 'inwardGauge', 'title' : 'Inward Gauge', 'fnedit':'inwardGauge', 'fnsave' : 'save'}";
        $inward = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Inward Gauge" flow="right" onclick="inwardGauge('.$inwardParam.');"><i class="fas fa-reply" ></i></a>';
    }elseif($data->status == 1){
        $reject = '<a href="javascript:void(0)" class="btn btn-dark rejectGauge permission-modify" data-id="'.$data->id.'" data-gauge_code="'.$data->item_code.'" datatip="Reject" flow="down"><i class="ti-close" ></i></a>';
    
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="right" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    }
    
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkGaugeChallan" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    
    $deleteButton='';
    $action = getActionButton($reject.$inward.$editButton.$deleteButton);    
    
    $lcd = (!empty($data->last_cal_date)) ? date('d-m-Y',strtotime($data->last_cal_date)) : '';
    $ncd = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-1 days")) : '';
    $pdate = (!empty($data->next_cal_date)) ? date('d-m-Y',strtotime($data->next_cal_date. "-".($data->cal_reminder+1)." days")) : '';
    if(!empty($ncd) AND (strtotime($ncd) <= strtotime(date('d-m-Y')))){$ncd = '<strong class="text-danger">'.$ncd.'</strong>';}
	if(!empty($pdate) AND (strtotime($pdate) <= strtotime(date('d-m-Y')))){$pdate = '<strong style="color:#ffbc34;">'.$pdate.'</strong>';}
	
	if(in_array($data->status,[1,5]))
	{
        return [$action,$data->sr_no,$selectBox,$data->item_code,$data->mfg_sr,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
	else
	{
        return [$action,$data->sr_no,$data->item_code,$data->mfg_sr,$data->item_name,$data->make_brand,$data->cal_required,$data->cal_freq,$data->location_name,$lcd,$ncd,$pdate];
	}
}


/* Test Certificate Table Data */
function getTestCertificateData($data){

    $tcPrint = '<a href="'.base_url('salesInvoice/printTestReport/'.$data->id).'" class="btn btn-primary btn-edit permission-modify" datatip="Print Test Certificate" flow="down" target="_blank"><i class="fa fa-print"></i></a>';

    $action = getActionButton($tcPrint);

    return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name];
}
?>