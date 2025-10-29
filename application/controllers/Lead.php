<?php
class Lead extends MY_Controller
{
    private $indexPage = "lead/index";
    private $leadForm = "lead/lead_form";
    private $leadStatus = Array("Initited","Appointment Fixed","Qualified","Inquiry Generated","Proposal","In Negotiation","Confirm","Close");
    private $appointmentMode = Array("Email","Online","Visit","Phone","Other");
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Lead";
		$this->data['headData']->controller = "lead";
		$this->data['headData']->pageUrl = "lead";
	}
	
	public function index(){
        $this->data['leadData'] = $this->leads->getLeadData();
        $this->load->view($this->indexPage,$this->data);
    }

    public function addLead(){
        $this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->leadForm,$this->data);
    }

	public function getLeadData(){
		$leadData = $this->leads->getLeadData();
		$inquiryData = $this->leads->getInquiries();
		$quoteData = $this->leads->getSalesQuotation();
		$no_records = '<img src="'.base_url().'assets/images/no_records.jpg" style = "width:100%;">';
		$leads = $no_records;$qualifiedLeads = $no_records;$leadInquiry = $no_records;$salesQuotation = $no_records;
		if(!empty($leadData))
		{
			$leads = '';$qualifiedLeads = '';
			foreach($leadData as $row)
			{
				$deleteParam = $row->id.",'Lead'";
				$editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editLead', 'title' : 'Update Lead Detail'}";
				
				if($row->lead_status <= 1)
				{
					$leads .= '<div class="lead-row bg-panel1-light transition" data-category="transition">
								<div class="lead-text w-100">
									<h6 class="font-medium ">'.$row->party_name.'</h6>
									<span class="m-b-15 d-block">'.$row->party_address.'</span>
									<div class="lead-footer lh-25">
										<div class="actionWrapper float-right pad-left-5" style="position:relative;">
											<div class="actionButtons actionButtonsLeft">
												<a class="mainButton btn-panel1 small-btn" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
												<div class="btnDiv small-btn-div">
													<a class="btn1 btn-primary leadAction" href="javascript:void(0)" data-id="'.$row->id.'"  datatip="Appointment" data-modal_id="modal-lg" data-form_title="Appointments"  data-fnsave="setAppointment" data-function="getAppointments" flow="down"><i class="far fa-calendar-check"></i></a>
													<a class="btn1 btn-success permission-modify" href="javascript:void(0)" onclick="editLead('.$editParam.');" datatip="Edit" flow="down"><i class="ti-pencil font-bold"></i></a>
													<a class="btn1 btn-danger permission-remove" href="javascript:void(0)" onclick="trashLead('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>
												</div>
											</div>
										</div>
										<span class="float-right">'.date('d F Y',strtotime($row->created_at)).'</span>
										<span class="dropdown">
											<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down"><span class="label label-panel1 label-rounded" style="font-size:12px;letter-spacing:1px;">Action</span></a>
											<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
												<div class="d-flex no-block align-items-center p-10 bg-primary text-white">Progress</div>
												
												<a class="dropdown-item leadActionStatic permission-modify" data-id="'.$row->id.'" data-lead_status="2" data-fnsave="setLeadStatus"  data-action_name="Qualified" href="javascript:void(0)"> Qualified</a>
												<a class="dropdown-item leadAction permission-remove" href="javascript:void(0)">Close</a>
											</div>
										</span>
									</div>
								</div>
								<div class="drag-handler border-panel1"></div>
							</div>';
				}
				if($row->lead_status == 2)
				{
					$qualifiedLeads .= '<div class="lead-row bg-success-light transition" data-category="transition">
								<div class="lead-text w-100">
									<h6 class="font-medium ">'.$row->party_name.'</h6>
									<span class="m-b-15 d-block">'.$row->party_address.'</span>
									<div class="lead-footer lh-25">
										<div class="actionWrapper float-right pad-left-5" style="position:relative;">
											<div class="actionButtons actionButtonsLeft">
												<a class="mainButton btn-success small-btn" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
												<div class="btnDiv small-btn-div">
													<a class="btn1 btn-primary leadAction" href="javascript:void(0)" data-id="'.$row->id.'"  datatip="Appointment" data-modal_id="modal-lg" data-form_title="Appointments"  data-fnsave="setAppointment" data-function="getAppointments" flow="down"><i class="far fa-calendar-check"></i></a>
													<a class="btn1 btn-success permission-modify" href="javascript:void(0)" onclick="editLead('.$editParam.');" datatip="Edit" flow="down"><i class="ti-pencil font-bold"></i></a>
													<a class="btn1 btn-danger permission-remove" href="javascript:void(0)" onclick="trashLead('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>
												</div>
											</div>
										</div>
										<span class="float-right">'.date('d F Y',strtotime($row->created_at)).'</span>
										<span class="dropdown">
											<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down"><span class="label label-success label-rounded" style="font-size:12px;letter-spacing:1px;">Action</span></a>
											<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
												<div class="d-flex no-block align-items-center p-10 bg-primary text-white">Progress</div>
												
												<a class="dropdown-item leadAction permission-modify" data-id="'.$row->id.'" href="'.base_url("salesEnquiry/addEnquiry/".$row->id).'">New Inquiries</a>
												<a class="dropdown-item leadAction permission-remove" href="javascript:void(0)">Close</a>
											</div>
										</span>
									</div>
								</div>
								<div class="drag-handler border-success"></div>
							</div>';
				}
			}
		}
		if(!empty($inquiryData))
		{
			$leadInquiry = '';
			foreach($inquiryData as $row)
			{
				$deleteParam = $row->id.",'Inquiry'";
				$custData = new stdClass;$caddres='';
				if(!empty($row->customer_id)){$custData = $this->party->getParty($row->customer_id);}
				if(!empty($custData)){$caddres = $custData->party_address;}
				
				$leadInquiry .= '<div class="lead-row bg-primary-light transition" data-category="transition">
								<div class="lead-text w-100">
									<h6 class="font-medium ">
										'.$row->customer_name.'
										<span class="float-right">#'.$row->enq_prefix.$row->enq_no.'</span>
									</h6>
									<span class="m-b-15 d-block">'.$caddres.'</span>
									<div class="lead-footer lh-25">
										<div class="actionWrapper float-right pad-left-5" style="position:relative;">
											<div class="actionButtons actionButtonsLeft">
												<a class="mainButton btn-primary small-btn" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
												<div class="btnDiv small-btn-div">
													<a class="btn1 btn-success permission-modify" href="'.base_url('salesEnquiry/edit/'.$row->id).'"  datatip="Edit" flow="down"><i class="ti-pencil font-bold"></i></a>
													<a class="btn1 btn-danger permission-remove" href="javascript:void(0)" onclick="trashLead('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>
												</div>
											</div>
										</div>
										<span class="float-right">'.date('d F Y',strtotime($row->enq_date)).'</span>
										<span class="dropdown">
											<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down"><span class="label label-primary label-rounded" style="font-size:12px;letter-spacing:1px;">Action</span></a>
											<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
												<div class="d-flex no-block align-items-center p-10 bg-primary text-white">Progress</div>
												
												<a href="javascript:void(0)" class="dropdown-item sendQuotation permission-write" data-id="'.$row->id.'"  data-party="'.$row->customer_name.'" data-ref_by="'.$row->ref_by.'" data-enqno="'.$row->enq_prefix.$row->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($row->enq_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getEnquiryData" data-form_title="Create Quotation" >Send Quotation</a>
												
												<a class="dropdown-item leadAction permission-remove" href="javascript:void(0)">Close</a>
											</div>
										</span>
									</div>
								</div>
								<div class="drag-handler border-success"></div>
							</div>';
			}
		}
		if(!empty($quoteData))
		{
			$salesQuotation = '';
			foreach($quoteData as $row)
			{
				$deleteParam = $row->id.",'Quotation'";
				$custData = new stdClass;$caddres='';
				if(!empty($row->customer_id)){$custData = $this->party->getParty($row->customer_id);}
				if(!empty($custData)){$caddres = $custData->party_address;}
				
				$cnfQuote = '<a href="javascript:void(0)" class="dropdown-item confirmQuotation" data-id="'.$row->id.'"  data-party="'.$row->customer_name.'" data-customer_id="'.$row->customer_id.'" data-quote_no="'.$row->quote_prefix.$row->quote_no.'" data-quotation_date="'.date("d-m-Y",strtotime($row->quotation_date)).'" data-button="both" data-modal_id="modal-md" data-function="getQuotationItems" data-form_title="Confirm Quotation" >Confirm Quotation</a>';
				
				if($row->quote_status == 1){$cnfQuote = '';}
				
				$salesQuotation .= '<div class="lead-row bg-warning-light transition" data-category="transition">
								<div class="lead-text w-100">
									<h6 class="font-medium ">
										'.$row->customer_name.'
										<span class="float-right">#'.$row->quote_prefix.$row->quote_no.'</span>
									</h6>
									<span class="m-b-15 d-block">'.$caddres.'</span>
									<div class="lead-footer lh-25">
										<div class="actionWrapper float-right pad-left-5" style="position:relative;">
											<div class="actionButtons actionButtonsLeft">
												<a class="mainButton btn-warning small-btn" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
												<div class="btnDiv small-btn-div">
													<a class="btn1 btn-success permission-modify" href="'.base_url('salesEnquiry/editQuotation/'.$row->id).'"  datatip="Edit" flow="down"><i class="ti-pencil font-bold"></i></a>
													<a class="btn1 btn-danger permission-remove" href="javascript:void(0)" onclick="trashQuote('.$deleteParam.');" datatip="Remove" flow="down"><i class="fas fa-trash-alt"></i></a>
												</div>
											</div>
										</div>
										<span class="float-right">'.date('d F Y',strtotime($row->quotation_date)).'</span>
										<span class="dropdown">
											<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down"><span class="label label-warning label-rounded" style="font-size:12px;letter-spacing:1px;">Action</span></a>
											<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
												<div class="d-flex no-block align-items-center p-10 bg-primary text-white">Progress</div>
												'.$cnfQuote.'
												<a href="'.base_url('lead/printQuotation/'.$row->id).'" class="dropdown-item permission-approve" target="_blank">Print Quotation</a>
												
												<a class="dropdown-item leadAction permission-remove" href="javascript:void(0)">Close</a>
											</div>
										</span>
									</div>
								</div>
								<div class="drag-handler border-warning"></div>
							</div>';
			}
		}
		
		$this->printJson(['status'=>1,'leadData'=>$leads,'qualifiedLeads'=>$qualifiedLeads,'leadInquiry'=>$leadInquiry,'salesQuotation'=>$salesQuotation]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if(empty($data['party_category']))
            $errorMessage['party_category'] = "Party Category is required.";
        if(empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
        if(empty($data['party_mobile']))
            $errorMessage['party_mobile'] = "Contact No. is required.";
        if(empty($data['country_id']))
			$errorMessage['country_id'] = 'Country is required.';
        if(empty($data['state_id']))
        {
            if(empty($data['statename']))
                $errorMessage['state_id'] = 'State is required.';
            else
                $data['state_id'] = $this->party->saveState($data['statename'],$data['country_id']);
        }
        unset($data['statename']);
        if(empty($data['city_id']))
        {
            if(empty($data['ctname']))
                $errorMessage['city_id'] = 'City is required.';
            else
                $data['city_id'] = $this->party->saveCity($data['ctname'],$data['state_id'],$data['country_id']);
        }
        unset($data['ctname']);
        if(empty($data['party_address']))
            $errorMessage['party_address'] = "Address is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['party_name'] = ucwords($data['party_name']);
			$data['delivery_address'] = $data['party_name'];$data['delivery_pincode'] = $data['party_pincode'];
            $this->printJson($this->party->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $result->state = $this->party->getStates($result->country_id,$result->state_id)['result'];
        $result->city = $this->party->getCities($result->state_id,$result->city_id)['result'];
        $this->data['dataRow'] = $result;
        $this->data['countryData'] = $this->party->getCountries();        
        $this->load->view($this->leadForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getStates(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->party->getStates($id));
        endif;
    }

    public function getCities(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
        else:
            $this->printJson($this->party->getCities($id));
        endif;
    }
    
    public function getAppointments(){
        $this->data['appointmentMode'] = $this->appointmentMode;
        $this->data['appintmentData'] = $this->leads->getAppointments($this->input->post('lead_id'));
        $this->load->view('lead/appointment_form',$this->data);
    }

    public function setAppointment(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['appointment_date']))
            $errorMessage['appointment_date'] = "Date is required.";
        if(empty($data['appointment_time']))
            $errorMessage['appointment_time'] = "Time is required.";
        if(empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['contact_person'] = ucwords($data['contact_person']);
			$data['appointment_date'] = formatDate($data['appointment_date'],'Y-m-d');
			$data['appointment_time'] = formatDate($data['appointment_time'],'h:i:s');
            $this->printJson($this->leads->setAppointment($data));
        endif;
    }

    public function deleteAppointment(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leads->deleteAppointment($id));
        endif;
    }

    public function setLeadStatus(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leads->setLeadStatus($this->input->post()));
        endif;
    }

    public function printQuotation($id)
	{
		$quoteData = $this->leads->getSalesQuotationById($id);
		$inquiryData = $this->leads->getInquiryData($quoteData->enq_id);
		$custData = $this->party->getParty($quoteData->customer_id);
		$companyData = $this->db->where("id",1)->get('company_info')->row();
		$currencyCode = "INR";$symbol = "";
		// if(!empty($quoteData->currency_id)):
			// $currencyData = $this->party->getCurrency($quoteData->currency_id);
			// $currencyCode = $currencyData->code;
			// $symbol = $currencyData->symbol;
		// else:
			// $currencyCode = "INR";
			// $symbol = "";
		// endif;
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/letterhead_top.png');
		$footerImg = base_url('assets/images/lh_footer.png');
		$logo_path=base_url('assets/images/'.$companyData->company_logo);
		$sign=base_url('assets/images/jpsign.png');
		
		$itemList='<table class="table table-bordered poItemList qtItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th><th style="text-align:left !important;">Product Description</th>
						<th style="width:10%;">UOM</th><th style="width:15%;">Quantity</th>
						<th style="width:15%;">Rate<br><small>('.$currencyCode.')</small></th>
						<th style="width:15%;">Amount<br><small>('.$currencyCode.')</small></th>
					</tr></thead><tbody>';
		
		$i=1;$itemHeight=40;$totalQty=0;$totalAmt=0;
		$tempData = $quoteData->trans;
		
		if(!empty($tempData)){
			foreach ($tempData as $row)
			{
				$amount =round(($row->price * $row->qty),2);
				$unitData = $this->leads->getItemUnit($row->unit_id);
				$itemList.='<tr>';
					$itemList.='<td class="text-center bb" height="'.$itemHeight.'">'.$i.'</td>';
					$itemList.='<td class="text-left bb">'.$row->item_name.'</td>';
					$itemList.='<td class="text-center bb">'.$unitData->unit_name.'</td>';
					$itemList.='<td class="text-center bb">'.sprintf('%0.2f', $row->qty).'</td>';
					$itemList.='<td class="text-center bb">'.$symbol.' '.sprintf('%0.2f', $row->price).'</td>';
					$itemList.='<td class="text-right bb">'.sprintf('%0.2f', $amount).'</td>';
				$itemList.='</tr>';$i++;$totalQty+=$row->qty;$totalAmt+=$amount;
			}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" height="30" style="font-size:14px;"><b>Total</b></td>';
			$itemList.='<td class="text-right" height="30" style="font-size:14px;"><b>'.sprintf('%0.2f', $totalQty).'</b></td>';
			$itemList.='<td class="text-right" height="30" style="font-size:14px;"></td>';
			$itemList.='<th class="text-right" style="border-top:1px solid #000000;font-size:14px;">'.sprintf('%0.2f', $totalAmt).'</th>';
		$itemList.='</tr>';
		$itemList.='</table>';
		$itemList.='</tbody></table><br>';
		
		$itemList.='<div class="fs-17"><b>Amount In Words : '.$currencyCode.' '.numToWordEnglish($totalAmt).'</b></div><br>';
		$itemList.='<div class="fs-17 bb"><b>Terms & Conditions :- </b></div>';
		$itemList.='<div style="padding-left:20px;">';
		$itemList.='<table>';
		if(!empty($quoteData->terms)):
			$terms_and_condition  = json_decode($quoteData->terms);
			foreach($terms_and_condition as $terms):
				$itemList.='<tr><td><b>'.$terms->title.'</b></td><td>: '.$terms->condition.'</td></tr>';
			endforeach;
		endif;
		$itemList.='</table></div>';
		
		
		if(empty($itemList)){$itemList='<tr><th colspan="5" class="text-center">No Item Available...!</th></tr>';}
				
		$baseDetail='<div class="table-wrapper">
					<table class="table txInvHead">
						<tr class="txRow">
							<td class="text-left pad-left-10" style="width:215px;"></td>
							<td class="fs-20 text-center" style="letter-spacing: 5px;font-weight:bold;">QUOTATION</td>
							<td class="text-right pad-right-10" style="width:215px;"></td>
						</tr>
					</table>
					</div>
					<table class="table">
						<tr>
							<td style="width:50%;"><b>M/S. '.$custData->party_name.'</b></td>
							<td style="width:30%;" class="text-right"><b>Quote No. : </b></td>
							<td>'.$quoteData->quote_prefix.$quoteData->quote_no.'</td>
						</tr>
						<tr><td height="5" colspan="3"></td></tr>
						<tr>
							<td><small>'.$custData->party_address.'</small></td>
							<td class="text-right"><b>Date : </b></td>
							<td>'.date('d/m/Y', strtotime($quoteData->quotation_date)).'</td>
						</tr>
						<tr><td height="5" colspan="3"></td></tr>
						<tr>
							<td><b>Referance By : '.$quoteData->ref_by.'</b></td>
							<td class="text-right"><b>Ref. Date : </b></td>
							<td>'.date('d/m/Y', strtotime($quoteData->quotation_date)).'</td>
						</tr>
						<tr>
							<td><b>Kind Attn. : '.$custData->contact_person.'</b></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td style="padding-left:12%;"><small>Contact : '.$custData->party_mobile.'<br>Email : '.$custData->contact_email.'</small></td>
							<td></td>
							<td></td>
						</tr>
					</table><br>';
		
		$bottomDiv='<table class="table" style="border-top:1px solid #000;margin:0px 40px;">
							<tr>
								<td style="width:50%;text-align:left;vertical-align:top;"><b>GSTIN : </b>'.$companyData->company_gst_no.'</td>
								<th colspan="2" style="vertical-align:top;text-align:center;font-size:1rem;padding:5px;">
									For, '.$companyData->company_name.'
								</th>
							</tr>
							<tr><td colspan="3"  height="60"></td></tr>
							<tr>
								<th style="width:50%;"></th>
								<td style="width:25%;vertical-align:top;text-align:center;font-size:1rem;padding:5px;">
									Prepared By<br>(Ashish Kiyada)
								</td>
								<td style="width:25%;vertical-align:top;text-align:center;font-size:1rem;padding:5px;">
									Approved By<br>(Rakesh Patel)
								</td>
							</tr>
						</table>';
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<img src="'.$headerImg.'">';
		$htmlFooter = $bottomDiv.'<img src="'.$footerImg.'">';
		
		$orsp=	'<div style="padding:10px 20px;"><div class="poDiv">'.$baseDetail.$itemList.'</div></div>';
		
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/quotation/quote_'.$id.'.pdf');
		$fpath='/assets/uploads/purchase/quote_'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo_path,0.05,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',0,0,40,0,5,0);
		$mpdf->WriteHTML($orsp);
		
		$mpdf->Output($pdfFileName,'I');
	}

}
?>