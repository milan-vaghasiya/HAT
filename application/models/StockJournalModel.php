<?php
class StockJournalModel extends MasterModel{
    private $stockJournal = "stock_journal";
    private $stockTrans = "stock_transaction";
    private $itemMaster = "item_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->stockJournal;

        $data['where']['date >= '] = $this->startYearDate;
        $data['where']['date <= '] = $this->endYearDate;

        $data['searchCol'][] = "date";
        $data['searchCol'][] = "rm_name";
        $data['searchCol'][] = "rm_qty";
        $data['searchCol'][] = "fg_name";
        $data['searchCol'][] = "fg_qty";

		$columns =array('','','date','rm_name','rm_qty','fg_name','fg_qty','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->stockJournal,$data,'Stock Journal');

        /*** UPDATE STOCK TRANSACTION DATA ***/
        /* RM PLUS */
        $stockQueryData['id']="";
        $stockQueryData['location_id'] = $data['rm_location_id'];
        $stockQueryData['batch_no'] = $data['rm_batch_no'];
        $stockQueryData['trans_type'] = 1;
        $stockQueryData['item_id'] = $data['rm_item_id'];
        $stockQueryData['qty'] = $data['rm_qty'];
        $stockQueryData['ref_type'] = 14;
        $stockQueryData['ref_id'] = $result['insert_id'];
        $stockQueryData['ref_date'] = $data['date'];
        $stockQueryData['created_by'] = $data['created_by'];
        $this->store($this->stockTrans,$stockQueryData);

        /* RM MINUS */
        $stockQueryData['trans_type'] = 2;
        $stockQueryData['qty'] = "-".$data['rm_qty'];
        $this->store($this->stockTrans,$stockQueryData);

        /* FG PLUS */
        $stockQueryData['id']="";
        $stockQueryData['location_id'] = $data['fg_location_id'];
        $stockQueryData['batch_no'] = $data['fg_batch_no'];
        $stockQueryData['trans_type'] = 1;
        $stockQueryData['item_id'] = $data['fg_item_id'];
        $stockQueryData['qty'] = $data['fg_qty'];
        $stockQueryData['ref_type'] = 14;
        $stockQueryData['ref_id'] = $result['insert_id'];
        $stockQueryData['ref_date'] = $data['date'];
        $stockQueryData['created_by'] = $data['created_by'];
        $this->store($this->stockTrans,$stockQueryData);

        /* FG PLUS IN ITEM MASTER */
        $setData = array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $data['fg_item_id'];
        $setData['set']['qty'] = 'qty, + '.$data['fg_qty'];
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

    public function getStockJournal($id){
        $data['tableName'] = $this->stockJournal;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $SJData = $this->getStockJournal($id); 
        
        $result = $this->trash($this->stockJournal,['id'=>$id],'Stock Journal');
        $this->trash($this->stockTrans,['ref_id'=>$id,'ref_type'=>14],'Stock Journal');

        /* FG UPDATE IN ITEM MASTER */
        $setData = array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $SJData->fg_item_id;
        $setData['set']['qty'] = 'qty, - '.$SJData->fg_qty;
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
	
	/*  Create By : Avruti @27-11-2021 2:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount($item_type=0){
        $data['tableName'] = $this->stockJournal;
		
        return $this->numRows($data);
    }

    public function getStockJournalList_api($limit, $start){
        $data['tableName'] = $this->stockJournal;

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>