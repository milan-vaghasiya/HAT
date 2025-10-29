<?php
class HsnMaster extends MY_Controller
{
    private $indexPage = "hsn_master/index";
    private $hsnForm = "hsn_master/form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "HSN Master";
		$this->data['headData']->controller = "hsnMaster";
        $this->data['headData']->pageUrl = "hsnMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $data = $this->input->post(); 
        $result = $this->hsnModel->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row): 
            $row->sr_no = $i++;         
            $sendData[] = getHSNMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addHSN(){
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->hsnForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['hsn_code']))
			$errorMessage['hsn_code'] = "HSN is required.";
        if(empty($data['gst_per']))
            $errorMessage['gst_per'] = "GST Per. is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->hsnModel->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->hsnModel->getHSNDetail($id);    
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->load->view($this->hsnForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->hsnModel->delete($id));
        endif;
    }
    
}