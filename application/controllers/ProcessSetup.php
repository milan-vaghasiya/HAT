<?php
class ProcessSetup extends MY_Controller{
    private $indexPage = "process_setup/index";
    private $formPage = "process_setup/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Process Setup";
		$this->data['headData']->controller = "processSetup";
		$this->data['headData']->pageUrl = "processSetup";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->processSetup->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($row->setup_status == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">'.$row->ins_status.'</span>';
			elseif($row->setup_status == 1):
				$row->status = '<span class="badge badge-pill badge-warning m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 2):
                $row->status = '<span class="badge badge-pill badge-info m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 3):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 4):
                $row->status = '<span class="badge badge-pill badge-primary m-1">'.$row->ins_status.'</span>';
			elseif($row->setup_status == 5):
				$row->status = '<span class="badge badge-pill badge-dark m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 6):
                $row->status = '<span class="badge badge-pill badge-info m-1">'.$row->ins_status.'</span>';
			endif;
            $sendData[] = getProcessSetupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function startSetup(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->processSetup->startSetup($id));
        endif;
    }

    public function processSetup(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->processSetup->getSetupData($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['setter_note']))
            $errorMessage['setter_note'] = "Note is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['setup_end_time'] = date("Y-m-d H:i:s");
            $this->printJson($this->processSetup->save($data));
        endif;
    }
}
?>