<?php
class CreditNoteModel extends MasterModel{
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
        $data['where']['trans_main.entry_type'] = 13;
        //$data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if(!empty($data['from_date']) AND !empty($data['to_date'])):
            $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; 
        else:
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;
            
        // $data['where_in']['trans_main.sales_type'] = $data['sales_type'];
        // $data['where_in']['trans_main.entry_type'] = $data['entry_type'];
        // $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "ASC";

        $data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getDTRows1($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.net_amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.net_amount as inv_amount,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where_in']['trans_child.entry_type'] = [6,7,8];
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

         $data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.net_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','party_master.party_name','trans_child.item_name','trans_child.net_amount','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    // public function salesTransRow($id){
    //     $queryData['tableName'] = $this->transChild;
    //     $queryData['where']['id'] = $id;
    //     return $this->row($queryData);
    // }

    public function save($masterData,$itemData,$expenseData,$redirect_url="creditNote"){
        try{
            $this->db->trans_begin();
            $id = $masterData['id'];		
            if(empty($id)):
                // Get Latest Trans Number
			    $masterData['trans_no'] = $this->transModel->getTransPrefix(13);
		        $masterData['nextTransNo'] = $this->transModel->nextTransNo(13);
				$masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'], $masterData['trans_no']);
				
                $saveCredit = $this->store($this->transMain,$masterData);
                $creditId = $saveCredit['insert_id'];	
                $masterData['id'] = $creditId;                

                $result = ['status'=>1,'message'=>'Credit Note saved successfully.','url'=>base_url($redirect_url)];
            else:
                $this->store($this->transMain,$masterData);
                $creditId = $id;	
                $masterData['id'] = $creditId;	
                
                $transDataResult = $this->creditTransactions($id);
                foreach($transDataResult as $row):
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

                $result = ['status'=>1,'message'=>'Credit Note updated successfully.','url'=>base_url($redirect_url)];
            endif;

            foreach($itemData['item_id'] as $key=>$value):
                $batch_qty = array(); $batch_no = array(); $location_id = array();
                $batch_qty[] = $itemData['qty'][$key];
                $batch_no[] = "General Batch";
                $location_id[] = $this->RTD_STORE->id;


                $creditTransData = [
                                    'id'=>$itemData['id'][$key],
                                    'trans_main_id'=>$creditId,
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
                                    'location_id' => implode(",",$location_id),
                                    'batch_no' => implode(",",$batch_no),
                                    'batch_qty' => implode(",",$batch_qty),
                                    'stock_eff' => $itemData['stock_eff'][$key],
                                    'hsn_code' => $itemData['hsn_code'][$key],
                                    'qty' => $itemData['qty'][$key],
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
                                    'created_by' => $masterData['created_by']
                                ];
                $saveTrans = $this->store($this->transChild,$creditTransData);
                $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];

                if($itemData['stock_eff'][$key] == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $itemData['item_id'][$key];
                    $setData['set']['qty'] = 'qty, + '.$itemData['qty'][$key];
                    $setData['set']['packing_qty'] = 'packing_qty, + '.$itemData['qty'][$key];
                    $this->setValue($setData);

                    /*** UPDATE STOCK TRANSACTION DATA ***/
                    foreach($batch_qty as $bk=>$bv):
                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$location_id[$bk];
                        if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                        $stockQueryData['trans_type']=1;
                        $stockQueryData['item_id']=$itemData['item_id'][$key];
                        $stockQueryData['qty'] = $bv;
                        $stockQueryData['ref_type']=19;
                        $stockQueryData['ref_id']=$refID;
                        $stockQueryData['ref_no']=getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                        $stockQueryData['ref_date']=$masterData['trans_date'];
                        $stockQueryData['created_by']=$this->loginID;
                        $this->store($this->stockTrans,$stockQueryData);
                    endforeach;
                endif;            
            endforeach;

            $ledgerEff = $this->transModel->ledgerEffects($masterData,$expenseData);

            if($ledgerEff == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
                /* Send Notification */
                $cnNo = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                $notifyData['notificationTitle'] = (empty($id))?"New Credit Note":"Update Credit Note";
                $notifyData['notificationMsg'] = (empty($id))?"New Credit Note Generated. CN. No. : ".$cnNo:"Credit Note updated. CN No. : ".$cnNo;
                $notifyData['payload'] = ['callBack' => base_url('creditNote')];
                $notifyData['controller'] = "'creditNote'";
                $notifyData['action'] = (empty($id))?"W":"M";
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

    public function getCreditNote($id){ 
        $queryData = array();   
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $creditData = $this->row($queryData);
        $creditData->itemData = $this->creditTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $creditData->expenseData = $this->row($queryData);
        return $creditData;
    }

    public function creditTransactions($id,$limit=""){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        if(!empty($limit)){$queryData['limit'] = $limit;}
        return $this->rows($queryData);
    }

    public function deleteCredit($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getCreditNote($id);
            foreach($transData->itemData as $row):
                if($row->stock_eff == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, - '.$row->qty;
                    $setData['set']['packing_qty'] = 'packing_qty, - '.$row->qty;
                    $qryresult = $this->setValue($setData);

                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>1,'ref_type'=>19]);
                endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            $result = $this->trash($this->transMain,['id'=>$id],'Credit Note');

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
                /* Send Notification */
                $cnNo = getPrefixNumber($transData->trans_prefix,$transData->trans_no);
                $notifyData['notificationTitle'] = "Delete Credit Note";
                $notifyData['notificationMsg'] = "Credit Note deleted. CN No. : ".$cnNo;
                $notifyData['payload'] = ['callBack' => base_url('creditNote')];
                $notifyData['controller'] = "'creditNote'";
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

    public function getInvoiceItemsForCreditNote($transIds){
		$data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.*,AVG(price) as avg_price";
        $data['where_in']['trans_child.trans_main_id'] = $transIds;
        $data['group_by'][]='trans_child.item_id';
        $result = $this->rows($data);
        
        return $result;
	}

    // public function getCreditNoteList($party_id){
    //     $data['tableName'] = $this->transMain;
    //     $data['where']['party_id'] = $party_id;
    //     $data['where_in']['entry_type'] = [6,7,8];
    //     return $this->rows($data);      
    // }
	
// /*  Create By : Avruti @29-11-2021 4:00 PM
//     update by : 
//     note : 
// */
//     //---------------- API Code Start ------//

//     public function getCount($type=0){
// 		  $data['tableName'] = $this->transMain;
		
//         return $this->numRows($data);
//     }

//     public function getCreditNoteList_api($limit, $start,$type=0){
//         $data['tableName'] = $this->transMain;
//         $data['select'] = "trans_main.*";
//         // $data['where_in']['trans_main.sales_type'] = $data['sales_type'];
//         // $data['where_in']['trans_main.entry_type'] = $data['entry_type'];
//         $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
//         // $data['order_by']['trans_main.trans_date'] = "DESC";
//         $data['order_by']['trans_main.trans_no'] = "ASC";

//         $data['length'] = $limit;
//         $data['start'] = $start;
//         return $this->rows($data);
//     }

//     //------ API Code End -------//
}
?>