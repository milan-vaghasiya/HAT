<?php 
class ToolDie extends MY_Controller{
    private $indexPage = "tools_die/index";
    private $form = "tools_die/form";
    private $itemStockUpdateForm = "item/stock_update";
    private $itemOpeningStockForm = "item/opening_update";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);

    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "ToolDie";
        $this->data['headData']->controller = "toolDie";
        $this->data['headData']->pageUrl = "toolDie";
    }
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
	public function getDTRows(){  
        $result = $this->item->getToolDieDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getToolDieData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addItem(){
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->form,$this->data);
    }
   
    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['item_code']))
            $errorMessage['item_code'] = "Item Code is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$dieDime = Array();
            if(!empty($data['die_od'])){$dieDime[] = $data['die_od'];} 
            if(!empty($data['die_id'])){$dieDime[] = $data['die_id'];}
            if(!empty($data['die_length'])){$dieDime[] = $data['die_length'];}
            $data['item_image'] = (!empty($dieDime)) ? implode(' X ',$dieDime) : '';
			unset($data['die_od'],$data['die_id'],$data['die_length']);
			
			$castDime = Array();
            if(!empty($data['cast_od'])){$castDime[] = $data['cast_od'];} 
            if(!empty($data['cast_id'])){$castDime[] = $data['cast_id'];}
            if(!empty($data['cast_length'])){$castDime[] = $data['cast_length'];}
            $data['material_grade'] = (!empty($castDime)) ? implode(' X ',$castDime) : '';
			unset($data['cast_od'],$data['cast_id'],$data['cast_length']);
			
			/*$finishDime = Array();
            if(!empty($data['finish_od'])){$finishDime[] = $data['finish_od'];} 
            if(!empty($data['finish_id'])){$finishDime[] = $data['finish_id'];}
            if(!empty($data['finish_length'])){$finishDime[] = $data['finish_length'];}
            $data['make_brand'] = (!empty($finishDime)) ? implode(' X ',$finishDime) : '';
			unset($data['finish_od'],$data['finish_id'],$data['finish_length']);*/
			
            $this->printJson($this->item->save($data));
        endif;
    }
	
	public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['itemData'] = $this->item->getItemLists(1);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addStockTrans(){
        $id = $this->input->post('id');
        $this->data['stockTransData'] = $this->item->getStockTrans($id);
        $this->load->view($this->itemStockUpdateForm,$this->data);
    }

    public function saveStockTrans(){
        $data = $this->input->post();
        if(empty($data['trans_date']))
            $errorMessage['trans_date'] = "Date is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Quantity is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
		    $this->printJson(["status"=>1,"stockData"=>$this->item->saveStockTrans($data)]);
        endif;
	}

    public function deleteStockTrans(){
		$id = $this->input->post('id');
		$this->printJson($this->item->deleteStockTrans($id));
	}
	
	public function addOpeningStock(){
        $id = $this->input->post('id');
        $this->data['openingStockData'] = $this->item->getItemOpeningTrans($id);
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->itemOpeningStockForm,$this->data);
    }

    public function saveOpeningStock(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['location_id']))
            $errorMessage['location_id'] = "Store Location is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['ref_date'] = $this->startYearDate;
            $data['created_by'] = $this->session->userdata('loginId');
            //print_r($data);exit;
            $this->printJson($this->item->saveOpeningStock($data));
        endif;
    }

    public function deleteOpeningStockTrans(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->deleteOpeningStockTrans($id));
        endif;
    }

    public function getItemData(){
        $id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
        $this->printJson($result);
    }
}
?>