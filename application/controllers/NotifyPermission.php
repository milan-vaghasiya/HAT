<?php
class NotifyPermission extends MY_Controller
{
    private $permissionPage = "notify_permission/notify_form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Notify Config";
		$this->data['headData']->controller = "notifyPermission";
        $this->data['headData']->pageUrl = "notifyPermission";
	}
	
	public function index(){        
        $this->data['permission'] = $this->notify->getPermission();
        $this->load->view($this->permissionPage,$this->data);
    }

    public function saveNotify(){
        $data = $this->input->post();
        $errorMessage = array();
    

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->notify->save($data));
        endif;
    }

    

	
    
    
}
?>