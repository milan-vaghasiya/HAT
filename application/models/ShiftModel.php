<?php
class ShiftModel extends MasterModel{
    private $shiftMaster = "shift_master";
    private $empMaster = "employee_master";
    private $empShiftLog = "emp_shiftlog";
    private $empPunches = "emp_punches";
    
    public function getDTRows($data){
		
        $data['tableName'] = $this->shiftMaster;
        $data['where']['latest_id > '] = 0;
        $data['searchCol'][] = "shift_name";
        $data['searchCol'][] = "start_time";
        $data['searchCol'][] = "end_time";
        $data['searchCol'][] = "production_hour";
        $data['searchCol'][] = "lunch_hour";
        $data['serachCol'][] = "shift_hour";
		$columns =array('','','shift_name','start_time','end_time','production_hour','lunch_hour','shift_hour');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getShift($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->shiftMaster;
        return $this->row($data);
    }

    public function getShiftByLatestId($latest_shift_id){
        $data['where']['latest_id'] = $latest_shift_id;
        $data['tableName'] = $this->shiftMaster;
        return $this->row($data);
    }

    public function getShiftList(){
        $data['tableName'] = $this->shiftMaster;
        $data['where']['latest_id > '] = 0;
        return $this->rows($data);
    }

    public function save($data){
		try {
            $this->db->trans_begin();
            $data['shift_name'] = trim($data['shift_name']);
			if($this->checkDuplicate($data['shift_name'],$data['id']) > 0):
				$errorMessage['shift_name'] = "Shift Name is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
				if(empty($data['id']))
				{
					$result = $this->store($this->shiftMaster,$data,'Shift');
					$updateLatestID = $this->store($this->shiftMaster,['id'=>$result['insert_id'],'latest_id'=>$result['insert_id']],'Shift');
				}
				else
				{
					$refRecord = $data;$refRecord['id']="";$refRecord['latest_id']=0;
					$addNewRecord = $this->store($this->shiftMaster,$refRecord,'Shift');
					$data['latest_id']=$addNewRecord['insert_id'];
					$result = $this->store($this->shiftMaster,$data,'Shift');
				}
			endif;
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
        
    }

    public function checkDuplicate($shiftname,$id=""){
        $data['tableName'] = $this->shiftMaster;
        $data['where']['shift_name'] = $shiftname;
        
        if(!empty($id)):
            $data['where']['id !='] = $id;
			$data['where']['latest_id > '] = 0;
		endif;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->shiftMaster,['id'=>$id],'Shift');
    }

    public function getEmpList($shift_id="",$dept_id="",$category_id=""){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name,emp_designation.title";
        $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['join']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_name!='] = "Admin";
        if(!empty($shift_id)){$data['where_in']['employee_master.shift_id'] = $shift_id;}
        //if(!empty($shift_id) AND $shift_id > 0){$data['where']['employee_master.shift_id'] = $shift_id;}
        // $data['where_in']['employee_master.shift_id'] = $shift_id;
        if(!empty($dept_id)){$data['where']['employee_master.emp_dept_id'] = $dept_id;}
        if(!empty($category_id)){$data['where']['employee_master.emp_category'] = $category_id;}

        return $this->rows($data);
    }

	public function updateEmpShift(){
		$empQ['tableName'] = $this->empMaster;
        $empQ['select'] = "employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time";
        $empQ['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
		$empData = $this->rows($empQ);
		
		$attendaceLog = Array();$empList = Array();
        if(!empty($empData)):
			$currentDate =  date("Y-m-d");
			$dd1Query['tableName'] = 'attendance_shiftlog';
			$dd1Query['where']['attendance_date'] = $currentDate;
			$oldData = $this->row($dd1Query);
			
			foreach($empData as $row):
				$shiftLog = Array();
				$shiftLog['emp_id']=$row->id;$shiftLog['emp_code']=$row->emp_code;$shiftLog['shift_id']=$row->shift_id;
				$shiftLog['shift_start']=$row->shift_start;$shiftLog['shift_end']=$row->shift_end;
				$shiftLog['shift_name']=$row->shift_name;$shiftLog['total_shift_time']=$row->total_shift_time;
				$empList[] = $shiftLog;
			endforeach;
			
			// Add Previous Day Data if not found
			$prevDate = date('Y-m-d', strtotime($currentDate.' -1 day'));
			$prevDay = date("D",strtotime($prevDate));
			if($prevDay == 'Wed')
			{
    			$dd1Query1['tableName'] = 'attendance_shiftlog';
    			$dd1Query1['where']['attendance_date'] = $prevDate;
    			$prevData = $this->row($dd1Query1);
    			
    			if(empty($prevData)):
    				$attendaceLog = ['id'=>"",'attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList),'created_by'=>$this->loginID];
    			else:
				    $attendaceLog = ['id'=>$prevData->id,'attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList)];
    			endif;
    			$this->store('attendance_shiftlog',$attendaceLog,'Attendance Log');
			}
			$attendaceLog = Array();
			// Add Current Day Data
			if(empty($oldData)):
				$attendaceLog = ['id'=>"",'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList),'created_by'=>$this->loginID];
			else:
				$attendaceLog = ['id'=>$oldData->id,'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList)];
			endif;
			$this->store('attendance_shiftlog',$attendaceLog,'Attendance Log');
        endif;
        
        return true;
    }
	
    public function getAttendanceLog($attendance_date,$emp_id){
		$alQuery['tableName'] = 'attendance_shiftlog';
		$alQuery['where']['attendance_date'] = $attendance_date;
		$alData = $this->row($alQuery);
        //$this->printQuery();exit;
        
		
		$punchData = Array();$shiftData = Array();
        if(!empty($alData)):
			$punchData = json_decode($alData->punchdata);
            //$punchData = (array) json_decode($punchData);
            //$punchData = json_decode($punchData[0]);
			$empPucnhes = array_keys(array_combine(array_keys($punchData), array_column($punchData, 'emp_id')),$emp_id);
			
			if(empty($empPucnhes))
			{
				$empQ['tableName'] = $this->empMaster;
			    $empQ['select'] = "employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time";
				$empQ['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
	        	$empQ['where']['employee_master.id'] = $emp_id;
				$empData = $this->row($empQ);
				$shiftLog = Array();
				$shiftLog['emp_id']=$empData->id;$shiftLog['emp_code']=$empData->emp_code;$shiftLog['shift_id']=$empData->shift_id;
				$shiftLog['shift_start'] = (empty($empData->shift_start)) ? date('H:i:s',strtotime('00:00:00')) : $empData->shift_start;
				$shiftLog['shift_end'] = (empty($empData->shift_end)) ? date('H:i:s',strtotime('00:00:00')) : $empData->shift_end;
				$shiftLog['shift_name'] = (empty($empData->shift_name)) ? '-' : $empData->shift_name;
				$shiftLog['total_shift_time'] = (empty($empData->total_shift_time)) ? '00:00' : $empData->total_shift_time;
				$shiftData = (object)$shiftLog;
			}
			else{$shiftData = $punchData[$empPucnhes[0]];}
			
        endif;
        //print_r($shiftData);exit;
        return $shiftData;
    }
    
    public function getEmployee($id){
        $data['tableName'] = $this->empMaster;
        $data['where']['id'] = $id;
        $data['where']['is_delete != '] = 2;
        return $this->row($data);
    }

    public function getEmpShiftByEmpcode($emp_code){
		$data['tableName'] = $this->empMaster;
		$data['select'] = "employee_master.id";
		/*$data['select'] = "employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time,shift_master.latest_id";
	    $data['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";*/
        $data['where']['employee_master.emp_code'] = $emp_code;
        $data['where']['employee_master.is_delete != '] = 2;
        return $this->row($data);
    }
    
    public function getShiftLogs($data){
		$alQuery['tableName'] = 'attendance_shiftlog';
		$alQuery['where']['attendance_date'] = $data['shift_date'];
		return $this->row($alQuery);
    }
    
    // Updated By JP @15012023
    public function saveManageShift($postData){
		try {
            $this->db->trans_begin();
			$result = Array();
            if(!empty($postData['id']))
			{				
				$where['id'] = $postData['id'];
				$where['emp_id'] = $postData['emp_id'];
				
				$result = $this->edit($this->empShiftLog, $where, [$postData['field_id'] => $postData['new_shift_id'], 'updated_by' => $postData['updated_by'], 'updated_at' => $postData['updated_at']]);
				$syncData = [];
				$syncData['attendance_date'] = $postData['shift_date'];
				$syncData['emp_id'] = $postData['emp_id'];
				$syncSummary = $this->biometric->syncAttendanceLogSummary($syncData);
				
				// Update Employee Shift in Master data if Manage Shift Date is Today
				$shift_date = date('Y-m-d',strtotime($postData['shift_date']));
				if(($result['status']==1) AND $shift_date==date('Y-m-d'))
				{
				    $shiftData = $this->getShiftByLatestId($postData['new_shift_id']);
				    if(!empty($shiftData->id))
				    {
            		    $updatempShift = $this->store('employee_master',['id' => $postData['emp_id'],'shift_id' => $shiftData->id]);
				    }
				}
			}
			
			
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
	
	/*** Created By JP@14-02-2023 ***/
	public function migrateAttendanceDate($postData=[])
	{
		if(!empty($postData['shift_date']))
		{
			$FromDate = date('Y-m-d',strtotime($postData['shift_date']));
			$ToDate = date('Y-m-d');
			
			
			$queryData['tableName'] = $this->empPunches;
			$queryData['select'] = 'shift_id,attendance_date';
			$queryData['where']['attendance_date >= '] = $FromDate;
			$queryData['where']['attendance_date <= '] = $ToDate;
			$queryData['where']['emp_id'] = $postData['emp_id'];
			$queryData['order_by']['punch_date'] = 'ASC';
			$empPunchData = $this->rows($queryData);
			print_r($this->printQuery());exit;
			if(!empty($empPunchData))
			{
				foreach($empPunchData as $row)
				{
					
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
}
?>