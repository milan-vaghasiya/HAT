<?php
class AdvanceSalaryModel extends MasterModel
{
    private $advanceSalary = "advance_salary";
    private $empMaster = "employee_master";

    
	public function getDTRows($data){ 
        $data['tableName'] = $this->advanceSalary;
        $data['select'] = "advance_salary.*,employee_master.emp_name,employee_master.emp_code,sanction.emp_name as sanctioned_by_name,(IFNULL(advance_salary.sanctioned_amount,0) - IFNULL(advance_salary.deposit_amount,0)) as pending_amount,facility_master.ficility_type";
        $data['leftJoin']['employee_master'] = "advance_salary.emp_id = employee_master.id";
        $data['leftJoin']['employee_master as sanction'] = "advance_salary.sanctioned_by = sanction.id";
        $data['leftJoin']['facility_master'] = "advance_salary.facility_id = facility_master.id AND facility_master.is_delete = 0";
        $data['where']['advance_salary.type'] = $data['type'];

        if(isset($data['status'])):
            if($data['status'] == 0):
                $data['where']['advance_salary.sanctioned_by'] = 0;
            elseif($data['status'] == 1):
                $data['where']['advance_salary.sanctioned_by !='] = 0;
                $data['where']['(IFNULL(advance_salary.sanctioned_amount,0) - IFNULL(advance_salary.deposit_amount,0)) > '] = 0;
            else:
                $data['where']['advance_salary.sanctioned_by !='] = 0;
                $data['where']['(IFNULL(advance_salary.sanctioned_amount,0) - IFNULL(advance_salary.deposit_amount,0)) <= '] = 0;
            endif;

            if(!empty($data['status'])):
                $data['where']['advance_salary.entry_date >='] = $this->startYearDate;
                $data['where']['advance_salary.entry_date <='] = $this->endYearDate;
            endif;
        endif;
        
        if($data['type'] == 2){
            
            $data['searchCol'][] = "employee_master.name";
            $data['searchCol'][] = "date";
            $data['searchCol'][] = "amount";
            $data['searchCol'][] = "reason";

            $columns =array('','','employee_master.emp_name','date','amount','reason');
		    //if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		    
        }elseif($data['type'] == 3){
            $data['searchCol'][] = "date";
            $data['searchCol'][] = "employee_master.name";
            $data['searchCol'][] = "facility_master.ficility_type";
            $data['searchCol'][] = "amount";
            $data['searchCol'][] = "reason";

            $columns =array('','','date','employee_master.emp_name','facility_master.ficility_type','amount','');
		    //if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		    
        }else{

            $data['searchCol'][] = "advance_salary.entry_date";
            $data['searchCol'][] = "employee_master.name";
            $data['searchCol'][] = "advance_salary.amount";
            $data['searchCol'][] = "advance_salary.reason";
            $data['searchCol'][] = "sanction.emp_name";
            $data['searchCol'][] = "advance_salary.sanctioned_at";
            $data['searchCol'][] = "advance_salary.sanctioned_amount";
            $data['searchCol'][] = "advance_salary.deposit_amount";
            $data['searchCol'][] = "(advance_salary.sanctioned_amount - advance_salary.deposit_amount)";
            
    		$columns =array('','');
            $i=2;
            foreach($data['searchCol'] as $key=>$colName):
                $columns[$i] = $colName;$i++;
            endforeach;
        }
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getAdvanceSalary($id){
        $data['tableName'] = $this->advanceSalary;        
        $data['select'] = "advance_salary.*,employee_master.emp_name,employee_master.emp_code";
        $data['leftJoin']['employee_master'] = "advance_salary.emp_id = employee_master.id";
        $data['where']['advance_salary.id'] = $id;
        return $this->row($data);
    }

    public function save($data){         
        return $this->store($this->advanceSalary,$data,'AdvanceSalary');         
    }  

    public function delete($id){
         return $this->trash($this->advanceSalary,['id'=>$id],'AdvanceSalary');
    }
    
    public function savePenalty($data){
        return $this->store($this->advanceSalary,$data,'Penalty');
    }

    public function saveFacility($data){
        return $this->store($this->advanceSalary,$data,'Facility');
    }
}
?>