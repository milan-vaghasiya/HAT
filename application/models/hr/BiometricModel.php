	<?php
	class BiometricModel extends MasterModel{
		private $deviceMasterTable = "device_master";
		private $devicePunchesTable = "device_punches";
		private $attendanceLog = "attendance_log";
		private $empPunches = "emp_punches";
		private $attendanceLogSummary = "alog_summary";
		
		/**** NEW STRUCTURE (emp_punches) | Created By JP @09-12-2022 ***/
		public function syncDevicePunchesV2()
		{
			$ddQuery['tableName'] = $this->deviceMasterTable;
			$ddQuery['where']['id'] = 1;
			$deviceData = $this->rows($ddQuery);

			if (!empty($deviceData)) :
				foreach ($deviceData as $row) :
					$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
					$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -1 day' ) ) );
					$end = new DateTime( date( 'Y-m-d' ));
					$end = $end->modify( '+1 day' ); 

					$row->Empcode = 'ALL';
					$row->FromDate = date("d/m/Y_00:01", strtotime($begin->format("Y-m-d")));
					$row->ToDate = date("d/m/Y_23:59", strtotime($end->format("Y-m-d")));

					$punchData = new StdClass();
					$punchData = $this->callDeviceApi($row);
					if (!empty($punchData)) {
						foreach ($punchData as $punch) {
							
							/*$this->db->where('punch_type',1);
							$this->db->where('device_id',$row->id);
							$this->db->where('punch_date',date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-'))));
							$this->db->where('emp_code',$punch->Empcode);
							$oldData = $this->db->get($this->empPunches)->row();*/
							
							$oldQ['tableName'] = $this->empPunches;
							$oldQ['where']['punch_type'] = 1;
							$oldQ['where']['device_id'] = $row->id;
							$oldQ['where']['emp_code'] = $punch->Empcode;
							$oldQ['where']['punch_date'] = date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-')));
							$oldData = $this->row($oldQ);
							
							if (empty($oldData)) {
								$logData = array();
								//$empData = $this->shiftModel->getEmpShiftByEmpcode($punch->Empcode);
								$empData = $this->employee->getEmpByCode($punch->Empcode);
								
								if (!empty($empData)) {
									$logData['id'] = "";
									$logData['punch_type'] = 1;
									$logData['device_id'] = $row->id;
									$logData['punch_date'] = date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-')));
									$logData['emp_id'] = $empData->id;
									$logData['emp_code'] = $punch->Empcode;
									$logData['created_at'] = date('Y-m-d H:i:s');
									$logData['created_by'] = $this->loginID;

									$this->store($this->empPunches, $logData, 'Emp Punches');
								}
							};
						}
					}

					$updateSyncStatus = ['id' => $row->id, 'last_sync_at' => date('Y-m-d H:i:s')];
					$this->store($this->deviceMasterTable, $updateSyncStatus, 'Attandance');
				endforeach;
				return ['status' => 1, 'message' => 'Device Synced successfully.', 'lastSyncedAt' => date('j F Y, g:i a')];
			else :
				return ['status' => 0, 'message' => 'You have no any Devices!'];
			endif;
		}
		
		
		/**** NEW STRUCTURE (attendance_log) | Created By JP @09-12-2022 ***/
		public function syncDevicePunches()
		{
			$ddQuery['tableName'] = $this->deviceMasterTable;
			$ddQuery['where']['id'] = 1;
			$deviceData = $this->rows($ddQuery);

			if (!empty($deviceData)) :
				foreach ($deviceData as $row) :
					$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
					$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -1 day' ) ) );
					$end = new DateTime( date( 'Y-m-d' ));
					$end = $end->modify( '+1 day' ); 

					$row->Empcode = 'ALL';
					$row->FromDate = date("d/m/Y_00:01", strtotime($begin->format("Y-m-d")));
					$row->ToDate = date("d/m/Y_23:59", strtotime($end->format("Y-m-d")));

					$punchData = new StdClass();
					$punchData = $this->callDeviceApi($row);
					if (!empty($punchData)) {
						foreach ($punchData as $punch) {
							
							$this->db->where('punch_type',1);
							$this->db->where('device_id',$row->id);
							$this->db->where('punch_date',date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-'))));
							$this->db->where('emp_code',$punch->Empcode);
							$oldData = $this->db->get($this->attendanceLog)->row();

							if (empty($oldData)) {
								$logData = array();
								//$empData = $this->shiftModel->getEmpShiftByEmpcode($punch->Empcode);
								$empData = $this->employee->getEmpByCode($punch->Empcode);

								if (!empty($empData)) {
									$logData['id'] = "";
									$logData['punch_type'] = 1;
									$logData['device_id'] = $row->id;
									$logData['punch_date'] = date('Y-m-d H:i:s', strtotime(strtr($punch->PunchDate, '/', '-')));
									$logData['emp_id'] = $empData->id;
									$logData['emp_code'] = $punch->Empcode;
									$logData['created_at'] = date('Y-m-d H:i:s');
									$logData['created_by'] = $this->loginID;

									$this->store($this->attendanceLog, $logData, 'Attandance Log');
								}
							}
						}
					}

					$updateSyncStatus = ['id' => $row->id, 'last_sync_at' => date('Y-m-d H:i:s')];
					$this->store($this->deviceMasterTable, $updateSyncStatus, 'Attandance');
				endforeach;
				return ['status' => 1, 'message' => 'Device Synced successfully.', 'lastSyncedAt' => date('j F Y, g:i a')];
			else :
				return ['status' => 0, 'message' => 'You have no any Devices!'];
			endif;
		}
		
		public function syncDeviceData()
		{
			$ddQuery['tableName'] = $this->deviceMasterTable;
			$ddQuery['where']['id'] = 1;
			$deviceData = $this->rows($ddQuery);
			if(!empty($deviceData)):
				foreach($deviceData as $row):
					$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
					
					$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -1 day' ) ) );
					//$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced) ) );
					$end = new DateTime( date( 'Y-m-d' ));
					$end = $end->modify( '+1 day' ); 
					
					$interval = new DateInterval('P1D');
					$daterange = new DatePeriod($begin, $interval ,$end);
					
					foreach($daterange as $date){
						$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
						$row->Empcode = 'ALL';
						$row->FromDate = date("d/m/Y_00:01",strtotime($date->format("Y-m-d")));
						$row->ToDate = date("d/m/Y_23:59",strtotime($date->format("Y-m-d")));
						
						$punchData = New StdClass();
						$punchData = $this->callDeviceApi($row);
						$pcData = Array();
						if(!empty($punchData)):
							$dd1Query['tableName'] = $this->devicePunchesTable;
							$dd1Query['where']['punch_date'] = $currentDate;
							$oldData = $this->row($dd1Query);
							$pnchData = Array();
							
							if(empty($oldData)):
								$pnchData = ['id'=>"",'device_id'=>$row->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData),'created_by'=>$this->loginID];
								$this->store($this->devicePunchesTable,$pnchData,'Attandance');
							else:
								$pnchData = ['id'=>$oldData->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData)];
								$this->store($this->devicePunchesTable,$pnchData,'Attandance');
							endif;
							
							// Add Records to new Table
							$res = $this->syncDeviceDataNew($punchData,$currentDate);
						endif;
					}
					$updateSyncStatus = ['id'=>$row->id,'last_sync_at'=>date( 'Y-m-d H:i:s')];
					$this->store($this->deviceMasterTable,$updateSyncStatus,'Attandance');
				endforeach;
				return ['status'=>1,'message'=>'Device Synced successfully.','lastSyncedAt'=>date('j F Y, g:i a')];
			else:
				return ['status'=>0,'message'=>'You have no any Devices!'];
			endif;
		}
		
		public function getAttendanceLog($FromDate,$ToDate,$empId)
		{
			$queryData['tableName'] = $this->attendanceLog;
			$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
			$queryData['where']['emp_id'] = $empId;
			return $this->rows($queryData);
		}
		
		/*** Created By JP@10-12-2022 ***/
		public function getAttendanceLogByEmp($punchDate, $empId)
		{
			$toDate = date('Y-m-d',strtotime($punchDate . ' +1 day'));
			$queryData['tableName'] = 'attendance_log';
			$queryData['customWhere'][] = 'punch_date BETWEEN "' . date('Y-m-d H:i:s', strtotime($punchDate . ' 00:00:01')) . '" AND "' . date('Y-m-d H:i:s', strtotime($toDate . ' 23:59:59')) . '"';
			$queryData['where']['emp_id'] = $empId;
			$queryData['where_in']['punch_type'] = "1,2";
			return $this->row($queryData);
		}
			
		/*** Created By : JP@27.12.2022***/
		public function getEmpPunchesByDate($empId,$currentDate)
		{
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time,shift_master.lunch_start,shift_master.lunch_end, shift_master.shift_type,shift_master.shift_name,ROUND(TIME_TO_SEC(shift_master.regular_ot)) as rot_time,ROUND(TIME_TO_SEC(shift_master.ru_in_time)) as ru_in_time,ROUND(TIME_TO_SEC(shift_master.rd_in_time)) as rd_in_time,ROUND(TIME_TO_SEC(shift_master.ru_out_time)) as ru_out_time,ROUND(TIME_TO_SEC(shift_master.rd_out_time)) as rd_out_time, shift_master.shift_start,shift_master.shift_end,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date,
			(
				SELECT GROUP_CONCAT(al.id) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_id,
			(
				SELECT GROUP_CONCAT(al.punch_type) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_type,
			(
				SELECT SUM(((ex_hours*60)+ex_mins)*xtype) as ex_mins FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.xtype=1 AND al.is_delete=0
			) as xmins,
			(
				SELECT SUM(((ex_hours*60)+ex_mins)*xtype) as ex_mins FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.xtype!=1 AND al.is_delete=0
			) as xminsMinus,
			(
				SELECT GROUP_CONCAT(al.remark) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.xtype=1 AND al.is_delete=0 
			) as xremark
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			WHERE emp_shiftlog.emp_id = ".$empId." AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year)->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@27.12.2022***/
		public function getEmpShiftLog($postData = [])
		{
			$queryData['tableName'] = 'emp_shiftlog';
			$queryData['select'] = 'emp_shiftlog.*,employee_master.emp_code,employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg, emp_category.category';		
			$queryData['join']['employee_master'] = "employee_master.id = emp_shiftlog.emp_id";
			$queryData['leftJoin']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
			$queryData['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
			$queryData['leftJoin']['emp_category'] = "employee_master.emp_category = emp_category.id";
			if(!empty($postData['emp_id'])){$queryData['where']['emp_shiftlog.emp_id'] = $postData['emp_id'];}
			if(!empty($postData['biomatric_id'])){$queryData['where']['employee_master.biomatric_id'] = $postData['biomatric_id'];}
			if(!empty($postData['dept_id'])){$queryData['where']['employee_master.emp_dept_id'] = $postData['dept_id'];}
			
			if(!empty($postData['emp_type'])):
				/*if($postData['emp_type'] == 1):
					$queryData['where']['employee_master.emp_type'] = 1;
				else:
					$queryData['where']['employee_master.emp_type !='] = 1;
				endif;*/
				if(!empty($postData['etype']) AND $postData['etype'] == 'STRICT'):
					$queryData['where']['employee_master.emp_type'] = $postData['emp_type'];
				else:
					if($postData['emp_type'] == 1):
						$queryData['where']['employee_master.emp_type'] = 1;
					else:
						$queryData['where']['employee_master.emp_type !='] = 1;
					endif;
				endif;
			endif;
			
			$queryData['where_in']['MONTH(emp_shiftlog.month)'] = date('m',strtotime($postData['month']));
			$queryData['where_in']['YEAR(emp_shiftlog.month)'] = date('Y',strtotime($postData['month']));
			$queryData['where']['employee_master.is_active'] = 1;
			$queryData['where']['employee_master.attendance_status'] = 1;
			
			$result = $this->rows($queryData);
			//$this->printQuery();
			return $result;
		}
		
		/*** Created By : JP@28.12.2022***/
		public function getMissedPunch($currentDate)
		{
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punchCount
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id HAVING (((punchCount%2) = 1) OR (punchCount > 4))")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@04.01.2022 ***/
		public function getAllPunches($postData = [])
		{
			$report_date = $postData['report_date'];
			$day = date('d',strtotime($report_date));$month = date('m',strtotime($report_date));$year = date('Y',strtotime($report_date));
			$countCondition = '';
			
			if(empty($postData['punch_status'])){$countCondition = ' HAVING punchCount > 0';}
			else
			{
				// Missed Punches
				if($postData['punch_status'] == 1){$countCondition = ' HAVING (((punchCount%2) = 1) OR (punchCount > 4))';}
				// Absent Punch
				if($postData['punch_status'] == 2){$countCondition = ' HAVING punchCount = 0';}
			}
			
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type, employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
			CAST(DATE_SUB(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date,
			(
				SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punchCount
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id".$countCondition)->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@10.03.2022 ***/
		public function getMissedPunches($postData = [])
		{
			$report_date = $postData['report_date'];
			$day = date('d',strtotime($report_date));$month = date('m',strtotime($report_date));$year = date('Y',strtotime($report_date));
			
			$countCondition = ' HAVING (((punchCount%2) = 1) AND punchCount > 0 AND punchCount < 4)';
			
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type, employee_master.emp_code, employee_master.emp_name,
			CAST(DATE_SUB(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$report_date."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date,
			(
				SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punchCount
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id".$countCondition)->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@02.01.2023***/
		public function getAbsentReport($currentDate)
		{
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT COUNT(*) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punchCount
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE employee_master.attendance_status = 1 AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id HAVING punchCount = 0 ORDER BY department_master.name")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@30.12.2022***/
		public function getTotalPunchesByDate($currentDate)
		{
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name ,employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." GROUP BY emp_shiftlog.emp_id")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@29.12.2022***/
		public function getPunchByDate($currentDate)
		{
		    $cdate = date('Y-m-d',strtotime($currentDate));
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type,  shift_master.shift_start, employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg,
			CAST(CONCAT('".$currentDate."', ' ', shift_master.shift_start) as datetime) as shift_start,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 2 HOUR) as datetime) as shiftStart,
			CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 79199 SECOND) as datetime) as shiftEnd,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = emp_shiftlog.emp_id AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." AND employee_master.is_active=1 AND employee_master.attendance_type=1 AND employee_master.is_delete=0 AND ((employee_master.emp_relieve_date IS NULL) OR (employee_master.emp_relieve_date >= '".$cdate."') OR (employee_master.emp_relieve_date = '0000-00-00')) GROUP BY emp_shiftlog.emp_id")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@29.12.2022***/
		public function getShiftByDate($postData)
		{
			$currentDate = $postData['shift_date'];$deptCondition = '';$newEmpCondition = '';
			if(!empty($postData['dept_id'])){$deptCondition = 'AND employee_master.emp_dept_id = '.$postData['dept_id'];}
			if(!empty($postData['shift_status'])){$newEmpCondition = 'AND employee_master.shift_id = 0';}
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.id,emp_shiftlog.emp_id,shift_master.shift_name, shift_master.shift_type,  employee_master.emp_code, employee_master.emp_name, department_master.name as dept_name, emp_designation.title as emp_dsg
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master ON employee_master.id = emp_shiftlog.emp_id
			LEFT JOIN department_master ON department_master.id = employee_master.emp_dept_id
			LEFT JOIN emp_designation ON emp_designation.id = employee_master.emp_designation
			WHERE MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year." AND employee_master.attendance_status = 1 ".$deptCondition." ".$newEmpCondition." GROUP BY emp_shiftlog.emp_id")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		public function getPunchData($FromDate,$ToDate,$device_id=2)
		{
			$queryData['tableName'] = $this->devicePunchesTable;
			$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
	// 		$queryData['where']['device_id'] = $device_id;
			return $this->rows($queryData);
		}
		
		public function getPunchData1($FromDate,$ToDate,$device_id=2)
		{
			$queryData['tableName'] = $this->devicePunchesTable;
			$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
	// 		$queryData['where']['device_id'] = $device_id;
			return $this->rows($queryData);
		}
		
		public function getDeviceData($device_id=1)
		{
			$ddQuery['tableName'] = $this->deviceMasterTable;
			$ddQuery['limit'] = 1;
			return $this->rows($ddQuery);
		}
		
		public function callDeviceApi($deviceData)
		{
			$punchData = New StdClass();
			$curl = curl_init();
			$api_url = "https://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$deviceData->Empcode."&FromDate=".$deviceData->FromDate."&ToDate=".$deviceData->ToDate;
			curl_setopt_array($curl, array(
				CURLOPT_URL => $api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			
			if ($err) {echo "cURL Error #:" . $err;exit;}
			else 
			{
				$resultapi = json_decode($response);
				$punchData = $resultapi->PunchData;
			}
			return $punchData;
		}
		
		public function addEmpDevice($deviceData)
		{
			$punchData = New StdClass();
			$curl = curl_init();
			$api_url = "https://api.etimeoffice.com/api/AddEmployee?Empcode=".$deviceData->Empcode."&EmpName=".$deviceData->emp_name."&DeviceSerialNo=".$deviceData->device_srno;
			
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
				CURLOPT_POSTFIELDS => ['Empcode'=>$deviceData->Empcode,'EmpName'=>$deviceData->emp_name,'DeviceSerialNo'=>$deviceData->device_srno]
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			
			if ($err):
				return ['status'=>0,'result'=>$err];
			else:
				return ['status'=>1,'result'=>$response];
			endif;
			
		}
		
		public function removeEmpDevice($deviceData)
		{
			$punchData = New StdClass();
			$curl = curl_init();
			$api_url = "https://api.etimeoffice.com/api/DeleteEmployee?Empcode=".$deviceData->Empcode."&EmpName=".$deviceData->emp_name."&DeviceSerialNo=".$deviceData->device_srno;
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
				CURLOPT_POSTFIELDS => ['Empcode'=>$deviceData->Empcode,'EmpName'=>$deviceData->emp_name,'DeviceSerialNo'=>$deviceData->device_srno]
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			
			if ($err):
				return ['status'=>0,'result'=>$err];
			else:
				return ['status'=>1,'result'=>$response];
			endif;
		}

		/*** Created By JP@14-02-2023 ***/
		public function getLastShiftByEmpCode($emp_code)
		{
			$queryData['tableName'] = $this->empPunches;
			$queryData['select'] = 'shift_id,attendance_date';
			$queryData['where']['emp_code'] = $emp_code;
			$queryData['where']['shift_id != '] = 0;
			$queryData['order_by']['punch_date'] = 'DESC';
			return $this->row($queryData);
		}
		
		/*** Migrate Attendance Log into alog_summary ***/
		public function saveAlogSummaryData($postData){
			try {
				$this->db->trans_begin();
                
				$i=1;
				foreach($postData['dateRange'] as $date):
					$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
					$nextDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
					$currentDay = date('D', strtotime($currentDate));
					if(!empty($postData['empData'])):
						foreach($postData['empData'] as $row):						
							$empAttendanceLog = $this->arrangeAttendanceDate($row->id, $currentDate);
							foreach($empAttendanceLog as $aRow):
								
								$oldQ['tableName'] = $this->attendanceLogSummary;
								$oldQ['where']['emp_id'] = $aRow->emp_id;
								$oldQ['where']['attendance_date'] = $currentDate;
								$oldQ['where']['is_delete'] = 0;
								$oldData = $this->row($oldQ);

								//$aRow = (object) $aRow;
								$alogSummaryData = [
									'id' => (!empty($oldData))?$oldData->id:"",
									'emp_id' => $aRow->emp_id,
									'emp_code' => $aRow->emp_code,
									'attendance_date' => $currentDate,
									'shift_id' => (!empty($aRow->shift_id))?$aRow->shift_id:0,
									'punch_date' => $aRow->punch_date,
									'ex_mins' => (!empty($aRow->xmins))? ($aRow->xmins*60):0
								];
								$result = $this->store($this->attendanceLogSummary,$alogSummaryData);
								$i++;
							endforeach;
						endforeach;					
					endif;
				endforeach;

				if ($this->db->trans_status() !== FALSE) :
					$this->db->trans_commit();
					return ['status'=>1,"message"=>$i." Attendace Log Summary saved successfully."];
				endif;
			} catch (\Exception $e) {
				$this->db->trans_rollback();
				return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
			}
		}

		/*** Created By : JP@20.02.2023 ***/
		public function arrangeAttendanceDate($empId,$currentDate)
		{
			$day = date('d',strtotime($currentDate));$month = date('m',strtotime($currentDate));$year = date('Y',strtotime($currentDate));
			$attendanceData = $this->db->query("SELECT emp_shiftlog.emp_id,shift_master.id as shift_id,
			CAST(DATE_SUB(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 3600 SECOND) as datetime) as shiftStart,emp.emp_code,
			CAST(DATE_ADD(CONCAT('".$currentDate."', ' ', shift_master.shift_start), INTERVAL 82799 SECOND) as datetime) as shiftEnd,
			(
				SELECT GROUP_CONCAT(al.punch_date) FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type IN (1,2) AND al.punch_date >= shiftStart AND al.punch_date <= shiftEnd AND al.is_delete=0
			) as punch_date, 
			(
				SELECT SUM(((ex_hours*60)+ex_mins)*xtype) as ex_mins FROM attendance_log as al WHERE al.emp_id = ".$empId." AND al.punch_type=3 AND DATE_FORMAT(al.punch_date, '%Y-%m-%d') = '".$currentDate."' AND al.is_delete=0
			) as xmins
			FROM emp_shiftlog
			LEFT JOIN shift_master ON shift_master.id = emp_shiftlog.d".intval($day)."
			LEFT JOIN employee_master emp ON emp.id = emp_shiftlog.emp_id
			WHERE emp_shiftlog.emp_id = ".$empId." AND MONTH(emp_shiftlog.month) = ".$month." AND YEAR(emp_shiftlog.month) = ".$year ." AND emp_shiftlog.d".intval($day)." > 0 HAVING punch_date IS NOT NULL")->result();
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Sync Alog Summary for Extra Hours and Manual Attendance ***/
		public function syncAttendanceLogSummary($data){
			try{
				$this->db->trans_begin();
				
				$empAttendanceLog = $this->arrangeAttendanceDate($data['emp_id'], $data['attendance_date']);
				
				foreach($empAttendanceLog as $row):
					
					$oldQ['tableName'] = $this->attendanceLogSummary;
					$oldQ['where']['emp_id'] = $row->emp_id;
					$oldQ['where']['attendance_date'] = $data['attendance_date'];
					$oldData = $this->row($oldQ);

					$row = (object) $row;
					$alogSummaryData = [
						'id' => (!empty($oldData))?$oldData->id:"",
						'emp_id' => $row->emp_id,
						'emp_code' => $row->emp_code,
						'attendance_date' => $data['attendance_date'],
						'shift_id' => (!empty($row->shift_id))?$row->shift_id:0,
						'punch_date' => $row->punch_date,
						'ex_mins' => (!empty($row->xmins))? ($row->xmins*60):0
					];
					$this->store($this->attendanceLogSummary,$alogSummaryData);
				endforeach;

				if ($this->db->trans_status() !== FALSE):
					$this->db->trans_commit();
					return true;
				endif;
			}catch(\Exception $e){
				$this->db->trans_rollback();
				return false;
			}	
		}

		/*** Created By : JP@14.02.2023 ***/
		public function migrateAttendanceDate($postData)
		{
			try {
				$this->db->trans_begin();
				$result = Array();
				if(!empty($postData))
				{
					$where['punch_type'] = 1;
					$where['emp_id'] = $postData['emp_id'];
					$where['emp_code'] = $postData['emp_code'];
					$where['punch_date'] = $postData['punch_date'];
					
					$result = $this->edit($this->attendanceLog, $where, ['attendance_date' => $postData['attendance_date'], 'shift_id' => $postData['shift_id'], 'updated_at' => date('Y-m-d H:i:s')]);
					//$this->printQuery();
				}
				
				if ($this->db->trans_status() !== FALSE) :
					$this->db->trans_commit();
					return $result;
				endif;
			} catch (\Exception $e) {
				$this->db->trans_rollback();
				return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
			}
		}
		
		/*** Created By : JP@14.02.2023**/
		public function getDateWiseSummary($postData)
		{
			$aData['tableName'] = $this->attendanceLog;
			$aData['select'] = "attendance_log.punch_date,attendance_log.punch_type, attendance_log.xtype,
			(((ex_hours*60)+ex_mins)*xtype) as ex_mins, shift_master.shift_type, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time,shift_master.lunch_start, shift_master.lunch_end, shift_master.shift_name, ROUND(TIME_TO_SEC(shift_master.regular_ot)) as rot_time,ROUND(TIME_TO_SEC(shift_master.ru_in_time)) as ru_in_time,ROUND(TIME_TO_SEC(shift_master.rd_in_time)) as rd_in_time, ROUND(TIME_TO_SEC(shift_master.ru_out_time)) as ru_out_time, ROUND(TIME_TO_SEC(shift_master.rd_out_time)) as rd_out_time,shift_master.shift_name";
			$aData['leftJoin']['shift_master'] = "attendance_log.shift_id=shift_master.id";
			$aData['where']['attendance_log.attendance_date'] = $postData['attendance_date'];
			$aData['where']['attendance_log.emp_code'] = $postData['emp_code'];
			$aData['where']['attendance_log.shift_id > '] = 0;
			$attendanceData = $this->rows($aData);
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@14.02.2023 ***/
		public function getDateWiseSummaryV2_old($postData)
		{
			$aData['tableName'] = $this->attendanceLogSummary;
			$aData['select'] = "alog_summary.emp_code,alog_summary.emp_id,alog_summary.punch_date,alog_summary.attendance_date, alog_summary.ex_mins, shift_master.shift_type, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time,shift_master.lunch_start, shift_master.lunch_end, shift_master.shift_name, shift_master.shift_name, emp.emp_name, emp.emp_joining_date, dept.name as dept_name";
			$aData['leftJoin']['shift_master'] = "alog_summary.shift_id=shift_master.id";
			$aData['leftJoin']['employee_master emp'] = "emp.id=alog_summary.emp_id";
			$aData['leftJoin']['department_master dept'] = "emp.emp_dept_id = dept.id";
			$aData['where']['alog_summary.attendance_date >= '] = $postData['from_date'];
			$aData['where']['alog_summary.attendance_date <= '] = $postData['to_date'];
			if(!empty($postData['emp_code'])){$aData['where']['alog_summary.emp_code'] = $postData['emp_code'];}
			$aData['order_by']['alog_summary.emp_code'] = 'ASC';
			$aData['order_by']['alog_summary.attendance_date'] = 'ASC';
			$attendanceData = $this->rows($aData);
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@14.02.2023 ***/
		public function getDateWiseSummaryV2($postData)
		{
			$aData['tableName'] = $this->attendanceLogSummary;
			$aData['select'] = "alog_summary.emp_code,alog_summary.emp_id,alog_summary.punch_date,alog_summary.attendance_date, alog_summary.ex_mins, alog_summary.ot_mins, alog_summary.adj_mins, alog_summary.adjust_from, alog_summary.adjust_to, shift_master.shift_type, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as shiftStart, ROUND(TIME_TO_SEC(shift_master.shift_end)) as shiftEnd, shift_master.lunch_start, shift_master.lunch_end, shift_master.shift_name, shift_master.shift_start, shift_master.shift_end, emp.emp_name, emp.emp_joining_date, dept.name as dept_name, IFNULL(ltp.minute_day, 0) as ltp_minute, IFNULL(ltp.day_month, 0) as ltp_days, IFNULL(ltp.penalty_hrs, 0) as ltp_phrs, IFNULL(eop.minute_day, 0) as eop_minute, IFNULL(eop.day_month, 0) as eop_days, IFNULL(eop.penalty_hrs, 0) as eop_phrs, ROUND(TIME_TO_SEC(shift_master.regular_ot)) as rot_time,shift_master.ru_in_time, shift_master.rd_in_time, shift_master.ru_out_time, shift_master.rd_out_time";
			$aData['leftJoin']['shift_master'] = "alog_summary.shift_id=shift_master.id";
			$aData['leftJoin']['employee_master emp'] = "emp.id=alog_summary.emp_id";
			$aData['leftJoin']['department_master dept'] = "emp.emp_dept_id = dept.id";
			$aData['leftJoin']['attendance_policy ltp'] = "emp.lt_policy = ltp.id AND ltp.penalty=1";
			$aData['leftJoin']['attendance_policy eop'] = "emp.eo_policy = eop.id AND eop.penalty=1";
			$aData['where']['alog_summary.attendance_date >= '] = $postData['from_date'];
			$aData['where']['alog_summary.attendance_date <= '] = $postData['to_date'];
			if(!empty($postData['emp_code'])){$aData['where']['alog_summary.emp_code'] = $postData['emp_code'];}
			if(!empty($postData['emp_id'])){$aData['where']['alog_summary.emp_id'] = $postData['emp_id'];}
			if(!empty($postData['dept_id'])){$aData['where']['emp.emp_dept_id'] = $postData['dept_id'];}
			$aData['order_by']['alog_summary.emp_code'] = 'ASC';
			$aData['order_by']['alog_summary.attendance_date'] = 'ASC';
			$attendanceData = $this->rows($aData);
			//$this->printQuery();
			return $attendanceData;
		}
		
		/*** Created By : JP@22.02.2023 ***/
		public function getALogSummary($postData)
		{
			if(!empty($postData['payroll']))
			{
				$postData['from_date'] = date('Y-m-01',strtotime($postData['from_date']));
				$postData['to_date'] = date('Y-m-t',strtotime($postData['from_date']));
			}
			else
			{
				$postData['from_date'] = date('Y-m-d',strtotime($postData['from_date']));
				$postData['to_date'] = date('Y-m-d',strtotime($postData['to_date']));
			}
			
			
			$aData['tableName'] = $this->attendanceLogSummary;
			$aData['select'] = "alog_summary.id,alog_summary.emp_code,alog_summary.emp_id,alog_summary.punch_date,alog_summary.attendance_date, alog_summary.ex_mins, alog_summary.ot_mins, alog_summary.ot_approved_by, otemp.emp_name as ot_approved_by_name, alog_summary.ot_approved_at, alog_summary.adj_mins, alog_summary.adjust_from, alog_summary.adjust_to, shift_master.shift_type, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as ts_time, ROUND(TIME_TO_SEC(shift_master.total_lunch_time)) as lunch_time, ROUND(TIME_TO_SEC(shift_master.total_shift_time)) as shiftStart, ROUND(TIME_TO_SEC(shift_master.shift_end)) as shiftEnd, shift_master.lunch_start, shift_master.lunch_end, shift_master.shift_name, shift_master.shift_start, shift_master.shift_end, emp.emp_name, emp.emp_joining_date, emp.emp_type, dept.name as dept_name, IFNULL(ltp.minute_day, 0) as ltp_minute, IFNULL(ltp.day_month, 0) as ltp_days, IFNULL(ltp.penalty_hrs, 0) as ltp_phrs, IFNULL(eop.minute_day, 0) as eop_minute, IFNULL(eop.day_month, 0) as eop_days, IFNULL(eop.penalty_hrs, 0) as eop_phrs, ROUND(TIME_TO_SEC(shift_master.regular_ot)) as rot_time,shift_master.ru_in_time, shift_master.rd_in_time, shift_master.ru_out_time, shift_master.rd_out_time";
			
			$aData['leftJoin']['shift_master'] = "alog_summary.shift_id=shift_master.id";
			$aData['leftJoin']['employee_master emp'] = "emp.id=alog_summary.emp_id";
			$aData['leftJoin']['employee_master otemp'] = "otemp.id=alog_summary.ot_approved_by";
			$aData['leftJoin']['department_master dept'] = "emp.emp_dept_id = dept.id";
			$aData['leftJoin']['attendance_policy ltp'] = "emp.lt_policy = ltp.id AND ltp.penalty=1";
			$aData['leftJoin']['attendance_policy eop'] = "emp.eo_policy = eop.id AND eop.penalty=1";
			if(!empty($postData['payroll']))
			{
				$aData['select'] .= ",advance.advance_json,loan.loan_json";
				$aData['leftJoin']["(SELECT emp_id, CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', id, 'entry_date', entry_date, 'payment_mode', payment_mode, 'pending_amount', (sanctioned_amount - deposit_amount))),']') AS advance_json FROM advance_salary WHERE is_delete = 0 AND (sanctioned_amount - deposit_amount) > 0 AND entry_date <= '".date("Y-m-t",strtotime($postData['from_date']))."' GROUP BY emp_id) as advance"] = "advance.emp_id = alog_summary.emp_id";

				$aData['leftJoin']["(SELECT emp_id,	CONCAT('[', GROUP_CONCAT(JSON_OBJECT('id', id, 'loan_no', trans_number, 'payment_mode', payment_mode, 'emi_amount', emi_amount, 'pending_amount', (sanctioned_amount - deposit_amount))),']')  AS loan_json
				FROM emp_loan WHERE is_delete = 0 AND (sanctioned_amount - deposit_amount) > 0 AND entry_date <= '".date("Y-m-t",strtotime($postData['from_date']))."' AND trans_status = 2 GROUP BY emp_id) as loan"] = "loan.emp_id = alog_summary.emp_id";
			}
			$aData['where']['alog_summary.attendance_date >= '] = $postData['from_date'];
			$aData['where']['alog_summary.attendance_date <= '] = $postData['to_date'];
			
			if(!empty($postData['emp_code'])){$aData['where']['alog_summary.emp_code'] = $postData['emp_code'];}
			if(!empty($postData['emp_id'])){$aData['where']['alog_summary.emp_id'] = $postData['emp_id'];}
			if(!empty($postData['dept_id'])){$aData['where']['emp.emp_dept_id'] = $postData['dept_id'];}
			if(!empty($postData['format_id'])){$aData['where']['emp.ctc_format'] = $postData['format_id'];}
			
			if(!empty($postData['emp_type'])){$aData['where_in']['emp.emp_type'] = $postData['emp_type'];}
			
			$aData['order_by']['alog_summary.attendance_date'] = 'ASC';
			$empAttendanceLog = $this->rows($aData);
			//$this->printQuery();
			return $empAttendanceLog;
		}
		
		/*** Created By : JP@03.03.2023 ***/
		public function getSalaryHours($postData)
		{		
			$empAttendanceLog = $this->getALogSummary($postData);
			
			$empTable='';$empSal=Array();$ltCount=0;$eoCount=0;$ltPenalty=0;$eoPenalty=0;
			foreach($empAttendanceLog as $row)
			{
				if(!isset($empSal[$row->emp_id]['twh'])){$empSal[$row->emp_id]['twh']=0;$ltCount=0;$eoCount=0;}
				if(!isset($empSal[$row->emp_id]['tpd'])){$empSal[$row->emp_id]['tpd']=0;}
				if(!isset($empSal[$row->emp_id]['tot'])){$empSal[$row->emp_id]['tot']=0;}
				if(!isset($empSal[$row->emp_id]['tst'])){$empSal[$row->emp_id]['tst']=$row->ts_time;}
				$empPunches = explode(',',$row->punch_date);
				$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
				$empPunches = sortDates($empPunches,$sortType);
				
				$currentDay = date('D', strtotime($row->attendance_date));
				$dateKey = date('d', strtotime($row->attendance_date));
				$xmins = $row->ex_mins;
				$startLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_start));
				$endLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_end));
				
				$shift_start = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->shift_start));
				$nextDate = ($row->shift_type == 2) ? date('Y-m-d', strtotime($row->attendance_date . ' +1 day')) : $row->attendance_date;
				$shift_end = date('d-m-Y H:i:s', strtotime($nextDate.' '.$row->shift_end));
				$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
				$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$xtime='';$status='';$ps='';
				$late_in_s = 0;$early_in_s = 0;$early_out_s = 0;$late_out_s = 0;$lastp_index = count($empPunches)-1;
				
				// Count Early In, Late In, Early Out, Late Out Time in Seconds
				if(strtotime($punch_in) < strtotime($shift_start)){$early_in_s = strtotime($shift_start) - strtotime($punch_in);}
				if(strtotime($punch_in) > strtotime($shift_start)){$late_in_s = strtotime($punch_in) - strtotime($shift_start);}
				if(strtotime($punch_out) < strtotime($shift_end)){$early_out_s = strtotime($shift_end) - strtotime($punch_out);}
				if(strtotime($punch_out) > strtotime($shift_end)){$late_out_s = strtotime($punch_out) - strtotime($shift_end);}
				
				// Apply Attendance Policy
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
				
				/*
				// Count Overtime as per shift
				if(strtotime($punch_out) > strtotime($shift_end)){$ot = strtotime($punch_out) - strtotime($shift_end);}
				$row->ot_mins = (empty($row->adjust_to)) ? $row->ot_mins : 0;
				*/
				
				// Count Total Time [1-2,3-4,5-6.....]
				foreach($empPunches as $punch)
				{
					$wph[$idx][]=strtotime($punch);
					if($t%2 == 0){$stay_time += floatVal($wph[$idx][1]) - floatVal($wph[$idx][0]);$idx++;}
					$t++;
				}
				$twh = $stay_time;
				
				$sh = (8*3600); // In Seconds
				// Count Overtime as per shift
				if($twh > $sh){$ot = $twh - $sh;}
				$row->ot_mins = (empty($row->adjust_to)) ? $row->ot_mins : 0;
				
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
							//$xlt = $countedLT - $row->lunch_time;
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
				
				$wh = $twh - $ot;
				$twh = $twh - $ot + $row->ot_mins; // Actual OT (-) and Approved OT (+)
				$twh += $row->ex_mins;
				$twh1 = $twh;
				
				$trimSeconds = 1800;$upperTrim = 1500;$lowerTrim = 300;// Round Punch By Seconds
				$trimDay = $twh % $trimSeconds;
				if($trimDay < $upperTrim){$twh -= $trimDay;}else{$twh += ($trimSeconds-$trimDay);}
							
				// Set Present/Absent/ Status
				$ps='A';$workDays = 0;$hdLimit = 0;$minLimitPerDay = 14400;	
				if($row->emp_type == 1){ $hdLimit = $row->ts_time;}else{$hdLimit = 28800;}
				if(count($empPunches) > 0)
				{
					if($twh > $minLimitPerDay)
					{
						$ps='P';
						if($twh >= $hdLimit){if($currentDay != 'Wed'){$workDays=1;}}else{$workDays=0.5;}
						
					}				
					else{$ps='A';}
					if(count($empPunches) % 2 != 0){$ps='M';}
				}
				else{$ps = 'A';}
				if($currentDay == 'Wed'){$ps = 'WO';}
				
				if(!empty($postData['payroll']))
				{
					$empSal[$row->emp_id]['advance_data'] = (!empty($row->advance_json))?json_decode($row->advance_json):[];
					$empSal[$row->emp_id]['loan_data'] = (!empty($row->loan_json))?json_decode($row->loan_json):[];
				}
				
				if(!empty($postData['datewise'])){$empSal[$row->emp_id][intVal($dateKey)]=$twh;}
				
				$empSal[$row->emp_id]['tpd']+=$workDays;
				$empSal[$row->emp_id]['tot']+=$ot;
				$empSal[$row->emp_id]['twh']+=$twh;	
				
				if(!empty($postData['is_report'])){
					$empSal[$row->emp_id]['id'] = $row->id;
					$empSal[$row->emp_id]['emp_id'] = $row->emp_id;
					$empSal[$row->emp_id]['emp_code'] = $row->emp_code;
					$empSal[$row->emp_id]['punch_date'] = $row->punch_date;	
					$empSal[$row->emp_id]['attendance_date'] = $row->attendance_date;	
					$empSal[$row->emp_id]['ex_mins'] = $row->ex_mins;	
					$empSal[$row->emp_id]['shift_type'] = $row->shift_type;	
					$empSal[$row->emp_id]['lunch_time'] = $row->lunch_time;	
					$empSal[$row->emp_id]['shiftStart'] = $row->shiftStart;	
					$empSal[$row->emp_id]['shiftEnd'] = $row->shiftEnd;	
					$empSal[$row->emp_id]['lunch_start'] = $row->lunch_start;	
					$empSal[$row->emp_id]['lunch_end'] = $row->lunch_end;	
					$empSal[$row->emp_id]['shift_name'] = $row->shift_name;	
					$empSal[$row->emp_id]['shift_start'] = $row->shift_start;	
					$empSal[$row->emp_id]['shift_end'] = $row->shift_end;	
					$empSal[$row->emp_id]['emp_name'] = $row->emp_name;	
					$empSal[$row->emp_id]['emp_joining_date'] = $row->emp_joining_date;	
					$empSal[$row->emp_id]['emp_type'] = $row->emp_type;	
					$empSal[$row->emp_id]['dept_name'] = $row->dept_name;	
					$empSal[$row->emp_id]['ltp_minute'] = $row->ltp_minute;	
					$empSal[$row->emp_id]['ltp_days'] = $row->ltp_days;	
					$empSal[$row->emp_id]['ltp_phrs'] = $row->ltp_phrs;	
					$empSal[$row->emp_id]['eop_minute'] = $row->eop_minute;	
					$empSal[$row->emp_id]['eop_days'] = $row->eop_days;	
					$empSal[$row->emp_id]['eop_phrs'] = $row->eop_phrs;	
					$empSal[$row->emp_id]['rot_time'] = $row->rot_time;	
					$empSal[$row->emp_id]['ru_in_time'] = $row->ru_in_time;	
					$empSal[$row->emp_id]['rd_in_time'] = $row->rd_in_time;	
					$empSal[$row->emp_id]['ru_out_time'] = $row->ru_out_time;	
					$empSal[$row->emp_id]['rd_out_time'] = $row->rd_out_time;				
					$empSal[$row->emp_id]['atot'] = $row->ot_mins;	
					$empSal[$row->emp_id]['ot_approved_by'] = $row->ot_approved_by;	
					$empSal[$row->emp_id]['ot_approved_by_name'] = $row->ot_approved_by_name;
					$empSal[$row->emp_id]['ot_approved_at'] = $row->ot_approved_at;
					$empSal[$row->emp_id]['day'] = $currentDay;
					$empSal[$row->emp_id]['status'] = $ps;
					$empSal[$row->emp_id]['tpd'] = $workDays;
					$empSal[$row->emp_id]['ot'] = $ot;
					$empSal[$row->emp_id]['adj_mins'] = $row->adj_mins;
					$empSal[$row->emp_id]['adjust_from'] = $row->adjust_from;
					$empSal[$row->emp_id]['adjust_to'] = $row->adjust_to;
					$empSal[$row->emp_id]['wh'] = $wh;
					$empSal[$row->emp_id]['twh'] = $twh;	
				}
			}
			return $empSal;
		}
		
		/*** Created By : JP@02.03.2023 ***/
		public function getAttendanceLogV2($postData)
		{
			$empAttendanceLog = $this->getALogSummary($postData);
			
			$empTable='';$empSal=Array();$ltCount=0;$eoCount=0;$ltPenalty=0;$eoPenalty=0;$alogData=Array();
			foreach($empAttendanceLog as $row)
			{
				if(!isset($empSal[$row->emp_id]['twh'])){$empSal[$row->emp_id]['twh']=0;$ltCount=0;$eoCount=0;}
				if(!isset($empSal[$row->emp_id]['tpd'])){$empSal[$row->emp_id]['tpd']=0;}
				$empPunches = explode(',',$row->punch_date);
				$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
				$empPunches = sortDates($empPunches,$sortType);
				
				$currentDay = date('D', strtotime($row->attendance_date));
				$dateKey = date('d', strtotime($row->attendance_date));
				$xmins = $row->ex_mins;
				$startLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_start));
				$endLunch = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->lunch_end));
				
				$shift_start = date('d-m-Y H:i:s', strtotime($row->attendance_date.' '.$row->shift_start));
				$nextDate = ($row->shift_type == 1) ? date('Y-m-d', strtotime($row->attendance_date . ' +1 day')) : $row->attendance_date;
				$shift_end = date('d-m-Y H:i:s', strtotime($nextDate.' '.$row->shift_end));
				$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
				$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
				
				$t=1;$wph = Array();$idx=0;$stay_time=0;$twh = 0;$wh=0;$ot=0;$xtime='';$status='';$ps='';
				$late_in_s = 0;$early_in_s = 0;$early_out_s = 0;$late_out_s = 0;$lastp_index = count($empPunches)-1;
				

				// Count Early In, Late In, Early Out, Late Out Time in Seconds
				// Trim Early In Punch
				$rps = 1800;$rpse_limit = 1500;$rpsl_limit = 300;// Round Punch By Seconds
				if(strtotime($punch_in) < strtotime($shift_start))
				{
					$extraSec = (strtotime($punch_in) % $rps);
					if($extraSec >= $rpse_limit){$empPunches[0]=date('Y-m-d H:i:s', strtotime($empPunches[0]) - ($rps-$extraSec));}
					if($extraSec < $rpse_limit){$empPunches[0] = date('Y-m-d H:i:s', strtotime($empPunches[0]) + $extraSec);}
					$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
				}
				// Trim Late In Punch
				if(strtotime($punch_in) > strtotime($shift_start))
				{
					$extraSec = (strtotime($punch_in) % $rps);
					if($extraSec <= $rpsl_limit){$empPunches[0]=date('Y-m-d H:i:s', strtotime($empPunches[0]) - $extraSec);}
					if($extraSec > $rpsl_limit){$empPunches[0]=date('Y-m-d H:i:s', strtotime($empPunches[0]) + ($rps-$extraSec));}
					$punch_in = date('d-m-Y H:i:s', strtotime(min($empPunches)));
				}
				// Trim Early Out Punch
				if(strtotime($punch_out) < strtotime($shift_end))
				{
					$extraSec = (strtotime($punch_out) % $rps);
					if($extraSec <= $rpsl_limit)
					{
						$empPunches[$lastp_index]=date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) + ($rps-$extraSec));
					}
					if($extraSec > $rpsl_limit)
					{
						$empPunches[$lastp_index]=date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) - $extraSec);
					}
					$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
				}
				// Trim Late Out Punch
				if(strtotime($punch_out) > strtotime($shift_end))
				{
					$extraSec = (strtotime($punch_out) % $rps);
					if($extraSec <= $rpse_limit)
					{
						$empPunches[$lastp_index]=date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) - $extraSec);
					}
					if($extraSec > $rpse_limit)
					{
						$empPunches[$lastp_index]=date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) + ($rps-$extraSec));
					}
					$punch_out = date('d-m-Y H:i:s', strtotime(max($empPunches)));
				}
				$late_in_s = 0;$early_in_s = 0;$early_out_s = 0;$late_out_s = 0;
				// Count Early In, Late In, Early Out, Late Out Time in Seconds
				if(strtotime($punch_in) > strtotime($shift_start)){$late_in_s = strtotime($punch_in) - strtotime($shift_start);}
				if(strtotime($punch_in) < strtotime($shift_start)){$early_in_s = strtotime($shift_start) - strtotime($punch_in);}
				if(strtotime($punch_out) > strtotime($shift_end)){$late_out_s = strtotime($punch_out) - strtotime($shift_end);}
				if(strtotime($punch_out) < strtotime($shift_end)){$early_out_s = strtotime($shift_end) - strtotime($punch_out);}
				
				
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
				if($early_in_s <= $row->ru_in_time){$empPunches[0] = date('Y-m-d H:i:s', strtotime($empPunches[0]) + $early_in_s );}
				// Trim Late In Punch
				if($late_in_s <= $row->rd_in_time){$empPunches[0] = date('Y-m-d H:i:s', strtotime($empPunches[0]) - $late_in_s);}
				
				// Trim Early Out Punch
				if($early_out_s <= $row->ru_out_time){$empPunches[$lastp_index] = date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) + $early_out_s);}
				// Trim Late Out Punch
				if($late_out_s <= $row->rd_out_time){$empPunches[$lastp_index] = date('Y-m-d H:i:s', strtotime($empPunches[$lastp_index]) - $late_out_s);}
				
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
					$countedLT = 0;
					if(count($empPunches) > 2){$countedLT = strtotime($empPunches[2]) - strtotime($empPunches[1]);$twh += $countedLT;}
					if($countedLT > $row->lunch_time){$row->lunch_time = $countedLT;}
					$twh -= $row->lunch_time;
				}
				
				// Get Extra Hours
				$twh += $row->ex_mins;
				
				// Set Present/Absent/ Status
				$ps='A';$presentStatus = false;
				if(count($empPunches) > 0)
				{
					if($twh > 0){$presentStatus = true;}else{$presentStatus = false;}
				}
				else{$presentStatus = false;}
				
				if($presentStatus)
				{
					$ps='P';
					if(count($empPunches) % 2 != 0){$ps='M';}
					if($currentDay != 'Wed'){$empSal[$row->emp_id]['tpd']++;}
					if($currentDay == 'Wed'){$ps = 'PWO';}
				}
				else
				{
					$ps = 'A';if($currentDay == 'Wed'){$ps = 'WO';}
				}
				
				if(!empty($postData['datewise'])){$empSal[$row->emp_id][intVal($dateKey)]=$twh;}
				if(!empty($postData['payroll']))
				{
					$empSal[$row->emp_id]['advance_data'] = (!empty($row->advance_json))?json_decode($row->advance_json):[];
					$empSal[$row->emp_id]['loan_data'] = (!empty($row->loan_json))?json_decode($row->loan_json):[];
				}
				$empSal[$row->emp_id]['twh']+=$twh;
				
				$workHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($wh).'</td>';
						$ltTd = '<td style="text-align:center;font-size:12px;">'.formatSeconds($row->lunch_time).'</td>';
						$exHrs = $xtime;
						$otData = '<td style="text-align:center;font-size:12px;">'.formatSeconds($ot).'</td>';
						$totalWorkHrs = '<td style="text-align:center;font-size:12px;">'.formatSeconds($twh).'</td>';
				
				$row->wh = $wh;
				$row->lunch_time = $row->lunch_time;
				$row->ot = $ot;
				$row->twh = $twh;
				$row->ex_mins = $row->ex_mins;
				$row->empPunches = $empPunches;
				
				$alogData[]=['empData'=>$row,'empSal'=>$empSal];
				
			}
			return $alogData;
		}
		
		
	}
	?>