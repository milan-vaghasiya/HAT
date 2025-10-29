<?php
class JobWorkOrderModel extends MasterModel{
    private $jobworkOrder = "job_work_order";

    public function getNextOrderNo(){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "MAX(jwo_no) as jobOrderNo";
        $data['where']['jwo_date >= '] = $this->startYearDate;
        $data['where']['jwo_date <= '] = $this->endYearDate;
        $jobOrderNo = $this->specificRow($data)->jobOrderNo;
		$nextOrderNo = (!empty($jobOrderNo))?($jobOrderNo + 1):1;
		return $nextOrderNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "job_work_order.*,party_master.party_code,party_master.party_name,item_master.item_name,item_master.item_code";
        $data['leftJoin']['party_master'] = "party_master.id = job_work_order.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_work_order.product_id";
        if($data['status'] == 0){$data['where']['job_work_order.jwo_status'] = 0; $data['where']['job_work_order.is_close != '] = 1;} 
        if($data['status'] == 1){
            $data['where']['job_work_order.jwo_status'] = 1; 
            $data['where']['job_work_order.is_close != '] = 1;
            $data['where']['job_work_order.jwo_date >= '] = $this->startYearDate;
            $data['where']['job_work_order.jwo_date <= '] = $this->endYearDate;
        } 
        if($data['status'] == 2){
            $data['where']['job_work_order.is_close'] = 1;
            $data['where']['job_work_order.jwo_date >= '] = $this->startYearDate;
            $data['where']['job_work_order.jwo_date <= '] = $this->endYearDate;
        }

		$data['order_by']['job_work_order.jwo_date']='DESC';
		$data['order_by']['job_work_order.id']='DESC';

        $data['searchCol'][] = "DATE_FORMAT(job_work_order.jwo_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_work_order.jwo_prefix,job_work_order.jwo_no)";
        $data['searchCol'][] = "party_master.party_name"; 
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "job_work_order.qty";

		$columns =array('','','job_work_order.jwo_date','job_work_order.jwo_no','party_master.party_name','item_master.item_code','job_work_order.qty','','job_work_order.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        if(empty($data['id'])){
            $data['jwo_prefix'] = "JWO/".$this->shortYear."/";
            $data['jwo_no'] = $this->getNextOrderNo();
        }
        $result = $this->store($this->jobworkOrder,$data,"Job Work Order");
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }	
    }

    public function getJobWorkOrder($id){
        $data['tableName'] = $this->jobworkOrder;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->jobworkOrder,['id'=>$id],"Job Work Order");
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getJobworkOutData($id){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "job_work_order.*,item_master.item_name,item_master.item_code, party_master.party_name,party_master.party_address,party_master.gstin";
        $data['join']['item_master'] =  "item_master.id = job_work_order.product_id";
        $data['join']['party_master'] = "party_master.id = job_work_order.vendor_id";
        $data['where']['job_work_order.vendor_id !='] = 0;
        $data['where']['job_work_order.id'] = $id;
        return $this->row($data);
    }
    
    public function approveJobWorkOrder($data){
        $date = ($data['val'] == 1)?date('Y-m-d'):"";
    	$isApprove =  ($data['val'] == 1)?$this->loginId:0;
        $this->store($this->jobworkOrder, ['id'=> $data['id'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
        return ['status' => 1, 'message' => 'Job Work Order '.$data['msg'].' successfully.'];
    }
	
	public function changeJobStatus($data){ 
        $msg ="";
        if($data['val'] == 1){
			$msg = "Close";
		} else {
			$msg = "Reopen";
		}
		$this->store($this->jobworkOrder, ['id'=> $data['id'], 'is_close' => $data['val']]);
        return ['status' => 1, 'message' => 'Job Work Order '.$msg.' successfully.'];
    }
	/*  Create By : Avruti @27-11-2021 3:25 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount($status){
        $data['tableName'] = $this->jobworkOrder;
        if($status == 0){$data['where']['job_work_order.jwo_status'] = 0;} 
        if($status == 1){$data['where']['job_work_order.jwo_status'] = 1;} 
        return $this->numRows($data);
    }

    public function getJobWorkOrderList_api($limit, $start,$status){
        $data['tableName'] = $this->jobworkOrder;
        $data['select'] = "job_work_order.*,party_master.party_code,party_master.party_name,item_master.item_name,item_master.item_code";
        $data['leftJoin']['party_master'] = "party_master.id = job_work_order.vendor_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_work_order.product_id";
        if($status == 0){$data['where']['job_work_order.jwo_status'] = 0;} 
        if($status == 1){$data['where']['job_work_order.jwo_status'] = 1;} 
		$data['order_by']['job_work_order.jwo_date']='DESC';
		$data['order_by']['job_work_order.id']='DESC';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>