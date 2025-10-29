<?php
class AutoCron extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('masterModel');
    }

	/*** Created By JP @09-12-2022 ***/
    public function updateShiftlogV1(){
        
		$this->db->select("employee_master.id,employee_master.shift_id,shift_master.latest_id,employee_master.is_active");
		$this->db->join('shift_master',"employee_master.shift_id = shift_master.id",'left');
        $this->db->where('employee_master.is_delete',0);
		$this->db->where('employee_master.is_active',1);
		$this->db->where('employee_master.attendance_status',1);
		$empData = $this->db->get('employee_master')->result();
		
		$day = date('d');$day = 1;$cmonth = date('m');$cyear = date('Y');$inserted=0;$updated=0;$deleted=0;
        if(!empty($empData)):
			foreach($empData as $row):
				$row->latest_id = (!empty($row->latest_id)) ? $row->latest_id : 0;
			
				$prevData=Array();$empShiftLog = Array();
				$this->db->where('MONTH(month)',$cmonth);
				$this->db->where('YEAR(month)',$cyear);
				$this->db->where('emp_id',$row->id);
				$this->db->where('is_delete',0);
				$prevData = $this->db->get('emp_shiftlog')->row();
				
				for($fkey=intVal($day);$fkey<=intVal(date('t',strtotime(date($cyear.'-'.$cmonth.'-01'))));$fkey++)
				{
					//if($fkey == intVal($day)){$empShiftLog['d'.$fkey]=$row->latest_id;}
					//else{$empShiftLog['d'.$fkey]=0;}
					$empShiftLog['d'.$fkey]=$row->latest_id;
				}
				//$empShiftLog['d'.intVal($day)]=$row->latest_id;
				
				$empShiftLog['created_by']=1;
				$empShiftLog['created_at']=date('Y-m-d H:i:s');
				if(empty($prevData)):
					$empShiftLog['month']=date('Y-m-01');$empShiftLog['emp_id']=$row->id;
    				$this->db->insert('emp_shiftlog',$empShiftLog);$inserted++;
    			else:
				    $this->db->where('id',$prevData->id);
				    if($row->is_active == 0)
				    {
				        $empShiftLog['is_delete']=0;$deleted++;
				    }
				    $this->db->update('emp_shiftlog',$empShiftLog);$updated++;
				    
    			endif;
			endforeach;			
        endif;
		echo "INSERTED : ".$inserted." | UPDATED : ".$updated." | DELETED : ".$deleted;
        return true;
    }
	
    public function updateShiftlog(){
        
		$this->db->select("employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time");
		$this->db->join('shift_master',"employee_master.shift_id = shift_master.id",'left');
        $this->db->where('employee_master.is_delete',0);
		$empData = $this->db->get('employee_master')->result();
		
		$attendaceLog = Array();$empList = Array();
        if(!empty($empData)):
			$currentDate =  date("Y-m-d");
			$this->db->where('attendance_date',$currentDate);
            $this->db->where('is_delete',0);
            $oldData1 = $this->db->get('attendance_shiftlog')->row();
			
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
    			$this->db->where('attendance_date',$prevDate);
                $this->db->where('is_delete',0);
                $oldData = $this->db->get('attendance_shiftlog')->row();
    			
    			if(empty($prevData)):
    				$attendaceLog = ['attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList),'created_by'=>1];
    				$this->db->insert('attendance_shiftlog',$attendaceLog);
    			else:
				    $attendaceLog = ['attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList)];
				    $this->db->where('id',$prevData->id);
				    $this->db->update('attendance_shiftlog',$attendaceLog);
    			endif;
			}
			$attendaceLog = Array();
			// Add Current Day Data
			if(empty($oldData1)):
				$attendaceLog = ['id'=>"",'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList),'created_by'=>1];
				$this->db->insert('attendance_shiftlog',$attendaceLog);
			else:
				$attendaceLog = ['id'=>$oldData->id,'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList)];
				$this->db->where('id',$prevData->id);
				$this->db->update('attendance_shiftlog',$attendaceLog);
			endif;
            //$this->db->insert('required_test',['requirement'=>date('Y-m-d'),'created_at'=>date('Y-m-d H:i:s')]);
			
        endif;
        return true;
    }
    
    public function updateShiftlogJson($fromDate = ''){
        
		$currentDate =  (!empty($fromDate)) ? date("Y-m-d",strtotime($fromDate)) : date("Y-m-d");
		$this->db->where('attendance_date',$currentDate);
        $oldData1 = $this->db->get('attendance_shiftlog')->row();
        
        $newData = '';
        if(!empty($oldData1))
        {
            $newData = str_replace("'\'","",json_decode($oldData1->punchdata));
            //$newData = json_decode($newData);
    		
    		$attendaceLog = ['id'=>$oldData1->id,'punchdata'=>$newData];
    		$this->db->where('id',$oldData1->id);
    		$this->db->update('attendance_shiftlog',$attendaceLog);
            
            print_r($currentDate);//exit;
        }
		
		
        return true;
    }
	
}
?>