<?php
class QcPRModel extends MasterModel{
    private $itemMaster = "qc_instruments";
    private $stockTrans = "stock_transaction";
    private $qc_indent = "qc_indent";
    
    public function getNextQPRNo($postData){
        $data['tableName'] = $this->qc_indent;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['qc_indent.trans_prefix'] = $postData['trans_prefix'];
        $maxNo = $this->specificRow($data)->trans_no;
        $nextQPRNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextQPRNo;
    }
 
    public function getDTRows($data){
        $data['tableName'] = $this->qc_indent;
        $data['select'] = "qc_indent.*,DATE_FORMAT(qc_indent.created_at,'%d-%m-%Y') as req_date,item_category.category_name,employee_master.emp_name as rejected_by";
        $data['leftJoin']['item_category'] = "item_category.id = qc_indent.category_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = qc_indent.rejected_by";
        
        // Set Status to 0 (Pending) if Empty
        //if(empty($data['status'])){$data['status'] = 0;}
        //$data['where']['qc_indent.status'] = $data['status'];
        
		$data['searchCol'][] = "";
		$data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(qc_indent.created_at,'%d-%m-%Y')";
		$data['searchCol'][] = "qc_indent.req_number";
		$data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "qc_indent.make";
        $data['searchCol'][] = "qc_indent.qty";
        $data['searchCol'][] = "DATE_FORMAT(qc_indent.delivery_date,'%d-%m-%Y')";
        $data['searchCol'][] = "qc_indent.reject_reason";
        
		$columns =array('','',"DATE_FORMAT(qc_indent.created_at,'%d-%m-%Y')","qc_indent.req_number",'item_category.category_name','qc_indent.make','qc_indent.qty','qc_indent.delivery_date','qc_indent.reject_reason');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }
    
    public function save($postData){
        try{
            $this->db->trans_begin();
            
            $postData['trans_prefix'] = 'QPR'.n2y(date('Y')).n2m(date('m'));
            $postData['trans_no'] = $this->getNextQPRNo($postData);
            $postData['req_number'] = $postData['trans_prefix'].str_pad($postData['trans_no'],3,"0",STR_PAD_LEFT);
            $catData = $this->itemCategory->getCategory($postData['category_id']);
            if(!empty($catData->category_type))
            {
                $reqItmCodeArr = [
                    'item_type'=>($catData->category_type == 7)? 1 : 2,
                    'category_id'=>$postData['category_id'],
                    'size'=>$postData['size']
                ];
                if($catData->category_type==6){$reqItmCodeArr['least_count']=$postData['least_count'];}
                $catNo = $this->qcPurchase->getDataForGenerateCode($reqItmCodeArr);
                $catSrNo='';
                if(!empty($catNo->cat_srno)){
                    $catSrNo = $catNo->cat_srno;
                }else{
                    $catNo = $this->qcPurchase->getDataForGenerateCode(['category_id'=>$postData['category_id']]);
                    $catSrNo = (!empty($catNo->cat_srno))?($catNo->cat_srno+1):1;
                }
                $postData['req_itm_code'] = sprintf("%03d",$catData->category_code).sprintf("-%03d",$catSrNo);
                $postData['item_type'] = ($catData->category_type == 7)? 1 : 2;
            }
            $result = $this->store($this->qc_indent,$postData,'QC Purchase Request');

            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}		
    }
	
	public function getQCPR($id){
        $data['tableName'] = $this->qc_indent;
        $data['select'] = 'qc_indent.*,item_category.category_name';
        $data['leftJoin']['item_category'] = "item_category.id = qc_indent.category_id";
		$data['where']['qc_indent.id'] = $id;
        return $this->row($data);
    }
	
	public function getQCPRList($postData){
		$data['where_in']['id'] = $postData['req_ids'];
        $data['tableName'] = $this->qc_indent;
        return $this->row($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            
            $result = $this->trash($this->qc_indent,['id'=>$id]);

            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}
    }
}