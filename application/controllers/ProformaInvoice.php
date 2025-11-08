<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class ProformaInvoice extends MY_Controller{	
	private $indexPage = "proforma_invoice/index";
    private $invoiceForm = "proforma_invoice/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Proforma Invoice";
		$this->data['headData']->controller = "proformaInvoice";
		$this->data['headData']->pageUrl = "proformaInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->proformaInv->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getProformaInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	/* public function createInvoice(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$this->data['invMaster'] = $invMaster;
		$this->data['invItems'] = ($data['from_entry_type'] == 5)?$this->challan->getChallanItems($data['ref_id']):$this->salesOrder->getOrderItems($data['ref_id']);
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->load->view($this->invoiceForm,$this->data);
	} */

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(9);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(9);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->load->view($this->invoiceForm,$this->data);
    }	
	
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
	    $data['currency'] = '';$data['inrrate'] = 0;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;
		endif;
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
		if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				endif;
				$i++;
			endforeach;
		endif;
		
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			
			if($data['apply_round'] == 1):
				$data['net_amount_total'] = $data['net_amount_total'] - $data['round_off'];
				$data['round_off'] = 0; 
			endif;
			
			$masterData = [ 
				'id' => $data['proforma_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'party_id' => $data['party_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gst_type' => $data['gst_type'], 
				'gst_applicable' => $data['gst_applicable'],
				'sales_type' => $data['sales_type'], 
				// 'challan_no' => $data['challan_no'], 
				'doc_no'=>$data['so_no'],
				'ref_by' => $data['ref_by'],
				// 'total_packet' => $data['total_packet'],
				// 'transport_name' => $data['transport'],
				// 'shipping_address' => $data['supply_place'],
				'total_amount' => $data['amount_total'] + $data['disc_amt_total'],
				'taxable_amount' => $data['amount_total'],
				'gst_amount' => $data['igst_amt_total'],
				'freight_amount' => $data['freight_amt'],
				'igst_amount' => $data['igst_amt_total'], 
				'cgst_amount' => $data['cgst_amt_total'], 
				'sgst_amount' => $data['sgst_amt_total'], 
				'disc_amount' => $data['disc_amt_total'],
				'apply_round' => $data['apply_round'], 
				'round_off_amount' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'terms_conditions' => $data['terms_conditions'],
                'remark' => $data['remark'],
				'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'created_by' => $this->session->userdata('loginId')
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'amount' => $data['amount'] + $data['disc_amt'],
				'taxable_amount' => $data['amount'],				
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount']
			];

			$this->printJson($this->proformaInv->save($masterData,$itemData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->proformaInv->getInvoice($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales');
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->proformaInv->deleteInv($id));
		endif;
	}

	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}
	
    public function invoice_pdf()
	{
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->proformaInv->getInvoice($sales_id);
		$partyData = $this->party->getParty($salesData->party_id);
		$companyData = $this->proformaInv->getCompanyInfo();
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');
		
		$gstHCol='';$gstCol='';$blankTD='';$bottomCols=2;$GSTAMT=$salesData->igst_amount;
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate<br><small>('.$partyData->currency.')</small></th>
						<!--<th style="width:6%;">Disc.</th>
						<th style="width:8%;">GST</th>-->
						<th style="width:11%;">Amount<br><small>('.$partyData->currency.')</small></th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		
		$blankLines=10;if(!empty($header_footer)){$blankLines=10;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;		
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);
			$blankLines=17 - count($tc);if(!empty($header_footer)){$blankLines=17 - count($tc);}
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>
										<!--<img src="'.$auth_sign.'" style="width:120px;">-->
									</th>
							</tr>';
				else:
					$terms .= '<tr>
									<th style="font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="font-size:12px;">: '.$trms->condition.'</td>
							</tr>';
				endif;$t++;
			endforeach;
		}
		else
		{
			$tc = array();
			$terms .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		
		$terms .= '</table>';
		$blankLines=10;if(!empty($header_footer)){$blankLines=10;}
		$subTotal=0;$lastPageItems = '';$pageCount = 0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();$totalPage = 0;
		$totalItems = count($salesData->itemData);
		
		$lpr = $blankLines ;$pr1 = $blankLines + 6 ;
		$pageRow = $pr = ($totalItems > $lpr) ? $pr1 : $totalItems;
		$lastPageRow = (($totalItems % $lpr)==0) ? $lpr : ($totalItems % $lpr);
		$remainRow = $totalItems - $lastPageRow;
		$pageSection = round(($remainRow/$pageRow),2);
		$totalPage = (numberOfDecimals($pageSection)==0)? (int)$pageSection : (int)$pageSection + 1;
		for($x=0;$x<=$totalPage;$x++)
		{
			$page_qty = 0;$page_amount = 0;
			$pageItems = '';$pr = ($x==$totalPage) ? $totalItems - ($i-1) : $pr;
			$tempData = $this->proformaInv->proformaTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				foreach ($tempData as $row)
				{
					$itemData = $this->item->getItem($row->item_id); $drawing_no=''; $part_no=''; $rev_no='';
					if(!empty($itemData->drawing_no)){ $drawing_no = 'Draw. No:'.$itemData->drawing_no;}
					if(!empty($itemData->part_no)){ $part_no = 'Part No:'.$itemData->part_no;}
					if(!empty($itemData->rev_no)){ $rev_no = 'Rev. No:'.$itemData->rev_no;}
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left"><b>'.$row->item_name.'</b><br> '.$drawing_no.' '.$part_no.' '.$rev_no.'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.3f', $row->price).'</td>';
						/* $pageItems.='<td class="text-center">'.floatval($row->disc_per).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->igst_per).'%</td>'; */
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount;$subTotal += $row->amount;$i++;
				}
			}
			if($x==$totalPage)
			{
				$pageData[$x]= '';
				$lastPageItems = $pageItems;
			}
			else
			{
				/* $pageItems.='<tr>';
					$pageItems.='<th class="text-right" style="border:1px solid #000;" colspan="3">Page Total</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.3f', $page_qty).'</th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;"></th>';
					$pageItems.='<th class="text-right" style="border-top:1px solid #000000;">'.sprintf('%0.2f', $page_amount).'</th>';
				$pageItems.='</tr>'; */
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			// echo $pageCount.'<br>';
			$pageCount += $pageRow;
		}
		// exit;
		$taxableAmt= $subTotal + $salesData->freight_amount;
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->sgst_amount + $fgst)).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;
			}
		}
		
		$taxable_amount='<tr>
			<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>
			<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>
			</tr>';
		
		if($salesData->gst_applicable == 0)
		{
			$gstRow='<tr>';
					$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;"></td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;"></td>';
				$gstRow.='</tr>';$rwspan= 3;
				$taxable_amount='<tr>
					<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">&nbsp;</th>
					<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;"></th>
					</tr>';
		}
		
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="1" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $subTotal).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;white-space:pre-wrap;"><b>Note : </b>'.str_replace("\n", '<br />', $salesData->remark).'
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">P & F</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->freight_amount).'</td>';
		$itemList.='</tr>';
		
		// $itemList.='<tr>';
			// $itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.$taxLabel.' Amount</th>';
			// $itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>';
		// $itemList.='</tr>';
		
		$itemList.=$taxable_amount.$gstRow;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words ('.$partyData->currency.') : </b>'.numToWordEnglish($salesData->net_amount).'</i></td>';
			$itemList.='<td colspan="2" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:1px;" class="text-left fs-16" >GSTIN: '.$companyData->company_gst_no.'</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">PROFORMA INVOICE</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right">'.$it.'</th>
									</tr>
								</table>';
		}
		$party_gstin = (!empty($party_gstin)) ? '<b>GSTIN : '.$party_gstin.'</b>' : '';
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:65%;">
								<table>
									<tr><td style="vartical-align:top;"><b>BILL TO : '.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.$partyData->party_address.'</td></tr><br>
									<tr><td class="text-left" style="">Contact To.: '.$partyData->contact_person.'</td></tr>
									<tr><td class="text-left" style="">Contact No.: '.$partyData->party_mobile.'</td></tr>
									<tr><td class="text-left" style="">Email: '.$partyData->party_email.'</td></tr>
									<tr><td class="text-left" style="">Reference: '.$salesData->ref_by.'</td></tr>
								</table>
							</td>
							<td style="width:35%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>Prof. Inv. No.</b></td><td>: '.$salesData->trans_prefix.$salesData->trans_no.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Date</b></td><td>: '.date('d/m/Y', strtotime($salesData->trans_date)).'</td></tr>
									<tr><td style="vertical-align:top;"><b>S.O. No.</b></td><td>: '.$salesData->doc_no.'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer))
		{
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
		}
		
		if(!empty($original))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',7,7,50,7,4,6);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[0].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($duplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($triplicate))
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		for($x=0;$x<$extra_copy;$x++)
		{
			foreach($pageData as $pg)
			{
				if(!empty($header_footer))
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,50,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		// $mpdf->Output(FCPATH.$fpath,'F');
		
		$mpdf->Output($pdfFileName,'I');
	}
}
?>