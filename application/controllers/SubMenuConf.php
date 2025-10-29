<?php
class SubMenuConf extends MY_Controller
{
    private $indexPage = "sub_menu/index";
    private $menuForm = "sub_menu/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "subMenuConf";
		$this->data['headData']->controller = "subMenuConf";
	}
	
	public function index(){
        $this->data['tableHeader'] = getConfigDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->subMenuConf->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSubMenuConfData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addSubMenuConf(){
        $this->data['menuRow'] = $this->mainMenuConf->getMainMenuList();
        $this->load->view($this->menuForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['sub_menu_seq']))
            $errorMessage['sub_menu_seq'] = "Sub Menu Seq is required.";
        if(empty($data['sub_menu_icon']))
            $errorMessage['sub_menu_icon'] = "Sub Menu Icon is required.";
        if(empty($data['sub_menu_name']))
            $errorMessage['sub_menu_name'] = "Sub Menu Name is required.";
         if(empty($data['sub_controller_name']))
            $errorMessage['sub_controller_name'] = "Sub Controller Name is required.";
        if(empty($data['menu_id']))
            $errorMessage['menu_id'] = "Main Menu is required.";
            
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->subMenuConf->save($data));
        endif;
    }

    public function edit(){
        $this->data['dataRow'] = $this->subMenuConf->getSubMenuConf($this->input->post('id'));
        $this->data['menuRow'] = $this->mainMenuConf->getMainMenuList(); 
        $this->load->view($this->menuForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->subMenuConf->delete($id));
        endif;
    }
}
?>