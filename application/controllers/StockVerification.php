<?php
class StockVerification extends MY_Controller
{
    private $indexPage = "stock_verification/index";
    private $formPage = "stock_verification/form";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Stock Verification";
		$this->data['headData']->controller = "stockVerification";
		$this->data['headData']->pageUrl = "stockVerification";
	}

    public function index(){
        $this->data['pageHeader'] = 'STOCK VERIFICATION REPORT';
        $this->data['dataUrl'] = 'getStockVerification/1';
        $this->data['tableHeader'] = getStoreDtHeader("stockVerification");
        $this->load->view($this->indexPage,$this->data);
    }         

    public function getStockVerification($item_type=""){
        $result = $this->stockVerify->getItemData($this->input->post(),$item_type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $editParam = "{'id' : ".$row->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editStock', 'title' : 'Update Stock'}";
            $editButton = '<a class="btn btn-success btn-sm waves-effect waves-light btn-edit" href="javascript:void(0)" datatip="Edit" onclick="editStock('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
            
            $itmStock = $this->store->getItemStock($row->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}

            $pqData = $this->stockVerify->getVarifyData($row->id);
            $pQty=0;  $entryDate='';
            if(!empty($pqData)){ 
                $pQty = $pqData->physicalQty; $entryDate = ' ('.formatDate($pqData->entry_date).')';
            }
            
            $sendData[] = [$i++,$row->item_name,$row->item_code,floatVal($row->qty),$pQty.$entryDate,$editButton];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function editStock(){   
        $data = $this->input->post();
        $result = $this->stockVerify->getItemWiseStock($data);
        $this->printJson($result);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
		if(!empty($data['physical_qty'])): $i=1;
			foreach($data['physical_qty'] as $key=>$value):
                if($value != ''){
				    if(empty($data['reason'][$key])){
					    $errorMessage['reason'.$i] = "Reason is required.";
                    }
                } $i++;
			endforeach;
		endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->stockVerify->save($data));
        endif;
    }
}
?>