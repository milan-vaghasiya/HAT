<?php
class Migration extends MY_Controller{

    public function __construct(){
        parent::__construct();
    } 

    //Migration/migrateRm
    public function migrateRm(){
        try{
            $this->db->trans_begin();
			
			$itemsData = $this->db->get('items')->result();
			$i=1;
            foreach($itemsData as $row):
                $this->db->where('is_delete',0);
                $this->db->where('id',$row->grade_id);
                $grade = $this->db->get('material_master')->row();
                
                $item_name = trim($row->size).' ';
				$item_name .= trim($row->shape).' ';
				$item_name .= trim($row->bar_type).' ';
				$item_name .= trim($grade->material_grade);
    			$item_image =  trim($row->size) . '☻' . trim($row->shape) . '☻' . trim($row->bar_type) . '☻' . trim($grade->material_grade);
                
				$transData = [
				    'id' => '',
                    'item_type' => 3,
                    'item_code' => '',
                    'item_name' => $item_name,
				    'material_grade_id' => $row->grade_id,
				    'category_id' => $row->category,
                    'unit_id' => $row->unit,
                    'hsn_code' => $row->hsn,
                    'gst_per' => $row->gst,
                    'min_qty' => $row->min,
                    'max_qty' => $row->max,
                    'material_grade' => $grade->material_grade,
                    'opening_qty' => $row->opning_stock,
                    'item_image' => $item_image
                ];
                
                $this->db->where('is_delete',0);
                $this->db->where('item_type',3);
                $this->db->where('item_name',$item_name);
                $items = $this->db->get('item_master')->result();
                
                if(count($items) <= 0){
                    $this->db->insert('item_master',$transData);
                    $transSave = $this->db->insert_id();
                    $this->db->where('id',$row->id);
                    $this->db->update("items",['item_id'=>$transSave]);
                    $i++;
                }
            endforeach;
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Inserted ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /*public function jobApprovalProcessIdsUpdate(){
        try{
            $this->db->trans_begin();

            $jobCardData = $this->db->where('is_delete',0)->get('job_card')->result();
            $processIds = array();
            $mismatchId = array();
            foreach($jobCardData as $job):
                $processIds = explode(",",$job->process);
                $countProcess = count($processIds) + 1;
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$job->id);
                $this->db->where('trans_type',0);
                $approvalData = $this->db->get('job_approval')->result();
                if(!empty($approvalData)):
                    $countApprovalProcess = count($approvalData);
                    if($countApprovalProcess == $countProcess):
                        $i=0;$postData = array();
                        foreach($approvalData as $row):
                            $postData = [
                                'in_process_id' => ($i==0)?0:$processIds[($i - 1)],
                                'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                            ];
                            $this->db->where('id',$row->id);
                            $this->db->update('job_approval',$postData);

                            if(isset($processIds[$i])):
                                $this->db->where('process_id',$processIds[$i]);
                                $this->db->where('job_card_id',$job->id);
                                $this->db->where('is_delete',0);
                                $this->db->update("job_transaction",['job_approval_id'=>$row->id]);
                            endif;
                            $i++;
                        endforeach;
                    else:
                        $mismatchId[] = $job->id;
                    endif;
                endif;
            endforeach;            

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Approval Process Migration Success. mismatch job ids : ".((!empty($mismatchId))?implode(",",$mismatchId):"none");
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /* public function jobApprovalInwardQty(){
        try{
            $this->db->trans_begin();

            $jobCardData = $this->db->where('is_delete',0)->get('job_card')->result();
            $processIds = array();
            foreach($jobCardData as $job):
                $processIds = explode(",","0,".$job->process);
                foreach($processIds as $value):
                    if($value == 0):
                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('in_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $this->db->update('job_approval',['inward_qty'=>$job->qty]);
                    else:
                        $this->db->select('SUM(rejection_qty) as rejection_qty,SUM(rework_qty) as rework_qty');
                        $this->db->where('process_id',$value);
                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('is_delete',0);
                        $this->db->where('entry_type',2);
                        $jobTrans = $this->db->get('job_transaction')->row();

                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('out_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $approvedQty = $this->db->get('job_approval')->row();
                        $outQty = ((!empty($approvedQty->out_qty)) && $approvedQty->out_qty > 0)?$approvedQty->out_qty:0;

                        $this->db->where('job_card_id',$job->id);
                        $this->db->where('in_process_id',$value);
                        $this->db->where('trans_type',0);
                        $this->db->where('is_delete',0);
                        $this->db->update('job_approval',['inward_qty'=>$outQty,'total_rejection_qty'=>((!empty($jobTrans))?$jobTrans->rejection_qty:0),'total_rework_qty'=>((!empty($jobTrans))?$jobTrans->rework_qty:0)]);
                    endif;
                endforeach;
            endforeach;

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Inward Qty Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */   

    /* public function jobTransactionTable(){
        try{
            $this->db->trans_begin();

            $this->db->where('entry_type',2);
            $this->db->where('is_delete',0);
            $jobTransaction = $this->db->get('job_transaction')->result();

            foreach($jobTransaction as $row):

                if($row->rework_qty > 0):
                    if(!in_array($row->process_id,explode(",",$row->rework_process_id))):
                        $row->rework_process_id = $row->rework_process_id.",".$row->process_id;

                        $this->db->where('id',$row->id);
                        $this->db->update('job_transaction',['rework_process_id'=>$row->rework_process_id]);
                    endif;
                endif;

                $transData = [
                    'entry_type' => 3,
                    'ref_id' => $row->id,
                    'entry_date' => $row->entry_date,
                    'job_card_id' => $row->job_card_id,
                    'job_approval_id' => $row->job_approval_id,
                    'job_order_id' => $row->job_order_id,
                    'vendor_id' => $row->vendor_id,
                    'process_id' => $row->process_id,
                    'product_id' => $row->product_id,
                    'material_used_id' => $row->material_used_id,
                    'issue_batch_no' => $row->issue_batch_no,
                    'issue_material_qty' => $row->issue_material_qty,
                    'in_qty' => $row->in_qty,
                    'in_w_pcs' => $row->in_w_pcs,
                    'in_total_weight' => $row->in_total_weight,
                    'rework_qty' => $row->rework_qty,
                    'rejection_qty' => $row->rejection_qty,
                    'rej_scrap_qty' => $row->rej_scrap_qty,
                    'out_qty' => $row->out_qty,
                    'ud_qty' => $row->ud_qty,
                    'w_pcs' => $row->w_pcs,
                    'total_weight' => $row->total_weight,
                    'rejection_reason' => $row->rejection_reason,
                    'rejection_remark' => $row->rejection_remark,
                    'rejection_stage' => $row->rejection_stage,
                    'remark' => $row->remark,
                    'challan_prefix' => $row->challan_prefix,
                    'challan_no' => $row->challan_no,
                    'in_challan_no' => $row->in_challan_no,
                    'charge_no' => $row->charge_no,
                    'challan_status' => $row->challan_status,
                    'operator_id' => $row->operator_id,
                    'machine_id' => $row->machine_id,
                    'shift_id' => $row->shift_id,
                    'production_time' => $row->production_time,
                    'cycle_time' => $row->cycle_time,
                    'job_process_ids' => $row->job_process_ids,
                    'rework_process_id' => $row->rework_process_id,
                    'rework_reason' => $row->rework_reason,
                    'rework_remark' => $row->rework_remark,
                    'material_issue_status' => $row->material_issue_status,
                    'setup_status' => $row->setup_status,
                    'inspection_by' => $row->created_by,
                    'created_by' => $row->created_by
                ];
                $this->db->insert('job_transaction',$transData);
                $transSave = $this->db->insert_id();

                $this->db->where('id',$row->id);
                $this->db->update('job_transaction',["inspection_by"=>$row->created_by,'inspected_qty'=>($row->out_qty + $row->rejection_qty + $row->rework_qty)]);

                if(!empty($row->rework_process_id)):
                    $processIds = explode(",",$row->rework_process_id);
                    $counter = count($processIds);
                    for($i=0;$i<$counter;$i++):
                        $approvalData = [
                            'entry_date' => $row->entry_date,
                            'trans_type' => 1,
                            'ref_id' => $transSave,
                            'job_card_id' => $row->job_card_id,
                            'product_id' => $row->product_id,
                            'in_process_id' => ($i == 0)?$row->process_id:$processIds[($i - 1)],
                            'inward_qty' => ($i == 0)?$row->rework_qty:0,
                            'in_qty' => ($i == 0)?$row->rework_qty:0,
                            'in_w_pcs' => ($i == 0)?$row->w_pcs:0,
                            'in_total_weight' => ($i == 0)?$row->total_weight:0,
                            'out_process_id' => (isset($processIds[$i]))?$processIds[$i]:0,
                            'created_by' => $row->created_by
                        ];
                        $this->db->insert("job_approval",$approvalData);
                    endfor; 
                endif;                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Job Transaction table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */ 

    /* public function partyMasterCurrencyUpdate(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('currency !=',"INR");
            $invoiceData = $this->db->get('party_master')->result();
            
            foreach($invoiceData as $row):
                $row->currency = (!empty($row->currency))?$row->currency:"INR";
                $row->currency = str_replace("$","",$row->currency);
                $row->currency = str_replace("€","",$row->currency);
                
                $this->db->where('id',$row->id);
                $this->db->update("party_master",['currency'=>trim($row->currency)]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Master Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /*public function partyMaster(){
        try{
            $this->db->trans_begin();
            $partyData = $this->db->get('party_master')->result();
            foreach($partyData as $row):
                $data = array();
                $groupCode = ($row->party_category == 1)?"SD":"SC";
                $groupData = $this->db->where('group_code',$groupCode)->get('group_master')->row();

                $data['group_id'] = $groupData->id;
                $data['group_name'] = $groupData->name;
                $data['group_code'] = $groupData->group_code;

                $data['balance_type'] = ($row->balance_type == "C")?1:-1;
                $data['cl_balance'] = $data['opening_balance'] = $row->opening_balance * $data['balance_type'];
                
                $this->db->where('id',$row->id);
                $this->db->update('party_master',$data);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Party Master table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function transMainCurrencyUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where_in('entry_type',[6,7,8,12]);
            $invoiceData = $this->db->get('trans_main')->result();
            
            foreach($invoiceData as $row):
                $this->db->select('party_master.*,currency.inrrate');
                $this->db->where('party_master.id',$row->party_id);
                $this->db->join('currency','currency.currency = party_master.currency','left');
                $partyData = $this->db->get('party_master')->row();

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['currency'=>(!empty($partyData->currency))?trim($partyData->currency):"INR",'inrrate'=>($partyData->inrrate > 0)?$partyData->inrrate:1]);
                
                $this->db->where('trans_main_id',$row->id);
                $this->db->where('is_delete',0);
                $transItems = $this->db->get('trans_child') ->result();
                foreach($transItems as $itm):
                    $this->db->where('id',$itm->id);
                    $this->db->update('trans_child',['currency'=>(!empty($partyData->currency))?trim($partyData->currency):"INR",'inrrate'=>($partyData->inrrate > 0)?$partyData->inrrate:1]);
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Main and Child table Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function defualtLedger(){
        $accounts = [
            ['name' => 'Sales Account', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESACC'],
            
            ['name' => 'Sales Account GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESGSTACC'],
            
            ['name' => 'Sales Account Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESTFACC'],
            
            ['name' => 'CGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTOPACC'],
            
            ['name' => 'SGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTOPACC'],
            
            ['name' => 'IGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTOPACC'],
            
            ['name' => 'UTGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTOPACC'],
            
            ['name' => 'CESS (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON SALES', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'Purchase Account', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURACC'],
            
            ['name' => 'Purchase Account GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURGSTACC'],
            
            ['name' => 'Purchase Account Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURTFACC'],
            
            ['name' => 'CGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTIPACC'],
            
            ['name' => 'SGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTIPACC'],
            
            ['name' => 'IGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTIPACC'],
            
            ['name' => 'UTGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTIPACC'],
            
            ['name' => 'CESS (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON PURCHASE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'ROUNDED OFF', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => 'ROFFACC'],
            
            ['name' => 'CASH ACCOUNT', 'group_name' => 'Cash-In-Hand', 'group_code' => 'CS', 'system_code' => 'CASHACC'],
            
            ['name' => 'ELECTRICITY EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'OFFICE RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'GODOWN RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TELEPHONE AND INTERNET CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PETROL EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALES INCENTIVE', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'INTEREST PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'INTEREST RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'SAVING BANK INTEREST', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SUSPENSE A/C', 'group_name' => 'Suspense A/C', 'group_code' => 'AS', 'system_code' => ''],
            
            ['name' => 'PROFESSIONAL FEES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'AUDIT FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'ACCOUNTING CHARGES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'LEGAL FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALARY', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'WAGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'FREIGHT CHARGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'PACKING AND FORWARDING CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'REMUNERATION TO PARTNERS', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TRANSPORTATION CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'DEPRICIATION', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PLANT AND MACHINERY', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FURNITURE AND FIXTURES', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FIXED DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => ''],
            
            ['name' => 'RENT DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => '']	
        ];

        try{
            $this->db->trans_begin();
            $accounts = (object) $accounts;
            foreach($accounts as $row):
                $row = (object) $row;
                $groupData = $this->db->where('group_code',$row->group_code)->get('group_master')->row();
                $ledgerData = [
                    'party_category' => 4,
                    'group_name' => $groupData->name,
                    'group_code' => $groupData->group_code,
                    'group_id' => $groupData->id,
                    'party_name' => $row->name,                    
                    'system_code' => $row->system_code
                ];

                $this->db->where('party_name',$row->name);
                $this->db->where('is_delete',0);
                $this->db->where('party_category',4);
                $checkLedger = $this->db->get('party_master');

                if($checkLedger->num_rows() > 0):
                    $id = $checkLedger->row()->id;
                    $this->db->where('id',$id);
                    $this->db->update('party_master',$ledgerData);
                else:
                    $this->db->insert('party_master',$ledgerData);
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Defualt Ledger Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /* public function migrateStock($item_id=""){
		try{
            $this->db->trans_begin();
            $i=0;
            if(!empty($item_id)){$this->db->where('item_master.id',$item_id);}
            //$this->db->where('item_master.id <= ',3500);
			$this->db->where('is_delete',0);
            $result = $this->db->get("item_master")->result();
            foreach($result as $row):
                $this->db->select("SUM(qty) as qty");
				$this->db->join('location_master','location_master.id = stock_transaction.location_id','left');
				$this->db->where('stock_transaction.is_delete',0);
				$this->db->where('location_master.ref_id',0);
                $this->db->where('stock_transaction.item_id',$row->id);
                $stockData = $this->db->get('stock_transaction')->row();
                
                //update Item Master
                if(!empty($stockData->qty)):
                    $data=['qty'=>$stockData->qty];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                else:
                    $data=['qty'=>0];
                    $this->db->where('id',$row->id);
                    $this->db->update('item_master',$data);
                endif;
                $i++;
                //echo $this->db->last_query().'<br>';
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    } */
    
    /*public function migratePackingStock(){
		try{
            $this->db->trans_begin();
            $i=0;
            $this->db->where('ref_type',16);
            $this->db->where('is_delete',0);
            $result = $this->db->get("stock_transaction")->result();
            foreach($result as $row):
                $this->db->select("packing_date");
                $this->db->where('id',$row->ref_id);
                $this->db->where('is_delete',0);
                $packingData = $this->db->get('packing_master')->row();
                
                //update Item Master
                if(!empty($packingData->packing_date)):
                    $data=['ref_date'=>$packingData->packing_date];
                    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',$data);
                    echo $this->db->last_query().'<br>';
                endif;
                $i++;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }*/

    /*public function migrateMCTLogsheet(){
		try{
            $this->db->trans_begin();
            $i=0;
            $this->db->where('is_delete',0);
            $result = $this->db->get("production_log")->result();
            foreach($result as $row):
                $this->db->select("product_process.cycle_time");
                $this->db->where('item_id',$row->item_id);
                $this->db->where('process_id',$row->process_id);
                $this->db->where('item_id',$row->item_id);
                $this->db->join('currency','currency.currency = party_master.currency','left');
                $this->db->where('is_delete',0);
                $packingData = $this->db->get('product_process')->row();
                
                //update Item Master
                if(!empty($packingData->packing_date)):
                    $data=['ref_date'=>$packingData->packing_date];
                    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',$data);
                    echo $this->db->last_query().'<br>';
                endif;
                $i++;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i. ' Records Updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
        
    }*/

    /*public function processWiseStore(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('is_delete',0);
			$processMaster = $this->db->get('process_master')->result();
				
			foreach($processMaster as $row):
				$this->db->where('ref_id',$row->id);
				$this->db->where('is_delete',0);
				$storeResult = $this->db->get('location_master')->row();
				
				if(empty($storeResult)):
					$this->db->select("(CASE WHEN MAX(store_type) < 10 THEN 10 ELSE (MAX(store_type) + 1) END) as store_type");
					$this->db->where('is_delete',0);
					$store_type = $this->db->get('location_master')->row()->store_type;
					
					$storeData = [
                        'store_name' => "Production",
                        'location' => $row->process_name,
                        'store_type' => $store_type,
                        'ref_id' => $row->id
                    ];
					$this->db->insert("location_master",$storeData);
				else:
					$storeData = [
                        'store_name' => "Production",
                        'location' => $row->process_name,
                        'ref_id' => $row->id
                    ];
					$this->db->where('id',$storeResult->id)->update("location_master",$storeData);
				endif;
			endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Store saved successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function updateRejectionQtyInMovement(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('version',2);
            $this->db->where('id != ',561);
            $result = $this->db->get('job_card')->result();

            $approvalIds = array();
            foreach($result as $row):
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$row->id);
                $approvalData = $this->db->get('production_approval')->result();

                $totalRejQty = 0;
                foreach($approvalData as $apTrans):
                    if(!empty($apTrans->in_process_id)):
                        $this->db->select("SUM(rej_qty) as total_rej_qty");
                        $this->db->where('is_delete',0);
                        $this->db->where_in('prod_type',[1,3]);
                        $this->db->where('job_card_id',$row->id);
                        $this->db->where('process_id',$apTrans->in_process_id);
                        $this->db->having('SUM(rej_qty) > ',0);
                        $rejectionLog = $this->db->get('production_log')->row();

                        $totalRejQty += (!empty($rejectionLog)) ? $rejectionLog->total_rej_qty : 0;
                        if($apTrans->total_ok_qty > 0):
                            $this->db->where('id',$apTrans->id);
                            $this->db->update('production_approval',['total_ok_qty'=>($apTrans->total_ok_qty - $totalRejQty),'out_qty'=>($apTrans->out_qty - $totalRejQty)]);

                            $this->db->where('production_approval_id',$apTrans->id);
                            $this->db->where('job_card_id',$row->id);
                            $this->db->where('out_qty >=',$totalRejQty);
                            $this->db->where('is_delete',0);
                            $this->db->order_by('id','ASC');
                            $this->db->limit(1);
                            $transRow = $this->db->get('production_transaction')->row();

                            if(!empty($transRow)):
                                $this->db->where('id',$transRow->id);
                                $this->db->update('production_transaction',["in_qty"=>($transRow->in_qty - $totalRejQty),"out_qty"=>($transRow->out_qty - $totalRejQty)]);
                            endif;

                            if(!empty($apTrans->out_process_id)):
                                $this->db->set('inward_qty','inward_qty - '.$totalRejQty,false);
                                $this->db->set('in_qty','in_qty - '.$totalRejQty,false);
                                $this->db->where('in_process_id',$apTrans->out_process_id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('is_delete',0);
                                $this->db->update('production_approval');
                            endif;
                        else:
                            $approvalIds[] = $apTrans->id;
                        endif;
                    endif;
                endforeach;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated in production movement. Pending for update ids : '.implode(",",$approvalIds);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function productionStockTransaction(){
		try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $this->db->set('`out_qty`','`total_ok_qty`',false);
            $this->db->update('production_approval');

            $this->db->where('version',2);
            $this->db->where('is_delete',0);
            $this->db->update('job_card',['unstored_qty'=>0]);
			
			$this->db->where('is_delete',0);
			$result = $this->db->get('production_transaction')->result();
				
			foreach($result as $row):
				$jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();
                $processApprovalData = $this->db->where('id',$row->production_approval_id)->get('production_approval')->row();
				
				if(!empty($processApprovalData->in_process_id)):
					$this->db->where('ref_id',$processApprovalData->in_process_id);
					$this->db->where('is_delete',0);
					$locationData = $this->db->get('location_master')->row(); 

                    $stockMinusTrans = [
                        'location_id' => $locationData->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 2,
                        'item_id' => $row->product_id,
                        'qty' => '-' . $row->out_qty,
                        'ref_type' => 23,
                        'ref_id' => $row->job_card_id,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_ref_id' => $row->id,
                        'ref_date' => date("Y-m-d")
                    ];
                    $this->db->insert('stock_transaction',$stockMinusTrans);
				endif;

                if(!empty($processApprovalData->out_process_id)):
                    $this->db->where('ref_id',$processApprovalData->out_process_id);
					$this->db->where('is_delete',0);
					$nxtPrsStore = $this->db->get('location_master')->row(); 

                    $stockPlusTrans = [
                        'location_id' => $nxtPrsStore->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 1,
                        'item_id' => $row->product_id,
                        'qty' =>  $row->out_qty,
                        'ref_type' => 23,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'ref_id' => $row->job_card_id,
                        'trans_ref_id' => $row->id,
                        'ref_date' => date("Y-m-d")
                    ];

                    $this->db->insert('stock_transaction',$stockPlusTrans);
                else:

                    $this->db->where('ref_id',$processApprovalData->in_process_id);
					$this->db->where('is_delete',0);
					$curentPrsStore = $this->db->get('location_master')->row(); 

                    $stockPlusTrans = [
                        'location_id' => $curentPrsStore->id,
                        'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'trans_type' => 1,
                        'item_id' => $row->product_id,
                        'qty' =>  $row->out_qty,
                        'ref_type' => 7,
                        'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                        'ref_id' => $row->job_card_id,
                        'trans_ref_id' => $row->production_approval_id,
                        'ref_date' => date("Y-m-d"),
                        'ref_batch' => 23
                    ];

                    $this->db->insert('stock_transaction',$stockPlusTrans);   
                    
                    $this->db->where('id',$row->job_card_id);
                    $this->db->set('unstored_qty','unstored_qty + '.$row->out_qty,false);
                    $this->db->update('job_card');
                endif;
			endforeach;

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $approvalData = $this->db->get('production_approval')->result();

            $unstoredQty = 0;$pendingQty = 0;
            foreach($approvalData as $row):
            
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',7);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->where('trans_ref_id',$row->id);
                $this->db->where('trans_type',1);
                $this->db->where('ref_batch',NULL);
                $stockTrans = $this->db->get('stock_transaction')->result();

                $jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();
                $unstoredQty = $jobCardData->unstored_qty;$pendingQty = 0;
                foreach($stockTrans as $trans):
                    $pendingQty = $unstoredQty;
                    $unstoredQty = $unstoredQty - $trans->qty;

                    $transQty = ($unstoredQty > 0)?$trans->qty:$pendingQty;                   
                    
                    if($transQty > 0):
                        $this->db->where('ref_id',$row->in_process_id);
                        $this->db->where('is_delete',0);
                        $curentPrsStore = $this->db->get('location_master')->row(); 
                        $stockMinusTrans = [
                            'location_id' => $curentPrsStore->id,
                            'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_type' => 2,
                            'item_id' => $jobCardData->product_id,
                            'qty' => '-' . $transQty,
                            'ref_type' => 7,
                            'ref_id' => $jobCardData->id,
                            'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_ref_id' => $row->id,
                            'ref_date' => date("Y-m-d")
                        ];
                        $this->db->insert('stock_transaction',$stockMinusTrans); 
                        
                        $stockPusTrans = [
                            'location_id' => 136,
                            'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_type' => 1,
                            'item_id' => $jobCardData->product_id,
                            'qty' => $transQty,
                            'ref_type' => 7,
                            'ref_id' => $jobCardData->id,
                            'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                            'trans_ref_id' => $row->id,
                            'ref_date' => date("Y-m-d")
                        ];
                        $this->db->insert('stock_transaction',$stockPusTrans);     
                        
                        $this->db->where('id',$row->job_card_id);
                        $this->db->set('unstored_qty','unstored_qty - '.$transQty,false);
                        $this->db->update('job_card');
                        
                    endif;
                    $this->db->where('id',$trans->id);
                    $this->db->delete('stock_transaction');
                endforeach;
                

                $this->db->where('id',$row->job_card_id);
                $jobData = $this->db->get('job_card')->row();

                $this->db->select('SUM(rej_qty) as rejection_qty');
                $this->db->where('job_card_id',$row->job_card_id);
                $this->db->where_in('prod_type',[1,3]);
                $this->db->having('SUM(rej_qty) > ',0);
                $this->db->where('is_delete',0);
                $logData = $this->db->get('production_log')->row();

                $totalJobQty = ((!empty($logData))?$logData->rejection_qty:0) + $row->total_ok_qty;
                if ($totalJobQty >= $jobData->qty) :
                    $this->db->where('id', $jobCardData->id);
                    $this->db->update('job_card', ['order_status' => 4]);
                endif;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock Transaction migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function rejectionStockTransaction(){
        try{
            $this->db->trans_begin();
			
			$this->db->where('is_delete',0);
            $this->db->where('rej_qty >',0);
            $this->db->where_in('prod_type',[1,3]);
            $rejectionLog = $this->db->get('production_log')->result();

            foreach($rejectionLog as $row):
                $jobCardData = $this->db->where('id',$row->job_card_id)->get('job_card')->row();

                $this->db->where('ref_id',$row->process_id);
                $this->db->where('is_delete',0);
                $locationData = $this->db->get('location_master')->row(); 

                $stockMinusTrans = [
                    'id' => "",
                    'location_id' => $locationData->id,
                    'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                    'trans_type' => 2,
                    'item_id' => $jobCardData->product_id,
                    'qty' =>  "-".$row->rej_qty,
                    'ref_type' => 23,
                    'ref_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no),
                    'ref_id' => $row->job_card_id,
                    'ref_date' => date("Y-m-d"),
                    'ref_batch'=> $row->id
                ];
                $this->db->insert("stock_transaction",$stockMinusTrans);

                $stockPlusTrans = [
                    'location_id' => $locationData->id,
                    'batch_no' => getPrefixNumber($jobCardData->job_prefix, $jobCardData->job_no)."-R",
                    'trans_type' => 1,
                    'item_id' => $jobCardData->product_id,
                    'qty' =>  $row->rej_qty,
                    'ref_type' => 24,
                    'ref_no' => 'REJ',
                    'ref_id' => $row->job_card_id,
                    'trans_ref_id' => $row->id,
                    'ref_date' => date("Y-m-d")
                ];
                $this->db->insert("stock_transaction",$stockPlusTrans);                
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function reverseRejectionQtyInMovement(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('version',2);
            $this->db->where('id != ',561);
            $result = $this->db->get('job_card')->result();

            $approvalIds = array();
            foreach($result as $row):
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$row->id);
                $approvalData = $this->db->get('production_approval')->result();

                foreach($approvalData as $apTrans):
                    if(!empty($apTrans->in_process_id)):
                        $this->db->select("SUM(rej_qty) as total_rej_qty");
                        $this->db->where('is_delete',0);
                        //$this->db->where_in('prod_type',[1,3]);
                        $this->db->where('job_card_id',$row->id);
                        $this->db->where('process_id',$apTrans->in_process_id);
                        $this->db->having('SUM(rej_qty) > ',0);
                        $rejectionLog = $this->db->get('production_log')->row();

                        if(!empty($rejectionLog)):
                            if($apTrans->out_qty > 0):
                                $this->db->where('id',$apTrans->id);
                                $this->db->update('production_approval',['total_ok_qty'=>($apTrans->total_ok_qty + $rejectionLog->total_rej_qty),'out_qty'=>($apTrans->out_qty + $rejectionLog->total_rej_qty)]);

                                $this->db->where('production_approval_id',$apTrans->id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('out_qty >=',$rejectionLog->total_rej_qty);
                                $this->db->where('is_delete',0);
                                $this->db->order_by('id','ASC');
                                $this->db->limit(1);
                                $transRow = $this->db->get('production_transaction')->row();

                                if(!empty($transRow)):
                                    $this->db->where('id',$transRow->id);
                                    $this->db->update('production_transaction',["in_qty"=>($transRow->in_qty + $rejectionLog->total_rej_qty),"out_qty"=>($transRow->out_qty + $rejectionLog->total_rej_qty)]);
                                endif;

                                $this->db->set('inward_qty','inward_qty + '.$rejectionLog->total_rej_qty,false);
                                $this->db->set('in_qty','in_qty + '.$rejectionLog->total_rej_qty,false);
                                $this->db->where('in_process_id',$apTrans->out_process_id);
                                $this->db->where('job_card_id',$row->id);
                                $this->db->where('is_delete',0);
                                $this->db->update('production_approval');
                            else:
                                $approvalIds[] = $apTrans->id;
                            endif;
                        endif;
                    endif;
                endforeach;
            endforeach;
				
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Rejection stock migrated in production movement. Pending for update ids : '.implode(",",$approvalIds);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/

    /*public function stockTransReverseForProduction(){
		try{
            $this->db->trans_begin();

            $this->db->where_in('ref_type',[23,24]);
            $this->db->delete('stock_transaction');

            $this->db->where('is_delete',0);
            $this->db->where('out_process_id',0);
            $approvalData = $this->db->get('production_approval')->result();
			
			foreach($approvalData as $row):
				$this->db->where('ref_id',$row->in_process_id);
				$this->db->where('is_delete',0);
				$curentPrsStore = $this->db->get('location_master')->row(); 
				
				$this->db->where('location_id' , $curentPrsStore->id);
				$this->db->where('trans_type' , 1);
				$this->db->where('ref_type' , 7);
				$this->db->where('ref_id' , $row->job_card_id);
				$this->db->where('trans_ref_id' , $row->id);
				$this->db->where('ref_batch' , 23);
				$this->db->delete('stock_transaction');

                $this->db->where('is_delete',0);
                $this->db->where('ref_type',7);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->where('trans_ref_id',$row->id);
                $this->db->where('trans_type',1);
                $this->db->where('ref_batch',NULL);
                $stockTrans = $this->db->get('stock_transaction')->result();
				
				foreach($stockTrans as $trans):
                    $this->db->where('ref_id',$row->in_process_id);
					$this->db->where('is_delete',0);
					$curentProcessStore = $this->db->get('location_master')->row(); 
					
					$this->db->where('location_id' , $curentProcessStore->id);
					$this->db->where('trans_type' , 2);
					$this->db->where('ref_type' , 7);
					$this->db->where('ref_id' , $trans->ref_id);
					$this->db->where('trans_ref_id' , $trans->trans_ref_id);
					$this->db->where('ref_batch' , NULL);
					$this->db->delete('stock_transaction');
					   
                endforeach;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock Transaction reversed successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /*public function updatePackingDateInStock(){
	    try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('ref_type',16);
            $stockData = $this->db->get('stock_transaction')->result();
			
			foreach($stockData as $row):
			    $this->db->where('is_delete',0);
                $this->db->where('id',$row->ref_id);
                $packingData = $this->db->get('packing_master')->row();
                if(!empty($packingData->packing_date)):
				    $this->db->where('id',$row->id);
                    $this->db->update('stock_transaction',["ref_date"=>$packingData->packing_date]);
                endif;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Packing Date updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
	}*/

    /* public function shortCloseJobCardV1(){
        try{
            $this->db->trans_begin();

            $jobNos = [486, 485, 484, 483, 482, 481, 479, 478, 477, 474, 473, 465, 464, 463, 460, 456, 455, 450, 448, 451, 447, 446, 445, 444, 443, 442, 437, 431, 427, 426, 425, 423, 416, 408, 406, 405, 401, 390, 389, 388, 387, 384, 382, 381, 379, 376, 375, 371, 370, 368, 363, 359, 354, 353, 347, 362, 335, 333, 332, 330, 323, 321, 320, 310, 303, 302, 293, 290, 289, 279, 278, 247, 254, 250, 98, 417, 439, 424, 339, 411, 402, 342, 286, 341, 472, 452, 361, 288, 273, 216, 347, 325, 346, 326, 306, 302, 299, 217];

            $notClosed = array();
            foreach($jobNos as $row):
                $this->db->where('job_no',$row);
                $this->db->where('is_delete',0);
                $result = $this->db->get('job_card')->row();

                if(!empty($result)):
                    $this->db->where('id',$result->id);
                    $this->db->update('job_card',['order_status'=>6]);
                else:
                    $notClosed[] = $row;
                endif;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'V1 Job Card Short Close successfully. Job Card not Found : '.implode(", ",$notClosed);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function oldJobStockTransRemove(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('version',1);
			$this->db->where('is_delete',0);
			$result = $this->db->get('job_card')->result();
			
			foreach($result as $row):
				$this->db->where('ref_type',7);
				$this->db->where('ref_id',$row->id);
				$this->db->where('location_id',178);
				$this->db->where('item_id',$row->product_id);
				$this->db->update('stock_transaction',['is_delete'=>1,'ref_batch'=>'M']);
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */

    /* public function fgStockUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->select('stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id,SUM(stock_transaction.qty) as stock_qty');
            $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
            $this->db->join('location_master','location_master.id = stock_transaction.location_id','left');
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->where('stock_transaction.ref_date <=',"2022-03-31");
            $this->db->where('item_master.item_type',1);
            $this->db->where('location_master.ref_id',0);
			//$this->db->having('SUM(stock_transaction.qty) !=',0);
            $this->db->group_by('location_id');
            $this->db->group_by('batch_no');
			$this->db->group_by('item_id');
            $result = $this->db->get('stock_transaction')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
				$stockTrans=array();
				if($row->stock_qty > 0):
					$stockTrans = [
						'location_id' => $row->location_id,
						'batch_no' => $row->batch_no,
						'trans_type' => 2,
						'item_id' => $row->item_id,
						'qty' => "-".$row->stock_qty,
						'ref_no' => 'Stock Adjustment',
						'ref_type' => 99,
						'ref_date' => "2022-03-31",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					$i++;
				elseif($row->stock_qty < 0):
					$stockTrans = [
						'location_id' => $row->location_id,
						'batch_no' => $row->batch_no,
						'trans_type' => 1,
						'item_id' => $row->item_id,
						'qty' => abs($row->stock_qty),
						'ref_type' => 99,
						'ref_no' => 'Stock Adjustment',
						'ref_date' => "2022-03-31",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					$i++;
				endif;					
			endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'FG Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */	
	
	/* public function updateOpeningStock(){
		try{
            $this->db->trans_begin();
			
			$itemCodes = ['J00110000001','J00110000002','J00110000004','J00110000005','J00110000007','J00110000008','J00110000010','J00010000079','J00010000078','J00010000112','J00010000229','J00010000133','J00020000075','J00030000004','J00030000008','J00040000001','J00040000003','J00040000005','J00040000007','J00040000008','J00040000011','J00040000013','J00040000014','J00040000018','J00040000020','J00040000023','J00040000029','J00040000034','J00050000001','J00060000003','J00100000001','J00100000002','J00100000003','J00100000006','J00160000001','J00160000002','J00160000006','J00160000008','J00160000018','J00160000026','J00180000001','J00180000003','J00220000001','J00240000001','J00260000001','J00260000002','J00280000002','J00280000003','J00290000001','J00290000002','J00290000003','J00310000012','J00340000014','J00340000020','J00340000035','J00440000001','J00440000009','J00440000006','J00440000007','J00440000013','J00440000016','J00440000017','J00440000049','J00440000050','J00450000011','J00450000015','J00450000005','J00450000006','J00450000007','J00450000008','J00450000009','J00450000012','J00450000013','J00450000014','J00450000017','J00450000018','J00450000019','J00450000020','J00450000010','J00460000001','J00460000005','J00460000006','J00460000009','J00460000011','J00460000012','J00470000003','J00490000001','J00490000002','J00500000001','J00500000002','J00500000004','J00500000005','J00500000006','J00500000008','J00500000009','J00500000014','J00500000015','J00500000016','J00500000017','J00500000019','J00530000005','J00530000006','J00550000001','J00550000003','J00560000001','J00560000008','J00560000009','J00560000015','j00560000004','j00560000005','J00560000006','J00560000016','J00560000017','J00560000018','J00560000019','J00560000020','J00560000021','J00560000022','J00560000030','J00580000001','J00600000001','J00660000001','J00660000002','J00660000003','J00660000004','J00660000005','J00660000006','J00660000007','J00660000010','J00660000011','J00660000012','J00660000013','J00660000014','J00660000017','J00660000018','J00660000019','J00690000001','J00690000002','J00690000003','J00700000002','J00700000003','J00720000001','J00720000002','J00720000004','J00720000005','J00740000001','J00750000002','J00750000003','J00750000004','J00750000005','J00750000006','J00750000007','J00750000008','J00750000009','J00750000010','J00750000011','J00750000013','J00750000014','J00750000015','J00750000016','J00750000017','J00750000018','J00750000019','J00750000020','J00750000021','J00750000022','J00750000023','J00750000024','J00750000025','J00750000026','J00750000027','J00750000028','J00750000029','J00750000030','J00750000031','J00750000033','J00750000034','J00760000001','J00760000002','J00760000003','J00760000004','J00760000005'];
			
			$itemQty = [1341, 2160, 1583, 2700, 3430, 8261, 3557, 6, 8, 724, 30, 1285, 90, 7200, 900, 191, 1052, 90, 478, 267, 407, 177, 1158, 877, 81, 295, 92, 86, 10750, 119, 23, 24, 22, 14, 1283, 1083, 1194, 79, 70, 748, 6, 112, 30, 14, 10, 10, 25, 14, 116, 197, 3003, 23000, 57, 2, 50, 6, 9, 13, 7, 82, 25, 12, 1, 7, 22, 28, 44, 40, 4, 31, 48, 16, 8, 10, 7, 69, 20, 7, 17, 13, 428, 44, 2854, 19514, 11, 9, 15, 25, 28, 2800, 154, 30, 31, 2484, 2531, 150, 531, 271, 281, 224, 3, 13, 71, 603, 776, 20, 1326, 10, 79, 153, 585, 1, 21, 100, 108, 199, 3, 9, 6, 3, 75, 78, 94, 19, 31, 11, 1, 231, 3, 11, 13, 967, 2, 27, 7, 12, 2640, 2228, 5, 34, 112, 16267, 73, 60, 1457, 49, 7, 6, 5, 98, 7, 4, 234, 10, 9, 103, 9, 8, 7, 10, 6, 640, 1, 7, 394, 409, 593, 334, 7, 10, 411, 490, 264, 383, 299, 9, 10, 227, 72, 16, 15, 20];
			
			$notFound = array();
			foreach($itemCodes as $key=>$itemCode):
				$this->db->where('item_code',$itemCode);
				$itemData = $this->db->get('item_master')->row();
				
				if(!empty($itemData)):
					$stockTrans = [
						'location_id' => 11,
						'batch_no' => "OS/22-23",
						'trans_type' => 1,
						'item_id' => $itemData->id,
						'qty' => $itemQty[$key],
						'ref_type' => -1,
						'ref_no' => 'OP Stock Adjustment',
						'ref_date' => "2022-04-01",						
					];
					$this->db->insert("stock_transaction",$stockTrans);
					
				else:
					$notFound[] = $itemCode;
				endif;
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Stock updated successfully. not found : <br> '.implode(", ",$notFound);
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */

    /* public function migrateHSNCodes(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->select("hsn_code");
            $this->db->where('hsn_code !=',"");
            $this->db->group_by('hsn_code');
            $result = $this->db->get('item_master')->result();

            $i=0;
            foreach($result as $row):
                $row->hsn_code = trim($row->hsn_code);

                $this->db->where('is_delete',0);
                $this->db->where('hsn_code',$row->hsn_code);
                $check = $this->db->get('hsn_master');

                if($check->num_rows() > 0):
                    $hsnData = $check->row();
                    $this->db->where('id',$hsnData->id);
                    $this->db->update('hsn_master',['hsn_code'=>$row->hsn_code]);
                else:
                    $this->db->insert('hsn_master',['hsn_code'=>$row->hsn_code]);
                endif;
                $i++;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i.' HSN Codes updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */
    
    /* public function updateJobNoInStockTrans(){
		try{
            $this->db->trans_begin();
			
			$this->db->where('ref_type',24);
			$this->db->where('created_by',0);
			$this->db->where('is_delete',0);
			$result = $this->db->get('stock_transaction')->result();
			$i=1;
			foreach($result as $row):
				$this->db->where('id',$row->ref_id);
				$jobData = $this->db->get('job_card')->row();
				$this->db->reset_query();
				if(!empty($jobData))
				{
					$prfx = explode('/',$jobData->job_prefix);
					$jobno = $prfx[0].'/'.$jobData->job_no.'/'.$prfx[1].'-R';
					$this->db->where('id',$row->id);
					$updateData = ['batch_no'=>$jobno];
					print_r(json_encode($updateData).' *** '.$i++.'<br>');
					//$this->db->update('stock_transaction',$updateData);
					$this->db->reset_query();
				}
				else{print_r($row->ref_id.',');}
			endforeach;
			
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'Job Number updated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */
	
    /* public function migrateRejRw(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $result = $this->db->get('production_log')->result();

            $i=0;
            foreach($result as $row):
                $rejArr = Array();
				if(!empty($row->rej_reason))
				{
					$rejArr = json_decode($row->rej_reason);
					if(!empty($rejArr))
					{
						foreach($rejArr as $rjrow)
						{
							//[{"rej_qty":"4","rej_reason":"5","rej_reason_code":"","rejection_reason":"Damage","rej_remark":""}]
							//[{"rej_qty":"3","rej_reason":"68","rej_stage":"0","rej_stage_name":"Row Material","rej_from":"0","rej_party_name":"In House","rej_remark":"","rejection_reason":"Material Crack"}]
							
							$rjData = Array();
							//$rjData['id']="";
							$rjData['log_id']=$row->id;
							$rjData['manag_type']=1;
							$rjData['job_card_id']=$row->job_card_id;
							$rjData['qty']=$rjrow->rej_qty;
							$rjData['reason']=$rjrow->rej_reason;
							$rjData['reason_name']=$rjrow->rejection_reason;
							$rjData['belongs_to']=(!empty($rjrow->rej_stage)) ? $rjrow->rej_stage : '';
							$rjData['belongs_to_name']=(!empty($rjrow->rej_stage_name)) ? $rjrow->rej_stage_name : '';
							$rjData['vendor_id']=$rjrow->rej_from;
							$rjData['vendor_name']=$rjrow->rej_party_name;
							$rjData['remark']=$rjrow->rej_remark;
							$rjData['created_by']=1;
							$rjData['created_at']=date('Y-m-d H:i:s');
							
							//print_r($rjData);print_r('***'.$row->id.'<br>');
							//$this->db->insert('rej_rw_management',$rjData);$i++;
						}
					}
				}
				
                $rwArr = Array();
				if(!empty($row->rw_reason))
				{
					$rwArr = json_decode($row->rw_reason);
					if(!empty($rwArr))
					{
						foreach($rwArr as $rwrow)
						{
							//[{"rej_qty":"4","rej_reason":"5","rej_reason_code":"","rejection_reason":"Damage","rej_remark":""}]
							//[{"rw_qty":"25","rw_reason":"107","rw_stage":"-1","rw_stage_name":"Handling Movement","rw_from":"0","rw_party_name":"In House","rw_remark":"","rework_reason":"Damage"}]
							
							$rwData = Array();
							//$rwData['id']="";
							$rwData['log_id']=$row->id;
							$rwData['manag_type']=2;
							$rwData['job_card_id']=$row->job_card_id;
							$rwData['qty']=$rwrow->rw_qty;
							$rwData['reason']=$rwrow->rw_reason;
							$rwData['reason_name']=$rwrow->rework_reason;
							$rwData['belongs_to']=(!empty($rwrow->rw_stage)) ? $rwrow->rw_stage : '';
							$rwData['belongs_to_name']=(!empty($rwrow->rw_stage_name)) ? $rwrow->rw_stage_name : '';
							$rwData['vendor_id']=$rwrow->rw_from;
							$rwData['vendor_name']=$rwrow->rw_party_name;
							$rwData['remark']=$rwrow->rw_remark;
							$rwData['created_by']=1;
							$rwData['created_at']=date('Y-m-d H:i:s');
							
							//print_r($rwData);print_r('***'.$row->id.'<br>');
							//$this->db->insert('rej_rw_management',$rwData);$i++;
						}
					}
				}
				
                
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo $i.' Rej Rw Migrated successfully.';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */
    
    // Stock By Jayveer @22-06-2022
	/* public function rmStockUpdate(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
            $this->db->where('stock_qty != ',0);
            $result = $this->db->get('rmstock')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
				$stockTrans=array();
				$trans_type = 0;
				if($row->stock_qty > 0){$trans_type = 1;}
				if($row->stock_qty < 0){$trans_type = 2;}
				$stockTrans = [
					'location_id' => $row->location_id,
					'batch_no' => $row->batch_no,
					'trans_type' => $trans_type,
					'item_id' => $row->item_id,
					'qty' => $row->stock_qty,
					'ref_no' => 'Stock Adjustment',
					'ref_type' => 99,
					'ref_date' => date('Y-m-d')						
				];	$i++;
				//print_r($stockTrans);print_r('<br>');
				//$this->db->insert("stock_transaction",$stockTrans);
			endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    } */
    
    /* public function migrateStockEffectInStockTransaction($item_type){
		try{
            $this->db->trans_begin();
			
			if(!empty($item_type))
			{
				$this->db->select('stock_transaction.id,stock_transaction.item_id,stock_transaction.stock_effect, stock_transaction.ref_type');
				$this->db->where('item_master.item_type',$item_type);
				$this->db->where('stock_transaction.is_delete',0);
                $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
				$result = $this->db->get('stock_transaction')->result();
				$i=1;
				foreach($result as $row):
					$refType = [7,8,23,24,26,27];
					if(!in_array($row->ref_type,$refType))
					{
						$updateData = ['stock_effect'=>1];
						print_r(json_encode($updateData).' *** '.$i++.'<br>');
						$this->db->reset_query();
						//$this->db->where('id',$row->id);
						//$this->db->update('stock_transaction',$updateData);
					}
				endforeach;
				
				if($this->db->trans_status() !== FALSE):
					$this->db->trans_commit();
					echo 'Stock Effect updated successfully.';
				endif;
			}
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
	} */
	
	/* public function migratePartyOpening(){
        try{
            $this->db->trans_begin();

            $this->db->where('opening_balance <>',0);
            $this->db->where('is_delete',0);
            $partyData = $this->db->get("party_master")->result();
            foreach($partyData as $row): 
                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('trans_date <=',"2022-03-31");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();

                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("opening_balance","`opening_balance` - ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Opening Balance Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function updateLedgerClosingBalance(){
        try{
            $this->db->trans_begin();

            $partyData = $this->db->where('is_delete',0)->get("party_master")->result();
            foreach($partyData as $row):
                //Set oprning balance as closing balance
                $this->db->where('id',$row->id);
                $this->db->update('party_master',['cl_balance'=>'opening_balance']);

                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();

                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("cl_balance","`cl_balance` + ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Closing Balance Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */
    
    
    /* public function updateGrn(){
        try{
            $this->db->trans_begin();

            $this->db->select('id');
            $this->db->where('grn_date <','2022-04-01');
            $this->db->where('is_delete',0);
            $result = $this->db->get('grn_master')->result();

            foreach($result as $row):
                $this->db->where('grn_id',$row->id);
                $this->db->where('is_delete',0);
                $this->db->update('grn_transaction',['trans_status'=>1]);

                $this->db->where('id',$row->id)->update('grn_master',['trans_status'=>1]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "GRN Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function updateTransChildInStockTrans(){
        try{
            $this->db->trans_begin();

            $this->db->select('trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date');
            $this->db->join('trans_main','trans_main.id = trans_child.trans_main_id','left');
            $this->db->where_in('trans_child.trans_main_id',[6294,6411,6423]);
            $this->db->where('trans_child.is_delete',0);
            $result = $this->db->get('trans_child')->result();

            foreach($result as $row):
                $this->db->where('ref_id',$row->id);
                $this->db->where('trans_type',2);
                $this->db->where('ref_type',4);
                $this->db->update('stock_transaction',['is_delete'=>1]);

                $location_id = array();$batch_no=array();$batch_qty=array();$rev_no=array();
                $location_id = explode(",",$row->location_id);
                $batch_no = explode(",",$row->batch_no);
                $batch_qty = explode(",",$row->batch_qty);
                $rev_no = explode(",",$row->rev_no);

                foreach($batch_qty as $bk=>$bv):
                    $stockQueryData['location_id']=$location_id[$bk];
                    $stockQueryData['batch_no'] = $batch_no[$bk];
                    $stockQueryData['trans_type']=2;
                    $stockQueryData['item_id']=$row->item_id;
                    $stockQueryData['qty'] = "-".$bv;
                    $stockQueryData['ref_type']=4;
                    $stockQueryData['ref_id']=$row->id;
                    $stockQueryData['ref_no']=getPrefixNumber($row->trans_prefix,$row->trans_no);
                    $stockQueryData['ref_date']=$row->trans_date;
                    $stockQueryData['created_by']=$row->created_by;
                    $this->db->insert('stock_transaction',$stockQueryData);
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Trans Child Item Stock Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function migrateCreditDaysTransMain(){
        try{
            $this->db->trans_begin();
            
            $this->db->select('trans_main.id,party_master.gstin,party_master.credit_days');
            $this->db->join('party_master','party_master.id = trans_main.party_id','LEFT');
			//$this->db->where('trans_main.is_delete',0);
			$invoiceData = $this->db->get("trans_main")->result();
            if(!empty($invoiceData))
            {
                foreach($invoiceData as $row)
                {
                    $updateData = Array();
                    if(!empty($row->gstin))
                    {
                        $updateData['gstin'] = $row->gstin;
                        $updateData['party_state_code'] = substr($row->gstin, 0, 2);
                    }
                    $updateData['credit_period'] = $row->credit_days;
                    //if($row->cm_id==1){$updateData['memo_type'] = 'Cash';}
                    print_r($updateData);print_r('<br>');
                    $this->db->where('id',$row->id);
                    //$this->db->update('trans_main',$updateData);
                }
            }
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Org Price Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    public function migratePaymentTransMain(){
        try{
            $this->db->trans_begin();
            
            $this->db->update('trans_main',['paid_amount'=>0]);
            
            $this->db->where('is_delete',0);
			$this->db->where('ref_id != ','');
			$this->db->where_in('entry_type',[15,16]);
			$paymentData = $this->db->get("trans_main")->result();

            if(!empty($paymentData)):
                foreach($paymentData as $row):                    
                    $this->db->where('id',$row->ref_id);
                    $this->db->where("net_amount != paid_amount");
                    $invData = $this->db->get('trans_main')->row();

                    if(!empty($invData)):
                        $pendingAmount = 0;
                        $pendingAmount = $row->net_amount - $invData->net_amount;
                        $adAmount = 0;
                        if($pendingAmount > 0):
                            $adAmount = $invData->net_amount;
                        elseif($pendingAmount < 0):
                            $adAmount = $row->net_amount;
                        else:
                            $adAmount = $invData->net_amount;
                        endif;

                        $refData = array();
                        $refData[] = ['trans_main_id'=>$row->ref_id,'ad_amount'=>$adAmount];

                        $this->db->where('id',$row->ref_id);
                        $this->db->set('paid_amount','`paid_amount` + '.$adAmount,false);
                        $this->db->update('trans_main');

                        $this->db->where('id',$row->id);
                        $this->db->set('extra_fields',json_encode($refData));
                        $this->db->set('paid_amount','`paid_amount` + '.$adAmount,false);
                        $this->db->update('trans_main');
                    endif;
                endforeach;
            endif;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Payment Adjustment migrated successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function updateTransNumber(){
        try{
            $this->db->trans_begin();

            $this->db->where('is_delete',0);
			$this->db->where_in('entry_type',[15,16]);
			$paymentData = $this->db->get("trans_main")->result();

            foreach($paymentData as $row):
                $this->db->where('trans_main_id',$row->id);
                $this->db->update('trans_ledger',['entry_type'=>$row->entry_type,'trans_date'=>$row->trans_date,'trans_number'=>getPrefixNumber($row->trans_prefix,$row->trans_no),'doc_date'=>$row->doc_date,'doc_no'=>$row->doc_no,'trans_mode'=>$row->trans_mode,'vou_name_s'=>getVoucherNameShort($row->entry_type),'vou_name_l'=>getVoucherNameLong($row->entry_type)]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Payment Trans Number migrated successfully.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /* public function updatePackingTransDispatchQty(){
        try{
            $this->db->trans_begin();

            $this->db->where_in('entry_type',[5,6,7,8]);
            $this->db->where('is_delete',0);
            $this->db->where('rev_no !=','');
            $result = $this->db->get('trans_child')->result();

            $this->db->where('version',2);
            $this->db->update('packing_transaction',['dispatch_qty'=>0]);
            foreach($result as $row):
                $packingIds = explode(",",$row->rev_no);
                $disc_qty = explode(",",$row->batch_qty);

                foreach($packingIds as $key => $ptid):
                    $this->db->where('id',$ptid);
                    $this->db->set('dispatch_qty','`dispatch_qty` + '.$disc_qty[$key],false);
                    $this->db->update('packing_transaction');
                endforeach;
            endforeach;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Packing Dispatch Qty Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */

    /* public function migrateShortCloseJobStockTrans(){
        try{
            $this->db->trans_begin();

            //find manual adjustment [Jayveersinh]
            //SELECT * FROM `stock_transaction` WHERE ref_type = 22 AND batch_no LIKE "%JOB%"
            //SELECT ref_id FROM `stock_transaction` WHERE ref_type = 22 AND batch_no LIKE "%JOB%" GROUP BY ref_id
            //SELECT * FROM `job_card` WHERE id IN (103,104,105,120,153,154,155,158,159,162,164) AND order_status = 6

            $this->db->where('order_status',6);
            $this->db->where('version',2);
            $this->db->where_not_in('id',[105,153,154,155,164]);
            $this->db->where('is_delete',0);
            $result = $this->db->get('job_card')->result();

            foreach($result as $job):
                $this->db->where('job_card_id',$job->id);
                $this->db->where('is_delete',0);
                $approvalData = $this->db->get('production_approval')->result();

                foreach($approvalData as $row):
                    if(!empty($row->in_process_id)):
                        $this->db->where('ref_id',$row->in_process_id);
                        $curentPrsStore = $this->db->get('location_master')->row();

                        $this->db->select('SUM(qty) as qty,batch_no');
                        $this->db->where('location_id',$curentPrsStore->id);
                        $this->db->where('item_id',$job->product_id);
                        $this->db->where('ref_id',$job->id);
                        //$this->db->where_in('ref_type',[23,24,31]);
                        $this->db->group_by('batch_no,location_id,item_id');
                        $this->db->having('SUM(qty) <>',0);
                        $stockData = $this->db->get('stock_transaction')->result();

                        if(!empty($stockData)):
                            foreach($stockData as $stk):
                                    $stockMinusTrans = [
                                        'id' => "",
                                        'location_id' => $curentPrsStore->id,
                                        'batch_no' => $stk->batch_no,
                                        'trans_type' => (($stk->qty < 0)?1:2),
                                        'item_id' => $job->product_id,
                                        'qty' => $stk->qty * -1,
                                        'ref_type' => 32,
                                        'ref_id' => $job->id,
                                        'ref_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                                        'trans_ref_id' => $row->id,
                                        'ref_date' => date("Y-m-d"),
                                        'ref_batch'=>'Short Close Migration'.(($stk->qty < 0)?" < 0":" > 0"),
                                        'created_by' => 0,
                                        'stock_effect' => 0
                                    ];
                                    //print_r($stockMinusTrans);print_r("<hr>");
                                    //$this->db->insert('stock_transaction',$stockMinusTrans);
                            endforeach;
                        endif;
                    endif;
                endforeach;

                ///Remove Stock from Hold area
                $this->db->select('SUM(qty) as qty,batch_no');
                $this->db->where('location_id',211);
                $this->db->where('item_id',$job->product_id);
                $this->db->where('ref_id',$job->id);
                //$this->db->where('ref_type',23);
                $this->db->where('is_delete',0);
                $this->db->having('SUM(qty) <>',0);
                $stockHldData = $this->db->get('stock_transaction')->row();

                if(!empty($stockHldData)):
                    $stockMinusTrans = [
                        'id' => "",
                        'location_id' => 211,
                        'batch_no' => $stockHldData->batch_no,
                        'trans_type' => (($stockHldData->qty < 0)?1:2),
                        'item_id' => $job->product_id,
                        'qty' => $stockHldData->qty * -1,
                        'ref_type' => 32,
                        'ref_id' => $job->id,
                        'ref_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                        'ref_date' => date("Y-m-d"),
                        'ref_batch'=>'Short Close Migration'.(($stockHldData->qty < 0)?" < 0":" > 0"),
                        'created_by' => 0,
                        'stock_effect' => 0
                    ];
                    //$this->db->insert('stock_transaction',$stockMinusTrans);
                endif;
            endforeach; 
                      
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Short Close Job Stock trans Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */
    
    /* public function migrateShortCloseJobPacking(){
        try{
            $this->db->trans_begin();

            $this->db->where('order_status',6);
            $this->db->where('version',2);
            $this->db->where_not_in('id',[105,153,154,155,164]);
            $this->db->where('is_delete',0);
            $result = $this->db->get('job_card')->result();

            foreach($result as $job):
                /* $this->db->where('location_id',136);
                $this->db->where('item_id',$job->product_id);
                $this->db->where('ref_id',$job->id);
                $this->db->where('ref_type',7);
                $this->db->where('is_delete',0);
                //$this->db->update('stock_transaction',['is_delete'=>1,'ref_batch'=>'Short Close Migration 136']);
                
                $stockHldData = $this->db->get('stock_transaction')->row();
                print_r($stockHldData);print_r("<hr>"); */

                /*echo $job->id.', ';
                
            endforeach; 
                      
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Short Close Migration [location_id = 136] Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    } */
    
    public function migrateRejectionToScrap(){
        try{
            $this->db->trans_begin();
    
            $this->db->select('job_card.id as job_card_id,job_card.job_no,job_card.job_prefix,job_card.product_id');
            $this->db->join('job_card','stock_transaction.ref_id = job_card.id','left');
            $this->db->where('stock_transaction.ref_type',24);
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->group_by('stock_transaction.ref_id');            
            $jobCard = $this->db->get('stock_transaction')->result();

            foreach($jobCard as $job):
                $this->db->select('SUM(stock_transaction.qty) as qty,stock_transaction.trans_ref_id,stock_transaction.batch_no,stock_transaction.ref_no,location_master.location,stock_transaction.location_id,stock_transaction.item_id,stock_transaction.ref_id,item_master.item_name,production_log.*');
                $this->db->join('location_master','stock_transaction.location_id = location_master.id','left');
                $this->db->join('item_master','stock_transaction.item_id = item_master.id','left');
                $this->db->join('production_log','production_log.id = stock_transaction.trans_ref_id','left');
                $this->db->where('stock_transaction.ref_id',$job->job_card_id);
                $this->db->where('stock_transaction.ref_type',24);
                $this->db->where('stock_transaction.is_delete',0);
                $this->db->where('stock_transaction.trans_ref_id >',0);
                $this->db->having('SUM(stock_transaction.qty) > 0');
                $this->db->group_by('stock_transaction.trans_ref_id');
                $stockTrans = $this->db->get('stock_transaction')->result();

                foreach($stockTrans as $st):
                    $this->db->where('log_id',$st->trans_ref_id);
                    $this->db->where('manag_type',1);
                    $this->db->where('is_delete',0);
                    $logData = $this->db->get('rej_rw_management')->result();

                    $transData=array();
                    foreach($logData as $log):
                        $this->db->select('SUM(scrap_book_trans.scrap_qty) as qty');
                        $this->db->where('is_delete',0);
                        $this->db->where('rej_log_id',$log->id);
                        $scrapData = $this->db->get('scrap_book_trans')->row();

                        $pending_qty=0;
                        $pending_qty=($log->qty - $scrapData->qty);
                        if ($pending_qty > 0):
                            $this->db->where('id',$st->id);
                            $logSheetData = $this->db->get('production_log')->row();

                            $this->db->where('is_delete',0);
                            $this->db->where('in_process_id',$logSheetData->process_id);
                            $this->db->where('job_card_id',$job->job_card_id);
                            $approveData = $this->db->get('production_approval')->row();

                            $transData[] = [
                                'log_id' => $st->id,
                                'rej_log_id' => $log->id,
                                'scrap_qty' => $pending_qty,
                                'ok_qty' => 0,
                                'rej_reason' => $log->reason,
                                'rej_stage' => $log->belongs_to_name,
                                'rej_from' => $log->vendor_id,
                                'wp_qty' => $approveData->finished_weight,
                                'location_id' => $st->location_id,
                                'ref_no' => $st->ref_no,
                                'ref_id' => $st->ref_id,
                            ];
                            print_r($transData);
                        endif;
                    endforeach;  
                endforeach;
                
                /* if(!empty($transData)):
                    $masterData = array();
                    $masterData = [
                        'trans_date' => date("Y-m-d"),
                        'job_card_id' => $job->job_card_id,
                        'item_id' => $job->product_id,
                        'scrap_qty' => array_sum(array_column($transData,'scrap_qty')),
                        'ok_qty' => 0,
                        'created_by' => 9999
                    ];
                    $this->db->insert('scrap_book',$masterData);
                    $masterId = $this->db->insert_id();
                    
                    $scrapQty=0;$location_id = 0;$ref_no = "";$ref_id = 0;
                    foreach($transData as $row):
                        $row['scrap_id'] = $masterId;
                        $location_id = $row['location_id'];$ref_no = $row['ref_no'];$ref_id = $row['ref_id'];
                        unset($row['location_id'],$row['ref_no'],$row['ref_id']);
                        $this->db->insert('scrap_book_trans',$row);

                        if(!empty($row['scrap_qty'])):
                            $stockMinusTrans = array();
                            $stockMinusTrans = [
                                'location_id' => $location_id,
                                'batch_no' => getPrefixNumber($job->job_prefix, $job->job_no) . '-R',
                                'trans_type' => 2,
                                'item_id' => $job->product_id,
                                'qty' =>  "-" . $row['scrap_qty'],
                                'ref_type' => 24,
                                'ref_no' => $ref_no,
                                'ref_id' => $ref_id,
                                'trans_ref_id' => $row['log_id'],
                                'ref_date' => date("Y-m-d"),
                                'created_by' => 9999,
                                'stock_effect'=>0
                            ];
                            $this->db->insert('stock_transaction',$stockMinusTrans);
                            $scrapQty+=$row['scrap_qty'] * $row['wp_qty'];
                        endif;
                    endforeach;

                    if(!empty($scrapQty)):
                        $this->db->select('job_bom.*,item_master.item_name,item_master.item_type,item_master.qty as stock_qty,material_master.scrap_group,material_master.material_grade');
                        $this->db->join('item_master','job_bom.ref_item_id = item_master.id','left');
                        $this->db->join('material_master','material_master.material_grade = item_master.material_grade','left');
                        $this->db->where('job_bom.item_id',$job->product_id);
                        $this->db->where('job_bom.job_card_id',$job->job_card_id);
                        $this->db->where('job_bom.is_delete',0);
                        $kitData = $this->db->get('job_bom')->row();

                        $stockPlusTrans = [
                            'id' => "",
                            'location_id' => 134,
                            'batch_no' => getPrefixNumber($job->job_prefix, $job->job_no),
                            'trans_type' => 1,
                            'item_id' => $kitData->scrap_group,
                            'qty'=>$scrapQty,
                            'ref_type' => 25,
                            'ref_no' => $kitData->ref_item_id,
                            'ref_batch' => $kitData->material_grade,
                            'ref_id' => $job->job_card_id,
                            'trans_ref_id' => $masterId,
                            'ref_date' => date("Y-m-d"),
                            'created_by' => 9999
                        ];
                        $this->db->insert('stock_transaction',$stockPlusTrans);
                    endif;
                endif; */
            endforeach;exit;

            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                $this->db->trans_rollback();
                echo "Rejection to Scrap Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function addEmployeeInDevice(){
        try{
            $this->db->trans_begin();
            
            $this->db->select('employee_master.id,employee_master.emp_code,employee_master.emp_name');
            //$this->db->where('is_delete',0);
            //$this->db->where('emp_role != ',-1); 
            $this->db->where('emp_code',20345);
            //$this->db->where('attendance_status',1);   
            //$this->db->order_by('emp_code','ASC');  
            //$this->db->limit(20, 80);
            $empList = $this->db->get('employee_master')->result();
    		$i=0;
            foreach($empList as $empData):
                if(!empty($empData->emp_code)):
                    $this->db->where('id',1);
                    $deviceData = $this->db->get('device_master')->row();
                    
                    $empCode = $deviceData->Empcode = trim($empData->emp_code);
                    $empName = $deviceData->emp_name = str_replace(" ","%20",$empData->emp_name);
                    
                    print_r($deviceData);print_r('<hr>');
                    
                    $deviceResponse = $this->biometric->addEmpDevice($deviceData);
                    
                    if($deviceResponse['status'] == 0):
                        print_r('cURL Error #: ' . $deviceResponse['result']);
                    else:
                        $responseData = json_decode($deviceResponse['result']);
                       
                        if(!empty($responseData)):
                            if($responseData->Error == false):
                                $i++;
                            else:
                               print_r('cURL Error #: ' . $deviceResponse['result']);
                            endif;
                        else:
                            print_r('cURL Error #: ' . $deviceResponse['result']);
                        endif;
                    endif;
                endif;
            endforeach;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                //$this->db->trans_rollback();
                echo $i." Employee Added Successfully Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    // Add New Material Specification from Material Grade
    public function insertMatSpecsByGrade(){
        try{
            $this->db->trans_begin();
            
            $this->db->where('id > ',37);
            $gradeMaster = $this->db->get('material_master')->result();
    		$i=0;
            foreach($gradeMaster as $row):
                $grade_id = $row->id;
                $chemical_name  = ["C%","Mn%","P%","S%","Si%","Cr%","Ni%","Mo%","N%","Other"];
                $mechanical_name  = ["TS(Mpa)","YS(Mpa)","Elong(%)","RA (%)"];
                $this->db->reset_query();
                foreach($chemical_name as $key=>$value):
                    $chemicalData = Array();
                    $chemicalData = [
                        'id' => '',
                        'grade_id' => $grade_id,
                        'spec_type' => 1,
                        'param_name' => $value,
                        'created_by' => 1,
                        'created_at' => '2022-09-29 12:27:35',
                        'updated_by' => 1,
                        'updated_at' => '2022-09-29 12:27:35'
                    ];
                    $this->db->insert('material_specification',$chemicalData);
                endforeach;

                foreach($mechanical_name as $key=>$value):
                    $mechanicalData = Array();
                    $mechanicalData = [
                        'id' => '',
                        'grade_id' => $grade_id,
                        'spec_type' => 2,
                        'param_name' => $value,
                        'created_by' => 1,
                        'created_at' => '2022-09-29 12:27:35',
                        'updated_by' => 1,
                        'updated_at' => '2022-09-29 12:27:35'
                    ];
                    $this->db->insert('material_specification',$mechanicalData);
                endforeach;
                $hardnessData = [
                    'id' => '',
                    'grade_id' => $grade_id,
                    'spec_type' => 6,
                    'param_name' => 'Hardness (BHN)',
                    'created_by' => 1,
                    'created_at' => '2022-09-29 12:27:35',
                    'updated_by' => 1,
                    'updated_at' => '2022-09-29 12:27:35'
                ];
                $this->db->insert('material_specification',$hardnessData);
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                $this->db->trans_rollback();
                echo $i." Specification Added Successfully Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    /*** Created By Jp@10-09-2022 update Old Stock to zero  Migration/toolStockUpdate ***/
    public function toolStockUpdate(){
        try{
            $this->db->trans_begin();
            
			$i=0;
		    $this->db->reset_query();
		    $this->db->select("SUM(stock_transaction.qty) as qty,stock_transaction.location_id,stock_transaction.batch_no,stock_transaction.item_id");
            $this->db->join('rmstock','rmstock.item_id = stock_transaction.item_id');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('rmstock.qty >',0);
			$this->db->where('rmstock.item_id !=',0);
			$this->db->where_in('stock_transaction.location_id NOT',[7,8]);
            $this->db->group_by('stock_transaction.item_id');
            $this->db->group_by('stock_transaction.location_id');
            $this->db->group_by('stock_transaction.batch_no');
            $stockData = $this->db->get('stock_transaction')->result();
            
            foreach($stockData as $row):
                //update Old stock
                if($row->qty != 0):
                    $stockTrans=array();
    				$trans_type = 0;$stock_qty=0;
    				if($row->qty > 0){$trans_type = 2;$stock_qty = ($row->qty * -1);}
    				if($row->qty < 0){$trans_type = 1;$stock_qty = abs($row->qty);}
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => $trans_type,
    					'item_id' => $row->item_id,
    					'qty' => $stock_qty,
    					'remark' => 'STOCK_ADJUST_BY_NYN',
    					'ref_type' => 999,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>'); $i++;
    				$this->db->reset_query();
    				$this->db->insert("stock_transaction",$stockTrans);
                endif;
		    endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }
    
    /*** Created By Jp@10-09-2022 Update current stock Migration/toolStockUpdateOP***/
    public function toolStockUpdateOP(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
			$this->db->where('qty > ',0);
			$this->db->where('item_id !=',0);
            $result = $this->db->get('rmstock')->result();
			//print_r($this->db->last_query());exit;
            
			$i=0;
			foreach($result as $row):
			    
                /*$this->db->reset_query();
			    $this->db->where('is_delete',0);
			    $this->db->where('item_type',2);
                $this->db->where('item_name',trim($row->item_name));
                $itemData = $this->db->get('item_master')->row();
                
                if(!empty($itemData)):
                     $this->db->reset_query();
                     $this->db->where('id',$row->id);
                     $this->db->update('tool_stock',['item_id'=> $itemData->id]);
                 endif;*/
                
                $this->db->reset_query();
                if($row->qty != 0):
                    $stockTrans=array();
    				$stockTrans = [
    					'location_id' => $row->location_id,
    					'batch_no' => $row->batch_no,
    					'trans_type' => 1,
    					'item_id' => $row->item_id,
    					'qty' => $row->qty,
    					'remark' => 'STOCK_OP_BY_NYN',
    					'ref_type' => -1,
    					'ref_date' => date('Y-m-d')
    				];	$i++;
    				print_r($stockTrans);print_r('<hr>');
    				$this->db->insert("stock_transaction",$stockTrans);
                endif;
			    
				
			endforeach;
            //exit;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo 'RM Stock updated successfully. '.$i.' Recored updated';
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        } 
    }


    /************************************************************************************** */
    /****Heat No wise Production */

    /***Rmove Qty from Allocation RM AND Receive RM */
    public function removeAllocatedNReceiveMaterial(){
        try{
            $this->db->trans_begin();

                $this->db->where_in('ref_type',[20,21]);
                $this->db->where('is_delete',0);
                $result = $this->db->update('stock_transaction',['is_delete'=>1,'Remark'=>'HeatMigration#31052023']);
                print_r($this->db->last_query());          
                print_r($result);          
            
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Stock Removed Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /*** Migrate Heat No */
    public function migrateJobHeatNo(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            /*** JSelect JOB BOM Data */
            $this->db->select('job_bom.*,job_card.qty as job_qty');
            $this->db->where('job_bom.is_delete',0);
            // $this->db->where('job_bom.id >',0);
            // $this->db->where('job_bom.id <=',60);
            // $this->db->where('job_bom.id >',60);
            // $this->db->where('job_bom.id <=',120);
            // $this->db->where('job_bom.id >',120);
            $this->db->join('job_card','job_card.id = job_bom.job_card_id','left');
            $jobBomData = $this->db->get('job_bom')->result();

            foreach($jobBomData as $row){
                $this->db->reset_query();
                /*** Select Stock Data */
                $this->db->select('ABS(SUM(qty)) as qty,stock_transaction.batch_no,stock_transaction.tc_no');
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',3);
                $this->db->where('ref_id',$row->job_card_id);
                $stockData = $this->db->get('stock_transaction')->row();

                /*** Update Job Bom id in stock transaction */
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',3);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->update('stock_transaction',['trans_ref_id'=>$row->id]);

                /** Select job approval data */
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('trans_type',1);
                $this->db->where('job_card_id',$row->job_card_id);
                $approvalData = $this->db->get('job_approval')->result();
                $i=0;$usedQty = 0;
                /*** Insert Data in job_heat_trans table */
                foreach($approvalData as $aprv){
                    $this->db->reset_query();

                    $heatData = [
                        'id' =>'',
                        'job_card_id'=>$aprv->job_card_id,
                        'job_approval_id'=>$aprv->id,
                        'batch_no' => $stockData->batch_no,
                        'tc_no' => $stockData->tc_no,
                        'in_qty' => ($i == 0) ?abs($stockData->qty) : ($aprv->in_qty),
                        'out_qty' => ($i==0)?($aprv->total_out_qty*$row->qty):$aprv->total_out_qty,
                    ];
                    $this->db->insert('job_heat_trans',$heatData);
                    if($i == 0){
                        $usedQty = $aprv->total_out_qty*$row->qty;
                    }
                    $i++;
                }

                /** Update required,Issue,Received & Used Qty in job bom table */
                $required_qty = $row->job_qty*$row->qty;
                $this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('job_bom',['req_qty'=>$required_qty,'issue_qty'=>$stockData->qty,'received_qty'=>$stockData->qty,'used_qty'=>$usedQty]);
            }
               
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateJobHeatINJobTrans(){
        try{
            $this->db->trans_begin();

            /**** Select jobcard data */
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $jobData = $this->db->get('job_card')->result();

            foreach($jobData as $row){
                /***** Select Stock data */
                $this->db->reset_query();
                $this->db->select('ABS(SUM(qty)) as qty,stock_transaction.batch_no,stock_transaction.tc_no');
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',3);
                $this->db->where('ref_id',$row->id);
                $stockData = $this->db->get('stock_transaction')->row();

                /**** Update Batch  No */
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('job_card_id',$row->id);
                $this->db->where('entry_type',6);
                $this->db->update('job_transaction',['batch_no'=>$stockData->batch_no]);
            }
               
        
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateFinishProduction(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $jobData = $this->db->get('job_card')->result();

            foreach($jobData as $row){
                $this->db->reset_query();
                $this->db->select('ABS(SUM(qty)) as qty,stock_transaction.batch_no,stock_transaction.tc_no');
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',3);
                $this->db->where('ref_id',$row->id);
                $stockData = $this->db->get('stock_transaction')->row();

                /*** Update Tc no & ref_type = 7 in stock Transaction */
                $updatArray = ['tc_no'=>$stockData->batch_no.(!empty($stockData->tc_no)?'~'.$stockData->tc_no:'')];
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('batch_no',$row->job_number);
                $this->db->where_in('ref_type',[7,4,5]);
                $this->db->update('stock_transaction',$updatArray);
            }
               
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateChQty(){
        try{
            $this->db->trans_begin();

            /**** Select jobcard data */
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where('send_to',1);
            $this->db->where('entry_type',6);
            $this->db->where('outsource_qty > ',0);
            $jobData = $this->db->get('job_transaction')->result();
            $i=1;
            foreach($jobData as $row){
                /***** Select Stock data */
                $this->db->reset_query();
                $this->db->set('ch_qty','ch_qty + '.$row->outsource_qty,false);
                $this->db->where('in_process_id',$row->process_id);
                $this->db->where('job_card_id',$row->job_card_id);
                $this->db->where('is_delete',0);
                $this->db->update('job_approval');
                print_r($this->db->last_query()."<br>");
                $i++;
            }
               
        
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateSysTCNo(){
        try{
            $this->db->trans_begin();

            /**** Select TC INspection data */
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where("ref_tc_no !=''");
            $this->db->where("ref_tc_no IS NOT NULL");
            $this->db->group_by('ref_tc_no');
            $this->db->order_by('id','ASC');
            $grnData = $this->db->get('tc_inspection')->result();
            $i=1;
            foreach($grnData as $row){
                /***** Select MAX TC data */
                $this->db->reset_query();
                $this->db->select( "MAX(tc_no) as maxTcNo");
                $this->db->where('is_delete',0);
                $this->db->where("ref_tc_no !=''");
                $this->db->where("ref_tc_no IS NOT NULL");
                $maxTcData = $this->db->get('tc_inspection')->row();
                $maxTcNo = (!empty($maxTcData->maxTcNo)) ? ($maxTcData->maxTcNo + 1) : 1;
                // print_r($maxTcData);
                // print_r('<br>');

                $this->db->reset_query();
                $this->db->where('ref_tc_no',$row->ref_tc_no);
                $this->db->update('tc_inspection',['tc_no'=>$maxTcNo,'tc_prefix'=>'STC']);

                $i++;
            }
               
        
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateGRNSysTCNo(){
        try{
            $this->db->trans_begin();

            /**** Select TC INspection data */
            $this->db->reset_query();
            $this->db->where('is_delete',0);
            $this->db->where("tc_no > 0 ");
            $grnData = $this->db->get('tc_inspection')->result();
            $i=1;
            foreach($grnData as $row){
               
                $this->db->reset_query();
                $this->db->where('id',$row->grn_trans_id);
                $this->db->update('grn_transaction',['sys_tc_no'=>'STC'.$row->tc_no]);
                $i++;
            }
               
        
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

   
    /**************************************** */
    /*** Re Migrate TC No in job_heat Trans , Job_transaction & stock transaction table - 05-06-2023 */
    public function migrateStockTCNo(){
        try{
            $this->db->trans_begin();

            /**** Select jobcard data */
            $this->db->reset_query();
            $this->db->select('grn_transaction.*,grn_master.party_id');
            $this->db->where('grn_transaction.is_delete',0);
            $this->db->where('grn_transaction.item_type',3);
            // $this->db->where("sys_tc_no !=''");
            // $this->db->where("sys_tc_no IS  NULL");
            $this->db->join('grn_master','grn_master.id = grn_transaction.grn_id','left');
            $grnData = $this->db->get('grn_transaction')->result();
            $i=1;
            
            // exit;
            foreach($grnData as $row){
            /***** Update TC No [batch_no~tc_no] data */
                $tc_no = $row->batch_no.'~'.$row->sys_tc_no;
                $this->db->reset_query();
                $this->db->where('batch_no',$row->batch_no);
                $this->db->where('item_id',$row->item_id);
                $this->db->update('stock_transaction',['tc_no'=>$tc_no]);
                print_r($this->db->last_query()."<br>");
                $i++;
            }
               
        
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function reMigrateJobHeatNo(){
        try{
            $this->db->trans_begin();

            $this->db->reset_query();
            /*** Select Job Heat Trans Data */
            $this->db->where('job_heat_trans.is_delete',0);
            // $this->db->where('job_heat_trans.id >',0);
            // $this->db->where('job_heat_trans.id <=',300);
            // $this->db->where('job_heat_trans.id >',300);
            // $this->db->where('job_heat_trans.id <=',600);
            // $this->db->where('job_heat_trans.id >',600);
            // $this->db->where('job_heat_trans.id <=',900);
            // $this->db->where('job_heat_trans.id >',900);
            // $this->db->where('job_heat_trans.id <=',1200);
            // $this->db->where('job_heat_trans.id >',1200);
            // $this->db->where('job_heat_trans.id <=',1500);
            // $this->db->where('job_heat_trans.id >',1500);
            // $this->db->where('job_heat_trans.id <=',1800);
            // $this->db->where('job_heat_trans.id >',1800);
            $heatData = $this->db->get('job_heat_trans')->result();

            foreach($heatData as $row){
                $this->db->reset_query();
                /*** Select Stock Data */
                $this->db->select('ABS(SUM(qty)) as qty,stock_transaction.batch_no,stock_transaction.tc_no');
                $this->db->where('is_delete',0);
                $this->db->where('ref_type',3);
                $this->db->where('ref_id',$row->job_card_id);
                $this->db->where('batch_no',$row->batch_no);
                $stockData = $this->db->get('stock_transaction')->row();

                /*** Update TC No in job_heat_trans table */
                $this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('job_heat_trans',['tc_no'=>$stockData->tc_no]);
                print_r($this->db->last_query()."<br>");
               
                /*** Update batch No in job_Transaction table */
                $this->db->reset_query();
                $this->db->where('job_approval_id',$row->job_approval_id);
                $this->db->where('batch_no',$row->batch_no);
                $this->db->update('job_transaction',['batch_no'=>$stockData->tc_no]);
                print_r($this->db->last_query()."<br>");

            }
               
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function reMigrateFinishProduction(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('SUM(qty) as qty,stock_transaction.batch_no,stock_transaction.tc_no');
			$this->db->where('is_delete',0);
			$this->db->where('ref_type',7);
			$this->db->group_by('batch_no');
			$this->db->group_by('tc_no');
			$stockData = $this->db->get('stock_transaction')->result();
			$i=0;
            foreach($stockData as $row){ $i++;
                /*** Update Tc no & ref_type = 7 in stock Transaction */
                $updatArray = ['tc_no'=>$row->tc_no];
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('batch_no',$row->batch_no);
                $this->db->where_in('ref_type',[4,5]);
                //$this->db->update('stock_transaction',$updatArray);
				print_r($updatArray); print_r($i.'<hr><br>');
            }
            exit;  
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateOpeningTc(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->where('stock_transaction.ref_type',-1);
            $this->db->where('tc_no IS NULL');
            // $this->db->where('item_master.item_type',3);
            // $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
            $opStockData = $this->db->get('stock_transaction')->result();
            // print_r($this->db->last_query());
            $i=0;
            foreach($opStockData as $row){
                $this->db->reset_query();
                $this->db->where('id',$row->id);
                $this->db->update('stock_transaction',['tc_no'=>$row->batch_no.'~OPTC']);
                // print_r($this->db->last_query());
                $i++;
            }
               
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migrateOpeningTcNOEffect(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
            $this->db->where('stock_transaction.is_delete',0);
            $this->db->where('stock_transaction.ref_type',-1);
            $this->db->where('tc_no IS NOT NULL');
            $this->db->where('item_master.item_type',3);
            $this->db->join('item_master','item_master.id = stock_transaction.item_id','left');
            $opStockData = $this->db->get('stock_transaction')->result();

            foreach($opStockData as $row){
                $this->db->reset_query();
                $this->db->where('batch_no',$row->batch_no);
                $this->db->where('tc_no IS NULL');
                $this->db->update('stock_transaction',['tc_no'=>$row->tc_no]);
            }
               
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //$this->db->trans_rollback();
                echo "Migrated Successfully";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /* NYN Migration/migrateItemCode */
    public function migrateItemCode(){
        try{
            $this->db->trans_begin();
            
            $itemData = $this->db->query("SELECT `qc_instruments`.*,`item_category`.`category_code`,`item_category`.`category_name` FROM `qc_instruments` LEFT JOIN `item_category` ON `item_category`.`id` = `qc_instruments`.`category_id`")->result();
            
            $i=1;
            foreach($itemData as $row):
                $updateData = ['item_code'=>$row->category_code.(sprintf('%03d',$row->serial_no)), 'item_name'=>$row->category_code.(sprintf('%03d',$row->serial_no)).' '.$row->category_name.' '.$row->size];
                $this->db->where('id',$row->id);
                $this->db->update("qc_instruments",$updateData);
				//print_r($updateData);print_r('<hr>');
                $i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Name Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	/* NYN Migration/migrateDueDate */
    public function migrateDueDate(){
        try{
            $this->db->trans_begin();
            
            $this->db->reset_query();
            $itemData = $this->db->get('qc_instruments')->result();
            
            $i=1;
            foreach($itemData as $row):
                $next_cal_date = date('Y-m-d', strtotime($row->last_cal_date . "+".$row->cal_freq." months"));
            
                $updateData = [
                    'next_cal_date'=>$next_cal_date
                ];
            
                $this->db->reset_query();
				$this->db->where('id',$row->id);
                //$this->db->update('qc_instruments',$updateData);
                //print_r($this->db->last_query()); print_r("<hr>");
                $i++;
            endforeach;
            exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Instrument Category Migration Success.".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	/* NYN Migration/rmItemSize */
    public function rmItemSize(){
        try{
            $this->db->trans_begin();
			
			$this->db->reset_query();
			$this->db->where('is_delete',0);
			$this->db->where('item_type',3);
			$itemsData = $this->db->get('item_master')->result();
			$i=1;
            foreach($itemsData as $row):
                $itemName = explode('☻',$row->item_image);
				$size = (!empty($itemName[0]))?$itemName[0]:NULL;
				$this->db->where('id',$row->id);
				//$this->db->update("item_master",['size'=>$size]);
				print_r(['size'=>$size]);print_r('<hr>');
				$i++;
            endforeach;
            exit;
            if ($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Updated ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    /***************************************/
    /***** Migrate MAteril Grade in stock transaction */
	//Migration/migrateMaterialGradeFG
    public function migrateMaterialGradeFG(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('stock_transaction.batch_no,stock_transaction.ref_id,job_card.material_grade');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('stock_transaction.ref_type',7);
            $this->db->join('job_card','job_card.id = stock_transaction.ref_id AND job_card.is_delete = 0','left');
            // $this->db->join('item_master','item_master.id = job_bom.ref_item_id','left');
			$this->db->group_by('batch_no');
			$stockData = $this->db->get('stock_transaction')->result();
			$i=0;
            foreach($stockData as $row){ $i++;
                /*** Update Tc no & ref_type = 7 in stock Transaction */
                $updatArray = ['ref_batch'=>$row->material_grade];
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('batch_no',$row->batch_no);
                $this->db->where_in('ref_type',[7,4,5]);
                //$this->db->update('stock_transaction',$updatArray);
				// print_r($updatArray); print_r($i.'<hr><br>');
                $i++;
            }
            exit;  
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                //echo "Migrated Successfully records : ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	//Migration/migrateMaterialGradeInJob
    public function migrateMaterialGradeInJob(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('job_bom.*,item_master.material_grade');
			$this->db->where('job_bom.is_delete',0);
            $this->db->join('item_master','job_bom.ref_item_id = item_master.id','left');
			$bomData = $this->db->get('job_bom')->result();
			$i=0;
            foreach($bomData as $row){ 
                /*** Update Tc no & ref_type = 7 in stock Transaction */
                $updatArray = ['material_grade'=>$row->material_grade];
                
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('id',$row->job_card_id);
                //$this->db->update('job_card',['material_grade'=>$row->material_grade]);
				print_r($this->db->last_query()); print_r($i.'<hr><br>');
                $i++;
            }
            exit;  
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Migrated Successfully records : ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }


    // Migrate Total Ok Qty in job_card table
    public function migrateJobOkQty(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('SUM(stock_transaction.qty) as total_ok_qty,ref_id');
			$this->db->where('stock_transaction.is_delete',0);
			$this->db->where('stock_transaction.ref_type',7);
            $this->db->join('job_card','job_card.id = stock_transaction.ref_id AND job_card.is_delete = 0','left');
			$this->db->group_by('batch_no');
			$stockData = $this->db->get('stock_transaction')->result();
			$i=0;
            foreach($stockData as $row){ 
             
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('id',$row->ref_id);
                $this->db->update('job_card',['total_out_qty'=>$row->total_ok_qty]);
				// print_r($this->db->last_query()); print_r('  -'.$i.'<hr><br>');
                $i++;
            }
            // exit;  
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                echo "Migrated Successfully records : ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }


    // Migrate Total Rej Qty in job_card table
    public function migrateJobRejQty(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('SUM(rej_rw_management.qty) as total_rej_qty,job_card_id');
			$this->db->where('rej_rw_management.is_delete',0);
			$this->db->where('rej_rw_management.entry_type',3);
			$this->db->where('rej_rw_management.operation_type',1);
			$this->db->group_by('job_card_id');
			$rejData = $this->db->get('rej_rw_management')->result();
			$i=0;
            foreach($rejData as $row){ 
             
                $this->db->reset_query();
                $this->db->where('is_delete',0);
                $this->db->where('id',$row->job_card_id);
                $this->db->update('job_card',['total_rej_qty'=>$row->total_rej_qty]);
				// print_r($this->db->last_query()); print_r('   '.$i.'<hr><br>');
                $i++;
            }
            exit;  
            if($this->db->trans_status() !== FALSE):
                // $this->db->trans_commit();
                echo "Migrated Successfully records : ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }


    // Migrate Order Status Migration/migrateOrderStatus
    public function migrateOrderStatus(){
        try{
            $this->db->trans_begin();
            /*** Select Job Data */
            $this->db->reset_query();
			$this->db->select('id,trans_status');
			$this->db->where('entry_type',4);
			$this->db->where('is_delete',0);
			//$this->db->where('trans_status',0);
			$mainData = $this->db->get('trans_main')->result();
			$i=0;
            foreach($mainData as $main){ 
			    /*** Select Stock Data */
				$this->db->reset_query();
				$this->db->select('COUNT(trans_status) as orderStatus');
                $this->db->where('is_delete',0);
                $this->db->where('entry_type',4);
                $this->db->where('trans_status',0);
                $this->db->where('trans_main_id',$main->id);
				$this->db->group_by('trans_main_id');
                $childData = $this->db->get('trans_child')->row();
             
				$pendingItems = (!empty($childData->orderStatus))?$childData->orderStatus:0;
				if(empty($pendingItems)):
					$this->db->reset_query();
					$this->db->where('id',$main->id);
					//$this->db->update('trans_main',['trans_status'=>1]);
				else:
					$this->db->reset_query();
					$this->db->where('id',$main->id);
					//$this->db->update('trans_main',['trans_status'=>0]);
				endif;
				
				print_r($this->db->last_query()); print_r('   '.$i.'<hr><br>');
				$i++;
            }
            exit;  
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Migrated Successfully records : ".$i;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
}
?>