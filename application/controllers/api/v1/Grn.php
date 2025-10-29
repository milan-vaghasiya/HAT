<?php
class Grn extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function listing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search];
        $this->data['grnList'] = $this->grnModel->getGrnListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function addGRN(){
        $this->data['grn_no'] = $this->grnModel->nextGrnNo();
		$this->data['grn_prefix'] = 'GRN/'.$this->shortYear.'/';
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function colorList(){
        $this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function grnTypeList(){
        $this->data['grnTypeList'] = [
            ['id'=>'1','name'=>'Regular'],
            ['id'=>'2','name'=>'Job Work']
        ];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();
		
		if(empty($data['grn_no']))
			$errorMessage['grn_no'] = "GRN No. is required.";
		if(empty($data['party_id']))
			$errorMessage['party_id'] = "Supplier Name is required.";
		if(empty($data['item_id'][0]))
			$errorMessage['general_error'] = "Item is required.";

		if(!empty($data['item_id'])):
			foreach($data['location_id'] as $key=>$value):
				if(empty($value)):
					$errorMessage['general_error'] = "Location is required.";
					break;
				endif;
			endforeach;
		endif;
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$masterData = [ 
				'id' => $data['grn_id'],
				'type' => $data['type'],
				'order_id' => $data['order_id'],
				'grn_prefix' => $data['grn_prefix'], 
				'grn_no' => $data['grn_no'], 
				'grn_date' => date('Y-m-d',strtotime($data['grn_date'])),
				'party_id' => $data['party_id'], 
				'challan_no' => $data['challan_no'], 
				'remark' => $data['remark'],
                'created_by' => $this->loginId
			];
							
			$itemData = [
				'id' => $data['trans_id'],
				'item_id' => $data['item_id'],
				'item_type' => $data['item_type'],
				'unit_id' => $data['unit_id'],
				'fgitem_id' => $data['fgitem_id'],
                'fgitem_name' => $data['fgitem_name'],
				'batch_no' => $data['batch_no'],
				'po_trans_id' => $data['po_trans_id'],
				'location_id' => $data['location_id'],
				'qty' => $data['qty'],				
				'qty_kg' => $data['qty_kg'],
				'price' => $data['price'],
				'color_code' => $data['color_code'],
                'created_by' => $this->loginId
			];

            $result = $this->grnModel->save($masterData,$itemData);
            unset($result['url']);
			$this->printJson($result);
		endif;
    }

    public function edit($id){
        if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
            $this->data['grnData'] = $this->grnModel->editInv($id);
			$this->data['grnTypeList'] = [
				['id'=>'1','name'=>'Regular'],
				['id'=>'2','name'=>'Job Work']
			];
			$this->data['colorList'] = explode(',',$this->grnModel->getMasterOptions()->color_code);
			$this->data['itemTypeList'] = $this->item->getItemGroup();
			$this->data['locationList'] = $this->store->getStoreLocationListWithoutProcess();
            $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
        endif;
    }

    public function delete($id){
        if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->grnModel->delete($id));
		endif;
    }

    public function partyPoList($id){
        $this->data['poList'] = $this->grnModel->getGrnOrders($id)['poList'];
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

    public function createGrnOnPO(){
        $data = $this->input->post();
        if($data['ref_id']):
			$orderItems = $this->purchaseOrder->getOrderItems($data['ref_id']);
			$orderData = new stdClass();
			$orderData->party_id = $data['party_id'];
			$orderData->id = implode(",",$data['ref_id']);
			
			$this->data['orderData'] = $orderData;
            $this->data['orderItems'] = $orderItems;
			$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
		else:
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		endif;
    }

    public function inwardQCListing($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $status = (isset($_REQUEST['status']) && !empty($_REQUEST['status']))?$_REQUEST['status']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'status'=>$status];
        $this->data['inwardQCListing'] = $this->grnModel->getInwardQCListing($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }

	public function inspectionStatusList(){
		$this->data['statusList'] = [
			['name'=>'OK'],
			['name'=>'Not Ok'],
		];
		$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
	}

	public function materialInspection($id){
		$this->data['materialData'] = $this->grnModel->getmaterialInspectionData($id);
		$this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
	}

	public function inspectedMaterialSave(){
		$data = $this->input->post();
		$errorMessage = array();
		$i=1;$total_qty = 0;
		foreach($data['item_id'] as $key=>$value):
			$inspected_qty = ($data['inspection_status'][$key] == "Ok")?($data['recived_qty'][$key] - $data['short_qty'][$key]):0;
			$data['reject_qty'][$key] = ($data['inspection_status'][$key] != "Ok")?$data['recived_qty'][$key]:0;
			$data['short_qty'][$key] = ($data['inspection_status'][$key] == "Ok")?$data['short_qty'][$key]:0;

			$total_qty = $inspected_qty + $data['ud_qty'][$key] + $data['reject_qty'][$key] + $data['scrape_qty'][$key];			
			if($total_qty > $data['recived_qty'][$key]):
				$errorMessage['recived_qty'.$i] = "Received Qty. mismatched.";
			endif;
			$i++;
		endforeach;

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $data['created_by'] = $this->loginId;
			$this->printJson($this->grnModel->inspectedMaterialSave($data));
		endif;
    }
}
?>