<?php
class SetupInspectionModel extends MasterModel{
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";
    private $productionTrans = "job_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->setupRequestTrans;
        $data['select'] = "prod_setup_trans.*,item_master.item_name as machine_name,item_master.item_code as machine_code,job_card.job_prefix,job_card.job_no,process_master.process_name,(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END) as setup_type_name,prod_setup_request.request_date,prod_setup_request.machine_id,prod_setup_request.item_code,timediff(prod_setup_trans.inspection_date,prod_setup_trans.inspection_start_date) AS duration,setter.emp_name as setter_name, inspector.emp_name as inspector_name,(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END) as ins_status";

        $data['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['employee_master as setter'] = "prod_setup_trans.setter_id = setter.id";
        $data['leftJoin']['employee_master as inspector'] = "prod_setup_trans.qci_id = inspector.id";

        // $data['where']['prod_setup_request.qci_id'] = $this->loginID;
        $data['where_in']['job_card.order_status'] = [1,2,3,4];

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')";
        $data['searchCol'][] = "prod_setup_request.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END)";
        $data['searchCol'][] = "DATE_FORMAT(prod_setup_trans.inspection_date,'%d-%m-%Y %h:%i:%s %a')";
        $data['searchCol'][] = "timediff(prod_setup_trans.inspection_date,prod_setup_trans.inspection_start_date)";
        $data['searchCol'][] = "setter.emp_name";
        $data['searchCol'][] = "inspector.emp_name";
        $data['searchCol'][] = "(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END)";

        $columns =array('','',"DATE_FORMAT(prod_setup_request.request_date,'%d-%m-%Y')",'','','CONCAT(job_card.job_prefix,job_card.job_no)','prod_setup_request.item_code','process_master.process_name','item_master.item_code','prod_setup_request.inspection_date','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getSetupInspectionData($id){
        $queryData['tableName'] = $this->setupRequestTrans;
        $queryData['select'] = "prod_setup_trans.*,prod_setup_request.request_date,prod_setup_request.job_trans_id";
        $queryData['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $queryData['where']['prod_setup_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function startInspection($id){
        //$transData = $this->getSetupData($id);

        $postData = [
            'id' => $id,
            'setup_status' => 6,
            'inspection_start_date' => date("Y-m-d H:i:s")
        ];
        $this->store($this->setupRequestTrans,$postData);
        /* $this->edit($this->setupRequest,['id'=>$transData->setup_id],['status'=>($transData->setup_type == 1)?1:4]); */
        return ['status'=>1,'message'=>'Inspection Started successfully.'];
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $createdBy = $data['created_by'];
        unset($data['created_by']);
        $this->store($this->setupRequestTrans,$data);

        $transData = $this->getSetupInspectionData($data['id']);
        $this->edit($this->setupRequest,['id'=>$transData->setup_id],['status'=>$data['setup_status']]);
        $this->edit($this->productionTrans,['id'=>$transData->job_trans_id],['setup_status'=>(($data['setup_status'] == 3)?1:0)]);

        if($data['setup_status'] == 4):
            $resetupData = [
                'id' => "",
                'setup_id' => $transData->setup_id,
                'ref_id' => $transData->id,
                'setup_type' => 2,
                'setup_note' => $data['qci_note'],
                'setter_id' => $transData->setter_id,
                'qci_id' => $transData->qci_id,
                'setup_status' => 0,
                'created_by' => $createdBy
            ];
            $this->store($this->setupRequestTrans,$resetupData);
        endif;
        $result = ['status'=>1,'message'=>'Setup Inspected successfully.'];
        if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
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
        $data['tableName'] = $this->setupRequestTrans;

        return $this->numRows($data);
    }

    public function getSetupInspectionList_api($limit, $start,$status=0){
        $data['tableName'] = $this->setupRequestTrans;
        $data['select'] = "prod_setup_trans.*,item_master.item_name as machine_name,item_master.item_code as machine_code,job_card.job_prefix,job_card.job_no,process_master.process_name,(CASE WHEN prod_setup_trans.setup_type=1 THEN 'New Setup' WHEN prod_setup_trans.setup_type=2 THEN 'Resetup' ELSE '' END) as setup_type_name,prod_setup_request.request_date,prod_setup_request.machine_id,prod_setup_request.item_code,timediff(prod_setup_trans.inspection_date,prod_setup_trans.inspection_start_date) AS duration,setter.emp_name as setter_name, inspector.emp_name as inspector_name,(CASE WHEN prod_setup_trans.setup_status = 0 THEN 'Pending' WHEN prod_setup_trans.setup_status = 1 THEN 'In Process' WHEN prod_setup_trans.setup_status = 2 THEN 'Finish By Setter' WHEN prod_setup_trans.setup_status = 3 THEN 'Approved' WHEN prod_setup_trans.setup_status = 4 THEN 'Resetup' WHEN prod_setup_trans.setup_status = 5 THEN 'On Hold' WHEN prod_setup_trans.setup_status = 6 THEN 'Accept By Inspector' ELSE '' END) as ins_status";

        $data['leftJoin']['prod_setup_request'] = "prod_setup_trans.setup_id = prod_setup_request.id";
        $data['leftJoin']['item_master'] = "prod_setup_request.machine_id = item_master.id";
        $data['leftJoin']['job_card'] = "prod_setup_request.job_card_id = job_card.id";
        $data['leftJoin']['process_master'] = "prod_setup_request.process_id = process_master.id";
        $data['leftJoin']['employee_master as setter'] = "prod_setup_trans.setter_id = setter.id";
        $data['leftJoin']['employee_master as inspector'] = "prod_setup_trans.qci_id = inspector.id";

        // $data['where']['prod_setup_request.qci_id'] = $this->loginID;
        $data['where_in']['job_card.order_status'] = [1,2,3,4];
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>