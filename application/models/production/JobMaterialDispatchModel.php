<?php
class JobMaterialDispatchModel extends MasterModel{
    private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobUsedMaterial = "job_used_material";
    private $purchaseTrans = "purchase_invoice_transaction";
    private $itemMaster = "item_master"; 
    private $jobBom = "job_bom";
    private $requisitionLog = "requisition_log";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data){ 
        $data['tableName'] = $this->jobBom;
        $data['select'] = "job_bom.*,job_card.job_number,job_card.job_date,item_master.item_name";
        $data['leftJoin']['job_card'] = "job_card.id = job_bom.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_bom.ref_item_id";

        if(empty($data['status'])):
            $data['customWhere'][] = '(job_bom.req_qty - job_bom.issue_qty) > 0';
            $data['where']['approved_by >'] =0;
        else:
            $data['customWhere'][] = '(job_bom.req_qty - job_bom.issue_qty) <= 0';
        endif;
        
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "job_bom.req_qty";

        $columns =array('','','job_card.job_no','job_card.job_date','item_master.item_name','job_bom.req_qty','','','','','');
        if(isset($data['order'])):
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        endif;            
        return $this->pagingRows($data);
    }

    public function getJobMaterial($id){
        $data['tableName'] = $this->jobBom;
        $data['select'] = "job_bom.*,job_card.job_number,job_card.job_date,item_master.item_name";
        $data['leftJoin']['job_card'] = "job_card.id = job_bom.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_bom.ref_item_id";
        $data['where']['job_bom.id'] = $id;
        return $this->row($data);
    }

    public function getIssueBatchTrans($job_bom_id,$job_card_id){
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*,location_master.store_name,location_master.location,(job_bom.issue_qty-job_bom.received_qty) as pending_receive";
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['leftJoin']['job_bom'] = 'job_bom.id = stock_transaction.trans_ref_id';
        $data['where']['stock_transaction.ref_id'] = $job_card_id;
        $data['where']['stock_transaction.trans_ref_id'] = $job_bom_id;
        $data['where']['stock_transaction.ref_type'] = 3;
        $data['where']['stock_transaction.trans_type'] = 2;
        return $this->rows($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            $jobData = $this->jobcard->getJobcard($data['job_card_id']);
           
            $stockMinusQuery = [
                'id' =>$data['id'],
                'location_id'=>$data['location_id'],
                'batch_no' => $data['batch_no'],
                'tc_no' => $data['tc_no'],
                'trans_type' => 2,
                'item_id' => $data['dispatch_item_id'],
                'qty' => '-'.$data['qty'],
                'ref_type' => 3,
                'ref_id' => $data['job_card_id'],
                'trans_ref_id' => $data['job_bom_id'],
                'ref_no' =>$jobData->job_number,
                'ref_date' => $data['dispatch_date'],
                'created_by' => $data['created_by'],
            ];
            $issueTrans = $this->store('stock_transaction', $stockMinusQuery);

            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['id'] = $data['job_bom_id'];
            $setData['set']['issue_qty'] = 'issue_qty, + ' .  $data['qty'];
            $this->setValue($setData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Material Issue suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id){
        try {
            $this->db->trans_begin();

            $transRow = $this->getStockTransRow($id);
            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['id'] = $transRow->trans_ref_id;
            $setData['set']['issue_qty'] = 'issue_qty, - ' .  abs($transRow->qty);
            $this->setValue($setData);
            $this->trash($this->stockTrans,['id'=> $id]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return ['status' => 1, 'message' => 'Qty updated suucessfully.'];
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getStockTransRow($id){
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*";
        $data['where']['stock_transaction.id'] = $id;
        return $this->row($data);
    }
}
?>