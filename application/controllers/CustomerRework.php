<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class CustomerRework extends MY_Controller{	
	private $indexPage = "customer_rework/index";
	private $formPage = "customer_rework/form";
	private $rework_index = "customer_rework/rework_index";
	private $complete_form = "customer_rework/complete_form";

	public function __construct(){ 
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Customer Rework";
		$this->data['headData']->controller = "customerRework";
		$this->data['headData']->pageUrl = "customerRework";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
		$data = $this->input->post(); $data['status']=$status;
        $result = $this->customerRework->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            $sendData[] = getCustomerReworkData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function addRework(){
        $this->data['trans_prefix'] = "DBR-" . n2y(date('Y')).n2m(date('m'));  
        $this->data['nextTransNo'] = $this->customerRework->getNextTransNo();
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->formPage,$this->data);
    }

	public function getInvbyParty(){
		$data = $this->input->post();
		$invList = $this->customerRework->getSalesInvoiceList($data['party_id']);
		$options='<option value=""> Select Invoice</option>';
		if(!empty($invList)){
			foreach($invList as $row){
				$options.='<option value="'.$row->id.'">'.$row->trans_number.'</option>';
			}
		}
		$this->printJson(['status'=>1,'options'=>$options]);
	}

	public function getInvItemList(){
		$data = $this->input->post();
		$invList = $this->customerRework->salesTransactions($data['inv_id']);
		$options='<option value=""> Select Item</option>';
		if(!empty($invList)){
			foreach($invList as $row){
				$soData = $this->salesOrder->getSoByTransId(['so_trans_id'=>$row->so_ref_id]);
				$options.="<option value='".$row->item_id."' data-row='".json_encode($row)."'>".(!empty($soData->trans_number)?'['.$soData->trans_number.']':'').$row->full_name."</option>";
			}
		}
		$this->printJson(['status'=>1,'options'=>$options]);
	}

	
	public function getItemWiseBatchDetail(){
		$data = $this->input->post();
		$siData = $this->salesInvoice->salesTransRow($data['inv_child_id']);
		$postData['item_id']=$siData->item_id;
		if($siData->from_entry_type == 5){
			$postData['ref_type'] = 4;
			$postData['ref_id'] = $siData->ref_id;
		}else{
			$postData['ref_type'] = 5;
			$postData['ref_id'] = $siData->id;
		}
		$batchList = $this->customerRework->getItemWiseBatchDetail($postData);
		// print_r($this->db->last_query());
		$tbody = "";
		if(!empty($batchList)){
			$i=1;
			foreach($batchList as $row):                
				$qty = abs($row->qty);
				$tbody .= '<tr>';
					$tbody .= '<td class="text-center">'.$i.'</td>';
					$tbody .= '<td>'.$row->batch_no.'</td>';
					$tbody .= '<td>'.$row->tc_no.(!empty($row->ref_batch)? ' / '.$row->ref_batch:'').'</td>';
					$tbody .= '<td>'.floatVal($qty).'</td>';
					$tbody .= '<td>
						<input type="text" name="batch_quantity[]" class="form-control batchQty floatOnly" data-rowid="'.$i.'" data-cl_stock="'.$qty.'" min="0" value="" />
						<input type="hidden" name="ref_batch[]" id="ref_batch'.$i.'" value="'.$row->ref_batch.'" />
						<input type="hidden" name="tc_number[]" id="tc_no'.$i.'" value="'.$row->tc_no.'" />
						<input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
						<input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
						<div class="error batch_qty'.$i.'"></div>
					</td>';
				$tbody .= '</tr>';
				$i++;
			endforeach;
		}
		$this->printJson(['status'=>1,'htmlData'=>$tbody]);
	}


	public function save(){
		$data = $this->input->post();
        $errorMessage = array();
        if (empty($data['party_id'])){$errorMessage['party_id'] = "Party is required.";}

		if (empty($data['trans_date'])){$errorMessage['trans_date'] = "Date is required.";}

        if (empty($data['item_id'])) { $errorMessage['item_id'] = "Item is required.";}

		if (empty($data['inv_id'])) { $errorMessage['inv_id'] = "Invoice is required.";}

        if (!isset($data['batch_quantity'])){$errorMessage['general_error'] = "Rework Qty is required.";}

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
           
            $this->printJson($this->customerRework->save($data));
        endif;
	}

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customerRework->delete($id));
        endif;
    }

	/** Production Index */
	public function reworkIndex(){
		$this->data['headData']->pageUrl = "customerRework/reworkIndex";
		$this->data['tableHeader'] = getProductionHeader('prodCustomerRework');    
        $this->load->view($this->rework_index,$this->data);
    }

	/** Production  */
    public function getReworkDTRows($status=0){
		$data = $this->input->post(); $data['status']=$status;
        $result = $this->customerRework->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->po_no = ''; $row->ref_no = '';
            $sendData[] = getProdCustomerReworkData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	public function acceptRework(){
        $data['id'] = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->customerRework->acceptRework($data['id']));
        endif;
    }

	public function addRWRejQty(){
		$data = $this->input->post();
		$this->data['dataRow'] = $this->customerRework->getReworkData(['id'=>$data['id'],'single_row'=>1]);
        $this->load->view($this->complete_form,$this->data);
    }

	public function saveRwRejQty(){
		$data = $this->input->post();
        $errorMessage = array();
		$okQty = !empty($data['ok_qty'])?$data['ok_qty']:'';
		$rejQty = !empty($data['rej_qty'])?$data['rej_qty']:'';
		if(floatval($okQty + $rejQty) != floatval($data['qty'])){
			$errorMessage['ok_qty'] = "Ok is Invalid.";
		}
        if (empty($data['ok_qty'])){$errorMessage['ok_qty'] = "Ok is required.";}

		if (empty($data['rej_qty'])){$errorMessage['rej_qty'] = "Rej Qty is required.";}
		
		
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
           
            $this->printJson($this->customerRework->saveRwRejQty($data));
        endif;
	}

}
?>