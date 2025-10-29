<?php
class StockJournal extends MY_Controller
{
    private $indexPage = "stock_journal/index";
    private $formPage = "stock_journal/form";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Stock Journal";
		$this->data['headData']->controller = "stockJournal";
		$this->data['headData']->pageUrl = "stockJournal";
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->stockJournal->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getStockjournalData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStockJournal(){
        $this->data['rmData'] = $this->item->getItemList(3);
        $this->data['fgData'] = $this->item->getItemList(1);
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		 
		if(empty($data['date']))
            $errorMessage['date'] = "Date  is required.";
        if(empty($data['rm_item_id']))
			$errorMessage['rm_item_id'] = "Raw Material  is required.";
        if(empty($data['rm_qty']))
			$errorMessage['rm_qty'] = "RM Qty.";
        if(empty($data['rm_location_id']))
			$errorMessage['rm_location_id'] = "RM Location is required.";
        if(empty($data['fg_item_id']))
			$errorMessage['fg_item_id'] = "Finish Goods is required.";
        if(empty($data['fg_qty']))
			$errorMessage['fg_qty'] = "FG Qty.";
        if(empty($data['fg_location_id']))
			$errorMessage['fg_location_id'] = "FG Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['date'] = formatDate('Y-m-d', $data['date']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->stockJournal->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->stockJournal->delete($id));
        endif;
    }

    
}
?>