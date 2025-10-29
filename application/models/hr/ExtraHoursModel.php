<?php
class ExtraHoursModel extends MasterModel{
    private $empAttendance = "attendance_log";

    public function getDTRows($data){
        $data['tableName'] = $this->empAttendance;
        $data['select'] = "attendance_log.*,employee_master.emp_name";
        $data['select'] .= ", (CASE WHEN FIND_IN_SET(".$this->loginID.",employee_master.fla_id) THEN 1 ELSE 0 END) as approvalAuth";
        $data['join']['employee_master'] = "employee_master.id = attendance_log.emp_id";
        $data['where']['attendance_log.punch_type'] =3;
        
        if($data['status']==1){
            $data['where']['attendance_log.approved_by > '] = 0;
            $data['where']['DATE_FORMAT(attendance_log.punch_date,"%Y-%m-%d") >='] = $this->startYearDate;
            $data['where']['DATE_FORMAT(attendance_log.punch_date,"%Y-%m-%d") <='] = $this->endYearDate;
        }  // Approved Data
        else{$data['where']['attendance_log.approved_by'] = 0;}  // Pending Data
        
        $data['order_by']['attendance_log.punch_date'] = 'DESC';
        $data['order_by']['attendance_log.id'] = 'DESC';
        
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "attendance_log.emp_code";
        $data['searchCol'][] = "attendance_log.punch_date";
        $data['searchCol'][] = "attendance_log.remark";
		$columns =array('','','emp_name','emp_code','punch_date','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getExtraHours($id){
        $data['select'] = "attendance_log.*,emp.emp_name,user.emp_name as createdBy";
        $data['tableName'] = $this->empAttendance;
        $data['leftJoin']['employee_master emp'] = "emp.id = attendance_log.emp_id";
        $data['leftJoin']['employee_master user'] = "user.id = attendance_log.created_by";
        $data['where']['attendance_log.id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->empAttendance,$data,'Extra Hours');
            $data['attendance_date'] = date('Y-m-d',strtotime($data['punch_date']));
            $syncSummary = $this->biometric->syncAttendanceLogSummary($data);
            if($syncSummary):
                $result['message'] .= " Attendance Summary Synced successfully.";
            else:
                $result['message'] .= " Attendance Summary not Synced.";
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

    public function approveXHRS($data){
        try{
            $this->db->trans_begin();
            
            $result = $this->store($this->empAttendance,$data,'Extra Hours');
            if($result['status'] == 1):
                $result['message'] = " Extra Time Approved successfully.";
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

    public function delete($id){
        try{
            $this->db->trans_begin();

            $attendaceLog = $this->getExtraHours($id);
            if(!empty($attendaceLog)):
                $attendance_date = date('Y-m-d',strtotime($attendaceLog->punch_date));
                $result = $this->trash($this->empAttendance,['id'=>$id],'Extra Hours');
                $syncSummary = $this->biometric->syncAttendanceLogSummary(['emp_id'=>$attendaceLog->emp_id,'attendance_date'=>$attendance_date]);
                
                if($syncSummary):
                    $result['message'] .= " Attendance Summary Synced successfully.";
                else:
                    $result['message'] .= " Attendance Summary not Synced.";
                endif;
            else:
                $result = ['status'=>0,'message'=>'Attendance Log already deleted.'];
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
}
?>