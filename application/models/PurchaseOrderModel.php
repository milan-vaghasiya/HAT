<?php
class PurchaseOrderModel extends MasterModel{
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
	private $purchaseEnquiryMaster = "purchase_enquiry";
    private $itemMaster = "item_master";
	private $grnTrans = "grn_transaction";
	
    public function nextPoNo(){ 
        $data['select'] = "MAX(po_no) as po_no";
        $data['tableName'] = $this->purchaseOrderMaster;
		$data['where']['po_date >= '] = $this->startYearDate;
		$data['where']['po_date <= '] = $this->endYearDate;
		$po_no = $this->specificRow($data)->po_no;
		$nextPoNo = (!empty($po_no))?($po_no + 1):1;
		return $nextPoNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_type,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,purchase_order_master.is_approve,purchase_order_master.approve_date ,party_master.party_name,item_master.item_name";
        $data['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
		$data['group_by'][]='purchase_order_trans.order_id';

		if($data['status'] == 0){ $data['where']['purchase_order_trans.order_status'] = 0; }
        if($data['status'] == 1){
			$data['where']['purchase_order_trans.order_status'] = 1;
			$data['where']['purchase_order_master.po_date >= '] = $this->startYearDate;
			$data['where']['purchase_order_master.po_date <= '] = $this->endYearDate;
		}
		if($data['status'] == 2){ $data['where']['purchase_order_trans.order_status'] = 2; }
		
		$data['order_by']['purchase_order_master.po_date']='DESC';
		$data['order_by']['purchase_order_master.po_no']='DESC';

        $data['searchCol'][] = "purchase_order_master.po_no";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_master.po_date, '%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "DATE_FORMAT(purchase_order_trans.delivery_date, '%d-%m-%Y')";

		$columns =array('','','purchase_order_master.po_no','purchase_order_master.po_date','party_master.party_name','item_master.item_name','','','','','purchase_order_trans.delivery_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
  
    public function getPurchaseOrder($id){
		$data['tableName'] = $this->purchaseOrderMaster;
		$data['select'] = "purchase_order_master.*,party_master.party_name,party_master.contact_person,party_master.contact_email, party_master.party_mobile,party_master.party_address,party_master.gstin,purchase_enquiry.enq_prefix,purchase_enquiry.enq_no,purchase_enquiry.enq_date";
		$data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['leftJoin']['purchase_enquiry'] = "purchase_enquiry.id = purchase_order_master.enq_id";
        $data['where']['purchase_order_master.id'] = $id;
        $result = $this->row($data);
		$result->itemData = $this->getPurchaseOrderTransactions($id);
		return $result;
	}
	
	public function getPurchaseOrderTransactions($id){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name,fg_master.make_brand,die_master.material_grade,item_master.item_image,die_master.item_code as die_no,die_master.item_name as pin_dia";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
		$data['leftJoin']['item_master as die_master'] = "die_master.id = item_master.part_no";
		$data['leftJoin']['item_master as fg_master'] = "fg_master.item_code = item_master.item_code";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['where']['purchase_order_trans.order_id'] = $id;
		$data['group_by'][] = 'purchase_order_trans.id';
        return $this->rows($data);
    }  
	
	public function getOrderItems($orderIds){ 
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.item_type";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		//$data['leftJoin']['material_master'] = "material_master.material_grade=item_master.material_grade";
		$data['where']['purchase_order_trans.order_status'] = 0;
        $data['where_in']['purchase_order_trans.order_id'] = $orderIds;
        return $this->rows($data);
	}

	public function getItemListForGrn($item_type,$order_id){
		$data['tableName'] = $this->purchaseOrderTrans;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,unit_master.unit_name,purchase_order_trans.id as ref_id,purchase_order_trans.delivery_date";
		$data['leftJoin']['item_master'] = "purchase_order_trans.item_id = item_master.id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		
		if(!empty($order_id)):
			$data['where_in']['purchase_order_trans.order_id'] = $order_id;
		endif;
		if(!empty($type)):
			$data['where']['item_master.item_type'] = $type;
		endif;
		return $this->rows($data);
	}

    public function save($masterData,$itemData){ 
		try{
			$this->db->trans_begin();
			$orderId = $masterData['id'];
			$req_id = $masterData['req_id']; unset($masterData['req_id']);
			
			if($this->checkDuplicateOrder($masterData['party_id'],$masterData['po_no'],$orderId) > 0):
				$errorMessage['po_no'] = "PO. No. is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:

				if(empty($orderId)):
					$masterData['po_no'] = $this->purchaseOrder->nextPoNo();
					$masterData['po_prefix'] = 'PO/'.$this->shortYear.'/';
					//save purchase master data
					$purchaseOrderSave = $this->store($this->purchaseOrderMaster,$masterData);
					$orderId = $purchaseOrderSave['insert_id'];		
					
					if(!empty($req_id)){  
						$pr_id = explode(',',$req_id); 
						foreach($pr_id as $key => $value):
							$this->store("purchase_request",["id"=>$value,"ref_id"=>2,"order_status"=>1]); 
						endforeach;	
					}
		
					if(!empty($masterData['enq_id'])):
						$this->store($this->purchaseEnquiryMaster,['id'=>$masterData['enq_id'],'enq_status'=>1]);
					endif;
		
					$result = ['status'=>1,'message'=>'Purchase order saved successfully.','url'=>base_url("purchaseOrder")];			
				else:
					$masterData['is_approve']=0;
					$masterData['approve_date']=NULL;
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
					
					$result = ['status'=>1,'message'=>'Purchase Order updated successfully.','url'=>base_url("purchaseOrder")];
				endif;
		
				foreach($itemData['item_id'] as $key=>$value):
					$transData = [
									'id' => $itemData['id'][$key],
									'order_id' => $orderId,
									'item_id' => $value,
									'item_type' => $itemData['item_type'][$key],
									'unit_id' => $itemData['unit_id'][$key],
									'fgitem_id' => $itemData['fgitem_id'][$key],
									'fgitem_name' => $itemData['fgitem_name'][$key],
									'wo_no' => $itemData['wo_no'][$key],
									'hsn_code' => $itemData['hsn_code'][$key],
									'delivery_date' => $itemData['delivery_date'][$key],
									'qty' => $itemData['qty'][$key],
									'qty_kg' => $itemData['qty_kg'][$key],
									'price' => $itemData['price'][$key],
									'rate_unit' => $itemData['rate_unit'][$key],
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
									'item_remark' => $itemData['item_remark'][$key],
									'created_by' => $itemData['created_by']
								];
					$this->store($this->purchaseOrderTrans,$transData);
				endforeach;

					/* Send Notification */
					$poNo = getPrefixNumber($masterData['po_prefix'],$masterData['po_no']);
					$notifyData['notificationTitle'] = (empty($masterData['id']))?"New Purchase Order":"Update Purchase Order";
					$notifyData['notificationMsg'] = (empty($masterData['id']))?"New Purchase Order Generated. PO. No. : ".$poNo:"Purchase Order updated. PO No. : ".$poNo;
					$notifyData['payload'] = ['callBack' => base_url('purchaseOrder')];
					$notifyData['controller'] = "'purchaseOrder'";
					$notifyData['action'] = (empty($masterData['id']))?"W":"M";
					$this->notify($notifyData);

			endif;
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function checkDuplicateOrder($partyId,$poNo,$id = ""){
        $data['tableName'] = $this->purchaseOrderMaster;
        $data['where']['party_id'] = $partyId;
        $data['where']['po_no'] = $poNo;   
		$data['where']['po_date >= '] = $this->startYearDate;
		$data['where']['po_date <= '] = $this->endYearDate;

		if(!empty($id))
            $data['where']['id != '] = $id;
		return $this->numRows($data);
    }
        
    public function deleteOrder($id){
		try{
            $this->db->trans_begin();
    		$orderData = $this->getPurchaseOrder($id);
            //order transation delete
    		$where['order_id'] = $id;
    		$this->trash($this->purchaseOrderTrans,$where);
    
    		if(!empty($orderData->enq_id)):
    			$this->store($this->purchaseEnquiryMaster,['id'=>$orderData->enq_id,'enq_status'=>0]);
    		endif;
            
            //order master delete
    		$result = $this->trash($this->purchaseOrderMaster,['id'=>$id],'Purchase Order');
    
    		 /* Send Notification */
    		 $poNo = getPrefixNumber($orderData->po_prefix,$orderData->po_no);
    		 $notifyData['notificationTitle'] = "Delete Purchase Order";
    		 $notifyData['notificationMsg'] = "Purchase Order deleted. PO No. : ".$poNo;
    		 $notifyData['payload'] = ['callBack' => base_url('purchaseOrder')];
    		 $notifyData['controller'] = "'purchaseOrder'";
    		 $notifyData['action'] = "D";
    		 $this->notify($notifyData);
    
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

	public function getPartyOrders($id){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "id,po_no,po_prefix,po_date";
        $queryData['where']['order_status'] = 0;
        $queryData['where']['party_id'] = $id;
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

	public function approvePOrder($data) {
        $date = ($data['val'] == 1)?date('Y-m-d'):"";
		$isApprove =  ($data['val'] == 1)?$this->loginId:0;
        $this->store($this->purchaseOrderMaster, ['id'=> $data['id'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }
    
    public function closePOrder($data) {
		$qrydata['order_status'] = $data['val'];
        $this->edit($this->purchaseOrderTrans, ['order_id' => $data['id']], $qrydata, '');
        $this->edit($this->purchaseOrderMaster, ['id' => $data['id']], $qrydata, '');
        return ['status' => 1, 'message' => 'Purchase Order ' . $data['msg'] . ' successfully.'];
    }
    
	public function getFamilyItem($item_id,$family_id){
        $data['tableName'] = $this->itemMaster;
        if(!empty($family_id)){$data['where']['family_id'] = $family_id;}else{$data['where']['id'] = $item_id;}
        $itemData = $this->rows($data);

		$tbody="";$i=1;
		if(!empty($itemData)):
			foreach($itemData as $row):
				$queryData['tableName'] = $this->grnTrans;
				$queryData['select'] = 'grn_transaction.*,grn_master.grn_date,party_master.party_name,item_master.item_name';
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

	public function getItemList($id){        
        $queryData['tableName'] = $this->purchaseOrderTrans;
        $queryData['select'] = "item_master.item_name,purchase_order_trans.hsn_code,purchase_order_trans.igst,purchase_order_trans.qty,unit_master.unit_name,purchase_order_trans.price,purchase_order_trans.amount";
        $queryData['join']['purchase_order_master'] = "purchase_order_trans.order_id = purchase_order_master.id";
        $queryData['join']['item_master'] = "purchase_order_trans.item_id = item_master.id";
        $queryData['join']['unit_master'] = "purchase_order_trans.unit_id = unit_master.id";
        $queryData['where']['purchase_order_master.id'] = $id;
        //print_r($queryData);exit;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_name.'</td>
                            <td class="text-center">'.$row->hsn_code.'</td>
                            <td class="text-center">'.$row->igst.'</td>
                            <td class="text-center">'.$row->qty.'</td>
                            <td class="text-center">'.$row->unit_name.'</td>
                            <td class="text-center">'.$row->price.'</td>
                            <td class="text-center">'.$row->amount.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }


	public function getPendingPoCount(){ 
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "COUNT(id) AS total_po ";
        $data['where']['purchase_order_trans.order_status'] = 0;
		$result = $this->row($data);
        return $result;
    } 
	
	
	
    /************************/
    /**
     * Created By Mansee [04-08-2023]
     * For App
     * Get Pending Purchase Order
     */
    
     public function getPendingPoForApp($data){ 
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,purchase_order_master.delivery_date as due_date,party_master.party_name,item_master.item_name";
        $data['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        
		$data['group_by'][]='purchase_order_trans.order_id';
        $data['where']['purchase_order_trans.order_status'] = 0;
        if($data['dispatch_status'] == 0) { 
            $data['customWhere'][] = "purchase_order_trans.delivery_date >= '".date("Y-m-d")."'"; 

        } elseif($data['dispatch_status'] == 1) { 
            $data['customWhere'][] = "purchase_order_trans.delivery_date < '".date("Y-m-d")."'"; 
         }

        $data['order_by']['purchase_order_trans.delivery_date'] = "ASC";

       
		$result = $this->rows($data);
        return $result;
    } 


	
	/*  Create By : Avruti @27-11-2021 1:00 PM
    update by : 
    note : 
*/
    //----------------------------- API Function Start -------------------------------------------//

    public function getCount($status = 0, $type = 0){
        $data['tableName'] = $this->purchaseOrderTrans;
		if($status == 0){$data['where']['purchase_order_trans.order_status'] = 0;}
        if($status == 1){$data['where']['purchase_order_trans.order_status != '] = 0;}
        return $this->numRows($data);
    }

    public function getPurchaseOrderList_api($limit, $start, $status = 0, $type = 0){
		$data['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,purchase_order_master.is_approve,party_master.party_name,item_master.item_name";
        $data['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        
		$data['group_by'][]='purchase_order_trans.order_id';
// 		if($data['status'] == 0){$data['customWhere'][] = '(purchase_order_trans.qty - purchase_order_trans.rec_qty) > 0';}
//      if($data['status'] == 1){$data['customWhere'][] = '(purchase_order_trans.qty - purchase_order_trans.rec_qty) <= 0';}
		if($status == 0){$data['where']['purchase_order_trans.order_status'] = 0;}
        if($status == 1){$data['where']['purchase_order_trans.order_status != '] = 0;}
		$data['order_by']['purchase_order_master.po_date']='DESC';
		$data['order_by']['purchase_order_master.po_no']='DESC';
        $data['tableName'] = $this->purchaseOrderTrans;

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

//------------------------------ API Function End --------------------------------------------//
}
?>