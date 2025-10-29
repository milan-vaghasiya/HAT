<?php 
class SalesReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";
    private $customer_feedback = "customer_feedback";

    /* Customer's Order Monitoring */
    public function getOrderMonitor($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.doc_date,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name,item_master.size,item_master.wt_pcs';
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		$queryData['where']['trans_main.entry_type'] = 4;
		if(!empty($data['party_id'])){$queryData['where']['trans_main.party_id'] = $data['party_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getOrderMonitorNew($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.doc_date,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name,item_master.size,item_master.wt_pcs,dc.dqty,dc.dc_date,dc.dc_prefix,dc.dc_no,dc.dc_delivery_date';
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['leftJoin']['(SELECT trans_child.trans_main_id,trans_child.item_id,SUM(trans_child.qty) as dqty, trans_child.ref_id, trans_main.trans_date as dc_date, trans_main.trans_no as dc_no, trans_main.trans_prefix as dc_prefix, trans_main.delivery_date as dc_delivery_date FROM trans_child JOIN trans_main ON trans_main.id = trans_child.trans_main_id  WHERE  trans_child.is_delete = 0 AND trans_child.entry_type IN(5,6) GROUP BY trans_child.trans_main_id, trans_child.item_id,trans_child.ref_id ORDER BY trans_main.trans_date ASC) as dc']='dc.ref_id = trans_child.id';
		$queryData['where']['trans_main.entry_type'] = 4;
		if(!empty($data['party_id'])){$queryData['where']['trans_main.party_id'] = $data['party_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
    

	public function getOrderMonitor_old($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.doc_date,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name';
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['where']['trans_main.entry_type'] = 4;
		if(!empty($data['party_id'])){$queryData['where']['trans_main.party_id'] = $data['party_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }

    public function getInvoiceData($data){
        // if(!empty($data['sales_type']) && $data['sales_type'] == 2):
        //     $queryData = array();
        //     $queryData['tableName'] = "packing_transaction";
        //     $queryData['select'] = "si.*,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date,trans_main.id as refId,trans_main.trans_prefix as inv_prefix,trans_main.trans_no as inv_no";
        //     $queryData['leftJoin']['trans_child as comp'] = "packing_transaction.id = comp.ref_id AND comp.from_entry_type = 0";
        //     $queryData['leftJoin']['trans_child as cump'] = "cump.ref_id = comp.id";
        //     $queryData['leftJoin']['trans_child as cuinv'] = "cuinv.ref_id = cump.id";
        //     $queryData['leftJoin']['trans_child as si'] = "si.ref_id = cuinv.id";
        //     $queryData['leftJoin']['trans_main'] = "trans_main.id = si.trans_main_id";
        //     //$queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id";
        //     $queryData['where']['packing_transaction.trans_child_id'] = $data['id'];
        //     $queryData['where']['si.is_delete'] = 0;
        //     $queryData['group_by'][] = 'si.trans_main_id';
        //     $queryData['group_by'][] = 'si.item_id';
        //     $result = $this->rows($queryData);
        //     //$this->printQuery();exit;
        //     return $result;
        // else:
            $queryData = array();
            $queryData['tableName'] = $this->transChild;
            $queryData['select'] = 'trans_child.*,SUM(trans_child.qty) as dqty,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date,transMain.id as refId,transMain.trans_prefix as inv_prefix,transMain.trans_no as inv_no';
            $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
            $queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id";
            $queryData['where']['trans_child.ref_id'] = $data['id'];
            // $queryData['where_in']['trans_main.entry_type'] = '6,7,8,10,11';
            $queryData['group_by'][] = 'trans_child.trans_main_id';
            $queryData['group_by'][] = 'trans_child.item_id';
            return $this->rows($queryData);
        // endif;
    }

    public function getDeliveredQty($item_id,$trans_main_id)
    {
        $data['tableName'] = $this->transChild;
        $data['select'] = 'SUM(trans_child.qty) as dqty';
        $data['where']['trans_child.item_id'] = $item_id;
        $data['where']['trans_child.trans_main_id'] = $trans_main_id;
        $data['group_by'][] = 'trans_child.trans_main_id';
        $data['group_by'][] = 'trans_child.item_id';
        return $this->row($data);
    }
    
    public function getDispatchPlan($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.id as so_id,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date, trans_main.remark, trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date, party_master.party_code, party_master.currency, item_master.packing_qty as packingQty';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_child.trans_status'] = 0;
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';

		return $this->rows($queryData);
    }
    
	public function getPackingPlan_old($data){
        $queryData = array();
		$queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'packing_master.id,packing_master.item_id,SUM(packing_master.packing_qty) as packing_qty,packing_master.packing_date,party_master.party_code, party_master.currency,item_master.qty as totalStock,item_master.item_code,item_master.price as item_price';
		$queryData['join']['item_master'] = "item_master.id = packing_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		// $queryData['where']['packing_master.item_id'] = 505;
        $queryData['customWhere'][] = "packing_master.packing_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['packing_master.packing_date'] = 'ASC';
		$queryData['group_by'][] = 'packing_master.packing_date';
		$queryData['group_by'][] = 'packing_master.item_id';

		return $this->rows($queryData);
    }
    
	public function getPackingPlan($data){
        $queryData = array();
		$queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'packing_master.id,packing_master.item_id,SUM(packing_master.packing_qty) as packing_qty,packing_master.packing_date,party_master.party_code, party_master.currency,item_master.qty as totalStock,item_master.item_code,(item_master.price*currency.inrrate) as item_price';
		$queryData['join']['item_master'] = "item_master.id = packing_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$queryData['leftJoin']['currency'] = "currency.currency = party_master.currency";
	//	$queryData['where']['packing_master.item_id'] = 713;
        //$queryData['customWhere'][] = "packing_master.packing_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['packing_master.packing_date'] = 'ASC';
		//$queryData['group_by'][] = 'packing_master.packing_date';
		$queryData['group_by'][] = 'packing_master.item_id';

		return $this->rows($queryData);
    }

    public function getRFDStock($item_id){
        $data['tableName'] = $this->stockTrans;
        $data['select'] = "SUM(qty) as rfd_qty";
        $data['where']['location_id'] = $this->RTD_STORE->id;
        $data['where']['item_id'] = $item_id;
        return $this->row($data);
    }
	
	public function getDispatchOnPacking($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty,AVG(trans_child.price) as dispatch_price,SUM(trans_child.disc_amount) as disc_amt,trans_child.item_id';
		$queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_child.item_id';
		return $this->row($queryData);
    }
	
	/* On Invoice Data */
	public function getDispatchMaterial($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty';
		$queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        // $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['trans_main.trans_date >= '] = $data['from_date'];
		$dm = $this->row($queryData);//if($data['item_id'] = 1289){print_r($this->db->last_query());}
		return $dm;
    }


	public function getJobcardBySO($sales_order_id,$product_id){
		$queryData = array();
		$queryData['tableName'] = 'job_card';
		$queryData['where']['sales_order_id'] = $sales_order_id;
		$queryData['where']['product_id'] = $product_id;
		return $this->row($queryData);
	}
	
    public function getWIPQtyForDispatchPlan($data){
        $queryData['tableName'] = "job_card";
        $queryData['select'] = "SUM(job_card.qty) as qty";
        $queryData['where']['job_card.sales_order_id'] = $data['trans_main_id'];
        $queryData['where']['job_card.product_id'] = $data['item_id'];
        $queryData['where']['job_card.order_status !=']= 4;
		return $this->rows($queryData);
    }

    public function getCurrencyConversion($currency){ 
        $data['tableName'] = 'currency';
        $data['where']['currency'] = $currency;
        $result= $this->rows($data);
        return $result;
    }
    
    public function getDispatchSummary_old($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['customWhere'][] = '((trans_main.entry_type IN (6,7,8) AND trans_main.from_entry_type != 5) OR trans_main.entry_type = 5)';
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        if(!empty($data['item_id'])){$queryData['where_in']['trans_child.item_id'] = $data['item_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		//print_r($this->db->last_query()); exit;
		return $result;
    }
    
    public function getDispatchSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency,transMain.trans_prefix as inv_prefix,transMain.trans_no as inv_no,transChild.price as inv_price,trans_main.doc_no,soMain.trans_no as so_no,soMain.trans_prefix as so_prefix,soMain.doc_no as cust_po_no';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id";
         $queryData['leftJoin']['trans_child as transChild'] = "transChild.ref_id = trans_child.id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['leftJoin']['trans_main as soMain'] = "trans_main.ref_id = soMain.id";
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_main.entry_type'] = 5;
        if(!empty($data['item_id'])){$queryData['where_in']['trans_child.item_id'] = $data['item_id'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		//print_r($this->db->last_query()); exit;
		return $result;
    }
    
    public function getItemHistory($item_id,$location_id=0){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name,location_master.location';
        $queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $item_id;
        if(!empty($location_id)){ $queryData['where']['stock_transaction.location_id'] = $location_id; }
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
        $queryData['order_by']['stock_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getSalesEnquiry($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency,rejection_comment.remark as reason';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.item_remark";
		$queryData['where']['trans_child.entry_type'] = 1;
		$queryData['where']['trans_child.feasible'] = 'No';
        if(!empty($data['reson_id']))    
            $queryData['where']['trans_child.item_remark'] = $data['reson_id'];
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];

        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Monthly Sales Report */
    public function getSalesData($data)
    {
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
        $queryData['customWhere'][] = 'trans_main.entry_type IN(6,7,10,11)';
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
        if($data['party']!=0){ $queryData['where']['party_id'] = $data['party']; }
        if($data['product']!=0){
            $queryData['leftJoin']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.item_id'] = $data['product'];
        }

		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Dispatch Plan Summary */
    public function getDispatchPlanSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*, trans_main.trans_prefix, trans_main.trans_no,trans_main.doc_no,party_master.party_code,party_master.party_name,party_master.currency,item_master.item_code';
        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['leftJoin']['item_master'] = "trans_child.item_id = item_master.id";
        if(!empty($data['party_id'])){ $queryData['where']['trans_main.party_id'] = $data['party_id']; }        
        if(!empty($data['sales_type'])){ $queryData['where']['trans_main.sales_type'] = $data['sales_type'];}
        $queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_main.is_approve != '] = 0;
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';
		$result = $this->rows($queryData);
		//print_r($this->printQuery());exit;
		return $result;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : 
    * Note : 
    */
    public function getEnquiryMonitoring($data){
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->rows($queryData); 
        return $result;
    }

    public function getEnquiryCount($data){ 
		$result = new StdClass; $result->pending=0; $result->totalEnquiry=0; $result->quoted=0; $result->confirmSo=0; $result->pendingSo=0;

        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        $result->totalEnquiry = count($totalEnquiry);
		
		$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status'] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.id';
        $quoted = $this->rows($queryData);
        $result->quoted = count($quoted);

        /*$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status != '] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pending = $this->rows($queryData);
		$result->pending = count($pending);*/
        $result->pending = $result->totalEnquiry - $result->quoted;
        
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where_in']['trans_child.entry_type'] = '2,3';
		$queryData['where']['trans_child.trans_status'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $confirmSo = $this->rows($queryData);
        $result->confirmSo = count($confirmSo);

       /* $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where']['trans_child.trans_status != '] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pendingSo = $this->rows($queryData);
        $result->pendingSo = count($pendingSo);*/
        $result->pendingSo = $result->totalEnquiry - $result->confirmSo;

		return $result;
	}
	
	 /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesEnquiryByParty($data){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        return $totalEnquiry;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesQuotation($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 2;
		$return= $this->rows($queryData);
        return $return;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesOrder($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 4;
		$return= $this->rows($queryData);
        return $return;
    }

    /* 
        Created By Avruti @ 30-12-2021
    */
    public function getSalesInvoiceTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount) as totalInvoiceAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [6,7,8];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    public function getSalesOrderTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount * inrrate) as totalOrderAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [4];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    public function getOrderSummary($data){
		$queryData['tableName'] = $this->transChild;
		$queryData['select'] = 'trans_child.*,trans_main.trans_date, trans_main.trans_prefix, trans_main.trans_no, trans_main.sales_type, party_master.party_name,party_master.currency, item_master.item_name, item_master.item_code,trans_main.doc_no,IFNULL(currency.inrrate,1) as inrrate';
        $queryData['join']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
        $queryData['leftJoin']['party_master'] = 'trans_main.party_id = party_master.id';
        $queryData['leftJoin']['item_master'] = 'trans_child.item_id = item_master.id';
        $queryData['leftJoin']['currency'] = 'currency.currency = party_master.currency';
		$queryData['where']['trans_main.entry_type'] = 4;
		$queryData['where']['trans_child.trans_status != '] = 2;
		if(!empty($data['party_id'])){$queryData['where']['trans_main.party_id'] = $data['party_id'];}
		if(!empty($data['pending'])){if($data['pending']==1){$queryData['customWhere'][] = '(trans_child.qty - trans_child.dispatch_qty) > 0';}else{$queryData['customWhere'][] = '(trans_child.qty - trans_child.dispatch_qty) <= 0';}}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->rows($queryData);
    }

    public function getOrderWiseDispatch($ref_id,$sales_type = ""){
        // if($sales_type == 2):
        //     $queryData = array();
        //     $queryData['tableName'] = "packing_transaction";
        //     $queryData['select'] = "SUM(si.qty) as dispatch_qty";
        //     $queryData['leftJoin']['trans_child as comp'] = "packing_transaction.id = comp.ref_id AND comp.from_entry_type = 0";
        //     $queryData['leftJoin']['trans_child as cump'] = "cump.ref_id = comp.id";
        //     $queryData['leftJoin']['trans_child as cuinv'] = "cuinv.ref_id = cump.id";
        //     $queryData['leftJoin']['trans_child as si'] = "si.ref_id = cuinv.id";
        //     $queryData['where']['packing_transaction.trans_child_id'] = $ref_id;
        //     $result = $this->row($queryData);
        //     //$this->printQuery();exit;
        //     return $result;
        // else:
            $queryData = array();
            $queryData['tableName'] = $this->transChild;
    		$queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty';
    		$queryData['where']['trans_child.ref_id'] = $ref_id;
    		$queryData['where']['trans_child.from_entry_type'] = 4;
    		return $this->row($queryData);
    	// endif;
    }
    
    public function getCustomerEnquiryRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,trans_main.doc_no,trans_main.doc_date,trans_main.ref_by,party_master.party_code,party_master.party_name,party_master.currency,rejection_comment.remark as reason,employee_master.emp_name,transMain.trans_prefix as quote_prefix,transMain.trans_no as quote_no,transMain.inrrate as quote_inrrate,transMain.trans_date as quote_date,so.trans_prefix as so_prefix,so.trans_no as so_no,so.trans_date as so_date,quote_child.amount as quote_amount,executive.emp_name as sales_executive';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.item_remark";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_child.created_by";
		$queryData['leftJoin']['employee_master as executive'] = "executive.id = trans_main.sales_executive";
        $queryData['leftJoin']['trans_main as transMain'] = "transMain.ref_id = trans_main.id AND transMain.is_delete = 0";
        $queryData['leftJoin']['trans_main as so'] = "so.ref_id = transMain.id AND so.entry_type=4 AND so.is_delete = 0";
        $queryData['leftJoin']['trans_child as quote_child'] = "quote_child.ref_id = trans_child.id AND quote_child.entry_type = 2 AND quote_child.is_delete = 0";

		$queryData['where']['trans_child.entry_type'] = 1;
        if(!empty($data['reson_id']))    
            $queryData['where']['trans_child.item_remark'] = $data['reson_id'];
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];

        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
    
    //Created By Karmi @05/07/2022
    public function getSalesQuotationMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.trans_status, trans_child.cod_date,trans_child.confirm_by,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.extra_fields';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.entry_type'] = 2;
        $queryData['where']['trans_child.trans_status != '] = 2;
		$queryData['group_by'][]='trans_child.trans_main_id';
		$resultData = $this->rows($queryData);
		return $resultData;
	}
	
	//created By Karmi @12/08/2022
    public function getPackingHistory($data){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_master.*,packing_transaction.trans_main_id,packing_transaction.total_qty,packing_transaction.dispatch_qty,ifnull(party_master.party_code,ifnull(item_party.party_code,'')) as party_code,ifnull(trans_main.trans_prefix,'') as so_prefix,ifnull(trans_main.trans_no,'') as so_no,item_master.item_code";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['trans_main'] = "packing_transaction.trans_main_id = trans_main.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['leftJoin']['item_master'] = "packing_transaction.item_id = item_master.id";
        $queryData['leftJoin']['party_master as item_party'] = "item_party.id = item_master.party_id";
        $queryData['customWhere'][] = '(packing_transaction.total_qty - packing_transaction.dispatch_qty) > 1';
        $queryData['customWhere'][] = "packing_master.entry_date < '".$data['to_date']."'";
        if(!empty($data['party_id'])){ $queryData['where']['item_party.id'] = $data['party_id']; }
        $queryData['order_by']['packing_master.entry_date'] = 'ASC';
        return $this->rows($queryData);
    }
    
    /* Customer Satisfaction Feedback Summary */
    public function getCustomerSatisfactionFeedback($data){
        $data['tableName'] = $this->customer_feedback;
        $data['select'] = "customer_feedback.*,cust_feedback_trans.feedback_id,cust_feedback_trans.param_id,cust_feedback_trans.parameter,cust_feedback_trans.grade,party_master.party_name";
        $data['leftJoin']['cust_feedback_trans'] = "cust_feedback_trans.feedback_id = customer_feedback.id";
        $data['leftJoin']['party_master'] = "party_master.id = customer_feedback.party_id";
        $data['where']['customer_feedback.party_id'] = $data['party_id'];
        return $this->rows($data);
    }

	public function getListOfCustomerData(){
        $data['tableName'] = "party_master";
        $data['where']['party_type'] = 1;
        $data['where']['party_category'] = 1;
        return $this->rows($data);
    }

    public function getlistOfCustomersApprovedProductDrawing($data){
        $data['tableName'] = "party_master";
        $data['select'] = "party_master.*,item_master.description,item_master.item_name,item_master.drawing_no,item_master.rev_no,item_master.rev_date,item_master.wt_pcs,item_master.material_grade,item_master.size";
        $data['leftJoin']['item_master'] = "item_master.party_id = party_master.id";
        $data['where']['party_type'] = 1;
        $data['where']['party_category'] = 1;
        if(!empty($data['party_id'])){ $data['where']['party_master.id'] = $data['party_id']; }
        return $this->rows($data);
    }

	// Get Sales Statastics
	public function getSalesStats(){		
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
		
        $queryData['select'] = "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=4 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq4,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=5 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq5,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=6 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq6,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=7 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq7,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=8 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq8,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=9 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq9,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=10 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq10,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=11 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq11,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=12 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as oq12,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=1 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as oq1,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=2 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as oq2,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=4 AND MONTH(trans_main.trans_date)=3 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as oq3,";
		
		$queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=4 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq4,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=5 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq5,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=6 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq6,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=7 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq7,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=8 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq8,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=9 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq9,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=10 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq10,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=11 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq11,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=12 AND YEAR(trans_main.trans_date)=".$this->startYear." THEN trans_child.qty ELSE 0 END) as dq12,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=1 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as dq1,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=2 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as dq2,";
        $queryData['select'] .= "SUM(CASE WHEN trans_child.entry_type=6 AND MONTH(trans_main.trans_date)=3 AND YEAR(trans_main.trans_date)=".$this->endYear." THEN trans_child.qty ELSE 0 END) as dq3";
		
        $queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where_in']['trans_main.entry_type'] = [4,6];
		$result = $this->row($queryData);
		//$this->printQuery();
		return $result;
    }
}
?>