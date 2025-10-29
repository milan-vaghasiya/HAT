<?php
class PurchaseRequest extends MY_Controller
{
    private $indexPage = "purchase_request/index";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PurchaseRequest";
		$this->data['headData']->controller = "purchaseRequest";
		$this->data['headData']->pageUrl = "purchaseRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data); 
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status'] = $status;
        $result = $this->purchaseRequest->getDTRows($data);
        $sendData = array();$i=1;$count=0;
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
            
            $sendData[] = getPurchaseRequestData($row);

            // if(!empty($row->item_data)):        
            //     $itemData = json_decode($row->item_data); $i=1; $row->req_item_name = ''; $row->material_type = ''; $row->req_qty='';
            //     foreach($itemData as $item):
            //         $item_type_id = $this->item->getItem($item->req_item_id)->item_type;
            //         $itemType = $this->item->getItemGroupById($item_type_id)->group_name;
            //         if($i == 1){$row->req_item_name = $item->req_item_name; $row->material_type=$itemType; $row->req_qty=$item->req_qty;}
            //         else{$row->req_item_name .= '<br>'.$item->req_item_name; $row->material_type.='<br>'.$itemType; $row->req_qty.='<br>'.$item->req_qty;}
            //         $i++;
            //     endforeach;
            // else:
            //     if(!empty($row->req_item_id)):
            //         $itemData = $this->item->getItem($row->req_item_id);
            //         $itemType = $this->item->getItemGroupById($item_type_id)->group_name;
            //         $row->req_item_name = $itemData->item_name;
            //         $row->material_type = $itemType;
            //     else:
            //         $row->req_item_name = '';
            //         $row->material_type = '';
            //     endif;
            // endif;
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function approvePreq(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseRequest->approvePreq($data));
		endif;
	}

    public function closePreq(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->purchaseRequest->closePreq($data));
		endif;
	}
	
	/*  Created By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : po
    */  
    public function getPurchaseOrder(){
        $data = $this->input->post();  
        $this->printJson($this->purchaseRequest->getPurchaseOrder());
    }
}
?>