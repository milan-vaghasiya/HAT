<?php
class LineInspectionModel extends MasterModel{
    private $productionTrans = "job_transaction";
    private $productionApproval = "job_approval";
    private $jobCard = "job_card";
    private $jobUsedMaterial = "job_used_material";
    private $itemMaster = "item_master";
    private $employee = "employee_master";
    
    //Updated By NYN @13/12/2021
	public function getDTRows($data){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "job_transaction.*,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,process_master.process_name,party_master.party_name";
        $data['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id"; 
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$data['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $data['where']['job_transaction.entry_type'] = 2;
        $data['where']['job_transaction.vendor_id'] = 0;
        $data['where_in']['job_card.order_status'] = [1,2,4];
        $data['order_by']['job_transaction.id'] = "DESC";

        if(!empty($data['job_card_id']))
            $data['where']['job_transaction.job_card_id'] = $data['job_card_id'];
        if(!empty($data['process_id']))
            $data['where']['job_transaction.process_id'] = $data['process_id'];
            
        if($data['status'] == 0) 
            $data['customWhere'][] = 'job_transaction.inspected_qty <= 0';
        if($data['status'] == 1)
            $data['customWhere'][] = 'job_transaction.inspected_qty >= job_transaction.in_qty';


        $data['searchCol'][] = "CONCAT(job_card.job_prefix,job_card.job_no)";
        $data['searchCol'][] = "process_master.process_name";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "job_transaction.in_qty";
        $data['searchCol'][] = "job_transaction.out_qty";
        $data['searchCol'][] = "job_transaction.rejection_qty";
        $data['searchCol'][] = "job_transaction.rework_qty";      
        
        
		$columns =array('','','CONCAT(job_card.job_prefix,job_card.job_no)','process_master.process_name','item_master.item_code','party_master.party_name','job_transaction.in_qty','job_transaction.out_qty','job_transaction.rejection_qty','job_transaction.rework_qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    //Updated By NYN @13/12/2021
    public function getJobList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*, item_master.item_code";
        $data['leftJoin']['item_master'] = "job_card.product_id = item_master.id"; 
        $data['where_in']['order_status'] = [1,2,4];
        return $this->rows($data); 
    }

    public function getProductionTransRow($id){
        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getInspectionData($data){
        $queryData['tableName'] = $this->productionTrans;
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['entry_type'] = 3;
        $queryData['where']['process_id'] = $data['process_id'];
        $result = $this->rows($queryData);
		
        $dataRow = array();$html = "";
        if(!empty($result)): 
            $i=1;   
            foreach($result as $row):
                $transDate = date("d-m-Y",strtotime($row->entry_date));
                $operatorName = "";$machineNo = "";$shiftName = "";
                if(!empty($row->operator_id)):
                    $queryData=array();
                    $queryData['where']['id'] = $row->operator_id;
                    $queryData['tableName'] = $this->employee;
                    $operatorName = $this->row($queryData)->emp_name;
                endif;
				if(!empty($row->machine_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->machine_id;
                    $mqData['tableName'] = $this->itemMaster;
                    $machineNo = $this->row($mqData)->item_code;
                endif;
				if(!empty($row->shift_id)):
                    $mqData=array();
                    $mqData['where']['id'] = $row->shift_id;
                    $mqData['tableName'] = 'shift_master';
                    $shiftName = $this->row($mqData)->shift_name;
                endif;
                $deleteBtn = '';
				$deleteBtn = '<button type="button" onclick="trashInspection('.$row->id.','.$row->in_qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $html .= '<tr class="text-center">
                            <td>'.$i++.'</td>
                            <td>'.$transDate.'</td>
                            <td>'.$row->out_qty.'</td>                            
                            <td>'.$row->ud_qty.'</td>
                            <td>'.$row->rejection_qty.'</td>
                            <td>'.$row->rework_qty.'</td>
                            <td>'.$row->production_time.'</td>
                            <td>'.$shiftName.'</td>
                            <td>'.$operatorName.'</td>
                            <td>'.$machineNo.'</td>
                            <td class="text-center" style="width:10%;">'.$deleteBtn.'</td>
                        </tr>';
                $dataRow[] = $row;
            endforeach;
        endif;
        return ['htmlData'=>$html,'inspectionTrans'=>$dataRow];
    }

    public function save($data){
        try{
            $this->db->trans_begin();
      
            $jobCardData = $this->jobcard->getJobcard($data['job_card_id']);
            $transData = $this->getProductionTransRow($data['ref_id']);
            $data['out_qty'] = (!empty($data['out_qty']))?$data['out_qty']:0;
            $data['rejection_qty'] = (!empty($data['rejection_qty']))?$data['rejection_qty']:0;
            $data['rework_qty'] = (!empty($data['rework_qty']))?$data['rework_qty']:0;
            $totalOutQty = $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'];

            if($transData->inspected_qty == $transData->in_qty):
                return ['status'=>0,'message'=>['out_qty'=>'Qty not avalible for outward.']];
            else:
                $inwardData = $this->getProductionTransRow($transData->ref_id);

                $queryData=array();
                $queryData['tableName'] = $this->productionApproval;
                $queryData['where']['id'] = $transData->job_approval_id;
                $approvalData = $this->row($queryData);
                $processes = explode(",",$jobCardData->process);

                if($processes[count($processes) - 1] != $data['process_id']):
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $jobCardUpdateData['total_reject_qty'] = $jobCardData->total_reject_qty + $data['rejection_qty'];
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);
                else:
                    $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty + $data['out_qty'];
                    $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty + $data['ud_qty'];
                    $jobCardUpdateData['total_reject_qty'] = $jobCardData->total_reject_qty + $data['rejection_qty'];
                    $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardUpdateData['total_reject_qty'] + $jobCardData->total_rework_qty;
                    if($jobCardData->qty <= $completeJobQty):
                        $jobCardUpdateData['order_status'] = 4;
                    endif;
                    $this->edit($this->jobCard,['id'=>$data['job_card_id']],$jobCardUpdateData);

                    if(empty($jobCardData->pre_disp_inspection)):
                        $setData = array();
                        $setData['tableName'] = $this->jobCard;
                        $setData['where']['id'] = $data['job_card_id'];
                        $setData['set']['unstored_qty'] = 'unstored_qty, + '.$data['out_qty'];
                        $this->setValue($setData);
                    else:
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $data['product_id'];
                        $setData['set']['pending_inspection_qty'] = 'pending_inspection_qty, + '.$data['out_qty'];
                        $this->setValue($setData);
                    endif;                                
                endif;

                $productionTime = (!empty($transData->production_time))?secondsToTime((timeToSeconds($transData->production_time) / $totalOutQty)):"00:00";

                $juq['select'] = 'wp_qty';
                $juq['tableName'] = $this->jobUsedMaterial;
                $juq['where']['id'] = $transData->material_used_id;
                $wpQty = $this->row($juq)->wp_qty;
                $imq = round(($data['out_qty'] * $wpQty),3);

                $data['id'] = "";
                $data['entry_type'] = 3;
                $data['issue_material_qty'] = $transData->issue_material_qty;
                $data['material_used_id'] = $transData->material_used_id;
                $data['issue_batch_no'] = $transData->issue_batch_no;                
                $data['job_approval_id'] = $transData->job_approval_id;
                $data['job_order_id'] = $transData->job_order_id;
                $data['vendor_id'] = $transData->vendor_id;
                $data['challan_prefix'] = $transData->challan_prefix;
                $data['challan_no'] = $transData->challan_no;
                $data['charge_no'] = $transData->charge_no;                
                $data['challan_status'] = $transData->challan_status;
                $data['operator_id'] = $transData->operator_id;
                $data['machine_id'] = $transData->machine_id;
                $data['shift_id'] = $transData->shift_id;
                $data['production_time'] = $productionTime;
                $data['job_process_ids'] = $transData->job_process_ids;
                $data['material_issue_status'] = $transData->material_issue_status;
                $data['setup_status'] = $transData->setup_status;
                $data['inspection_by'] = $data['created_by'];
                $data['in_qty'] = $data['out_qty'] + $data['rejection_qty'] + $data['rework_qty'];

                if(!empty($data['rework_process_id'])):
                    $rewProcessIds = explode(",",$data['rework_process_id']);
                    if(!in_array($data['process_id'],$rewProcessIds)):
                        $data['rework_process_id'] = $data['rework_process_id'].",".$data['process_id'];
                    endif;
                endif;
                
                $transSave = $this->store($this->productionTrans,$data);

                /* $trasnProductionTime = secondsToTime(timeToSeconds($transData->production_time) - timeToSeconds($productionTime)); */
                $inspection_by = (!empty($transData->inspection_by))?(!in_array($data['created_by'],explode(",",$transData->inspection_by))?",".$data['created_by']:$transData->inspection_by):$data['created_by'];

                $this->edit($this->productionTrans,['id'=>$data['ref_id']],["inspection_by"=>$inspection_by,'inspected_qty'=>($transData->inspected_qty + $totalOutQty)]);
                
                /* 
                $this->edit($this->productionTrans,['id'=>$data['ref_id']],['out_qty'=>($transData->out_qty - $data['out_qty']),'rework_qty'=>($transData->rework_qty - $data['rework_qty']),'rejection_qty'=>($transData->rejection_qty - $data['rejection_qty']),'production_time'=>$trasnProductionTime,"inspection_by"=>$inspection_by]);
                */

                

                $setData = array();
                $setData['tableName'] = $this->productionApproval;
                $setData['where']['in_process_id'] = $data['process_id'];
                $setData['where']['job_card_id'] = $transData->job_card_id;
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
                /* $setData['where']['trans_type'] = $approvalData->trans_type;
                $setData['where']['ref_id'] = $approvalData->ref_id; */
                $setData['set']['in_qty'] = 'in_qty, + '.$data['out_qty'];
                $setData['set']['total_rejection_qty'] = 'total_rejection_qty, + '.$data['rejection_qty'];
                $setData['set']['total_rework_qty'] = 'total_rework_qty, + '.$data['rework_qty'];
                $this->setValue($setData);

                if(!empty($data['rework_process_id'])):
                    $processIds = explode(",",$data['rework_process_id']);
                    $counter = count($processIds);
                    for($i=0;$i<$counter;$i++):
                        $approvalData = [
                            'id' => "",
                            'entry_date' => date("Y-m-d"),
                            'trans_type' => 1,
                            'ref_id' => $transSave['insert_id'],
                            'job_card_id' => $jobCardData->id,
                            'product_id' => $jobCardData->product_id,
                            'in_process_id' => ($i == 0)?$data['process_id']:$processIds[($i - 1)],
                            'inward_qty' => ($i == 0)?$data['rework_qty']:0,
                            'in_qty' => ($i == 0)?$data['rework_qty']:0,
                            'in_w_pcs' => ($i == 0)?$jobCardData->w_pcs:0,
                            'in_total_weight' => ($i == 0)?$jobCardData->total_weight:0,
                            'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                            'created_by' => $jobCardData->created_by
                        ];
                        $this->store($this->productionApproval,$approvalData);
                    endfor;     
                endif; 

                $result = ['status'=>1,'message'=>'Outward saved successfully.','htmlData'=>$this->getInspectionData(['ref_id'=>$data['ref_id'],'process_id'=>$data['process_id']])['htmlData']];
            endif;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
           return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
        
    }

	public function delete($id){
        try{
            $this->db->trans_begin();
            $currentTransData = $this->getProductionTransRow($id);       

            $transQty = $currentTransData->out_qty;

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['id'] = $currentTransData->job_approval_id;
            $approvalData = $this->row($queryData);

            $refTransData = $this->getProductionTransRow($currentTransData->ref_id);
            $inwardData = $this->getProductionTransRow($refTransData->ref_id);  

            $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['in_process_id'] = $currentTransData->process_id;
            $queryData['where']['job_card_id'] = $currentTransData->job_card_id;
            if(!empty($inwardData->rework_process_id)):
                $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                if($lastReworkProcess[count($lastReworkProcess) - 1] != $currentTransData->process_id):
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
            $nextApprovalData = $this->row($queryData);

            $pendingQty = $nextApprovalData->in_qty - ($nextApprovalData->total_rework_qty + $nextApprovalData->total_rejection_qty + $nextApprovalData->out_qty);

            if($transQty > $pendingQty):
                return ['status'=>0,'message'=>"You can't delete this outward because This outward forwared to next process."];
            endif;

            /* $queryData = array();
            $queryData['tableName'] = $this->productionApproval;
            $queryData['where']['in_process_id'] = $currentTransData->process_id;
            $queryData['where']['job_card_id'] = $currentTransData->job_card_id;
            $queryData['where']['trans_type'] = 1;
            $queryData['where']['ref_id'] = $currentTransData->id;
            $reworkApprovalData = $this->row($queryData);

            if(!empty($reworkApprovalData)):
                $pendingQty = $reworkApprovalData->in_qty - ($reworkApprovalData->total_rework_qty + $reworkApprovalData->total_rejection_qty + $reworkApprovalData->out_qty);

                if($currentTransData->rework_qty > $pendingQty):
                    return ['status'=>0,'message'=>"You can't delete this outward because This Rework forwared to next process."];
                endif;
            endif; */

            $jobCardData = $this->jobcard->getJobcard($currentTransData->job_card_id);

            $processes = explode(",",$jobCardData->process);
            //check last process
            if($processes[count($processes) - 1] == $currentTransData->process_id):
                $jobCardUpdateData['total_out_qty'] = $jobCardData->total_out_qty - $currentTransData->out_qty;
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $currentTransData->ud_qty;
                $completeJobQty = $jobCardUpdateData['total_out_qty'] + $jobCardData->total_reject_qty + $jobCardData->total_rework_qty;
                if($jobCardData->qty != $completeJobQty):
                    $jobCardUpdateData['order_status'] = 2;
                endif;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);

                                    
                if(empty($jobCardData->pre_disp_inspection)):
                    $setData = Array();
                    $setData['tableName'] = $this->jobCard;
                    $setData['where']['id'] = $jobCardData->id;
                    $setData['set']['unstored_qty'] = 'unstored_qty, - '.$currentTransData->out_qty;
                    $this->setValue($setData);
                else:
                    $setData = Array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $currentTransData->product_id;
                    $setData['set']['pending_inspection_qty'] = 'pending_inspection_qty, - '.$currentTransData->out_qty;
                    $this->setValue($setData);
                endif;                    
                
            else:
                $jobCardUpdateData['total_ud_qty'] = $jobCardData->total_ud_qty - $currentTransData->ud_qty;
                $this->edit($this->jobCard,['id'=>$jobCardData->id],$jobCardUpdateData);
            endif;

            /* $trasnProductionTime = secondsToTime(timeToSeconds($refTransData->production_time) + timeToSeconds($currentTransData->production_time)); */                      
            
            $totalOutQty = $currentTransData->out_qty + $currentTransData->rework_qty + $currentTransData->rejection_qty;
            $this->edit($this->productionTrans,['id'=>$currentTransData->ref_id],['inspected_qty'=>($refTransData->inspected_qty - $totalOutQty)]);
        
            $setData = array();
            $setData['tableName'] = $this->productionApproval;
            $setData['where']['in_process_id'] = $currentTransData->process_id;
            $setData['where']['job_card_id'] = $currentTransData->job_card_id;
            if(!empty($inwardData->rework_process_id)):
                $lastReworkProcess = explode(",",$inwardData->rework_process_id);
                if($lastReworkProcess[count($lastReworkProcess) - 1] != $currentTransData->process_id):
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
            //$setData['where']['trans_type'] = 0;
            $setData['set']['in_qty'] = 'in_qty, - '.$currentTransData->out_qty;
            $setData['set']['total_rejection_qty'] = 'total_rejection_qty, - '.$currentTransData->rejection_qty;
            $setData['set']['total_rework_qty'] = 'total_rework_qty, - '.$currentTransData->rework_qty;
            $this->setValue($setData);

            if(!empty($inwardData->rework_process_id)):
                $this->trash($this->productionApproval,['ref_id'=>$id]);
            endif;

            $result = $this->trash($this->productionTrans,['id'=>$id],'Outward');
            $result['productionTrans'] = $this->getProductionTransRow($currentTransData->ref_id); 
            $result['htmlData'] = $this->getInspectionData(['ref_id'=>$currentTransData->ref_id,'process_id'=>$currentTransData->process_id])['htmlData'];
            //return $result;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	/*  Create By : Avruti @29-11-2021 12:00 AM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->productionTrans;
        $data['where']['job_transaction.entry_type'] = 2;

        return $this->numRows($data);
    }

    public function getLineInspectionList_api($limit, $start){
        $data['tableName'] = $this->productionTrans;
        $data['select'] = "job_transaction.*,job_card.job_no,job_card.job_prefix,job_card.delivery_date,item_master.item_name as product_name,item_master.item_code as product_code,process_master.process_name,party_master.party_name";
        $data['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id"; 
        $data['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$data['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
        $data['where']['job_transaction.entry_type'] = 2;
        $data['where_in']['job_card.order_status'] = [1,2,4];

        if(!empty($data['job_card_id']))
            $data['where']['job_transaction.job_card_id'] = $data['job_card_id'];
        if(!empty($data['process_id']))
            $data['where']['job_transaction.process_id'] = $data['process_id'];

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>