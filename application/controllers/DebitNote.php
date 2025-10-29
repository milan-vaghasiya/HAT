<?php
defined('BASEPATH') or exit('No direct script access allowed');
class DebitNote extends MY_Controller
{
	private $indexPage = "debit_note/index";
	private $invoiceForm = "debit_note/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Debit Note";
		$this->data['headData']->controller = "debitNote";
	}

	public function index(){
		$this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
		$this->load->view($this->indexPage, $this->data);
	}

	public function getDTRows(){
		$columns = array('', '', 'trans_main.inv_no', 'trans_main.inv_date', 'trans_main.party_name', 'trans_main.net_amount');
		$result = $this->debitNote->getDTRows($this->input->post(), $columns);
		$sendData = array();
		$i = 1;
		foreach ($result['data'] as $row) :
			$row->sr_no = $i++;
			$row->controller = "debitNote";
			$sendData[] = getDebitNoteData($row);
		endforeach;
		$result['data'] = $sendData;
		$this->printJson($result);
	}

	public function addDebitNote(){
		$this->data['ref_id'] = '';
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(14);
		$this->data['nextTransNo'] = $this->transModel->nextTransNo(14);
		$this->data['itemData'] = $this->item->getItemList(0);
		$this->data['unitData'] = $this->item->itemUnits();
		$this->data['partyData'] = $this->party->getPartyList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(1);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'PA'"]);
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'", "'ED'", "'EI'", "'ID'", "'II'"]);
		$this->data['terms'] = $this->terms->getTermsListByType('Purchase');
		$this->load->view($this->invoiceForm, $this->data);
	}

	public function getPartyOrders(){
		$this->printJson($this->debitNote->getPartyOrders($this->input->post('party_id')));
	}

	public function getItemData(){
		$id = $this->input->post('itemId');
		$result = $this->item->getItem($id);
		$result->unit_name = $this->item->itemUnit($result->unit_id)->unit_name;
		$this->printJson($result);
	}

	public function saveDebitNote(){
		$data = $this->input->post();
		$errorMessage = array();
		$data['currency'] = '';
		$data['inrrate'] = 0;
		if (empty($data['party_id'])) :
			$errorMessage['party_id'] = "Party Name is required.";
		else :
			$partyData = $this->party->getParty($data['party_id']);
			if (floatval($partyData->inrrate) <= 0) :
				$errorMessage['party_id'] = "Currency not set.";
			else :
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;
		endif;
		if (empty($data['sp_acc_id']))
			$errorMessage['sp_acc_id'] = "Purchase A/c. is required.";
		if (empty($data['trans_date']))
			$errorMessage['trans_date'] = 'Date is required.';
		if (empty($data['item_id'][0]))
			$errorMessage['item_id'] = 'Item Name is required.';
		/* if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required."; */

		if (!empty($errorMessage)) :
			$this->printJson(['status' => 0, 'message' => $errorMessage]);
		else :
			$data['terms_conditions'] = "";
			$termsArray = array();
			if (isset($data['term_id']) && !empty($data['term_id'])) :
				foreach ($data['term_id'] as $key => $value) :
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;

			$gstAmount = 0;
			if ($data['gst_type'] == 1) :
				if (isset($data['cgst_amount'])) :
					$gstAmount = $data['cgst_amount'] + $data['sgst_amount'];
				endif;
			elseif ($data['gst_type'] == 2) :
				if (isset($data['igst_amount'])) :
					$gstAmount = $data['igst_amount'];
				endif;
			endif;

			$masterData = [
				'id' => $data['id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['trans_no'],
				'trans_prefix' => $data['trans_prefix'],
				'trans_number' => getPrefixNumber($data['trans_prefix'], $data['trans_no']),
				'trans_date' => date('Y-m-d', strtotime($data['trans_date'])),
				'credit_period' => $data['credit_period'],
				'party_id' => $data['party_id'],
				'opp_acc_id' => $data['party_id'],
				'sp_acc_id' => $data['sp_acc_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gstin' => $data['gstin'],
				'gst_applicable' => $data['gst_applicable'],
				'gst_type' => $data['gst_type'],
				'doc_no' => $data['doc_no'],
				'doc_date' => date('Y-m-d', strtotime($data['trans_date'])),
				'challan_no' => $data['challan_no'],
				'total_amount' => array_sum($data['amount']) + array_sum($data['disc_amt']),
				'taxable_amount' => $data['taxable_amount'],
				'gst_amount' => $gstAmount,
				'igst_acc_id' => (isset($data['igst_acc_id'])) ? $data['igst_acc_id'] : 0,
				'igst_per' => (isset($data['igst_per'])) ? $data['igst_per'] : 0,
				'igst_amount' => (isset($data['igst_amount'])) ? $data['igst_amount'] : 0,
				'sgst_acc_id' => (isset($data['sgst_acc_id'])) ? $data['sgst_acc_id'] : 0,
				'sgst_per' => (isset($data['sgst_per'])) ? $data['sgst_per'] : 0,
				'sgst_amount' => (isset($data['sgst_amount'])) ? $data['sgst_amount'] : 0,
				'cgst_acc_id' => (isset($data['cgst_acc_id'])) ? $data['cgst_acc_id'] : 0,
				'cgst_per' => (isset($data['cgst_per'])) ? $data['cgst_per'] : 0,
				'cgst_amount' => (isset($data['cgst_amount'])) ? $data['cgst_amount'] : 0,
				'cess_acc_id' => (isset($data['cess_acc_id'])) ? $data['cess_acc_id'] : 0,
				'cess_per' => (isset($data['cess_per'])) ? $data['cess_per'] : 0,
				'cess_amount' => (isset($data['cess_amount'])) ? $data['cess_amount'] : 0,
				'cess_qty_acc_id' => (isset($data['cess_qty_acc_id'])) ? $data['cess_qty_acc_id'] : 0,
				'cess_qty' => (isset($data['cess_qty'])) ? $data['cess_qty'] : 0,
				'cess_qty_amount' => (isset($data['cess_qty_amount'])) ? $data['cess_qty_amount'] : 0,
				'tcs_acc_id' => (isset($data['tcs_acc_id'])) ? $data['tcs_acc_id'] : 0,
				'tcs_per' => (isset($data['tcs_per'])) ? $data['tcs_per'] : 0,
				'tcs_amount' => (isset($data['tcs_amount'])) ? $data['tcs_amount'] : 0,
				'tds_acc_id' => (isset($data['tds_acc_id'])) ? $data['tds_acc_id'] : 0,
				'tds_per' => (isset($data['tds_per'])) ? $data['tds_per'] : 0,
				'tds_amount' => (isset($data['tds_amount'])) ? $data['tds_amount'] : 0,
				'disc_amount' => array_sum($data['disc_amt']),
				'apply_round' => $data['apply_round'],
				'round_off_acc_id'  => (isset($data['roff_acc_id'])) ? $data['roff_acc_id'] : 0,
				'round_off_amount' => (isset($data['roff_amount'])) ? $data['roff_amount'] : 0,
				'net_amount' => $data['net_inv_amount'],
				'terms_conditions' => $data['terms_conditions'],
				'remark' => $data['remark'],
				'currency' => $data['currency'],
				'inrrate' => $data['inrrate'],
				'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
				'ledger_eff' => 1,
				'created_by' => $this->session->userdata('loginId')
			];

			$transExp = getExpArrayMap($data);
			$expAmount = $transExp['exp_amount'];
			$expenseData = array();
			if ($expAmount > 0) :
				unset($transExp['exp_amount']);
				$expenseData = $transExp;
			endif;

			$accType = getSystemCode($data['entry_type'], false);
			if (!empty($accType)) :
				$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
				$masterData['vou_acc_id'] = (!empty($spAcc)) ? $spAcc->id : 0;
			else :
				$masterData['vou_acc_id'] = 0;
			endif;

			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'location_id' => $data['location_id'],
				'batch_no' => $data['batch_no'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'amount' => $data['amount'],
				'taxable_amount' => $data['amount'],
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amt'],
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount'],
				'stock_eff' => $data['stock_eff'],
			];

			$this->printJson($this->debitNote->save($masterData, $itemData, $expenseData));
		endif;
	}

	public function edit($id)
	{
		$this->data['ref_id'] = '';
		$this->data['invoiceData'] = $this->debitNote->getDebitNote($id);
		$this->data['itemData'] = $this->item->getItemList(0);
		$this->data['unitData'] = $this->item->itemUnits();
		$this->data['partyData'] = $this->party->getPartyList();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Purchase');
		$this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(1);
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(1);
		$this->data['spAccounts'] = $this->ledger->getLedgerList(["'PA'"]);
		$this->data['ledgerList'] = $this->ledger->getLedgerList(["'DT'", "'ED'", "'EI'", "'ID'", "'II'"]);
		$this->load->view($this->invoiceForm, $this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if (empty($id)) :
			$this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
		else :
			$this->printJson($this->debitNote->delete($id));
		endif;
	}

	public function getItemList(){
		$this->printJson($this->purchaseInvoice->getItemList($this->input->post('id')));
	}

	public function getPurchaseInvoiceList(){
		$data = $this->input->post();
		$purInvData = $this->purchaseInvoice->getPurchaseInvoiceList($data['party_id']);
		$options = "";
		foreach ($purInvData as $row) {
			$selected = (!empty($data['doc_no']) && (in_array($row->doc_no, explode(',', $data['doc_no'])))) ? "selected" : "";
			$options .= '<option value="' . $row->id . '" '.$selected.'>' . $row->doc_no . '</option>';
		}
		$this->printJson(['status' => 1, 'options' => $options]);
	}

	public function getInvoiceItem(){
		$data = $this->input->post();
		$itemData = $this->item->getItemList(0);

		$itemList = array();
		if ($data['invoice_ids']) :
			$itemList = $this->debitNote->getInvoiceItemsForDebitNote($data['invoice_ids']);
		endif;

		$itemOptions = "<option value=''>Select Item</option>";
		if (!empty($itemList)) :
			foreach ($itemData as $row) :
				if (in_array($row->id, array_column($itemList, 'item_id'))) :
					$key = array_search($row->id, array_column($itemList, 'item_id'));
					$row->price = $itemList[$key]->avg_price;
					$row->hsn_code = $itemList[$key]->hsn_code;
					$row->gst_per = $itemList[$key]->gst_per;
					$row->unit_id = $itemList[$key]->unit_id;
					$row->unit_name = $itemList[$key]->unit_name;
					$itemOptions .= "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->item_name . "</option>";
				endif;
			endforeach;
		else :
			foreach ($itemData as $row) :
				$itemOptions .= "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->item_name . "</option>";			
			endforeach;
		endif;
		$this->printJson(['status' => 1, 'itemOptions' => $itemOptions]);
	}

	public function getItemLocation(){
		$item_id = $this->input->post('item_id');
		$location_id = $this->input->post('location_id');
		$storeData = $this->debitNote->getItemLocation($item_id);
		$options = "<option value='' data-store_name=''>Select Store Location</option>";
		$locationOptions = '';
		foreach($storeData as $row):
			$locationOptions = '';
			foreach($row->location as $lcData):
				if(!empty($location_id) && $location_id == $lcData->id || $lcData->stock_qty > 0):
					$locationOptions .= '<option value="'.$lcData->id.'" data-store_name="'.$row->store_name.'">'.$lcData->location.' </option>';
				endif;
			endforeach;
			if(!empty($locationOptions)):
				$options .= '<optgroup label="'.$row->store_name.'">';
				$options .= $locationOptions;
				$options .= '</optgroup>';
			endif;
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
	}

	public function getItemLocationWiseBatch(){
		$item_id = $this->input->post('item_id');
		$location_id = $this->input->post('location_id');
		$batch_no = $this->input->post('batch_no');
		$batchData = $this->debitNote->getItemLocationWiseBatch($item_id,$location_id);

		$options = '<option value="" data-batch_qty="0">Select Batch No.</option>';
		foreach($batchData as $row):
			if(!empty($batch_no) && $batch_no == $row->batch_no || $row->batch_qty > 0 ):
				$options .= '<option value="'.$row->batch_no.'" data-stock_qty="'.$row->batch_qty.'">'.$row->batch_no.'</option>';
			endif;
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
	}
}
