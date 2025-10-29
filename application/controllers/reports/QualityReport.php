<?php
class QualityReport extends MY_Controller
{
    private $qc_report_page = "report/qc_report/index";
    private $batch_tracability = "report/qc_report/batch_tracability";
    private $batch_history = "report/qc_report/batch_history";
	private $supplier_rating = "report/qc_report/supplier_rating";
	private $vendor_rating = "report/qc_report/vendor_rating";
	private $measuring_thread = "report/qc_report/measuring_thread";
	private $measuring_instrument = "report/qc_report/measuring_instrument";
    private $rejection_rework_monitoring = "report/production/rejection_rework_monitoring";
    private $rejection_monitoring = "report/qc_report/rejection_monitoring";
    private $nc_report = "report/qc_report/nc_report";
    private $vendor_gauge = "report/qc_report/vendor_gauge";
	private $rm_testing_register = "report/qc_report/rm_testing_register";
	private $inprocess_inspection = "report/qc_report/inprocess_inspection";
	private $calibration_status = "report/qc_report/calibration_status";
	private $rework_monitoring = "report/qc_report/rework_monitoring";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Quality Report";
		$this->data['headData']->controller = "reports/qualityReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/qc_report/floating_menu',[],true);
	    $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product');
	}
	
	public function index(){
		$this->data['pageHeader'] = 'QUALITY REPORT';
        $this->load->view($this->qc_report_page,$this->data);
    }

	/* Batch History */
	public function batchHistory(){
        $this->data['pageHeader'] = 'BATCH WISE HISTORY REPORT';
		$this->data['batchData'] = $this->qualityReports->getBatchNoListForHistory();
        $this->load->view($this->batch_history,$this->data);
    }

	public function getBatchHistory(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchHistory($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['stockTypes'][$row->ref_type] : "General Issue";
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$row->item_name.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Batch Tracability Report */
	public function batchTracability(){
        $this->data['pageHeader'] = 'Batch Tracability Report';
		$this->data['itemData'] = $this->item->getItemList(3);
        $this->load->view($this->batch_tracability,$this->data);
    }

	public function getBatchList(){
		$data = $this->input->post();
		$batchData = $this->qualityReports->getBatchListByItem($data['item_id']);
		$itemList="<option value=''>Select Batch</option>";
		foreach($batchData as $row):
			$itemList.='<option value="'.$row->batch_no.'">'.$row->batch_no.'</option>';
		endforeach;
		$this->printJson(['status'=>1,"itemList"=>$itemList]);
	}

	public function getBatchTracability(){
        $data = $this->input->post();
        $batchTracData = $this->qualityReports->getBatchTracability($data);
		$tbodyData=""; $tfootData="";$i=1;$stockQty=0;
		foreach($batchTracData as $row):
			$refType = ($row->ref_type > 0)?$this->data['stockTypes'][$row->ref_type] : "General Issue";
			$reference="Purchase Material Arrived";
			if($row->ref_type==3)
			{
				$refData = $this->qualityReports->getMIfgName($row->ref_id);
				if(!empty($refData)){ $reference = $refData->item_name.' <a href="'.base_url('jobcard').'/printDetailedRouteCard/'.$refData->job_id.'" target="_blank">('.$refData->job_prefix.$refData->job_no.')</a>'; } 
				else { $reference = "General Issue"; }
			}
			if($row->ref_type==10)
			{
				$returnData = $this->qualityReports->getReturnfgName($row->ref_id);
				$reference = $returnData->item_name.' ('.$returnData->job_prefix.$returnData->job_no.')';
			}
			$tbodyData .= '<tr>
				<td class="text-center">'.$i++.'</td>
				<td>'.formatDate($row->ref_date).'</td>
                <td>'.$row->ref_no.'</td>
				<td>'.$refType.'</td>
				<td>'.$reference.'</td>
				<td>'.(($row->trans_type == 1)?floatVal($row->qty):"").'</td>
				<td>'.(($row->trans_type == 2)?abs(floatVal($row->qty)):"").'</td>';
			$tbodyData .='</tr>';
			$stockQty += floatVal($row->qty);
		endforeach;
		$tfootData .= '<tr class="thead-info">
					<th colspan="5" style="text-align:right !important;">Current Stock</th>
					<th colspan="2">'.round($stockQty,2).'</th>
					</tr>';
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
	}

	/* Supplier Rating Report */
	public function supplierRating(){
        $this->data['pageHeader'] = 'SUPPLIER RATING REPORT';
		$this->data['supplierData'] = $this->party->getSupplierList();
        $this->load->view($this->supplier_rating,$this->data);
    }

	public function getSupplierRating(){
		$data = $this->input->post();
		$errorMessage = array();

		if($data['to_date'] < $data['from_date'])
			$errorMessage['to_date'] = "Invalid date.";

		$supplierItems = $this->qualityReports->getSupplierRatingItems($data);
		$supplierDetails = $this->party->getParty($data['party_id']);

		$tbodyData=""; $tfootData="";$i=1; 
		$tsq=0;$tn=0;$tq1=0;$tq2=0;$tq3=0;$tt1=0;$tt2=0;$tt3=0;

		foreach($supplierItems as $items):
			$data['item_id']=$items->id;
			$qtyData = $this->qualityReports->getInspectedMaterialGBJ($data);
				
			$supplierData = $this->qualityReports->getSupplierRating($data);
			$qty=0; $t1=0; $t2=0; $t3=0; $remark="";$wdate ="";

			foreach($supplierData as $row):
				$qty+= $row->qty;
				$wdate = date('Y-m-d',strtotime("+7 day", strtotime($row->delivery_date)));
				
				if($row->grn_date <= $row->delivery_date){$t1 += $row->qty;}
				elseif($row->grn_date <= $wdate){$t2 += $row->qty;}
				else{$t3 += $row->qty;}
				
				$remark=$row->remark;
			endforeach;

				$tbodyData .= '<tr>
            					<td class="text-center">'.$i++.'</td>
            					<td>'.$items->item_name.'</td>
            					<td>'.$qty.'</td>
            					<td>'.$qtyData->insQty.'</td>
            					<td>'.$qtyData->aQty.'</td>
            					<td>'.$qtyData->udQty.'</td>
            					<td>'.$qtyData->rQty.'</td>
            					<td>'.$t1.'</td>
            					<td>'.$t2.'</td>
            					<td>'.$t3.'</td>
            					<td></td>
            					<td>'.$remark.'</td>
        					</tr>';
				$tsq+=$qty;$tn+=$qtyData->insQty;
				$tq1+=$qtyData->aQty;$tq2+=$qtyData->udQty;$tq3+=$qtyData->rQty;
				$tt1+=$t1;$tt2+=$t2;$tt3+=$t3;
		endforeach;
		
		// RT = [{T1+(0.75*T2)+(0*T3)}/N]*100
		$deliveryRate = 0;
		if(!empty($tt1) && !empty($tt2) && !empty($tt3) && !empty($tsq)){
		    $deliveryRate = round(((($tt1 + (0.75 * $tt2) + (0 * $tt3) ) / $tsq ) * 100),2);
		}
		$theadData = '<tr class="text-center">
							<th colspan="10">SUPPLIER RATING REPORT</th>
							<th colspan="2">F PU 06 (00/01.06.20)</th>
						</tr>
						<tr>
							<th colspan="4">Supplier\'s Name : '.$supplierDetails->party_name.'</th>
							<th colspan="4">Period : '.date('d-m-Y',strtotime($data['from_date'])).' TO '.date('d-m-Y',strtotime($data['to_date'])).'</th>
							<th colspan="4">Date : '.date('d-m-Y').'</th>
						</tr>
						<tr class="text-center">
							<th rowspan="3" style="min-width:50px;">Sr No.</th>
							<th rowspan="3" style="min-width:100px;">Item Description</th>
							<th rowspan="3" style="min-width:50px;">Quantity Supplied</th>
							<th rowspan="3" style="min-width:50px;">Inspected Qty.<br />(N)</th>
							<th colspan="3">Quality Rating</th>
							<th colspan="3">Delivery Rating : '.$deliveryRate.'%</th>
							<th rowspan="3" style="min-width:50px;">Premium Freight</th>
							<th rowspan="3" style="min-width:100px;">Remark</th>
						</tr>
						<tr class="text-center">
							<th colspan="3">Quantity</th>
							<th colspan="3">Quantity Received</th>
						</tr>
						<tr class="text-center">
							<th>Accepted<br>(Q1)</th>
							<th>Accept.U/D<br>(Q2)</th>
							<th>Rejected<br>(Q3)</th>
							<th>Intime<br>(T1)</th>
							<th>Late upto 1 week<br>(T2)</th>
							<th>Late beyond week<br>(T3)</th>
						</tr>';
		
		$tfootData = '<tr>
    					<th colspan="2" class="text-right">Total</th>
    					<th>'.$tsq.'</th>
    					<th>'.$tn.'</th>
    					<th>'.$tq1.'</th>
    					<th>'.$tq2.'</th>
    					<th>'.$tq3.'</th>
    					<th>'.$tt1.'</th>
    					<th>'.$tt2.'</th>
    					<th>'.$tt3.'</th>
    					<th></th>
    					<th></th>
					</tr>';

		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$this->printJson(['status'=>1,"theadData"=>$theadData,"tbodyData"=>$tbodyData,"tfootData"=>$tfootData]);
		endif;
	}

	/* Vendor Rating report  */
	public function vendorRating(){
		$this->data['pageHeader'] = 'VENDOR RATING REPORT';
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->vendor_rating,$this->data);
	}

	/* Measuring Thread Data */
	public function measuringThread(){
		$this->data['pageHeader'] = ' MEASURING THREAD RING GAUGES REPORT';
		$this->data['threadData'] = $this->qualityReports->getMeasuringDevice(7);
        $this->load->view($this->measuring_thread,$this->data);
	}

	/* Measuring Instrument Data */
	public function measuringInstrument(){
		$this->data['pageHeader'] = 'LIST OF MEASURING DEVICE & TEST EQUIPMENT';
		$this->data['instrumentsData'] = $this->qualityReports->getGaugeInstrumentData();
        $this->load->view($this->measuring_instrument,$this->data);
	}
	
    /* Rejection Rework Monitoring  avruti*/
    public function rejectionReworkMonitoring(){
        $this->data['pageHeader'] = 'REJECTION & REWORK MONITORING REPORT';
        $this->data['itemDataList'] = $this->item->getItemList(1);
        $this->load->view($this->rejection_rework_monitoring,$this->data);
    }

    public function getRejectionReworkMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['rtype'] = 1;
            $rejectionData = $this->productionReports->getRejectionReworkMonitoring($data);
            $this->printJson($rejectionData);
        endif;
    }

	/* Rejection Monitoring  avruti*/
	public function rejectionMonitoring(){
		$this->data['pageHeader'] = 'REJECTION MONITORING REPORT';
		$this->data['itemDataList'] = $this->item->getItemList(1);
		$this->load->view($this->rejection_monitoring,$this->data);
	}

    public function getRejectionMonitoring(){
		$data = $this->input->post(); 
		$errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['rtype'] = 2;
 			$rejectionData = $this->qualityReports->getRejectionMonitoring($data);
			 $tbody = ''; $tfoot = '';
			 if (!empty($rejectionData)) :
			 	$i = 1;
			 	$totalRejectCost = 0;$totalRejectQty = 0;
			 	foreach ($rejectionData as $row) :
						$totalRejectQty +=$row->qty;
			 			$tbody .= '<tr>
			                 <td>' . $i++ . '</td>
			                 <td>' . formatDate($row->entry_date) . '</td>
			                 <td>' . $row->item_code . '</td>
			                 <td>' . $row->process_name . '</td>
			                 <td>' . $row->shift_name . '</td>
			                 <td>['.$row->machine_code.'] ' . $row->machine_name . '</td>
			                 <td>' . $row->emp_name . '</td>';
			 			
			 			$tbody .= '<td>' . $row->qty . '</td>
			                 <td>' . $row->rejection_reason . '</td>
			                 <td>' . $row->rej_remark . '</td>
			                 <td>' . $row->rejection_stage . '</td>
			 				 <td>' . (!empty($row->vendor_name) ? $row->vendor_name : 'IN HOUSE') . '</td>
			 			</tr>';
			 		
			 	endforeach;
	 
			 	$tfoot .= '<tr class="thead-info">
				 <th colspan="7" class="text-right">Total Reject Qty.</th>
				 <th>' . $totalRejectQty . '</th>
			 	<th colspan="4" class="text-right"></th>
			 	</tr>';
			endif;
			 
			$this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
		endif;
	}
	
	public function ncReport(){
		$this->data['pageHeader'] = ' Final Inspection NC Report';
        $this->load->view($this->nc_report,$this->data);
	}

	public function getNCReportData(){
		$data=$this->input->post();
		$reportdata = $this->qualityReports->getNCReportData($data);
		$tbodyData="";
		foreach($reportdata as $row):
			$rejection_reason="";
			$rejection_from="";
			$rej_reason=(!empty($row->rej_reason))?json_decode($row->rej_reason):'';
			if(!empty($rej_reason)){
				$i=0;
				foreach($rej_reason as $rej){
					if($i>0){
						$rejection_reason.=',';
						$rejection_from.=',';
					}
					
					$rejection_reason.=$rej->rejection_reason;
					$rejection_from.=$rej->rej_stage_name;
					$i++;
				}
			}
			
			$rework_reason="";
			$rework_from="";
			$rw_reason=(!empty($row->rw_reason))?json_decode($row->rw_reason):'';
			if(!empty($rw_reason)){
				$i=0;
				foreach($rw_reason as $rw){
					if($i>0){
						$rework_reason.=',';
						$rework_from.=',';
					}
					$rework_reason.=$rw->rework_reason;
					$rework_from.=$rw->rw_stage_name;
					$i++;
				}
			}
			$tbodyData .= '<tr>
								<td>'.formatDate($row->log_date).'</td>
								<td>'.$row->item_name.'</td>
								<td>'.$row->inspection_type_name.'</td>
								<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
								<td>'.$row->production_qty.'</td>
								<td>'.$row->ok_qty.'</td>
								<td>'.$row->rw_qty.'</td>
								<td>'.$rework_reason.'</td>
								<td>'.$rework_from.'</td>
								<td>'.$row->rej_qty.'</td>
								<td>'.$rejection_reason.'</td>
								<td>'.$rejection_from.'</td>
								<td>'.$row->emp_name.'</td>
								<td>'.$row->supervisor.'</td>
								<td>'.number_format($row->rej_qty*$row->price,2).'</td>
								<td>'.number_format(($row->production_time/60),2).'</td>';
			$tbodyData .='</tr>';
		endforeach;
		
		$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
	}

    /* Vendor Gauge In Out Challan Report Data * Created By Meghavi @10/09/2022*/
	public function vendorGaugeInOut(){
		$this->data['pageHeader'] = 'VENDOR GAUGE IN OUT CHALLAN';
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->vendor_gauge,$this->data);
	}

	public function getVendorGaugeData(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $vendorGaugeData = $this->qualityReports->getVendorGaugeData($data);
            $tbody=''; $i=1; $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            $partyName = (!empty($row->party_name)) ? $row->party_name : 'In House';
            if(!empty($vendorGaugeData)):
                foreach($vendorGaugeData as $row): 

					$return['item_id'] = $row->item_id; $return['ref_id'] = $row->id; $return['trans_type'] = 1;
                    $returnData = $this->outChallan->getReceiveItemTrans($return)['result'];
                    $returnCount = count($returnData);

                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.getPrefixNumber($row->challan_prefix, $row->challan_no).'</td>
                        <td>'.formatDate($row->challan_date).'</td>
                        <td>'.$partyName.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.floatVal($row->qty).'</td>';

						if($returnCount > 0):
                            $j=1; $totalPend = 0;
                            foreach($returnData as $recRow):
								$pending_qty = abs($row->qty) - (abs($recRow->qty) + $totalPend);
                                $tbody.='<td>'.formatDate($recRow->ref_date).'</td>
                                            <td>'.abs($recRow->qty).'</td>
                                            <td>'.floatval($pending_qty).'</td>';
                                if($j != $returnCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++; $totalPend += $recRow->qty;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>';
                        endif;
                    $tbody.='</tr>';
                endforeach;
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Vendor Gauge In Out Challan Report Data *
	 Created By Meghavi @10/09/2022*/
	public function rmTestingRegister(){
		$this->data['pageHeader'] = 'RAW MATERIAL TESTING REGISTER';
        $this->load->view($this->rm_testing_register,$this->data);
	}

	public function getRmTestingRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $rmTestingRegisterData = $this->qualityReports->getRmTestingRegister($data);
            $tbody=''; $i=1;
			
            if(!empty($rmTestingRegisterData)): 
                foreach($rmTestingRegisterData as $row):  
					$row->product_code = ''; $c=0;
					if(!empty($row->fgitem_id)):
						$la = explode(",",$row->fgitem_id);
						if(!empty($la)){
							foreach($la as $fgid){
								$fg = $this->grnModel->getFinishGoods($fgid);
								if(!empty($fg)):
									if($c==0){
										$row->product_code .= $fg->item_code;
									}else{
										$row->product_code .= '<br>'.$fg->item_code;
									}$c++;
								else:
									$row->product_code = "";
								endif;
							}
						}
					endif; 
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.formatDate($row->grn_date).'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->material_grade.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->batch_no.'</td>
                        <td>'.$row->qty.'</td>
                        <td>'.$row->unit_name.'</td>
                        <td>'.$row->product_code.'</td>
                        <td>'.$row->name_of_agency.'</td>
                        <td>'.$row->test_description.'</td>
                        <td>'.$row->sample_qty.'</td>
                        <td>'.$row->test_report_no.'</td>
                        <td>'.$row->test_remark.'</td>
                        <td>'.$row->test_result.'</td>
                        <td>'.$row->inspector_name.'</td>
                        <td>'.$row->mill_tc.'</td>
                    </tr>';
                endforeach;
            endif;
			
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    public function inprocessInspection(){
        $this->data['pageHeader'] = 'IN PROCESS INSPECTION REPORT ';
		$this->data['jobData'] = $this->qualityReports->getJobcardListFromInspection();
        $this->load->view($this->inprocess_inspection,$this->data);
    }
    
	public function getProcessList(){ 
        $job_card_id = $this->input->post('job_card_id');  
		$processData = $this->qualityReports->getProcessFromInspection($job_card_id); 
		$options = '<option value="">Select Process</option>';
		foreach($processData as $row):
			$options .= '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
		endforeach;
        $this->printJson(['status'=>1, 'options'=>$options]);
	}

	public function getInProcessInspectionData(){
        $data = $this->input->post();
		$paramData = $this->controlPlan->getLineInspectionForPrint('',$data['job_card_id'],$data['process_id']); //print_r($paramData);exit;
        $tbodyDataA="";
		if(!empty($paramData)): 
			$tbodyDataA.= '<tr>
						<td>'.$paramData->item_code.'</td>
						<td>'.$paramData->process_name.'</td>
						<td>'.$paramData->emp_name.'</td>
						<td>'.$paramData->shift_name.'</td>
						<td>' . (!empty($paramData->itm_cd) ? '[' . $paramData->itm_cd . '] ' . $paramData->itm_nm : $paramData->itm_nm) . '</td>
						<td>'.getPrefixNumber($paramData->job_prefix,$paramData->job_no).'</td>
						<td>'.$paramData->insp_date.'</td>';
			$tbodyDataA.='</tr>';
		else:
			$tbodyDataA.= '<tr><td colspan="8" style="text-align:center;">No Data Found</td></tr>';
		endif;
			
		
		$pData = $this->controlPlan->getLineInspectionData($data['item_id'],$data['process_id']);
		$inspData = $this->qualityReports->getLineInspectionForReport($data);
		
		$tbodyDataB="";$i=1; $theadDataB='';$observation='';$tblsample='';

		foreach($inspData as $sample){
			$sQty = (!empty($sample->sampling_qty))?$sample->sampling_qty:2;
			$observation.='<th colspan="'.($sQty+1).'">'.$sample->created_at.'</th>';

			for($j=1;$j<=$sQty;$j++):
				$tblsample.='<th>'.$j.'</th>';
			endfor; 
			$tblsample.='<th>Result</th>';
		}

		$theadDataB.='<tr style="text-align:center;">
			 <th rowspan="2" style="width:5%;">#</th>
			 <th rowspan="2">Parameter</th>
			 <th rowspan="2">Specification</th>
			 <th rowspan="2">Lower Limit</th>
			 <th rowspan="2">Uper Limit</th>
			 <th rowspan="2">Instrument Used</th>
			'.$observation.'
		</tr>
		<tr style="text-align:center;">
		 	'.$tblsample.' 
		</tr>';

		if(!empty($pData)): 
			foreach($pData as $row): 
				
				$paramItems = '';$flag=false;
					$tbodyDataB.= '<tr>
								<td style="text-align:center;" height="30">'.$i.'</td>
								<td style="text-align:center;">'.$row->parameter.'</td>
								<td style="text-align:center;">'.$row->specification.'</td>
								<td style="text-align:center;">'.$row->lower_limit.'</td>
								<td style="text-align:center;">'.$row->upper_limit.'</td>
								<td style="text-align:center;">'.$row->measure_tech.'</td>';

					foreach($inspData as $paramData):
						$obj = New StdClass;
						if(!empty($paramData)):
							$obj = json_decode($paramData->observe_samples);
						endif;
						for($c=1;$c<=$paramData->sampling_qty;$c++):
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c-1].'</td>';
							endif;
							if(!empty($obj->{$row->id}[$c-1])){$flag=true;}
						endfor;
						if(!empty($obj->{$row->id})):
							$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[floatval($paramData->sampling_qty)].'</td>';
						endif;
					endforeach;
					if($flag):
						$tbodyDataB .= $paramItems;$i++;
					endif;
					$tbodyDataB.= '</tr>';
			endforeach;
		else:
			$tbodyDataB.= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
		endif;
		$this->printJson(['status'=>1,"tbodyDataA"=>$tbodyDataA,"tbodyDataB"=>$tbodyDataB,"theadDataB"=>$theadDataB]);
    }

    /* Created At : 03-12-2022 [Milan Chauhan] */
	public function monthlyRejectionRegister(){
		$this->data['pageHeader'] = 'MONTHLY REJECTION REGISTER';
        $this->load->view("report/qc_report/monthly_rejection_register",$this->data);
	}
	
	/* Created At : 03-12-2022 [Milan Chauhan] */
	public function getMonthlyRejectionRegister($fromDate = "",$toDate = ""){
		if(!empty($fromDate) && !empty($toDate)):
			$data['is_pdf'] = 1;
			$data['from_date'] = $fromDate;
			$data['to_date'] = $toDate;
		else:
			$data = $this->input->post();
			$data['is_pdf'] = 0;
		endif;
		$rejectionData = $this->qualityReports->getMonthlyRejectionRegister($data);
		// print_r($rejectionData);exit;
		$tbody = '';$i=1;
		$totalQty = 0; $totalOkQty = 0; $totalRejQty = 0; $totalTurningQty = 0; $totalMillingQty = 0; 
		$totalOtherQty = 0; $totalCmoQty = 0;
		foreach($rejectionData as $row):
			if($row->total_rej_qty > 0){
				$jobCardData = array();
				$jobCardData['id'] = $row->job_card_id;
				$jobCardData['product_id'] = $row->product_id;
				$jobCardData = (object)$jobCardData;
				//print_r($jobCardData);exit;
				$materialData = $this->jobcard->getMaterialIssueData($jobCardData);
				$metal = (!empty($materialData['resultData']))?$materialData['resultData']['material_grade']:'';
				$row->total_qty = $row->ok_qty+$row->total_rej_qty;
				$tbody .= '<tr class="text-center">
					<td>'.$i++.'</td>
					<td>'.formatDate($row->entry_date).'</td>
					<td>'.$row->item_name.'</td>
					<td>'.$metal.'</td>
					<td>'.((!empty($row->sales_order_id))?getPrefixNumber($row->trans_prefix,$row->trans_no):"-").'</td>
					<td>'.floatVal($row->total_qty).'</td>
					<td>'.floatVal($row->ok_qty).'</td>
					<td>'.floatVal($row->total_rej_qty).'</td>
					<td>'.floatVal($row->turning_rej_qty).'</td>
					<td>'.floatVal($row->milling_rej_qty).'</td>
					<td>'.floatVal($row->other_rej_qty).'</td>
					<td>'.floatVal($row->cmo_rej_qty).'</td>
					<td></td>
				</tr>';
	
				$totalQty += floatVal($row->total_qty); 
				$totalOkQty += floatVal($row->ok_qty); 
				$totalRejQty += floatVal($row->total_rej_qty); 
				$totalTurningQty += floatVal($row->turning_rej_qty); 
				$totalMillingQty += floatVal($row->milling_rej_qty); 
				$totalOtherQty += floatVal($row->other_rej_qty); 
				$totalCmoQty += floatVal($row->cmo_rej_qty);
			}
			
		endforeach;

		$tfooter = '<tr>
			<th class="text-right" colspan="5">Total</th>
			<th class="text-center">'.$totalQty.'</th>
			<th class="text-center">'.$totalOkQty.'</th>
			<th class="text-center">'.$totalRejQty.'</th>
			<th class="text-center">'.$totalTurningQty.'</th>
			<th class="text-center">'.$totalMillingQty.'</th>
			<th class="text-center">'.$totalOtherQty.'</th>
			<th class="text-center">'.$totalCmoQty.'</th>
			<th></th>
		</tr>      
		<tr>
			<th class="text-right" colspan="5">Net Total</th>
			<th class="text-center">'.$totalQty.'</th>
			<th class="text-center">'.$totalOkQty.'</th>
			<th class="text-center">'.$totalRejQty.'</th>
			<th class="text-center" colspan="3">'.($totalTurningQty + $totalMillingQty + $totalOtherQty).'</th>
			<th class="text-center">'.$totalCmoQty.'</th>
			<th></th>
		</tr> 
		<tr>
			<th class="text-right" colspan="5">REJECTION IN PERCENTAGE</th>
			<th class="text-center"></th>
			<th class="text-center"></th>
			<th class="text-center">'.((!empty($totalQty) && !empty($totalRejQty))?round(($totalRejQty * 100) / $totalQty,2):0).'%</th>
			<th class="text-center" colspan="3">'.((!empty($totalRejQty) && ($totalTurningQty + $totalMillingQty + $totalOtherQty) > 0)?round((($totalTurningQty + $totalMillingQty + $totalOtherQty) * 100) / $totalRejQty,2):0).'%</th>
			<th class="text-center">'.((!empty($totalRejQty) && !empty($totalCmoQty))?round(($totalCmoQty * 100) / $totalRejQty,2):0).'%</th>
			<th></th>
		</tr>';

		if($data['is_pdf'] == 0):
			$this->printJson(['status'=>1,'tbody'=>$tbody,'tfooter'=>$tfooter]);
		else:
			$pdfData = '';
			$pdfData .= '<html>
				<head>
					<title>
						MONTHLY REJECTION REGISTER
					</title>
				</head>
				<body style="padding:10px;">
					<table class="table table-bordered itemList">
						<tr>
							<th style="width:70%;">AKSHAR ENGINEERS</th>
							<th style="width:30%;">
								<img src="'.base_url("assets/images/logo_text.png").'" alt="logo" style="width:20%;">
							</th>
						</tr>
						<tr>
							<th>MONTHLY REJECTION REGISTER</th>
							<th>F/QC/07 (01/01.07.22)</th>
						</tr>
						<tr>
							<th class="text-right" colspan="2">Date : '.formatDate($data['from_date']).' to '.formatDate($data['to_date']).'</th>
						</tr>
					</table>
					<table class="table table-bordered itemList">
						<thead class="thead-info" id="theadData">
							<tr>
								<th rowspan="2" style="min-width:50px;">#</th>
								<th rowspan="2" style="min-width:100px;">Date</th>
								<th rowspan="2" style="min-width:80px;">Part Name</th>
								<th rowspan="2" style="min-width:80px;">Metal</th>
								<th rowspan="2" style="min-width:80px;">OA. No.</th>
								<th rowspan="2" style="min-width:100px;">Total Qty.</th>
								<th rowspan="2" style="min-width:150px;">OK Nos.</th>
								<th rowspan="2" style="min-width:50px;">Total Rejection</th>
								<th colspan="4" class="text-center" style="min-width:50px;">Rejection </th>
								<th rowspan="2" style="min-width:50px;">Remarks</th>
							</tr>
							<tr>
								<th>Turning</th>
								<th>Milling</th>
								<th>Other</th>
								<th>CASTING/ MATERIAL/ OUT SOURCE</th>
							</tr>
						</thead>
						<tbody>
							'.$tbody.'
						</tbody>
						<tfoot>
							'.$tfooter.'
							<tr>
								<th class="text-left" colspan="5">Prepared By. : </th>
								<th class="text-left" colspan="8">Approved By: </th>
							</tr>
						</tfoot>
					</table>
				</body>
			</html>';

			$mpdf = new \Mpdf\Mpdf();
            $pdfFileName = 'REJECTION_REGISTER_'.date("d_m_Y",strtotime($data['from_date'])).'_'.date("d_m_Y",strtotime($data['to_date'])).'.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetProtection(array('print'));

            $mpdf->AddPage('P', '', '', '', '', 5, 5, 5, 5, 5, 5, '', '', '', '', '', '', '', '', '', 'A4-P');
            $mpdf->WriteHTML($pdfData);
            $mpdf->Output($pdfFileName, 'I');
		endif;
	}

	//Created By Meghavi @22/06/2023
	public function caliOfMeasuring(){
		$this->data['pageHeader'] = 'CALIBRATION STATUS OF MEASURING DEVICE & TEST EQUIPMENT';   
		$this->data['calibrationData'] = $this->qualityReports->getCalibrationStatusForReport();
		$this->load->view($this->calibration_status, $this->data);
	}

	/*CREATED BY MEGHAVI @23/06/23*/ 
	public function printCalibrationStatus(){
		$calibrationData = $this->qualityReports->getCalibrationStatusForReport(); 
        $logo=base_url('assets/images/logo.png');		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%"> CALIBRATION STATUS OF MEASURING DEVICE & TEST EQUIPMENT</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F/QC/06 (01/01.01.16)</td>
						</tr>
					</table>';
        $itemList='<table id="reportTable" class="table table-bordered align-items-center itemList">
								<thead class="thead-info" id="theadData">
								<tr class="text-center">
									<th rowspan="2">#</th>
									<th rowspan="2">Description of Measuring Device</th>
									<th rowspan="2">Location</th>
									<th rowspan="2">Code No.</th>
									<th rowspan="2">Size</th>
									<th rowspan="2">Make</th>
									<th rowspan="2">Calibration Frequency</th>
									<th colspan="3">calibration</th>
								</tr>
								<tr class="text-center">
									<th>Date</th>
									<th>Due Date</th>
									<th>Result</th>
								</tr>
								</thead>';                                
        $itemList.='<tbody id="tbodyData">';
                        $i=1;
						foreach($calibrationData as $row)  
						{
							$size = (!empty($row->size)) ? $row->size : '' ;
							$itemList.='<tr>
											<td>'.$i++.'</td>
											<td>'.$row->item_name.'</td>
											<td>'.$row->location_name.'</td>
											<td>'.$row->mfg_sr.'</td>
											<td>'.$size.'</td>
											<td>'.$row->make_brand.'</td>
											<td>'.$row->cal_freq.'</td>
											<td>'.$row->last_cal_date.'</td>
											<td>'.$row->next_cal_date.'</td>
											<td></td>
										</tr>';
						}

        $itemList.='</tbody>
                </table>';
	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';		
		$pdfData = $originalCopy;		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='CUST-LIST-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('L','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }	
	
	public function reworkMonitoring(){
		$this->data['pageHeader'] = 'REWORK MONITORING REGISTER';   
		$this->load->view($this->rework_monitoring, $this->data);
	}
	
	public function getReworkMonitoring(){
		$data = $this->input->post();
		$errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['rtype'] = 2;
 			$reworkData = $this->qualityReports->getReworkMonitoring($data);
			 $tbody = '';
			 if (!empty($reworkData)) :
			 	$i = 1;
			 	foreach ($reworkData as $row) :
						$tbody .= '<tr>
			                 <td>' . $i++ . '</td>
			                 <td>' . formatDate($row->entry_date) . '</td>
			                 <td>' . $row->job_number . '</td>
			                 <td>' . (!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name . '</td>
			                 <td>' . $row->party_name . '</td>
			                 <td>' . $row->job_qty . '</td>
			                 <td>' . $row->rw_qty . '</td>
			                 <td></td>
							 <td></td>
							 <td></td>
							 <td></td>';
			 	endforeach;
			endif;
			$this->printJson(['status' => 1, 'tbody' => $tbody]);
		endif;
	}
}
?>