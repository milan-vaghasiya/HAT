<?php
class LeaveSettingModel extends MasterModel{
    private $leaveType = "leave_type";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
	
	public function getDTRows($data){
        $data['tableName'] = $this->leaveType;
        $data['customWhere'][] = "type IS NULL";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "remark";
        return $this->pagingRows($data);
    }

    public function getLeaveType($id){
        $data['tableName'] = $this->leaveType;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
	
	public function getEmpDesignations(){
		$data['tableName'] = $this->empDesignation;
		return $this->rows($data);
	}
	
    public function save($data){
        if($this->checkDuplicate($data['leave_type'],$data['id']) > 0):
            $errorMessage['leave_type'] = "Leave Type is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->leaveType,$data,'Leave Type');
        endif;
    }

    public function checkDuplicate($leave_type,$id=""){
        $data['tableName'] = $this->leaveType;
        $data['where']['leave_type'] = $leave_type;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->leaveType,['id'=>$id],'Leave Type');
    }
	
}
?>