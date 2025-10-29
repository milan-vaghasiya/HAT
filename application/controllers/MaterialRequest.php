<?php 
class MaterialRequest extends MY_Controller{
    private $indexPage = "material_request/index";
    private $requestForm = "material_request/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Request";
		$this->data['headData']->controller = "materialRequest";
		$this->data['headData']->pageUrl = "materialRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->jobMaretialRequest->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;

            if($row->order_status == 0):
				$row->order_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
			elseif($row->order_status == 1):
                $row->order_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
            elseif($row->order_status == 2):
                $row->order_status_label = '<span class="badge badge-pill badge-info m-1">Accepted</span>';
            elseif($row->order_status == 3):
				$row->order_status_label = '<span class="badge badge-pill badge-dark m-1">Closed</span>';
			endif;
           
            $sendData[] = getMaterialRequest($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRequest(){
        $this->data['nextReqNo'] = $this->jobMaretialRequest->nextReqNo();
        $this->data['fgList'] = $this->item->getItemList(1);
        $this->data['processList'] = $this->process->getProcessList();
        //$this->data['itemData'] = $this->item->getItemList(3);
        $this->load->view($this->requestForm,$this->data);
    }

    public function getItemOptions(){
        $type = $this->input->post('type');
        $itemData = $this->item->getItemList();
        $options = '<option value="">Select Item Name</option>';
        foreach($itemData as $row):
			if($row->item_type == $type):             
				$options .= '<option value="'.$row->id.'" data-stock="'.$row->qty.' '.$row->unit_name.'">'.$row->item_name.'</option>';     
			endif;
        endforeach;
        $this->printJson(['status'=>1,'options'=>$options]);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!isset($data['req_item_id']) and empty($data['req_item_id'][0]))
            $errorMessage['req_item_id'] = "Item Name is required.";
        if(empty($data['req_date']))
            $errorMessage['req_date'] = "Request Date is required.";
        if(empty($data['req_qty'][0]))
            $errorMessage['req_qty'] = "Request Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 	
			$itemData = [
				'id' => $data['trans_id'],
				'req_item_id' => $data['req_item_id'],
				'req_qty' => $data['req_qty'],
				'req_item_name' => $data['req_item_name']
			];
            unset($data['req_item_id'], $data['req_item_name'], $data['req_qty']);
            $this->printJson($this->jobMaretialRequest->save($data,$itemData));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['nextReqNo'] = $this->jobMaretialRequest->nextReqNo();
        $this->data['fgList'] = $this->item->getItemList(1);
        $this->data['processList'] = $this->process->getProcessList();
        $this->data['itemData'] = $this->item->getItemList(3);
        $this->data['dataRow'] = $dataRow = $this->jobMaretialRequest->getRequestData($id);
        $this->data['editData'] = $this->jobMaretialRequest->getRequestEditData($this->data['dataRow']->req_no);
        $this->data['productKitData'] = $this->item->getProductKitData($dataRow->fg_item_id); 

        $this->load->view($this->requestForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobMaretialRequest->delete($id));
        endif;
    }
    
    public function bomWiseItemData(){ 
		$data = $this->input->post();
        $productKitData = $this->item->getProductKitData($data['item_id']); 
        $tbodyData="";$i=1; 
        if(!empty($productKitData)):
            $i=1;
            foreach($productKitData as $row):
                $tbodyData.= '<tr>
                                <td>'.$row->item_name.'</td>
                                <td>'.$row->qty.' ('.$row->unit_name.')</td>
                            </tr>';
            endforeach;
        else:
            $tbodyData.= '<tr><td colspan="3" style="text-align:center;">No Data Found</td></tr>';
        endif;
        
        $options = '<option value="">Select Item Name</option>';
        foreach($productKitData as $row):
            $itmStock = $this->store->getItemStock($row->ref_item_id);
            $stock_qty = 0;
            if(!empty($itmStock->qty)){$stock_qty = $itmStock->qty;}
			$options .= '<option value="'.$row->id.'" data-stock="'.$stock_qty.' '.$row->unit_name.'">'.$row->item_name.'</option>';     
        endforeach;

        $this->printJson(['status'=>1,"tbodyData"=>$tbodyData,'options'=>$options]);
	}

    //Created By Karmi @12/08/2022
    public function getItemStock()
    {
        $id = $this->input->post('item_id');
        $req_item_stock = (!empty($id))?$this->store->getItemStockRTD($id,3)->qty:"";
        $this->printJson($req_item_stock);
    }
}
?>