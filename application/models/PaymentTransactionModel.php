<?php 
class PaymentTransactionModel extends MasterModel
{
	private $paymentTran = "payment_transaction";
	private $transMain = "trans_main";

	public function getDtRows($data){
		$data['tableName'] = $this->paymentTran;
		$data['select'] = "payment_transaction.*,party_master.party_name";
		$data['join']['party_master'] = "party_master.id = payment_transaction.party_id";
		
		$data['searchCol'][] = "party_master.party_name";
		$data['searchCol'][] = "payment_transaction.invoice_no";
		$data['searchCol'][] = "payment_transaction.tran_mode";
		$data['searchCol'][] = "payment_transaction.bank_ledger_id";
		$data['searchCol'][] = "payment_transaction.total_amount";

		$columns = array('','','party_name','invoice_no','tran_mode','bank_ledger_id','total_amount');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);

	}

	public function getPartyList(){
		$data['tableName'] = 'party_master';
		return $this->rows($data);
	}

	public function getParty($id){
		$data['where_in']['id'] = $id;
		$data['tableName'] = 'party_master';
		return $this->rows($data);
	}

	public function getPartyRefNo($party_id){
		$data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*";
		$data['where']['trans_main.party_id'] = $party_id;
        $data['where_in']['trans_main.entry_type'] = [6,7,8];
		return $this->rows($data);
	}

	public function getRefNo($id){
		$data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.trans_prefix,trans_main.trans_no";
		$data['where_in']['trans_main.id'] = $id;
		return $this->rows($data);
	}

	public function save($data){
		try{
            $this->db->trans_begin();
		$result = $this->store($this->paymentTran,$data,'Payment Transaction');
		if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
	}

	public function getPayment($id){
		$data['where']['id'] = $id;
		$data['tableName'] = $this->paymentTran;
		return $this->row($data);
	}

	public function delete($id){
		try{
            $this->db->trans_begin();
		$result = $this->trash($this->paymentTran,['id'=>$id],'Record');
		if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
	}

	public function getCheckStatus(){

	}
}
?>