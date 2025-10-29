 <?php 
class PaymentTransaction extends My_controller{
	private $indexPage = "payment_transaction/index";
	private $formPage = "payment_transaction/form";
    private $check_status = "payment_transaction/check_status";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Payment Transaction";
		$this->data['headData']->controller = "PaymentTransaction";
		$this->data['headData']->pageurl = "payment_transaction";
	}

	public function index(){
		$this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

	public function getDtRows(){
		$result = $this->paymentTrans->getDtRows($this->input->post()); 
		$sendData = array(); $i=1;
		foreach($result['data'] as $row):
			$row->sr_no = $i++; $row->invNo="";
			if($row->invoice_no == 0){ $row->invNo = "Advance"; } else {
				$refno = array();
				$invData = $this->paymentTrans->getRefNo($row->invoice_no);
				if(!empty($invData)){ 
					foreach($invData as $inv):
						$refno[] = getPrefixNumber($inv->trans_prefix,$inv->trans_no); 
					endforeach;
				} 
				$row->invNo = implode(", ",$refno);
			}

			$sendData[] = getPaymentTransactionData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

	public function addPayment(){
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->load->view($this->formPage,$this->data);
	}

	public function getPartyRefNo(){
		$party_id = $this->input->post('party_id');
		$invData = $this->paymentTrans->getPartyRefNo($party_id);
		
		$html = '<option value="0">Advance</option>';
		foreach($invData as $row):
			$html .= '<option value="'.$row->id.'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';
		endforeach;
		$this->printJson(['status'=>1,'options'=>$html]);
	}

	public function save(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['party_id']))
		 	$errorMessage['party_id'] = "Party name is required";
		if(empty($data['tran_mode']))
			$errorMessage['tran_mode'] = "Mode is required";
		if($data['tran_mode'] == "Cheque"){
			if(empty($data['ch_date']))
				$errorMessage['ch_date'] = "Check Date is required";
			if(empty($data['ch_no']))
				$errorMessage['ch_no'] = "Check No. is required";
		}

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			unset($data['invoiceSelect']);
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->paymentTrans->save($data));
		endif;
	}

	public function edit(){
		$id = $this->input->post('id');
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->data['dataRow'] = $this->paymentTrans->getPayment($id);
		$this->data['invData'] = $this->paymentTrans->getPartyRefNo($this->data['dataRow']->party_id);
		$this->load->view($this->formPage,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Something went wrong... Please try again.']);
		else:
			$this->printJson($this->paymentTrans->delete($id));
		endif;
	}

	public function getCheckStatus(){
        $p_id = $this->input->post('id');
        $this->data['dataRow'] = $this->paymentTrans->getPayment($p_id);
        $this->data['p_id'] = $p_id;
        $this->load->view($this->check_status,$this->data);
	}

	public function updateCheckStatus(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ch_status']))
		 	$errorMessage['ch_status'] = "Cheque Status is required";
		if(empty($data['ch_clear_date']))
			$errorMessage['ch_clear_date'] = "Clear Date is required";
		if(empty($data['ch_reason']))
			$errorMessage['ch_reason'] = "Remark is required";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->paymentTrans->save($data));
		endif;
	}
}
?>