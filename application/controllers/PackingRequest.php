<?php
class PackingRequest extends MY_Controller
{
    private $indexPage = "packing_request/index";
    private $packing_request_form = "packing_request/packing_request_form";
    
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PackingRequest";
		$this->data['headData']->controller = "packingRequest";
		$this->data['headData']->pageUrl = "packingRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
		$result = $this->packingRequest->getRequestedRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function getPackingRequset(){
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->load->view($this->packing_request_form,$this->data);
    }

    public function save(){ 
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['trans_child_id']))
            $errorMessage['trans_child_id'] = "Sales Order is required.";
        if(empty($data['request_qty']))
            $errorMessage['request_qty'] = "Qty. is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Requested Date is required.";
        if(empty($data['delivery_date']))
            $errorMessage['delivery_date'] = "Delivery Date is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['packing_id'])):
                $data['trans_prefix'] = "PCK/".$this->shortYear.'/';
                $data['trans_no'] = $this->packings->getNextNo();
                $data['trans_number'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
            endif;
            $this->printJson($this->packingRequest->savePackingRequest($data));
        endif;
    }

    public function editPackingRequset(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->packingRequest->getPackingReqData($id);
        $this->data['soData'] = '';
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->load->view($this->packing_request_form,$this->data);
    }

    public function getSOList(){
        $item_id = $this->input->post('item_id');
		$options="";
        $itmStock = $this->store->getItemCurrentStock($item_id,$this->PROD_STORE->id);
        $stock_qty = 0; if(!empty($itmStock->qty)){$stock_qty = $itmStock->qty;}
        if(!empty($item_id)):
            $soData = $this->packingRequest->getSalesOrderList($item_id);
            $options = '<option value="">Select Sales Order</option>';
            foreach($soData as $row):        
                $selected = (!empty($item_id) && $item_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" '.$selected.'  data-delivery_date="'.$row->delivery_date.'" data-item_code="'.$row->item_code.'" data-item_alias="'.$row->item_alias.'" data-trans_main_id="'.$row->trans_main_id.'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).' ['.$row->item_code.' | Pend. Qty:'.($row->qty-$row->dispatch_qty).']</option>';
            endforeach;
        endif;
        $this->printJson(['options'=>$options,'stock_qty'=>$stock_qty]);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->packingRequest->delete($id));
        endif;
    }
}
?>