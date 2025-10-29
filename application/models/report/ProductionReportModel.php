<?php 
class ProductionReportModel extends MasterModel
{
    private $jobCard = "job_card";
	private $jobOutward = "job_outward";    
	private $jobRejection = "job_rejection";
	private $empMaster = "employee_master";
	private $jobTrans = "job_transaction";
	private $itemMaster = "item_master";
	private $JobWorkChallan = "jobwork_challan";
	private $itemKit = "item_kit";

    public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        return $this->rows($data); 
    }

    //Updated By NYN @13/12/2021
    public function getJobWiseProduction($data)
	{
		$jobData = $this->jobcard->getJobcard($data['job_id']);
		
		$thead = '<tr><th colspan="10">Job Card : '.getPrefixNumber($jobData->job_prefix,$jobData->job_no).'</th></tr>
				    <tr>
				    	<th>#</th>
				    	<th>Date</th>
				    	<th>Process Name</th>
				    	<th>OK Qty.</th>
				    	<th>UD Qty.</th>
				    	<th>Reject Qty.</th>
				    	<th>Rework Qty.</th>
				    	<th>Operator</th>
				    	<th>Machine</th>
				    	<th>Shift</th>
				    </tr>';
		$tbody = ''; $i=1;

            $queryData = Array();
		    $queryData['tableName'] = $this->jobTrans;
		    $queryData['select'] = "job_transaction.*,item_master.item_code,employee_master.emp_name,shift_master.shift_name,process_master.process_name";
            $queryData['leftJoin']['item_master'] = "job_transaction.machine_id = item_master.id";
            $queryData['join']['shift_master'] = "shift_master.id = job_transaction.shift_id";
            $queryData['join']['process_master'] = "process_master.id = job_transaction.process_id";
            $queryData['join']['employee_master'] = "employee_master.id = job_transaction.operator_id";
		    $queryData['where']['job_transaction.job_card_id'] = $data['job_id'];
			$queryData['where']['job_transaction.entry_type'] = 2;
		    $result = $this->rows($queryData);

		    if(!empty($result))
		    {
		    	foreach($result as $row)
		    	{
		    		$tbody .= '<tr>';
		    			$tbody .= '<td class="text-center">'.$i++.'</td>';
		    			$tbody .= '<td>'.formatDate($row->entry_date).'</td>';
		    			$tbody .= '<td>'.$row->process_name.'</td>';
		    			$tbody .= '<td>'.floatVal($row->in_qty).'</td>';
		    			$tbody .= '<td>'.floatVal($row->ud_qty).'</td>';
                        $tbody .= '<td>'.floatval($row->rejection_qty).'</td>';
		    			$tbody .= '<td>'.floatval($row->rework_qty).'</td>';
                        $tbody .= '<td>'.$row->emp_name.'</td>';
		    			$tbody .= '<td>'.$row->item_code.'</td>';
		    			$tbody .= '<td>'.$row->shift_name.'</td>';
		    		$tbody .= '</tr>';
		    	}
		    }

        return ['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody];
    }

	public function getJobworkRegister($data)
	{
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "job_transaction.*,job_work_order.jwo_no,item_master.item_name,item_master.item_code,process_master.process_name,job_card.process,unit_master.unit_name as punit";
		$queryData['join']['item_master'] = "item_master.id = job_transaction.product_id";
		$queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$queryData['leftJoin']['job_work_order'] = "job_work_order.id = job_transaction.job_order_id";
		$queryData['leftJoin']['process_master'] = "process_master.id = job_transaction.process_id";
		$queryData['leftJoin']['job_card'] = "job_card.id = job_transaction.job_card_id";
		$queryData['where']['job_transaction.vendor_id'] = $data['vendor_id'];
		$queryData['where']['job_transaction.entry_type'] = 1;
		$result = $this->rows($queryData);
	   	return $result;
	}
	
	public function getJobOutwardData($ref_id)
	{
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "job_transaction.*,item_master.item_name,item_master.item_code";
		$queryData['join']['item_master'] = "job_transaction.product_id = item_master.id";
		$queryData['where']['job_transaction.ref_id'] = $ref_id;
		$queryData['where']['job_transaction.entry_type'] = 2;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$queryData['order_by']['job_transaction.id'] = 'ASC';
		$result = $this->rows($queryData);
	   	return $result;
	}
	
	public function getVendorRejectionSum($ref_id)
	{
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "SUM(rejection_qty) as rejectQty";
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['job_transaction.entry_type'] = 1;
		$result = $this->row($queryData);
	   	return $result;
	}

	public function getUsedMaterial($id){
		$data['tableName'] = 'job_used_material';
		$data['select'] = "job_used_material.*,item_master.item_name,item_master.item_code,item_master.item_type,unit_master.unit_name";
		$data['join']['item_master'] = "item_master.id = job_used_material.bom_item_id";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['job_used_material.id'] = $id;
		return $this->row($data);
	}

	/* Get Production Analysis Data */
	public function getProductionAnalysis($data){
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		//$queryData['leftJoin']['machine_master'] = 'machine_master.id = job_outward.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['job_transaction.out_qty != '] = 0;
		$queryData['where']['job_transaction.entry_type'] = 2;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i=1;$prev_date="";$pid=0;$tbody="";
		foreach($result as $row):
			$rjqty = 0;$rwqty = 0;$rjRatio=0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineData))?$machineData->item_code:"";
			if($prev_date != $row->entry_date or $pid != $row->process_id)
			{
				$queryData = array();
				$queryData['select'] = "SUM(rejection_qty) as qty";
				$queryData['tableName'] =$this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$rejectQty = $this->row($queryData);
				$rjqty = (!empty($rejectQty)) ? $rejectQty->qty : 0;
				
				$queryData = array();
				$queryData['select'] = "SUM(rework_qty) as qty";
				$queryData['tableName'] =$this->jobTrans;
				$queryData['where']['job_card_id'] = $row->job_id;
				$queryData['where']['process_id'] = $row->process_id;
				$queryData['where']['entry_date'] = $row->entry_date;
				$queryData['where']['entry_type'] = 1;
				$reworkQty = $this->row($queryData);
				$rwqty = (!empty($reworkQty)) ? $reworkQty->qty : 0;

				if(!empty($row->out_qty) AND $row->out_qty > 0):
					$rjRatio = round((($rjqty * 100) / $row->out_qty),2);		
				endif;		
			}

			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.formatDate($row->entry_date).'</td>
						<td>'.$machineNo.'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$row->item_code.'</td>
						<td>'.$row->production_time.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->cycle_time.'</td>
						<td>'.$row->out_qty.'</td>
						<td>'.$rwqty.'</td>
						<td>'.$rjqty.'</td>
						<td>'.$rjRatio.'%</td>
					</tr>';
			$prev_date = $row->entry_date; $pid = $row->process_id;
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
	}
	
	/* Stage Wise Production */
    public function getProductList(){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'item_master.id,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id= job_card.product_id';
        //$queryData['where_in']['job_card.order_status'] = [0,1,2,3];
        $queryData['group_by'][] = 'job_card.product_id';
        $queryData['order_by']['item_master.item_code'] = 'ASC';
        return $this->rows($queryData);
    }
	
    public function getJobs($data){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['customWhere'][] = "job_card.job_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['item_id'])){$queryData['where']['product_id'] = $data['item_id'];}
        $queryData['where_in']['job_card.order_status'] = [0,1,2,3];
        return $this->rows($queryData);
    }
	
	public function getJobsByTrans($data){
        $queryData['tableName'] = $this->jobCard;
        $queryData['select'] = 'job_card.id,job_card.job_no,job_card.job_prefix,job_card.job_date,job_card.product_id,job_card.process, job_card.total_out_qty, job_card.qty as job_qty,item_master.item_name, item_master.item_code';
		$queryData['join']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['join']['job_transaction'] = 'job_transaction.job_card_id = job_card.id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        if(!empty($data['item_id'])){$queryData['where']['job_card.product_id'] = $data['item_id'];}
        //$queryData['where_in']['job_card.order_status'] = [0,1,2,3];
        $queryData['group_by'][] = 'job_card.id';
        return $this->rows($queryData);
    }
	
	public function getStageWiseProduction($data){
		$jobData = $this->getJobsByTrans($data);
		$allProcess = Array();
		if(!empty($jobData)):
			foreach($jobData as $row):
				$allProcess = array_merge(explode(',',$row->process),$allProcess);
			endforeach;
		endif;
		
		$processList = array_unique($allProcess);		
		return ['jobData'=>$jobData,"processList"=>$processList];
	}
	
	public function getProductionQty($job_card_id,$process_id){
        $queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = "SUM(in_qty) as qty";
        $queryData['where']['job_card_id'] = $job_card_id;
        $queryData['where']['process_id'] = $process_id;
        $queryData['where']['entry_type'] = 3;
        $result = $this->row($queryData);
        //print_r($this->db->last_query());exit;
        return $result;
	}

	/* Job card Register */
	public function getJobcardRegister(){
		$queryData = array();
		$queryData['tableName'] = $this->jobCard;
		$queryData['select'] = 'job_card.*,party_master.party_name,party_master.party_code,item_master.item_code,employee_master.emp_name';
		$queryData['leftJoin']['party_master'] = 'party_master.id = job_card.party_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_card.created_by';
		$queryData['where']['job_card.job_category'] = 0;
		return $this->rows($queryData);
	}
		
	/* Machine Wise Production */
		public function getDepartmentWiseMachine($dept_id){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.location'] = $dept_id; 
		$result = $this->rows($data);
		return $result;
	}

	public function getMachineWiseProduction($data,$dept_id){
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name';
		$queryData['leftJoin']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_transaction.machine_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['job_transaction.out_qty != '] = 0;
		$queryData['where']['process_master.dept_id'] = $dept_id;
		$queryData['where']['job_transaction.machine_id'] = $data['machine_id'];
		$queryData['where']['job_transaction.entry_type'] = 2;
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i=1; $prev_date=""; $pid=0; $tbody=""; $okqty=0; $runtime=""; $qualityRate=0;$breakdownTime = 0; $otherdownTime = 0;
		$availability=0; $performance=0; $overall=0; $oee=0; $cs=0; $cm=0; $tc=0; $ps=0; $pm=0; $tp=0;
		$planQty=0;$runTime=0;
		$plannedProductionTime = 630;
		foreach($result as $row):

			$data1['tableName'] = 'machine_idle_logs';
			$data1['select'] = "SUM(idle_time_in_min) as breakdownTime";
			$data1['where']['process_id'] = $row->process_id;
			$data1['where']['machine_id'] = $row->machine_id;
			$data1['where']['job_card_id'] = $row->job_card_id;
			$data1['where']['breck_type'] = 0;
			$break_time = $this->row($data1);
			if(!empty($break_time)){ $breakdownTime = abs($break_time->breakdownTime); }

			$data2['tableName'] = 'machine_idle_logs';
			$data2['select'] = "SUM(idle_time_in_min) as breakdownTime";
			$data2['where']['process_id'] = $row->process_id;
			$data2['where']['machine_id'] = $row->machine_id;
			$data2['where']['job_card_id'] = $row->job_card_id;
			$data2['where']['breck_type'] = 1;
			$other_time = $this->row($data2);
			if(!empty($other_time)){ $otherdownTime = abs($other_time->breakdownTime); }

			$data3['tableName'] = 'machine_idle_logs';
			$data3['select'] = "machine_idle_logs.*,rejection_comment.code, rejection_comment.remark";
			$data3['join']['rejection_comment'] = 'rejection_comment.id = machine_idle_logs.idle_reason';
			$data3['where']['process_id'] = $row->process_id;
			$data3['where']['machine_id'] = $row->machine_id;
			$data3['where']['job_card_id'] = $row->job_card_id;
			$machineBreak = $this->rows($data3);

			$breakReson=""; $otherReson=""; $x=1;
			foreach($machineBreak as $break):
				if($x=1):
					if(empty($break->breck_type)){ $breakReson.= $break->remark; } 
					else { $otherReson.=$break->remark; }
				else:
					if(empty($break->breck_type)){ $breakReson.= ', '.$break->remark; } 
					else { $otherReson.= ', '.$break->remark; }
				endif;
			endforeach;

			$rjqty = 0;$rwqty = 0;$rjRatio=0;
			$productData = $this->item->getItem($row->product_id);
			$productCode = (!empty($productData))?$productData->item_code:"";
			$cycleTime = (!empty($row->cycle_time))?round(minutes($row->cycle_time),2):0;
			$runTime = $plannedProductionTime - $breakdownTime;

			if(!empty($cycleTime)):
				$planQty = $plannedProductionTime/$cycleTime;	
				$performance = round(((($cycleTime*$row->in_qty)/$runTime)*100),2);
				$overall = round(((($cycleTime*$row->in_qty)/$plannedProductionTime)*100),2);			
				$oee = round((($runTime/$plannedProductionTime) * (($cycleTime*$row->in_qty)/$runTime) * ($row->out_qty/$row->in_qty)) * 100,2);
			else:
				$planQty = 0;					
				$performance = 0;
				$overall = 0;			
				$oee = 0;
			endif;

			$availability = round((($runTime/$plannedProductionTime)*100),2);
			$qualityRate = round((($row->out_qty/$row->in_qty)*100),2);

			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.formatDate($row->entry_date).'</td>
						<td>'.$row->shift_name.'</td>
						<td>['.$row->item_code.'] '.$row->item_name.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$productCode.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.round($cycleTime,2).'</td>
						<td>'.$row->in_qty.'</td>
						<td>'.$row->rework_qty.'</td>
						<td>'.$row->rejection_qty.'</td>
						<td>'.$breakdownTime.'</td>
						<td>'.$breakReson.'</td>
						<td>'.$otherdownTime.'</td>
						<td>'.$otherReson.'</td>
						<td>'.$plannedProductionTime.'</td>
						<td>'.(int)$planQty.'</td>
						<td>'.$runTime.'</td>
						<td>'.$row->out_qty.'</td>
						<td>'.$availability.'%</td>
						<td>'.$performance.'%</td>
						<td>'.$overall.'%</td>
						<td>'.$qualityRate.'%</td>
						<td>'.$oee.'%</td>
					</tr>';
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
	}

	/* Operator Monitoring */
	public function getOperatorList(){
		$data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name";
        $data['where']['employee_master.emp_role !='] = "-1";
        $data['where']['employee_master.emp_designation'] = 11;
		return $this->rows($data);
	}

	public function getOperatorMonitoring($data){
		$queryData = array();
		$queryData['tableName'] = $this->jobTrans;
		$queryData['select'] = 'job_transaction.*,process_master.process_name,item_master.item_name,item_master.item_code,employee_master.emp_name,shift_master.shift_name,job_card.id as job_id';
		$queryData['join']['process_master'] = 'process_master.id = job_transaction.process_id';
		$queryData['join']['job_card'] = 'job_card.id = job_transaction.job_card_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_card.product_id';
		$queryData['leftJoin']['employee_master'] = 'employee_master.id = job_transaction.operator_id';
		$queryData['leftJoin']['shift_master'] = 'shift_master.id = job_transaction.shift_id';
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['job_transaction.out_qty != '] = 0;
		$queryData['where']['employee_master.id'] = $data['emp_id'];
		$queryData['order_by']['job_transaction.entry_date'] = 'ASC';
		$result = $this->rows($queryData);

		$i=1; $prev_date=""; $pid=0; $tbody=""; $okqty=0; $runtime=""; $qualityRate=0;
		$availability=0; $performance=0; $overall=0; $oee=0; $cs=0; $cm=0; $tc=0; $ps=0; $pm=0; $tp=0;
		foreach($result as $row):
			$rjqty = 0;$rwqty = 0;$rjRatio=0;
			$machineData = $this->machine->getMachine($row->machine_id);
			$machineNo = (!empty($machineData))?$machineData->item_code:"";
			if($prev_date != $row->entry_date or $pid != $row->process_id)
			{
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

				if(!empty($row->out_qty) AND $row->out_qty > 0):
					$rjRatio = round((($rjqty * 100) / $row->out_qty),2);		
				endif;		
			}

			$okqty = ($row->out_qty - ($rwqty - $rjqty));
			/* $qualityRate = round(($row->out_qty * 100) / $okqty, 2);

			$ct = explode(':',$row->cycle_time); 
			$cm = intVal($ct[1]);
			$cs = intVal($ct[2]);
			$tc = ($cm * 3600) + ($cs * 60);

			$pt = explode(':',$row->production_time);
			$pm = intVal($pt[0]);
			$ps = intVal($pt[1]);
			$tp = ($pm * 3600) + ($ps * 60);

			$availability = round(($tc * 100) / $tp,2);
			$performance = round(($tc * $okqty) / $tp,2);
			$overall = round(($tc * $okqty) / $tp,2); 
			$oee =  round((($availability * ($performance * $qualityRate)) / 100), 2); */

			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.formatDate($row->entry_date).'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$machineNo.'</td>
						<td>'.$row->item_code.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->cycle_time.'</td>
						<td>'.$row->production_time.'</td>
						<td>'.$okqty.'</td>
						<td>'.$rwqty.'</td>
						<td>'.$rjqty.'</td>
						<td>'.$row->remark.'</td>
					</tr>';
			$prev_date = $row->entry_date; $pid = $row->process_id;
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
	}
	
    /* avruti */
    //updated at : 10-12-2021 [Nayan Chikhaliya]
	public function getRejectionReworkMonitoring($data){
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
		if($data['rtype'] == 2){
			$queryData['where']['job_transaction.rejection_qty >'] = 0;
		}
		/*$queryData['where']['job_transaction.rejection_qty >'] = 0;
		$queryData['where']['job_transaction.rework_qty >'] = 0;*/
		$queryData['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		if(!empty( $data['item_id'])){$queryData['where_in']['job_transaction.product_id'] = $data['item_id'];}
		$jobtransaction= $this->rows($queryData);
		
		$tbody = ''; $tfoot = ''; $clspan = 13;
		if(!empty($jobtransaction)):
			$i=1; $totalRejectCost = 0; 
			foreach($jobtransaction as $row):
			    if($row->rejection_qty > 0 || $row->rework_qty > 0):
    			    $machine = (!empty($row->machine_id))?$this->item->getItem($row->machine_id)->item_code : '';
                    $rejection_stage = (!empty($row->rejection_stage))?$this->process->getProcess($row->rejection_stage)->process_name :"";
                    $rejection_reason = (!empty($row->rejection_reason))?$this->comment->getComment($row->rejection_reason)->remark:"";
                    $rework_reason = (!empty($row->rework_reason))?$this->comment->getComment($row->rework_reason)->remark:"";
    				
    				$rework_stage = '';
    				$reworkStage = (!empty($row->rework_process_id))?explode(',', $row->rework_process_id):"";
    				if(!empty($reworkStage)){ $r=1;
    					foreach($reworkStage as $rework_process_id):
    						if($r == 1){
    							$rework_stage .= $this->process->getProcess($rework_process_id)->process_name;
    						}else{
    							$rework_stage .= ', '.$this->process->getProcess($rework_process_id)->process_name;
    						} $r++;
    					endforeach;
    				}
    
    				$item_price = $row->price;
    				$jobCardData = $this->jobcard->getJobCard($row->job_card_id);
    				if($jobCardData->party_id > 0):
    				    $partyData = $this->party->getParty($jobCardData->party_id);
    				    if($partyData->currency != 'INR'):         
                            $inr = $this->salesReportModel->getCurrencyConversion($partyData->currency);
                            if(!empty($inr)):$item_price=$inr[0]->inrrate*$row->price;endif;
                        else:
                            $item_price = $row->price;
                        endif;
    				endif;
    
    				$rejectCost = $row->rejection_qty * $item_price;
    				
    				$tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->entry_date).'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->process_name.'</td>
                        <td>'.$row->shift_name.'</td>
                        <td>'.$machine.'</td>
                        <td>'.$row->emp_name.'</td>
                        <td>'.$row->issue_batch_no.'</td>';
						if($data['rtype'] == 1):
							$clspan = 18;
					$tbody .= ' <td>'.$row->rework_qty.'</td>
                        <td>'.$rework_reason.'</td>
                        <td>'.$row->rework_remark.'</td>
    					<td>'.$rework_stage.'</td>
    					<td>'.(!empty($row->party_name)?$row->party_name:'IN HOUSE').'</td>';
						endif;
                    $tbody .= '<td>'.$row->rejection_qty.'</td>
                        <td>'.$rejection_reason.'</td>
                        <td>'.$row->rejection_remark.'</td>
                        <td>'.$rejection_stage.'</td>
						<td>'.(!empty($row->vendor_name)?$row->vendor_name:'IN HOUSE').'</td>
                        <td>'.$rejectCost.'</td>
    				</tr>';
    			    $totalRejectCost += $rejectCost;
    			endif;
			endforeach;
			
			$tfoot .= '<tr class="thead-info">
				<th colspan="'.$clspan.'" class="text-right">Total Reject Cost</th>
				<th>'.$totalRejectCost.'</th>
			</tr>';
		endif;
		return ['status'=>1,'tbody'=>$tbody,'tfoot'=>$tfoot]; 
	}
	
	public function getOperatorPerformance($data){
		$data['tableName'] = $this->jobTrans;
		$data['select'] = 'job_transaction.*, item_master.item_code, item_master.item_name';
		$data['join']['item_master'] = "item_master.id = job_transaction.product_id";
		$data['where']['job_transaction.operator_id'] = $data['emp_id'];
		$data['customWhere'][] = "job_transaction.entry_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$performance = $this->rows($data);

		$tbody = ''; $i=1; 
		if(!empty($performance)):
			foreach($performance as $row):

				$cdata['tableName'] = 'product_process';
				$cdata['where']['product_process.item_id'] = $row->product_id;
				$cdata['where']['product_process.process_id'] = $row->process_id;
				$cTime = $this->row($cdata);

				$prodTime = (!empty($row->production_time))?round(minutes($row->production_time),2):0;
				$cycleTime = (!empty($cTime->cycle_time))?round(minutes($cTime->cycle_time),2):0;
				if(!empty($prodTime) AND !empty($cycleTime)){
					$planQty = round($prodTime/$cycleTime,0);
					$productivity =  round((($row->out_qty * 100)/$prodTime),2);
				}else{
					$planQty = 0;
					$productivity = 0;
				}
				
				$cycle_time = (!empty($cTime->cycle_time))?$cTime->cycle_time:0;
				$tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->entry_date).'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$prodTime.'</td>
                    <td>'.$cycle_time.'</td>
                    <td>'.$planQty.'</td>
                    <td>'.floatval($row->out_qty).'</td>
                    <td>'.$productivity.' %</td>
				</tr>';
			endforeach;
		endif;
		return ['status'=>1, 'tbody'=>$tbody];
	}

	/*  Create By : Avruti @15-12-2021 5:00 PM
		update by : 
		note : 
	*/
	public function getJobChallan($id=''){
        $data['tableName'] = $this->JobWorkChallan;
        $data['customWhere'][] = "FIND_IN_SET('".$id."', job_inward_id)";
        return $this->rows($data);
    }
    
    /*  Create By : Meghavii @1-1-2022 12:00 PM*/
	public function getItemWiseBom($data){
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name';
		$queryData['join']['item_master'] = "item_kit.ref_item_id = item_master.id";
		if(!empty($data['item_id'])){$queryData['where']['item_kit.item_id'] = $data['item_id'];}
		$result = $this->rows($queryData);
		$i=1;  $tbody="";
		foreach($result as $row):
			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.$row->item_name.'</td>
						<td>'.$row->qty.'</td>
					</tr>';
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
    }

	public function getProductionBomData($data){
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_name';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		if(!empty($data['ref_item_id'])){$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];}
		$result = $this->rows($queryData);
		$i=1;  $tbody="";
		foreach($result as $row):
			$tbody .= '<tr class="text-center">
						<td>'.$i++.'</td>
						<td>'.$row->item_name.'</td>
						<td>'.$row->qty.'</td>
					</tr>';
		endforeach;
		return ['status'=>1, 'tbody'=>$tbody];
    }
    
    // Avruti @3-2-2022
	public function getRmPlaning($data)
	{
		$queryData = array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['select'] = 'item_kit.*,item_master.item_code,item_master.item_name,unit_master.unit_name,um.unit_name as uname,itm.qty as ref_qty';
		$queryData['join']['item_master'] = "item_kit.item_id = item_master.id";
		$queryData['join']['item_master itm'] = "item_kit.ref_item_id = itm.id";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['leftJoin']['unit_master um'] = "um.id = itm.unit_id";
		if(!empty($data['ref_item_id'])){$queryData['where']['item_kit.ref_item_id'] = $data['ref_item_id'];}
		$result = $this->rows($queryData);
		
		return $result;
    }

	// Created By Mansee @ 08-02-2022
	public function getStockTrans($item_id,$location_id)
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
		$result= $this->row($queryData);
		return $result;
	}
}
?>