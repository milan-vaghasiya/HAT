<?php
class ProcessMovementModel extends MasterModel
{
    private $jobCard = "job_card";
    private $jobBom = "job_bom";
    private $productionApproval = "production_approval";
    private $productionTrans = "production_transaction";
    private $productionLog = "production_log";
    private $vendorProductionTrans = "vendor_production_trans";
    private $jobUsedMaterial = "job_used_material";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $setupRequest = "prod_setup_request";
    private $setupRequestTrans = "prod_setup_trans";
    private $itemMaster = "item_master";
    private $stockTransaction = "stock_transaction";
    private $jobWorkChallan = "jobwork_challan";
    private $rej_rw_management = "rej_rw_management";

    public function getProcessWiseApprovalData($job_card_id, $process_id, $type = 0)
    {
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.*,(CASE WHEN production_approval.in_process_id = 0 THEN 'Raw Material' ELSE ipm.process_name END) as in_process_name";
        $data['leftJoin']['process_master as ipm'] = "production_approval.in_process_id = ipm.id";
        $data['where']['production_approval.job_card_id'] = $job_card_id;
        $data['where']['production_approval.in_process_id'] = $process_id;
        $data['where']['production_approval.trans_type'] = $type;
        $result = $this->row($data);
        if (!empty($result)) :
            $vendorData = array();
            $vendorData['tableName'] = $this->productionTrans;
            $vendorData['select'] = "production_transaction.vendor_id,(CASE WHEN production_transaction.vendor_id = 0 THEN 'In House' ELSE party_master.party_name END) as party_name";
            $vendorData['leftJoin']['party_master'] = "party_master.id = production_transaction.vendor_id";
            $vendorData['where']['production_transaction.job_card_id'] = $job_card_id;
            $vendorData['where']['production_transaction.process_id'] = $process_id;
            $vendorData['where']['production_transaction.trans_ref_id'] =0;
            $vendorData['group_by'][] = ['production_transaction.vendor_id'];
            $vndData = $this->rows($vendorData);

            $result->vendor = (!empty($vndData)) ? implode(", ", array_column($vndData, 'party_name')) : "In House";
        endif;
        return $result;
    }

    public function getReworkData($job_id)
    {
        $data['tableName'] = $this->productionApproval;
        $data['where']['job_card_id'] = $job_id;
        $data['where']['trans_type'] = 1;
        $data['where']['is_delete'] = 0;
        $result = $this->rows($data);
        return $result;
    }

    public function getApprovalData($id)
    {
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,(CASE WHEN production_approval.in_process_id = 0 THEN 'Initial Stage' ELSE ipm.process_name END) as in_process_name, opm.process_name as out_process_name,product_process.finished_weight";
        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['leftJoin']['process_master AS ipm'] = "production_approval.in_process_id = ipm.id";
        $data['leftJoin']['process_master AS opm'] = "production_approval.out_process_id = opm.id";
        $data['leftJoin']['product_process'] = "product_process.item_id = production_approval.product_id AND product_process.process_id = production_approval.in_process_id";
        $data['where']['production_approval.id'] = $id;
        return $this->row($data);
    }

    public function getBatchStock($job_card_id, $processId)
    {
        $data['tableName'] = $this->jobUsedMaterial;
        $data['where']['job_card_id'] = $job_card_id;
        $data['where']['process_id'] = $processId;
        $data['where']['bom_item_type'] = 3;
        return $this->rows($data);
    }

    public function getBatchStockOnProductionTrans($job_card_id, $in_process_id, $out_process_id, $id)
    {
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "SUM(issue_material_qty) as issue_qty,material_used_id as id,issue_batch_no as batch_no";
        $data['where']['job_card_id'] = $job_card_id;
        $data['where']['process_id'] = $in_process_id;
        //$data['where']['entry_type'] = 1;
        $data['group_by'][] = "issue_batch_no";
        $data['group_by'][] = "material_used_id";
        $result = $this->rows($data);

        $resultData = array();
        foreach ($result as $row) :
            $juq = array();
            $juq['select'] = 'wp_qty';
            $juq['tableName'] = $this->jobUsedMaterial;
            $juq['where']['id'] = $row->id;
            $wp_qty = $this->row($juq);
            $row->wp_qty = (!empty($wp_qty)) ? $wp_qty->wp_qty : 0;
            $data = array();
            $data['tableName'] = $this->productionTrans;
            $data['select'] = "SUM(issue_material_qty) as used_qty";
            $data['where']['job_card_id'] = $job_card_id;
            $data['where']['process_id'] = $out_process_id;
            $data['where']['production_approval_id'] = $id;
            $data['where']['issue_batch_no'] = $row->batch_no;
            $usedStock = $this->row($data);
            $row->used_qty = (!empty($usedStock->used_qty)) ? $usedStock->used_qty : 0;
            $row->stock_qty = $row->issue_qty - $row->used_qty;
            $resultData[] = $row;
        endforeach;
        return $resultData;
    }

