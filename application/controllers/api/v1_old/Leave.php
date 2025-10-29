<?php
class Leave extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

/* Create By : Avruti @30-11-2021 10:10 AM
     update by : 
     note :
*/

    public function LeaveList(){
        $total_rows = $this->leave->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/leaveSetting/leaveList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['leaveList'] = $this->leave->getLeaveList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }

    public function addLeave(){
        $this->data['leaveType'] = $this->leave->getLeaveType();
        $this->data['empData'] = $this->leave->getEmpData($this->loginId);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }
    public function getEmpLeaves(){
		$login_id = $this->loginId;
		$start_date=date("Y-m-d",strtotime($this->session->userdata('startDate')));
		$end_date=date("Y-m-d",strtotime($this->session->userdata('endDate')));
        $this->printJson($this->leave->getEmpLeaves($login_id,$this->input->post('leave_type_id'))[0],$start_date,$end_date);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['leave_type_id'])):
            $errorMessage['leave_type_id'] = "Leave Type is required.";
		else:
			$login_id = $this->loginId;
			$start_date=date("Y-m-d",strtotime($this->session->userdata('startDate')));
			$end_date=date("Y-m-d",strtotime($this->session->userdata('endDate')));
			$empLD = $this->leave->getEmpLeaves($login_id,$data['leave_type_id'],$start_date,$end_date)[0];
			if($data['total_days'] > $empLD['remain_leaves'])
				$errorMessage['generalError'] = "You have not remain leaves for selected leave type";
		endif;
		if(empty($data['start_date']))
            $errorMessage['start_date'] = "Start Date is required.";
		if(empty($data['end_date']))
            $errorMessage['end_date'] = "End Date is required.";
		if(empty($data['total_days']))
            $errorMessage['generalError'] = "You have to apply atleast 1 Day Leave";
			
		if(!empty($errorMessage)):
				$this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$data['leave_type'] = $this->leaveSetting->getLeaveType($data['leave_type_id'])->leave_type;
			$data['created_by'] = $this->loginId;
			$this->printJson($this->leave->save($data));
        endif;
    }

    public function view(){
        $id = $this->input->post('id');
        $this->data['leaveType'] = $this->leave->getLeaveType();
        $this->data['empData'] = $this->leave->getEmpData($this->loginId);
        $this->data['dataRow'] = $this->leave->getLeave($id);
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->leave->delete($id));
        endif;
    }
}
?>