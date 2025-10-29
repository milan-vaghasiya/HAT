<?php
class JobcardModel extends MasterModel
{
    private $jobCard = "job_card";
    private $jobTrans = "job_transaction";
    private $jobApproval = "job_approval";
    private $productKit = "item_kit";
    private $jobBom = "job_bom";
    private $transMain = "trans_main";
    private $productProcess = "product_process";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $stockTrans = "stock_transaction";
    private $jobcardLog = "jobcard_log";
    private $requisitionLog = 'requisition_log';
    private $job_heat_trans = 'job_heat_trans';
    private $setting_parameter = 'setting_parameter';
    private $trans_child = 'trans_child';

    public function getNextJobNo($job_type = 0)
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(job_no) as job_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $data['where']['job_category'] = $job_type;
        $maxNo = $this->specificRow($data)->job_no;
        $nextJobNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextJobNo;
    }

    public function getNextBatchNo()
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "MAX(batch_no) as batch_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $maxNo = $this->specificRow($data)->batch_no;
        $nextBatchNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextBatchNo;
    }

    public function getDTRows($data, $type = 0)
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*,item_master.item_name,item_master.full_name,item_master.item_code,party_master.party_name,party_master.party_code,item_category.category_name,IFNULL(jobBom.issue_qty,0) as issue_qty,IFNULL(jobBom.received_qty,0) as received_qty,jobBom.used_as,jobBom.approved_by";

        $data['leftJoin']['(select job_bom.issue_qty,job_bom.received_qty,job_bom.job_card_id,job_bom.used_as,job_bom.approved_by FROM job_bom LEFT JOIN item_master ON item_master.id = job_bom.ref_item_id WHERE job_bom.is_delete = 0 AND item_master.item_type = 3) as jobBom'] = "jobBom.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "job_card.party_id = party_master.id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        // $data['where']['job_card.job_category'] = $type;

        if (isset($data['status']) && $data['status'] == 0) {
            $data['where_in']['job_card.order_status'] = [0, 1, 2];
            $data['where']['job_card.is_npd'] = 0;
        }
        if (isset($data['status']) && $data['status'] == 1) {
            $data['where_in']['job_card.order_status'] = [4];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        if (isset($data['status']) && $data['status'] == 2) {
            $data['where_in']['job_card.order_status'] = [5, 6];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        if (isset($data['status']) && $data['status'] == 3) {
            $data['where_in']['job_card.order_status'] = [3];
        }
        if (isset($data['status']) && $data['status'] == 4) {
            $data['where_in']['job_card.order_status'] = [0, 1, 2];
            $data['where']['job_card.is_npd'] = 1;
        }

        $data['order_by']["LENGTH(job_card.job_number)"] = "DESC";

        $data['searchCol'][] = "job_card.job_number";
        $data['searchCol'][] = "DATE_FORMAT(job_card.job_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_code";
        $data['searchCol'][] = "item_master.item_code";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "job_card.qty";
        $data['searchCol'][] = "job_card.total_out_qty";
        $data['searchCol'][] = "job_card.total_rej_qty";
        $data['searchCol'][] = "job_card.remark";

        $columns = array('', '', 'job_card.job_number', 'job_card.job_date', 'job_card.party_code', 'item_master.item_code', 'item_category.category_name', 'job_card.qty',"job_card.total_out_qty","job_card.total_rej_qty", '', 'job_card.remark', '');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        $result = $this->pagingRows($data);
        return $result;
    }
    
    public function getMaterialStatus($job_id){
        $queryData = array();
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "SUM(allocated_qty) as total_allocated_qty, SUM(dispatch_qty) as total_dispatch_qty";
        $queryData['where']['job_card_id'] = $job_id;
        $result = $this->row($queryData);

        $status = ($result->total_allocated_qty <= $result->total_dispatch_qty)?1:0;
        return $status;
    }

    public function getCustomerList()
    {
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.party_id,party_master.party_name,party_master.party_code";
        $data['join']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_main.trans_status'] = 0;
        $data['where']['trans_main.entry_type'] = 4;
        $data['group_by'][] = 'trans_main.party_id';
        return $this->rows($data);
    }

    public function getCustomerSalesOrder($party_id)
    {
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getProductList($data)
    {
        $html = '<option value="" data-delivery_date="'.date("Y-m-d").'" data-trans_id="0" data-order_type="0">Select Product</option>';
        $trans_date = '';
        if (empty($data['sales_order_id'])) :
            $productData = $this->item->getItemList(1);
            if (!empty($productData)) :
                foreach ($productData as $row) :
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->id) ? "selected" : "";
				    $item_name = (!empty($row->item_code)? "[".$row->item_code."] ".$row->item_name:$row->item_name);
                    $html .= '<option value="' . $row->id . '" data-delivery_date="' . date("Y-m-d") . '" data-order_type="0" data-trans_id="0" data-wo_no="" ' . $selected . '>' . $item_name . '</option>';
                endforeach;
            endif;
        else :
            //trans_child
            $queryData['select'] = "trans_child.id,trans_child.item_id,trans_child.qty,trans_child.cod_date,trans_child.grn_data,trans_main.trans_date,trans_main.order_type,item_master.item_code, item_master.item_name,item_master.full_name,trans_child.material_grade";
            $queryData['join']['item_master'] = "trans_child.item_id = item_master.id";
            $queryData['join']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.trans_main_id'] = $data['sales_order_id'];
            $queryData['where']['trans_child.trans_status'] = 0;
            $queryData['where']['trans_child.entry_type'] = 4;
            $queryData['tableName'] = "trans_child";
            $productData = $this->rows($queryData);
            if (!empty($productData)) :
                foreach ($productData as $row) :
                    $selected = (!empty($data['product_id']) && $data['product_id'] == $row->item_id) ? "selected" : "";
                    $jobType = ($row->order_type == 1) ? 0 : 1;
                    $item_name = (!empty($row->item_code))? "[".$row->item_code."] ".$row->item_name : $row->item_name;
                    $html .= '<option value="' . $row->item_id . '" data-delivery_date="' . ((!empty($row->cod_date)) ? $row->cod_date : date("Y-m-d")) . '" data-trans_id="'.$row->id.'" data-order_type="' . $jobType . '" data-wo_no="'.$row->grn_data.'" ' . $selected . '>' . $item_name . ' | ' . $row->material_grade . ' (Ord. Qty.: ' . $row->qty . ') | '.$row->grn_data.'</option>';
                    $trans_date = (!empty($row->trans_date)) ? $row->trans_date : '';
                endforeach;
            endif;
        endif;
        return ['status' => 1, 'htmlData' => $html, 'productData' => $productData, 'trans_date' => $trans_date];
    }

    public function getProductProcess($data, $id = "")
    {
        $jobCardData = array(); $bom_id = ""; $req_qty=0;;
        if (!empty($id)) :
            $jobCardData = $this->jobcard->getJobcard($id);
            $bomData = $this->getJobBomRawMaterialData($id);
            $bom_id = (!empty($bomData->id))?$bomData->ref_item_id:'';
            $req_qty = (!empty($bomData->qty))?($bomData->qty*$jobCardData->qty):0;
        endif;

        $data['select'] = "product_process.process_id,process_master.process_name,product_process.sequence";
        $data['where']['product_process.item_id'] = $data['product_id'];
        $data['join']['process_master'] = "product_process.process_id = process_master.id";
        $data['order_by']['product_process.sequence']  = 'asc';
        $data['tableName'] = $this->productProcess;
        $processData = $this->rows($data);
        $html = "";
        $toolCount = 0;
        if (!empty($processData)) :
            $i = 1;
            foreach ($processData as $row) :
               
                if (!empty($jobCardData)) :
                    $process = explode(",", $jobCardData->process);
                    $checked = (in_array($row->process_id, $process)) ? "checked" : "";
                    $html .= '<input type="checkbox" id="md_checkbox_' . $i . '" name="process[]" class="filled-in chk-col-success" value="' . $row->process_id . '" ' . $checked . ' ><label for="md_checkbox_' . $i . '" class="mr-3">' . $row->process_name . '</label>';
                else :
                    $html .= '<input type="checkbox" id="md_checkbox_' . $i . '" name="process[]" class="filled-in chk-col-success" value="' . $row->process_id . '" checked ><label for="md_checkbox_' . $i . '" class="mr-3">' . $row->process_name . '</label>';
                endif;
                $i++;
            endforeach;
        else :
            $html = '<div class="error">Product Process not found.</div>';
        endif;
        $errorHtml = '';
        $optionStatus = $this->item->checkProductOptionStatus($data['product_id']); //print_r($optionStatus);exit;
        $errorHtml .= (empty($optionStatus->bom)) ? '<div class="error">Product BOM not found.</div>' : '';
        // $errorHtml .= (!empty($optionStatus->cycleTime)) ? '<div class="error">Product Process cycle time not found.</div>' : '';
        // $errorHtml.= (empty($optionStatus->tool) && !empty($toolCount)) ? '<div class="error">Tool not found.</div>' : '';
        $status = 1;
        if (!empty($errorHtml)) {
            $status = 0;
        }

        $bomDataHtml = "";
        $itemKit = $this->item->getProductKitData($data['product_id']);
        if (!empty($itemKit)) {
            $i=1;
            foreach ($itemKit as $row) {
                if($row->item_type == 3){
					$option = '<option value="">Select Batch</option>';
					$stockData = $this->store->getItemStockBatchWise(['item_id'=>$row->ref_item_id,'stock_required'=>1]);
					foreach($stockData as $stock):
						$option .= '<option value="'.$stock->batch_no.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'':'disabled').'>'.$stock->batch_no.' | Stock: '.$stock->qty.'</option>';
					endforeach;
					
                    $bomDataHtml .= '
                    <tr>
                        <td>
                            <input type="checkbox" id="md_ch_'.$i.'" name="bom" class="filled-in batchCheck chk-col-success" value="'.$row->id.'"  data-rowid="'.$i.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'checked':'').'><label for="md_ch_'.$i.'" class="mr-3"></label>
                        </td>
                        <td > 
                           '.$row->full_name.'
                            <div class="error item_error_'.$row->ref_item_id.'"></div>
                            <input type="hidden" name="bom_item_id" value="'.$row->ref_item_id.'" id="bom_item_id'.$i.'" data-rowid="'.$i.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'':'disabled').'>
                        </td>
                        <td > 
							'.(($row->used_as == 1)?"Main":"Alternative").'
							 <div class="error item_error_'.$row->ref_item_id.'"></div>
							 <input type="hidden" name="used_as" value="'.$row->used_as.'" id="used_as'.$i.'" data-rowid="'.$i.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'':'disabled').'>
						 </td>
                        <td>
                           '.$row->qty.'
                            <input type="hidden" name="bom_qty" value="'.$row->qty.'" id="bom_qty'.$i.'" data-rowid="'.$i.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'':'disabled').'>
                        </td>
                        <td>
                            <input type="text" name="req_qty" id="req_qty'.$i.'" value="'.$req_qty.'" data-bom_qty="'.$row->qty.'" readOnly class="reqQty form-control text-bold" style="background: transparent;border: none;font-weight : bold" data-rowid="'.$i.'" '.((!empty($bom_id) && $bom_id == $row->ref_item_id)?'':'disabled').'>
                        </td>
						<td>
                            <select name="batch_no" id="batch_no'.$i.'" class="form-control">
								'.$option.'
							</select>
                        </td>
                    </tr>';
                    $i++;
                }
            }
        } else {
            $errorHtml = '<div class="error">Product BOM. not found.</div>';
        }
        return ['status' => $status, 'htmlData' => $html . $errorHtml, 'processData' => $processData,'BomTable'=>$bomDataHtml];
    }
    
    public function getLotNo($product_id){
        $data['tableName'] = $this->jobCard;
        $data['select'] = "ifnull((MAX(item_lot_no) + 1),1) as item_lot_no";
        $data['where']['job_date >= '] = $this->startYearDate;
        $data['where']['job_date <= '] = $this->endYearDate;
        $data['where']['product_id'] = $product_id;
        return $this->row($data)->item_lot_no;
    }

    public function save($data)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = array();
            if (!empty($data['id'])) :
                $jobCardData = $this->getJobCard($data['id']);
                if (!empty($jobCardData->md_status) || !empty($jobCardData->order_status)) :
                    return ['status' => 2, 'message' => "Production In-Process. You can't update this job card."];
                endif;
                $data['job_number'] = $jobCardData->job_number;
                if(!empty($jobCardData->so_trans_id) && $data['so_trans_id'] != $jobCardData->so_trans_id):
                    $setData = array();
                    $setData['tableName'] = $this->trans_child;
                    $setData['where']['id'] = $jobCardData->so_trans_id;
                    $setData['set']['production_qty'] = 'production_qty, - ' . $jobCardData->qty;
                    $this->setValue($setData);
                endif;
            else :
                $data['job_prefix'] = ($data['job_category'] == 0) ? "JC-" : "JCW-" ;
                $data['job_no'] = $this->getNextJobNo($data['job_category']);
                $data['item_lot_no'] = $this->getLotNo($data['product_id']);
                $data['job_number'] = $data['job_prefix'].sprintf('%04d',$data['job_no']);
               
            endif;

            $data['process'] = implode(',', $data['process']);
            $jobQueryData=[
                'id' => $data['id'],                
                'job_date' => $data['job_date'],
                'party_id' => $data['party_id'],
                'sales_order_id' => $data['sales_order_id'],
                'so_trans_id' => $data['so_trans_id'],
                'product_id' => $data['product_id'],
                'job_category' => $data['job_category'],
                'qty' => $data['qty'],
                'is_npd' => $data['is_npd'],
                'process' => $data['process'],
                'remark' => $data['remark'],
                'md_status'=> 0,
                'created_by'=> $this->loginID,
                'created_at'=> date('Y-m-d H:i:s')
            ];

            if(empty($data['id'])):
                $jobQueryData['job_no'] = $data['job_no'];
                $jobQueryData['job_prefix'] = $data['job_prefix'];
                $jobQueryData['job_number'] = $data['job_number'];
            endif;
            $saveJobCard = $this->store($this->jobCard, $jobQueryData, 'Job Card');
            $jobCardId = (!empty($data['id']))?$data['id']:$saveJobCard['insert_id'];

            if(!empty($data['so_trans_id'])){
                $this->setProductionQtyInSO($data['so_trans_id']);
            }
            // Insert Raw Material in JOB BOM
            $bomQuery = array();
            $bomQuery['tableName'] = $this->jobBom;
            $bomQuery['select'] = 'job_bom.*';
            $bomQuery['leftJoin']['item_master'] = "item_master.id = job_bom.ref_item_id";
            $bomQuery['where']['job_bom.item_id'] = $data['product_id'];
            $bomQuery['where']['job_bom.job_card_id'] = $jobCardId;
            $bomQuery['where']['item_master.item_type'] = 3;
            $bomData = $this->row($bomQuery);
            if(!empty($data['bom_item_id'])){
                $bomId =( !empty($bomData))?$bomData->id:'';
                $rawMtr = [
                    'id'=>$bomId,
                    'job_card_id'=>$jobCardId,
                    'created_by'=>$data['created_by'],
                    'item_id'=>$data['product_id'],
                    'ref_item_id'=>$data['bom_item_id'],
                    'qty'=>$data['bom_qty'],
                    'used_as'=>$data['used_as'],
                    'req_qty'=>($data['qty']*$data['bom_qty']),
                    'batch_no'=>$data['batch_no']
                ];
                if($data['used_as'] == 1){
                    $rawMtr['approved_by'] = $data['created_by'];
                    $rawMtr['approved_at'] = date('Y-m-d');
                }else{
                    $rawMtr['approved_by'] = 0;
                    $rawMtr['approved_at'] = NULL;
                }
                $this->store($this->jobBom,$rawMtr,'Job BOM');
                $rmData = $this->item->getItem($data['bom_item_id']);
                $this->store($this->jobCard,['id'=>$jobCardId,'material_grade'=>$rmData->material_grade]);
            }elseif(!empty($data['id']) && !empty($bomData)){
                $this->trash("job_bom",['id'=>$bomData->id]);
            }


            //set job bom For Consumable Items
            $queryData = array();
            $queryData['tableName'] = $this->productKit;
            $queryData['select'] = 'item_kit.*';
            $queryData['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
            $queryData['where']['item_kit.item_id'] = $data['product_id'];
            $queryData['where']['item_master.item_type !='] = 3;
            $kitData = $this->rows($queryData);
            if(empty($data['id'])){
                if (!empty($kitData)) {
                    foreach($kitData as $kit):
                        $kit->id = "";
                        $kit->job_card_id = $saveJobCard['insert_id'];
                        $kit->req_qty = ($kit->qty*$data['qty']);
                        $kit->created_by = $data['created_by'];
                        $jobBomArray = (Array) $kit;
                       
                        $this->store($this->jobBom,$jobBomArray,'Job BOM');
                    endforeach;
                }
            }
            else{
                if($data['product_id'] != $jobCardData->product_id){
                    $this->trash("job_bom",['job_card_id'=>$data['id']]);
                    if (!empty($kitData)) {
                        foreach($kitData as $kit):
                            $kit->id = "";
                            $kit->job_card_id = $saveJobCard['insert_id'];
                            $kit->created_by = $data['created_by'];
                            $jobBomArray = (Array) $kit;
                            $this->store($this->jobBom,$jobBomArray,'Job BOM');
                        endforeach;
                    }
                }
            }

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $saveJobCard;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function setProductionQtyInSO($so_trans_id){
        try {
            $this->db->trans_begin();
            $data['tableName'] = $this->jobCard;
            $data['select'] = 'SUM(qty) as production_qty';
            $data['where']['job_card.so_trans_id'] = $so_trans_id;
            $jobData = $this->row($data);
            // print_r($this->db->last_query());

           
            $result = $this->store($this->trans_child,['id'=>$so_trans_id,'production_qty'=>((!empty($jobData->production_qty))?$jobData->production_qty:0)]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
   
    public function getJobcard($id)
    {
        $data['tableName'] = $this->jobCard;
        //$data['select'] = 'job_card.*,item_master.item_code as product_code,item_master.item_name as product_name,item_master.item_name as full_name,item_master.part_no,item_master.size as part_size,item_master.drawing_no,item_master.rev_no,item_master.wt_pcs,party_master.party_name,party_master.party_code,unit_master.unit_name,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_date,trans_main.doc_no,trans_child.qty as so_qty,trans_child.cod_date as del_date, trans_child.material_grade, material_master.standard';
		$data['select'] = 'job_card.*,job_card.material_grade as rm_material_grade,item_master.item_code as product_code,item_master.item_name as product_name,item_master.item_name as full_name,item_master.part_no,item_master.size as part_size,item_master.drawing_no,item_master.rev_no,item_master.wt_pcs,party_master.party_name,party_master.party_code,unit_master.unit_name,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_date,trans_main.doc_no,trans_child.qty as so_qty,trans_child.cod_date as del_date,trans_child.target_date,trans_child.material_grade, material_master.standard,employee_master.emp_name as created_name';
		$data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
        $data['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
        $data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $data['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $data['leftJoin']['trans_child'] = "trans_child.id = job_card.so_trans_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = job_card.created_by";
		$data['leftJoin']['material_master'] = "material_master.material_grade = trans_child.material_grade AND material_master.is_delete = 0";
        $data['where']['job_card.id'] = $id;
        return $this->row($data);
    }

    public function getJobcardList($order_status = array())
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        if (!empty($order_status)) {
            $data['where_in']['job_card.order_status'] = $order_status;
        }
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        return $this->rows($data);
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobCard($id);
            if (!empty($jobCardData->md_status) && empty($jobCardData->ref_id)) :
                $result = ['status' => 0, 'message' => "Production In-Process. You can't Delete this job card."];
            endif;

            if (!empty($jobCardData->ref_id) && !empty($jobCardData->order_status)) :
                $result = ['status' => 0, 'message' => "Production In-Process. You can't Delete this job card."];
            endif;

            $this->trash($this->jobMaterialDispatch, ['ref_id' => $id, 'is_delete' => 0]);
            $this->trash($this->jobBom, ['job_card_id' => $id]);

            $this->trash('requisition_log',['log_type'=>1,'reqn_type'=>3,'req_from' =>$id]);
            $this->remove('stock_transaction',['trans_type'=>2,'ref_type'=>3,'ref_id'=>$id]);
            $this->remove('stock_transaction',['trans_type'=>1,'ref_type'=>20,'ref_id'=>$id]);

            

            $result = $this->trash($this->jobCard, ['id' => $id], "Job Card");
            if(!empty($jobCardData->so_trans_id)){
                $this->setProductionQtyInSO($jobCardData->so_trans_id);
            }
            /* Send Notification */
            // $jobNo = ($jobData->job_prefix.sprintf("%04d",$jobData->job_no));
            // $notifyData['notificationTitle'] = "Delete Job Card";
            // $notifyData['notificationMsg'] = "Job Card deleted. JOB No. : " . $jobNo;
            // $notifyData['payload'] = ['callBack' => base_url('production_v2/jobcard')];
            // $notifyData['controller'] = "'production_v2/jobcard'";
            // $notifyData['action'] = "D";
            // $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    public function getLastTrans($id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['select'] = 'MAX(updated_at) as updated_at, id';
        $data['where']['job_card_id'] = $id;
        $data['orderBy']['updated_at'] = "DESC";
        return $this->row($data);
    }

    public function getJobPendingQty($job_card_id)
    {
        $data['tableName'] = $this->jobApproval;
        $data['where']['in_process_id'] = 0;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->row($data);
    }

    public function getJobBomQty($jobCardId, $item_id)
    {
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "qty";
        $queryData['where']['job_card_id'] = $jobCardId;
        $queryData['where']['item_id'] = $item_id;
        $result = $this->row($queryData);
        return $result;
    }
    public function getMaterialIssueData($data)
    {
       
        $resultArray = array();$result = Array();
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "ABS(SUM(stock_transaction.qty) )as issue_qty,stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.item_name as item_full_name, item_master.item_code as rm_item_code, item_master.material_grade, location_master.store_name, location_master.location,party_master.party_name,grn_master.party_id";
        $queryData['leftJoin']['item_master'] =  "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['location_master'] =  "stock_transaction.location_id = location_master.id";
        $queryData['leftJoin']['grn_transaction'] =  "grn_transaction.tc_no = stock_transaction.tc_no";
        $queryData['leftJoin']['grn_master'] =  "grn_transaction.grn_id = grn_master.id";
        $queryData['leftJoin']['party_master'] =  "grn_master.party_id = party_master.id";
        $queryData['where']['stock_transaction.trans_type'] = 2;
        $queryData['where']['stock_transaction.ref_type'] = 3;
        $queryData['where']['stock_transaction.ref_id'] = $data->id;
        $queryData['group_by'][] = 'stock_transaction.batch_no,stock_transaction.tc_no';
        $result = $this->rows($queryData);
        $resultArray = $result;
        $itemName = (!empty($result))?implode(',',array_unique(array_column($result,'item_full_name'))):'';
        $materialGrade = (!empty($result))?implode(',',array_column($result,'material_grade')):'';
        $issue_qty = (!empty($result))?implode(',',(array_column($result,'issue_qty'))):'';
        $heat_no = (!empty($result))?implode(',',array_column($result,'heat_no')):'';
        $batch_no = (!empty($result))?implode(',',array_column($result,'batch_no')):'';
        $supplier_name = (!empty($result))?implode(',',array_column($result,'party_name')):'';
        $total_issue_qty = (!empty($result))?array_sum(array_column($result,'issue_qty')):'';
        $resultData = ['material_name' => $itemName, 'material_grade' => $materialGrade, 'issue_qty' => $issue_qty, 'heat_no' => $heat_no, 'batch_no' => $batch_no, 'supplier_name' => $supplier_name,'total_issue_qty'=>$total_issue_qty];

        return ['status' => 1, 'message' => 'Data Found.', "resultData" => $resultData,'result'=>$resultArray];
    }
    
    public function getRequestItemData($id)
    {
        $jobCardData = $this->getJobcard($id);

        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,unit_master.unit_name";
        $queryData['join']['item_master'] = "job_bom.ref_item_id = item_master.id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where']['job_bom.item_id'] = $jobCardData->product_id;
        $queryData['where']['job_bom.job_card_id'] = $id;
        $kitData = $this->rows($queryData);

        $dataRows = array();
        foreach ($kitData as $row) :
            $row->request_qty = $row->qty * $jobCardData->qty;
            $dataRows[] = $row;
        endforeach;

        return $dataRows;
    }

    
    public function nextRequisitionNo()
    {
        $data['tableName'] = $this->requisitionLog;
        $data['select'] = "MAX(log_no) as req_no";
        $data['where']['requisition_log.log_type'] = 1;
        $maxNo = $this->specificRow($data)->req_no;
        $nextReqNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextReqNo;
    }

    public function saveMaterialRequest($data)
    {
        try {
            $this->db->trans_begin();

            foreach($data['item'] as $row):
                if(!empty($row['req_qty']) && $row['req_qty'] > 0):
                    $reqNo = $this->nextRequisitionNo();
                    $row['req_date'] = date("Y-m-d H:i:s");
                    $row['log_no'] = $reqNo;
                    $row['used_at'] = $data['used_at'];
                    $row['handover_to'] = $data['handover_to'];
                    $row['created_by'] = $data['created_by'];
                    $row['ref_id'] = $row['job_bom_id'];
                    unset($row['job_bom_id'],$row['pending_qty']);

                    $this->store('requisition_log',$row);                    

                    $this->edit($this->jobCard, ['id' => $row['req_from']], ['md_status' => 1]);
                endif;
            endforeach;

            $result = ['status' => 1, 'message' => 'Material Request send successfully.'];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getAllocatedMaterial($id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.id, stock_transaction.trans_ref_id, stock_transaction.location_id, stock_transaction.batch_no, stock_transaction.qty, stock_transaction.item_id, item_master.full_name as item_full_name,item_master.item_name, location_master.store_name, location_master.location,st.stock_qty";
        $queryData['join']['item_master'] =  "stock_transaction.item_id = item_master.id AND item_master.is_delete = 0";
        $queryData['join']['location_master'] =  "stock_transaction.location_id = location_master.id AND location_master.is_delete = 0";
        $queryData['leftJoin']['(SELECT SUM(qty) as stock_qty,ref_id,item_id,location_id,batch_no  FROM stock_transaction WHERE ref_type = 20 AND ref_id='.$id.' AND is_delete = 0 GROUP BY item_id,location_id,batch_no) as st'] = "st.ref_id = stock_transaction.ref_id AND st.item_id = stock_transaction.item_id AND st.location_id = stock_transaction.location_id AND st.batch_no = stock_transaction.batch_no";
        $queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['where']['stock_transaction.ref_type'] = 20;
        $queryData['where']['stock_transaction.ref_id'] = $id;
        $queryData['where']['stock_transaction.location_id !='] = $this->ALLOT_RM_STORE->id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function materialReceived($data)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobcard($data['id']);
           
            $kitData = $this->getJobBomRawMaterialData($data['id']);
            $pendingReceivedQty = $kitData->issue_qty-$kitData->received_qty;
            $inwardQty = round($pendingReceivedQty/$kitData->qty);
            $batchData = $this->getProductionIssuedMtrBatch($data['id'],$kitData->id);

            if($jobCardData->order_status == 0){
                $processIds = explode(",", "0,".$jobCardData->process);
                $counter = count($processIds);
                $preFinishedWeight = 0;
                for ($i = 0; $i < $counter; $i++) :
                    $finishedWeight = 0;
                    if ($i == 0) :
                        $finishedWeight = $preFinishedWeight = (!empty($kitData)) ? $kitData->qty : 0;
                    else :
                        if(isset($processIds[$i])) :
                            $queryData = array();
                            $queryData['tableName'] = $this->productProcess;
                            $queryData['where']['item_id'] = $jobCardData->product_id;
                            $queryData['where']['process_id'] = $processIds[$i];
                            $productProcessData = $this->row($queryData);

                            $finishedWeight = (!empty($productProcessData)) ? (($productProcessData->finished_weight > 0) ? $productProcessData->finished_weight : $preFinishedWeight) : $preFinishedWeight;
                        else :
                            $finishedWeight = $preFinishedWeight;
                        endif;
                    endif;

                    $approvalData = [
                        'id' => "",
                        'entry_date' => date("Y-m-d"),
                        'job_card_id' => $jobCardData->id,
                        'product_id' => $jobCardData->product_id,
                        'in_process_id' =>$processIds[$i],
                        'inward_qty' => ($i == 0) ?  $jobCardData->qty: 0,
                        'in_qty' => ($i == 0) ? $jobCardData->qty: 0,
                        'ok_qty' => ($i == 0) ? $jobCardData->qty : 0,
                        'total_prod_qty' => ($i == 0) ? $jobCardData->qty : 0,
                        'ih_prod_qty' => ($i == 0) ? $jobCardData->qty : 0,
                        'out_process_id' => (isset($processIds[$i+1])) ? $processIds[$i+1] : 0,
                        'pre_finished_weight' => ($i == 0) ? $finishedWeight : $preFinishedWeight,
                        'finished_weight' => $finishedWeight,
                        'created_by' => $this->loginId
                    ];
                    $preFinishedWeight = $finishedWeight;
                    $saveApproval = $this->store($this->jobApproval, $approvalData);

                    if($i == 0):
                        $firstApprovalId = $saveApproval['insert_id'];
                        $transData = [
                            'id' => "",
                            'entry_type' => 0,
                            'entry_date' => date("Y-m-d"),
                            'job_card_id' => $jobCardData->id,
                            'job_approval_id' => $firstApprovalId,
                            'process_id' => $processIds[$i],
                            'product_id' => $jobCardData->product_id,
                            'qty' => $jobCardData->qty,
                            'w_pcs' => $finishedWeight,
                            'total_weight' => ($finishedWeight * $jobCardData->qty),
                            'created_by' => $this->loginId
                        ];
                        $this->store($this->jobTrans,$transData);
                    endif;
                endfor;

                $this->store($this->jobCard, ['id' => $jobCardData->id, 'order_status' => 2]);
            }
            $aprvData = $this->processMovement->getApprovalDataByJob($data['id'],1);
            $i=0;
            foreach($aprvData as $aprv){
                foreach($batchData as $row){
                    $heat = $this->processMovement->getHeatData(['job_approval_id'=>$aprv->id,'tc_no'=>$row->tc_no,'single_row'=>1]);
                    if(!empty($heat)){
                        if(($i == 0)){
                            $heatData = [
                                'id' =>$heat->id,
                                'batch_no' =>$heat->batch_no,
                                'tc_no' =>$row->tc_no,
                                'in_qty' => abs($row->qty),
                            ];
                            $this->store($this->job_heat_trans, $heatData);
                        }
                    }else{
                        $heatData = [
                            'id' =>'',
                            'job_card_id'=>$aprv->job_card_id,
                            'job_approval_id'=>$aprv->id,
                            'batch_no' =>$row->batch_no,
                            'tc_no' =>$row->tc_no,
                            'in_qty' => ($i == 0) ?abs($row->qty) : 0,
                        ];
                        $this->store($this->job_heat_trans, $heatData);
                    }
                }
                $i++;
            }
                
            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['id'] = $kitData->id;
            $setData['set']['received_qty'] = 'received_qty, + ' . $pendingReceivedQty;
            $this->setValue($setData);

            $result = ['status' => 1, 'message' => "Material received successfully."];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function changeJobStatus($data)
    {
        $jobData = $this->getJobcard($data['id']);
        if ($data['order_status'] == 1) :            
            if ($jobData->md_status != 3) :
                return ['status' => 0, 'message' => "Required Material is not issued yet! Please Issue material before start"];
            endif;
        endif;
        $this->store($this->jobCard, $data);

        if($jobData->order_status == 0 && $data['order_status'] == 1):
            $this->sendJobApproval($data['id']);
        endif;

        $msg = "";
        if ($data['order_status'] == 1) {
            $msg = "Start";
        } else if ($data['order_status'] == 3) {
            $msg = "Hold";
        } else if ($data['order_status'] == 2) {
            $msg = "Restart";
        } else if ($data['order_status'] == 5) {
            $msg = "Close";
        } else if ($data['order_status'] == 4) {
            $msg = "Reopen";
        } else if ($data['order_status'] == 6) {
            $msg = "Close";
        }
        return ['status' => 1, 'message' => "Job Card " . $msg . " successfully."];
    }

    public function sendJobApproval($id)
    {
        try {
            $this->db->trans_begin();
            $jobCardData = $this->getJobcard($id);
            
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return true;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobBomData($jobCardId, $item_id="")
    {
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_code,item_master.full_name,unit_master.unit_name,item_master.material_grade";
        $queryData['leftJoin']['item_master'] = 'item_master.id = job_bom.ref_item_id';
        $queryData['leftJoin']['unit_master'] = 'unit_master.id = item_master.unit_id';
        $queryData['where']['job_bom.job_card_id'] = $jobCardId;
        if(!empty($item_id)){$queryData['where']['job_bom.item_id'] = $item_id;}
        $result = $this->rows($queryData);
        return $result;
    }

    public function getLastActivitLog($id)
    {
        $data['tableName'] = $this->jobTrans;
        $data['where']['job_card_id'] = $id;
        $data['order_by']['created_at'] = "DESC";
        $data['order_by']['updated_at'] = "DESC";
        $data['limit'] = 5;
        $result = $this->rows($data);

        return $result;
    }

   

    public function updateJobProcessSequance($data)
    {
        try {
            $this->db->trans_begin();
            $saveJobCard = array();
            if (!empty($data['id'])) :
                $newProcesses = $data['process_id'];
                if (!empty($data['rnstages'])) {
                    $newProcesses = $data['rnstages'] . ',' . $data['process_id'];
                }
                $saveJobCard = $this->store($this->jobCard, ['id' => $data['id'], 'process' => $newProcesses], 'Job Card');


                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if (!empty($approvalData)) :
                    $rnStage = (!empty($data['rnstages'])) ? explode(",", $data['rnstages']) : [0];
                    $newProcessesStage = explode(",", $data['process_id']);

                    $countRnStage = count($rnStage);
                    $i = 0;
                    $j = 0;
                    $previusSatge = 0;
                    $previusSatgeId = 0;

                    foreach ($approvalData as $row) :
                        if ($i > $previusSatge) :
                            /* print_r(['id'=>$row->id,'in_process_id'=>$previusSatgeId,'out_process_id'=>(isset($newProcessesStage[$i]))?$newProcessesStage[$i]:0]);
                            print_r("---"); */
                            $this->store($this->jobApproval, ['id' => $row->id, 'in_process_id' => $previusSatgeId, 'out_process_id' => (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0]);
                            $previusSatgeId = (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0;
                            $previusSatge = $i;
                            $i++;
                        endif;
                        if ($row->in_process_id == $rnStage[($countRnStage - 1)]) :
                            /* print_r(['id'=>$row->id,'out_process_id'=>$newProcessesStage[$i]]);
                            print_r("---"); */
                            $this->store($this->jobApproval, ['id' => $row->id, 'out_process_id' => $newProcessesStage[$i]]);
                            $previusSatgeId = $newProcessesStage[$i];
                            $previusSatge = $i;
                            $i++;
                        endif;
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function removeJobStage($data)
    {
        try {
            $this->db->trans_begin();
            $saveJobCard = array();
            if (!empty($data['id'])) :
                $jobCardData = $this->getJobCard($data['id']);
                $process = explode(",", $jobCardData->process);
                $updateProcesses = array();
                foreach ($process as $pid) {
                    if ($pid != $data['process_id']) {
                        $updateProcesses[] = $pid;
                    }
                }
                $newProcesses = implode(',', $updateProcesses);

                $saveJobCard = $this->store($this->jobCard, ['id' => $data['id'], 'process' => $newProcesses], 'Job Card');

                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $data['id'];
                $approvalData = $this->rows($queryData);
                if (!empty($approvalData)) :
                    $jobProcess = explode(",", "0," . $newProcesses . ",0");
                    foreach ($approvalData as $row) :
                        $nextProcessId = ((count($process) - 1) != array_search($data['process_id'], $process)) ? $process[array_search($data['process_id'], $process) + 1] : 0;
                        if (!in_array($row->out_process_id, $jobProcess)) :
                            $this->store($this->jobApproval, ['id' => $row->id, "out_process_id" => $nextProcessId]);
                        endif;
                        if (!in_array($row->in_process_id, $jobProcess)) :
                            $this->trash($this->jobApproval, ['id' => $row->id]);
                        endif;
                    endforeach;
                endif;
            endif;
            $result = $this->getJobStages($data['id']);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobStages($job_id)
    {
        $stageRows = "";
        $pOptions = '<option value="">Select Stage</option>';
        $jobCardData = $this->getJobCard($job_id);
        $process = explode(",", $jobCardData->process);

        if (!empty($process)) :
            $i = 0;
            $inQty = 0;
            foreach ($process as $pid) :
                $process_name = (!empty($pid)) ? $this->process->getProcess($pid)->process_name : "Initial Stage";
                $jobProcessData = $this->production->getProcessWiseProduction($job_id, $pid, 0);
                $inQty = (!empty($jobProcessData)) ? $jobProcessData->in_qty : 0;
                if ($inQty <= 0 and $i > 0) :
                    $stageRows .= '<tr id="' . $pid . '">
									<td class="text-center">' . $i . '</td>
									<td>' . $process_name . '</td>
									<td class="text-center">' . ($i + 1) . '</td>
									<td class="text-center">
										<button type="button" data-pid="' . $pid . '" class="btn btn-outline-danger waves-effect waves-light removeJobStage"><i class="ti-trash"></i></button>
									</td>
								  </tr>';
                endif;
                $i++;
            endforeach;
        else :
            $stageRows .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        $processDataList = $this->process->getProcessList();
        foreach ($processDataList as $row) :
            if (!empty($process) && (!in_array($row->id, $process))) :
                $pOptions .= '<option value="' . $row->id . '">' . $row->process_name . '</option>';
            endif;
        endforeach;

        return [$stageRows, $pOptions];
    }

    public function getProcessWiseRequiredMaterials($data)
    {
        $kitData = $this->getJobBomData($data->id, $data->product_id);

        $resultData = array(); $i = 1; $html = "";
        if (!empty($kitData)) :
            foreach ($kitData as $row) :
                $issueQty = 0;
                // $issueMtrData = $this->getIssueMaterialDetail($row->job_card_id, $row->ref_item_id);
                $pendingQty =$row->received_qty - ($row->used_qty + $row->return_qty);//$issueMtrData->issue_qty - abs($issueMtrData->used_qty);

                $html .= '<tr class="text-center">
                    <td>' . $i++ . '</td>
                    <td class="text-left">' . $row->item_name . '</td>
                    <td>' . $row->qty . '</td>
                    <td>' . ($row->qty * $data->qty) . '</td>
                    <td>' . $row->issue_qty . '</td>
                    <td>' . ($row->used_qty) . '</td>
                    <td>' . ($pendingQty) . '</td>
                    <td>
                        <a class="btn btn-outline-success openMaterialReturnModal" href="javascript:void(0)" datatip="Return Material Scrap" flow="left" data-ref_id="0" data-product_id="' . $row->item_id . '" data-id="'.$data->id.'" data-job_card_id="' . $row->job_card_id . '" data-product_name="' . $data->product_code . '" data-process_name="" data-pending_qty="" data-item_name="' . $row->full_name . '" data-item_id="' . $row->ref_item_id . '" data-dispatch_id="' . $row->dispatch_id . '" data-wp_qty="' . $row->qty . '" data-modal_id="modal-lg"><i class="fas fa-reply" ></i></a>
                    </td>
                </tr>';
                $resultData[] = ['item_id' => $row->ref_item_id, 'item_name' => $row->full_name, 'bom_qty' => $row->qty, 'req_qty' => ($row->qty * $data->qty), 'issue_qty' => $issueQty, 'pending_qty' => $pendingQty];
            endforeach;
        endif;
        return ['status' => 1, 'message' => 'Data Found.', "resultData" => $resultData, 'result' => $html];
    }
    public function getBatchNoForReturnMaterial($job_id, $issueId)
    {
        $options = '<option value="">Select Batch No</option>';
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "batch_no,tc_no";
        $queryData['where']['trans_type'] = 2;
        $queryData['where']['ref_type'] = 3;
        $queryData['where_in']['ref_id'] = $job_id;
        // $queryData['where_in']['trans_ref_id'] = $issueId;
        $queryData['group_by'][] = "batch_no";
        $batchNoList = $this->rows($queryData);
        foreach ($batchNoList as $row) :
            $options .= '<option value="' . $row->batch_no . '" data-tc_no = "'.$row->tc_no.'">' . $row->batch_no . '</option>';
        endforeach;
        return ['status' => 1, 'options' => $options];
    }

    public function getIssueMaterialDetail($job_card_id, $item_id)
    {
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "SUM(CASE WHEN trans_type = 1 AND ref_type = 21 THEN qty ELSE 0 END) as issue_qty,SUM(CASE WHEN trans_type = 2  AND ref_type = 21 THEN qty ELSE 0 END)as used_qty";
        $queryData['where']['ref_id'] = $job_card_id;
        $queryData['where']['item_id'] = $item_id;
        $result = $this->row($queryData);
        return $result;
    }

    /* Material Return , Scrap , Used in Job */
    public function saveMaterialReturn($data)
    {
        try {
            $this->db->trans_begin();
            $issueMtrData = $this->getJobBomRawMaterialData($data['job_card_id']);
            $pendingQty = $issueMtrData->received_qty - abs($issueMtrData->used_qty+$issueMtrData->return_qty);

            if (round($data['qty'],3) > round($pendingQty,3)) {
                $errorMessage['qty'] = "Qty is Invalid";
                return ['status' => 0, 'message' => $errorMessage];
            }
            $jobData = $this->getJobcard($data['job_card_id']);

            /** Stock Effect */
            if ($data['ref_type'] != 21) {
                $stockTrans = [
                    'id' => "",
                    'location_id' => $data['location_id'],
                    'batch_no' => (!empty($data['batch_no']) && $data['ref_type'] == 10) ? $data['batch_no'] : $jobData->job_number,
                    'tc_no' => $data['tc_no'],
                    'trans_type' => 1,
                    'item_id' => $data['item_id'],
                    'qty' => $data['qty'],
                    'ref_type' => $data['ref_type'],
                    'ref_id' => $data['job_card_id'],
                    'ref_no' => $jobData->job_number,
                    'ref_date' => date("Y-m-d"),
                    'created_by' => $data['created_by'],
                ];
                $this->store("stock_transaction", $stockTrans);
            }

            $queryData['tableName'] = $this->jobApproval;
            $queryData['select'] = "job_approval.id";
            $queryData['where']['in_process_id'] = 0;
            $queryData['where']['job_card_id'] = $data['job_card_id'];
            $aprvData = $this->row($queryData);

            /** Job Heat Trans Effect */
            $setData = array();
            $setData['tableName'] = $this->job_heat_trans;
            $setData['where']['job_approval_id'] = $aprvData->id;
            $setData['where']['tc_no'] = $data['tc_no'];
            $setData['where']['batch_no'] = $data['batch_no'];
            $setData['set']['in_qty'] = 'in_qty, - ' . $data['qty'];
            $this->setValue($setData);

            /*** Job Bom Effect */
            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['job_card_id'] =$data['job_card_id'];
            $setData['where']['ref_item_id'] = $data['item_id'];
            $setData['set']['return_qty'] = 'return_qty, + ' . $data['qty'];
            $this->setValue($setData);
            
            $issueMtrData = $this->getJobBomRawMaterialData($data['job_card_id']);
            $pendingQty = $issueMtrData->received_qty - abs($issueMtrData->used_qty + $issueMtrData->return_qty);
            $result = ['status' => 1, 'message' => "Scrap saved successfully.", 'result' => $this->getMaterialReturnTrans(['job_card_id' => $data['job_card_id'], 'item_id' => $data['item_id']]), 'pending_qty' => round($pendingQty,3)];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getMaterialReturnTrans($data)
    {
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.*,item_master.item_name,unit_master.unit_name";
        $queryData['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['join']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['where_in']['stock_transaction.ref_type'] = '10,13';
        $queryData['where']['stock_transaction.ref_id'] = $data['job_card_id'];
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $result = $this->rows($queryData);

        $i = 1;
        $html = "";
        $functionName = "";
        foreach ($result as $row) :
            $functionName = "deleteMaterialReturn";
            $button = '<button type="button" onclick="' . $functionName . '(' . $row->id . ',' . $row->qty . ');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
            $returnType = ($row->ref_type == 10) ? 'Material Return' : 'Scrap';
            $html .= '<tr>
                        <td style="width:5%;">' . $i++ . '</td>
                        <td>' . $row->item_name . '</td>
                        <td>' . $returnType . '</td>
                        <td>' . $row->qty . ' (' . $row->unit_name . ')</td>
						<td class="text-center" style="width:10%;">
							' . $button . '
						</td>
					</tr>';
        endforeach;

        $sendData['result'] = $result;
        $sendData['resultHtml'] = $html;
        return $sendData;
    }

    public function deleteMaterialReturn($id)
    {
        try {
            $this->db->trans_begin();
            $queryData['tableName'] = $this->stockTrans;
            $queryData['where']['stock_transaction.id'] = $id;
            $returnData = $this->row($queryData);

            $this->remove("stock_transaction", ['id' => $id]);

            // $queryData['tableName'] = $this->jobApproval;
            // $queryData['select'] = "job_approval.id";
            // $queryData['where']['in_process_id'] = 0;
            // $queryData['where']['job_card_id'] = $returnData->ref_id;
            $aprvData = $this->getJobPendingQty($returnData->ref_id);

            /** Job Heat Trans Effect */
            $setData = array();
            $setData['tableName'] = $this->job_heat_trans;
            $setData['where']['job_approval_id'] = $aprvData->id;
            $setData['where']['tc_no'] = $returnData->tc_no;
            $setData['where']['batch_no'] = $returnData->batch_no;
            $setData['set']['in_qty'] = 'in_qty, + ' . abs($returnData->qty);
            $this->setValue($setData);

            /*** Job Bom Effect */
            $setData = array();
            $setData['tableName'] = $this->jobBom;
            $setData['where']['job_card_id'] = $returnData->ref_id;
            $setData['where']['ref_item_id'] = $returnData->item_id;
            $setData['set']['return_qty'] = 'return_qty, - ' . abs($returnData->qty);
            $this->setValue($setData);

            $issueMtrData = $this->getJobBomRawMaterialData($returnData->ref_id);
            $pendingQty = $issueMtrData->issue_qty - (abs($issueMtrData->used_qty) + abs($issueMtrData->return_qty));
            $result = ['status' => 1, 'message' => "Scrap deleted successfully.", 'result' => $this->getMaterialReturnTrans(['job_card_id' => $returnData->ref_id, 'item_id' => $returnData->item_id]), 'pending_qty' => round($pendingQty,3)];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getJobLog($id)
    {
        $data['tableName'] = $this->jobcardLog;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function getJobLogData($job_card_id)
    {
        $data['tableName'] = $this->jobcardLog;
        $data['where']['job_card_id'] = $job_card_id;
        return $this->rows($data);
    }

    public function saveJobQty($data)
    {
        try {
            $this->db->trans_begin();
            $operation = ($data['log_type'] == 1) ? '+' : '-';
            $logResult = $this->store($this->jobcardLog, $data, 'Jobcard Log');

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $data['job_card_id'];
            $updateQuery['set']['qty'] = 'qty,' . $operation . $data['qty'];
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobApproval;
            $updateQuery['where']['job_card_id'] = $data['job_card_id'];
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['inward_qty'] = 'inward_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['ih_prod_qty'] = 'ih_prod_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['ok_qty'] = 'ok_qty, ' . $operation . $data['qty'];
            $updateQuery['set']['total_prod_qty'] = 'total_prod_qty, ' . $operation . $data['qty'];
            $this->setValue($updateQuery);

            /*$jobUsedQuery['tableName'] = $this->jobBom;
            $jobUsedQuery['where']['job_card_id'] = $data['job_card_id'];
            $jobRslt = $this->rows($jobUsedQuery);
            $jobData = $this->getJobcard($data['job_card_id']);
            foreach ($jobRslt as $row) {
                $stockPlusTrans = [
                    'id' => "",
                    'location_id' => $this->RCV_RM_STORE->id,
                    'batch_no' => $jobData->job_number,
                    'trans_type' => ($data['log_type'] == 1) ? 1 : 2,
                    'item_id' => $row->ref_item_id,
                    'qty' => (($data['log_type'] == 1) ? '' : '-') . ($data['qty'] * $row->qty),
                    'ref_type' => 21,
                    'ref_id' => $data['job_card_id'],
                    'ref_batch' => $logResult['insert_id'],
                    'ref_no' => $jobData->job_number,
                    'ref_date' => date("Y-m-d"),
                    // 'remark'=>'Update Job Qty',
                    'created_by' => $this->session->userdata('loginId'),
                    'stock_effect' => 0
                ];
                $this->store('stock_transaction', $stockPlusTrans);
            }*/
			
            $result = $this->getJobLogData($data['job_card_id']);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteJobUpdateQty($id)
    {
        try {
            $this->db->trans_begin();
            $logData = $this->getJobLog($id);
            $operation = ($logData->log_type == 1) ? '-' : '+';
            $this->trash($this->jobcardLog, ['id' => $id], 'Jobcard Log');

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobCard;
            $updateQuery['where']['id'] = $logData->job_card_id;
            $updateQuery['set']['qty'] = 'qty,' . $operation . $logData->qty;
            $this->setValue($updateQuery);

            $updateQuery = array();
            $updateQuery['tableName'] = $this->jobApproval;
            $updateQuery['where']['job_card_id'] = $logData->job_card_id;
            $updateQuery['where']['in_process_id'] = 0;
            $updateQuery['set']['in_qty'] = 'in_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['in_qty'] = 'in_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['inward_qty'] = 'inward_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['ih_prod_qty'] = 'ih_prod_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['ok_qty'] = 'ok_qty, ' . $operation . $logData->qty;
            $updateQuery['set']['total_prod_qty'] = 'total_prod_qty, ' . $operation . $logData->qty;
            $this->setValue($updateQuery);

            $this->remove($this->stockTrans, ['ref_batch' => $id, 'ref_type' => 21, 'ref_id' => $logData->job_card_id]);
            $result = $this->getJobLogData($logData->job_card_id);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
    
    /* Used for Tag Print (Rej.,Rew.,Susp.) */
    public function getTagData($id){
        $data['tableName'] = $this->jobTrans;
		$data['select'] = "job_transaction.*,party_master.party_name,job_card.job_number,item_master.full_name,item_master.item_name,item_master.item_code,current_process.process_name,next_process.process_name as next_process , department_master.name as dept_name,job_approval.out_process_id,machine.item_code as machine_code,machine.item_name as machine_name,employee_master.emp_name,operator_master.emp_name as operator_name";
		$data['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
		$data['leftJoin']['item_master as machine'] = "job_transaction.machine_id = machine.id";
		$data['leftJoin']['party_master'] = "job_transaction.vendor_id = party_master.id";
		$data['leftJoin']['employee_master as operator_master'] = "job_transaction.operator_id = operator_master.id";
		$data['leftJoin']['employee_master'] = "job_transaction.created_by = employee_master.id";
        $data['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id"; 
        $data['leftJoin']['process_master as next_process'] = "next_process.id = job_approval.out_process_id";
        $data['leftJoin']['process_master as current_process'] = "current_process.id = job_transaction.process_id";
        $data['leftJoin']['department_master'] = "department_master.id = current_process.dept_id";
        $data['where']['job_transaction.id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function getProductionIssuedMtrBatch($job_card_id,$bom_id){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = "stock_transaction.batch_no,stock_transaction.tc_no,SUM(qty) as qty";
        $queryData['where']['stock_transaction.trans_type'] =2;
        $queryData['where']['stock_transaction.ref_type'] =3;
        $queryData['where']['stock_transaction.ref_id'] = $job_card_id;
        $queryData['where']['stock_transaction.trans_ref_id'] = $bom_id;
        $queryData['group_by'][] = 'batch_no,tc_no';
        return $this->rows($queryData);
    }

    public function getJobBomRawMaterialData($jobCardId)
    {
        $queryData['tableName'] = $this->jobBom;
        $queryData['select'] = "job_bom.*,item_master.item_name,item_master.item_code,item_master.full_name,unit_master.unit_name";
        $queryData['leftJoin']['item_master'] = 'item_master.id = job_bom.ref_item_id';
        $queryData['leftJoin']['unit_master'] = 'unit_master.id = item_master.unit_id';
        $queryData['where']['job_bom.job_card_id'] = $jobCardId;
        $queryData['where']['item_master.item_type'] =3;
        $result = $this->row($queryData);
        return $result;
    }
	
	public function getSettingParamData($postData){
        $queryData['tableName'] = $this->setting_parameter;
        $queryData['select'] = "setting_parameter.*";
        if(!empty($postData['job_approval_id'])){ $queryData['where']['setting_parameter.job_approval_id'] =$postData['job_approval_id']; }
        $result = $this->rows($queryData);
        return $result;
    }

    public function saveSettingParameter($data)
    {
        try {
            $this->db->trans_begin();
            $result = $this->store($this->setting_parameter,$data);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function deleteSettingParam($id)
    {
        try {
            $this->db->trans_begin();
            $result = $this->trash($this->setting_parameter,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function approveRM($data){
        try {
            $this->db->trans_begin();
            $result = $this->edit($this->jobBom,['job_card_id'=>$data['id']],['approved_by'=>$this->loginId,'approved_at'=>date("Y-m-d")]);
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

	// Created By JP @ 06.07.2023
    public function trackOrderByTransId($paramData){  
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'job_card.job_number, process_master.process_name, SUM(job_approval.in_qty) as oq, SUM(job_approval.ok_qty) as fq, SUM(job_approval.total_rejection_qty) as rq';
        $queryData['leftJoin']['job_approval'] = "job_approval.job_card_id = job_card.id";
		$queryData['leftJoin']['process_master'] = "process_master.id =  job_approval.in_process_id";
        $queryData['where']['job_card.so_trans_id'] = $paramData['so_trans_id'];
        $queryData['where']['job_card.order_status < '] = 4; // Only Pending/Started/In Process
        $queryData['where']['job_approval.is_delete'] = 0;
		 $queryData['where']['job_approval.in_process_id > '] = 0;
        $queryData['group_by'][] = 'job_approval.in_process_id';
        $queryData['order_by']['job_approval.id'] = 'ASC';
        $result = $this->rows($queryData);
		//$this->printQuery();
		return $result;
    }
    
    /*** Updated JOb Process Sequence & Remove Process */
    public function saveJobProcessSequence($data)
    {
        try {
            $this->db->trans_begin();
            $queryData = array();
            $queryData['tableName'] = $this->jobApproval;
            $queryData['where']['job_card_id'] = $data['job_card_id'];
            $queryData['where']['in_process_id >'] = 0;
            $queryData['customWhere'][] ='(inward_qty+outward_qty)>0';
            $rnAprvData = $this->rows($queryData);
            $rnstages ='';
            if(!empty($rnAprvData)){
                $rnstages = implode(",",array_column($rnAprvData,'in_process_id'));
            }
            $newProcesses ='';
            if (!empty($rnstages)) {
                $newProcesses = $rnstages . ',' . implode(",",$data['in_process_id']);
            }else{
                $newProcesses = implode(",",$data['in_process_id']);
            }
            
            /*** Set job_card - process */
            $result = $this->store($this->jobCard, ['id' => $data['job_card_id'], 'process' => $newProcesses], 'Job Card');

            $queryData = array();
            $queryData['tableName'] = $this->jobApproval;
            $queryData['where']['job_card_id'] = $data['job_card_id'];
            $approvalData = $this->rows($queryData);
            if (!empty($approvalData)) :
                $rnStage = (!empty($rnstages)) ? explode(",",'0,'.$rnstages) : [0];
                $newProcessesStage = $data['in_process_id'];

                $countRnStage = count($rnStage);
                $i = 0; $j = 0; $previusSatge = 0; $previusSatgeId = 0;

                $newCount = count(explode(",",'0,'.$newProcesses)); $rowCount = 1;;
                foreach ($approvalData as $row) :
                    /** SetNew in out Process As per Sequence  */
                    if ($i > $previusSatge) :
                        $this->store($this->jobApproval, ['id' => $row->id, 'in_process_id' => $previusSatgeId, 'out_process_id' => (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0]);
                        $previusSatgeId = (isset($newProcessesStage[$i])) ? $newProcessesStage[$i] : 0;
                        $previusSatge = $i;
                        $i++;
                    endif;
                    /** If Process is Running Process Last Process then Updates New first process AS Running Last Process's Out Process */
                    if ($row->in_process_id == $rnStage[($countRnStage - 1)]) :
                        
                        $this->store($this->jobApproval, ['id' => $row->id, 'out_process_id' => $newProcessesStage[$i]]);
                        $previusSatgeId = $newProcessesStage[$i];
                        $previusSatge = $i;
                        $i++;
                    endif;

                    /** Remove deleted Process row */
                    if($rowCount > $newCount){
                        $this->trash($this->jobApproval,['id'=>$row->id]);
                        $this->trash($this->job_heat_trans,['job_approval_id'=>$row->id]);
                    }
                    $rowCount++;
                endforeach;
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


    public function addJobStage($data)
    {
        try {
            $this->db->trans_begin();
            if (!empty($data['job_card_id'])) :
                $jobCardData = $this->getJobCard($data['job_card_id']);
                $process = explode(",", $jobCardData->process);
                $process[] = $data['process_id'];
                $newProcesses = implode(',', $process);

                $result = $this->store($this->jobCard, ['id' => $data['job_card_id'], 'process' => $newProcesses], 'Job Card');

                $queryData = array();
                $queryData['tableName'] = $this->jobApproval;
                $queryData['where']['job_card_id'] = $data['job_card_id'];
                $queryData['order_by']['id'] = "DESC";
                $approvalData = $this->row($queryData);

                if (!empty($approvalData)) :
                    $this->store($this->jobApproval, ['id' => $approvalData->id, 'out_process_id' => $data['process_id']]);
                    $aprvResult = $this->store($this->jobApproval, ['id' => "", 'entry_date' => date("Y-m-d"), 'job_card_id' => $data['job_card_id'], 'product_id' => $approvalData->product_id, 'in_process_id' => $data['process_id'], 'out_process_id' => 0, 'created_by' => $this->loginId]);
                    $kitData = $this->getJobBomRawMaterialData($data['job_card_id']);
                    $batchData = $this->getProductionIssuedMtrBatch($data['job_card_id'],$kitData->id);
                    foreach($batchData as $row){
                        $heatData = [
                            'id' =>'',
                            'job_card_id'=>$data['job_card_id'],
                            'job_approval_id'=>$aprvResult['insert_id'],
                            'batch_no' =>$row->batch_no,
                            'tc_no' =>$row->tc_no,
                            'in_qty' => 0,
                        ];
                        $this->store($this->job_heat_trans, $heatData);
                    }
                endif;
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

    /*** For App */
    public function getJobListForApp($data)
    {
        $data['tableName'] = $this->jobCard;
        $data['select'] = "job_card.*,item_master.item_name,item_master.full_name,item_master.item_code,party_master.party_name,party_master.party_code,item_category.category_name,IFNULL(jobBom.issue_qty,0) as issue_qty,IFNULL(jobBom.received_qty,0) as received_qty,jobBom.used_as,jobBom.approved_by";

        $data['leftJoin']['(select job_bom.issue_qty,job_bom.received_qty,job_bom.job_card_id,job_bom.used_as,job_bom.approved_by FROM job_bom LEFT JOIN item_master ON item_master.id = job_bom.ref_item_id WHERE job_bom.is_delete = 0 AND item_master.item_type = 3) as jobBom'] = "jobBom.job_card_id = job_card.id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['party_master'] = "job_card.party_id = party_master.id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
    

        if (isset($data['status']) && $data['status'] == 0) {
            $data['where_in']['job_card.order_status'] = [0, 1, 2];
            $data['where']['job_card.is_npd'] = 0;
        }
        if (isset($data['status']) && $data['status'] == 1) {
            $data['where_in']['job_card.order_status'] = [4];
            $data['where']['job_card.job_date >= '] = $this->startYearDate;
            $data['where']['job_card.job_date <= '] = $this->endYearDate;
        }
        $data['order_by']["LENGTH(job_card.job_number)"] = "DESC";

       
        $result = $this->rows($data);
        return $result;
    }
}
