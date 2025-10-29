<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Common Table Header */
function reportHeader($data){
	$c=0;$colsAlignment=array();$srno_position=1;
    $html = '<thead class="thead-info"><tr>';
    foreach($data as $row):
        $name = $row['name'];
        $style = (isset($row['style']))?$row['style']:"";
		$html .= '<th style="'.$style.'">'.$name.'</th>';
		
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
function getReportHeader($page)
{
	/* Inward Register Header */
	$data['inwardRegister'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
	$data['inwardRegister'][] = ["name"=>"Date","textAlign"=>"center"];
    $data['inwardRegister'][] = ["name"=>"Ch./Bill No.","textAlign"=>"center"];
    $data['inwardRegister'][] = ["name"=>"Party Name"];
	$data['inwardRegister'][] = ["name"=>"Item Description"];
    $data['inwardRegister'][] = ["name"=>"Qty."];
	$data['inwardRegister'][] = ["name"=>"Unit"];
    $data['inwardRegister'][] = ["name"=>"Remark"];
    $data['inwardRegister'][] = ["name"=>"Sign."];

	/* list of product non automative */
	$data['nonAutoProduct'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['nonAutoProduct'][] = ["name"=>"Part Name"];
	$data['nonAutoProduct'][] = ["name"=>"Customer Drg. No."];
    $data['nonAutoProduct'][] = ["name"=>"Our Part No."];
    $data['nonAutoProduct'][] = ["name"=>"Customer Name"];
    $data['nonAutoProduct'][] = ["name"=>"Remark"];

	/* list of product automative */
	$data['autoProduct'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['autoProduct'][] = ["name"=>"Part Name"];
	$data['autoProduct'][] = ["name"=>"Customer Drg. No."];
    $data['autoProduct'][] = ["name"=>"Our Part No."];
    $data['autoProduct'][] = ["name"=>"Customer Name"];
    $data['autoProduct'][] = ["name"=>"Remark"];

	/* LIST OF CUSTOMERS AUTOMOTIVE */
	$data['customerAutomotive'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['customerAutomotive'][] = ["name"=>"Name of Customer & Address"];
    $data['customerAutomotive'][] = ["name"=>"Contact Person"];
    $data['customerAutomotive'][] = ["name"=>"Contact Phone"];
    $data['customerAutomotive'][] = ["name"=>"Contact Fax"];
    $data['customerAutomotive'][] = ["name"=>"Contact Mobile"];
    $data['customerAutomotive'][] = ["name"=>"Contact E-mail"];
    $data['customerAutomotive'][] = ["name"=>"Customer Specific Req."];

	/* LIST OF CUSTOMERS GENERALS */
	$data['customerGenerals'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['customerGenerals'][] = ["name"=>"Name of Customer & Address"];
    $data['customerGenerals'][] = ["name"=>"Contact Person"];
    $data['customerGenerals'][] = ["name"=>"Contact Phone"];
    $data['customerGenerals'][] = ["name"=>"Contact Fax"];
    $data['customerGenerals'][] = ["name"=>"Contact Mobile"];
    $data['customerGenerals'][] = ["name"=>"Contact E-mail"];
    $data['customerGenerals'][] = ["name"=>"Remark"];

	/* LIST OF PURCHASE MONITORING REGISTER */
	$data['purchaseRegister'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['purchaseRegister'][] = ["name"=>"Date","style"=>"width:12%"];
    $data['purchaseRegister'][] = ["name"=>"Item Description"];
    $data['purchaseRegister'][] = ["name"=>"Supplier Name"];
    $data['purchaseRegister'][] = ["name"=>"Order No."];
    $data['purchaseRegister'][] = ["name"=>"Order Qty."];
    $data['purchaseRegister'][] = ["name"=>"Delivery Date","style"=>"width:10%"];
    $data['purchaseRegister'][] = ["name"=>"Inv. No."];
    $data['purchaseRegister'][] = ["name"=>"inv. Date","style"=>"width:10%"];
    $data['purchaseRegister'][] = ["name"=>"Qty."];
    $data['purchaseRegister'][] = ["name"=>"Remarks"];
    $data['purchaseRegister'][] = ["name"=>"Sign."];

    /* LIST OF STOCK STATEMENT */
    $data['stockStatement'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['stockStatement'][] = ["name"=>"Part No."];
    $data['stockStatement'][] = ["name"=>"Part Name"];
    $data['stockStatement'][] = ["name"=>"Customer Name"];
    $data['stockStatement'][] = ["name"=>"Drg. No."];
    $data['stockStatement'][] = ["name"=>"Rev. No."];
    $data['stockStatement'][] = ["name"=>"Closing Stock Qty."];
    $data['stockStatement'][] = ["name"=>"Remarks"];
    $data['stockStatement'][] = ["name"=>"Sign."];

    

    /* LIST OF Supplier Purchase */
    $data['supplierPurchase'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];

    $data['supplierPurchase'][] = ["name"=>"External Provider Name & Address"];
    $data['supplierPurchase'][] = ["name"=>"Contact"];
    $data['supplierPurchase'][] = ["name"=>"Item Supplied"];
    $data['supplierPurchase'][] = ["name"=>"Basis of Approval"];
    $data['supplierPurchase'][] = ["name"=>"Approved Date"];
    $data['supplierPurchase'][] = ["name"=>"Approved By"];
    $data['supplierPurchase'][] = ["name"=>"Remarks"];

    /* LIST OF Supplier Service */
    $data['supplierService'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['supplierService'][] = ["name"=>"Supplier Code"];
    $data['supplierService'][] = ["name"=>"Supplier Name & Addressate"];
    $data['supplierService'][] = ["name"=>"Contact Person"];
    $data['supplierService'][] = ["name"=>"Phone"];
    $data['supplierService'][] = ["name"=>"Fax"];
    $data['supplierService'][] = ["name"=>"E-mail"];
    $data['supplierService'][] = ["name"=>"Mobile"];
    $data['supplierService'][] = ["name"=>"Item/Service Supplied"];
    $data['supplierService'][] = ["name"=>"Approval Date"];
    $data['supplierService'][] = ["name"=>"Approved By"];
    $data['supplierService'][] = ["name"=>"Base of Approval"];
    $data['supplierService'][] = ["name"=>"Remarks"];

	return reportHeader($data[$page]);
}

/* Process Table Data */
function getReportData($page,$data){
	
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

?>