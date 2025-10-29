<?php
class ScrapModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $scrapBook = "scrap_book";
    private $scrapBookTrans = "scrap_book_trans";
    private $rejRwManagement = "rej_rw_management";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->scrapBook;
        $data['select'] = 'scrap_book.*,item_master.item_name,job_card.job_no,job_card.job_prefix';
        $data['leftJoin']['item_master'] = "item_master.id=scrap_book.item_id";
        $data['leftJoin']['job_card'] = "job_card.id=scrap_book.job_card_id";

        $columns = array('', '', 'scrap_book.trans_date', 'item_master.item_name', 'scrap_book.scrap_book', 'scrap_book.ok_qty', 'scrap_book.job_no');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function saveProductionRejScrape($data)
    {
        // print_r($data);exit;
        try {
            $this->db->trans_begin();
            $masterData = [
                'id' => "",
                'trans_date' => $data['ref_date'],
                'job_card_id' => $data['job_card_id'],
                'item_id' => $data['item_id'],
                'scrap_qty' => array_sum($data['scrap_qty']),
                'ok_qty' => array_sum($data['ok_qty']),
                'created_by' => $this->loginId
            ];
            $result = $this->store("scrap_book", $masterData);
            $jobData = $this->jobcard_v2->getJobcard($data['job_card_id']);
            $scrapQty=0;
            foreach ($data['log_sheet_id'] as $key => $value) {
                if (!empty($data['scrap_qty'][$key]) || !empty($data['ok_qty'][$key])) {
                    $logData=$this->logSheet->getLogs($value); 
                    $approveData=$this->jobcard_v2->getJobApprovalDetail($data['job_card_id'],$logData->process_id);
                    
                    $transData = [
                        'id' => '',
                        'scrap_id' => $result['insert_id'],
                        'log_id' => $value,
                        'rej_log_id' => $data['rej_log_id'][$key],
                        'scrap_qty' => $data['scrap_qty'][$key],
                        'ok_qty' => $data['ok_qty'][$key],
                        'rej_reason' => $data['rej_reason'][$key],
                        'rej_stage' => $data['rej_stage'][$key],
                        'rej_from' => $data['rej_from'][$key],
                        'wp_qty' => $approveData->finished_weight
                    ];
                    $this->store("scrap_book_trans", $transData);
                    if (!empty($data['ok_qty'][$key])) {
                        $setData = array();
                        $setData['tableName'] = 'production_log';
                        $setData['where']['id'] = $value;
                        $setData['set']['rej_qty'] = 'rej_qty, - ' . $data['ok_qty'][$key];
                        $this->setValue($setData);



                        /** Add Ok qty on jobcard process store */
                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' => $data['location_id'][$key],
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                            'trans_type' => 1,
                            'item_id' => $jobData->product_id,
                            'qty' =>  $data['ok_qty'][$key],
                            'ref_type' => 23,
                            'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                            'ref_id' => $data['job_card_id'],
                            'ref_date' => date("Y-m-d"),
                            'ref_batch' => $value,
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        $this->store($this->stockTrans, $stockMinusTrans);

                        /** Remove Ok qty from rejection store */
                        $stockPlusTrans = [
                            'id' => "",
                            'location_id' => $data['location_id'][$key],
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no) . "-R",
                            'trans_type' => 2,
                            'item_id' => $jobData->product_id,
                            'qty' => '-' . $data['ok_qty'][$key],
                            'ref_type' => 24,
                            'ref_no' => 'REJ',
                            'ref_id' => $data['job_card_id'],
                            'trans_ref_id' => $value,
                            'ref_date' => date("Y-m-d"),
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        $this->store($this->stockTrans, $stockPlusTrans);

                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $data['rej_log_id'][$key];
                        $setData['set']['qty'] = 'qty, - ' . $data['ok_qty'][$key];
                        $this->setValue($setData);

                        // $logData = $this->logSheet->getLogs($value);
                        // $jsonData = json_decode($logData->rej_reason);
                        // $arrayVar = [];
                        // // print_r($jsonData);
                        // foreach ($jsonData as $jsData) {
                        //     if ($jsData->rej_reason == $data['rej_reason'][$key] && $jsData->rej_stage == $data['rej_stage'][$key]) {
                        //         $jsData->rej_qty = $jsData->rej_qty - $data['ok_qty'][$key];
                        //     }
                        //     $arrayVar[] = $jsData;
                        // }
                        // // print_r($jsData);exit;

                        // $rej_reason = json_encode($arrayVar);
                        // // print_r($rej_reason);exit;
                        // $this->store("production_log", ['id' => $value, 'rej_reason' => $rej_reason]);
                        // // print_r($rej_reason);
                    }

                    if (!empty($data['scrap_qty'][$key])) {
                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' => $data['location_id'][$key],
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no) . '-R',
                            'trans_type' => 2,
                            'item_id' => $data['item_id'],
                            'qty' =>  "-" . $data['scrap_qty'][$key],
                            'ref_type' => 24,
                            'ref_no' => $data['ref_no'][$key],
                            'ref_id' => $data['job_card_id'],
                            'trans_ref_id' => $value,
                            'ref_date' => $data['ref_date'],
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        // print_r($stockMinusTrans);exit;
                        $this->store($this->stockTrans, $stockMinusTrans);
                        $scrapQty+=$data['scrap_qty'][$key] * $approveData->finished_weight;
                        

                    }
                }
            }
            if(!empty($scrapQty)){
                $queryData['tableName'] = 'job_bom';
                $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,material_master.scrap_group,material_master.material_grade";
                $queryData['leftJoin']['item_master'] = "job_bom.ref_item_id = item_master.id";
                $queryData['leftJoin']['material_master'] = "material_master.material_grade = item_master.material_grade";
                $queryData['where']['job_bom.item_id'] = $data['item_id'];
                $queryData['where']['job_bom.job_card_id'] = $data['job_card_id'];
                $kitData = $this->row($queryData);
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $this->SCRAP_STORE->id,
                    'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                    'trans_type' => 1,
                    'item_id' => $kitData->scrap_group,
                    // 'qty' => array_sum($data['scrap_qty']),
                    'qty'=>$scrapQty,
                    'ref_type' => 25,
                    'ref_no' => $kitData->ref_item_id,
                    'ref_batch' => $kitData->material_grade,
                    'ref_id' => $data['job_card_id'],
                    'trans_ref_id' => $result['insert_id'],
                    'ref_date' => $data['ref_date'],
                    'created_by' => $this->loginId
                ];
                $this->store($this->stockTrans, $stockPlusTrans);
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

    public function getRejectionStock($ref_id)
    {
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.trans_ref_id,stock_transaction.batch_no,stock_transaction.ref_no,location_master.location,stock_transaction.location_id,stock_transaction.item_id,stock_transaction.ref_id,item_master.item_name,production_log.*";
        $queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['join']['production_log'] = "production_log.id = stock_transaction.trans_ref_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
        $queryData['where']['stock_transaction.ref_type'] = 24;
        $queryData['group_by'][] = "stock_transaction.trans_ref_id";
        $result = $this->rows($queryData);
        // print_r($result);exit;
        return $result;
    }

    public function delete00($id)
    {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = "stock_transaction";
            $queryData['where']['stock_transaction.id'] = $id;
            $queryResult = $this->row($queryData);
            $result = $this->remove($this->stockTrans, ['id' => $id]);
            $result = $this->remove($this->stockTrans, ['id' => $queryResult->trans_ref_id]);
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

            $result = $this->getScrapBookData($id);

            $jobData = $this->jobcard_v2->getJobcard($result->job_card_id);
            $logData = $this->getScrapBookTransData($id);
            foreach ($logData as $row) {
                if (!empty($row->scrap_qty) || !empty($row->ok_qty)) {

                    $logSheetData = $this->logSheet->getLogs($row->log_id);
                    $curentPrsStore = $this->processApprove_v2->getProcessStore($logSheetData->process_id);
                    if (!empty($row->ok_qty)) {
                        $setData = array();
                        $setData['tableName'] = 'production_log';
                        $setData['where']['id'] = $row->log_id;
                        $setData['set']['rej_qty'] = 'rej_qty, + ' . $row->ok_qty;
                        $this->setValue($setData);

                        /** Remove Ok qty on jobcard process store */

                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' => $curentPrsStore->id,
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                            'trans_type' => 2,
                            'item_id' => $jobData->product_id,
                            'qty' =>  '-' . $row->ok_qty,
                            'ref_type' => 23,
                            'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                            'ref_id' => $jobData->id,
                            'ref_date' => date("Y-m-d"),
                            'ref_batch' => $row->log_id,
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        $this->store($this->stockTrans, $stockMinusTrans);

                        /** Remove Ok qty from rejection store */
                        $stockPlusTrans = [
                            'id' => "",
                            'location_id' => $curentPrsStore->id,
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no) . "-R",
                            'trans_type' => 1,
                            'item_id' => $jobData->product_id,
                            'qty' => $row->ok_qty,
                            'ref_type' => 24,
                            'ref_no' => 'REJ',
                            'ref_id' => $jobData->id,
                            'trans_ref_id' => $row->log_id,
                            'ref_date' => date("Y-m-d"),
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        $this->store($this->stockTrans, $stockPlusTrans);

                        $setData = array();
                        $setData['tableName'] = $this->rejRwManagement;
                        $setData['where']['id'] = $row->rej_log_id;
                        $setData['set']['qty'] = 'qty, + ' . $row->ok_qty;
                        $this->setValue($setData);

                        // $jsonData = json_decode($logSheetData->rej_reason);
                        // $arrayVar = [];
                        // foreach ($jsonData as $jsData) {
                        //     if ($jsData->rej_reason == $row->rej_reason  && $jsData->rej_stage == $row->rej_stage) {
                        //         $jsData->rej_qty = $jsData->rej_qty + $row->ok_qty;
                        //     }
                        //     $arrayVar[] = $jsData;
                        // }

                        // $rej_reason = json_encode($arrayVar);
                        // // print_r($rej_reason);exit;
                        // $this->store("production_log", ['id' =>$row->log_id, 'rej_reason' => $rej_reason]);
                        // // print_r($rej_reason);
                    }

                    if (!empty($row->scrap_qty)) {
                        $stockMinusTrans = [
                            'id' => "",
                            'location_id' => $curentPrsStore->id,
                            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no) . "-R",
                            'trans_type' => 1,
                            'item_id' => $jobData->product_id,
                            'qty' => $row->scrap_qty,
                            'ref_type' => 24,
                            'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                            'ref_id' => $jobData->id,
                            'trans_ref_id' => $row->log_id,
                            'ref_date' => date("Y-m-d"),
                            'created_by' => $this->loginId,
                            'stock_effect'=>0
                        ];
                        // print_r($stockMinusTrans);exit;
                        $this->store($this->stockTrans, $stockMinusTrans);
                    }
                    $this->trash($this->scrapBookTrans, ['id' => $row->id]);
                }
            }
            $this->trash($this->scrapBook, ['id' => $id]);
            $this->remove($this->stockTrans, ['ref_type' => 25, 'ref_id' => $result->job_card_id, 'trans_ref_id' => $id]);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobcardList()
    {
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "job_card.id as job_card_id,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.item_name,SUM(stock_transaction.qty) as qty";
        $queryData['leftJoin']['job_card'] = "stock_transaction.ref_id = job_card.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_type'] = 24;
        $queryData['group_by'][] = "stock_transaction.ref_id";
        $result = $this->rows($queryData);

        return $result;
    }

    public function getScrapBookData($id)
    {
        $data['tableName'] = $this->scrapBook;
        $data['select'] = 'scrap_book.*,item_master.item_name,job_card.job_no,job_card.job_prefix';
        $data['leftJoin']['item_master'] = "item_master.id=scrap_book.item_id";
        $data['leftJoin']['job_card'] = "job_card.id=scrap_book.job_card_id";
        $data['where']['scrap_book.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getScrapBookTransData($id)
    {
        $data['tableName'] = $this->scrapBookTrans;
        $data['select'] = 'scrap_book_trans.*,process_master.process_name';
        $data['leftJoin']['production_log'] = "production_log.id=scrap_book_trans.log_id";
        $data['leftJoin']['process_master'] = "process_master.id=production_log.process_id";
        $data['where']['scrap_id'] = $id;
        $result = $this->rows($data);
        return $result;
    }

    public function getScrapBookTransRejData($id)
    {
        $data['tableName'] = $this->scrapBookTrans;
        $data['select'] = 'SUM(scrap_book_trans.scrap_qty) as qty';
        $data['where']['rej_log_id'] = $id;
        $result = $this->row($data);
        return $result;
    }
}
