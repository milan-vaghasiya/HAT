<?php
class ProductionReportNewModel extends MasterModel
{
	private $jobCard = "job_card";
	private $jobOutward = "job_outward";
	private $jobRejection = "job_rejection";
	private $empMaster = "employee_master";
	private $jobTrans = "job_transaction";
	private $itemMaster = "item_master";
	private $JobWorkChallan = "jobwork_challan";
	private $itemKit = "item_kit";
	private $production_log = "production_log";	
	private $productionTrans="production_transaction";
	private $product_process="product_process";
    private $processInspection = "process_inspection";

	public function getJobcardList()
	{
		$data['tableName'] = $this->jobCard;
		$data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.wo_no, job_card.job_date,item_master.item_code,item_master.item_name';
		$data['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		//$data['where']['version'] = 2;
		return $this->rows($data);
	}
	
	public function getJobWiseProduction($data)
	{
		$jobData = $this->jobcard->getJobcard($data['job_id']);

		$thead = '<tr><th colspan="10">Job Card : ' . $jobData->job_number . '</th></tr>
				    <tr>
				    	<th>#</th>
				    	<th>Date</th>
				    	<th>Process Name</th>
				    	<th>OK Qty.</th>
				    	<th>Reject Qty.</th>
				    	<th>Rework Qty.</th>
				    	<th>Operator</th>
				    	<th>Machine</th>
				    </tr>';
		$tbody = '';
		$i = 1;

		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = " job_transaction.*,item_master.item_code,employee_master.emp_name,process_master.process_name,rejection.qty as rej_qty,rework.qty as rw_qty";
		$queryData['leftJoin']['item_master'] = " job_transaction.machine_id = item_master.id";
		$queryData['join']['process_master'] = "process_master.id =  job_transaction.process_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id =  job_transaction.operator_id";
		$queryData['leftJoin']['rej_rw_management as rejection'] = "rejection.id =  job_transaction.rej_rw_manag_id AND rejection.operation_type = 1";
		$queryData['leftJoin']['rej_rw_management as rework'] = "rework.id =  job_transaction.rej_rw_manag_id AND rework.operation_type = 2";
		$queryData['where']['job_transaction.job_card_id'] = $data['job_id'];
		$queryData['where']['job_transaction.entry_type'] = 0;
		$result = $this->rows($queryData);

		if (!empty($result)) {
			foreach ($result as $row) {
				$tbody .= '<tr>';
				$tbody .= '<td class="text-center">' . $i++ . '</td>';
				$tbody .= '<td>' . formatDate($row->entry_date) . '</td>';
				$tbody .= '<td>' . $row->process_name . '</td>';
				$tbody .= '<td>' . floatVal($row->qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rej_qty) . '</td>';
				$tbody .= '<td>' . floatval($row->rw_qty) . '</td>';
				$tbody .= '<td>' . $row->emp_name . '</td>';
				$tbody .= '<td>' . $row->item_code . '</td>';
				$tbody .= '</tr>';
			}
		}

		return ['status' => 1, 'thead' => $thead, 'tbody' => $tbody];
	}
	
	public function getJobworkRegister($data){
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "job_transaction.*,outsource_challan.trans_number as challan_no,item_master.item_name,item_master.item_code,process_master.process_name,job_card.job_number,job_card.wo_no,party_master.party_name";
		$queryData['join']['item_master'] = "item_master.id = job_transaction.product_id";
		$queryData['leftJoin']['outsource_challan'] = "outsource_challan.id = job_transaction.challan_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = job_transaction.vendor_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
		$queryData['where']['job_transaction.vendor_id'] = $data['vendor_id'];
		$queryData['where']['job_transaction.entry_type'] = 3;
		//$queryData['where']['job_transaction.entry_date >= '] = $this->startYearDate;
        //$queryData['where']['job_transaction.entry_date <= '] = $this->endYearDate;
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getJobOutwardData($ref_id)
	{
		$queryData['tableName'] = $this->jobTrans;
		$queryData['where']['job_transaction.ref_id'] = $ref_id;
		$queryData['where_in']['job_transaction.entry_type'] = 4;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$queryData['order_by']['job_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getVendorRejectionSum($ref_id)
	{
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = "SUM(rej_qty) as rejectQty";
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['production_log.prod_type'] = 3;
		$result = $this->row($queryData);
		return $result;
	}

	public function getUsedMaterial($id)
	{
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.id'] = $id;
		return $this->row($data);
	}
	
	//Created By Karmi @05/07/2022 for JobWork Register Report
	public function getUsedMaterialByJobCardId($job_card_id){
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.job_card_id'] = $job_card_id;
		return $this->row($data);
	}

	/* Get Production Analysis Data */
	public function getProductionAnalysis($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		//$queryData['leftJoin']['machine_master'] = 'machine_master.id = job_outward.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['where']['job_transaction.out_qty != '] = 0;
		$queryData['where']['job_transaction.entry_type'] = 2;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i = 1;
		$prev_date = "";
		$pid = 0;
		$tbody = "";
		foreach ($result as $row) :
			$rjqty = 0;
			$rwqty = 0;
			$rjRatio = 0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineData)) ? $machineData->item_code : "";
			if ($prev_date != $row->entry_date or $pid != $row->process_id) {
				$queryData = array();
				$queryData['select'] = "SUM(rejection_qty) as qty";
				$queryData['tableName'] = $this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;

				$queryData = array();
				$queryData['select'] = "SUM(rework_qty) as qty";
				$queryData['tableName'] = $this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;

				if (!empty($row->out_qty) and $row->out_qty > 0) :
					$rjRatio = round((($rjqty * 100) / $row->out_qty), 2);
				endif;
			}

			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . formatDate($row->entry_date) . '</td>
						<td>' . $machineNo . '</td>
						<td>' . $row->shift_name . '</td>
						<td>' . $row->emp_name . '</td>
						<td>' . $row->item_code . '</td>
						<td>' . $row->production_time . '</td>
                        <td>' . $row->process_name . '</td>
                        <td>' . $row->cycle_time . '</td>
						<td>' . $row->out_qty . '</td>
						<td>' . $rwqty . '</td>
						<td>' . $rjqty . '</td>
						<td>' . $rjRatio . '%</td>
					</tr>';
			$prev_date = $row->entry_date;
			$pid = $row->process_id;
		endforeach;
		return ['status' => 1, 'tbody' => $tbody];
	}

	/* Machine Wise Production OEE */
	public function getDepartmentWiseMachine($dept_id)
	{
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.location'] = $dept_id;
		$result = $this->rows($data);
		return $result;
	}

	public function getMachineWiseProduction($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'SUM(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.qty ELSE 0 END) as ok_qty,
		SUM(CASE WHEN job_transaction.entry_type = 8 THEN job_transaction.production_time ELSE 0 END) as idle_time,
		SUM(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.production_time ELSE 0 END ) as total_production_time,
		AVG(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.cycle_time ELSE 0 END) as m_ct,
		SUM(rej_rw_management.qty) as rej_qty,
		job_transaction.entry_date,job_transaction.shift_id,shift_master.shift_name,item_master.item_code as mc_code,item_master.item_name as mc_name,process_master.process_name,job_transaction.machine_id,fg.item_code';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_transaction.machine_id';
		$queryData['leftJoin']['item_master as fg'] = 'fg.id = job_transaction.product_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['leftJoin']['rej_rw_management'] = 'rej_rw_management.job_trans_id = job_transaction.id AND rej_rw_management.entry_type = 1';
		if(!empty($data['machine_id'])){$queryData['where']['job_transaction.machine_id'] = $data['machine_id'];}
		if(!empty($data['process_id'])){$queryData['where']['job_transaction.process_id'] = $data['process_id'];}
		$queryData['where_in']['job_transaction.entry_type'] = '0,8';
		$queryData['where']['job_transaction.process_id > '] = 0;
		$queryData['where']['job_transaction.rej_rw_manag_id'] = 0;
		$queryData['where']['job_transaction.entry_date'] = $data['date'];
		$queryData['group_by'][] = "job_transaction.machine_id";
		$queryData['group_by'][] = "job_transaction.product_id";
		$queryData['group_by'][] = "job_transaction.process_id";
		$queryData['group_by'][] = "job_transaction.entry_date";
		$queryData['group_by'][] = "job_transaction.shift_id";
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		return $result;
	}
	public function getIdleTimeReasonMachineWise($log_date, $shift_id, $machine_id)
	{
		$queryData = array();
        $queryData['tableName'] = 'rejection_comment';
        $queryData['select'] = "rejection_comment.code,SUM(IFNULL(job_transaction.production_time,0)) as idle_time";
		$queryData['leftJoin']['job_transaction'] = "rejection_comment.id = job_transaction.rr_reason AND job_transaction.entry_type = 8 AND job_transaction.entry_date ='".$log_date."' AND job_transaction.shift_id =".$shift_id." AND job_transaction.machine_id =".$machine_id." AND job_transaction.is_delete =0";
       	$queryData['where']['rejection_comment.type'] = 2;
		$queryData['group_by'][]='rejection_comment.id';
        $result = $this->rows($queryData);
		$td = '';
		foreach ($result as $row) {
			$td .= '<td>' . $row->idle_time . '</td>';
		}
		return $td;
	}

	/** Operator Wise OEE */
	public function getOperatorWiseProduction($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'SUM(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.qty ELSE 0 END) as ok_qty,
		SUM(CASE WHEN rej_rw_management.operation_type = 4 THEN rej_rw_management.qty ELSE 0 END) as cft_ok_qty,
		SUM(CASE WHEN rej_rw_management.operation_type = 1 AND rej_rw_management.entry_type = 3 THEN rej_rw_management.qty ELSE 0 END) as rej_qty,
		SUM(CASE WHEN rej_rw_management.operation_type = 1 AND rej_rw_management.entry_type != 3 THEN (rej_rw_management.qty-rej_rw_management.cft_qty) ELSE 0 END) as pend_rej_qty,
		SUM(CASE WHEN rej_rw_management.operation_type = 2 AND rej_rw_management.entry_type != 3 THEN (rej_rw_management.qty-rej_rw_management.cft_qty) ELSE 0 END) as pend_rw_qty,
		SUM(CASE WHEN rej_rw_management.operation_type = 3 AND rej_rw_management.entry_type != 3 THEN (rej_rw_management.qty-rej_rw_management.cft_qty) ELSE 0 END) as pend_hold_qty,
		SUM(CASE WHEN job_transaction.entry_type = 0 THEN production_time END) as total_production_time,
		AVG(CASE WHEN job_transaction.entry_type=0 THEN job_transaction.cycle_time ELSE 0 END) as m_ct,
		job_transaction.entry_date,shift_master.shift_name,employee_master.emp_name,process_master.process_name,item_master.item_code';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_transaction.product_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['leftJoin']['rej_rw_management'] = 'rej_rw_management.job_trans_id = job_transaction.id';
		if(!empty($data['operator_id'])){ $queryData['where']['job_transaction.operator_id'] = $data['operator_id']; }
		if(!empty($data['process_id'])){ $queryData['where']['job_transaction.process_id'] = $data['process_id']; }
		$queryData['where_in']['job_transaction.entry_type'] = '0';
		$queryData['where']['job_transaction.entry_date'] = $data['date'];
		$queryData['where']['job_transaction.process_id > '] = 0;
		$queryData['where']['job_transaction.rej_rw_manag_id'] = 0;
		
		$queryData['group_by'][] = "job_transaction.operator_id";
		$queryData['group_by'][] = "job_transaction.product_id";
		$queryData['group_by'][] = "job_transaction.process_id";
		$queryData['group_by'][] = "job_transaction.entry_date";
		$queryData['group_by'][] = "job_transaction.shift_id";
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		return $result;
	}

	
	/** General OEE Report */

	public function getOeeData($data)
	{
		/*$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,SUM(production_log.rej_qty) as rej_qty,SUM(production_log.rw_qty) as rw_qty,SUM(production_log.production_qty) as production_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,AVG(production_log.m_ct) as m_ct,production_log.log_date,production_log.shift_id,production_log.operator_id,production_log.process_id,production_log.load_unload_time,SUM(production_log.load_unload_time) as total_load_unload_time,SUM(production_log.cycle_time) as cycle_time,production_log.machine_id,,production_log.part_code,shift_master.shift_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,job_card.product_id,employee_master.emp_name,process_master.process_name';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'job_card.product_id = item_master.id';
		$queryData['leftJoin']['item_master as machine'] = 'production_log.machine_id = machine.id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['group_by'][] = "production_log.shift_id";
		$queryData['group_by'][] = "DATE(production_log.log_date)";
		$queryData['group_by'][] = "production_log.machine_id";
		$queryData['group_by'][] = "production_log.operator_id";
		$queryData['group_by'][] = "production_log.process_id";
		$queryData['group_by'][] = "job_card.product_id";
		$result = $this->rows($queryData);*/
		
		$queryData = array();
        $queryData['tableName'] = $this->production_log;
        $queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,pl.rej_qty,pl.rw_qty,SUM(production_log.production_qty) as production_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,AVG(production_log.cycle_time) as m_ct,production_log.log_date,production_log.shift_id,production_log.operator_id,production_log.process_id,production_log.load_unload_time,production_log.cycle_time,production_log.machine_id,SUM(production_log.load_unload_time) as total_load_unload_time,production_log.part_code,shift_master.shift_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,job_card.product_id,employee_master.emp_name,process_master.process_name';
        
        $queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
        $queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
        $queryData['leftJoin']['item_master'] = 'job_card.product_id = item_master.id';
        $queryData['leftJoin']['item_master as machine'] = 'production_log.machine_id = machine.id';
        $queryData['leftJoin']['employee_master'] = 'employee_master.id = production_log.operator_id';
        $queryData['leftJoin']['process_master'] = 'process_master.id = production_log.process_id';
        
        $queryData['leftJoin']['(select SUM(rej_qty) as rej_qty, SUM(rw_qty) as rw_qty,DATE(log_date) as log_date,shift_id,machine_id,operator_id,process_id,job_card_id from production_log where is_delete = 0 group by shift_id,DATE(log_date),machine_id,operator_id,process_id,job_card_id) as pl'] = "pl.shift_id = production_log.shift_id AND DATE(pl.log_date) = DATE(production_log.log_date) AND pl.machine_id = production_log.machine_id AND pl.operator_id = production_log.operator_id AND pl.process_id = production_log.process_id AND pl.job_card_id = production_log.job_card_id";
        
        $queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
        $queryData['where']['production_log.prod_type'] = 1;
        $queryData['where']['ok_qty >'] = 0;
        
        $queryData['group_by'][] = "production_log.shift_id";
        $queryData['group_by'][] = "DATE(production_log.log_date)";
        $queryData['group_by'][] = "production_log.machine_id";
        $queryData['group_by'][] = "production_log.operator_id";
        $queryData['group_by'][] = "production_log.process_id";
        $queryData['group_by'][] = "job_card.product_id";
        $result = $this->rows($queryData);
        //print_r($this->db->last_query()); exit;
		return $result;
	}

	public function getIdleTimeReasonForOee($data)
	{
	    $idleReasonList = $this->comment->getIdleReason();
	    $td = '';$totalIdleTime = 0;
	    foreach($idleReasonList as $row):
	    
    		$queryData = array();
    		$queryData['tableName'] = $this->jobTrans;
    		$queryData['select'] = 'SUM(job_transaction.production_time) as idle_time';
    		
    		if(!empty($data['entry_date'])): $queryData['where']['job_transaction.entry_date'] = $data['entry_date']; endif;
    		if(!empty($data['shift_id'])): $queryData['where']['job_transaction.shift_id'] = $data['shift_id']; endif;
    		if(!empty($data['machine_id'])): $queryData['where']['job_transaction.machine_id'] = $data['machine_id']; endif;
    		if(!empty($data['process_id'])): $queryData['where']['job_transaction.process_id'] = $data['process_id']; endif;
    		if(!empty($data['operator_id'])): $queryData['where']['job_transaction.operator_id'] = $data['operator_id']; endif;
    		if(!empty($data['product_id'])): $queryData['where']['job_transaction.product_id'] = $data['product_id']; endif;
    		if(!empty($data['job_card_id'])): $queryData['where']['job_transaction.job_card_id'] = $data['job_card_id']; endif;
    		
    		$queryData['where']['job_transaction.entry_type'] = 8;
    		$queryData['where']['job_transaction.rr_reason'] = $row->id;
    		$result = $this->row($queryData);
    		
    		if(!empty($result->idle_time)):
    		    $td .= '<td class="bg-light">' . $result->idle_time . '</td>';
    		else:
    		    $td .= '<td class="">0</td>'; 
    		endif;
    		$totalIdleTime = (!empty($result->idle_time))?$result->idle_time:0;
    	endforeach;
		
		return ['td' => $td, 'total_idle_time' => $totalIdleTime];
	}

	/* Stage Wise Production */
	public function getProductList()
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'item_master.id,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id= job_card.product_id';
		//$queryData['where_in']['job_card.order_status'] = [0,1,2,3];
		$queryData['group_by'][] = 'job_card.product_id';
		$queryData['order_by']['item_master.item_code'] = 'ASC';
		return $this->rows($queryData);
	}

	public function getJobs($data)
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['customWhere'][] = "job_card.job_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where']['product_id'] = $data['item_id'];
		}
		$queryData['where_in']['job_card.order_status'] = [0, 1, 2, 3];
		return $this->rows($queryData);
	}

	public function getJobsByTrans($data)
	{
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_number,job_card.wo_no,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty, job_card.qty as job_qty,item_master.item_name, item_master.item_code, party_master.party_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['join']['job_transaction'] = 'job_transaction.job_card_id = job_card.id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['party_id'])) {
			$queryData['where']['item_master.party_id'] = $data['party_id'];
		}
		$queryData['group_by'][] = 'job_card.id';
		return $this->rows($queryData);
	}

	public function getStageWiseProduction($data)
	{
		$jobData = $this->getJobsByTrans($data);
		$allProcess = array();
		if (!empty($jobData)) :
			foreach ($jobData as $row) :
				$allProcess = array_merge(explode(',', $row->process), $allProcess);
			endforeach;
		endif;

		$processList = array_unique($allProcess);
		return ['jobData' => $jobData, "processList" => $processList];
	}

	public function getProductionQty($job_card_id, $process_id)
	{
		$queryData['tableName'] = 'job_transaction';
		$queryData['select'] = "SUM(qty) as qty";
		$queryData['where']['entry_type'] = 0;
		$queryData['where']['job_card_id'] = $job_card_id;
		$queryData['where']['process_id'] = $process_id;
		$result = $this->row($queryData);
		return $result;
	}

	/* Job card Register */
	public function getJobcardRegister()
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.*,party_master.party_name,party_master.party_code,item_master.item_code,employee_master.emp_name,GROUP_CONCAT(DISTINCT job_heat_trans.batch_no) as batch_no';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_card.created_by';
		$queryData['leftJoin']['job_heat_trans'] = 'job_heat_trans.job_card_id = job_card.id';
        $data['where']['job_card.job_date >= '] = $this->startYearDate;
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
		$queryData['group_by'][] = 'job_card.id';
		return $this->rows($queryData);
	}

	/* Operator Monitoring */
	public function getOperatorList()
	{
		$data['tableName'] = $this->empMaster;
		$data['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name";
		$data['where']['employee_master.emp_role !='] = "-1";
		$data['where']['employee_master.emp_designation'] = 11;
		return $this->rows($data);
	}

	public function getOperatorMonitoring($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id, machine.item_code as machine_no';
		$queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['item_master as machine'] = 'machine.id = job_transaction.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['where']['job_transaction.entry_type'] = 0;
		$queryData['where']['employee_master.id'] = $data['emp_id'];
		if($data['type'] == 1){$queryData['where']['job_transaction.qty != '] = 0;}
		if($data['type'] == 2){
			$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "' AND (job_transaction.rej_qty != 0 OR production_log.rw_qty != 0)";
		}else{
			$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		}
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		return $this->rows($queryData);
	}

	public function getRejectionReworkMonitoring($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,item_master.item_code, item_master.price, process_master.process_name,shift_master.shift_name,employee_master.emp_name,party_master.party_name,vendor.party_name as vendor_name';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_transaction.product_id';
		$queryData['leftJoin']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_transaction.rework_from';
		$queryData['leftJoin']['party_master as vendor'] = 'vendor.id = job_transaction.rejection_from';
		$queryData['where_in']['job_transaction.entry_type'] = ['2,3'];
		if ($data['rtype'] == 2) {
			$queryData['where']['job_transaction.rejection_qty >'] = 0;
		}
		/*$queryData['where']['job_transaction.rejection_qty >'] = 0;
		$queryData['where']['job_transaction.rework_qty >'] = 0;*/
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		if (!empty($data['item_id'])) {
			$queryData['where_in']['job_transaction.product_id'] = $data['item_id'];
		}
		$jobtransaction = $this->rows($queryData);

		$tbody = '';
		$tfoot = '';
		$clspan = 13;
		if (!empty($jobtransaction)) :
			$i = 1;
			$totalRejectCost = 0;
			foreach ($jobtransaction as $row) :
				if ($row->rejection_qty > 0 || $row->rework_qty > 0) :
					$machine = (!empty($row->machine_id)) ? $this->item->getItem($row->machine_id)->item_code : '';
					$rejection_stage = (!empty($row->rejection_stage)) ? $this->process->getProcess($row->rejection_stage)->process_name : "";
					$rejection_reason = (!empty($row->rejection_reason)) ? $this->comment->getComment($row->rejection_reason)->remark : "";
					$rework_reason = (!empty($row->rework_reason)) ? $this->comment->getComment($row->rework_reason)->remark : "";

					$rework_stage = '';
					$reworkStage = (!empty($row->rework_process_id)) ? explode(',', $row->rework_process_id) : "";
					if (!empty($reworkStage)) {
						$r = 1;
						foreach ($reworkStage as $rework_process_id) :
							if ($r == 1) {
								$rework_stage .= $this->process->getProcess($rework_process_id)->process_name;
							} else {
								$rework_stage .= ', ' . $this->process->getProcess($rework_process_id)->process_name;
							}
							$r++;
						endforeach;
					}

					$item_price = $row->price;
					$jobCardData = $this->jobcard->getJobCard($row->job_card_id);
					if ($jobCardData->party_id > 0) :
						$partyData = $this->party->getParty($jobCardData->party_id);
						if ($partyData->currency != 'INR') :
							$inr = $this->salesReportModel->getCurrencyConversion($partyData->currency);
							if (!empty($inr)) : $item_price = $inr[0]->inrrate * $row->price;
							endif;
						else :
							$item_price = $row->price;
						endif;
					endif;

					$rejectCost = $row->rejection_qty * $item_price;

					$tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->entry_date) . '</td>
                        <td>' . $row->item_code . '</td>
                        <td>' . $row->process_name . '</td>
                        <td>' . $row->shift_name . '</td>
                        <td>' . $machine . '</td>
                        <td>' . $row->emp_name . '</td>
                        <td>' . $row->issue_batch_no . '</td>';
					if ($data['rtype'] == 1) :
						$clspan = 18;
						$tbody .= ' <td>' . $row->rework_qty . '</td>
                        <td>' . $rework_reason . '</td>
                        <td>' . $row->rework_remark . '</td>
    					<td>' . $rework_stage . '</td>
    					<td>' . (!empty($row->party_name) ? $row->party_name : 'IN HOUSE') . '</td>';
					endif;
					$tbody .= '<td>' . $row->rejection_qty . '</td>
                        <td>' . $rejection_reason . '</td>
                        <td>' . $row->rejection_remark . '</td>
                        <td>' . $rejection_stage . '</td>
						<td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>
                        <td>' . $rejectCost . '</td>
    				</tr>';
					$totalRejectCost += $rejectCost;
				endif;
			endforeach;

			$tfoot .= '<tr class="thead-info">
				<th colspan="' . $clspan . '" class="text-right">Total Reject Cost</th>
				<th>' . $totalRejectCost . '</th>
			</tr>';
		endif;
		return ['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot];
	}

	public function getOperatorPerformance($data)
	{
		$data['tableName'] = $this->production_log;
		$data['select'] = 'SUM(production_log.production_qty) as production_qty,SUM(production_log.production_qty) as ok_qty,SUM(idle_time) as idle_time,SUM(production_time) as production_time,AVG(production_log.m_ct) as m_ct,SUM(production_log.load_unload_time) as load_unload_time,production_log.log_date,production_log.shift_id,item_master.item_code, item_master.item_name,production_log.cycle_time';
		$data['join']['job_card'] = "job_card.id = production_log.job_card_id";
		$data['join']['item_master'] = "item_master.id = job_card.product_id";
		$data['where']['production_log.operator_id'] = $data['emp_id'];
		$data['where']['production_log.prod_type'] = 1;
		$data['group_by'][]='production_log.log_date';
		$data['group_by'][]='job_card.product_id';
		$data['customWhere'][] = "production_log.log_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$performance = $this->rows($data);

		$tbody = '';
		$i = 1;
		// print_r($performance);exit;
		if (!empty($performance)) :
			foreach ($performance as $row) :

				$cycle_time=0;
				if (!empty($row->production_time) and !empty($row->cycle_time)) {
					$cycle_time=secondsToMinutes(($row->cycle_time*$row->production_qty));
					$planQty = round($row->production_time / ($cycle_time), 0);
					$productivity =  round((($row->production_qty * 100) / $row->production_time), 2);
				} else {
					$planQty = 0;
					$productivity = 0;
				}

				$tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->log_date) . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . $row->production_time . '</td>
                    <td>' . $cycle_time . '</td>
                    <td>' . $planQty . '</td>
                    <td>' . floatval($row->production_qty) . '</td>
                    <td>' . $productivity . ' %</td>
				</tr>';
			endforeach;
		endif;
		return ['status' => 1, 'tbody' => $tbody];
	}

	public function getJobChallan($id = '')
	{
		$data['tableName'] = $this->JobWorkChallan;
		$data['customWhere'][] = "FIND_IN_SET('" . $id . "', job_inward_id)";
		return $this->rows($data);
	}

	public function getItemWiseBom($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name';
		$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
		if (!empty($data['item_id'])) {
			$queryData['where']['item_kit.item_id'] = $data['item_id'];
		}
		$result = $this->rows($queryData);
		$i = 1;
		$tbody = "";
		foreach ($result as $row) :
			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . $row->item_name . '</td>
						<td>' . $row->qty . '</td>
					</tr>';
		endforeach;
		return ['status' => 1, 'tbody' => $tbody];
	}

	public function getProductionBomData($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		if (!empty($data['ref_item_id'])) {
			$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];
		}
		$result = $this->rows($queryData);
		$i = 1;
		$tbody = "";
		foreach ($result as $row) :
			$tbody .= '<tr class="text-center">
						<td>' . $i++ . '</td>
						<td>' . $row->item_name . '</td>
						<td>' . $row->qty . '</td>
					</tr>';
		endforeach;
		return ['status' => 1, 'tbody' => $tbody];
	}

	public function getRmPlaning($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name,um.unit_name as uname,itm.qty as ref_qty';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		$queryData['join']['item_master itm'] = "item_kit.ref_item_id = itm.id";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['leftJoin']['unit_master um'] = "um.id = itm.unit_id";
		if (!empty($data['ref_item_id'])) {
			$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];
		}
		$result = $this->rows($queryData);

		return $result;
	}

	public function getStockTrans($item_id, $location_id)
	{
		$queryData = array();
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(qty) as stock_qty,batch_no,location_id,ref_type";
		$queryData['where']['item_id'] = $item_id;
		$queryData['where']['location_id'] = $location_id;
		$queryData['order_by']['id'] = "asc";
		$queryData['group_by'][] = "batch_no";
		return $this->rows($queryData);
	}

	public function getJobcardWIPQty($item_id)
	{
		$queryData = array();
		$queryData['tableName'] = "job_card";
		$queryData['select'] = "SUM(qty) as qty";
		$queryData['where']['product_id'] = $item_id;
		$result = $this->row($queryData);
		return $result;
	}

	public function getPrdLogOnJob($job_card_id)
    {
        $queryData['tableName'] = "production_log";
        $queryData['select'] = 'SUM(rej_qty) as rejection_qty,SUM(rw_qty) as rework_qty,SUM(ok_qty) as ok_qty,SUM(production_qty) as production_qty';
        $queryData['where']['production_log.job_card_id'] = $job_card_id;
        return $this->row($queryData);
    }
    
    public function getIdleTimeReasonForDailyOee($fromDate, $to_date, $dept_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'production_log.idle_reason';
		$queryData['leftJoin']['item_master'] = 'item_master.id = production_log.machine_id';
		//$queryData['where']['DATE(production_log.log_date)'] = $log_date;
		$queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $fromDate . "' AND '" . $to_date . "'";
		$queryData['where']['item_master.location'] = $dept_id;
		$queryData['where']['production_log.prod_type'] = 1;

		$result = $this->rows($queryData);
		$idleReasonList = $this->comment->getIdleReason();
		$td = '';$cls='';
		if (!empty($idleReasonList)) {
			foreach ($idleReasonList as $idl) {
				$idleTime = 0;$cls='';
				foreach ($result as $row) {
					$idleReasonData = !empty($row->idle_reason) ? json_decode($row->idle_reason) : '';

					if (!empty($idleReasonData)) {
						foreach ($idleReasonData as $row1) {
							if ($row1->idle_reason_id == $idl->id) {
								$idleTime += $row1->idle_time;
							}
						}
					}
				}
				$cls=($idleTime > 0) ? 'bg-light' : '';
				$td .= '<td class="'.$cls.'">' . number_format($idleTime/60,2) . '</td>';
			}
		}
		return $td;
	}

	public function getDepartmentWiseOee($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->production_log;
		$queryData['select'] = 'SUM(production_log.ok_qty) as ok_qty,SUM(production_log.rej_qty) as rej_qty,SUM(production_log.rw_qty) as rw_qty,SUM(production_log.production_qty) as production_qty,SUM(idle_time) as idle_time,SUM(production_time) as shift_hour,AVG(production_log.m_ct) as m_ct,production_log.log_date,production_log.shift_id,production_log.operator_id,production_log.process_id,production_log.load_unload_time,SUM(production_log.load_unload_time) as total_load_unload_time,production_log.cycle_time,production_log.machine_id,,production_log.part_code,shift_master.shift_name,item_master.item_name,item_master.item_code,machine.item_code as machine_code,machine.machine_hrcost,job_card.product_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = production_log.shift_id';
		$queryData['leftJoin']['job_card'] = 'job_card.id = production_log.job_card_id';
		$queryData['leftJoin']['item_master'] = 'job_card.product_id = item_master.id';
		$queryData['leftJoin']['item_master as machine'] = 'production_log.machine_id = machine.id';
		
		
		if(!empty($data['fromDate'])){ 
		    $queryData['customWhere'][] = "DATE(production_log.log_date) BETWEEN '" . $data['fromDate'] . "' AND '" . $data['date'] . "'"; 
		} else { 
		    $queryData['where']['DATE(production_log.log_date)'] = $data['date']; 
		}
		
		$queryData['where']['machine.location'] = $data['dept_id'];
		$queryData['where']['production_log.prod_type'] = 1;
		$queryData['group_by'][] = "production_log.shift_id";
		$queryData['group_by'][] = "DATE(production_log.log_date)";
		$queryData['group_by'][] = "production_log.machine_id";
		$queryData['group_by'][] = "production_log.operator_id";
		$queryData['group_by'][] = "production_log.process_id";
		$queryData['group_by'][] = "job_card.product_id";
		$result = $this->rows($queryData);
		return $result;
	}
	
	public function getVendorTrackingData($data){
		$queryData = array();
		$queryData['tableName'] = $this->JobWorkChallan;
		$queryData['select'] ='jobwork_challan.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = jobwork_challan.vendor_id';
		if(!empty($data['vendor_id'])){ $queryData['where']['jobwork_challan.vendor_id'] = $data['vendor_id']; }
		$queryData['customWhere'][] = "jobwork_challan.challan_date BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		return $this->rows($queryData);
	}
	
	//Created By Karmi @10/08/2022
	public function getFGPlaning($data){
		$queryData = array();
			$queryData['tableName'] = $this->itemKit;
			$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name';
			$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
			$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
			if (!empty($data['fg_item_id'])) {
				$queryData['where']['item_kit.item_id'] = $data['fg_item_id'];
			}
			$result = $this->rows($queryData);

		return $result;
	}
	
    public function getJobCosting($data)
	{
		/*$queryData['tableName'] = "vendor_production_trans";
		$queryData['select'] = "vendor_production_trans.*,job_work_order.jwo_no,job_work_order.jwo_prefix,item_master.item_name,item_master.item_code,process_master.process_name,job_card.process,unit_master.unit_name as punit";
		$queryData['join']['item_master'] = "item_master.id = vendor_production_trans.product_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['job_work_order'] = "job_work_order.id = vendor_production_trans.job_order_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = vendor_production_trans.process_id";
		$queryData['leftJoin']['production_transaction'] = "production_transaction.id = vendor_production_trans.ref_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = vendor_production_trans.job_card_id";
		$queryData['where']['vendor_production_trans.vendor_id'] = $data['vendor_id'];
		$queryData['customWhere'][] = "DATE(production_transaction.entry_date) BETWEEN '" . $data['from_date'] . "' AND '" . $data['to_date'] . "'";
		$result = $this->rows($queryData);*/
		
		$result = Array();
		return 
		$result = Array();;
	}
	
	//Created By Karmi @14/08/2022
    public function getCompletedJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        $data['where']['job_card.order_status'] = 4;
        $data['where']['job_card.job_date >= '] = $this->startYearDate;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
        $data['where']['job_card.job_date <= '] = $this->endYearDate;
        return $this->rows($data); 
    }
	
	//Created By Karmi @14/08/2022
	public function getJobCardWiseCosting($data,$process,$item_id)
	{
		$queryData = array();
		$queryData['tableName'] = $this->product_process;
		$queryData['select'] = 'product_process.*,process_master.process_name,SUM(production_transaction.out_qty) as outQty';
		$queryData['leftJoin']['production_transaction'] = "production_transaction.process_id = product_process.process_id";
 		$queryData['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$queryData['where']['product_process.process_id'] = $process;
		$queryData['where']['product_process.item_id '] = $item_id;
		$queryData['where']['production_transaction.job_card_id '] = $data['job_id'];
		$queryData['where']['production_transaction.is_delete'] = 0;
		$queryData['group_by'][] = "production_transaction.process_id";		
		return $this->rows($queryData);
	}
	
	/* Created At: 03-12-2022 [ Milan Chauhan ] */
	public function getProductionMonitoringData($data){
		$queryData = array();
		$queryData['tableName'] = "job_transaction";
		$queryData['select'] = "job_transaction.entry_date, job_transaction.job_card_id,job_transaction.operator_id , employee_master.emp_name as operator_name, job_transaction.production_time, job_transaction.cycle_time, job_transaction.load_unload_time, job_transaction.machine_id, job_transaction.process_id, job_transaction.product_id, machine.item_code as machine_code, job_transaction.shift_id, shift_master.shift_name, process_master.process_name,
		SUM(job_transaction.load_unload_time) as total_load_unload_time,
		SUM(job_transaction.qty) as ok_qty,
		SUM(job_transaction.production_time) as shift_hour,
		AVG(job_transaction.cycle_time) as m_ct,
		SUM(ifnull(jtl.rej_qty,0)) as rej_qty,
		SUM(ifnull(jtl.rw_qty,0)) as rw_qty,
		SUM(ifnull(jtl.hold_qty,0)) as hold_qty,
		SUM(job_transaction.qty + ifnull(jtl.rej_qty,0) + ifnull(jtl.rw_qty,0) + ifnull(jtl.hold_qty,0)) as production_qty";

		$queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
		$queryData['leftJoin']['shift_master'] = "shift_master.id = job_transaction.shift_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$queryData['leftJoin']['item_master AS machine'] = "machine.id = job_transaction.machine_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = job_transaction.operator_id";

		$queryData['leftJoin']['(
			SELECT SUM( CASE WHEN entry_type = 1 THEN qty ELSE 0 END ) as rej_qty,
			SUM( CASE WHEN entry_type = 2 THEN qty ELSE 0 END ) AS rw_qty,
			SUM( CASE WHEN entry_type = 5 THEN qty ELSE 0 END ) AS hold_qty,
			ref_id 
			FROM job_transaction 
			WHERE is_delete = 0 AND entry_type IN (1,2,5) AND job_card_id = '.$data['job_id'].' AND process_id = '.$data['process_id'].'
			GROUP BY ref_id
		) AS jtl'] = "jtl.ref_id = job_transaction.id";

		$queryData['where_in']['job_transaction.entry_type'] = [0,4];
		$queryData['where']['job_transaction.job_card_id'] = $data['job_id'];
		$queryData['where']['job_transaction.process_id'] = $data['process_id'];
		if(!empty( $data['machine_id']))
			$queryData['where']['job_transaction.machine_id'] = $data['machine_id'];

		$queryData['group_by'][] = "job_transaction.entry_date";
		$queryData['group_by'][] = "job_transaction.operator_id";
		$queryData['group_by'][] = "job_transaction.shift_id";
		$result = $this->rows($queryData);
		return $result;
	}
	
	/* Created At: 04-12-2022 [ Milan Chauhan ] */
	public function getDailyProductionLogSheet($logDate=""){
        $logDate = (!empty($logDate))?date("Y-m-d",strtotime($logDate)):date("Y-m-d");

        $queryData = array();
        $queryData['tableName'] = "job_transaction";
        $queryData['select'] = "employee_master.emp_name,machine_master.item_name as machine_name,shift_master.shift_name,item_master.item_name as product_name,job_card.job_number,job_card.wo_no,process_master.process_name,
		SUM(job_transaction.production_time) as total_production_time,
		SUM(job_transaction.cycle_time) as total_cycle_time,
		SUM(CASE WHEN job_transaction.entry_type = 0 THEN job_transaction.qty ELSE 0 END ) as total_ok_qty,job_approval.pre_finished_weight,job_approval.finished_weight,
		SUM(CASE WHEN job_transaction.entry_type = 1 THEN job_transaction.qty ELSE 0 END) as total_rej_qty,
		SUM(CASE WHEN job_transaction.entry_type = 2 THEN job_transaction.qty ELSE 0 END) as total_rw_qty,
		job_transaction.operator_id,job_transaction.machine_id,job_transaction.process_id,job_transaction.product_id,job_transaction.job_card_id,job_transaction.shift_id,trans_main.trans_prefix,trans_main.trans_no,job_card.sales_order_id";
		//SUM(production_log.total_idle_time) as total_idle_time,

        $queryData['leftJoin']['employee_master'] = "job_transaction.operator_id = employee_master.id";
        $queryData['leftJoin']['process_master'] = "job_transaction.process_id = process_master.id";
        $queryData['leftJoin']['shift_master'] = "job_transaction.shift_id = shift_master.id";
        $queryData['leftJoin']['job_card'] = "job_transaction.job_card_id = job_card.id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = job_card.sales_order_id";
        $queryData['leftJoin']['item_master'] = "job_transaction.product_id = item_master.id";
        $queryData['leftJoin']['item_master as machine_master'] = "job_transaction.machine_id = machine_master.id";
        $queryData['leftJoin']['job_approval'] = "job_transaction.job_approval_id = job_approval.id";

        $queryData['where']['job_transaction.entry_date'] = $logDate;
        $queryData['where']['job_transaction.process_id > '] = 0;
        $queryData['where']['job_transaction.vendor_id'] = 0;

        $queryData['group_by'][] = "job_transaction.operator_id";
        $queryData['group_by'][] = "job_transaction.machine_id";
        $queryData['group_by'][] = "job_transaction.process_id";
        $queryData['group_by'][] = "job_transaction.job_card_id";
        $queryData['group_by'][] = "job_transaction.shift_id";

        $result = $this->rows($queryData);

        $dataRows=array();$total_qty = 0;$estimated_qty = 0;
        if(!empty($result)):
            foreach($result as $row):
				$jobCardData = array();
				$jobCardData['id'] = $row->job_card_id;
				$jobCardData['product_id'] = $row->product_id;
				$jobCardData = (object)$jobCardData;
				$materialData = $this->jobcard->getMaterialIssueData($jobCardData);
				$row->rm_grade = (!empty($materialData['resultData']))?$materialData['resultData']['material_grade']:'';

				$row->total_idle_time = 0;

                //$productProcessData = $this->item->getProductProcessData($row->product_id,$row->process_id);
                
                $cycleTime = $row->total_cycle_time;
                
                //if(!empty($productProcessData)):
                //    $cycleTime = timeToSeconds($productProcessData->cycle_time);
                //endif;
                
                $total_qty = round($row->total_ok_qty +  $row->total_rej_qty);
                $estimated_qty = (!empty($row->total_production_time) && !empty($cycleTime))?(int) (($row->total_production_time * 60) / $cycleTime):0;

                $row->cycle_time = $cycleTime;
                $row->total_production_time = (!empty($row->total_production_time))?$row->total_production_time:0;
                $row->effecincy_per = (!empty($estimated_qty))?round(($total_qty*100)/$estimated_qty,2):0;
				$row->wo_no = $row->wo_no; //(!empty($row->sales_order_id))?getPrefixNumber($row->trans_prefix,$row->trans_no):"";
                
                $queryData = array();
				$queryData['tableName'] = "job_transaction";
                $queryData['select'] = "job_transaction.qty,rejection_comment.remark";
				$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
                $queryData['where']['job_transaction.operator_id'] = $row->operator_id;
                $queryData['where']['job_transaction.machine_id'] = $row->machine_id;
                $queryData['where']['job_transaction.process_id'] = $row->process_id;
                $queryData['where']['job_transaction.job_card_id'] = $row->job_card_id;
                $queryData['where']['job_transaction.shift_id'] = $row->shift_id;
                $queryData['where']['job_transaction.entry_type'] = 1;
                $queryData['where']['job_transaction.entry_date'] = $logDate;
        		$queryData['where']['job_transaction.vendor_id'] = 0;
                $rejData = $this->rows($queryData);

				$queryData = array();
                $queryData['tableName'] = "job_transaction";
                $queryData['select'] = "job_transaction.qty,rejection_comment.remark";
				$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = job_transaction.rr_reason";
                $queryData['where']['job_transaction.operator_id'] = $row->operator_id;
                $queryData['where']['job_transaction.machine_id'] = $row->machine_id;
                $queryData['where']['job_transaction.process_id'] = $row->process_id;
                $queryData['where']['job_transaction.job_card_id'] = $row->job_card_id;
                $queryData['where']['job_transaction.shift_id'] = $row->shift_id;
                $queryData['where']['job_transaction.entry_type'] = 2;
                $queryData['where']['job_transaction.entry_date'] = $logDate;
        		$queryData['where']['job_transaction.vendor_id'] = 0;
                $rewData = $this->rows($queryData);

				$row->rej_reason = (!empty($rejData))?implode(", ",array_column($rejData,'remark')):"";
				$row->rw_reason = (!empty($rewData))?implode(", ",array_column($rejData,'remark')):"";
                $row->idle_reason = "";

                $dataRows[] = $row;
            endforeach;
        endif;
        return $dataRows;
    }

	/* DEVICE DATA * CREATED BY MEGHAVI @22/06/2023 */
    public function getToolDataForReport($type=0){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = 'item_master.id,item_master.item_name,item_master.location,item_master.make_brand,item_master.size,item_master.rev_specification,item_master.description,location_master.location,st.stock_qty';
		$data['leftJoin']['location_master'] = 'location_master.id = item_master.location';
		$data['leftJoin']['(SELECT SUM(qty) as stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 GROUP BY item_id) as st'] = "st.item_id = item_master.id";
		$data['where']['item_master.sub_group'] = $type;
		return $this->rows($data);
	}

	/* Created By :- Sweta @08-08-2023 */
	public function getMaterialReqPlan($data){
		$queryData = array();
			$queryData['tableName'] = $this->itemKit;
			$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name';
			$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
			$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
			if (!empty($data['fg_item_id'])) {
				$queryData['where']['item_kit.item_id'] = $data['fg_item_id'];
			}
			$result = $this->rows($queryData);

		return $result;
	}


	public function getSetupSummaryData($postData){
        $queryData['tableName'] = $this->processInspection;
        $queryData['select'] = "process_inspection.insp_date,COUNT(process_inspection.id) as total_setup,SUM(process_inspection.setting_time) as setting_time,item_master.item_code,item_master.item_name,process_master.process_name,job_card.job_number,job_card.job_prefix,job_card.job_no,employee_master.emp_name,opr.emp_name as operator_name,mc.item_code as mc_code,mc.item_name as mc_name";
		$queryData['leftJoin']['item_master'] = "item_master.id = process_inspection.product_id";
		$queryData['leftJoin']['item_master mc'] = "mc.id = process_inspection.machine_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = process_inspection.process_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = process_inspection.job_card_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = process_inspection.inspector_id";
		$queryData['leftJoin']['employee_master opr'] = "opr.id = process_inspection.operator_id";
		$queryData['where']['process_inspection.report_type'] = 2;
		$queryData['where']['process_inspection.insp_date'] = $postData['date'];
		if(!empty($postData['setter_id'])){ $queryData['where']['process_inspection.inspector_id'] = $postData['setter_id']; }
		if(!empty($postData['process_id'])){ $queryData['where']['process_inspection.process_id'] = $postData['process_id']; }
		$queryData['group_by'][] = "process_inspection.inspector_id";
		$queryData['group_by'][] = "process_inspection.product_id";
		$queryData['group_by'][] = "process_inspection.process_id";
		$queryData['group_by'][] = "process_inspection.insp_date";
		return $this->rows($queryData);
    }
}
