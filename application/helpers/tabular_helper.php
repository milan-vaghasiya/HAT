<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Common Table Header */
function tableHeader($data){
	$c=0;$colsAlignment=array();$srno_position=1;
    $html = '<thead class="thead-info"><tr>';
    foreach($data as $row):
        $name = $row['name'];
        $style = (isset($row['style']))?$row['style']:"";
        $orderable = (isset($row['orderable']))?$row['orderable']:"true";
		$html .= '<th style="'.$style.'" data-orderable="'.$orderable.'">'.$name.'</th>';
		
        if(isset($row['srnoPosition'])):
			$srno_position = $row['srnoPosition'];
        endif;
		
		if(isset($row['textAlign']) and $row['textAlign']=="left"):
			$colsAlignment['left'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="right"):
			$colsAlignment['right'][]= $c;
		elseif(isset($row['textAlign']) and $row['textAlign']=="center"):
			$colsAlignment['center'][]= $c;
		endif;
        $c++;
    endforeach;
    $html .= '</tr></thead>';
    return [$html,json_encode($colsAlignment),$srno_position];
}

/* get Pagewise Table Header */
function getDtHeader($page)
{
	
	/* Purchase Invoice Header */
    $data['purchaseInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"Inv No."];
    $data['purchaseInvoice'][] = ["name"=>"Inv Date"];
    $data['purchaseInvoice'][] = ["name"=>"Supplier Name"];
    $data['purchaseInvoice'][] = ["name"=>"Amount"];
	

    /* Product Inspection Header */
    $data['productInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['productInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['productInspection'][] = ["name" => "Inspection Type"];
    $data['productInspection'][] = ["name" => "Inspection Date"];
    $data['productInspection'][] = ["name" => "Product Name"];
    $data['productInspection'][] = ["name" => "Qty."];
	
	/* ISO Reports Header */
    $data['iso'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['iso'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['iso'][] = ["name"=>"Documents"];
    $data['iso'][] = ["name"=>"Document No."];
    $data['iso'][] = ["name"=>"Rev. No. & Date"];
    $data['iso'][] = ["name"=>"Category"];
    
	return tableHeader($data[$page]);
}

/* Create Action Button */
function getActionButton($buttons){
	$action = '<div class="actionWrapper" style="position:relative;">
					<div class="actionButtons actionButtonsRight">
						<a class="mainButton btn-instagram " href="javascript:void(0)"><i class="fa fa-cog"></i></a>
						<div class="btnDiv">'.$buttons.'</div>
					</div>
				</div>';
	return $action;
}

/* Product Inspection Table Data */
function getProductInspectionData($data){
    $deleteParam = $data->id.",'Product Inspection'";

    if($data->type == 1):
        $type = "OK";
    elseif($data->type == 2):
        $type = "Reject";
    else:
        $type = "Scrape";
    endif;

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($delete);

    return [$action,$data->sr_no,$type,date("d-m-Y",strtotime($data->inspection_date)),$data->item_name,$data->qty."(".$data->unit_name.")"];
}

/* Print Decimal Without 0 Precision */
function printDecimal($val){return number_format($val,0,'','');}

/* Ignore Single/Double Quote **/
function trimQuotes($val){return str_replace('"','\"',$val);}

/** Date Format **/
function formatDate($date,$format='d-m-Y'){return (!empty($date)) ? date($format,strtotime($date)) : '';}

/** GET PREFIX ARRAY **/
function getPrefix($prefix,$explodeBy = '/'){return explode($explodeBy,$prefix);}

/** GET NO WITH FORMATED PREFIX **/
//function getPrefixNumber($prefix,$no,$explodeBy = '/'){ $prfx = explode($explodeBy,$prefix);return $prfx[0].'/'.$no.'/'.$prfx[1]; }
function getPrefixNumber($prefix,$no,$explodeBy = '/'){ return $prefix.$no; }

function minutes($time){
    $time = explode(':', $time);
    $h = (isset($time[0]))?($time[0]*60):0;
    $m = (isset($time[1]))?$time[1]:0;
    $s = (isset($time[2]))?($time[2]/60):0;
    return $h + $m + $s;
}

/* Convert Seconds to Time */
function secondsToTime($seconds) {
    /* $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds - ($h * 3600) - ($m * 60);
    return sprintf('%02d:%02d:%02d', $h, $m, $s); */
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    return sprintf('%02d:%02d', $h, $m);
}

function getVoucherNameLong($entryType){
    switch($entryType){
        case 1:
            return "Sales Enquiry";
        case 2:
            return "Sales Quotation";
        case 3:
            return "Quotation Revision";
        case 4:
            return "Sales Order";
        case 5:
            return "Delivery Challan";
        case 6: //Manufacturing (Domestics)
            return "Sales Invoice";
        case 7: //Jobwork (Domestics)
            return "Sales Invoice";
        case 8: //Manufacturing (Export)
            return "Sales Invoice";
        case 9:
            return "Proforma Invoice";
        case 10: //Commercial Invoice
            return "Sales Invoice";
        case 11: //Custom Invoice
            return "Sales Invoice";
        case 12:
            return "Purchase Invoice";
        case 13:
            return "Credit Note";
        case 14:
            return "Debit Note";
        case 15:
            return "Cash/Bank Received";
        case 16:
            return "Cash/Bank Paid";
        case 17:
            return "Journal Voucher";
        case 18:
            return "GST Expense";
        case 21:
            return "Advance Salary";
        case 22:
            return "Employee Loan";
        default:
			return "";
    }
}

function getVoucherNameShort($entryType){
    switch($entryType){
        case 1:
            return "SEnq";
        case 2:
            return "SQuo";
        case 3:
            return "QRev";
        case 4:
            return "SOrd";
        case 5:
            return "Chln";
        case 6: //Manufacturing (Domestics)
            return "Sale";
        case 7: //Jobwork (Domestics)
            return "Sale";
        case 8: //Manufacturing (Export)
            return "Sale";
        case 9:
            return "PrIn";
        case 10: //Commercial Invoice
            return "Sale";
        case 11: //Custom Invoice
            return "Sale";
        case 12:
            return "Purc";
        case 13:
            return "C.N.";
        case 14:
            return "D.N.";
        case 15:
            return "BCRct";
        case 16:
            return "BCPmt";
        case 17:
            return "Jrnl";
        case 18:
            return "GExp";
        case 21:
            return "AdSal";
        case 22:
            return "EmpLoan";
        default:
			return "";
    }
}

function getSystemCode($type,$isChild,$gstType=0){
	$retVal = "";
	if($isChild == false){
		switch($type){	
			case 12: // Purchase Invoice
				$retVal = "PURACC";
				break;
			case 6: // Sales Invoice
				$retVal = "SALESACC";
				break;
            case 7: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 8: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 10: // Sales Invoice
                $retVal = "SALESACC";
                break;
            case 11: // Sales Invoice
                $retVal = "SALESACC";
                break;
			case 13: // Credit Note
				$retVal = "SALESACC";
				break;	
			case 14: // Debit Note
				$retVal = "PURACC";
				break;
		}
	}else{
		switch($type){	
			case 12: // Purchase Invoice
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
			case 6: // Sales Invoice
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;
            case 7: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 8: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 10: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
            case 11: // Sales Invoice
                $retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
                break;
			case 13: // Credit Note
				$retVal = ($gstType == 3)?"SALESTFACC":"SALESGSTACC";
				break;	
			case 14: // Debit Note
				$retVal = ($gstType == 3)?"PURTFACC":"PURGSTACC";
				break;
		}
	}
	return $retVal;
}

function getCrDrEff($type){
	$result = array();
	switch($type){
		case 12: //Purchase Invoice
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;	

		case 6: //Sales Invoice
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;  
        case 7: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 8: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 10: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
        case 11: //Sales Invoice
            $result['vou_type'] = "DR";
            $result['opp_type'] = "CR";	
            break;
		case 13: //Credit Note
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";		
			break;	
		case 14: //Debit Note
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;
		case 15: //Bank/Cash Receipt
			$result['vou_type'] = "DR";
			$result['opp_type'] = "CR";	
			break;
		case 16: //Bank/Cash Payment
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;
		case 18: //GST Expense
			$result['vou_type'] = "CR";
			$result['opp_type'] = "DR";	
			break;
        case 21: //Advance Salary Payment
            $result['vou_type'] = "CR";
            $result['opp_type'] = "DR";	
            break;
        case 22: //Employee Loan Payment
            $result['vou_type'] = "CR";
            $result['opp_type'] = "DR";	
            break;
	}
	return $result;
}

function getExpArrayMap($input){
	$expAmount=0;
	for($i=1; $i<=25 ; $i++):
		$result['exp'.$i.'_acc_id'] = (isset($input['exp'.$i.'_acc_id']))?$input['exp'.$i.'_acc_id']:0;
		$result['exp'.$i.'_per'] = (isset($input['exp'.$i.'_per']))?$input['exp'.$i.'_per']:0;
		$result['exp'.$i.'_amount'] = (isset($input['exp'.$i.'_amount']))?$input['exp'.$i.'_amount']:0;
		$expAmount += $result['exp'.$i.'_amount'];
	endfor;
	$result['exp_amount'] = $expAmount;
	return $result;
}
?>