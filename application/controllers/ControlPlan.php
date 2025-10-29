<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ControlPlan extends MY_Controller{	
    private $indexPage = "control_plan/index";
    private $inspectionForm = "control_plan/form";
    private $lineIndex = "control_plan/line_index";
    private $lineInspectionForm = "control_plan/line_inspection_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin(); 
		$this->data['headData']->pageTitle = "Control Plan";
		$this->data['headData']->controller = "controlPlan";
		$this->data['headData']->pageUrl = "controlPlan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$data=$this->input->post();
        $result = $this->item->getProdOptDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getControlPlanData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	/* Pre Inspection  Change By Meghavi @09/06/2023*/
    public function getInspectionParameter(){
        $data=$this->input->post();
        $this->data['param'] = explode(',',$this->grnModel->getMasterOptions()->ins_param); 
        $this->data['instruments'] = $this->item->getCategoryList('6,7');
        $this->data['processData'] = $this->item->getItemProcess($data['item_id']);
        $this->data['paramData']=$this->parameterHtml(['item_id'=>$data['item_id']]);;
        $this->data['itemData'] = $this->item->getItem($data['item_id']);
        $this->data['item_id']=$data['item_id'];
        $this->load->view($this->inspectionForm,$this->data);
    }
    
    //Change By Avruti @09/08/2022
    public function saveInspectionParameter(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['drg_diameter'])){
            $errorMessage['drg_diameter'] = "Diameter is required.";
        }   
        if(empty($data['specification'])){
            $errorMessage['specification'] = "Specification is required.";
        }
        if(empty($data['sequence'])){
            $errorMessage['sequence'] = "Tolerance is required.";
        }
        if($this->controlPlan->checkDuplicateParamSequence(['item_id'=>$data['item_id'],'rev_no'=>$data['rev_no'],'sequence'=>$data['sequence'],'id'=>$data['id']]) > 0){
            $errorMessage['sequence'] =  "Sequence is duplicate."; 
        }
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $result = $this->controlPlan->savePreInspectionParam($data);
            $result['tbodyData'] = $this->parameterHtml(['item_id'=>$data['item_id']]);
            $this->printJson($result);
        endif;
    }

    public function parameterHtml($postData){
        $paramData = $this->controlPlan->getPreInspectionParam($postData);
        $tbodyData="";$i=1; 
        if(!empty($paramData)):
            $i=1;
            foreach($paramData as $row):
               
                $tbodyData.= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->process_name.'</td>
                            <td>'.$row->drg_diameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->min_value.'</td>
                            <td>'.$row->max_value.'</td>
                            <td>'.$row->inst_used.'</td>
                            <td>'.((empty($row->is_line))?'No':'Yes').'</td>
                            <td>'.((empty($row->is_final))?'No':'Yes').'</td>
                            <td>'.$row->sequence.'</td>
                            <td>'.$row->rev_no.'</td>
                           
                            <td class="text-center">
                                <button type="button" onclick="editInspectionParameter('.$row->id.',this);" class="btn btn-outline-success waves-effect waves-light btn-delete permission-remove"><i class="fa fa-edit"></i></button>
                                <button type="button" onclick="deleteInspectionParameter('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                            </td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
        endif;

        return $tbodyData;
    }

    //Change By Avruti @09/08/2022
    public function deleteInspectionParameter(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->controlPlan->deleteInspectionParameter($data['id']);
            $result['tbodyData'] = $this->parameterHtml(['item_id'=>$data['item_id']]);
            $this->printJson($result);
        endif;
    }
     
	//Created By Mansee
    public function createInspectionExcel($item_id){
        $paramData = $this->controlPlan->getPreInspectionParam($item_id);
        $processData = $this->item->getItemProcess($item_id);
        $table_column = array('drg_diameter', 'specification', 'min_value','max_value','inst_used','is_line','is_final','sequence', 'rev_no');
        $instruments = explode(',', $this->grnModel->getMasterOptions()->ins_instruments);
        $spreadsheet = new Spreadsheet();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $i = 0;
        foreach ($processData as $row) :
            $html = "<tr>
                    <th>drg_diameter</th>
                    <th>specification</th>
                    <th>min_value</th>
                    <th>max_value</th>
                    <th>inst_used</th>
                    <th>is_line</th>
                    <th>is_final</th>
                    <th>sequence</th>
                    <th>rev_no</th>
                </tr>";
            $pdfData = '<table>' . $html . '</table>';

            $reader->setSheetIndex($i);

            if ($i == 0) :
                $spreadsheet = $reader->loadFromString($pdfData);
            else :
                $spreadsheet = $reader->loadFromString($pdfData, $spreadsheet);
            endif;

            $row->process_name = trim(preg_replace('/[^A-Za-z0-9]/', '', $row->process_name));
            $spreadsheet->getSheet($i)->setTitle($row->process_name);
            $excelSheet = $spreadsheet->getSheet($i);
            $hcol = $excelSheet->getHighestColumn();
            $hrow = $excelSheet->getHighestRow();
            $packFullRange = 'A1:' . $hcol . $hrow;
            foreach (range('A', $hcol) as $col) :
                $excelSheet->getColumnDimension($col)->setAutoSize(true);
            endforeach;
            $i++;
        endforeach;

        $fileDirectory = realpath(APPPATH . '../assets/uploads/inspection');
        $fileName = '/product_inspection_' . time() . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/inspection') . $fileName);
    }
	
	//Created By Mansee
    public function importExcel(){
        $postData = $this->input->post();
        $insp_excel = '';
        if (isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name'])) :
            $this->load->library('upload');
            $_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
            $_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
            $_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
            $_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];

            $imagePath = realpath(APPPATH . '../assets/uploads/inspection');
            $config = ['file_name' => "inspection_" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $imagePath];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload()) :
                $errorMessage['insp_excel'] = $this->upload->display_errors();
                $this->printJson(["status" => 0, "message" => $errorMessage]);
            else :
                $uploadData = $this->upload->data();
                $insp_excel = $uploadData['file_name'];
            endif;
            
            if (!empty($insp_excel)) {
                
                $processData = $this->item->getItemProcess($postData['item_id']);
                $row = 0;
                foreach ($processData as $prs) :
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath . '/' . $insp_excel);
                    $prs->process_name = trim(preg_replace('/[^A-Za-z0-9]/', '', $prs->process_name));
                    $fileData = array($spreadsheet->getSheetByName($prs->process_name)->toArray(null, true, true, true));
                    $fieldArray = array();
                    if (!empty($fileData)) {
                        $fieldArray = $fileData[0][1];
                        
                        for ($i = 2; $i <= count($fileData[0]); $i++) {
                            $rowData = array();
                            $c = 'A';
                            foreach ($fileData[0][$i] as $key => $colData) :
								if(!empty($colData)):
									$rowData[strtolower($fieldArray[$c++])] = $colData;
								endif;
                            endforeach;
                            if(!empty($rowData['drg_diameter'])):
                                $rowData['process_id']=$prs->process_id;
                                $rowData['item_id'] = $postData['item_id'];
                                $rowData['id']="";
                                $this->controlPlan->savePreInspectionParam($rowData);
                                $row++;
                            endif;
                        }
                    }
                endforeach;
				$result['tbodyData'] = $this->parameterHtml(['item_id'=>$postData['item_id']]);
                
                $this->printJson(['status' => 1, 'message' => $row . ' Record updated successfully.', 'tbodyData' => $result['tbodyData']]);
            } else {
                $this->printJson(['status' => 0, 'message' => 'Data not found...!']);
            }
        else :
            $this->printJson(['status' => 0, 'message' => 'Please Select File!']);
        endif;
    }
	
	public function lineInspection(){
		$this->data['headData']->pageUrl = "controlPlan/lineInspection";
        $this->data['tableHeader'] = getQualityDtHeader("lineInspection");
        $this->load->view($this->lineIndex,$this->data);
    }

    public function getLineDTRows($report_type = 1){
		$data=$this->input->post(); $data['report_type'] = $report_type;
        $result = $this->controlPlan->getLineDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getLineInspectData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
 
    public function addLineInspection(){
        $this->data['jobData'] = $this->jobcard->getJobcardList();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['vendorList'] = $this->party->getVendorList();
        $this->data['processData'] = '';
        $this->data['machineData'] = '';
        $this->load->view($this->lineInspectionForm,$this->data);
    }

    public function getProcessList(){ 
        $job_card_id = $this->input->post('job_card_id'); 
        $process = $this->input->post('process');  
        $processList = (!empty($process))?explode(",",$process):array();

		$options = '<option value="">Select Process</option>';
		foreach($processList as $key=>$value):
				$processData = $this->process->getProcess($value);
				$options .= '<option value="'.$processData->id.'">'.$processData->process_name.'</option>';
		endforeach;
        $this->printJson(['status'=>1, 'options'=>$options]);
	}

	// Updated By Meghavi @10/06/2023
    public function getLineInspection(){
        $data = $this->input->post(); 
        $paramData = $this->controlPlan->getLineInspectionData($data['product_id'],$data['process_id']);
        $tbodyData="";$i=1; $theadData='';
                $theadData .= '<tr class="thead-info" style="text-align:center;">
                            <th style="width:5%;">#</th>
                            <th>Parameter</th>
                            <th style="width:35%;">Specification</th>
                            <th>Min. Value</th>
                            <th>Max. Value</th>
                            <th>Instrument</th>
                            <th>Reading</th>
                        </tr>
                        <tr style="text-align:center;">';   
                $theadData .='</tr>';
        if(!empty($paramData)):
            foreach($paramData as $row):
                $tbodyData.= '<tr style="text-align:center;">
                            <td>'.$i++.'</td>
                            <td>'.$row->drg_diameter.'</td>
                            <td>'.$row->specification.'</td>
                            <td>'.$row->min_value.'</td>
                            <td>'.$row->max_value.'</td>
                            <td>'.$row->inst_used.'</td>
                            <td><input type="text" name="remark_'.$row->id.'" class="form-control" value=""></td>
                        </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="4" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,"theadData"=>$theadData]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = Array();

        if(empty($data['job_card_id']))
            $errorMessage['job_card_id'] = "Job Card is required.";
        if(empty($data['insp_date']))
            $errorMessage['insp_date'] = "Date is required.";

        if($data['report_type'] == 2 && empty($data['setting_time'])){
            $errorMessage['setting_time'] = "Setting Time is required.";
        }
        $insParamData =$this->controlPlan->getLineInspectionData($data['product_id'],$data['process_id']);
        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Inspection Parameter is required.";

        $pre_inspection = Array();
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                // for($j = 1; $j <= $data['sampling_qty']; $j++):
                //     $param[] = $data['sample'.$j.'_'.$row->id];
                //     unset($data['sample'.$j.'_'.$row->id]);
                // endfor;
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
            if($data['report_type'] == 2){
                $data['sampling_qty'] = 1;
            }
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->controlPlan->save($data));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $dataRow = $this->controlPlan->getLineInspection($id);
        $this->data['paramData'] = $this->controlPlan->getLineInspectionData($this->data['dataRow']->product_id,$this->data['dataRow']->process_id);
        $this->data['jobData'] = $this->jobcard->getJobcardList();
        $this->data['operatorList'] = $this->employee->getMachineOperatorList();
        $this->data['employeeList'] = $this->employee->getEmployeeList();
        $this->data['vendorList'] = $this->party->getVendorList();
        $jobData = $this->jobcard->getJobcard($dataRow->job_card_id);
        
        $processList = explode(',' , $jobData->process);
        $options = '<option value="">Select Process</option>';
        if(isset($processList)):
            foreach($processList as $key=>$value):
                $pdata = $this->process->getProcess($value);
                $selected = (!empty($dataRow->process_id) && $dataRow->process_id == $pdata->id)?"selected":"";
                $options .= '<option value="'.$pdata->id.'" '.$selected.'>'.$pdata->process_name.'</option>';               
            endforeach; 
        endif;
        $this->data['processData'] = $options;

        $machineData = $this->item->getProcessWiseMachine($dataRow->process_id);        
        $mcOptions = '<option value="" >Select Machine</option>'; 
        if(!empty($machineData))
        {
            foreach($machineData as $row):
                $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id)?"selected":"";
                $mcOptions .= '<option value="'.$row->id.'" '.$selected.'>[ '.$row->item_code.' ] '.$row->item_name.'</option>';
            endforeach;
        }
        $this->data['machineData'] = $mcOptions;

        $this->load->view($this->lineInspectionForm,$this->data);
       
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->controlPlan->delete($id));
        endif;
    }
    
    //@24/08/2022
    public function getMachineByProcess(){
        $process_id=$this->input->post('process_id');
        $machineData = $this->item->getProcessWiseMachine($process_id);
        
        $mcOptions = '<option value="">Select Machine</option>'; 
        if(!empty($machineData))
        {
            foreach($machineData as $row):
                $mcOptions .= '<option value="'.$row->id.'" >[ '.$row->item_code.' ] '.$row->item_name.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'mcOptions'=>$mcOptions]);
    }

	// Updated By Meghavi @10/06/2023
    function printLineInspection($id){
		$this->data['lineInspectData'] = $inspData = $this->controlPlan->getLineInspectionForPrint($id);
        $paramData = $this->controlPlan->getLineInspectionData($inspData->product_id,$inspData->process_id);
        $tbodyData="";$theadData="";$i=1;$blankRow='';
        if(!empty($paramData)):
            foreach($paramData as $row):
                $paramItems = '<tr>
                    <td style="text-align:center;" height="30">'.$i.'</td>
                    <td style="text-align:center;">'.$row->drg_diameter.'</td>
                    <td style="text-align:center;">'.$row->specification.'</td>
                    <td style="text-align:center;">'.$row->min_value.'</td>
                    <td style="text-align:center;">'.$row->max_value.'</td>
                    <td style="text-align:center;">'.$row->inst_used.'</td>';
                
                    $objData = $this->controlPlan->getLineInspectionForPrint('',$inspData->job_card_id,$inspData->process_id,$inspData->insp_date);
                    $rcount = count($objData);
                    foreach($objData as $read):
                        if($i==1){
                            $insp_date = (!empty($read->insp_time)?date("h:i A",strtotime($read->insp_time)):'');
                            $theadData .= '<td style="text-align:center;">'.$insp_date.'</td>';
                        }
                        $obj = New StdClass; 
                        $obj = json_decode($read->observe_samples);
                        if(!empty($obj->{$row->id})):
                            $paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[0].'</td>';
                        endif;
                    endforeach;
                    $paramItems .= '</tr>';
                $tbodyData .= $paramItems;
                $i++;
            endforeach;
            for($j=28; $i<=$j; $i++):
                $blankRow .= '<tr>
                                <td>&nbsp;</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>';
                                for($td=1; $td<=$rcount; $td++){
                                    $blankRow .= '<td></td>';
                                }
                $blankRow .= '</tr>';
            endfor;
            $tbodyData .= $blankRow;
        else:
            $tbodyData.= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
        endif;
        $this->data['rcount'] = $rcount;
        $this->data['theadData'] = $theadData;
        $this->data['tbodyData'] = $tbodyData;
		$bodyData = $this->load->view('control_plan/printLineInspection',$this->data,true);

		$companyData = $this->db->where('id',1)->get('company_info')->row();
		$response="";$logo=base_url('assets/images/logo.png');
		$htmlHeader ='<table class="table" style="border-bottom:1px solid #036aae;">
						<tr>
							<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
							<td class="org_title text-uppercase text-center" style="font-size:1.4rem;width:50%">'.(($inspData->report_type == 1)?'INPROCESS INSPECTION REPORT':'SETUP APPROVAL REPORT').'</td>
							<td style="width:25%;" class="text-right"><span style="letter-spacing:1px;"><td>
						</tr>
					</table>
					<table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
						<tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
					</table>';
	
		$htmlFooter = '<table class="table table-bordered" style="width:100%;">
                        <tr>
                            <td class="text-center" style="width:50%;border:0px;"></td>
                            <td class="text-center" style="width:50%;font-size:1rem;border:0px;"><b>For, '.$companyData->company_name.'</b></td>
                        </tr>
                        <tr><td colspan="2" height="60" style="border:0px;"></td></tr>
                        <tr>
                            <td class="text-center" style="vertical-align:bottom !important;font-size:1rem;border:0px;">Prepare By</td>
                            <td class="text-center" style="font-size:1rem;border:0px;">Authorised Signatory</td>
                        </tr>
                    </table>';
		
		$pdfData = '<div style="width:210mm;height:140mm;">'.$htmlHeader.$bodyData.'</div>';
		
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
	
    public function editInspectionParameter(){
        $id = $this->input->post('id');
        $paramData = $this->controlPlan->getPreInspectionParamDetail($id);
        $this->printJson($paramData);
    }
}