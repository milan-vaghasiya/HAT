<?php
class StockAdjustment extends MY_Controller{
    private $indexPage = "stock_adjustment/index";
    private $storeForm = "store/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Stock Adjustment";
		$this->data['headData']->controller = "stockAdjustment";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "store";
        $this->data['tableHeader'] = getDispatchDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$result = $this->packingRequest->getDTRows($this->input->post());
        // print_r($this->db->last_query());exit;
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->transfer_location=$this->MIS_PLC_STORE->id;
            $sendData[] = getStockAdjustmentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function saveStockTransfer(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['to_location_id']))
            $errorMessage['to_location_id'] = "Store Location is required.";
        if(empty($data['transfer_qty']))
            $errorMessage['transfer_qty'] = "Qty is required.";
        if(empty($data['transfer_reason']))
            $errorMessage['transfer_reason'] = "Reason is required.";
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $checkStock = $this->store->checkBatchWiseStock($data);
            if($checkStock->qty < $data['transfer_qty']):
                $this->printJson(['status'=>2,'message'=>'Stock not avalible.','stock_qty'=>$checkStock->qty]);
            else:
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->store->saveStockTransfer($data));
            endif;
        endif;
    }
}
?>