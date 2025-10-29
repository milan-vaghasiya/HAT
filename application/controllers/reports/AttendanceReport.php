<?php
class AttendanceReport extends MY_Controller
{
	private $indexPage = "report/hr_report/index";
	private $emp_report = "report/hr_report/emp_report";
	private $monthlyAttendance = "report/hr_report/month_attendance";
	private $monthSummary = "report/hr_report/month_summary";
	private $monthlySummary = "report/hr_report/monthly_summary";
	private $empRole = ["1" => "Admin", "2" => "Production Manager", "3" => "Accountant", "4" => "Sales Manager", "5" => "Purchase Manager", "6" => "Employee"];

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HR Report";
		$this->data['headData']->controller = "reports/attendanceReport";
		$this->data['floatingMenu'] = $this->load->view('report/hr_report/floating_menu',[],true);
	}

	public function index(){
		$this->data['pageHeader'] = 'HR REPORT';
		$this->load->view($this->indexPage, $this->data);
	}
	
	// Daterange Attendance Summary
	public function monthlyAttendanceSummary(){
		$this->data['empList'] = $this->employee->getEmployeeList('emp_code');
		$this->load->view($this->monthSummary, $this->data);
	}

	// Monthly Attendance Hourly
	public function monthlyAttendance(){
		$this->data['deptRows'] = $this->department->getDepartmentList();
		$this->load->view($this->monthlyAttendance, $this->data);
	}
	
	public function getHourlyReport($month="",$dept_id="",$emp_type="All",$report_type=1,$file_type = 'pdf',$biomatric_id="ALL"){
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post())):
			$month = $this->input->post('month');
			$dept_id = $this->input->post('dept_id');
			$file_type = $this->input->post('file_type');
			$emp_type = $this->input->post('emp_type');
			$report_type = $this->input->post('report_type');
		endif;
		$emp_type=($emp_type == 'All') ? '' : $emp_type;
		$postData['month'] = date('Y-m-d',strtotime($month));
		$postData['dept_id'] = $dept_id;
		$postData['file_type'] = $file_type;
		$postData['emp_type'] = $emp_type;
		$postData['etype'] = 'STRICT';
		$postData['report_type'] = $report_type;
		$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		//$etype = ($emp_type != 1) ? '2,3' : $emp_type;
		
		if(!empty($month)):
			$empData = $this->biometric->getEmpShiftLog($postData);
			$attendanceData = $this->biometric->getSalaryHours(['from_date'=>date('Y-m-01',strtotime($month)),'to_date'=>date('Y-m-t',strtotime($month)),'dept_id'=>$dept_id,'emp_type'=>$emp_type,'datewise'=>1]);	
			
			$lastDay = intVal(date('t',strtotime($postData['month'])));
			$thead='<tr style="background:#dddddd;"><th style="width:50px;">Code</th><th style="width:220px;">Emp Name</th>';
			for($d=1;$d<=$lastDay;$d++):	
				$thead.='<th class="text-center">'.$d.'</th>'; 
			endfor;
			if($report_type == 1):
			    $thead.='<th class="text-center">WP/WO</th>';
			    $thead.='<th class="text-center">Present <br> Days</th>';
			    $thead.='<th class="text-center">Half <br>Leave</th>';
			    $thead.='<th class="text-center">Absent <br> Days</th>';    
			    $thead.='<th class="text-center">Total <br> Days</th>';
			    $thead.='</tr>';   
			else:
			    $thead.='<th class="text-center">Total</th></tr>';
			endif;
			
			$tbody='';$i=0;$hdLimit = 0;$minLimitPerDay = 14400;
			
			foreach($empData as $row):
				if($emp_type == 1){ $hdLimit = ((!empty($attendanceData[$row->emp_id]['tst']))?$attendanceData[$row->emp_id]['tst']:39600);}else{$hdLimit = 28800;}
				$tr_bg = ($i % 2 == 0) ? '' : '#efefef';$i++;
				$dept_name = $row->dept_name;

				$tbody.='<tr style="background:'.$tr_bg.'">';
				$tbody.='<td class="text-center">'.$row->emp_code.'</td>';
				$tbody.='<td>'.$row->emp_name.'</td>';
				
                if($report_type == 1):
                    $totalDays = date("t",strtotime($month)); 
                    $holiday = countDayInMonth("Wednesday",$month);
                    $totalDays -= $holiday; 
                    $presentDays = 0;$absentDays = 0;$weekOff=0;$wp=0;$hl=0;
    				for($d=1;$d<=$lastDay;$d++):
    					$wh = (!empty($attendanceData[$row->emp_id][$d]))?$attendanceData[$row->emp_id][$d]:0;
    					$day = 0;$text = "A";$class="bg-danger text-white";
    					if($wh >= $hdLimit):
    					    $day = 1;
    					    $text = "P";
    					    $class = "text-success";
    					elseif($wh > 0 && $wh < $minLimitPerDay):
    					    $day = 0;
    					    $text = "A";
    					    $class="bg-danger text-white";
						elseif($wh >= $minLimitPerDay && $wh < $hdLimit):
    					    $day = 0.5;
    					    $text = "HL";$hl++;
    					    $class = "bg-info text-dark";
    					endif;
    					
    					if(date("D",strtotime(date($d."-m-Y",strtotime($month)))) == "Wed")
						{
    					    if($text == "A"){$text = "W";}
							if($text == "P"){$text = "WP";$wp++;$class = "text-success";}
							if($text == "HL"){$text = "WHL";$wp++;}
    					    $class = "bg-light text-dark";
    					    $weekOff ++;
							$day = 0;
    					}
    					
    					$tbody .= '<td class="text-center '.$class.'">'.$text.'</td>';
    					$presentDays += $day;
    					
    				endfor;
    				$absentDays = (($totalDays - $presentDays) > 0)?$totalDays - $presentDays:0;
    				$tbody .= '<td class="text-center" style="width:45px;">'.$wp.'/'.$weekOff.'</td>';
    				$tbody .= '<td class="text-center" style="width:45px;background:#7ae8d6;">'.$presentDays.'</td>';
    				$tbody .= '<td class="text-center" style="width:45px;">'.$hl.'</td>';
    				$tbody .= '<td class="text-center" style="width:45px;">'.$absentDays.'</td>';
    				$tbody .= '<td class="text-center" style="width:45px;">'.$totalDays.'</td>'; 
    				$tbody .= '</tr>';
                else:
    				$wh = 0;
    				for($d=1;$d<=$lastDay;$d++):
    					$wh = (!empty($attendanceData[$row->emp_id][$d]))?$attendanceData[$row->emp_id][$d]:0;
    					$tbody .= '<td class="text-center">'.sprintf('%0.2f',(round($wh/3600,2))).'</td>';
    				endfor;
    
    				$twh = 0;
    				$twh = (!empty($attendanceData[$row->emp_id]['twh']))?$attendanceData[$row->emp_id]['twh']:0;
    				$tbody.='<td class="text-center" style="width:45px;">'.sprintf('%0.2f',round(($twh/3600),2)).'</td>'; 
    				$tbody.='</tr>';
    			endif;
			endforeach;


			$tableHeader = '';if(empty($postData['dept_id'])){$dept_name = 'All Department';};
			$tableHeader = '<tr style="background:#dddddd;"><th colspan="2" class="text-left">'.$dept_name.'</th>';
			$tableHeader .= '<th colspan="'.($lastDay-5).'">'.$companyData->company_name.'</th>';
			$tableHeader .= '<th colspan="6" class="text-right">'.date("F-Y",strtotime($postData['month'])).'</th></tr>';
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
			$response .= '<thead>'.$tableHeader.$thead.'</thead>';
			$response .= '<tbody>'.$tbody.'</tbody></table>';
 			//echo $response;exit;
			if($file_type == 'excel'):
				$xls_filename = 'monthlyAttendance_'.$dept_name.'.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			
			elseif($file_type == 'pdf'):
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($postData['month'])).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='monthlyAttendance.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			
			else:
				$this->printJson(['status'=>1,'thead'=>$thead,'tbody'=>$tbody]);
			endif;
		endif;
	}
	
	// Missed Punch
	public function mismatchPunch(){        
        $this->data['pageHeader'] = 'EMPLOYEE PUNCHES';
        $this->load->view("report/hr_report/mismatch_punch",$this->data);
    }
	
	public function getAllPunches($report_date="",$punch_status=""){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$report_date = $this->input->post('report_date');
			$punch_status = $this->input->post('punch_status');
		}
		$postData['report_date'] = date('Y-m-d',strtotime($report_date));
		$postData['punch_status'] = $punch_status;
		//$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		//print_r($postData['punch_status']);exit;
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getAllPunches($postData);
			$html = "";
			foreach($mpData as $row):
				$allPunches =  '';$mcls = "";
				if($row->punchCount > 0)
				{
					$empPunches = explode(',',$row->punch_date);
					$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
					$empPunches = sortDates($empPunches,$sortType);
					$ap = Array();$mcls = (($row->punchCount % 2 != 0) OR ($row->punchCount > 4)) ? 'text-danger' : '';
					foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
					$allPunches = implode(', ',$ap);
				}
				$html .= '
					<tr>
						<td>'.$row->emp_code.'</td>
						<td>'.$row->emp_name.'</td>
						<td>'.$row->dept_name.'</td>
						<td>'.$row->shift_name.'</td>
						<td>'.$row->emp_dsg.'</td>
						<td class="text-left '.$mcls.'">'.$allPunches.'</td>
						<td class="text-center"><a href="#" class="float-right manualAttendance btn btn-sm btn-success" data-button="close" data-empid="'.$row->emp_id.'" data-adate="'.$report_date.'" data-button="both" data-modal_id="modal-lg" data-function="addManualAttendance" data-form_title="Add Manual Attendance"> View ('.$row->punchCount.')</a></td>
					</tr>
				';
			endforeach;
			$this->printJson(['status'=>1,'tbody'=>$html]);
		}
	}
	
	// Clear Miss Punch Created By JP@10.03.2023
	public function clearPunches($report_date=""){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$report_date = $this->input->post('report_date');
		}
		$postData['report_date'] = date('Y-m-d',strtotime($report_date));
		$postData['punch_status'] = 3;
		//$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		//print_r($postData['punch_status']);exit;
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getMissedPunches($postData);
			
			$html = "";
			foreach($mpData as $row):
				$empPunches = explode(',',$row->punch_date);
				$punch_out = date('H:i', strtotime(max($empPunches)));
				$cl = findClosestDate([$row->shiftStart,$row->shiftEnd], $punch_out);
				$empPunches[] = $cl;
				print_r($row->shift_name);
				$empPunches = sortDates($empPunches,'ASC');	
				print_r($empPunches);
				print_r('<hr>');
				
			endforeach;
			exit;
			$this->printJson(['status'=>1,'tbody'=>$html]);
		}
	}
	
	// Get Daily Attendance (Used In Attendance Dashboard)
	public function getDailyAttendance($report_date=""){
		
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$report_date = $this->input->post('report_date');
		}
		$postData['report_date'] = date('Y-m-d',strtotime($report_date));
		//$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getPunchByDate($postData['report_date']);
			
			$empTable = "";$totalEmp=0;$presentEmp=0;$absentEmp=0;$lateEmp=0;
			if(!empty($mpData))
			{
				$totalEmp=count($mpData);
				foreach($mpData as $row):
					$empPunches = $row->punch_date;$status= 'P';$allPunches = "";			
					if(!empty($empPunches))
					{
						$empPunches = explode(',',$empPunches);
						$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
						$empPunches = sortDates($empPunches,$sortType);
						if(strtotime($row->shift_start) < strtotime($empPunches[0])){$lateEmp++;$status= 'P (L)';} // Count Late Arrival
						
						$ap = Array();
						foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
						$allPunches = implode(', ',$ap);
						$status= 'P';$presentEmp++;
					}
					else{$status= 'A';$absentEmp++;}
					
					$empTable .= '
						<tr>
							<td>'.$row->emp_code.'</td>
							<td>'.$row->emp_name.'</td>
							<td>'.$row->dept_name.'</td>
							<td>'.$row->shift_name.'</td>
							<td>'.$row->emp_dsg.'</td>
							<td>'.$status.'</td>
							<td>'.$allPunches.'</td>
						</tr>
					';
				endforeach;
				$this->printJson(['status'=>1,"totalEmp"=>$totalEmp,"present"=>$presentEmp,"late"=>$lateEmp,"absent"=>$absentEmp,'tbody'=>$empTable]);
			}
			else
			{
				$this->printJson(['status'=>1,"totalEmp"=>0,"present"=>0,"late"=>0,"absent"=>0,'tbody'=>""]);
			}
		}
	}

	public function getAbsentReport($fDate="",$file_type='pdf'){
		
		set_time_limit(0);
		
		if(empty($fDate)){$postData['report_date'] = date('Y-m-d');}
		else{$postData['report_date'] = date('Y-m-d',strtotime($fDate));}
		$companyData = $this->attendance->getCompanyInfo();
		$reportData = "";
		if(!empty($postData['report_date']))
		{
			$mpData = $this->biometric->getAbsentReport($postData['report_date']);
			$i=1;
			foreach($mpData as $row):
				$reportData .= '<tr>
									<td class="text-center">'.$i++.'</td>
									<td class="text-center">'.$row->emp_code.'</td>
									<td>'.$row->emp_name.'</td>
									<td>'.$row->shift_name.'</td>
									<td>'.$row->dept_name.'</td>
									<td>'.$row->emp_dsg.'</td>
								</tr>';
			endforeach;			
		}
		
		$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
		$response .= '<thead>
				<tr style="background:#eee;"><th colspan="6">Absent Report [ '.date('d-m-Y',strtotime($postData['report_date'])).' ]</th></tr>
				<tr style="background:#eee;">
					<th style="width:50px;">#</th>
					<th style="width:100px;">Emp Code</th>
					<th>Employee</th>
					<th>Shift</th>
					<th>Department</th>
					<th>Designation</th>
				</tr></thead><tbody>'.$reportData.'</tbody></table>';
		//echo $response;exit;
		if($file_type == 'excel')
		{
			$xls_filename = 'monthlyAttendance.xls';
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$xls_filename);
			header('Pragma: no-cache');
			header('Expires: 0');
			
			echo $response;
		}
		else
		{
			$htmlHeader = '<div class="table-wrapper">
								<table class="table txInvHead">
									<tr class="txRow">
										<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
										<td class="text-right pad-right-10"><b>Report Date : '.date('d-m-Y',strtotime($postData['report_date'])).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = new \Mpdf\Mpdf();
			$pdfFileName='absentReport_'.date('d-m-Y',strtotime($postData['report_date'])).'.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetProtection(array('print'));
			$mpdf->SetTitle('Absent Report '.date('d-m-Y',strtotime($postData['report_date'])));
			
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
			
			$mpdf->AddPage('P','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-P');
			$mpdf->WriteHTML($response);
			$mpdf->Output($pdfFileName,'I');
		}
	}
	
	// Print Monthly Attendance EMP WISE | 04.12.2022
	public function printMonthlyAttendance($dept_id,$month, $file_type = 'excel')
	{
		set_time_limit(0);
		$postData = Array();
		if(!empty($this->input->post()))
		{
			$month = $this->input->post('month');
			$dept_id = $this->input->post('dept_id');
			$file_type = $this->input->post('file_type');
		}
		$postData['month'] = date('Y-m-d',strtotime($month));
		$postData['dept_id'] = $dept_id;
		$postData['file_type'] = $file_type;
		$companyData = $this->attendance->getCompanyInfo();
		$dept_name = '';
		
		if(!empty($month))
		{
			$empData = $this->biometric->getEmpShiftLog($postData);			
			$lastDay = intVal(date('t',strtotime($postData['month'])));

			$punchData = NULL;$thead = '';$tbody = '';$i = 1;$printData = '';$empCount = 1;
			
			$emp1 = array();$response = '';$empTable = '';$pageData = array();
			if (!empty($empData)) {
				foreach ($empData as $emp) {
					$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';
					$wo = 0;$wh = 0;$wi = 0;$oth = 0;$oti = 0;$ot=0;$totalWH= 0;$totalOT = 0;$totalTWH = 0;
					$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';
					$workHrs = '';$otData = '';$status = '';$dept_name = $emp->dept_name;

					$inData .= '<tr><th style="border:1px solid #888;font-size:12px;">IN</th>';
					$lunchInData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-START</th>';
					$lunchOutData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-END</th>';
					$outData .= '<tr><th style="border:1px solid #888;font-size:12px;">OUT</th>';
					$workHrs .= '<tr><th style="border:1px solid #888;font-size:12px;">WH</th>';
					$otData .= '<tr><th style="border:1px solid #888;font-size:12px;">OT</th>';
					$status .= '<tr><th style="border:1px solid #888;font-size:12px;">STATUS</th>';
					for($d=1;$d<=$lastDay;$d++){
						$attend_status = false;
						$dt = str_pad($d, 2, '0', STR_PAD_LEFT);
						$currentDate = date('Y-m-'.$dt,strtotime($postData['month']));
						$punchDates = array();
						$day = date("D", strtotime($currentDate));
						if ($day == 'Wed') {$wo++;}
						$theadDate .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">' . $d . '</th>';
						$theadDay .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">' . $day . '</th>';
						
						$empAttendanceLog = $this->biometric->getEmpPunchesByDate($emp->emp_id,$currentDate);
						$empPunches = array_column($empAttendanceLog, 'punch_date');
						
						if(!empty($empPunches[0]))
						{
							$empPunches = explode(',',$empPunches[0]);
							
							$ts_time = array_column($empAttendanceLog, 'ts_time')[0]; // Total Shift Time
							$lunch_time = array_column($empAttendanceLog, 'lunch_time')[0];
							$lunch_start = array_column($empAttendanceLog, 'lunch_start')[0];
							$lunch_end = array_column($empAttendanceLog, 'lunch_end')[0];
							$shift_type = array_column($empAttendanceLog, 'shift_type')[0];
							$shift_name = array_column($empAttendanceLog, 'shift_name')[0];
							$xmins = array_column($empAttendanceLog, 'xmins')[0];
							$startLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_start));
							$endLunch = date('d-m-Y H:i:s', strtotime($currentDate.' '.$lunch_end));
							
							$sortType = ($shift_type == 1) ? 'ASC' : 'ASC';
							$empPunches = sortDates($empPunches,$sortType);
							$punch_in = date('H:i', strtotime(min($empPunches)));
							$punch_out = date('H:i', strtotime(max($empPunches)));
							
							$lunch_in = '--:--';
							$lunch_out = '--:--';
							$totalPunches = count($empPunches);
							if (intVal($totalPunches) > 2) :
								$lunch_in = date('H:i', strtotime($empPunches[1]));
								if (intVal($totalPunches) > 3) :
									$lunch_out = date('H:i', strtotime($empPunches[2]));
								endif;
							endif;
							
							$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$present_status = 'P';
							// Count Total Time [1-2,3-4,5-6.....]
							foreach($empPunches as $punch)
							{
								$wph[$idx][]=strtotime($punch);
								if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							$twh = $stay_time;
							
							// Reduce Lunch Time
							if((strtotime(min($empPunches)) < strtotime($startLunch)) AND (strtotime(max($empPunches)) > strtotime($endLunch)))
							{
								$countedLT = 0;
								if(count($empPunches) > 2){$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;}
								if($countedLT > $lunch_time){$lunch_time = $countedLT;}
								$twh = $twh - $lunch_time;
							}
							
							// Get Extra Hours
							$exTime = 0;
							if(!empty($xmins)){$exTime = intVal($xmins) * 60;$twh += $exTime;}
							
							// Count Overtime and Working Time as per shift
							if($twh > $ts_time){$wh = $ts_time;$ot = $twh - $ts_time;}else{$wh = $twh;}
							
							$totalWH += $wh;$totalOT += $ot;$totalTWH += $twh;
							
							$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $punch_in . '</td>';
							$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $lunch_in . '</td>';
							$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $lunch_out . '</td>';
							$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . $punch_out . '</td>';
							$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . s2hi($wh) . '</td>';
							$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">' . s2hi($ot) . '</td>';
							$dayStatus = 'P';
							if ($day == 'Wed') {$dayStatus = 'PW';}
							$status .= '<th style="border:1px solid #888;text-align:center;color:#00aa00; font-size:12px;width:40px;">'.$dayStatus.'</th>';
							

							$present++;
						}
						else{
							$attend_status = false;
							$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
							$dayStatus = 'WO';
							if ($day != 'Wed') {$dayStatus = 'A';$absent++;}
							$status .= '<th style="border:1px solid #888;text-align:center;color:#cc0000;font-size:12px;width:40px;">'.$dayStatus.'</th>';
							
						}
					}

					$inData .= '</tr>';
					$outData .= '</tr>';
					$lunchInData .= '</tr>';
					$lunchOutData .= '</tr>';
					$workHrs .= '</tr>';
					$otData .= '</tr>';
					$status .= '</tr>';

					$empTable = '<table class="table-bordered" style="border:1px solid #888;margin-bottom:10px;">';
					$empTable .= '<tr style="background:#eeeeee;">';
					//$empTable .= '<th style="border:1px solid #888;font-size:12px;">Empcode</th>';
					$empTable .= '<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="'.($lastDay - 15).'">' . $emp->emp_code . ' - ' . $emp->emp_name . '</th>';
					//$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">Name</th>';
					//$empTable .= '<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="' . ($lastDay - 18) . '">' . $emp->emp_name . '</th>';
					$empTable .= '<th style="border:1px solid #888;color:#00aa00;font-size:12px;" colspan="3">Present : ' . $present . '</th>';
					$empTable .= '<th style="border:1px solid #888;color:#cc0000;font-size:12px;" colspan="3">Absent : ' . $absent . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">LV : ' . $leave . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="2">WO : ' . $wo . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="3">WH : ' . s2hi($totalTWH) . '</th>';
					$empTable .= '<th style="border:1px solid #888;font-size:12px;" colspan="3">Total OT : ' . s2hi($totalOT) . '</th>';
					$empTable .= '</tr>';

					$empTable .= '<tr><td rowspan="2" style="border:1px solid #888;font-size:12px;text-align:center;">#</td>' . $theadDate . '</tr>';
					$empTable .= '<tr>' . $theadDay . '</tr>';
					$empTable .= $inData . $lunchInData . $lunchOutData . $outData . $workHrs . $otData . $status;
					$empTable .= '</table>';
					$response .= $empTable;
					if ($empCount == 4) {
						$pageData[] = $response;
						$response = '';
						$empCount = 1;
					} else {$empCount++;}
				}
			}
			$pageData[] = $response;
			//print_r($pageData);exit;
			if ($file_type == 'excel') {
				//$xls_filename = 'monthlyAttendance.xls';
				$xls_filename = 'monthlyAttendance_'.$dept_name.'_v2.xls';

				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename=' . $xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				foreach ($pageData as $page) :
					echo $page;
				endforeach;
			} else {
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">' . $companyData->company_name . '</td>
											<td class="text-right pad-right-10"><b>Report Month : ' . date("F-Y", strtotime($postData['month'])) . '</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';

				// $mpdf = new \Mpdf\Mpdf();
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName = 'monthlyAttendance.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet, 1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));

				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);

				foreach ($pageData as $page) :
					$mpdf->AddPage('L', '', '', '', '', 5, 5, 17, 10, 5, 0, '', '', '', '', '', '', '', '', '', 'A4-L');
					$mpdf->WriteHTML($page);
				endforeach;
				$mpdf->Output($pdfFileName, 'I');
			}
		}
	}
	
	/*** Migrate Attendnace Data Date Wise By JP @ 27.02.2023 ***/
	public function migrateAttendanceDateWise($dates,$biomatric_id="ALL"){
		set_time_limit(0);
		if(!empty($dates)):
			$i=0;
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			//$empData = $this->employee->getEmpListForReport(['biomatric_id'=>$biomatric_id]);
			$empData = $this->employee->getEmpList(['biomatric_id'=>$biomatric_id,'emp_role'=>'-1']);
			//print_r($empData);exit;
			$current_month  = date("m");
			$month  = date("m",strtotime($duration[0]));
			$year  = date("Y",strtotime($duration[0]));
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));			
			
			$empCount = 1;
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$dateRange = new DatePeriod($begin, $interval ,$end);
			//print_r($dateRange);exit;
			$postData = ['dateRange'=>$dateRange,'empData'=>$empData];
			$result = $this->biometric->saveAlogSummaryData($postData);
			
			print_r($result);exit;
		else:
			echo "Something hoes Wrong...!";
		endif;
	}
	
	/*** New Summary Report By JP @ 25.02.2023 replace printMonthlySummary_old ***/
	public function printMonthlySummary($dates,$biomatric_id="ALL",$file_type = 'excel'){
		set_time_limit(0);
		if(!empty($dates)){
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			$empAttendanceLog = $this->biometric->getDateWiseSummaryV2(['from_date'=>$duration[0],'to_date'=>$duration[1],'emp_code'=>$biomatric_id]);
			
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));			
			
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$dateRange = new DatePeriod($begin, $interval ,$end);

			$empTable='';$aLogData=Array();$ltCount=0;$eoCount=0;$ltPenalty=0;$eoPenalty=0;
			foreach($empAttendanceLog as $row)
			{
				$shift_name = '';$allPunches = '';$allPunches1 = '';
				//if(!empty($row->emp_joining_date) AND (strtotime($row->emp_joining_date) <= strtotime($currentDate)))
				//{
					$empPunches = explode(',',$row->punch_date);
					$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
					$empPunches = sortDates($empPunches,$sortType);					
					
					$currentDay = date('D', strtotime($row->attendance_date));
					$xmins = $row->ex_mins;
					$startLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_start));
					$endLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_end));
					
					$shift_start = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->shift_start));
					$nextDate = ($row->shift_type == 2) ? date('Y-m-d', strtotime($row->attendance_date . ' +1 day')) :$row->attendance_date;
					$shift_end = date('d-m-Y H:i:s', strtotime($nextDate.' '.$row->shift_end));
					$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
					$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));					
					
					$ap = Array();
					foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
					$allPunches1 = implode(', ',$ap);
					
					$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$xtime='';$status='';$ps='';
					$late_in_s = 0;$early_in_s = 0;$early_out_s = 0;$late_out_s = 0;$lastp_index = count($empPunches)-1;	
					
					// Count Early In, Late In, Early Out, Late Out Time in Seconds
					if(strtotime($punch_in) < strtotime($shift_start)){$early_in_s = strtotime($shift_start) - strtotime($punch_in);}
					if(strtotime($punch_in) > strtotime($shift_start)){$late_in_s = strtotime($punch_in) - strtotime($shift_start);}
					if(strtotime($punch_out) < strtotime($shift_end)){$early_out_s = strtotime($shift_end) - strtotime($punch_out);}
					if(strtotime($punch_out) > strtotime($shift_end)){$late_out_s = strtotime($punch_out) - strtotime($shift_end);}
					
					
					// Apply Attendace Policy
					{
						// Apply Late In Policy
						if($late_in_s > ($row->ltp_minute*60)){$ltCount++;}
						if($ltCount > $row->ltp_days){$twh -= ($row->ltp_phrs*3600);$ltPenalty += $row->ltp_phrs;$ltCount--;}
						
						// Apply Early Out Policy
						if($early_out_s > ($row->eop_minute*60)){$eoCount++;}
						if($eoCount > $row->eop_days){$twh -= ($row->eop_phrs*3600);$eoPenalty += $row->eop_phrs;$eoCount--;}
					}
					
					// Trim Early In Punch
					if($early_in_s <= ($row->ru_in_time*60)){$empPunches[0] = date('Y-m-d H:i:s', strtotime($empPunches[0]) + $early_in_s );}
					// Trim Late In Punch
					if($late_in_s <= ($row->rd_in_time*60)){$empPunches[0] = date('Y-m-d H:i:s', strtotime($empPunches[0]) - $late_in_s);}
					
					// Trim Early Out Punch
					if($early_out_s <= ($row->ru_out_time*60)){$empPunches[$lastp_index] = date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) + $early_out_s);}
					// Trim Late Out Punch
					if($late_out_s <= ($row->rd_out_time*60)){$empPunches[$lastp_index] = date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) - $late_out_s);}
					$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
					$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
					
					$ap = Array();
					foreach($empPunches as $p){$ap[] = date("H:i:s",strtotime($p));}
					$allPunches = implode(', ',$ap);
					
					// Count Overtime as per shift
					if(strtotime($punch_out) > strtotime($shift_end)){$ot = strtotime($punch_out) - strtotime($shift_end);}
					$row->ot_mins = (empty($row->adjust_to)) ? $row->ot_mins : 0;
					
					// Count Total Time [1-2,3-4,5-6.....]
					foreach($empPunches as $punch)
					{
						$wph[$idx][]=strtotime($punch);
						if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
						$t++;
					}
					$twh = $stay_time;
					
					// Reduce Lunch Time
					if((strtotime($punch_in) < strtotime($startLunch)) AND (strtotime($punch_out) > strtotime($endLunch)))
					{
						$countedLT = 0;$xlt = 0;
						if(count($empPunches) > 2)
						{
							$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);
							if($countedLT > $row->lunch_time)
							{
								$row->lunch_time = $countedLT;
							}
							else
							{
								$xlt = $row->lunch_time - $countedLT;
								$twh -= $xlt;
							}
						}
						else
						{
							$twh -= $row->lunch_time;
						}
					}else{$row->lunch_time=0;}
					
					// Get Extra Hours
					$xtime = '<td style="text-align:center;font-size:12px;">--:--</td>';    							
					if(!empty($row->ex_mins))
					{
						$textStyle= ($row->ex_mins < 0) ? "color:#aa0000;font-weight:bold;" : "";
						$xtime = '<td style="text-align:center;font-size:12px;'.$textStyle.'">'.formatSeconds(abs($row->ex_mins),'H:i').'</td>';
						
					}
					$twh -= $ot;
					$wh = $twh;
					$twh += $row->ot_mins;
					$twh += $row->ex_mins;
					$twh += $row->adj_mins;
					$twh1 = $twh;
					
					$trimSeconds = 1800;$upperTrim = 1500;$lowerTrim = 300;// Round Punch By Seconds
					$trimDay = $twh % $trimSeconds;
					if($trimDay < $upperTrim){$twh -= $trimDay;}else{$twh += ($trimSeconds-$trimDay);}
										
					// Set Present/Absent/ Status					
					$ps='A';$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$ps.'</td>';
					if($currentDay == 'Wed'){$ps = 'WO';}
					if(count($empPunches) > 0)
					{
						if($twh > 0){$ps='P';}else{$ps='A';}
						if(count($empPunches) % 2 != 0){$ps='M';}
					}
					
					if($ps == 'M'){$status = '<td style="text-align:center;color:#233288;font-size:12px;">'.$ps.'</td>';}
					if($ps == 'WO'){$status = '<td style="text-align:center;color:#000000;font-size:12px;">'.$ps.'</td>';}
					if($ps == 'P'){$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">'.$ps.'</td>';}
					
					
					$workHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($wh,'H:i').'</td>';
					$ltTd = '<td style="text-align:center;font-size:12px;">'.formatSeconds($row->lunch_time,'H:i').'</td>';
					$exHrs = $xtime;
					$otData = '<td style="text-align:center;font-size:12px;">'.formatSeconds($ot,'H:i').'</td>';
					$aotData = '<td style="text-align:center;font-size:12px;">'.formatSeconds($row->ot_mins,'H:i').'</td>';
					$adjTime = '<td style="text-align:center;font-size:12px;">'.formatSeconds($row->adj_mins,'H:i').'</td>';
					$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($twh,'H:i').'</td>';
					$totalWorkHrs .= '<td style="text-align:center;font-size:12px;">'.formatSeconds($twh1,'H:i').'</td>';
					
					
					if($ps == 'A')
					{
						$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$ps.'</td>';
						$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$ltTd = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
						$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$aotData = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$adjTime = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td><td></td>';
					}
					
				// }
				// else
				// {
					// $shift_name = 'NA';$allPunches='NA';
					// $status = '<td style="text-align:center;;font-size:12px;">NA</td>';
					// $workHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
					// $ltTd = '<td style="text-align:center;font-size:12px;">NA</td>';
					// $exHrs= '<td style="text-align:center;font-size:12px;">NA</td>';
					// $otData = '<td style="text-align:center;font-size:12px;">NA</td>';
					// $totalWorkHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
				// }
				$empTable = '';
				$empTable .='<tr>';
					$empTable .='<td style="text-align:center;font-size:12px;">'.$row->emp_code.'</td>';
					$empTable .='<td style="text-align:left;font-size:12px;">'.$row->emp_name.'</td>';
					$empTable .='<td style="font-size:12px;">'.$row->dept_name.'</td>';
					$empTable .='<td style="font-size:12px;">'.$row->shift_name.'</td>';
					$empTable .='<td style="font-size:12px;">'.$currentDay.'</td>';
					$empTable .='<td style="font-size:12px;">'.date("d-m-Y",strtotime($row->attendance_date)).'</td>';
					$empTable .= $status.$workHrs.$ltTd.$exHrs.$otData.$aotData.$adjTime.$totalWorkHrs;
					$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches.'</td>';
					$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches1.'</td>';
				$empTable .='</tr>';
				$aLogData[$row->emp_id][date("dmY",strtotime($row->attendance_date))]['htmlData']=$empTable;
				$aLogData[$row->emp_id][date("dmY",strtotime($row->attendance_date))]['lastShift']=$row->shift_name;		
			}
			$empTable = '';$lastShift = '';
			$empData = $this->employee->getEmpListForReport(['biomatric_id'=>$biomatric_id,'currentDate'=>$duration[0]]);
			
			foreach($empData as $row)
			{
				$lastShift = $row->lastShift;
				foreach($dateRange as $date)
				{
					$dateKey =  date("dmY",strtotime($date->format("Y-m-d")));
					$currentDate =  date("d-m-Y",strtotime($date->format("Y-m-d")));
					$currentDay =  date("D",strtotime($date->format("Y-m-d")));
					if(!empty($aLogData[$row->id][$dateKey]))
					{
						$empTable .= $aLogData[$row->id][$dateKey]['htmlData'];
						$lastShift = $aLogData[$row->id][$dateKey]['lastShift'];
					}
					else
					{
						$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';
						if($currentDay == 'Wed'){$status = '<td style="text-align:center;color:#000000;font-size:12px;">WO</td>';}
						$empTable .='<tr>';
							$empTable .='<td style="text-align:center;font-size:12px;">'.$row->emp_code.'</td>';
							$empTable .='<td style="text-align:left;font-size:12px;">'.$row->emp_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$row->dept_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$lastShift.'</td>';
							$empTable .='<td style="font-size:12px;">'.$currentDay.'</td>';
							$empTable .='<td style="font-size:12px;">'.$currentDate.'</td>';
							$empTable .= $status;
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$empTable .= '<td style="text-align:center;font-size:12px;">--:--</td><td></td>';
							$empTable .='<td style="font-size:12px;text-align:left;"></td>';
							$empTable .='<td style="font-size:12px;text-align:left;"></td>';
						$empTable .='</tr>';
					}
					
				}
			}
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th style="width:80px;">Emp Code</th>
							<th>Employee</th>
							<th>Department</th>
							<th>Shift</th>
							<th>Day</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>Lunch</th>
							<th>Ex.Hrs</th>
							<th>OT</th>
							<th>AOT</th>
							<th>Adj. Time</th>
							<th>TWH</th>
							<th>ATWH</th>
							<th>All Pucnhes</th>
							<th>Actual Punch</th>
						</tr></thead><tbody>'.$empTable.'</tbody></table>';
			//echo $response;exit;
			if($file_type == 'excel')
			{
				$xls_filename = 'monthlyAttendance.xls';
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment; filename='.$xls_filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				
				echo $response;
			}
			else
			{
				$htmlHeader = '<div class="table-wrapper">
									<table class="table txInvHead">
										<tr class="txRow">
											<td class="fs-17 text-left" style="letter-spacing: 1px;font-weight:bold;">'.$companyData->company_name.'</td>
											<td class="text-right pad-right-10"><b>Report Period : '.date("d.m.Y",strtotime($duration[0]).'-'.date("d.m.Y",strtotime($duration[1]))).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = new \Mpdf\Mpdf();
				$pdfFileName='attendanceSummary.pdf';
				$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
				$mpdf->WriteHTML($stylesheet,1);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->SetProtection(array('print'));
				
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($response);
				$mpdf->Output($pdfFileName,'I');
			}
		}
	}
	
	/*** New Summary Report By JP @ 25.02.2023 ***/
	public function getSalaryHours($from_date,$biomatric_id="ALL"){		
		$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
		$empAttendanceLog = $this->biometric->getSalaryHours(['from_date'=>$from_date,'emp_code'=>$biomatric_id]);
		
		print_r($empAttendanceLog);exit;
		return $empAttendanceLog;
	}
	
	/*** New Summary Report By JP @ 25.02.2023 ***/
	public function getAttendanceLogV2($dates,$biomatric_id="ALL"){
		$duration = explode('~',$dates);
		$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
		$empAttendanceLog = $this->biometric->getAttendanceLogV2(['from_date'=>$duration[0],'to_date'=>$duration[1],'emp_code'=>$biomatric_id]);
		print_r($empAttendanceLog);exit;
		foreach($empAttendanceLog['empSal'] as $sdt)
		{
			foreach($sdt as $sdt1)
			{
				print_r(round(($sdt1/3600),2));print_r('<hr>');
			}
		}
		exit;
		print_r($empAttendanceLog);exit;
		return $empAttendanceLog;
	}
	
	
}
