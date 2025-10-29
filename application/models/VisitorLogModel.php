<?php
class VisitorLogModel extends MasterModel{
    private $visitorLog = "visitor_log";
    private $itemMaster = "item_master";
    
    public function save($data){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->visitorLog,$data,'Visitor Log');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    public function checkForApproval($postData){
        $queryData = array();
        $queryData['tableName'] = $this->visitorLog;
        $queryData['select'] = 'DATE_FORMAT(approved_at, "%d-%m-%Y %H:%i:%s") as approved_at';
        $queryData['where']['id'] = $postData['id'];
        $queryData['where']['approved_at > '] = 0;
        $result = $this->row($queryData);
        return $result;
    }
    
    public function getVisitorLogs(){
        $queryData = array();
        $queryData['tableName'] = $this->visitorLog;
        $queryData['order_by']['id'] = "DESC";
        $result = $this->rows($queryData);
        return $result;
    }
}

?>