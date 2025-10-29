<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Attendance extends MY_Controller
{
    private $indexPage = "hr/attendance/index";
    private $monthlyAttendance = "hr/attendance/month_attendance";
    private $attendanceForm = "hr/attendance/form";
    private $approveOTPage = "hr/attendance/approve_ot";
    private $approveOTForm = "hr/attendance/approve_ot_form";
    private $importForm = "hr/attendance/import_form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
	}
	
	public function index(){
		$this->data['lastSyncedAt'] = "";
		$sycData = $this->biometric->getDeviceData();
		$this->data['lastSyncedAt'] = (!empty($sycData[0]->last_sync_at)) ? date('j F Y, g:i a',strtotime($sycData[0]->last_sync_at)) : "";
        $this->load->view($this->indexPage,$this->data);
    }

	public function monthlyAttendance(){
        $this->load->view($this->monthlyAttendance,$this->data);
    }

    public function loadAttendanceSheet(){
        $data = $this->input->post();
		$this->printJson($this->attendance->loadAttendanceSheet($data['month']));
    }

    public function syncDeviceData(){
		$this->printJson($this->biometric->syncDeviceData());
    }
    
    /**** NEW STRUCTURE (attendance_log) | Created By JP @09-12-2022 ***/
    public function syncDevicePunches(){
		$this->printJson($this->biometric->syncDevicePunches());
    }
    
    /**** NEW STRUCTURE (emp_punches) | Created By JP @09-12-2022 ***/
    /*public function syncDevicePunchesV2(){
		$this->printJson($this->biometric->syncDevicePunchesV2());
    }*/
    
    /** Approve OT | Created By Milan @11-03-2023 **/
    public function approveOT(){
        //$result = $this->biometric->getSalaryHours(['from_date'=>"2023-03-10",'to_date'=>"2023-03-10",'is_report'=>1,'emp_id'=>169]);
        //print_r($result);exit;
        $this->load->view($this->approveOTPage,$this->data);
    }
    
    public function getEmployeeAttendanceData(){
        $data = $this->input->post();
        $data['is_report'] = 1;
        $result = $this->biometric->getSalaryHours($data);
        
        $html = '';$i=1;
        foreach($result as $row):
            $row = (object) $row;
            
            $empPunches = explode(',',$row->punch_date);
			$sortType = ($row->shift_type == 1) ? 'ASC' : 'ASC';
			$empPunches = sortDates($empPunches,$sortType);	
            
            $ap = Array();
			foreach($empPunches as $p){$ap[] = date("H:i",strtotime($p));}
			$allPunches = implode(', ',$ap);
			
			$editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-lg', 'form_id' : 'approveOT', 'title' : 'Approve Employee OT','fnedit':'editEmployeeOT','fnsave':'saveEmployeeOt','ot':".$row->ot."}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Approve OT" flow="down" onclick="approveOT('.$editParam.');"><i class="ti-check" ></i></a>';
            $action = getActionButton($editButton);
            
            if($row->ot > 0):
                $adjFrom = '';
                if(!empty($row->adjust_from)){$af = explode('@',$row->adjust_from);$adjFrom = formatSeconds($row->adj_mins,'H:i').'<br><small>'.formatDate($af[1]).'</small>';}
                $adjTo = '';
                if(!empty($row->adjust_to)){$at = explode('@',$row->adjust_to);$adjTo = formatSeconds($row->ot,'H:i').'<br><small>'.formatDate($at[1]).'</small>';}
                $html .= '<tr>
                    <td>'.$action.'</td>
                    <td class="fs-12">'.$i.'</td>
                    <td class="fs-12">'.$row->emp_code.'</td>
                    <td class="fs-12 text-left">'.$row->emp_name.'</td>
                    <!--<td class="fs-12">'.$row->dept_name.'</td>-->
                    <td class="fs-12">'.$row->shift_name.'</td>
                    <td class="fs-12">'.formatDate($row->attendance_date).'</td>
                    <!--<td class="fs-12">'.$row->day.'</td>-->
                    <!--<td class="'.(($row->status == "P")?"text-success":(($row->status == "A")?"text-danger":"")).'">'.$row->status.'</td>
                    <td class="fs-12">'.formatSeconds(abs($row->wh),'H:i').'</td>
                    <td class="fs-12">'.formatSeconds($row->lunch_time,'H:i').'</td>
                    <td class="fs-12">'.formatSeconds($row->ex_mins,'H:i').'</td>
                    <td class="fs-12">'.formatSeconds($row->twh,'H:i').'</td>-->
                    <td class="fs-12">'.formatSeconds($row->ot,'H:i').'</td>
                    <td class="fs-12">'.formatSeconds($row->atot,'H:i').'</td>
                    <td class="fs-12">'.$adjFrom.'</td>
                    <td class="fs-12">'.$adjTo.'</td>
                    <td class="fs-12">'.$allPunches.'</td>
                </tr>';
                $i++;
            endif;
            
        endforeach;
        
        $this->printJson(['status'=>1,'tbody'=>$html]);
    }
    
    public function editEmployeeOT(){
        $data = $this->input->post();
        $summaryData = $this->attendance->getAlogSummary($data['id']);
        $summaryData->actual_ot = $data['ot'];
        $this->data['dataRow'] = $summaryData;
        $this->data['deptList'] = $this->department->getDepartmentList(1);
        $this->load->view($this->approveOTForm,$this->data);
    }
    
    public function saveEmployeeOt(){
        $data = $this->input->post();
        $errorMessage = array();

        if(timeToSeconds($data['ot_mins']) > timeToSeconds($data['actual_ot']))
            $errorMessage['ot_mins'] = "Invalid OT.";
        if($data['ot_type'] == 2 AND empty($data['adjust_date']))
            $errorMessage['adjust_date'] = "Adjust Date is required";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['actual_ot']);
            $this->printJson($this->attendance->saveEmployeeOt($data));
        endif;
    }
    
    public function approveOTBulkByDate($dates=""){
        
        if(!empty($dates))
        {
            $duration = explode('~',$dates);
            if(count($duration) == 2)
            {
                $FromDate = date("Y-m-d",strtotime($duration[0]));
			    $ToDate  = date("Y-m-d",strtotime($duration[1]));
                $result = $this->biometric->getSalaryHours(['from_date'=>$FromDate,'to_date'=>$ToDate,'is_report' => 1]);
                //print_r($result);exit;
                $i=0;
                if(!empty($result))
                {
                    foreach($result as $row)
                    {
                        $row = (object) $row;
                        if($row->ot > 0)
                        {
                            $otApprovalData = Array();
                            
                            $otApprovalData['id'] = $row->id;
                            $otApprovalData['ot_mins'] = formatSeconds($row->ot,'H:i');
                            $otApprovalData['remark'] = 'BULK APPROVED';
                            print_r($otApprovalData);print_r('<hr>');
                            $this->attendance->saveEmployeeOt($otApprovalData);
                            $i++;
                        }
                    }
                }
                echo 'Total OT Approved = '.$i;
            }
            else
            {
                echo '<h1 class="text-center">Invalid Date</h1>';
            }
        }
        else
        {
            echo '<h1 class="text-center">OT Date Not Found</h1>';
        }
     }
     
    public function importAttendance(){
		$this->load->view($this->importForm);
    }

    public function downloadAttendanceSheet(){
		$spreadsheet = new Spreadsheet();
		$attendSheet = $spreadsheet->getActiveSheet();
		$attendSheet = $attendSheet->setTitle('EmpAttendance');
		$xlCol = 'A';
		$rows = 1;
		$table_column = array('Sr. No.', 'Emp Code','Attendance Date','P1','P2','P3','P4','P5','P6','P7','P8');

		foreach ($table_column as $tCols) {
			$attendSheet->setCellValue($xlCol . $rows, $tCols);
			$xlCol++;
		}

		$rows++;$i=1;
		$empData = $this->employee->getEmpList();
		foreach($empData as $emp):
			if(!empty($emp->emp_code)):
				$attendSheet->setCellValue('A' . $rows, $i++);
				$attendSheet->setCellValue('B' . $rows, $emp->emp_code);
				$rows++;
			endif;
		endforeach;

		$fileDirectory = realpath(APPPATH . '../assets/uploads/attendance_excel');
		$fileName = '/EmpAttendance.xlsx';
		$writer = new Xlsx($spreadsheet);

		if(is_dir($fileDirectory) === false) {
			mkdir($fileDirectory, 0755);
		}

		$writer->save($fileDirectory . $fileName);
		header("Content-Type: application/vnd.ms-excel");
		redirect(base_url('assets/uploads/attendance_excel') . $fileName);
    }

    public function importEmpAttendance(){
		$postData = $this->input->post();
		if(!isset($_FILES['emp_attendance']['name']) || empty($_FILES['emp_attendance']['name'])) :
			$this->printJson(['status' => 2, 'message' => 'Please Select File!']);
		else:
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $_FILES['emp_attendance']['name'];
			$_FILES['userfile']['type']     = $_FILES['emp_attendance']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['emp_attendance']['tmp_name'];
			$_FILES['userfile']['error']    = $_FILES['emp_attendance']['error'];
			$_FILES['userfile']['size']     = $_FILES['emp_attendance']['size'];

			$filePath = realpath(APPPATH . '../assets/uploads/attendance_excel');
			$config = ['file_name' => date("Y_m_d_H_i_s") . "emp_attend" . $_FILES['userfile']['name'], 'allowed_types' => '*', 'max_size' => 10240, 'overwrite' => FALSE, 'upload_path' => $filePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()) :
				$errorMessage['emp_attendance'] = $this->upload->display_errors();
				$this->printJson(["status" => 0, "message" => $errorMessage]);
			else :
				$uploadData = $this->upload->data();
				$emp_attend = $uploadData['file_name'];
			endif;

			if(!empty($emp_attend)):
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath . '/' . $emp_attend);
				$fileData = array($spreadsheet->getSheetByName('EmpAttendance')->toArray(null, true, true, true));

				if(!empty($fileData)):
					$inserted = 0;
					for($i=2;$i<=count($fileData[0]);$i++):
						if(!empty($fileData[0][$i]['B']) AND !empty($fileData[0][$i]['C'])):
							$empData = $this->shiftModel->getEmpShiftByEmpcode($fileData[0][$i]['B']);
					
							if(!empty($empData)):
								$time = array();
								if(!empty($fileData[0][$i]['D'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['D'])); endif;
								if(!empty($fileData[0][$i]['E'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['E'])); endif;
								if(!empty($fileData[0][$i]['F'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['F'])); endif;
								if(!empty($fileData[0][$i]['G'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['G'])); endif;
								if(!empty($fileData[0][$i]['H'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['H'])); endif;
								if(!empty($fileData[0][$i]['I'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['I'])); endif;
								if(!empty($fileData[0][$i]['J'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['J'])); endif;
								if(!empty($fileData[0][$i]['K'])): $time[] = date("H:i:s",strtotime($fileData[0][$i]['K'])); endif;


								foreach($time as $tm):
									$dataRow = array();
									$attendanceDate = date("Y-m-d",strtotime($fileData[0][$i]['C']));
									$punchDate = date("Y-m-d H:i:s",strtotime($attendanceDate." ".$tm));

									$this->db->where('punch_type',2);
									$this->db->where('is_delete',0);
									$this->db->where('punch_date',$punchDate);
									$this->db->where('emp_code',$fileData[0][$i]['B']);
									$oldData = $this->db->get("attendance_log")->row();
						
									if(empty($oldData)):
										$dataRow = [
											'id' => '',
											'punch_type' => 2,
											'punch_date' => date("Y-m-d H:i:s",strtotime($punchDate)),
											'attendance_date' => $attendanceDate,
											'emp_id' => $empData->id,
											'emp_code' => $fileData[0][$i]['B'],
											'created_at' => date('Y-m-d H:i:s'),
											'created_by' => $this->session->userdata('loginId')
										];
										$this->manualAttendance->save($dataRow);                      
										$inserted++;
									endif;
								endforeach;
							endif;
						endif;				  
					endfor;
                    unlink($filePath . '/' . $emp_attend);
					$this->printJson(['status'=>1,'message'=>$inserted." Record Inserted Successfully."]);
				else:
                    unlink($filePath . '/' . $emp_attend);
					$this->printJson(['status' => 2, 'message' => 'Data not found...!']);
				endif;
			else:
                unlink($filePath . '/' . $emp_attend);
				$this->printJson(['status' => 2, 'message' => 'Data not found...!']);
			endif;
		endif;
    }
}
?>