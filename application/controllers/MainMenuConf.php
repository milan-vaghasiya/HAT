<?php
class MainMenuConf extends MY_Controller
{
    private $indexPage = "main_menu/index";
    private $menuForm = "main_menu/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "mainMenuConf";
		$this->data['headData']->controller = "mainMenuConf";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->mainMenuConf->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getMainMenuConfData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addMainMenuConf(){
        $this->load->view($this->menuForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['menu_name']))
            $errorMessage['menu_name'] = "Menu Name is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->mainMenuConf->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->mainMenuConf->getMainMenuConf($this->input->post('id'));
        $this->load->view($this->menuForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->mainMenuConf->delete($id));
        endif;
    }
}
?>