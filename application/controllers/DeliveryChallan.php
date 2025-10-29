<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class DeliveryChallan extends MY_Controller{	
	private $indexPage = "delivery_challan/index";
    private $challanForm = "delivery_challan/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Delivery Challan";
		$this->data['headData']->controller = "deliveryChallan";
		$this->data['headData']->pageUrl = "deliveryChallan";
		$this->data['boxTypes']=['Crate','Box','Bag','Pallet','Parcel'];
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->challan->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $invData = $this->challan->getChallanWiseInv($row->trans_main_id);
			$row->inv_no = '';
			if(!empty($invData)): $c=1;
				foreach($invData as $inv):
					if($c==1){ $row->inv_no .= $inv->trans_prefix.$inv->trans_no; }else{ $row->inv_no .= ', '.$inv->trans_prefix.$inv->trans_no; } $c++;
				endforeach;
			endif;
            
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getDeliveryChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createChallan(){
        $data = $this->input->post();
        $orderMaster = new stdClass();
        $orderMaster = $this->party->getParty($data['party_id']);
        $orderMaster->party_id = $data['party_id'];
        $this->data['from_entry_type'] = 4;
		$this->data['ref_id'] = implode(',',$data['ref_id']);       
		$soData = $this->salesOrder->getOrderByRefid($data['ref_id']);
		$soTransNo= ''; $i=1;
		foreach($soData as $row):
			if($i==1){$soTransNo .= getPrefixNumber($row->trans_prefix,$row->trans_no);}
			else{$soTransNo .= ', '.getPrefixNumber($row->trans_prefix,$row->trans_no);}
			$i++;
		endforeach;
		$this->data['soTransNo'] = $soTransNo;
        $this->data['orderItems'] = $this->salesOrder->getOrderItems($data['ref_id']);
        $this->data['orderMaster'] = $orderMaster;      
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(5);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(5);
        $this->data['customerData'] = $this->party->getCustomerList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemLists("1,3,10");
        $this->data['rawItemData'] = $this->item->getItemList(3);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->challanForm,$this->data);
    } 
	//Created By Karmi @15/04/2022
	public function createChallanfromGrn(){
        $data = $this->input->post();
        $orderMaster = new stdClass();
        $orderMaster = $this->party->getParty($data['party_id']);
        $orderMaster->party_id = $data['party_id'];
        $this->data['from_entry_type'] = 4;
		$this->data['ref_id'] = implode(',',$data['ref_id']);       
		$dcData = $this->grnModel->getGRNByRefid($data['ref_id']);
		//print_r($dcData);exit;
		$soTransNo= ''; $i=1;
		foreach($dcData as $row):
			if($i==1){$soTransNo .= getPrefixNumber($row->grn_prefix,$row->grn_no);}
			else{$soTransNo .= ', '.getPrefixNumber($row->grn_prefix,$row->grn_no);}
			$i++;
		endforeach;
		$this->data['soTransNo'] = $soTransNo;
        $this->data['grnItems'] = $this->grnModel->getOrderItems($data['ref_id']);
		$dcHeatNo= ''; $i=1;
		foreach($this->data['grnItems'] as $row):
			if($i==1){$dcHeatNo .= $row->batch_no;}
			else{$dcHeatNo .= ', '.$row->batch_no;}
			$i++;
		endforeach;
		$this->data['dcHeatNo'] = $dcHeatNo;
        $this->data['orderMaster'] = $orderMaster;      
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(5);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(5);
        $this->data['customerData'] = $this->party->getCustomerList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['itemData'] = $this->item->getItemLists("1,3,10");
        $this->data['rawItemData'] = $this->item->getItemList(3);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['transportData'] = $this->transport->getTransportList();
        
        $this->load->view($this->challanForm,$this->data);
    } 

    public function addChallan(){
        $this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(5);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(5);
        $this->data['customerData'] = $this->party->getCustomerList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['itemData'] = $this->item->getItemLists("1,3,10");
        $this->data['rawItemData'] = $this->item->getItemList(3);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->challanForm,$this->data);
    }

    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}

    public function save00(){
        $data = $this->input->post();
        $errorMessage = array();
		//print_r($data);exit;
        if(empty($data['dc_no']))
            $errorMessage['dc_no'] = "DC. No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Product is required.";       
        
        if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				//$packing_qty = $this->challan->getPackedItemQty($value)->qty;
				$qty_error=Array();
				foreach(explode(',',$data['location_id'][$key]) as $lkey=>$lid)
				{
				    $stockQ = Array();
    				$stockQ['item_id'] = $value;$stockQ['location_id'] = $lid;$stockQ['batch_no'] = explode(',',$data['batch_no'][$key])[$lkey];
    				$stockData = $this->store->getItemStockGeneral($stockQ);
    				$packing_qty = (!empty($stockData)) ? $stockData->qty : 0;
    				
    				$old_qty = 0;
    				if(!empty($data['trans_id'][$key])):
    					$oldCHData = $this->challan->challanTransRow($data['trans_id'][$key]);
    					$oldBatches = explode(',',$oldCHData->batch_no);$oldLocations = explode(',',$oldCHData->location_id);
    					if(in_array($stockQ['batch_no'],$oldBatches))
    					{
    					    $batchQtyKey = array_search($oldBatches,$stockQ['batch_no']);
    					    $old_qty = explode(',',$oldCHData->batch_qty)[$batchQtyKey];
    					}
    				endif;
    				
    				if(($packing_qty + $old_qty) < explode(',',$data['batch_qty'][$key])[$lkey]):
    				    $qty_error[]= $stockQ['batch_no'];
    				endif;
				}
				if(!empty($qty_error)){$errorMessage["qty".$i] = "Stock not available. Batch No. = ".implode(', ',$qty_error);}
    
				if(empty($data['batch_no'][$key])):
					$errorMessage['batch'.$i] = "Batch Details is required.";
				endif;
				$i++;
			endforeach;
		endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$masterData = [ 
				'id' => $data['dc_id'],
                'entry_type' => $data['entry_type'],
                'from_entry_type' => $data['reference_entry_type'],
                'ref_id' => $data['reference_id'],
                'order_type' => $data['order_type'],
                'order_file' => $data['order_file'],
                'total_packet' => $data['total_packet'],
				'doc_no'=>$data['so_no'],
                'net_weight' => $data['net_weight'],
				'trans_prefix' => $data['dc_prefix'],
				'trans_no' => $data['dc_no'],
				'trans_date' => date('Y-m-d',strtotime($data['dc_date'])),
				'party_id' => $data['party_id'], 
				'party_name' => $data['party_name'], 
				'transport_name' => $data['dispatched_through'], 
				'lr_no' => $data['lr_no'], 
				'vehicle_no' => $data['vehicle_no'],
				'remark' => $data['remark'],
				'vou_name_s' => $data['vou_name_s'],
				'vou_name_l' => $data['vou_name_l'],
                'created_by' => $this->session->userdata('loginId')
			];		
							
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'stock_eff' => $data['stock_eff'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'hsn_code' => $data['hsn_code'],
				'gst_per' => $data['gst_per'],
				'price' => $data['price'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'qty' => $data['qty'],
				'rej_qty' => $data['rej_qty'],
				
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'batch_qty' => $data['batch_qty'],
				'packing_trans_id' => $data['packing_trans_id'],
				'item_remark' => $data['item_remark'],
				'grn_data' => $data['grn_data']
			];
            //$this->printJson($this->challan->save($masterData,$itemData));
        endif;
    }

	public function save(){
        $data = $this->input->post();
        $errorMessage = array();
		//print_r($data);exit;
        if(empty($data['dc_no']))
            $errorMessage['dc_no'] = "DC. No. is required.";
        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Product is required.";       
        
        if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				//$packing_qty = $this->challan->getPackedItemQty($value)->qty;
				$qty_error=Array();
				foreach(explode(',',$data['location_id'][$key]) as $lkey=>$lid)
				{
				    $stockQ = Array();
    				$stockQ['item_id'] = $value;$stockQ['location_id'] = $lid;$stockQ['batch_no'] = explode(',',$data['batch_no'][$key])[$lkey];
    				$stockData = $this->store->getItemStockGeneral($stockQ);
    				$packing_qty = (!empty($stockData)) ? $stockData->qty : 0;
    				$old_qty = 0;
    				if(!empty($data['trans_id'][$key])):
    					$oldCHData = $this->challan->challanTransRow($data['trans_id'][$key]);
    					$oldBatches = explode(',',$oldCHData->batch_no);$oldLocations = explode(',',$oldCHData->location_id);
    					if(in_array($stockQ['batch_no'],$oldBatches))
    					{
    					    $batchQtyKey = array_search($stockQ['batch_no'],$oldBatches);
    					    $old_qty = explode(',',$oldCHData->batch_qty)[$batchQtyKey];
    					}
    				endif;
    				if(($packing_qty + $old_qty) < explode(',',$data['batch_qty'][$key])[$lkey]):
    				    $qty_error[]= $stockQ['batch_no'];
    				endif;
				}
				if(!empty($qty_error)){$errorMessage["qty".$i] = "Stock not available. Batch No. = ".implode(', ',$qty_error);}
    
				if(empty($data['batch_no'][$key])):
					$errorMessage['batch'.$i] = "Batch Details is required.";
				endif;
				$i++;
			endforeach;
		endif;
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$masterData = [ 
				'id' => $data['dc_id'],
                'entry_type' => $data['entry_type'],
                'from_entry_type' => $data['reference_entry_type'],
                'ref_id' => $data['reference_id'],
                'order_type' => $data['order_type'],
                'order_file' => $data['order_file'],
                'total_packet' => $data['total_packet'],
				'doc_no'=>$data['so_no'],
                'net_weight' => $data['net_weight'],
				'trans_prefix' => $data['dc_prefix'],
				'trans_no' => $data['dc_no'],
				'trans_date' => date('Y-m-d',strtotime($data['dc_date'])),
				'party_id' => $data['party_id'], 
				'party_name' => $data['party_name'], 
				'transport_name' => $data['dispatched_through'], 
				'lr_no' => $data['lr_no'], 
				'vehicle_no' => $data['vehicle_no'],
				'remark' => $data['remark'],
				'vou_name_s' => $data['vou_name_s'],
				'vou_name_l' => $data['vou_name_l'],
                'created_by' => $this->session->userdata('loginId')
			];		
							
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'stock_eff' => $data['stock_eff'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'hsn_code' => $data['hsn_code'],
				'gst_per' => $data['gst_per'],
				'price' => $data['price'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'qty' => $data['qty'],
				'rej_qty' => $data['rej_qty'],				
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'tc_no' => $data['tc_no'],
				'ref_batch' => $data['ref_batch'],
				'batch_qty' => $data['batch_qty'],
				'packing_trans_id' => $data['packing_trans_id'],
				'item_remark' => $data['item_remark'],
				'grn_data' => $data['grn_data']
			];
            $this->printJson($this->challan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
        $this->data['challanData'] = $this->challan->getChallan($id);
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['customerData'] = $this->party->getCustomerList();
		$this->data['itemData'] = $this->item->getItemLists("1,3,10");
        $this->data['rawItemData'] = $this->item->getItemList(3);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->load->view($this->challanForm,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->challan->deleteChallan($id));
		endif;
	}

    public function getBatchNo(){
        $item_id = $this->input->post('item_id');
        $location_id = $this->input->post('location_id');
        $batchData = $this->item->locationWiseBatchStock($item_id,$location_id);
        $options = '<option value="">Select Batch No.</option>';
        foreach($batchData as $row):
			if($row->qty > 0):
				$options .= '<option value="'.$row->batch_no.'" data-stock="'.$row->qty.'">'.$row->batch_no.'</option>';
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function getPartyChallans(){
        $this->printJson($this->challan->getPartyChallans($this->input->post('party_id')));
    }

    public function getPendingOrders(){
        $party_id = $this->input->post('party_id');
		$pendingOrders = '<option value="">General Items</option>';
		$soData = $this->salesOrder->getPendingOrders($party_id);
		foreach($soData as $row):
			$pendingOrders .= '<option value="'.$row->id.'" >'.getPrefixNumber($row->so_prefix,$row->so_no).'</option>';
		endforeach;
		$this->printJson(['status'=>1,'pendingOrders'=>$pendingOrders]);
    }

    public function getPendingOrderItems(){
        $order_id = $this->input->post('order_id');
		$orderItems = '<option value="">Select Product Name</option>';
		if(!empty($order_id)):
			$poItems = $this->salesOrder->getPendingOrderItems($order_id);
			if(!empty($poItems)):
				foreach($poItems as $row):
					$pendingQty = $row->qty - $row->dispatch_qty;
					$orderItems .= '<option value="'.$row->item_id.'" data-iname="['.$row->item_code.'] '.$row->item_name.'" data-so_trans_id="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.' (Pending : '.$pendingQty.')</option>';
				endforeach;
			endif;
		else:
			$itemData = $this->item->getItemList(1);
			if(!empty($itemData)):
				foreach($itemData as $row):		
					$orderItems .= '<option value="'.$row->id.'" data-so_trans_id="">['.$row->item_code.'] '.$row->item_name.'</option>';
				endforeach; 
			endif;
			
		endif;
		$this->printJson(['status'=>1,'orderItems'=>$orderItems]);
    }

	/* public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->challan->batchWiseItemStock($data);
        $this->printJson($result);
	} */

	public function getCustomerGrnNo(){
		$party_id = $this->input->post('party_id');
		$grnData = $this->grnModel->getCustomerGrn($party_id);
		
		$html = '<option value="">Select GRN No.</option>';
		foreach($grnData as $row):
			$html .= '<option value="'.$row->id.'">'.$row->challan_no.'</option>';
		endforeach;
		$this->printJson(['status'=>1,'options'=>$html]);
	}

	public function getGrnItems(){
		$grn_id = $this->input->post('grn_id');
		$grnItems = $this->grnModel->getGrnItems($grn_id);
		
		$html = '<option value="" data-remaining_qty="" >Select Item Name</option>';
		foreach($grnItems as $row):
			$html .= '<option value="'.$row->item_id.'" data-grn_trans_id="'.$row->id.'" data-remaining_qty="'.$row->remaining_qty.'">'.$row->item_name.'(Qty.: '.$row->remaining_qty.')</option>';
		endforeach;
		$this->printJson(['status'=>1,'options'=>$html]);
	}

	public function getItemList(){
        $this->printJson($this->challan->getItemList($this->input->post('id')));
    }
    
    /*
	 * Created By : Avruti @31-12-2021
	 * 
	*/
	 /* 	Created By : Avruti @31-12-2021
	 	Updated By : Meghavi @25-07-2022 */
	public function challan_pdf(){
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->challan->getChallan($sales_id);
		$companyData = $this->challan->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
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
						<th style="width:10%;">Material</th>
						<th style="width:10%;">Batch No.</th>
						<th style="width:10%;">PO NO</th>
						<th style="width:15%;">HSN/SAC</th>
						<th style="width:10%;">W.O. No.</th>
						<th style="width:10%;">Qty</th>
						
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
			$tempData = $this->challan->getChallanTransactions($sales_id,$pr.','.$pageCount);
			if(!empty($tempData))
			{
				foreach ($tempData as $row)   
				{
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left">['.$row->item_code.'] '.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->material_grade.'</td>';
						$pageItems.='<td class="text-center">'.$row->batch_no.'</td>';
						$pageItems.='<td class="text-center">'.$row->doc_no.'<br>'.formatDate($row->doc_date).'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.$row->wo_no.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
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
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}
		$taxableAmt= $subTotal + $salesData->freight_amount;
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;
		
		// $gstRow='<tr>';
		// 	$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST</td>';
		// 	$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $fgst)).'</td>';
		// $gstRow.='</tr>';
		
		// $gstRow.='<tr>';
		// 	$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST</td>';
		// 	$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->sgst_amount + $fgst)).'</td>';
		// $gstRow.='</tr>';
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;
			}
		}
		$totalCols = 9;
		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="7" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			// $itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			// $itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $subTotal).'</th>';
		$itemList.='</tr>';
		
			// $itemList.='<tr>';
			// 	$itemList.='<td colspan="4" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			// 	<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			// 	<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			// 	</td>';
			// 	$itemList.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">P & F</td>';
			// 	$itemList.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->freight_amount).'</td>';
			// $itemList.='</tr>';
			
			// $itemList.='<tr>';
			// 	$itemList.='<th colspan="3" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			// 	$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $taxableAmt).'</th>';
			// $itemList.='</tr>';
		
		// $itemList.=$gstRow;
		
		// $itemList.='<tr>';
		// 	$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;"><i><b>Bill Amount In Words ('.$partyData->currency.') : </b>'.numToWordEnglish($salesData->net_amount).'</i></td>';
		// 	$itemList.='<td colspan="3" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
		// 	$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		// $itemList.='</tr>';
		
		// $itemList.='<tr>';
		// 	$itemList.='<th colspan="3" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
		// 	$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		// $itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		foreach($invType as $it)
		{
			$invoiceType[$i++]='<br><br><table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" >GSTIN: '.$companyData->company_gst_no.'</th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">DELIVERY CHALLAN</th>
										<th style="width:35%;letter-spacing:2px;" class="text-right">'.$it.'</th>
									</tr>
								</table>';
		}
		
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:55%;" rowspan="3">
								<table>
									<tr><td style="vartical-align:top;"><b>CHALLAN TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>'.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.$salesData->billing_address.'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$party_gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Challan No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : '.date('d/m/Y', strtotime($salesData->trans_date)).'</b>
							</td>
						</tr>
						<tr>
							<td style="width:45%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>P.O. No.</b></td><td>: '.$salesData->doc_no.'</td></tr>
									
									<tr><td style="vertical-align:top;"><b>Transport</b></td><td>: '.$salesData->transport_name.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Lr. No.</b></td><td>: '.$salesData->lr_no.'</td></tr>
									<tr><td style="vertical-align:top;"><b>No. of Packages</b></td><td>: '.$salesData->total_packet.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Total Weight</b></td><td>: '.$salesData->net_weight.'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">CHALLAN No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
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
					$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
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
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
				else
				{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		$mpdf->Output($pdfFileName,'I');
	}
	
	//Created By Karmi @11/04/2022
	public function challan_pdf_Forbhvani($id){
		$salesData = $this->challan->getChallan($id);
		$this->data['companyData'] = $this->challan->getCompanyInfo();
		$this->data['salesData'] = $salesData;		
		$this->data['partyData'] = $this->party->getParty($salesData->party_id);
		$itemData = $salesData->itemData;		
		$this->data['stateData'] = $this->party->getPartyState($this->data['partyData']->state_id);		
		$tblData = '';$i=1; $TaxAmt =0; $cgst=0;$sgst=0;
		foreach($itemData as $row){
			$tblData .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td colspan="2" >'.$row->item_name.'</td>
                            <td>'.$row->hsn_code.'</td>
                            <td> '.$row->qty.'</td>
                            <td>'.$row->unit_name.'</td>
                            <td>'.$row->price.'</td>
                            <td>'.($row->qty * $row->price).'</td>
                        </tr>';
						$TaxAmt += ($row->qty * $row->price);
		}
		$this->data['tableBody'] = $tblData; 
		$this->data['TaxAmt'] = $TaxAmt;
		$this->data['cgst'] = ($this->data['TaxAmt'] * 6)/100;
		$this->data['totalTaxAmt'] = $this->data['TaxAmt'] + $this->data['cgst'] + $this->data['cgst'];	
		$auth_sign=base_url('assets/images/rtth_sign.png');
        $pdfData = $this->load->view('delivery_challan/bhvani_dc_pdf',$this->data,true);
		
        $mpdf = $this->m_pdf->load();
        $pdfFileName='JWO -REG-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName,'I');
     }

	/* Created By : Karmi @15-04-2022*/
	public function back_pdf_forBhavani($id){
		$salesData = $this->challan->getBackPrintForChallan($id);
		$this->data['companyData'] = $this->challan->getCompanyInfo();
		$this->data['salesData'] = $salesData;		
		$tblData = '';$i=1; $totalRejQty = 0; $totalQty=0;
		foreach($salesData as $row){
			$tblData .= '<tr class="text-center">
                            <td >'.$row->vou_name_s.'</td>
                            <td >'.$row->trans_date.'</td>
                            <td >'.$row->trans_no.'</td>
                            <td >'.$row->qty.'</td>
                            <td >'.$row->rej_qty.'</td>
                            <td >'.($row->grnQty - $row->dc_qty).'</td>                
                        </tr>';
			$totalRejQty += $row->rej_qty;
			$totalQty += $row->qty;
		}
		$this->data['tableBody'] = $tblData; 
		$this->data['totalRejQty'] = $totalRejQty; 
		$this->data['totalQty'] = $totalQty; 
		$auth_sign=base_url('assets/images/rtth_sign.png');
        $pdfData = $this->load->view('delivery_challan/bhavani_back_pdf',$this->data,true);
		
        $mpdf = $this->m_pdf->load();
        $pdfFileName='JWO -REG-'.$id.'.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName,'I');
    }

	/*Created By Milan v @29-10-2025*/
	public function dispatch_advise_pdf($id = ''){		
		$sales_id = $id;
		$salesData = $this->challan->getChallan($sales_id);
		$companyData = $this->challan->getCompanyInfo();

		$letter_head=base_url('assets/images/letterhead_top.png');

		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Product No.</th>
						<th style="width:10%;">Act. Ball Size</th>
						<th style="width:10%;">Grade</th>
						<th style="width:10%;">Heat No.</th>
						<th style="width:10%;">P.O NO.</th>
						<th style="width:10%;">P.O Dt.</th>
						<th style="width:15%;">Mat. Recd. Dt.</th>
						<th style="width:10%;">Sign. (Disp. Dept.)</th>
						<th style="width:10%;">Sign. (Sales Dept.)</th>
						<th style="width:10%;">Qty</th>
						
					</tr></thead><tbody>';
		
		// Terms & Conditions		
		$blankLines=10; $blankLines=10;
		$terms = '<table class="table">';$t=0;$tc=new StdClass;		
		if(!empty($salesData->terms_conditions))
		{
			$tc=json_decode($salesData->terms_conditions);
			$blankLines=17 - count($tc);
			foreach($tc as $trms):
				if($t==0):
					$terms .= '<tr>
									<th style="width:17%;font-size:12px;text-align:left;">'.$trms->term_title.'</th>
									<td style="width:48%;font-size:12px;">: '.$trms->condition.'</td>
									<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
										For, '.$companyData->company_name.'<br>
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
							</th>
					</tr>';
		}
		
		$terms .= '</table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0;
		$i=1;$total_qty=0;$page_qty = 0;$page_amount = 0;
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
			$tempData = $this->challan->getChallanTransactions($sales_id,$pr.','.$pageCount);
			
			if(!empty($tempData))
			{
				foreach ($tempData as $row)   
				{
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="37">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_code.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->material_grade.'</td>';
						$pageItems.='<td class="text-center">'.$row->batch_no.'</td>';
						$pageItems.='<td class="text-center">'.$row->doc_no.'</td>';
						$pageItems.='<td class="text-center">'.formatDate($row->doc_date).'</td>';
						$pageItems.='<td class="text-center"></td>';
						$pageItems.='<td class="text-center"></td>';
						$pageItems.='<td class="text-center"></td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
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
				$pageData[$x]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			}
			$pageCount += $pageRow;
		}
		
		$party_gstin = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[0] : '';
		$party_stateCode = (!empty($salesData->party_state_code)) ? explode('#',$salesData->party_state_code)[1] : '';
		
		if(!empty($party_gstin))
		{
			if($party_stateCode!="24")
			{
				$gstRow='<tr>';
					$gstRow.='<td colspan="3" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->cgst_amount + $salesData->sgst_amount + $salesData->freight_gst)).'</td>';
				$gstRow.='</tr>';
			}
		}

		$itemList .= $lastPageItems;
		if($i<$blankLines)
		{
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td  height="37">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="10" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage] .= $itemList;
		$pageData[$totalPage] .= '<br><b><u>Terms & Conditions : </u></b><br>'.$terms.'';

		$invoiceType = '<br><br><table style="margin-bottom:5px;">
								<tr>
									<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" >GSTIN: '.$companyData->company_gst_no.'</th>
									<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">DISPATCH ADVISE</th>
									<th style="width:35%;letter-spacing:2px;" class="text-right"></th>
								</tr>
							</table>';
		
		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:55%;" rowspan="3">
								<table>
									<tr><td style="vartical-align:top;"><b>CHALLAN TO</b></td></tr>
									<tr><td style="vertical-align:top;"><b>'.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.$salesData->billing_address.'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$party_gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:25%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
								<b>Challan No. : '.$salesData->trans_prefix.$salesData->trans_no.'</b>
							</td>
							<td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
								<b>Date : '.date('d/m/Y', strtotime($salesData->trans_date)).'</b>
							</td>
						</tr>
						<tr>
							<td style="width:45%;" colspan="2">
								<table>
									<tr><td style="vertical-align:top;"><b>P.O. No.</b></td><td>: '.$salesData->doc_no.'</td></tr>
									
									<tr><td style="vertical-align:top;"><b>Transport</b></td><td>: '.$salesData->transport_name.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Lr. No.</b></td><td>: '.$salesData->lr_no.'</td></tr>
									<tr><td style="vertical-align:top;"><b>No. of Packages</b></td><td>: '.$salesData->total_packet.'</td></tr>
									<tr><td style="vertical-align:top;"><b>Total Weight</b></td><td>: '.$salesData->net_weight.'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
				
		$htmlHeader = '<img src="'.$letter_head.'">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">CHALLAN No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;

		$pdfFileName = 'Dispatch-advise.pdf';
		
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);

		foreach($pageData as $pg)
		{
			$mpdf->AddPage('P','','','','',7,7,38,7,4,6);
			$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$invoiceType.$baseDetail.$pg.'</div></div>');
		}
		
		$mpdf->Output($pdfFileName,'I');
	}
}
?>