    public function getOutwardTrans($id, $processId,$trans_ref_id=0,$entry_type=1,$vp_trans_id=0)
    {
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "production_transaction.*,production_approval.trans_type,(CASE WHEN vendor_production_trans.vendor_id = 0 THEN 'In House' ELSE party_master.party_name END) as vendor_name, production_log.rej_qty";
        $data['leftJoin']['production_approval'] = "production_transaction.production_approval_id = production_approval.id";
        $data['leftJoin']['vendor_production_trans'] = "production_transaction.id = vendor_production_trans.ref_id";
        $data['leftJoin']['party_master'] = "vendor_production_trans.vendor_id = party_master.id";
        $data['leftJoin']['production_log'] = "production_transaction.id = production_log.ref_id";
        $data['where']['production_transaction.production_approval_id'] = $id;
        $data['where']['production_transaction.process_id'] = $processId;
        if(!empty($vp_trans_id)){ $data['where']['production_transaction.ref_id'] = $vp_trans_id; }
        $data['where']['production_transaction.trans_ref_id'] = $trans_ref_id;
        $data['where_in']['production_transaction.entry_type'] = $entry_type;
        $result = $this->rows($data);
        
        $dataRow = array();
        $html = "";
        if (!empty($result)) :
            $i = 1;
            foreach ($result as $row) :
                $transDate = date("d-m-Y", strtotime($row->entry_date));
                
                
                $transType = ($row->entry_type ==2 || $row->entry_type==3) ? "Hold Area" : "Regular";
                $deleteBtn = '';
                if (empty($row->accepted_by)) :    
                    $printBtn = '<a href="'.base_url('production_v2/jobcard/printProcessIdentification/'.$row->id).'" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                    $deleteBtn = '<button type="button" onclick="trashOutward(' . $row->id . ');" class="btn btn-sm btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                endif;
                $html .= '<tr>
                            <td>' . $i++ . '</td>
                            <td>' . $transDate . '</td>
                            <td>' . $transType . '</td>
                            <td>' . $row->vendor_name . '</td>
                            <td>' . $row->in_qty . '</td>
                            <td>' . $row->rej_qty . '</td>
                            <td>' . $row->remark . '</td>
                            <td class="text-center" style="width:10%;">'.$printBtn.$deleteBtn.'</td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        else :
            $html = '<td colspan="7" class="text-center">No Data Found.</td>';
        endif;
        return ['htmlData' => $html, 'outwardTrans' => $dataRow];
    }

    public function getJobWorkOrderNoList($data)
    {
        $data['tableName'] = "job_work_order";
        $data['where']['job_work_order.product_id'] = $data['product_id'];
        $data['where']['job_work_order.vendor_id'] = $data['vendor_id'];
        $data['where']['job_work_order.jwo_status'] = 0;
        $data['where']['job_work_order.is_approve > '] = 0;
        $data['customWhere'][] = 'find_in_set("' . $data['process_id'] . '", process_id)';
        $result = $this->rows($data);

        $options = '<option value="">Select Job Order No.</option>';
        foreach ($result as $row) :
            $options .= '<option value="' . $row->id . '">' . getPrefixNumber($row->jwo_prefix, $row->jwo_no) . ' [ Ord. Qty. : ' . $row->qty . ' ]</option>';
        endforeach;
        return ['status' => 1, 'options' => $options, 'result' => $result];
    }

    public function getJobWorkOrderProcessList($data)
    {
        if (!empty($data['job_order_id'])) :
            $queryData =  array();
            $queryData['tableName'] = "job_work_order";
            $queryData['where']['id'] = $data['job_order_id'];
            $result = $this->row($queryData);

            $options = '<option value="">Select Process</option>';
            $jobProcessList = array();
            $jobWorkProcessList = array();
            if (!empty($result->process_id)) :
                $processList = explode(",", $result->process_id);
                $queryData =  array();
                $queryData['tableName'] = $this->jobCard;
                $queryData['select'] = "process";
                $queryData['where']['id'] = $data['job_card_id'];
                $jobProcess = $this->row($queryData)->process;
                $jobProcess = explode(",", $jobProcess);
                $a = 0;
                $jwoProcessIds = array();
                foreach ($jobProcess as $key => $value) :
                    if (isset($processList[$a])) :
                        $processKey = array_search($processList[$a], $jobProcess);
                        $jwoProcessIds[$processKey] = $processList[$a];
                        $a++;
                    endif;
                endforeach;
                ksort($jwoProcessIds);
                $processList = array();
                foreach ($jwoProcessIds as $key => $value) :
                    $processList[] = $value;
                endforeach;

                $in_process_key = array_search($data['process_id'], $jobProcess);
                $i = 0;
                foreach ($jobProcess as $key => $value) :
                    if ($key >= $in_process_key) :
                        if (isset($processList[$i]) && in_array($value, $processList)) :
                            $processData = $this->process->getProcess($value);
                            $options .= '<option value="' . $processData->id . '">' . $processData->process_name . '</option>';
                            $jobProcessList[] = $processData->id;
                            $jobWorkProcessList[] = ['id' => $processData->id, 'process_name' => $processData->process_name];
                            $i++;
                        endif;
                    endif;
                endforeach;
            endif;
        else :
            $vendorProcess = $this->party->getParty($data['vendor_id']);

            $options = '<option value="">Select Process</option>';
            $jobProcessList = array();
            $jobWorkProcessList = array();
            if (!empty($vendorProcess->process_id)) :
                $processList = explode(",", $vendorProcess->process_id);

                $queryData =  array();
                $queryData['tableName'] = $this->jobCard;
                $queryData['select'] = "process";
                $queryData['where']['id'] = $data['job_card_id'];
                $jobProcess = $this->row($queryData)->process;
                $jobProcess = explode(",", $jobProcess);

                $jwoProcessIds = array();
                $countVendorProcess = count($processList);
                for ($a = 0; $a < $countVendorProcess; $a++) :
                    $processKey = array_search($processList[$a], $jobProcess);
                    if (is_numeric($processKey) and $processKey >= 0) :
                        $jwoProcessIds[$processKey] = $processList[$a];
                    endif;
                endfor;
                ksort($jwoProcessIds);

                $processList = array();
                foreach ($jwoProcessIds as $key => $value) :
                    $processList[] = $value;
                endforeach;

                $in_process_key = array_search($data['process_id'], $jobProcess);
                $i = 0;
                foreach ($jobProcess as $key => $value) :
                    if ($key >= $in_process_key) :
                        if (isset($processList[$i]) && in_array($value, $processList)) :
                            $processData = $this->process->getProcess($value);
                            $options .= '<option value="' . $processData->id . '">' . $processData->process_name . '</option>';
                            $jobProcessList[] = $processData->id;
                            $jobWorkProcessList[] = ['id' => $processData->id, 'process_name' => $processData->process_name];
                            $i++;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
        return ['status' => 1, 'options' => $options, 'result' => $jobProcessList, 'job_process' => implode(",", $jobProcessList), 'jobWorkProcessList' => $jobWorkProcessList];
    }

