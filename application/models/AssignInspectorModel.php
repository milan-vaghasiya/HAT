<?php
class AssignInspectorModel extends MasterModel{
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";

    public function getDTRows($data){
        $data['tableName'] = $this->setupRequest;
        $data['select'] = "prod_setup_request.*,process_master.process_name,item_master.item_name as machine_name,item_master.item_code as machine_code,setter_master.emp_name as setter_name,inspector_master.emp_name as inspector_name,job_card.job_prefix,job_card.job_no";
        $data['leftJoin']['employee_master as setter_master'] = "prod_setup_request.setter_id = setter_master.id";
        $data['leftJoin']['employee_master as inspector_master'] = "prod_setup_request.qci_id = inspector_master.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "setter_master.emp_name";
        $data['searchCol'][] = "inspector_master.emp_name";

		$columns =array('','',"DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')",'CONCAT(job_card.job_prefix,job_card.job_no)','prod_setup_request.item_code','process_master.process_name','item_master.item_code','setter_master.emp_name','inspector_master.emp_name','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getRequestData($id){
        $queryData['tableName'] = $this->setupRequest;
        $queryData['select'] = "prod_setup_request.*,process_master.process_name,item_master.item_name as machine_name,item_master.item_code as machine_code,setter_master.emp_name as setter_name,inspector_master.emp_name as inspector_name,job_card.job_prefix,job_card.job_no";
        $queryData['leftJoin']['employee_master as setter_master'] = "prod_setup_request.setter_id = setter_master.id";
        $queryData['leftJoin']['employee_master as inspector_master'] = "prod_setup_request.qci_id = inspector_master.id";
        $queryData['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $queryData['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $queryData['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";
        $queryData['where']['prod_setup_request.id'] = $id;
        return $this->row($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $this->store($this->setupRequest,$data);
        $this->edit($this->setupRequestTrans,['setup_id'=>$data['id']],['qci_id'=>$data['qci_id']]);
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return ['status'=>1,'message'=>'Inspector assign successfully.'];

        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }
	
	/*  Create By : Avruti @29-11-2021 12:00 AM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->setupRequest;
        return $this->numRows($data);
    }

    public function getAssignInspectorList_api($limit, $start){
        $data['tableName'] = $this->setupRequest;
        $data['select'] = "prod_setup_request.*,process_master.process_name,item_master.item_name as machine_name,item_master.item_code as machine_code,setter_master.emp_name as setter_name,inspector_master.emp_name as inspector_name,job_card.job_prefix,job_card.job_no";
        $data['leftJoin']['employee_master as setter_master'] = "prod_setup_request.setter_id = setter_master.id";
        $data['leftJoin']['employee_master as inspector_master'] = "prod_setup_request.qci_id = inspector_master.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>