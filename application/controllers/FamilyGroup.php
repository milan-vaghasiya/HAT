<?php
class FamilyGroup extends MY_Controller
{
    private $indexPage = "family_group/index";
    private $familyGroupForm = "family_group/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "family Group";
		$this->data['headData']->controller = "familyGroup";
		$this->data['headData']->pageUrl = "familyGroup";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->familyGroup->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getfamilyGroupData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addfamilyGroup(){
        $this->load->view($this->familyGroupForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['family_name']))
            $errorMessage['family_name'] = "Family Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->familyGroup->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->familyGroup->getFamilyGroup($this->input->post('id'));
        $this->load->view($this->familyGroupForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->familyGroup->delete($id));
        endif;
    }
    
}
?>