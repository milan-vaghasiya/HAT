<?php
class PackingInstruction extends MY_Controller
{
    private $indexPage = "packing_instruction/index";
    private $formPage = "packing_instruction/form";
   
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Packing Instruction";
		$this->data['headData']->controller = "packingInstruction";
		$this->data['headData']->pageUrl = "packingInstruction";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
		$result = $this->packingInstruction->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            if($row->status == 0):
				$row->packing_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            else:
                $row->packing_status_label = '<span class="badge badge-pill badge-warning m-1">Complete</span>';
			endif;

            $sendData[] = getPackingInstructionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $this->data['itemData'] = $this->packingInstruction->getItemList();
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id']))
            $errorMessage['item_id'] = "Product is required.";
        if(empty($data['qty']))
            $errorMessage['qty'] = "Qty. is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->packingInstruction->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['itemData'] = $this->packingInstruction->getItemList();
        $this->data['dataRow'] = $this->packingInstruction->getPackingData($id);
        $this->load->view($this->formPage,$this->data);
    }
}
?>