<?php
class ReportsView extends MY_Controller
{
    private $indexPage = "report/index_report";
  
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Report";
		$this->data['headData']->controller = "reportsView";
	}
	
	public function index(){        
        $this->data['pageHeader'] ='Reports';
        $this->data['permission'] = $this->permission->getEmployeeReportMenus();
        $this->load->view($this->indexPage,$this->data);
    }
}
?>