    public function nextJobWorkChNo()
    {
        $data['select'] = "MAX(challan_no) as jobChNo";
        $data['where']['vendor_id > '] = 0;
        $data['tableName'] = $this->productionTrans;
        $jobChNo = $this->specificRow($data)->jobChNo;
        $nextChNo = (!empty($jobChNo)) ? ($jobChNo + 1) : 1;
        return $nextChNo;
    }

    public function save($data)
    {
        try {
            $this->db->trans_begin();
            $data['challan_prefix'] = '';
            $data['challan_no'] = 0;
            if (!empty($data['vendor_id'])) :
                $data['challan_prefix'] = 'JO/' . $this->shortYear . '/';
                $data['challan_no'] = $this->nextJobWorkChNo();
            endif;
            $outwardData = $this->getApprovalData($data['ref_id']);
            $reworkProcess = "";
            if ($outwardData->trans_type == 1) :
                $queryData = array();
                $queryData['tableName'] = $this->productionTrans;
                $queryData['where']['id'] = $outwardData->ref_id;
                $refInwardData = $this->row($queryData);
                $reworkProcess = $refInwardData->rework_process_id;
            endif;
            $vendorProdData = array();
            if (!empty($data['vp_trans_id'])) :
                $queryData = array();
                $queryData['tableName'] = $this->vendorProductionTrans;
                $queryData['where']['id'] = $data['vp_trans_id'];
                $vendorProdData = $this->row($queryData);
            endif;
            $inwardData = [
                'id' => $data['id'],
                'entry_date' => $data['entry_date'],
                'entry_type' => (!empty($data['entry_type']))?$data['entry_type']:1,
                'trans_ref_id' => !empty($data['trans_ref_id'])?$data['trans_ref_id']:'',
                'ref_id' => $data['vp_trans_id'],
                'job_card_id' => $data['job_card_id'],
                'production_approval_id' => $data['ref_id'],
                'job_order_id' => (!empty($vendorProdData)) ? $vendorProdData->job_order_id : 0,
                'vendor_id' => (!empty($vendorProdData)) ? $vendorProdData->vendor_id : 0,
                'process_id' => $data['in_process_id'],
                'product_id' => $data['product_id'],
                'in_qty' => $data['out_qty'],
                'out_qty' => $data['out_qty'],
                'w_pcs' => (!empty($data['in_qty_kg']) AND !empty($data['out_qty'])) ? ($data['in_qty_kg'] / $data['out_qty']) : 0,
                'remark' => $data['remark'],
                'challan_prefix' => $data['challan_prefix'],
                'challan_no' => $data['challan_no'],
                'in_challan_no' => $data['in_challan_no'],
                'charge_no' => $data['charge_no'],
                'material_used_id' => "0", //$data['material_used_id'],
                'issue_batch_no' => "", //$data['batch_no'],
                'issue_material_qty' => "0", //$data['req_qty'],
                'production_time' => "",
                'cycle_time' => "",
                'job_process_ids' => (!empty($vendorProdData)) ? $vendorProdData->job_process_ids : "",
                'created_by' => $data['created_by']
            ];
            $saveInward = $this->store($this->productionTrans, $inwardData);

            if (!empty($data['vp_trans_id'])) :
                $setData = array();
                $setData['tableName'] = $this->vendorProductionTrans;
                $setData['where']['id'] = $data['vp_trans_id'];
                $setData['set']['in_qty'] = "in_qty, + " . $data['production_qty'];
                $this->setValue($setData);
            endif;
            if (!empty($data['vendor_id']) && !empty($data['out_process_id'])) :
                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['in_process_id'] = $data['out_process_id'];
                $queryData['where']['job_card_id'] = $data['job_card_id'];
                $queryData['where']['trans_type'] = $outwardData->trans_type;
                $queryData['where']['ref_id'] = $outwardData->ref_id;
                $nextApprovalData = $this->row($queryData);
                $vendorIds = array();
                if (!empty($nextApprovalData->vendor_id)) :
                    $vendors = explode(",", $nextApprovalData->vendor_id);
                    if (!in_array($data['vendor_id'], $vendors)) :
                        $vendors[] = $data['vendor_id'];
                    endif;
                    $vendorIds = $vendors;
                else :
                    $vendorIds[] = $data['vendor_id'];
                endif;
                $vendorIds = implode(",", $vendorIds);
                $this->store($this->productionApproval, ['id' => $nextApprovalData->id, 'vendor_id' => $vendorIds]);
                $jobWorkData = [
                    'id' => "",
                    'ref_id' => $saveInward['insert_id'],
                    'job_card_id' => $data['job_card_id'],
                    'production_approval_id' => $nextApprovalData->id,
                    'job_order_id' => $data['job_order_id'],
                    'vendor_id' => $data['vendor_id'],
                    'process_id' => $data['out_process_id'],
                    'product_id' => $data['product_id'],
                    'out_qty' => $data['out_qty'],
                    'w_pcs' => (!empty($data['in_qty_kg'])  AND !empty($data['out_qty'])) ? ($data['in_qty_kg'] / $data['out_qty']) : 0,
                    'job_process_ids' => (isset($data['job_process_ids'])) ? $data['job_process_ids'] : "",
                    'created_by' => $data['created_by']
                ];
                $this->store($this->vendorProductionTrans, $jobWorkData);
            endif;

            //update out qty
            if($data['entry_type']==1){
                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['id'] = $data['ref_id'];
                //if (!empty($data['out_process_id']))
                if($data['from_entry_type']==3){
                    $setData['set']['out_qty'] = 'out_qty, - ' . (!empty($data['rejection_reason'])?array_sum(array_column($data['rejection_reason'], 'rej_qty')):0);
                    $setData['set']['total_ok_qty'] = 'total_ok_qty, - ' .(!empty($data['rejection_reason'])?array_sum(array_column($data['rejection_reason'], 'rej_qty')):0);    
                }
                else{
                    $setData['set']['out_qty'] = 'out_qty, + ' . $data['out_qty'];
                    $setData['set']['total_ok_qty'] = 'total_ok_qty, + ' . $data['out_qty'];
                }
                
                $this->setValue($setData);
            }
            $jobData = $this->jobcard_v2->getJobcard($data['job_card_id']);


            /* product stock minus in current process*/
            if (!empty($data['in_process_id']) && empty($data['from_entry_type'])):
                $curentPrsStore = $this->getProcessStore($data['in_process_id']);
                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => $curentPrsStore->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 2,
                    'item_id' => $data['product_id'],
                    'qty' => '-' . $data['out_qty'],
                    'ref_type' => 23,
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_ref_id' => $saveInward['insert_id'],
                    'ref_date' => $data['entry_date'],
                    'created_by' => $this->loginId,
                    'stock_effect' => 0
                ];

                $this->saveProcessStockEffect($stockMinusTrans);
            endif;
            if(!empty($data['from_entry_type'])){
                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => $this->HLD_STORE->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 2,
                    'item_id' => $data['product_id'],
                    'qty' => '-' . $data['out_qty'],
                    'ref_type' => 23,
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_ref_id' => $saveInward['insert_id'],
                    'ref_date' => $data['entry_date'],
                    'created_by' => $this->loginId,
                    'stock_effect' => 0
                ];
                $this->saveProcessStockEffect($stockMinusTrans);
            }
            if(!empty($data['entry_type']) && $data['entry_type']==1){
                if (!empty($data['out_process_id'])) :
                    $setData = array();
                    $setData['tableName'] = $this->productionApproval;
                    $setData['where']['in_process_id'] = $data['out_process_id'];
                    $setData['where']['job_card_id'] = $data['job_card_id'];
                    $setData['where']['trans_type'] = $outwardData->trans_type;
                    $setData['where']['ref_id'] = $outwardData->ref_id;
                    $setData['set']['inward_qty'] = 'inward_qty, + ' . $data['out_qty'];
                    $setData['set']['in_qty'] = 'in_qty, + ' . $data['out_qty'];
                    $this->setValue($setData);                

                    /* product stock plus in next process*/
                    $nxtPrsStore = $this->getProcessStore($data['out_process_id']);
                    $stockPlusTrans = [
                        'id' => "",
                        'location_id' => $nxtPrsStore->id,
                        'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                        'trans_type' => 1,
                        'item_id' => $data['product_id'],
                        'qty' =>  $data['out_qty'],
                        'ref_type' => 23,
                        'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                        'ref_id' => $data['job_card_id'],
                        'trans_ref_id' => $saveInward['insert_id'],
                        'ref_date' => $data['entry_date'],
                        'created_by' => $this->loginId,
                        'stock_effect' => 0
                    ];
                    $this->saveProcessStockEffect($stockPlusTrans);
                else:
                    
                    $curentPrsStore = $this->getProcessStore($data['in_process_id']);
                    $stockPlusTrans = [
                        'id' => "",
                        'location_id' =>(!empty($data['from_entry_type']) && $data['from_entry_type']==3)?$this->PROD_STORE->id:$curentPrsStore->id,
                        'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                        'trans_type' => 1,
                        'item_id' => $data['product_id'],
                        'qty' =>  $data['out_qty'],
                        'ref_type' => 7,
                        'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                        'ref_id' => $data['job_card_id'],
                        'trans_ref_id' => $saveInward['insert_id'],
                        'ref_date' => $data['entry_date'],
                        'ref_batch' => 23,
                        'created_by' => $this->loginId,
                        'stock_effect' => (!empty($data['from_entry_type']) && $data['from_entry_type']==3)?1:0
                    ];
                    $this->saveProcessStockEffect($stockPlusTrans);
                endif;
            }
            else{
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $this->HLD_STORE->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $data['product_id'],
                    'qty' =>  $data['out_qty'],
                    'ref_type' => 23,
                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $saveInward['insert_id'],
                    'ref_date' => $data['entry_date'],
                    'created_by' => $this->loginId,
                    'stock_effect' => 0
                ];
                $this->saveProcessStockEffect($stockPlusTrans);
            }

            /*** If First Process then Maintain Batchwise Rowmaterial ***/
            $jobProcesses = explode(",", $jobData->process);

            
            if ($jobProcesses[count($jobProcesses) - 1] == $data['in_process_id'] && empty($data['from_entry_type'])) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $data['job_card_id'];
                $setData['set']['unstored_qty'] = 'unstored_qty, + ' . $data['out_qty'];
                $this->setValue($setData);
            endif;
            
            if ($data['in_process_id'] == $jobProcesses[0] && $data['entry_type'] == 1) :
                $this->edit($this->jobCard, ['id' => $data['job_card_id']], ['order_status' => 2]);
            endif;

            /* if($data['material_request'] == 1):
                $queryData = array();
                $queryData['tableName'] = $this->jobBom;
                $queryData['where']['job_card_id'] = $data['job_card_id'];
                $queryData['where']['process_id'] = $data['out_process_id'];
                $jobBomData = $this->rows($queryData);
                if(!empty($jobBomData)):
                    foreach($jobBomData as $row):
                        $itemData = $this->item->getItem($row->ref_item_id);
                        $materialDispatchData = [
                            'id' => "",
                            'job_card_id' => $data['job_card_id'],
                            'job_trans_id' => $saveInward['insert_id'],
                            'material_type' => $itemData->item_type,
                            'process_id' => $data['out_process_id'],
                            'req_date' => date("Y-m-d"),
                            'req_item_id' => $row->ref_item_id,
                            'req_qty' => $row->qty * $data['out_qty'],
                            'machine_id' => "0",//$data['machine_id'],
                            'created_by' => $data['created_by']
                        ];
                        $this->store($this->jobMaterialDispatch,$materialDispatchData);
                    endforeach;
                endif;
            endif; */

            /** If Process Movement From Vendor Then Production Log  */
            if (!empty($data['vp_trans_id']) || !empty($data['from_entry_type'])) :
                $productionLog = [];
                if (!empty($data['rejection_reason'])) :
                    $productionLog['rej_qty'] = array_sum(array_column($data['rejection_reason'], 'rej_qty'));
                    $productionLog['rej_reason'] = json_encode($data['rejection_reason']);
                    $productionLog['rejection_reason'] = $data['rejection_reason'];
                endif;
                $productionLog['production_qty'] = $data['production_qty'];
                $productionLog['ok_qty'] = $data['out_qty'];
                $productionLog['created_by'] = $data['created_by'];
                $productionLog['job_card_id'] = $data['job_card_id'];
                $productionLog['process_id'] = $data['in_process_id'];
                $productionLog['log_date'] = $data['entry_date'];
                $productionLog['ref_id'] = $saveInward['insert_id'];
                $productionLog['prod_type'] = 3;
                $productionLog['from_entry_type'] = $data['from_entry_type'];
                $productionLog['id'] = "";
                $this->rejectionLog->save($productionLog);
            endif;

            /* Get Pending Qty for Movement */
            $logData = $this->logSheet->getPrdLogOnProcessNJob($data['job_card_id'], $data['in_process_id']);
            $rejection_qty = (!empty($logData->rejection_qty)) ? $logData->rejection_qty : 0;
            $pendingQty = 0;
            $approvalData = $this->getApprovalData($data['ref_id']);
            if(empty($data['vp_trans_id'])):
                $pendingQty = $approvalData->in_qty - $approvalData->out_qty - $rejection_qty;
            else:
                $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($data['vp_trans_id']);
                $pendingQty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
            endif;

            $totalLogData=$this->logSheet->getPrdLogOnProcessNJob($data['job_card_id']);
            $total_rej_qty = (!empty($totalLogData->rejection_qty)) ? $totalLogData->rejection_qty : 0;
            $total_prod_qty=($approvalData->out_qty+$total_rej_qty);
            
            if ($data['in_process_id'] == $jobProcesses[(count($jobProcesses)-1)] && $data['entry_type'] == 1 &&  $total_prod_qty>= $jobData->qty) :
                /* Update Used Stock in Job Material Used */
                $setData = array();
                $setData['tableName'] = $this->jobUsedMaterial;
                $setData['where']['job_card_id'] = $data['job_card_id'];
                $setData['set']['used_qty'] = 'used_qty, + (' . $total_prod_qty . ' * wp_qty)';
                $this->setValue($setData);

                /** Minus stock in received material */
                $queryData = array();
                $queryData['tableName'] = "stock_transaction";
                $queryData['where']['trans_type'] = 1;
                $queryData['where']['ref_type'] = 27;
                $queryData['where']['ref_id'] = $data['job_card_id'];
                $queryData['where']['location_id'] = $this->RCV_RM_STORE->id;
                $queryData['group_by'][] = "batch_no";
                $issueRMList = $this->rows($queryData);
                if(!empty($issueRMList)):
                    foreach($issueRMList as $row):
                        $bomQuery['tableName'] = $this->jobBom;
                        $bomQuery['where']['ref_item_id'] = $row->item_id;
                        $bomQuery['where']['job_card_id'] = $data['job_card_id'];
                        $bomData = $this->row($bomQuery);

                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' =>$this->RCV_RM_STORE->id,
                            'batch_no' =>$row->batch_no,
                            'trans_type' => 2,
                            'item_id' => $row->item_id,
                            'qty' =>'-'.($bomData->qty*$total_prod_qty),
                            'ref_type' => 27,
                            'ref_id' =>$row->ref_id,
                            'trans_ref_id' =>$saveInward['insert_id'],
                            'ref_no'=>$row->ref_no,
                            'ref_date' =>$data['entry_date'],
                            'created_by' => $this->session->userdata('loginId'),
                            'stock_effect' => 0
                        ];
                        $this->store('stock_transaction',$stockMinusTrans);
                    endforeach;
                endif;
            endif;

            //Changed By Karmi @02/08/2022
            if(!empty($data['return_challan_id'])):
                $transData = [
                    'id' => $data['return_challan_id'],
                    'material_data' => $data['material_data']
                ];
                $this->store($this->jobWorkChallan,$transData,'Return Material');
            endif;

            $result = ['status' => 1, 'message' => 'Outward saved successfully.', 'outwardTrans' => $this->getOutwardTrans($data['ref_id'], $data['in_process_id'],$data['trans_ref_id'],$data['entry_type'])['htmlData'], 'insert_id' => $saveInward['insert_id'],'pending_qty'=>$pendingQty];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $data['tableName'] = $this->productionTrans;
            $data['where']['id'] = $id;
            $inwardData = $this->row($data);
            
            $jobData = $this->jobcard_v2->getJobcard($inwardData->job_card_id);
            $processApproval = $this->getApprovalData($inwardData->production_approval_id);
            $nextProcessApproval = $this->getProcessWiseApprovalData($inwardData->job_card_id, $processApproval->out_process_id);
            
            $logSheetData = $this->logSheet->getPrdLogOnProcessNJob($nextProcessApproval->job_card_id, $nextProcessApproval->in_process_id);
            $nextProcessRejQty = (!empty($logSheetData->rejection_qty))?$logSheetData->rejection_qty:0;
            $pendingQty = $nextProcessApproval->in_qty - $nextProcessApproval->out_qty - $nextProcessRejQty;
            if(empty($processApproval->out_process_id) && $inwardData->out_qty > $jobData->unstored_qty && empty($inwardData->trans_ref_id)):
                return ['status' => 0, 'message' => "You can't delete this outward because This outward is Stored"];
            else:
                if (!empty($processApproval->out_process_id) && $inwardData->out_qty > $pendingQty && $inwardData->entry_type==1 && empty($inwardData->trans_ref_id)) :
                    return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                else:
                    $dataInsp['tableName'] = $this->productionTrans;
                    $dataInsp['where']['trans_ref_id'] = $id;
                    $inspData = $this->row($dataInsp);
                    if(!empty($inspData)){
                        return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                    }
                endif;
            endif;
            
            /*if (!empty($processApproval->out_process_id)) :
                $pendingQty = $nextProcessApproval->in_qty - $nextProcessApproval->out_qty;
                if ($inwardData->out_qty > $pendingQty) :
                    return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                endif;
            else :
                if ($jobData->unstored_qty < $inwardData->out_qty) :
                    return ['status' => 0, 'message' => "You can't delete this outward because This outward moved to next process."];
                endif;
            endif;*/
            
            $this->trash($this->jobMaterialDispatch, ['job_card_id' => $inwardData->job_card_id, 'job_trans_id' => $inwardData->id]);
            if (!empty($inwardData->ref_id) && $inwardData->entry_type==1) :
                $queryData = array();
                $queryData['tableName'] = $this->productionLog;
                $queryData['where']['ref_id'] = $inwardData->id;
                $productionLog = $this->row($queryData);
                $rejQty = (!empty($productionLog))?$productionLog->rej_qty:0;

                $setData = array();
                $setData['tableName'] = $this->vendorProductionTrans;
                $setData['where']['id'] = $inwardData->ref_id;
                $setData['set']['in_qty'] = "in_qty, - " . ($inwardData->out_qty + $rejQty);
                $this->setValue($setData);
            endif;
            $queryData = array();
            $queryData['tableName'] = $this->vendorProductionTrans;
            $queryData['where']['ref_id'] = $inwardData->id;
            $vendorProductionData = $this->row($queryData);
            if (!empty($vendorProductionData)) :
                $queryData = array();
                $queryData['tableName'] = $this->vendorProductionTrans;
                $queryData['where']['ref_id'] = $inwardData->id;
                $queryData['where']['product_id'] = $inwardData->product_id;
                $queryData['where']['job_card_id'] = $inwardData->job_card_id;
                $queryData['where']['vendor_id'] = $vendorProductionData->vendor_id;
                $queryData['where']['process_id'] = $vendorProductionData->process_id;
                $queryData['where']['id != '] = $vendorProductionData->id;
                $checkForAnotherProcess = $this->numRows($queryData);
                if ($checkForAnotherProcess <= 0) :
                    $vendorIds = array();
                    $vendors = explode(",", $nextProcessApproval->vendor_id);
                    if (in_array($vendorProductionData->vendor_id, $vendors)) :
                        $key = array_search($vendorProductionData->vendor_id, $vendors);
                        unset($vendors[$key]);
                    endif;
                    $vendorIds = $vendors;
                    $vendorIds = implode(",", $vendorIds);
                    $this->store($this->productionApproval, ['id' => $nextProcessApproval->id, 'vendor_id' => $vendorIds]);
                endif;
                $this->trash($this->vendorProductionTrans, ['id' => $vendorProductionData->id]);
            endif;
            
            if ($inwardData->entry_type==1 && empty($inwardData->trans_ref_id)) :
                //update out qty
                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['id'] = $inwardData->production_approval_id;
                //if (!empty($processApproval->out_process_id))
                $setData['set']['out_qty'] = 'out_qty, - ' . $inwardData->in_qty;
                $setData['set']['total_ok_qty'] = 'total_ok_qty, - ' . $inwardData->out_qty;
                $this->setValue($setData);
            endif;
            if (!empty($processApproval->out_process_id) && $inwardData->entry_type==1 && empty($inwardData->trans_ref_id)) :
                /** Next Process */
                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $processApproval->out_process_id;
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = 0;
                $setData['set']['inward_qty'] = 'inward_qty, - ' . $inwardData->in_qty;
                $setData['set']['in_qty'] = 'in_qty, - ' . $inwardData->out_qty;
                $this->setValue($setData);
            endif;

            /*** If First Process then Maintain Batchwise Rowmaterial ***/
            $jobProcesses = explode(",", $jobData->process);
            if ($inwardData->process_id == $jobProcesses[count( $jobProcesses)-1]) :
                $totalLogData=$this->logSheet->getPrdLogOnProcessNJob($inwardData->job_card_id);
                $total_rej_qty = (!empty($totalLogData->rejection_qty)) ? $totalLogData->rejection_qty : 0;
                $total_prod_qty=($processApproval->out_qty+$total_rej_qty);
                /* Update Used Stock in Job Material Used */
                $setData = array();
                $setData['tableName'] = $this->jobUsedMaterial;
                $setData['where']['job_card_id'] = $inwardData->job_card_id;
                $setData['set']['used_qty'] = 'used_qty, - (' . $total_prod_qty . '* wp_qty)';
                $qryresult = $this->setValue($setData);
                $this->remove($this->stockTransaction,['trans_type' => 2,'trans_ref_id'=> $id,'ref_type'=>27,'ref_id' =>$inwardData->job_card_id]);
            endif;

            if ($jobProcesses[count($jobProcesses) - 1] == $inwardData->process_id && empty($inwardData->trans_ref_id)) :
                $setData = array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $inwardData->job_card_id;
                $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $inwardData->out_qty;
                $this->setValue($setData);

                // $this->remove($this->stockTransaction,['trans_type'=>1,'trans_ref_id'=> $inwardData->production_approval_id,'ref_type'=>7,'ref_id' =>$inwardData->job_card_id]);

                $this->remove($this->stockTransaction,['trans_type'=>1,'trans_ref_id'=> $id,'ref_type'=>7,'ref_id' =>$inwardData->job_card_id]);
            endif;

            if ($jobProcesses[count($jobProcesses) - 1] == $inwardData->process_id && !empty($inwardData->trans_ref_id)) :
                $this->remove($this->stockTransaction,['trans_type'=>1,'trans_ref_id'=> $id,'ref_type'=>7,'ref_id' =>$inwardData->job_card_id]);
            endif;
            $result = $this->trash($this->productionTrans, ['id' => $id], 'Outward');

            $queryData = array();
            $queryData['tableName'] = $this->productionLog;
            $queryData['where']['ref_id'] = $id;
            $queryData['where']['prod_type'] = 3;
            $logData = $this->row($queryData);
            if(!empty($logData)):
                $this->trash($this->productionLog, ['ref_id' => $id, 'prod_type' => 3], 'Outward');
                $this->trash($this->rej_rw_management, ['log_id' =>$logData->id], 'Outward');
                $this->remove($this->stockTransaction,['ref_type'=>24,'ref_no'=>'REJ','ref_id'=>$logData->job_card_id,'trans_ref_id'=>$logData->id]);
                $this->remove($this->stockTransaction,['ref_type'=>23,'ref_id'=>$logData->job_card_id,'ref_batch'=>$logData->id,'trans_type'=>2]);
            endif;

            $this->remove($this->stockTransaction,['trans_ref_id' => $id,'ref_type' => 23,'ref_id' =>$inwardData->job_card_id]); 
                 
            
            /* Get Pending Qty for Movement */
            $logData = $this->logSheet->getPrdLogOnProcessNJob($inwardData->job_card_id, $inwardData->process_id);
            $rejection_qty = (!empty($logData->rejection_qty)) ? $logData->rejection_qty : 0;
            $pendingQty = 0;
            if(empty($inwardData->ref_id)):
                $approvalData = $this->getApprovalData($inwardData->production_approval_id);
                $pendingQty = $approvalData->in_qty - $approvalData->out_qty - $rejection_qty;
            else:
                $vendorTransData = $this->jobWorkVendor_v2->getJobWorkVendorRow($inwardData->ref_id);
                $pendingQty = (!empty($vendorTransData->pending_qty))?$vendorTransData->pending_qty:0;
            endif;

            $result['outwardTrans'] = $this->getOutwardTrans($inwardData->production_approval_id, $inwardData->process_id,$inwardData->trans_ref_id,$inwardData->entry_type)['htmlData'];
            $result['job_approval_id'] = $inwardData->production_approval_id;
			$result['process_id'] = $inwardData->process_id;
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

    public function getOutward($id)
    {
        $data['tableName'] = $this->productionApproval;
        $data['select'] = "production_approval.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code";
        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['where']['production_approval.id'] = $id;
        return $this->row($data);
    }

    public function getStoreLocationTrans($id)
    {
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['where']['stock_transaction.ref_type'] = 28;
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_batch'] = NULL;
        $stockTrans = $this->rows($queryData);
        $html = '';
        if (!empty($stockTrans)) :
            $i = 1;
            foreach ($stockTrans as $row) :
                $deleteBtn = '<button type="button" onclick="trashStockTrans(' . $row->id . ');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr>
                            <td class="text-center" style="width: 5%;">' . $i++ . '</td>
                            <td class="text-center">' . $row->batch_no . '</td>
                            <td class="text-center">[ ' . $row->store_name . ' ] ' . $row->location . '</td>
                            <td class="text-center">' . $row->qty . '</td>
                            <td class="text-center" style="width: 8%;">' . $deleteBtn . '</td>
                        </tr>';
            endforeach;
        else :
            $html .= '<tr>
                        <td class="text-center" colspan="5">No Data Found.</td>
                    </tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $stockTrans];
    }

    public function saveStoreLocation($data)
    {
        try {
            $this->db->trans_begin();
            $jobData = $this->jobcard_v2->getJobcard($data['job_id']);

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $data['ref_id'];
            $processMovementData = $this->row($queryData);            

            /** Process Store Minus Effect */
            $curentPrsStore = $this->getProcessStore($processMovementData->in_process_id);
            $stockMinusTrans = [
                'id' => "",
                'location_id' => $curentPrsStore->id,
                'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_type' => 2,
                'item_id' => $data['product_id'],
                'qty' => '-' . $data['qty'],
                'ref_type' => 7,
                'ref_id' => $data['job_id'],
                'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_ref_id' => $data['ref_id'],
                'ref_date' => $data['trans_date'],
                'created_by' => $data['created_by'],
                'stock_effect' => 0
            ];
            $this->saveProcessStockEffect($stockMinusTrans);

            $stockTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'trans_type' => 1,
                'item_id' => $data['product_id'],
                'qty' => $data['qty'],
                'ref_type' => 28,
                'ref_id' => $data['job_id'],
                'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_ref_id' => $data['ref_id'],
                'ref_date' => $data['trans_date'],
                'created_by' => $data['created_by']
            ];
            if($data['location_id'] == $this->HLD_STORE->id){
                $stockTrans['stock_effect']=0;
            }
            if (!empty($data['batch_no']))
                $stockTrans['batch_no'] = ($data['location_id'] == $this->RTD_STORE->id)?'General Batch':getPrefixNumber($jobData->job_prefix, $jobData->job_no);
            $stockSave = $this->store($this->stockTransaction, $stockTrans);            

            /*************************************************/
            /* Mansee @ 14-06-2022 Save In Production Transaction*/
            if($data['location_id'] == $this->HLD_STORE->id){
                $prdTrans=[
                    'id'=>'',
                    'entry_type'=>'3',
                    'entry_date'=>$data['trans_date'],
                    'ref_id'=> $stockSave['insert_id'],
                    'job_card_id'=>$data['job_id'],
                    'production_approval_id'=>$data['ref_id'],
                    'process_id'=>$processMovementData->in_process_id,
                    'product_id'=>$processMovementData->product_id,
                    'in_qty'=>$data['qty'],
                    'created_by'=>$this->session->userdata('loginId'),
                    'created_at'=>date("Y-m-d H:i:s")
                ];
                $this->store($this->productionTrans,$prdTrans); 
            }
            /*************************************************/
            $setData = array();
            $setData['tableName'] = $this->jobCard;
            $setData['where']['id'] = $data['job_id'];
            $setData['set']['unstored_qty'] = 'unstored_qty, - ' . $data['qty'];
            $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $data['product_id'];
            $setData['set']['qty'] = 'qty, + ' . $data['qty'];
            $this->setValue($setData);

            /* $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['id'] = $data['ref_id'];
            $setData['set']['out_qty'] = 'out_qty, + ' . $data['qty'];
            $this->setValue($setData); */

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $data['ref_id'];
            $approvalData = $this->row($queryData);

            $jobCardData = $this->jobcard_v2->getJobCard($data['job_id']); 
            $logData=$this->logSheet->getPrdLogOnProcessNJob($data['job_id']);
            $totalQty = $logData->rejection_qty + $approvalData->out_qty;
            if ($totalQty >= $jobCardData->qty) :
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
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $stockTrans->item_id;
                $setData['set']['qty'] = 'qty, - ' . $stockTrans->qty;
                $this->setValue($setData);

                /*  $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['id'] = $stockTrans->trans_ref_id;
                $setData['set']['out_qty'] = 'out_qty, - ' . $stockTrans->qty;
                $this->setValue($setData); */

                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['id'] = $stockTrans->trans_ref_id;
                $approvalData = $this->row($queryData);

                $jobCardData = $this->jobcard_v2->getJobCard($stockTrans->ref_id);
                // $logData=$this->logSheet->getPrdLogOnProcessNJob($stockTrans->ref_id);
                // $totalQty = $logData->rejection_qty + $approvalData->out_qty;
                // if ($totalQty < $jobCardData->qty) :
                    
                // endif;
                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 2]);
                $this->remove($this->stockTransaction, ['id' => $id]);
                $this->trash($this->productionTrans, ['ref_id' => $id,'entry_type'=>3]);
                $this->remove($this->stockTransaction,['trans_type'=>2,'ref_type'=>7,'trans_ref_id'=>$stockTrans->trans_ref_id,'ref_id'=> $stockTrans->ref_id]);

                
                $result = ['status' => 1, 'message' => 'Stock Transaction deleted successfully.', 'htmlData' => $this->getStoreLocationTrans($stockTrans->ref_id)['htmlData'], 'unstored_qty' => $jobCardData->unstored_qty,'ref_id'=>$stockTrans->ref_id];
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

    /** Save Out qty in process Stock */
    public function saveProcessStockEffect($data)
    {
        try {
            $this->db->trans_begin();
            $result = $this->store("stock_transaction", $data);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }


    /** Get Process wise store */
    public function getProcessStore($process_id)
    {
        $strQuery = array();
        $strQuery['where']['ref_id'] = $process_id;
        $strQuery['tableName'] = 'location_master';
        $strResult = $this->row($strQuery);
        return $strResult;
    }

    public function getOutwardTransDetail($id){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "production_transaction.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,(CASE WHEN production_approval.in_process_id = 0 THEN 'Initial Stage' ELSE ipm.process_name END) as in_process_name, opm.process_name as out_process_name,production_approval.in_process_id,production_approval.out_process_id";
        $data['leftJoin']['production_approval'] = "production_approval.id = production_transaction.production_approval_id";
        $data['leftJoin']['job_card'] = "production_approval.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "production_approval.product_id = item_master.id";
        $data['leftJoin']['process_master AS ipm'] = "production_approval.in_process_id = ipm.id";
        $data['leftJoin']['process_master AS opm'] = "production_approval.out_process_id = opm.id";
        $data['where']['production_transaction.id'] = $id;
        return $this->row($data);
    }
}
