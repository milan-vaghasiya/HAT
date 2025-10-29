<?php
class PackingModel extends MasterModel{
    private $transChild = "trans_child";
    private $itemMaster = "item_master";
    private $packingKit = "packing_kit";
    private $packingMaster = "packing_master";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "packing_master.*,item_master.item_name,item_master.item_code";   
        $data['leftJoin']['item_master'] = "item_master.id = packing_master.item_id";
        
        
        if($data['status'] == 0){
            $data['where']['packing_master.status'] = 0;
        }
        if($data['status'] == 1){
            $data['where_in']['packing_master.status'] = [1,2]; 
            $data['where']['packing_master.packing_date >= '] = $this->startYearDate;
            $data['where']['packing_master.packing_date <= '] = $this->endYearDate;
        }

        $data['searchCol'][] = "DATE_FORMAT(packing_master.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "packing_master.qty";
        $data['searchCol'][] = "packing_master.remark";
        $data['searchCol'][] = "packing_master.status";

		$columns =array('','','packing_master.dispatch_date','item_master.item_code','item_master.item_name','packing_master.qty','','packing_master.packing_date','packing_master.remark','packing_master.status');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPackingData($id){
        $data['tableName'] = $this->packingMaster; 
        $data['select'] = "packing_master.*,item_master.item_name,item_master.item_code,trans_main.trans_prefix,trans_main.trans_no as so_no,trans_main.doc_no,party_master.party_code";   
        $data['leftJoin']['item_master'] = "item_master.id = packing_master.item_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = packing_master.trans_main_id";
        $data['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $data['where']['packing_master.id'] = $id;
        return $this->row($data);
    }

    public function savePacking($data,$productData){ 
        try{
            $this->db->trans_begin();
            $packingData = Array(); 
            if(empty($data['id'])):
                /*** STORE DATA IN PACKING MASTER ***/
                $data['dispatch_date'] = '';
                $data['packing_batches'] = json_encode($productData);
                $result = $this->store($this->packingMaster,$data,'Packing');
                $data['id'] = $result['insert_id'];

                if(!empty($data['trans_child_id'])):
                    $this->store('trans_child',['id'=>$data['trans_child_id'],'packing_status'=>(($data['status'] == 0)?1:2)]);
                endif;
                /* $packingData = $this->getPackingData($data['id']); */
            else: 
                $packingData = $this->getPackingData($data['id']);

                if(!empty($packingData->trans_child_id)):
                    $this->store('trans_child',['id'=>$packingData->trans_child_id,'packing_status'=>0]);
                endif;
                if(!empty($packingData)):
                    if($packingData->status == 1):
                        /*** UPDATE PRODUCT PACKING QTY ***/
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $packingData->item_id;
                        $setData['set']['packing_qty'] = 'packing_qty, - '.$packingData->packing_qty;
                        $this->setValue($setData);
                        
                        /*** UPDATE PACKING QTY IN TRANS CHILD ***/
                        if(!empty($packingData->trans_child_id)):
                            $setData = array();
                            $setData['tableName'] = $this->transChild;
                            $setData['where']['id'] = $packingData->trans_child_id;
                            $setData['set']['packing_qty'] = 'packing_qty, - '.$packingData->packing_qty;
                            $this->setValue($setData);
                        endif;

                        $oldBom =  json_decode($packingData->bom);
                        if(!empty($oldBom)):
                            foreach($oldBom as $row):
                                $setData = array();
                                $setData['tableName'] = $this->itemMaster;
                                $setData['where']['id'] = $row->box_id;
                                $setData['set']['qty'] = 'qty, + '.$row->noof_box;
                                $this->setValue($setData); 

                                /** REMOVE STOCK TRANSACTION **/
                                $this->remove($this->stockTrans,['ref_id'=>$row->box_id,'trans_type'=>2,'ref_type'=>15]);
                            endforeach;
                        endif;
                    endif;
                endif;

                /*** STORE DATA IN PACKING MASTER ***/
                $data['packing_batches'] = json_encode($productData);
                $this->store($this->packingMaster,$data,'Packing');

                if(!empty($data['trans_child_id'])):
                    $this->store('trans_child',['id'=>$data['trans_child_id'],'packing_status'=>(($data['status'] == 0)?1:2)]);
                endif;
            endif;

            if($data['status'] == 1):
                /*** UPDATE PRODUCT PACKING QTY ***/
                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $data['item_id'];
                $setData['set']['packing_qty'] = 'packing_qty, + '.$data['packing_qty'];
                $this->setValue($setData);
                
                /*** UPDATE PACKING QTY IN TRANS CHILD ***/
                if(!empty($data['trans_child_id'])):
                    $setData = array();
                    $setData['tableName'] = $this->transChild;
                    $setData['where']['id'] = $data['trans_child_id'];
                    $setData['set']['packing_qty'] = 'packing_qty, + '.$data['packing_qty'];
                    $this->setValue($setData);
                endif;
                
                /*** UPDATE PRODUCT STOCK TRANSACTION DATA ***/
                $batch_qty = array(); $batch_no = array(); $location_id = array();
                $batchQty = (!empty($productData['batch_qty'])) ? $productData['batch_qty'] : Array();
                $batchNo = (!empty($productData['batch_number'])) ? $productData['batch_number'] : Array();
                $locationId = (!empty($productData['location'])) ? $productData['location'] : Array();
                
                foreach($batchNo as $ak=>$av):
                    if(!empty($batchQty[$ak])):
                        $batch_qty[] = $batchQty[$ak];
                        $batch_no[] = $av;
                        $location_id[] = $locationId[$ak];
                    endif;
                endforeach;

                foreach($batch_qty as $bk=>$bv):
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id'] = $location_id[$bk];
                    if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                    $stockQueryData['trans_type'] = 2;
                    $stockQueryData['item_id'] = $data['item_id'];
                    $stockQueryData['qty'] = '-'.$bv;
                    $stockQueryData['ref_type'] = 16;
                    $stockQueryData['ref_id'] = $data['id'];
                    $stockQueryData['ref_date'] = $data['packing_date'];    
                    $stockQueryData['created_by'] = $this->session->userdata('loginId');
                    $stransResult = $this->store($this->stockTrans,$stockQueryData);
                
                    /*** PRODUCT STOCK TRANSFER READY TO DISPATCH ***/
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id'] = $this->RTD_STORE->id;
                    //if($data['packing_type'] == "Export"){ $stockQueryData['batch_no'] = $data['packing_type']; }
                    $stockQueryData['trans_type'] = 1;
                    $stockQueryData['item_id'] = $data['item_id'];
                    $stockQueryData['qty'] = $bv;
                    $stockQueryData['ref_type'] = 16;
                    $stockQueryData['ref_id'] = $data['id'];
                    $stockQueryData['ref_date'] = $data['packing_date']; 
                    if(!empty($batch_no[$bk])){$stockQueryData['ref_batch'] = $batch_no[$bk];}   
                    $stockQueryData['created_by'] = $this->session->userdata('loginId');
                    $this->store($this->stockTrans,$stockQueryData);
                endforeach;

                /*** UPDATE PACKING MATERIAL STOCK ***/
                $bom = json_decode($data['bom']);
                foreach($bom as $row):
                    $setData = array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->box_id;
                    $setData['set']['qty'] = 'qty, - '.$row->noof_box;
                    $this->setValue($setData);

                    /*** UPDATE STOCK TRANSACTION DATA ***/
                    $stockQueryData = array();
                    $stockQueryData['id']="";
                    $stockQueryData['location_id']=$this->PKG_STORE->id;
                    $stockQueryData['batch_no'] = "General Batch";
                    $stockQueryData['trans_type'] = 2;
                    $stockQueryData['item_id'] = $row->box_id;
                    $stockQueryData['qty'] = "-".$row->noof_box;
                    $stockQueryData['ref_type'] = 15;
                    $stockQueryData['ref_id'] = $data['id'];
                    $stockQueryData['ref_date'] = $data['packing_date'];
                    $stockQueryData['created_by'] = $this->session->userdata('loginId');
                    $this->store($this->stockTrans,$stockQueryData);
                endforeach;
            endif;

            $result = ['status'=>1,'message'=>"Packing quantity updated successfully."];
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
            $packingData = $this->getPackingData($id);

            if($packingData->status == 1):
                return ['status'=>0,'message'=>"The packing is done. you can not delete it."];
            endif;

            if(!empty($packingData->trans_child_id)):
                $this->store('trans_child',['id'=>$packingData->trans_child_id,'packing_status'=>0]);
            endif;
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

    public function batchWiseItemStock($data){ 
        $i=1;$tbody="";
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
                        $batch_no = array();
						foreach($result as $row){
                            $cl_stock = 0;
                            $batch_no = (!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no'];
                            $batch_qty = (!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty'];
                            $location_id = (!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id'];
                            if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):     
                                $qty = 0; $cl_stock = floatVal($row->qty);
                                if(floatVal($cl_stock) > 0 && $this->PROD_STORE->id == $batch->id):
                                    $tbody .= '<tr>';
                                        $tbody .= '<td class="text-center">'.$i.'</td>';
                                        $tbody .= '<td>['.$lData['store_name'].'] '.$batch->location.'</td>';
                                        $tbody .= '<td>'.$row->batch_no.'</td>';
                                        $tbody .= '<td>'.floatVal($cl_stock).'</td>';
                                        $tbody .= '<td>
                                            <input type="text" name="batch_qty[]" class="form-control floatOnly batchQty bQty'.$i.'" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                                            <input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                                            <input type="hidden" name="location[]" id="location'.$i.'" value="'.$batch->id.'" />
                                            <div class="error batch_qty'.$i.'"></div>
                                        </td>';
                                    $tbody .= '</tr>';
                                    $i++;
                                endif;
                            endif;
						}
					}
				endforeach;
			}
		} else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        
        return ['status'=>1,'batchData'=>$tbody];
    }

    public function getItemCurrentStock($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as stock_qty";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		return $this->row($data);
	}

    /* Packing Bom */
    public function getBomDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_master.item_type'] = 1;
        $data['searchCol'][] = "trans_child.item_code";
		$columns =array('','','trans_child.item_code','');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getConsumable($category_id){
        $data['tableName'] = $this->itemMaster;    
        $data['where']['item_master.item_type'] = 2;
        $data['where']['item_master.category_id'] = $category_id;
        return $this->rows($data);
    }

    public function getPackingBom($item_id){
        $data['tableName'] = $this->packingKit;    
        $data['where']['packing_kit.item_id'] = $item_id;
        return $this->row($data);
    }

    public function saveBom($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->packingKit,$data,"Paking Bom");
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getSalesOrderNoListForPacking(){
        $queryData = array();
        $queryData['tableName'] = "trans_child";
        $queryData['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,party_master.party_code";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $queryData['where']['trans_child.packing_status'] = 0;
        $queryData['where']['trans_child.trans_status'] = 0;
        $queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_main.sales_type'] = 2;
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

    public function getPackingDataPdf($trans_no){
        $data['tableName'] = $this->packingMaster; 
        $data['select'] = "packing_master.*,trans_child.item_name,trans_child.item_code,trans_child.item_alias";   
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_master.trans_child_id";
        $data['where']['packing_master.trans_no'] = $trans_no;
        return $this->rows($data);
    }
	
	/*  Create By : Avruti @29-11-2021 01:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->packingMaster;
        return $this->numRows($data);
    }

    public function getPackingList_api($limit, $start,$status){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "packing_master.*,item_master.item_name,item_master.item_code";   
        $data['leftJoin']['item_master'] = "item_master.id = packing_master.item_id";
        if($status == 0){$data['where']['packing_master.status'] = 0; $data['where']['packing_master.ref_id != '] = 0;}
        if($status == 1){$data['where']['packing_master.status'] = 1; $data['where']['packing_master.ref_id != '] = 0;}
        if($status == 2){$data['where']['packing_master.ref_id'] = 0;}

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>