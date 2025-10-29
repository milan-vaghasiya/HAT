<?php
class ContactDirectory extends MY_Controller
{
    private $indexPage = "contact_directory/index";
    private $contactForm = "contact_directory/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "ContactDirectory";
		$this->data['headData']->controller = "contactDirectory";
        $this->data['headData']->pageUrl = "contactDirectory";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->contactDirectory->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getContactDirectoryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addContact(){
        $this->load->view($this->contactForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['comapny_name']))
			$errorMessage['comapny_name'] = " Company Name is required.";
        if(empty($data['contact_person']))
			$errorMessage['contact_person'] = "Contact Person is required.";
        if(empty($data['contact_number']))
			$errorMessage['contact_number'] = "Contact Number is required.";
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->contactDirectory->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->contactDirectory->getContactDirectory($id);
        $this->load->view($this->contactForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->contactDirectory->delete($id));
        endif;
    }
}
?>