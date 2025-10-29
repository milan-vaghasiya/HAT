<?php 
class JobWorkInspectionModel extends MasterModel{
    private $jobOutward = "job_outward";
	private $jobTrans = "job_transaction";
	private $production_transaction = "production_transaction";
    private $jobCard = "job_card";
    private $ic_inspection = "ic_inspection";

    //Changed By Karmi @30/07/2022
    public function getDTRows($data){
        $data['tableName'] = $this->production_transaction;
        $data['select'] = "production_transaction.*,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_code,process_master.process_name";
        $data['join']['party_master'] = "party_master.id = production_transaction.vendor_id";
        $data['join']['item_master'] =  "item_master.id = production_transaction.product_id";
        $data['join']['process_master'] =  "process_master.id = production_transaction.process_id";
        $data['join']['job_card'] =  "job_card.id = production_transaction.job_card_id";
        $data['where']['production_transaction.vendor_id !='] = 0;
        $data['where_in']['production_transaction.entry_type'] = ['1,2'];
        if(!empty($data['job_id']))
            $data['where']['production_transaction.job_card_id'] = $data['job_id'];

        $data['where']['production_transaction.entry_date >= '] = $this->startYearDate;
        $data['where']['production_transaction.entry_date <= '] = $this->endYearDate;
        $data['order_by']['production_transaction.entry_date'] = 'DESC';
        
        $data['searchCol'][] = "DATE_FORMAT(production_transaction.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "production_transaction.challan_no";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "production_transaction.charge_no";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "production_transaction.in_qty";
        $data['searchCol'][] = "production_transaction.out_qty";
        
		$columns = array('','','DATE_FORMAT(production_transaction.entry_date,"%d-%m-%Y")','production_transaction.challan_no','CONCAT(job_card.job_prefix,job_card.job_no)','party_master.party_name','item_master.item_code','production_transaction.charge_no','process_master.process_name','production_transaction.in_qty','production_transaction.out_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result =  $this->pagingRows($data);
        return $result;
    }

    public function jobCardNoList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix';
        $data['where']['job_card.job_category'] = 1;
        return $this->rows($data); 
    }

    //Changed By Karmi @30/07/2022
    public function getJobWorkData($id){
        $data['tableName'] = $this->production_transaction;
        $data['select'] = "production_transaction.*,job_card.job_no,job_card.job_prefix";
        $data['join']['job_card'] = "job_card.id = production_transaction.job_card_id";
        $data['where']['production_transaction.id'] = $id;
        return $this->row($data); 
    }

    //Changed By Karmi @30/07/2022
    public function getInInspection($id){
		$data['tableName'] = $this->ic_inspection;
		$data['select'] = "ic_inspection.id as inpect_id, ic_inspection.sampling_qty, ic_inspection.grn_id, ic_inspection.grn_trans_id, ic_inspection.party_id, ic_inspection.item_id, ic_inspection.param_count, ic_inspection.result, ic_inspection.observation_sample, party_master.party_name, item_master.item_code, production_transaction.entry_date, production_transaction.issue_batch_no, production_transaction.challan_no, production_transaction.charge_no, production_transaction.in_qty, production_transaction.out_qty, process_master.process_name";
        $data['leftJoin']['party_master'] = "party_master.id = ic_inspection.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = ic_inspection.item_id";
        $data['leftJoin']['production_transaction'] = "production_transaction.id = ic_inspection.grn_trans_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_transaction.process_id";
        $data['where']['ic_inspection.grn_trans_id'] = $id;       
        $data['where']['ic_inspection.trans_type'] = 1;
		return $this->row($data);
	}

    public function saveInInspection($data){
        try{
            $this->db->trans_begin();
		$result = $this->store($this->ic_inspection,$data,'Incoming Inspection');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }	
	}
	
	/*  Create By : Avruti @29-11-2021 12:10 AM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){        
        $data['tableName'] = $this->jobTrans;
        return $this->numRows($data);
    }

    public function getJobWorkInspectionList_api($limit, $start){
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_code,process_master.process_name";
        $data['join']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $data['join']['item_master'] =  "item_master.id = job_transaction.product_id";
        $data['join']['process_master'] =  "process_master.id = job_transaction.process_id";
        $data['join']['job_card'] =  "job_card.id = job_transaction.job_card_id";
        $data['where']['job_transaction.vendor_id !='] = 0;
        if(!empty($data['job_id']))
            $data['where']['job_transaction.job_card_id'] = $data['job_id'];

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>