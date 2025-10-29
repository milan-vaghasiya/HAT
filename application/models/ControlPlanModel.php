<?php
class ControlPlanModel extends MasterModel{
	
    private $productDimensions = "product_dimensions";
    private $processInspection = "process_inspection";
	
	// Updated By Meghavi @10/06/2023
	public function getPreInspectionParam($postData){ 
		$data['tableName'] = $this->productDimensions;
		$data['select'] = "product_dimensions.*,process_master.process_name";		
		$data['leftJoin']['process_master'] = "process_master.id = product_dimensions.process_id";
		if(!empty($postData['item_id'])){$data['where']['product_dimensions.item_id']=$postData['item_id'];}
		// if(!empty($postData['param_type'])){$data['where']['product_dimensions.param_type']=$postData['param_type'];}
		$data['order_by']['product_dimensions.id'] = 'ASC';
		return $this->rows($data);
	}
	
    //Change By Avruti @09/08/2022
	public function savePreInspectionParam($data){
		try{
            $this->db->trans_begin();
			$result = $this->store($this->productDimensions,$data,'Inspection Parameter');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

    //Change By Avruti @09/08/2022
	public function deleteInspectionParameter($id){
		try{
			$this->db->trans_begin();
			$result = $this->trash($this->productDimensions,['id'=>$id],"Record");
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

    public function getLineDTRows($data){
        $data['tableName'] = $this->processInspection;
        $data['select'] = "process_inspection.*,item_master.item_code,item_master.item_name,process_master.process_name,job_card.job_number,job_card.job_prefix,job_card.job_no,employee_master.emp_name,party_master.party_name";
		$data['leftJoin']['item_master'] = "item_master.id = process_inspection.product_id";
		$data['leftJoin']['process_master'] = "process_master.id = process_inspection.process_id";
		$data['leftJoin']['job_card'] = "job_card.id = process_inspection.job_card_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = process_inspection.inspector_id";
		$data['leftJoin']['party_master'] = "party_master.id = process_inspection.vendor_id";
		$data['order_by']['process_inspection.insp_date'] = 'DESC';
		$data['where']['process_inspection.report_type'] = $data['report_type'];
        $data['searchCol'][] = "DATE_FORMAT(process_inspection.insp_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "process_inspection.sampling_qty";
        $data['searchCol'][] = "employee_master.emp_name";

        $columns =array('','','process_inspection.insp_date','job_card.job_number','item_master.item_code','process_master.process_name','process_inspection.sampling_qty','employee_master.emp_name');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

	// Updated By @meghavi 10/06/2023
	public function getLineInspectionData($item_id,$process_id){
		$data['tableName'] = $this->productDimensions;
		$data['select'] = "product_dimensions.*,item_master.item_name,item_master.item_code";	
		$data['leftJoin']['item_master'] = "item_master.id = product_dimensions.item_id";
		$data['where']['product_dimensions.process_id'] = $process_id;
		$data['where']['product_dimensions.item_id'] = $item_id;
		$data['where']['product_dimensions.is_line'] = 1;
		return $this->rows($data);
	}

	public function save($data){
        try{
            $this->db->trans_begin();
			$result = $this->store($this->processInspection,$data,'First Piece Inspection');
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
			$result = $this->trash($this->processInspection,['id'=>$id],'First Piece Inspection');
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

	public function getLineInspection($id){
        $data['tableName'] = $this->processInspection;
		$data['where']['id'] = $id;
        return $this->row($data);
    }
    
    //change by meghavi 25/08/2022
	public function getLineInspectionForPrint($id="",$job_card_id="",$process_id="",$insp_date=""){
        $data['tableName'] = $this->processInspection;
		$data['select'] = "process_inspection.*,item_master.item_code,item_master.item_name,item_master.drawing_no,process_master.process_name,job_card.job_number,job_card.job_prefix,job_card.job_no,employee_master.emp_name,employee_master.shift_id,shift_master.shift_name,ims.item_code as itm_cd, ims.item_name as itm_nm,party_master.party_name";
		$data['leftJoin']['item_master'] = "item_master.id = process_inspection.product_id";
		$data['leftJoin']['item_master as ims'] = "ims.id = process_inspection.machine_id";
		$data['leftJoin']['process_master'] = "process_master.id = process_inspection.process_id";
		$data['leftJoin']['job_card'] = "job_card.id = process_inspection.job_card_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = process_inspection.inspector_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = process_inspection.operator_id";
		$data['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
		$data['leftJoin']['party_master'] = "party_master.id = process_inspection.vendor_id";
		if(!empty($job_card_id)){ $data['where']['process_inspection.job_card_id'] = $job_card_id; }
		if(!empty($process_id)){ $data['where']['process_inspection.process_id'] = $process_id; }
		if(!empty($process_id)){ $data['where']['process_inspection.process_id'] = $process_id; }
		if(!empty($insp_date)){ $data['where']['process_inspection.insp_date'] = $insp_date; }
		if(!empty($id)){
			$data['where']['process_inspection.id'] = $id; 
			return $this->row($data);
		}else{ 
			return $this->rows($data); 
		}
    }

	public function getPreInspectionParamDetail($id){
		$data['tableName'] = $this->productDimensions;
		$data['select'] = "product_dimensions.*";		
	
		$data['where']['product_dimensions.id']=$id;
		return $this->row($data);
	}

	public function checkDuplicateParamSequence($postData){
        $data['tableName'] = $this->productDimensions;
        $data['where']['item_id'] = $postData['item_id'];
        if(!empty($postData['param_type'])){$data['where']['param_type'] = $postData['param_type'];}
        $data['where']['rev_no'] = $postData['rev_no'];
        $data['where']['sequence'] = $postData['sequence'];
        
		if(!empty($postData['id'])){
            $data['where']['id !='] = $postData['id'];
		}
        return $this->numRows($data);
    }

	public function getSarIprData($postData){
		$data['tableName'] = $this->processInspection;
		$data['select'] = "process_inspection.*,item_master.item_code,item_master.item_name,item_master.drawing_no,process_master.process_name,job_card.job_number,job_card.job_prefix,job_card.job_no,employee_master.emp_name,employee_master.shift_id,shift_master.shift_name,ims.item_code as itm_cd, ims.item_name as itm_nm,party_master.party_name,operator.emp_name as operator_name";
		$data['leftJoin']['item_master'] = "item_master.id = process_inspection.product_id";
		$data['leftJoin']['item_master as ims'] = "ims.id = process_inspection.machine_id";
		$data['leftJoin']['process_master'] = "process_master.id = process_inspection.process_id";
		$data['leftJoin']['job_card'] = "job_card.id = process_inspection.job_card_id";
		$data['leftJoin']['employee_master'] = "employee_master.id = process_inspection.inspector_id";
		$data['leftJoin']['employee_master as operator'] = "employee_master.id = process_inspection.operator_id";
		$data['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
		$data['leftJoin']['party_master'] = "party_master.id = process_inspection.vendor_id";
		if(!empty($postData['job_card_id'])){ $data['where']['process_inspection.job_card_id'] = $postData['job_card_id']; }
		if(!empty($postData['process_id'])){ $data['where']['process_inspection.process_id'] = $postData['process_id']; }
		if(!empty($postData['machine_id'])){ $data['where']['process_inspection.process_id'] = $postData['machine_id']; }
		if(isset($postData['limit'])){
			$data['limit']= $postData['limit'];
		}
		if(isset($postData['single_row']) && $postData['single_row'] == 1){
			return $this->row($data);
		}else{ 
			return $this->rows($data); 
		}
	
	}
	
}