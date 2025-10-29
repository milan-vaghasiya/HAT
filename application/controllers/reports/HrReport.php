<?php
class HrReport extends MY_Controller
{
    private $indexPage = "report/hr_report/index";
    private $emp_report = "report/hr_report/emp_report";
    private $monthlyAttendance = "report/hr_report/month_attendance";
    private $monthSummary = "report/hr_report/month_summary";
    private $monthlySummary = "report/hr_report/monthly_summary";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HR Report";
		$this->data['headData']->controller = "reports/hrReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/hr_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'HR REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

	public function empReport(){
        $this->data['pageHeader'] = 'LIST OF EMPLOYEE';
        $empData = $this->employee->getEmpReport();
        $i=1; $this->data['empData'] = ""; 
        foreach($empData as $row):
            $empEdu = $this->employee->getEmpEdu($row->id);
            $course = Array();
            foreach($empEdu as $edu):
                $course[] = $edu->course;
            endforeach;
            $this->data['empData'] .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->emp_name.'</td>
                <td>'.$row->title.'</td>
                <td>'.$row->emp_contact.'</td>
                <td>'.formatDate($row->emp_joining_date).'</td>
                <td>'.implode(', ',$course).'</td>
                <td>'.$row->emp_experience.'</td>
                <td></td>
            </tr>';
        endforeach;
        $this->load->view($this->emp_report,$this->data);
    }

    public function mismatchPunch(){        
        $this->data['pageHeader'] = 'MISMATCH PUNCH REPORT';
        $this->load->view("report/hr_report/mismatch_punch",$this->data);
    }

    public function getMismatchPunch(){
        $report_date = $this->input->post('report_date');
        $empData = $this->attendance->getMismatchPunchData($report_date);
        $html = "";
        foreach($empData as $row):
            $html .= '
                <tr>
                    <td>'.$row->emp_code.'</td>
                    <td>'.$row->emp_name.'</td>
                    <td>'.$row->department_name.'</td>
                    <td>'.$row->shift_name.'</td>
                    <td>'.$row->title.'</td>
                    <td>'.$row->category.'</td>
                    <td>'.$row->punch_time.'</td>
                    <td>'.$row->missed_punch.' <a href="#" class="float-right manualAttendance" data-empid="'.$row->id.'" data-adate="'.$report_date.'" data-button="both" data-modal_id="modal-lg" data-function="addManualAttendance" data-form_title="Add Manual Attendance"> Add</a></td>
                </tr>
            ';
        endforeach;
        $this->printJson(['status'=>1,'tbody'=>$html]);
    }

	public function monthlyAttendance(){
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->load->view($this->monthlyAttendance,$this->data);
    }

    public function printMonthlyAttendance($month,$file_type = 'excel'){
	
		set_time_limit(0);
		$empData = $this->attendance->getEmployeeList();
		$companyData = $this->attendance->getCompanyInfo();
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y_00:01",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y_11:59",strtotime($year.'-'.$month.'-01'));
		
		$fdate = date("Y-m-d 00:00:01",strtotime($year.'-'.$month.'-01'));
		$tdate  = date("Y-m-t 23:59:59",strtotime($year.'-'.$month.'-01'));
		
		$first_day = 1;
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		$punchData = NULL;
		$attendanceDataDB = $this->attendance->getEmployeePunchDataDB($fdate,$tdate);
		if(!empty($attendanceDataDB)){$punchData = $attendanceDataDB->punchdata;}
		$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
		if(empty($punchData)):
			$punchData = $this->attendance->saveBiometricData($fdate,$tdate);
		else:
			$punchData = json_decode($punchData);
		endif;
		
		$emp1 = Array();$response = '';$empTable='';$pageData = Array();
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';$wo = 0;$wh = 0;$wi = 0;$oth = 0;$oti = 0;
				$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';$workHrs = '';$otData = '';$status = '';
				$empPucnhes = array_keys(array_combine(array_keys($punchData), array_column($punchData, 'Empcode')),$ecode);
				
				$inData .= '<tr><th style="border:1px solid #888;font-size:12px;">IN</th>';
				$lunchInData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-START</th>';
				$lunchOutData .= '<tr><th style="border:1px solid #888;font-size:12px;">L-END</th>';
				$outData .= '<tr><th style="border:1px solid #888;font-size:12px;">OUT</th>';
				$workHrs .= '<tr><th style="border:1px solid #888;font-size:12px;">WH</th>';
				$otData .= '<tr><th style="border:1px solid #888;font-size:12px;">OT</th>';
				$status .= '<tr><th style="border:1px solid #888;font-size:12px;">STATUS</th>';
				for($d=1;$d<=$last_day;$d++)
				{
					$attend_status = false;
					$currentDate = date('d/m/Y', strtotime($year.'-'.$month.'-'.$d));$punchDates = Array();
					$day = date("D",strtotime($year.'-'.$month.'-'.$d));if($day == 'Wed'){$wo++;}
					$theadDate .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">'.$d.'</th>';
					$theadDay .= '<th style="border:1px solid #888;text-align:center;font-size:12px;">'.$day.'</th>';
					
					if(!empty($empPucnhes))
					{
						foreach($empPucnhes as $punch)
						{
							$todayPunch = $punchData[$punch];
							if($currentDate == date('d/m/Y', strtotime(strtr($todayPunch->PunchDate, '/', '-')))) 
							{
								$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
							}
						}
					}
					if(!empty($punchDates))
					{
						$attend_status = true;
						$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
						$shiftStart = date('d-m-Y H:i:s', strtotime($d.'-'.$month.'-'.$year.' '.$emp->shift_start));
						$shiftEnd = date('d-m-Y H:i:s', strtotime('+8 hours',strtotime($shiftStart)));
						$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
						if(strtotime($punch_in) > strtotime($shiftEnd))
						{
							$shiftEnd = date('d-m-Y H:i:s', strtotime($year.'-'.$month.'-'.$d.' 23:59:59'));
						}
						if( count($punchDates) == 1 ):
							$punch_out = $shiftEnd;
						endif;
						
						$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
						$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
						$interval = $time1->diff($time2);
						$total_hours = $interval->format('%H:%I:%S');
						$total_is = $interval->format('%I:%S');
						$overtime = floatVal($total_hours) - floatVal($emp->total_shift_time);
						$wh += floatVal($interval->h);
						$wi += floatVal($interval->format('%I'));
						if(empty($overtime) or $overtime < 0){$overtime='--:--';}else{$overtime = date('H:i', strtotime($overtime.':'.$total_is));}
						
						$punch_in = date('H:i', strtotime($punch_in));
						$punch_out = date('H:i', strtotime($punch_out));
						$total_hours = date('H:i', strtotime($total_hours));
						
						if($day == 'Wed'){$total_hours = '--:--';$overtime = date('H:i', strtotime($total_hours));}
						
						$sortPunches = sortDates($punchDates);
						$lunch_in = '--:--';$lunch_out = '--:--';$totalPunches = count($sortPunches);$linIdx = $totalPunches - 1;
						if(intVal($totalPunches) > 2):
							$lunch_in = date('H:i', strtotime($sortPunches[1]));
							if(intVal($totalPunches) > 3):
								$lunch_out = date('H:i', strtotime($sortPunches[2]));
							endif;
						endif;
						
						$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$punch_in.'</td>';
						$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$lunch_in.'</td>';
						$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$lunch_out.'</td>';
						$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$punch_out.'</td>';
						$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$total_hours.'</td>';
						$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">'.$overtime.'</td>';
						$status .= '<th style="border:1px solid #888;text-align:center;color:#00aa00;font-size:12px;width:40px;">P</th>';
						
						$present++;
					}
					else
					{
						$attend_status = false;
						$inData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$lunchInData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$lunchOutData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$outData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$workHrs .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$otData .= '<td style="border:1px solid #888;text-align:center;font-size:12px;width:40px;">--:--</td>';
						$status .= '<th style="border:1px solid #888;text-align:center;color:#cc0000;font-size:12px;width:40px;">A</th>';
						$absent++;
					}
				}
				
