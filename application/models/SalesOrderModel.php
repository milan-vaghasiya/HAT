<?php
class SalesOrderModel extends MasterModel{

    private $orderMaster = "sales_order";
    private $orderTrans = "sales_order_trans";
    private $salesQuotation = "sales_quotation";
    private $salesQuotationTrans = "sales_quote_transaction";
    private $itemMaster = "item_master";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_code,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.is_approve,trans_main.approve_date,trans_main.doc_no,trans_main.ref_id,trans_main.from_entry_type,trans_child.close_reason,trans_child.grn_data as wo_no';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 4;
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}

        if($data['status'] == 1) { 
            $data['where']['trans_child.trans_status'] = 1; 
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        } 
        elseif($data['status'] == 2) { 
            $data['where']['trans_child.trans_status'] = 2; 
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        }
        else { $data['where']['trans_child.trans_status'] = 0; }

        //$data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.trans_no'] = "DESC";
        //$data['order_by']['trans_main.is_approve'] = "ASC";

        $data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_child.grn_data";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.qty";
        $data['searchCol'][] = "trans_child.dispatch_qty";
        $data['searchCol'][] = "DATE_FORMAT(trans_child.cod_date,'%d-%m-%Y')";
		
		$columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','trans_main.doc_no','','trans_child.grn_data','trans_child.item_name','trans_child.qty','trans_child.dispatch_qty', '','trans_child.cod_date');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    } 
    
    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            $id = $masterData['id'];
			$created_by = $masterData['created_by'];
            
			// Get Latest Trans Number
			if(empty($id)):
				$masterData['trans_prefix'] = $this->transModel->getTransPrefix(4);
				$masterData['trans_no'] = $this->transModel->nextTransNo(4);
				$masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
			endif;
			
            if ($this->checkDuplicateOrder($masterData['party_id'], $masterData['trans_prefix'], $masterData['trans_no'], $id) > 0) :
                $errorMessage['so_no'] = "SO. No. is duplicate.";
                return ['status' => 0, 'message' => $errorMessage];
            else :
                if(empty($id)):  
                    $salesOrderSave = $this->store($this->transMain, $masterData);
                    $orderId = $salesOrderSave['insert_id'];                
            
                    $result = ['status' => 1, 'message' => 'Sales Order saved successfully.','url'=>base_url("salesOrder")];
                else:
                    $orderId = $id;
                    $salesOrderTrans = $this->getSalesOrderTransactions($id);
                    $masterData['is_approve']=0;
                    $masterData['approve_date']=NULL;
					$masterData['updated_by']=$this->loginID;
					$masterData['updated_at']=date('Y-m-d');
					unset($masterData['created_by']);
                    $this->store($this->transMain, $masterData);               
                    
                    foreach ($salesOrderTrans as $row) :
                        if (!in_array($row->id, $itemData['id'])):
                            $this->trash($this->transChild,['id'=> $row->id]);
                        endif;
                    endforeach;               
            
                    $result = ['status' => 1, 'message' => 'Sales Order updated successfully.','url'=>base_url("salesOrder")];
                endif;

                foreach($itemData['item_id'] as $key => $value) :
                    $transData = [
                        'id'=>$itemData['id'][$key],
                        'trans_main_id' => $orderId,
                        'entry_type' => $masterData['entry_type'],
                        'currency' => $masterData['currency'],
                        'inrrate' => $masterData['inrrate'],
                        'from_entry_type' => $itemData['from_entry_type'][$key],
                        'ref_id' => $itemData['ref_id'][$key],
                        'item_id' => $value,
                        'item_name' => $itemData['item_name'][$key],
                        'item_type' => $itemData['item_type'][$key],
                        'item_code' => $itemData['item_code'][$key],
                        'item_desc' => $itemData['item_desc'][$key],
                        'item_alias' => $itemData['item_alias'][$key],
                        'material_grade' => $itemData['material_grade'][$key],
                        'unit_id' => $itemData['unit_id'][$key],
                        'unit_name' => $itemData['unit_name'][$key],
                        'hsn_code' => $itemData['hsn_code'][$key],
                        'drg_rev_no' => $itemData['drg_rev_no'][$key],
                        'qty' => $itemData['qty'][$key],
                        'qty_kg' => $itemData['qty_kg'][$key],
                        'price' => $itemData['price'][$key],         
						'packing_charge' => $itemData['packing_charge'][$key],
                        'cod_date' => $itemData['cod_date'][$key],
                        'target_date' => $itemData['target_date'][$key],
                        'amount' => $itemData['amount'][$key],
                        'taxable_amount' => $itemData['taxable_amount'][$key],
                        'gst_per' => $itemData['gst_per'][$key],
                        'gst_amount' => $itemData['gst_amount'][$key],
                        'igst_per' => $itemData['igst_per'][$key],
                        'igst_amount' => $itemData['igst_amount'][$key],
                        'cgst_per' => $itemData['cgst_per'][$key],
                        'cgst_amount' => $itemData['cgst_amount'][$key],
                        'sgst_per' => $itemData['sgst_per'][$key],
                        'sgst_amount' => $itemData['sgst_amount'][$key],
                        'disc_per' => $itemData['disc_per'][$key],
                        'disc_amount' => $itemData['disc_amount'][$key],
                        'net_amount' => $itemData['net_amount'][$key],
                        'item_remark' => $itemData['item_remark'][$key],
                        'created_by' => $created_by 
                    ];
                    $this->store($this->transChild, $transData);  
                    if(!empty($itemData['ref_id'][$key])):
                        $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
                    endif;
                endforeach;

                /* Send Notification */
                $soNo = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
                $notifyData['notificationTitle'] = (empty($masterData['id']))?"New Sales Order":"Update Sales Order";
                $notifyData['notificationMsg'] = (empty($masterData['id']))?"New Sales Order Generated. SO. No. : ".$soNo:"Sales Order updated. SO No. : ".$soNo;
                //$notifyData['payload'] = ['callBack' => base_url('salesOrder')];
                $notifyData['payload'] = ['callBack' => base_url('reports/salesReport/orderMonitor')];
                $notifyData['controller'] = "'salesOrder'";
                $notifyData['action'] = (empty($masterData['id']))?"W":"M";
                $this->notify($notifyData);

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

    public function checkDuplicateOrder($party_id,$so_prefix,$so_no,$id = ""){
		$data['tableName'] = $this->transMain;
		//$data['where']['party_id'] = $party_id;
		$data['where']['trans_prefix']  = $so_prefix;
		$data['where']['trans_no']  = $so_no;
        $data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
		$data['where']['entry_type']  = 4;
		if(!empty($id)){$data['where']['id != '] = $id;}
		return $this->numRows($data);
    }

    public function getSalesOrderTransactions($id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.*,item_master.rev_no as itemRevNO , item_master.drawing_no as itemDrgNo, item_master.part_no as partNo,item_master.wt_pcs,item_master.size,GROUP_CONCAT(DISTINCT(job_card.wo_no) SEPARATOR ", ") as wo_no,item_master.full_name';
        //$data['select'] = 'trans_child.*,item_master.rev_no as itemRevNO , item_master.drawing_no as itemDrgNo, item_master.part_no as partNo,item_master.wt_pcs,GROUP_CONCAT(DISTINCT(job_card.wo_no) SEPARATOR ", ") as wo_no';
        $data['join']['item_master'] = 'item_master.id = trans_child.item_id';
        $data['leftJoin']['job_card'] = "job_card.so_trans_id = trans_child.id";
        $data['where']['entry_type'] = 4;
        $data['where']['trans_main_id'] = $id;
        $data['group_by'][] = "trans_child.id";
        return $this->rows($data);
    }
	
    public function getOrderItems($transIds){
        $data['tableName'] = $this->transChild;        
        $data['where']['entry_type'] = 4;
        $data['where_in']['trans_main_id'] = $transIds;
        return $this->rows($data);
    }
	
	// Created By JP @ 06.07.2023
    public function getSoByTransId($postData){  
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.*,trans_main.trans_date,trans_main.trans_number,item_master.item_code,item_master.item_name, st.stock_qty';
        $data['join']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
        $data['join']['item_master'] = 'item_master.id = trans_child.item_id';
		$data['leftJoin']['(SELECT SUM(qty) as stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND stock_effect = 1 AND location_id !='.$this->SCRAP_STORE->id.' GROUP BY item_id) as st'] = "st.item_id = item_master.id";
        $data['where']['trans_child.id'] = $postData['so_trans_id'];
        return $this->row($data);
    }

	// Created By JP @ 06.07.2023
    public function getPendingSoTrans(){
        $data['tableName'] = $this->transChild;  
		$data['select'] = 'trans_child.id,trans_child.grn_data,trans_child.qty,trans_child.dispatch_qty,trans_main.trans_date,trans_main.trans_number, trans_main.doc_no';
        $data['where']['trans_child.entry_type'] = 4;
		$data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		//$data['join']['party_master'] = "party_master.id = trans_main.party_id";
		$data['where']['trans_child.trans_status'] = 0; 
		//$data['where']['trans_main.trans_date >= '] = $this->startYearDate;
		//$data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $data['customWhere'][] = '(trans_child.qty - trans_child.dispatch_qty) > 0';
        $result = $this->rows($data);
		//$this->printQuery();
		return $result;
    }
    
	public function getSalesOrder($id){
        $data['tableName'] = $this->transMain;
        $data['where']['id'] = $id;
        $result = $this->row($data);
        $result->items = $this->getSalesOrderTransactions($id);
        return $result;
    }

    public function deleteOrder($id){
        try{
            $this->db->trans_begin();
            $orderData = $this->getSalesOrder($id);

            foreach($orderData->items as $row):
                if(!empty($row->ref_id)):
                    $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                endif;

                $where['id'] = $row->id;
                $this->trash($this->transChild,$where);
            endforeach;	
            
            //enquiry master delete
            $result = $this->trash($this->transMain,['id'=>$id],'Sales Order');

            /* Send Notification */
            $soNo = getPrefixNumber($orderData->trans_prefix,$orderData->trans_no);
            $notifyData['notificationTitle'] = "Delete Sales Order";
            $notifyData['notificationMsg'] = "Sales Order deleted. SO No. : ".$soNo;
            //$notifyData['payload'] = ['callBack' => base_url('salesOrder')];
            $notifyData['payload'] = ['callBack' => base_url('reports/salesReport/orderMonitor')];
            $notifyData['controller'] = "'salesOrder'";
            $notifyData['action'] = "D";
            $this->notify($notifyData);

            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function checkSalesOrderPendingStatus($id){
        $data['select'] = "COUNT(trans_status) as orderStatus";
        $data['where']['trans_main_id'] = $id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        $data['tableName'] = $this->transChild;
        return $this->specificRow($data)->orderStatus;
    }

    public function completeSalesOrderItem($id, $val, $msg) {
        $this->store($this->transChild, ['id'=> $id,'trans_status' => $val]);

        $data['tableName'] = $this->transChild;
        $data['where']['id'] = $id;
        $orderId = $this->row($data)->trans_main_id;
        $pendingOrder = $this->checkSalesOrderPendingStatus($orderId);

        if(empty($pendingOrder)) :
            $this->store($this->transMain, ['id' => $orderId,'trans_status' => 2]);
        else :
            $this->store($this->transMain, ['id' => $orderId,'trans_status' => 0]);
        endif;

        return ['status' => 1, 'message' => 'Order Item ' . $msg . ' successfully.'];
    }

    public function getPartyOrders($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_prefix,trans_no,trans_date,doc_no";
        $queryData['where']['trans_status'] = 0;
        $queryData['where']['entry_type'] = 4;
        $queryData['where']['is_approve != '] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $wono = array(); $qty = array();
                $partData = $this->getSalesOrderTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_code; 
                    $wono[] = $part->grn_data; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); 
                $wo_no = implode(",<br> ",$wono); 
				$part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
                            <td class="text-center">'.$row->doc_no.'</td>
                            <td class="text-center">'.$wo_no.'</td>
                            <td class="text-center">'.$part_code.'</td>
                            <td class="text-center">'.$part_qty.'</td>
                          </tr>';
                $i++;
            endforeach;
        // else:
        //     $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }

    public function getPartyItems($party_id){
		
		$queryData['tableName'] = $this->itemMaster;
	    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['where']['item_master.item_type'] = 1;
        $queryData['where']['item_master.party_id'] = $party_id;
        $itemData = $this->rows($queryData);
        
        $partyItems='<option value="">Select Product Name</option>';
        if(!empty($itemData)):
            $i=1;
			foreach ($itemData as $row):
				$partyItems .= '<option value='.$row->id.' data-row='.json_encode($row).'>['.$row->item_code.'] '.$row->item_name.'</option>';
			endforeach;
        endif;
        return ['status'=>1,'partyItems'=>$partyItems];
    }

    public function approveSOrder($data){
		$date = ($data['val'] == 1)?date('Y-m-d'):"";
    	$isApprove =  ($data['val'] == 1)?$this->loginId:0;
		$this->store($this->transMain, ['id'=> $data['id'], 'is_approve' => $isApprove, 'approve_date'=>$date]);
        return ['status' => 1, 'message' => 'Sales Order '.$data['msg'].' successfully.'];
	}

    public function getItemList($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $resultData = $this->rows($queryData); 
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):              
                $html .= '<tr>
                            <td class="text-center">'.$i.'</td>
                            <td class="text-center">'.$row->item_name.'</td>
                            <td class="text-center">'.$row->hsn_code.'</td>
                            <td class="text-center">'.$row->igst_per.'</td>
                            <td class="text-center">'.$row->qty.'</td>
                            <td class="text-center">'.$row->unit_name.'</td>
                            <td class="text-center">'.$row->price.'</td>
                            <td class="text-center">'.$row->amount.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="8">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
    
    /*  Create By : Avruti @3-02-2022 */
    public function getSalesOrderData($id){
        $data['tableName'] = $this->transChild;
        $data['where']['id'] = $id;
        $result = $this->row($data);
        return $result;
    }

    public function saveCloseSO($data) { 
        $this->store($this->transChild, ['id'=> $data['id'],'trans_status' => $data['trans_status'],'close_reason' =>$data['close_reason']]);

        $data['tableName'] = $this->transChild;
        $data['where']['id'] = $data['id'];
        $orderId = $this->row($data)->trans_main_id;
        $pendingOrder = $this->checkSalesOrderPendingStatus($orderId);

        if(empty($pendingOrder)) :
            $this->store($this->transMain, ['id' => $orderId,'trans_status' => 2]);
        else :
            $this->store($this->transMain, ['id' => $orderId,'trans_status' => 0]);
        endif;

        return ['status' => 1, 'message' => 'Order Item Closed successfully.'];
    }
    
    public function getOrderByRefid($transIds){
        $data['tableName'] = $this->transMain;        
        $data['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_child.trans_main_id,tm.trans_prefix as ref_prefix,tm.trans_no as ref_no";
        $data['leftJoin']['trans_child'] = "trans_main.id = trans_child.trans_main_id";
		$data['leftJoin']['trans_main as tm'] = 'tm.id = trans_main.ref_id';
        $data['where_in']['trans_child.trans_main_id'] = $transIds;
        $data['group_by'][] = 'trans_child.trans_main_id';
        return $this->rows($data);
    }
    
    public function getSalesOrderForPrint($id){
        $data['tableName'] = $this->transMain;
        $data['where']['id'] = $id;
        $result = $this->row($data); 
        if(empty($result->from_entry_type)){
            $result->items = $this->getSalesOrderTransactions($id);
        }else {
            $result->items = $this->getSOTransactionsForPrint($id);
        }
        return $result;
    }
    
    public function getSOTransactionsForPrint($id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.*,transChild.rev_no as itemRevNO,transChild.drg_rev_no as itemDrgNo, transChild.batch_no as partNo,item_master.item_name,item_master.material_grade,item_master.wt_pcs,item_master.size,GROUP_CONCAT(DISTINCT(job_card.wo_no) SEPARATOR ", ") as wo_no';
        //$data['select'] = 'trans_child.*,transChild.rev_no as itemRevNO,transChild.drg_rev_no as itemDrgNo, transChild.batch_no as partNo,item_master.material_grade,item_master.wt_pcs,GROUP_CONCAT(DISTINCT(job_card.wo_no) SEPARATOR ", ") as wo_no';
        $data['leftJoin']['trans_child as transChild'] = 'transChild.id = trans_child.ref_id';
        $data['join']['item_master'] = 'item_master.id = trans_child.item_id';
        $data['leftJoin']['job_card'] = "job_card.so_trans_id = trans_child.id";
        $data['where']['trans_child.entry_type'] = 4;
        $data['where']['trans_child.trans_main_id'] = $id;
        $data['group_by'][] = "trans_child.id";
        return $this->rows($data);
    }
    
	//Created By Karmi @26/03/2022
    public function getSoItems($transIds){
        $data['tableName'] = $this->transChild;        
        $data['where']['entry_type'] = 2;
        $data['where_in']['trans_main_id'] = $transIds;
        return $this->rows($data);
    }
    
    public function getSOEstimateQty($data){
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.qty_kg';
        $data['where']['trans_child.trans_main_id'] = $data['order_id'];
        $data['where']['trans_child.item_id'] = $data['item_id'];
        $data['where']['trans_child.qty_kg >'] = 0;
        return $this->row($data);
    }
    
    public function getSOTransactions($transIds){
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.*,trans_main.id as so_id,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date, trans_main.remark, trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date, party_master.party_code, party_master.currency, item_master.packing_qty as packingQty, item_master.size as item_size';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$data['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $data['where_in']['trans_child.id'] = $transIds;
        return $this->rows($data);
    }

	/*  Create By : Avruti @29-11-2021 01:00 PM
    update by : 
    note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_child.entry_type'] = 4;
        return $this->numRows($data);
    }

    public function getSalesOrderList_api($limit, $start,$status){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.is_approve';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 4;
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}
		$data['group_by'][]='trans_child.trans_main_id';

        if($status == 1) { $data['where']['trans_child.trans_status'] = 1; } 
        else { $data['where']['trans_child.trans_status != '] = 1; }

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//


    
    /**
        * Created By Mansee [22-08-2023]
     * For App
     * Get Pending Sales Order
     */
    
     public function getPendingSOForApp($data){ 
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.grn_data as wo_no,trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.net_amount,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.doc_no';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $data['where']['trans_child.entry_type'] = 4;
        if(!empty($data['sales_type'])){$data['where']['trans_main.sales_type'] = $data['sales_type'];}
        
		$data['group_by'][]='trans_child.trans_main_id';
        $data['where']['trans_main.trans_status != '] = 1;
        if($data['dispatch_status'] == 0) { 
            $data['customWhere'][] = "trans_child.cod_date >= '".date("Y-m-d")."'"; 

        } elseif($data['dispatch_status'] == 1) { 
            $data['customWhere'][] = "trans_child.cod_date < '".date("Y-m-d")."'"; 
         }

        $data['order_by']['trans_child.cod_date'] = "ASC";

       
		$result = $this->rows($data);
        return $result;
    } 
}
?>