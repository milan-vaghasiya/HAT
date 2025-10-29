<?php
class TermsModel extends MasterModel{
    private $terms = "terms";
	
    public function getDTRows($data){
        $data['tableName'] = $this->terms;
        $data['searchCol'][] = "title";
        $data['serachCol'][] = "conditions";
		$columns =array('','','title','','conditions');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getTerms($id,$type=""){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->terms;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->terms,$data,'Terms');
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
            $result = $this->trash($this->terms,['id'=>$id],'Terms');
            if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function getTermsList(){
        $data['tableName'] = $this->terms;
        return $this->rows($data);
    }
    
    /*
    * Create By : Nayan Patel
    * Updated By : Nayan Patel @16-11-2022 03:40 PM 
    * Note : Terms List By Type
    */
    public function getTermsListByType($type=''){
        $data['tableName'] = $this->terms;
        $data['customWhere'][] = "FIND_IN_SET('".$type."', type)";
        return $this->rows($data);
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
		update by : 
		note : 
	*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->terms;
        return $this->numRows($data);
    }

    public function getTermsList_api($limit, $start){
        $data['tableName'] = $this->terms;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>