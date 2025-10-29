<?php
class RejectionLogModel extends MasterModel
{
    private $productionLogMaster = "production_log";
    private $itemMaster = "item_master";
    private $processMaster = "process_master";
    private $empMaster = "employee_master";
    private $jobApproval = "job_approval";
    private $stockTransaction = "stock_transaction";
    private $rejRwManagement = "rej_rw_management";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->productionLogMaster;
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,itm.item_code as product_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $data['leftJoin']['item_master itm'] = "itm.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        //$data['where_in']['production_log.prod_type'] = "1,3";
        $data['where_in']['production_log.prod_type'] = $data['prod_type'];
        $data['where']['production_log.log_date >= '] = $this->startYearDate;
        $data['where']['production_log.log_date <= '] = $this->endYearDate;
        $data['customWhere'][] = '(production_log.rej_qty > 0 OR production_log.rw_qty > 0)';
        
        $data['searchCol'][] ="";
        $data['searchCol'][] ="";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "itm.item_code";
        $data['searchCol'][] = "DATE_FORMAT(production_log.log_date,'%d-%m-%Y')";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "production_log.rej_qty";
        $data['searchCol'][] = "production_log.rw_qty";

        $columns = array('', '', 'job_card.job_no', 'itm.item_code', 'production_log.log_date', 'process_master.process_name', 'item_master.item_code', 'employee_master.emp_name', 'production_log.production_qty', 'production_log.rej_qty', 'production_log.rw_qty');
        if (isset($data['order'])) {

            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        } else {
            $data['order_by']['production_log.log_date'] = 'DESC';
            $data['order_by']['production_log.id'] = 'DESC';
        }
        return $this->pagingRows($data);
    }

    public function getJobWorkOrder($data)
    {
        $queryData['tableName'] = "vendor_production_trans";
        $queryData['select'] = "vendor_production_trans.vendor_id,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = vendor_production_trans.vendor_id";
        $queryData['where']['vendor_production_trans.product_id'] = $data['part_id'];
        $queryData['where']['vendor_production_trans.process_id'] = $data['process_id'];
        $queryData['where']['vendor_production_trans.job_card_id'] = $data['job_card_id'];
        $queryData['group_by'][] = ['vendor_production_trans.vendor_id'];
        return $this->rows($queryData);
    }

    public function getJobWorkOrder00($data)
    {
        $queryData['tableName'] = "job_work_order";
        $queryData['select'] = "job_work_order.vendor_id,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = job_work_order.vendor_id";
        $queryData['where']['job_work_order.product_id'] = $data['part_id'];
        $queryData['where']['job_work_order.process_id'] = $data['process_id'];
        $queryData['where']['job_work_order.is_approve != '] = 0;
        $queryData['group_by'][] = ['job_work_order.vendor_id'];
        return $this->rows($queryData);
    }

    public function getLogs($id)
    {
        $data['tableName'] = $this->productionLogMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data, $reworkRejData = array())
    {
        // print_r($data);exit;
        try {
            $this->db->trans_begin();
            $rejectionData = !empty($data['rejection_reason']) ? $data['rejection_reason'] : '';
            $reworkData = !empty($data['rework_reason']) ? $data['rework_reason'] : '';
            $from_entry_type=!empty($data['from_entry_type'])?$data['from_entry_type']:'';
            unset($data['rework_reason'], $data['rejection_reason'],$data['from_entry_type']);

            if (!empty($data['id'])) :
                $logData = $this->getLogs($data['id']);
                $this->remove($this->stockTransaction, ['ref_type' => 24, 'ref_no' => 'REJ', 'ref_id' => $logData->job_card_id, 'trans_ref_id' => $data['id']]);
                $this->remove($this->stockTransaction, ['ref_type' => 23, 'ref_id' => $logData->job_card_id, 'ref_batch' => $data['id'], 'trans_type' => 2]);

                $rejRwData = $this->getRejRwData($data['id']);
                foreach ($rejRwData as $row) :
                    if (!empty($rejectionData)) :
                        if (!in_array($row->id, array_column($rejectionData, 'trans_id')) && $row->manag_type == 1) :
                            
                            // print_r($row);
                            if ($row->rej_type == 1) {
                               
                                //**  qty plus in rework log */

                                $setData = array();
                                $setData['tableName'] = $this->rejRwManagement;
                                $setData['where']['id'] = $row->ref_id;
                                $setData['set']['qty'] = 'qty, +' . $row->qty;
                                $this->setValue($setData);

                                // print_r($this->db->last_query());

                                //**  qty Plus in production_log log */

                                $setData = array();
                                $setData['tableName'] = 'production_log';
                                $setData['where']['id'] = $data['id'];
                                $setData['set']['rw_qty'] = 'rw_qty, + ' . $row->qty;
                                $this->setValue($setData);

                                // print_r($this->db->last_query());
                            }

                            $this->trash($this->rejRwManagement, ['id' => $row->id]);
                        endif;
                    endif;
                    if (!empty($reworkData)) :
                        if (!in_array($row->id, array_column($reworkData, 'trans_id'))  && $row->manag_type == 2) :
                            $this->trash($this->rejRwManagement, ['id' => $row->id]);
                        endif;
                    endif;
                endforeach;
                // exit;
            endif;

            $result = $this->store($this->productionLogMaster, $data, 'Production Log');
            $id = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
            if (!empty($rejectionData)) {
                foreach ($rejectionData as $row) {
                    $rejArray = [
                        'id' => (!empty($row['trans_id'])?$row['trans_id']:''),
                        'manag_type' => 1,
                        'log_id' => $id,
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rej_qty'],
                        'ref_id' => (!empty($row['rej_ref_id'])?$row['rej_ref_id']:''),
                        'rej_type' => (!empty($row['rej_type'])?$row['rej_type']:0),
                        'reason' => $row['rej_reason'],
                        'reason_name' => $row['rejection_reason'],
                        'belongs_to' => $row['rej_stage'],
                        'belongs_to_name' => $row['rej_stage_name'],
                        'vendor_id' => $row['rej_from'],
                        'vendor_name' => $row['rej_party_name'],
                        'remark' => $row['rej_remark'],
                        'created_by' => $data['created_by'],
                    ];
                    $this->store($this->rejRwManagement, $rejArray);
                }

                if (!empty($reworkRejData)) {
                    //** ok qty minus in rework log */

                    $setData = array();
                    $setData['tableName'] = $this->rejRwManagement;
                    $setData['where']['id'] = $reworkRejData['id'];
                    $setData['set']['qty'] = 'qty, - ' . $reworkRejData['rej_qty'];
                    $this->setValue($setData);


                    //** ok qty minus in production_log log */

                    $setData = array();
                    $setData['tableName'] = 'production_log';
                    $setData['where']['id'] = $id;
                    $setData['set']['rw_qty'] = 'rw_qty, - ' . $reworkRejData['rej_qty'];
                    $this->setValue($setData);
                }
            }
            if (!empty($reworkData)) {
                foreach ($reworkData as $row) {
                    $rwArray = [
                        'id' => $row['trans_id'],
                        'manag_type' => 2,
                        'log_id' => $id,
                        'job_card_id' => $data['job_card_id'],
                        'qty' => $row['rw_qty'],
                        'reason' => $row['rw_reason'],
                        'reason_name' => $row['rework_reason'],
                        'belongs_to' => $row['rw_stage'],
                        'belongs_to_name' => $row['rw_stage_name'],
                        'vendor_id' => $row['rw_from'],
                        'vendor_name' => $row['rw_party_name'],
                        'remark' => $row['rw_remark'],
                        'created_by' => $data['created_by'],
                    ];
                    $this->store($this->rejRwManagement, $rwArray);
                }
            }



            if (!empty($data['rej_qty'])) :
                $curentPrsStore = $this->processApprove_v2->getProcessStore($data['process_id']);
                $jobData = $this->jobcard_v2->getJobcard($data['job_card_id']);

                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => ((!empty($from_entry_type))?$this->HLD_STORE->id:$curentPrsStore->id),
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 2,
                    'item_id' => $jobData->product_id,
                    'qty' =>  "-" . $data['rej_qty'],
                    'ref_type' => 23,
                    'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'ref_id' => $data['job_card_id'],
                    'ref_date' => date("Y-m-d"),
                    'ref_batch' => $id,
                    'created_by' => $this->loginId,
                    'stock_effect'=>0
                ];
                $this->store($this->stockTransaction, $stockMinusTrans);

                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $curentPrsStore->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no) . "-R",
                    'trans_type' => 1,
                    'item_id' => $jobData->product_id,
                    'qty' =>  $data['rej_qty'],
                    'ref_type' => 24,
                    'ref_no' => 'REJ',
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $id,
                    'ref_date' => date("Y-m-d"),
                    'created_by' => $this->loginId,
                    'stock_effect'=>0
                ];
                $this->store($this->stockTransaction, $stockPlusTrans);


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

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $logData = $this->getLogs($id);
            $this->trash($this->rejRwManagement, ['log_id' => $id]);
            $this->remove($this->stockTransaction, ['ref_type' => 24, 'ref_no' => 'REJ', 'ref_id' => $logData->job_card_id, 'trans_ref_id' => $id]);
            $this->remove($this->stockTransaction, ['ref_type' => 23, 'ref_id' => $logData->job_card_id, 'ref_batch' => $id, 'trans_type' => 2]);
            $result = $this->trash($this->productionLogMaster, ['id' => $id], 'Production Log');
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /**
     * Created By Mansee
     * Note : Machine Log
     * Date : 12-02-2022
     */
    public function getMachineLogDtRows()
    {
        $data['tableName'] = $this->productionLogMaster;
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix";
        $data['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['where']['prod_type'] = 0;
        return $this->rows($data);
    }

    public function getPrdLogOnProcessNJob($job_card_id, $process_id = '')
    {
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['select'] = 'SUM(rej_qty) as rejection_qty,SUM(rw_qty) as rework_qty,SUM(ok_qty) as ok_qty,SUM(production_qty) as production_qty';
        $queryData['where']['production_log.job_card_id'] = $job_card_id;
        //$queryData['where_in']['production_log.prod_type'] = [1,3];
        if ($process_id != '')
            $queryData['where']['production_log.process_id'] = $process_id;
        return $this->row($queryData);
    }

    public function getRejBelongsTo($job_card_id, $process_id)
    {
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['where']['production_log.job_card_id'] = $job_card_id;
        $result = $this->rows($queryData);
        $rej_belongs = 0;
        if (!empty($result)) {
            foreach ($result as $row) {
                $rejData = json_decode($row->rej_reason);
                if (!empty($rejData)) {
                    foreach ($rejData as $row) {
                        if (isset($row->rej_stage)) {
                            if ($row->rej_stage == $process_id) {
                                $rej_belongs += $row->rej_qty;
                            }
                        }
                    }
                }
            }
        }
        return $rej_belongs;
    }

    public function getMasterCycleTime($data)
    {
        $data['where']['item_id'] = $data['product_id'];
        $data['where']['process_id'] = $data['process_id'];
        $data['tableName'] = 'product_process';
        return $this->row($data);
    }


    public function getJobWorkVendorTransData($data)
    {
        $queryData['tableName'] = "vendor_production_trans";
        $queryData['select'] = "vendor_production_trans.vendor_id,party_master.party_name";
        $queryData['leftJoin']['party_master'] = "party_master.id = vendor_production_trans.vendor_id";
        $queryData['where']['vendor_production_trans.job_card_id'] = $data['job_card_id'];
        $queryData['where']['vendor_production_trans.process_id'] = $data['process_id'];
        $queryData['group_by'] = ['vendor_production_trans.vendor_id'];
        return $this->rows($queryData);
    }

    public function saveReworkQty($data)
    {
        try {
            $this->db->trans_begin();
            /** Rework to Ok qty Entry */
            $reworkData = [
                'id' => '',
                'manag_type' => 3,
                'log_id' => $data['log_id'],
                'job_card_id' => $data['job_card_id'],
                'qty' => $data['rw_qty'],
                'reason' => $data['rw_reason'],
                'reason_name' => $data['rework_reason'],
                'belongs_to' => $data['rw_stage'],
                'belongs_to_name' => $data['rw_stage_name'],
                'vendor_id' => $data['rw_from'],
                'vendor_name' => $data['rw_party_name'],
                'remark' => $data['rw_remark'],
                'created_by' => $data['created_by'],
                'ref_id' => $data['trans_id']
            ];

            $result = $this->store($this->rejRwManagement, $reworkData);

            //** ok qty minus in rework log */
            $setData = array();
            $setData['tableName'] = $this->rejRwManagement;
            $setData['where']['id'] = $data['trans_id'];
            $setData['set']['qty'] = 'qty, - ' . $data['rw_qty'];
            $this->setValue($setData);

            //** ok qty minus in production_log log */

            $setData = array();
            $setData['tableName'] = 'production_log';
            $setData['where']['id'] = $data['log_id'];
            $setData['set']['rw_qty'] = 'rw_qty, - ' . $data['rw_qty'];

            $this->setValue($setData);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getRejRwData($log_id)
    {
        $queryData['tableName'] = $this->rejRwManagement;
        $queryData['select'] = "rej_rw_management.*";
        $queryData['where']['log_id'] = $log_id;
        return $this->rows($queryData);
    }

    public function getRejectionData($log_id, $manag_type = "1")
    {
        $queryData['tableName'] = $this->rejRwManagement;
        $queryData['select'] = "rej_rw_management.*";
        $queryData['where']['log_id'] = $log_id;
        $queryData['where']['manag_type'] = $manag_type;
        return $this->rows($queryData);
    }

    /* API Function Start */
    public function getLogSheetListing($data)
    {
        $queryData['tableName'] = $this->productionLogMaster;
        $queryData['select'] = "production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,job_card.job_no,job_card.job_prefix";
        $queryData['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = production_log.machine_id";
        $queryData['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $queryData['where_in']['production_log.prod_type'] = "1,3";
        $queryData['where']['production_log.log_date >= '] = $this->startYearDate;
        $queryData['where']['production_log.log_date <= '] = $this->endYearDate;
        $queryData['order_by']['production_log.log_date'] = 'DESC';

        if (!empty($data['search'])) :
            $queryData['like']['CONCAT(job_card.job_prefix,job_card.job_no)'] = $data['search'];
            $queryData['like']["process_master.process_name"] = $data['search'];
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['employee_master.emp_name'] = $data['search'];
            $queryData['like']['production_log.production_qty'] = $data['search'];
            $queryData['like']['production_log.rej_qty'] = $data['search'];
            $queryData['like']['production_log.rw_qty'] = $data['search'];
        endif;

        $queryData['length'] = $data['limit'];
        $queryData['start'] = $data['off_set'];

        return $this->rows($queryData);
    }
    /* API Function End */
}
