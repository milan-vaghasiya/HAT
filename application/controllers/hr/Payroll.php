<?php
class Payroll extends MY_Controller
{
    private $indexPage = "hr/payroll/index";
    private $payrollForm = "hr/payroll/form";
    private $editEmpSalaryForm = "hr/payroll/edit_emp_salary_form";
    private $payrollView = "hr/payroll/view";
    private $payrollDataPage = "hr/payroll/payroll_data";
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Payroll";
		$this->data['headData']->controller = "hr/payroll";
		$this->data['headData']->pageUrl = "hr/payroll";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader('payroll');
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){
        $result = $this->payroll->getDTRows($this->input->post());
		$sendData = array();$i=1;
        foreach($result['data'] as $row):      
			$row->sr_no = $i++;
			$row->salary_sum = $this->payroll->getSalarySumByMonth($row->month)->salary_sum;
            $sendData[] = getPayrollData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
	
    public function loadSalaryForm(){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats();
        //$this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function getPayrollData($month){
        $this->data['empData'] = $this->payroll->getPayrollData($month);
        $this->load->view($this->payrollDataPage,$this->data);
    }

    public function makeSalary(){
        $this->data['empData'] = $this->payroll->getEmpSalary();
        $this->load->view($this->payrollForm,$this->data);
    }
    
    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->payroll->save($data));
        endif;
    }

    public function edit($month){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats();
        
        $salaryData = $this->payroll->getPayrollData($month);
        $ctcFormat = $this->salaryStructure->getCtcFromat($salaryData[0]->format_id);
        $this->data['earningHeads'] = $this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $this->data['deductionHeads'] = $this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);
        $this->data['salaryData'] = $salaryData;
        //print_r($salaryData);exit;
        $this->load->view($this->payrollForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->payroll->delete($id));
        endif;
    }

    /*************************** Load Salary Data***************************************/    
    public function getEmployeeSalaryData($dept_id="",$format_id="",$month="",$file_type="pdf"){
        if($_SERVER['REQUEST_METHOD'] === 'POST'):
            $data = $this->input->post();
        else:
            $data['dept_id'] = $dept_id;
            $data['format_id'] = $format_id;
            $data['month'] = $month;
            $data['file_type'] = $file_type;
            $data['view'] = 1;
        endif;

        $ctcFormat = [];//$this->salaryStructure->getCtcFromat($data['format_id']);
        $earningHeads = [];//$this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $deductionHeads = [];//$this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);

        $headCount = (empty($data['view']))?12:11;
        $eth = '';$betd = '';
        $dth = '';$bdtd = '';
        $thead = '<tr>
            <th>Emp Code</th>
            <th>Emp Name</th>
            <th>Working<br>Days</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Wages/Day</th>
            <th>Amount</th>
            <th>OT</th>
            <th>OT<br>Rate</th>
            <th>OT<br>Amount</th>
            <th>Other<br>Allounces</th>
            <th>Gross Salary</th>
            <th>Advance</th>
            <th>Pending<br>Loan (Before)</th>
            <th>Loan EMI</th>
            <th>Pending<br>Loan (After)</th>
            <th>PF</th>
            <th>PT</th>
            <th>Other<br>Deduction</th>
            <th>Net Salary</th>
            <th>Cheque</th>
            <th>Cash</th>
            '.((empty($data['view']))?"<th>Action</th>":"").'
        </tr>';
		//$data['emp_code'] = '20005';
        $empData = $this->payroll->getEmployeeListForSalary($data);
        
        
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge; 
        $empAttendanceData['month'] = $data['month'];
                
        $html = '';$sr_no=1; 
        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday; 
        if(!empty($empData)):
            foreach($empData as $row):  
                $empSalaryData =  $this->calculateEmpSalaryData($sr_no,$row,$empAttendanceData,$earningHeads,$deductionHeads);

                $empSalaryData['betd'] = $betd;
                $empSalaryData['bdtd'] = $bdtd;
                $empSalaryData['view'] = $data['view'];
                $rowHtml = $this->getEmployeeSalaryHtml($empSalaryData);

                $html .= "<tr id='".$sr_no."'>".$rowHtml."</tr>";

                $sr_no++;
            endforeach;
        else:
            if(empty($data['view'])):
                /*$html = '<tr>
                    <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
                </tr>';*/
            endif;
        endif;
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'):
            $this->printJson(['status'=>1,'emp_salary_head'=>$thead,'emp_salary_html'=>$html]);
        else:
            $response = '<table class="table-bordered jpExcelTable" border="1" repeat_header="1">';
            $response .= '<thead>'.$thead.'</thead><tbody>'.$html.'</tbody></table>';
            if($data['file_type'] == 'excel'):
				$xls_filename = 'payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			else:
			    $companyData = $this->attendance->getCompanyInfo();
				$htmlHeader = '<div class="table-wrapper">
                    <table class="table txInvHead">
                        <tr class="txRow">
                            <td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
                            <td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($data['month'])).'</td>
                        </tr>
                    </table>
                </div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
                    <tr>
                        <td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td>
                        <td style="width:50%;text-align:right;">Page No :- {PAGENO}</td>
                    </tr>
                </table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			endif;
        endif;
    }
    
    public function viewSalary(){
        $start = new DateTime($this->startYearDate);
        $start->modify('first day of this month');
        $end = new DateTime($this->endYearDate);
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        $monthList = array();
        foreach ($period as $dt): $monthList[] = $dt->format("Y-m-t"); endforeach;

        $this->data['monthList'] = (object) $monthList;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['ctcFormat'] = $this->salaryStructure->getCtcFormats();
        $this->load->view($this->payrollView,$this->data);   
    }
    
    public function getEmployeeActualSalaryData($dept_id="",$format_id="",$month="",$file_type="pdf"){
        $data['dept_id'] = $dept_id;
        $data['format_id'] = $format_id;
        $data['month'] = $month;
        
        $ctcFormat = $this->salaryStructure->getCtcFromat($data['format_id']);
        $postData = ['type'=>1,'ids'=>$ctcFormat->eh_ids];
        //if($ctcFormat->salary_duration == "H"): $postData['is_system'] = 0; endif;
        $earningHeads = $this->salaryStructure->getSalaryHeadList($postData);

        $postData['type'] = -1;
        $postData['ids'] = $ctcFormat->dh_ids;
        $deductionHeads = $this->salaryStructure->getSalaryHeadList($postData);

        $headCount = 9;
        $eth = '';$betd = '';
        $dth = '';$bdtd = '';
        $ctcFormat->salary_duration = "H";
        
        $thead = '<tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Wage":"Total Days").'</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Rate Hour":"Present").'</th>
            <th>'.(($ctcFormat->salary_duration == "H")?"Working Hour":"Absent").'</th>
            <th>Gross Salary</th>
            <th>Advance</th>
            <th>Loan</th>
            <th>Actual Salary</th>
        </tr>';
        
        $empData = $this->payroll->getEmployeeListForSalary($data);
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'payroll'=>1]);
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge;      
        $empAttendanceData['month'] = $data['month'];  

        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday; $sr_no = 1;
        $html = "";
        if(!empty($empData)):
            foreach($empData as $row):
                $empSalaryData =  $this->calculateEmpSalaryData($sr_no,$row,$empAttendanceData,$earningHeads,$deductionHeads);

                $empSalaryData['betd'] = $betd;
                $empSalaryData['bdtd'] = $bdtd;

                $etd = "";
                foreach($empSalaryData['earning_data'] as $row):
                    $etd .= "<td>".$row['org_amount']."</td>";
                endforeach;

                $dtd = "";
                foreach($empSalaryData['deduction_data'] as $row):
                    $dtd .= "<td>".$row['org_amount']."</td>";
                endforeach;

                $empSalaryData['etd'] = $etd;
                $empSalaryData['dtd'] = $dtd;

                $row = (object) $empSalaryData;
                $html .= "<tr>
                    <td>".$row->sr_no."</td>
                    <td>
                        ".$row->emp_name."
                    </td>
                    <td>
                        ".(($row->salary_basis == "H")?$row->wage:$row->working_days)."
                    </td>                                                                    
                    <td>
                        ".(($row->salary_basis == "H")?$row->r_hr:$row->present_days)."
                    </td>
                    <td>
                        ".(($row->salary_basis == "H")?$row->total_wh:$row->absent_days)."
                    </td>
                    ".((!empty($row->etd))?$row->etd:$row->betd)."
                    <td>
                        ".$row->org_total_earning."            
                    </td>
                    ".((!empty($row->dtd))?$row->dtd:$row->bdtd)."
                    <td>".$row->org_advance_deduction."</td>
                    <td>".$row->org_emi_amount."</td>
                    <td>
                        ".$row->actual_sal."
                    </td>
                </tr>";
                $sr_no++;
            endforeach;
        else:
            $html = '<tr>
                <td id="noData" class="text-center" colspan="'.$headCount.'">No data available in table</td>
            </tr>';
        endif;
        
        $response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
        $response .= '<thead>'.$thead.'</thead><tbody>'.$html.'</tbody></table>';
        $xls_filename = 'actual-payroll-'.date("m-Y",strtotime($data['month'])).'.xls';
				
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename='.$xls_filename);
		header('Pragma: no-cache');
		header('Expires: 0');
		
		echo $response;
    }

    public function editEmployeeSalaryData(){
        $data = $this->input->post();
        $salaryData = $data['salary_data'][$data['key_value']];
        $sr_no = $data['key_value'];
        $salaryData = (object) $salaryData;    
        $salaryData->earning_data = (!empty($salaryData->earning_data))?json_decode($salaryData->earning_data):array();
        $salaryData->deduction_data = (!empty($salaryData->deduction_data))?json_decode($salaryData->deduction_data):array();

        $ctcFormat = [];//$this->salaryStructure->getCtcFromat($data['format_id']);
        $earningHeads = [];//$this->salaryStructure->getSalaryHeadList(['type'=>1,'ids'=>$ctcFormat->eh_ids]);
        $deductionHeads = [];//$this->salaryStructure->getSalaryHeadList(['type'=>-1,'ids'=>$ctcFormat->dh_ids]);

        $empData = $this->payroll->getEmployeeSalaryStructure($salaryData->emp_id);
        $empAttendanceData = $this->biometric->getSalaryHours(['from_date'=>$data['month'],'dept_id'=>$data['dept_id'],'emp_id'=>$salaryData->emp_id,'payroll'=>1]);
       
        $canteenCharges = $this->masterModel->getMasterOptions();
        $empAttendanceData['cl_charge'] = $canteenCharges->cl_charge;
        $empAttendanceData['cd_charge'] = $canteenCharges->cd_charge;      
        $empAttendanceData['month'] = $data['month'];  

        $empAttendanceData['totalDays'] = date("t",strtotime($data['month'])); 
        $holiday = countDayInMonth("Wednesday",$data['month']);
        $empAttendanceData['totalDays'] -= $holiday;      
        if(!empty($empData)):
            $empSalaryData = $this->calculateEmpSalaryData($sr_no,$empData,$empAttendanceData,$earningHeads,$deductionHeads,$salaryData);           
        endif;

        $this->data['salaryData'] = (object) $empSalaryData;
        $this->load->view($this->editEmpSalaryForm,$this->data);
    }

    public function saveEmployeeSalaryData(){
        $data = $this->input->post();
        $data['sr_no'] = $data['row_index'];
        $etd = "";
		$data['earning_data'] = (Array) json_decode($data['earning_data']);
		$data['deduction_data'] = (Array) json_decode($data['deduction_data']);
        foreach($data['earning_data'] as $row):
            $etd .= "<td>".$row['amount']."</td>";
        endforeach;

        $dtd = "";
        foreach($data['deduction_data'] as $row):
            $dtd .= "<td>".$row['amount']."</td>";
        endforeach;

        $data['etd'] = $etd;
        $data['dtd'] = $dtd;
        $data['view'] = 0;

        $html = $this->getEmployeeSalaryHtml($data);
        $this->printJson(['status'=>1,'salary_data'=>$html]);
    }

    public function calculateEmpSalaryData($sr_no,$empData,$empAttendanceData,$earningHeads,$deductionHeads,$salaryData = array()){
        $cl_charge = $empAttendanceData['cl_charge'];
        $cd_charge = $empAttendanceData['cd_charge'];  
        $empSalarayHeads = (!empty($empData->salary_head_json))?json_decode($empData->salary_head_json):array();

        $totalDays =  $empAttendanceData['totalDays'];
        $total_wh = (isset($empAttendanceData[$empData->emp_id]))?round(($empAttendanceData[$empData->emp_id]['twh']/3600),2):0;
        $tot = (isset($empAttendanceData[$empData->emp_id]))?round(($empAttendanceData[$empData->emp_id]['tot']/3600),2):0;
        $wh = $total_wh - $tot;
        $present = (isset($empAttendanceData[$empData->emp_id]))?($empAttendanceData[$empData->emp_id]['tpd']):0;
        $absent = $totalDays - $present; 

        $empEarningData = array();$empDeductionData = array();$etd = '';$dtd = '';
        $actual_wage=0;$on_paper_wages=0;$r_hr = 0;$r_hr_op=0;$basic_amount = 0;$ot_amount=0;
        $grossSalary = 0; $pf_amount=0;$other_allounces=0;$other_deduction=0;$pt_amount=0;
        $totalDeduction = 0;$netSalary = 0;$pf_per = 12;
		
		$actual_wage = ((!empty($empData->salary_actual))?$empData->salary_actual:0);
		$r_hr = ($actual_wage / 8);
		
		$empData->salary_duration = "H";
		
        if($empData->salary_duration == "H"):
            $basic_amount = round(($r_hr * $wh),0);
			$ot_amount = round(($r_hr * $tot),0);
			$actual_salary = $basic_amount + $ot_amount;
		else:
            $basic_amount = round(($actual_wage * $present),0);
			$ot_amount = round(($r_hr * $tot),0);
            $actual_salary = $basic_amount + $ot_amount;
        endif;
        
		
		$grossSalary += $actual_salary;		
		$grossSalary += $other_allounces;
		
		$empEarningData[] = ['field_name'=>'basic_salary','head_name'=>'Basic Salary','amount'=>$basic_amount,'editable'=>''];
		$empEarningData[] = ['field_name'=>'ot_amount','head_name'=>'Overtime','amount'=>$ot_amount,'editable'=>''];
		$empEarningData[] = ['field_name'=>'other_allounces','head_name'=>'Other Allounces','amount'=>$other_allounces,'editable'=>'1'];
		
		
		// Count PF
		$pfLimit = 15000;$pf_per=12;
		if(!empty($empData->pf_applicable))
		{
			$pf_amount = ($grossSalary <= $pfLimit) ? round((($grossSalary * $pf_per)/100),0) : round((($pfLimit * $pf_per)/100),0);
		}
		
        // Advance Salary
        $adsData = (!empty($empAttendanceData[$empData->emp_id]['advance_data']))?$empAttendanceData[$empData->emp_id]['advance_data']:array();
        $adSalary=0;$orgAdSalary=0;
        $a=0;$adsHtml='';$adSalaryData=array();
        if(!empty($salaryData->advance_data)):
            $adSalaryData = $salaryData->advance_data;
            $adSalary = $salaryData->advance_deduction;
        else:
            foreach($adsData as $adsRow):
                $adSalaryData[$a] = [
                    'id'=>$adsRow->id,
                    'entry_date' => $adsRow->entry_date,
                    'payment_mode' => $adsRow->payment_mode,
                    'amount'=> $adsRow->pending_amount
                ];
                $adSalary += $adsRow->pending_amount;
                $a++;
            endforeach;
        endif;

        // Employee Loans
        $l=0;$loanEmi=0;$orgLoanEmi=0;$pendingLoan=0;$emiAmount=0;$loanHtml = '';
        $loanData = (!empty($empAttendanceData[$empData->emp_id]['loan_data']))?$empAttendanceData[$empData->emp_id]['loan_data']:array();
        $loanDataRows = array();        
        if(!empty($salaryData->loan_data)):
            foreach($salaryData->loan_data as $loanRow):
                $loanRow = (object) $loanRow;
                $loanDataRows[$l] = [
                    'id'=>$loanRow->id,
                    'payment_mode' => $loanRow->payment_mode,
                    'loan_no'=>$loanRow->loan_no,
                    'amount'=> $loanRow->amount,
                    'loan_amount'=> $loanRow->loan_amount
                ];
                $loanEmi += $loanRow->amount;
                $pendingLoan += ($loanRow->loan_amount - $loanRow->amount);
                $l++;
            endforeach;
        else:
            foreach($loanData as $loanRow):
                $emiAmount = ($loanRow->pending_amount > $loanRow->emi_amount)?$loanRow->emi_amount:$loanRow->pending_amount;
                
                $loanDataRows[$l] = [
                    'id'=>$loanRow->id,
                    'payment_mode' => $loanRow->payment_mode,
                    'loan_no'=>$loanRow->loan_no,
                    'amount'=> $emiAmount,
                    'loan_amount'=>$loanRow->pending_amount
                ];
                $loanEmi += $emiAmount;
                $pendingLoan += ($loanRow->pending_amount - $emiAmount);
                $l++;
            endforeach;
        endif;
        

        $totalDeduction += $pf_amount;
        $totalDeduction += $adSalary;
        $totalDeduction += $loanEmi;
        $totalDeduction += $other_deduction;
        		
		if(($grossSalary - $totalDeduction) >= 12000){$pt_amount = 200;}
		
        $totalDeduction += $pt_amount;	
		
		$empDeductionData[] = ['field_name'=>'pf_amount','head_name'=>'PF','amount'=>$pf_amount,'editable'=>''];
		$empDeductionData[] = ['field_name'=>'pt_amount','head_name'=>'Professional Tax','amount'=>$pt_amount,'editable'=>''];
		$empDeductionData[] = ['field_name'=>'other_deduction','head_name'=>'Other Deduction','amount'=>$other_deduction,'editable'=>'1'];
		
        $netSalary = round($grossSalary - $totalDeduction,0);
        
		
        $dataRow = [
            'sr_no' => $sr_no,
            'id' => (!empty($salaryData->id))?$salaryData->id:"",
            'emp_id' => $empData->emp_id,
            'emp_code' => $empData->emp_code,
            'emp_name' => $empData->emp_name,
            'emp_type' => $empData->emp_type,
            'salary_code' => $empData->salary_code,
            'salary_basis' => $empData->salary_duration,
            'pf_per' => $pf_per,
            'pf_applicable' => $empData->pf_applicable,
            'total_wh' => $total_wh,
            'tot' => $tot,
            'wh' => $wh,
			'ot_amount' => $ot_amount,
            'wage' => $actual_wage,
            'r_hr' => $r_hr,
			'basic_salary' => $basic_amount,
            'present_days' => $present,
            'working_days' => $totalDays,
            'absent_days' => $absent,
            'other_allounces' => $other_allounces,
            'total_earning' => $grossSalary,
            'earning_data' => $empEarningData,
            'other_deduction' => $other_deduction,
            'pf_amount' => $pf_amount,
            'pt_amount' => $pt_amount,
            'total_deduction' => $totalDeduction,
            'deduction_data' => $empDeductionData,
            'advance_deduction' => $adSalary,
            'advance_data' => $adSalaryData,
            'emi_amount' => $loanEmi,
            'loan_data' => $loanDataRows,
            'pending_loan' => $pendingLoan,
            'net_salary' => $netSalary
        ];

        return $dataRow;
    }

    public function getEmployeeSalaryHtml($row){
        $row = (object) $row;
        $salaryCode = '"'.$row->salary_code.'"';
        $editButton = "<button type='button' class='btn btn-outline-warning' title='Edit' onclick='Edit(".$row->sr_no.", ".$salaryCode.");'><i class='ti-pencil-alt'></i></button>";

        $hiddenInputs = "";
        if(empty($row->view)):
            $hiddenInputs = "<input type='hidden' name='salary_data[".$row->sr_no."][id]' value='".$row->id."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_id]' value='".$row->emp_id."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_name]' value='".$row->emp_name."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emp_type]' value='".$row->emp_type."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][pf_per]' value='".$row->pf_per."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][pf_applicable]' value='".$row->pf_applicable."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][salary_basis]' value='".$row->salary_basis."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_wh]' value='".$row->total_wh."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][tot]' value='".$row->tot."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][wh]' value='".$row->wh."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][ot_amount]' value='".$row->ot_amount."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][wage]' value='".$row->wage."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][r_hr]' value='".$row->r_hr."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][basic_salary]' value='".$row->basic_salary."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][present_days]' value='".$row->present_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][working_days]' value='".$row->working_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][absent_days]' value='".$row->absent_days."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][other_allounces]' value='".$row->other_allounces."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_earning]' value='".$row->total_earning."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][earning_data]' value='".json_encode($row->earning_data)."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][net_salary]' value='".$row->net_salary."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][pf_amount]' value='".$row->pf_amount."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][pt_amount]' value='".$row->pt_amount."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][other_deduction]' value='".$row->other_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][total_deduction]' value='".$row->total_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][deduction_data]' value='".json_encode($row->deduction_data)."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][advance_deduction]' value='".$row->advance_deduction."'>
            <input type='hidden' name='salary_data[".$row->sr_no."][emi_amount]' value='".$row->emi_amount."'>";

            $a=0;
            if(!empty($row->advance_data)):
                foreach($row->advance_data as $adsRow):
                    $adsRow = (object) $adsRow;
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][id]' value='".$adsRow->id."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][entry_date]' value='".$adsRow->entry_date."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][payment_mode]' value='".$adsRow->payment_mode."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][advance_data][".$a."][amount]' value='".$adsRow->amount."'>";
                    $a++;
                endforeach;
            endif;

            $l=0;
            if(!empty($row->loan_data)):
                foreach($row->loan_data as $loanRow):
                    $loanRow = (object) $loanRow;
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][id]' value='".$loanRow->id."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][payment_mode]' value='".$loanRow->payment_mode."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][loan_no]' value='".$loanRow->loan_no."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][amount]' value='".$loanRow->amount."'>";
                    $hiddenInputs .= "<input type='hidden' name='salary_data[".$row->sr_no."][loan_data][".$l."][loan_amount]' value='".$loanRow->loan_amount."'>";
                    $l++;
                endforeach;
            endif;
        endif;
		
        $html = "<td>".$row->emp_code."</td>
        <td>
            ".$row->emp_name."
            ".((empty($row->view))?$hiddenInputs:"")."
        </td>
        <td>".$row->working_days."</td>                                                                    
        <td>".$row->present_days."</td>
        <td>".$row->absent_days."</td>
        <td>".$row->wage."</td>
        <td>".$row->basic_salary."</td>
        <td>".$row->tot."</td>
        <td>".$row->r_hr."</td>
        <td>".$row->ot_amount."</td>
        <td>".$row->other_allounces."</td>
        <td>".$row->total_earning."  </td>
        <td>".$row->advance_deduction."</td>
        <td></td>
        <td>".$row->emi_amount."</td>
        <td></td>
        <td>".$row->pf_amount."</td>
        <td>".$row->pt_amount."</td>
        <td>".$row->other_deduction."</td>
        <td>".$row->net_salary."</td>
        <td>".$row->net_salary."</td>
        <td>0</td>";

        $html .= (empty($row->view))?"<td>".$editButton."</td>":"";
        return $html;
    }
}
?>