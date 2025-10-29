<?php
class ExtraHours extends MY_Controller
{
    private $indexPage = "hr/extrahours/index";
    private $manualForm = "hr/extrahours/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ExtraHours";
		$this->data['headData']->controller = "hr/extraHours";
	}
	
	public function index(){
        $this->data['tableHeader'] = getHrDtHeader("extraHours");
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows($status=0){
        $postData = $this->input->post();
        $postData['status']=$status;
        $result = $this->extraHours->getDTRows($postData);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->loginID = $this->loginId;
            //$row->loginID = $this->userRole;
            $sendData[] = getExtraHoursData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addExtraHours(){
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->load->view($this->manualForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['emp_id']))
			$errorMessage['emp_id'] = "Employee is required.";
        if(empty($data['punch_date']))
			$errorMessage['punch_date'] = "Attendance Date Time is required.";
        if(empty($data['ex_hours']) && empty($data['ex_mins']))
			$errorMessage['ex_hours'] = "Extra Hours is required.";
        if(empty($data['remark']))
			$errorMessage['remark'] = "Reason is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            
            $empData = $this->employee->getEmp($data['emp_id']);
            $data['created_by'] = $this->session->userdata('loginId');
            $data['emp_code'] = (!empty($empData)) ? $empData->emp_code : '';
			
            $this->printJson($this->extraHours->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['empData'] = $this->leave->getEmpData($this->session->userdata('loginId'));
        $this->data['empList'] = $this->employee->getEmpList();
        $this->data['dataRow'] = $this->extraHours->getExtraHours($id);
        $this->data['loginID'] = $this->session->userdata('loginId');
        $this->load->view($this->manualForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->extraHours->delete($id));
        endif;
    }

    public function getXHRSDetail(){     
        $id = $this->input->post('id');
        $xData = $this->extraHours->getExtraHours($id);
        $approvalData = '<table class="table table-bordered table-striped">';
        if(!empty($xData))
        {
            $tsign = ($xData->xtype == 1) ? "+ " : "- ";
            $approvalData .= '<tr><th colspan="2">'.$xData->emp_name.'</th></tr>';
            $approvalData .= '<tr><th>Attendance Date</th><td>'.formatDate($xData->attendance_date).'</td></tr>';
            $approvalData .= '<tr><th>Extra Time</th><td>'.$tsign.formatSeconds((($xData->ex_hours * 3600) + ($xData->ex_mins * 60))).'</td></tr>';
            $approvalData .= '<tr><th>Ctreated By</th><td>'.$xData->createdBy.'</td></tr>';
            $approvalData .= '<tr><th>Ctreated Time</th><td>'.date('d-m-Y H:i:s',strtotime($xData->created_at)).'</td></tr>';
            $approvalData .= '<tr><td colspan="2"><b>Remark:</b><br>'.$xData->remark.'</td></tr>';
        }
        $approvalData .= '</table><input type="hidden" id="id" value="'.$id.'">';
        $this->printJson(['status'=>1,'approvalData'=>$approvalData]);
    }

    public function approveXHRS(){     
        $postData = $this->input->post();
        $postData['approved_at'] = date('Y-m-d H:i:s');
        $postData['approved_by'] = $this->loginId;
        $this->printJson($this->extraHours->approveXHRS($postData));
    }
}
?>