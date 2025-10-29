<?php
class PaymentVoucher extends MY_Controller
{
    private $indexPage = "payment_voucher/index";
    private $formPage = "payment_voucher/form";
	private $paymentMode=['CASH','CHEQUE','IB','CARD','UPI'];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "PaymentVoucher";
		$this->data['headData']->controller = "paymentVoucher";
        $this->data['headData']->pageUrl = "paymentVoucher";
	}

	public function index(){
		$this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage,$this->data);
	}

	public function getDtRows($status=0){
		$data = $this->input->post();$data['status'] = $status;
		$result = $this->paymentVoucher->getDtRows($data); 
		$sendData = array(); $i=1;
		foreach($result['data'] as $row):
			$row->sr_no = $i++; $row->invNo="";

			/*$opp_party=$this->party->getParty($row->opp_acc_id);
			$opp_acc_name=(!empty($opp_party->party_name)?$opp_party->party_name:"");
			$row->opp_acc_name=$opp_acc_name;

			$vou_party=$this->party->getParty($row->vou_acc_id);
			$vou_acc_id=(!empty($vou_party->party_name)?$vou_party->party_name:"");
			$row->vou_acc_name=$vou_acc_id;*/

			$sendData[] = getPaymentVoucher($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

    public function addPaymentVoucher(){
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
		$this->data['paymentMode'] = $this->paymentMode;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(15);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(15);		
		$this->load->view($this->formPage,$this->data);
	}

	public function getTransNo(){
		$data = $this->input->post();
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix($data['entry_type']);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo($data['entry_type']);
		$this->printJson(['status'=>1,'trans'=>$this->data]);
	}

	public function getReference(){
		$data=$this->input->post();
		$referenceData= array();		
		$referenceData = $this->paymentVoucher->getRefInvoiceList($data);		
		
		$optionsHtml='';
		foreach($referenceData as $row):
			$optionsHtml.='<option value="'.$row->id.'" data-due_amount="'.round($row->due_amount,2).'">'.getPrefixNumber($row->trans_prefix,$row->trans_no).' <br>[ Due Amt. : '.round($row->due_amount,2).' ]</option>';
		endforeach;
		
		$this->printJson(['status'=>1,'referenceData'=>$optionsHtml]);
	}

	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
		if(empty($data['trans_date']))
			$errorMessage['trans_date'] = "Voucher Date is required.";
		if(empty($data['entry_type']))
			$errorMessage['entry_type'] = "Entry Type is required.";
		if(empty($data['opp_acc_id']))
			$errorMessage['opp_acc_id'] = "Party Name is required.";
		if(empty($data['vou_acc_id']))
			$errorMessage['vou_acc_id'] = "Ledger Name is required.";
		if(empty($data['trans_mode']))
			$errorMessage['trans_mode'] = "Payment Mode is required.";
		if(empty($data['net_amount']))
			$errorMessage['net_amount'] = "Amount is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$data['party_id'] = $data['opp_acc_id'];
			$data['created_by'] = $this->session->userdata('loginId');
			$this->printJson($this->paymentVoucher->save($data));
		endif;
	}

	public function edit(){
        $data = $this->input->post();

		$voucherData = $this->paymentVoucher->getVoucher($data['id']);
        $this->data['dataRow'] = $voucherData;
		$this->data['partyData'] = $this->paymentTrans->getPartyList();
		$this->data['ledgerData'] = $this->party->getPartyListOnGroupCode(['"BA"','"CS"']);
		$this->data['paymentMode'] =$this->paymentMode;
		
		$options=array();
		$optionsHtml='';
		$data = ['party_id'=>$voucherData->party_id,'entry_type'=>$voucherData->entry_type,'ref_id'=>$voucherData->ref_id];
		$options = $this->paymentVoucher->getRefInvoiceList($data);

		$refIds = (!empty($voucherData->ref_id))?explode(",",$voucherData->ref_id):array();
		$refData = (!empty($voucherData->extra_fields))?json_decode($voucherData->extra_fields):array();
		$ref_amount = 0;
		foreach($options as $row):
			$selected = (!empty($refIds) && in_array($row->id,$refIds)) ? "selected":"";

			if(!empty($refIds) && in_array($row->id,$refIds)):				
				$refDataKey = array_search($row->id,array_column($refData,'trans_main_id'));
				$row->due_amount = $row->due_amount + $refData[$refDataKey]->ad_amount;
				$ref_amount += $row->due_amount;
			endif;

			$optionsHtml.='<option value="'.$row->id.'" data-due_amount="'.round($row->due_amount,2).'" '.$selected.'>'.getPrefixNumber($row->trans_prefix,$row->trans_no).' <br>[ Due Amt. : '.round($row->due_amount,2).' ]</option>';
		endforeach;	
		$this->data['optionsHtml']=$optionsHtml;
		$this->data['ref_amount'] = $ref_amount;

        $this->load->view($this->formPage,$this->data);
    }
    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->paymentVoucher->delete($id));
        endif;
    }
    
    /*public function migrateReceiptNo(){
        $result = $this->paymentVoucher->migrateReceiptNo();
        echo $result;exit;
    }*/

}
?>