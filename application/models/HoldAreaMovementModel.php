<?php
class HoldAreaMovementModel extends MasterModel{
    private $productionTrans = "production_transaction";
    private $productionLog = "production_log";

	
    public function getDTRows($data){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "production_transaction.*,process_master.process_name,item_master.item_name,job_card.job_no,job_card.job_prefix,party_master.party_name";
        $data['leftJoin']['job_card'] = "job_card.id = production_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_transaction.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_transaction.process_id";
        $data['leftJoin']['party_master'] = "party_master.id = production_transaction.vendor_id";
        $data['where_in']['production_transaction.entry_type'] ='2,3';
        
        $data['searchCol'][] = "DATE_FORMAT(production_transaction.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "production_transaction.in_qty";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns = array('', '','production_transaction.entry_date', 'job_card.job_no', 'item_master.item_name','process_master.process_name',  'party_master.party_name', 'production_transaction.in_qty', '', '');
        if (isset($data['order'])) {
            
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }
    
    public function getOutwardQtyFrmHLD($trans_ref_id,$entry_type=[1,4]){
        $data['tableName'] = $this->productionLog;
        $data['select'] = "SUM(ok_qty) as ok_qty,SUM(rej_qty) as rej_qty";
        $data['leftJoin']['production_transaction'] = "production_log.ref_id = production_transaction.id";
        $data['where_in']['production_transaction.trans_ref_id'] =$trans_ref_id;
        $data['where_in']['production_transaction.entry_type'] =$entry_type;
        return $this->row($data);
    }

    public function getHoldAreaTRans($id){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "production_transaction.*";
        $data['where']['production_transaction.id'] =$id;
        return $this->row($data);
    }
   
}
?>