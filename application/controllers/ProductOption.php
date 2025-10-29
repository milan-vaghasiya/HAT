<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ProductOption extends MY_Controller
{
    private $indexPage = "product_options/index";
    private $cycletimeForm = "product_options/ct_form";
    private $consumptionForm = "product_options/tool_form";
    private $viewProductProcess = "product_options/view_product_process";
    private $productKitItem = "product_options/product_kit";
    private $inspectionForm = "product_options/inspection_form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ProductOption";
		$this->data['headData']->controller = "productOption";
		$this->data['headData']->pageUrl = "productOption";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($is_child='No'){        
        $data = $this->input->post(); $data['is_child'] = $is_child;
        $result = $this->item->getProdOptDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$optionStatus = $this->item->checkProductOptionStatus($row->id);
			
			$lastUpdate = $this->item->getlastUpdatedProcess($row->id); $updated_at = 'Update Date:'; $updated_by = 'Update By:  ';
			if(!empty($lastUpdate)){
			    $updated_at = (!empty($lastUpdate->updated_at)) ? ', Update Date: '.formatDate($lastUpdate->updated_at) : ', Update Date:' ;
			    $updated_by = (!empty($lastUpdate->emp_name)) ? 'Update By: '.$lastUpdate->emp_name : 'Update By:  ' ;
			}
			
			$process =  '<span datatip="'.$updated_by.$updated_at.'" flow="down">'.$optionStatus->finishedWeight.'/'.$optionStatus->process.'</span>';
			$cycle_time =  '<span datatip="'.$updated_by.$updated_at.'" flow="down">'.$optionStatus->cycleTime .'/'.$optionStatus->process.'</span>';
			
            if($is_child == 'Yes'){ $row->item_code = '['.$row->item_code.'] '.$row->item_name; }
			$row->bom = (!empty($optionStatus->bom)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->process = (!empty($optionStatus->process)) ?  $process : '';
			$row->cycleTime = (!empty($optionStatus->cycleTime) or (!empty($optionStatus->process)) ) ? $cycle_time : '';
			$row->tool = (!empty($optionStatus->tool)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->inspection = (!empty($optionStatus->inspection)) ? '<i class="fa fa-check text-primary"></i>' : '';
			$row->incoming_inspection = (!empty($optionStatus->incoming_inspection)) ? '<i class="fa fa-check text-primary"></i>' : '';
            $sendData[] = getProductOptionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCycleTime(){
        $id = $this->input->post('id'); 
        $this->data['processData'] = $this->item->getItemProcess($id);   
        $this->load->view($this->cycletimeForm,$this->data);
    }

    public function saveCT(){
        $data = $this->input->post();
        $errorMessage = array();

        $data['loginId'] = $this->session->userdata('loginId');
        $cycleTimeData = [ 'id' => $data['id'], 'cycle_time' => $data['cycle_time'], 'loginId' => $data['loginId']];

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveProductProcessCycleTime($cycleTimeData));
        endif;
    }

   /* Updated By :- Sweta @08-08-2023 */
   public function addToolConsumption(){
        $id = $this->input->post('id'); 
        $this->data['item_id'] = $id;
        $this->data['unitData'] = $this->item->itemUnits(1);
        $this->data['processList'] = $this->item->getItemProcess($id);
        $this->data['toolConsumptionData'] = $this->toolConsumptionHtml($id);
        $this->data['subGroupList'] = $this->masterDetail->getMasterDetailsByCategory(['type'=>4]);
        $this->load->view($this->consumptionForm,$this->data);
    }

    /* Updated By :- Sweta @08-08-2023 */
    public function saveToolConsumption(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if(empty($data['process_id']))
            $errorMessage['process_id'] = "Process is required.";     
        if(empty($data['ref_item_id']))
            $errorMessage['ref_item_id'] = "Item is required.";     
        if(empty($data['req_qty']))
            $errorMessage['req_qty'] = "Req. Qty is required.";
        if(empty($data['tool_unit']))
            $errorMessage['tool_unit'] = "Tool Unit is required.";
        if($data['sub_group'] == '')
            $errorMessage['sub_group'] = "Material Type is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['created_by'] = $this->session->userdata('loginId');
            else:
                $data['updated_by'] = $this->session->userdata('loginId');
            endif;
            $this->item->saveToolConsumption($data);
            $this->printJson($this->toolConsumptionHtml($data['item_id']));
        endif;
    } 

    public function addProductProcess(){
        $id = $this->input->post('id');        
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->productProcessForm,$this->data);
    }

    public function saveProductProcess(){
        $data = $this->input->post();
        $errorMessage = "";

        if(empty($data['item_id']))
            $errorMessage .= "Somthing went wrong.";
        /* if(empty($data['process'][0]))
            $errorMessage .= " Pelase select product process."; */

        if(!empty($errorMessage)):
            $this->printJson(['status'=>2,'message'=>$errorMessage]);
        else:
            //$data['created_by'] = $this->session->userdata('loginId');
            $response = $this->item->saveProductProcess($data);
            $this->printJson($this->setProcessView($data['item_id']));
        endif;
    }

    public function setProcessView($id)
    {
        $processData = $this->item->getItemProcess($id);
        $operationData = $this->operation->getOperationList();
        $processHtml = '';
        if (!empty($processData)) :
            $i = 1; $html = ""; $options=Array(); $opt='';
            foreach ($processData as $row) :
                $opt='';
                $ops = $this->item->getProductOperationForSelect($row->id);
                foreach($operationData as $operation):
                    $selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
                     $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
                endforeach;
                $options[$row->id] = $opt;
            endforeach;

            foreach ($processData as $row) :
                $processHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        <td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="operation_id'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                $options[$row->id]
                            .'</select>
                            <input type="hidden" name="operation_id" id="operation_id'.$row->id.'" data-id="'.$row->id.'" value="'.$row->operation.'" /></td>
                      </tr>';
            endforeach;
        else :
            $processHtml .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "processHtml" => $processHtml];
    }

    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 

		$this->data['productOperation']="";$options=Array();$opt='';
		foreach ($this->data['processData'] as $row) :
			$opt='';
			$ops = $this->item->getProductOperationForSelect($row->id);
			foreach($this->data['operationDataList'] as $operation):
				$selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
				 $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;
        $this->data['item_id'] = $id;   
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function updateProductProcessSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->item->updateProductProcessSequance($data));			
		endif;
    }

    public function addProductKitItems(){
        $id = $this->input->post('id');
        $this->data['productKitData'] = $this->item->getProductKitData($id);
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->data['process'] = $this->item->getProductWiseProcessList($id);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function saveProductKit(){ 
        $data = $this->input->post();
		$errorMessage = array();
		
		if(empty($data['item_id'])){
			$errorMessage['kit_item_id'] = "Item is required.";
		}
		if(empty($data['kit_item_id'])){
			$errorMessage['kit_item_id'] = "Item is required.";
		}
		if(empty($data['kit_item_qty'])){
			$errorMessage['kit_item_qty'] = "Qty. is required.";
		}
		
		if($this->item->checkDuplicateKitItem($data['item_id'],$data['kit_item_id'],$data['id']) > 0){
			$errorMessage['kit_item_id'] =  "Item is duplicate.";
		}
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->item->saveProductKit($data);
			$this->printJson($this->productKitHtml($data['item_id']));
		endif;
    }
	
	public function deleteProductKit(){ 
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deleteProductKit($data['id']);
			$this->printJson($this->productKitHtml($data['item_id']));
		endif;
    }
	
	public function productKitHtml($item_id){
        $productKitData = $this->item->getProductKitData($item_id);
		$i=1; $tbody='';
		if(!empty($productKitData)):
			
			foreach($productKitData as $row):
				$tbody.= '<tr>
						<td>'.$i++.'</td>
						<td>
							'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'
						</td>
						<td>
							'.$row->qty.'
						</td>
						<td>
							'.(($row->used_as == 1)?'Main':'Alternate').'
						</td>
						<td class="text-center">
							<button type="button" onclick="deleteProductKit('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
						</td>
					</tr>';
			endforeach;
		endif;
		return ['status'=>1,'tbody'=>$tbody];
	}
	
    public function saveProductOperation(){
        $data = $this->input->post();
        $this->printJson($this->item->saveProductOperation($data));
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}

    /* Pre Inspection */
    public function getPreInspection(){
        $data=$this->input->post();
		$this->data['param'] = explode(',',$this->grnModel->getMasterOptions()->ins_param); 
		$this->data['instruments'] = explode(',',$this->grnModel->getMasterOptions()->ins_instruments); 
        $this->data['paramData']=$this->item->getPreInspectionParam($data['item_id'], $data['param_type']);
        $this->data['item_id']=$data['item_id'];
        $this->data['param_type']=$data['param_type'];
        $this->load->view($this->inspectionForm,$this->data);
    }
     
    public function savePreInspectionParam(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['parameter']))
            $errorMessage['parameter'] = "Parameter is required.";
        if(empty($data['specification']))
			$errorMessage['specification'] = "Specification is required.";
        if(empty($data['lower_limit']))
			$errorMessage['lower_limit'] = "Tolerance is required.";
        if(empty($data['upper_limit']))
			$errorMessage['upper_limit'] = "Psc/Sp. Char. is required.";
        if(empty($data['measure_tech']))
			$errorMessage['measure_tech'] = "Instrument Used is required.";
            
        /* if($this->item->checkDuplicateParam($data['parameter'],$data['id']) > 0)
            $errorMessage['parameter'] =  "Perameter is duplicate."; */

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->item->savePreInspectionParam($data);
            $paramData = $this->item->getPreInspectionParam($data['item_id'], $data['param_type']);
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->lower_limit.'</td>
                                <td>'.$row->upper_limit.'</td>
                                <td>'.$row->measure_tech.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.','.$row->param_type.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }

    public function deletePreInspection(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deletePreInspection($data['id']);
            $paramData = $this->item->getPreInspectionParam($data['item_id'], $data['param_type']);
            $tbodyData="";$i=1; 
            if(!empty($paramData)):
                $i=1;
                foreach($paramData as $row):
                    $tbodyData.= '<tr>
                                <td>'.$i++.'</td>
                                <td>'.$row->parameter.'</td>
                                <td>'.$row->specification.'</td>
                                <td>'.$row->lower_limit.'</td>
                                <td>'.$row->upper_limit.'</td>
                                <td>'.$row->measure_tech.'</td>
                                <td class="text-center">
                                    <button type="button" onclick="trashPreInspection('.$row->id.','.$row->item_id.','.$row->param_type.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                </td>
                            </tr>';
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1,"tbodyData"=>$tbodyData]);
        endif;
    }
    
    public function saveProductFinishedWeight(){
        $data = $this->input->post();
        $data['loginId'] = $this->session->userdata('loginId');
        $this->printJson($this->item->saveProductFinishedWeight($data));
    }
    
    /*Tool Consumption Print Data */
    public function printToolConsumption($id){

        $toolConsumptionData = $this->item->getToolConsumption($id);
        
        $logo=base_url('assets/images/logo.png');
		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">Tool Consumption Data</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;">F ST 12<br>(00/01.08.2021)</td>
						</tr>
					</table>';
        $itemList='<table id="toolConsumption" class="table table-bordered align-items-center itemList">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Department</th> 
                                <th>Operation</th>
                                <th>Item Name</th>
                                <th>Setup</th>
                                <th>Tool Life Of Corner</th>
                                <th>Number Of Corner</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="kitItems">';
        if(!empty($toolConsumptionData)):
            $i=1;
            foreach($toolConsumptionData as $row):
                $itemList.= '<tr>
                            <td>'.$i++.'</td>
                            <td>
                                '.$row->dept_name.'
                                <input type="hidden" name="dept_id[]" value="'.$row->dept_id.'">
                            </td>
                            <td>
                                '.$row->ops_name.'
                                <input type="hidden" name="operation_id[]" value="'.$row->operation.'">
                            </td>
                            <td>
                                '.$row->item_name.'
                                <input type="hidden" name="ref_item_id[]" value="'.$row->ref_item_id.'">
                                <input type="hidden" name="id[]" value="'.$row->id.'">
                            </td>
                            <td>
                                '.$row->process_name.'
                                <input type="hidden" name="setup[]" value="'.$row->setup.'">
                            </td>
                            <td>
                                '.$row->tool_life.'
                                <input type="hidden" name="tool_life[]" value="'.$row->tool_life.'">
                            </td>
                            <td>
                                '.$row->number_corner.'
                                <input type="hidden" name="number_corner[]" value="'.$row->number_corner.'">
                            </td>
                            <td>
                                '.$row->price.'
                                <input type="hidden" name="price[]" value="'.$row->price.'">
                            </td>
                        
                        </tr>';
            endforeach;
        endif;
        $itemList.='</tbody></table>';

	    $originalCopy = '<div style="">'.$topSectionO.$itemList.'</div>';
		
		$pdfData = $originalCopy;
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
    
    //Created By Mansee
    public function createInspectionExcel($item_id,$param_type) {
        $paramData=$this->item->getPreInspectionParam($item_id,$param_type);
		$table_column = Array('id','parameter','specification','lower_limit','upper_limit','measure_tech');
		$spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('Inspection');
		$xlCol = 'A';$rows=1;
		foreach ($table_column as $tCols){$inspSheet->setCellValue($xlCol.$rows, $tCols);$xlCol++;}
        $rows = 2;
        foreach ($paramData as $row):
            $inspSheet->setCellValue('A' . $rows, $row->id);
            $inspSheet->setCellValue('B' . $rows, $row->parameter);
            $inspSheet->setCellValue('C' . $rows, $row->specification);
            $inspSheet->setCellValue('D' . $rows, $row->lower_limit);
            $inspSheet->setCellValue('E' . $rows, $row->upper_limit);
            $inspSheet->setCellValue('F' . $rows, $row->measure_tech);
            $rows++;
        endforeach;

		$fileDirectory = realpath(APPPATH . '../assets/uploads/inspection');
		$fileName = '/product_inspection_'.time().'.xlsx';
        $writer = new Xlsx($spreadsheet);
		$writer->save($fileDirectory.$fileName);
		header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/inspection').$fileName);              
    }    
    
    //Created By Mansee
	public function importExcel(){
		$postData = $this->input->post(); //print_r($postData);exit;
		$insp_excel = '';
		if(isset($_FILES['insp_excel']['name']) || !empty($_FILES['insp_excel']['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['insp_excel']['name'];
			$_FILES['userfile']['type']     = $_FILES['insp_excel']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['insp_excel']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['insp_excel']['error'];
			$_FILES['userfile']['size']     = $_FILES['insp_excel']['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/inspection');
			$config = ['file_name' => "inspection_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['insp_excel'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$insp_excel = $uploadData['file_name'];
			endif;
			if(!empty($insp_excel))
			{
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$insp_excel);
				$fileData = array($spreadsheet->getSheetByName('Inspection')->toArray(null,true,true,true));
				$fieldArray = Array();
				
				if(!empty($fileData))
				{
					$fieldArray = $fileData[0][1];$row = 0;
					for($i=2;$i<=count($fileData[0]);$i++)
					{
						$rowData = Array();$c='A';
						foreach($fileData[0][$i] as $key=>$colData):
							$rowData[strtolower($fieldArray[$c++])] = $colData;
						endforeach;
                        $rowData['item_id']=$postData['item_id'];
                        $rowData['param_type']=$postData['param_type'];
                        $rowData['item_type']=1;
						$this->item->savePreInspectionParam($rowData);
                        $row++;
					}
					
				}
                $paramData = $this->item->getPreInspectionParam($postData['item_id']);
                $tbodyData="";$i=1; 
                if(!empty($paramData)):
                    $i=1;
                    foreach($paramData as $param):
                        $tbodyData.= '<tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$param->parameter.'</td>
                                    <td>'.$param->specification.'</td>
                                    <td>'.$param->lower_limit.'</td>
                                    <td>'.$param->upper_limit.'</td>
                                    <td>'.$param->measure_tech.'</td>
                                    <td class="text-center">
                                        <button type="button" onclick="trashPreInspection('.$param->id.','.$param->item_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                    </td>
                                </tr>';
                    endforeach;
                else:
                    $tbodyData.= '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                endif;
				$this->printJson(['status'=>1,'message'=>$row.' Record updated successfully.','tbodyData'=>$tbodyData]);
			}
			else{$this->printJson(['status'=>0,'message'=>'Data not found...!']);}
		else:
			$this->printJson(['status'=>0,'message'=>'Please Select File!']);
		endif;
    }
       
    //Changed  By Karmi @13/12/2021
    /*Material Bom Print Data */
    public function printMaterialBom($id){
        $productKitData = $this->item->getMaterialBomPrintData($id);
        $processData = $this->item->getProductProcesswithCycleTime($id); 
        $logo=base_url('assets/images/logo.png');		
		$topSectionO ='<table class="table">
						<tr>
							<td style="width:20%;"><img src="'.$logo.'" style="height:40px;"></td>
							<td class="org_title text-center" style="font-size:1rem;width:60%">['.$productKitData[0]->product_code.'] '.$productKitData[0]->product_name.'</td>
							<td style="width:20%;" class="text-right"><span style="font-size:0.8rem;"></td>
						</tr>
                        <tr>
                            <th colspan=3> Bill Of Material</th>
                        </tr>
					</table>';
        $itemList='<table id="materialBom" class="table table-bordered align-items-center itemList">
                        <thead class="thead-info">

                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item Name</th> 
                                <th>Qty</th>
                                
                            </tr>
                        </thead>
                        <tbody id="kitItems">';
        if(!empty($productKitData)):
            $i=1;
            foreach($productKitData as $row):
                $itemList.= '<tr>                            
                            <td>'.$i++.'</td>
                            <td class="text-left">
                                '.$row->item_name.'                                
                            </td>
                            <td class="text-center">
                                '.$row->qty.' '.$row->unit_name .'                               
                            </td>                  
                        </tr>';
            endforeach;
        endif;
       
        $itemList.='</tbody></table>';

        $topSectionP = '<table class="table" style=" margin-top:50px;">
        <tr> <th colspan=3> Product Process Flow</th>
        </tr></table>';

        $processList='<table id="materialBom" class="table table-bordered align-items-center itemList">
                        <thead class="thead-info">

                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Process Name</th> 
                                <th>Cycle Time</th> 
                                <th>Finished Weight</th>                                
                            </tr>
                        </thead>
                        <tbody id="kitItems">';
        if(!empty($processData)):
            $i=1;
            foreach($processData as $row):
                $processList.= '<tr>                            
                            <td class="text-center">'.$i++.'</td>
                            <td class="text-left">'.$row->process_name.'</td>                
                            <td class="text-center">'.$row->cycle_time.'</td>                
                            <td class="text-center">'.$row->finished_weight.'</td>                
                        </tr>';
            endforeach;
        endif;
       
        $processList.='</tbody></table>';

        
	    $originalCopy = '<div style="">'.$topSectionO.$itemList.$topSectionP.$processList.'</div>';		
		$pdfData = $originalCopy;		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='JWO-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetProtection(array('print'));
		$mpdf->AddPage('P','','','','',5,5,5,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }

    public function finishedWeightEffectOnRuningJobs(){
        $product_id = $this->input->post('product_id');
        $this->printJson($this->jobcard_v2->finishedWeightEffectOnRuningJobs($product_id));
    }

    /* Created By :- Sweta @08-08-2023 */
    public function deleteToolConsumption(){
        $data=$this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->item->deleteToolConsumption($data['id']);
			$this->printJson($this->toolConsumptionHtml($data['item_id']));
		endif;
    }

    /* Created By :- Sweta @08-08-2023 */
    public function toolConsumptionHtml($item_id){
        $toolData = $this->item->getToolConsumption($item_id);
        $i=1; $tbody='';
		if(!empty($toolData)):
			
			foreach($toolData as $row):
				$item_name=''; $material_type='';
				if(!empty($row->sub_group)){
					$item_code = (!empty($row->item_code)? $row->item_code:'');
					$item_name = $row->item_name;
					$material_type = $row->material_type;
				}else{
					$item_code = (!empty($row->inst_code)? $row->inst_code:'');
					$item_name = $row->inst_name;
					$material_type = 'Instrument & Gauges';
				}
				$tbody.= '<tr>
						<td>'.$i++.'</td>
						<!--<td>'.$row->tool_no.'</td>-->
						<td>'.$row->process_name.'</td>
						<td>'.$item_code.'</td>
						<td>'.$item_name.'</td>
						<td>'.$material_type.'</td>
						<td>'.$row->tool_unit.'</td>
						<td>'.$row->req_qty.'</td>
						<td>'.(($row->used_for == 1)?'Per. Pcs':'Per. Job').'</td>
						<td>'.$row->remark.'</td>
						<td class="text-center">
							<button type="button" onclick="deleteToolConsumption('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
						</td>
					</tr>';
			endforeach;
		endif;
		return ['status'=>1,'tbody'=>$tbody];
    } 

    /* Created By :- Sweta @08-08-2023 */
    public function getGroupWiseItem(){
		$sub_group = $this->input->post('sub_group');
		$itemData = array();
		if(!empty($sub_group)):
			$itemData = $this->item->getGroupWiseItem($sub_group);
		else:
			$itemData = $this->qcInstrument->getGaugeInstList(); 
		endif;
		
		$option='<option value="">Select Item</option>';
		if(!empty($itemData)):
			foreach($itemData as $row):
				$option.= '<option value="'.$row->id.'">'.(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name.'</option>';
			endforeach;
		endif;
		$this->printJson(['status'=>1,'option'=>$option]);
	}
}
?>