				$inData .= '</tr>';$outData .= '</tr>';$lunchInData .= '</tr>';
				$lunchOutData .= '</tr>';$workHrs .= '</tr>';$otData .= '</tr>';$status .= '</tr>';
				
				$wh = $wh + intVal($wi / 60);$wi = intVal(floatVal($wi) % 60);$wh = $wh.':'.$wi;
				
				$empTable = '<table class="table-bordered" style="border:1px solid #888;margin-bottom:10px;">';
				$empTable .='<tr style="background:#eeeeee;">';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">Empcode</th>';
					$empTable .='<th style="border:1px solid #888;text-align:center;font-size:12px;" colspan="2">'.$ecode.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">Name</th>';
					$empTable .='<th style="border:1px solid #888;text-align:left;font-size:12px;" colspan="'.($last_day - 20).'">'.$emp->emp_name.'</th>';
					$empTable .='<th style="border:1px solid #888;color:#00aa00;font-size:12px;" colspan="2">Present</th>';
					$empTable .='<th style="border:1px solid #888;color:#00aa00;font-size:12px;">'.$present.'</th>';
					$empTable .='<th style="border:1px solid #888;color:#cc0000;font-size:12px;" colspan="2">Absent</th>';
					$empTable .='<th style="border:1px solid #888;color:#cc0000;font-size:12px;">'.$absent.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">LV</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$leave.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">WO</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$wo.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">WH</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">'.$wh.'</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;" colspan="2">Total OT</th>';
					$empTable .='<th style="border:1px solid #888;font-size:12px;">'.$oth.'</th>';
				$empTable .='</tr>';
					
