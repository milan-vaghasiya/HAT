<?php
class Machines extends MY_Controller{
    private $indexPage = "machine/index";
    private $machineForm = "machine/form";
    private $activityForm = "machine/activity";
    private $reportForm = "machine/maintenance_report";
    
	private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machines";
		$this->data['headData']->controller = "machines";
		$this->data['headData']->pageUrl = "machines";
        $this->data['monthArr'] = ['Apr-'.$this->startYear => '01-04-'.$this->startYear, 'May-'.$this->startYear=>'01-05-'.$this->startYear,'Jun-'.$this->startYear=>'01-06-'.$this->startYear,'Jul-'.$this->startYear=>'01-07-'.$this->startYear,'Aug-'.$this->startYear=>'01-08-'.$this->startYear,'Sep-'.$this->startYear=>'01-09-'.$this->startYear,'Oct-'.$this->startYear=>'01-10-'.$this->startYear,'Nov-'.$this->startYear=>'01-11-'.$this->startYear,'Dec-'.$this->startYear=>'01-12-'.$this->startYear,'Jan-'.$this->endYear=>'01-01-'.$this->endYear,'Feb-'.$this->endYear=>'01-02-'.$this->endYear,'Mar-'.$this->endYear=>'01-03-'.$this->endYear];
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->machine->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->process_name = '';
            if(!empty($row->process_id)):
                $pdata = $this->machine->getProcess($row->process_id);
                $z=0;
                foreach($pdata as $row1):
                    if($z==0) {$row->process_name .= $row1->process_name;}else{$row->process_name .= ', '.$row1->process_name;}$z++;
                endforeach;
            endif;
            $sendData[] = getMachineData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachine(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->load->view($this->machineForm,$this->data);
    }

    public function getProcessData(){
        $id = $this->input->post('dept_id');
        $processData = $this->process->getDepartmentWiseProcess($id);
        $option = "";
        foreach ($processData as $row):
            $option .= '<option value="' . $row->id . '" >' . $row->process_name . '</option>';
        endforeach;
        
        $this->printJson(['status'=>1, 'option'=>$option]);
    }

