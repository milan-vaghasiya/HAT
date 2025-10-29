<?php 
class GstExpenseModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
	private $grnMain = "grn_master";
    private $grnTrans = "grn_transaction";
	
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount, trans_main.doc_no';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_child.entry_type'] = 18;
        $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
		$data['group_by'][]='trans_child.trans_main_id';
		$data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_child.net_amount";
      
		$columns =array('','','trans_main.trans_no','trans_main.trans_date','','','party_master.party_name','trans_child.item_name','trans_main.net_amount');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    } 

	public function getPartyOrders($id){
        $queryData['tableName'] = $this->grnMain;
        $queryData['select'] = "id,grn_prefix,grn_no,challan_no,grn_date";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->getGrnTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_name; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'" ><label for="md_checkbox_'.$i.'" class="mr-3 check'.$row->id.'"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                            <td class="text-center">'.$row->challan_no.'</td>
                            <td class="text-center">'.formatDate($row->grn_date).'</td>
                            <td class="text-center">'.$part_code.'</td>
                            <td class="text-center">'.floatval($part_qty).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

	public function getGrnTransactions($grn_id){
		$queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_transaction.*,item_master.item_name";
        $queryData['join']['item_master'] = "grn_transaction.item_id = item_master.id";
        $queryData['where']['grn_transaction.grn_id'] = $grn_id;
        return $this->rows($queryData);
	}

	public function getGrnItemsForInvoice($grnIds){
		$data['tableName'] = $this->grnTrans;
        $data['select'] = "grn_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.item_type, item_master.gst_per";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        $data['join']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $data['where_in']['grn_transaction.grn_id'] = $grnIds;
        $data['where']['grn_transaction.trans_status'] = 0;
        return $this->rows($data);
	}

	public function checkDuplicateINV($party_id,$inv_no,$entry_type,$id){
        $data['tableName'] = $this->transMain;
        $data['where']['trans_no'] = $inv_no;
        $data['where']['entry_type'] = $entry_type;
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
        $data['where']['party_id'] = $party_id;
        if(!empty($id))
            $data['where']['id != '] = $id;

        return $this->numRows($data);
	}
	
	public function save($masterData,$itemData,$expenseData){
		try{
            $this->db->trans_begin();
			$purchaseId = $masterData['id'];
			
			$checkDuplicate = $this->checkDuplicateINV($masterData['party_id'],$masterData['trans_no'],$masterData['entry_type'],$purchaseId);
			if($checkDuplicate > 0):
				$errorMessage['inv_no'] = "Invoice No. is Duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
				//save purchase master data
				$purchaseInvSave = $this->store($this->transMain,$masterData);
				$purId = (empty($purchaseId))?$purchaseInvSave['insert_id']:$masterData['id'];
                $masterData['id'] = $purId;

				if(!empty($purchaseId)):
					$this->trash($this->transChild,['trans_main_id'=>$purId]);
				endif;
					
				//save purchase items
				foreach($itemData['item_id'] as $key=>$value):
					$transData = [
						'id'=>$itemData['id'][$key],
						'trans_main_id'=>$purId,
						'entry_type' => $masterData['entry_type'],
						'currency' => $masterData['currency'],
						'inrrate' => $masterData['inrrate'],
						'from_entry_type' => $itemData['from_entry_type'][$key],
						'ref_id' => $itemData['ref_id'][$key],
						'item_id'=>$value,
						'item_name' => $itemData['item_name'][$key],
						'item_type' => $itemData['item_type'][$key],
						'item_code' => $itemData['item_code'][$key],
						'item_desc' => $itemData['item_desc'][$key],
						'unit_id' => $itemData['unit_id'][$key],
						'unit_name' => $itemData['unit_name'][$key],
						'location_id' => $itemData['location_id'][$key],
						'batch_no' => $itemData['batch_no'][$key],
						'hsn_code' => $itemData['hsn_code'][$key],
						'qty' => $itemData['qty'][$key],
                        'p_or_m' => -1,
						'price' => $itemData['price'][$key],
						'amount' => $itemData['amount'][$key] + $itemData['disc_amount'][$key],
						'taxable_amount' => $itemData['taxable_amount'][$key],
						'gst_per' => $itemData['gst_per'][$key],
						'cess_per' => $itemData['cess_per'][$key],
						'gst_amount' => $itemData['igst_amount'][$key],
						'igst_per' => $itemData['igst_per'][$key],
						'igst_amount' => $itemData['igst_amount'][$key],
						'cgst_per' => $itemData['cgst_per'][$key],
						'cgst_amount' => $itemData['cgst_amount'][$key],
						'sgst_per' => $itemData['sgst_per'][$key],    
						'sgst_amount' => $itemData['sgst_amount'][$key],
						'disc_per' => $itemData['disc_per'][$key],
						'disc_amount' => $itemData['disc_amount'][$key],
						'item_remark' => $itemData['item_remark'][$key],
						'net_amount' => $itemData['net_amount'][$key],
						'created_by' => $masterData['created_by'],
						'is_delete' => 0
					];
					$this->store($this->transChild,$transData);
                endforeach;

                $this->transModel->ledgerEffects($masterData,$expenseData);
				$result = ['status'=>1,'message'=>'Gst Expense saved successfully.','url'=>base_url("gstExpense")];	
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

	public function getGstExpense($id){    
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
		$invoiceData->itemData = $this->gstExpenseTransaction($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }
	
	public function gstExpenseTransaction($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        return $this->rows($queryData);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();
			$invoiceData = $this->getGstExpense($id);

			$this->trash($this->transChild,['trans_main_id'=>$id]);
			$result = $this->trash($this->transMain,['id'=>$id],'GST Expense');

            $this->transModel->deleteLedgerTrans($id);
            $this->transModel->deleteExpenseTrans($id);

			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function getItemList($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
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
                            <td class="text-center">'.$row->igst_per.'</td>
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

	public function getPurchaseInvoiceList($party_id){
		$data['tableName'] = $this->transMain;
		$data['where']['party_id'] = $party_id;
        $data['where']['entry_type'] = 12;
		return $this->rows($data);
	}
	
    public function checkGrnPendingStatus($grn_id){
        $data['select'] = "COUNT(trans_status) as orderStatus";
        $data['where']['grn_id'] = $grn_id;
        $data['where']['trans_status'] = 0;
        $data['tableName'] = $this->grnTrans;
        return $this->specificRow($data)->orderStatus;
    }

	/*  Create By : Avruti @29-11-2021 4:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount($type=0){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_child.entry_type'] = 12;
        return $this->numRows($data);
    }

    public function getPurchaseInvoiceList_api($limit, $start,$type=0){
		$data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 12;
		$data['group_by'][]='trans_child.trans_main_id';
		$data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>