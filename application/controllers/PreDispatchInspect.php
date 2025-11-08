<?php
class PreDispatchInspect extends MY_Controller
{
    private $indexPage = "predispatch_inspect/index";
    private $formPage = "predispatch_inspect/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Final Inspection";
		$this->data['headData']->controller = "preDispatchInspect";
		$this->data['headData']->pageUrl = "preDispatchInspect";
	}

    public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->preDispatch->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPreDispatchInspectData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addFinalInspection(){
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['report_prefix'] = 'FI';
        $this->data['nextReportNo'] = $this->preDispatch->nextReportNo();
        $this->data['itemData'] = '';
        $this->data['soData'] = '';
        $this->data['jobData'] = '';
        $this->load->view($this->formPage,$this->data);
    }

    public function getItemsByParty(){
        $party_id = $this->input->post('party_id');
        $itemData = $this->item->getPartyItemList($party_id);
        $partyItems='<option value="">Select Product</option>';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        $this->printJson(['status'=>1,'partyItems'=>$partyItems]);
    }

	// Updated By Meghavi @10/06/2023
    public function getFinalInspection(){
        $data = $this->input->post(); 
        $paramData = $this->item->getPreDispatchInspParam($data['item_id']);

        $tbodyData="";$i=1; 
        if(!empty($paramData)):
            foreach($paramData as $row):
                $tbodyData.= '<tr style="text-align:center;">
                                <td>'.$i++.'</td>
                                <td>'.$row->drg_diameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->min_value.'</td>
                                <td>'.$row->max_value.'</td>
                                <td>'.$row->inst_used.'</td>
                                <td><input type="text" name="sample1_'.$row->id.'" class="form-control" value=""></td>
                                <td><input type="text" name="sample2_'.$row->id.'" class="form-control" value=""></td>
                                <td><input type="text" name="sample3_'.$row->id.'" class="form-control" value=""></td>
                                <td><input type="text" name="sample4_'.$row->id.'" class="form-control" value=""></td>
                                <td><input type="text" name="sample5_'.$row->id.'" class="form-control" value=""></td>
                                <td><input type="text" name="remark_'.$row->id.'" class="form-control" value=""></td>
                            </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
        endif;

        $soData = $this->preDispatch->getSalesOrderList($data['item_id']);
        $soOptions = '<option value="">Select P.O. No.</option>';
        if(!empty($soData)): 
            foreach($soData as $row):
                if(!empty($row->doc_no))
                {
                    $soOptions .= "<option data-row='".json_encode($row)."' value='".$row->id."' data-party_id='".$row->party_id."'>".$row->doc_no."</option>";                    
                }else
                {      
                    $soOptions .= "<option data-row='".json_encode($row)."' value='".$row->id."' data-party_id='".$row->party_id."'>".$row->trans_prefix.$row->trans_no."</option>";              
                }
            endforeach;
        else:
            $soOptions .= '';
        endif;

        $jobData = $this->preDispatch->getJobCardNo($data['item_id']);             
		$jobNo = '<option value="">Select Batch No</option>';
		if(!empty($jobData)): 
            foreach($jobData as $row):
                $jobNo .= "<option data-batch_no='".$row->job_number."' value='".$row->id."'>".$row->job_number."</option>";  
            endforeach;
		endif;

        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,'soOptions'=>$soOptions,"jobNo"=>$jobNo]);
    }
    
	public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

        if(empty($data['report_no']))
            $errorMessage['report_no'] = "Report No. is required.";
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if(empty($data['dispatch_qty']))
            $errorMessage['dispatch_qty'] = "Inspection Qty. is required.";
        if(empty($data['date']))
            $errorMessage['date'] = "Date is required.";

        $insParamData = $this->item->getPreDispatchInspParam($data['item_id']);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= 5; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['remark_'.$row->id];
                $pre_inspection[$row->id] = $param;
                unset($data['remark_'.$row->id]);
            endforeach;
        endif;

        $data['observe_samples'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['report_number'] = $data['report_prefix'].$data['report_no'];
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->preDispatch->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->preDispatch->getPreInspection($id);
        $this->data['paramData'] = $this->item->getPreDispatchInspParam($dataRow->item_id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['report_prefix'] = 'FI';
        $this->data['nextReportNo'] = $this->preDispatch->nextReportNo();

        $itemData = $this->item->getPartyItemList($dataRow->party_id);
        $partyItems='<option value="">Select Product</option>';
        if(!empty($itemData)):
			foreach ($itemData as $row):
                $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'';
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."' ".$selected.">[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        $this->data['itemData'] = $partyItems;

        $soData = $this->preDispatch->getSalesOrderList($dataRow->item_id);
        $soOptions = '<option value="">Select P.O. No.</option>';
        if(!empty($soData)): 
            foreach($soData as $row):
                $selected = (!empty($dataRow->po_no) && $dataRow->po_no == $row->id)?'selected':'';
                if(!empty($row->doc_no))
                {
                    $soOptions .= "<option data-row='".json_encode($row)."' value='".$row->id."' data-party_id='".$row->party_id."' ".$selected.">".$row->doc_no."</option>";                    
                }else
                {      
                    $soOptions .= "<option data-row='".json_encode($row)."' value='".$row->id."' data-party_id='".$row->party_id."' ".$selected.">".$row->trans_prefix.$row->trans_no."</option>";              
                }
            endforeach;
        endif;
        $this->data['soData'] = $soOptions;

        $jobData = $this->preDispatch->getJobCardNo($dataRow->item_id);             
		$jobNo = '<option value="">Select Batch No</option>';
		if(!empty($jobData)): 
            foreach($jobData as $row):
                $selected = (!empty($dataRow->job_id) && $dataRow->job_id == $row->id)?'selected':'';
                $jobNo .= "<option data-batch_no='".$row->job_number."' value='".$row->id."' ".$selected.">".$row->job_number."</option>";  
            endforeach;
		endif;
        $this->data['jobData'] = $jobNo;

        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->preDispatch->delete($id));
        endif;
    }

	public function printFinalInspection($id){
        $this->data['dataRow'] = $dataRow = $this->preDispatch->getPreInspectionForPrint($id);
        $this->data['paramData'] = $this->item->getPreDispatchInspParam($dataRow->item_id);
		$this->data['companyData'] = $companyData = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('predispatch_inspect/printFinalInspection',$this->data,true);
		
		$dataRow = $this->data['dataRow'];
		
		$htmlHeader = '<table class="table top-table-border">
                        <tr>
                            <th style="width:80%;font-size:25px;">AKSHAR ENGINEERS</th>
                            <td>
                                <img src="'.$logo.'">
                            </td>                                   
                        </tr>
                        <tr>
                            <th style="width:80%;font-size:20px;">FINAL CUM PREDISPATCH INSPECTION REPORT</th>
                            <th>F/QC/02 (00/01.01.16)</th>                                   
                        </tr>
                    </table>';
		$htmlFooter = '<table class="table top-table-border" style="margin-top:10px;">
                        <tr>
							<td style="width:25%;" class="text-right"><b>Qty. Received:</b></td>
							<td style="width:25%;" class="text-right">'.$dataRow->dispatch_qty.'</td>
							<td style="width:50%;vertical-align:top;" rowspan="3"><b>Reason: </b>'.$dataRow->reason.'</td>
						</tr>
                        <tr>
							<td class="text-right"><b>Qty. Accepted:</b></td>
							<td class="text-right">'.$dataRow->accepted_qty.'</td>
						</tr>
                        <tr>
							<td class="text-right"><b>Qty. Rejected:</b></td>
							<td class="text-right">'.$dataRow->rejected_qty.'</td>
						</tr>
						<tr>
							<td colspan="2" rowspan="2" class="text-left">
                                <b>Inspected By:</b>
                                <br><br>'.$dataRow->prepare_by.'
                            </td>
							<td rowspan="2" class="text-left">
                                <b>Approved By:</b>
                                <br><br>'.$dataRow->authorize_by.'
                            </td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,50,25,5,75,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}

    public function preInspection_pdf($id){
        $this->data['inInspectData'] = $dataRow = $this->preDispatch->getPreInspectionForPrint($id);
        $this->data['paramData'] = $this->item->getPreDispatchInspParam($dataRow->item_id);
		$this->data['companyData'] = $companyData = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('predispatch_inspect/printPreInspection',$this->data,true);
		
		$inInspectData = $this->data['inInspectData'];
		
		$htmlHeader = '<table class="table" style="background-color:black;">
                <tr>
                    <td style="width:60%;">
                        <img src="'.$logo.'" style="width:250px;">
                    </td>
                    <td class="fs-14 text-white" style="width:40%;"><b>'.$companyData->company_address.'</td>
                            
                </tr>
            </table>';
		$htmlFooter = '<table class="table table-top" style="margin-top:10px;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">'.$dataRow->prepare_by.'</td>
							<td style="width:25%;" class="text-center">'.$dataRow->authorize_by.'</td>
						</tr>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Approved By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		//print_r($pdfData); exit;
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = false;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,35,25,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
}
?>