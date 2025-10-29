<?php
class OutsourceModel extends MasterModel
{
    private $jobCard = "job_card";
    private $jobTrans = "job_transaction";
    private $jobApproval = "job_approval";
    private $outsourceChallan = "outsource_challan";

    public function getDTRows($data)
    { 
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,job_card.job_date,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.wo_no,party_master.party_name,item_master.item_name,item_master.item_code,item_master.full_name,process_master.process_name,(job_transaction.qty - job_transaction.outsource_qty) as pending_qty,outsource_challan.trans_number,outsource_challan.trans_date";

        $data['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $data['leftJoin']['outsource_challan'] = "job_transaction.challan_id = outsource_challan.id";
        $data['leftJoin']['party_master'] = "job_transaction.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id";
        $data['leftJoin']['process_master'] = "job_transaction.process_id = process_master.id";
        $data['where']['job_transaction.entry_type'] = 3;
        if ($data['status'] == 0) :
            $data['where']['(job_transaction.qty-job_transaction.outsource_qty) > '] = 0;
        endif;
        if ($data['status'] == 1) :
            $data['where']['(job_transaction.qty-job_transaction.outsource_qty)'] = 0;
        endif;
        if (!empty($data['from_date'])) {
            $data['where']['job_card.job_date >= '] = $data['from_date'];
        }
        if (!empty($data['to_date'])) {
            $data['where']['job_card.job_date <= '] = $data['to_date'];
        }

        $data['order_by']['job_transaction.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(outsource_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "outsource_challan.trans_number";
        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "job_card.wo_no";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.full_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "job_transaction.qty";
        $data['searchCol'][] = "job_transaction.outsource_qty";
        $data['searchCol'][] = "(job_transaction.qty - job_transaction.outsource_qty)";

        $columns = array('', '', 'outsource_challan.trans_date', 'outsource_challan.trans_number', 'job_card.job_no', 'job_card.wo_no', 'party_master.party_name', 'item_master.full_name', 'process_master.process_name', 'job_transaction.qty', 'job_transaction.outsource_qty', '', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        // print_r($this->printQuery());exit;
        return $result;
    }

    public function getPendingOSTransaction($postData)
    {
        $queryData['tableName'] = $this->jobTrans;
        $queryData['select'] = "job_transaction.*,(job_transaction.qty - job_transaction.outsource_qty) as pending_qty,process_master.process_name,item_master.full_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,job_card.job_number,party_master.party_name,job_card.job_date";
        $queryData['leftJoin']['process_master'] =  "process_master.id = job_transaction.process_id";
        $queryData['leftJoin']['item_master'] =  "item_master.id = job_transaction.product_id";
        $queryData['leftJoin']['job_card'] =  "job_card.id = job_transaction.job_card_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = job_transaction.send_to";
        $queryData['where']['job_transaction.vendor_id'] = $postData['vendor_id'];
        $queryData['where_in']['job_transaction.entry_type'] = '6';
        $queryData['where']['(job_transaction.qty - job_transaction.outsource_qty) > '] = 0;
        $resultData = $this->rows($queryData);
        return $resultData;
    }

    public function nextChallanNo()
    {
        $data['select'] = "MAX(trans_no) as transNo";
        $data['tableName'] = $this->outsourceChallan;
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
        $transNo = $this->specificRow($data)->transNo;
        $nextChallanNo = (!empty($transNo)) ? ($transNo + 1) : 1;
        return $nextChallanNo;
    }

    public function save($data){ 
        //print_r($data); exit;
        try {
            $this->db->trans_begin();
            $transData = [
                'id' => $data['challan_id'],
                'trans_no' => $data['trans_no'],
                'trans_prefix' => $data['trans_prefix'],
                'trans_number' => $data['trans_prefix'] . sprintf("%04d", $data['trans_no']),
                'trans_date' => date('Y-m-d', strtotime($data['trans_date'])),
                'vendor_id' => $data['vendor_id'],
                'job_trans_id' => implode(',', $data['id']),
                'remark' => $data['remark'],
                'created_by' => $data['created_by']
            ];

            $result = $this->store($this->outsourceChallan, $transData, 'Vendor Challan');
            $challan_id = (!empty($data['challan_id']) ? $data['challan_id'] : $result['insert_id']);


            foreach ($data['id'] as $key => $value) :
                $movementData = $this->processMovement->getOutwardTransPrint($value);
                $nextPrsData = $this->processMovement->getProcessWiseApprovalData($movementData->job_card_id, $movementData->out_process_id);
                $jobTransData = [
                    'id' => (!empty($data['trans_id'][$key])) ? $data['trans_id'][$key] : '',
                    'entry_type' => 3,
                    'ref_id' => $value,
                    'entry_date' => date('Y-m-d', strtotime($data['trans_date'])),
                    'job_card_id' => $movementData->job_card_id,
                    'job_approval_id' => $nextPrsData->id,
                    'job_order_id' => '',
                    'vendor_id' => $data['vendor_id'],
                    'process_id' => $movementData->out_process_id,
                    'product_id' => $movementData->product_id,
                    'qty' => $data['ch_qty'][$key],
                    'remark' => $data['trans_remark'][$key],
                    'challan_id' =>  $challan_id,
                    'created_by' => $data['created_by']
                ];
                $outTransData = $this->store($this->jobTrans, $jobTransData);

                $setData = array();
                $setData['tableName'] = $this->jobTrans;
                $setData['where']['id'] = $value;
                $setData['set']['outsource_qty'] = 'outsource_qty, + ' . $data['ch_qty'][$key];
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $nextPrsData->id;
                $setData['set']['in_qty'] = 'in_qty, + ' . $data['ch_qty'][$key];
                $setData['set']['ch_qty'] = 'ch_qty, + ' . $data['ch_qty'][$key];
                $this->setValue($setData); 
            endforeach;

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
            $transData = $this->getVendorChallanTransData($id);
            foreach ($transData as $itm) {
                $setData = array();
                $setData['tableName'] = $this->jobTrans;
                $setData['where']['id'] = $itm->ref_id;
                $setData['set']['outsource_qty'] = 'outsource_qty, - ' . $itm->qty;
                $this->setValue($setData);
                $this->trash('job_transaction', ['ref_id' => $itm->id, 'entry_type' => 4], 'Vendor Challan');

                 $setData = array();
                $setData['tableName'] = $this->jobApproval;
                $setData['where']['id'] = $itm->job_approval_id;
                $setData['set']['in_qty'] = 'in_qty, - ' . $itm->qty;
                $setData['set']['ch_qty'] = 'ch_qty, - ' . $itm->qty;
                $this->setValue($setData);           
            }
            $this->trash($this->outsourceChallan, ['id' => $id], 'Vendor Challan');
            $result = $this->trash('job_transaction', ['challan_id' => $id, 'entry_type' => 3], 'Vendor Challan');
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getVendorChallan($id)
    {
        $queryData['tableName'] = $this->outsourceChallan;
        $queryData['select'] = "outsource_challan.*,party_master.party_name,party_master.party_address,party_master.gstin,employee_master.emp_name as prepared_by";
        $queryData['join']['party_master'] = "party_master.id = outsource_challan.vendor_id";
        $queryData['join']['employee_master'] = "employee_master.id = outsource_challan.created_by";
        $queryData['where']['outsource_challan.id'] = $id;
        $result = $this->row($queryData);
        //print_r($this->printQuery());
        return $result;
    }

    public function getVendorChallanTransData($challan_id)
    {
        $queryData['tableName'] = $this->jobTrans;
        $queryData['select'] = "job_transaction.*,job_card.job_no,job_card.job_prefix,job_card.job_number,item_master.full_name,item_master.item_name,item_master.size,item_master.item_code,process_master.process_name,job_card.wo_no,trans_child.material_grade,item_master.wt_pcs as finished_weight";
        $queryData['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $queryData['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id";
        $queryData['leftJoin']['process_master'] = "job_transaction.process_id = process_master.id";
        $queryData['leftJoin']['trans_child'] = "job_card.so_trans_id = trans_child.id";
		$queryData['leftJoin']['product_process'] = "product_process.item_id = item_master.id AND product_process.process_id = job_transaction.process_id";
        $queryData['where']['job_transaction.challan_id'] = $challan_id;
        $queryData['where_in']['job_transaction.entry_type'] = '3';
        $resultData = $this->rows($queryData);
        return $resultData;
    }
}
