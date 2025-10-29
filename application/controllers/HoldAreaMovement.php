<?php
class HoldAreaMovement extends MY_Controller
{
    private $indexPage = "hold_area/index";


    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Hold Area";
		$this->data['headData']->controller = "holdAreaMovement";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "holdAreaMovement";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=1){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->holdArea->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $qtyData=$this->holdArea->getOutwardQtyFrmHLD($row->id);
            // print_r($this->db->last_query());
            $row->ok_qty = !empty($qtyData->ok_qty)?$qtyData->ok_qty:0;
            $row->rej_qty = !empty($qtyData->rej_qty)?$qtyData->rej_qty:0;
            $totalInspQty=$row->ok_qty+$row->rej_qty;
            if($status == 1 && $row->in_qty > $totalInspQty)
            {
                $row->sr_no = $i++;
                $row->vendor_name = !empty($row->party_name)?$row->party_name:'In House';
                $row->movement_btn=1;
                $sendData[] = getHoldAreaMovementData($row);

            }

            if($status == 2 && $row->in_qty == $totalInspQty)
            {
                $row->sr_no = $i++;
                $row->vendor_name = !empty($row->party_name)?$row->party_name:'In House';
                $row->movement_btn=1;
                if($row->entry_type==2){
                    $row->movement_btn=0;
                }
                $sendData[] = getHoldAreaMovementData($row);
            }
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
   

}
?>