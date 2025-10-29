<?php
class Shift extends MY_Controller
{
    private $indexPage = "shift/index";
    private $shiftForm = "shift/form";
    private $assign_shift = "shift/assign_shift";
    private $manage_shift = "shift/manage_shift";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Shift";
		$this->data['headData']->controller = "shift";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->shiftModel->getDTRows($this->input->post());
		//$jData = (Array) json_decode($result->shift_data);
		//$empPucnhes = array_keys(array_combine(array_keys($jData), array_column($jData, 'emp_id')),63);
		//print_r($jData);exit;
		
		
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;         
            $sendData[] = getShiftData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addShift(){
        $this->load->view($this->shiftForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['shift_name']))
			$errorMessage['shift_name'] = "Shift Name is required.";
        if(empty($data['shift_start']))
			$errorMessage['shift_start'] = "Shift Start Time is required.";
        if(empty($data['shift_end']))
            $errorMessage['shift_end'] = "Shift End Time is required.";
        if(empty($data['lunch_start']))
            $errorMessage['lunch_start'] = "Lunch Start Time is required.";
        if(empty($data['lunch_end']))
            $errorMessage['lunch_end'] = "Lunch End Time is required.";

        $shiftStart = new DateTime(date('H:i:s',strtotime(date('Y-m-d').' '.$data['shift_start'])));
        $shiftEnd = new DateTime(date('H:i:s',strtotime(date('Y-m-d').' '.$data['shift_end'])));

        if($shiftEnd < $shiftStart){$shiftEnd->modify('+1 day');}

        $totalShift = $shiftStart->diff($shiftEnd);
        $data['total_shift_time'] = $totalShift->format('%H:%I');
        $totalSM = (intVal($totalShift->format('%H')) * 60) + intVal($totalShift->format('%I'));

        $lunchStart = new DateTime(date('H:i:s',strtotime($data['lunch_start'])));
        $lunchEnd = new DateTime(date('H:i:s',strtotime($data['lunch_end'])));
        $totalLunch = $lunchStart->diff($lunchEnd);
        $data['total_lunch_time'] = $totalLunch->format('%H:%I');
        $totalLM = (intVal($totalLunch->format('%H')) * 60) + intVal($totalLunch->format('%I'));
		$totalDM = $totalSM - $totalLM;//print_r('SM='.$totalSM.'***LM'.$totalLM.'***DM'.$totalDM);
		
		$pHour = intVal($totalDM / 60);
		$pMinute = intVal($totalDM % 60);
        $data['production_hour'] = str_pad($pHour, 2, '0', STR_PAD_LEFT).':'.str_pad($pMinute, 2, '0', STR_PAD_LEFT);

        if($data['production_hour'] > 24)
            $errorMessage['general_error'] = "Invalid Time.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->shiftModel->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->shiftModel->getShift($id);
        $this->load->view($this->shiftForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->shiftModel->delete($id));
        endif;
    }

    public function updateEmpShift(){
		$this->printJson($this->shiftModel->updateEmpShift());
    }

    /* Manage Shift */
    public function manageShift(){
		$this->data['deptRows'] = $this->department->getDepartmentList();
        $this->load->view($this->manage_shift,$this->data);
    }

    public function getManageShift(){
        $postData = $this->input->post();
        $shiftData = $this->biometric->getShiftByDate($postData);
		$day = date('d',strtotime($postData['shift_date']));
        $punchData = Array(); $i=1; $tbody=''; $option='';
        if(!empty($shiftData)):
            //$punchData = json_decode($shiftData->punchdata);
            $shift = $this->shiftModel->getShiftList();
            foreach($shift as $srow):
                $option.= '<option value="'.$srow->latest_id.'">'.$srow->shift_name.'</option>';
            endforeach;
            if(!empty($shiftData)):
                foreach($shiftData as $row):
                    $tbody.='<tr>
						<td>'.$row->emp_code.'</td>
						<td>
							'.$row->emp_name.'
							<input type="hidden" id="trans_id_'.$row->id.'" name="trans_id_'.$row->id.'" value="'.$row->id.'">
							<input type="hidden" id="emp_id_'.$row->id.'" name="emp_id_'.$row->id.'" value="'.$row->emp_id.'">
							<input type="hidden" id="field_id_'.$row->id.'" name="field_id_'.$row->id.'" value="d'.intVal($day).'">
							<input type="hidden" id="shift_date_'.$row->id.'" name="shift_date_'.$row->id.'" value="'.$postData['shift_date'].'">
						</td>
						<td>'.$row->dept_name.'</td>
						<td>'.$row->emp_dsg.'</td>
						<td>'.$row->shift_name.'</td>
						<td>
							<select name="new_shift_id_'.$row->id.'" id="new_shift_id_'.$row->id.'" class="form-control single-select">
								<option value="">Select Shift</option> 
								'.$option.'
								<option value="0">NA</option>
							</select>
						</td>
						<td>
							<button type="button" class="btn waves-effect waves-light btn-success" onclick="saveManageShift('.$row->id.');">Save</button>
						</td>
					</tr>';
                endforeach;
            endif;
        endif;
        $this->printJson(['status'=>1,"tbodyData"=>$tbody]);
    }

    public function saveManageShift(){
        $data = $this->input->post();
        $errorMessage = "";		
        if(empty($data['id']))
			$errorMessage = "ID not Found.";
        if(empty($data['emp_id']))
			$errorMessage = "Employee is Required";
        if(empty($data['field_id']))
            $errorMessage = "Date is Required";
        if($data['new_shift_id'] == "")
            $errorMessage = "New Shift is required.";
				
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['updated_by'] = $this->session->userdata('loginId');
            $data['updated_at'] = date("Y-m-d H:i:s");
            $result = $this->shiftModel->saveManageShift($data);
            if($result['status']==1){}
            $this->printJson($result);
        endif;
		
        if (!empty($errorMessage)): 
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else:
            $this->printJson($this->shiftModel->saveManageShift($qryData));
        endif;
    }
}
?>