<?php 
class StoreReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $jobDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
	private $itemGroup = "item_group";
    private $locationMaster = "location_master";
    private $jobCard = "job_card";

	/* Issue Register Data */
    public function getIssueRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'stock_transaction.*,item_master.item_name,employee_master.emp_name,job_card.job_number,job_card.wo_no';
		$queryData['join']['job_bom'] = 'job_bom.id = stock_transaction.trans_ref_id';
		$queryData['join']['job_card'] = 'job_card.id = job_bom.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = stock_transaction.created_by';
		$queryData['where']['stock_transaction.ref_type'] = 3;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }

    public function getIssueItemPrice($dispatch_id){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
        $queryData['select'] = 'job_material_dispatch.*,grn_transaction.price as ItemPrice';
		$queryData['join']['grn_transaction'] = 'grn_transaction.item_id = job_material_dispatch.req_item_id';
        $queryData['where']['job_material_dispatch.id'] = $dispatch_id;
        $queryData['order_by']['job_material_dispatch.dispatch_date'] = 'ASC';
        $queryData['limit'] = 1;		
        $result = $this->rows($queryData);  
		return $result;
    }

	/* Stock Register */
	public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 1;
		//$queryData['where']['stock_transaction.location_id'] = 11;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
		//$queryData['where']['stock_transaction.location_id'] = 11;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	/* Consumable UPDATED BY MEGHAVI @23/06/2023 */
    public function getConsumable(){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = 'item_master.id,item_master.item_name,item_master.location,item_master.make_brand,item_master.size,item_master.material_grade,item_master.min_qty,item_master.description,location_master.location';
		$data['leftJoin']['location_master'] = 'location_master.id = item_master.location';
		$data['where']['item_master.item_type'] = 2;
		return $this->rows($data);
	}
	
	/* Raw Material */
    public function getRawMaterialReport(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 3;
		return $this->rows($data);
	}

	/* Group wise Item List */
    public function getItemsByGroup($data){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = 'item_master.*,currency.inrrate';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		$data['where_in']['item_master.item_type'] = $data['item_type'];
		return $this->rows($data);
	}

	/* Inventory Monitoring */
	public function getItemGroup(){
		$data['tableName'] = $this->itemGroup;
		return $this->rows($data);
	}
	
	public function getFyearOpningStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as fyosqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.ref_type'] = -1;
        $queryData['where']['stock_transaction.ref_date <= '] = date('Y-m-d', strtotime($this->dates[0]));
		return $this->row($queryData);
	}
	public function getOpningStockQty($data){
		//if($data['from_date'] == date('Y-m-d', strtotime($this->dates[0]))){$data['from_date'] = date('Y-m-d', strtotime('+1 day', strtotime($data['from_date'])));} 
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as osqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        // $queryData['where']['stock_transaction.ref_date < '] = $data['from_date'];
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".date('Y-m-d', strtotime($this->dates[0]))."' AND '".date('Y-m-d', strtotime('-1 day', strtotime($data['from_date'])))."'";
		return $this->row($queryData);
	}

	public function getItemPrice($data){
        $queryData = array();
		$queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'SUM(grn_transaction.price * grn_transaction.qty) as amount';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
        $queryData['where']['grn_transaction.item_id'] =  $data['item_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);  
    }

    /* Stock Statement finish producct */
	public function getFinishProduct(){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = 'item_master.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['where']['item_master.item_type'] = 1;
		return $this->rows($queryData);
	}

	public function getClosingStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as csqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}
	
	public function getStockRegister($type){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
	/*Tool Issue Register Data */ 
    public function getToolIssueRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
		$queryData['select'] = 'job_material_dispatch.*,department_master.name,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.price, item_master.item_name';
		$queryData['leftJoin']['job_card'] = 'job_material_dispatch.job_card_id = job_card.id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_material_dispatch.dispatch_item_id';
		$queryData['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
		$queryData['where']['job_material_dispatch.dispatch_item_id != '] = 0;
		$queryData['where']['job_material_dispatch.tools_dispatch_id != '] = 0;
		if(!empty($data['job_card_id'])){$queryData['where']['job_material_dispatch.job_card_id'] = $data['job_card_id'];}
		if(!empty($data['dept_id'])){$queryData['where']['job_material_dispatch.dept_id'] = $data['dept_id'];}
        if(empty($data['job_card_id'])){$queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";}
		$queryData['order_by']['job_material_dispatch.dispatch_date'] = 'DESC';

		$result = $this->rows($queryData);
		return $result;
    }
	
	/* Item Location */
	public function getItemLocation($item_id){
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(qty) as qty, location_id";
		$queryData['where']['item_id'] = $item_id;
		$queryData['having'][] = 'SUM(qty) > 0';
		$queryData['group_by'][] = 'location_id';
		return $this->row($queryData);
	}

	/**
	 * Created By Mansee @ 09-12-2021
	 */

	 /* Stock Statement Row Material Item */
    public function getRowMaterialScrapQty($data){
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as scrap_qty,item_master.item_name,item_master.item_code,item_master.price';
		$queryData['leftJoin']['item_master'] = 'stock_transaction.item_id = item_master.id';
		$queryData['where']['item_master.item_type'] = 10;
		if($data['scrap_group'] != 'ALL')
		{
			$queryData['where']['stock_transaction.item_id'] = $data['scrap_group'];
		}
		$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = "stock_transaction.item_id";
		$result= $this->rows($queryData);
		return $result;
	}
	
	public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        return $this->rows($data); 
    }
    
    /* Avruti @21-04-2022 */
	public function getStoreLocationList(){
        $data['tableName'] = $this->locationMaster;
        return  $this->rows($data);
    }

	public function getStoreWiseStockReport($data)
    {
        $data['tableName'] = $this->stockTrans;
        $data['select'] = 'stock_transaction.*,item_master.item_name,item_master.item_code,SUM(stock_transaction.qty) as qty';
        $data['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$data['where']['stock_transaction.location_id'] = $data['location_id'];
		$data['group_by'][] = 'stock_transaction.item_id,stock_transaction.batch_no';
		return  $this->rows($data);
    }
    
	public function getInventoryMonitor($postData){
        $data['tableName'] =  $this->itemMaster;
		$data['select'] = 'item_master.id, item_master.item_name, item_master.item_code, item_master.item_type, item_master.price, item_master.rev_no, item_master.drawing_no,item_master.min_qty, currency.inrrate,party_master.party_name';
		
		if($postData['item_type'] != 1):
		    
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 1 AND stock_transaction.is_delete = 0 AND stock_transaction.ref_type != -1 THEN stock_transaction.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 2 AND stock_transaction.is_delete = 0 THEN stock_transaction.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_type = -1 AND stock_transaction.is_delete = 0 THEN stock_transaction.qty ELSE 0 END) AS opening_qty';
    		
		else:
		    
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 1 AND stock_transaction.is_delete = 0 AND stock_transaction.ref_type != -1 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS rqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.created_at <="'.$postData['to_date'].'" AND stock_transaction.trans_type = 2 AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS iqty';
    		$data['select'] .= ',SUM(CASE WHEN stock_transaction.ref_type = -1 AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = "'.$this->RTD_STORE->id.'" THEN stock_transaction.qty ELSE 0 END) AS opening_qty';
    		
		endif;
		
		$data['leftJoin']['stock_transaction'] = 'stock_transaction.item_id = item_master.id';
		$data['leftJoin']['party_master'] = 'item_master.party_id=party_master.id';
		$data['leftJoin']['currency'] = 'currency.currency=party_master.currency';
		
		if($postData['item_type'] == 3){
		    $data['where']['item_master.item_type'] = $postData['item_type'];
		    $data['where_in']['item_master.rm_shape '] = ["'Coil'","'Solid Cup'","'Strip'"];
		}else{
		    $data['where']['item_master.item_type'] = $postData['item_type'];
		}
		
		$data['where']['stock_transaction.is_delete'] = 0;
		$data['order_by']['item_master.item_code'] = 'ASC';
		$data['group_by'][] = 'stock_transaction.item_id';
		return $this->rows($data);
    }
	
	public function getLastPurchasePrice($item_id){
        $data['tableName'] = 'purchase_order_trans';
        $data['select'] = 'purchase_order_trans.price,purchase_order_trans.qty,purchase_order_trans.unit_id';
        $data['where']['purchase_order_trans.item_id'] = $item_id;
        $data['order_by']['purchase_order_trans.id'] = 'DESC';
        return $this->row($data);
    }
	
    public function getMisplacedItemList(){
		$queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$queryData['where']['stock_transaction.location_id'] = $this->MIS_PLC_STORE->id;
		$queryData['where']['stock_transaction.ref_type'] = 9;
		$queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['group_by'][] = 'stock_transaction.item_id';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getMisplacedItemHistory($data){
		
		$queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name,employee_master.emp_name';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = stock_transaction.created_by";
        if(!empty($data['item_id'])){$queryData['where']['stock_transaction.item_id'] = $data['item_id'];}
		$queryData['where']['stock_transaction.location_id'] = $this->MIS_PLC_STORE->id;
		$queryData['where']['stock_transaction.ref_type'] = 9;
		$queryData['where']['stock_transaction.trans_type'] = 1;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
        $queryData['order_by']['stock_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
	}

	public function getRmStockRegisterData($data){
		$formDate = date("Y-m-01",strtotime($data['month']));
		$toDate = date("Y-m-t",strtotime($data['month']));

		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = "ifnull(SUM(qty),0) as op_qty";
		$queryData['where']['ref_date <'] = $formDate;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['stock_effect'] = 1;
		$opQty = $this->row($queryData)->op_qty;

		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = "ref_date,SUM((CASE WHEN trans_type = 1 THEN qty ELSE 0 END)) as in_qty,SUM((CASE WHEN trans_type = 2 THEN ABS(qty) ELSE 0 END)) as out_qty";
		$queryData['where']['ref_date >='] = $formDate;
		$queryData['where']['ref_date <='] = $toDate;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['stock_effect'] = 1;
		$queryData['group_by'][] = "ref_date";
		$queryData['order_by']['ref_date'] = "ASC";
		$result = $this->rows($queryData);

		return ['resultData'=>$result,'op_qty'=>$opQty];
	}
}
?>