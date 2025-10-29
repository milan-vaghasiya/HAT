<?php 
class GeneralMaterialRequest extends MY_Controller{
    private $indexPage = "general_material_request/index";
    private $requestForm = "general_material_request/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Material Request";
		$this->data['headData']->controller = "generalMaterialRequest";
	}
	
	public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=1){
        $data = $this->input->post();$data['status'] = $status;
        $result = $this->jobMaterial->getGeneralRequestDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;
            $row->req_item_stock = (!empty($row->req_item_id))?$this->store->getItemStockRTD($row->req_item_id,3)->qty:"";
            $sendData[] = getGeneralRequestData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRequest()
    {
        $this->data['req_no'] = $this->jobMaterial->getMaxRequestNo(2);
        $this->data['itemData'] = $this->item->getItemList(2);
        $this->load->view($this->requestForm,$this->data);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        
        if(empty($data['req_item_id'][0]))
            $errorMessage['general_item'] = "Item Name is required.";
       
        if(empty($data['req_qty'][0]))
            $errorMessage['general_item'] = "Qty is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
            
            $data['created_by'] = $this->loginId;
            $this->printJson($this->jobMaterial->saveGeneralRequest($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->jobMaterial->deleteGeneralRequest($id));
        endif;
    }
    
    //Karmi 
    public function getItemStock(){
        $id = $this->input->post('item_id');
        $req_item_stock = (!empty($id))?$this->store->getItemStockRTD($id,3)->qty:"";
        $this->printJson($req_item_stock);
    }
}
?>