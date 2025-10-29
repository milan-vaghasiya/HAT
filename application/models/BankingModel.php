<?php
class BankingModel extends MasterModel{
    private $bankingMaster = "banking_master";

    public function getDTRows($data){
        $data['tableName'] = $this->bankingMaster;
        $data['searchCol'][] = "bank_name";
        $data['searchCol'][] = "branch_name";
        $data['searchCol'][] = "ifsc_code";
        $data['searchCol'][] = "address";
		$columns =array('','','bank_name','branch_name','ifsc_code','address');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getBankingDetails($id){
        $data['tableName'] = $this->bankingMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
		return $this->store($this->bankingMaster,$data);
	}

    public function delete($id){
		
        return $this->trash($this->bankingMaster,['id'=>$id]);
    }
	
}