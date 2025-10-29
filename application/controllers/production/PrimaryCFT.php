<?php
class PrimaryCFT extends MY_Controller
{
    private $indexPage = "production/primary_cft/index";
    private $cft_ok_form = "production/primary_cft/cft_ok_form";
    private $cft_rej_form = "production/primary_cft/cft_rej_form";
    private $cft_rw_form = "production/primary_cft/cft_rw_form";
    private $cft_ud_form = "production/primary_cft/cft_ud_form";
    private $udIndexPage = "production/primary_cft/ud_index";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Primary CFT";
        $this->data['headData']->controller = "production/primaryCFT";
    }

    public function index()
    {
        $this->data['tableHeader'] = getProductionHeader("primaryCFT");
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($entry_type = 1, $operation_type = 1)
    {
        $data = $this->input->post();
        $data['entry_type'] = $entry_type;
        $data['operation_type'] = $operation_type;
        $result = $this->primaryCFT->getDTRows($data);
        $sendData = array(); 
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getPrimaryCFTData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function convertToOk()
    {
        $data = $this->input->post();
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 4;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form, $this->data);
    }

    public function saveCFTQty()
    {
        $data = $this->input->post(); //print_r($data);exit;
        $errorMessage = array();
        $i = 1;
        if (empty($data['qty'])) :
            $errorMessage['qty'] = "Qty is required.";
        else :
            $cftData = $this->primaryCFT->getRejMovementData($data['ref_id']);
            if ($data['qty'] > ($cftData->qty - $cftData->cft_qty)) {
                $errorMessage['qty'] = "Qty is Invalid.";
            }
        endif;
        if ($data['entry_type'] == 2 && $data['operation_type'] == 1  && empty($data['rej_type']))
            $errorMessage['rej_type'] = "Rejection Type is required.";

        if ($data['entry_type'] == 2 && ($data['operation_type'] == 1 || $data['operation_type'] == 2) && empty($data['rr_reason']))
            $errorMessage['rr_reason'] = "Reason is required.";

        if ($data['entry_type'] == 2 && ($data['operation_type'] == 1 || $data['operation_type'] == 2) && $data['rr_stage'] == '')
            $errorMessage['rr_stage'] = "Rej Stage is required.";

        if ($data['entry_type'] == 2 && ($data['operation_type'] == 1 || $data['operation_type'] == 2) && $data['rr_by'] == '')
            $errorMessage['rr_by'] = "Rejection By is required.";

        if ($data['entry_type'] == 2 && ($data['operation_type'] == 1 || $data['operation_type'] == 2  || $data['operation_type'] == 5) && empty($data['remark']))
            $errorMessage['remark'] = "Description is required.";

        if ($data['operation_type'] == 2 && empty($data['rw_process_id']))
            $errorMessage['rework_process'] = "Rework Process is required.";

        if ($data['operation_type'] == 5 && empty($data['rw_process_id']))
            $errorMessage['rw_process_id'] = "Special Marking is required.";

        if ($data['operation_type'] == 5 && empty($data['rr_stage']))
            $errorMessage['rr_stage'] = "Deviation Reason is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['entry_date'] = date("Y-m-d");
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->primaryCFT->saveCFTQty($data));
        endif;
    }

    public function convertToRej()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->primaryCFT->getRejMovementData($data['id']);
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 1;
        $this->data['rejectionComments'] = $this->comment->getCommentList();
        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_name="Raw Material" data-process_id="Raw Material">Raw Material</option>';
        $jobProcess = explode(",", $dataRow->process);
        if (!empty($dataRow->process_id)) {
            $in_process_key = array_keys($jobProcess, $dataRow->process_id)[0];
            foreach ($jobProcess as $key => $value) {
                if ($key <= $in_process_key) {
                    $processData = $this->process->getProcess($value);
                    if(!empty($this->controlPlanEnable)){
                        $prdProcessData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $dataRow->product_id, 'process_id' => $value]);
                        $pfcData = (!empty($prdProcessData->pfc_process)) ? $this->controlPlan->getPfcForProcess($prdProcessData->pfc_process) : array();
                        
                        if (!empty($pfcData)) {
                            foreach ($pfcData as $pfc) {
                                $stageHtml .= '<option value="' . $pfc->id . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">[' . $pfc->process_no . '] ' . $pfc->parameter . '</option>';
                            }
                        }
                    }else{
                        $stageHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                    }
                }
            }
        }
        $this->data['dataRow']->stage = $stageHtml;

        $this->load->view($this->cft_rej_form, $this->data);
    }

    public function getRRByOptions()
    {
        $data = $this->input->post(); //print_r($data);
        $rejOption = '<option value="" data-party_name="In House">Select </option>';
        if(!empty($data['rej_type']) && $data['rej_type'] == 2){
            $jobData = new stdClass();
            $jobData->id = $data['job_card_id'];
            $jobData->product_id = $data['part_id'];
            $rmData = $this->jobcard->getMaterialIssueData($jobData)['result'];
            if (!empty($rmData)) :
                foreach ($rmData as $row) :
                    $rejOption .= '<option value="' . (!empty($row->party_name) ? $row->party_id : 0) . '" data-party_name="' . (!empty($row->party_name) ? $row->party_name : 'In House') . '">' . (!empty($row->party_name) ? $row->party_name : 'In House') . '</option>';
                endforeach;
            endif;
        } else {
            $vendorData = $this->processMovement->getRejRWBy($data);
            if (!empty($vendorData)) :
                foreach ($vendorData as $row) :
                    $rejOption .= '<option value="' . (!empty($row->party_name) ? $row->vendor_id : 0) . '" data-party_name="' . (!empty($row->party_name) ? $row->party_name : 'In House') . '">' . (!empty($row->party_name) ? $row->party_name : 'In House') . '</option>';
                endforeach;
            endif;
        }

        $dimOptions = '<option value="">Select</option>';
        if(!empty($this->controlPlanEnable)){
            $fmeData = $this->controlPlan->getCPDimenstion(['pfc_id' => $data['pfc_id'], 'item_id' => $data['part_id'], 'rmd' => 1]);
            if (!empty($fmeData)) {
                foreach ($fmeData as $fme) {
                    if ($fme->parameter_type == 1) {
                        $range = '';
                        if ($fme->requirement == 1) {
                            $range = $fme->min_req . '/' . $fme->max_req;
                        }
                        if ($fme->requirement == 2) {
                            $range = $fme->min_req . ' ' . $fme->other_req;
                        }
                        if ($fme->requirement == 3) {
                            $range = $fme->max_req . ' ' . $fme->other_req;
                        }
                        if ($fme->requirement == 4) {
                            $range = $fme->other_req;
                        }
                        $dimOptions .= '<option value="' . $fme->id . '" >' . $fme->parameter . ' [' . $range . ']</option>';
                    }
                }
            }
        }
       
        
       
        $this->printJson(['status' => 1, 'rejOption' => $rejOption, 'dimOptions' => $dimOptions]);
    }

    public function convertToRw()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->primaryCFT->getRejMovementData($data['id']);
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 2;
        $this->data['reworkComment'] = $this->comment->getReworkCommentList();
        $stageHtml = '<option value="">Select Stage</option><option value="0" data-process_name="Raw Material" data-process_id="Raw Material">Raw Material</option>';
        $reworkProcessHtml = '<option value="">Select Rework Process</option>';
        $jobProcess = explode(",", $dataRow->process);
        if (!empty($dataRow->process_id)) {
            $in_process_key = array_keys($jobProcess, $dataRow->process_id)[0];
            foreach ($jobProcess as $key => $value) {
                $processData = $this->process->getProcess($value);
                if(!empty($this->controlPlanEnable)){
                    $prdProcessData = $this->item->getPrdProcessDataProductProcessWise(['item_id' => $dataRow->product_id, 'process_id' => $value]);
                    $pfcData = (!empty($prdProcessData->pfc_process)) ? $this->controlPlan->getPfcForProcess($prdProcessData->pfc_process) : array();
                    
                    if (!empty($pfcData)) {
                        foreach ($pfcData as $pfc) {
                            $stageHtml .= '<option value="' . $pfc->id . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">[' . $pfc->process_no . '] ' . $pfc->parameter . '</option>';
                        }
                    }
                }else{
                    $stageHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                }
                
                if ($key <= $in_process_key) {
                    $reworkProcessHtml .= '<option value="' . $value . '" data-process_name="' . $processData->process_name . '" data-process_id="' . $value . '">' . $processData->process_name . '</option>';
                }
            }
        }
        $this->data['dataRow']->stage = $stageHtml;
        $this->data['dataRow']->reworkProcess = $reworkProcessHtml;
        $this->load->view($this->cft_rw_form, $this->data);
    }

    public function convertToHold()
    {
        $data = $this->input->post();
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 3;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form, $this->data);
    }

    public function printTag($tag_type, $id)
    {
        $tagData = $this->primaryCFT->getTagData($id);
        $rejData = $this->processMovement->getRejCFTData(['job_trans_id' => $tagData->job_trans_id, 'entry_type' => 1]);
        $jobData = new stdClass();
        $jobData->id = $tagData->job_card_id;
        $jobData->product_id = $tagData->product_id;
        $reqMaterials = $this->jobcard->getMaterialIssueData($jobData)['resultData'];

        $vendorName = (!empty($tagData->party_name)) ? $tagData->party_name : $tagData->operator_name;
        $title = "";
        $mtitle = "";
        $revno = "";
        $qtyLabel = "";
        $reasonLabel = "";
        if ($tag_type == "REJ") :
            $mtitle = 'Rejection';
            if ($tagData->rej_type == 1) {
                $title = 'Machine Rejection';
            } else {
                $title = 'Raw Material Rejection';
            }
            $revno = 'R-QC-17 (01/01.10.22)';
            $qtyLabel = "Rej Qty";
            $reasonLabel = "Rej Reason";
        elseif ($tag_type == "REW") :
            $mtitle = 'Rework';
            $revno = 'R-QC-18 (01/01.10.22)';
            $qtyLabel = "RW Qty";
            $reasonLabel = "R/w Reason";
        elseif ($tag_type == "SUSP") :
            $mtitle = 'Suspected';
            $revno = 'R-QC-19 (01/01.10.22)';
            $qtyLabel = "Susp. Qty";
            $reasonLabel = "Susp Reason";
        endif;

        $range = '';
        if ($tagData->requirement == 1) {
            $range = $tagData->min_req . '/' . $tagData->max_req;
        }
        if ($tagData->requirement == 2) {
            $range = $tagData->min_req . ' ' . $tagData->other_req;
        }
        if ($tagData->requirement == 3) {
            $range = $tagData->max_req . ' ' . $tagData->other_req;
        }
        if ($tagData->requirement == 4) {
            $range = $tagData->other_req;
        }

        $logo = base_url('assets/images/logo.png');


        $topSection = '<table class="table">
            <tr>
                <td style="width:20%;"><img src="' . $logo . '" style="height:40px;"></td>
                <td class="org_title text-center" style="font-size:1rem;width:50%;">' . $mtitle . ' <br><small><span class="text-dark">' . $title . '</span></small></td>
                <td style="width:30%;" class="text-right"><span style="font-size:0.8rem;">' . $revno . '<br>' . date("d.m.Y H:i", strtotime($tagData->created_at)) . '</td>
            </tr>
        </table>';

        $itemList = '<table class="table table-bordered vendor_challan_table">
            <tr>
                <td style="width:20%;font-size:0.7rem;" class="text-center"><b>Sr No</b></td>
                <td style="font-size:0.7rem;width:20%" class="text-center"><b>Rej Date</b></td>
                <td style="font-size:0.7rem;width:20%" class="text-center"><b>Job No</b></td>
                <td style="font-size:0.7rem;width:20%" class="text-center"><b>Prod Qty</b></td>
                <td style="font-size:0.7rem;width:20%" class="text-center"><b>Rej Qty</b></td>
            </tr>
            <tr>
                <td style="font-size:0.7rem;" class="text-center">' . $tagData->tag_prefix . $tagData->tag_no . '</td>
                <td style="font-size:0.7rem;" class="text-center">' . formatDate($tagData->entry_date) . '</td>
				<td style="font-size:0.7rem;" class="text-center">' . $tagData->job_number . '</td>
				<td style="font-size:0.7rem;" class="text-center">' . ($tagData->ok_qty + $rejData->rej_qty + $rejData->rw_qty + $rejData->hold_qty) . '</td>
				<td style="font-size:0.7rem;" class="text-center">' . $tagData->qty . '</td>
            </tr>
          
			<tr class="bg-light">
                <td style="font-size:0.7rem;"><b>Part</b></td>
                <td colspan="4" style="font-size:0.7rem;">' . $tagData->full_name . '</td>
            </tr>
            <tr class="bg-light">
                <td style="font-size:0.7rem;"><b>Supplier</b></td>
                <td colspan="4" style="font-size:0.7rem;">' . $reqMaterials['supplier_name'] . '</td>
            </tr>
			          
			<tr>
                <td style="font-size:0.7rem;" colspan="2"><b>M/c : </b>[' . $tagData->machine_code . '] ' . $tagData->machine_name . '</td>
                <td style="font-size:0.7rem;" colspan="3"><b>Drg Dim : </b>' . $tagData->process_parameters . ' [' . $range . ']</td>
			</tr>
            <tr>
                <td style="font-size:0.7rem;" colspan="2"><b>Variance : </b>' . $tagData->remark . '</td>
                <td style="font-size:0.7rem;" colspan="3"><b>' . $reasonLabel . ' : </b>' . $tagData->reason . '</td>
            </tr>
            <tr>
                <td style="font-size:0.7rem;"><b>Vendor/Ope.</b></td>
                <td style="font-size:0.7rem;" colspan="4">' . $vendorName . '</td>
            </tr>
            <tr>            
				<td style="font-size:0.7rem;"><b>Stage</b></td>
				<td style="font-size:0.7rem;" colspan="4">' . $tagData->parameter . '</td>
			</tr>
            <tr>
                <td style="font-size:0.7rem;"><b>Insp. By</b></td>
                <td style="font-size:0.7rem;" colspan="4">' . $tagData->emp_name . '</td>
            </tr>
		</table>';
        $pdfData = '<div style="width:140mm;height:60mm;text-align:center;float:left;padding:0mm 0.7mm;">' . $topSection . $itemList . '</div>';

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 80]]);
        $pdfFileName = str_replace(" ", "_", str_replace("/", " ", $mtitle)) . '.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->AddPage('P', '', '', '', '', 0, 0, 2, 2, 2, 2);
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName, 'I');
    }

    public function convertToUD()
    {
        $data = $this->input->post();
        $this->data['entry_type'] = 2;
        $this->data['operation_type'] = 5;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ud_form, $this->data);
    }


    public function underDeviation()
    {
        $this->data['tableHeader'] = getProductionHeader("underDeviation");
        $this->load->view($this->udIndexPage, $this->data);
    }

    public function getUdDTRows($status = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->primaryCFT->getUdDTRows($data);
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getUdCFTData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function convertToUdOk()
    {
        $data = $this->input->post();
        $this->data['entry_type'] = 4;
        $this->data['operation_type'] = 4;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form, $this->data);
    }
    public function convertToUdRej()
    {
        $data = $this->input->post();
        $this->data['entry_type'] = 4;
        $this->data['operation_type'] = 1;
        $this->data['dataRow'] = $this->primaryCFT->getRejMovementData($data['id']);
        $this->load->view($this->cft_ok_form, $this->data);
    }
}
