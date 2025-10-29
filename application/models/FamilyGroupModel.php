<?php
class FamilyGroupModel extends MasterModel{
    private $familyGroup = "family_group";
    
	public function getDTRows($data){
        $data['tableName'] = $this->familyGroup;
        $data['searchCol'][] = "family_group.family_name";
        $data['searchCol'][] = "item_category.remark";
		$columns =array('','','family_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getFamilyGroup($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->familyGroup;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['family_name'],$data['id']) > 0):
            $errorMessage['family_name'] = "Family Name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->familyGroup,$data,'family Group');
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
        $data['tableName'] = $this->familyGroup;
        $data['where']['family_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->familyGroup,['id'=>$id],'family Group');

        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
        
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
    update by : 
    note : 
*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->familyGroup;
        return $this->numRows($data);
    }

    public function getFamilyGroupList_api($limit, $start){
        $data['tableName'] = $this->familyGroup;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>