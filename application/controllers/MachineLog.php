<?php
class MachineLog extends CI_Controller{
    private $machine_log = "log_sheet/machine_log";
    public function __construct(){
        parent::__construct();
        $this->data['headData'] = new StdClass;
        $this->load->model('masterModel');
        $this->load->model('LogSheetModel','logSheet');
        $this->data['headData']->pageTitle = "Machine Log";
    }

    public function index(){
        $this->data['machineLogData'] = $this->logSheet->getMachineLogDtRows($this->input->post());
        $this->load->view($this->machine_log,$this->data);
    }
}
?>