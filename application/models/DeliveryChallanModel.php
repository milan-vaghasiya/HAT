<?php
class DeliveryChallanModel extends MasterModel{
    private $deliveryChallan = "delivery_challan";
    private $deliveryTrans = "delivery_transaction";
    private $productMaster = "item_master";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $orderMaster = "sales_order";
    private $orderTrans = "sales_order_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $grnTable = "grn_master";
    private $grnItemTable = "grn_transaction";
    private $packingTrans = "packing_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,party_master.party_code,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $data['where']['trans_child.entry_type'] = 5;
        if($data['status'] == 1) { 
            $data['where']['trans_child.trans_status'] = 1; 
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        } 
        else { $data['where']['trans_child.trans_status != '] = 1; }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
		$data['group_by'][]='trans_child.trans_main_id';

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_code";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','party_master.party_code');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}

		return $this->pagingRows($data);
    }
 
    public function save($masterData, $itemData){ //print_r($masterData); print_r($itemData); exit;
        try{
            $this->db->trans_begin();
            $dc_id = $masterData['id'];
            if(empty($dc_id)):
                /** Insert Delivery Challan Master Data **/
                $dcSaved = $this->store($this->transMain,$masterData);
                $dc_id = $dcSaved['insert_id'];
			    $result = ['status'=>1,'message'=>'Delivery Challan saved successfully.','url'=>base_url("deliveryChallan")];
            else:
                $queryData['tableName'] = $this->transChild;
                $queryData['where']['trans_main_id'] = $dc_id;
                $queryData['where']['entry_type'] = 5;
                $challanData = $this->rows($queryData);
                
                foreach($challanData as $row):
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
                    
                    if(!empty($row->ref_id) && $row->from_entry_type == 0):
                        $setData = Array();
                        $setData['tableName'] = "grn_transaction";
                        $setData['where']['id'] = $row->ref_id;
                        $setData['set']['dc_qty'] = 'dc_qty, - '.$row->qty.'-'.$row->rej_qty;
                        $this->setValue($setData);
                    endif;

                    if(!empty($row->grn_data)):
                        $grnData = json_decode($row->grn_data);
                        foreach($grnData as $grnRow):
                            $grnRow = json_decode($grnRow);
                            $setData = Array();
                            $setData['tableName'] = "grn_transaction";
                            $setData['where']['id'] = $grnRow->grn_trans_id;
                            $setData['set']['remaining_qty'] = 'remaining_qty, + '.$grnRow->grn_qty;
                            $this->setValue($setData);
                        endforeach;
                    endif;

                    if(!empty($row->ref_id) && !empty($row->from_entry_type)):
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
                        $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>4]);
                    endif;

                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->transChild,['id'=>$row->id]);
                    endif;

                    
                endforeach;
                
                /** Update Delivery Challan Master Data **/
                $dcSaved = $this->store($this->transMain,$masterData);			
                
                $result = ['status'=>1,'message'=>'Delivery Challan updated successfully.','url'=>base_url("deliveryChallan")];
            endif;

            foreach($itemData['item_id'] as $key=>$value):
                
                $batch_qty = array(); $batch_no = array(); $tc_no = array(); $ref_batch = array(); $location_id = array();$packing_trans_id = array();
                $batch_qty = explode(",",$itemData['batch_qty'][$key]);
                $batch_no = explode(",",$itemData['batch_no'][$key]);
                $tc_no = explode(",",$itemData['tc_no'][$key]);
                $ref_batch = explode(",",$itemData['ref_batch'][$key]);
				$location_id = explode(",",$itemData['location_id'][$key]);
                $packing_trans_id = explode(",",$itemData['packing_trans_id'][$key]);

                $transData = [
                    'id' => $itemData['id'][$key],
                    'entry_type' => $masterData['entry_type'],
                    'trans_main_id' => $dc_id,
                    'from_entry_type' => $itemData['from_entry_type'][$key],
                    'ref_id' => $itemData['ref_id'][$key],
                    'stock_eff' => $itemData['stock_eff'][$key],
                    'item_id' => $value,
                    'item_name' => $itemData['item_name'][$key],
                    'item_type' => $itemData['item_type'][$key],
                    'item_code' => $itemData['item_code'][$key],
                    'item_desc' => $itemData['item_desc'][$key],
                    'hsn_code' => $itemData['hsn_code'][$key],
                    'gst_per' => $itemData['gst_per'][$key],
                    'price' => $itemData['price'][$key],
                    'unit_id' => $itemData['unit_id'][$key],
                    'unit_name' => $itemData['unit_name'][$key],
                    'qty' => $itemData['qty'][$key],
                    'rej_qty' => $itemData['rej_qty'][$key],
                    'location_id' => implode(",",$location_id),
                    'batch_no' => implode(",",$batch_no),
                    'tc_no' => implode(",",$tc_no),
					'material_grade' => implode(",",$ref_batch),
                    'batch_qty' => implode(",",$batch_qty),
                    'rev_no' => implode(",",$packing_trans_id),
                    'item_remark' => $itemData['item_remark'][$key],
                    'grn_data' => $itemData['grn_data'][$key],
                    'created_by' => $masterData['created_by']
                ];

                /** Insert Record in Delivery Transaction **/
                $saveDCTrans = $this->store($this->transChild,$transData);
                $refID = (empty($itemData['id'][$key]))?$saveDCTrans['insert_id']:$itemData['id'][$key];

                if(!empty($itemData['grn_data'][$key]) && $masterData['order_type'] == 2):                    
                    $grnData = json_decode($itemData['grn_data'][$key]);
                    foreach($grnData as $row):
                        $row = json_decode($row);
                        $setData = Array();
                        $setData['tableName'] = "grn_transaction";
                        $setData['where']['id'] = $row->grn_trans_id;
                        $setData['set']['remaining_qty'] = 'remaining_qty, - '.$row->grn_qty;
                        $this->setValue($setData);
                    endforeach;
                endif;

                if(!empty($itemData['ref_id'][$key]) && $itemData['from_entry_type'][$key] != '0'):
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

                if(!empty($itemData['ref_id'][$key]) && $itemData['from_entry_type'][$key] == 0):
                    $setData = Array();
                    $setData['tableName'] = "grn_transaction";
                    $setData['where']['id'] = $itemData['ref_id'][$key];
                    $setData['set']['dc_qty'] = 'dc_qty, + '.$itemData['qty'][$key].'+'.$itemData['rej_qty'][$key];
                    $this->setValue($setData);
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
                
                if( $itemData['stock_eff'][$key] == 1):
                    /** Update Item Stock **/
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $itemData['item_id'][$key];
                    $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
                    $setData['set']['packing_qty'] = 'packing_qty, - '.$itemData['qty'][$key];
                    $this->setValue($setData);

                    /*** UPDATE STOCK TRANSACTION DATA ***/
                    foreach($batch_qty as $bk=>$bv):
                        $stockQueryData['id']="";
                        $stockQueryData['location_id']=$location_id[$bk];
                        if(!empty($batch_no[$bk])){$stockQueryData['batch_no'] = $batch_no[$bk];}
                        if(!empty($tc_no[$bk])){$stockQueryData['tc_no'] = $tc_no[$bk];}
                        $stockQueryData['trans_type']=2;
                        $stockQueryData['item_id']=$itemData['item_id'][$key];
                        $stockQueryData['qty'] = "-".$bv;
                        $stockQueryData['ref_type']=4;
                        $stockQueryData['ref_id']=$refID;
                        $stockQueryData['ref_no']=getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                        $stockQueryData['ref_date']=$masterData['trans_date'];
                        $stockQueryData['created_by']=$this->loginID;
                        $this->store($this->stockTrans,$stockQueryData);
                    endforeach;
                endif;
            endforeach;
            
            if(!empty($masterData['ref_id'])):
                $refIds = explode(",",$masterData['ref_id']);
                foreach($refIds as $key=>$value):
                    $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
                    endif;
                endforeach;
            endif;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;

		    endif;
	    }catch(\Exception $e){
		    $this->db->trans_rollback();
		    return ['status'=>1,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }
    }

    public function challanTransRow($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $queryData['where']['entry_type'] = 5;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getChallanTransactions($id);
        return $challanData;
    }
	
	public function getChallanTransactions($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,IFNULL(so_trans.grn_data,"") as wo_no,so_trans.material_grade,so_trans.trans_main_id,trans_main.doc_no,trans_main.doc_date';
        $queryData['leftJoin']['trans_child so_trans'] = 'so_trans.id = trans_child.ref_id AND trans_child.ref_id != 0';
        $queryData['leftJoin']['trans_main'] = 'trans_main.id = so_trans.trans_main_id';
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $queryData['where']['trans_child.entry_type'] = 5;
        return $this->rows($queryData);
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getChallan($id);
            foreach($transData->itemData as $row):
                if(!empty($row->grn_data)):
                    $grnData = json_decode($row->grn_data);
                    foreach($grnData as $grnRow):
                        $grnRow = json_decode($grnRow);
                        $setData = Array();
                        $setData['tableName'] = "grn_transaction";
                        $setData['where']['id'] = $grnRow->grn_trans_id;
                        $setData['set']['remaining_qty'] = 'remaining_qty, + '.$grnRow->grn_qty;
                        $this->setValue($setData);
                    endforeach;
                endif;

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
                    $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>4]);
                endif;
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;

            if(!empty($transData->ref_id)):
                $refIds = explode(",",$transData->ref_id);
                foreach($refIds as $key=>$value):
                    $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                    if(empty($pendingItems)):
                        $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                    endif;
                endforeach;
            endif;
            //return $this->trash($this->transMain,['id'=>$id],'Delivery Challan');
            $result = $this->trash($this->transMain,['id'=>$id],'Delivery Challan');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;

            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>1,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }
    }

    public function getPartyChallans($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_prefix,trans_no,trans_date";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['entry_type'] = 5;
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
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="3">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getChallanItems($transIds){
        $data['tableName'] = $this->transChild;        
        $data['where']['entry_type'] = 5;
        $data['where_in']['trans_main_id'] = $transIds;
        return $this->rows($data);
    }

    public function checkChallanPendingStatus($id){
        $data['select'] = "COUNT(trans_status) as orderStatus";
        $data['where']['trans_main_id'] = $id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 5;
        $data['tableName'] = $this->transChild;
        return $this->specificRow($data)->orderStatus;
    }    

    public function getPackedItemQty($item_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
        $queryData['where']['location_id'] = $this->RTD_STORE->id;
        return $this->row($queryData);
    }

    public function getItemList($id){        
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        $queryData['where']['trans_child.entry_type'] = 5;
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
                            
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    //Created By Karmi @16/04/2022
    public function getBackPrintForChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.rej_qty,grn_transaction.qty as grnQty,grn_transaction.dc_qty';
        $queryData['join']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['join']['grn_transaction'] = "trans_child.ref_id = grn_transaction.id";
        $queryData['where']['trans_main.id'] = $id;
        $queryData['where']['trans_main.entry_type'] = 5;
        $challanData = $this->rows($queryData);
        return $challanData;
    }
	
	public function getChallanWiseInv($challan_id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.trans_no, trans_main.trans_prefix';
	    $queryData['customWhere'][] = 'find_in_set("'.$challan_id.'", trans_main.ref_id)';
        return $this->rows($queryData);
    }
	
	public function getChallanForTc($id){
        $queryData['tableName'] = $this->transMain;
		$queryData['select'] = 'trans_main.id,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.ref_id,soMain.doc_no';
        $queryData['leftJoin']['trans_main as soMain'] = 'soMain.id = trans_main.ref_id AND trans_main.from_entry_type = 4';
		$queryData['where_in']['trans_main.id'] = $id;
        $queryData['where']['trans_main.entry_type'] = 5;
        $challanData = $this->rows($queryData);
		
        return $challanData;
    }
	
	/*  Create By : Avruti @29-11-2021 01:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->transChild;
        $data['where']['trans_child.entry_type'] = 5;
        return $this->numRows($data);
    }

    public function getDeliveryChallanList_api($limit, $start,$status){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 5;
        if($status == 1) { $data['where']['trans_child.trans_status'] = 1; } 
        else { $data['where']['trans_child.trans_status != '] = 1; }
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
		$data['group_by'][]='trans_child.trans_main_id';

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>