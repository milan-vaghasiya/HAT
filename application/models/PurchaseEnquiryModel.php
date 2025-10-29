<?php
class PurchaseEnquiryModel extends MasterModel{
    private $purchaseEnquiryMaster = "purchase_enquiry";
    private $purchaseEnquiryTrans = "purchase_enquiry_transaction";

    public function nextEnqNo(){
        $data['tableName'] = $this->purchaseEnquiryMaster;
        $data['select'] = "MAX(enq_no) as enq_no";
		$data['where']['enq_date >= '] = $this->startYearDate;
		$data['where']['enq_date <= '] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->enq_no;
		$nextEnqNo = (!empty($maxNo))?($maxNo + 1):1;
		return $nextEnqNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->purchaseEnquiryTrans;
		$data['select'] = "purchase_enquiry_transaction.*,purchase_enquiry.enq_no,purchase_enquiry.enq_prefix,purchase_enquiry.enq_date,purchase_enquiry.supplier_id,purchase_enquiry.supplier_name,purchase_enquiry.supplier_name,purchase_enquiry.enq_ref_date,purchase_enquiry.enq_status";
        $data['join']['purchase_enquiry'] = "purchase_enquiry.id = purchase_enquiry_transaction.ref_id";
		$data['where']['purchase_enquiry_transaction.confirm_status'] = $data['status'];
		$data['where']['purchase_enquiry.enq_date >= '] = $this->startYearDate;
		$data['where']['purchase_enquiry.enq_date <= '] = $this->endYearDate;
		$data['group_by'][]='purchase_enquiry_transaction.ref_id';
		
		$data['searchCol'][] = "CONCAT(purchase_enquiry.enq_prefix,purchase_enquiry.enq_no)";
		$data['searchCol'][] = "DATE_FORMAT(purchase_enquiry.enq_date, '%d-%m-%Y')";
        $data['searchCol'][] = "purchase_enquiry.supplier_name";
        $data['searchCol'][] = "purchase_enquiry_transaction.item_name";
        $data['searchCol'][] = "purchase_enquiry_transaction.confirm_qty";
        $data['searchCol'][] = "purchase_enquiry_transaction.confirm_rate";
		$data['searchCol'][] = "DATE_FORMAT(purchase_enquiry.enq_date, '%d-%m-%Y')";
        $data['searchCol'][] = "remark";    
		
		$columns =array('','','purchase_enquiry.enq_no','purchase_enquiry.enq_date','purchase_enquiry.supplier_name','purchase_enquiry_transaction.item_name','purchase_enquiry_transaction.confirm_qty','purchase_enquiry_transaction.confirm_rate','','','purchase_enquiry.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
		return $this->pagingRows($data);
    }
    
    /* public function getDTRows($data){
        $data['tableName'] = $this->purchaseEnquiryTrans;        
        $data['searchCol'][] = "DATE_FORMAT(enq_date, '%d-%m-%Y')";
        $data['searchCol'][] = "supplier_name";
        $data['searchCol'][] = "remark";    
        $data['searchCol'][] = "DATE_FORMAT(enq_ref_date, '%d-%m-%Y')";    
        $data['searchCol'][] = "CONCAT(enq_prefix,enq_no)";
		$columns =array('','','enq_no','enq_date','supplier_name','enq_ref_date','status','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    } */
    
    public function getEnquiry($id){
        $data['tableName'] = $this->purchaseEnquiryMaster;
        $data['where']['id'] = $id;
		$result = $this->row($data);		
        
		$result->itemData = $this->getEnquiryTrans($id);
		return $result;
	}  
	
	public function getEnquiryTrans($id){
        $data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description,item_master.item_code,item_master.hsn_code,item_master.gst_per";
        $data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
        $data['leftJoin']['item_master'] = "item_master.id = purchase_enquiry_transaction.item_id";
        $data['where']['purchase_enquiry_transaction.ref_id'] = $id;
        $data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->rows($data);
		return $result;
	}

	public function getEnquiryTransPenddingConfirm($id){		
		$data['where']['ref_id'] = $id;
		$data['where']['confirm_status'] = 0;
        $data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->numRows($data);
		return $result;
	}

	public function getEnquiryTransConfirm($id){		
		$data['where']['ref_id'] = $id;
		$data['where']['confirm_status'] = 1;
        $data['tableName'] = $this->purchaseEnquiryTrans;
		$result = $this->numRows($data);
		return $result;
	}

    public function save($masterData,$itemData){
		try{
            $this->db->trans_begin();
			$orderId = $masterData['id'];
			$req_id = $masterData['req_id']; unset($masterData['req_id']);
			
			if($this->checkDuplicateEnquiry($masterData['supplier_name'],$masterData['enq_no'],$orderId) > 0):
				$errorMessage['enq_no'] = "Enquiry No. is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
				if(empty($orderId)):
					//Get Latest Trans Number
					$masterData['enq_prefix'] = "ENQ/".$this->shortYear."/";
					$masterData['enq_no'] = $this->purchaseEnquiry->nextEnqNo();
                    
					//save purchase enquiry data
					$enquirySave = $this->store($this->purchaseEnquiryMaster,$masterData);
					$ordId = $enquirySave['insert_id'];
					
					//save purchase items
					foreach($itemData['item_name'] as $key=>$value):
						$transData = [
										'id' => $itemData['id'][$key],
										'ref_id' => $ordId,
										'item_name' => $value,
										'item_type' => $itemData['item_type'][$key],
										'fgitem_id' => $itemData['fgitem_id'][$key],
										'fgitem_name' => $itemData['fgitem_name'][$key],
										'unit_id' => $itemData['unit_id'][$key],
										'qty' => $itemData['qty'][$key],
										'item_remark' => $itemData['item_remark'][$key],
										'created_by' => $itemData['created_by']
									];
						$this->store($this->purchaseEnquiryTrans,$transData);					
					endforeach;
					
					if(!empty($req_id)){ $this->store("purchase_request",["id"=>$req_id,"ref_id"=>1,"order_status"=>1]); }
					$result = ['status'=>1,'message'=>'Purchase Enquiry saved successfully.','url'=>base_url("purchaseEnquiry")];				
				else:
					$this->store($this->purchaseEnquiryMaster,$masterData);
					
					$data['select'] = "id";
					$data['where']['ref_id'] = $orderId;
					$data['tableName'] = $this->purchaseEnquiryTrans;
					$ptransIdArray = $this->rows($data);
					
					foreach($itemData['item_name'] as $key=>$value):
						$transData = [
										'id' => $itemData['id'][$key],
										'ref_id' => $orderId,
										'item_name' => $value,
										'item_type' => $itemData['item_type'][$key],
										'fgitem_id' => $itemData['fgitem_id'][$key],
										'fgitem_name' => $itemData['fgitem_name'][$key],
										'unit_id' => $itemData['unit_id'][$key],
										'qty' => $itemData['qty'][$key],
										'item_remark' => $itemData['item_remark'][$key],									
										'created_by' => $itemData['created_by']
									];					
						$this->store($this->purchaseEnquiryTrans,$transData);
					endforeach;
					
					foreach($ptransIdArray as $key=>$value):
						if(!in_array($value->id,$itemData['id'])):		
							$this->trash($this->purchaseEnquiryTrans,['id'=>$value->id]);
						endif;
					endforeach;
					
					$result = ['status'=>1,'message'=>'Purchase Enquiry updated successfully.','url'=>base_url("purchaseEnquiry")];
				endif;
					/* Send Notification */
					$peNo = getPrefixNumber($masterData['enq_prefix'],$masterData['enq_no']);
					$notifyData['notificationTitle'] = (empty($masterData['id']))?"New Purchase Enquiry":"Update Purchase Enquiry";
					$notifyData['notificationMsg'] = (empty($masterData['id']))?"New Purchase Enquiry Generated. Enq. No. : ".$peNo:"Purchase Enquiry updated. Enq No. : ".$peNo;
					$notifyData['payload'] = ['callBack' => base_url('purchaseEnquiry')];
					$notifyData['controller'] = "'purchaseEnquiry'";
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

    public function checkDuplicateEnquiry($supplier_name,$enq_no,$id = ""){
        $data['tableName'] = $this->purchaseEnquiryMaster;
        $data['where']['supplier_name'] = $supplier_name;
        $data['where']['enq_no'] = $enq_no;	
		$data['where']['enq_date >= '] = $this->startYearDate;
		$data['where']['enq_date <= '] = $this->endYearDate;
			
		if(!empty($id))
            $data['where']['id != '] = $id;
		return $this->numRows($data);
    }
        
    public function deleteEnquiry($id)	{
		try{
            $this->db->trans_begin();
		$enqData = $this->getEnquiry($id);

        //enquiry transation delete
		$where['ref_id'] = $id;
		$this->trash($this->purchaseEnquiryTrans,$where);
        
        //enquiry master delete
		$result = $this->trash($this->purchaseEnquiryMaster,['id'=>$id],'Purchase Enquiry');

			/* Send Notification */
			$peNo = getPrefixNumber($enqData->enq_prefix,$enqData->enq_no);
			$notifyData['notificationTitle'] = "Delete Purchase Enquiry";
			$notifyData['notificationMsg'] = "Purchase Enquiry deleted. Enq No. : ".$peNo;
			$notifyData['payload'] = ['callBack' => base_url('purchaseEnquiry')];
			$notifyData['controller'] = "'purchaseEnquiry'";
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
    
    public function getEnquiryData($enq_id){
        $data = array();
        $data['tableName'] = $this->purchaseEnquiryTrans;
        $data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description";
        $data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
        $data['where']['purchase_enquiry_transaction.ref_id'] = $enq_id;
        $result = $this->rows($data);		

		if(!empty($result)):
			$i=1; $html="";
			foreach($result as $row):
				if(empty($row->confirm_status)):
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
								<label for="md_checkbox'.$i.'">'.$i.'</label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="rate[]" id="rate'.$i.'" class="form-control floatOnly" value="0" min="0" '.$disabled.' />
								<div class="error rate'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="gst_per[]" id="gst_per'.$i.'" class="form-control floatOnly" value="0" min="0" '.$disabled.' />
							</td>
						</tr>';
				else:
                    $data = array();
                    $data['tableName'] = 'item_master';
                    $data['where']['id'] = $row->item_id;
					$itemData = $this->row($data);
					
					$checked = "checked";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' '.$disabled.' />
								<label for="md_checkbox'.$i.'">'.$i.'</label>
							</td>
							<td>
								'.$itemData->item_name.'
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$itemData->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
							</td>
							<td>
								<input type="number" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" value="'.$row->confirm_qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="rate[]" id="rate'.$i.'" class="form-control floatOnly" value="'.$row->confirm_rate.'" min="0" '.$disabled.' />
								<div class="error rate'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="gst_per[]" id="gst_per'.$i.'" class="form-control floatOnly" value="'.$itemData->gst_per.'" min="0" '.$disabled.' />
							</td>
						</tr>';
				endif;$i++;
			endforeach;
		else:
			$html = '<tr><td colspan="5" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	}

	public function enquiryConfirmed($enqConData){
        $data = array();
        $data['tableName'] = $this->purchaseEnquiryMaster;
        $data['where']['id'] = $enqConData['enq_id'];
		$enquiryData = $this->row($data);

        $data = array();
        $data['tableName'] = $this->purchaseEnquiryTrans;
        $data['select'] = "purchase_enquiry_transaction.*,unit_master.unit_name,unit_master.description";
        $data['join']['unit_master'] = "unit_master.id = purchase_enquiry_transaction.unit_id";
        $data['where']['purchase_enquiry_transaction.ref_id'] = $enqConData['enq_id'];
        $data['where_in']['purchase_enquiry_transaction.id'] = $enqConData['trans_id'];
        $enquiryItemData = $this->rows($data);
		
		
		if(empty($enquiryData->supplier_id)):
            $data = array();
            $data['tableName'] = "party_master";
            $data['where']['party_category'] = 3;
            $data['where']['party_name'] = $enquiryData->supplier_name;
			$supplierData = $this->row($data);

			if(empty($supplierData)):
				$supplierSave = $this->store('party_master',['id'=>'','party_category'=>3,'party_name'=>$enquiryData->supplier_name]);
				$supplierId = $supplierSave['insert_id'];
			else:
				$supplierId = $supplierData->id;
			endif;
		else:
			$supplierId = $enquiryData->supplier_id;					
		endif;

		$masterData = [
			'id' => $enqConData['enq_id'],
			'enq_ref_date'=>date("Y-m-d"),
			'supplier_id' => $supplierId
		];
		//save purchase enquiry master data
		$this->store($this->purchaseEnquiryMaster,$masterData);	

		//save purchase enquiry items
		foreach($enquiryItemData as $key=>$row):
			$item_type = (empty($row->item_type))?2:3;
			$itemMasterData = [
				'id' => "",
				'item_name'=>$enqConData['item_name'][$key],
				'price'=>$enqConData['rate'][$key],
				'gst_per'=>$enqConData['gst_per'][$key],
				'unit_id'=>$row->unit_id,
                'item_type' => $item_type
			];
            $data = array();
            $data['tableName'] = "item_master";
            $data['where']['item_type'] = $item_type;
            $data['where']['item_name'] = $enqConData['item_name'][$key];
			$item = $this->row($data);
			if(empty($item)):
				$itemSave = $this->store('item_master',$itemMasterData);
				$itemId = $itemSave['insert_id'];
			else:
				$itemId = $item->id;
                $itemMasterData['id'] = $item->id;
                $itemSave = $this->store('item_master',$itemMasterData);
			endif;

			$transData = [
							'id' => $row->id,
							'item_id' => $itemId,
							'confirm_qty' => $enqConData['qty'][$key],
							'confirm_rate' => $enqConData['rate'][$key],
							'confirm_status'=>1
						];
			$this->store($this->purchaseEnquiryTrans,$transData);		
		endforeach;	

		$confirmedItems = $this->getEnquiryTransConfirm($enqConData['enq_id']);
		if($confirmedItems <=0 ):
			$this->store($this->purchaseEnquiryMaster,['id'=>$enqConData['enq_id'],'enq_status' => 1]);
		endif;

		return ['status'=>1,'message'=>'Purchase Enquiry Confirmed Successfully.'];
	}

	public function closeEnquiry($id){
		$this->store($this->purchaseEnquiryMaster,['id'=>$id,'enq_status'=>1]);
		return ['status'=>1,'message'=>'Purchase Enquiry Closed Successfully.'];
	}

	public function reopenEnquiry($id){
		$this->store($this->purchaseEnquiryMaster,['id'=>$id,'enq_status'=>0]);
		return ['status'=>1,'message'=>'Purchase Enquiry re-open Successfully.'];
	}

    public function itemSearch(){
		$data['tableName'] = 'item_master';
		$data['select'] = 'item_name';
		$data['where']['item_type != '] = 1;
		$result = $this->rows($data);
		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->item_name;}
		return  $searchResult;
    }
    
    public function getPurchaseEnqForPrint($ref_id){
		$result = array();
		$data['tableName'] = 'purchase_enquiry';
		$data['select'] = "purchase_enquiry.enq_prefix,purchase_enquiry.supplier_name,purchase_enquiry.enq_no,purchase_enquiry.enq_date,party_master.party_name, party_master.contact_person, party_master.party_mobile,party_master.contact_email,party_master.party_phone, party_master.party_email, party_master.party_address,party_master.party_pincode";		
		$data['join']['party_master'] = 'party_master.id = purchase_enquiry.supplier_id';
		$data['where']['purchase_enquiry.id'] = $ref_id;
		$result = $this->row($data);		
		$result->itemData = $this->getEnquiryTrans($ref_id);
		return $result;
	}
	
	/*  Create By : Avruti @27-11-2021 1:00 PM
        update by : 
        note : 
    */
    //----------------------------- API Function Start -------------------------------------------//

    public function getCount($status = 0, $type = 0){
        $data['tableName'] = $this->purchaseEnquiryTrans;
        return $this->numRows($data);
    }

    public function getPurchaseEnquiryList_api($limit, $start, $status = 0, $type = 0){
        $data['tableName'] = $this->purchaseEnquiryTrans;
		$data['select'] = "purchase_enquiry_transaction.*,purchase_enquiry.enq_no,purchase_enquiry.enq_prefix,purchase_enquiry.enq_date,purchase_enquiry.supplier_id,purchase_enquiry.supplier_name,purchase_enquiry.supplier_name,purchase_enquiry.enq_ref_date,purchase_enquiry.enq_status";
        $data['join']['purchase_enquiry'] = "purchase_enquiry.id = purchase_enquiry_transaction.ref_id";
		$data['where']['purchase_enquiry_transaction.confirm_status'] = $status;
		$data['group_by'][]='purchase_enquiry_transaction.ref_id';
        
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

//------------------------------ API Function End --------------------------------------------//
}
?>