<?php
class SetupInspection extends MY_Controller{
    private $indexPage = "setup_inspection/index";
    private $formPage = "setup_inspection/form";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Setup Inspection";
		$this->data['headData']->controller = "setupInspection";
		$this->data['headData']->pageUrl = "setupInspection";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->setupInspection->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($row->setup_status == 0):
				$row->status = '<span class="badge badge-pill badge-danger m-1">'.$row->ins_status.'</span>';
			elseif($row->setup_status == 1):
				$row->status = '<span class="badge badge-pill badge-warning m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 2):
                $row->status = '<span class="badge badge-pill badge-info m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 3):
                $row->status = '<span class="badge badge-pill badge-success m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 4):
                $row->status = '<span class="badge badge-pill badge-primary m-1">'.$row->ins_status.'</span>';
			elseif($row->setup_status == 5):
				$row->status = '<span class="badge badge-pill badge-dark m-1">'.$row->ins_status.'</span>';
            elseif($row->setup_status == 6):
                $row->status = '<span class="badge badge-pill badge-info m-1">'.$row->ins_status.'</span>';
			endif;
            $sendData[] = getSetupInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function acceptSetupInspection(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->setupInspection->startInspection($id));
        endif;
    }

    public function setupInspection(){
        $this->data['inspectionStatus'] = ["3"=>"Approved","4"=>"Resetup"];
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->setupInspection->getSetupInspectionData($id);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['setup_status']))
            $errorMessage['setup_status'] = "Inspection Status is required.";
        if(empty($data['qci_note']))
            $errorMessage['qci_note'] = "Qci Note is required.";
        if(!empty($data['setup_status']) && $data['setup_status'] == 3 && empty($data['id']))
            $errorMessage['attachment'] = "Attachment is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if($_FILES['attachment']['name'] != null || !empty($_FILES['attachment']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['attachment']['name'];
				$_FILES['userfile']['type']     = $_FILES['attachment']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['attachment']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['attachment']['error'];
				$_FILES['userfile']['size']     = $_FILES['attachment']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/setup_ins_report/');
				$config = ['file_name' => time()."_Ins_Rep_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if(!$this->upload->do_upload()):
					$errorMessage['attachment'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['attachment'] = $uploadData['file_name'];
				endif;
			endif;
            $data['created_by'] = $this->session->userdata('loginId');
            $data['inspection_date'] = date("Y-m-d H:i:s");
            $this->printJson($this->setupInspection->save($data));
        endif;
    }
}
?>