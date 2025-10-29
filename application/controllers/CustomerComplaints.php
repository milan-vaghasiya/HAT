<?php
class CustomerComplaints extends MY_Controller
{
    private $indexPage = "customer_complaints/index";
    private $formPage = "customer_complaints/form";
    private $solution_form = "customer_complaints/solution_form";
   

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Customer Complaints";
		$this->data['headData']->controller = "customerComplaints";
		$this->data['headData']->pageUrl = "customerComplaints";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post(); $data['status'] = $status;
        $result = $this->customerComplaints->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            if($row->product_returned == 1)
            { $row->product_returned = "No"; }
            if($row->product_returned == 2)
            { $row->product_returned = "Yes"; }
            $sendData[] = getCustomerComplaintsData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addCustomerComplaints(){
        $this->data['trans_prefix'] = "C" . n2y(date('Y')).n2m(date('m'));  
        $this->data['nextTransNo'] = $this->customerComplaints->getNextTransNo();
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData']=$this->item->getItemList(1);
        $this->load->view($this->formPage,$this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        // print_r($data);exit;
        $errorMessage = array();
        if (empty($data['so_number']))
            $errorMessage['so_number'] = "Ref. of Complaint is required.";

		if (empty($data['complaint']))
            $errorMessage['complaint'] = "Details of Complaint is required.";

        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";

        if (empty($data['product_returned']))
            $errorMessage['product_returned'] = "product returned is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $masterData = [ 
				'id' => $data['id'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_no' => $data['trans_no'], 
				'trans_number' => $data['trans_prefix'].sprintf("%04d",$data['trans_no']),
				'trans_date' => date('Y-m-d',strtotime($data['trans_date'])),
				'party_id' => $data['party_id'],
				'so_number' => $data['so_number'],
				'so_id' => $data['so_id'],
				'item_id' => $data['item_id'],
				'complaint' => $data['complaint'],
				'product_returned' => $data['product_returned'],
				'created_by' => $this->session->userdata('loginId')
            ];
            $this->printJson($this->customerComplaints->save($masterData));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $dataRow = $this->customerComplaints->getCustomerComplaints($id);
        $resultData=$this->customerComplaints->getSalesOrderByParty( $this->data['dataRow']->party_id);

		$html="";
		foreach($resultData as $row)
		{
			$selected = (!empty( $this->data['dataRow']->so_number) &&  $this->data['dataRow']->so_number == $row->id) ? "selected" : '';
			$html .= '<option value="'.$row->id.'" ' . $selected . '>'.getPrefixNumber($row->trans_prefix,$row->trans_no).' (PO No : '.$row->doc_no.')</option>';
		}
		$this->data['html']=$html;
        
        if(!empty( $dataRow->so_trans_id)){
            $itemData=$this->salesOrder->getSalesOrderTransactions( $this->data['dataRow']->party_id);
            $options="";
            foreach($itemData as $row)
            {
                $selected = (!empty( $this->data['dataRow']->so_number) &&  $this->data['dataRow']->so_number == $row->id) ? "selected" : '';
                $options .= "<option data-row='".json_encode($row)."' data-so_trans_id='".$row->id."' value='".$row->id."'> ".$row->full_name."</option>";
                
                // '<option data-row="'.json_encode($row).'" data-so_trans_id="'.$row->id.'" value="'.$row->item_id.'" ' . $selected . '>'.$row->full_name.'</option>';
            }
            $this->data['options']=$options;
        }else{
            $this->data['itemData']=$this->item->getItemList(1);
        }
        
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customerComplaints->delete($id));
        endif;
    }

    public function getPObyParty()
	{
		$resultData=$this->customerComplaints->getSalesOrderByParty($this->input->post('party_id'));
		$html="<option value=''>Select Sales Order</option>";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                $html .= '<option value="'.$row->id.'" data-so_number ="'.(getPrefixNumber($row->trans_prefix,$row->trans_no)).'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).' (PO No : '.$row->doc_no.')</option>';
                $i++;
            endforeach;
        else:
            $html = '<option value="">No Data Found</option>';
        endif;
		$result['htmlData']=$html;
		$this->printJson($result);

	}

    public function getItemList()
    {
        $so_number=$this->input->post('so_number');
        if(!empty($so_number)){
            $itemData=$this->salesOrder->getSalesOrderTransactions($so_number);
            $options="<option value=''>Select Item</option>";$i=1;
            foreach($itemData as $row):
                $options .= "<option data-row='".json_encode($row)."' data-so_trans_id='".$row->id."' value='".$row->item_id."'> ".$row->item_name."</option>";
            endforeach;
        }else{
            $itemData=$this->item->getItemList(1);
            $options="<option value=''>Select Item</option>";$i=1;
            foreach($itemData as $row):
                $options .= "<option data-row='".json_encode($row)."' data-so_trans_id='0' value='".$row->id."'> ".$row->item_name."</option>";
            endforeach;
        }
		
	$this->printJson(['status'=>1,'options'=>$options]);
    }
 
    public function complaintSolution(){
        $data = $this->input->post();
        $this->data['dataRow'] = $dataRow = $this->customerComplaints->getCustomerComplaints($data['id']);
        $this->load->view($this->solution_form,$this->data);
    }
    
    public function saveSolution(){
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['action_taken']))
            $errorMessage['action_taken'] = " Action Taken is required.";


        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['status'] =1;
            $this->printJson($this->customerComplaints->save($data));
        endif;
    }
}
?>