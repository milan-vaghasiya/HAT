<?php
class ManualAttendance extends MY_Controller
{
    private $indexPage = "hr/manual_attendance/index";
    private $manualForm = "hr/manual_attendance/form";
    private $punchTypeArr = ['0'=>'','1'=>'Device Punch','2'=>'Manual Punch','3'=>'Extra Hours'];

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Manual Attendance";
		$this->data['headData']->controller = "hr/manualAttendance";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader("manualAttendance");
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->manualAttendance->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getManualAttendanceData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addManualAttendance(){
        $postData = $this->input->post();
        $this->data['empData'] = $this->employee->getEmp($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmpListV1($postData);
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->data['attendance_date'] = $postData['attendance_date'];
        $this->load->view($this->manualForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['emp_id']))
			$errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['punch_date'])):
			$errorMessage['punch_date'] = "Attendance Date Time is required.";
        else:
            $attendanceDates = [$data['attendance_date'],date('Y-m-d', strtotime($data['attendance_date'] . ' +1 day'))];
            if(!in_array($data['punch_date'],$attendanceDates)):
                $errorMessage['punch_date'] = "Attendance Date and Report Date not match.";
            endif;
        endif;
        if(empty($data['punch_in']))
			$errorMessage['punch_in'] = "Punch Time is required.";
        if(empty($data['remark']))
			$errorMessage['remark'] = "Reason is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['punch_date'] = (!empty($data['punch_in'])) ? date('Y-m-d H:i:s', strtotime($data['punch_date'].' '.$data['punch_in'])) : "";
            unset($data['punch_in']);
            
            $data['created_by'] = $this->session->userdata('loginId');
            $empData = $this->employee->getEmp($data['emp_id']);
            $data['emp_code'] = $empData->emp_code;
            $result = $this->manualAttendance->save($data);

            if($result['status']==1):
                $result['tbody'] = $this->getEmpPunchData($data['emp_id'], date('Y-m-d', strtotime($data['punch_date'])))['tbody'];
            endif;
            $this->printJson($result);
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmployeeList();
        $this->data['dataRow'] = $dataRow = $this->manualAttendance->getManualAttendance($id);
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->data['punchData'] = $this->getEmpPunchData($dataRow->emp_id, $dataRow->punch_date);
        $this->load->view($this->manualForm,$this->data);
    }

    /* public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $loginID = $this->session->userdata('loginId');
            $this->printJson($this->manualAttendance->delete($id),$loginID);
        endif;
    } */

    public function deletePunch(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $result = $this->manualAttendance->delete($data['id'],$data['attendance_date']);
            if($result['status']==1):
                $result['tbody'] = $this->getEmpPunchData($data['emp_id'], date('Y-m-d', strtotime($data['punch_date'])))['tbody'];
            endif;
            $this->printJson($result);
        endif;
    }

    public function getEmpPunchData($emp_id="", $atte_date=""){
        $rflag=0;
        if(empty($emp_id) && empty($atte_date)):
            $data = $this->input->post(); 
            $emp_id = $data['emp_id'];
            $atte_date = $data['punch_date'];
            $rflag=1;
        endif;
        $html = '';$i=1;
        if(!empty($emp_id) && !empty($atte_date)):
            $empAttendanceLog = $this->biometric->getEmpPunchesByDate($emp_id, date('Y-m-d',strtotime($atte_date)));
            
            $empPunches = array_column($empAttendanceLog, 'punch_date');			
            $punchType = array_column($empAttendanceLog, 'punch_type');
            $punchID = array_column($empAttendanceLog, 'punch_id');
            

            if(!empty($empPunches[0])):
                $shift_type = array_column($empAttendanceLog, 'shift_type')[0];
                $shift_name = array_column($empAttendanceLog, 'shift_name')[0];
                $empPunches = explode(',',$empPunches[0]);				
                $punchType = explode(',',$punchType[0]);				
                $punchID = explode(',',$punchID[0]);

                //$sortType = ($shift_type == 1) ? 'ASC' : 'ASC';
				//$empPunches = sortDates($empPunches,$sortType);

                foreach($empPunches as $key=>$value):
                    $html .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$shift_name.'</td>
                            <td>'.formatDate($value, 'd-m-Y H:i:s').'</td>
                            <td>'.$this->punchTypeArr[$punchType[$key]].'</td>
                            <td>
                                <a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashPunch('.$punchID[$key].');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
                            </td>
                        </tr>';
                endforeach;
            else:
                $html .= "<tr><td class='text-center' colspan='5'>No Data Found</td></tr>";
            endif;
        else:
            $html .= "<tr><td class='text-center' colspan='5'>No Data Found</td></tr>";
        endif;

        if(empty($rflag)):
            return ['status'=>1,'tbody'=>$html];
        else:
            $this->printJson(['status'=>1,'tbody'=>$html]);
        endif;
    }
}
?>