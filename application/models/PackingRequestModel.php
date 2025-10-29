<?php
 
class PackingRequestModel extends MasterModel {
    private $stockTransaction = "stock_transaction";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = "stock_transaction.*,SUM(stock_transaction.qty) as current_stock,item_master.item_name,item_master.item_code,location_master.location,location_master.store_name";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $data['where']['location_id'] = $this->PROD_STORE->id;
        $data['where']['item_master.item_type'] = 1;
        $data['group_by'][] = "stock_transaction.item_id,stock_transaction.batch_no";
        $data['having'][] = 'SUM(stock_transaction.qty) > 0';
    
        $data['searchCol'][] = "CONCAT(item_master.item_code,item_master.item_name)";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "stock_transaction.batch_no";
        $data['searchCol'][] = "stock_transaction.qty";
		$columns =array('','','item_master.item_name','location_master.location','stock_transaction.batch_no','stock_transaction.qty');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getRequestedRows($data){ 
        $data['tableName'] = $this->packingTrans;
        $data['select'] = "packing_transaction.*,packing_master.trans_number,packing_master.entry_date,packing_master.entry_type";
        $data['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
      
		$data['order_by']['packing_master.entry_date'] = "DESC";

        if($data['status'] == 0){
            $data['where']['packing_master.packing_by'] = 1;
            $data['where']['packing_master.entry_type'] = 'New Request';
        }
        if($data['status'] == 1){
            $data['where']['packing_master.packing_by'] = 1;
            $data['where']['packing_master.entry_type !='] = 'New Request';
        }
        $data['searchCol'][] = "packing_master.entry_date";
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "packing_transaction.item_name";
        $data['searchCol'][] = "packing_transaction.request_qty";

		$columns =array('','','packing_master.entry_date','packing_master.trans_number','packing_transaction.item_name','packing_transaction.request_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
 
    // public function getPackingRequset($id){
    //     $data['tableName'] = $this->packingTrans;
    //     $data['select'] = "packing_transaction.*,packing_master.trans_number,packing_master.entry_date,packing_master.delivery_date,packing_master.trans_prefix as packing_prefix, packing_master.trans_no as packing_no ,packing_master.remark,trans_main.trans_prefix,trans_main.trans_no";
    //     $data['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
    //     $data['leftJoin']['trans_main'] = "trans_main.id = packing_transaction.trans_main_id";
    //     $data['where']['packing_transaction.id'] = $id;
    //     return $this->row($data);
    // }

    public function getPackingReqData($id){
        $queryData = array();
        $queryData['tableName'] = $this->packingMaster;
        $queryData['select'] = "packing_master.*,packing_master.trans_prefix as packing_prefix, packing_master.trans_no as packing_no";
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);
        $result->itemData = $this->getPackingReqTransData($id);
        return $result;
    }

    public function getPackingReqTransData($id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = 'packing_transaction.*,trans_main.trans_prefix,trans_main.trans_no';
        $queryData['leftJoin']['trans_main'] = "trans_main.id = packing_transaction.trans_main_id";
        $queryData['where']['packing_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getSalesOrderList($id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_code,trans_child.item_name,trans_child.item_alias,trans_child.trans_status,trans_child.qty,trans_main.trans_prefix,trans_main.trans_no,trans_child.cod_date as delivery_date,trans_child.dispatch_qty';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 4;
        $data['where']['trans_child.trans_status'] = 0;
        $data['where']['trans_child.item_id'] = $id;
        $data['group_by'][] = "trans_main.trans_no";
        $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $result= $this->rows($data);
        return $result;
    }

    public function savePackingRequest($data){ 
        try{
            $this->db->trans_begin();   
            $masterData = [
				'id' => $data['packing_id'],
				'entry_date' => $data['req_date'],
				'trans_no' => $data['trans_no'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_number' => $data['trans_number'],
				'packing_by' => 1,
				'entry_type' => "New Request",
				'remark' => $data['remark'],
                'created_by' => $this->loginId
			];
            $result = $this->store($this->packingMaster,$masterData);
            $packing_id = (!empty($data['packing_id']))?$data['packing_id']:$result['insert_id'];
                if(!empty($packing_id)){
                    $data['tableName'] = $this->packingTrans;
                    $data['select'] = "id";
                    $data['where']['packing_id'] = $packing_id;
                    $ptransIdArray = $this->rows($data);
                
                    foreach($ptransIdArray as $key=>$value):
                        if(!in_array($value->id,$data['trans_id'])):		
                            $this->trash($this->packingTrans,['id'=>$value->id]);
                        endif;
                    endforeach;
                }
            foreach($data['item_id'] as $key=>$value):
                $transData = [
                                'id' => $data['trans_id'][$key],
                                'packing_id' =>$packing_id,
                                'trans_main_id' => $data['trans_main_id'][$key],
                                'trans_child_id' => $data['trans_child_id'][$key],
                                'item_id' => $value,
                                'request_qty' => $data['request_qty'][$key],
                                'delivery_date' => $data['delivery_date'][$key],
                                'item_name' => $data['item_name'][$key],
                                'item_code' => $data['item_code'][$key],
                                'item_alias' => $data['item_alias'][$key],
                                'created_by'  => $this->session->userdata('loginId')
                            ];
                $this->store($this->packingTrans,$transData);
            endforeach;
          
            $result = ['status'=>1,'message'=>'Purchase Request send successfully.'];
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
            $packingData = $this->getPackingReqData($id);

            foreach($packingData->itemData as $row): 
                $this->trash($this->packingTrans,['id'=>$row->id]);
            endforeach;
            $result = $this->trash($this->packingMaster,['id'=>$id],'Request');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
}

?>