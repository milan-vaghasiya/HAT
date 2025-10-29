<?php
class ProformaInvoiceModel extends MasterModel{
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.net_amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.net_amount as inv_amount,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 9;
        $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        $data['group_by'][] = 'trans_child.trans_main_id';

       $data['searchCol'][] = "CONCAT(trans_main.trans_prefix,trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "trans_child.net_amount";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_no','trans_main.trans_date','party_master.party_name','trans_child.item_name','trans_child.net_amount','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
        $id = $masterData['id'];		
		if(empty($id)):
			$saveInvoice = $this->store($this->transMain,$masterData);
			$salesId = $saveInvoice['insert_id'];			

			$result = ['status'=>1,'message'=>'Proforma Invoice saved successfully.','url'=>base_url("proformaInvoice")];
		else:
			$this->store($this->transMain,$masterData);
            $salesId = $id;		
            
			$transDataResult = $this->proformaTransactions($id);
			foreach($transDataResult as $row):
                if(!in_array($row->id,$itemData['id'])):
                    $this->trash($this->transChild,['id'=>$row->id]);
                endif;
			endforeach;

			$result = ['status'=>1,'message'=>'Proforma Invoice updated successfully.','url'=>base_url("proformaInvoice")];
		endif;

        foreach($itemData['item_id'] as $key=>$value):

            $salesTransData = [
                                'id'=>$itemData['id'][$key],
                                'trans_main_id'=>$salesId,
                                'entry_type' => $masterData['entry_type'],
                                'currency' => $masterData['currency'],
                                'inrrate' => $masterData['inrrate'],
                                'from_entry_type' => $itemData['from_entry_type'][$key],
                                'ref_id' => $itemData['ref_id'][$key],
                                'item_id'=>$value,
                                'item_name' => $itemData['item_name'][$key],
                                'item_type' => $itemData['item_type'][$key],
                                'item_code' => $itemData['item_code'][$key],
                                'item_desc' => $itemData['item_desc'][$key],
                                'unit_id' => $itemData['unit_id'][$key],
                                'unit_name' => $itemData['unit_name'][$key],
				                'stock_eff' => $itemData['stock_eff'][$key],
                                'hsn_code' => $itemData['hsn_code'][$key],
                                'qty' => $itemData['qty'][$key],
                                'price' => $itemData['price'][$key],
                                'amount' => $itemData['amount'][$key],
                                'taxable_amount' => $itemData['taxable_amount'][$key],
                                'gst_per' => $itemData['gst_per'][$key],
				                'gst_amount' => $itemData['igst_amount'][$key],
                                'igst_per' => $itemData['igst_per'][$key],
                                'igst_amount' => $itemData['igst_amount'][$key],
                                'cgst_per' => $itemData['cgst_per'][$key],
                                'cgst_amount' => $itemData['cgst_amount'][$key],
                                'sgst_per' => $itemData['sgst_per'][$key],    
                                'sgst_amount' => $itemData['sgst_amount'][$key],
                                'disc_per' => $itemData['disc_per'][$key],
                                'disc_amount' => $itemData['disc_amount'][$key],
                                'item_remark' => $itemData['item_remark'][$key],
                                'net_amount' => $itemData['net_amount'][$key],
                                'created_by' => $masterData['created_by']
                              ];
            $saveTrans = $this->store($this->transChild,$salesTransData);
            $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];
            
            /* if(!empty($itemData['ref_id'][$key])):
                $setData = Array();
                $setData['tableName'] = $this->transChild;
                $setData['where']['id'] = $itemData['ref_id'][$key];
                $setData['set']['dispatch_qty'] = 'dispatch_qty, + '.$itemData['qty'][$key];
                $this->setValue($setData);

                $queryData = array();
                $queryData['tableName'] = $this->transChild;
                $queryData['where']['id'] = $itemData['ref_id'][$key];
                $transRow = $this->row($queryData);

                if($transRow->qty == $transRow->dispatch_qty):
                    $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
                endif;
            endif; */
                   
        endforeach;

        /* if(!empty($masterData['ref_id'])):
            $refIds = explode(",",$masterData['ref_id']);
            foreach($refIds as $key=>$value):
                if($masterData['from_entry_type'] == 5):
                    $pendingItems = $this->challan->checkChallanPendingStatus($value);
                elseif($masterData['from_entry_type'] == 4):
                    $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                endif;
                if(empty($pendingItems)):
                    $this->store($this->transMain,['id'=>$value,'trans_status'=>1]);
                endif;
            endforeach; 
        endif; */
            /* Send Notification */
            $piNo = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
            $notifyData['notificationTitle'] = (empty($masterData['id']))?"New Proforma Invoice":"Update Proforma Invoice";
            $notifyData['notificationMsg'] = (empty($masterData['id']))?"New Proforma Invoice Generated. PI. No. : ".$piNo:"Proforma Invoice updated. PI. No. : ".$piNo;
            $notifyData['payload'] = ['callBack' => base_url('proformaInvoice')];
            $notifyData['controller'] = "'proformaInvoice'";
            $notifyData['action'] = (empty($masterData['id']))?"W":"M";
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

    public function getInvoice($id){    
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $queryData['where']['entry_type'] = 9;
        $invoiceData = $this->row($queryData);
        $invoiceData->itemData = $this->proformaTransactions($id);
        return $invoiceData;
    }

    public function proformaTransactions($id,$limit=""){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        $queryData['where']['entry_type'] = 9;
        if(!empty($limit)){$queryData['limit'] = $limit;}
        return $this->rows($queryData);
    }

    public function deleteInv($id){
        try{
            $this->db->trans_begin();
        $transData = $this->getInvoice($id);
        foreach($transData->itemData as $row):
            $this->trash($this->transChild,['id'=>$row->id]);
        endforeach;

        /* if(!empty($transData->ref_id)):
            $refIds = explode(",",$transData->ref_id);
            foreach($refIds as $key=>$value):
                if($transData->from_entry_type == 5):
                    $pendingItems = $this->challan->checkChallanPendingStatus($value);
                elseif($transData->from_entry_type == 4):
                    $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                endif;
                if(empty($pendingItems)):
                    $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                endif;
            endforeach;
        endif; */
        $result = $this->trash($this->transMain,['id'=>$id],'Proforma Invoice');
        /* Send Notification */
			$piNo = getPrefixNumber($transData->trans_prefix,$transData->trans_no);
			$notifyData['notificationTitle'] = "Delete Proforma Invoice";
			$notifyData['notificationMsg'] = "Proforma Invoice deleted. PI. No. : ".$piNo;
			$notifyData['payload'] = ['callBack' => base_url('proformaInvoice')];
			$notifyData['controller'] = "'proformaInvoice'";
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
	
	/*  Create By : Avruti @29-11-2021 01:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_child.entry_type'] = 9;
        return $this->numRows($data);
    }

    public function getProformaInvoiceList_api($limit, $start,$status){
        $data['tableName'] = $this->transChild;
        $data['select'] = "trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.net_amount,trans_child.item_remark,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.net_amount as inv_amount,trans_main.remark";

        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 9;
        $data['group_by'][] = 'trans_child.trans_main_id';


        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>