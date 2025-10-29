<?php
class AttendanceModel extends MasterModel{
    private $empAttendance = "emp_attendance";
    private $empMaster = "employee_master";
    private $empDesignation = "emp_designation";
    private $leaveMaster = "leave_master";
    private $alogSummary = "alog_summary";
	private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];
	
	public function getAttendanceStatsByDate($filterDate)
	{
		$FromDate = date("d/m/Y",strtotime($filterDate));
		$ToDate  = date("d/m/Y",strtotime($filterDate));
		
		$empQuery['select'] = "employee_master.*,shift_master.shift_name,shift_master.shift_start,shift_master.total_shift_time,shift_master.shift_end,department_master.name, department_master.section, emp_designation.title,emp_category.category,attendance_policy.early_in as late_in,attendance_policy.early_out,attendance_policy.no_early_in as no_late_in,attendance_policy.no_early_out, attendance_policy.punch_tolerance";
        $empQuery['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $empQuery['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $empQuery['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $empQuery['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $empQuery['leftJoin']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
		$empQuery['where']['employee_master.id!='] = 1;
		$empQuery['where']['employee_master.biomatric_id!='] = 0;
		$empQuery['where']['employee_master.is_active'] = 1;
		// $empQuery['where_in']['employee_master.biomatric_id'] = '10035,1021,1038';
		$empQuery['order_by']['employee_master.emp_code'] = 'ASC';
        $empQuery['tableName'] = $this->empMaster;
		$empData = $this->rows($empQuery);
		
		$empInfo = Array();
		$totalEmp=0;$present=0;$absent=0;$late=0;$presentPer = 0;$absentPer = 0;$latePer = 0;$absentEmp=Array();$lateEmp=Array();
		$punchData =Array();
		$punchData = $this->biometric->getPunchData($filterDate,$filterDate);
		
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$empArray = Array();
				$attend_status = false;$emp->attend_status = "Absent";$punchDates = Array();$emp->punch_time = "";
				if(!empty($punchData))
				{
					foreach($punchData as $punchRow):
						$empPunches = json_decode($punchRow->punch_data);
						foreach($empPunches as $device)
						{
							if($emp->biomatric_id == (int)$device->Empcode)
							{
								$punchDates[]=date('H:i:s',strtotime(strtr($device->PunchDate, '/', '-')));
								
								$sshi = '00:00:00';
								if(!empty($emp->shift_start)):
									$sstime = explode(':',$emp->shift_start);
									$sstimeMins = (intVal($sstime[0]) * 60) + intVal($sstime[1]) - intVal($emp->punch_tolerance);
									$sshi = str_pad(floor($sstimeMins / 60),2,"0",STR_PAD_LEFT) . ':' . str_pad(floor($sstimeMins % 60),2,"0",STR_PAD_LEFT) .':00';
								endif;
								$sehi = '00:00:00';
								if(!empty($emp->shift_end)):
									$setime = explode(':',$emp->shift_end);
									$setimeMins = ($setime[0] * 60) + $setime[1] + $emp->punch_tolerance;
									$sehi = str_pad(floor($setimeMins / 60),2,"0",STR_PAD_LEFT) . ':' . str_pad(floor($setimeMins % 60),2,"0",STR_PAD_LEFT) .':00';
								endif;
								
								$pnchDT = date('Y-m-d',strtotime(strtr($device->PunchDate, '/', '-')));
								
								$punch_in = "";$punch_out = "";$work_hour = "";$total_hours = "";$overtime = "";
								$device->shiftStart = date('d-m-Y H:i:s', strtotime($pnchDT.' '.$sshi));
								$device->shiftEnd = date('d-m-Y H:i:s', strtotime($pnchDT.' '.$sehi));
								
								$empArray[] = $device;
							}
						}
					endforeach;
				}	
				// Get Manual Punches
				$mpData = $this->attendance->getManualPunchData(date("Y-m-d",strtotime($filterDate)),$emp->id);
				if(!empty($mpData))
				{
					foreach($mpData as $mpRow):
						$punchDates[]=date('H:i:s',strtotime(strtr($mpRow->punch_in, '/', '-')));
					endforeach;
				}
				array_multisort(array_map('strtotime',array_column($empArray,'PunchDate')),SORT_DESC, $empArray);
				
				if(!empty($punchDates)):
					//print_r($punchDates);print_r(' => '.$emp->emp_code.'<br>');
					$attend_status = true;$emp->attend_status = "Present";
					$emp->punch_time = implode(', ',sortDates($punchDates));
				else:
					$attend_status = false;$emp->attend_status = "Absent";
				endif;
				
				$emp->leave_reason='';
				if($attend_status)
				{
					$present++;
				}
				else
				{
					$emp->attend_status = "Absent";
					$leaveQuery['where']['emp_id'] = $emp->id;
					$leaveQuery['customWhere'][] = '"'.$filterDate. '" BETWEEN start_date AND end_date';
					$leaveQuery['where']['approve_status'] = 1;
					$leaveQuery['tableName'] = $this->leaveMaster;
					$leaveData = $this->row($leaveQuery);
					
					if(!empty($leaveData))
					{
						$emp->leave_reason=$leaveData->leave_reason;
						$emp->attend_status = 'On Leave<br><small>'.$emp->leave_reason.'</small>';
					}else{$emp->leave_reason='';}
					
					$absent++;$absentEmp[]=$emp;
				}
				$totalEmp++;
				
				$empInfo[] = $emp; 
			}
		}
		if(!empty($totalEmp)){$presentPer = round((($present * 100) / $totalEmp),2);}
		if(!empty($totalEmp)){$absentPer = round((($absent * 100) / $totalEmp),2);}
		if(!empty($present)){$latePer = round((($late * 100) / $present),2);}
		$response = ['totalEmp'=>$totalEmp,'present'=>$present,'absent'=>($absent-$late),'late'=>$late,'absentEmp'=>$absentEmp,'lateEmp'=>$lateEmp,'presentPer'=>$presentPer,'absentPer'=>$absentPer,'latePer'=>$latePer,"empInfo"=>$empInfo];
		return $response;
	}

	public function getMismatchPunchData($filterDate){
		$FromDate = date("d/m/Y",strtotime($filterDate))."_00:00";
		$ToDate  = date("d/m/Y",strtotime($filterDate))."_23:59";
		$currentDate =  date("Y-m-d",strtotime($filterDate));
		$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
		set_time_limit(0);
		$empQuery['select'] = "employee_master.*,shift_master.shift_name,shift_master.shift_start,shift_master.total_shift_time, shift_master.shift_end,department_master.name as department_name, department_master.section,emp_designation.title, emp_category.category,attendance_policy.early_in as late_in,attendance_policy.early_out,attendance_policy.no_early_in as no_late_in,attendance_policy.no_early_out";
        $empQuery['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $empQuery['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $empQuery['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $empQuery['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $empQuery['leftJoin']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
		$empQuery['where']['employee_master.id!='] = 1;
// 		$empQuery['where']['employee_master.biomatric_id!='] = 0;
	//	$empQuery['where']['employee_master.emp_code'] = '20001';
// 		$empQuery['where']['employee_master.emp_code!='] = '60073';
		$empQuery['where']['employee_master.is_active'] = 1;
		$empQuery['order_by']['employee_master.emp_code'] = 'ASC';
        $empQuery['tableName'] = $this->empMaster;
		$empData = $this->rows($empQuery);	
		
		// $pucnhData = $this->biometric->getPunchData($currentDate,$nextDate);
		// $punches = json_decode($pucnhData[0]->punch_data);		
		
		$todayPunchData = $this->biometric->getPunchData($currentDate,$nextDate);
		$punches = Array();
		foreach($todayPunchData as $pnc)
		{
			$jarr = json_decode($pnc->punch_data);
			$punches = array_merge($punches,$jarr);
		}
		
		
		$resultData = array();
		$missedPunch = 0;$punchCount=0;
		$i=1;
		foreach($empData as $emp):
		    $missedPunch = 0;$punchCount=0;
			$ecode = sprintf("%04d", $emp->biomatric_id);$punchDates = Array();$punchTimes = Array();
			$attend_status = false;$wo = 0;$wh = 0;$wi = 0;$late = '';$allPunches ='';$dorn=1;$present_status = 'P';
			$workHrs = '';$otData = '';$status = '';$exOtHrs = '';$totalWorkHrs = '';$lateStatus = '';$exHrs = '';
			
			// Get ShiftData
			$shiftData = $this->shiftModel->getAttendanceLog($currentDate,$emp->id);
			$emp->shift_start = $shiftData->shift_start;
			$emp->shift_end = $shiftData->shift_end;
			$emp->shift_id = $shiftData->shift_id;
			$emp->shift_name = $shiftData->shift_name;
			$emp->total_shift_time = $shiftData->total_shift_time;
			$nextDayShiftData = $this->shiftModel->getAttendanceLog($nextDate,$emp->id);
			$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start. ' -180 minutes'));
			//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' '.$emp->shift_start. ' -30 minutes'));
			if($emp->shift_end < date('H:i:s', strtotime('11:00:00')))
			{
			    if(!empty($nextDayShiftData))
				{
					$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$nextDayShiftData->shift_start. ' -180 minutes'));
				}
				else
				{
					$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -180 minutes'));
				}
				//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$shiftData->shift_start. ' -5 minutes'));$dorn=2;
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
				//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' '.$emp->shift_start. ' -5 minutes'));
				//$shiftStart = date('d-m-Y H:i:s', strtotime($currentDate.' 04:30:00'));
				//$shiftEnd = date('d-m-Y H:i:s', strtotime($nextDate.' 04:00:00'));
			}
			$empPucnhes = array_keys(array_combine(array_keys($punches), array_column($punches, 'Empcode')),$ecode);
			
			if(!empty($empPucnhes))
			{
				foreach($empPucnhes as $punch)
				{
					$todayPunch = $punches[$punch];	
					$pnchDate = date('d-m-Y H:i:s', strtotime(strtr($todayPunch->PunchDate, '/', '-')));
				// 	if(($pnchDate >= $shiftStart) AND ($pnchDate <= $shiftEnd))
					//if(($pnchDate >= $shiftStart))
					if((strtotime($pnchDate) >= strtotime($shiftStart)) AND (strtotime($pnchDate) <= strtotime($shiftEnd)))
					{
						$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
						$punchTimes[]=date('H:i:s',strtotime(strtr($todayPunch->PunchDate, '/', '-')));
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
					}
				endforeach;
			}
			
			if(count($punchTimes) % 2 != 0):
				$missedPunch = 1;
			endif;
			
			if(!empty($missedPunch)):
				$emp->punch_time = '';
				if(!empty($punchTimes)):
					$emp->punch_time = implode(', ',sortDates($punchTimes));
					if($dorn==2){$emp->punch_time = implode(', ',sortDates($punchTimes,'DESC'));}
				endif;					
				$emp->missed_punch = $missedPunch;
				$resultData[] = $emp;
			endif;
			
		endforeach;
		
		return $resultData;
	}

	public function getManualPunchData($filterDate,$emp_id=""){
		if(!empty($emp_id)){$mpQuery['where']['emp_id'] = $emp_id;}
		$mpQuery['where']['source'] = 2;
		$mpQuery['where']['attendance_date'] = $filterDate;
		$mpQuery['tableName'] = $this->empAttendance;
		return $this->rows($mpQuery);
	}
	
	public function getExtraHours($filterDate,$emp_id=""){
		/* if(!empty($emp_id)){$mpQuery['where']['emp_id'] = $emp_id;}
		$mpQuery['where']['source'] = 3;
		$mpQuery['where']['attendance_date'] = $filterDate;
		$mpQuery['tableName'] = $this->empAttendance;
		return $this->rows($mpQuery); */
		
        $mpQuery['select'] = "SUM(ex_hours*xtype) as ex_hours,SUM(ex_mins*xtype) as ex_mins";
		if(!empty($emp_id)){$mpQuery['where']['emp_id'] = $emp_id;}
		$mpQuery['where']['source'] = 3;
		$mpQuery['where']['attendance_date'] = $filterDate;
		$mpQuery['tableName'] = $this->empAttendance;
        return $this->row($mpQuery);
	}

	public function getPunchData($FromDate,$ToDate,$Empcode="ALL"){
		$punchData = New StdClass();		
		$url = "http://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$Empcode."&FromDate=".$FromDate."&ToDate=".$ToDate;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.BIOMETRIC_TOKEN),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else{
			$resultapi = json_decode($response);
			if($resultapi->Error == false):
				$punchData = $resultapi->PunchData;
			endif;
		}
		return $punchData;
	}
	
	public function loadAttendanceSheet($month)
	{
		set_time_limit(0);
		$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y",strtotime($year.'-'.$month.'-01'));
		$first_day = 1;
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		$data['select'] = "employee_master.*,shift_master.shift_name,shift_master.shift_start,shift_master.total_shift_time,emp_designation.title,attendance_policy.early_in as late_in,attendance_policy.early_out,attendance_policy.no_early_in as no_late_in,attendance_policy.no_early_out";
        $data['join']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $data['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['leftJoin']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
        $data['tableName'] = $this->empMaster;
		$empData = $this->rows($data);
		
		$thead ='';$tbody ='';$i=1;
		$thead .='<tr><th class="text-center" colspan="'.($last_day + 1).'">Employee Attandance Sheet for '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</th></tr>';
		$thead .='<tr><th>Employee</th>';
		$printData='';
		// $punchData = $this->getBiometricData($FromDate,$ToDate,'ALL');
		$punchData = $this->getInOutPunchData($FromDate,$ToDate,'ALL');
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				
				$profile_pic=base_url('assets/images/users/user_default.png');
				if(!empty($emp->emp_profile)){$profile_pic=base_url('assets/images/users/'.$emp->emp_profile);}
				
				$tbody .='<tr>';
				$tbody .='<td datatip="'.$this->empRole[$emp->emp_role].'" flow="down"><b>'.$emp->emp_name.'</b><br><small>'.$emp->title.'</small></td>';
				for($d=$first_day;$d<=$last_day;$d++)
				{
					$attend_status = false; $punch_in = '';$punch_out = '';$total_hours = 0;$overtime = 0;
					if($i==1){$thead .='<th>'.$d.'</th>';}
					$currentDate = date('d-m-Y', strtotime($year.'-'.$month.'-'.$d));
					
					$count = 1;$punchDates = Array();
					if(!empty($punchData))
					{
						foreach($punchData as $device)
						{
							$device->DateString = date('d-m-Y', strtotime($device->DateString));
							if($emp->biomatric_id == (int)$device->Empcode and $currentDate == $device->DateString) 
							{
								$punch_in = "";$punch_out = "";$work_hour = 0;
								$shiftStart = date('d-m-Y H:i:s', strtotime($device->DateString.' '.$emp->shift_start));
								$shiftEnd = date('d-m-Y H:i:s', strtotime('+12 hours',strtotime($shiftStart)));
								
								if($device->INTime != "--:--"):
									$attend_status = true;
									$punch_in =  date('d-m-Y H:i:s', strtotime($device->DateString.' '.$device->INTime));
									
									if($device->OUTTime == "--:--"):
										if(date('H:i:s',strtotime($shiftEnd)) < date("H:i:s")):
											$punch_out = $shiftEnd;
										endif;
									else:
										$punch_out = date('d-m-Y H:i:s', strtotime($device->DateString.' '.$device->OUTTime));
									endif;
									
									if($device->WorkTime == "00:00"):
										if(date('H:i:s',strtotime($shiftEnd)) > date("H:i:s")):
											$time1 = new DateTime(date('H:i:s',strtotime($device->DateString.' '.$device->INTime)));
											$time2 = new DateTime();
											$interval = $time1->diff($time2);
											$total_hours = $interval->format('%H:%I:%S');
										else:
											$total_hours = "00:00:00";
										endif;
									else:
										$total_hours = date('H:i:s', strtotime($device->DateString.' '.$device->WorkTime));
									endif;
									
									if($device->OverTime == "00:00"):
										$overtime = "00:00:00";
									else:
										$overtime = date('H:i:s', strtotime($device->DateString.' '.$device->OverTime));
									endif;
									
									if($device->Late_In == "00:00"):
										$emp->late_time = "00:00:00";
									else:
										$emp->late_time = date('H:i:s', strtotime($device->DateString.' '.$device->Late_In));
									endif;
									
								else:
									$attend_status = false;
								endif;
							}
						}
					}else{$attend_status = false;}
					
					// if(empty($overtime) or $overtime < 0){$overtime=0;}else{$overtime = $overtime.':'.$total_is.' Hrs';}
					$attendanceDate = date("d-m-Y",strtotime($year.'-'.$month.'-'.$d));
					$infotitle = date("jS, F Y ",strtotime($year.'-'.$month.'-'.$d));
					$totalhour = $total_hours.' Hrs';
					$pin = (!empty($punch_in)) ? date("h:i:s A",strtotime($punch_in)) : "";
					$pout = (!empty($punch_out)) ? date("h:i:s A",strtotime($punch_out)) : "";
					$biometricParam = 'data-date="'.$attendanceDate.'" data-emp_name="'.$emp->emp_name.'"  data-emp_id="'.$emp->id.'" data-infotitle="'.$infotitle.'" data-punch_in="'.$pin.'" data-punch_out="'.$pout.'" data-totalhour="'.$totalhour.'" data-overtime="'.$overtime.'"';
					
					if($attend_status):
						$tbody .='<td><a href="javascript:void(0)" '.$biometricParam.' class="attendanceInfo text-success" datatip="Attendance Info" flow="down" ><i class="mdi mdi-check-circle font-bold" ></i></a></td>';
					else:
						$tbody .='<td><a href="javascript:void(0)" '.$biometricParam.' class="text-danger" datatip="Attendance Info" flow="down" ><i class="mdi mdi-close-circle font-bold" ></i></a></td>';
					endif;
				}
				$tbody .='</tr>';$i++;
			}
		}
		$thead .='</tr>';
		return ["status"=>1,"thead"=>$thead,"tbody"=>$tbody];
	}
	
	public function loadAttendanceSheet1($data)
	{
		set_time_limit(0);
		$month  = $data['month'];$current_month  = date("m");
		$year  = ((int)$month >= 1 and (int)$month < 4)?$this->session->userdata('endYear') : $this->session->userdata('startYear');;
		$FromDate = date("d/m/Y",strtotime($year.'-'.$month.'-01'));
		$ToDate  = date("t/m/Y",strtotime($year.'-'.$month.'-01'));
		$first_day = 1;
		$last_day = date("t",strtotime($year.'-'.$month.'-01'));
		
		$data['select'] = "employee_master.*,shift_master.shift_name,shift_master.shift_start,shift_master.total_shift_time,emp_designation.title";
        $data['join']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['tableName'] = $this->empMaster;
		$empData = $this->rows($data);
		
		$thead ='';$tbody ='';$i=1;
		$thead .='<tr><th class="text-center" colspan="'.($last_day + 1).'">Employee Attandance Sheet for '.date("F-Y",strtotime($year.'-'.$month.'-01')).'</th></tr>';
		$thead .='<tr><th>Employee</th>';
		$printData='';
		if(!empty($empData))
		{
			foreach($empData as $emp)
			{
				$ecode = sprintf("%04d", $emp->biomatric_id);
				// $punchData = $this->getBiometricData($FromDate,$ToDate,$ecode);
				$punchData = $this->getInOutPunchData($FromDate,$ToDate,$ecode);
				
				$profile_pic=base_url('assets/images/users/user_default.png');
				if(!empty($emp->emp_profile)){$profile_pic=base_url('assets/images/users/'.$emp->emp_profile);}
				
				$tbody .='<tr>';
				$tbody .='<td datatip="'.$this->empRole[$emp->emp_role].'" flow="down"><b>'.$emp->emp_name.'</b><br><small>'.$emp->title.'</small></td>';
				for($d=$first_day;$d<=$last_day;$d++)
				{
					$attend_status = false; $punch_in = '';$punch_out = '';$total_hours = 0;$overtime = 0;
					if($i==1){$thead .='<th>'.$d.'</th>';}
					$currentDate = date('d/m/Y', strtotime($year.'-'.$month.'-'.$d));
					
					$count = 1;$punchDates = Array();
					if(!empty($punchData))
					{
						foreach($punchData as $device)
						{
							$pdate = strtr($device->PunchDate, '/', '-');
							if($emp->biomatric_id == $device->Empcode and $currentDate == date('d/m/Y', strtotime(strtr($device->PunchDate, '/', '-')))) 
							{
								$punchDates[]=date('Y/m/d H:i:s',strtotime(strtr($device->PunchDate, '/', '-')));
							}
						}
					}
					
					if(!empty($punchDates))
					{
						$punch_in = date('d-m-Y H:i:s', strtotime(min($punchDates)));
						$shiftStart = date('d-m-Y H:i:s', strtotime($d.'-'.$month.'-'.$year.' '.$emp->shift_start));
						$shiftEnd = date('d-m-Y H:i:s', strtotime('+8 hours',strtotime($shiftStart)));
						
						if(strtotime(min($punchDates)) <= strtotime($shiftEnd))
						{
							if( count($punchDates) == 1 ):
								$punch_out = $shiftEnd;
							else:
								$punch_out = date('d-m-Y H:i:s', strtotime(max($punchDates)));
							endif;
							
							$attend_status = true;
							$time1 = new DateTime(date('H:i:s',strtotime($punch_in)));
							$time2 = new DateTime(date('H:i:s',strtotime($punch_out)));
							$interval = $time1->diff($time2);
							$total_hours = $interval->format('%H:%I:%S');
							$total_is = $interval->format('%I:%S');
							$overtime = floatVal($total_hours) - floatVal($emp->total_shift_time);
						}else{$attend_status = false;}
					}else{$attend_status = false;}
					
					if(empty($overtime) or $overtime < 0){$overtime=0;}else{$overtime = $overtime.':'.$total_is.' Hrs';}
					$attendanceDate = date("d-m-Y",strtotime($year.'-'.$month.'-'.$d));
					$infotitle = date("jS, F Y ",strtotime($year.'-'.$month.'-'.$d));
					$totalhour = $total_hours.' Hrs';
					$biometricParam = 'data-date="'.$attendanceDate.'" data-emp_name="'.$emp->emp_name.'"  data-emp_id="'.$emp->id.'" data-infotitle="'.$infotitle.'" data-punch_in="'.date("h:i:s A",strtotime($punch_in)).'" data-punch_out="'.date("h:i:s A",strtotime($punch_out)).'" data-totalhour="'.$totalhour.'" data-overtime="'.$overtime.'"';
					
					if($attend_status):
						$tbody .='<td><a href="javascript:void(0)" '.$biometricParam.' class="attendanceInfo text-success" datatip="Attendance Info" flow="down" ><i class="mdi mdi-check-circle font-bold" ></i></a></td>';
					else:
						$tbody .='<td><a href="javascript:void(0)" '.$biometricParam.' class="text-danger" datatip="Attendance Info" flow="down" ><i class="mdi mdi-close-circle font-bold" ></i></a></td>';
					endif;
				}
				$tbody .='</tr>';$i++;
			}
		}
		$thead .='</tr>';
		return ["status"=>1,"thead"=>$thead,"tbody"=>$tbody];
	}
	
	public function getInOutPunchData($FromDate,$ToDate,$Empcode="ALL")
	{
		$punchData = New StdClass();
		
		$url = 'http://api.etimeoffice.com/api/DownloadInOutPunchData?Empcode='.$Empcode.'&FromDate='.$FromDate.'&ToDate='.$ToDate;
		
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.BIOMETRIC_TOKEN),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else 
		{
			$resultapi = json_decode($response);
			$punchData = $resultapi->InOutPunchData;
		}
		return $punchData;
	}
	
	public function saveBiometricData($fdate,$tdate,$Empcode="ALL")
	{
		$FromDate = date("d/m/Y_00:01",strtotime($fdate));
		$ToDate  = date("d/m/Y_23:59",strtotime($tdate));
		$punchData = New StdClass();
		
		$url = "http://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$Empcode."&FromDate=".$FromDate."&ToDate=".$ToDate;
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.BIOMETRIC_TOKEN),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else 
		{
			$resultapi = json_decode($response);
			$punchData = $resultapi->PunchData;
			$cfdate = date("Y-m-d 00:00:01");$ctdate = date("Y-m-t 23:59:59");
			if(($fdate != $cfdate) and ($tdate != $ctdate)):
				$pnchData = ['id'=>"",'device_id'=>BIOMETRIC_MACHINE_ID,'from_date'=>$fdate, 'to_date'=>$tdate, 'punchdata'=>json_encode($punchData),'created_by'=>$this->loginID];
				$this->store('attendance_monthly_data',$pnchData,'Attandance');
			endif;
		}
		return $punchData;
		
	}
	
	public function getBiometricData($FromDate,$ToDate,$Empcode="ALL")
	{
		return $FromDate;
		return formatDate($FromDate,'Y-m-d h:i:s');
		$punchData = New StdClass();
		
		$url = "http://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$Empcode."&FromDate=".$FromDate."&ToDate=".$ToDate;
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.BIOMETRIC_TOKEN),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else 
		{
			$resultapi = json_decode($response);
			$punchData = $resultapi->PunchData;
			$pnchData = ['device_id'=>BIOMETRIC_MACHINE_ID,'from_date'=>formatDate($FromDate,'Y-m-d h:i:s'), 'to_date'=>formatDate($ToDate,'Y-m-d h:i:s'), 'punchdata'=>json_encode($punchData),'created_by'=>$this->loginID];
			$this->store('attendance_monthly_data',$pnchData,'Attandance');
		}
		return $punchData;
		
	}
	
	public function getEmployeePunchDataDB($FromDate,$ToDate)
	{
		$data['where']['from_date'] = $FromDate;
        $data['where']['to_date'] = $ToDate;
        $data['tableName'] = 'attendance_monthly_data';
		$empData = $this->row($data);
		return $empData;
	}
	
	public function getEmployeeList($biomatric_id='',$deleted = '')
	{
		$empQuery['select'] = "employee_master.*,department_master.name, department_master.section, emp_designation.title, emp_category.category,attendance_policy.early_in as late_in, attendance_policy.early_out,attendance_policy.no_early_in as no_late_in,attendance_policy.no_early_out";
        $empQuery['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $empQuery['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $empQuery['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $empQuery['leftJoin']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
		$empQuery['where']['employee_master.id!='] = 1;
		if(!empty($biomatric_id)){$empQuery['where']['employee_master.biomatric_id'] = $biomatric_id;}
		else{$empQuery['where']['employee_master.biomatric_id!='] = 0;}
		
		if($this->loginId != -1 && $this->loginId != 1){ $empQuery['where']['employee_master.id'] = $this->loginId; }
		
		$empQuery['where']['employee_master.shift_id > '] = 0;
		$empQuery['where']['employee_master.is_active'] = 1;
        if(!empty($deleted)){$data['all']['employee_master.is_delete'] = [0,1];}
		$empQuery['order_by']['employee_master.emp_code'] = 'ASC';
        $empQuery['tableName'] = $this->empMaster;
		$empData = $this->rows($empQuery);
		return $empData;
	}
	
	public function getEmployeeListForMonthAttendance($id="")
	{
		$empQuery['select'] = "employee_master.*,shift_master.shift_name,shift_master.shift_start,shift_master.total_shift_time,shift_master.shift_end,department_master.name, department_master.section, emp_designation.title,emp_category.category,attendance_policy.early_in as late_in,attendance_policy.early_out,attendance_policy.no_early_in as no_late_in,attendance_policy.no_early_out";
        $empQuery['leftJoin']['shift_master'] = "shift_master.id = employee_master.shift_id";
        $empQuery['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $empQuery['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $empQuery['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
        $empQuery['leftJoin']['attendance_policy'] = "attendance_policy.id = employee_master.attendance_policy";
		$empQuery['where']['employee_master.id!='] = 1;
		$empQuery['where']['employee_master.biomatric_id!='] = 0;
		$empQuery['where']['employee_master.is_active'] = 1;
		if(!empty($id)){$empQuery['where']['employee_master.id'] = $id;}
		$empQuery['where']['employee_master.shift_id!='] = 0;
		$empQuery['order_by']['employee_master.emp_code'] = 'ASC';
        $empQuery['tableName'] = $this->empMaster;
		$empData = $this->rows($empQuery);
		return $empData;
	}
	
    /* public function save($data){
        if($this->checkDuplicate($data['emp_contact'],$data['id']) > 0):
            $errorMessage['emp_contact'] = "This Number is already exist.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
			if(empty($data['id'])):
                $data['emp_psc'] = $data['emp_password'];
                $data['emp_password'] = md5($data['emp_password']); 
            endif;
            return $this->store($this->empMaster,$data,'Employee');
        endif;
    }

	public function checkDuplicate($emp_contact,$id=""){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_contact'] = $emp_contact;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    } */

    public function delete($id){
        return $this->trash($this->empMaster,['id'=>$id],'Employee');
    }
    
    public function getAlogSummary($id){
        $queryData = array();
        $queryData['tableName'] = $this->alogSummary;
        $queryData['select'] = "alog_summary.*,employee_master.emp_name,employee_master.emp_dept_id";
        $queryData['leftJoin']["employee_master"] = "employee_master.id = alog_summary.emp_id";
        $queryData['where']['alog_summary.id'] = $id;
        return $this->row($queryData);
    }
    
    public function getAlogSummaryByDate($postData){
        $queryData = array();
        $queryData['tableName'] = $this->alogSummary;
        $queryData['select'] = "alog_summary.*";
        $queryData['where']['alog_summary.attendance_date'] = $postData['attendance_date'];
        $queryData['where']['alog_summary.emp_id'] = $postData['emp_id'];
        return $this->row($queryData);
    }
    
    public function saveEmployeeOt($postData){
        try{
			$this->db->trans_begin();
			$postData['attendance_date'] = $attendance_date = date("Y-m-d",strtotime($postData['attendance_date']));
			$postData['adjust_date'] = date("Y-m-d",strtotime($postData['adjust_date']));
			$emp_id = $postData['emp_id'];
			unset($postData['attendance_date'],$postData['emp_id']);
			$adjustData = Array();$updateData = Array();
            if($postData['ot_type'] == 2)
            {
                $adjDBRow = $this->getAlogSummaryByDate(['attendance_date'=>$postData['adjust_date'],'emp_id'=>$emp_id]);
                
                if(!empty($adjDBRow->id))
                {
        			// Update Adjustment Record
                    $adjustData['id'] = $adjDBRow->id;
                    $adjustData['adj_mins'] = timeToSeconds($postData['ot_mins']);
        			$adjustData['adj_by'] = $this->loginId.'@'.date("Y-m-d H:i:s");
        			$adjustData['adjust_from'] = $postData['id'].'@'.$attendance_date;
        			$adjustData['adj_from_remark'] = $postData['remark'];
        			
        			$result = $this->store($this->alogSummary,$adjustData);
        			//$syncSummary = $this->biometric->syncAttendanceLogSummary(['attendance_date'=>$postData['adjust_date'],'emp_id'=>$emp_id]);
        			
        			// Update Current Record
        			if($result['status']==1)
        			{
            			$updateData['id'] = $postData['id'];
            			$updateData['ot_type'] = $postData['ot_type'];
            			$updateData['dept_id'] = $postData['dept_id'];
                        $updateData['ot_mins'] = timeToSeconds($postData['ot_mins']);
            			$updateData['ot_approved_by'] = $this->loginId;
            			$updateData['ot_approved_at'] = date("Y-m-d H:i:s");
        			    $updateData['adjust_to'] = $adjDBRow->id.'@'.$postData['adjust_date'];
        			    $updateData['remark'] = $postData['remark'];
        			    $result1 = $this->store($this->alogSummary,$updateData);
        			}
        			$result['message'] = "Employee OT Approved successfully.";
                }
            }
            else
            {
                unset($postData['adjust_date']);
                $postData['ot_mins'] = timeToSeconds($postData['ot_mins']);
    			$postData['ot_approved_by'] = $this->loginId;
    			$postData['ot_approved_at'] = date("Y-m-d H:i:s");
        		$postData['adjust_to'] = NULL;
    			$result = $this->store($this->alogSummary,$postData);
    			$result['message'] = "Employee OT Approved successfully.";
            }
			//$syncSummary = $this->biometric->syncAttendanceLogSummary(['attendance_date'=>$attendance_date,'emp_id'=>$emp_id]);
			
			if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
?>