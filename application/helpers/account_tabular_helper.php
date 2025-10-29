<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getAccountDtHeader($page)
{   
	
	/* Payment Voucher  */
    $data['paymentVoucher'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['paymentVoucher'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['paymentVoucher'][] = ["name"=>"Voucher No."];
    $data['paymentVoucher'][] = ["name"=>"Voucher Date"];    
    $data['paymentVoucher'][] = ["name"=>"Voucher Type"];
    $data['paymentVoucher'][] = ["name"=>"Party Name"];
    $data['paymentVoucher'][] = ["name"=>"Amount","style"=>"width:5%;","textAlign"=>"center"];   
    $data['paymentVoucher'][] = ["name"=>"Doc. No."];	
    $data['paymentVoucher'][] = ["name"=>"Doc. Date"];
    $data['paymentVoucher'][] = ["name"=>"Remark"];

	/* Ledger Header */
    $data['ledger'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['ledger'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['ledger'][] = ["name"=>"Ledger Name"];
    $data['ledger'][] = ["name"=>"Group Name"];
    $data['ledger'][] = ["name"=>"Op. Balance"];
    $data['ledger'][] = ["name"=>"Cl. Balance"];

     /* Debit Note Header */
     $data['debitNote'][] = ["name"=>"Action","style"=>"width:5%;"];
     $data['debitNote'][] = ["name"=>"#","style"=>"width:5%;"];
     $data['debitNote'][] = ["name"=>"Inv No."];
     $data['debitNote'][] = ["name"=>"Inv Date"];
     $data['debitNote'][] = ["name"=>"Supplier Name"];
     $data['debitNote'][] = ["name"=>"Amount"];

     /* Credit Note Header */
    $data['creditNote'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['creditNote'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['creditNote'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['creditNote'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['creditNote'][] = ["name"=>"Invoice Type","textAlign"=>"center"];
    $data['creditNote'][] = ["name"=>"Customer Name"]; 
    $data['creditNote'][] = ["name"=>"Cust. PO.NO."];
    $data['creditNote'][] = ["name"=>"Bill Amount","textAlign"=>"right"];  
    
    /* GST Expense Header */
    $data['gstExpense'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['gstExpense'][] = ["name" => "#", "style" => "width:5%;"];
    $data['gstExpense'][] = ["name" => "Trans No."];
    $data['gstExpense'][] = ["name" => "Trans Date."];
    $data['gstExpense'][] = ["name" => "Ref. No."];
    $data['gstExpense'][] = ["name" => "Supplier Name"];
    $data['gstExpense'][] = ["name" => "Amount"];

    /* Journal Entry Header */
    $data['journalEntry'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['journalEntry'][] = ["name" => "#", "style" => "width:5%;"];
    $data['journalEntry'][] = ["name" => "JV No."];
    $data['journalEntry'][] = ["name" => "JV Date."];
    $data['journalEntry'][] = ["name" => "Ledger Name"];
    $data['journalEntry'][] = ["name" => "Amount"];
    $data['journalEntry'][] = ["name" => "Note"];

    /* Service Item Header */
    $data['serviceItem'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['serviceItem'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['serviceItem'][] = ["name"=>"Item Name"];
	$data['serviceItem'][] = ["name"=>"Item Code"];
    $data['serviceItem'][] = ["name"=>"HSN Code"];

    return tableHeader($data[$page]);
}

/* Ledger Data */
function getLedgerData($data){
    $action = "";
    if($data->party_category == 4):
        $deleteParam = $data->id.",'Ledger'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'edit', 'title' : 'Update Ledger'}";

        $editButton = '<a class="btn btn-success permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        $action = getActionButton($editButton.$deleteButton);
    endif;

    return [$action,$data->sr_no,$data->party_name,$data->name,($data->opening_balance >= 0)?floatVal(abs($data->opening_balance)).((floatval($data->opening_balance) != 0)?" CR":""):floatVal(abs($data->opening_balance))." DR",($data->cl_balance >= 0)?floatVal(abs($data->cl_balance)).((floatval($data->cl_balance) != 0)?" CR":""):floatVal(abs($data->cl_balance))." DR"];
}

/* Payment Voucher Data */
function getPaymentVoucher($data){
    $deleteParam = $data->id;
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editLedger', 'title' : 'Update Ledger'}";    
   
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
    $entry_type = ($data->entry_type == 15)?"Receive":"Paid";
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$entry_type,$data->opp_acc_name,$data->net_amount,$data->doc_no,formatDate($data->doc_date),$data->remark];
}

/* Debit Note Data */
function getDebitNoteData($data){
    $deleteParam = $data->trans_main_id.",'Invoice'";$itemList = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editInvoice', 'title' : 'Update Invoice'}";
    $printBtn = '';//'<a class="btn btn-dribbble btn-edit permission-approve" href="'.base_url($data->controller.'/purchaseInvoice_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($printBtn.$itemList.$editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->doc_no,formatDate($data->trans_date),$data->party_name,$data->net_amount];
}


/* Credit Note Table Data */
function getCreditNoteData($data){
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

/* GST Expense Data */
function getGstExpenseData($data)
{
    $deleteParam = $data->trans_main_id . ",'GST Expense'";
    $itemList = "";
    $editParam = "{'id' : " . $data->id . ", 'modal_id' : 'modal-md', 'form_id' : 'editInvoice', 'title' : 'Update Invoice'}";
    $printBtn = ''; //'<a class="btn btn-dribbble btn-edit permission-approve" href="'.base_url($data->controller.'/purchaseInvoice_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="' . base_url($data->controller . '/edit/' . $data->trans_main_id) . '"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="' . $data->trans_main_id . '" data-party_name="' . $data->party_name . '" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($printBtn . $itemList . $editButton . $deleteButton);

    return [$action, $data->sr_no, getPrefixNumber($data->trans_prefix, $data->trans_no), formatDate($data->trans_date), $data->doc_no, $data->party_name, $data->net_amount];
}


/* JournalEntry Data */
function getJournalEntryData($data)
{
    $deleteParam = $data->id . ",'GST Expense'";

    $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="' . base_url($data->controller . '/edit/' . $data->id) . '"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash(' . $deleteParam . ');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton . $deleteButton);

    return [$action, $data->sr_no, $data->trans_number, formatDate($data->trans_date), $data->acc_name, $data->amount ." ".$data->c_or_d, $data->remark];
}

function getServiceItemData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    
    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->hsn_code];
}
?>