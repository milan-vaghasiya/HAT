<?php
class ProcessMovementModel extends MasterModel
{
    private $jobApproval = "job_approval";
    private $jobTrans = "job_transaction";
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $stockTransaction = "stock_transaction";
    private $rejRWManage = "rej_rw_management";
    private $job_heat_trans = "job_heat_trans";

    public function getProcessWiseApprovalData($job_card_id, $process_id, $type = 1, $ref_id = '')
    {
        $data['tableName'] = $this->jobApproval;
        $data['select'] = "job_approval.*,(CASE WHEN job_approval.in_process_id = 0 THEN 'Raw Material' ELSE ipm.process_name END) as in_process_name";
        $data['leftJoin']['process_master as ipm'] = "job_approval.in_process_id = ipm.id";
        $data['where']['job_approval.job_card_id'] = $job_card_id;
        $data['where']['job_approval.in_process_id'] = $process_id;
        $data['where']['job_approval.trans_type'] = $type;
        if (!empty($ref_id)) {
            $data['where']['job_approval.ref_id'] = $ref_id;
        } 
        $result = $this->row($data);

        if (!empty($result)) :
            $vendorData = array();
            $vendorData['tableName'] = $this->jobTrans;
            $vendorData['select'] = "job_transaction.vendor_id,(CASE WHEN job_transaction.vendor_id = 0 THEN 'In House' ELSE party_master.party_name END) as party_name";
            $vendorData['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
            $vendorData['leftJoin']['job_approval'] = "job_approval.id = job_transaction.job_approval_id";
            $vendorData['where']['job_transaction.job_card_id'] = $job_card_id;
            $vendorData['where']['job_transaction.process_id'] = $process_id;
            $vendorData['where']['job_transaction.entry_type'] = 6;
            $vendorData['where']['job_approval.trans_type'] = $type;
            $vendorData['group_by'][] = ['job_transaction.vendor_id'];
            $vndData = $this->rows($vendorData);

            $result->vendor = (!empty($vndData)) ? implode(",<br> ", array_unique(array_column($vndData, 'party_name'))) : "In House";
        endif;
        return $result;
    }
    
	/*************************************************************************************
	* Get Job Approval Data By Job Card ID
	*
	* @param	Required	Int	$job_card_id : JOB CARD ID (Primary Key Of Job Card Table)
	* @param	Required	Int	$type : Approval Type (Regular/Rework)
	* @param	Optional	Int	$ref_id : Ref Id of Rework From (Job Approval ID)
	*
	* @return 	Object : Data object of Job Approval
	*
	* @CreatedBy : Jaldeep Patel => 01-10-2022
	* @UpdatedBy :  => 
	* @UsedAt : Jobcard->jobDetail
	*************************************************************************************/
	public function getApprovalDataByJob($job_card_id,$type,$ref_id='')
    {
        $data['tableName'] = $this->jobApproval;
        $data['select'] = "job_approval.*,(CASE WHEN job_approval.in_process_id = 0 THEN 'Raw Material' ELSE ipm.process_name END) as process_name";
        $data['leftJoin']['process_master as ipm'] = "job_approval.in_process_id = ipm.id";
        $data['where']['job_approval.job_card_id'] = $job_card_id;
        $data['where']['job_approval.trans_type'] = $type;
        if (!empty($ref_id)) {$data['where']['job_approval.ref_id'] = $ref_id;}
        $result = $this->rows($data);
        return $result;
    }

    public function getSendTo($job_id){
        $queryData = array();
        $queryData['tableName'] = "requisition_log";
        $queryData['where']['log_type'] = 1;
        $queryData['where']['reqn_type'] = 3;
        $queryData['where']['req_from'] = $job_id;
        $queryData['limit'] = 1;
        $requisitionLogData = $this->row($queryData);
        return  $requisitionLogData;
    }

    public function getProcessMovementTrans($approval_id){
        $queryData = array();
        $queryData['tableName'] = $this->jobTrans;
        $queryData['select'] = "job_transaction.*,item_master.item_code,item_master.item_name,party_master.party_code,party_master.party_name";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_transaction.machine_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $queryData['where']['job_transaction.entry_type'] = 6;
        $queryData['where']['job_transaction.job_approval_id'] = $approval_id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function saveProcessMovement($data){
        try {
            $this->db->trans_begin();
            $aprvData = $this->getApprovalData($data['job_approval_id']);
            $bomData = $this->jobcard->getJobBomRawMaterialData($aprvData->job_card_id);
            $issueQty = $data['qty'];

            if(empty($aprvData->in_process_id)){
                $issueQty = $bomData->qty * $data['qty'];
            }else{
                $issueQty = $data['qty'];
            }
            if($aprvData->trans_type == 1){            
                $heatData = $this->processMovement->getHeatData(['job_approval_id'=>$data['job_approval_id'],'tc_no'=>$data['batch_no'],'single_row'=>1]);
                $pendingQty = $heatData->in_qty - $heatData->out_qty;
                
                if(round($issueQty,2) > round($pendingQty,2)){
                    $errorMessage['qty'] = "Qty not avalible for movement.".round($issueQty,2).'>'.round($pendingQty,2);
                    return ['status'=>0,'message'=>$errorMessage];
                }
            }

            $data['process_id'] = $aprvData->out_process_id;
            $data['job_card_id'] = $aprvData->job_card_id;
            $data['product_id'] = $aprvData->product_id;
            $data['w_pcs'] = $bomData->qty;
            $data['total_weight'] = $bomData->qty*$data['qty'];
            $result = $this->store($this->jobTrans, $data,'Movement');

            /*** Update out qty & rejection qty in current process ***/
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $data['job_approval_id'];
            $setData['where']['trans_type'] = $aprvData->trans_type;
            $setData['set']['total_out_qty'] = 'total_out_qty, + ' . $data['qty'];
            $this->setValue($setData);

            
            /*** If First Process then Maintain Batchwise Rowmaterial ***/
            if (empty($aprvData->in_process_id)) :
                $setData = array();
                $setData['tableName'] = $this->jobBom;
                $setData['where']['id'] = $bomData->id;
                $setData['set']['used_qty'] = 'used_qty, + ' . $issueQty;
                $this->setValue($setData);
            endif;

            $setData = array();
            $setData['tableName'] = $this->job_heat_trans;
            $setData['where']['job_approval_id'] = $data['job_approval_id'];
            $setData['where']['tc_no'] = $data['batch_no'];
            $setData['set']['out_qty'] = 'out_qty, + ' . $issueQty;
            $this->setValue($setData);

            /*** If Next Process then Add in qty ***/
            if(!empty($aprvData->out_process_id)) :

                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['in_process_id'] = $aprvData->out_process_id;
                $setData['where']['job_card_id'] = $aprvData->job_card_id;
                $setData['where']['trans_type'] = ($aprvData->trans_type == 2)?(($aprvData->out_process_id == $aprvData->process_ref_id)?1:2):1;
                if($data['send_to'] == 0):
                    $setData['set']['inward_qty'] = 'inward_qty, + ' . $data['qty'];
                else:
                    $setData['set']['outward_qty'] = 'outward_qty, + ' . $data['qty'];
                    // $setData['set']['in_qty'] = 'in_qty, + ' . $data['qty'];
                endif;
                $this->setValue($setData);

                $nxtAprvData = $this->getProcessWiseApprovalData($aprvData->job_card_id,$aprvData->out_process_id);
                $setData = array();
                $setData['tableName'] = $this->job_heat_trans;
                $setData['where']['job_approval_id'] = $nxtAprvData->id;
                $setData['where']['tc_no'] = $data['batch_no'];
                $setData['set']['in_qty'] = 'in_qty, + ' . $data['qty'];
                $this->setValue($setData);
            endif;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteMovement($id){
        try {
            $this->db->trans_begin(); 

            $queryData = array();
            $queryData['tableName'] = $this->jobTrans;
            $queryData['where']['id'] = $id;
            $transData = $this->row($queryData);

           
            if(!empty($transData)):
                $aprvData = $this->getApprovalData($transData->job_approval_id);
                $bomData = $this->jobcard->getJobBomRawMaterialData($transData->job_card_id);
                $rmQty = $transData->qty;
                if(empty($aprvData->in_process_id)){
                    $rmQty = $transData->qty*$bomData->qty;
                }
            
                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $transData->job_card_id;
                $queryData['where']['in_process_id'] = $transData->process_id;
                $queryData['where']['trans_type'] = 1;
                $nextApproval = $this->row($queryData);

                $inwardQty = ($transData->send_to == 0)?(($nextApproval->inward_qty + $nextApproval->outward_qty) - $nextApproval->in_qty):$nextApproval->outward_qty;
                
                if(!empty($nextApproval) && $inwardQty < $transData->qty):
                    return ['status' => 2, 'message' => "You can't delete this movement because This movement accepted to next process."];
                endif;


                /*** Update out qty in current process ***/
                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $transData->job_approval_id;
                // $setData['where']['trans_type'] = 1;
                $setData['set']['total_out_qty'] = 'total_out_qty, - ' . $transData->qty;
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->job_heat_trans;
                $setData['where']['job_approval_id'] = $transData->job_approval_id;
                $setData['where']['tc_no'] = $transData->batch_no;
                $setData['set']['out_qty'] = 'out_qty, - ' . $rmQty;
                $this->setValue($setData);

                if (empty($aprvData->in_process_id)) :
                    $setData = array();
                    $setData['tableName'] = $this->jobBom;
                    $setData['where']['id'] = $bomData->id;
                    $setData['set']['used_qty'] = 'used_qty, - ' . $rmQty;
                    $this->setValue($setData);
                endif;

                /*** If Next Process then Remove in qty ***/
                if (!empty($aprvData->out_process_id)) :
                    $setData = array();
                    $setData['tableName'] = $this->jobApproval;
                    $setData['where']['in_process_id'] = $aprvData->out_process_id;
                    $setData['where']['job_card_id'] = $aprvData->job_card_id;
                    $setData['where']['trans_type'] = 1;
                    if($transData->send_to == 0):
                        $setData['set']['inward_qty'] = 'inward_qty, - ' . $transData->qty;
                    else:
                        $setData['set']['outward_qty'] = 'outward_qty, - ' . $transData->qty;
                        // $setData['set']['in_qty'] = 'in_qty, - ' . $transData->qty;
                    endif;
                    $this->setValue($setData);

                    $nxtAprvData = $this->getProcessWiseApprovalData($aprvData->job_card_id,$aprvData->out_process_id);
                    $setData = array();
                    $setData['tableName'] = $this->job_heat_trans;
                    $setData['where']['job_approval_id'] = $nxtAprvData->id;
                    $setData['where']['tc_no'] = $transData->batch_no;
                    $setData['set']['in_qty'] = 'in_qty, - ' . $transData->qty;
                    $this->setValue($setData);

                endif;

                $result = $this->trash($this->jobTrans,['id'=>$id],'Movement');
                $result['job_approval_id'] = $aprvData->id;
            else:
                return ['status' => 2, 'message' => "somthing is wrong. Error : Data already deleted."];
            endif;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function saveAcceptedQty($data){
        try{
            $this->db->trans_begin(); 

            /*** Update In qty in current process ***/
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $data['job_approval_id'];
            $setData['where']['trans_type'] = $data['trans_type'];
            $setData['set']['in_qty'] = 'in_qty, + ' . $data['in_qty'];
            $this->setValue($setData);

            $result = ['status'=>1,'message'=>'Inward Qty accepted successfully.'];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getApprovalData($id)
    {
        $data['tableName'] = $this->jobApproval;
        $data['select'] = "job_approval.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,(CASE WHEN job_approval.in_process_id = 0 THEN 'Raw Material' ELSE ipm.process_name END) as in_process_name, opm.process_name as out_process_name,product_process.finished_weight";
        $data['leftJoin']['job_card'] = "job_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_approval.product_id = item_master.id";
        $data['leftJoin']['process_master AS ipm'] = "job_approval.in_process_id = ipm.id";
        $data['leftJoin']['process_master AS opm'] = "job_approval.out_process_id = opm.id";
        $data['leftJoin']['product_process'] = "product_process.item_id = job_approval.product_id AND product_process.process_id = job_approval.in_process_id";
        $data['where']['job_approval.id'] = $id;
        return $this->row($data);
    }

    public function save($movementData){
        try {
            $this->db->trans_begin();
            $aprvData = $this->getApprovalData($movementData['job_approval_id']);
            $totalProdQty = $movementData['rej_qty'] + $movementData['rw_qty'] + $movementData['hold_qty'] + $movementData['qty'];
            $totalRejQty = $movementData['rej_qty'];
            $totalRWQty = $movementData['rw_qty'];
            $totalHoldQty = $movementData['hold_qty'];
            $transType = $movementData['trans_type'];
            $rejData = $movementData['rejection_reason'];
            $rwData = $movementData['rework_reason'];
            unset($movementData['rej_qty'], $movementData['rw_qty'], $movementData['hold_qty'],$movementData['trans_type'],$movementData['rejection_reason'],$movementData['rework_reason']);
            /*** Save Movement */
            if (!empty($movementData)) {
                $saveInward = $this->store($this->jobTrans, $movementData);
            }
            /*** Save Rejection Log */
            if (!empty($totalRejQty)) {
                foreach($rejData as $row):
                    $row['id'] = "";
                    $row['entry_type'] = 1;
                    $row['operation_type'] = 1;
                    $row['entry_date'] = $movementData['entry_date'];
                    $row['job_card_id'] = $movementData['job_card_id'];
                    $row['job_trans_id'] = $saveInward['insert_id'];
                    $row['created_by'] = $movementData['created_by'];
                    $this->store($this->rejRWManage, $row);
                endforeach;
                /* $rejData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 1,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalRejQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $rejData); */
            }

            if (!empty($totalRWQty)) {
                foreach($rwData as $row):
                    $row['id'] = "";
                    $row['entry_type'] = 1;
                    $row['operation_type'] = 2;
                    $row['entry_date'] = $movementData['entry_date'];
                    $row['job_card_id'] = $movementData['job_card_id'];
                    $row['job_trans_id'] = $saveInward['insert_id'];
                    $row['created_by'] = $movementData['created_by'];

                    $this->store($this->rejRWManage, $row);
                endforeach;

                /* $rwData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 2,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalRWQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $rwData); */
            }

            if (!empty($totalHoldQty)) {
                $holdData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 3,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalHoldQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $holdData);
            }
            
            /*** Update out qty & rejection qty in current process ***/
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $movementData['job_approval_id'];
            $setData['where']['trans_type'] = $transType;
            if(!empty($movementData['vendor_id'])):
                $setData['set']['v_prod_qty'] = 'v_prod_qty, + ' . $totalProdQty;
            else:
                $setData['set']['ih_prod_qty'] = 'ih_prod_qty, + ' . $totalProdQty;
            endif;
            $setData['set']['ok_qty'] = 'ok_qty, + ' . $movementData['qty'];
            $setData['set']['total_rejection_qty'] = 'total_rejection_qty, + ' . $totalRejQty;
            $setData['set']['total_rework_qty'] = 'total_rework_qty, + ' . $totalRWQty;
            $setData['set']['total_hold_qty'] = 'total_hold_qty, + ' . $totalHoldQty;
            $setData['set']['total_prod_qty'] = 'total_prod_qty, + ' . $totalProdQty;
            $this->setValue($setData);

            /** Set Qty REj RW MANAGEMENT  */
            if($aprvData->out_process_id == $aprvData->process_ref_id && $transType == 2) :
                $setData = array();
                $setData['tableName'] = $this->rejRWManage;
                $setData['where']['id'] = $aprvData->ref_id;
                $setData['set']['cft_qty'] = 'cft_qty, + ' . ($movementData['qty']+$totalRejQty+$totalRWQty+$totalHoldQty);
                $this->setValue($setData);
            endif;

            $jobData = $this->jobcard->getJobcard($movementData['job_card_id']);

            $jobProcesses = explode(",", $jobData->process);
            

            /*** If Lst Process then Maintain Unstored Qty ***/
            if ($jobProcesses[count($jobProcesses) - 1] == $aprvData->in_process_id) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $movementData['job_card_id'];
                $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $movementData['qty'];
                $this->setValue($setData);
            endif;

            /***If Entry type 4 Vendor inward ****/
            if ($movementData['entry_type'] == 4) {
                $setData = array();
                $setData['tableName'] = $this->jobTrans;
                $setData['where']['id'] = $movementData['ref_id'];
                $setData['set']['outsource_qty'] = 'outsource_qty, + ' . ($movementData['qty'] + $totalRejQty + $totalRWQty + $totalHoldQty);
                $this->setValue($setData);

                $transData = $this->processMovement->getOutwardTransPrint($movementData['ref_id']);
                $pendingQty = $transData->qty - $transData->outsource_qty;
            } else {
                $jobApproveData = $this->getApprovalData($movementData['job_approval_id']);
                $pendingQty = $jobApproveData->inward_qty - $jobApproveData->ih_prod_qty;
            }
            $result = ['status' => 1, 'message' => 'Outward saved successfully.', 'outwardTrans' => $this->getOutwardTrans($movementData['job_approval_id'], $movementData['entry_type'])['htmlData'], 'insert_id' => $saveInward['insert_id'], 'pending_qty' => $pendingQty];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getOutwardTrans($id, $entry_type = 0)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,(CASE WHEN job_transaction.send_to = 0 THEN 'In House' ELSE party_master.party_name END) as vendor_name,employee_master.emp_name,mc.item_name as machine_name,mc.item_code as machine_code,shift_master.shift_name,job_approval.trans_type";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id	 = job_approval.id";
        $data['leftJoin']['party_master'] = "job_transaction.send_to = party_master.id";
        $data['leftJoin']['employee_master'] = "job_transaction.operator_id = employee_master.id";
        $data['leftJoin']['shift_master'] = "shift_master.id = job_transaction.shift_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = job_transaction.machine_id";
        // $data['leftJoin']['rej_rw_management as jt'] = 'jt.job_trans_id = job_transaction.id AND jt.entry_type=1';
        // $data['where']['jt.entry_type'] = 1;
        $data['where']['job_transaction.job_approval_id'] = $id;
        $data['where']['job_transaction.rej_rw_manag_id'] = 0;
        $data['where_in']['job_transaction.entry_type'] = $entry_type;
        $result = $this->rows($data);
        // print_r($this->db->last_query());
        $masterOption = $this->getMasterOptions();
        $dataRow = array();
        $html = "";
        if (!empty($result)) :
            $i = 1;
            foreach ($result as $row) :
                if (!empty($row->id)) :
                    $transDate = date("d-m-Y", strtotime($row->entry_date));

                    $rejData = $this->getRejCFTData(['job_trans_id'=>$row->id,'entry_type'=>1]);
                    $transType = ($row->entry_type == 1) ? "Regular" : "";
                    $deleteBtn = '';
                    if (empty($row->accepted_by)) :
                        $printBtn = '';
                        if($rejData->rej_qty > 0):
                            $printBtn .= '<a href="' . base_url('production/jobcard/printTag/REJ/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-dark waves-effect waves-light mr-1" title="Rejection Tag"><i class="fas fa-print"></i></a>';
                        endif;
                        if($rejData->rw_qty > 0):
                            $printBtn .= '<a href="' . base_url('production/jobcard/printTag/REW/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-1" title="Rework Tag"><i class="fas fa-print"></i></a>';
                        endif;
                        if($rejData->hold_qty > 0):
                            $printBtn .= '<a href="' . base_url('production/jobcard/printTag/SUSP/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-warning waves-effect waves-light mr-1" title="Suspected Tag"><i class="fas fa-print"></i></a>';
                        endif;

                        $functionName = '"delete"';
                        $deleteBtn = "<button type='button' onclick='trashOutward(" . $row->id . "," . $functionName . ");' class='btn btn-sm btn-outline-danger waves-effect waves-light permission-remove' title='Delete'><i class='ti-trash'></i></button>";
                    endif;
                    $machineName = (!empty($row->machine_code) ? '[' . $row->machine_code . '] ' : "") . $row->machine_name;
                    $html .='<tr class="text-center">
                                <td>' . $i++ . '</td>
                                <td>' . $transDate . '</td>
                                <td>' . $row->cycle_time . '</td>
                                <td>' . $row->start_time . '</td>
                                <td>' . $row->production_time . '</td>
                                <td class="text-left">' . $machineName . '</td>
                                <td class="text-left">' . $row->emp_name . '</td>
                                <td>' . $row->shift_name . '</td>
                                <td>' . $row->qty . '</td>
                                <td>' . $rejData->rej_qty . '</td>
                                <td>' . $rejData->rw_qty . '</td>
                                <td>' . $rejData->hold_qty . '</td>
                                <td>' . $row->remark . '</td>
                                <td>' . $printBtn . $deleteBtn . '</td>
                            </tr>';
                    $dataRow[] = $row;
                endif;
            endforeach;
        else :
            $html = '<td colspan="' . ((!empty($masterOption->op_mc_shift)  && $masterOption->op_mc_shift == 1) ? 12 : 11) . '" class="text-center">No Data Found.</td>';
        endif;
        return ['htmlData' => $html, 'outwardTrans' => $dataRow];
    }

    public function delete($id)
    {
        try {
           
            $this->db->trans_begin();
            $data['tableName'] = $this->jobTrans;
            $data['where']['id'] = $id;
            $inwardData = $this->row($data);
            // print_r($inwardData);exit;

            $rejQuery['tableName'] = $this->rejRWManage;
            $rejQuery['select'] = "ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 1 THEN qty END),0) as rej_qty,ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 2  THEN qty END),0) as rw_qty,ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 3 THEN qty END),0) as hold_qty";
            $rejQuery['where']['job_trans_id'] = $id;
            $rejData = $this->row($rejQuery);


            $jobData = $this->jobcard->getJobcard($inwardData->job_card_id);
            $processApproval = $this->getApprovalData($inwardData->job_approval_id);

            if (empty($processApproval->out_process_id) && $inwardData->qty > $jobData->unstored_qty) :
                return ['status' => 0, 'message' => "You can't delete this outward because This outward is Stored"];
            elseif(!empty($processApproval->out_process_id)) :
                $pendingQty = $processApproval->ok_qty - $processApproval->total_out_qty;
                $cftData = $this->getCftQtyFromRejectionTble($id);
                if ($inwardData->qty > $pendingQty || !empty($cftData->cft_qty)) :
                    return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                endif;
            endif;
            
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $inwardData->job_approval_id;
            if(!empty($inwardData->vendor_id)):
                $setData['set']['v_prod_qty'] = 'v_prod_qty, - ' . ($inwardData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty);
            else:
                $setData['set']['ih_prod_qty'] = 'ih_prod_qty, - ' . ($inwardData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty);
            endif;
            $setData['set']['ok_qty'] = 'ok_qty, - ' . $inwardData->qty;
            $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - ' . $rejData->rej_qty;
            $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $rejData->rw_qty;
            $setData['set']['total_hold_qty'] = 'total_hold_qty, - ' . $rejData->hold_qty;
            $setData['set']['total_prod_qty'] = 'total_prod_qty, - ' . ($inwardData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty);
            $this->setValue($setData);

            /** Set Qty REj RW MANAGEMENT  */
            if($processApproval->out_process_id == $processApproval->process_ref_id && $processApproval->trans_type == 2) :
                $setData = array();
                $setData['tableName'] = $this->rejRWManage;
                $setData['where']['id'] =  $processApproval->ref_id;
                $setData['set']['cft_qty'] = 'cft_qty, + ' . $inwardData->qty;
                $this->setValue($setData);
            endif;

            /*** If First Process then Maintain Batchwise Rowmaterial ***/
            $jobProcesses = explode(",", $jobData->process);
            if ($inwardData->process_id == $jobProcesses[0]  && $processApproval->trans_type == 1) :
                /* Update Used Stock in Job Material Used */
                $setData = array();
                $setData['tableName'] = $this->jobBom;
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                $setData['set']['used_qty'] = 'used_qty, - (' . ($inwardData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty) . '* qty)';
                $qryresult = $this->setValue($setData);
                $this->remove($this->stockTransaction, ['trans_type' => 2, 'trans_ref_id' => $id, 'ref_type' => 21, 'ref_id' => $inwardData->job_card_id]);
            endif;

            if ($jobProcesses[count($jobProcesses) - 1] == $inwardData->process_id) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $inwardData->job_card_id;
                $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $inwardData->qty;
                $this->setValue($setData);
            endif;

            // $this->trash($this->jobTrans, ['ref_id' => $id,'entry_type'=>1]);
            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 1], 'Rejection');
            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 2], 'Rework');
            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 3], 'Hold');
            $result = $this->trash($this->jobTrans, ['id' => $id], 'Outward');

            $result['outwardTrans'] = $this->getOutwardTrans($inwardData->job_approval_id)['htmlData'];
            $result['job_approval_id'] = $inwardData->job_approval_id;
            if ($inwardData->entry_type == 4) {
                $setData = array();
                $setData['tableName'] = $this->jobTrans;
                $setData['where']['id'] = $inwardData->ref_id;
                $setData['set']['outsource_qty'] = 'outsource_qty, - ' . ($inwardData->qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty);
                $this->setValue($setData);

                $transData = $this->processMovement->getOutwardTransPrint($inwardData->ref_id);
                $pendingQty = $transData->qty - $transData->outsource_qty;
            } else {
                $jobApproveData = $this->getApprovalData($inwardData->job_approval_id);
                $pendingQty = $jobApproveData->inward_qty - $jobApproveData->ih_prod_qty;
            }

            $result['pending_qty'] = $pendingQty;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getStoreLocationTrans($id)
    {
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location,item_master.full_name";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['where']['stock_transaction.ref_type'] = 7;
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.trans_type'] = 1;
        // $queryData['where']['stock_transaction.ref_batch'] = NULL;
        $stockTrans = $this->rows($queryData);
        $html = '';
        if (!empty($stockTrans)) :
            $i = 1;
            foreach ($stockTrans as $row) :
               
                $stockData = $this->store->getItemStockBatchWise(['location_id'=>$row->location_id,'batch_no'=>$row->batch_no,'tc_no'=>$row->tc_no,'item_id'=>$row->item_id,'single_row'=>1]);
                $deleteBtn = '';
                if( round($stockData->qty,3) >= round($row->qty,3)){
                    $deleteBtn = '<button type="button" onclick="trashStockTrans(' . $row->id . ');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                }
                $html .= '<tr>
                            <td class="text-center" style="width: 5%;">' . $i++ . '</td>
                            <td class="text-center">' . $row->batch_no . '</td>
                            <td class="text-center">' . $row->tc_no . '</td>
                            <td class="text-center">[ ' . $row->store_name . ' ] ' . $row->location . '</td>
                            <td class="text-center">' . $row->qty . '</td>
                            <td class="text-center" style="width: 8%;">' . $deleteBtn . '</td>
                        </tr>';
            endforeach;
        else :
            $html .= '<tr>
                        <td class="text-center" colspan="6">No Data Found.</td>
                    </tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $stockTrans];
    }

    public function saveStoreLocation($data)
    {
        try {
            $this->db->trans_begin();
            $jobData = $this->jobcard->getJobcard($data['job_id']);
            $heatData = $this->processMovement->getHeatData(['job_approval_id'=>$data['ref_id'],'tc_no'=>$data['batch_no'],'single_row'=>1]);
            $pendingQty = $heatData->in_qty - $heatData->out_qty;
            if($data['qty'] > $pendingQty){
                $errorMessage['qty'] = "Qty not avalible for movement.";
                return ['status'=>0,'message'=>$errorMessage];
            }

            $stockTrans = [
                'id' => "",
                'location_id' => $this->RTD_STORE->id,
                'batch_no' => $jobData->job_number,
                'tc_no' => $data['batch_no'],
                'trans_type' => 1,
                'item_id' => $data['product_id'],
                'qty' => $data['qty'],
                'ref_type' => 7,
                'ref_id' => $data['job_id'],
                'ref_no' => $jobData->job_number,
                'trans_ref_id' => $data['ref_id'],
                'ref_date' => $data['trans_date'],
                'ref_batch' => $jobData->rm_material_grade,
                'created_by' => $data['created_by']
            ];
            $this->store($this->stockTransaction, $stockTrans);

            $setData = array();
            $setData['tableName'] = $this->jobCard;
            $setData['where']['id'] = $data['job_id'];
            $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $data['qty'];
            $setData['set']['total_out_qty'] = 'total_out_qty, + ' . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['total_out_qty'] = 'total_out_qty, + ' . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->job_heat_trans;
            $setData['where']['job_approval_id'] = $data['ref_id'];
            $setData['where']['tc_no'] = $data['batch_no'];
            $setData['set']['out_qty'] = 'out_qty, + ' .  $data['qty'];
            $this->setValue($setData);

            $queryData = array();
            $queryData['tableName'] = $this->jobApproval;
            $queryData['where']['id'] = $data['ref_id'];
            $approvalData = $this->row($queryData);

            $queryData = array();
            $queryData['tableName'] = $this->jobApproval;
            $queryData['select'] = "SUM(total_rejection_qty) as total_rejection_qty";
            $queryData['where']['job_card_id'] = $data['job_id'];
            $rejData = $this->row($queryData);

            $jobCardData = $this->jobcard->getJobCard($data['job_id']);
            $totalQty = $rejData->total_rejection_qty + $approvalData->ok_qty;
            if ($totalQty >= $jobCardData->qty):
                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 4]);
            endif;

            $result = ['status' => 1, 'message' => "Stock Transfer successfully.", 'htmlData' => $this->getStoreLocationTrans($data['job_id'])['htmlData'], 'unstored_qty' => $jobCardData->unstored_qty];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteStoreLocationTrans($id)
    {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTransaction;
            $queryData['where']['id'] = $id;
            $stockTrans = $this->row($queryData);
            if (!empty($stockTrans)) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $stockTrans->ref_id;
                $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $stockTrans->qty;
                $setData['set']['total_out_qty'] = 'total_out_qty, - ' . $stockTrans->qty;
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $stockTrans->trans_ref_id;
                $setData['set']['total_out_qty'] = 'total_out_qty, - ' . $stockTrans->qty;
                $this->setValue($setData);

                $batchData = explode('~',$stockTrans->tc_no);
                
                if(!empty($batchData[0])){
                    $setData = array();
                    $setData['tableName'] = $this->job_heat_trans;
                    $setData['where']['job_approval_id'] =  $stockTrans->trans_ref_id;
                    $setData['where']['tc_no'] = $stockTrans->tc_no;
                    $setData['set']['out_qty'] = 'out_qty, - ' .  $stockTrans->qty;
                    $this->setValue($setData);
                }
                

                $jobCardData = $this->jobcard->getJobCard($stockTrans->ref_id);

                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 2]);
                $this->remove($this->stockTransaction, ['id' => $id]);


                $result = ['status' => 1, 'message' => 'Stock Transaction deleted successfully.', 'htmlData' => $this->getStoreLocationTrans($stockTrans->ref_id)['htmlData'], 'unstored_qty' => $jobCardData->unstored_qty, 'ref_id' => $stockTrans->ref_id];
            else :
                $result = ['status' => 0, 'message' => 'Stock transaction already deleted.'];
            endif;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getRejRWBy($data)
    {
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "job_transaction.vendor_id,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $queryData['where']['job_transaction.product_id'] = $data['part_id'];
        $queryData['where']['job_transaction.process_id'] = $data['process_id'];
        $queryData['where']['job_transaction.job_card_id'] = $data['job_card_id'];
        $queryData['group_by'][] = ['job_transaction.vendor_id'];
        return $this->rows($queryData);
    }

    public function getOutwardTransPrint($id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,party_master.party_name,job_card.job_no, job_card.job_prefix,job_card.job_number,job_card.wo_no,item_master.full_name,item_master.item_name, item_master.item_code,process_master.process_name,process.process_name as next_process , department_master.name as dept_name,job_approval.out_process_id";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id";
        $data['leftJoin']['party_master'] = "job_transaction.vendor_id = party_master.id";
        $data['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $data['leftJoin']['process_master as process'] = "process.id = job_approval.out_process_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['department_master'] = "department_master.id = process_master.dept_id";
        // $data['leftJoin']['job_material_dispatch'] = "job_material_dispatch.ref_id = job_transaction.job_card_id";
        // $data['where']['job_material_dispatch.issue_type'] =1;
        $data['where']['job_transaction.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getRejBelongsToStages($job_card_id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "product_process.process_id,SUM(job_transaction.qty) as total_rej_belongs";
        //$data['leftJoin']['product_process'] = 'FIND_IN_SET(job_transaction.rr_stage, product_process.pfc_process)';
        $data['where']['job_transaction.entry_type'] = 1;
        $data['where']['job_transaction.job_card_id'] = $job_card_id;
        $data['group_by'][] = 'product_process.process_id';
        return $this->rows($data);
    }
	
    public function getRejBelongsTo($job_card_id, $process_id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "SUM(qty) as qty";
        //$data['leftJoin']['product_process'] = 'FIND_IN_SET(job_transaction.rr_stage, product_process.pfc_process)';
        $data['where']['job_transaction.entry_type'] = 1;
        $data['where']['job_transaction.job_card_id'] = $job_card_id;
        $data['where']['job_transaction.rr_stage'] = $process_id;
        return $this->row($data)->qty;
    }

    public function getReworkDTRow($data)
    {
        $data['tableName'] = $this->rejRWManage;
        $data['select'] = "rej_rw_management.*,(rej_rw_management.qty-rej_rw_management.cft_qty) as pending_qty,process_master.process_name,itm.item_code as product_name,itm.full_name,itm.item_code,itm.item_name,job_card.job_no,job_card.job_prefix,job_card.job_number,rejection_comment.remark as rejection_reason,rejection_comment.code as reason_code,pfc_trans.parameter,party_master.party_name";
        $data['leftJoin']['job_card'] = "job_card.id = rej_rw_management.job_card_id";
        $data['leftJoin']['job_transaction'] = "job_transaction.id = rej_rw_management.job_trans_id";
        $data['leftJoin']['rejection_comment'] = "rejection_comment.id = rej_rw_management.rr_reason";
        $data['leftJoin']['pfc_trans'] = "pfc_trans.id = rej_rw_management.rr_stage";
        $data['leftJoin']['party_master'] = "party_master.id = rej_rw_management.rr_by";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['where_in']['rej_rw_management.entry_type'] = 3;
        $data['where_in']['rej_rw_management.operation_type'] = 2;
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        if (empty($data['status'])) {
            $data['customWhere'][] = '( rej_rw_management.qty - rej_rw_management.cft_qty) > 0';
        } else {
            $data['customWhere'][] = 'rej_rw_management.cft_qty >= rej_rw_management.qty';
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "DATE_FORMAT(rej_rw_management.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(rej_rw_management.tag_prefix,rej_rw_management.tag_no)";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "rej_rw_management.qty";
        $data['searchCol'][] = "rejection_comment.remark";
        $data['searchCol'][] = "pfc_trans.parameter";
        $data['searchCol'][] = "party_master.party_name";

        $columns = array('', '', 'rej_rw_management.entry_date', 'rej_rw_management.tag_no', 'job_card.job_no', 'itm.item_code', 'process_master.process_name', 'rej_rw_management.qty', 'rejection_comment.remark', 'pfc_trans.parameter', 'party_master.party_name');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } else {
            $data['order_by']['rej_rw_management.entry_date'] = 'DESC';
            $data['order_by']['rej_rw_management.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }    
    

    public function getCftQtyFromRejectionTble($id)
    {
        $cftQuery['tableName'] = $this->rejRWManage;
        $cftQuery['select'] = "MAX(cft_qty) as cft_qty";
        $cftQuery['where']['job_trans_id'] = $id;
        $cftQuery['where']['entry_type'] = 1;
        $cftData = $this->row($cftQuery);
        return $cftData;
    }

    public function getRejCFTData($data){
        $queryData['tableName'] = $this->rejRWManage;
        $queryData['select']='ifnull(SUM(CASE WHEN rej_rw_management.operation_type = 1 THEN rej_rw_management.qty END),0) as rej_qty,ifnull(SUM(CASE WHEN  rej_rw_management.operation_type = 2  THEN rej_rw_management.qty END),0) as rw_qty,ifnull(SUM(CASE WHEN  rej_rw_management.operation_type = 3 THEN rej_rw_management.qty END),0) as hold_qty';
        $queryData['where']['rej_rw_management.job_trans_id'] = $data['job_trans_id'];
        $queryData['where_in']['rej_rw_management.entry_type'] = $data['entry_type'];
        return $this->row($queryData);
    }

    public function getMovementTransactions($id,$entry_type){
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,(CASE WHEN job_transaction.send_to = 0 THEN 'In House' ELSE party_master.party_name END) as vendor_name,,(CASE WHEN job_transaction.vendor_id = 0 THEN 'In House' ELSE vendor.party_name END) as party_name,employee_master.emp_name,mc.item_name as machine_name,mc.item_code as machine_code,shift_master.shift_name,job_approval.trans_type,process_master.process_name";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id	 = job_approval.id";
        $data['leftJoin']['party_master'] = "job_transaction.send_to = party_master.id";
        $data['leftJoin']['party_master as vendor'] = "job_transaction.vendor_id = vendor.id";
        $data['leftJoin']['employee_master'] = "job_transaction.operator_id = employee_master.id";
        $data['leftJoin']['shift_master'] = "shift_master.id = job_transaction.shift_id";
        $data['leftJoin']['item_master as mc'] = "mc.id = job_transaction.machine_id";
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
        $data['where']['job_transaction.job_card_id'] = $id;
        $data['where']['job_transaction.rej_rw_manag_id'] = 0;
        $data['where_in']['job_transaction.entry_type'] = $entry_type;
        $result = $this->rows($data);
        $resultData = array();
        if (!empty($result)) :
            $i = 1;
            foreach ($result as $row) :
                $rejData = $this->getRejCFTData(['job_trans_id'=>$row->id,'entry_type'=>1]);
                $row->rej_qty = $rejData->rej_qty ;
                $row->rw_qty =$rejData->rw_qty; 
                $row->hold_qty =  $rejData->hold_qty;
                $resultData[] =$row;
            endforeach;
        endif;
        return $resultData;
    }

    	public function saveRework($movementData)
    {

        try {
            $this->db->trans_begin();
            $aprvData = $this->getApprovalData($movementData['job_approval_id']); //print_r($aprvData);exit;
            $totalRejQty = $movementData['rej_qty'];
            $totalRWQty = $movementData['rw_qty'];
            $totalHoldQty = $movementData['hold_qty'];
            unset($movementData['rej_qty'], $movementData['rw_qty'], $movementData['hold_qty']);
            // Save Movement 
            if (!empty($movementData)) {
                $saveInward = $this->store($this->jobTrans, $movementData);
            }
            // Save Rejection Log 
            if (!empty($totalRejQty)) {
                $rejData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 1,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalRejQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $rejData);
            }
            if (!empty($totalRWQty)) {
                $rwData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 2,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalRWQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $rwData);
            }
            if (!empty($totalHoldQty)) {
                $holdData = [
                    'id' => '',
                    'entry_type' => 1,
                    'operation_type' => 3,
                    'entry_date' => $movementData['entry_date'],
                    'job_card_id' => $movementData['job_card_id'],
                    'job_trans_id' => $saveInward['insert_id'],
                    'qty' => $totalHoldQty,
                    'created_by' => $movementData['created_by'],
                ];
                $this->store($this->rejRWManage, $holdData);
            }
            /*** Update out qty & rejection qty in current process ***/
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $movementData['job_approval_id'];
            $setData['where']['trans_type'] = 2;
            if(!empty($movementData['vendor_id'])):
                $setData['set']['outward_qty'] = 'outward_qty, - ' . $movementData['qty'];
            else:
                $setData['set']['inward_qty'] = 'inward_qty, - ' . $movementData['qty'];
            endif;
            $setData['set']['ok_qty'] = 'ok_qty, + ' . $movementData['qty'];
            $setData['set']['total_rejection_qty'] = 'total_rejection_qty, + ' . $totalRejQty;
            $setData['set']['total_rework_qty'] = 'total_rework_qty, + ' . $totalRWQty;
            $setData['set']['total_hold_qty'] = 'total_hold_qty, + ' . $totalHoldQty;
            $this->setValue($setData);

            /** Set Qty REj RW MANAGEMENT  */
            if ($aprvData->out_process_id == $aprvData->process_ref_id) :
                $setData = array();
                $setData['tableName'] = $this->rejRWManage;
                $setData['where']['id'] = $aprvData->ref_id;
                $setData['set']['cft_qty'] = 'cft_qty, + ' . ($movementData['qty']+$totalRejQty+$totalRWQty+$totalHoldQty);
                $this->setValue($setData);
            endif;

            $jobApproveData = $this->getApprovalData($movementData['job_approval_id']);
            $pendingQty = $jobApproveData->in_qty - $jobApproveData->outward_qty - $jobApproveData->total_prod_qty;

            $result = ['status' => 1, 'message' => 'Outward saved successfully.', 'outwardTrans' => $this->getOutwardTrans($movementData['job_approval_id'], $movementData['entry_type'])['htmlData'], 'insert_id' => $saveInward['insert_id'], 'pending_qty' => $pendingQty];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteRework($id)
    {
        try {
            $this->db->trans_begin();
            $data['tableName'] = $this->jobTrans;
            $data['where']['id'] = $id;
            $inwardData = $this->row($data);

            $rejQuery['tableName'] = $this->rejRWManage;
            $rejQuery['select'] = "ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 1 THEN qty END),0) as rej_qty,ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 2  THEN qty END),0) as rw_qty,ifnull(SUM(CASE WHEN entry_type = 1 AND operation_type = 3 THEN qty END),0) as hold_qty";
            $rejQuery['where']['job_trans_id'] = $id;
            $rejData = $this->row($rejQuery);


            $jobData = $this->jobcard->getJobcard($inwardData->job_card_id);
            $processApproval = $this->getApprovalData($inwardData->job_approval_id);

            if (empty($processApproval->out_process_id) && $inwardData->qty > $jobData->unstored_qty) :
                return ['status' => 0, 'message' => "You can't delete this outward because This outward is Stored"];
            elseif(!empty($processApproval->out_process_id)):
                $pendingQty = $processApproval->ok_qty - $processApproval->total_out_qty;
                if (!empty($processApproval->out_process_id) && $inwardData->qty > $pendingQty ) :
                    return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                endif;
            endif;
            
            //update out qty
            $setData = array();
            $setData['tableName'] = $this->jobApproval;
            $setData['where']['id'] = $inwardData->job_approval_id;
            if(!empty($inwardData->vendor_id)):
                $setData['set']['outward_qty'] = 'outward_qty, + ' . $inwardData->qty;
            else:
                $setData['set']['inward_qty'] = 'inward_qty, + ' . $inwardData->qty;
            endif;
            $setData['set']['ok_qty'] = 'ok_qty, - ' . $inwardData->qty;
            $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - ' . $rejData->rej_qty;
            $setData['set']['total_rework_qty'] = 'total_rework_qty, - ' . $rejData->rw_qty;
            $setData['set']['total_hold_qty'] = 'total_hold_qty, - ' . $rejData->hold_qty;
            $this->setValue($setData);

            /** Set Qty REj RW MANAGEMENT  */
            if($processApproval->out_process_id == $processApproval->process_ref_id) :
                $setData = array();
                $setData['tableName'] = $this->rejRWManage;
                $setData['where']['id'] =  $processApproval->ref_id;
                $setData['set']['cft_qty'] = 'cft_qty, + ' . $inwardData->qty;
                $this->setValue($setData);
            endif;


            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 1], 'Rejection');
            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 2], 'Rework');
            $this->trash($this->rejRWManage, ['job_trans_id' => $id, 'entry_type' => 1, 'operation_type' => 3], 'Hold');
            $result = $this->trash($this->jobTrans, ['id' => $id], 'Outward');

            $result['outwardTrans'] = $this->getOutwardTrans($inwardData->job_approval_id)['htmlData'];
            $result['job_approval_id'] = $inwardData->job_approval_id;

            $jobApproveData = $this->getApprovalData($inwardData->job_approval_id);
            $pendingQty = $jobApproveData->in_qty - $jobApproveData->outward_qty - $jobApproveData->total_prod_qty;


            $result['pending_qty'] = $pendingQty;
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    /* Created At : 06-12-2022 [Milan Chauhan] */
    public function getIdleTimeData($id){
        $queryData = array();
        $queryData['tableName'] = $this->jobTrans;
        $queryData['select'] = "job_transaction.*,rejection_comment.code as reason_code,rejection_comment.remark as reason,machine_master.item_code as machine_code, machine_master.item_name as machine_name, shift_master.shift_name, operator_master.emp_code as operator_code, operator_master.emp_name as operator_name";
        $queryData['leftJoin']['item_master as machine_master'] = "machine_master.id = job_transaction.machine_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['shift_master'] = "shift_master.id = job_transaction.shift_id";
        $queryData['leftJoin']['employee_master as operator_master'] = "operator_master.id = job_transaction.operator_id";
        $queryData['where']['job_transaction.entry_type'] = 8;
        $queryData['where']['job_transaction.job_approval_id'] = $id;
        $result = $this->rows($queryData);

        return $result;
    }

    /* Created At : 06-12-2022 [Milan Chauhan] */
    public function saveIdleTime($data){
        try {
            $this->db->trans_begin();

            $result = $this->store($this->jobTrans,$data,'Idle Time');

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /* Created At : 06-12-2022 [Milan Chauhan] */
    public function deleteIdleTime($id){
        try {
            $this->db->trans_begin();

            $queryData = array();
            $queryData['tableName'] = $this->jobTrans;
            $queryData['where']['id'] = $id;
            $transData = $this->row($queryData);

            $result = $this->trash($this->jobTrans,['id'=>$id],'Idle Time');
            $result['job_approval_id'] = $transData->job_approval_id;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getHeatData($postData){
        $queryData = array();
        $queryData['tableName'] = $this->job_heat_trans;
        $queryData['select'] = "job_heat_trans.*";
        if(!empty($postData['job_approval_id'])){$queryData['where']['job_heat_trans.job_approval_id'] = $postData['job_approval_id'];}
        if(!empty($postData['batch_no'])){$queryData['where']['job_heat_trans.batch_no'] = $postData['batch_no'];}
        if(!empty($postData['tc_no'])){$queryData['where']['job_heat_trans.tc_no'] = $postData['tc_no'];}
        if(isset($postData['single_row'])){
            $result = $this->row($queryData);
        }else{
            $result = $this->rows($queryData);
        }
        return $result;
    }

    public function saveCutWeight($data){
        try{
            $this->db->trans_begin(); 
            $queryData = array();
            $queryData['tableName'] = $this->jobBom;
            $queryData['where']['id'] = $data['job_bom_id'];
            $bomData = $this->row($queryData);

            $aprvData = $this->getProcessWiseApprovalData($bomData->job_card_id,0);
            $jobData = $this->jobcard->getJobcard($bomData->job_card_id);

            /*** Update Cut Weight in JOB BOM ***/
            $requiredQty = $data['cut_weight'] * $jobData->qty;
            $usedQty = $data['cut_weight'] * $aprvData->total_out_qty;
            $result = $this->store($this->jobBom,['id'=>$data['job_bom_id'],'qty'=>$data['cut_weight'],'req_qty'=>$requiredQty,'used_qty'=>$usedQty]);

            /***** Update Out qty in job_heat_trans */
            if($aprvData->total_out_qty > 0){
                $heatData = $this->getHeatData(['job_approval_id'=>$aprvData->id]);
                $this->edit($this->job_heat_trans,['job_approval_id'=>$aprvData->id],['out_qty'=>0]);

                $queryData = array();
                $queryData['tableName'] = $this->jobTrans;
                $queryData['where']['job_approval_id'] = $aprvData->id;
                $queryData['where']['entry_type'] = 6;
                $jobTransData = $this->rows($queryData);
                foreach($jobTransData as $row){
                    $totalWeight = $row->qty * $data['cut_weight'];
                    $this->store($this->jobTrans,['id'=>$row->id,'w_pcs'=>$data['cut_weight'],'total_weight'=>$totalWeight]);

                    $setData = array();
                    $setData['tableName'] = $this->job_heat_trans;
                    $setData['where']['job_approval_id'] = $aprvData->id;
                    $setData['where']['tc_no'] = $row->batch_no;
                    $setData['set']['out_qty'] = 'out_qty, + ' . $totalWeight;
                    $this->setValue($setData);
                }
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
	
	public function getProcessMovementLastTrans($approval_id){
        $queryData = array();
        $queryData['tableName'] = $this->jobTrans;
        $queryData['where']['entry_type'] = 0;
        $queryData['where']['job_approval_id'] = $approval_id;
		$queryData['order_by']['id'] = 'DESC';
        $result = $this->row($queryData);
        return $result;
    }

}
