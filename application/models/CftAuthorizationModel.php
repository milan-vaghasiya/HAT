<?php
class CftAuthorizationModel extends MasterModel{
    private $cftAuth = "cft_auth";
    
    public function saveCftAuth($data){
		try{
            $this->db->trans_begin();
		$queryData['select'] = "emp_id,id,sequence";
		$queryData['tableName'] = $this->cftAuth;
		$emp_ids =  $this->rows($queryData);

		$cft = '';
		if(!empty($data['emp_id'])):
			$cft = explode(',',$data['emp_id']);
		endif;
		$z=0;
		foreach($emp_ids as $key=>$value):
			if(!in_array($value->emp_id,$cft)):
			
				$upProcess['tableName'] = $this->cftAuth;
				$upProcess['where']['sequence > ']=($value->sequence - $z++);
				$upProcess['set']['sequence']='sequence, - 1';
				$q = $this->setValue($upProcess);
				$this->remove($this->cftAuth,['id'=>$value->id],'');
			endif;
		endforeach;
		foreach($cft as $key=>$value):			
			if(!in_array($value,array_column($emp_ids,'emp_id'))):
				$queryData = array();
				$queryData['select'] = "MAX(sequence) as value";
				$queryData['tableName'] = $this->cftAuth;
				$sequence = $this->specificRow($queryData)->value;

				$empdata=$this->employee->getEmp($value);
								
				$cftAuthData = [
					'id'=>"",
					'emp_id'=>$value,
					'dept_id'=>$empdata->emp_dept_id,
					'designation_id'=>$empdata->emp_designation,
					'sequence'=>(!empty($sequence))?($sequence + 1):1,
					'created_by' => $this->session->userdata('loginId')
				];
				$this->store($this->cftAuth,$cftAuthData,'');
			endif;
		endforeach;

		if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
			return ['status'=>1,'message'=>'CFT Authorization saved successfully.'];
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
		//echo "somthing is wrong. Error : ".$e->getMessage();exit;
		return ['status'=>1,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

	}

	}

    public function getCftList(){
		$data['tableName'] = $this->cftAuth;
		$data['select'] = "cft_auth.*,employee_master.emp_name,department_master.name,emp_designation.title";
		$data['join']['employee_master'] = "employee_master.id = cft_auth.emp_id";
		$data['join']['department_master'] = "department_master.id = cft_auth.dept_id";
		$data['join']['emp_designation'] = "emp_designation.id = cft_auth.designation_id";
		$data['order_by']['cft_auth.sequence'] = "ASC";
		return $this->rows($data);
	}
    
	public function updateEmpSequance($data){
		try{
            $this->db->trans_begin();
		$ids = explode(',', $data['id']);
		$i=1;
		foreach($ids as $pp_id):
			$seqData=Array("sequence"=>$i++);
			$this->edit($this->cftAuth,['id'=>$pp_id],$seqData);
		endforeach;

		$queryData['tableName'] = $this->cftAuth;
		$queryData['where']['id'] = $ids[0];
		$queryData['order_by']['sequence'] = "ASC";		
		$empSequanceRow = $this->row($queryData);
		$this->edit($this->cftAuth,['emp_id'=>$empSequanceRow->emp_id],['dept_id'=>$empSequanceRow->dept_id],['designation_id'=>$empSequanceRow->designation_id]);
		
		if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
			return ['status'=>1,'message'=>'Employee Sequence updated successfully.'];

		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
		return ['status'=>1,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

	}
	}
}
?>