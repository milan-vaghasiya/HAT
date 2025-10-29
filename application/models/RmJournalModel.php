<?php
class RmJournalModel extends MasterModel{
    private $stockTrans = "stock_transaction";
    private $itemMaster = "item_master";
    private $locationMaster = "location_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = 'stock_transaction.*,item_master.item_name,location_master.store_name,location_master.location';
        $data['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$data['where']['stock_transaction.ref_batch'] = "RMOS/22-23";
		$data['where']['stock_transaction.ref_type'] = -1;

        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "location_master.store_name";
        $data['searchCol'][] = "location_master.location";
        $data['searchCol'][] = "stock_transaction.qty";

		$columns =array('','','item_master.item_name','location_master.store_name','location_master.location','stock_transaction.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        
            $stockQueryData['id']="";
            $stockQueryData['location_id'] = $data['location_id'];
            $stockQueryData['batch_no'] = $data['batch_no'];
            $stockQueryData['trans_type'] = 1;
            $stockQueryData['item_id'] = $data['item_id'];
            $stockQueryData['qty'] = $data['qty'];
            $stockQueryData['ref_type'] = -1;
            $stockQueryData['ref_id'] = "";
            $stockQueryData['ref_date'] = $data['ref_date'];
            $stockQueryData['ref_batch'] = "RMOS/22-23";
            $stockQueryData['created_by'] = $data['created_by'];

        //print_r($stockQueryData);exit;
        $result = $this->store($this->stockTrans,$stockQueryData);

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
            $result = $this->trash($this->stockTrans,['id'=>$id],'RM Journal');
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