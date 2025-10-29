<?php
class SalesInvoiceModel extends MasterModel{
    private $salesMaster = "sales_invoice";
    private $salesTrans = "sales_invoice_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingTrans = "packing_transaction";
    
    /** As Per Shining **/
    public function getDTRows($data){ 
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        $data['where_in']['trans_main.entry_type'] = [6,7,8];
        if(!empty($data['from_date']) AND !empty($data['to_date'])):
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; 
        else:
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;
        
        if(!empty($data['party_id'])){$data['where']['trans_main.party_id'] = $data['party_id'];}
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}
        if(!empty($data['sales_executive'])){$data['where']['trans_main.sales_executive'] = $data['sales_executive'];}
        if(!empty($data['state_code']) AND $data['state_code'] == '1'){$data['where']['trans_main.party_state_code'] = 24;}
        if(!empty($data['state_code']) AND $data['state_code'] == '2'){$data['where']['trans_main.party_state_code != '] = 24;}
        $data['order_by']['trans_main.trans_no'] = "ASC";

        if($data['list_type'] == 'LISTING'){$data['searchCol'][] = "";}
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.taxable_amount";
        $data['searchCol'][] = "trans_main.gst_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        
        if(!empty($data['list_type']) AND $data['list_type'] == 'LISTING'){$columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_main.taxable_amount','trans_main.gst_amount','trans_main.net_amount');}
        else{$columns =array('','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_main.taxable_amount','trans_main.gst_amount','trans_main.net_amount');}

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getItemWiseDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.price, trans_child.disc_amount,trans_child.amount,trans_child.gst_amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no, trans_main.trans_date,trans_main.trans_number,trans_main.sales_type,trans_main.party_id,trans_main.party_name, trans_main.net_amount as inv_amount,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['customWhere'][] = 'trans_child.entry_type IN (6,7,8)';
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}
        if(!empty($data['from_date']) AND !empty($data['to_date'])):
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; 
        else:
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif; 
        if(!empty($data['party_id'])){$data['where']['trans_main.party_id'] = $data['party_id'];}
        if(!empty($data['sales_executive'])){$data['where']['trans_main.sales_executive'] = $data['sales_executive'];}
        if(!empty($data['state_code']) AND $data['state_code'] == '1'){$data['where']['trans_main.party_state_code'] = 24;}
        if(!empty($data['state_code']) AND $data['state_code'] == '2'){$data['where']['trans_main.party_state_code != '] = 24;}
        $data['where_in']['trans_child.entry_type'] = [6,7,8];
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        if($data['list_type'] == 'LISTING'){$data['searchCol'][] = "";}
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "trans_child.price";
        $data['searchCol'][] = "trans_child.disc_amount";
        $data['searchCol'][] = "trans_child.amount";

