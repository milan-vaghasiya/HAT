<?php
class SalesOrder extends MY_Controller
{
	private $indexPage = "app/sales_order";

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Order";
		$this->data['headData']->controller = "app/salesOrder";
		$this->data['headData']->pageUrl = "salesOrder";
	}

	
	public function index($dispatch_status =0)
	{
        $this->data['dispatch_status'] = $dispatch_status;
		$this->data['bottomMenuName'] ='salesOrder';
        $this->data['soData'] = $this->salesOrder->getPendingSOForApp(['dispatch_status'=>$dispatch_status]);;
		$this->load->view($this->indexPage, $this->data);
	}

    
}
?>