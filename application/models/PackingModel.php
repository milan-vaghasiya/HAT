<?php
class PackingModel extends MasterModel{
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->packingTrans;
        $data['select'] = "packing_master.*,packing_transaction.trans_main_id,packing_transaction.total_qty,packing_transaction.dispatch_qty,ifnull(party_master.party_code,ifnull(item_party.party_code,'')) as party_code,party_master.packing_sticker";
        $data['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $data['leftJoin']['trans_main'] = "packing_transaction.trans_main_id = trans_main.id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
        $data['leftJoin']['party_master as item_party'] = "item_party.id = item_master.party_id";
		$data['order_by']['packing_master.entry_date'] = "DESC";
        $data['group_by'][] = "packing_transaction.packing_id";
        if($data['status'] == 0){
            $data['where']['packing_master.packing_status'] = 0;
            $data['where']['packing_master.entry_type !='] = 'New Request';
        }
        if($data['status'] == 1){            
            $data['where']['packing_master.entry_type'] = 'Export';
            $data['where_in']['packing_master.packing_status'] = [1,2]; 
            $data['where']['packing_master.entry_date >= '] = $this->startYearDate;
            $data['where']['packing_master.entry_date <= '] = $this->endYearDate;
        }
        if($data['status'] == 2){            
            $data['where']['packing_master.entry_type'] = 'Regular';
            $data['where_in']['packing_master.packing_status'] = [1,2]; 
            $data['where']['packing_master.entry_date >= '] = $this->startYearDate;
            $data['where']['packing_master.entry_date <= '] = $this->endYearDate;
        }
        if($data['status'] == 3){
            $data['customWhere'][] = '(packing_transaction.total_qty - packing_transaction.dispatch_qty) < 1';
        } 
        if($data['status'] == 4){
            $data['where']['packing_master.packing_by'] = 1;
            $data['where']['packing_master.entry_type'] = 'New Request';
        }


        $data['searchCol'][] = "DATE_FORMAT(packing_master.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "packing_master.entry_type";
        $data['searchCol'][] = "party_master.party_code";
        $data['searchCol'][] = "item_party.party_code";
        $data['searchCol'][] = "packing_master.total_net_weight";
        $data['searchCol'][] = "packing_master.total_wooden_box_weight";
        $data['searchCol'][] = "packing_master.total_gross_weight";

		$columns =array('','','packing_master.entry_date','packing_master.trans_number','packing_master.entry_type','party_master.party_code','packing_master.total_net_weight','packing_master.total_wooden_box_weight','packing_master.total_gross_weight','packing_master.packing_status');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getConsumable($category_id){
        $data['tableName'] = $this->itemMaster;    
        $data['where']['item_master.item_type'] = 2;
        $data['where']['item_master.category_id'] = $category_id;
        return $this->rows($data);
    }

    public function getRegularPackingBoxOnItem($item_id,$ref_id=""){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_name,item_master.size";
        $queryData['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
        $queryData['leftJoin']['item_master'] = "packing_transaction.box_id = item_master.id";
        $queryData['where']['packing_transaction.item_id'] = $item_id;
        $queryData['where']['packing_master.entry_type'] = "Regular";
        if(!empty($ref_id)):
            $queryData['customWhere'][] = '( packing_transaction.export_qty < packing_transaction.total_qty OR packing_transaction.id = '.$ref_id.' )';
        else:
            $queryData['customWhere'][] = 'packing_transaction.export_qty < packing_transaction.total_qty';
        endif;
        
        return $this->rows($queryData);
    }

    public function getSalesOrderNoListForPacking($item_id="",$order_id=""){
        $queryData = array();
        $queryData['tableName'] = "trans_child";
        $queryData['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,party_master.party_code";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_main.sales_type'] = 2;
        if(!empty($order_id)):
            $queryData['customWhere'][] = '(trans_child.packing_status = 0 AND trans_child.trans_status = 0 OR trans_main.id ='.$order_id.')';
        else:
            $queryData['where']['trans_child.packing_status'] = 0;
            $queryData['where']['trans_child.trans_status'] = 0;
        endif;
        if(!empty($item_id)){
            $queryData['where']['trans_child.item_id'] = $item_id;
        }
        $queryData['group_by'][] = "trans_child.trans_main_id";
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesOrderItemsForPacking($order_id){
        $queryData = array();
        $queryData['tableName'] = "trans_child";
        $queryData['select'] = "trans_child.id as trans_child_id,trans_child.item_id as id,item_master.item_code,item_master.item_name";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.packing_status'] = 0;
        $queryData['where']['trans_child.trans_main_id'] = $order_id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function batchWiseItemStock($data){ 
        $i=1;$tbody='';
		$locationData = $this->store->getStoreLocationListWithoutProcess('store_type != 1'); 
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
                        $batchData = (!empty($data['batch_data']))?$data['batch_data']:array();
                        $batch_no = (!empty($batchData))?(array_column($batchData,'batch_no')):array();
						foreach($result as $row){
                            $cl_stock = 0;$qty = 0; 
                            if(!empty($batch_no) && in_array($row->batch_no,$batch_no)):
                                if(in_array($row->batch_no,$batch_no)):
                                    $batchKey = array_search($row->batch_no,array_column($batchData,'batch_no'));
                                    $qty = $batchData[$batchKey]['batch_qty'];
                                endif;
                            endif;
                            $cl_stock = floatVal($row->qty);
                            if(floatVal($cl_stock) > 0 && $this->PROD_STORE->id == $batch->id):
                                $readOnly = (floatVal($cl_stock) > 0)?"":"readonly";
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$i.'</td>';
                                    $tbody .= '<td>['.$lData['store_name'].'] '.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($cl_stock).'</td>';
                                    $tbody .= '<td>
                                        <input type="text" name="batch_qty[]" class="form-control floatOnly batchQty bQty'.$i.'" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" '.$readOnly.'/>
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
		} else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        
        $tbody = (!empty($tbody))?$tbody:'<tr><td class="text-center aaa" colspan="5">No Data Found.</td></tr>';
        return ['status'=>1,'batchData'=>$tbody];
    }

    public function getNextNo(){
        $data['select'] = "MAX(trans_no) as trans_no";
		$data['where']['entry_date >= '] = $this->startYearDate;
        $data['where']['entry_date <= '] = $this->endYearDate;
        $data['tableName'] = $this->packingMaster;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $itemData = $data['item_data'];
            unset($data['item_data']);

            if(!empty($data['id'])):
                 
                $packingData = $this->getPackingData($data['id']);

                foreach($packingData->itemData as $row):
                    if(!empty($row->trans_child_id)):
                        $this->store($this->transChild,['id'=>$row->trans_child_id,'packing_status'=>0]);
                    endif; 

                    if($packingData->is_final == 1):
                        if($packingData->entry_type == "Regular"):
                            /*** UPDATE PRODUCT PACKING QTY ***/
                            $setData = array();
                            $setData['tableName'] = $this->itemMaster;
                            $setData['where']['id'] = $row->item_id;
                            $setData['set']['packing_qty'] = 'packing_qty, - '.$row->packing_qty;
                            $this->setValue($setData);
                        endif;
    
                        /*** UPDATE PACKING QTY IN TRANS CHILD ***/
                        if(!empty($row->trans_child_id)):
                            $setData = array();
                            $setData['tableName'] = $this->transChild;
                            $setData['where']['id'] = $row->trans_child_id;
                            $setData['set']['packing_qty'] = 'packing_qty, - '.$row->packing_qty;
                            $this->setValue($setData);
                        endif;
    
                        $batchData = json_decode($row->json_data);
                        foreach($batchData as $batch):
                            $this->remove($this->stockTrans,['ref_id'=>$packingData->id,'trans_ref_id'=>$row->id,'ref_type'=>30]);
                        endforeach;
                        
                        if(empty($row->ref_id)):
                            $setData = array();
                            $setData['tableName'] = $this->itemMaster;
                            $setData['where']['id'] = $row->box_id;
                            $setData['set']['qty'] = 'qty, + '.$row->total_box;
                            $this->setValue($setData);
        
                            $this->remove($this->stockTrans,['ref_id'=>$packingData->id,'trans_ref_id'=>$row->id,'ref_type'=>15,'trans_type'=>2]);
                        else:
                            $setData = array();
                            $setData['tableName'] = $this->packingTrans;
                            $setData['where']['id'] = $row->ref_id;
                            $setData['set']['export_qty'] = 'export_qty, - '.$row->total_qty;
                            $this->setValue($setData);
                        endif;
                    endif;

                    $this->trash($this->packingTrans,['id'=>$row->id]);
                endforeach;
               
            endif;

            $result = $this->store($this->packingMaster,$data,'Packing');
            $packingId = (empty($data['id']))?$result['insert_id']:$data['id'];

            foreach($itemData as $key=>$row):
                $itemData = $this->item->getItem($row['item_id']);
                $row['item_name'] = $itemData->item_name;
                $row['item_alias'] = $itemData->item_alias;
                $row['packing_id'] = $packingId;
                $row['created_by'] = $data['created_by'];
                $row['json_data'] = $row['batch_data'];
                $row['is_delete'] = 0;
                unset($row['batch_data']);
                $trans = $this->store($this->packingTrans,$row);
                $row['id'] = (empty($row['id']))?$trans['insert_id']:$row['id'];

                if(!empty($data['trans_child_id'])):
                    $this->store($this->transChild,['id'=>$row['trans_child_id'],'packing_status'=>(($data['is_final'] == 0)?1:2)]);
                endif;

                if($data['is_final'] == 1):
                    if($data['entry_type'] == "Regular"):
                        /*** UPDATE PRODUCT PACKING QTY ***/
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $row['item_id'];
                        $setData['set']['packing_qty'] = 'packing_qty, + '.$row['packing_qty'];
                        $this->setValue($setData);
                    endif;

                    /*** UPDATE PACKING QTY IN TRANS CHILD ***/
                    if(!empty($row['trans_child_id'])):
                        $setData = array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row['trans_child_id'];
                        $setData['set']['packing_qty'] = 'packing_qty, + '.$row['packing_qty'];
                        $this->setValue($setData);
                    endif;

                    $batchData = json_decode($row['json_data']);
                    foreach($batchData as $batch):
                        $currentStock = $this->checkItemWiseBatchStock(['item_id'=>$row['item_id'],'batch_no'=>$batch->batch_no,'location_id'=>$batch->location_id]);
                        if($batch->batch_qty > $currentStock->qty):
                            $errorMessage['batch_qty_'.$key] = "Batch Stock not avalible.";
                            $this->db->trans_rollback();
                            return ['status'=>0,'message'=>$errorMessage];
                        endif;

                        $stockQueryData = array();
                        $stockQueryData['id']="";
                        $stockQueryData['location_id'] = $batch->location_id;
                        if(!empty($batch->batch_no)){$stockQueryData['batch_no'] = $batch->batch_no;}
                        $stockQueryData['trans_type'] = 2;
                        $stockQueryData['item_id'] = $row['item_id'];
                        $stockQueryData['qty'] = '-'.$batch->batch_qty;
                        $stockQueryData['ref_type'] = 30;
                        $stockQueryData['ref_id'] = $packingId;
                        $stockQueryData['trans_ref_id'] = $row['id'];
                        $stockQueryData['ref_no'] = $data['trans_number'];
                        $stockQueryData['ref_date'] = $data['entry_date'];    
                        $stockQueryData['created_by'] = $data['created_by'];
                        $this->store($this->stockTrans,$stockQueryData);

                        /*** PRODUCT STOCK TRANSFER READY TO DISPATCH ***/
                        $stockQueryData = array();
                        $stockQueryData['id']="";
                        $stockQueryData['location_id'] = $this->RTD_STORE->id;
                        if(!empty($batch->batch_no)){$stockQueryData['batch_no'] = $batch->batch_no."/".$row['id'];}
                        $stockQueryData['trans_type'] = 1;
                        $stockQueryData['item_id'] = $row['item_id'];
                        $stockQueryData['qty'] = $batch->batch_qty;
                        $stockQueryData['ref_type'] = 30;
                        $stockQueryData['ref_id'] = $packingId;
                        $stockQueryData['trans_ref_id'] = $row['id'];
                        $stockQueryData['ref_no'] = $data['trans_number'];
                        $stockQueryData['ref_date'] = $data['entry_date']; 
                        if(!empty($batch->batch_no)){$stockQueryData['ref_batch'] = $batch->batch_no;}  
                        $stockQueryData['created_by'] = $data['created_by'];
                        $this->store($this->stockTrans,$stockQueryData);
                    endforeach;
					
					if(empty($row['ref_id'])):
						//$boxData = $this->item->getItem($row['box_id']);
						$boxData = $this->getItemCurrentStock($row['box_id'],$this->PKG_STORE->id);
						if($row['total_box'] > $boxData->stock_qty):
							$errorMessage['total_box_'.$key] = "Stock not avalible.";
							$this->db->trans_rollback();
							return ['status'=>0,'message'=>$errorMessage];
						endif;
						
						$setData = array();
						$setData['tableName'] = $this->itemMaster;
						$setData['where']['id'] = $row['box_id'];
						$setData['set']['qty'] = 'qty, - '.$row['total_box'];
						$this->setValue($setData);

						/*** UPDATE STOCK TRANSACTION DATA ***/
						$stockQueryData = array();
						$stockQueryData['id']="";
						$stockQueryData['location_id']=$this->PKG_STORE->id;
						$stockQueryData['batch_no'] = "General Batch";
						$stockQueryData['trans_type'] = 2;
						$stockQueryData['item_id'] = $row['box_id'];
						$stockQueryData['qty'] = "-".$row['total_box'];
						$stockQueryData['ref_type'] = 15;
						$stockQueryData['ref_id'] = $packingId;
						$stockQueryData['trans_ref_id'] = $row['id'];
						$stockQueryData['ref_no'] = $data['trans_number'];
						$stockQueryData['ref_date'] = $data['entry_date'];    
						$stockQueryData['created_by'] = $data['created_by'];
						$this->store($this->stockTrans,$stockQueryData);
                    else:
                        $setData = array();
                        $setData['tableName'] = $this->packingTrans;
                        $setData['where']['id'] = $row['ref_id'];
                        $setData['set']['export_qty'] = 'export_qty, + '.$row['total_qty'];
                        $this->setValue($setData);
					endif;
                endif;
            endforeach;            
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getPackingData($id){
        $queryData = array();
        $queryData['tableName'] = $this->packingMaster;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);
        $result->itemData = $this->getPackingItems($id);
        return $result;
    }

    public function getPackingItems($id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['where']['packing_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function checkItemWiseBatchStock($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['location_id'];
        $queryData['where']['batch_no'] = $data['batch_no'];        
        return $this->row($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $packingData = $this->getPackingData($id);

            foreach($packingData->itemData as $row):               

                if($packingData->is_final == 1):
                    $batchData = json_decode($row->json_data);
                    foreach($batchData as $batch):
                        $currentStock = $this->checkItemWiseBatchStock(['item_id'=>$row->item_id,'batch_no'=>"General Batch",'location_id'=>$this->RTD_STORE->id]);
                        if($batch->batch_qty > $currentStock->qty):
                            $this->db->trans_rollback();
                            return ['status'=>0,'message'=>'Batch Stock not avalible. You can not delete it.'];
                        endif;
                        $this->remove($this->stockTrans,['ref_id'=>$packingData->id,'trans_ref_id'=>$row->id,'ref_type'=>30]);
                    endforeach;

                    if($packingData->entry_type == "Regular"):
                        /*** UPDATE PRODUCT PACKING QTY ***/
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $row->item_id;
                        $setData['set']['packing_qty'] = 'packing_qty, - '.$row->packing_qty;
                        $this->setValue($setData);
                    endif;

                    /*** UPDATE PACKING QTY IN TRANS CHILD ***/
                    if(!empty($row->trans_child_id)):
                        $setData = array();
                        $setData['tableName'] = $this->transChild;
                        $setData['where']['id'] = $row->trans_child_id;
                        $setData['set']['packing_qty'] = 'packing_qty, - '.$row->packing_qty;
                        $this->setValue($setData);
                    endif;                    

                    if(empty($row->ref_id)):
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $row->box_id;
                        $setData['set']['qty'] = 'qty, + '.$row->total_box;
                        $this->setValue($setData);

                        $this->remove($this->stockTrans,['ref_id'=>$packingData->id,'trans_ref_id'=>$row->id,'ref_type'=>15,'trans_type'=>2]);
                    else:
                        $setData = array();
                        $setData['tableName'] = $this->packingTrans;
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set']['export_qty'] = 'export_qty, - '.$row->total_qty;
                        $this->setValue($setData);
                    endif;
                endif;

                if(!empty($row->trans_child_id)):
                    $this->store($this->transChild,['id'=>$row->trans_child_id,'packing_status'=>0]);
                endif; 

                $this->trash($this->packingTrans,['id'=>$row->id]);
            endforeach;

            $result = $this->trash($this->packingMaster,['id'=>$id],'Packing');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getItemCurrentStock($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as stock_qty";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		return $this->row($data);
	}

    public function getBoxQty($ref_id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = 'SUM(packing_transaction.total_box) as total_box';
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['where']['ref_id'] = $ref_id;
        $queryData['where']['packing_master.is_final'] = 1;
        $result = $this->row($queryData);
        return $result;
    }

    public function packingTransGroupByPackage($packing_id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['where']['packing_transaction.packing_id'] = $packing_id;
        $queryData['group_by'][] = "packing_transaction.package_no";
        $queryData['order_by']['cast(packing_transaction.package_no as unsigned)'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function packingTransByPackage($packing_id,$package_no){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.part_no,item_master.item_alias as alias_name";
        $queryData['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
        $queryData['where']['packing_transaction.packing_id'] = $packing_id;
        $queryData['where']['packing_transaction.package_no'] = $package_no;
        $queryData['order_by']["packing_transaction.item_id"] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function updateItemName($data){
        try{
            $this->db->trans_begin();
            
            if($data['table'] == "packing"):
                $queryData['tableName'] = $this->packingTrans;
                $queryData['select'] = "packing_transaction.*,item_master.part_no,item_master.item_alias as alias,item_master.item_code as itm_code, item_master.item_name as itm_name";
                $queryData['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
                $queryData['where']['packing_transaction.packing_id'] = $data['id'];
                $result = $this->rows($queryData);
                
                foreach($result as $row):
                    $this->store($this->packingTrans,['id'=>$row->id,'item_code'=>$row->itm_code,'item_name'=>$row->itm_name,'item_alias'=>$row->alias]);   
                endforeach;
                
            elseif($data['table'] == "tc"):
                $queryData['tableName'] = $this->transChild;
                $queryData['select'] = "trans_child.*,item_master.part_no,item_master.item_alias as alias,item_master.item_code as itm_code, item_master.item_name as itm_name";
                $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
                $queryData['where']['trans_child.trans_main_id'] = $data['id'];
                $result = $this->rows($queryData);
                
                foreach($result as $row):
                    $this->store($this->transChild,['id'=>$row->id,'item_code'=>$row->itm_code,'item_name'=>$row->itm_name,'item_alias'=>$row->alias]);   
                endforeach;
                
            endif;
            

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return ['status'=>1,'message'=>'Item Name updated successfully.'];
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    //Created By Karmi @04/08/2022
    public function getPackingDataForPrint($id,$item_id){
        $data['tableName'] = $this->packingTrans;
        $data['select'] = "packing_transaction.*,ifnull(party_master.party_code,ifnull(item_party.party_code,'')) as party_code,ifnull(trans_main.trans_no,'') as trans_no,item_master.part_no";
        $data['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $data['leftJoin']['trans_main'] = "packing_transaction.trans_main_id = trans_main.id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
        $data['leftJoin']['party_master as item_party'] = "item_party.id = item_master.party_id";
        $data['where']['packing_master.id'] = $id;
        $data['where']['packing_transaction.item_id'] = $item_id;
        $result = $this->row($data);
        return $result;
    }
    
     //Created By Meghavi @27/08/2022
    public function getItemList($id){
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.*,item_master.item_code,packing_master.total_net_weight,packing_master.total_gross_weight";
        $queryData['leftJoin']['packing_master'] = "packing_transaction.packing_id = packing_master.id";
        $queryData['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
        $queryData['where']['packing_id'] = $id;
        $resultData = $this->rows($queryData); 
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_code.'</td>
                            <td class="text-center">'.$row->box_size.'</td>
                            <td class="text-center">'.$row->qty_per_box.'</td>
                            <td class="text-center">'.$row->total_box.'</td>
                            <td class="text-center">'.$row->total_qty.'</td>
                            <td class="text-center">'.$row->wt_pcs.'</td>
                            <td class="text-center">'.$row->total_net_weight.'</td>
                            <td class="text-center">'.$row->packing_wt.'</td>
                            <td class="text-center">'.$row->wooden_wt.'</td>
                            <td class="text-center">'.$row->total_gross_weight.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="8">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
}
?>