<?php

class RejectionCommentModel extends MasterModel{
    private $rejectionComment = "rejection_comment";

    public function getDTRows($data){
        $data['tableName'] = $this->rejectionComment;
		if($data['type'] != 2){ $data['where_in']['type'] = '1,4'; }
        else{ $data['where']['type'] = $data['type']; }
        $data['searchCol'][] = "remark";
		$columns =array('','','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getCommentList(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 1;
        return $this->rows($data);
    }

    public function getReworkCommentList(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 4;
        return $this->rows($data);
    }
    
    public function getIdleReason(){
        $data['tableName'] = $this->rejectionComment;
		$data['where']['type'] = 2;
        return $this->rows($data);
    }

    public function getComment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->rejectionComment;
        return $this->row($data);
    }
    public function getCommentsOnRejectionStage($stageId){
        $data['where']['type'] = 1;
	    //$data['customWhere'][] = 'find_in_set("'.$stageId.'", process_id)';
        $data['tableName'] = $this->rejectionComment;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $sub_type = "";
            if($data['type'] == 1){ $sub_type  = $data['sub_type']; }
            if($this->checkDuplicate($data['type'],$data['remark'],$data['id'],$sub_type) > 0):
                $errorMessage['remark'] = "Reason is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            endif;
            $result = $this->store($this->rejectionComment,$data,'Template');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkDuplicate($type,$name,$id="",$sub_type = ""){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['remark'] = $name;
        $data['where']['type']=$type;   
        if(!empty($sub_type)){ $data['where']['sub_type']=$sub_type;   }     
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $result = $this->trash($this->rejectionComment,['id'=>$id],'Template');

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