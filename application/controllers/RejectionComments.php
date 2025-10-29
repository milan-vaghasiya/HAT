<?php
class RejectionComments extends MY_Controller
{
    private $indexPage = "rejection_comment/index";
    private $rejectionCommentForm = "rejection_comment/form";
    private $idleIndex = "idle_reason/index";
    private $idleForm = "idle_reason/form";
    private $rejectionSubTypes = [
        1=>"Turning", 2=>"Milling", 3=>"CASTING / MATERIAL / OUT SOURCE", 4=>"Other"
    ];


	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Rejection Comments";
		$this->data['headData']->controller = "rejectionComments";		
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "rejectionComments";
        $this->data['type'] = 1;
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function idleIndex(){
        $this->data['headData']->pageUrl = "rejectionComments/idleIndex";
        $this->data['type'] = 2;
        $this->data['tableHeader'] = getProductionHeader("idleReason");
        $this->load->view($this->idleIndex,$this->data);
    }

    public function getDTRows($type){
        $data = $this->input->post();
        $data['type'] = $type;
        $result = $this->comment->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getRejectionCommentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRejectionComment(){
        $this->data['subTypes'] = $this->rejectionSubTypes;
        $this->load->view($this->rejectionCommentForm,$this->data);
    }
	
	public function addIdleReason(){
        $this->load->view($this->idleForm);
    }
	
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['remark']))
             $errorMessage['remark'] = "Reason is required.";
        if($data['type'] == 2): 
            if(empty($data['code']))
                $errorMessage['code'] = "Code is required.";
        endif;
        if($data['type'] == 1):
            if(empty($data['sub_type']))
                $errorMessage['sub_type'] = "Sub type is required.";
        endif;
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->comment->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->comment->getComment($this->input->post('id'));
		if($this->data['dataRow']->type == 1): 
            $this->data['subTypes'] = $this->rejectionSubTypes;
            $this->load->view($this->rejectionCommentForm,$this->data);
        else:
            $this->load->view($this->idleForm,$this->data);
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->comment->delete($id));
        endif;
    }
}
?>