        if(!empty($data['list_type']) AND $data['list_type'] = 'LISTING'){$columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_child.item_name','trans_child.qty','trans_child.price','trans_child.disc_amount','trans_child.amount');}
        else{$columns =array('','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_child.item_name','trans_child.qty','trans_child.price','trans_child.disc_amount','trans_child.amount');}
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function salesTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function save($masterData,$itemData,$expenseData,$redirect_url="salesInvoice"){
        try{
            $this->db->trans_begin();
            $id = $masterData['id'];		
            if(empty($id)):
                // Get Latest Trans Number
                //$masterData['trans_prefix'] = $this->transModel->getTransPrefix(6);
                //$masterData['trans_no'] = $this->transModel->nextTransNo(6);
                //$masterData['trans_number'] = $this->getPrefixNumber($data['trans_prefix'],$data['trans_no']);
                
                $saveInvoice = $this->store($this->transMain,$masterData);
                $salesId = $saveInvoice['insert_id'];	
                $masterData['id'] = $salesId;                

                $result = ['status'=>1,'message'=>'Sales Invoice saved successfully.','url'=>base_url($redirect_url)];
            else:
                $this->store($this->transMain,$masterData);
                $salesId = $id;	
                $masterData['id'] = $salesId;	
                
                $transDataResult = $this->salesTransactions($id);
                foreach($transDataResult as $row):
                    $batch_qty = array();$packing_trans_id=array();
                    $batch_qty = explode(",",$row->batch_qty);
                    $packing_trans_id = explode(",",$row->rev_no); 
                    foreach($batch_qty as $k=>$v):
                        if(!empty($packing_trans_id[$k])){
                            $setData = Array();
                            $setData['tableName'] = $this->packingTrans;
                            $setData['where']['id'] = $packing_trans_id[$k];
                            $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$v;
                            $this->setValue($setData);
                        }
                    endforeach;
                    if(!empty($row->ref_id)):
                        $setData = Array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                        $this->setValue($setData);
    
                        $queryData = array();
                        $queryData['tableName'] = $this->transChild;
                        $queryData['where']['id'] = $row->ref_id;
                        $transRow = $this->row($queryData);
    
                        if($transRow->qty <= $transRow->dispatch_qty):
                            $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                        endif;
                    endif;

                    if($row->stock_eff == 1):
                        /** Update Item Stock **/
                        $setData = Array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $row->item_id;
                        $setData['set']['qty'] = 'qty, + '.$row->qty;
                        $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                        $qryresult = $this->setValue($setData);

                        /** Remove Stock Transaction **/
                        $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>5]);
                    endif;

                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->transChild,['id'=>$row->id]);
                    endif;
                endforeach;

                $result = ['status'=>1,'message'=>'Sales Invoice updated successfully.','url'=>base_url($redirect_url)];
            endif;

            foreach($itemData['item_id'] as $key=>$value):
                $batch_qty = array(); $batch_no = array(); $tc_no = array(); $ref_batch = array(); $location_id = array();$packing_trans_id = array();
                /* $batch_qty[] = $itemData['batch_qty'][$key];
                $batch_no[] = $itemData['batch_no'][$key];
                $location_id[] = $itemData['location_id'][$key];
                $packing_trans_id[] = $itemData['packing_trans_id'][$key]; */
                if($itemData['stock_eff'][$key] == 1):
                    $batch_qty = explode(",",$itemData['batch_qty'][$key]);
                    $tc_no = explode(",",$itemData['tc_no'][$key]);
					$ref_batch = explode(",",$itemData['ref_batch'][$key]);
                    $batch_no = explode(",",$itemData['batch_no'][$key]);
                    $location_id = explode(",",$itemData['location_id'][$key]);
                    $packing_trans_id = explode(",",$itemData['packing_trans_id'][$key]);
                endif;


