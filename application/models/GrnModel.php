<?php 
class GrnModel extends MasterModel
{
	private $grnTable = "grn_master";
    private $grnItemTable = "grn_transaction";
    private $purchaseOrderMaster = "purchase_order_master";
    private $purchaseOrderTrans = "purchase_order_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $ic_inspection = "ic_inspection";
	private $trans_main = "trans_main";
    private $gradeMaster = "grade_master";
    private $tcInspect = "tc_inspection";
    private $materialSpeci = "material_specification";
    private $fg_opening_stock = "fg_opening_stock";
	
    public function nextGrnNo(){
        $data['select'] = "MAX(grn_no) as grnNo";
        $data['tableName'] = $this->grnTable;
		$data['where']['grn_date >= '] = $this->startYearDate;
		$data['where']['grn_date <= '] = $this->endYearDate;
        $data['where']['is_delete'] = 0;
		$grnNo = $this->specificRow($data)->grnNo;
		$nextGrnNo = (!empty($grnNo))?($grnNo + 1):1;
		return $nextGrnNo;
    }
	
	public function getDTRows($data){
        $data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,	grn_master.remark,grn_master.type,party_master.party_name,item_master.item_name,purchase_order_master.po_no,purchase_order_master.po_prefix,unit_master.unit_name,location_master.location as store_name";
        $data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['join']['party_master'] = "party_master.id = grn_master.party_id";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['leftJoin']['location_master'] = "location_master.id = grn_transaction.location_id";
		$data['where']['grn_master.grn_date >= '] = $this->startYearDate;
		$data['where']['grn_master.grn_date <= '] = $this->endYearDate;
		$data['where']['grn_transaction.grn_type'] = 0;
        $data['order_by']['grn_master.grn_date'] = "DESC";
        $data['order_by']['grn_master.id'] = "DESC";
		//$data['group_by'][]='grn_transaction.grn_id';

        $data['searchCol'][] = "CONCAT(grn_master.grn_prefix,grn_master.grn_no)";
        $data['searchCol'][] = "grn_master.challan_no";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.grn_date,'%d-%m-%Y')";
        $data['searchCol'][] = "purchase_order_master.po_prefix";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_transaction.qty";
        $data['searchCol'][] = "unit_master.unit_name";
        $data['searchCol'][] = "grn_transaction.batch_no";
        $data['searchCol'][] = "grn_transaction.color_code";
		$data['searchCol'][] = "grn_transaction.fgitem_name";

		$columns =array('','','grn_master.grn_no','grn_master.grn_date','purchase_order_master.po_prefix','party_master.party_name','item_master.item_name','grn_transaction.qty','unit_master.unit_name,grn_transaction.batch_no','grn_transaction.color_code','grn_transaction.fgitem_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

        return $this->pagingRows($data);
	}

