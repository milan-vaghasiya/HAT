<?php
class PurchaseOrder extends MY_Controller
{
	private $indexPage = "app/purchase_order";

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Order";
		$this->data['headData']->controller = "app/purchaseOrder";
		$this->data['headData']->pageUrl = "purchaseOrder";
	}

	
	public function index($dispatch_status =0)
	{
        $this->data['dispatch_status'] = $dispatch_status;
		$this->data['bottomMenuName'] ='purchaseOrder';
        $this->data['poData'] = $this->purchaseOrder->getPendingPoForApp(['dispatch_status'=>$dispatch_status]);;
		$this->load->view($this->indexPage, $this->data);
	}

    
}
?>