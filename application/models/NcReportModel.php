<?php
class NcReportModel extends MasterModel{
    private $productionLogMaster = "production_log";
    private $itemMaster = "item_master";
    private $processMaster = "process_master";
    private $empMaster = "employee_master";
    private $jobApproval = "job_approval";
	
    public function getDTRows($data){
        $data['tableName'] = $this->productionLogMaster;
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['where']['production_log.prod_type'] = 2;
        $data['where']['production_log.log_date >= '] = $this->startYearDate;
        $data['where']['production_log.log_date <= '] = $this->endYearDate;
        $data['order_by']['production_log.log_date'] = 'DESC';
        
        $data['searchCol'][] = "job_card.job_no";
        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['serachCol'][] = "employee_master.emp_name";
        $data['serachCol'][] = "production_log.production_qty";
        $data['serachCol'][] = "production_log.rej_qty";
        $data['serachCol'][] = "production_log.rw_qty";

        $columns = array('', '','job_card.job_no','DATE_FORMAT(production_log.log_date,"%d-%m-%Y")', 'process_master.process_name', 'employee_master.emp_name', 'production_log.production_qty', 'production_log.rej_qty', 'production_log.rw_qty');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

   
    public function getLogs($id){
        $data['tableName'] = $this->productionLogMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $result=$this->logSheet->save($data);
            //$result = $this->store($this->productionLogMaster,$data,'NC Report');
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
            $result=$this->logSheet->delete($id);
            //$result = $this->trash($this->productionLogMaster,['id'=>$id],'Production Log');
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
?>