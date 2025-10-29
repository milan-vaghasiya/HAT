<?php
class Outsource extends MY_Controller
{
    private $indexPage = "production/outsource/index";
    private $movementForm = "production/jobcard/production_form";
    
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Outsource";
        $this->data['headData']->controller = "production/outsource";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("outSource"); 
		$this->data['vendorData'] = $this->party->getVendorList();
        $this->load->view($this->indexPage,$this->data);
    }
    
    public function getDTRows($status=0,$dates=''){
		$data = $this->input->post(); $data['status'] = $status;
		if(!empty($dates)){$data['from_date'] = explode('~',$dates)[0];$data['to_date'] = explode('~',$dates)[1];}
        $sendData = array();$i=1;
        
        $result = $this->outsource->getDTRows($data);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;			
            $sendData[] = getOutsourceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function createOutsourceChallan(){
        $postData=$this->input->post();
        $processData = $this->process->getProcessList();
        $processOption ="<option value=''>Select Process</option>";
		if(!empty($processData)):
			foreach($processData as $row):
			    $processOption .= '<option value="'.$row->id.'">'.$row->process_name.'</option>';
			endforeach;
		endif;
		$result = ['status'=>1,'processOption'=>$processOption];
        $this->printJson($result);
    }
    
    public function getPendingOSTransaction(){
        $postData=$this->input->post(); 
        $resultData = $this->outsource->getPendingOSTransaction($postData);// print_r($resultData);exit;
		$transData="";
        $i=1;
		if(!empty($resultData)):
			foreach ($resultData as $row) :
				$transData .= '<tr>
					<td class="text-center fs-12">
						<input type="checkbox" id="md_checkbox_' . $i . '" name="id[]" class="filled-in chk-col-success challanCheck" data-rowid="'.$i.'" value="' . $row->id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
					</td>
					<td class="text-center fs-12">' . $row->job_number. '</td>
					<td class="text-cente fs-12">' . formatDate($row->job_date) . '</td>
					<td class="text-center fs-12">' .(!empty($row->item_code)?'['.$row->item_code.'] '.$row->item_name:$row->item_name) . '</td>
					<td class="text-center fs-12">' . floatVal($row->qty) . '</td>
					<td class="text-center fs-12">' . $row->pending_qty . '</td>
					<td class="text-center fs-12">
						<input type="hidden" id="out_qty'.$i.'" value="'.floatVal($row->pending_qty).'">                   
						<input type="text" id="ch_qty' . $i . '" name="ch_qty[]" data-rowid="'.$i.'" class="form-control challanQty floatOnly" value="0" disabled>
						<div class="error chQty'.$row->id.'"></div>
					</td>
					<td>
						<input type="text" id="trans_remark' . $i . '" name="trans_remark[]" data-rowid="'.$i.'" class="form-control" value="" disabled>
					</td>
				</tr>';
				$i++;
			endforeach;
		else:
			$transData .= '<tr><td colspan="7" class="text-center">No data available in table</td></tr>';
		endif;
		
        $this->printJson(['status'=>1,'transData'=>$transData]);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
		
		$data['trans_prefix'] = (!empty($data['trans_prefix']))?$data['trans_prefix']:'VC/' .n2y(date('Y'));
		if(empty($data['challan_id']) && empty($data['trans_no'])):
			$data['trans_no'] = $this->outsource->nextChallanNo();
		endif;

		if (!isset($data['id']))
			$errorMessage['orderError'] = "Please Check atleast one order.";
		
		if(!empty($data['id'][0])):	
    		foreach($data['id'] as $key=>$value):
    			if(empty($data['ch_qty'][$key]) && empty($data['challan_id'])):
    				$errorMessage['chQty'.$value] = "Qty. is required.";
				// else:
				// 	if(!empty($data['challan_id'])):
				// 		$data['ch_qty'][$key]+=$data['receive_qty'][$key];
    			// 	endif;
				endif;
    		endforeach;
        endif;
        
		if (!empty($errorMessage)) :
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else :
			$data['created_by'] = $this->loginId;
			$this->printJson($this->outsource->save($data));
		endif;
    }

    public function vendorInward(){
        $data = $this->input->post();
        $id =  $data['id'];
        $outwardData = $this->processMovement->getApprovalData($data['id']);
        $transData = $this->processMovement->getOutwardTransPrint($data['job_trans_id']);
        $outwardData->pqty = $transData->qty - $transData->outsource_qty;
        $this->data['dataRow'] = $outwardData;
        $this->data['dataRow']->entry_type = 4;
        $this->data['dataRow']->ref_id =$data['job_trans_id'];
        $this->data['dataRow']->vendor_id =$transData->vendor_id;
        $this->data['outwardTrans'] = $this->processMovement->getOutwardTrans($outwardData->id,4)['htmlData'];
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['idleReasonList'] = $this->comment->getIdleReason();
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $this->data['shiftData'] = $this->shiftModel->getShiftList();
        $this->data['masterOption'] = $this->processMovement->getMasterOptions();
        $prdPrsData = $this->item->getPrdProcessDataProductProcessWise(['item_id'=>$outwardData->product_id,'process_id'=>$outwardData->in_process_id]);
        $this->data['cycle_time'] = $prdPrsData->cycle_time;
        $this->data['machineData'] = $this->item->getMachineTypeWiseMachine();
        $jobCardData = $this->jobcard->getJobcard($outwardData->job_card_id);
        $jobProcess = explode(",", $jobCardData->process);

        $stageHtml = '<option value="">Select Stage</option>
                    <option value="0" data-process_name="Row Material">Row Material</option>';
        if (!empty($outwardData->in_process_id)) {
            $in_process_key = array_keys($jobProcess, $outwardData->in_process_id)[0];
            foreach ($jobProcess as $key => $value) :
                if ($key <= $in_process_key) :
                    $processData = $this->process->getProcess($value);
                    $stageHtml .= '<option value="' . $processData->id . '" data-process_name="' . $processData->process_name . '">' . $processData->process_name . '</option>';
                endif;
            endforeach;
        }
        $this->data['dataRow']->stage = $stageHtml;
        $this->load->view($this->movementForm, $this->data);
    }
    
    public function delete(){
        $id = $this->input->post('id');
		if (empty($id)) :
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else :
			$this->printJson($this->outsource->delete($id));
		endif;
    }
    
    function jobworkOutChallan($id){
		$jobData = $this->outsource->getVendorChallan($id);
		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK CHALLAN</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span><br><b>Original Copy</b></span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:50px;"></td>
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
								<td class="text-left" height="25"><b>Challan No. :</b> '.$jobData->trans_number.' </td>
							</tr>
							<tr>
								<td class="text-left" height="25"><b>Challan Date :</b> '.date("d-m-Y",strtotime($jobData->trans_date)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th style="width:10px">#</th>
						<th style="width:50px">Grade</th>
						<th>Product</th>
						<th style="width:60px">Wo.No.</th>
						<th style="width:110px;">F.M. Size</th>
						<th style="width:50px">PRC No.</th>
						<th style="width:80px">Process</th>
						<th style="width:50px">Weight</th>
						<th style="width:40px">Qty</th>
					</tr>';
                     
			$i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalOut=0; $totalWeight=0; $blnkRow=4;
			$challanData = $this->outsource->getVendorChallanTransData($id);
            if(!empty($challanData)){
                foreach($challanData as $jobTransData):
					$wt_pcs = round(($jobTransData->finished_weight * $jobTransData->qty),2);
					$notes = !empty($jobTransData->remark)?'<br>(<small>Notes : '.$jobTransData->remark.'</small>)':'';
					$itemList.='<tr class="text-center">
									<td>'.$i++.'</td>
									<td>'.$jobTransData->material_grade.'</td>
									<td>'.(!empty($jobTransData->item_code)?'['.$jobTransData->item_code.'] '.$jobTransData->item_name:$jobTransData->item_name).$notes.'</td>
									<td>'.$jobTransData->wo_no.'</td>
									<td>'.$jobTransData->size.'</td>
									<td>'.$jobTransData->job_number.'</td>
									<td>'.$jobTransData->process_name.'</td>
									<td>'.sprintf('%0.2f',($wt_pcs)).'</td>
									<td>'.((!empty($jobTransData->qty))?sprintf('%0.0f',$jobTransData->qty):'').'</td>
								</tr>';
                    $totalOut += sprintf('%0.0f',$jobTransData->qty);
                    $totalWeight += $wt_pcs;
                endforeach;
            }
		
		for($j=$i;$j<$blnkRow;$j++):
			$itemList.='<tr>
    			<td height="50px"></td>
    			<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
			</tr>';
		endfor;
		
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;" colspan="7">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $totalWeight).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', $totalOut).'</th>';
		$itemList.='</tr></table>';	
		
		$bottomTable='<table class="table table-bordered" style="width:100%;">';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-left fs-13" style="width:50%;border:0px;"><i><b>Notes: </b>'.$jobData->remark.'</i></td>';
				$bottomTable.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
			$bottomTable.='</tr>';
			$bottomTable.='<tr><td colspan="2" height="30" style="border:0px;"></td></tr>';
			$bottomTable.='<tr>';
				$bottomTable.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
				$bottomTable.='<td class="text-center" style="font-size:0.9rem;border:0px;">'.$jobData->prepared_by.'<br><b>(Authorised Signatory)</b></td>';
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
}