    public function save(){
        $data = $this->input->post();
        if(isset($data['ftype']) and $data['ftype'] == 'activities'):
            unset($data['ftype']);
            $this->saveActivity($data);
        else:
            $errorMessage = array();
            if(empty($data['item_code']))
                $errorMessage['item_code'] = "Machine no. is required.";
            //if(empty($data['machine_brand']))
                //$errorMessage['machine_brand'] = "Brand Name is required.";
            //if(empty($data['machine_model']))
                //$errorMessage['machine_model'] = "Machine Model is required.";
            if(empty($data['location']))
                $errorMessage['location'] = "Department is required.";
            //if(empty($data['process_id']))
                //$errorMessage['process_id'] = "Process Name is required.";
            if(empty($data['category_id']))
                $errorMessage['category_id'] = "Category is required.";

            if(!empty($errorMessage)):
                $this->printJson(['status'=>0,'message'=>$errorMessage]);
            else:
                unset($data['processSelect']);
                $data['item_type']=5;
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->machine->save($data));
            endif;
        endif;
        
    }

    public function edit(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        //$this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->data['dataRow'] = $this->machine->getMachine($this->input->post('id'));
        $this->data['processData'] =  $this->process->getDepartmentWiseProcess($this->data['dataRow']->location);
        $this->data['categoryList'] = $this->item->getCategoryList(5);
        $this->load->view($this->machineForm,$this->data);
    }

    public function setActivity(){
        $id = $this->input->post('id');
        $this->data['activityData'] = $this->machine->getActivity();
		$this->data['dataRow'] = $this->machine->getmaintanenceData($id);
        $this->load->view($this->activityForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->machine->delete($id));
        endif;
    }
  
    public function saveActivity() {
		$data = $this->input->post();
		$errorMessage = array();
		if(empty($data['activity_id']))
			$errorMessage['activity_id'] = "Machine Activities is required.";
        if(empty($data['activity_id'][0]))
			$errorMessage['activity_error'] = "Activities is required.";     
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$createdBy = Array();
			foreach($data['activity_id'] as $key=>$value){$createdBy[] = $this->session->userdata('loginId');}
            $activityData = [
                'id' => $data['id'],
				'activity_id' => $data['activity_id'],
				'checking_frequancy' => $data['checking_frequancy'],
                'created_by' =>  $createdBy
            ];
            $this->printJson($this->machine->saveActivity($data['machine_id'],$activityData));
        endif;
    } 
    
    // Report Data Created By Meghavi : 03/03/2022
    /* Updated By :- Sweta @08/07/2023 */
    public function machineReport($id)
	{
        $this->data['machine_id'] = $id;
        $this->data['reportData'] = $this->machine->getMachineForReportData($id);
        $this->data['dailyData'] = $this->machine->getmaintanenceDailyData($id);
        $this->data['weekData'] = $this->machine->getmaintanenceWeekData($id);
        $this->data['halfMonthlyData'] = $this->machine->getmaintanenceHalfMonthlyData($id);
        $this->data['monthlyData'] = $this->machine->getmaintanenceMonthlyData($id);
        $this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$this->load->view($this->reportForm,$this->data);
	}

    // Load Month Wise Machine Report Data
    /* Updated By :- Sweta @08/07/2023 */
    public function monthWiseMachineReport(){
        $data = $this->input->post();
        $maintenLogData = $this->machine->getMachineMaintenLogData($data['id'],$data['month']);
        $id = "";
        $id = (!empty($maintenLogData->id) ? $maintenLogData->id : '');
        $month = ""; $days="";
        foreach($this->data['monthArr'] as $key=>$value)
        {
            if($data['month'] == $value)
            {
                $month = $key;
                $days = cal_days_in_month(CAL_GREGORIAN,date('m',strtotime($value)),date('Y',strtotime($value)));
            }            
        }

        $cols=""; $cols = $days + 1;
        $thead=""; $tbody="";
        $thead = '<tr class="text-center">
                    <th colspan="'.$cols.'">'.$month.'</th>
                  </tr>
                  <tr class="text-center">
                    <th></th>';

                    $tbody = '<tr class="text-center">
                                <th>Daily</th>';
                    for($day=1; $day<=$days; $day++)
                    {
                        $thead .= '<th>'.$day.'</th>';
                        
                        $tbody .= '<th height="37;">
                                        <input type="checkbox" id="d'.$day.'" name="d'.$day.'" value="1" '.(!empty($maintenLogData->{'d'.$day}) ? "checked" : "").'> <label for="d'.$day.'"></label> 
                                    </th>';
                    }
        $thead .= '</tr>';  
        
        $tbody .= '</tr>';

        $tbody .= '<tr class="text-center">

                    <th>Weekly</th>
                    <th height="37;" colspan="7">
                        <input type="checkbox" id="w1" name="w1" value="1" '.(!empty($maintenLogData->w1) ? "checked" : "").'> <label for="w1"></label> 
                    </th>
                    <th height="37;" colspan="7">
                        <input type="checkbox" id="w2" name="w2" value="1" '.(!empty($maintenLogData->w2) ? "checked" : "").'> <label for="w2"></label> 
                    </th>
                    <th height="37;" colspan="7">
                        <input type="checkbox" id="w3" name="w3" value="1" '.(!empty($maintenLogData->w3) ? "checked" : "").'> <label for="w3"></label> 
                    </th>';
        if($days == 31){ $cols = 10; }
        elseif($days == 30) { $cols = 9; }
        elseif($days == 29) { $cols = 8; }
        else { $cols = 7; }
        $tbody .= '<th height="37;" colspan="'.$cols.'">
                        <input type="checkbox" id="w4" name="w4" value="1" '.(!empty($maintenLogData->w4) ? "checked" : "").'> <label for="w4"></label> 
                    </th>';                    
        $tbody .= '</tr>';  
        
        $tbody .= '<tr class="text-center">

                    <th>Half Monthly</th>
                    <th height="37;" colspan="15">
                        <input type="checkbox" id="hm1" name="hm1" value="1" '.(!empty($maintenLogData->hm1) ? "checked" : "").'> <label for="hm1"></label> 
                    </th>';
        if($days == 31){ $cols = 16; }
        elseif($days == 30) { $cols = 15; }
        elseif($days == 29) { $cols = 14; }
        else { $cols = 13; }
        $tbody .= '<th height="37;" colspan="'.$cols.'">
                        <input type="checkbox" id="hm2" name="hm2" value="1" '.(!empty($maintenLogData->hm2) ? "checked" : "").'> <label for="hm2"></label> 
                    </th>';  
        $tbody .= '</tr>';

        $tbody .= '<tr class="text-center">

                    <th>Monthly</th>';
        if($days == 31){ $cols = 31; }
        elseif($days == 30) { $cols = 30; }
        elseif($days == 29) { $cols = 29; }
        else { $cols = 28; }
        $tbody .= '<th height="37;" colspan="'.$cols.'">
                        <input type="checkbox" id="m1" name="m1" value="1" '.(!empty($maintenLogData->m1) ? "checked" : "").'> <label for="m1"></label> 
                    </th>';  
        $tbody .= '</tr>';
                
        $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody, 'id'=>$id]);
    }

    //Machine Report PDF Created By Meghavi 05/03/2022
    /* Updated By :- Sweta @08/07/2023 */
    public function machineReport_pdf($id){
        $this->data['reportData'] = $this->machine->getMachineForReportData($id);
        $this->data['dailyData'] = $this->machine->getmaintanenceDailyData($id);
        $this->data['weekData'] = $this->machine->getmaintanenceWeekData($id);
        $this->data['halfMonthlyData'] = $this->machine->getmaintanenceHalfMonthlyData($id);
        $this->data['monthlyData'] = $this->machine->getmaintanenceMonthlyData($id);
        $this->data['maintenLogData'] = $this->machine->getMachineMaintenLogData($id,$month='');//updated by karmi @29/03/2022
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();

        $thead=""; $tbody="";
        $thead = '<tr class="text-center">
                    <th>Month/Day</th>';
                    for($day=1; $day<=31; $day++)
                    {
                        $thead .='<th>'.$day.'</th>';
                    }
                    $thead .= '<th>Week 1</th>
                               <th>Week 2</th>
                               <th>Week 3</th>
                               <th>Week 4</th>
                               <th>Half Monthly1 (01 - 15)</th>
                               <th>Half Monthly2 (16 - 31)</th>
                               <th>Monthly</th>
                               <th>Remarks</th>
                </tr>';

        foreach($this->data['monthArr'] as $key=>$value){
            $tbody .= '<tr class="text-center">
                <th>'.$key.'</th>';
                $resultData = $this->machine->getMachineMaintenLogData($id,$value);
                $img = '<img src="'.base_url('assets/images/check_icon.png').'" height="10" />';
                for($i=1; $i<=31; $i++)
                {
                    $tbody .= '<td>'.(!empty($resultData->{'d'.$i}) ? $img : '').'</td>';    
                }
                for($i=1; $i<=4; $i++)
                {
                    $tbody .= '<td>'.(!empty($resultData->{'w'.$i}) ? $img : '').'</td>';
                }
                for($i=1; $i<=2; $i++)
                {
                    $tbody .= '<td>'.(!empty($resultData->{'hm'.$i}) ? $img : '').'</td>';
                }
                $tbody .= '<td>'.(!empty($resultData->m1) ? $img : '').'</td>';
                $tbody .= '<td></td>';
        }
        $tbody .= '</tr>';

		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $this->data['theadData'] = $thead;
        $this->data['tbodyData'] = $tbody;
		
		$pdfData = $this->load->view('machine/maintenance_pdf',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1rem;width:50%">PREVENTIVE MAINTENANCE RECORD</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">F/MNT/02 (00/01.01.16)</td>
							</tr>
						</table><hr>';
		$htmlFooter = '<table class="table table-top" style="margin-top:10px;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Approved By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,30,30,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
   
	//Created By Karmi @29/03/2022
    public function saveMachineMaintanLog() {
		$data = $this->input->post();
        $this->printJson($this->machine->saveMachineMaintanLog($data));        
    }
}
?>