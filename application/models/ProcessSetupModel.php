<?php
class ProcessSetupModel extends MasterModel{
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";

    public function getDTRows($data){
        $data['tableName'] = $this->setupRequestTrans;
        $data['select'] = "prod_setup_trans.*,item_master.item_name as machine_name,item_master.item_code as machine_code,job_card.job_prefix,job_card.job_no,process_master.process_name,(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END) as setup_type_name,prod_setup_request.request_date,prod_setup_request.machine_id,prod_setup_request.item_code,timediff(prod_setup_trans.setup_end_time,prod_setup_trans.setup_start_time) AS duration,setter.emp_name as setter_name, inspector.emp_name as inspector_name,(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END) as ins_status";

        $data['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['employee_master as setter'] = "prod_setup_trans.setter_id = setter.id";
        $data['leftJoin']['employee_master as inspector'] = "prod_setup_trans.qci_id = inspector.id";

        $data['where_in']['job_card.order_status'] = [1,2,3,4];
        // $data['where']['prod_setup_request.setter_id'] = $this->loginID;

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END)";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_trans.setup_end_time,'%d-%m-%Y %h:%i:%s %a')";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_trans.setup_start_time,'%d-%m-%Y %h:%i:%s %a')";
        $data['searchCol'][] = "timediff(prod_setup_trans.setup_end_time,prod_setup_trans.setup_start_time)";
        $data['searchCol'][] = "setter.emp_name";
        $data['searchCol'][] = "inspector.emp_name";
        $data['searchCol'][] = "(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END)";

        $columns =array('','',"DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')",'','CONCAT(job_card.job_prefix,job_card.job_no)','prod_setup_request.item_code','process_master.process_name','item_master.item_code','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSetupData($id){
        $queryData['tableName'] = $this->setupRequestTrans;
        $queryData['select'] = "prod_setup_trans.*,prod_setup_request.request_date";
        $queryData['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $queryData['where']['prod_setup_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function startSetup($id){
        $transData = $this->getSetupData($id);

        $postData = [
            'id' => $id,
            'setup_start_time' => date("Y-m-d H:i:s")
        ];
        $this->store($this->setupRequestTrans,$postData);
        $this->edit($this->setupRequest,['id'=>$transData->setup_id],['status'=>($transData->setup_type == 1)?1:4]);
        return ['status'=>1,'message'=>'Setup Started successfully.'];
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $this->store($this->setupRequestTrans,$data);

        $transData = $this->getSetupData($data['id']);
        $this->edit($this->setupRequest,['id'=>$transData->setup_id],['status'=>2]);
        $result = ['status'=>1,'message'=>'Process Setup successfully.'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @27-11-2021 4:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->setupRequestTrans;
        return $this->numRows($data);
    }

    public function getProcessSetupList_api($limit, $start,$status){
        $data['tableName'] = $this->setupRequestTrans;
        $data['select'] = "prod_setup_trans.*,item_master.item_name as machine_name,item_master.item_code as machine_code,job_card.job_prefix,job_card.job_no,process_master.process_name,(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END) as setup_type_name,prod_setup_request.request_date,prod_setup_request.machine_id,prod_setup_request.item_code,timediff(prod_setup_trans.setup_end_time,prod_setup_trans.setup_start_time) AS duration,setter.emp_name as setter_name, inspector.emp_name as inspector_name,(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END) as ins_status";

        $data['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['employee_master as setter'] = "prod_setup_trans.setter_id = setter.id";
        $data['leftJoin']['employee_master as inspector'] = "prod_setup_trans.qci_id = inspector.id";

        $data['where_in']['job_card.order_status'] = [1,2,3,4];
		
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>