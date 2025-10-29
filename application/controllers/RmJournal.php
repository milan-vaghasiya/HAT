<?php
class RmJournal extends MY_Controller
{
    private $indexPage = "rm_journal/index";
    private $formPage = "rm_journal/form";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "RM Journal";
		$this->data['headData']->controller = "rmJournal";
		$this->data['headData']->pageUrl = "stockJournal";
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $data = $this->input->post();
        $result = $this->rmJournal->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getRmjournalData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addRMJournal(){
        $this->data['rmData'] = $this->item->getItemList(3);
		$this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		 
		
        if(empty($data['item_id']))
			$errorMessage['item_id'] = "Item is required.";
        if(empty($data['qty']))
			$errorMessage['qty'] = "Qty is Required.";
        if(empty($data['location_id']))
			$errorMessage['location_id'] = "Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->rmJournal->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->rmJournal->delete($id));
        endif;
    }
}
?>