<?php
class PrimaryCFTModel extends MasterModel
{
    private $rejRWManage = "rej_rw_management";
    private $qc_fmea = "qc_fmea";
    private $jobTrans = "job_transaction";
    private $jobApproval = "job_approval";
    private $jobCard = "job_card";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "rej_rw_management.*,(rej_rw_management.qty-rej_rw_management.cft_qty) as pending_qty,process_master.process_name,itm.item_code as product_code,itm.item_name as product_name,itm.full_name,item_master.item_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.wo_no,rejection_comment.remark as rejection_reason";
        $data['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $data['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = rej_rw_management.rr_reason";
      
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        if($data['entry_type'] == 1){ $data['customWhere'][] = '(rej_rw_management.entry_type = 1 OR (rej_rw_management.ref_type = 2 AND rej_rw_management.entry_type != 3)) AND (rej_rw_management.qty - rej_rw_management.cft_qty) > 0'; }
        else{$data['where_in']['rej_rw_management.entry_type'] = $data['entry_type']; }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "job_card.wo_no";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(rej_rw_management.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
		$data['searchCol'][] = "rejection_comment.remark";

        $columns = array('', '', 'job_card.job_no', 'job_card.wo_no', 'itm.item_code', 'rej_rw_management.entry_date', 'process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', '', '', '', 'rejection_comment.remark');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } else {
            $data['order_by']['rej_rw_management.entry_date'] = 'DESC';
            $data['order_by']['rej_rw_management.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }

    public function getRejMovementData($id)
    {
        $queryData['tableName'] = $this->rejRWManage;
        $queryData['select'] = "rej_rw_management.*,job_transaction.process_id,job_card.process,job_card.product_id,job_transaction.job_approval_id,job_card.job_no,job_card.job_prefix,job_card.job_number,item_master.full_name,job_transaction.entry_type as job_entry_type";
        $queryData['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $queryData['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['rej_rw_management.id'] = $id;
        return $this->row($queryData);
    }

    public function getNextTagNo($entry_type, $operation_type){
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "MAX(tag_no) as tag_no";
        $data['where']['entry_type'] = $entry_type;
        $data['where']['operation_type'] = $operation_type;
        $maxNo = $this->specificRow($data)->tag_no;
        $nextTagNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextTagNo;
    }

    public function saveCFTQty($data)
    {
        try {
            $this->db->trans_begin();
            if($data['entry_type']==2){
                $data['tag_no'] = $this->getNextTagNo($data['entry_type'], $data['operation_type']);
                $data['tag_prefix'] = (($data['operation_type'] == 1) ? 'RJ/' : (($data['operation_type'] == 2) ? 'RW/' : 'OK/') ). n2y(date('Y'));
            }
            $result = $this->store($this->rejRWManage,$data);

            $setData = array();
            $setData['tableName'] = $this->rejRWManage;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['cft_qty'] = 'cft_qty, + ' . $data['qty'];
            $this->setValue($setData);

            if( $data['operation_type'] == 4){
                if($data['entry_type'] == 4 ){
                    $udCftData = $this->primaryCFT->getRejMovementData($data['ref_id']); // Select Previous CFT Data either primary or final
                    if($data['ref_type'] == 3){
                        $primaryCftData = $this->primaryCFT->getRejMovementData($udCftData->ref_id); // If UD From Final CFT then select primary cft Data
                        $cftData = $this->primaryCFT->getRejMovementData($primaryCftData->ref_id); // FROM Primary select Operator CFT Data
                    }else{
                        $cftData = $this->primaryCFT->getRejMovementData($udCftData->ref_id); //  OPerator CFT Data
                    }
                }else{
                    $cftData = $this->primaryCFT->getRejMovementData($data['ref_id']); // Operator CFT
                }
                $jobData = $this->jobcard->getJobcard($cftData->job_card_id);
                $jobProcesses = explode(",", $jobData->process);
                $aprvData = $this->processMovement->getApprovalData($cftData->job_approval_id);

                $jobTransData = [
                    'id' => '',
                    'entry_date' => $data['entry_date'],
                    'entry_type' => ($data['operation_type'] == 4) ? 0 : $data['operation_type'],
                    'ref_id' => $data['job_trans_id'],
                    'rej_rw_manag_id' => $result['insert_id'],
                    'job_card_id' => $data['job_card_id'],
                    'job_approval_id' => $aprvData->id,
                    'process_id' => $aprvData->in_process_id,
                    'product_id' => $aprvData->product_id,
                    'rr_stage' => (!empty($data['rr_stage']) ? $data['rr_stage'] : ''),
                    'rr_reason' => (!empty($data['rr_reason']) ? $data['rr_reason'] : ''),
                    'rr_by' => (!empty($data['rr_by']) ? $data['rr_by'] : ''),
                    'qty' => $data['qty'],
                    'created_by' => $this->session->userdata('loginId')
                ];
                $this->store($this->jobTrans, $jobTransData);
                $setData = array();
                $setData['tableName'] = $this->rejRWManage;
                $setData['where']['id'] = $result['insert_id'];
                $setData['set']['cft_qty'] = 'cft_qty, + ' . $data['qty'];
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $cftData->job_approval_id;
                $valid=0;
                if ($cftData->operation_type == 1) {
                    $valid=1;
                    $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - ' . $data['qty'];
                } elseif ($cftData->operation_type == 2 ) {
                    $valid=1;
                    $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $data['qty'];
                } elseif ($cftData->operation_type == 3) {
                    $valid=1;
                    $setData['set']['total_hold_qty'] = 'total_hold_qty, - ' . $data['qty'];
                }
                $setData['set']['ok_qty'] = 'ok_qty, + ' . $data['qty'];
                $this->setValue($setData);
            

                if ($jobProcesses[count($jobProcesses) - 1] == $aprvData->in_process_id) :
                    $setData = array();
                    $setData['tableName'] = $this->jobCard;
                    $setData['where']['id'] = $cftData->job_card_id;
                    $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['qty'];
                    $this->setValue($setData);
                endif;
            }
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getFmeData($data){
        $data['tableName'] = $this->qc_fmea;
		$data['select'] = "qc_fmea.*";
		$data['where_in']['qc_fmea.pfc_id'] = $data['pfc_id'];
		$data['where_in']['qc_fmea.item_id'] = $data['item_id'];
		return $this->rows($data);
    }
    
    public function getTagData($id){
        $queryData['tableName'] = $this->rejRWManage;
        $queryData['select'] = "rej_rw_management.*,party_master.party_name,job_card.product_id,job_card.job_number,item_master.full_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,machine.item_name as machine_name,employee_master.emp_name,operator_master.emp_name as operator_name,job_transaction.qty as ok_qty,rejection_comment.remark as reason,qc_fmea.parameter as process_parameters, qc_fmea.min_req, qc_fmea.max_req, qc_fmea.other_req,qc_fmea.requirement,pfc_trans.parameter";
        $queryData['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $queryData['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['leftJoin']['item_master as machine'] = "job_transaction.machine_id = machine.id";
		$queryData['leftJoin']['party_master'] = "job_transaction.vendor_id = party_master.id";
		$queryData['leftJoin']['employee_master as operator_master'] = "job_transaction.operator_id = operator_master.id";
		$queryData['leftJoin']['employee_master'] = "rej_rw_management.created_by = employee_master.id";   
        $queryData['leftJoin']['pfc_trans'] = "pfc_trans.id = rej_rw_management.rr_stage";

        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = rej_rw_management.rr_reason";
        $queryData['leftJoin']['pfc_trans qc_fmea'] = "qc_fmea.id = rej_rw_management.dimension_range";
        $queryData['where_in']['rej_rw_management.id'] = $id;
        return $this->row($queryData);
    }

    public function getUdDTRows($data)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "rej_rw_management.*,(rej_rw_management.qty-rej_rw_management.cft_qty) as pending_qty,process_master.process_name,itm.item_code as product_name,itm.full_name,job_card.job_no,job_card.job_prefix,job_card.job_number";
        $data['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $data['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        if($data['status'] == 0){ 
            $data['where_in']['rej_rw_management.operation_type'] = 5; 
            $data['customWhere'][] = '( rej_rw_management.qty - rej_rw_management.cft_qty) > 0'; 
        }else{
            $data['where_in']['rej_rw_management.entry_type'] = 4; 
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(rej_rw_management.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "itm.full_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

        $columns = array('', '', 'rej_rw_management.entry_date', 'job_card.job_number', 'itm.full_name', 'process_master.process_name', '', '', '','');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } else {
            $data['order_by']['rej_rw_management.entry_date'] = 'DESC';
            $data['order_by']['rej_rw_management.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }
}
