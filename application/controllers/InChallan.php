<?php
class InChallan extends MY_Controller{
    private $indexPage = "in_challan/index";
    private $formPage = "in_challan/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "In Challan";
		$this->data['headData']->controller = "inChallan";
		$this->data['headData']->pageUrl = "inChallan";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->inChallan->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;  
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getInChallanData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addChallan(){
        $this->data['challan_prefix'] = 'ICH/'.$this->shortYear.'/';
        $this->data['challan_no'] = $this->inChallan->nextTransNo(1);
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData']  = $this->item->getItemLists([6,7]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Customer Name is required.";
        if(empty($data['item_name']))
            $errorMessage['item_name_error'] = "Items is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $masterData = [
                'id' => $data['challan_id'],
                'challan_prefix' => $data['challan_prefix'],  
                'challan_no' => $data['challan_no'],
                'doc_no' => $data['doc_no'],
                'challan_type' => 1,
                'challan_date' => $data['challan_date'],
                'party_id' => $data['party_id'],
                'party_name' => $data['party_name'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_name' => $data['item_name'],
                'qty' => $data['qty'],
                'is_returnable' => $data['is_returnable'],
                'item_remark' => $data['item_remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $this->printJson($this->inChallan->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData']  = $this->item->getItemLists([6,7]);
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $this->inChallan->getInChallan($id);
        $this->data['orderData'] =  $this->inChallan->getCustomerSalesOrder($this->data['dataRow']->party_id);
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->inChallan->deleteChallan($id));
		endif;
	}

    public function getReturnItemTrans(){  
        $this->printJson($this->inChallan->getReturnItemTrans($this->input->post())); 
    }

    public function saveReturnItem(){
        $data = $this->input->post();  
        $errorMessage = array();
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($data['qty'])):
            $inItemData = $this->inChallan->getInChallanTransRow($data['ref_id']); 
            $pendingQty = $inItemData->qty - $inItemData->return_qty;
            if($data['qty'] > $pendingQty):
                $errorMessage['qty'] = "Invalid Qty.";
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['challan_trans_id'] = $inItemData->id;
            $data['created_by'] = $this->loginId;
            $this->printJson($this->inChallan->saveReturnItem($data));
        endif;
    }

    public function deleteReturnItem(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong...Please try again.']);
		else:
			$this->printJson($this->inChallan->deleteReturnItem($id));
		endif;
	}
}
?>