<?php
class GradeMaster extends MY_Controller
{
    private $indexPage = "grade_master/index";
    private $formPage = "grade_master/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Grade Master";
		$this->data['headData']->controller = "gradeMaster";
        $this->data['headData']->pageUrl = "gradeMaster";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->gradeMaster->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getGradeMasterData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addGradeMaster(){
        $this->data['materialGrade'] = $this->gradeMaster->getMaterialGradeName();
        $this->data['standard'] = $this->gradeMaster->getStandardName();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
        if(empty($data['material_grade']))
            if(empty($data['materialGrade']))
			    $errorMessage['material_grade'] = "Material Grade is required.";
            else	
            $data['material_grade'] = $data['materialGrade'];
     
        if(empty($data['standard']))
            if(empty($data['standardName']))
                $errorMessage['standard'] = "Standard is required.";
            else	
            $data['standard'] = $data['standardName'];
            unset($data['materialGrade'],$data['standardName']);
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->gradeMaster->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['materialGrade'] = $this->gradeMaster->getMaterialGradeName();
        $this->data['standard'] = $this->gradeMaster->getStandardName();
        $this->data['dataRow'] = $this->gradeMaster->getGradeMaster($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->gradeMaster->delete($id));
        endif;
    }
}
?>