<?php
class MachineTicketModel extends MasterModel{
    private $machineMaintenance = "machine_maintenance";
    private $itemMaster = "item_master";
	private $deptMaster = "department_master";

    public function getDTRows($data){
        $data['tableName'] = $this->machineMaintenance;
        $data['select'] = "machine_maintenance.*,item_master.item_name";
        $data['join']['item_master'] = "item_master.id = machine_maintenance.machine_id";
        $data['where']['item_type'] = 5;
        $data['where']['problem_date >= '] = $this->startYearDate;
        $data['where']['problem_date <= '] = $this->endYearDate;
        $data['order_by']['machine_maintenance.trans_no'] = "DESC";
        
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "CONCAT(trans_no,trans_prefix)";
        $data['searchCol'][] = "problem_title";
        $data['searchCol'][] = "DATE_FORMAT(problem_date,'%d-%m-%Y')";
        $data['searchCol'][] = "solution_detail";
        $data['searchCol'][] = "DATE_FORMAT(solution_date,'%d-%m-%Y')";

		$columns =array('','','item_name','trans_no','trans_prefix','problem_title','problem_date','solution_detail','solution_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function nextTransNo(){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['tableName'] = $this->machineMaintenance;
        $data['where']['problem_date >= '] = $this->startYearDate;
        $data['where']['problem_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function getMachineTicketList(){
        $data['tableName'] = $this->machineMaintenance;
        return $this->rows($data);
	}
    
    public function getMachineName(){
	    $data['where']['item_type'] = 5;
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}

    public function getDepartment(){
        $data['tableName'] = $this->deptMaster;
        return $this->rows($data);
	}

    public function getMachineTicket($id){
	    $data['where']['id'] = $id;
        $data['tableName'] = $this->machineMaintenance;
        return $this->row($data);
	}

    public function save($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->machineMaintenance,$data,'Machine Ticket');
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
        $result =  $this->trash($this->machineMaintenance,['id'=>$id],'Machines Ticket');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
    
	public function getMachineTicketListByDate($data){
		$queryData = array();
		$queryData['tableName'] = $this->machineMaintenance;
		$queryData['select'] = "machine_maintenance.*,item_master.item_name,item_master.item_code";
		$queryData['join']['item_master'] = "item_master.id = machine_maintenance.machine_id";
		$queryData['customWhere'][] = "machine_maintenance.problem_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$mlogData= $this->rows($queryData);
		
		$tbody = '';
		if(!empty($mlogData)):
			$i=1; 
			foreach($mlogData as $row):

				$tbody .= '<tr class="text-center">
                <td>'.$i++.'</td>
                <td>'.formatDate($row->problem_date).'</td>
                <td>'.$row->problem_time.'</td>
                <td>'.$row->problem_detail.'</td>
                <td>'.$row->solution_detail.'</td>
                <td>'.$row->solution_by.'</td>
                <td>'.date('d-m-Y H:i:s',strtotime($row->solution_date)).'</td>
                <td></td>
                <td>'.(!empty($row->solution_by) ? "YES" : "NO").'</td>
                <td>'.$row->down_time.'</td>
                <td></td>
            </tr>';
			endforeach;
		endif;
		return ['status'=>1,'tbody'=>$tbody,'mlogData'=>$mlogData];
	}
	
	/*  Create By : Avruti @27-11-2021 4:29 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->machineMaintenance;
        return $this->numRows($data);
    }

    public function getMachineTicketList_api($limit, $start){
        $data['tableName'] = $this->machineMaintenance;
        $data['select'] = "machine_maintenance.*,item_master.item_name";
        $data['join']['item_master'] = "item_master.id = machine_maintenance.machine_id";
        $data['where']['item_type'] = 5;
        $data['order_by']['machine_maintenance.trans_no'] = "DESC";
		
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>