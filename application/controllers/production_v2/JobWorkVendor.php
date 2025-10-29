<?php
class JobWorkVendor extends MY_Controller{
    private $indexPage = "production_v2/job_work_vendor/index";
    private $returnForm = "production_v2/job_work_vendor/job_work_return";
	private $challanForm = "production_v2/job_work_vendor/vendor_challan";
	private $return_material = "production_v2/job_work_vendor/return_material";
	private $hold_ok_mevement = "production_v2/job_work_vendor/hold_ok_mevement";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work Vendor";
		$this->data['headData']->controller = "production_v2/jobWorkVendor";
		$this->data['headData']->pageUrl = "production_v2/jobWorkVendor";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader("jobWorkVendor");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0,$dates=''){
		$data = $this->input->post(); $data['status'] = $status;
		if(!empty($dates)){$data['from_date'] = explode('~',$dates)[0];$data['to_date'] = explode('~',$dates)[1];}
        $result = $this->jobWorkVendor_v2->getDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->status_per = round($row->status_per,2);
            if($row->status_per == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">Pending - '.$row->status_per.'%</span>';
			elseif($row->status_per < 100):
				$row->status = '<span class="badge badge-pill badge-warning m-1">In Process - '.$row->status_per.'%</span>';
			else:
				$row->status = '<span class="badge badge-pill badge-success m-1">Completed - '.$row->status_per.'%</span>';
			endif;			
			
			$challan = $this->jobWorkVendor_v2->getChallanData($row->id); $row->challan_date = '';
			if(!empty($challan->challan_date)){ 
				$chhallanDate = new DateTime(date('Y-m-d',strtotime($challan->challan_date)));
				$todayDate = new DateTime(date('Y-m-d',strtotime(date('Y-m-d'))));
				$dueDays = $chhallanDate->diff($todayDate);
				$day = $dueDays->format('%d');
				$row->challan_date = formatDate($challan->challan_date).'<br>('.($day + 1).' Days)'; 
			}
			
            $row->controller = $this->data['headData']->controller;			
            $sendData[] = getJobWorkVendorData($row);
        endforeach;
        $result['data'] =$sendData;
        $this->printJson($result);
    }

    public function jobWorkReturn(){
        $data = $this->input->post();
        $this->data['dataRow'] = $data;
        $this->data['transHtml'] = $this->jobWorkVendor_v2->getReturnTransaction($data['id'])['html'];
        $this->load->view($this->returnForm,$this->data);
    }

    public function jobWorkReturnSave(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['qty'])):
            $errorMessage['qty'] = "Qty is required.";
        else:
            $pendingQty = $this->jobWorkVendor_v2->getJobWorkVendorRow($data['id'])->pending_qty;
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Qty not available for returned.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->jobWorkVendor_v2->saveJobWorkReturn($data));
        endif;
    }

    public function deleteReturnTrans(){
        $data = $this->input->post();
        if(empty($data['id']) || $data['key'] == ""):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v2->deleteReturnTrans($data));
        endif;
    }

    /* Vendor Challan */
	public function challan(){
		$this->data['tableHeader'] = getProductionHeader('vendorChallan');
		$this->data['vendorData'] = $this->party->getVendorList();
		$this->load->view($this->challanForm,$this->data);
	}

    public function getChallanDTRows(){
        $result = $this->jobWorkVendor_v2->getChallanDTRows($this->input->post());

		$sendData = array();$i=1;
        foreach($result['data'] as $row):
			$row->sr_no = $i++;
			$items =explode(',',$row->job_inward_id);
            $row->item_code="";$qty='';
			if(!empty($items)):
                $itemCode = array(); $qty=array();
				foreach($items as $itm):
					$jobInData = $this->jobWorkVendor_v2->getJobInardDataById($itm);
                    $itemCode[] = (!empty($jobInData->item_code))?$jobInData->item_code:'';
                    $qty[] = (!empty($jobInData->out_qty))?$jobInData->out_qty:'';  
				endforeach;
                $row->item_code=(!empty($itemCode))?implode(",",$itemCode):'';
				$row->qty=(!empty($qty))?implode(",",$qty):'';
			endif;
			$row->controller = $this->data['headData']->controller;
            $sendData[] = getVendorChallanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function createVendorChallan(){
        $party_id=$this->input->post('party_id');
        $result = $this->jobWorkVendor_v2->createVendorChallan($party_id);        
        $this->printJson($result);
    }

    public function saveVendorChallan(){
		$data = $this->input->post(); 
		$errorMessage = array();

		$data['challan_prefix'] = 'JO/'.$this->shortYear.'/';
		$data['challan_no'] = $this->jobWorkVendor_v2->nextChallanNo();

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
			$data['version'] = 2;
			$this->printJson($this->jobWorkVendor_v2->saveVendorChallan($data));
		endif;
	}

    /* delete vendor challan */
	public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkVendor_v2->deleteChallan($id));
        endif;
    }

    function jobworkOutChallan($id){
		$jobData = $this->jobWorkVendor_v2->getVendorChallan($id);
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
					</tr>';
			$jobTrans = explode(',', $jobData->job_inward_id);
			$packingData = json_decode($jobData->material_data); 
			$i=1;$itemCode="";$jobNo="";$deliveryDate="";$processName="";$remark="";$inQty="";$weight=""; $totalOut=0; $totalWeight=0; $blnkRow=4;
			foreach($jobTrans as $row):
				$jobTransData = $this->jobWorkVendor_v2->getJobworkOutData($row);
				if(!empty($jobTransData)){
                    $jobData = new stdClass();
                    $jobData->id = $jobTransData->job_card_id;
                    $jobData->product_id = $jobTransData->product_id;
                    $reqMaterials = $this->jobcard_v2->getProcessWiseRequiredMaterials($jobData)['resultData'][0]; 
                    
    				$pdays = (!empty($jobTransData->production_days)) ? "+".$jobTransData->production_days." day" : "+0 day";
    				$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobTransData->created_at)));
    
    
    				$itemList.='<tr>
    					<td style="vertical-align:top;padding:5px;">'.$i++.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$jobTransData->item_code.'</td>
    					<td style="vertical-align:top;padding:5px;">'. getPrefixNumber($jobTransData->job_prefix,$jobTransData->job_no).' <br> <small>(Batch No: '.((!empty($reqMaterials['heat_no']))?$reqMaterials['heat_no']:"").')</small> </td>
    					<td style="vertical-align:top;padding:5px;">'.$delivery_date.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$jobTransData->process_name.'</td>
    					<td style="vertical-align:top;padding:5px;">'.$jobTransData->jwoRemark.'</td>
    					<td class="text-center" style="vertical-align:top;padding:5px;">'.((!empty($jobTransData->out_qty))?sprintf('%0.0f',$jobTransData->out_qty):'').'</td>
    				</tr>';
    				$totalOut += sprintf('%0.0f',$jobTransData->out_qty);
				}
			endforeach;
		$materialDetails="";
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
			</tr>';
		endfor;
		
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;" colspan="6">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', $totalOut).'</th>';
		$itemList.='</tr>';	
		
		
		$itemList.='<tr>
			<th class="text-left" style="vertical-align:top;height:50px;padding:5px;font-weight: normal;" colspan="7">
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

    public function returnVendorMaterial(){
		$id = $this->input->post('id');
		$dataRow = $this->jobWorkVendor_v2->getVendorChallan($id);
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

		$this->printJson($this->jobWorkVendor_v2->saveReturnMaterial($data));
	}

	public function holdToOkMovement(){
		$this->data['headData']->pageUrl = "production_v2/jobWorkVendor";
        $this->data['tableHeader'] = getProductionHeader('holdToOk');
        $this->load->view($this->hold_ok_mevement,$this->data);
	}

	public function getMovementDTRows(){
		$data=$this->input->post();

		$result = $this->jobWorkVendor_v2->getMovementDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$row->vendor_name = !empty($row->party_name)?$row->party_name:'In House';
			$sendData[] = getHoldToOkMovementData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
	}
}
?>