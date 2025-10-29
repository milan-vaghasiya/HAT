<?php
class CustomerComplaintsModel extends MasterModel{
    private $customerComplaints = "customer_complaints";
    private $transMain = "trans_main";

    public function getDTRows($data){
        $data['tableName'] = $this->customerComplaints;
        $data['select'] = "customer_complaints.*,party_master.party_name,item_master.full_name,trans_main.trans_prefix as so_prefix,trans_main.trans_no as so_no,trans_main.doc_no as so_doc_no,trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "customer_complaints.party_id = party_master.id";
        $data['leftJoin']['item_master'] = "item_master.id = customer_complaints.item_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = customer_complaints.so_trans_id";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['customer_complaints.status'] = $data['status'];
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "so_number";
        $data['searchCol'][] = "complaint";
        $data['searchCol'][] = "product_returned";
        $data['searchCol'][] = "action_taken";
        $data['searchCol'][] = "ref_feedback";
        $data['searchCol'][] = "remark";

		$columns =array('','','trans_number','trans_date','party_master.party_name','item_master.full_name','so_number','complaint','product_returned',"action_taken","action_taken","remark");
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getNextTransNo(){
        $data['tableName'] = $this->customerComplaints;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] = date("Y");
        $data['where']['MONTH(trans_date)'] = date("m");
        $maxNo = $this->specificRow($data)->trans_no;
        $nextNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextNo;
    }

    public function getCustomerComplaints($id){
        $data['tableName'] = $this->customerComplaints;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        
            $result = $this->store($this->customerComplaints,$data);
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
        
            $result = $this->trash($this->customerComplaints,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
		
    }
	
    public function getSalesOrderByParty($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_prefix,trans_no,doc_no";
		$queryData['where']['entry_type'] = 4;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        return $resultData;
    }
    
    public function getCustomerComplaintsData($data){
        $data['tableName'] = $this->customerComplaints;
        $data['customWhere'][] = "customer_complaints.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        return $this->rows($data);
    }

}