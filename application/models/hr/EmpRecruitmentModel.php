<?php
class EmpRecruitmentModel extends MasterModel{
    private $empRecruitment = "emp_recruitment";
    private $empMaster = "employee_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->empRecruitment;
        $data['select'] = "emp_recruitment.*,department_master.name as dept_name,emp_designation.title as emp_designation,emp_category.category as emp_category";
        $data['leftJoin']['department_master'] = "emp_recruitment.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "emp_recruitment.emp_designation = emp_designation.id";
        $data['leftJoin']['emp_category'] = "emp_recruitment.emp_category = emp_category.id";
        
        $data['where']['emp_recruitment.status'] = $data['status'];
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "emp_recruitment.emp_name";
        $data['searchCol'][] = "emp_category.category";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "emp_designation.title";
        $data['searchCol'][] = "emp_recruitment.emp_education";
        $data['searchCol'][] = "emp_recruitment.emp_experience";
        $data['searchCol'][] = "emp_recruitment.emp_contact";
        
		$columns =array('','','emp_recruitment.emp_name','emp_category.category','department_master.name','emp_designation.title','emp_recruitment.emp_education','emp_recruitment.emp_experience','emp_recruitment.emp_contact');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getEmployee($emp_id){
        $data['tableName'] = $this->empRecruitment;
        $data['select'] = "emp_recruitment.*, department_master.name, emp_designation.title";
        $data['leftJoin']['department_master'] = "emp_recruitment.emp_dept_id = department_master.id";
        $data['leftJoin']['emp_designation'] = "emp_recruitment.emp_designation = emp_designation.id";
        $data['where']['emp_recruitment.id'] = $emp_id;
        return $this->row($data);
    }
    
    public function getEmpList($postData = array()){
        $queryData = array();
        $queryData['tableName'] = $this->empRecruitment;
        $queryData['select'] = "emp_recruitment.*,department_master.name as dept_name,emp_designation.title as emp_designation,emp_category.category as emp_category";
        $queryData['leftJoin']['department_master'] = "emp_recruitment.emp_dept_id = department_master.id";
        $queryData['leftJoin']['emp_designation'] = "emp_recruitment.emp_designation = emp_designation.id";
        $queryData['leftJoin']['emp_category'] = "emp_recruitment.emp_category = emp_category.id";
        if(!empty($postData['status'])){$queryData['where']['emp_recruitment.status'] = $data['status'];}
		return $this->rows($queryData);
    }

    public function save($data){
        if($this->checkDuplicate($data['emp_name'],$data['emp_contact'],$data['id']) > 0):
            $errorMessage['emp_name'] = "Duplicate Entry";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->empRecruitment,$data,'Recruitment');
            return $result;
        endif;
    }

    public function checkDuplicate($emp_name,$emp_contact,$id=""){
        $numRows = 0;
        if(!empty($emp_contact)):
            $data['tableName'] = $this->empRecruitment;
            $data['where']['emp_name'] = $emp_name;
            $data['where']['emp_contact'] = $emp_contact;
            
            if(!empty($id))
                $data['where']['id !='] = $id;
            $numRows = $this->numRows($data);
        else:
            $numRows = 0;
        endif;
        return $numRows;
    }
    
    public function delete($id){
        return $this->trash($this->empRecruitment,['id'=>$id],'Recruitment');
    }

    public function activeInactive($id,$status){ 
        $this->edit($this->empRecruitment,['id'=>$id],['status'=>$status],'');
        $msg = ($value == 1)?"actived":"in-active";
        return ['status'=>1,'message'=> "Employee ".$msg." successfully."];
    }

    
}
?>