	// avruti 14-9-21
	public function purchaseMaterialInspectionList($data){
		$data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.material_grade_id,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,purchase_order_master.po_no,purchase_order_master.po_prefix, party_master.party_name, purchase_inspection.inspection_status";
		$data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
		$data['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
		$data['leftJoin']['purchase_inspection'] = "purchase_inspection.ptrans_id = grn_transaction.id";
        $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
        
        //if(!empty($data['fgitem_id']) && isset($data['fgitem_id'])){ $data['customWhere'][] = "FIND_IN_SET('".$data['fgitem_id']."', fgitem_id)"; }
		$data['where']['grn_master.type'] = 1;
		$data['where']['grn_transaction.item_type'] = 3;
        $data['order_by']['grn_master.grn_date'] = "DESC";

		if($data['status'] == 1) { 
			$data['where']['grn_transaction.inspected_qty != '] = 0; 
			$data['where']['grn_master.grn_date >= '] = $this->startYearDate;
			$data['where']['grn_master.grn_date <= '] = $this->endYearDate;
			$data['where']['purchase_inspection.is_delete'] = 0;
		} 
		if($data['status'] == 0) { $data['where']['grn_transaction.inspected_qty'] = 0; }

        $data['searchCol'][] = "grn_master.grn_no";
        $data['searchCol'][] = "DATE_FORMAT(grn_master.grn_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_master.challan_no";
        $data['searchCol'][] = "purchase_order_master.po_no";
        $data['searchCol'][] = "grn_transaction.qty";
		$data['searchCol'][] = "party_master.party_name";
		$data['searchCol'][] = "grn_transaction.batch_no";
		$data['searchCol'][] = "grn_transaction.color_code";
		
		$columns = array('','','grn_master.grn_no','grn_master.grn_date','grn_master.challan_no','purchase_order_master.po_no','party_master.party_name','item_master.item_name','','grn_transaction.qty','grn_transaction.batch_no','grn_transaction.color_code','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}
	
	public function checkDuplicateGRN($party_id,$grn_no,$id){
        $data['tableName'] = $this->grnTable;
		$data['where']['grn_date >= '] = $this->startYearDate;
		$data['where']['grn_date <= '] = $this->endYearDate;
        $data['where']['grn_no'] = $grn_no;
        $data['where']['party_id'] = $party_id;
        if(!empty($id))
            $data['where']['id != '] = $id;

        return $this->numRows($data);
	}
	
	public function save($masterData,$itemData){ 
		try{
            $this->db->trans_begin();
			$purchaseId = $masterData['id'];
			
			$checkDuplicate = $this->checkDuplicateGRN($masterData['party_id'],$masterData['grn_no'],$purchaseId);
			if($checkDuplicate > 0):
				$errorMessage['grn_no'] = "GRN No. is Duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			endif;

			if(empty($purchaseId)):				
				//save purchase master data
				$purchaseInvSave = $this->store($this->grnTable,$masterData);
				$purchaseId = $purchaseInvSave['insert_id'];			
				
				$result = ['status'=>1,'message'=>'GRN saved successfully.','url'=>base_url("grn")];			
			else:
				$data = Array();
				$data['tableName'] = $this->grnTable;
				$data['where']['id'] = $purchaseId;
				$grnData = $this->row($data);

				$this->store($this->grnTable,$masterData);
				
				$data = Array();
				$data['where']['grn_id'] = $purchaseId;
				$data['tableName'] = $this->grnItemTable;
				$ptransArray = $this->rows($data);
				// print_r($ptransArray);exit;
				
				foreach($ptransArray as $value):
					if(!in_array($value->id,$itemData['id'])):						
						$this->trash($this->grnItemTable,['id'=>$value->id]);
					endif;
					if($grnData->type == 2 || $value->item_type != 3):
						$setData = array();
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $value->item_id;
						$setData['set']['qty'] = 'qty, - '.$value->qty;
						$qryresult = $this->setValue($setData);
						
						$this->remove($this->stockTrans,['ref_id'=>$value->id,'ref_type'=>1,'item_id'=>$value->item_id]);
					endif;

					if(!empty($value->po_trans_id)):
						$setData = array();
						$setData['tableName'] = $this->purchaseOrderTrans;
						$setData['where']['id'] = $value->po_trans_id;
						$setData['set']['rec_qty'] = 'rec_qty, - '.$value->qty;
						$qryresult = $this->setValue($setData);

						/** If Po Order Qty is Complete then Close PO **/
						$poTrans = $this->getPoTransactionRow($value->po_trans_id);
						if($poTrans->rec_qty >= $poTrans->qty):
							$this->store($this->purchaseOrderTrans,["id"=>$value->po_trans_id, "order_status"=>1]);
						else:
							$this->store($this->purchaseOrderTrans,["id"=>$value->po_trans_id, "order_status"=>0]);
						endif;
					endif;
				endforeach;		
				
				$result = ['status'=>1,'message'=>'GRN updated successfully.','url'=>base_url("grn")];
			endif;

			//save purchase items
			foreach($itemData['item_id'] as $key=>$value):
				$transData = [
								'id' => $itemData['id'][$key],
								'grn_id' => $purchaseId,
								'batch_no' => $itemData['batch_no'][$key],
								'po_trans_id' => $itemData['po_trans_id'][$key],
								'so_id' => $itemData['so_id'][$key],
								'item_id' => $value,
								'item_type' =>$itemData['item_type'][$key],
								'unit_id' => $itemData['unit_id'][$key],
								'fgitem_id' => $itemData['fgitem_id'][$key],
								'fgitem_name' => $itemData['fgitem_name'][$key],
								'location_id' => $itemData['location_id'][$key],
								'qty' => $itemData['qty'][$key],
								'qty_kg' => $itemData['qty_kg'][$key],
								'price' => $itemData['price'][$key],
								'color_code' => $itemData['color_code'][$key],
								'item_remark' => $itemData['item_remark'][$key],
								'created_by' => $itemData['created_by']
							];
				if(empty($itemData['id'][$key]) && $masterData['type'] == 2):
					$transData['inspected_qty'] = $itemData['qty'][$key];
					$transData['remaining_qty'] = $itemData['qty'][$key];	
				elseif(!empty($itemData['id'][$key]) && $masterData['type'] == 2):
					$queryData = array();
					$queryData['tableName'] = $this->grnItemTable;
					$queryData['where']['id'] = $itemData['id'][$key];
					$transRow = $this->row($queryData);
					if($transRow->qty == $transRow->inspected_qty):
						$transData['inspected_qty'] = $itemData['qty'][$key];
						$transData['remaining_qty'] = $itemData['qty'][$key];
					endif;
				endif;

				$transRowSave = $this->store($this->grnItemTable,$transData);
				$trans_id = 0;
				$trans_id = (!empty($itemData['id'][$key])) ? $itemData['id'][$key] : $transRowSave['insert_id'];


				if($masterData['type'] == 2 || $itemData['item_type'][$key] != 3):
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $value;
					$setData['set']['qty'] = 'qty, + '.$itemData['qty'][$key];
					$qryresult = $this->setValue($setData);
					
					/*** UPDATE STOCK TRANSACTION DATA ***/
					$stockQueryData['id']="";
					$stockQueryData['location_id']=$itemData['location_id'][$key];
					if(!empty($itemData['batch_no'][$key])){
						$stockQueryData['batch_no'] = $itemData['batch_no'][$key];
						$stockQueryData['tc_no'] = $itemData['batch_no'][$key];
					}else{
						$stockQueryData['batch_no'] = 'General Batch';
						$stockQueryData['tc_no'] = 'General Batch';
					}
					$stockQueryData['trans_type']=1;
					$stockQueryData['item_id']=$value;
					$stockQueryData['qty']=$itemData['qty'][$key];
					$stockQueryData['ref_type']=1;
					$stockQueryData['ref_id']=$trans_id;
					$stockQueryData['ref_no']=getPrefixNumber($masterData['grn_prefix'],$masterData['grn_no']);
					$stockQueryData['ref_date']=$masterData['grn_date'];
					$stockQueryData['created_by']=$this->loginID;
					$this->store($this->stockTrans,$stockQueryData);
				endif;
				
				if(!empty($itemData['po_trans_id'][$key])):
					$setData = array();
					$setData['tableName'] = $this->purchaseOrderTrans;
					$setData['where']['id'] = $itemData['po_trans_id'][$key];
					$setData['set']['rec_qty'] = 'rec_qty, + '.$itemData['qty'][$key];
					$qryresult = $this->setValue($setData);
					
					/** If Po Order Qty is Complete then Close PO **/
					$poTrans = $this->getPoTransactionRow($itemData['po_trans_id'][$key]);
					if($poTrans->rec_qty >= $poTrans->qty):
						$this->store($this->purchaseOrderTrans,["id"=>$itemData['po_trans_id'][$key], "order_status"=>1]);
					else:
						$this->store($this->purchaseOrderTrans,["id"=>$itemData['po_trans_id'][$key], "order_status"=>0]);
					endif;
				endif;
				
				
			endforeach;

			if(!empty($masterData['order_id'])):
				$poIds = explode(",",$masterData['order_id']);
				foreach($poIds as $key=>$value):
					$queryData = array();
					$queryData['tableName'] = $this->purchaseOrderTrans;
					$queryData['select'] = "COUNT(id) as pendingItems";
					$queryData['where']['order_id'] = $value;
					$queryData['where']['order_status'] = 0;
					$pendingItems = $this->specificRow($queryData)->pendingItems;
					
					if(empty($pendingItems)):
						$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>1]);
					else:
						$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>0]);
					endif;
				endforeach;
			endif;
				/* Send Notification */
				$grnNo = getPrefixNumber($masterData['grn_prefix'],$masterData['grn_no']);
				$notifyData['notificationTitle'] = (empty($masterData['id']))?"New GRN":"Update GRN";
				$notifyData['notificationMsg'] = (empty($masterData['id']))?"New GRN Generated. GRN. No. : ".$grnNo:"GRN updated. GRN No. : ".$grnNo;
				$notifyData['payload'] = ['callBack' => base_url('grn')];
				$notifyData['controller'] = "'grn'";
				$notifyData['action'] = (empty($masterData['id']))?"W":"M";
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
	
	public function getPoTransactionRow($id){
		$data['tableName'] = $this->purchaseOrderTrans;
        $data['where']['id'] = $id;
        return $this->row($data);
	}
	
	//Updated By Meghavi 28-06-2022
	public function editInv($id){
        $data['tableName'] = $this->grnTable;
		$data['select'] = "grn_master.*,party_master.party_name,party_master.party_code,party_master.party_address,party_master.party_pincode,purchase_order_master.po_no,purchase_order_master.po_prefix";
        $data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
		$data['leftJoin']['party_master'] = "grn_master.party_id = party_master.id";
        $data['where']['grn_master.id'] = $id;
		$result = $this->row($data);

        $data = array();
        $data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name,location_master.location as store_name,item_master.drawing_no,purchase_order_master.po_no,purchase_order_master.po_prefix";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
		$data['leftJoin']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
		$data['leftJoin']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
		$data['leftJoin']['location_master'] = "location_master.id = grn_transaction.location_id";
        $data['where']['grn_transaction.grn_id'] = $id;
		$result->itemData = $this->rows($data);
		return $result;
	}
	
	public function delete($id){
		try{
            $this->db->trans_begin();
			$grnData = $this->editInv($id);

			foreach($grnData->itemData as $row):
				if($grnData->type == 2 || $row->item_type != 3):
					$setData = array();
					$setData['tableName'] = $this->itemMaster;
					$setData['where']['id'] = $row->item_id;
					$setData['set']['qty'] = 'qty, - '.$row->qty;
					$qryresult = $this->setValue($setData);
					
					$this->remove($this->stockTrans,['ref_id'=>$row->id,'ref_type'=>1,'item_id'=>$row->item_id]);
				else:
					if($row->inspected_qty > 0):
						$data = array();
						$data['tableName'] = 'purchase_inspection';
						$data['where']['ptrans_id'] = $row->id;
						$data['where']['grn_id'] = $row->grn_id;
						$data['where']['is_delete'] = 0;
						$inspectedData = $this->row($data);
						
						/** Update Item Stock **/	
						$setData = array();			
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $row->item_id;
						$setData['set']['qty'] = 'qty, - '.$inspectedData->inspected_qty;
						$setData['set']['reject_qty'] = 'reject_qty, - '.$inspectedData->reject_qty;
						$setData['set']['scrape_qty'] = 'scrape_qty, - '.$inspectedData->scrape_qty;
						$setData['set']['short_qty'] = 'short_qty, - '.$inspectedData->short_qty;
						$qryresult = $this->setValue($setData);
		
						/*** UPDATE STOCK TRANSACTION DATA ***/
						$this->remove($this->stockTrans,['ref_id'=>$inspectedData->ptrans_id,'ref_type'=>1,'item_id'=>$inspectedData->item_id]);
						$this->trash('purchase_inspection',['id'=>$inspectedData->id],'');
					endif;
				endif;
				

				if(!empty($row->po_trans_id)):
					$setData = array();
					$setData['tableName'] = $this->purchaseOrderTrans;
					$setData['where']['id'] = $row->po_trans_id;
					$setData['set']['rec_qty'] = 'rec_qty, - '.$row->qty;
					$qryresult = $this->setValue($setData);

					/** If Po Order Qty is Complete then Close PO **/
					$poTrans = $this->getPoTransactionRow($row->po_trans_id);
					if($poTrans->rec_qty >= $poTrans->qty):
						$this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>1]);
					else:
						$this->store($this->purchaseOrderTrans,["id"=>$row->po_trans_id, "order_status"=>0]);
					endif;
				endif;
				$this->trash($this->grnItemTable,['id'=>$row->id]);
			endforeach;
			
			if(!empty($grnData->order_id)):
				$poIds = explode(",",$grnData->order_id);
				foreach($poIds as $key=>$value):
					$queryData = array();
					$queryData['tableName'] = $this->purchaseOrderTrans;
					$queryData['select'] = "COUNT(id) as pendingItems";
					$queryData['where']['order_id'] = $value;
					$queryData['where']['order_status'] = 0;
					$pendingItems = $this->specificRow($queryData)->pendingItems;				
					if(empty($pendingItems)):
						$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>1]);
					else:
						$this->store($this->purchaseOrderMaster,['id'=>$value,'order_status'=>0]);
					endif;
				endforeach;
			endif;

			$result = $this->trash($this->grnTable,['id'=>$id],'GRN');
			/* Send Notification */
			$grnNo = getPrefixNumber($grnData->grn_prefix,$grnData->grn_no);
			$notifyData['notificationTitle'] = "Delete GRN";
			$notifyData['notificationMsg'] = "GRN deleted. GRN No. : ".$grnNo;
			$notifyData['payload'] = ['callBack' => base_url('grn')];
			$notifyData['controller'] = "'grn'";
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

	public function getInspectedMaterial($id){
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where']['grn_transaction.id'] = $id;       
        $data['tableName'] = $this->grnItemTable;
		$result = $this->rows($data);
		
		if(!empty($result)):
			$i=1;$html="";
			foreach($result as $row):
                $data = array();
                $data['tableName'] = 'purchase_inspection';
                $data['where']['ptrans_id'] = $row->id;
                $data['where']['grn_id'] = $row->grn_id;
                $data['where']['is_delete'] = 0;
				$inspectedData = $this->row($data);
				
				$readonly = ($row->inspected_qty != $row->remaining_qty)?"readonly":"";
				$disable = ($row->inspected_qty != $row->remaining_qty)?"disabled":"";
				$inspOptions = '<option value="">Select Status</option>';
				if(!empty($inspectedData)):
					$inspected_id = $inspectedData->id;
					$inspected_qty = $inspectedData->inspected_qty;
					$ud_qty = $inspectedData->ud_qty;
					$reject_qty = $inspectedData->reject_qty;
					$scrape_qty = $inspectedData->scrape_qty;
					$short_qty = $inspectedData->short_qty;
					if($inspectedData->inspection_status == "Ok"):
						$inspOptions = '<option value="Ok" selected '.$disable.'>Ok</option><option value="Not Ok" '.$disable.'>Not Ok</option>';
					else:
						$inspOptions = '<option value="Ok" '.$disable.'>Ok</option><option value="Not Ok" selected '.$disable.'>Not Ok</option>';
					endif;
				else:
					$inspected_id = "";
					$inspected_qty = $row->qty;
					$ud_qty = 0;
					$reject_qty = 0;
					$scrape_qty = 0;
					$short_qty = 0;
					$inspOptions = '<option value="Ok" '.$disable.'>Ok</option><option value="Not Ok" '.$disable.'>Not Ok</option>';
				endif;
				
				$html .= '<tr class="text-center">
							<td>'.$i.'</td>
							<td>'.$row->batch_no.'</td>
							<td>
								'.$row->qty.'
								<input type="hidden" name="recived_qty[]" id="recived_qty'.$i.'" value="'.$row->qty.'">
								<input type="hidden" name="inspected_id[]" id="inspected_id'.$i.'" value="'.$inspected_id.'">
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" value="'.$row->item_id.'">
								<input type="hidden" name="ptrans_id[]" id="ptrans_id'.$i.'" value="'.$row->id.'">
								<input type="hidden" name="ud_qty[]" id="ud_qty'.$i.'" class="form-control floatOnly" value="'.$ud_qty.'">
								<input type="hidden" name="reject_qty[]" id="reject_qty'.$i.'" class="form-control floatOnly" value="'.$reject_qty.'">
								<input type="hidden" name="scrape_qty[]" id="scrape_qty'.$i.'" class="form-control floatOnly" value="'.$scrape_qty.'">
								<input type="hidden" name="inspected_qty[]" id="inspected_qty'.$i.'" class="form-control floatOnly" value="'.$inspected_qty.'">
								<input type="hidden" name="tc_no[]" id="tc_no'.$i.'" class="form-control" value="'.$row->batch_no.'~'.$row->sys_tc_no.'">
								<div class="error recived_qty'.$i.'"></div>
							</td>
							<td>
								<select name="inspection_status[]" id="inspection_status'.$i.'" class="form-control">'.$inspOptions.'</select>
								<div class="error inspection_status'.$i.'"></div>
							</td>
							<td>
								<input type="number" name="short_qty[]" id="short_qty'.$i.'" class="form-control floatOnly" value="'.$short_qty.'" '.$readonly.'>
								<div class="error short_qty'.$i.'"></div>
							</td>
						</tr>';
				$i++;
			endforeach;
		else:
			$html = '<tr><td class="text-center" colspan="5">No data available in table</td></tr>';
		endif;
		return $html;
	}

	public function inspectedMaterialSave($data){
		try{
            $this->db->trans_begin();
		
			$grnData = $this->editInv($data['grn_id']);
			foreach($data['item_id'] as $key=>$value):
				$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
				$reject_qty = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
				$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;
				if(empty($data['inspected_id'][$key])):				
					$dataRow = [
						'id' => "",
						'inspection_date' => date('Y-m-d'),
						'grn_id' => $data['grn_id'],
						'ptrans_id' => $data['ptrans_id'][$key],
						'item_id' => $value,
						'recived_qty' => $data['recived_qty'][$key],
						'inspection_status' => $data['inspection_status'][$key],
						'inspected_qty' => $inspected_qty,
						'ud_qty' => $data['ud_qty'][$key],
						'reject_qty' => $reject_qty,
						'scrape_qty' => $data['scrape_qty'][$key],
						'short_qty' => $data['short_qty'][$key],
						'created_by' => $data['created_by']
					]; 
					$this->store('purchase_inspection',$dataRow);

					if($data['inspection_status'][$key] == "Ok"):
						/** Update Item Stock **/	
						$setData = array();					
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $value;
						$setData['set']['qty'] = 'qty, + '.$inspected_qty;
						$setData['set']['reject_qty'] = 'reject_qty, + '.$reject_qty;
						$setData['set']['scrape_qty'] = 'scrape_qty, + '.$data['scrape_qty'][$key];
						$setData['set']['short_qty'] = 'short_qty, + '.$data['short_qty'][$key];
						$qryresult = $this->setValue($setData);

						/*** UPDATE STOCK TRANSACTION DATA ***/
						if($data['inspected_qty'][$key] > 0):
							$ptransItem = $this->getgrnItemTableRow($data['ptrans_id'][$key]);
							
							if(!empty($ptransItem)):
								$stockQueryData['id']="";
								$stockQueryData['location_id']=$ptransItem->location_id;
								if(!empty($ptransItem->batch_no)){$stockQueryData['batch_no'] = $ptransItem->batch_no;}
								else{$stockQueryData['batch_no'] = 'General Batch';}
								$stockQueryData['tc_no'] = $data['tc_no'][$key];
								$stockQueryData['trans_type']=1;
								$stockQueryData['item_id']=$ptransItem->item_id;
								$stockQueryData['qty']=$inspected_qty;
								$stockQueryData['ref_type']=1;
								$stockQueryData['ref_id']=$data['ptrans_id'][$key];
								$stockQueryData['ref_no']=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no);
								$stockQueryData['ref_date']=$grnData->grn_date;
								$stockQueryData['created_by']=$this->loginID;
								$this->store($this->stockTrans,$stockQueryData);
							endif;
						endif;
					endif;
					
					$grnItemTableData = [
						'id' => $data['ptrans_id'][$key],
						'inspected_qty' => $data['recived_qty'][$key],
						'remaining_qty' => $data['recived_qty'][$key]
					];
					$this->store($this->grnItemTable,$grnItemTableData);
				else:
					$queryData['tableName'] = "purchase_inspection";
					$queryData['where']['id'] = $data['inspected_id'][$key];
					$inspectedData = $this->row($queryData);

					if($inspectedData->inspection_status == "Ok"):
						/** Update Item Stock **/	
						$setData = array();			
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $value;
						$setData['set']['qty'] = 'qty, - '.$inspectedData->inspected_qty;
						$setData['set']['reject_qty'] = 'reject_qty, - '.$inspectedData->reject_qty;
						$setData['set']['scrape_qty'] = 'scrape_qty, - '.$inspectedData->scrape_qty;
						$setData['set']['short_qty'] = 'short_qty, - '.$inspectedData->short_qty;
						$qryresult = $this->setValue($setData);

						/*** UPDATE STOCK TRANSACTION DATA ***/
						$this->remove($this->stockTrans,['ref_id'=>$inspectedData->ptrans_id,'ref_type'=>1,'item_id'=>$inspectedData->item_id]);
					endif;

					$dataRow = [
						'id' => $data['inspected_id'][$key],
						'inspection_date' => date('Y-m-d'),
						'grn_id' => $data['grn_id'],
						'ptrans_id' => $data['ptrans_id'][$key],
						'item_id' => $value,
						'recived_qty' => $data['recived_qty'][$key],
						'inspection_status' => $data['inspection_status'][$key],
						'inspected_qty' => $inspected_qty,
						'ud_qty' => $data['ud_qty'][$key],
						'reject_qty' => $reject_qty,
						'scrape_qty' => $data['scrape_qty'][$key],
						'short_qty' => $data['short_qty'][$key]
					]; 
					$this->store('purchase_inspection',$dataRow);				

					if($data['inspection_status'][$key] == "Ok"):
						/** Update Item Stock **/
						$setData = array();						
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $value;
						$setData['set']['qty'] = 'qty, + '.$inspected_qty;
						$setData['set']['reject_qty'] = 'reject_qty, + '.$reject_qty;
						$setData['set']['scrape_qty'] = 'scrape_qty, + '.$data['scrape_qty'][$key];
						$setData['set']['short_qty'] = 'short_qty, + '.$data['short_qty'][$key];
						$qryresult = $this->setValue($setData);

						/*** UPDATE STOCK TRANSACTION DATA ***/
						if($inspected_qty > 0):
							$ptransItem = $this->getgrnItemTableRow($data['ptrans_id'][$key]);
							// $grnData = $this->editInv($data['ptrans_id'][$key]);
							if(!empty($ptransItem)):
								$stockQueryData['id']="";
								$stockQueryData['location_id']=$ptransItem->location_id;
								if(!empty($ptransItem->batch_no)){$stockQueryData['batch_no'] = $ptransItem->batch_no;}
								$stockQueryData['tc_no'] = $data['tc_no'][$key];
								$stockQueryData['trans_type']=1;
								$stockQueryData['item_id']=$ptransItem->item_id;
								$stockQueryData['qty']=$inspected_qty;
								$stockQueryData['ref_type']=1;
								$stockQueryData['ref_id']=$data['ptrans_id'][$key];
								$stockQueryData['ref_no']=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no);
								$stockQueryData['ref_date']=$grnData->grn_date;
								$stockQueryData['created_by']=$this->loginID;
								$this->store($this->stockTrans,$stockQueryData);
							endif;
						endif;
					endif;
					
					$grnItemTableData = [
						'id' => $data['ptrans_id'][$key],
						'inspected_qty' => $data['recived_qty'][$key],
						'remaining_qty' => $data['recived_qty'][$key]
					];
					$this->store($this->grnItemTable,$grnItemTableData);				
				endif;
			endforeach;

			$result = ['status'=>1,'message'=>'Inspected Material saved successfully.'];
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function getLotWisegrnItemTables(){
		$queryData['select'] = "grn_transaction.id,grn_transaction.batch_no,grn_transaction.remaining_qty,grn_transaction.qty,grn_transaction.item_id,item_master.item_name,unit_master.unit_name";
		$queryData['join']['item_master'] = "grn_transaction.item_id = item_master.id";
		$queryData['join']['unit_master'] = "grn_transaction.unit_id = unit_master.id";
		$queryData['where']['grn_transaction.remaining_qty !='] = "0.000";
		$queryData['where']['item_master.rm_type'] = 1;
        $queryData['tableName'] = $this->grnItemTable;
		return $this->rows($queryData);
	}

	public function getgrnItemTableRow($id){
		$data['tableName'] = $this->grnItemTable;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

    public function getItemsForGRN($party_id){
		
		$itemOptions='<option value="">Select Product Name</option>';
				
		$qdata['tableName'] = $this->purchaseOrderTrans;
		$qdata['select'] = "purchase_order_trans.*,item_master.item_name as itmname,item_master.item_code,item_master.unit_id,item_master.item_type,item_master.hsn_code, item_master.gst_per,item_master.price,unit_master.unit_name,purchase_order_master.po_no";
		$qdata['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
		$qdata['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
		$qdata['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$qdata['where']['purchase_order_master.party_id'] = $party_id;
		$qdata['where']['purchase_order_trans.order_status != '] = 2;
		$qdata['customWhere'][] = "purchase_order_trans.qty > purchase_order_trans.rec_qty";
		$itemData = $this->rows($qdata);
		$selectedIds = Array();
		if(!empty($itemData)):
			foreach ($itemData as $row):
				$dataRow = json_encode($row);$dataRow = "data-data_row='".$dataRow."'";
				$year = (date('m') > 3)?date('y').'-'.(date('y') +1):(date('y')-1).'-'.date('y');
				$itemOptions .= '<option value="'.$row->item_id.'" data-po_trans_id="'.$row->id.'" data-year="'.$year.'" '.$dataRow.' >['.$row->item_code.'] '.$row->itmname.' [PO/'.$row->po_no.'/'.$year.']</option>';
			endforeach;
		endif;
		$itemData = $this->item->getItemList();
		if(!empty($itemData)):
			foreach ($itemData as $row):
				if(!in_array($row->id,$selectedIds)):
					$dataRow = json_encode($row);$dataRow = "data-data_row='".$dataRow."'";
					$itemOptions .= '<option value="'.$row->id.'" data-po_trans_id="" data-year="" '.$dataRow.' >['.$row->item_code.'] '.$row->item_name.' </option>';
				endif;
			endforeach;
		endif;
		
		return $itemOptions;
	}

	public function getItemList($type=0){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
    
    public function itemColorCode(){
		$data['tableName'] = $this->grnItemTable;
		$data['select'] = 'color_code';
		$result = $this->rows($data);
		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->color_code;}
		return  $searchResult;
    }

	public function getCustomerGrn($party_id){
		$data['tableName'] = $this->grnTable;
		$data['select'] = "id,grn_prefix,grn_no,grn_date,challan_no";
		$data['where']['party_id'] = $party_id;
		$data['where']['type'] = 2;
		$result = $this->rows($data);
		return $result;
	}

    //Created By Avruti @08/09/2022
	public function getGrnItems($id){
		$data = array();
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where_in']['grn_transaction.grn_id'] = $id;
        $data['tableName'] = $this->grnItemTable;
		$result = $this->rows($data);
		return $result;
	}

	public function getInInspectionEdit($id,$grn_id){
		$data['tableName'] = $this->ic_inspection;
        $data['where']['ic_inspection.grn_trans_id'] = $id;         
        $data['where']['ic_inspection.grn_id'] = $grn_id;         
        $data['where']['ic_inspection.trans_type'] = 0;           
		return $this->row($data);
	}

	public function getInInspectionMaterial($id){
        $data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,grn_transaction.id as grn_trans_id,grn_master.grn_no,grn_master.party_id,item_master.item_name,item_master.material_grade_id";
		$data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
		$data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['where']['grn_transaction.id'] = $id;       
		return $this->row($data);
	}

	public function getInInspection($id,$param_id=""){
		$data['tableName'] = $this->ic_inspection;
		//$data['select'] = "ic_inspection.*, item_master.item_code, item_master.item_name, item_master.material_grade, grn_transaction.batch_no, grn_transaction.color_code, grn_transaction.qty, grn_transaction.is_approve, grn_transaction.approve_date, grn_master.grn_date, grn_transaction.fgitem_id,grn_transaction.approval_remarks,grn_transaction.item_type,item_group.group_name";
       	$data['select'] = "ic_inspection.*, item_master.item_code, item_master.item_name, item_master.material_grade, grn_transaction.batch_no, grn_transaction.color_code, grn_transaction.qty, grn_transaction.is_approve, grn_transaction.approve_date, grn_master.grn_date,grn_master.challan_no, grn_transaction.fgitem_id,grn_transaction.approval_remarks,grn_transaction.item_type,item_group.group_name";
		$data['leftJoin']['grn_transaction'] = "grn_transaction.id = ic_inspection.grn_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
		$data['leftJoin']['item_group'] = "item_group.id = grn_transaction.item_type";
        $data['where']['ic_inspection.grn_trans_id'] = $id;         
        $data['where']['ic_inspection.trans_type'] = 0;       
		if(!empty($param_id))
		  $data['where']['ic_inspection.param_id'] = $param_id;    
		return $this->row($data);
	}

	public function saveInInspection($data){
		try{
            $this->db->trans_begin();
    		$result = $this->store($this->ic_inspection,$data,'Incoming Inspection');
    		if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

	public function getFinishGoods($fgitem_id){
		$data['tableName']=$this->itemMaster;
		$data['select']='item_code';
		$data['where_in']['id']=$fgitem_id;
		return $this->rows($data);
	}
	
	/*
    * Create By : Meghavi
    * Updated By : NYN @04-11-2021 12:48 AM 
    * Note : Reject Inspection
    */
	public function approveInspection($data) {
		$date = ($data['val'] == 1)?date('Y-m-d'):NULL;
		$isApprove =  ($data['val'] == 1)?$this->loginId:0;
		$approvalRemarks =  (!empty($data['approval_remarks']))?$data['approval_remarks']:"";
        $this->store($this->grnItemTable, ['id'=> $data['id'], 'is_approve' => $isApprove,'approval_remarks'=> $approvalRemarks, 'approve_date'=>$date]);
        return ['status' => 1, 'message' => 'Inspection ' . $data['msg'] . ' successfully.'];
    }

	public function getGrnOrders($id){
        $queryData['tableName'] = $this->purchaseOrderMaster;
        $queryData['select'] = "purchase_order_master.*";
        $queryData['join']['purchase_order_trans'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $queryData['where']['purchase_order_trans.order_status'] = 0;
        $queryData['where']['purchase_order_master.is_approve != '] = 0;
        $queryData['where']['purchase_order_master.party_id'] = $id;
        $queryData['group_by'][] = 'purchase_order_trans.order_id';
        $resultData = $this->rows($queryData); 
        
        $html="";$dataRow=array();
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->getGrnTransactions($row->id);  //print_r($partData);//exit;
                if(!empty($partData)):
                    foreach($partData as $part):
                        $partCode[] = $part->item_name; 
                        $qty[] = $part->qty; 
                    endforeach;
                
                    $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                    $dataRow[] = [
    					'id'=>$row->id,
    					'po_no'=>getPrefixNumber($row->po_prefix,$row->po_no),
    					'po_date'=>$row->po_date,
    					'part_code'=>str_replace("<br>","",$part_code),
    					'part_qty'=>str_replace("<br>","",$part_qty),
    				];
                    $html .= '<tr>
                                <td class="text-center">
                                    <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                                </td>
                                <td class="text-center">'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                                <td class="text-center">'.formatDate($row->po_date).'</td>
                                <td class="text-center">'.$part_code.'</td>
                                <td class="text-center">'.$part_qty.'</td>
                            </tr>';
                    $i++;
                endif;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData,'poList'=>$dataRow];
    }

	public function getGrnTransactions($id){
        $queryData['tableName'] = $this->purchaseOrderTrans;
		$queryData['select'] = "purchase_order_trans.*,purchase_order_master.po_no,purchase_order_master.po_prefix,purchase_order_master.po_date,purchase_order_master.party_id,purchase_order_master.net_amount,party_master.party_name,item_master.item_name";
        $queryData['join']['purchase_order_master'] = "purchase_order_master.id = purchase_order_trans.order_id";
        $queryData['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        $queryData['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $queryData['where']['purchase_order_trans.order_id'] = $id;
        $queryData['where']['purchase_order_master.order_status'] = 0;
        return $this->rows($queryData);
    }

	public function getGrnList($grn_id)
	{     
        $queryData['tableName'] = $this->grnItemTable;
        $queryData['select'] = "item_master.item_name,grn_transaction.qty,unit_master.unit_name,grn_transaction.price";
        $queryData['leftJoin']['grn_master'] = "grn_transaction.grn_id = grn_master.id";
        $queryData['leftJoin']['item_master'] = "grn_transaction.item_id = item_master.id";
        $queryData['leftJoin']['unit_master'] = "grn_transaction.unit_id = unit_master.id";
        $queryData['where']['grn_transaction.grn_id'] = $grn_id;
        $resultData = $this->rows($queryData);
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_name.'</td>                            
                            <td class="text-center">'.$row->qty.'</td>
                            <td class="text-center">'.$row->unit_name.'</td>
                            <td class="text-center">'.$row->price.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	//Created By Karmi @15/04/2022
	public function getPartyOrders($id){
        $queryData['tableName'] = $this->grnTable;
        $queryData['select'] = "id,grn_prefix,grn_no,grn_date,challan_no,party_id";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
		//print_r($resultData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->getGRNTransactionsForDC($row->id); //print_r($partData);exit;
                foreach($partData as $part):
					if($part->qty > $part->dc_qty){
						$partCode[] = $part->item_name; 
						$qty[] = $part->qty - $part->dc_qty; 
						$dc_qty[] = $part->dc_qty;
				}
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
				if(!empty($part_qty)):
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                            <td class="text-center">'.formatDate($row->grn_date).'</td>
                            <td class="text-center">'.$row->challan_no.'</td>
                            <td class="text-center">'.$part_code.'</td>
                            <td class="text-center">'.$part_qty.'</td>
                          </tr>';
                $i++;
				endif;
            endforeach;
        // else:
        //     $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	//Created By Karmi @15/04/2022
	public function getGRNTransactionsForDC($id){  
        $data['tableName'] = $this->grnItemTable;    
        $data['select'] = 'grn_transaction.*,item_master.part_no as partNo,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
        $data['where']['grn_id'] = $id;
        return $this->rows($data);
    }
	//Created By Karmi @15/04/2022
	public function getGRNByRefid($transIds){
        $data['tableName'] = $this->grnTable;        
        $data['select'] = "grn_master.id,grn_master.grn_prefix,grn_master.grn_no,grn_transaction.grn_id,grn_transaction.id as transID,";
        $data['leftJoin']['grn_transaction'] = "grn_master.id = grn_transaction.grn_id";
		//$data['leftJoin']['trans_main as tm'] = 'tm.id = trans_main.ref_id';
        $data['where_in']['grn_transaction.grn_id'] = $transIds;
        $data['group_by'][] = 'grn_transaction.grn_id';
        return $this->rows($data);
    }

	//Created By Karmi @15/04/2022
	public function getOrderItems($transIds){
        $data['tableName'] = $this->grnItemTable; 
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code";
		$data['join']['item_master'] = "item_master.id = grn_transaction.item_id";       
        $data['where_in']['grn_id'] = $transIds;
        return $this->rows($data);
    }

	//Create By : Avruti @15-04-2022
	public function getSoListForSelect($id){
		$data['tableName'] = $this->trans_main;
        $data['select'] = "id,trans_prefix,trans_no,trans_date,doc_no";
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        $data['where']['is_approve != '] = 0;
       	$data['where']['party_id'] = $id;
		return $this->rows($data);
	}
	
	//Test Report Create By : Megghavi @10-08-2022
	public function saveTestReport($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->grnItemTable,$data,'Grn');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

	//Test Report Create By : Megghavi @10-08-2022
	public function getTestReport($grn_id){
        $data['tableName'] = $this->grnItemTable;
        $data['where']['id'] = $grn_id;
        return $this->row($data);
    }
    
    //Created By Avruti @14/08/2022
	public function getInInspectionDeviation($id){
		$data['tableName'] = $this->ic_inspection;
		$data['select'] = "ic_inspection.*, party_master.party_name, item_master.item_code, item_master.item_name, item_master.material_grade, grn_transaction.batch_no, grn_transaction.color_code, grn_transaction.qty, grn_transaction.is_approve, grn_transaction.approve_date, grn_master.grn_date, grn_transaction.fgitem_id,grn_transaction.approval_remarks";
		$data['leftJoin']['party_master'] = "party_master.id = ic_inspection.party_id";
        $data['leftJoin']['grn_transaction'] = "grn_transaction.id = ic_inspection.grn_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['where']['ic_inspection.grn_trans_id'] = $id;         
        $data['where']['ic_inspection.trans_type'] = 2;    
		return $this->row($data);
	}
	
	public function getInInspectionData($item_id){
	    $data['tableName'] = $this->gradeMaster;
	    $data['select'] = 'grade_master.*';
	    $data['join']['item_master'] = 'item_master.material_grade = grade_master.material_grade';
        $data['where']['item_master.item_id'] = $item_id;
        return $this->row($data);
	}

	public function getGrnTrans($id){
        $data['tableName'] = $this->grnItemTable; 
        $data['select'] = "grn_transaction.*,grn_master.grn_date,item_master.material_grade,item_master.material_grade_id,item_master.size,item_category.category_name";
		$data['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";   
		$data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";    
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['grn_transaction.id'] = $id;
        return $this->row($data);
    }
	
    public function getTcInspectionParam($data){
        $data['tableName'] = $this->tcInspect;
        $data['where']['grade_id'] = $data['grade_id'];
        if(!empty($data['batch_no'])){ $data['where']['batch_no'] = $data['batch_no']; }
        if(!empty($data['tc_no'])){ 
            $data['where']['ref_tc_no'] = $data['tc_no'];
        }
		$data['limit'] = 15;
        $data['order_by']['id'] = 'ASC';
        return $this->rows($data);
    }

    public function getMaterialSpecification($grade_id){
        $data['tableName'] = $this->materialSpeci;
        $data['select'] = 'grade_id,spec_type,param_name,sub_param,min_value,max_value';
        $data['where']['grade_id'] = $grade_id;
        return $this->rows($data);
    }

	public function checkDuplicateTC($ref_tc_no){
		$data['tableName'] = $this->tcInspect;
        $data['where']['ref_tc_no'] = $ref_tc_no;
        return $this->numRows($data);
	}
	
    public function nextTcNo(){
        $data['tableName'] = $this->tcInspect;
        $data['select'] = "MAX(tc_no) as tcno";
        $data['where']['is_delete'] = 0;
		$tc_no = $this->specificRow($data)->tcno;
		$nexttcno = (!empty($tc_no))?($tc_no + 1):1;
		return $nexttcno;
    }

	public function getTcNumber($grade_id,$batch_no){
        $data['tableName'] = $this->tcInspect;
        $data['select'] = "ref_tc_no,batch_no,grn_trans_id";
		$data['where']['grade_id'] = $grade_id;
		$data['where']['batch_no'] = $batch_no;
		$data['group_by'][] = 'ref_tc_no';
		$data['group_by'][] = 'batch_no';
		return $this->rows($data);
	}
	
	public function getTcNo($ref_tc_no){
		$data['tableName'] = $this->tcInspect;
        $data['where']['ref_tc_no'] = $ref_tc_no;
		return $this->row($data);
	}

	public function saveTcInspectionParam($data){  
        try{  
            $this->db->trans_begin();
			if($data['type'] == 1):
				$oldtc = $this->getTcNo($data['ref_tc_no']);
				if(!empty($oldtc->tc_no)):
					$data['tc_no'] = $oldtc->tc_no;
				else:
					$data['tc_no'] = $this->nextTcNo(); // Generate New System TC Number
					
					foreach($data['id'] as $key=>$value):
						$specification = [
							'id' => $value,
							'tc_type' => (!empty($data['tc_type'])?$data['tc_type']:0),
							//'grn_id' => $data['grn_id'],
							//'grn_trans_id' => $data['grn_trans_id'],
							'batch_no' => $data['batch_no'],
							'ref_tc_no' => $data['ref_tc_no'],
							'tc_prefix' => 'STC',
							'tc_no' => $data['tc_no'],
							'grade_id' => $data['grade_id'][$key],
							'spec_type' => $data['spec_type'][$key],
							'param_name' => $data['param_name'][$key],
							'sub_param' => $data['sub_param'][$key],
							'min_value' => $data['min_value'][$key],
							'max_value' => $data['max_value'][$key],
							'result' => $data['result'][$key],
							'created_by' => $data['created_by']
						];
						$this->store($this->tcInspect,$specification,'Material Specification');
					endforeach;
				endif;
				$this->edit($this->grnItemTable,['id'=>$data['grn_trans_id']],['sys_tc_no'=>'STC'.$data['tc_no'],'tc_no'=>$data['ref_tc_no']]);

			elseif($data['type'] == 2):
				foreach($data['id'] as $key=>$value):
					if(!empty($value)):
						$specification = [
							'id' => $value,
							'result' => $data['result'][$key]
						];
						$this->store($this->tcInspect,$specification,'Material Specification');
					endif;
				endforeach;
			endif;
            $result = ['status'=>1,'message'=>'TC Parameter saved successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function getFgOpeningDTRows($data){
        $data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,fg.item_name as fg_item_name";
        $data['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['item_master as fg'] = "fg.id = grn_transaction.fgitem_id";
		// $data['where']['grn_transaction.created_at >= '] = $this->startYearDate;
		// $data['where']['grn_transaction.created_at <= '] = $this->endYearDate;
        $data['order_by']['grn_transaction.created_at'] = "DESC";
		$data['where']['grn_transaction.item_type'] = $data['item_type'];
		$data['where']['grn_transaction.grn_type'] =1;
       
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "grn_transaction.material_grade";
        $data['searchCol'][] = "grn_transaction.fg_batch_no";
        $data['searchCol'][] = "grn_transaction.batch_no";
        $data['searchCol'][] = "grn_transaction.tc_no";
		$data['searchCol'][] = "grn_transaction.qty";

		$columns =array('','',"item_master.item_name","grn_transaction.material_grade","grn_transaction.fg_batch_no","grn_transaction.batch_no","grn_transaction.tc_no","grn_transaction.qty");
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

        return $this->pagingRows($data);
	}

	
	public function getFgOpeningDTRowsOld($data){
        $data['tableName'] = $this->fg_opening_stock;
        $data['select'] = "fg_opening_stock.*,item_master.item_name";
        $data['leftJoin']['item_master'] = "item_master.id = fg_opening_stock.item_id";
		$data['where']['fg_opening_stock.created_at >= '] = $this->startYearDate;
		$data['where']['fg_opening_stock.created_at <= '] = $this->endYearDate;
        $data['order_by']['fg_opening_stock.created_at'] = "DESC";
		$data['where']['item_master.item_type'] = $data['item_type'];
       
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "fg_opening_stock.material_grade";
        $data['searchCol'][] = "fg_opening_stock.batch_no";
        $data['searchCol'][] = "fg_opening_stock.heat_no";
        $data['searchCol'][] = "fg_opening_stock.tc_no";
		$data['searchCol'][] = "fg_opening_stock.qty";

		$columns =array('','',"item_master.item_name","fg_opening_stock.material_grade","fg_opening_stock.batch_no","fg_opening_stock.heat_no","fg_opening_stock.tc_no","fg_opening_stock.qty");
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

        return $this->pagingRows($data);
	}
	
	public function getFgOpeningData($postData){
        $data['tableName'] = $this->fg_opening_stock;
        $data['select'] = "fg_opening_stock.*,item_master.item_name";
        $data['join']['item_master'] = "item_master.id = fg_opening_stock.item_id";
		$data['where']['fg_opening_stock.id'] = $postData['id'];
        return $this->row($data);
	}

	public function saveFgStock($data){  
        try{  
			
            $this->db->trans_begin();
			$stockData = [
				'id' => "",
				'grn_type' => 1,
				'item_id' => $data['item_id'],
				'item_type' => $data['item_type'],
				'fgitem_id' =>(!empty( $data['fg_item_id'])? $data['fg_item_id']:''),
				'material_grade' => $data['material_grade'],
				'fg_batch_no' => (!empty($data['batch_no'])?$data['batch_no']:''),
				'batch_no' => $data['heat_no'],
				'tc_no' => $data['ref_tc_no'],
				'qty' => $data['qty'],
				'item_remark' => $data['remark'],
				'created_by' => $data['created_by'],
				'created_at' => date("Y-m-d H:i:s"),
			];
			
			$result = $this->store($this->grnItemTable,$stockData);
			$data['grn_trans_id'] = $result['insert_id'];
			$data['tc_type'] = 1;
			$data['batch_no'] =  $data['heat_no'];
			$tcResult = $this->saveTcInspectionParam($data);

			$fgData = $this->getGrnTrans($data['grn_trans_id']);
			$stockEffect = [
				'id'=>'',
                'location_id' => $this->RTD_STORE->id,
				'ref_type'=>-1,
				'trans_type'=>1,
				'qty'=>$data['qty'],
				'item_id'=>(!empty($data['fg_item_id'])?$data['fg_item_id']:$data['item_id']),
				'ref_id'=>$data['grn_trans_id'],
				'batch_no'=>(!empty($data['fg_item_id'])?$data['batch_no']:$data['heat_no']),				
				'ref_batch'=>$data['material_grade'],				
				'ref_date'=>date("Y-m-d"),				
				'tc_no'=>$data['heat_no'].'~'.$fgData->sys_tc_no,
				'created_by' => $data['created_by'],

			];
			$this->store($this->stockTrans,$stockEffect);
            $result = ['status'=>1,'message'=>'TC Parameter saved successfully.'];

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	public function deleteFgStock($id){  
        try{  
			
            $this->db->trans_begin();
			$fgData = $this->getGrnTrans($id);
			$queryData['tableName'] = "stock_transaction";
			$queryData['select'] = "stock_transaction.*";
			$queryData['where']['stock_transaction.trans_type'] = 2;
			if($fgData->item_type == 1){
				$queryData['where']['stock_transaction.batch_no'] = $fgData->fg_batch_no;
				$queryData['where']['stock_transaction.item_id'] = $fgData->fgitem_id;

			}else{
				$queryData['where']['stock_transaction.batch_no'] = $fgData->batch_no;
				$queryData['where']['stock_transaction.item_id'] = $fgData->item_id;

			}
			$queryData['where']['stock_transaction.tc_no'] = $fgData->batch_no.'~'.$fgData->sys_tc_no;
			$stockData = $this->rows($queryData);
			
			if(!empty($stockData)){
				return ['status' => 0, 'message' => "You can't delete this Stock"];
			}
			$tcNo = explode("STC",$fgData->sys_tc_no);
			$this->trash($this->tcInspect,['tc_no'=>$tcNo[1],'ref_tc_no'=>$fgData->tc_no,'tc_type'=>1]);
			if($fgData->item_type == 1){
				$this->remove($this->stockTrans,['ref_id'=>$id,'ref_type'=>-1,'item_id'=>$fgData->fgitem_id]);
			}else{
				$this->remove($this->stockTrans,['ref_id'=>$id,'ref_type'=>-1,'item_id'=>$fgData->item_id]);
			}
			$result = $this->trash($this->grnItemTable,['id'=>$id]);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

	/*  Create By : Avruti @27-11-2021 3:00 PM
		update by : 
		note : 
	*/
    //---------------- API Code Start ------//
    public function getGrnListing($data){
		$queryData['tableName'] = $this->grnItemTable;
        $queryData['select'] = "grn_transaction.*,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,grn_master.remark,grn_master.type,party_master.party_name,item_master.item_name,purchase_order_master.po_no,purchase_order_master.po_prefix,unit_master.unit_name,location_master.store_name";
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = grn_transaction.location_id";
		$queryData['where']['grn_master.grn_date >= '] = $this->startYearDate;
		$queryData['where']['grn_master.grn_date <= '] = $this->endYearDate;
        $queryData['order_by']['grn_master.grn_date'] = "DESC";
        $queryData['order_by']['grn_master.id'] = "DESC";
		
		if(!empty($data['search'])):
			$queryData['like']['grn_master.grn_no'] = $data['search'];
			$queryData['like']['DATE_FORMAT(grn_master.grn_date,"%d-%m-%Y")'] = $data['search'];
			$queryData['like']['purchase_order_master.po_prefix'] = $data['search'];
			$queryData['like']['party_master.party_name'] = $data['search'];
			$queryData['like']['item_master.item_name'] = $data['search'];
			$queryData['like']['grn_transaction.qty'] = $data['search'];
			$queryData['like']['unit_master.unit_name'] = $data['search'];
			$queryData['like']['grn_transaction.batch_no'] = $data['search'];
			$queryData['like']['grn_transaction.color_code'] = $data['search'];
		endif;
		$queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        return $this->rows($queryData);
	}

	public function getInwardQCListing($data){
		$queryData['tableName'] = $this->grnItemTable;
        $queryData['select'] = "grn_transaction.*,item_master.item_name,grn_master.grn_no,grn_master.grn_prefix,grn_master.grn_date,grn_master.challan_no,purchase_order_master.po_no,purchase_order_master.po_prefix, party_master.party_name, purchase_inspection.inspection_status";
		$queryData['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
		$queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
		$queryData['leftJoin']['purchase_inspection'] = "purchase_inspection.ptrans_id = grn_transaction.id";
        $queryData['leftJoin']['purchase_order_master'] = "purchase_order_master.id = grn_master.order_id";
		
		$queryData['where']['grn_master.type'] = 1;
		$queryData['where']['grn_transaction.item_type'] = 3;
        $queryData['order_by']['grn_master.grn_date'] = "DESC";
		if($data['status'] == 1) { 
			$queryData['where']['grn_transaction.inspected_qty != '] = 0; 
			$queryData['where']['grn_master.grn_date >= '] = $this->startYearDate;
			$queryData['where']['grn_master.grn_date <= '] = $this->endYearDate;
		} 
		if($data['status'] == 0) {  $queryData['where']['grn_transaction.inspected_qty'] = 0; }
		if(!empty($data['search'])):
			$queryData['like']['grn_master.grn_no'] = $data['search'];
			$queryData['like']['DATE_FORMAT(grn_master.grn_date,"%d-%m-%Y")'] = $data['search'];
			$queryData['like']['purchase_order_master.po_no'] = $data['search'];
			$queryData['like']['party_master.party_name'] = $data['search'];
			$queryData['like']['grn_master.challan_no'] = $data['search'];
			$queryData['like']['item_master.item_name'] = $data['search'];
			$queryData['like']['grn_transaction.qty'] = $data['search'];
			$queryData['like']['grn_transaction.batch_no'] = $data['search'];
			$queryData['like']['grn_transaction.color_code'] = $data['search'];
		endif;
		$queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        return $this->rows($queryData);
	}

	public function getmaterialInspectionData($id){
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where']['grn_transaction.id'] = $id;       
        $data['tableName'] = $this->grnItemTable;
		$result = $this->rows($data);
		
		$dataRow = array();
		foreach($result as $row):
			$data = array();
			$data['tableName'] = 'purchase_inspection';
			$data['where']['ptrans_id'] = $row->id;
			$data['where']['grn_id'] = $row->grn_id;
			$data['where']['is_delete'] = 0;
			$inspectedData = $this->row($data);

			$row->inspected_id = (!empty($inspectedData->id))?$inspectedData->id:"";
			$row->inspected_qty = (!empty($inspectedData->inspected_qty))?$inspectedData->inspected_qty:$row->qty;
			$row->ud_qty = (!empty($inspectedData->ud_qty))?$inspectedData->ud_qty:0;
			$row->reject_qty = (!empty($inspectedData->reject_qty))?$inspectedData->reject_qty:0;
			$row->scrape_qty = (!empty($inspectedData->scrape_qty))?$inspectedData->scrape_qty:0;
			$row->short_qty = (!empty($inspectedData->short_qty))?$inspectedData->short_qty:0;
			$row->inspection_status = (!empty($inspectedData->inspection_status))?$inspectedData->inspection_status:"OK";
			
			$dataRow[] = $row;
		endforeach;

		return $dataRow;
	}
    //------ API Code End -------//
}
?>