                $salesTransData = [
					'id'=>$itemData['id'][$key],
					'trans_main_id'=>$salesId,
					'entry_type' => $masterData['entry_type'],
					'currency' => $masterData['currency'],
					'inrrate' => $masterData['inrrate'],
					'from_entry_type' => $itemData['from_entry_type'][$key],
					'ref_id' => $itemData['ref_id'][$key],
					'so_ref_id' => $itemData['so_ref_id'][$key],
					'item_id'=>$value,
					'item_name' => $itemData['item_name'][$key],
					'item_type' => $itemData['item_type'][$key],
					'item_code' => $itemData['item_code'][$key],
					'item_desc' => $itemData['item_desc'][$key],
					'unit_id' => $itemData['unit_id'][$key],
					'unit_name' => $itemData['unit_name'][$key],
					'location_id' => implode(",",$location_id),
					'batch_no' => implode(",",$batch_no),
					'batch_qty' => implode(",",$batch_qty),
					'tc_no' => implode(",",$tc_no),
					'material_grade' => implode(",",$ref_batch),
					'rev_no' => implode(",",$packing_trans_id),
					'stock_eff' => $itemData['stock_eff'][$key],
					'hsn_code' => $itemData['hsn_code'][$key],
					'qty' => $itemData['qty'][$key],
					'price' => $itemData['price'][$key],
					'org_price' => $itemData['org_price'][$key],
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
					'created_by' => $masterData['created_by']
				];
                $saveTrans = $this->store($this->transChild,$salesTransData);
                $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];
                
                if(!empty($itemData['ref_id'][$key])):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $itemData['ref_id'][$key];
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$itemData['qty'][$key];
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $itemData['ref_id'][$key];
                    $transRow = $this->row($queryData);

                    if($transRow->qty <= $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
                    endif;
                endif;

                //Karmi 
                foreach($batch_qty as $k=>$v):
                    if(!empty($packing_trans_id[$k])){
                        $setData = Array();
                        $setData['tableName'] = $this->packingTrans;
                        $setData['where']['id'] = $packing_trans_id[$k];
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$v;
                        $this->setValue($setData);
                    }
                endforeach;

                if($itemData['stock_eff'][$key] == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $itemData['item_id'][$key];
                    $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
                    $setData['set']['packing_qty'] = 'packing_qty, - '.$itemData['qty'][$key];
                    $qryresult = $this->setValue($setData);

                    /*** UPDATE STOCK TRANSACTION DATA ***/
                    foreach($batch_qty as $bk=>$bv):
                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$location_id[$bk];
                        if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                        if(!empty($tc_no[$bk])){$stockQueryData['tc_no'] = $tc_no[$bk];}
                        $stockQueryData['ref_batch'] = (!empty($ref_batch[$bk])) ? $ref_batch[$bk] : '';
                        $stockQueryData['trans_type']=2;
                        $stockQueryData['item_id']=$itemData['item_id'][$key];
                        $stockQueryData['qty'] = "-".$bv;
                        $stockQueryData['ref_type']=5;
                        $stockQueryData['ref_id']=$refID;
                        $stockQueryData['ref_no']=$masterData['trans_prefix'].$masterData['trans_no'];
                        $stockQueryData['ref_date']=$masterData['trans_date'];
                        $stockQueryData['created_by']=$this->loginID;
                        $this->store($this->stockTrans,$stockQueryData);
                    endforeach;
                endif;            
            endforeach;

            if(!empty($masterData['ref_id'])):
                $refIds = explode(",",$masterData['ref_id']);
                foreach($refIds as $key=>$value):
                    if($masterData['from_entry_type'] == 5):
                        $pendingItems = $this->challan->checkChallanPendingStatus($value);
                    elseif($masterData['from_entry_type'] == 4):
                        $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    elseif($masterData['from_entry_type'] == 11):
                        $pendingItems = $this->customInvoice->checkCustomInvoicePendingStatus($value);
                    endif;
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
                    endif;
                endforeach;
            endif;

            $ledgerEff = $this->transModel->ledgerEffects($masterData,$expenseData);
            if($ledgerEff == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
                /* Send Notification 
                $siNo = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                $notifyData['notificationTitle'] = (empty($id))?"New Sales Invoice":"Update Sales Invoice";
                $notifyData['notificationMsg'] = (empty($id))?"New Sales Invoice Generated. SI. No. : ".$siNo:"Sales Invoice updated. SI No. : ".$siNo;
                $notifyData['payload'] = ['callBack' => base_url('salesInvoice')];
                $notifyData['controller'] = "'salesInvoice'";
                $notifyData['action'] = (empty($id))?"W":"M";
                $this->notify($notifyData);*/

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	public function getInvoice($id){ 
        $queryData = array();   
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*,challanMain.trans_date as challan_date,soMain.doc_no';
        $queryData['leftJoin']['trans_main as challanMain'] = "challanMain.id = trans_main.ref_id AND trans_main.from_entry_type = 5";
        $queryData['leftJoin']['trans_main as soMain'] = "soMain.id = trans_main.ref_id AND trans_main.from_entry_type = 4";
        $queryData['where']['trans_main.id'] = $id;
        $invoiceData = $this->row($queryData);
        $invoiceData->itemData = $this->salesTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }

    public function salesTransactions($id,$limit=""){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,item_master.full_name,item_master.drawing_no,item_master.material_grade'; 
        $queryData['leftJoin']['item_master'] = 'item_master.id = trans_child.item_id';
        $queryData['where']['trans_main_id'] = $id;
        if(!empty($limit)){$queryData['limit'] = $limit;}
        return $this->rows($queryData);
    }

    public function deleteInv($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getInvoice($id);
            foreach($transData->itemData as $row):
                $batch_qty = array();$packing_trans_id=array();
                $batch_qty = explode(",",$row->batch_qty);
                $packing_trans_id = explode(",",$row->rev_no); 
                foreach($batch_qty as $k=>$v):
                    if(!empty($packing_trans_id[$k])){
                         $setData = Array();
                        $setData['tableName'] = $this->packingTrans;
                        $setData['where']['id'] = $packing_trans_id[$k];
                        $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$v;
                        $this->setValue($setData); 
                    }
                endforeach;
                
                if(!empty($row->ref_id)):
                    $setData = Array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                    $this->setValue($setData);

                    $queryData = array();
                    $queryData['tableName'] = $this->transChild;
                    $queryData['where']['id'] = $row->ref_id;
                    $transRow = $this->row($queryData);

                    if($transRow->qty != $transRow->dispatch_qty):
                        $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                    endif;
                endif;

                if($row->stock_eff == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, + '.$row->qty;
                    $setData['set']['packing_qty'] = 'packing_qty, + '.$row->qty;
                    $qryresult = $this->setValue($setData);

                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>5]);
                endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($transData->ref_id)):
                $refIds = explode(",",$transData->ref_id);
                foreach($refIds as $key=>$value):
                    if($transData->from_entry_type == 5):
                        $pendingItems = $this->challan->checkChallanPendingStatus($value);
                    elseif($transData->from_entry_type == 4):
                        $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    elseif($transData->from_entry_type == 11):
                        $pendingItems = $this->customInvoice->checkCustomInvoicePendingStatus($value);
                    endif;
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                    endif;
                endforeach;
            endif;
            $result = $this->trash($this->transMain,['id'=>$id],'Sales Invoice');

            $deleteLedgerTrans = $this->transModel->deleteLedgerTrans($id);
            if($deleteLedgerTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
            $deleteExpenseTrans = $this->transModel->deleteExpenseTrans($id);
            if($deleteExpenseTrans == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
                /* Send Notification 
                $siNo = getPrefixNumber($transData->trans_prefix,$transData->trans_no);
                $notifyData['notificationTitle'] = "Delete Sales Invoice";
                $notifyData['notificationMsg'] = "Sales Invoice deleted. SI No. : ".$siNo;
                $notifyData['payload'] = ['callBack' => base_url('salesInvoice')];
                $notifyData['controller'] = "'salesInvoice'";
                $notifyData['action'] = "D";
                $this->notify($notifyData);*/
                
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function batchWiseItemStock($data){
		
        $i=1;$tbody="";
		$locationData = $this->store->getStoreLocationList();
		if(!empty($locationData)){
			foreach($locationData as $lData){                
				
				foreach($lData['location'] as $batch):
                    $queryData = array();
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty,batch_no";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result)){
                        $batch_no = array();
						foreach($result as $row){
                            $batch_no = (!empty($data['trans_id']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!empty($data['trans_id']))?explode(",",$data['batch_qty']):$data['batch_qty'];
                            if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                if(!empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                    $arrayKey = array_search($row->batch_no,$batch_no);
                                    $qty = $batch_qty[$arrayKey];
                                    $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $batch_qty[$arrayKey]):floatVal($row->qty);
                                else:
                                    $qty = "0";
                                    $cl_stock = floatVal($row->qty);
                                endif;                                
                                
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$i.'</td>';
                                    $tbody .= '<td>['.$lData['store_name'].'] '.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($row->qty).'</td>';
                                    $tbody .= '<td>
                                        <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                                        <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                                        <input type="hidden" name="location[]" id="location'.$i.'" value="'.$batch->id.'" />
                                        <div class="error batch_qty'.$i.'"></div>
                                    </td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
						}
					}
				endforeach;
			}
		}else{
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status'=>1,'batchData'=>$tbody];
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

    public function getSalesInvoiceList($party_id){
        $data['tableName'] = $this->transMain;
        $data['where']['party_id'] = $party_id;
        $data['where_in']['entry_type'] = [6,7,8];
        return $this->rows($data);      
    }
    
    //Create By : JP @24-05-2022 11:25 AM
    public function getInvoiceSummary($postData){
        $qData['tableName'] = 'trans_main';
		$qData['select'] = "SUM(trans_main.taxable_amount) as taxable_amount,SUM(trans_main.gst_amount) as gst_amount,SUM(trans_main.net_amount) as net_amount";
        $qData['where_in']['trans_main.entry_type'] = [6,7,8];
        if(!empty($postData['sales_type'])){$qData['where_in']['trans_main.sales_type'] = $postData['sales_type'];}
        if(!empty($postData['party_id'])){$qData['where']['trans_main.party_id'] = $postData['party_id'];}
        if(!empty($postData['sales_executive'])){$qData['where']['trans_main.sales_executive'] = $postData['sales_executive'];}
        if(!empty($postData['state_code']) AND $postData['state_code'] == '1'){$qData['where']['trans_main.party_state_code'] = 24;}
        if(!empty($postData['state_code']) AND $postData['state_code'] == '2'){$qData['where']['trans_main.party_state_code != '] = 24;}
        
        if(!empty($postData['from_date']) AND !empty($postData['to_date'])):
            $qData['customWhere'][] = "trans_main.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'"; 
        else:
            $qData['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $qData['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;
		$invData = $this->row($qData);
		return $invData;
    }
    
    //Created By Karmi @26/05/2022
    public function getSalesInvDataBillWise($postData){
        $data['tableName'] = $this->transMain;
        $data['where_in']['trans_main.entry_type'] = '6,7,8';
        if(!empty($data['sales_type'])){$data['where']['trans_main..sales_type'] = $data['sales_type'];}
        if(!empty($postData['party_id'])){$data['where']['trans_main.party_id'] = $postData['party_id'];}
        if(!empty($postData['from_date']) AND !empty($postData['to_date'])):
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'"; 
        else:
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;
        if(!empty($postData['sales_executive'])){$qData['where']['trans_main.sales_executive'] = $postData['sales_executive'];}
        $data['order_by']['trans_date'] = "ASC";
        $data['order_by']['id'] = "ASC";        
        $result = $this->rows($data);
        return $result;

    }
    
	//Created By Karmi @26/05/2022
    public function getSalesInvDataItemWise($postData){

        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.price, trans_child.disc_amount,trans_child.amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no, trans_main.trans_date,trans_main.trans_number,trans_main.sales_type,trans_main.party_id,trans_main.party_name, trans_main.net_amount as inv_amount,trans_main.remark";
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where_in']['trans_child.entry_type'] = '6,7,8';
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}
        if(!empty($postData['party_id'])){$data['where']['trans_main.party_id'] = $postData['party_id'];}
        if(!empty($postData['sales_executive'])){$qData['where']['trans_main.sales_executive'] = $postData['sales_executive'];}
        if(!empty($postData['from_date']) AND !empty($postData['to_date']))
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'"; 
        $data['where_in']['trans_child.entry_type'] = [6,7,8];
        $data['order_by']['trans_main.trans_date'] = "ASC";
        $data['order_by']['trans_main.id'] = "ASC";       
        return $this->rows($data);

    }

    
    public function getCustomerListOnlySales($executive_id=0,$postData=Array()){
        $data['tableName'] = 'trans_main';
        $data['select'] = 'party_master.id,party_master.party_name,currency.inrrate,states.name as state_name,cities.name as city_name';
        $data['join']['party_master'] = 'party_master.id = trans_main.party_id';
        $data['leftJoin']['currency'] = 'currency.currency = party_master.currency';
        $data['leftJoin']['states'] = 'party_master.state_id = states.id';
        $data['leftJoin']['cities'] = 'party_master.city_id = cities.id';
        $data['where_in']['trans_main.entry_type'] = "6,7,8";
		$data['where']['trans_main.trans_date >='] = date('Y-m-d',strtotime($postData['from_date']));
		$data['where']['trans_main.trans_date <='] = date('Y-m-d',strtotime($postData['to_date']));
        $data['group_by'][] = 'trans_main.party_id';
        if(!empty($executive_id) && !in_array($this->userRole,[-1,1,3])){$data['where']['party_master.sales_executive'] = $executive_id;}
        return $this->rows($data);
    }
	
	public function getInvoiceTransForTc($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.party_name , trans_main.trans_no, trans_main.trans_number,trans_main.trans_date, item_master.full_name, item_master.drawing_no, IFNULL(dc.batch_no,trans_child.batch_no) as batchNo,so_main.doc_no AS po_no'; 
        $queryData['leftJoin']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
        $queryData['leftJoin']['item_master'] = 'item_master.id = trans_child.item_id';
        $queryData['leftJoin']['trans_child dc'] = 'dc.id = trans_child.ref_id';
        $queryData['leftJoin']['trans_child so'] = 'so.id = trans_child.so_ref_id';
        $queryData['leftJoin']['trans_main so_main'] = 'so_main.id = so.trans_main_id';
        $queryData['where']['trans_child.trans_main_id'] = $id;
        return $this->rows($queryData);
    }
	
    public function getStockTransForInv($trans_id,$from_entry_type){
        $data['tableName'] = $this->stockTrans;
		$data['select'] = 'stock_transaction.*,material_master.id AS grade_id, material_master.color_code,job_bom.ref_item_id AS rm_item_id';
        $data['where_in']['stock_transaction.ref_id'] = $trans_id;
        $data['where']['stock_transaction.trans_type'] = 2;
		$data['leftJoin']['job_card'] = 'job_card.job_number = stock_transaction.batch_no';
		$data['leftJoin']['job_bom'] = 'job_bom.job_card_id = job_card.id';
		$data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$data['leftJoin']['material_master'] = 'material_master.id = item_master.material_grade_id';
        if($from_entry_type == 5):
            $data['where']['stock_transaction.ref_type'] = 4;
        else:
            $data['where']['stock_transaction.ref_type'] = 5;
        endif;
        return $this->rows($data);
    }
	
    public function getTcParamByTcNo($tc_no){
        $data['tableName'] = 'tc_inspection';
        $data['select'] = 'tc_inspection.id,tc_inspection.param_name,tc_inspection.min_value,tc_inspection.max_value,tc_inspection.spec_type,';
        $data['where']['CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)'] = $tc_no;
        $data['order_by']['tc_inspection.tc_no'] = 'ASC';
        $data['order_by']['tc_inspection.id'] = 'ASC';
        $data['limit'] = 15;
        return $this->rows($data);
    }
	
    public function getTcParameterOld($tc_no,$ht_no,$rm_item_id){
        $data['tableName'] = 'tc_inspection';
        $data['select'] = "  tc_inspection.*,grn_trans.batch_no as heat_no, item_master.material_grade,item_master.size,item_category.category_name,material_master.color_code";
		$data['leftJoin']['(SELECT item_id,batch_no,tc_no,sys_tc_no,is_delete FROM grn_transaction WHERE is_delete=0 GROUP BY tc_no,batch_no,item_id) AS grn_trans'] = 'grn_trans.sys_tc_no = CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)';
        // $data['leftJoin']['grn_transaction AS grn_trans'] = 'grn_trans.sys_tc_no = CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)';
        $data['leftJoin']['item_master'] = 'grn_trans.item_id = item_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['leftJoin']['material_master'] = 'material_master.id = item_master.material_grade_id AND material_master.is_delete=0';
        $data['where_in']['CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)'] = $tc_no;
        if(!empty($ht_no)){$data['where_in']['grn_trans.batch_no'] = $ht_no;}
        if(!empty($rm_item_id)){$data['where_in']['grn_trans.item_id'] = $rm_item_id;}
        return $this->rows($data);
    }

    public function getTcParameter($tc_no,$ht_no,$rm_item_id){
        $data['tableName'] = 'grn_transaction';
        $data['select'] = "  tc_inspection.*,grn_transaction.batch_no as heat_no, item_master.material_grade,item_master.size,item_category.category_name,material_master.color_code,item_master.material_grade";
        $data['leftJoin']['tc_inspection'] = 'grn_transaction.sys_tc_no = CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)';
        $data['leftJoin']['item_master'] = 'grn_transaction.item_id = item_master.id';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['leftJoin']['material_master'] = 'material_master.id = item_master.material_grade_id';
        $data['where_in']['CONCAT(tc_inspection.tc_prefix,tc_inspection.tc_no)'] = $tc_no;
        if(!empty($ht_no)){$data['where_in']['grn_transaction.batch_no'] = $ht_no;}
        if(!empty($rm_item_id)){$data['where_in']['grn_transaction.item_id'] = $rm_item_id;}
        $data['group_by'][]="grn_transaction.tc_no,grn_transaction.batch_no,grn_transaction.item_id,tc_inspection.id";
        return $this->rows($data);
    }
	
	public function getSalesOrderMasterData($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*,transMain.doc_no,transMain.trans_date,trans_child.material_grade'; 
        $queryData['leftJoin']['trans_main as transMain'] = 'transMain.id = trans_main.ref_id';
        $queryData['leftJoin']['trans_child'] = 'trans_child.trans_main_id = transMain.id';
        $queryData['where']['trans_main.entry_type'] = 6;
        $queryData['where']['trans_child.ref_id'] = $id;
        return $this->row($queryData);
    }

    public function getDeliveryChallanData($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*,trans_child.ref_id,transChild.batch_no,transMain.ref_id,transChildSO.material_grade,transMainSO.doc_no,transMainSO.trans_date'; 
        $queryData['leftJoin']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
        $queryData['leftJoin']['trans_child as transChild'] = 'transChild.id = trans_child.ref_id';
        $queryData['leftJoin']['trans_main as transMain'] = 'transMain.id = trans_main.ref_id';
        $queryData['leftJoin']['trans_child as transChildSO'] = 'transChildSO.trans_main_id = transMain.ref_id';
        $queryData['leftJoin']['trans_main as transMainSO'] = 'transMainSO.id = transMain.ref_id';
        $queryData['where']['trans_main.entry_type'] = 6;
        $queryData['where']['trans_child.ref_id'] = $id;
        return $this->row($queryData);
    }
	
	/*
        Created By :- Sweta @27-07-2023
        Used At :- SalesInvoice/testCertificateDTRows
    */
    public function testCertificateDTRows($data){ 
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        $data['where_in']['trans_main.entry_type'] = [6,7,8];
        $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $data['order_by']['trans_main.trans_no'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        
        $columns =array('','trans_main.trans_no','trans_main.trans_date','trans_main.party_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
	
	/*  Create By : Avruti @29-11-2021 4:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount($type=0){
		  $data['tableName'] = $this->transMain;
		
        return $this->numRows($data);
    }

    public function getSalesInvoiceList_api($limit, $start,$type=0){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        // $data['where_in']['trans_main.sales_type'] = $data['sales_type'];
        // $data['where_in']['trans_main.entry_type'] = $data['entry_type'];
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        // $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "ASC";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>