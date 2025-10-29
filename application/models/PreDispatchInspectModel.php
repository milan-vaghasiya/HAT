<?php
class PreDispatchInspectModel extends MasterModel{
    private $preDispatch = "predispatch_inspection";
	
	public function getDTRows($data){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code,item_master.item_name";
		$data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['where']['date >= '] = $this->startYearDate;
        $data['where']['date <= '] = $this->endYearDate;
		
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "predispatch_inspection.param_count";
		
		$columns =array('','','item_master.item_code','predispatch_inspection.param_count');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPreInspection($id){
        $data['tableName'] = $this->preDispatch;
		$data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->preDispatch,$data,'Predispatch Inspection');
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
        $result = $this->trash($this->preDispatch,['id'=>$id],'Predispatch Inspection');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getPreInspectionForPrint($id){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code,item_master.item_name,item_master.part_no,party_master.party_name,party_master.party_code,prepare.emp_name as prepare_by,authorize.emp_name as authorize_by,trans_child.material_grade,trans_main.doc_no,job_card.job_number";
        $data['leftJoin']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$data['leftJoin']['employee_master as prepare'] = "prepare.id = predispatch_inspection.prepare_id";
		$data['leftJoin']['employee_master as authorize'] = "authorize.id = predispatch_inspection.authorize_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = predispatch_inspection.po_no";
        $data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        // $data['leftJoin']['job_card'] = "job_card.so_trans_id = trans_child.id";
        $data['leftJoin']['job_card'] = "job_card.id = predispatch_inspection.job_id";
		$data['where']['predispatch_inspection.id'] = $id;
        return $this->row($data);
    }
	
	/*  Create By : Avruti @29-11-2021 10:10 AM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){        
        $data['tableName'] = $this->preDispatch;
        return $this->numRows($data);
    }

    public function getPreDispatchInspectList_api($limit, $start){
        $data['tableName'] = $this->preDispatch;
        $data['select'] = "predispatch_inspection.*,item_master.item_code";
		$data['join']['item_master'] = "item_master.id = predispatch_inspection.item_id";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//

    public function nextReportNo(){
        $data['select'] = "MAX(report_no) as report_no";
		$data['where']['date >= '] = $this->startYearDate;
        $data['where']['date <= '] = $this->endYearDate;
        $data['tableName'] = $this->preDispatch;
		$report_no = $this->specificRow($data)->report_no;
		$nextReportNo = (!empty($report_no))?($report_no + 1):1;
		return $nextReportNo;
    }

    public function getSalesOrderList($item_id=0){
		$data['tableName'] = 'trans_child';
		$data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.party_id";
		$data['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_child.entry_type'] = 4;

		if(!empty($item_id))
			$data['where']['trans_child.item_id'] = $item_id;
		return $this->rows($data);
	}

    public function getJobCardNo($item_id=0){
		$data['tableName'] = 'job_card';
		$data['select'] = "job_card.id,job_card.job_number";

		if(!empty($item_id))
			$data['where']['job_card.product_id'] = $item_id;
		return $this->rows($data);
	}
}
?>