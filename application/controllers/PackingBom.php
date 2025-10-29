<?php
class PackingBom extends MY_Controller
{
    private $indexPage = "packing_bom/index";
    private $formPage = "packing_bom/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PackingBom";
		$this->data['headData']->controller = "packingBom";
		$this->data['headData']->pageUrl = "packingBom";
	}

    public function index(){
        $this->data['tableHeader'] = getPackingDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $data = $this->input->post();
        $result = $this->packings->getBomDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPackingBomData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function edit(){
        $item_id = $this->input->post('id');
        $this->data['item_id'] = $item_id;
        $this->data['bagData'] =  $this->packings->getConsumable(1);
        $this->data['boxData'] =  $this->packings->getConsumable(2);
        $this->data['paletteData'] =  $this->packings->getConsumable(3);
        $this->data['dataRow'] = $this->packings->getPackingBom($item_id);
        $this->load->view($this->formPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['box_id']) AND empty($data['palette_id']) AND empty($data['bag_capacity']))
            $errorMessage['generalError'] = "Please select atlest one unit.";

        if(!empty($data['box_id']))
            if(empty($data['box_capacity']))
                $errorMessage['box_capacity'] = "Box Capacity is required.";
        if(!empty($data['palette_id']))
            if(empty($data['palette_capacity']))
                $errorMessage['palette_capacity'] = "Palette Capacity is required.";
        if(!empty($data['bag_id']))
            if(empty($data['bag_capacity']))
                $errorMessage['bag_capacity'] = "Bag Capacity is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->packings->saveBom($data));
        endif;
    }


}
?>