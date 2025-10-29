<?php
class DesignationModel extends MasterModel{
    private $designationMaster = "emp_designation";
    private $departmentMaster = "department_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->designationMaster;
        $data['select'] = "emp_designation.*";
        // $data['join']['department_master'] = "department_master.id = emp_designation.dept_id";
        $data['searchCol'][] = "title";
        $data['searchCol'][] = "description";

		$columns =array('','','title','description');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getDepartments($data){
        $data['tableName'] = $this->departmentMaster;
        return $this->rows($data);
    }

    public function getDesignation($id){
        $data['tableName'] = $this->designationMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        if($this->checkDuplicate($data['title'],$data['id']) > 0):
            $errorMessage['title'] = "Designation name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->designationMaster,$data,'Designation');
        endif;
    }

    public function checkDuplicate($title,$id=""){
        $data['tableName'] = $this->designationMaster;
        $data['where']['title'] = $title;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->designationMaster,['id'=>$id],'Designation');
    }
}
?>