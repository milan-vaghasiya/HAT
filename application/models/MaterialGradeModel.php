<?php
class MaterialGradeModel extends MasterModel{
    private $materialMaster = "material_master";
    private $materialSpeci = "material_specification";
    private $scrapGroup = "scrap_group";
    private $itemMaster = "item_master"; 

    private $chemical_name  = ["C%","Mn%","P%","S%","Si%","Cr%","Ni%","Mo%","N%","Other"];
    private $mechanical_name  = ["TS(Mpa)","YS(Mpa)","Elong(%)","RA (%)"];
	
	public function getDTRows($data){
        $data['tableName'] = $this->materialMaster;
        $data['select'] = "material_master.*,item_master.item_name as group_name,";
        $data['leftJoin']['item_master'] = "item_master.id = material_master.scrap_group";
        $data['order_by']['material_master.id'] = "ASC";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "material_master.material_grade";
        $data['searchCol'][] = "item_master.item_name"; 
        $data['searchCol'][] = "material_master.color_code";
        
		$columns =array('','','material_master.material_grade','item_master.item_name','material_master.color_code');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getScrapList(){
        $data['tableName'] = 'item_master';
        $data['where']['item_type'] = 10;
        return $this->rows($data);
    }

    public function getMaterialGrades(){
        $data['tableName'] = $this->materialMaster;
        return $this->rows($data);
    }

    public function getMaterial($id){
        $data['tableName'] = $this->materialMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $result = Array();
            
            $data['material_grade'] = trim($data['material_grade']);
            if($this->checkDuplicate($data['material_grade'],$data['standard'],'material_grade',$data['id']) > 0):
                $errorMessage['material_grade'] = "Material Grade is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            //elseif($this->checkDuplicate($data['metal_code'],$data['standard'],'metal_code',$data['id']) > 0):
            //    $errorMessage['metal_code'] = "Code is duplicate.";
            //    return ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->materialMaster,$data,'Material Grade');
            endif;

            if(empty($data['id'])):
                $grade_id = (empty($data['id']))?$result['insert_id']:$data['id'];
                foreach($this->chemical_name as $key=>$value):
                    $chemicalData = [
                        'id' => '',
                        'grade_id' => $grade_id,
                        'spec_type' => 1,
                        'param_name' => $value,
                        'created_by' => $data['created_by']
                    ];
                    $this->store($this->materialSpeci,$chemicalData,'Material Specification');
                endforeach;

                foreach($this->mechanical_name as $key=>$value):
                    $mechanicalData = [
                        'id' => '',
                        'grade_id' => $grade_id,
                        'spec_type' => 2,
                        'param_name' => $value,
                        'created_by' => $data['created_by']
                    ];
                    $this->store($this->materialSpeci,$mechanicalData,'Material Specification');
                endforeach;
                $hardnessData = [
                    'id' => '',
                    'grade_id' => $grade_id,
                    'spec_type' => 6,
                    'param_name' => 'Hardness (BHN)',
                    'created_by' => $data['created_by']
                ];
                $this->store($this->materialSpeci,$hardnessData,'Material Specification');
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

    public function checkDuplicate($materialGrade,$standard,$field="material_grade",$id=""){
        
        
        $data['tableName'] = $this->materialMaster;
        $data['where'][$field] = $materialGrade;
        $data['where']['standard'] = $standard;
        //$data['where']['material_grade'] = $materialGrade;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        $result = $this->numRows($data);
        //$this->printQuery();
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->materialMaster,['id'=>$id],'Material Grade');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getStandardName(){
        $data['tableName'] = $this->materialMaster;
        $data['select'] = "DISTINCT(standard)";
        return $this->rows($data);
    }

    public function saveInspectionParam($masterData,$specification){
        try{
            $result = $this->store($this->materialMaster,$masterData,'Material Grade');

            foreach($specification['id'] as $key=>$value):
                $specifData = [
                    'id' => $value,
                    'min_value' => $specification['min_value'][$key],
                    'max_value' => $specification['max_value'][$key]
                ];
                $this->store($this->materialSpeci,$specifData,'Material Specification');
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }

    public function getMaterialSpecification($grade_id){
        $data['tableName'] = $this->materialSpeci;
        $data['where']['grade_id'] = $grade_id;
        return $this->rows($data);
    }
    
    public function getItemWiseMaterialGrade($data){
        $data['tableName'] = $this->materialMaster;
        $data['where_in']['id'] = $data['material_grade'];
        return $this->rows($data);
    }
}
?>