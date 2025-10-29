<?php 
class LedgerModel extends MasterModel{
	private $partyMaster = "party_master";

	public function getDTRows($data){
		$data['tableName'] = $this->partyMaster;
		$data['select'] = "party_master.*,group_master.name";
		$data['join']['group_master'] = "group_master.id = party_master.group_id";
		//$data['where']['party_master.party_category'] = 4;
        
		$data['searchCol'][] = "group_name";
		$data['searchCol'][] = "party_name";
		$data['searchCol'][] = "is_gst_applicable";
		$data['searchCol'][] = "hsn_code";
		$data['searchCol'][] = "opening_balance";
		$data['searchCol'][] = "cess_per";

		$columns =array('','','group_name','ledger_name','is_gst_applicable','hsn_code','opening_bal','cess_per');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}

	public function save($data){
		try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data['party_name'],$data['party_category'],$data['id']) > 0):
                $errorMessage['party_name'] = "Ledger name is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $data['opening_balance'] = (!empty($data['opening_balance']))?$data['opening_balance']:0;
				if(empty($data['id'])):
					$data['cl_balance'] = $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
				else:
					$partyData = $this->getLedger($data['id']);
                    $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
                    if($partyData->opening_balance > $data['opening_balance']):
                        $varBalance = $partyData->opening_balance - $data['opening_balance'];
                        $data['cl_balance'] = $partyData->cl_balance - $varBalance;
                    elseif($partyData->opening_balance < $data['opening_balance']):
                        $varBalance = $data['opening_balance'] - $partyData->opening_balance;
                        $data['cl_balance'] = $partyData->cl_balance + $varBalance;
                    else:
                        $data['cl_balance'] = $partyData->cl_balance;
                    endif;
				endif;
				$result = $this->store($this->partyMaster,$data,'Ledger');
			endif;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
        return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
	}

	public function checkDuplicate($name,$party_category,$id=""){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_name'] = $name;
        $data['where']['party_category'] = $party_category;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
            
        return $this->numRows($data);
    }

	public function getLedger($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->partyMaster;
        return $this->row($data);
    }

    public function getLedgerList($groupCode = array()){
        $queryData = array();
        $queryData['tableName'] = $this->partyMaster;
        $queryData['where']['party_category'] = 4;
        if(!empty($groupCode))
            $queryData['where_in']['group_code'] = $groupCode;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getLedgerOnSystemCode($systemCode){
        $queryData = array();
        $queryData['tableName'] = "party_master";
        $queryData['where']['system_code'] = $systemCode;
        $ledger = $this->row($queryData);
        return $ledger;
    }

	public function delete($id){
        try{
            $this->db->trans_begin();
            $ledgerData = $this->getLedger($id);
            if(!empty($ledgerData->system_code)):
                return ['status'=>0,'message'=>'You cannot delete. Because This is default ledger.'];
            endif;

            $result = $this->trash($this->partyMaster,['id'=>$id],'Ledger');

            if($this->db->trans_status() !== FALSE):
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