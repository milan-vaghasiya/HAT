<?php
class GenerateScrap extends MY_Controller{
    private $indexPage = "generate_scrap/index";
    private $form = "generate_scrap/form";
    
    public function __construct(){
        parent::__construct();
        $this->data['headData']->pageTitle = "Generate Scrap";
		$this->data['headData']->controller = "generateScrap";
        $this->data['headData']->pageUrl = "generateScrap";
    }

    public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->generateScrap->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getGenerateScrapData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addScrap(){
        $this->data['itemList'] = $this->item->getItemList(10);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data= $this->input->post();
        $errorMessage = array();

        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item Name is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->generateScrap->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->generateScrap->getScrap($id);
        $this->data['itemList'] = $this->item->getItemList(10);
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->generateScrap->delete($id));
        endif;
    }
}
?>