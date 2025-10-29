<?php
class ToolConsumption extends MY_Controller
{
    private $indexPage = "tool_consumption/index";
    private $consumptionForm = "tool_consumption/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Tool Consumption";
		$this->data['headData']->controller = "toolConsumption";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = ToolConsumption($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addToolConsumption(){
        $id = $this->input->post('id'); 
        $this->data['consumableData'] = $this->item->getItemLists("2");
        $this->data['toolConsumptionData'] = $this->item->getToolConsumption($id);
        $this->data['operationData'] = $this->operation->getOperationList();
        $this->data['item_id'] = $id;
        $this->load->view($this->consumptionForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        $toolConsumptionData = [
            'id' => $data['id'],
            'item_id' => $data['item_id'],
            'ref_item_id' => $data['ref_item_id'],
            'tool_life' => $data['tool_life'],
            'operation' => $data['operation_id']
        ];

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveToolConsumption($toolConsumptionData));
        endif;
    }  
}
?>