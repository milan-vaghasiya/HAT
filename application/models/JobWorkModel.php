<?php
class JobWorkModel extends MasterModel{
    
    private $jobWork = "job_inward";
    private $jobOutward = "job_outward";
    private $jobInward = "job_inward";
    private $jobCard = "job_card";
    private $jobUsedMaterial = "job_used_material";
    private $productionTrans = "job_transaction";
    private $productionApproval = "job_approval";
    private $jobTrans = "job_transaction";
    private $jobworkChallan = "jobwork_challan";
    
	public function getDTRows($data){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,party_master.party_name";
        $data['join']['process_master'] =  "process_master.id = job_transaction.process_id";
        $data['join']['item_master'] =  "item_master.id = job_transaction.product_id";
        $data['join']['job_card'] =  "job_card.id = job_transaction.job_card_id";
        $data['join']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $data['where']['job_transaction.vendor_id !='] = 0;
        $data['where']['job_transaction.entry_type'] = 1;
		if($data['status'] == 0){$data['customWhere'][] = '(job_transaction.out_qty + job_transaction.rejection_qty+job_transaction.rework_qty) < job_transaction.in_qty';}
        if($data['status'] == 1){$data['customWhere'][] = '(job_transaction.out_qty + job_transaction.rejection_qty+job_transaction.rework_qty) = job_transaction.in_qty';}
        $data['order_by']['job_transaction.id'] = "DESC";

        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "job_transaction.in_qty";
        $data['searchCol'][] = "job_transaction.out_qty";
        $data['searchCol'][] = "job_transaction.rejection_qty";
        $data['searchCol'][] = "job_transaction.rework_qty";
        
		$columns =array('','','job_card.job_no','party_master.party_name','item_master.item_code','process_master.process_name','job_transaction.in_qty','job_transaction.out_qty','job_transaction.rejection_qty','job_transaction.rework_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
	public function getJobworkOutData($id){
        $data['tableName'] = $this->jobTrans;
        $data['select'] = "job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix, party_master.party_name,party_master.party_address,party_master.gstin,job_work_order.jwo_prefix,job_work_order.jwo_no,job_work_order.production_days, job_work_order.remark as jwoRemark";
        $data['join']['process_master'] =  "process_master.id = job_transaction.process_id";
        $data['join']['item_master'] =  "item_master.id = job_transaction.product_id";
        $data['join']['job_card'] =  "job_card.id = job_transaction.job_card_id";
        $data['join']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $data['leftJoin']['job_work_order'] = "job_work_order.id = job_transaction.job_order_id";
        $data['where']['job_transaction.vendor_id !='] = 0;
        $data['where']['job_transaction.id'] = $id;
        return $this->row($data);
    }
	
    //Updated AT : 30-11-2021 [Milan Chauhan]
    public function jobWorkReturnSave($data){
        try{
            $this->db->trans_begin();
            $queryData = array(); 
            $queryData['tableName'] = $this->productionTrans;
            $queryData['where']['id'] = $data['job_trans_id'];
            $jobInwardData = $this->row($queryData);

            $jobWorkProcessIds = explode(",",$jobInwardData->job_process_ids);

            if($data['return_process_id'] == $jobWorkProcessIds[0]):
                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['id'] = $data['job_approval_id'];
                $jobApprovalData = $this->row($queryData);

                $returnMaterial = round(($data['qty'] * $jobInwardData->issue_material_qty) / $jobInwardData->in_qty,3);
                $setData = array();
                $setData['tableName'] = $this->productionTrans;
                $setData['where']['id'] = $data['job_trans_id'];
                $setData['set']['in_qty'] = 'in_qty, - '.$data['qty'];
                $setData['set']['issue_material_qty'] = 'issue_material_qty, - '.$returnMaterial;
                if(!empty($data['total_weight']))
                    $setData['set']['in_total_weight'] = 'in_total_weight, - '.$data['total_weight'];
                $this->setValue($setData);            

                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['id'] = $data['job_approval_id'];
                $setData['set']['out_qty'] = 'out_qty, - '.$data['qty'];
                if(!empty($data['total_weight']))
                    $setData['set']['out_total_weight'] = 'out_total_weight, - '.$data['total_weight'];
                $this->setValue($setData);

                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $data['job_approval_id'];
                $setData['where']['job_card_id'] = $jobInwardData->job_card_id;                
                if(!empty($jobInwardData->rework_process_id)):
                    $lastReworkProcess = explode(",",$jobInwardData->rework_process_id);
                    if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                        $setData['where']['trans_type'] = 1;
                        $setData['where']['ref_id'] = $jobApprovalData->ref_id;
                    else:
                        $setData['where']['trans_type'] = 0;
                        $setData['where']['ref_id'] = 0;
                    endif;
                else:
                    $setData['where']['trans_type'] = 0;
                    $setData['where']['ref_id'] = $jobApprovalData->ref_id;
                endif;
                $setData['set']['inward_qty'] = 'inward_qty, - '.$data['qty'];
                $this->setValue($setData);
            else:
                $i=0;$recordId=0;$countProcess=count($jobWorkProcessIds);   
                $lastKey = array_search($data['return_process_id'],$jobWorkProcessIds);
                foreach($jobWorkProcessIds as $key=>$value):
                    if(($lastKey -1) >= $key):
                        $data['ref_id'] = ($i == 0)?$data['job_trans_id']:$recordId;
                        $data['process_id'] = $value;
                        $recordId = $this->jobRecordInserts($data,((($lastKey -1) == $key)?true:false));
                        $i++;
                    endif;
                endforeach;
                unset($data['ref_id'],$data['process_id']);
            endif;

            $jqry = array();
            $jqry['select'] = 'process';
            $jqry['where']['id'] = $jobInwardData->job_card_id;
            $jqry['tableName'] = $this->jobCard;
            $jobData = $this->row($jqry); 
            $jobProcesses = explode(",",$jobData->process);

            if($data['return_process_id'] == $jobProcesses[0]):
                $setData = Array();
                $setData['tableName'] = $this->jobUsedMaterial;
                $setData['where']['id'] = $jobInwardData->material_used_id;
                $setData['set']['used_qty'] = 'used_qty, - '.$returnMaterial;
                $this->setValue($setData);
            endif;

            $result = $this->store("job_work_return",$data,"Record");
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    //Updated AT : 30-11-2021 [Milan Chauhan]
    public function jobRecordInserts($data,$is_last){
        try{
            $this->db->trans_begin();
            $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);

            $queryData['tableName'] = $this->productionTrans;
            $queryData['where']['id'] = $data['ref_id'];
            $inwardData = $this->row($queryData);       

            $queryData=array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $inwardData->job_approval_id;
            $approvalData = $this->row($queryData);
            
            $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['qty'];
            $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
            if($jobCardData->qty <= $completeJobQty):
                $jobCardUpdateData['order_status'] = 4;
            endif;
            $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                    
            if(empty($jobCardData->pre_disp_inspection)):
                $setData = Array();
                $setData['tableName'] = $this->jobCard;
                $setData['where']['id'] = $data['job_card_id'];
                $setData['set']['unstored_qty'] = 'unstored_qty, + '.$data['qty'];
                $this->setValue($setData);
            else:
                $itemData = $this->item->getItem($data['product_id']);
                $stockQty['pending_inspection_qty'] = $itemData->pending_inspection_qty + $data['qty'];
                $this->edit($this->itemMaster,['id'=>$data['product_id']],$stockQty);
            endif;  
            
            
            $juq['select'] = 'wp_qty';
            $juq['tableName'] = $this->jobUsedMaterial;
            $juq['where']['id'] = $inwardData->material_used_id;
            $wpQty = $this->row($juq)->wp_qty;
            $imq = round((($data['qty']) * $wpQty),3);
            
            $wpcs = 0;if($data['qty'] > 0){$wpcs = floatVal($data['total_weight']) / floatVal($data['qty']);}            
            
            $outwardPostData = [
                'id' => '',
                'entry_type' => '2',
                'ref_id' => $data['ref_id'],
                'entry_date' => $data['entry_date'],
                'job_card_id' => $data['job_card_id'],
                'job_approval_id' => $inwardData->job_approval_id,
                'job_order_id' => $inwardData->job_order_id,
                'vendor_id' => $inwardData->vendor_id,
                'process_id' => $inwardData->process_id,
                'product_id' => $inwardData->product_id,
                'in_qty' => $data['qty'],
                'in_w_pcs' => "",
                'in_total_weight' => "",
                'rework_qty' => 0,
                'rejection_qty' => 0,
                'out_qty' => $data['qty'],
                'ud_qty' => 0,
                'w_pcs' => $wpcs,
                'total_weight' => $data['total_weight'],
                'rejection_reason' => "",
                'rejection_stage' => "",
                'remark' => $data['remark'],
                'challan_prefix' => "",
                'challan_no' => "",
                'in_challan_no' => "",
                'charge_no' => "",
                'material_used_id' => $inwardData->material_used_id,
                'issue_batch_no' => $inwardData->issue_batch_no,
                'issue_material_qty' => $imq,
                'challan_status' => $inwardData->challan_status,
                'operator_id' => "",
                'machine_id' => "",
                'shift_id' => "",
                "production_time" => "",
                "cycle_time" => "",
                "job_process_ids" => $inwardData->job_process_ids,
                "rework_process_id" => "",
                "created_by" => $data['created_by'],
                'inspection_by' => $data['created_by'],
                'inspected_qty' => $data['qty']
            ];
            $otData = $this->store($this->productionTrans,$outwardPostData);

            //Line Inspection Save
            $outwardPostData['entry_type'] = 3;
            $outwardPostData['ref_id'] = $otData['insert_id'];
            $this->store($this->productionTrans,$outwardPostData);

            $this->edit($this->productionTrans,['id'=>$data['ref_id']],['out_qty'=>($inwardData->out_qty + $data['qty'])]);

            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['in_process_id'] = $data['process_id'];
            $setData['where']['job_card_id'] = $inwardData->job_card_id;
            if(!empty($inwardData->rework_process_id)):
                $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                    $setData['where']['trans_type'] = 1;
                    $setData['where']['ref_id'] = $approvalData->ref_id;
                else:
                    $setData['where']['trans_type'] = 0;
                    $setData['where']['ref_id'] = 0;
                endif;
            else:
                $setData['where']['trans_type'] = 0;
                $setData['where']['ref_id'] = $approvalData->ref_id;
            endif;
            $setData['set']['in_qty'] = 'in_qty, + '.$data['qty'];
            if($is_last != true):
                $setData['set']['out_qty'] = 'out_qty, + '.$data['qty'];
            endif;
            $this->setValue($setData);

            $saveInward['insert_id'] = 0;
            if($is_last != true):   
                $queryData = array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['select'] = "id,out_process_id";
                $queryData['where']['in_process_id'] = $data['process_id'];
                $queryData['where']['job_card_id'] = $inwardData->job_card_id;
                if(!empty($inwardData->rework_process_id)):
                    $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                    if($lastReworkProcess[count($lastReworkProcess) - 1] != $data['process_id']):
                        $queryData['where']['trans_type'] = 1;
                        $queryData['where']['ref_id'] = $approvalData->ref_id;
                    else:
                        $queryData['where']['trans_type'] = 0;
                        $queryData['where']['ref_id'] = 0;
                    endif;
                else:
                    $queryData['where']['trans_type'] = 0;
                    $queryData['where']['ref_id'] = $approvalData->ref_id;
                endif;
                $nextApproval = $this->row($queryData);   

                if(!empty($nextApproval->out_process_id)):
                    $data['challan_prefix'] = '';$data['ch_no'] = 0;$jobWorkProcess="";
                    if(!empty($inwardData->vendor_id)):
                        $data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
                        $data['ch_no'] = $this->processApprove->nextJobWorkChNo();
                    endif;
                    $outwardData = $this->processApprove->getOutward($nextApproval->id);
                    $reworkProcess = "";
                    if($outwardData->trans_type == 1):
                        $queryData = array();
                        $queryData['tableName'] = $this->productionTrans;
                        $queryData['where']['id'] = $outwardData->ref_id;
                        $refInwardData = $this->row($queryData);
                        $reworkProcess = $refInwardData->rework_process_id;
                    endif;
                    $materialUsedId = $inwardData->material_used_id;
                    $inwardPostData = [
                        'id' => "",
                        'entry_type' => 1,
                        'entry_date' => $data['entry_date'],
                        'job_card_id' => $data['job_card_id'],
                        'job_approval_id' => $nextApproval->id,
                        'job_order_id' => $inwardData->job_order_id,
                        'vendor_id' => $inwardData->vendor_id,
                        'process_id' => $nextApproval->out_process_id,            
                        'product_id' => $inwardData->product_id,            
                        'in_qty' => $data['qty'],
                        'in_w_pcs' => $data['total_weight'] / $data['qty'],
                        'in_total_weight' => $data['total_weight'],
                        'remark' => $data['remark'],
                        'challan_prefix' => $data['challan_prefix'],
                        'challan_no' => $data['ch_no'],
                        'material_used_id' => $inwardData->material_used_id,
                        'issue_batch_no' => $inwardData->issue_batch_no,
                        'issue_material_qty' => $imq,
                        'job_process_ids' => $inwardData->job_process_ids,  
                        'rework_process_id' => $reworkProcess,
                        'created_by' => $data['created_by'],
                        'accepted_by' => $data['created_by'],
                        'accepted_at' => $data['entry_date']." ".date("H:i:s")
                    ];
                    $saveInward = $this->store($this->productionTrans,$inwardPostData);

                    //update next process inward qty
                    $setData = Array();
                    $setData['tableName'] = $this->productionApproval;
                    $setData['where']['in_process_id'] = $nextApproval->out_process_id;
                    $setData['where']['job_card_id'] = $data['job_card_id'];
                    $setData['where']['trans_type'] = $outwardData->trans_type;
                    $setData['where']['ref_id'] = $outwardData->ref_id;
                    $setData['set']['inward_qty'] = 'inward_qty, + '.$data['qty'];
                    $this->setValue($setData);
                    
                    // If First Process then Maintain Batchwise Rowmaterial 
                    $jqry['select'] = 'process';
                    $jqry['where']['id'] = $data['job_card_id'];
                    $jqry['tableName'] = $this->jobCard;
                    $jobData = $this->row($jqry); 
                    $jobProcesses = explode(",",$jobData->process);
                    
                    if($nextApproval->out_process_id == $jobProcesses[0]):
                        // Update Used Stock in Job Material Used 
                        $setData = Array();
                        $setData['tableName'] = $this->jobUsedMaterial;
                        $setData['where']['id'] = $materialUsedId;
                        $setData['set']['used_qty'] = 'used_qty, + '.$data['total_weight'];
                        $qryresult = $this->setValue($setData);
                    endif;
                endif;
            endif;

            $result = $saveInward['insert_id'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    /* Vendor Challan */
    public function getVendorInward($id){
        $queryData['tableName'] = $this->jobTrans;
        $queryData['select'] = "job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,party_master.party_name";
        $queryData['join']['process_master'] =  "process_master.id = job_transaction.process_id";
        $queryData['join']['item_master'] =  "item_master.id = job_transaction.product_id";
        $queryData['join']['job_card'] =  "job_card.id = job_transaction.job_card_id";
        $queryData['join']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $queryData['where']['job_transaction.challan_status'] = 0;
        $queryData['where']['job_transaction.entry_type'] = 1;
        $queryData['where']['job_transaction.vendor_id'] = $id;
        $resultData = $this->rows($queryData);

        $html=""; $pending_qty=0;
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $pending_qty = $row->in_qty - ($row->out_qty + $row->rejection_qty + $row->rework_qty);
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
                            <td class="text-center">'.$row->item_code.'</td>
                            <td class="text-center">'.$row->process_name.'</td>
                            <td class="text-center">'.floatVal($row->in_qty).'</td>
                            <td class="text-center">'.floatVal($row->out_qty).'</td>
                            <td class="text-center">'.floatVal($pending_qty).'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="7">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getChallanDTRows($data){
        $data['tableName'] = $this->jobworkChallan;
        $data['select'] = "jobwork_challan.*,party_master.party_name";
        $data['leftJoin']['party_master'] = "party_master.id = jobwork_challan.vendor_id";
        $data['where']['jobwork_challan.version'] = 1;
        $data['order_by']['jobwork_challan.id'] = "DESC";

        $data['searchCol'][] = "formatDate(jobwork_challan.challan_date)";
        $data['searchCol'][] = "jobwork_challan.challan_no";
        $data['searchCol'][] = "party_master.party_name";
        
		$columns =array('','','jobwork_challan.challan_date','jobwork_challan.challan_no','party_master.party_name','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getVendorChallan($id){
        $queryData['tableName'] = $this->jobworkChallan;
        $queryData['select'] = "jobwork_challan.*,party_master.party_name,party_master.party_address,party_master.gstin";
        $queryData['join']['party_master'] = "party_master.id = jobwork_challan.vendor_id";
        $queryData['where']['jobwork_challan.id'] = $id;
        return $this->row($queryData);
    }

    public function nextChallanNo(){
        $data['select'] = "MAX(challan_no) as challanNo";
        $data['tableName'] = "jobwork_challan";
		$challanNo = $this->specificRow($data)->challanNo;
		$nextChallanNo = (!empty($challanNo))?($challanNo + 1):1;
		return $nextChallanNo;
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
                'version' => $data['version'],
                'created_by' => $data['created_by']
            ];
            $invardData = explode(',', $data['job_inward_id']);
            foreach($invardData as $key=>$value):
                $updateInward = $this->store($this->jobTrans,['id'=>$value,'challan_status'=>1]);  
            endforeach;
            $result = $this->store($this->jobworkChallan,$transData,'Vendor Challan');
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
            $result = $this->trash($this->jobworkChallan,['id'=>$id],'Vendor Challan');
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
    
    /* Vendor Challan */
	public function getCreateVendorChallan($id){
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,job_card.job_no,job_card.job_prefix,party_master.party_name";
		$queryData['join']['process_master'] =  "process_master.id = job_transaction.process_id";
		$queryData['join']['item_master'] =  "item_master.id = job_transaction.product_id";
		$queryData['join']['job_card'] =  "job_card.id = job_transaction.job_card_id";
		$queryData['join']['party_master'] = "party_master.id = job_transaction.vendor_id";
		$queryData['where']['job_transaction.challan_status'] = 0;
		$queryData['where']['job_transaction.entry_type'] = 1;
		$queryData['where']['job_transaction.vendor_id'] = $id;
		$resultData = $this->rows($queryData);
	
		$html=""; $pending_qty=0;
		if(!empty($resultData)):
			$i=1;
			foreach($resultData as $row):
				$pending_qty = $row->in_qty - ($row->out_qty + $row->rejection_qty + $row->rework_qty);
				$html .= '<option value="' . $row->id . '" >['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).' (In Qty.: '.floatVal($row->in_qty).', Out Qty.: '.floatVal($row->out_qty).')</option>';
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
	
	public function saveReturnMaterial($data){
        try{
            $this->db->trans_begin();
        $transData = [
            'id' => $data['id'],
            'material_data' => $data['material_data']
        ];
        $result = $this->store($this->jobworkChallan,$transData,'Return Material');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }	
    }
	
	/*  Create By : Avruti @27-11-2021 3:25 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount($status){
        $data['tableName'] = $this->jobworkChallan;
        return $this->numRows($data);
    }

    public function getJobWorkList_api($limit, $start,$status){
        $data['tableName'] = $this->jobworkChallan;
        $data['select'] = "jobwork_challan.*,party_master.party_name";
        $data['join']['party_master'] = "party_master.id = jobwork_challan.vendor_id";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>