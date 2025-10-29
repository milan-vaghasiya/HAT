<?php
class MachineModel extends MasterModel{
    private $machineMaster = "item_master";
    private $machineActivities = "machine_activities";
    private $machinePrevnective = "machine_preventive";
    private $machineMaintenance = "machine_maintenance";
    private $itemMaster = "item_master";
    private $machineMaintenLog = "machine_maintanance_log";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_type,item_master.description,item_master.make_brand,item_master.mfg_year,item_master.install_year,item_master.prev_maint_req,item_master.process_id,item_master.size, department_master.name as location";
        $data['leftJoin']['department_master'] = "department_master.id = item_master.location";
        $data['where']['item_master.item_type'] = 5;
        
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_master.make_brand";
        $data['searchCol'][] = "item_master.size";
        $data['searchCol'][] = "item_master.install_year";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "item_master.prev_maint_req";

		$columns =array('','','item_master.item_name','item_master.item_code','item_master.make_brand','item_master.size','item_master.install_year','department_master.name','item_master.prev_maint_req','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getProcess($id){
        $data['where_in']['id'] = $id;
        $data['tableName'] = 'process_master';
        return $this->rows($data);
    }

    public function getMachine($id){
        $data['select'] = "item_master.*";
        $data['where']['id'] = $id;
        $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->row($data);
    }

    public function getMachineList(){
        $data['select'] = "item_master.*";
        $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
    }
	
	public function getmaintanenceData($machine_id){
		$data['select'] = "machine_preventive.*,machine_activities.activities,item_master.item_name";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
        $data['leftJoin']['item_master'] = "item_master.id = machine_preventive.machine_id";
		$data['where']['machine_id'] = $machine_id;
        $data['tableName'] = $this->machinePrevnective;
        return $this->rows($data);
	}
	
    public function getActivity(){
        $data['tableName'] = $this->machineActivities;
        return $this->rows($data);
    }

    public function getProcessWiseMachine($processId){
        $data['customWhere'][] = 'find_in_set("'.$processId.'", process_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
    }

    public function save($data){    
        try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data['item_code'],$data['id']) > 0):
                $errorMessage['item_code'] = "Machine No. is duplicate.";
                return ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->itemMaster,$data,'Machine');
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

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = 5;
        $data['where']['item_code'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        $result = $this->numRows($data);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->itemMaster,['id'=>$id],'Machines');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function saveActivity($machine_id,$itemData){
        try{
            $this->db->trans_begin();

    		$queryData['select'] = "id";
    		$queryData['where']['machine_id'] = $machine_id;
            $queryData['tableName'] = $this->machinePrevnective;
            $old_data = $this->rows($queryData);
    		
            foreach($itemData['activity_id'] as $key=>$value):
                $activityData = [
                    'id' => $itemData['id'][$key],
    				'machine_id' => $machine_id,
    				'activity_id' => $value,
    				'checking_frequancy' => $itemData['checking_frequancy'][$key],
                    'created_by' => $itemData['created_by'][$key]
                ];
                $this->store($this->machinePrevnective,$activityData,'Machines');
            endforeach;
    		
    		foreach($old_data as $value):
    			if(!in_array($value->id,$itemData['id'])):						
    				$this->trash($this->machinePrevnective,['id'=>$value->id]);
    			endif;
    		endforeach;
            $result = ['status'=>1,'message'=>'Machine Activity saved successfully.','url'=>base_url("machines")];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getMachineForReport(){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = 5;
        return $this->rows($data);
    }
    
    public function getMachineForReportData($id){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = 5;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

	public function getmaintanenceDailyData($id){
        $data['tableName'] = $this->machinePrevnective;
		$data['select'] = "machine_preventive.*,machine_activities.activities";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
		$data['where']['machine_id'] = $id;
		$data['where']['checking_frequancy'] = "Daily";
        return $this->rows($data);
	}

    public function getmaintanenceWeekData($id){
        $data['tableName'] = $this->machinePrevnective;
		$data['select'] = "machine_preventive.*,machine_activities.activities";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
		$data['where']['machine_id'] = $id;
		$data['where']['checking_frequancy'] = "Week";
        return $this->rows($data);
	}

    public function getmaintanenceHalfMonthlyData($id){
        $data['tableName'] = $this->machinePrevnective;
		$data['select'] = "machine_preventive.*,machine_activities.activities";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
		$data['where']['machine_id'] = $id;
		$data['where']['checking_frequancy'] = "Half Monthly";
        return $this->rows($data);
	}

    public function getmaintanenceMonthlyData($id){
        $data['tableName'] = $this->machinePrevnective;
		$data['select'] = "machine_preventive.*,machine_activities.activities";
        $data['join']['machine_activities'] = "machine_activities.id = machine_preventive.activity_id";
		$data['where']['machine_id'] = $id;
		$data['where']['checking_frequancy'] = "Monthly";
        return $this->rows($data);
	}
	
    /* Updated By :- Sweta @12/07/2023 */
    public function saveMachineMaintanLog($data){
        try{
            $this->db->trans_begin();

            if(!empty($data['id']))
            {
                $result = $this->edit($this->machineMaintenLog,['id'=>$data['id'],'machine_id'=>$data['machine_id'],'month'=>$data['month']],$data,'Machine Maintenance Log');
            }
            else
            {
                $result = $this->store($this->machineMaintenLog,$data,'Machine Maintenance Log'); 
            }     

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
    }
	
	public function getMachineMaintenLogData($id,$month){
        $data['tableName'] = $this->machineMaintenLog;
        $data['where']['machine_id'] = $id;
        $data['where']['month'] = $month;
        return $this->row($data);
    }
	
	/*  Create By : Avruti @27-11-2021 4:29 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_master.item_type'] = 5;

        return $this->numRows($data);
    }

    public function getMachinesList_api($limit, $start){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_type,item_master.description,item_master.make_brand,item_master.mfg_year,item_master.install_year,item_master.prev_maint_req,item_master.process_id,item_master.size, department_master.name as location";
        $data['leftJoin']['department_master'] = "department_master.id = item_master.location";
        $data['where']['item_master.item_type'] = 5;
		
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>