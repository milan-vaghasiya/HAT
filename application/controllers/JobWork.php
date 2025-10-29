<?php
class JobWork extends MY_Controller{
    private $indexPage = "job_work/index";
    private $returnForm = "job_work/job_work_return";
	private $challanForm = "job_work/vendor_challan";
	private $return_material = "job_work/return_material";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work";
		$this->data['headData']->controller = "jobWork";
		$this->data['headData']->pageUrl = "jobWork";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWork->getDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->pending_qty = $row->in_qty - ($row->out_qty + $row->rejection_qty + $row->rework_qty); 
            $jobPer = 0;
            if($row->in_qty>0)
            {$jobPer = round(((($row->out_qty + $row->rejection_qty + $row->rework_qty) * 100) / $row->in_qty),2);} 
            if($jobPer == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Pending - '.$jobPer.'%</span>';
			elseif($jobPer < 100):
				$row->status = '<span class="badge badge-pill badge-warning m-1">In Process - '.$jobPer.'%</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1">Completed - '.$jobPer.'%</span>';
			endif;
			
			$jobInData = $this->production->getJobInwardDataById($row->id);
			//print_r($this->db->last_query());
			$row->issue_batch_no = $jobInData->issue_batch_no;
			$row->issue_material_qty = $jobInData->issue_material_qty;
			$row->material_used_id = $jobInData->material_used_id;
			$row->minDate = (!empty($jobInData->entry_date)) ? $jobInData->entry_date : "";
			
            $row->controller = $this->data['headData']->controller;
			
            $sendData[] = getJobWorkData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function jobWorkReturn(){
		$data = $this->input->post();
		$this->data['dataRow'] = $data;
		$this->load->view($this->returnForm,$this->data);
	}

	public function jobWorkReturnSave(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['qty']))
			$errorMessage['qty'] = "Qty is required.";

