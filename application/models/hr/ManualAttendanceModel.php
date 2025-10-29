<?php
class ManualAttendanceModel extends MasterModel{
    private $empAttendance = "attendance_log";
    private $attendanceLogSummary = "alog_summary";

    public function getDTRows($data){
        $data['tableName'] = $this->empAttendance;
        $data['select'] = "attendance_log.*,employee_master.emp_name";
        $data['join']['employee_master'] = "employee_master.id = attendance_log.emp_id";
        $data['where']['punch_type'] = 2;

        $data['where']['DATE_FORMAT(attendance_log.punch_date,"%Y-%m-%d") >='] = $this->startYearDate;
        $data['where']['DATE_FORMAT(attendance_log.punch_date,"%Y-%m-%d") <='] = $this->endYearDate;
        
        $data['order_by']['attendance_log.id'] = "DESC";
        
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "attendance_log.emp_code";
        $data['searchCol'][] = "attendance_log.punch_date";
        $data['searchCol'][] = "remark";

		$columns =array('','','employee_master.emp_name','attendance_log.emp_code','attendance_log.punch_date','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
	
		return $result;
    }

    public function getManualAttendance($id){
        $data['tableName'] = $this->empAttendance;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();

            $result = $this->store($this->empAttendance,$data,'Manual Attendance');

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

    public function delete($id,$attendanceDate){
        try{
            $this->db->trans_begin();

            $attendaceLog = $this->getManualAttendance($id);
            if(!empty($attendaceLog)):
                $result = $this->trash($this->empAttendance,['id'=>$id],'Manual Attendance');
                $syncSummary = $this->biometric->syncAttendanceLogSummary(['emp_id'=>$attendaceLog->emp_id,'attendance_date'=>date('Y-m-d',strtotime($attendanceDate))]);
                
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
    
    /* Transfered to Biometric Model
    public function syncAttendanceLogSummary($data){
        try{
            $this->db->trans_begin();
            
            $empAttendanceLog = $this->biometric->arrangeAttendanceDate($data['emp_id'], $data['attendance_date']);
            
            foreach($empAttendanceLog as $row):
                
                $oldQ['tableName'] = $this->attendanceLogSummary;
                $oldQ['where']['emp_id'] = $row->emp_id;
                $oldQ['where']['attendance_date'] = $data['attendance_date'];
                $oldData = $this->row($oldQ);

                $row = (object) $row;
                $alogSummaryData = [
                    'id' => (!empty($oldData))?$oldData->id:"",
                    'emp_id' => $row->emp_id,
                    'emp_code' => $row->emp_code,
                    'attendance_date' => $data['attendance_date'],
                    'shift_id' => (!empty($row->shift_id))?$row->shift_id:0,
                    'punch_date' => $row->punch_date,
                    'ex_mins' => (!empty($row->xmins))? ($row->xmins*60):0
                ];
                $this->store($this->attendanceLogSummary,$alogSummaryData);
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return true;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return false;
        }	
    }*/
}
?>