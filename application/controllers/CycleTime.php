<?php
class CycleTime extends MY_Controller
{
    private $indexPage = "cycletime/index";
    private $cycletimeForm = "cycletime/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "CycleTime";
		$this->data['headData']->controller = "cycleTime";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getCycleTimeData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCycleTime(){
        $id = $this->input->post('id'); 
        $this->data['processData'] = $this->item->getItemProcess($id);   
        $this->load->view($this->cycletimeForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        $cycleTimeData = [
            'id' => $data['id'],
            'cycle_time' => $data['cycle_time']
        ];

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveProductProcessCycleTime($cycleTimeData));
        endif;
    }

    
}
?>