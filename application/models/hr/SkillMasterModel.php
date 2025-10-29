<?php
class SkillMasterModel extends MasterModel{
    private $skillMaster = "skill_master";
	
    public function getDTRows($data){
        $data['tableName'] = $this->skillMaster;
        $data['select'] = "skill_master.*,department_master.name,emp_designation.title";
        $data['leftJoin']['department_master'] = "department_master.id = skill_master.dept_id";
        $data['leftJoin']['emp_designation'] = "emp_designation.id = skill_master.designation_id";
       
        $data['serachCol'][] = "skill_master.skill";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "skill_master.req_per";
        
		$columns =array('','','skill_master.skill','department_master.name','emp_designation.title','skill_master.req_per');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getSkillMaster($id){
        $data['tableName'] = $this->skillMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->skillMaster,$data,'skillMaster');
    }

    public function delete($id){
        return $this->trash($this->skillMaster,['id'=>$id],'skillMaster');
    }

    public function getDeptWiseSkill($id){
		$data['tableName'] = $this->skillMaster; 
		$data['where']['dept_id'] = $id;
		return $this->rows($data);
	}
}
?>