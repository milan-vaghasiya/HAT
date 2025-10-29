<?php 
class DebitNoteModel extends MasterModel
{
    private $locationMaster = "location_master";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
	
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount, trans_main.doc_no';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_child.entry_type'] = 14;
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
     
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*";
        $queryData['where']['party_id'] = $id;
        $queryData['where']['entry_type'] = 12;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->debitTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_name; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'" ><label for="md_checkbox_'.$i.'" class="mr-3 check'.$row->id.'"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center"></td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
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
	public function getInvoiceItemsForDebitNote($transIds){
		$data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.*,AVG(price) as avg_price";
        $data['where_in']['trans_child.trans_main_id'] = $transIds;
        $data['group_by'][]='trans_child.item_id';
        $result = $this->rows($data);
        
        return $result;
	}

	public function checkDuplicateINV($party_id,$inv_no,$id){
        $data['tableName'] = $this->transMain;
        $data['where']['trans_no'] = $inv_no;
        $data['where']['party_id'] = $party_id;
        $data['where']['entry_type'] = 14;
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
        if(!empty($id))
            $data['where']['id != '] = $id;

        return $this->numRows($data);
	}
	
	public function save($masterData,$itemData,$expenseData){
		try{
            $this->db->trans_begin();
			$purchaseId = $masterData['id'];
			$checkDuplicate = $this->checkDuplicateINV($masterData['party_id'],$masterData['trans_no'],$purchaseId);
			if($checkDuplicate > 0):
				$errorMessage['trans_no'] = "Invoice No. is Duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
			    // Get Latest Trans Number
			    $masterData['trans_no'] = $this->transModel->getTransPrefix(14);
		        $masterData['nextTransNo'] = $this->transModel->nextTransNo(14);
				$masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'], $masterData['trans_no']);
		        
				//save purchase master data
				unset($masterData['nextTransNo']);
				$purchaseInvSave = $this->store($this->transMain,$masterData);
				$purId = (empty($purchaseId))?$purchaseInvSave['insert_id']:$masterData['id'];
                $masterData['id'] = $purId;

                $transDataResult = $this->debitTransactions($purId);
                foreach($transDataResult as $row):
                    if($row->stock_eff == 1):
                        /** Update Item Stock **/
                        $setData = Array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $row->item_id;
                        $setData['set']['qty'] = 'qty, + '.$row->qty;
                        $qryresult = $this->setValue($setData);

                        /** Remove Stock Transaction **/
                        $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>20]);
                    endif;

                    $this->trash($this->transChild,['id'=>$row->id]);                    
                endforeach;
				/* if(!empty($purchaseId)):
					$this->trash($this->transChild,['trans_main_id'=>$purId]);
				endif; */
					
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
                        'p_or_m' => 1,
						'price' => $itemData['price'][$key],
						'amount' => $itemData['amount'][$key] + $itemData['disc_amount'][$key],
						'taxable_amount' => $itemData['taxable_amount'][$key],
						'gst_per' => $itemData['gst_per'][$key],
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
						'stock_eff' => $itemData['stock_eff'][$key],
						'created_by' => $masterData['created_by'],
						'is_delete' => 0
					];
					$transChildSave = $this->store($this->transChild,$transData);
                    $refID = (!empty($itemData['id'][$key]))?$itemData['id'][$key]:$transChildSave['insert_id'];

                    if($itemData['stock_eff'][$key] == 1):
                        /** Update Item Stock **/
                        $setData = Array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $itemData['item_id'][$key];
                        $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
                        $this->setValue($setData);
    
                        /*** UPDATE STOCK TRANSACTION DATA ***/
                        
                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$itemData['location_id'][$key];
                        if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no'] = $itemData['batch_no'][$key];}
                        $stockQueryData['trans_type']=2;
                        $stockQueryData['item_id']=$itemData['item_id'][$key];
                        $stockQueryData['qty'] = "-".$itemData['qty'][$key];
                        $stockQueryData['ref_type']=20;
                        $stockQueryData['ref_id']=$refID;
                        $stockQueryData['ref_no']=$masterData['trans_number'];
                        $stockQueryData['ref_date']=$masterData['trans_date'];
                        $stockQueryData['created_by']=$this->loginID;
                        $this->store($this->stockTrans,$stockQueryData);
                        
                    endif; 
                endforeach;

                $this->transModel->ledgerEffects($masterData,$expenseData);

				$result = ['status'=>1,'message'=>'Debit Note saved successfully.','url'=>base_url("debitNote")];	
			endif;
                /* Send Notification */
                $dnNo = $masterData['doc_no'];
                $notifyData['notificationTitle'] = (empty($purchaseId))?"New Debit Note":"Update Debit Note";
                $notifyData['notificationMsg'] = (empty($purchaseId))?"New Debit Note Generated. DN. No. : ".$dnNo:"Debit Note updated. DN No. : ".$dnNo;
                $notifyData['payload'] = ['callBack' => base_url('debitNote')];
                $notifyData['controller'] = "'debitNote'";
                $notifyData['action'] = (empty($purchaseId))?"W":"M";
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

    public function delete($id){
		try{
            $this->db->trans_begin();
			$invoiceData = $this->getDebitNote($id);
			
            foreach($invoiceData->itemData as $row):
                if($row->stock_eff == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, + '.$row->qty;
                    $qryresult = $this->setValue($setData);

                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>20]);
                endif;

                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;
			$result = $this->trash($this->transMain,['id'=>$id],'Debit Note');

            $this->transModel->deleteLedgerTrans($id);
            $this->transModel->deleteExpenseTrans($id);

                /* Send Notification */
                $dnNo = $invoiceData->doc_no;
                $notifyData['notificationTitle'] = "Delete Debit Note";
                $notifyData['notificationMsg'] = "Debit Note deleted. DN No. : ".$dnNo;
                $notifyData['payload'] = ['callBack' => base_url('debitNote')];
                $notifyData['controller'] = "'debitNote'";
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


	public function getDebitNote($id){    
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
		$invoiceData->itemData = $this->debitTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }
	
	public function debitTransactions($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        return $this->rows($queryData);
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

    public function getItemLocation($item_id){
        $queryData = array();
        $queryData['tableName'] = $this->locationMaster;
        $queryData['select'] = "DISTINCT(store_name)";
        $storeList = $this->rows($queryData);
        
        $locationList = array();
        $i=0;
        foreach($storeList as $store):  
            
            $queryData = array();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['select'] = "SUM(stock_transaction.qty) as stock_qty,stock_transaction.location_id as id,location_master.store_name,location_master.location";
            $queryData['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
            $queryData['where']['stock_transaction.item_id'] = $item_id;
            $queryData['where']['location_master.store_name'] = $store->store_name;
            $queryData['group_by'][] = 'stock_transaction.location_id';
            $result = $this->rows($queryData);
            
            if(count($result) > 0):
                $locationList[$i] = new stdClass();
                $locationList[$i]->store_name = $store->store_name;
                $locationList[$i++]->location = $result;
            endif;
        endforeach;
        //print_r($locationList);exit;
        return $locationList;
    }

    public function getItemLocationWiseBatch($item_id,$location_id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "SUM(qty) as batch_qty,batch_no";
        $queryData['where']['item_id'] = $item_id;
        $queryData['where']['location_id'] = $location_id;
        $queryData['group_by'][] = "batch_no"; 
        $result = $this->rows($queryData);
        return $result;
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