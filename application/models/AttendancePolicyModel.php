<?php
class AttendancePolicyModel extends MasterModel{
    private $attendancePolicy = "attendance_policy";
    private $empMaster = "employee_master";

    public function getDTRows($data){
        $data['tableName'] = $this->attendancePolicy;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "policy_name";
        $data['searchCol'][] = "policy_type";
        $data['searchCol'][] = "minite_day";
        $data['searchCol'][] = "day_month";

		$columns =array('','','policy_name','policy_type','minite_day','day_month');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getAttendancePolicy($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->attendancePolicy;
        return $this->row($data);
    }

    public function getAttendancePolicies(){
        $data['tableName'] = $this->attendancePolicy;
        return $this->rows($data);
    }

    public function save($data){
        $data['policy_name'] = trim($data['policy_name']);
        if($this->checkDuplicate($data['policy_name'],$data['id']) > 0):
            $errorMessage['policy_name'] = "Policy Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->attendancePolicy,$data,'Attendance Policy');
        endif;
    }

    public function checkDuplicate($policyname,$id=""){
        $data['tableName'] = $this->attendancePolicy;
        $data['where']['policy_name'] = $policyname;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->attendancePolicy,['id'=>$id],'Attendance Policy');
    }

    public function getEmpList($policy_id="",$dept_id="",$category_id=""){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name,emp_designation.title";
        $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['join']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_name!='] = "Admin";
        if(!empty($policy_id)){$data['where']['employee_master.attendance_policy'] = $policy_id;}
        if(!empty($dept_id)){$data['where']['employee_master.emp_dept_id'] = $dept_id;}
        if(!empty($category_id)){$data['where']['employee_master.emp_category'] = $category_id;}

        return $this->rows($data);
    }

    public function saveAssignPolicy($data){
        $empData = $this->getEmpList($data['policy_id'],$data['dept_id'],$data['category_id']);
        $newEmpId = Array();
        if(!empty($data['attendance_policy'])):
            foreach($data['attendance_policy'] as $emp_id):
                $newEmpId[] = $emp_id;
                $this->store($this->empMaster,['id'=>$emp_id,'attendance_policy'=>$data['policy_id']],'Assign Policy');
            endforeach;
        endif;
        foreach($empData as $row):
            if(!in_array($row->id, $newEmpId)):
                $this->store($this->empMaster,['id'=>$row->id,'attendance_policy'=>0],'Assign Policy');
            endif;
        endforeach;
        return ['status'=>1,'message'=>'Policy Assigned Successfully.','policy_id'=>$data['policy_id']];
    }
}
?>