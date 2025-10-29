<?php
class MachineActivitiesModel extends MasterModel{
    private $machineActivities = "machine_activities";

    public function getDTRows($data){
        $data['tableName'] = $this->machineActivities;
        $data['searchCol'][] = "activities";
		$columns =array('','','activities');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getActivities($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->machineActivities;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $data['activities'] = trim($data['activities']);
        if($this->checkDuplicate($data['activities'],$data['id']) > 0):
            $errorMessage['activities'] = "Activities is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->machineActivities,$data,'Machine Activities');
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

    public function checkDuplicate($activities,$id=""){
        $data['tableName'] = $this->machineActivities;
        $data['where']['activities'] = $activities;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->machineActivities,['id'=>$id],'Machine Activities');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @27-11-2021 4:29 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->machineActivities;

        return $this->numRows($data);
    }

    public function getMachineActivitiesList_api($limit, $start){
        $data['tableName'] = $this->machineActivities;

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>