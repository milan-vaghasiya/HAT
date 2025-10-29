<?php
class QCPurchaseModel extends MasterModel{
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
	private $purchaseEnquiryMaster = "purchase_enquiry";
    private $itemMaster = "qc_instruments";
    private $grnMaster = "grn_master";
	private $grnTrans = "grn_transaction";
	private $itemCategory = "item_category";
    private $qc_indent = "qc_indent";

    public function nextPoNo(){
        $data['select'] = "MAX(po_no) as po_no";
        $data['tableName'] = $this->purchaseOrderMaster;
		$po_no = $this->specificRow($data)->po_no;
		$nextPoNo = (!empty($po_no))?($po_no + 1):1;
		return $nextPoNo;
    } 

    public function getDTRows($data){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,party_master.party_name,item_category.category_code,item_category.category_name,purchase_order_master.order_type";
        $data['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['leftJoin']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['join']['item_category'] = "item_category.id = purchase_order_trans.category_id";
        $data['where']['purchase_order_master.order_type'] = 3;
        // if(!empty($data['order_type'])){ $data['where']['purchase_order_master.order_type'] = 3;}
    
        if(empty($data['status'])){
            $data['where']['purchase_order_trans.order_status'] = 0;
        }else{
            $data['where']['purchase_order_trans.order_status'] = 1;
        }
        

        $data['searchCol'][] = "CONCAT(SUBSTRING_INDEX(SUBSTRING_INDEX(purchase_order_master.po_prefix, '/', 1), '/', -1),'/',purchase_order_master.po_no,'/',SUBSTRING_INDEX(SUBSTRING_INDEX(purchase_order_master.po_prefix, '/', 2), '/', -1))";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_master.po_date, '%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "purchase_order_trans.price";
        $data['searchCol'][] = "purchase_order_trans.qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_trans.delivery_date, '%d-%m-%Y')";

		$columns =array('','','purchase_order_master.po_no','purchase_order_master.po_date','party_master.party_name','item_category.category_name','purchase_order_trans.price','purchase_order_trans.qty','','','purchase_order_trans.delivery_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
       
		return $this->pagingRows($data);
    }
  
    public function getPurchaseOrder($id){
		$data['tableName'] = $this->purchaseOrderMaster;
		$data['select'] = "purchase_order_master.*,party_master.party_name,party_master.contact_person,party_master.contact_email, party_master.party_mobile,party_master.gstin,party_master.party_address,purchase_enquiry.enq_prefix,purchase_enquiry.enq_no,purchase_enquiry.enq_date";
		$data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = purchase_order_master.enq_id";
        $data['where']['purchase_order_master.id'] = $id;
        $result = $this->row($data);
		$result->itemData = $this->getPurchaseOrderTransactions($id);
		return $result;
	}
	
	public function getPurchaseOrderTransactions($id){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_category.category_name,item_category.category_code,item_category.hsn_code,item_category.gst_per";
        $data['leftJoin']['item_category'] = "item_category.id = purchase_order_trans.category_id";
        $data['where']['purchase_order_trans.order_id'] = $id;
        return $this->rows($data);
    }  

	public function getOrderItems($orderIds,$edit_mode=0){
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_category.category_name,item_category.category_code,unit_master.unit_name";
        $data['leftJoin']['item_category'] = "item_category.id = purchase_order_trans.category_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
		if(empty($edit_mode)):
			$data['where']['purchase_order_trans.order_status'] = 0;
		endif;
        $data['where_in']['purchase_order_trans.order_id'] = $orderIds;
        return $this->rows($data);
	}

    public function save($masterData,$itemData){
        $orderId = $masterData['id'];
		
		// if($this->checkDuplicateOrder($masterData['party_id'],$masterData['po_no'],$orderId) > 0):
		// 	$errorMessage['po_no'] = "PO. No. is duplicate.";
		// 	return ['status'=>0,'message'=>$errorMessage];
		// endif;

		if(empty($orderId)):			
			//save purchase master data
			$purchaseOrderSave = $this->store($this->purchaseOrderMaster,$masterData);
			$orderId = $purchaseOrderSave['insert_id'];
			
			

			$result = ['status'=>1,'message'=>'Purchase order saved successfully.','url'=>base_url("qcPurchase")];			
		else:
			$this->store($this->purchaseOrderMaster,$masterData);
			
			$data['select'] = "id";
			$data['where']['order_id'] = $orderId;
			$data['tableName'] = $this->purchaseOrderTrans;
			$ptransIdArray = $this->rows($data);
			
			foreach($ptransIdArray as $key=>$value):
				if(!in_array($value->id,$itemData['id'])):		
					$this->trash($this->purchaseOrderTrans,['id'=>$value->id]);
				endif;
			endforeach;
			
			$result = ['status'=>1,'message'=>'Purchase Order updated successfully.','url'=>base_url("qcPurchase")];
		endif;

		foreach($itemData['category_id'] as $key=>$value):
			$transData = [
							'id' => $itemData['id'][$key],
							'req_id' => $itemData['req_id'][$key],
							'order_id' => $orderId,
							// 'order_type' => 3,
							'category_id' => $value,
							'size' => $itemData['size'][$key],
							'make' => $itemData['make'][$key],
							'unit_id' => 25,
							'gst_per' => $itemData['gst_per'][$key],
							'hsn_code' => $itemData['hsn_code'][$key],
							'delivery_date' => $itemData['delivery_date'][$key],
							'qty' => $itemData['qty'][$key],
							'price' => $itemData['price'][$key],
							'igst' => $itemData['igst'][$key],
							'sgst' => $itemData['sgst'][$key],
							'cgst' => $itemData['cgst'][$key],
							'igst_amt' => $itemData['igst_amt'][$key],
							'sgst_amt' => $itemData['sgst_amt'][$key],
							'cgst_amt' => $itemData['cgst_amt'][$key],
							'amount' => $itemData['amount'][$key],
							'disc_per' => $itemData['disc_per'][$key],
							'disc_amt' => $itemData['disc_amt'][$key],
							'net_amount' => $itemData['net_amount'][$key],
							'created_by' => $itemData['created_by']
						];
			if(!empty($itemData['req_id'][$key])):
				$this->store($this->qc_indent,['id'=>$itemData['req_id'][$key],'status'=>1]);
			endif;
			$this->store($this->purchaseOrderTrans,$transData);
		endforeach;

		return $result;		
    }
	
    public function getQCPRListForPO($id)
    {
        $data['tableName'] = $this->qc_indent;
        $data['select'] = "qc_indent.*,item_category.category_name,item_category.category_code,item_category.hsn_code,item_category.gst_per,(CASE WHEN item_category.category_type = 6 THEN 2 ELSE 1 END) as item_type";
        $data['leftJoin']['item_category'] = "item_category.id = qc_indent.category_id";
        $data['where_in']['qc_indent.id'] = str_replace("~", ",", $id);
        $result = $this->rows($data);
        return $result;
    }

    public function checkDuplicateOrder($partyId,$poNo,$id = ""){
        $data['tableName'] = $this->purchaseOrderMaster;
        $data['where']['party_id'] = $partyId;
        $data['where']['po_no'] = $poNo;        
		if(!empty($id))
            $data['where']['id != '] = $id;
		return $this->numRows($data);
    }
        
    public function deleteOrder($id){
		$orderData = $this->getPurchaseOrder($id);
        //order transation delete
		$where['order_id'] = $id;
		$this->trash($this->purchaseOrderTrans,$where);

		if(!empty($orderData->enq_id)):
			$this->store($this->purchaseEnquiryMaster,['id'=>$orderData->enq_id,'enq_status'=>0]);
		endif;
        
        //order master delete
		return $this->trash($this->purchaseOrderMaster,['id'=>$id],'Purchase Order');
    }

	public function getPartyOrders($party_id,$order_id=""){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "id,po_no,po_prefix,po_date";
        if(!empty($order_id)):
        	$queryData['customWhere'][] = "(order_status = 0 OR id IN (".$order_id."))";
		else:
			$queryData['where']['order_status'] = 0;
		endif;
        $queryData['where']['party_id'] = $party_id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                            <td class="text-center">'.formatDate($row->po_date).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	public function getFamilyItem($item_id,$family_id){
        $data['tableName'] = $this->itemMaster;
        if(!empty($family_id)){$data['where']['family_id'] = $family_id;}else{$data['where']['id'] = $item_id;}
        $itemData = $this->rows($data);

		$tbody="";$i=1;
		if(!empty($itemData)):
			foreach($itemData as $row):
				$queryData['tableName'] = $this->grnTrans;
				$queryData['select'] = 'grn_transaction.*,grn_master.grn_date,party_master.party_name,item_category.category_name';
				$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
				$queryData['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
				$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
				$queryData['where']['grn_transaction.item_id'] = $row->id;
				$queryData['limit'][] = 1;
				// $queryData['group_by'][] = "grn_master.party_id";
				$queryData['order_by']['grn_master.grn_date'] = "DESC";
				// $queryData['order_by']['grn_master.id'] = "DESC";
				$queryData['order_by']['grn_transaction.price'] = "ASC";
				$result = $this->rows($queryData);

				if(!empty($result)):
					foreach($result as $grn):
						$tbody .= '<tr class="text-center">
							<td>'.$i++.'</td>
							<td>'.$grn->item_name.'</td>
							<td>'.$grn->party_name.'</td>
							<td>'.formatDate($grn->grn_date).'</td>
							<td>'.$grn->qty.'</td>
							<td>'.$grn->price.'</td>	
						</tr>';
					endforeach;
				endif;
			endforeach;
		else:
			$tbody .= '<tr class="text-center"><td colspan="6">No data found</td></tr>';
		endif;
		return ['status'=>1,'tbody'=>$tbody];
    }
    
    public function getPendingPartyWisePOItems($data){
		$queryData['tableName'] = $this->purchaseOrderTrans;
        $queryData['select'] = "purchase_order_trans.*,item_category.category_code,item_category.category_name,unit_master.unit_name,purchase_order_master.po_prefix,purchase_order_master.po_no,party_master.party_name";

        $queryData['leftJoin']['item_category'] = "item_category.id = purchase_order_trans.category_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
		$queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = purchase_order_master.id";

		if(!empty($data['category_id'])):
			$queryData['where']['purchase_order_trans.category_id'] = $data['category_id'];
		endif;
		if(!empty($data['party_id'])):
			$queryData['where']['purchase_order_master.party_id'] = $data['party_id'];
		endif;

		if(!empty($data['po_trans_id'])):
			$queryData['customWhere'][] = "(purchase_order_trans.id = ".$data['po_trans_id']." OR purchase_order_trans.order_status = 0)";
		else:
			$queryData['where']['purchase_order_trans.order_status'] = 0;
		endif;
        
        $result = $this->rows($queryData); //print_r($this->db->last_query()); exit;
        return $result;
	}
    
    public function getPOItems($id){
		$data['tableName'] = $this->purchaseOrderTrans;
        //$data['select'] = "purchase_order_trans.*,item_category.category_name,item_category.category_code,item_category.category_type as item_group, item_category.cal_required, item_category.cal_freq, item_category.cal_reminder, item_category.cal_freq_nos, item_category.cal_reminder_nos, unit_master.unit_name";
        $data['select'] = "purchase_order_trans.*,item_category.category_name,item_category.category_code,item_category.category_type as item_group, unit_master.unit_name,(CASE WHEN item_category.category_type = 6 THEN 2 ELSE 1 END) as item_type,IFNULL(qc_indent.req_itm_code,'') as req_itm_code,qc_instruments.least_count";
        $data['leftJoin']['item_category'] = "item_category.id = purchase_order_trans.category_id";
        $data['leftJoin']['qc_indent'] = "qc_indent.id = purchase_order_trans.req_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
        $data['leftJoin']['qc_instruments'] = "qc_instruments.ref_id = purchase_order_trans.id";
        $data['where']['purchase_order_trans.id'] = $id;
        return $this->row($data);
	}

	public function getDataForGenerateCode($postData){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'qc_instruments.item_code,MAX(cat_srno) as cat_srno,MAX(serial_no) as serial_no,qc_instruments.least_count,qc_instruments.instrument_range,item_category.category_name,qc_instruments.item_type,qc_instruments.instrument_range,qc_instruments.size,item_category.category_code';
        $data['leftJoin']['item_category'] = 'item_category.id = qc_instruments.category_id';
        if(!empty($postData['category_id'])){ $data['where']['qc_instruments.category_id'] =$postData['category_id']; }
        if(!empty($postData['instrument_range'])){ $data['where']['qc_instruments.instrument_range'] =$postData['instrument_range']; }
        if(!empty($postData['least_count'])){ $data['where']['qc_instruments.least_count'] =$postData['least_count']; }
        if(!empty($postData['item_type'])){ $data['where']['qc_instruments.item_type'] =$postData['item_type']; }
        if(!empty($postData['size'])){ $data['where']['qc_instruments.size'] =$postData['size']; }
        if(!empty($postData['category_code'])){ $data['where']['item_category.category_code'] =$postData['category_code']; }
        if(!empty($postData['item_code'])){ $data['where']['qc_instruments.item_code'] =$postData['item_code']; }
        $result = $this->row($data);
        return $result;
    }
    
    public function purchaseRecive($data){
        try{
            $this->db->trans_begin();
            foreach($data['id'] as $key=>$value):
                if($data['rec_qty'][$key] > 0):
                    $poitem = $this->getPOItems($value); 
                    
                    for($i=1; $i<=$data['rec_qty'][$key]; $i++): 
                        if(empty($poitem->req_itm_code) AND empty(explode('-',$poitem->req_itm_code)[1]))
                        {
                            if($poitem->item_type == 1){
                                $postData = [
                                    //'item_type'=>$poitem->item_type,
                                    'category_id'=>$poitem->category_id,
                                    //'size'=>$poitem->size
                                ];
                                $generateCode = $this->getDataForGenerateCode($postData);
                            }else{
                                $postData = [
                                    //'item_type'=>$poitem->item_type,
                                    'category_id'=>$poitem->category_id,
                                    //'size'=>$poitem->size,
                                    //'least_count'=>$poitem->least_count
                                ];
                                $generateCode = $this->getDataForGenerateCode($postData); 
                            }
                    		$catNo = $this->getDataForGenerateCode(['category_id'=>$poitem->category_id,'size'=>$poitem->size,'least_count'=>$poitem->least_count]);
                	        $catSrNo = '';
                            
                            if(!empty($catNo->cat_srno)){
                                $catSrNo = $catNo->cat_srno;
                            }else{
                                $catNo = $this->getDataForGenerateCode(['category_id'=>$poitem->category_id]);
                                $catSrNo = (!empty($catNo->cat_srno))?($catNo->cat_srno+1):1;
                            }
                        }
                        else{$itmCatCode = explode('-',$poitem->req_itm_code);$catSrNo = (!empty($itmCatCode[1])) ? intval($itmCatCode[1]) : '';}
                        
                        $serial_no = (!empty($generateCode->serial_no)?$generateCode->serial_no+1:1);
                        $code = $poitem->category_code.sprintf("%03d",$catSrNo);
            	        $name = $code.' '.$poitem->category_name.' '.$poitem->size;
        		        
                        $qcInst = [
                            'id'=>NULL,
                            'ref_id'=>$value,
                            'item_code'=>$code,
                            'serial_no'=>$serial_no,
                            'cat_srno'=>$catSrNo,
                            'item_name'=>$name,
                            'item_type'=>($poitem->item_group == 7)? 1 : 2,
                            'category_id'=>$poitem->category_id,
                            'unit_id'=>25,
                            'gst_per'=>$poitem->igst,
                            'make_brand'=>$poitem->make,
                            'size'=>$poitem->size,
                            //'cal_required' => $poitem->cal_required,
                            //'cal_freq' => $poitem->cal_freq,
                            //'cal_reminder' => $poitem->cal_reminder,
                            //'cal_freq_nos' => $poitem->cal_freq_nos,
                            //'cal_reminder_nos' => $poitem->cal_reminder_nos,
                            'grn_date'=>date('Y-m-d',strtotime($data['grn_date'])),
                            'in_challan_no'=>$data['in_challan_no']
                        ];
                        $this->store('qc_instruments',$qcInst);
                    endfor;
                    
                    $setData = array();
                    $setData['tableName'] = $this->purchaseOrderTrans;
    				$setData['where']['id'] = $value;
    				$setData['set']['rec_qty'] = 'rec_qty, + '.$data['rec_qty'][$key];
    				$qryresult = $this->setValue($setData);
    				
    				$potrans = $this->getPOItems($value); 
    				/** If Po Order Qty is Complete then Close PO **/
    				if($potrans->rec_qty >= $potrans->qty):
    					$this->store($this->purchaseOrderTrans,["id"=>$value, "order_status"=>1]);
    				else:
    					$this->store($this->purchaseOrderTrans,["id"=>$value, "order_status"=>0]);
    				endif;
                endif;
            endforeach;
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Recived Sucessfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}
?>