<?php
class MasterOptions extends MY_Controller{
    private $indexPage = "master_options";
    private $currencyPage = "currencyIndex";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Master Options";
		$this->data['headData']->controller = "masterOptions";
        $this->data['headData']->pageUrl = "masterOptions";
	}
	
	public function index(){
        $this->data['dataRow'] = $this->masterOption->getMasterOptions();
        $this->load->view($this->indexPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['color_code']))
            $errorMessage['color_code'] = "Color Code is required.";
		
        /*if(empty($data['material_grade']))
             $errorMessage['material_grade'] = "Material Grade is required.";
		if(empty($data['thread_types']))
            $errorMessage['thread_types'] = "Thread Types is required.";
        if(empty($data['machine_idle_reason']))
            $errorMessage['machine_idle_reason'] = "Machine Idle Reason is required.";
        if(empty($data['ppap_level']))
            $errorMessage['ppap_level'] = "PPAP Level is required.";
        if(empty($data['dev_charge']))
            $errorMessage['dev_charge'] = "Development Charge is required.";*/

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->masterOption->save($data));
        endif;
    }

    public function currencyIndex(){
        $this->data['headData']->pageUrl = "masterOptions/currencyIndex";
        $this->data['tableHeader'] = getConfigDtHeader('currency');
        $this->load->view($this->currencyPage,$this->data);
    }

    public function getCurrencyRows(){
        $result = $this->masterOption->getCurrencyRows($this->input->post());

        $sendData = array(); $i=1;

        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->inrinput = '<input type="text" id="inrrate_'.$row->id.'" name="inrrate[]" class="form-control floatOnly" value="'.$row->inrrate.'" /><input type = "hidden"  id="id_'.$row->id.'" name=id[] value="'.$row->id.'" >' ;
            $sendData[] = getCurrencyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function saveCurrency(){
        $data = $this->input->post();
        $this->printJson($this->masterOption->saveCurrency($data));
    }
}
?>