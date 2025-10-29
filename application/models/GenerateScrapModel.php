<?php
class GenerateScrapModel extends MasterModel{
    private $stockTrans = "stock_transaction";
    private $itemMaster = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "stock_transaction.id,stock_transaction.item_id,stock_transaction.qty,DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y') as ref_date,item_master.item_name,stock_transaction.remark";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['where']['stock_transaction.ref_type'] = 17;

        $data['searchCol'][] = "DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y')";
        $data['serachCol'][] = "item_master.item_name";
        $data['serachCol'][] = "stock_transaction.qty";
        $data['serachCol'][] = "stock_transaction.remark";
        
		$columns =array('','',"DATE_FORMAT(stock_transaction.ref_date,'%d-%m-%Y')",'item_master.item_name','stock_transaction.qty','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['id'])):
                $transData = $this->getScrap($data['id']);
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $transData->item_id;
                $setData['set']['qty'] = 'qty, - '.$transData->qty;
                $setData['set']['packing_qty'] = 'packing_qty, - '.$transData->qty;
                $this->setValue($setData);                
            endif;

            $result = $this->store($this->stockTrans,$data,'Scrape');
            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $data['item_id'];
            $setData['set']['qty'] = 'qty, + '.$data['qty'];
            $setData['set']['packing_qty'] = 'packing_qty, + '.$data['qty'];
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getScrap($id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $transData = $this->getScrap($id);

            if(!empty($transData)):
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $transData->item_id;
                $setData['set']['qty'] = 'qty, - '.$transData->qty;
                $setData['set']['packing_qty'] = 'packing_qty, - '.$transData->qty;
                $this->setValue($setData); 
            endif;

            $result = $this->remove($this->stockTrans,['id'=>$id],'Scrap');

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