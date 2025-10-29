<?php
class JournalEntryModel extends MasterModel
{
    private $transMain = "trans_main";
    private $transLedger = "trans_ledger";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->transLedger;
        $data['select'] = 'trans_main.id,trans_ledger.trans_number,trans_ledger.trans_date,party_master.party_name as acc_name,trans_ledger.amount,trans_ledger.remark,trans_ledger.c_or_d';
        
        $data['join']['trans_main'] = "trans_main.id = trans_ledger.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_ledger.vou_acc_id";
        $data['where']['trans_ledger.entry_type'] = 17;
        $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $data['order_by']['trans_ledger.trans_date'] = "DESC";
        $data['order_by']['trans_ledger.id'] = "DESC";

        $data['searchCol'][] = "trans_ledger.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_ledger.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_ledger.amount";

        $columns = array('', '', 'trans_ledger.trans_number', 'trans_ledger.trans_date',  'party_master.party_name', 'trans_ledger.amount', 'trans_ledger.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }


    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
        
            $firstCrKey = array_search("CR",$itemData['cr_dr'],true);
            $firstDrKey = array_search("DR",$itemData['cr_dr'],true);
            
            $saveJv = $this->store($this->transMain,$masterData,"Journal Entry");
            $jvId = (!empty($masterData['id']))?$masterData['id']:$saveJv['insert_id'];
            
            //remove old trans
            $this->transModel->deleteLedgerTrans($jvId);

            //save new trans
            foreach($itemData['cr_dr'] as $key=>$value):
                $transLedgerData = ['id'=>"",'entry_type'=>$masterData['entry_type'],'trans_main_id'=>$jvId,'trans_date'=>$masterData['trans_date'],'trans_number'=>$masterData['trans_number'],'doc_date'=>$masterData['trans_date'],'doc_no'=>$masterData['trans_number'],'c_or_d'=>$value,'remark'=>$itemData['item_remark'][$key],'created_by'=>$masterData['created_by']];
                
                $transLedgerData['vou_acc_id'] = $itemData['acc_id'][$key];
                if($value == "DR"):
                    $transLedgerData['opp_acc_id'] = $itemData['acc_id'][$firstCrKey];
                    $transLedgerData['amount'] = $itemData['debit_amount'][$key];
                else:
                    $transLedgerData['opp_acc_id'] = $itemData['acc_id'][$firstDrKey];
                    $transLedgerData['amount'] = $itemData['credit_amount'][$key];
                endif; 
                $this->transModel->storeTransLedger($transLedgerData);
            endforeach;

            $result = ['status'=>1,'message'=>'Journal Entry saved successfully.','url'=>base_url("journalEntry")];	

            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}		
    }

    public function editJournal($id){
        $queryData = array();
        $queryData['tableName']  = $this->transMain;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);

        $result->ledgerData = $this->getLedgerTrans($id);
        return $result;
    }

    public function getLedgerTrans($id){
        $queryData = array();
        $queryData['tableName']  = $this->transLedger;
        $queryData['select'] = "trans_ledger.*,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_ledger.vou_acc_id";
        $queryData['where']['trans_ledger.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
		try{
            $this->db->trans_begin();

			$result = $this->trash($this->transMain,['id'=>$id],'Journal Entry');
            $this->transModel->deleteLedgerTrans($id);

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
