<?php
class Terms extends MY_Controller
{
    private $indexPage = "terms/index";
    private $termsForm = "terms/form";
    private $typeArray = ["Purchase","Sales","Vendor"];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Terms";
		$this->data['headData']->controller = "terms";
        $this->data['headData']->pageUrl = "terms";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->terms->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getTermsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addTerms(){
        $this->data['typeArray'] = $this->typeArray;
        $this->load->view($this->termsForm, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['title']))
			$errorMessage['title'] = "Title is required.";
        if(empty($data['conditions']))
			$errorMessage['conditions'] = "Conditions is required.";
        if(empty($data['type']))
			$errorMessage['type'] = "Type is required.";
        unset($data['typeSelect']);
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->terms->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->terms->getTerms($id);
        $this->data['typeArray'] = $this->typeArray;
        $this->load->view($this->termsForm, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->terms->delete($id));
        endif;
    }
}
?>