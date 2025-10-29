<?php 
class QualityReportModel extends MasterModel
{
    private $stockTransaction = "stock_transaction";
    private $grnTrans = "grn_transaction";
	private $jobCard = "job_card";
    private $jobMaterialDispatch = "job_material_dispatch";
    private $jobRejection = "job_rejection";
    private $productKit = "item_kit";
    private $itemMaster = "item_master";
    private $processInspection = "process_inspection";

    public function getBatchNoListForHistory(){
        $data['tableName'] = $this->stockTransaction;
        $data['select'] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        $data['group_by'][] = 'batch_no';
        $result = $this->rows($data);
        return $result;
    }

    public function getBatchHistory($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }
	
    public function getBatchList(){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['item_master.item_type'] = 3;
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }
    
    public function getBatchListByItem($item_id){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['stock_transaction.item_id'] = $item_id;
        $data['where']['item_master.item_type'] = 3;
        $data['group_by'][] = 'batch_no';
        $data['order_by']['batch_no'] = 'ASC';
        return $this->rows($data); 
    }
	
    public function getBatchItemList($batch_no){
        $data['tableName'] = $this->stockTransaction;
		$data['select'] = "stock_transaction.batch_no,stock_transaction.item_id,item_master.item_name,item_master.item_type,item_master.item_code";
		$data['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $data['where']['stock_transaction.batch_no'] = $batch_no;
        $data['group_by'][] = 'stock_transaction.item_id';
        return $this->rows($data); 
    }

    public function getBatchTracability($data){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "stock_transaction.*,item_master.item_name";
		$queryData['join']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        if(!empty($data['item_id'])){$queryData['where']['stock_transaction.item_id'] = $data['item_id'];}
        $queryData['order_by']['ref_date'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getMIfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no, job_card.id as job_id";
		$queryData['join']['job_material_dispatch'] = "stock_transaction.ref_id = job_material_dispatch.id";
        $queryData['join']['job_card'] = "job_card.id = job_material_dispatch.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getReturnfgName($ref_id){
        $queryData['tableName'] = $this->stockTransaction;
		$queryData['select'] = "item_master.item_name,item_master.item_code,job_card.job_prefix,job_card.job_no";
		$queryData['join']['job_return_material'] = "stock_transaction.ref_id = job_return_material.id";
        $queryData['join']['job_card'] = "job_card.id = job_return_material.job_card_id";
		$queryData['join']['item_master'] = "item_master.id = job_card.product_id";
        $queryData['where']['stock_transaction.ref_id'] = $ref_id;
		$result = $this->row($queryData);
	   	return $result; 
    }

    public function getSupplierRatingItems($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "item_master.id,item_master.item_name";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['item_master'] = "item_master.id = grn_transaction.item_id";
        // $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
        $queryData['group_by'][] = 'item_master.id';
		$result = $this->rows($queryData);
	   	return $result;
    }

    public function getSupplierRating($data){
        $queryData['tableName'] = $this->grnTrans;
        $queryData['select'] = "grn_transaction.*, grn_master.order_id, grn_master.grn_prefix, grn_master.grn_no, grn_master.grn_date, grn_master.remark, purchase_order_trans.delivery_date";
		$queryData['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['join']['purchase_order_trans'] = "purchase_order_trans.id = grn_transaction.po_trans_id";
        $queryData['where']['grn_transaction.item_id'] = $data['item_id'];
        $queryData['where']['grn_master.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['where']['grn_master.order_id != '] = 0;
        $queryData['where']['grn_transaction.po_trans_id != '] = 0;
        $queryData['where']['grn_master.type'] = 1;
		$result = $this->rows($queryData);
	   	return $result;
    }
   
	public function getInspectedMaterialGBJ($data){
        $queryData['tableName'] = $this->jobMaterialDispatch;
        $queryData['select'] = "job_material_dispatch.job_card_id,job_material_dispatch.dispatch_qty, job_card.product_id";
        $queryData['join']['job_card'] = "job_material_dispatch.job_card_id = job_card.id";
        $queryData['where']['job_material_dispatch.dispatch_item_id'] = $data['item_id'];
        // $queryData['where']['job_card.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'job_material_dispatch.job_card_id';
		$result = $this->rows($queryData);
		
		$qtyData = New StdClass;
		$qtyData->rQty = 0; $qtyData->aQty = 0; $qtyData->udQty = 0;$qtyData->insQty = 0;
		if(!empty($result)):
			foreach($result as $row):
				
				$queryData = Array();
				$queryData['tableName'] = $this->jobRejection;
				$queryData['select'] = 'SUM(qty) as rejQty,SUM(pending_qty) as pendingRejQty';
				$queryData['where']['job_card_id'] = $row->job_card_id;
				$queryData['where']['rejection_type_id'] = -1;
				//$rejectionData = $this->row($queryData);
				
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

    public function getMeasuringDevice($type){
        $data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
    }
    
    /* NC Report */
    public function getNCReportData($data)
    {
        $data['tableName'] = "production_log";
        $data['select'] = "production_log.*,process_master.process_name,item_master.item_name,item_master.item_code,item_master.price,employee_master.emp_name,job_card.job_no,job_card.job_prefix,super.emp_name as supervisor,inspection_type.inspection_type as inspection_type_name";
        $data['leftJoin']['job_card'] = "job_card.id = production_log.job_card_id";
        $data['leftJoin']['item_master'] = "item_master.id = job_card.product_id";
        $data['leftJoin']['process_master'] = "process_master.id = production_log.process_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = production_log.operator_id";
        $data['leftJoin']['employee_master as super'] = "super.id = production_log.created_by";
        $data['leftJoin']['inspection_type'] = "inspection_type.id = production_log.inspection_type";
        $data['where']['production_log.prod_type'] = 2;
        $data['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
        return $this->rows($data);
    }
    
    //Vendor Gauge Report Data //*Created By Meghavi*//
    public function getVendorGaugeData($data){
        $queryData = array();
        $queryData['tableName'] = "in_out_challan_trans";
        $queryData['select'] = 'in_out_challan_trans.*,in_out_challan.party_name,in_out_challan.challan_date,in_out_challan.challan_no,in_out_challan.challan_prefix';
        $queryData['leftJoin']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$queryData['where']['in_out_challan_trans.is_returnable'] = 1;
        $queryData['where']['in_out_challan.party_id'] = $data['party_id'];
        $queryData['customWhere'][] = " in_out_challan.challan_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['order_by'][' in_out_challan_trans.return_date'] = 'DESC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    //Raw Material Tesing  Report Data //*Created By Meghavi*//
    public function getRmTestingRegister($data){
        $queryData = array();
        $queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'grn_transaction.*,grn_master.party_id,grn_master.grn_date,party_master.party_name,item_master.item_name,item_master.material_grade,unit_master.unit_name';
        $queryData['leftJoin']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = grn_transaction.unit_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = grn_transaction.item_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = grn_master.party_id";
        $queryData['where']['grn_transaction.item_type'] = 3;
        $queryData['customWhere'][] = " grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$result = $this->rows($queryData);
		return $result;
    }
    
        //@24/08/2022
	public function getJobcardListFromInspection(){
        $data['tableName'] = $this->processInspection;
        $data['select'] = 'process_inspection.*,item_master.item_code,item_master.item_name,job_card.job_no,job_card.job_prefix';
        $data['join']['item_master'] = 'item_master.id = process_inspection.product_id';
        $data['join']['job_card'] = 'job_card.id = process_inspection.job_card_id';
		$data['group_by'][] = "process_inspection.job_card_id";
        return $this->rows($data); 
    }

	public function getProcessFromInspection($job_card_id){
        $data['tableName'] = $this->processInspection;
		$data['select'] = 'process_inspection.*,process_master.process_name';
        $data['join']['process_master'] = 'process_master.id = process_inspection.process_id';
		$data['where']['job_card_id'] = $job_card_id;
		$data['group_by'][] = "process_inspection.process_id";
        return $this->rows($data);
    }
    
    public function getLineInspectionForReport($data){
        $data['tableName'] = $this->processInspection;
		$data['where']['product_id'] = $data['item_id'];
		$data['where']['process_id'] = $data['process_id'];
		$data['where']['insp_date'] = $data['to_date'];
        return $this->rows($data);
    }
    
    public function getRejectionMonitoring($data){
		$queryData = array();
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "job_transaction.*,item_master.item_code,item_master.item_name,item_master.price,process_master.process_name,shift_master.shift_name,employee_master.emp_name,mc.item_name as machine_name,mc.item_code as machine_code,rejection_comment.remark as rejection_reason,rrStage.process_name as rejection_stage,rej_rw_management.remark as rej_remark,party_master.party_name as vendor_name";

        $queryData['leftJoin']['rej_rw_management'] = "rej_rw_management.id = job_transaction.rej_rw_manag_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $queryData['leftJoin']['job_transaction as okt'] = "job_transaction.ref_id = okt.id";
        $queryData['leftJoin']['process_master'] = "okt.process_id = process_master.id";
        $queryData['leftJoin']['shift_master'] = "okt.shift_id = shift_master.id";
        $queryData['leftJoin']['employee_master'] = "okt.operator_id = employee_master.id";
        $queryData['leftJoin']['item_master as mc'] = "mc.id = okt.machine_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['process_master as rrStage'] = "job_transaction.rr_stage = rrStage.id";
        $queryData['leftJoin']['party_master'] = "rejection_comment.id = rej_rw_management.rr_by";

        $queryData['where']['job_transaction.entry_type'] = 1;
        $queryData['where']['job_transaction.entry_date >= '] = $data['from_date'];
        $queryData['where']['job_transaction.entry_date <= '] = $data['to_date'];
	
		
		if (!empty($data['item_id'])) {
			$queryData['where_in']['job_card.product_id'] = $data['item_id'];
		}
		
		return $this->rows($queryData);
	}
	
	/* Created At : 03-12-2022 [Milan Chauhan] */
    public function getMonthlyRejectionRegister($data){
        $queryData = array();
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "job_transaction.entry_date,job_transaction.product_id,item_master.item_name,
        SUM( CASE WHEN job_transaction.entry_type = 1 THEN job_transaction.qty ELSE 0 END) as total_rej_qty,
        SUM( CASE WHEN (job_transaction.entry_type=1 AND rej_rw_management.opr_mt = 'Turning' ) THEN job_transaction.qty ELSE 0 END ) AS turning_rej_qty,
        SUM( CASE WHEN (job_transaction.entry_type=1 AND rej_rw_management.opr_mt = 'Milling') THEN job_transaction.qty ELSE 0 END ) AS milling_rej_qty,
        SUM( CASE WHEN (job_transaction.entry_type=1 AND (rej_rw_management.opr_mt = 'Casting' OR rej_rw_management.opr_mt = 'Material' OR rej_rw_management.opr_mt = 'OutSource')) THEN job_transaction.qty ELSE 0 END ) AS cmo_rej_qty,
        SUM( CASE WHEN  (job_transaction.entry_type=1 AND (rej_rw_management.opr_mt = 'Other' OR rej_rw_management.opr_mt =null OR rej_rw_management.opr_mt ='')) THEN job_transaction.qty ELSE 0 END ) AS other_rej_qty,
        SUM( CASE WHEN job_transaction.entry_type=0 THEN job_transaction.qty ELSE 0 END ) as ok_qty,
        job_transaction.job_card_id,job_card.sales_order_id, trans_main.trans_prefix, trans_main.trans_no";

        $queryData['leftJoin']['rej_rw_management'] = "rej_rw_management.id = job_transaction.rej_rw_manag_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = job_transaction.product_id";
        $queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
        $queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        // $queryData['leftJoin']['job_transaction as okt'] = "job_transaction.ref_id = okt.id";

        // $queryData['where']['job_transaction.entry_type'] = 1;
        $queryData['where']['job_transaction.entry_date >= '] = $data['from_date'];
        $queryData['where']['job_transaction.entry_date <= '] = $data['to_date'];
        $queryData['group_by'][] = "job_transaction.entry_date";
        $queryData['group_by'][] = "job_transaction.job_card_id";

        $result = $this->rows($queryData);
        return $result;
    }

   /* DEVICE DATA * CREATED BY MEGHAVI @22/06/2023 */
    public function getCalibrationStatusForReport(){
		$data['tableName'] = 'qc_instruments';
		$data['select'] = 'qc_instruments.id,qc_instruments.item_name,qc_instruments.location_name,qc_instruments.make_brand,qc_instruments.size,qc_instruments.mfg_sr,qc_instruments.description,qc_instruments.cal_freq,qc_instruments.last_cal_date,qc_instruments.next_cal_date';
		$data['where_in']['qc_instruments.item_type'] = [1,2];
        return $this->rows($data);
	}

	public function getReworkMonitoring($data){
		$queryData = array();
		$queryData['tableName'] = "job_transaction";
		$queryData['select'] = 'job_transaction.entry_date,job_transaction.qty as rw_qty,job_card.job_number,job_card.qty as job_qty,party_master.party_name,item_master.item_code,item_master.item_name';
		$queryData['leftJoin']['job_card'] = 'job_transaction.job_card_id = job_card.id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['where_in']['job_transaction.entry_type'] = 2;
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		return $this->rows($queryData);
	}
	
	public function getGaugeInstrumentData(){
        $data['tableName'] = 'qc_instruments';
        $data['where']['status != '] = 4;
        $data['order_by']['item_type'] = 'ASC';
		return $this->rows($data);
    }
}
?>