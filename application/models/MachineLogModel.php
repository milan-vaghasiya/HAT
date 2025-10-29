<?php
class MachineLogModel extends MasterModel{
    private $machineLog = "tpm_log";
    private $itemMaster = "item_master";
    
    public function save($data){
        try{
            $this->db->trans_begin();
            
            $data['id'] = "";
            $queryData = array();
            $queryData['tableName'] = $this->itemMaster;
            $querydata['where']['drawing_no'] = $data['device_no'];
            $machineData = $this->row($queryData);
            
            $data['machine_id'] = $machineData->id;
            $data['created_by'] = $machineData->operator_id;
            $data['job_card_id'] = $machineData->job_card_id;
            $data['created_at'] = date("Y-m-d H:i:s");
            
            $result = $this->store($this->machineLog,$data,'Machine Log');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function getMachineLogs(){
        $queryData = array();
        $queryData['tableName'] = $this->machineLog;
        $queryData['order_by']['id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }
}

?>