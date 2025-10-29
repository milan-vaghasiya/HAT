<?php
class GradeMasterModel extends MasterModel{
    private $gradeMaster = "grade_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->gradeMaster;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "material_grade";
        $data['searchCol'][] = "standard";
        
		$columns =array('','','material_grade','standard');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getMaterialGradeName(){
        $data['tableName'] = $this->gradeMaster;
        $data['select'] = "DISTINCT(material_grade)";
        return $this->rows($data);
    }

    public function getStandardName(){
        $data['tableName'] = $this->gradeMaster;
        $data['select'] = "DISTINCT(standard)";
        return $this->rows($data);
    }

    public function getGradeMaster($id){
        $data['tableName'] = $this->gradeMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
    
     public function getMaterialGradeList(){
        $data['tableName'] = $this->gradeMaster;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $data['material_grade'] = trim($data['material_grade']);
            $result = $this->store($this->gradeMaster,$data,'Grade Master');
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
            $result = $this->trash($this->gradeMaster,['id'=>$id],'Material Grade');
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