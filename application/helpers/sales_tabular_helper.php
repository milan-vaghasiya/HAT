<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
/* get Pagewise Table Header */

function getSalesDtHeader($page){	
    /* Party Header */
    $data['customer'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['customer'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['customer'][] = ["name"=>"Company Name"];
	$data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];
    $data['customer'][] = ["name"=>"Party Code"];
    $data['customer'][] = ["name"=>"Currency"];
    
	/* Sales Enquiry Header */
	$data['salesEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"Enq. No."];
    $data['salesEnquiry'][] = ["name"=>"Enq. Date"];
    $data['salesEnquiry'][] = ["name"=>"Customer Name"];
    //$data['salesEnquiry'][] = ["name"=>"Item Name"];
    //$data['salesEnquiry'][] = ["name"=>"Qty"];
    $data['salesEnquiry'][] = ["name"=>"Status"];
    $data['salesEnquiry'][] = ["name"=>"Quoted","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Not Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Remark"];

	/* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"Quote No."];
    $data['salesQuotation'][] = ["name"=>"Quote Date"];
    $data['salesQuotation'][] = ["name"=>"Customer Name"];
    //$data['salesQuotation'][] = ["name"=>"Product Name"];
    //$data['salesQuotation'][] = ["name"=>"Qty"];
    //$data['salesQuotation'][] = ["name"=>"Quote Price"];
    //$data['salesQuotation'][] = ["name"=>"Confirmed Price"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Date"];
    $data['salesQuotation'][] = ["name"=>"Enq. No."];

    /* Sales Order Header */
    $data['salesOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesOrder'][] = ["name"=>"#","textAlign"=>"center"];
	$data['salesOrder'][] = ["name"=>"SO. No.","style"=>"width:10%;","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"SO. Date","style"=>"width:10%;","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"SO. Type","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Customer"];
    $data['salesOrder'][] = ["name"=>"Cust. PO.NO."];
	$data['salesOrder'][] = ["name"=>"Quot. No."];
    $data['salesOrder'][] = ["name"=>"Product"];
    $data['salesOrder'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Pending Qty.","textAlign"=>"center","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Delivery Date","textAlign"=>"center"]; 
    $data['salesOrder'][] = ["name"=>"Status","textAlign"=>"center"]; 

    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Invoice No."]; 
    //$data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    //$data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];

    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Type","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Customer Name"]; 
    $data['salesInvoice'][] = ["name"=>"Cust. PO.NO."];
    $data['salesInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"right"];  

	/* packing instruction Header */
	$data['packingInstruction'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['packingInstruction'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['packingInstruction'][] = ["name"=>"Dispatch Date"];
	$data['packingInstruction'][] = ["name"=>"Item Code"];
	$data['packingInstruction'][] = ["name"=>"Item Name"];
	$data['packingInstruction'][] = ["name"=>"Qty."];
	$data['packingInstruction'][] = ["name"=>"Remark"];
	$data['packingInstruction'][] = ["name"=>"Status","textAlign"=>"center"];

	/* Product Header */
	$data['products'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"Product No."];
	$data['products'][] = ["name"=>"Item Name","style"=>"width:20%;"];
	$data['products'][] = ["name"=>"Part Size","style"=>"width:15%;"]; 
	$data['products'][] = ["name"=>"Finish Diamention"];
	$data['products'][] = ["name"=>"HSN Code"];
	$data['products'][] = ["name"=>"Part No"];
	$data['products'][] = ["name"=>"Customer Code"];
	$data['products'][] = ["name"=>"Drawing No."];
	$data['products'][] = ["name"=>"Rev. No."];
	$data['products'][] = ["name"=>"Price"];
	//$data['products'][] = ["name"=>"Opening Qty"];

	/*	Cycle Time Header */
    $data['cycleTime'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['cycleTime'][] = ["name"=>"Part Code"];
    $data['cycleTime'][] = ["name"=>"Manage Time","style"=>"width:20%;"];

    /* Tool Consumption Header */
    $data['toolConsumption'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['toolConsumption'][] = ["name"=>"Tool Description"];
    $data['toolConsumption'][] = ["name"=>"Action","style"=>"width:20%;"];

    /* Proforma Invoice Header */
    $data['proformaInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"Invoice No."];
    $data['proformaInvoice'][] = ["name"=>"Invoice Date"];
    $data['proformaInvoice'][] = ["name"=>"Customer Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Amount"]; 
    $data['proformaInvoice'][] = ["name"=>"Bill Amount"]; 
    
    /* feasibility Reason Header */
	$data['feasibilityReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['feasibilityReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['feasibilityReason'][] = ["name"=>"Type"];
	$data['feasibilityReason'][] = ["name"=>"Feasibility Reason"];

    /* Commercial Packing Header */
    $data['commercialPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Com. Pac. No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialPacking'][] = ["name"=>"Customer Name"]; 
    $data['commercialPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['commercialPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 
	
	/* Commercial Invoice Header */
    $data['commercialInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Com. INV. No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Customer Name"]; 
    $data['commercialInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"]; 

    /* Commercial Packing Header */
    $data['customPacking'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Cum. Pac. No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customPacking'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customPacking'][] = ["name"=>"Customer Name"]; 
    $data['customPacking'][] = ["name"=>"Total Net Weight","textAlign"=>"center"]; 
    $data['customPacking'][] = ["name"=>"Total Gross Weight","textAlign"=>"center"]; 

    /* Custom Invoice Header */
    $data['customInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Cum. INV. No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Packing No.","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Customer Name"]; 
    $data['customInvoice'][] = ["name"=>"Net Amount","textAlign"=>"center"];
    
    /* Request Header */
	$data['packingRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['packingRequest'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['packingRequest'][] = ["name"=>"Packing Date"];
	$data['packingRequest'][] = ["name"=>"Packing No."];
	$data['packingRequest'][] = ["name"=>"Item Name"];
	$data['packingRequest'][] = ["name"=>"Qty","textAlign"=>"center"];

    /* Feedback Header */
    $data['feedback'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['feedback'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['feedback'][] = ["name"=>"Feedback No."];
    $data['feedback'][] = ["name"=>"Customer"];
    $data['feedback'][] = ["name"=>"Survey From"];
    $data['feedback'][] = ["name"=>"Survey To"];
    $data['feedback'][] = ["name"=>"Feedback Link"];
    $data['feedback'][] = ["name"=>"Feedback By"];
    $data['feedback'][] = ["name"=>"Feedback Date"];
    
    /* Customer Complaints Header */
    $data['customerComplaints'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['customerComplaints'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['customerComplaints'][] = ["name"=>"SO. No.","style"=>"width:10%;","textAlign"=>"center"];
	$data['customerComplaints'][] = ["name"=>"Receipt Date"];
	$data['customerComplaints'][] = ["name"=>"Customer Name"];
    $data['customerComplaints'][] = ["name"=>"Ref. of Complaint"];
    $data['customerComplaints'][] = ["name"=>"Item Description"];
    $data['customerComplaints'][] = ["name"=>"Details of Complaint"];
    $data['customerComplaints'][] = ["name"=>"Product Returned"];
    $data['customerComplaints'][] = ["name"=>"Details of  Action Taken"];
    $data['customerComplaints'][] = ["name"=>"Reference of feed back to Customer"];
    $data['customerComplaints'][] = ["name"=>"Remarks"];


     /* Customer Rework Header */
     $data['customerRework'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['customerRework'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
     $data['customerRework'][] = ["name"=>"Debit. No.","style"=>"width:10%;","textAlign"=>"center"];
     $data['customerRework'][] = ["name"=>"Date"];
     $data['customerRework'][] = ["name"=>"Customer Name"];
     $data['customerRework'][] = ["name"=>"Inv No"];
     $data['customerRework'][] = ["name"=>"Item Description"];
     $data['customerRework'][] = ["name"=>"Qty"];
     $data['customerRework'][] = ["name"=>"Remarks"];
    
	return tableHeader($data[$page]);
}

/* Sales Enquiry Table Data */
function getSalesEnquiryData($data){
    $deleteParam = $data->trans_main_id.",'Sales Enquiry'";
    $closeParam = $data->trans_main_id.",'Sales Enquiry'";
    $edit = "";$delete = "";$close = "";$reopen = "";$quotation="";   $changeParty="";
    if(empty($data->trans_status)):
        $quotation = '<a href="'.base_url('salesQuotation/createQuotation/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Quotation" flow="down"><i class="fa fa-file-alt"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    elseif($data->trans_status == 1 && $data->notFisibalTab != 2):
        $changeParty = '<a href="javascript:void(0);" class="btn btn-warning changeParty" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" data-party_id="'.$data->party_id.'"  datatip="Change Party" flow="down"><i class="fas fa-retweet"></i></a>';
    else:
        $edit = '';//'<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        /*if($data->trans_status == 1):
            $close = '<a href="javascript:void(0)" class="btn btn-dark" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
        else:
            $reopen = '<a href="javascript:void(0)" class="btn btn-warning" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
        endif;*/
    endif;
    
    $quotedCount = ''; $fisibleCount =''; $notfisibalCount ='';
    if($data->notFisibalTab != 2){
        if(!empty($data->quotedCount) > 0){
            $quotedCount = '<a href="javascript:void(0);" class="getFeasibleData" data-status="3" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="QuotedCount" flow="down"><b>'.$data->quotedCount.'</b></a>';
        }
        if(!empty($data->fisibleCount) > 0){
            $fisibleCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="1" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="FisibleCount" flow="down"><b>'.$data->fisibleCount.'</b></a>';
        }
    } else {
        $quotedCount = "-";
        $fisibleCount = "-";
    }
    if(!empty($data->notfisibalCount) > 0){
        $notfisibalCount = '<a href="javascript:void(0);" class=" getFeasibleData" data-status="2" data-trans_main_id="'.$data->trans_main_id.'" data-enq_no="'.(getPrefixNumber($data->trans_prefix,$data->trans_no)).'" datatip="NotfisibalCount" flow="down"><b>'.$data->notfisibalCount.'</b></a>';
    }
    $action = getActionButton($quotation.$edit.$delete.$close.$reopen);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->status,$quotedCount,$fisibleCount,$notfisibalCount,$data->remark];
}

/* Sales Quotation Table Data */
function getSalesQuotationData($data){
    $deleteParam = $data->trans_main_id.",'Sales Quotation'";
    $closeParam = $data->trans_main_id.",'Sales Quotation'";
    $confirm = "";$edit = "";$delete = "";$saleOrder ="";$printBtn = '';$revision = ''; $followup="";
    if(empty($data->confirm_by)):
        $confirm = '<a href="javascript:void(0)" class="btn btn-info confirmQuotation permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getQuotationItems" data-form_title="Confirm Quotation" datatip="Confirm Quotation" flow="down"><i class="fa fa-check"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
		
		$followup='<a href="javascript:void(0)" class="btn btn-warning addFolloUp permission-write" data-id="'.$data->trans_main_id.'" data-button="both" data-modal_id="modal-lg" data-function="getFollowUp" data-form_title="Follow Up" datatip="Follow Up" flow="down"><i class="fa fa-list-ul"></i></a>';
		
        $revision = '<a href="'.base_url($data->controller.'/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';
		$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    else:
        //if(empty($data->trans_status)):
            $saleOrder = '<a href="javascript:void(0)" class="btn btn-info createSalesOrder permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg"  data-form_title="Create Sales Order" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
            //$saleOrder = '<a href="'.base_url('salesOrder/createOrder/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
        //endif;
    endif;
	
	$printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printQuotation/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printRevisionBtn = '<a class="btn btn-facebook btn-edit permission-approve createSalesQuotation"  datatip="View Revised Quatation" data-id="'.$data->trans_main_id.'" data-sq_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" flow="down"><i class="fas fa-eye" ></i></a>';
    $action = getActionButton($printBtn.$printRevisionBtn.$confirm.$followup.$revision.$edit.$delete.$saleOrder);
	
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no).' (Rev. No.'.$data->quote_rev_no.')',date("d-m-Y",strtotime($data->trans_date)),$data->party_name,(!empty($data->cod_date))?date("d-m-Y",strtotime($data->cod_date)):"",$data->ref_no];
}

/* Sales Order Table Data */
function getSalesOrderData($data){
    $deleteParam = $data->trans_main_id.",'Sales Order'";
    $view = ""; $edit = ""; $delete = ""; $complete = ""; $invoiceCreate = "";$dispatch = ""; $approve='';$invoice = "";$itemList='';
    $closeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'closeSalesOrder', 'title' : 'Close Sales Order', 'fnEdit' : 'closeSalesOrder', 'fnsave' : 'saveCloseSO'}";
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url($data->controller.'/salesOrder_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Order Acceptance" flow="down"><i class="fas fa-print" ></i></a>';
	$printSo = '<a class="btn btn-info btn-edit permission-modify" href="'.base_url($data->controller.'/salesOrder_pdf2/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';
	
    if(empty($data->trans_status)):
        if(!empty($data->is_approve == 0)){
            // $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down" ><i class="fa fa-check" ></i></a>';
            $approve = '<a href="javascript:void(0)" onclick="openView('.$data->trans_main_id.')" class="btn btn-info btn-edit permission-approve" datatip="Approve Order" flow="down"><i class="fa fa-check"></i></a>';
            $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';     
        }
        else{
            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveSOrder permission-approve" data-id="'.$data->trans_main_id.'" data-val="0" data-msg="Reject" datatip="Reject Order" flow="down" ><i class="ti-close" ></i></a>';
			$edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $dispatch = '<a href="javascript:void(0)" class="btn btn-primary createDeliveryChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Challan" flow="down"><i class="fa fa-truck" ></i></a>';
            $invoice = '<a href="javascript:void(0)" class="btn btn-primary createSalesInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';
            $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
        }
        $complete = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="edit('.$closeParam.');"><i class="fa fa-window-close"></i></a>';  
    endif;    
    $action = getActionButton($approve.$printBtn.$printSo.$itemList.$complete.$edit.$delete);
    $orderType = "";
    $salesType = "";
    if($data->sales_type == 1):
        $orderType = "Manufacturing";
        $salesType = "Domestics";
    elseif($data->sales_type == 2):
        $orderType = "Manufacturing";
        $salesType = "Export";
    elseif($data->sales_type == 3):
        $orderType = "Job Order";
        $salesType = "Jobwork <br>(Domestics)";
    endif;
	
	$responseData[] = $action;
	$responseData[] = $data->sr_no;
	$responseData[] = getPrefixNumber($data->trans_prefix,$data->trans_no);
    $responseData[] = formatDate($data->trans_date);
    $responseData[] = $salesType;
    $responseData[] = $data->party_name;    
    $responseData[] = $data->doc_no;
	$responseData[] = $data->ref_no;
    $responseData[] = (!empty($data->item_code))?'['.$data->item_code.'] '.$data->item_name : $data->item_name;
    $responseData[] = floatVal($data->qty);
    $responseData[] = floatVal($data->dispatch_qty);
    $responseData[] = floatVal($data->pending_qty);
    $responseData[] = formatDate($data->cod_date); 	
    $responseData[] = $data->order_status_label;
	return $responseData;
}

/* Delivery Challan */
function getDeliveryChallanData($data){
    $deleteParam = $data->trans_main_id.",'Delivery Challan'";
    $invoice = "";$edit = "";$delete = "";$itemList="";$printBtn="";$backPrint ="";
    if(empty($data->trans_status)):
        $invoice = '<a href="javascript:void(0)" class="btn btn-info createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $advisePrintBtn = '<a class="btn btn-dribbble btn-edit" href="'.base_url($data->controller.'/dispatch_advise_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Dispatch Advise" flow="down"><i class="fas fa-print" ></i></a>';

    if($data->party_id == 5):
        $backPrint = '<a class="btn btn-danger btn-edit" href="'.base_url('deliveryChallan/back_pdf_forBhavani/'.$data->trans_main_id).'" target="_blank" datatip="Back Print" flow="down"><i class="fas fa-print" ></i></a>';

        $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url('deliveryChallan/challan_pdf_Forbhvani/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    else:
        $printBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printInvoice" datatip="Print Delivery Challan" flow="down" data-id="'.$data->trans_main_id.'" data-function="challan_pdf"><i class="fa fa-print"></i></a>';
    endif;
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
    $action = getActionButton($advisePrintBtn.$printBtn.$backPrint.$invoice.$itemList.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_code,$data->inv_no];
}

/* Packing Instruction Table Data*/
function getPackingInstructionData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editPacking', 'title' : 'Update Packing Quantity'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
    $action = getActionButton($editButton);
    return [$action,$data->sr_no,formatDate($data->dispatch_date),$data->item_code,$data->item_name,$data->qty,$data->remark,$data->packing_status_label];
}

/* Sales Invoice Table Data */
function getSalesInvoiceData($data){
    $deleteParam = $data->id.",'Sales Invoice'";
    
    if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $copyInv = '<a href="'.base_url($data->controller.'/copyInv/'.$data->id).'" class="btn btn-info btn-edit permission-modify" datatip="Copy" flow="down"><i class="ti-write"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$printExport=""; $printCustom=""; $print="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
    }
    $tcPrint = '';//'<a href="'.base_url($data->controller.'/printTestReport/'.$data->id).'" class="btn btn-primary btn-edit permission-modify" datatip="Print Test Certificate" flow="down" target="_blank"><i class="fa fa-print"></i></a>';
    
    $packingSlipPrintBtn = '';
    if($data->is_available_packing_slip){
        $packingSlipPrintBtn = '<a class="btn btn-dribbble btn-edit" href="'.base_url($data->controller.'/packing_slip_pdf/'.$data->id).'" target="_blank" datatip="Print Packing Slip" flow="down"><i class="fas fa-print" ></i></a>';
    }

    $packingParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'packingForm', 'button':'close', 'title' : 'Packing Slip', fnEdit:'addPackingSlip', showHtml:true, fnhtml:'getPackingSlipItemList' }";
    $packingSlip = '<a class="btn btn-primary permission-approve" href="javascript:void(0)" datatip="Add Packing Slip" flow="down" onclick="editWithHtml('.$packingParam.');"><i class="fa fa-file-alt" ></i></a>'; 

    if($data->listType == 'LISTING')
    {
        $action = getActionButton($packingSlip.$packingSlipPrintBtn.$tcPrint.$printCustom.$printExport.$print.$edit.$delete);
    	if($data->tp == 'BILLWISE')
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$action,$data->sr_no,$data->trans_number,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
    
    if($data->listType == 'REPORT')
    {
        if($data->tp == 'ITEMWISE'){$data->id = $data->trans_main_id;}
        $trno = $data->trans_number;
        //if(in_array($data->userRole,[-1,1,3])){$trno= '<a href="'.base_url('salesInvoice/edit/'.$data->id).'" target="_blank" datatip="Edit Invoice" flow="right"> '.$data->trans_number.'</a>';}
          
    	if($data->tp == 'BILLWISE')
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
    	}
    	else
    	{
    		return [$data->sr_no,$trno,date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->item_name.' <small>'.$data->item_remark.'</small>',floatVal($data->qty),$data->price,$data->disc_amount,$data->amount];
    	}
    }
}
function getSalesInvoiceData00($data){
    $deleteParam = $data->id.",'Sales Invoice'";
    $itemlist="";
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
	$printExport=""; $printCustom=""; $print="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
    }
    
    if($data->sales_type == 1):
        $salesType = "Manufacturing (Domestics)";
    elseif($data->sales_type == 2):
        $salesType = "Manufacturing (Export)";
    elseif($data->sales_type == 3):
        $salesType = "Jobwork (Domestics)";
    endif;
	
    $action = getActionButton($printCustom.$printExport.$print.$itemList.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$salesType,$data->party_name,$data->po_no,$data->net_amount];
}

/* Proforma Invoice Table Data */
function getProformaInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Proforma Invoice'";
    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$print = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printInvoice" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'"><i class="fa fa-print"></i></a>';
	
    $action = getActionButton($print.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->inv_amount];
}

/* Product Table Data */
function getProductData($data){
    $deleteParam = $data->id.",'Product'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editProduct', 'title' : 'Update Product'}";
    $fgRevisionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'fgRevision', 'title' : 'Fg Revision', 'fnEdit' : 'getFgRevision', 'fnsave' : 'updateFgRevision'}";
    $fgRevisionButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Fg Revision" flow="down" onclick="edit('.$fgRevisionParam.');"><i class="fa fa-list"></i></a>';
    $setProductProcess = '<a href="javascript:void(0)" class="btn btn-info setProductProcess permission-modify" datatip="Set Product Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addProductProcess" data-form_title="Set Product Process" flow="down"><i class="fas fa-cogs"></i></a>';
    $viewProductProcess = '<a href="javascript:void(0)" class="btn btn-purple viewItemProcess permission-modify" datatip="View Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="View Product Process" flow="down"><i class="fa fa-list"></i></a>';
    $productKit = '<a href="javascript:void(0)" class="btn btn-warning productKit permission-modify" datatip="Product BOM" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Product BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></a>';
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';
    
    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	$openingStock = '';
	// $action = getActionButton($viewProductProcess.$productKit.$editButton.$deleteButton);
	$action = getActionButton($fgRevisionButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->size,$data->make_brand,$data->hsn_code,$data->part_no,$data->party_code,$data->drawing_no,$data->rev_no,$data->price];
}

/* Tool Cunsumption Table Data*/
function ToolConsumption($data){
    $toolConsumption = '<button type="button" class="btn waves-effect waves-light btn-outline-primary addToolConsumption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addToolConsumption" data-form_title="Add Tool Consumption">Add Tool Consumption</button>';
    return [$data->sr_no,$data->item_code,$toolConsumption];
}

/* Feasibility Reason Data  */
function getFeasibilityReasonData($data){
    
    $deleteParam = $data->id.",'Rejected Reason'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editFeasibilityReason', 'title' : 'Update Rejected Reason'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $type = ($data->type == 3)?"Item Feasibility":"Customer Feedback";
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$type,$data->remark];
}

/* Commercial Packing Data  */
function getCommercialPackingData($data){
    $deleteParam = $data->id.",'Commercial Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';


    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialPackingPdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Commercial Invoice Data */
function getCommercialInvoiceData($data){
    $deleteParam = $data->id.",'Commercial Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $excelBtn = '<a class="btn btn-facebook btn-edit" href="'.base_url($data->controller.'/commercialInvoicePdf/'.$data->id.'/EXCEL').'" target="_blank" datatip="Excel" flow="down"><i class="fa fa-file" ></i></a>';

    $action = getActionButton($migrateItemNames.$excelBtn.$printBtn.$edit.$delete);

    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}


/* Custom Packing Data */
function getCustomPackingData($data){
    $deleteParam = $data->id.",'Custom Packing'";

    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customPackingPdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $data->doc_date = (!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):"";
    $action = getActionButton($migrateItemNames.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,$data->doc_date,$data->party_name,$data->total_amount,$data->net_amount];
}

/* Custom Invoice Data */
function getCustomInvocieData($data){
    $deleteParam = $data->id.",'Custom Invoice'";
    
    $edit = '';$delete = '';
    if($data->trans_status == 0):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    
    $migrateItemNamesParam = $data->id.",'tc'";//first param is main table id and second param is table name alias for checking condition in model
    $migrateItemNames = '<a href="javascript:void(0)" class="btn btn-info" onclick="migrateItemNames('.$migrateItemNamesParam.');" datatip="Update Item Name" flow="down"><i class="fas fa-sync"></i></a>';

    
    $printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/customInvoicePdf/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    $evdPrintBtn = '<a class="btn btn-dark btn-edit" href="'.base_url($data->controller.'/evdPdf/'.$data->id).'" target="_blank" datatip="EVD Print" flow="down"><i class="fas fa-print" ></i></a>';
    $scometPrintBtn = '<a href="javascript:void(0)" class="btn btn-info btn-edit printScomet" datatip="Scomet Print" flow="down" data-id="'.$data->id.'" data-function="scometPrint"><i class="fa fa-print"></i></a>';

    $dbkPrintBtn = '<a class="btn btn-info btn-edit" href="'.base_url($data->controller.'/dbkPdf/'.$data->id).'" target="_blank" datatip="DBK Print" flow="down"><i class="fas fa-print" ></i></a>';

    $action = getActionButton($migrateItemNames.$dbkPrintBtn.$evdPrintBtn.$scometPrintBtn.$printBtn.$edit.$delete);
    return [$action,'',$data->trans_number,$data->packing_no,$data->doc_no,((!empty($data->doc_date))?date("d-m-Y",strtotime($data->doc_date)):""),$data->party_name,$data->net_amount];
}

/* Request Table Data*/
function getPackingRequestData($data){ 
    $deleteParam = $data->packing_id.",'Request'"; 
    $editButton=""; $deleteButton="";
    $editParam = "{'id' : ".$data->packing_id.", 'modal_id' : 'modal-lg', 'form_id' : 'packingRequset', 'title' : 'Packing Requset', 'fnEdit' : 'editPackingRequset'}";
    if($data->entry_type == "New Request"){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Packing Requset" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-edit"></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
    
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->entry_date),$data->trans_number,$data->item_name,$data->request_qty];
}

function getFeedbackData($data){

    $editButton='';$deleteButton='';$printBtn='';
    if(empty($data->feedback_by)){
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editOrderFeasibility', 'title' : 'Update Order Feasibility Commitment'}";
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
        $deleteParam = $data->id.",'Team Feasibility'";
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        
        $feedLink ='<a  href="'.base_url('CustomerFeedback/getFeedback/'.$data->id).'" target="_blank"  flow="down">'.base_url('CustomerFeedback/getFeedback/'.$data->id).'</a>';
    }else{
        $printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/printFeedback/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

        $feedLink='<span class="badge badge-pill badge-success m-1">Complete</span>';
    }
    
	$action = getActionButton($printBtn.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_prefix.sprintf('%04d',$data->trans_no),$data->party_name,formatDate($data->survey_from),formatDate($data->survey_to),$feedLink,$data->feedback_by,formatDate($data->feedback_at)];
}


/* Customer Complaints Table Data*/
function getCustomerComplaintsData($data){ 
    $editButton="";$deleteButton="";$solution="";
    if(empty($data->status)){
        $deleteParam = $data->id.",'Customer Complaints'";

        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editcustomerComplaints', 'title' : 'Update customerComplaints'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

        $solParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editcustomerComplaints', 'title' : 'Solution', 'fnEdit' : 'complaintSolution', 'fnsave' : 'saveSolution'}";
    
        $solution = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Solution" flow="down" onclick="edit('.$solParam.');"><i class="ti-check" ></i></a>';
    }
    
    
    $action = getActionButton($solution.$editButton.$deleteButton);
   
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->so_number,$data->full_name,$data->complaint,$data->product_returned,$data->action_taken,$data->ref_feedback,$data->remark];
}

/* Customer Complaints Table Data*/
function getCustomerReworkData($data){ 
    $editButton="";$deleteButton="";$solution="";
    if(empty($data->status)){
        $deleteParam = $data->id.",'Rework'";

        // $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editcustomerComplaints', 'title' : 'Update customerComplaints'}";
    
        // $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    }
    
    
    $action = getActionButton($deleteButton);
   
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->inv_no,$data->full_name,$data->qty,$data->remark];
}
?>