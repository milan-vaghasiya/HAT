<?php
class ProductReport extends MY_Controller
{
    private $item_report_page = "report/item/index";
    private $item_wise_stock = "report/item/item_stock";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Product Report";
		$this->data['headData']->controller = "reports/productReport";
		$this->data['floatingMenu'] = '';//$this->load->view('report/item/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'ITEM/PRODUCT REPORT';
        $this->load->view($this->item_report_page,$this->data);
    }

    /* Item Ledger */    
    public function itemWiseStock($item_id=""){
        $this->data['pageHeader'] = 'Stock Ledger';
		$this->data['fgData'] = $this->item->getItemList(1);
		$this->data['rmData'] = $this->item->getItemList(0);
		$this->data['itemId'] = $item_id;
        $this->load->view($this->item_wise_stock,$this->data);
    }

    public function getItemWiseStock()
	{
		$data = $this->input->post();
        $result = $this->productReporModel->getItemWiseStock($data);
        $this->printJson($result);
    }
}
?>