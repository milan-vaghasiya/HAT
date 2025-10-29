<?php
class JobWorkVendorModel extends MasterModel{
    private $jobCard = "job_card";
    private $productionApproval = "production_approval";
    private $productionTrans = "production_transaction";
    private $vendorProductionTrans = "vendor_production_trans";
    private $jobWorkChallan = "jobwork_challan";

    public function getDTRows($data){
        $data['tableName'] = $this->vendorProductionTrans;
        $data['select'] = "vendor_production_trans.*,job_card.job_date,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) as status_per,(vendor_production_trans.out_qty - vendor_production_trans.in_qty) as pending_qty";

        $data['leftJoin']['job_card'] = "vendor_production_trans.job_card_id = job_card.id";
        $data['leftJoin']['party_master'] = "vendor_production_trans.vendor_id = party_master.id";
        $data['leftJoin']['item_master'] = "vendor_production_trans.product_id = item_master.id";
        $data['leftJoin']['process_master'] = "vendor_production_trans.process_id = process_master.id";

        if($data['status'] == 0):
            $data['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) < '] = 100;
        endif;
        if($data['status'] == 1):
            $data['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) >= '] = 100;
            //$data['where']['job_card.job_date >= '] = $this->startYearDate;
            //$data['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        if(!empty($data['from_date'])){$data['where']['job_card.job_date >= '] = $data['from_date'];}
        if(!empty($data['to_date'])){$data['where']['job_card.job_date <= '] = $data['to_date'];}

        $data['order_by']['vendor_production_trans.id'] = "DESC";

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";

        $columns =array('','','job_card.job_no','party_master.party_name','item_master.item_code','process_master.process_name','vendor_production_trans.out_qty','vendor_production_trans.in_qty','vendor_production_trans.return_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
		//print_r($this->printQuery());exit;
        return $result;
    }

    public function getJobWorkVendorRow($id){
        $data['tableName'] = $this->vendorProductionTrans;
        $data['select'] = "vendor_production_trans.*,(vendor_production_trans.out_qty - vendor_production_trans.in_qty) as pending_qty";
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function saveJobWorkReturn($data){
        $setData = array();
        $setData['tableName'] = $this->vendorProductionTrans;
        $setData['where']['id'] = $data['id'];
        $setData['set']['out_qty'] = "out_qty, - " . $data['qty'];
        $setData['set']['return_qty'] = "return_qty, + " . $data['qty'];
        $this->setValue($setData);

        $returnData = $this->getJobWorkVendorRow($data['id']);
        $jsonData = array();
        if (!empty($returnData->return_json)) :
            $jsonData = json_decode($returnData->return_json);
            $jsonData[] = ['entry_date' => $data['entry_date'], 'qty' => $data['qty'], 'total_weight' => $data['total_weight'], 'remark' => $data['remark']];
        else :
            $jsonData[] = ['entry_date' => $data['entry_date'], 'qty' => $data['qty'], 'total_weight' => $data['total_weight'], 'remark' => $data['remark']];
        endif;
        $this->store($this->vendorProductionTrans, ['id' => $data['id'], 'return_json' => json_encode($jsonData)]);

        /*******************************************************/
        /** Remove From Transactions */
        /** Previous Process Data */
        $queryData = array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['job_card_id'] = $data['job_card_id'];
        $queryData['where']['out_process_id'] = $data['process_id'];
        $prevPrsData = $this->row($queryData);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['id'] = $prevPrsData->id;
        $setData['set']['out_qty'] = "out_qty, - " . $data['qty'];
        $setData['set']['total_ok_qty'] = "total_ok_qty, - " . $data['qty'];
        $this->setValue($setData);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['id'] = $data['production_approval_id'];
        $setData['set']['in_qty'] = "in_qty, - " . $data['qty'];
        $setData['set']['inward_qty'] = "inward_qty, - " . $data['qty'];
        $this->setValue($setData);

        
        $setData = array();
        $setData['tableName'] = $this->productionTrans;
        $setData['where']['id'] = $data['production_trans_id'];
        $setData['set']['out_qty'] = "out_qty, - " . $data['qty'];
        $this->setValue($setData);

        /** Process Stock Minus */
        /* product stock minus in current process*/
        $jobData = $this->jobcard_v2->getJobcard($data['job_card_id']);
        $curentPrsStore = $this->processApprove_v2->getProcessStore($data['process_id']);
        $stockMinusTrans = [
            'id' => "",
            'location_id' => $curentPrsStore->id,
            'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
            'trans_type' => 2,
            'item_id' => $data['product_id'],
            'qty' => '-' . $data['qty'],
            'ref_type' => 31,
            'ref_id' => $data['job_card_id'],
            'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
            'trans_ref_id' => $data['id'],
            'ref_batch'=>(count($jsonData)-1),
            'ref_date' => $data['entry_date'],
            'created_by' => $this->loginId
        ];
        $this->processApprove_v2->saveProcessStockEffect($stockMinusTrans);

        /* Stock Plus in previous process */    
        if(!empty($prevPrsData->in_process_id)){    
            $prevPrsStore = $this->processApprove_v2->getProcessStore($prevPrsData->in_process_id);
            $stockMinusTrans = [
                'id' => "",
                'location_id' => $prevPrsStore->id,
                'batch_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_type' => 1,
                'item_id' => $data['product_id'],
                'qty' =>$data['qty'],
                'ref_type' => 31,
                'ref_id' => $data['job_card_id'],
                'ref_no' => getPrefixNumber($jobData->job_prefix, $jobData->job_no),
                'trans_ref_id' => $data['id'],
                'ref_batch'=>(count($jsonData)-1),
                'ref_date' => $data['entry_date'],
                'created_by' => $this->loginId
            ];
            $this->processApprove_v2->saveProcessStockEffect($stockMinusTrans);
        }
        /*********************************************************/
        $transData = $this->getReturnTransaction($data['id']);
        return ['status' => 1, 'message' => "Material returned successfully.", 'transHtml' => $transData['html'], 'pending_qty' => $transData['pending_qty']];
    }

    public function deleteReturnTrans($data){
        $returnData = $this->getJobWorkVendorRow($data['id']);
        $jsonData = json_decode($returnData->return_json);
        $dataRow = $jsonData[$data['key']];
        unset($jsonData[$data['key']]);

        $setData = array();
        $setData['tableName'] = $this->vendorProductionTrans;
        $setData['where']['id'] = $data['id'];
        $setData['set']['out_qty'] = "out_qty, + " . $dataRow->qty;
        $setData['set']['return_qty'] = "return_qty, - " . $dataRow->qty;
        $this->setValue($setData);

        $jsonData = (!empty($jsonData)) ? json_encode($jsonData) : "";
        $this->store($this->vendorProductionTrans, ['id' => $data['id'], 'return_json' => $jsonData]);


        /** Remove From Transactions */
        /** Previous Process Data */
        $queryData = array();
        $queryData['tableName'] = $this->productionApproval;
        $queryData['where']['job_card_id'] = $returnData->job_card_id;
        $queryData['where']['out_process_id'] =$returnData->process_id;
        $prevPrsData = $this->row($queryData);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['id'] = $prevPrsData->id;
        $setData['set']['out_qty'] = "out_qty, + " . $dataRow->qty;
        $setData['set']['total_ok_qty'] = "total_ok_qty, + " . $dataRow->qty;
        $this->setValue($setData);

        $setData = array();
        $setData['tableName'] = $this->productionApproval;
        $setData['where']['id'] =$returnData->production_approval_id;
        $setData['set']['in_qty'] = "in_qty, + " . $dataRow->qty;
        $setData['set']['inward_qty'] = "inward_qty, + " . $dataRow->qty;
        $this->setValue($setData);

        
        $setData = array();
        $setData['tableName'] = $this->productionTrans;
        $setData['where']['id'] = $returnData->ref_id;
        $setData['set']['out_qty'] = "out_qty, + " .$dataRow->qty;
        $this->setValue($setData);

        $this->remove($this->stockTransaction,['trans_ref_id' => $data['id'],'ref_type' => 31,'ref_id' =>$returnData->job_card_id,'ref_batch'=>$data['key']]); 

        $transData = $this->getReturnTransaction($data['id']);
        return ['status' => 1, 'message' => "Transaction Removed successfully.", 'transHtml' => $transData['html'], 'pending_qty' => $transData['pending_qty']];
    }


    public function getReturnTransaction($id){
        $returnData = $this->getJobWorkVendorRow($id);
        $jsonData = array();
        if(!empty($returnData->return_json)):
            $jsonData = json_decode($returnData->return_json);
            $transHtml = "";
            $i=1;
            foreach($jsonData as $key=>$row):
                $transHtml .= '<tr>
                    <td class="text-center" style="width:10%;">'.$i.'</td>
                    <td class="text-center">'.$row->entry_date.'</td>
                    <td class="text-center">'.$row->qty.'</td>
                    <td>'.$row->remark.'</td>
                    <td class="text-center" style="width:20%;">
                        <button type="button" onclick="trashReturn('.$returnData->id.','.$key.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                    </td>
                </tr>';
                $i++;
            endforeach;
        else:
            $transHtml =  '<tr><td colspan="5" class="text-center">No data available in table</td></tr>';
        endif;
        return ['html'=>$transHtml,'result'=>$jsonData,'pending_qty'=>floatVal($returnData->pending_qty)];
    }

    public function getChallanDTRows($data){
        $data['tableName'] = $this->jobWorkChallan;
        $data['select'] = "jobwork_challan.*,party_master.party_name";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork_challan.vendor_id";
        $data['where']['jobwork_challan.version'] = 2;
        $data['where']['jobwork_challan.challan_date >= '] = $this->startYearDate;
        $data['where']['jobwork_challan.challan_date <= '] = $this->endYearDate;
        $data['order_by']['jobwork_challan.id'] = "DESC";

        $data['searchCol'][] = "DATE_FORMAT(jobwork_challan.challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "jobwork_challan.challan_no";
        $data['searchCol'][] = "party_master.party_name";
        
		$columns =array('','','jobwork_challan.challan_date','jobwork_challan.challan_no','party_master.party_name','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getJobInardDataById($id){
        $queryData['tableName'] = $this->vendorProductionTrans;
        $queryData['select'] = "vendor_production_trans.*,item_master.item_code,item_master.item_name";
        $queryData['leftJoin']['item_master'] = "vendor_production_trans.product_id = item_master.id";
		$queryData['where']['vendor_production_trans.id'] = $id;
		return $this->row($queryData);
    }    

    /* Vendor Challan */
	public function createVendorChallan($party_id){
		$queryData['tableName'] = $this->vendorProductionTrans;
		$queryData['select'] = "vendor_production_trans.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,party_master.party_name";
		$queryData['leftJoin']['process_master'] =  "process_master.id = vendor_production_trans.process_id";
		$queryData['leftJoin']['item_master'] =  "item_master.id = vendor_production_trans.product_id";
		$queryData['leftJoin']['job_card'] =  "job_card.id = vendor_production_trans.job_card_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = vendor_production_trans.vendor_id";
		$queryData['where']['vendor_production_trans.challan_status'] = 0;
		$queryData['where']['vendor_production_trans.vendor_id'] = $party_id;
		$resultData = $this->rows($queryData);
	
		$html="";
		if(!empty($resultData)):
			$i=1;
			foreach($resultData as $row):
				$html .= '<option value="' . $row->id . '" >['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).' (Out Qty.: '.floatVal($row->out_qty).', In Qty.: '.floatVal($row->in_qty).')</option>';
				$i++;
			endforeach;
		else:
			$html = '<tr><td class="text-center" colspan="7">No Data Found</td></tr>';
		endif;

		$materialData =  $this->packings->getConsumable(1);
		$mOption='<option value="">Select Material </option>';
		foreach($materialData as $row):
			$mOption.= '<option value="'.$row->id.'">'.$row->item_name.'</option>';
		endforeach; 

		return ['status'=>1,'htmlData'=>$html,'result'=>$resultData,'materialData'=>$mOption];
	}

    public function nextChallanNo(){
        $data['select'] = "MAX(challan_no) as challanNo";
        $data['tableName'] = $this->jobWorkChallan;
        $data['where']['version'] = 2;
        $data['where']['challan_date >= '] = $this->startYearDate;
        $data['where']['challan_date <= '] = $this->endYearDate;
		$challanNo = $this->specificRow($data)->challanNo;
		$nextChallanNo = (!empty($challanNo))?($challanNo + 1):1;
		return $nextChallanNo;
    }

    public function getVendorChallan($id){
        $queryData['tableName'] = $this->jobWorkChallan;
        $queryData['select'] = "jobwork_challan.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $queryData['leftJoin']['party_master'] = "party_master.id = jobwork_challan.vendor_id";
        $queryData['where']['jobwork_challan.id'] = $id;
        return $this->row($queryData);
    }

    public function saveVendorChallan($data){
        try{
            $this->db->trans_begin();

            $transData = [
                'id' => "",
                'challan_no' => $data['challan_no'],
                'challan_prefix' => $data['challan_prefix'],
                'challan_date' => date('Y-m-d',strtotime($data['challan_date'])),
                'vendor_id' => $data['vendor_id'],
                'job_inward_id' => $data['job_inward_id'],
                'material_data' => $data['material_data'],
                'created_by' => $data['created_by']
            ];
            $invardData = explode(',', $data['job_inward_id']);
            foreach($invardData as $key=>$value):
                $this->store($this->vendorProductionTrans,['id'=>$value,'challan_status'=>1]);  
            endforeach;

            $result = $this->store($this->jobWorkChallan,$transData,'Vendor Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();

            $challanData = $this->getVendorChallan($id);
            $invardData = explode(',', $challanData->job_inward_id);
            foreach($invardData as $key=>$value):
                $this->store($this->vendorProductionTrans,['id'=>$value,'challan_status'=>0]);  
            endforeach;

            $result = $this->trash($this->jobWorkChallan,['id'=>$id],'Vendor Challan');

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getJobworkOutData($id){
        $data['tableName'] = $this->vendorProductionTrans;
        $data['select'] = "vendor_production_trans.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix, party_master.party_name,party_master.party_address,party_master.gstin,job_work_order.jwo_prefix,job_work_order.jwo_no,job_work_order.production_days, job_work_order.remark as jwoRemark";
        $data['leftJoin']['process_master'] =  "process_master.id = vendor_production_trans.process_id";
        $data['leftJoin']['item_master'] =  "item_master.id = vendor_production_trans.product_id";
        $data['leftJoin']['job_card'] =  "job_card.id = vendor_production_trans.job_card_id";
        $data['leftJoin']['party_master'] = "party_master.id = vendor_production_trans.vendor_id";
        $data['leftJoin']['job_work_order'] = "job_work_order.id = vendor_production_trans.job_order_id";
        $data['where']['vendor_production_trans.id'] = $id;
        return $this->row($data);
    }

    public function saveReturnMaterial($data){
        try{
            $this->db->trans_begin();

            $transData = [
                'id' => $data['id'],
                'material_data' => $data['material_data']
            ];
            $result = $this->store($this->jobWorkChallan,$transData,'Return Material');
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getChallanData($id){
        $data['tableName'] = $this->jobWorkChallan;
        $data['where']['job_inward_id != '] = 0;
        $data['where']['version'] = 2;
        $data['customWhere'][] = "FIND_IN_SET('".$id."', job_inward_id)";
        return $this->row($data);
    }

    public function getMovementDTRows($data){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "production_transaction.*,process_master.process_name,item_master.item_name,job_card.job_no,job_card.job_prefix,party_master.party_name";
        $data['leftJoin']['job_card'] = "job_card.id = production_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = production_transaction.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_transaction.process_id";
        $data['leftJoin']['party_master'] = "party_master.id = production_transaction.vendor_id";
        $data['where_in']['production_transaction.entry_type'] ='4';

        
        
        $data['searchCol'][] = "DATE_FORMAT(production_transaction.entry_date,'%d-%m-%Y')";
        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "production_transaction.in_qty";

        $columns = array('', '','production_transaction.entry_date', 'job_card.job_no', 'item_master.item_name','process_master.process_name',  'party_master.party_name', 'production_transaction.in_qty');
        if (isset($data['order'])) {
            
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    /* API Function Start */

    public function getJobWorkVendorList($data){
        $queryData['tableName'] = $this->vendorProductionTrans;
        $queryData['select'] = "vendor_production_trans.*,job_card.job_no,job_card.job_prefix,party_master.party_name,item_master.item_name,item_master.item_code,process_master.process_name,((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) as status_per,(vendor_production_trans.out_qty - vendor_production_trans.in_qty) as pending_qty";

        $queryData['leftJoin']['job_card'] = "vendor_production_trans.job_card_id = job_card.id";
        $queryData['leftJoin']['party_master'] = "vendor_production_trans.vendor_id = party_master.id";
        $queryData['leftJoin']['item_master'] = "vendor_production_trans.product_id = item_master.id";
        $queryData['leftJoin']['process_master'] = "vendor_production_trans.process_id = process_master.id";

        if($data['status'] == 0):
            $queryData['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) < '] = 100;
        endif;
        if($data['status'] == 1):
            $queryData['where']['((vendor_production_trans.in_qty * 100) / vendor_production_trans.out_qty) >= '] = 100;
            $queryData['where']['job_card.job_date >= '] = $this->startYearDate;
            $queryData['where']['job_card.job_date <= '] = $this->endYearDate;
        endif;
        
        $queryData['order_by']['vendor_production_trans.id'] = "DESC";

        if(!empty($data['search'])):
            $queryData['like']['CONCAT(job_card.job_prefix,job_card.job_no)'] = $data['search'];
            $queryData['like']['party_master.party_name'] = $data['search'];
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['process_master.process_name'] = $data['search'];
        endif;

        $queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        
        return $this->rows($queryData);
    }

    /* API Function End */
}
?>