<?php
class JobWorkOrder extends MY_Controller{
    private $indexPage = "job_work_order/index";
    private $orderForm = "job_work_order/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work Order";
		$this->data['headData']->controller = "jobWorkOrder";
		$this->data['headData']->pageUrl = "jobWorkOrder";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->jobWorkOrder->getDTRows($data);
        
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->process = "";$row->approve_status="";
            if(!empty($row->process_id)):
                $processIds = explode(",",$row->process_id);
                $processName = array();
                foreach($processIds as $key=>$value):
                    $processName[] = $this->process->getProcess($value)->process_name; 
                endforeach;
                $row->process = implode(", ",$processName);
            endif;
            if(empty($row->is_approve)):
				$row->approve_status='<span class="badge badge-pill badge-danger m-1">Pending</span>';
			else:
				$row->approve_status='<span class="badge badge-pill badge-success m-1">Approved</span><br>'.formatDate($row->approve_date);
			endif;
			if(!empty($row->is_close)):
				$row->approve_status='<span class="badge badge-pill badge-dark m-1">Close</span>';
			endif;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addOrder(){
        $this->data['jobOrderPrefix'] = "JWO/".$this->shortYear."/";
        $this->data['jobOrderNo'] = $this->jobWorkOrder->getNextOrderNo();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['productList'] = $this->item->getItemList(1);
        $this->data['processList'] = $this->process->getProcessList();
		$this->data['terms'] = $this->terms->getTermsListByType('Vendor');
		$this->data['termsCount'] = 0;
        $this->load->view($this->orderForm,$this->data);
    }

