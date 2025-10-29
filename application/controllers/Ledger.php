<?php
class Ledger extends MY_Controller
{
    private $indexPage = "ledger/index";
    private $ledgerForm = "ledger/form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Ledger";
		$this->data['headData']->controller = "ledger";		
	}
	
	public function index(){
        $this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->ledger->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getLedgerData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function addLedger(){
        $this->data['gstPercentage'] = $this->gstPercentage;
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->data['grpData'] = $this->group->getGroupListOnGroupCode("group_code NOT IN ('SD','SC')");
        $this->load->view($this->ledgerForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_name']))
            $errorMessage['party_name'] = "Ledger name is required.";
        if(empty($data['group_id']))
            $errorMessage['group_id'] = "Group Name is required.";

        if(!empty($data['is_gst_applicable'])):
            if(empty($data['gst_per']))
                $errorMessage['gst_per'] = "Gst Percentage Name is required.";
            if(empty($data['cess_per'])) 
                $errorMessage['cess_per'] = "Cess Percentage is required.";
            if(empty($data['hsn_code'])) 
                $errorMessage['hsn_code'] = "Hsn code is required.";
        endif;
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->ledger->save($data));
        endif;
    }

    public function edit(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->ledger->getLedger($data['id']);
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['grpData'] = $this->group->getGroupList();
		$this->data['hsnData'] = $this->hsnModel->getHsnList();
        $this->load->view($this->ledgerForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->ledger->delete($id));
        endif;
    }
    
    
}
?>