<?php

class RmProcessModel extends MasterModel
{
    private $stockTransaction = "stock_transaction";
    private $jobworkOrder = "job_work_order";

    public function getDTRows($data){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = "stock_transaction.*,item_master.item_name,item_master.item_code,party_master.party_name,job_work_order.jwo_prefix,job_work_order.jwo_no";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['party_master'] = "party_master.id = stock_transaction.ref_no";
        $data['leftJoin']['job_work_order'] = "job_work_order.id = stock_transaction.ref_batch";
        $data['where']['stock_transaction.ref_id'] = '';
        $data['where']['stock_transaction.ref_type'] = 29;
        $data['where']['stock_transaction.location_id'] = $this->RM_PRS_STORE->id;
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "stock_transaction.ref_date";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "stock_transaction.qty";
        $data['searchCol'][] = "party_master.party_name";
        $columns = array('', '', 'stock_transaction.ref_date', 'item_master.item_name', 'stock_transaction.qty', 'party_master.party_name');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getRmProcess($id){
        $data['tableName'] = $this->stockTransaction;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();
            foreach ($data['batch_quantity'] as $key => $value) :
                if ($value > 0) :
                    $stockMinusTrans = [
                        'id' => "",
                        'location_id' => $data['location'][$key],
                        'batch_no' => (!empty($data['batch_number'][$key]))?$data['batch_number'][$key]:'General Batch',
                        'trans_type' => 2,
                        'item_id' => $data['item_id'],
                        'qty' =>  "-" . $value,
                        'ref_type' => 29,
                        'ref_date' => $data['ref_date'],
                        'ref_batch' => $data['job_order_id'],
                        'created_by' => $this->loginId
                    ];
                    $result = $this->store($this->stockTransaction, $stockMinusTrans);
                endif;
            endforeach;
            
            $stockPlusTrans = [
                'id' => "",
                'location_id' => $this->RM_PRS_STORE->id,
                'trans_type' => 1,
                'item_id' => $data['item_id'],
                'qty' =>  array_sum($data['batch_quantity']),
                'ref_type' => 29,
                'ref_no' => $data['vendor_id'],
                'trans_ref_id' => $result['insert_id'],
                'ref_date' => $data['ref_date'],                        
                'ref_batch' => $data['job_order_id'],
                'created_by' => $this->loginId,
                'stock_effect'=>0
            ];
            $this->store($this->stockTransaction, $stockPlusTrans);

            if(!empty($data['job_order_id'])){
                $setData = array();
                $setData['tableName'] = $this->jobworkOrder;
                $setData['where']['id'] = $data['job_order_id'];
                $setData['set']['challan_qty'] = 'challan_qty, + '.array_sum($data['batch_quantity']);
                $this->setValue($setData);

                $orderData = $this->jobWorkOrder->getJobWorkOrder($data['job_order_id']);
                if($orderData->qty <= $orderData->challan_qty || $orderData->qty_kg <= $orderData->challan_qty){
                    $this->edit($this->jobworkOrder,['id'=>$data['job_order_id']],['jwo_status'=>1]);
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

    public function delete($id){
        try {
            $this->db->trans_begin();
            $result = $this->trash($this->stockTransaction,['ref_batch'=>$id],'RM Process');
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function batchWiseItemStock($data){
        $i = 1;
        $tbody = "";
        $locationData = $this->store->getStoreLocationList();
        if (!empty($locationData)) {
            foreach ($locationData as $lData) {
                foreach ($lData['location'] as $batch) :
                    $queryData = array();
                    $queryData['tableName'] = "stock_transaction";
                    $queryData['select'] = "SUM(qty) as qty,batch_no";
                    $queryData['where']['item_id'] = $data['item_id'];
                    $queryData['where']['location_id'] = $batch->id;
                    $queryData['order_by']['id'] = "asc";
                    $queryData['group_by'][] = "batch_no";
                    $result = $this->rows($queryData);
                    if (!empty($result)) {
                        $batch_no = array();
                        foreach ($result as $row) {
                            $batch_no = (!is_array($data['batch_no'])) ? explode(",", $data['batch_no']) : $data['batch_no'];
                            $batch_qty = (!is_array($data['batch_qty'])) ? explode(",", $data['batch_qty']) : $data['batch_qty'];
                            $location_id = (!is_array($data['location_id'])) ? explode(",", $data['location_id']) : $data['location_id'];
                            if ($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no, $batch_no)) :
                                if (!empty($batch_no) && in_array($row->batch_no, $batch_no) && in_array($batch->id, $location_id)) :
                                    $qty = 0;
                                    foreach ($batch_no as $key => $value) :
                                        if ($key == array_search($batch->id, $location_id)) :
                                            $qty = $batch_qty[$key];
                                            break;
                                        endif;
                                    endforeach;
                                    $cl_stock = (!empty($data['trans_id'])) ? floatVal($row->qty + $qty) : floatVal($row->qty);
                                else :
                                    $qty = "0";
                                    $cl_stock = floatVal($row->qty);
                                endif;
                                $tbody .= '<tr>';
                                $tbody .= '<td class="text-center">' . $i . '</td>';
                                $tbody .= '<td>[' . $lData['store_name'] . '] ' . $batch->location . '</td>';
                                $tbody .= '<td>' . $row->batch_no . '</td>';
                                $tbody .= '<td>' . floatVal($row->qty) . '</td>';
                                $tbody .= '<td>
                                            <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="' . $i . '" data-cl_stock="' . $cl_stock . '" min="0" value="' . $qty . '" />
                                            <input type="hidden" name="batch_number[]" id="batch_number' . $i . '" value="' . $row->batch_no . '" />
                                            <input type="hidden" name="location[]" id="location' . $i . '" value="' . $batch->id . '" />
                                            <div class="error batch_qty' . $i . '"></div>
                                        </td>';
                                $tbody .= '</tr>';
                                $i++;
                            endif;
                        }
                    }
                endforeach;
            }
        } else {
            $tbody = '<tr><td class="text-center" colspan="5">No Data Found.</td></tr>';
        }
        return ['status' => 1, 'batchData' => $tbody];
    }

    /*Created By : Avruti @23-4-2022 */
    public function getRmProcessList($id){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = "stock_transaction.*,item_master.item_name,item_master.item_code,unit_master.unit_name,location_master.location";
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $data['where']['stock_transaction.ref_id'] = $id;
        $data['where']['trans_type'] = 1;
        $data['where']['ref_type'] = 29;
        return $this->rows($data);
    }

    public function getReturnPending($item_id,$ref_batch){
        $queryData = array();
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['select'] = "SUM(qty) as stock_qty";
        $queryData['where']['item_id'] = $item_id;
        $queryData['where']['ref_batch'] = $ref_batch;
        $queryData['where']['ref_type'] = 29;
        $queryData['where']['location_id'] = $this->RM_PRS_STORE->id;
        $result = $this->row($queryData);
        $stockQty = (!empty($result->stock_qty))?$result->stock_qty:0;
        return $stockQty;
    }

    public function saveReturnRm($data){
        try {
            $this->db->trans_begin();
            $stockPlusTrans = [
                'id' => "",
                'location_id' => $this->RM_PRS_STORE->id,
                'trans_type' => 2,
                'item_id' => $data['trans_ref_id'],
                'qty' =>  '-' . $data['qty'],
                'ref_type' => 29,
                'ref_no' => $data['ref_no'],
                'ref_id' => $data['ref_id'],
                'ref_date' => $data['ref_date'],
                'ref_batch' => $data['ref_batch'],
                'created_by' => $this->loginId,
                'stock_effect'=>0
            ];
            $result = $this->store($this->stockTransaction, $stockPlusTrans);
            $stockPlusTrans = [
                'id' => "",
                'location_id' => $data['location_id'],
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'item_id' => $data['return_item_id'],
                'qty' =>  $data['qty'],
                'ref_type' => 29,
                'ref_no' => $data['ref_no'],
                'ref_id' => $data['ref_id'],
                'trans_ref_id' => $result['insert_id'],
                'ref_date' => $data['ref_date'],
                'ref_batch' => $data['ref_batch'],
                'created_by' => $this->loginId
            ];
            $this->store($this->stockTransaction, $stockPlusTrans);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteReturnRm($id){
        $data['tableName'] = $this->stockTransaction;
        $data['where']['id'] = $id;
        $transData=$this->row($data);
        $this->remove($this->stockTransaction, ['id' =>$transData->trans_ref_id,'trans_type'=>2,'ref_type'=>29], "Record");
        $result = $this->remove($this->stockTransaction, ['id' => $id], "Record");
        return $transData->ref_id;
    }
    
    public function getJobOrderList($vendor_id,$product_id){
		$data['tableName'] = $this->jobworkOrder;
		$data['select'] = "job_work_order.id,job_work_order.jwo_prefix,job_work_order.jwo_no,job_work_order.product_id,job_work_order.vendor_id,party_master.party_name";
		$data['leftJoin']['party_master'] = "party_master.id = job_work_order.vendor_id";
        $data['where']['job_work_order.vendor_id'] = $vendor_id;
        $data['where']['job_work_order.product_id'] = $product_id;
        $data['where']['job_work_order.is_approve !='] = 0;
		return $this->rows($data);
	}
}