	public function getVendorProcessList(){
		$data = $this->input->post();
		$vendorData = $this->party->getParty($data['vendor_id']);
		$productData = $this->item->getProductProcessForSelect($data['product_id']);
		$options = '';
		$processList = (!empty($vendorData->process_id))?explode(",",$vendorData->process_id):array();
		foreach($processList as $key=>$value):
			if(in_array($value ,$productData)):
				$processData = $this->process->getProcess($value);
				$options .= '<option value="'.$processData->id.'">'.$processData->process_name.'</option>';
			endif;
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['vendor_id']))
            $errorMessage['vendor_id'] = "Vendor Name is required.";
        if(empty($data['process_id']))
            $errorMessage['process_id'] = "Process is required.";
        if(empty($data['product_id']))
            $errorMessage['product_id'] = "Product is required.";
        if($data['item_type'] == 1){
            if(empty($data['qty'])){ $errorMessage['qty'] = "Qty. is required."; }
        }else{
            if(empty($data['qty_kg'])){ $errorMessage['qty_kg'] = "Qty Kg. is required."; }
        }
        if(empty($data['production_days']))
            $errorMessage['production_days'] = "Production Days is required.";
		if(empty($data['rate']))
			$errorMessage['rate'] = "Rate is required.";
		if(empty($data['rate_per']))
			$errorMessage['rate_per'] = "Rate Per is required.";
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			unset($data['term_id'],$data['term_title'],$data['condition']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobWorkOrder->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
		$orderData = $this->jobWorkOrder->getJobWorkOrder($id);
		$vendorData = $this->party->getParty($orderData->vendor_id);
		$options = '';
		$processList = (!empty($vendorData->process_id))?explode(",",$vendorData->process_id):array();
		foreach($processList as $key=>$value):
			$processData = $this->process->getProcess($value);
			$selected = '';
			if(!empty($orderData->process_id)){
				if (in_array($value,explode(',',$orderData->process_id))) {
					$selected = "selected";
				}
			}
			$options .= '<option value="'.$processData->id.'" '.$selected.'>'.$processData->process_name.'</option>';
		endforeach;
		$orderData->vendorProcess = $options;
        $this->data['dataRow'] = $orderData;
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['productList'] = $this->item->getItemList($orderData->item_type);
        $this->data['processList'] = $this->process->getProcessList();
		$this->data['terms'] = $this->terms->getTermsListByType('Vendor');
		$count=0;
		if(!empty($this->data['terms'])):
			$termaData = (!empty($this->data['dataRow']->terms_conditions))?json_decode($this->data['dataRow']->terms_conditions):array();
			foreach($this->data['terms'] as $row):
				if(in_array($row->id,array_column($termaData,'term_id'))):
					$count++;
				endif;
			endforeach;
		endif;	
		$this->data['termsCount'] = $count;
		$this->load->view($this->orderForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkOrder->delete($id));
        endif;
    }

    public function approveJobWorkOrder(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->jobWorkOrder->approveJobWorkOrder($data));
		endif;
	}
	
	public function changeJobStatus(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobWorkOrder->changeJobStatus($data));
        endif;
    }

    function jobworkOrderChallan($id)
	{
		$jobData = $this->jobWorkOrder->getJobworkOutData($id);
        $jobData->process = "";
        if(!empty($jobData->process_id)):
            $processIds = explode(",",$jobData->process_id);
            $processName = array();
            foreach($processIds as $key=>$value):
                $processName[] = $this->process->getProcess($value)->process_name; 
            endforeach;
            $jobData->process = implode(", ",$processName);
        endif;

		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		
		$pdays = (!empty($jobData->production_days)) ? "+".$jobData->production_days." day" : "+0 day";
		
		$delivery_date = date('d-m-Y',strtotime($pdays, strtotime($jobData->created_at)));
		
		$topSectionO ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK ORDER</td>
							<td style="width:30%;" class="text-right"><span style="letter-spacing:1px;">GST No.:<span style="letter-spacing:1px;">'.$companyData->company_gst_no.'</span></td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
		$topSectionV ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:30%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">JOB WORK ORDER</td>
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
								<td class="text-left" height="35"><b>Order No. :</b> '.getPrefixNumber($jobData->jwo_prefix,$jobData->jwo_no).' </td>
							</tr>
							<tr>
								<td class="text-left" height="35"><b>Date :</b> '.date("d-m-Y",strtotime($jobData->created_at)).' </td>
							</tr>
						</table>';
		$itemList='<table class="table table-bordered jobChallanTable">
					<tr class="text-center bg-light-grey">
						<th>Material Description</th><th style="width:15%;">'.(($jobData->rate_per == 1)?"Pcs.":"Kg.").'</th><th style="width:15%;">Rate</th><th style="width:15%;">Amount</th>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;">
							<b>Item Code : </b>'.$jobData->item_code.(($jobData->rate_per == 2)?' ('.sprintf('%0.0f', $jobData->qty).' Pcs.)':"").'
						</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.0f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->rate).'</td>
						<td class="text-center" rowspan="4" style="vertical-align:top;padding:5px;">'.sprintf('%0.2f', $jobData->amount).'</td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;"><b>Delivery Date : </b>'.$delivery_date.'</td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:25px;padding:5px;"><b>Process : </b>'.$jobData->process.'</b></td>
					</tr>
					<tr>
						<td style="vertical-align:top;height:150px;padding:5px;"><b>Remarks : </b>'.$jobData->remark.'</td>
					</tr>';
		$itemList.='<tr class="bg-light-grey">';
			$itemList.='<th class="text-right" style="font-size:14px;">Total</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.0f', ($jobData->rate_per == 1)?$jobData->qty:$jobData->qty_kg).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->rate).'</th>';
			$itemList.='<th class="text-center" style="font-size:14px;">'.sprintf('%0.2f', $jobData->amount).'</th>';
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
		
		// $originalCopy = '<div style="width:210mm;height:140mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$originalCopy = '<div style="width:210mm;">'.$topSectionO.$baseSection.$itemList.$bottomTable.'</div>';
		$vendorCopy = '<div style="width:210mm;height:140mm;">'.$topSectionV.$baseSection.$itemList.$bottomTable.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO -REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
    function jobworkOrderChallanFull($id)
	{
		$this->data['jobData'] = $jobData = $this->jobWorkOrder->getJobworkOutData($id);
        $jobData->process = "";
        if(!empty($jobData->process_id)):
            $processIds = explode(",",$jobData->process_id);
            $processName = array();
            foreach($processIds as $key=>$value):
                $processName[] = $this->process->getProcess($value)->process_name; 
            endforeach;
            $this->data['jobData']->process = $jobData->process = implode(", ",$processName);
        endif;

		$this->data['companyData'] = $companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		
		$prepare = $this->employee->getEmp($jobData->created_by);
		$prepareBy = $prepare->emp_name.' <br>('.formatDate($jobData->created_at).')'; 
		$prepareSign='';$approveBy = '';$approveSign = '';
		if(!empty($jobData->is_approve)){
			$approve = $this->employee->getEmp($jobData->is_approve);
			$approveBy .= $approve->emp_name.' <br>('.formatDate($jobData->approve_date).')'; 
			$sign_img = base_url('assets/uploads/emp_sign/sign_'.$jobData->is_approve.'.png');
			$approveSign = '<img src="'.$sign_img.'" style="width:100px;">'; 
		}
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">';
		$htmlFooter.='<tr>';
			$htmlFooter.='<td class="text-center" style="width:50%;border:0px;"></td>';
			$htmlFooter.='<td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>';
		$htmlFooter.='</tr>';
		$htmlFooter.='<tr>';
			$htmlFooter.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;"></td>';
			$htmlFooter.='<td class="text-center" style="font-size:1rem;border:0px;">'.$approveSign.'</td>';
		$htmlFooter.='</tr>';
		$htmlFooter.='<tr>';
			$htmlFooter.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;"></td>';
			$htmlFooter.='<td class="text-center" style="font-size:1rem;border:0px;">'.$approveBy.'</td>';
		$htmlFooter.='</tr>';
		$htmlFooter.='<tr>';
			$htmlFooter.='<td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Received By</td>';
			$htmlFooter.='<td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>';
		$htmlFooter.='</tr>';
		$htmlFooter.='</table>';
		$pdfData = $this->load->view('job_work_order/printjw',$this->data,true);
		//echo $pdfData;exit;
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO -REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($this->data['jobData']->is_approve)){ $mpdf->SetWatermarkImage($logo,0.05,array(120,60)); $mpdf->showWatermarkImage = true; }
		else{ $mpdf->SetWatermarkText('Not Approved Copy'); $mpdf->showWatermarkText = true;}
		
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,39,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}
	
	public function jobWorkOrderView()
	{
		$id = $this->input->post('id');
		$this->data['jobData'] = $jobData = $this->jobWorkOrder->getJobworkOutData($id);
        $jobData->process = "";
        if(!empty($jobData->process_id)):
            $processIds = explode(",",$jobData->process_id);
            $processName = array();
            foreach($processIds as $key=>$value):
                $processName[] = $this->process->getProcess($value)->process_name; 
            endforeach;
            $this->data['jobData']->process = $jobData->process = implode(", ",$processName);
        endif;

		$this->data['companyData'] = $companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png'); $unapproved = base_url('assets/images/unapproved.jpg');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');

		$pdfData = $this->load->view('job_work_order/printjw',$this->data,true);

		$this->printJson(['status'=>1,'pdfData'=>$pdfData]);
	}
	
	public function getItemListForSelect(){
		$item_type = $this->input->post('item_type');
		$product_id = $this->input->post('product_id'); 
        $result = $this->item->getItemListForSelect($item_type);
		$options="";
		if(!empty($result)): 
			$options .= '<option value="">Select Product</option>';
			foreach($result as $row):
				$selected = (!empty($product_id) && $product_id == $row->id)?'selected':'';
				if($row->item_type == 1){
					$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected."> ".$row->item_code."</option>";
				}else{
					$options .= "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->item_name."</option>";
				}
				endforeach;
		else:
			$options .= '<option value="">Select Product</option>';
		endif;
		
		$this->printJson(['status'=>1, 'options'=>$options]);
	}
}
?>