		if(!empty($data['qty'])):
			if($data['qty'] > $data['pending_qty'])
				$errorMessage['qty'] = "Invalid Qty.";
		endif;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			unset($data['pending_qty']);
			$data['created_by'] = $this->loginId;
			$this->printJson($this->jobWork->jobWorkReturnSave($data));
		endif;
	}
	
	function jobworkOutChallan1($id){
		$jobData = $this->jobWork->getVendorChallan($id);
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : '.$jobData->party_name.'</b><br>
									<span style="font-size:12px;">'.$jobData->party_address.'<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">'.$jobData->gstin.'</span>
								</td>
								<td class="text-left" height="25"><b>Challan No. :</b> '.getPrefixNumber($jobData->challan_prefix,$jobData->challan_no).' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Date :</b> '.date("d-m-Y",strtotime($jobData->created_at)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>Material Description</th><th style="width:15%;">Nos.</th><th style="width:15%;">Weight</th>
					</tr>';
			$jobTrans = explode(',', $jobData->job_inward_id);
			$i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalIn=0; $totalWeight=0;
			foreach($jobTrans as $row):
				$jobTransData = $this->jobWork->getJobworkOutData($row);

				$pdays = (!empty($jobTransData->production_days)) ? "+".$jobTransData->production_days." day" : "+0 day";
				$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobTransData->created_at)));

				if($i==1){
					$itemCode .= $jobTransData->item_code;
					$jobNo .= getPrefixNumber($jobTransData->job_prefix,$jobTransData->job_no);
					$deliveryDate .= $delivery_date; $processName .= $jobTransData->process_name;
					$remark .= $jobTransData->jwoRemark;

					$inQty .= sprintf('%0.0f',$jobTransData->in_qty); 
					$weight .= sprintf('%0.3f', $jobTransData->in_total_weight);
				} else {
					$itemCode .= (!empty($jobTransData->item_code))?', '.$jobTransData->item_code:'';
					$jobNo .= (!empty($jobTransData->job_prefix) && !empty($jobTransData->job_no))?', '.getPrefixNumber($jobTransData->job_prefix,$jobTransData->job_no):'';
					$deliveryDate .= (!empty($delivery_date))?', '.$delivery_date:'';
					$remark .= (!empty($jobTransData->jwoRemark))?', '.$jobTransData->jwoRemark:'';

					
					$inQty .= (!empty($jobTransData->in_qty))?', '.sprintf('%0.0f',$jobTransData->in_qty):'';
					$weight .= (!empty($jobTransData->in_total_weight))?', '.sprintf('%0.3f', $jobTransData->in_total_weight):'';
				} $i++;
				$totalIn += sprintf('%0.0f',$jobTransData->in_qty);
				$totalWeight += $jobTransData->in_total_weight;
			endforeach;
			$itemList.='<tr>
					<td style="vertical-align:top;height:230px;padding:5px;">
						<b>Item Code : </b>'.$itemCode.'<br><br>
						<b>Job Number : </b>'.$jobNo.'<br><br>
						<b>Delivery Date : </b>'.$deliveryDate.'<br><br>
						<b>Process : </b>'.$processName.'<br><br>
						<b>Remarks : </b>'.$remark.'
					</td>
					<td class="text-center" style="vertical-align:top;padding:5px;">'.$inQty.'</td>
					<td class="text-center" style="vertical-align:top;padding:5px;">'.$weight.'</td>
				</tr>';
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', $totalIn).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.3f', $totalWeight).'</th>';
		$itemList.='</tr>';		
		$itemList.='</table>';
		
		$bottomTable='<table class="table table-bordered" style="width:100%;">';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="width:50%;border:0px;"></td>';
				$bottomTable.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
			$bottomTable.='</tr>';
			$bottomTable.='<tr><td colspan="2" height="60" style="border:0px;"></td></tr>';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
				$bottomTable.='<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
			$bottomTable.='</tr>';
		$bottomTable.='</table>';
		
		$originalCopy = '<div style="width:210mm;height:140mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">'.$topSectionV.$baseSection.$itemList.$bottomTable.'</div>';
		
		$pdfData = $originalCopy."<br>".$vendorCopy;
		
		// print_r($itemList);exit;
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	function jobworkOutChallan($id){
		$jobData = $this->jobWork->getVendorChallan($id);
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Vendor Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$baseSection = '<table class="vendor_challan_table">
							<tr style="">
								<td rowspan="2" style="width:70%;vertical-align:top;">
									<b>TO : '.$jobData->party_name.'</b><br>
									<span style="font-size:12px;">'.$jobData->party_address.'<br>
									<b>GSTIN No. :</b> <span style="letter-spacing:2px;">'.$jobData->gstin.'</span>
								</td>
								<td class="text-left" height="25"><b>Challan No. :</b> '.getPrefixNumber($jobData->challan_prefix,$jobData->challan_no).' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Date :</b> '.date("d-m-Y",strtotime($jobData->challan_date)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>#</th>
						<th>Part Code</th>
						<th>Job No.</th>
						<th>Delivery Date</th>
						<th>Process</th>
						<th>Remarks</th>
						<th style="width:15%;">Nos.</th>
						<th style="width:15%;">Weight</th>
					</tr>';
			$jobTrans = explode(',', $jobData->job_inward_id);
			$i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalIn=0; $totalWeight=0; $blnkRow=4;
			foreach($jobTrans as $row):
				$jobTransData = $this->jobWork->getJobworkOutData($row);

				$pdays = (!empty($jobTransData->production_days)) ? "+".$jobTransData->production_days." day" : "+0 day";
				$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobTransData->created_at)));


				$itemList.='<tr>
					<td style="vertical-align:top;padding:5px;">'.$i++.'</td>
					<td style="vertical-align:top;padding:5px;">'.$jobTransData->item_code.'</td>
					<td style="vertical-align:top;padding:5px;">'. getPrefixNumber($jobTransData->job_prefix,$jobTransData->job_no).' <br> <small>(Batch No: '.$jobTransData->issue_batch_no.')</small> </td>
					<td style="vertical-align:top;padding:5px;">'.$delivery_date.'</td>
					<td style="vertical-align:top;padding:5px;">'.$jobTransData->process_name.'</td>
					<td style="vertical-align:top;padding:5px;">'.$jobTransData->jwoRemark.'</td>
					<td class="text-center" style="vertical-align:top;padding:5px;">'.((!empty($jobTransData->in_qty))?sprintf('%0.0f',$jobTransData->in_qty):'').'</td>
					<td class="text-center" style="vertical-align:top;padding:5px;">'.((!empty($jobTransData->in_total_weight))?sprintf('%0.3f', $jobTransData->in_total_weight):'').'</td>
				</tr>';
				$totalIn += sprintf('%0.0f',$jobTransData->in_qty);
				$totalWeight += $jobTransData->in_total_weight;
			endforeach;
		$packingData = json_decode($jobData->material_data); $materialDetails="";
		if(!empty($packingData)): $i=1; 
			foreach($packingData as $row):
				$item_name = $this->item->getItem($row->item_id)->item_name;
				if($i==1){$materialDetails .= $item_name.' ( Out Qty:. '.$row->out_qty.' )';}
				else{$materialDetails .= '<br> '.$item_name.' ( Out Qty:. '.$row->out_qty.' )';}
				$i++;
			endforeach;
		endif;
		
		for($j=$i;$j<$blnkRow;$j++):
			$itemList.='<tr>
    			<td style="vertical-align:top;padding:5px;" height="50px"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
    			<td style="vertical-align:top;padding:5px;"></td>
			</tr>';
		endfor;
		
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;" colspan="6">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', $totalIn).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.3f', $totalWeight).'</th>';
		$itemList.='</tr>';	
		
		
		$itemList.='<tr>
			<th class="text-left" style="vertical-align:top;height:50px;padding:5px;font-weight: normal;" colspan="8">
				<b>Material Details : </b>'.$materialDetails.'
			</th>
		</tr>';
		
		$itemList.='</table>';
		
		$bottomTable='<table class="table table-bordered" style="width:100%;">';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="width:50%;border:0px;"></td>';
				$bottomTable.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
			$bottomTable.='</tr>';
			$bottomTable.='<tr><td colspan="2" height="60" style="border:0px;"></td></tr>';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
				$bottomTable.='<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
			$bottomTable.='</tr>';
		$bottomTable.='</table>';
		
		$originalCopy = '<div style="width:210mm;height:140mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">'.$topSectionV.$baseSection.$itemList.$bottomTable.'</div>';
		
		$pdfData = $originalCopy."<br>".$vendorCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	/* Vendor Challan */
	public function vendorChallan(){
		$this->data['tableHeader'] = getProductionHeader('vendorChallan');
		$this->data['vendorData'] = $this->party->getVendorList();
		$this->load->view($this->challanForm,$this->data);
	}

	public function getChallanDTRows(){
        $result = $this->jobWork->getChallanDTRows($this->input->post());

		$sendData = array();$i=1;
        foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$items =explode(',',$row->job_inward_id);$row->item_code="";$qty='';$x=0;
			if(!empty($items)):
				foreach($items as $itm):
					$jobInData = $this->production->getJobInardDataById($itm);
					if($x==0){
						$row->item_code=(!empty($jobInData->item_code))?$jobInData->item_code:'';
						$row->qty=(!empty($jobInData->in_qty))?$jobInData->in_qty:'';
					}else{
						$row->item_code.=(!empty($jobInData->item_code))?', '.$jobInData->item_code:'';
						$row->qty.=(!empty($jobInData->in_qty))?', '.$jobInData->in_qty:'';
					}$x++;
				endforeach;
			endif;
			$row->controller = $this->data['headData']->controller;
            $sendData[] = getVendorChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function getVendorInward(){
		$this->printJson($this->jobWork->getVendorInward($this->input->post('party_id')));
	}
	
	public function getCreateVendorChallan(){
     	$data=$this->input->post('party_id');
     	$this->printJson($this->jobWork->getCreateVendorChallan($data));
    }

	public function saveVendorChallan(){
		$data = $this->input->post(); 
		$errorMessage = array();

		$data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
		$data['challan_no'] = $this->jobWork->nextChallanNo();

		$data['material_data']=""; $materialArray = array();
		if(isset($data['item_id']) && !empty($data['item_id'])):
			foreach($data['item_id'] as $key=>$value):
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $data['out_qty'][$key],
					'in_qty' => 0
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;

		if(!isset($data['job_inward_id']))
			$errorMessage['orderError'] = "Please Check atleast one order.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['created_by'] = $this->loginId;
			$data['version'] = 1;
			$this->printJson($this->jobWork->saveVendorChallan($data));
		endif;
	}

	/* delete vendor challan */
	public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWork->deleteChallan($id));
        endif;
    }
    
    public function returnVendorMaterial(){
		$id = $this->input->post('id');
		$dataRow = $this->jobWork->getVendorChallan($id);
		$packingData = json_decode($dataRow->material_data);
		$tbody="";
		if(!empty($packingData)): $i=1;
			foreach($packingData as $row):
				$item_name = $this->item->getItem($row->item_id)->item_name;
				$pendingQty = $row->out_qty - $row->in_qty;
				$tbody.='<tr>
					<td style="width:5%;">'.$i++.'</td>
					<td>
						'.$item_name.'
						<input type="hidden" name="item_id[]" value="'.$row->item_id.'">  
					</td>
					<td>
						'.$row->out_qty.'
						<input type="hidden" name="out_qty[]" value="'.$row->out_qty.'">
					</td>
					<td>
						<input type="text" class="form-control floatOnly" name="in_qty[]" value="'.$row->in_qty.'">                                
					</td>
					<td>
						'.$pendingQty.'                                
					</td>
				</tr>';
			endforeach;
		else:
		    $tbody.='<tr class="text-center"><td colspan="5">No Data Found</td></tr>';
		endif;

		$dataRow->tbody = $tbody;
		$this->data['dataRow'] = $dataRow;
		$this->load->view($this->return_material,$this->data);
	}

	public function saveReturnMaterial(){
		$data = $this->input->post();
		$data['material_data']=""; $materialArray = array();
		if(isset($data['item_id']) && !empty($data['item_id'])):
			foreach($data['item_id'] as $key=>$value):
				$materialArray[] = [
					'item_id' => $value,
					'out_qty' => $data['out_qty'][$key],
					'in_qty' => $data['in_qty'][$key]
				];
			endforeach;
			$data['material_data'] = json_encode($materialArray);
		endif;

		$this->printJson($this->jobWork->saveReturnMaterial($data));
	}
}
?>