				$empTable .='<tr><td rowspan="2" style="border:1px solid #888;font-size:12px;text-align:center;">#</td>'.$theadDate.'</tr>';
				$empTable .='<tr>'.$theadDay.'</tr>';
				$empTable .= $inData.$lunchInData.$lunchOutData.$outData.$workHrs.$otData.$status;
				$empTable .= '</table>';
				$response .= $empTable;
				if($empCount == 4){$pageData[] = $response;$response='';$empCount=1;}else{$empCount++;}
			}
		}
		
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
										<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = $this->m_pdf->load();
			$pdfFileName='monthlyAttendance.pdf';
			$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
			$mpdf->WriteHTML($stylesheet,1);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->SetProtection(array('print'));
			
			$mpdf->SetHTMLHeader($htmlHeader);
			$mpdf->SetHTMLFooter($htmlFooter);
			
			foreach($pageData as $page):
				$mpdf->AddPage('L','','','','',5,5,17,10,5,0,'','','','','','','','','','A4-L');
				$mpdf->WriteHTML($page);
			endforeach;
			$mpdf->Output($pdfFileName,'I');
		}
        
    }

	public function monthlyAttendanceSummary(){
        $this->data['empList'] = $this->employee->getEmployeeList(1);
        $this->load->view($this->monthSummary,$this->data);
    }

    public function printMonthlySummary1($month,$file_type = 'excel'){
	
		set_time_limit(0);
		$empData = $this->attendance->getEmployeeList('',1);
		$companyData = $this->attendance->getCompanyInfo();
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y_00:01",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y_23:59",strtotime($year.'-'.$month.'-01'));
		
		$fdate = date("Y-m-d 00:00:01",strtotime($year.'-'.$month.'-01'));
		$tdate  = date("Y-m-t 23:59:59",strtotime($year.'-'.$month.'-01'));
		
		$first_day = 1;
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		$punchData = NULL;
		// $attendanceDataDB = $this->attendance->getEmployeePunchDataDB($fdate,$tdate);
		// if(!empty($attendanceDataDB)){$punchData = $attendanceDataDB->punchdata;}
		$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
		// if(empty($punchData)):
		// 	$punchData = $this->attendance->saveBiometricData($fdate,$tdate);
		// else:
		// 	$punchData = json_decode($punchData);
		// endif;
		$punchData = $this->attendance->saveBiometricData($fdate,$tdate);
        $empTable = '';
		$emp1 = Array();$response = '';$pageData = Array();
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);				
				$empPucnhes = array_keys(array_combine(array_keys($punchData), array_column($punchData, 'Empcode')),$ecode);
				
				for($d=1;$d<=$last_day;$d++)
				{
					$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';
					$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';$punches = '';
					$currentDate = date('d/m/Y', strtotime($year.'-'.$month.'-'.$d));$punchDates = Array();$punchTimes = Array();
                    $today = date("d-m-Y",strtotime($year.'-'.$month.'-'.$d));
					$day = date("D",strtotime($year.'-'.$month.'-'.$d));if($day == 'Wed'){$wo++;}
					
					// Get Device Punches
					if(!empty($empPucnhes))
					{
						foreach($empPucnhes as $punch)
						{
							$todayPunch = $punchData[$punch];							
							if($currentDate == date('d/m/Y', strtotime(strtr($todayPunch->PunchDate, '/', '-')))) 
							{
								$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
								$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
							}
							
						}						
					}
					// Get Manual Punches
					$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($today)),$emp->id);
					if(!empty($mpData))
					{
						foreach($mpData as $mpRow):
							$time = explode(" ",$mpRow->punch_in);
							$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
							$punchTimes[] = date("H:i:s",strtotime($time[1]));
						endforeach;
					}
					
					if(!empty($punchDates))
					{
						$attend_status = true;
						$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
						$shiftStart = date('d-m-Y H:i:s', strtotime($d.'-'.$month.'-'.$year.' '.$emp->shift_start));
						$shiftEnd = date('d-m-Y H:i:s', strtotime('+8 hours',strtotime($shiftStart)));
						$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
						if(strtotime($punch_in) > strtotime($shiftEnd))
						{
							$shiftEnd = date('d-m-Y H:i:s', strtotime($year.'-'.$month.'-'.$d.' 23:59:59'));
						}
						if( count($punchDates) == 1 ):
							$punch_out = $shiftEnd;
						endif;

						$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
						$late = ($punch_in > $late_in) ? 'Y' : '';
						
						$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
						$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
						$interval = $time1->diff($time2);
						$total_hours = $interval->format('%H:%I:%S');
						$total_is = $interval->format('%I:%S');
						
						$punch_in = date('H:i', strtotime($punch_in));
						$punch_out = date('H:i', strtotime($punch_out));
						$total_hours = date('H:i', strtotime($total_hours));
						
						// Total Hours Calculation
						$totalHrs = explode(':',$total_hours);
						$whrs = 0;
						if(intVal($totalHrs[0]) > 0 OR intVal($totalHrs[1]) > 0):
							$whrs = (intVal($totalHrs[0]) * 3600) + (intVal($totalHrs[1]) * 60);
						endif;

						// Shift Time Calculation
						$totalShiftTime = explode(':',$emp->total_shift_time);
						$stime = 0;
						if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
							$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
						endif;
						
						
						//if(empty($overtime) or $overtime < 0){$overtime='--:--';$ot=0;}else{$overtime = date('H:i', strtotime(($overtime * 3600)));}
						
						$all_puch = sortDates($punchTimes);
						$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
						foreach($all_puch as $punch)
						{
							$twh = 0;
							$tm = explode(':',$punch);
							if(intVal($tm[0]) > 0 OR intVal($tm[1]) > 0):
								$twh = (intVal($tm[0]) * 3600) + (floatVal($tm[1]) * 60);
							endif;
							
							$wph[$idx][]=$twh;
							if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
							$t++;
						}
						$punches = implode(', ',sortDates($punchTimes));
						
						$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
						
						$wh = intVal($TWHRS) - intVal($ot);
						
						$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
						$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
						$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
						
						$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">P</td>';
						$workHrs = '<td style="text-align:center;font-size:12px;">'.$work_hours.'</td>';
						$otData = '<td style="text-align:center;font-size:12px;">'.$ot.'</td>';
						$exOtHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$exHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.$TWHRS.'</td>';
						$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';	
					}
					else
					{
						$attend_status = false;
						$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';
						$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$exOtHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
						$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
						$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';
					}
					
					$empTable .='<tr>';
						$empTable .='<td style="text-align:center;font-size:12px;">'.$ecode.'</td>';
						$empTable .='<td style="text-align:left;font-size:12px;">'.$emp->emp_name.'</td>';
						$empTable .='<td style="font-size:12px;">'.$emp->name.'</td>';
								$empTable .='<td style="font-size:12px;">'.$emp->shift_name.'</td>';
						$empTable .='<td style="font-size:12px;">'.$today.'</td>';
						$empTable .= $status.$workHrs.$otData.$exOtHrs.$exHrs.$totalWorkHrs.$lateStatus;
						$empTable .='<td style="font-size:12px;text-align:left;">'.$punches.'</td>';
					$empTable .='</tr>';
					
				}
			}
		}
		
		$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
			$response .= '<thead>
					<tr style="background:#eee;">
						<th>Emp Code</th>
						<th>Employee</th>
						<th>Department</th>
						<th>Shift</th>
						<th>Punch Date</th>
						<th>Status</th>
						<th>WH</th>
						<th>OT</th>
						<th>Ex. OT</th>
						<th>Ex. Hours</th>
						<th>TWH</th>
						<th>Late</th>
						<th>All Pucnhes</th>
					</tr></thead><tbody>'.$empTable.'</tbody></table>';
		
		// echo $response;exit;
		
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
										<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = $this->m_pdf->load();
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
		}
        
    }

    public function printMonthlySummary($dates,$biomatric_id="ALL",$file_type = 'excel'){
		
		set_time_limit(0);
		if(!empty($dates))
		{
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			$empData = $this->attendance->getEmployeeList($biomatric_id);
// 			$empData = $this->attendance->getEmployeeList('20400');
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			$month  = date("m",strtotime($duration[0]));
			$year  = date("Y",strtotime($duration[0]));
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));
			
			$fdate = date("Y-m-d 00:00:01",strtotime($duration[0]));
			$tdate  = date("Y-m-d 23:59:59",strtotime($duration[1]));
			
			$first_day = date("d",strtotime($duration[0]));
			$last_day = date("d",strtotime($duration[1]));
			
			$empTable = '';
			$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			foreach($daterange as $date)
			{
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$currentDay = date('D', strtotime($currentDate));
				$punchData = New StdClass();
				$todayPunchData = $this->biometric->getPunchData($currentDate,$nextDate);
				
				$punches = Array();
				foreach($todayPunchData as $pnc)
				{
					$jarr = json_decode($pnc->punch_data);
					$punches = array_merge($punches,$jarr);
				}
				
				if(!empty($empData))
				{
					foreach($empData as $emp)
					{
    						$ecode = sprintf("%04d", $emp->biomatric_id);$punchDates = Array();$punchTimes = Array();$mflag=Array();
    						$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';$allPunches ='';$dorn=1;$present_status = 'P';
    						$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';
					    if(!empty($emp->emp_joining_date) AND (strtotime($emp->emp_joining_date) <= strtotime($currentDate)))
					    {
    						
    						// Get ShiftData
    						$shiftData = $this->shiftModel->getAttendanceLog($currentDate,$emp->id);
    						$emp->shift_start = $shiftData->shift_start;
    						$emp->shift_end = $shiftData->shift_end;
    						$emp->shift_id = $shiftData->shift_id;
    						$emp->shift_name = $shiftData->shift_name;
    						$emp->total_shift_time = $shiftData->total_shift_time;
    						$nextDayShiftData = $this->shiftModel->getAttendanceLog($nextDate,$emp->id);
    						$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start. ' -180 minutes'));
    						if($emp->shift_end < date('H:i:s', strtotime('11:00:00'))) // Night Shift
    						{
    							if(!empty($nextDayShiftData))
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
    							}
    							else
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
    							}
    							//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' 13:15:00'));
    							//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' 12:30:00'));
    							$dorn=2;
    						}
    						else
    						{
    							if(!empty($nextDayShiftData))
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
    							}
    							else
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
    							}
    							//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' 04:30:00'));
    							//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' 04:00:00'));
    						}
    						//print_r($nextDayShiftData);
    						//print_r($emp->shift_end.'@@@'.$shiftStart.' *** '.$shiftEnd);
    						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
    						if(!empty($empPucnhes))
    						{
    							foreach($empPucnhes as $punch)
    							{
    								$todayPunch = $punches[$punch];	
    								$pnchDate = date('d-m-Y H:i:s', strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    								if((strtotime($pnchDate) >= strtotime($shiftStart)) AND (strtotime($pnchDate) <= strtotime($shiftEnd)))
    								//if(($pnchDate >= $shiftStart))
    								{
    									$punchDates[]=date('Y-m-d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    									$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    									$mflag[]='S';
    								}
    							}
    						}
    						// Get Manual Punches
    						$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
    						$mpDataND = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($nextDate)),$emp->id);
    						$mpData = array_merge($mpData,$mpDataND);
    						if(!empty($mpData))
    						{
    							foreach($mpData as $mpRow):
    								$time = explode(" ",$mpRow->punch_in);
    								
    								$pDate = date('Y-m-d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
                					if((strtotime($pDate) >= strtotime($shiftStart)) AND (strtotime($pDate) <= strtotime($shiftEnd)))
                					{
                					    $punchDates[]=$pDate;
        								$punchTimes[] = date("H:i:s",strtotime($time[1]));
        								$mflag[]=$pDate;
                					}
    							endforeach;
    						}
    						
    						if(!empty($punchDates))
    						{
    							$attend_status = true;if($currentDay == 'Wed'){$present_status = 'WP';}
    							$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
    							$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
    							if( count($punchDates) == 1 ):
    								$punch_out = $shiftEnd;
    							endif;
    
    							$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
    							$late = ($punch_in > $late_in) ? 'Y' : '';
    							
    							$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
    							$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
    							$interval = $time1->diff($time2);
    							$total_hours = $interval->format('%H:%I:%S');
    							$total_is = $interval->format('%I:%S');
    							
    							$punch_in = date('H:i', strtotime($punch_in));
    							$punch_out = date('H:i', strtotime($punch_out));
    							$total_hours = date('H:i', strtotime($total_hours));
    							
    							// Total Hours Calculation
    							$totalHrs = explode(':',$total_hours);
    							// Get Extra Hours
    							$exHrsTime = '';$exTime = 0;
    							$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
    							
    							if(!empty($exHrsData))
    							{
    								$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
    								$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
    								$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
    								
    								if($exh < 0 OR $exm < 0):
    								    $exHrsTime = '<td style="text-align:center;color:#aa0000;font-size:12px;font-weight:bold;">'.abs($exh).' H : '.abs($exm).' M</td>';
    								else:
    								    $exHrsTime = '<td style="text-align:center;font-size:12px;">'.abs($exh).' H : '.abs($exm).' M</td>';
    								endif;
    							}
    							// Shift Time Calculation
    							$totalShiftTime = (!empty($emp->total_shift_time)) ? explode(':',$emp->total_shift_time) : explode(':','08:45');
    							$stime = 0;$stime = 0;//print_r($totalShiftTime);print_r(' = '.$emp->emp_code.'@'.$currentDate.'<br>');
    							if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
    								$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
    							endif;
    							
    							//$all_puch = sortDates($punchTimes);
    							$all_puch = sortDates($punchDates);
    							$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
    							foreach($all_puch as $punch)
    							{
    								$wph[$idx][]=strtotime($punch);
    								if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
    								$t++;
    							}
    							
    							// Count Lunch Time (If Lunch time > 45 Mins then Lunch Time Time is Actual Otherwise 45 Mins Fixed
    							$countedLT =0;$ltime = 0;
    							if(count($wph) >= 2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
    							$halfDayTime = intval($stime/2) + 60;
    							$lunchTime = 0;
    							if((intVal($TWHRS) > $halfDayTime) OR (count($wph) >= 2)){$lunchTime = 2700;}
    							//if(count($wph) > 2){$lunchTime = 2700;}
    							$ltime = $lunchTime;
    							if($countedLT < 2700){$lunchTime -= $countedLT;}else{$lunchTime = 0;$ltime = $countedLT;}
    							$TWHRS = $TWHRS - $lunchTime;
    							
    							$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
    							
    							$wh = intVal($TWHRS) - intVal($ot);
    							
    							$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
    							$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
    							$TWHRS += $exTime;$totalWorkTime = $TWHRS;
    							$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
    							$lunchTime = floor($ltime / 3600) .':'. floor($ltime / 60 % 60);
    							
    							// $allPunchDates = implode(', ',sortDates($punchDates));
    							// if($dorn==2){$allPunches = implode(', ',sortDates($allPunchDates,'DESC'));}
    							$allPunches = '';
    							if(!empty($punchDates))
    							{
    								$allPunchDates = ($dorn==2) ? sortDates($punchDates) : sortDates($punchDates);
    								$ap = Array();
    								foreach($allPunchDates as $p)
    								{
    									$spanTag = '';
    									if(in_array($p,$mflag)){$ap[] = '<b>'.date("d H:i:s",strtotime($p)).'</b>';}
    									else{$ap[] = date("d H:i:s",strtotime($p));}
    								}
    								$allPunches = implode(', ',$ap);
    							}
    							
    							
    							// Check For Missed Punch
    							if(count($punchTimes) % 2 != 0)
    							{
    								$status = '<td style="text-align:center;color:#233288;font-size:12px;">M</td>';
    							}
    							else
    							{
    								if($totalWorkTime <= 0){$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';}
    								else{$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">'.$present_status.'</td>';}
    							}
    							$workHrs = '<td style="text-align:center;font-size:12px;">'.$work_hours.'</td>';
    							$ltTd = '<td style="text-align:center;font-size:12px;">'.$lunchTime.'</td>';
    							$exHrs = $exHrsTime;//'<td style="text-align:center;font-size:12px;">'.$exHrsTime.'</td>';
    							$otData = '<td style="text-align:center;font-size:12px;">'.$ot.'</td>';
    							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.$TWHRS.'</td>';
    							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';	
    						} 
    						else
    						{
    							$attend_status = false;if($currentDay == 'Wed'){$present_status = 'W';}else{$present_status='A';}
    							$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$present_status.'</td>';
    							$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$ltTd = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';
    						} 
					    }	
						else
						{
						    $emp->shift_name = 'NA';$allPunches='NA';
							$status = '<td style="text-align:center;;font-size:12px;">NA</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
							$ltTd = '<td style="text-align:center;font-size:12px;">NA</td>';
							$exHrs= '<td style="text-align:center;font-size:12px;">NA</td>';
							$otData = '<td style="text-align:center;font-size:12px;">NA</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
							$lateStatus = '<td style="text-align:center;font-size:12px;">NA</td>';
						}
					
						$empTable .='<tr>';
							$empTable .='<td style="text-align:center;font-size:12px;">'.$ecode.'</td>';
							$empTable .='<td style="text-align:left;font-size:12px;">'.$emp->emp_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->shift_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.date("d-m-Y",strtotime($currentDate)).'</td>';
							$empTable .= $status.$workHrs.$ltTd.$exHrs.$otData.$totalWorkHrs.$lateStatus;
							$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches.'</td>';
						$empTable .='</tr>';
					}
				}
			}
			
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th>Emp Code</th>
							<th>Employee</th>
							<th>Department</th>
							<th>Shift</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>Lunch</th>
							<th>Ex. Hours</th>
							<th>OT</th>
							<th>TWH</th>
							<th>Late</th>
							<th>All Pucnhes</th>
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
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = $this->m_pdf->load();
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
			}
			
		}
	}

	public function monthlyAttendanceSummaryNew(){
        $this->data['empList'] = $this->employee->getEmployeeList('emp_code');
        $this->load->view($this->monthlySummary,$this->data);
    }

    public function printMonthlySummaryNew($dates,$biomatric_id="ALL",$file_type = 'excel'){
		
		set_time_limit(0);
		if(!empty($dates))
		{
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			//$empData = $this->attendance->getEmployeeList($biomatric_id);
			$empData = $this->attendance->getEmployeeList('10057');
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			$month  = date("m",strtotime($duration[0]));
			$year  = date("Y",strtotime($duration[0]));
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));
			
			$fdate = date("Y-m-d 00:00:01",strtotime($duration[0]));
			$tdate  = date("Y-m-d 23:59:59",strtotime($duration[1]));
			
			$first_day = date("d",strtotime($duration[0]));
			$last_day = date("d",strtotime($duration[1]));
			
			$empTable = '';
			$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			foreach($daterange as $date)
			{
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$currentDay = date('D', strtotime($currentDate));
				$punchData = New StdClass();
				$todayPunchData = $this->biometric->getPunchData($currentDate,$nextDate);
				$punches = Array();
				foreach($todayPunchData as $pnc)
				{
					$jarr = json_decode($pnc->punch_data);
					$punches = array_merge($punches,$jarr);
				}
				
				if(!empty($empData))
				{
					foreach($empData as $emp)
					{
						$ecode = sprintf("%04d", $emp->biomatric_id);$punchDates = Array();$punchTimes = Array();$mflag=Array();
						$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';$allPunches ='';$dorn=1;$present_status = 'P';
						$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';
						
						$todayPunches = $this->biometric->getAttendanceLogByEmp($currentDate,$emp->id);
						$nextDayPunches = $this->biometric->getAttendanceLogByEmp($nextDate,$emp->id);
						$empPucnhes = array_merge($todayPunches,$nextDayPunches);
						print_r($empPucnhes);exit;
						// Get ShiftData
						/* $shiftData = $this->shiftModel->getAttendanceLog($currentDate,$emp->id);
						$emp->shift_start = $shiftData->shift_start;
						$emp->shift_end = $shiftData->shift_end;
						$emp->shift_id = $shiftData->shift_id;
						$emp->shift_name = $shiftData->shift_name;
						$emp->total_shift_time = $shiftData->total_shift_time;
						$nextDayShiftData = $this->shiftModel->getAttendanceLog($nextDate,$emp->id); */
						$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$todayPunches->shift_start. ' -180 minutes'));
						if($todayPunches->shift_end < date('H:i:s', strtotime('11:00:00'))) // Night Shift
						{
							if(!empty($nextDayPunches))
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayPunches->shift_start. ' -180 minutes'));
							}
							else
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
							}
							$dorn=2;
						}
						else
						{
							if(!empty($nextDayPunches))
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayPunches->shift_start. ' -180 minutes'));
							}
							else
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
							}
						}
						//$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
						
						if(!empty($empPucnhes))
						{
							foreach($empPucnhes as $punch)
							{
								$todayPunch = $punch->punch_date;	
								$pnchDate = date('d-m-Y H:i:s', strtotime(strtr($todayPunch->PunchDate, '/', '-')));
								if((strtotime($pnchDate) >= strtotime($shiftStart)) AND (strtotime($pnchDate) <= strtotime($shiftEnd)))
								//if(($pnchDate >= $shiftStart))
								{
									$punchDates[]=date('Y-m-d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
									$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
									$mflag[]='S';
								}
							}
						}
						// Get Manual Punches
						/*$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
						$mpDataND = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($nextDate)),$emp->id);
						$mpData = array_merge($mpData,$mpDataND);
						if(!empty($mpData))
						{
							foreach($mpData as $mpRow):
								$time = explode(" ",$mpRow->punch_in);
								
								$pDate = date('Y-m-d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
            					if((strtotime($pDate) >= strtotime($shiftStart)) AND (strtotime($pDate) <= strtotime($shiftEnd)))
            					{
            					    $punchDates[]=$pDate;
    								$punchTimes[] = date("H:i:s",strtotime($time[1]));
    								$mflag[]=$pDate;
            					}
							endforeach;
						}*/
						
						if(!empty($punchDates))
						{
							$attend_status = true;if($currentDay == 'Wed'){$present_status = 'WP';}
							$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
							$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
							if( count($punchDates) == 1 ):
								$punch_out = $shiftEnd;
							endif;

							$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
							$late = ($punch_in > $late_in) ? 'Y' : '';
							
							$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
							$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
							$interval = $time1->diff($time2);
							$total_hours = $interval->format('%H:%I:%S');
							$total_is = $interval->format('%I:%S');
							
							$punch_in = date('H:i', strtotime($punch_in));
							$punch_out = date('H:i', strtotime($punch_out));
							$total_hours = date('H:i', strtotime($total_hours));
							
							// Total Hours Calculation
							$totalHrs = explode(':',$total_hours);
							// Get Extra Hours
							$exHrsTime = '';$exTime = 0;
							$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
							
							if(!empty($exHrsData))
							{
								$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
								$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
								$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
								
								if($exh < 0 OR $exm < 0):
								    $exHrsTime = '<td style="text-align:center;color:#aa0000;font-size:12px;font-weight:bold;">'.abs($exh).' H : '.abs($exm).' M</td>';
								else:
								    $exHrsTime = '<td style="text-align:center;font-size:12px;">'.abs($exh).' H : '.abs($exm).' M</td>';
								endif;
							}
							// Shift Time Calculation
							$totalShiftTime = (!empty($emp->total_shift_time)) ? explode(':',$emp->total_shift_time) : explode(':','08:45');
							$stime = 0;$stime = 0;//print_r($totalShiftTime);print_r(' = '.$emp->emp_code.'@'.$currentDate.'<br>');
							if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
								$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
							endif;
							
							//$all_puch = sortDates($punchTimes);
							$all_puch = sortDates($punchDates);
							$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
							foreach($all_puch as $punch)
							{
								$wph[$idx][]=strtotime($punch);
								if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							
							// Count Lunch Time (If Lunch time > 45 Mins then Lunch Time Time is Actual Otherwise 45 Mins Fixed
							$countedLT =0;$ltime = 0;
							if(count($wph) >= 2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
							$halfDayTime = intval($stime/2) + 60;
							$lunchTime = 0;
							if((intVal($TWHRS) > $halfDayTime) OR (count($wph) >= 2)){$lunchTime = 2700;}
							//if(count($wph) > 2){$lunchTime = 2700;}
							$ltime = $lunchTime;
							if($countedLT < 2700){$lunchTime -= $countedLT;}else{$lunchTime = 0;$ltime = $countedLT;}
							$TWHRS = $TWHRS - $lunchTime;
							
							$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
							
							$wh = intVal($TWHRS) - intVal($ot);
							
							$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
							$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
							$TWHRS += $exTime;$totalWorkTime = $TWHRS;
							$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
							$lunchTime = floor($ltime / 3600) .':'. floor($ltime / 60 % 60);
							
							// $allPunchDates = implode(', ',sortDates($punchDates));
							// if($dorn==2){$allPunches = implode(', ',sortDates($allPunchDates,'DESC'));}
							$allPunches = '';
							if(!empty($punchDates))
							{
								$allPunchDates = ($dorn==2) ? sortDates($punchDates) : sortDates($punchDates);
								$ap = Array();
								foreach($allPunchDates as $p)
								{
									$spanTag = '';
									if(in_array($p,$mflag)){$ap[] = '<b>'.date("d H:i:s",strtotime($p)).'</b>';}
									else{$ap[] = date("d H:i:s",strtotime($p));}
								}
								$allPunches = implode(', ',$ap);
							}
							
							
							// Check For Missed Punch
							if(count($punchTimes) % 2 != 0)
							{
								$status = '<td style="text-align:center;color:#233288;font-size:12px;">M</td>';
							}
							else
							{
								if($totalWorkTime <= 0){$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';}
								else{$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">'.$present_status.'</td>';}
							}
							$workHrs = '<td style="text-align:center;font-size:12px;">'.$work_hours.'</td>';
							$ltTd = '<td style="text-align:center;font-size:12px;">'.$lunchTime.'</td>';
							$exHrs = $exHrsTime;//'<td style="text-align:center;font-size:12px;">'.$exHrsTime.'</td>';
							$otData = '<td style="text-align:center;font-size:12px;">'.$ot.'</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.$TWHRS.'</td>';
							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';	
						}
						else
						{
							$attend_status = false;if($currentDay == 'Wed'){$present_status = 'W';}else{$present_status='A';}
							$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$present_status.'</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$ltTd = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
							$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';
						}
					
						$empTable .='<tr>';
							$empTable .='<td style="text-align:center;font-size:12px;">'.$ecode.'</td>';
							$empTable .='<td style="text-align:left;font-size:12px;">'.$emp->emp_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->name.'</td>';
							$empTable .='<td style="font-size:12px;">'.$emp->shift_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.date("d-m-Y",strtotime($currentDate)).'</td>';
							$empTable .= $status.$workHrs.$ltTd.$exHrs.$otData.$totalWorkHrs.$lateStatus;
							$empTable .='<td style="font-size:12px;text-align:left;">'.$allPunches.'</td>';
						$empTable .='</tr>';
					}
				}
			}
			
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th>Emp Code</th>
							<th>Employee</th>
							<th>Department</th>
							<th>Shift</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>Lunch</th>
							<th>Ex. Hours</th>
							<th>OT</th>
							<th>TWH</th>
							<th>Late</th>
							<th>All Pucnhes</th>
						</tr></thead><tbody>'.$empTable.'</tbody></table>';
 			// echo $response;exit;
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
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = $this->m_pdf->load();
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
			}
			
		}
	}

    public function loadAttendanceSheet(){
        $data = $this->input->post();
		
		$month = date('m',strtotime($data['month']));
		set_time_limit(0);
		$empData = $this->attendance->getEmployeeListForMonthAttendance();
		$companyData = $this->attendance->getCompanyInfo();
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y_00:01",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y_11:59",strtotime($year.'-'.$month.'-01'));
		
		
		$first_day = 1;$punchData = NULL;$empCount = 1;$printData='';
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		
		$thead ='';$tbody ='';$i=1;
		$thead .='<tr><th class="text-center" colspan="'.($last_day + 2).'">Employee Attandance Sheet for '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</th></tr>';
		$thead .='<tr><th>Employee</th><th>Emp Code</th>';
		
		$emp1 = Array();$response = '';$empTable='';$pageData = Array();
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';$wo = 0;$monthWH = 0;$wh=0;$wi = 0;$oth = 0;$oti = 0;
				$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';$workHrs = '';$otData = '';$status = '';
				
				$tbody .='<tr>';
				$tbody .='<td><b>'.$emp->emp_name.'</b><br><small>'.$emp->title.'</small></td>';
				$tbody .='<td><b>'.$emp->emp_code.'</b></td>';
				for($d=1;$d<=$last_day;$d++)
				{
					$punchData = New StdClass();$empPucnhes = Array();
					$filterDate = date("Y-m-d",strtotime($year.'-'.$month.'-'.$d));
					$punchData = $this->biometric->getPunchData($filterDate,$filterDate);
					
					if(!empty($punchData))
					{
						$punches = json_decode($punchData[0]->punch_data);
						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
					}
					$attend_status = false;
					if($i==1){$thead .='<th>'.$d.'</th>';}
					$currentDate = date('d/m/Y', strtotime($year.'-'.$month.'-'.$d));$punchDates = Array();
					
					if(!empty($empPucnhes))
					{
						foreach($empPucnhes as $punch)
						{
							$todayPunch = $punches[$punch];							
							$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
						}
					}							
					// Get Manual Punches
					$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
					if(!empty($mpData))
					{
						foreach($mpData as $mpRow):
							$time = explode(" ",$mpRow->punch_in);
							$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
						endforeach;
					}
					
					if(!empty($punchDates)):
						$tbody .='<th class="text-success">P</th>';
					else:
						$tbody .='<th class="text-danger">A</th>';
					endif;
				}
				$tbody .='</tr>';$i++;
			}
		}
		$thead .='</tr>';
		
		$this->printJson(["status"=>1,"thead"=>$thead,"tbody"=>$tbody]);
    }

    public function printSalarySheet($month,$biomatric_id="ALL",$file_type = 'excel'){
        $data = $this->input->post();
		
		set_time_limit(0);
		$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
		$empData = $this->attendance->getEmployeeList($biomatric_id);
		$companyData = $this->attendance->getCompanyInfo();
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');
		$FromDate = date("d/m/Y_00:01",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y_11:59",strtotime($year.'-'.$month.'-01'));
		$last_day = date("t",strtotime($ToDate));
		
		$endDate  = date("t-m-Y",strtotime($year.'-'.$month.'-01'));
		/*if(strtotime($endDate) > strtotime(date('d-m-Y')))
		{
			$ToDate  = date("d/m/Y_11:59",strtotime(date('d-m-Y')));
			$last_day = date("d",strtotime(date('d-m-Y')));
		}*/	
		$first_day = 1;$punchData = NULL;$empCount = 1;$printData='';		
		$theadDates ='';$tbody ='';$i=1;
		
		$emp1 = Array();$response = '';$empTable='';$pageData = Array();
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				$present = 0;$leave = 0;$absent = 0;$theadDate = '';$theadDay = '';$wo = 0;$monthWH = 0;$wh=0;$wi = 0;$oth = 0;$oti = 0;
				$inData = '';$outData = '';$lunchInData = '';$lunchOutData = '';$workHrs = '';$otData = '';$status = '';
				
				if($i==1){$theadDates .='<tr><th>Employee</th><th>Emp Code</th>';}
				$tbody .='<tr>';
				$tbody .='<td><b>'.$emp->emp_name.'</b><br><small>'.$emp->title.'</small></td>';
				$tbody .='<td><b>'.$emp->emp_code.'</b></td>';
				for($d=1;$d<=$last_day;$d++)
				{
					$currentDate =  date("Y-m-d",strtotime($year.'-'.$month.'-'.$d));
					$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
					if($i==1){$theadDates .='<th>'.$d.'</th>';}
					if(strtotime($currentDate) <= strtotime(date('d-m-Y')))
					{
						$currentDay = date('D', strtotime($currentDate));
						$punchData = New StdClass();
						$todayPunchData = $this->biometric->getPunchData($currentDate,$nextDate);
						$punches = Array();$punchDates = Array();
						foreach($todayPunchData as $pnc)
						{
							$jarr = json_decode($pnc->punch_data);
							$punches = array_merge($punches,$jarr);
						}
						
						// Get ShiftData
						$shiftData = $this->shiftModel->getAttendanceLog($currentDate,$emp->id);
						$emp->shift_start = $shiftData->shift_start;
						$emp->shift_end = $shiftData->shift_end;
						$emp->shift_id = $shiftData->shift_id;
						$emp->shift_name = $shiftData->shift_name;
						$emp->total_shift_time = $shiftData->total_shift_time;
						$nextDayShiftData = $this->shiftModel->getAttendanceLog($nextDate,$emp->id);
						$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start. ' -180 minutes'));
						if($emp->shift_end < date('H:i:s', strtotime('11:00:00'))) // Night Shift
						{
							if(!empty($nextDayShiftData))
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
							}
							else
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
							}
							$dorn=2;
						}
						else
						{
							if(!empty($nextDayShiftData))
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
							}
							else
							{
								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
							}
						}
						//print_r($nextDayShiftData);
						//print_r($emp->shift_end.'@@@'.$shiftStart.' *** '.$shiftEnd);
						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
						if(!empty($empPucnhes))
						{
							foreach($empPucnhes as $punch)
							{
								$todayPunch = $punches[$punch];	
								$pnchDate = date('d-m-Y H:i:s', strtotime(strtr($todayPunch->PunchDate, '/', '-')));
								if((strtotime($pnchDate) >= strtotime($shiftStart)) AND (strtotime($pnchDate) <= strtotime($shiftEnd)))
								{
									$punchDates[]=date('Y-m-d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
									$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
									$mflag[]='S';
								}
							}
						}
						// Get Manual Punches
						$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
						$mpDataND = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($nextDate)),$emp->id);
						$mpData = array_merge($mpData,$mpDataND);
						if(!empty($mpData))
						{
							foreach($mpData as $mpRow):
								$time = explode(" ",$mpRow->punch_in);
								
								$pDate = date('Y-m-d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
								if((strtotime($pDate) >= strtotime($shiftStart)) AND (strtotime($pDate) <= strtotime($shiftEnd)))
								{
									$punchDates[]=$pDate;
									$punchTimes[] = date("H:i:s",strtotime($time[1]));
									$mflag[]=$pDate;
								}
							endforeach;
						}
						//print_r($punchDates);
						$attend_status = false;
						
						if(!empty($punchDates))
						{
							// Get Extra Hours
							$exHrsTime = '';$exTime = 0;
							$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
							
							if(!empty($exHrsData))
							{
								$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
								$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
								$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
								
								if($exh < 0 OR $exm < 0):
									$exHrsTime = '<td style="text-align:center;color:#aa0000;font-size:12px;font-weight:bold;">'.abs($exh).' H : '.abs($exm).' M</td>';
								else:
									$exHrsTime = '<td style="text-align:center;font-size:12px;">'.abs($exh).' H : '.abs($exm).' M</td>';
								endif;
							}
							// Shift Time Calculation
							$totalShiftTime = (!empty($emp->total_shift_time)) ? explode(':',$emp->total_shift_time) : explode(':','08:45');
							$stime = 0;$stime = 0;
							if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
								$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
							endif;
							
							//$all_puch = sortDates($punchTimes);
							$all_puch = sortDates($punchDates);
							$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
							foreach($all_puch as $punch)
							{
								$wph[$idx][]=strtotime($punch);
								if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
								$t++;
							}
							
							// Count Lunch Time (If Lunch time > 45 Mins then Lunch Time Time is Actual Otherwise 45 Mins Fixed
							$countedLT =0;$ltime = 0;
							if(count($wph) >= 2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
							$halfDayTime = intval($stime/2) + 60;
							$lunchTime = 0;
							if((intVal($TWHRS) > $halfDayTime) OR (count($wph) >= 2)){$lunchTime = 2700;}
							//if(count($wph) > 2){$lunchTime = 2700;}
							$ltime = $lunchTime;
							if($countedLT < 2700){$lunchTime -= $countedLT;}else{$lunchTime = 0;$ltime = $countedLT;}
							$TWHRS = $TWHRS - $lunchTime;
							
							$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
							
							$wh = intVal($TWHRS) - intVal($ot);
							
							$TWHRS += $exTime;
							//$TWHRS = floor($TWHRS / 3600) .'H '. floor($TWHRS / 60 % 60). 'M';
							$TWHRS = round(($TWHRS / 3600),2);
								
							$tbody .='<th>'.$TWHRS.'</th>';
						}
						else
						{
							$tbody .='<th></th>';
						}
					}
					else{$tbody .='<th></th>';}
				}
				$tbody .='</tr>';$theadDates .='</tr>';$i++;
			}
		}
		
		$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
		$response .= '<thead>
							<tr><th class="text-center" colspan="'.($last_day + 2).'">Employee Attandance Sheet for '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</th></tr>
							
							'.$theadDates.'
					</thead>';
		$response .= '<tbody>'.$tbody.'</tbody></table>';
		
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
										<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
									</tr>
								</table>
							</div>';
			$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
							<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
							</table>';
			
			$mpdf = $this->m_pdf->load();
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
		}
		
		//$this->printJson(["status"=>1,"thead"=>$thead,"tbody"=>$tbody]);
    }
    
    /* Employee Recruitment Form | CREATED AT : 22/09/2022 | CREATED BY : MEGHAVI*/
    function empRecruitmentForm(){
		$this->data['companyData'] = $this->employee->getCompanyInfo();
	
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('report/hr_report/emp_recruitment_form',$this->data,true);
		
		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:50px;"></td>
								<td class="org_title text-center" style="font-size:1.5rem;width:50%">Employee Information Form</td>
								<td style="width:25%;" class="text-right"><span style="font-size:0.8rem;">R-HR-01 (00/01.10.17)</td>
							</tr>
						</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-.pdf';
		//$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		//$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,25,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
	}

    public function printHourlyReport($dates,$biomatric_id="ALL",$file_type = 'excel'){
		
		set_time_limit(0);
		if(!empty($dates))
		{
			$duration = explode('~',$dates);
			$biomatric_id = ($biomatric_id == "ALL") ? '' : $biomatric_id ;
			$empData = $this->attendance->getEmployeeList($biomatric_id);
// 			$empData = $this->attendance->getEmployeeList('20400');
			$companyData = $this->attendance->getCompanyInfo();
			$current_month  = date("m");
			$month  = date("m",strtotime($duration[0]));
			$year  = date("Y",strtotime($duration[0]));
			$FromDate = date("Y-m-d",strtotime($duration[0]));
			$ToDate  = date("Y-m-d",strtotime($duration[1]));
			
			$fdate = date("Y-m-d 00:00:01",strtotime($duration[0]));
			$tdate  = date("Y-m-d 23:59:59",strtotime($duration[1]));
			
			$first_day = date("d",strtotime($duration[0]));
			$last_day = date("d",strtotime($duration[1]));
			
			$empTable = '';
			$thead ='';$tbody ='';$i=1;$printData='';$empCount = 1;
			$begin = new DateTime($FromDate);
			$end = new DateTime($ToDate);
			$end = $end->modify( '+1 day' ); 
			
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval ,$end);
			
			foreach($daterange as $date)
			{
				$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
				$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
				$currentDay = date('D', strtotime($currentDate));
				$punchData = New StdClass();
				$todayPunchData = $this->biometric->getPunchData($currentDate,$nextDate);
				
				$punches = Array();
				foreach($todayPunchData as $pnc)
				{
					$jarr = json_decode($pnc->punch_data);
					$punches = array_merge($punches,$jarr);
				}
				
				if(!empty($empData))
				{
					foreach($empData as $emp)
					{
    						$ecode = sprintf("%04d", $emp->biomatric_id);$punchDates = Array();$punchTimes = Array();$mflag=Array();
    						$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';$allPunches ='';$dorn=1;$present_status = 'P';
    						$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';
					    if(!empty($emp->emp_joining_date) AND (strtotime($emp->emp_joining_date) <= strtotime($currentDate)))
					    {
    						
    						// Get ShiftData
    						$shiftData = $this->shiftModel->getAttendanceLog($currentDate,$emp->id);
    						$emp->shift_start = $shiftData->shift_start;
    						$emp->shift_end = $shiftData->shift_end;
    						$emp->shift_id = $shiftData->shift_id;
    						$emp->shift_name = $shiftData->shift_name;
    						$emp->total_shift_time = $shiftData->total_shift_time;
    						$nextDayShiftData = $this->shiftModel->getAttendanceLog($nextDate,$emp->id);
    						$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start. ' -180 minutes'));
    						if($emp->shift_end < date('H:i:s', strtotime('11:00:00'))) // Night Shift
    						{
    							if(!empty($nextDayShiftData))
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
    							}
    							else
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
    							}
    							//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' 13:15:00'));
    							//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' 12:30:00'));
    							$dorn=2;
    						}
    						else
    						{
    							if(!empty($nextDayShiftData))
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
    							}
    							else
    							{
    								$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
    							}
    							//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' 04:30:00'));
    							//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' 04:00:00'));
    						}
    						//print_r($nextDayShiftData);
    						//print_r($emp->shift_end.'@@@'.$shiftStart.' *** '.$shiftEnd);
    						$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
    						if(!empty($empPucnhes))
    						{
    							foreach($empPucnhes as $punch)
    							{
    								$todayPunch = $punches[$punch];	
    								$pnchDate = date('d-m-Y H:i:s', strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    								if((strtotime($pnchDate) >= strtotime($shiftStart)) AND (strtotime($pnchDate) <= strtotime($shiftEnd)))
    								//if(($pnchDate >= $shiftStart))
    								{
    									$punchDates[]=date('Y-m-d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    									$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
    									$mflag[]='S';
    								}
    							}
    						}
    						// Get Manual Punches
    						$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($currentDate)),$emp->id);
    						$mpDataND = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($nextDate)),$emp->id);
    						$mpData = array_merge($mpData,$mpDataND);
    						if(!empty($mpData))
    						{
    							foreach($mpData as $mpRow):
    								$time = explode(" ",$mpRow->punch_in);
    								
    								$pDate = date('Y-m-d H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
                					if((strtotime($pDate) >= strtotime($shiftStart)) AND (strtotime($pDate) <= strtotime($shiftEnd)))
                					{
                					    $punchDates[]=$pDate;
        								$punchTimes[] = date("H:i:s",strtotime($time[1]));
        								$mflag[]=$pDate;
                					}
    							endforeach;
    						}
    						
    						if(!empty($punchDates))
    						{
    							$attend_status = true;if($currentDay == 'Wed'){$present_status = 'WP';}
    							$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
    							$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
    							if( count($punchDates) == 1 ):
    								$punch_out = $shiftEnd;
    							endif;
    
    							$late_in =  date('d-m-Y H:i:s', strtotime($shiftStart.' + '.intVal($emp->late_in).' minute'));
    							$late = ($punch_in > $late_in) ? 'Y' : '';
    							
    							$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
    							$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
    							$interval = $time1->diff($time2);
    							$total_hours = $interval->format('%H:%I:%S');
    							$total_is = $interval->format('%I:%S');
    							
    							$punch_in = date('H:i', strtotime($punch_in));
    							$punch_out = date('H:i', strtotime($punch_out));
    							$total_hours = date('H:i', strtotime($total_hours));
    							
    							// Total Hours Calculation
    							$totalHrs = explode(':',$total_hours);
    							// Get Extra Hours
    							$exHrsTime = '';$exTime = 0;
    							$exHrsData = $this->attendance->getExtraHours(date("Y-m-d",strtotime($currentDate)),$emp->id);
    							
    							if(!empty($exHrsData))
    							{
    								$exTime = (intVal($exHrsData->ex_hours) * 3600) + (intVal($exHrsData->ex_mins) * 60);
    								$exh = (!empty($exHrsData->ex_hours)) ? $exHrsData->ex_hours : '00';
    								$exm = (!empty($exHrsData->ex_mins)) ? $exHrsData->ex_mins : '00';
    								
    								if($exh < 0 OR $exm < 0):
    								    $exHrsTime = '<td style="text-align:center;color:#aa0000;font-size:12px;font-weight:bold;">'.abs($exh).' H : '.abs($exm).' M</td>';
    								else:
    								    $exHrsTime = '<td style="text-align:center;font-size:12px;">'.abs($exh).' H : '.abs($exm).' M</td>';
    								endif;
    							}
    							// Shift Time Calculation
    							$totalShiftTime = (!empty($emp->total_shift_time)) ? explode(':',$emp->total_shift_time) : explode(':','08:45');
    							$stime = 0;$stime = 0;//print_r($totalShiftTime);print_r(' = '.$emp->emp_code.'@'.$currentDate.'<br>');
    							if(intVal($totalShiftTime[0]) > 0 OR intVal($totalShiftTime[1]) > 0):
    								$stime = (intVal($totalShiftTime[0]) * 3600) + (intVal($totalShiftTime[1]) * 60);
    							endif;
    							
    							//$all_puch = sortDates($punchTimes);
    							$all_puch = sortDates($punchDates);
    							$twh = 0;$TWHRS=0;$t=1;$wph = Array();$idx=0;
    							foreach($all_puch as $punch)
    							{
    								$wph[$idx][]=strtotime($punch);
    								if($t%2 == 0){$TWHRS += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
    								$t++;
    							}
    							
    							// Count Lunch Time (If Lunch time > 45 Mins then Lunch Time Time is Actual Otherwise 45 Mins Fixed
    							$countedLT =0;$ltime = 0;
    							if(count($wph) >= 2){$countedLT = floatVal($wph[1][0]) - floatVal($wph[0][1]);}
    							$halfDayTime = intval($stime/2) + 60;
    							$lunchTime = 0;
    							if((intVal($TWHRS) > $halfDayTime) OR (count($wph) >= 2)){$lunchTime = 2700;}
    							//if(count($wph) > 2){$lunchTime = 2700;}
    							$ltime = $lunchTime;
    							if($countedLT < 2700){$lunchTime -= $countedLT;}else{$lunchTime = 0;$ltime = $countedLT;}
    							$TWHRS = $TWHRS - $lunchTime;
    							
    							$ot = $ot1 = (intVal($TWHRS) > intVal($stime)) ? (intVal($TWHRS) - intVal($stime)) : 0;
    							
    							$wh = intVal($TWHRS) - intVal($ot);
    							
    							$work_hours = floor($wh / 3600) .':'. floor($wh / 60 % 60);
    							$ot = floor($ot / 3600) .':'. floor($ot / 60 % 60);
    							$TWHRS += $exTime;$totalWorkTime = $TWHRS;
    							$TWHRS = floor($TWHRS / 3600) .':'. floor($TWHRS / 60 % 60);
    							$lunchTime = floor($ltime / 3600) .':'. floor($ltime / 60 % 60);
    							
    							// $allPunchDates = implode(', ',sortDates($punchDates));
    							// if($dorn==2){$allPunches = implode(', ',sortDates($allPunchDates,'DESC'));}
    							$allPunches = '';
    							if(!empty($punchDates))
    							{
    								$allPunchDates = ($dorn==2) ? sortDates($punchDates) : sortDates($punchDates);
    								$ap = Array();
    								foreach($allPunchDates as $p)
    								{
    									$spanTag = '';
    									if(in_array($p,$mflag)){$ap[] = '<b>'.date("d H:i:s",strtotime($p)).'</b>';}
    									else{$ap[] = date("d H:i:s",strtotime($p));}
    								}
    								$allPunches = implode(', ',$ap);
    							}
    							
    							
    							// Check For Missed Punch
    							if(count($punchTimes) % 2 != 0)
    							{
    								$status = '<td style="text-align:center;color:#233288;font-size:12px;">M</td>';
    							}
    							else
    							{
    								if($totalWorkTime <= 0){$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">A</td>';}
    								else{$status = '<td style="text-align:center;color:#00aa00;font-size:12px;">'.$present_status.'</td>';}
    							}
    							$workHrs = '<td style="text-align:center;font-size:12px;">'.$work_hours.'</td>';
    							$ltTd = '<td style="text-align:center;font-size:12px;">'.$lunchTime.'</td>';
    							$exHrs = $exHrsTime;//'<td style="text-align:center;font-size:12px;">'.$exHrsTime.'</td>';
    							$otData = '<td style="text-align:center;font-size:12px;">'.$ot.'</td>';
    							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.$TWHRS.'</td>';
    							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';	
    						} 
    						else
    						{
    							$attend_status = false;if($currentDay == 'Wed'){$present_status = 'W';}else{$present_status='A';}
    							$status = '<td style="text-align:center;color:#aa0000;font-size:12px;">'.$present_status.'</td>';
    							$workHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$ltTd = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$exHrs= '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$otData = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">--:--</td>';
    							$lateStatus = '<td style="text-align:center;font-size:12px;">'.$late.'</td>';
    						} 
					    }	
						else
						{
							$status = '<td style="text-align:center;;font-size:12px;">NA</td>';
							$workHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
							$otData = '<td style="text-align:center;font-size:12px;">NA</td>';
							$totalWorkHrs = '<td style="text-align:center;font-size:12px;">NA</td>';
						}
					
						$empTable .='<tr>';
							$empTable .='<td style="text-align:center;font-size:12px;">'.$ecode.'</td>';
							$empTable .='<td style="text-align:left;font-size:12px;">'.$emp->emp_name.'</td>';
							$empTable .='<td style="font-size:12px;">'.date("d-m-Y",strtotime($currentDate)).'</td>';
							$empTable .= $status.$workHrs.$otData.$totalWorkHrs;
						$empTable .='</tr>';
					}
				}
			}
			
			
			$response = '<table class="table-bordered itemList" border="1" repeat_header="1">';
				$response .= '<thead>
						<tr style="background:#eee;">
							<th>Emp Code</th>
							<th>Employee</th>
							<th>Punch Date</th>
							<th>Status</th>
							<th>WH</th>
							<th>OT</th>
							<th>TWH</th>
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
											<td class="text-right pad-right-10"><b>Report Month : '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</td>
										</tr>
									</table>
								</div>';
				$htmlFooter = '<table style="border-top:1px solid #000;padding:3px;">
								<tr><td style="width:50%;text-align:left;">Printed On {DATE j-m-Y}</td><td style="width:50%;text-align:right;">Page No :- {PAGENO}</td></tr>
								</table>';
				
				$mpdf = $this->m_pdf->load();
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
			}
			
		}
	}

}
?>