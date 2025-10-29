<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* store header */
    $data['store'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['store'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['store'][] = ["name"=>"Store Name"];
    $data['store'][] = ["name"=>"Location"];
    $data['store'][] = ["name"=>"Remark"];

    /* Dispatch Material */ 
    $data['jobMaterialDispatch'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Job No.", "style" => "width:9%;"];
    $data['jobMaterialDispatch'][] = ["name" => "Request Date", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Item Name"];
    //$data['jobMaterialDispatch'][] = ["name" => "Allocated Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Requested Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Issue Date", "textAlign" => "center"];
    $data['jobMaterialDispatch'][] = ["name" => "Pending Qty", "textAlign" => "center"];
    
    /* Allocated Material */    
    $data['allocatedMaterial'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Job No.", "style" => "width:9%;"];
    $data['allocatedMaterial'][] = ["name" => "Allocated Date", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Item Name"];
    $data['allocatedMaterial'][] = ["name" => "Location"];
    $data['allocatedMaterial'][] = ["name" => "Batch No."];
    $data['allocatedMaterial'][] = ["name" => "Allocated Qty", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Issue Qty", "textAlign" => "center"];
    $data['allocatedMaterial'][] = ["name" => "Pending Qty", "textAlign" => "center"];

    $data['toolsIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue Date","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue Qty","textAlign"=>"center"];

    /* Item Header */
    $data['items'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"Item Code"];
    $data['items'][] = ["name"=>"Item Name"];
    $data['items'][] = ["name"=>"Category"];
    $data['items'][] = ["name"=>"HSN Code"];
    //$data['items'][] = ["name"=>"Opening Qty"];
    //$data['items'][] = ["name"=>"Stock Qty"];
    //$data['items'][] = ["name"=>"Manage Stock"];

    /* Capital Goods Header */
    $data['capitalGoods'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['capitalGoods'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['capitalGoods'][] = ["name"=>"Item Name"];
    $data['capitalGoods'][] = ["name"=>"Category"];
    $data['capitalGoods'][] = ["name"=>"Opening Qty"];
    $data['capitalGoods'][] = ["name"=>"Stock Qty"];
    $data['capitalGoods'][] = ["name"=>"Manage Stock"];

    /* Item Header */
    $data['storeItem'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
	$data['storeItem'][] = ["name"=>"Item Code"];
    $data['storeItem'][] = ["name"=>"Item Name"];
    $data['storeItem'][] = ["name"=>"HSN Code"];
    $data['storeItem'][] = ["name"=>"Opening Qty","textAlign"=>"right"];
    $data['storeItem'][] = ["name"=>"Stock Qty","textAlign"=>"right"];

    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Part Name"];
    $data['stockVerification'][] = ["name"=>"Part No."];
    $data['stockVerification'][] = ["name"=>"Stock Register Qty."];
    $data['stockVerification'][] = ["name"=>"Physical Qty."];
    $data['stockVerification'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];

	/* Stock Journal Header */
    $data['stockJournal'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['stockJournal'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['stockJournal'][] = ["name"=>"Date"];
    $data['stockJournal'][] = ["name"=>"RM Item Name"];
    $data['stockJournal'][] = ["name"=>"RM Qty."];
    $data['stockJournal'][] = ["name"=>"FG Item Name"];
    $data['stockJournal'][] = ["name"=>"FG Qty."];
    $data['stockJournal'][] = ["name"=>"Remark"];

    /* GRN Header */
    $data['grn'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"GRN No."];
	$data['grn'][] = ["name"=>"Challan No."];
    $data['grn'][] = ["name"=>"GRN Date"];
    $data['grn'][] = ["name"=>"Order No."];
    $data['grn'][] = ["name"=>"Supplier/Customer"];
    $data['grn'][] = ["name"=>"Item"];
    $data['grn'][] = ["name"=>"Qty"];
    $data['grn'][] = ["name"=>"UOM"];
    $data['grn'][] = ["name"=>"Qty.(Opt. UOM)"];
    $data['grn'][] = ["name"=>"F.G.(Used In)"];
    $data['grn'][] = ["name"=>"Heat/Batch No."];
    $data['grn'][] = ["name"=>"Colour Code"];	
    $data['grn'][] = ["name"=>"Location"];	
	
	 /* General Material Issue */
    $data['generalIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue No.","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue Date","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Item Name","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Location","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue Qty.","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Return Qty.","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Pending Qty.","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Issue By","style"=>"width:4%;","textAlign"=>"center"];
	$data['generalIssue'][] = ["name"=>"Collected By","style"=>"width:4%;","textAlign"=>"center"];
	
    /* Item Header */
	$data['rmStock'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['rmStock'][] = ["name"=>"ID"];
    $data['rmStock'][] = ["name"=>"LID"];
    $data['rmStock'][] = ["name"=>"Item Name"];
    $data['rmStock'][] = ["name"=>"Location"];
    $data['rmStock'][] = ["name"=>"Batch No"];
    $data['rmStock'][] = ["name"=>"Current Stock","textAlign"=>"right"];
    
    /* General Material Pending Request */
    $data['pendingRequest'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['pendingRequest'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Item Name"];
    $data['pendingRequest'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Requested Qty","textAlign"=>"center"];    
    $data['pendingRequest'][] = ["name"=>"Issue Qty","textAlign"=>"center"];
    $data['pendingRequest'][] = ["name"=>"Pending Qty","textAlign"=>"center"]; 
    
    /* General Material Request */
    $data['generalMaterialRequest'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['generalMaterialRequest'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Item Name"];
    $data['generalMaterialRequest'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['generalMaterialRequest'][] = ["name"=>"Requested Qty","textAlign"=>"center"]; 
    
    /* FG STock Header */
    $data['fgOpeningStock'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['fgOpeningStock'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['fgOpeningStock'][] = ["name"=>"Item"];
	$data['fgOpeningStock'][] = ["name"=>"Material Grade"];
    $data['fgOpeningStock'][] = ["name"=>"Batch No"];
    $data['fgOpeningStock'][] = ["name"=>"Heat No"];
    $data['fgOpeningStock'][] = ["name"=>"TC No"];
    $data['fgOpeningStock'][] = ["name"=>"Qty"];

    return tableHeader($data[$page]);
}

/* Store Table Data */
function getStoreData($data){
    $deleteParam = $data->id.",'Store'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";

    $editButton=''; $deleteButton='';
    if($data->store_type == 0){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	$action = getActionButton($editButton.$deleteButton);
	
    return [$action,$data->sr_no,$data->store_name,$data->location,$data->remark];
}

/* Job Material Dispatch Table Data */
function getJobMaterialIssueData($data){
    $dispatchBtn="";
    $pendingQty = $data->req_qty - $data->issue_qty;
    $pendingQty = ($pendingQty < 0)?0:floatVal(round($pendingQty,3));
    // if($pendingQty > 0):
        $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Material Issue','button':'close'}";
        $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="fas fa-paper-plane"></i></a>';     
    // endif;
    $action = getActionButton($dispatchBtn);

    return [$action,$data->sr_no,(!empty($data->job_number))?$data->job_number:"General Issue",(!empty($data->job_date))?date("d-m-Y",strtotime($data->job_date)):"",$data->item_name,$data->req_qty,$data->issue_qty,(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$pendingQty];
}


/* Job Tools Dispatch Table Data */
function getToolsIssueData($data){
    $deleteParam = $data->id.",'Dispatch'";
    $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Tools Issue'}";

    $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($dispatchBtn.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->issue_date))?date("d-m-Y",strtotime($data->issue_date)):"",$data->total_qty];
}

/* Item Table Data */
function getItemsData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
}

/* Capital Goods Table Data */
function getCapitalGoods($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
    return [$action,$data->sr_no,$data->item_name,$data->category_name,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
}

/* Store Item Table Data */
function getStoreItemData($data){
    $mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('store/itemStockTransfer/'.$data->id).'" class="'.$mq.'">'.number_format($data->qty ,3).' ('.$data->unit_name.')</a>';
	
    return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$qty];
}

/* Process Table Data */
function getStoresData($page,$data){
	
	switch($page)
	{
		case 'purchaseReport':
						return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,printDecimal($data->gst),printDecimal($data->qty)];
						break;
		case 'products':
						break;
	}
	return [];
}

/* Stock Journal Data */
function getStockjournalData($data){
    $deleteParam = $data->id.",'Stock journal'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($deleteButton);   
    return [$action,$data->sr_no,formatDate($data->date),$data->rm_name,$data->rm_qty,$data->fg_name,$data->fg_qty,$data->remark];
}

/* GRN Table Data */
function getGRNData($data){
    $deleteParam = $data->grn_id.",'GRN'";$itemList = "";

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->grn_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->grn_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$printBtn = '<a class="btn btn-warning btn-edit permission-modify" href="'.base_url($data->controller.'/printGrn/'.$data->grn_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    //$tcButton = '<a href="'.base_url('grn/inInspection/'.$data->grn_id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>'; 


	$action = '';$order_no = "";
	if($data->type == 1 && $data->inspected_qty < $data->qty):
		$action = getActionButton($printBtn.$itemList.$edit.$delete);
    else:
        $action = getActionButton($printBtn);
    endif;

    if($data->type == 2):
        $action = getActionButton($printBtn.$itemList.$edit.$delete);
	endif;

	if(!empty($data->po_no) and !empty($data->po_prefix)):
		$order_no = getPrefixNumber($data->po_prefix,$data->po_no);
	endif;
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),$data->challan_no,formatDate($data->grn_date),$order_no,$data->party_name,$data->item_name,$data->qty,$data->unit_name,$data->qty_kg,$data->product_code,$data->batch_no,$data->color_code,$data->store_name];
}

/* General Issue Table Data */
function getGeneralIssueData($data){
    $deleteParam = "'".($data->req_prefix.$data->req_no)."','Material'";
    $editParam = "{'id' : '".($data->req_prefix.$data->req_no)."', 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'General Issue'}";

    $editBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)"  onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = '';
    if(!empty($data->is_return)):
        $returnParam = "{'id' : '".$data->id."', 'modal_id' : 'modal-lg', 'form_id' : 'returnMaterial', 'title' : 'Material Return','fnEdit':'materialReturn', 'fnsave':'saveReturnMaterial'}";
        $returnBtn = '<a class="btn btn-warning btn-edit permission-modify" href="javascript:void(0)" datatip="Return" flow="down" onclick="edit('.$returnParam.');"><i class="fa fa-reply"></i></a>';
    endif;

    if(!empty($data->is_return) && ($data->dispatch_qty - $data->return_qty) <= 0):
        $returnBtn = ''; $editBtn = '';$deleteButton = '';
    endif;

    $action = getActionButton($returnBtn.$editBtn.$deleteButton);

    return [$action,$data->sr_no,$data->req_prefix.sprintf('%03d', $data->req_no),(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$data->item_name,$data->location,$data->dispatch_qty,$data->return_qty,((!empty($data->is_return))?($data->dispatch_qty - $data->return_qty):0),$data->issue_by,$data->collect_by];
}

function getStoreRMItemData($data){
    return [$data->item_id,$data->item_id,$data->location_id,$data->item_name,$data->location.'( '.$data->store_name.' )',$data->batch_no,number_format($data->current_stock ,3)];
}

function getGeneralPendingRequestData($data){
    $deleteParam = $data->id.",'Material'";  $pendingQty=0; 
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)"  onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $issueParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editStoreLocation', 'title' : 'Issue General Material'}";    
    
    $issueButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Issue Material" flow="down" onclick="edit('.$issueParam.');"><i class="fas fa-paper-plane"></i></a>';
    $pendingQty = $data->req_qty - $data->dispatch_qty;
    $action = getActionButton($issueButton.$deleteButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->item_name,floatVal($data->req_item_stock),floatVal($data->req_qty),floatVal($data->dispatch_qty),$pendingQty];
}

function getGeneralRequestData($data)
{
    $deleteParam = $data->id.",'Request'";   
    $deleteButton = '<a class="btn btn-danger btn-delete" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $action = getActionButton($deleteButton);
    return [$action,$data->sr_no,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->item_name,floatVal($data->req_item_stock),floatVal($data->req_qty)];
    
}

/* FG Opening Table Data */
function getFgOpeningData($data){
    $deleteParam = $data->id.",'Stock'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="deleteFgStock('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
	$action = getActionButton($deleteButton);
	
    return [$action,$data->sr_no,(!empty($data->fgitem_id)?$data->fg_item_name:$data->item_name),$data->material_grade,$data->fg_batch_no,$data->batch_no,$data->tc_no,floatval($data->qty)];

    // return [$action,$data->sr_no,(!empty($data->fgitem_id)?$data->fg_item_name:$data->item_name),$data->material_grade,$data->fg_batch_no,$data->heat_no,$data->tc_no,floatval($data->qty)];
}
?>