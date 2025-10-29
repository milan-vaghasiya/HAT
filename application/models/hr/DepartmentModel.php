<?php
class DepartmentModel extends MasterModel{
    private $departmentMaster = "department_master";
    private $empMaster = "employee_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->departmentMaster;
        $data['searchCol'][] = "name";
		$data['searchCol'][] = "remark";
		$columns =array('','','name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
    
	public function getDepartmentList($is_production=""){
        $data['tableName'] = $this->departmentMaster;
		if(!empty($type)){$data['where']['is_production'] = $is_production;}
        return $this->rows($data);
    }

    public function getDepartment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->departmentMaster;
        return $this->row($data);
    }

    public function getEmployees($id=0){
		if(!empty($id))
		{
			$data['where']['id'] = $id;
			$data['tableName'] = $this->empMaster;
			return $this->row($data);
		}
		else
		{
			$data['order_by']['emp_name']='ASC';
			$data['tableName'] = $this->empMaster;
			return $this->rows($data);
		}
    }
	
    public function getLeaveAuthorities($emp_ids){
        $data['select'] = 'emp_name';
        $data['where_in']['id'] = $emp_ids;
        $data['tableName'] = $this->empMaster;
		$data['resultType']='resultRows';
        return $this->specificRow($data);
    }
	
    public function getLeaveAuthority($emp_id){
        $data['select'] = 'emp_name';
        $data['where']['id'] = $emp_id;
        $data['tableName'] = $this->empMaster;
        return $this->specificRow($data);
    }
	
	//Updated  By Karmi @27/04/2022
    public function save($data){
        if($this->checkDuplicate($data['name'],$data['id']) > 0):
            $errorMessage['name'] = "Department name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result =  $this->store($this->departmentMaster,$data,'Department');

            /** Department added in store 
            $dept_id = !empty($data['id']) ? $data['id'] : $result['insert_id'];
            $strQuery['where']['other_ref'] = $dept_id;
            $strQuery['where']['store_type'] = 102;
            $strQuery['tableName'] = 'location_master';
            $strResult = $this->row($strQuery);
            if (empty($strResult)) {
                $nextlevel='';
				$level = $this->store->getNextStoreLevel(8);
				$count = count($level);
				$nextlevel = '8.'.($count+1);
				$storeData = [
					'id' => '',
					'store_name' => "Department",
					'location' => $data['name'],
					'store_type' => 102,
					'ref_id' => 8,
					'main_store_id'=>8,
					'other_ref'=>$dept_id,
					'store_level'=>$nextlevel
				];
				$this->store->save($storeData);
            }*/
        endif;
        return $result;
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->departmentMaster;
        $data['where']['name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->departmentMaster,['id'=>$id],'Department');
    }
}
?>