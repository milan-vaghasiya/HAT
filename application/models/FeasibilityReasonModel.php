<?php
class FeasibilityReasonModel extends MasterModel{
    private $rejectionComment = "rejection_comment";
    
	public function getDTRows($data){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type']=3;
        $data['searchCol'][] = "remark";
		$columns =array('','','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getFeasibility($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->rejectionComment;
        return $this->row($data);
    }
    public function getFeasibilityReasonList($type=3){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type'] = $type;
      
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['remark'],$data['id']) > 0):
            $errorMessage['remark'] = "Feasibility Reason is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->rejectionComment,$data,'Feasibility Reason');
        endif;
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['remark'] = $name;
        $data['where']['type']=3;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->rejectionComment,['id'=>$id],'Feasibility Reason');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }
	
	/*  Create By : Avruti @29-11-2021 03:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type']=3;
        return $this->numRows($data);
    }

    public function getFeasibilityReasonList_api($limit, $start){
        $data['tableName'] = $this->rejectionComment;
        $data['where']['type']=3;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>