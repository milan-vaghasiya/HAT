<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



/* get Pagewise Table Header */

function getDispatchDtHeader($page)

{	   

    /* packing Header */
    /*$data['packing'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['packing'][] = ["name"=>"Packing No."];
    $data['packing'][] = ["name"=>"Item Code"];
    $data['packing'][] = ["name"=>"Item Name"];
    $data['packing'][] = ["name"=>"Qty."];
    $data['packing'][] = ["name"=>"Packed Qty."];
    $data['packing'][] = ["name"=>"Packing Date"];
    $data['packing'][] = ["name"=>"Remark"];
    $data['packing'][] = ["name"=>"Status","textAlign"=>"center"];*/
    
    /* packing Header */
    $data['packing'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['packing'][] = ["name"=>"Packing Date"];
    $data['packing'][] = ["name"=>"Packing No."];
    $data['packing'][] = ["name"=>"Packing Type"];
    $data['packing'][] = ["name"=>"Customer"];
    $data['packing'][] = ["name"=>"Total Net <br> Weight (Kg)"];
    $data['packing'][] = ["name"=>"Total Wooden <br> Box Weight (Kg)"];
    $data['packing'][] = ["name"=>"Total Gross <br> Weight (Kg)"];
    $data['packing'][] = ["name"=>"Status","textAlign"=>"center"];



    /* packing bom Header */

    $data['packingBom'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0]; 
    $data['packingBom'][] = ["name"=>"Product"];
    $data['packingBom'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

	
     /* Delivery Challan Header */

    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"]; 


    /* Stock Adjustment Header */
    $data['stockAdjustment'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['stockAdjustment'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['stockAdjustment'][] = ["name"=>"Product"];
    $data['stockAdjustment'][] = ["name"=>"Location"];
    $data['stockAdjustment'][] = ["name"=>"Batch No."]; 
    $data['stockAdjustment'][] = ["name"=>"Stock Qty.","textAlign"=>"center"];
    
    /* Planned So Header */
    $data['plannedSo'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['plannedSo'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['plannedSo'][] = ["name"=>"Plan Date"];
    $data['plannedSo'][] = ["name"=>"Plan No"];
    $data['plannedSo'][] = ["name"=>"So No"]; 
    $data['plannedSo'][] = ["name"=>"Customer","textAlign"=>"center"];
    $data['plannedSo'][] = ["name"=>"Item","textAlign"=>"center"];
    $data['plannedSo'][] = ["name"=>"Plan Qty","textAlign"=>"center"];
    
    return tableHeader($data[$page]);

}



function getPackingData($data){    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('packing/edit/'.$data->id).'" datatip="Edit" flow="down"><i class="fa fa-edit" ></i></a>';
    $delete="";$printBtn=""; 
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'" data-soid="'.$data->trans_main_id.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $deleteParam = $data->id.",'Packing'";
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $internalPrintBtn = '<a class="btn btn-warning btn-edit " href="'.base_url('packing/packingPdf/'.$data->id).'" target="_blank" datatip="Internal Print" flow="down"><i class="fas fa-print" ></i></a>';
    $customerPrintBtn = '<a class="btn btn-primary btn-edit " href="'.base_url('packing/packingPdf/'.$data->id.'/1').'" target="_blank" datatip="Customer Print" flow="down"><i class="fas fa-print" ></i></a>';
    $customPrintBtn = '<a class="btn btn-dark btn-edit " href="'.base_url('packing/packingPdf/'.$data->id.'/2').'" target="_blank" datatip="Custom Print" flow="down"><i class="fas fa-print" ></i></a>';

    $migrateItemNamesParam = $data->id.",'packing'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';
    $packingPrint = '<a href="javascript:void(0)" class="btn btn-danger packingTag permission-write" data-id="'.$data->id.'" data-soid="'.$data->trans_main_id.'" data-packing_sticker="'.$data->packing_sticker.'" datatip="Print Packing Tag" flow="down"><i class="fas fa-print" ></i></a>';
    
    $packingNo = (!empty($data->trans_number))?$data->trans_number:"Self Packing";
    if(!empty($data->entry_date)){ $packingDate = formatDate($data->entry_date); } else { $packingDate = ''; }
    $action = getActionButton($itemList.$packingPrint.$internalPrintBtn.$customerPrintBtn.$customPrintBtn.$migrateItemNames.$editButton.$delete);
    return [$action,$data->sr_no,$packingDate,$packingNo,$data->entry_type,$data->party_code,$data->total_net_weight,$data->total_wooden_box_weight,$data->total_gross_weight,$data->packing_status_label];
}



function getPackingBomData($data){

    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPackingBom', 'title' : 'Update Packing BOM'}";

    $btn = '<div class="btn-group" role="group" aria-label="Basic example">

                <a class="btn btn-success btn-sm btn-edit permission-modify" href="javascript:void(0)" datatip="BOM" flow="down" onclick="edit('.$editParam.');"><i class="fas fa-dolly-flatbed"></i></a>

            </div>';



    return [$data->sr_no,$data->item_code,$btn];

}



/* Delivery Challan */

function getDeliveryChallansData($data){

    $deleteParam = $data->trans_main_id.",'Delivery Challan'";

    $invoice = "";$edit = "";$delete = "";

    if(empty($data->trans_status)):

        $invoice = '<a href="javascript:void(0)" class="btn btn-primary createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    



        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';



        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    endif;	



    $action = getActionButton($invoice.$edit.$delete);



    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_code,$data->item_name,floatVal($data->qty)];

}


/* Stock Adjustment Data*/
function getStockAdjustmentData($data){ 
    $stfParam = "{'location_id':".$data->location_id.",'transfer_location':".$data->transfer_location.",'item_id':".$data->item_id.",'stock_qty':".floatVal($data->current_stock).",'batch_no':'".$data->batch_no."','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
    $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer('.$stfParam.');"><i class="ti-control-shuffle" ></i></a>';

    
    $itemName = (!empty($data->item_code))?'['.$data->item_code.'] '.$data->item_name:$data->item_name;
    $action = getActionButton($stfBtn);

    return [$action,$data->sr_no,$itemName,$data->store_name.' ['.$data->location.']',$data->batch_no,$data->current_stock];
}

function getPlannedSOData($data){
    $printBtn = '<a class="btn btn-primary btn-edit " href="'.base_url('dispatchPlan/printPlanning/'.$data->trans_number).'" target="_blank" datatip="Plan Print" flow="down"><i class="fas fa-print" ></i></a>';
    $action = getActionButton($printBtn);

    return [$action,$data->sr_no,formatDate($data->trans_date),$data->trans_number,getPrefixNumber($data->so_prefix,$data->so_no),$data->party_code,$data->item_name,floatval($data->qty)];
}

?>