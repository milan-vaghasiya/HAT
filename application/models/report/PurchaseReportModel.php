<?php 
class PurchaseReportModel extends MasterModel
{
    private $grnTrans = "grn_transaction";
    private $purchaseTrans = "purchase_order_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getPurchaseMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,purchase_order_master.reference_by,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.remark,item_master.item_name,party_master.party_name,unit_master.unit_name';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['unit_master'] = 'unit_master.id = item_master.unit_id';
		
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_order_master.po_date'] = 'ASC';
		return $this->rows($queryData);
    }

    public function getPurchaseReceipt($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date,grn_master.challan_no';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['where']['grn_transaction.item_id'] = $data['item_id'];
		$queryData['where']['grn_transaction.po_trans_id'] = $data['grn_trans_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
    /* Last Purchase Price */
	public function getLastPrice($data){
		$queryData = array();
		$queryData['tableName'] = 'trans_child';
		$queryData['select'] = 'trans_child.price';
		$queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
		$queryData['where']['trans_main.entry_type'] = 12;
		if(!empty($data['from_date']))
		{
			$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
		$queryData['order_by']['trans_main.trans_date'] = 'DESC';
		$queryData['limit']=1;
		
		$result = $this->row($queryData);
		// print_r($this->db->last_query());
		return $result;
	}
	
	public function getPurchaseInward($data){
		$queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date,party_master.party_name,item_master.item_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.po_date';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
		$queryData['join']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['purchase_order_master'] = 'purchase_order_master.id = grn_master.order_id';
		$queryData['where']['grn_master.type'] = 1;
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'DESC';
		return $this->rows($queryData);
	}
	
	public function getItemLastPurchasePrice($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['order_by']['id'] = 'DESC';
		$queryData['limit']=1;
		$result = $this->row($queryData);
		return $result;
    }
    
    //Created By Avruti @08/08/2022
	public function getSupplierWiseItem($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.party_id,party_master.party_name,item_master.item_name,item_master.item_code';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = grn_transaction.item_id';
		if(!empty($data['item_id'])){$queryData['where']['grn_transaction.item_id'] = $data['item_id'];}
		if(!empty($data['party_id'])){$queryData['where']['grn_master.party_id'] = $data['party_id'];}
        $queryData['group_by'][] = 'grn_master.party_id';
        $queryData['group_by'][] = 'grn_transaction.item_id';
		return $this->rows($queryData);
    }
    
    //Created By Avruti @09/08/2022
	public function getGrnTracking($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_date,grn_master.grn_no,grn_master.grn_prefix,grn_master.party_id,party_master.party_name,item_master.item_name,item_master.item_code,purchase_inspection.inspection_date';
		$queryData['leftJoin']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = grn_transaction.item_id';
		$queryData['leftJoin']['purchase_inspection'] = 'purchase_inspection.ptrans_id = grn_transaction.id';
		$queryData['where']['grn_transaction.item_type'] = 3;
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		if(!empty($data['party_id'])){$queryData['where']['grn_master.party_id'] = $data['party_id'];}
		return $this->rows($queryData);
    }

	public function getSupplierRating($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "party_master.id,party_master.party_name";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
        $queryData['group_by'][] = 'grn_master.party_id';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getSupplierItemData($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_transaction.item_id, purchase_order_trans.delivery_date";
		$queryData['select'] .= ',SUM(CASE WHEN grn_master.grn_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" AND grn_master.party_id = "'.$data['party_id'].'" AND grn_transaction.is_delete = 0 THEN grn_transaction.qty ELSE 0 END) AS recive_qty';
		$queryData['select'] .= ',SUM(CASE WHEN grn_master.grn_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" AND grn_master.party_id = "'.$data['party_id'].'" AND grn_transaction.is_delete = 0 AND grn_master.grn_date <= purchase_order_trans.delivery_date THEN grn_transaction.qty ELSE 0 END) AS in_recive_qty';
		$queryData['select'] .= ',SUM(CASE WHEN grn_master.grn_date BETWEEN "'.$data['from_date'].'" AND "'.$data['to_date'].'" AND grn_master.party_id = "'.$data['party_id'].'" AND grn_transaction.is_delete = 0 AND grn_master.grn_date > purchase_order_trans.delivery_date THEN grn_transaction.qty ELSE 0 END) AS late_recive_qty';

		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
		$result = $this->row($queryData);
	   	return $result;
    }
   
	public function getInspectedMaterialGBJ($data){
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "job_material_dispatch.job_card_id,job_material_dispatch.dispatch_qty, job_card.product_id";
        $queryData['join']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $queryData['where']['job_material_dispatch.dispatch_item_id'] = $data['item_id'];
        $queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'job_material_dispatch.job_card_id';
		$result = $this->rows($queryData);
		
		$qtyData = New StdClass;
		$qtyData->rQty = 0; $qtyData->aQty = 0; $qtyData->udQty = 0;$qtyData->insQty = 0;
		if(!empty($result)):
			foreach($result as $row):
				
				$queryData = Array();
				$queryData['tableName'] = 'rej_rw_management';
				$queryData['select'] = 'SUM(qty) as rejQty';
				$queryData['where']['job_card_id'] = $row->job_card_id;
				$queryData['where']['manag_type'] = 1;
				$rejectionData = $this->row($queryData);
				
				$queryData = Array();
				$queryData['tableName'] = $this->productKit;
				$queryData['select'] = "item_kit.*";
				$queryData['where']['ref_item_id'] = $data['item_id'];
				$queryData['where']['item_id'] = $row->product_id;
				$kitData = $this->row($queryData);
				
				if(!empty($rejectionData) and !empty($kitData)):
					$qtyData->rQty += ($rejectionData->rejQty * $kitData->qty);
				endif;
				
				$qtyData->insQty += $row->dispatch_qty;
			endforeach;
		endif;
		$qtyData->aQty = $qtyData->insQty - $qtyData->rQty;
		
	   	return $qtyData;
    }

	/*
		Created By :- Sweta @29-07-2023 
		Used At :- PurchaseReport/getPendingPO
	*/
	public function getPendingPO($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,purchase_order_master.reference_by,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.remark,item_master.item_name,party_master.party_name,unit_master.unit_name';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['unit_master'] = 'unit_master.id = item_master.unit_id';
		$queryData['where']['purchase_order_trans.order_status'] = 0;
		
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_order_master.po_date'] = 'ASC';
		return $this->rows($queryData);
    }
}
?>