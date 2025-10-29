<?php
class MachineActivities extends MY_Controller
{
    private $indexPage = "machine_activity/index";
    private $activityForm = "machine_activity/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Machine Activities";
		$this->data['headData']->controller = "machineActivities";
		$this->data['headData']->pageUrl = "machineActivities";
	}
	
	public function index(){
        $this->data['tableHeader'] = getMaintenanceDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->activities->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMachineActivitiesData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMachineActivities(){
        $this->load->view($this->activityForm);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['activities']))
            $errorMessage['activities'] = "Activities is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->activities->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->activities->getActivities($this->input->post('id'));
        $this->load->view($this->activityForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->activities->delete($id));
        endif;
    }
    
}
?>