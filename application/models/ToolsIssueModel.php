<?php
class ToolsIssueModel extends MasterModel{
    private $jobCard = "job_card";
    private $toolsIssueMaster = "tools_dispatch";
    private $toolsIssueTrans = "job_material_dispatch";
    private $jobUsedMaterial = "job_used_material";
    private $itemMaster = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->toolsIssueMaster;

        $data['where']['issue_date >= '] = $this->startYearDate;
		$data['where']['issue_date <= '] = $this->endYearDate;

        $data['searchCol'][] = "issue_no";
        $data['searchCol'][] = "DATE_FORMAT(issue_date,'%d-%m-%Y')";
        $data['searchCol'][] = "total_qty";

        $columns =array('','','issue_date','total_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getToolsIssue($id){
        $data['tableName'] = $this->toolsIssueMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getJobMaterial($id){
        $data['tableName'] = $this->toolsIssueTrans;
        $data['where']['tools_dispatch_id'] = $id;
        return $this->rows($data);
    }

    public function getIssueBatchTrans($id){
        $data['tableName'] = $this->toolsIssueTrans;
        $data['select'] = "job_material_dispatch.id as trans_id,job_material_dispatch.job_card_id,job_material_dispatch.tools_dispatch_id,job_material_dispatch.material_type,job_material_dispatch.collected_by,job_material_dispatch.process_id,job_material_dispatch.dispatch_date,job_material_dispatch.dispatch_item_id,job_material_dispatch.dispatch_qty as qty, job_material_dispatch.dept_id, department_master.name as dept_name, stock_transaction.location_id,stock_transaction.batch_no,location_master.store_name,location_master.location,item_master.item_name,itm.item_code as part_code";
        $data['leftJoin']['item_master'] = 'item_master.id = job_material_dispatch.dispatch_item_id';
        $data['leftJoin']['job_card'] = 'job_card.id = job_material_dispatch.job_card_id';
        $data['leftJoin']['item_master itm'] = 'itm.id = job_card.product_id';
        $data['leftJoin']['stock_transaction'] = 'job_material_dispatch.id = stock_transaction.ref_id';
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';

        $data['where']['stock_transaction.ref_no'] = $id;
        $data['where']['stock_transaction.ref_type'] = 13;
        $data['where']['stock_transaction.trans_type'] = 2;
        return $this->rows($data);
    }

    public function nextTransNo(){
        $data['select'] = "MAX(issue_no) as issue_no";
        $data['tableName'] = $this->toolsIssueMaster;
        $data['where']['issue_date >= '] = $this->startYearDate;
		$data['where']['issue_date <= '] = $this->endYearDate;
		$trans_no = $this->specificRow($data)->issue_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            if(empty($data['id'])):
                $issueNo = $this->nextTransNo();
                $toolMasterData = ['id'=>'','issue_date'=>$data['dispatch_date'],'issue_no'=>$issueNo,'total_qty'=>$data['dispatch_qty'],'remark'=>$data['remark']];
                $saveToolMaster = $this->store($this->toolsIssueMaster,$toolMasterData);
                
                foreach($data['item_data'] as $row):
                    $row = json_decode($row);

                    $issueData = [
                        'id' => "",
                        'tools_dispatch_id' => $saveToolMaster['insert_id'],
                        'job_card_id' => $row->job_card_id,
                        'material_type' => $row->material_type,
                        'collected_by' => $row->collected_by,
                        'dept_id' => $row->dept_id,
                        'process_id' => $row->process_id,
                        'dispatch_date' => $row->dispatch_date,
                        'dispatch_item_id' => $row->dispatch_item_id,
                        'dispatch_qty' => $row->qty,
                        'dispatch_by' => $data['dispatch_by'],
                        'remark' => $data['remark'],
                        'req_type' => 2,
                        'created_by'  => $data['created_by'],
                    ];
                    $saveIssueData = $this->store($this->toolsIssueTrans,$issueData);
                    $issueId = $saveIssueData['insert_id'];
                
                    $stockTrans = [
                        'id' => "",
                        'location_id' => $row->location_id,
                        'batch_no' => $row->batch_no,
                        'trans_type' => 2,
                        'item_id' => $row->dispatch_item_id,
                        'qty' => "-".$row->qty,
                        'ref_type' => 13,
                        'ref_id' => $saveIssueData['insert_id'],
                        'ref_no' => $saveToolMaster['insert_id'],
                        'ref_date' => $row->dispatch_date,
                        'created_by' => $data['created_by']
                    ];

                    $this->store('stock_transaction',$stockTrans);

                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->dispatch_item_id;
                    $setData['set']['qty'] = 'qty, - '.$row->qty;
                    $this->setValue($setData);
                endforeach;

                $result = ['status'=>1,'message'=>'Material Issue suucessfully.'];
            else:
                $this->remove('stock_transaction',['ref_no'=>$data['id'],'ref_type'=>13]);
                $issueTransData = $this->getJobMaterial($data['id']);
                
                foreach($issueTransData as $row):
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->dispatch_item_id;
                    $setData['set']['qty'] = 'qty, + '.$row->dispatch_qty;
                    $this->setValue($setData);

                    $this->store($this->toolsIssueTrans,['id'=>$row->id,'is_delete'=>1]);
                endforeach;

                $toolMasterData = ['id'=>$data['id'],'issue_date'=>$data['dispatch_date'],'total_qty'=>$data['dispatch_qty'],'remark'=>$data['remark']];
                $saveToolMaster = $this->store($this->toolsIssueMaster,$toolMasterData);

                foreach($data['item_data'] as $row):
                    $row = json_decode($row);
                    $issueData = [
                        'id' => $row->id,
                        'tools_dispatch_id' => $data['id'],
                        'job_card_id' => $row->job_card_id,
                        'material_type' => $row->material_type,
                        'collected_by' => $row->collected_by,
                        'dept_id' => $row->dept_id,
                        'process_id' => $row->process_id,
                        'dispatch_date' => $row->dispatch_date,
                        'dispatch_item_id' => $row->dispatch_item_id,
                        'dispatch_qty' => $row->qty,
                        'dispatch_by' => $data['dispatch_by'],
                        'remark' => $data['remark'],
                        'req_type' => 2,
                        'created_by'  => $data['created_by'],
                        'is_delete'=>0
                    ];
                    $saveIssueData = $this->store($this->toolsIssueTrans,$issueData);
                    $issueId = (empty($row->id))?$saveIssueData['insert_id']:$row->id;
                
                    $stockTrans = [
                        'id' => "",
                        'location_id' => $row->location_id,
                        'batch_no' => $row->batch_no,
                        'trans_type' => 2,
                        'item_id' => $row->dispatch_item_id,
                        'qty' => "-".$row->qty,
                        'ref_type' => 13,
                        'ref_id' => $issueId,
                        'ref_no' => $data['id'],
                        'ref_date' => $row->dispatch_date,
                        'created_by' => $data['created_by']
                    ];

                    $this->store('stock_transaction',$stockTrans);

                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->dispatch_item_id;
                    $setData['set']['qty'] = 'qty, - '.$row->qty;
                    $this->setValue($setData);
                endforeach;           

                $result = ['status'=>1,'message'=>'Material Issue suucessfully.'];
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

    public function delete($id){
        try{
            $this->db->trans_begin();
        $issueTransData = $this->getJobMaterial($id);
        foreach($issueTransData as $row):
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $row->dispatch_item_id;
            $setData['set']['qty'] = 'qty, + '.$row->dispatch_qty;
            $this->setValue($setData);

            $this->store($this->toolsIssueTrans,['id'=>$row->id,'is_delete'=>1]);
        endforeach;
        $this->remove('stock_transaction',['ref_no'=>$id,'ref_type'=>13]);
       
        $result = $this->trash($this->toolsIssueMaster,['id'=>$id],'Tools Issue');
        if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
    }
	
	/*  Create By : Avruti @27-11-2021 2:00 PM
		update by : 
		note : 
	*/
    //---------------- API Code Start ------//

    public function getToolsIssueListing($data){
        $queryData['tableName'] = $this->toolsIssueMaster;

        $queryData['where']['issue_date >= '] = $this->startYearDate;
		$queryData['where']['issue_date <= '] = $this->endYearDate;

        if(!empty($data['search'])):
			$queryData['like']['issue_no'] = $data['search'];
			$queryData['like']['DATE_FORMAT(issue_date,"%d-%m-%Y")'] = $data['search'];
			$queryData['like']['total_qty'] = $data['search'];
		endif;
        
		$queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        return $this->rows($queryData);
    }

    //------ API Code End -------//
}
?>