<?php
class Costing extends MY_Controller
{
    private $indexPage = "costing/index";
    private $viewProductProcess = "costing/view_process";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Costing";
		$this->data['headData']->controller = "costing";
		$this->data['headData']->pageUrl = "costing";
	}

    public function index(){
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $data = $this->input->post();
        $result = $this->item->getCostingDTRows($data,1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
			$costingStatus = $this->item->checkCostingStatus($row->id);

            $row->process = (!empty($costingStatus->process)) ?  $costingStatus->cstng.'/'.$costingStatus->process : '';
			
            $sendData[] = getCostingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 

		$this->data['productOperation']="";$options=Array();$opt='';
		foreach ($this->data['processData'] as $row) :
			$opt='';
			$ops = $this->item->getProductOperationForSelect($row->id);
			foreach($this->data['operationDataList'] as $operation):
				$selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
				 $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;
        $this->data['item_id'] = $id;   
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function saveCosting(){
        $data = $this->input->post();
        $errorMessage = array();

        $costingData = ['id' => $data['id'], 'costing' => $data['costing'] ];

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->item->saveCosting($costingData));
        endif;
    }
    
}
?>