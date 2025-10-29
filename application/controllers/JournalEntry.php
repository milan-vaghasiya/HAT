<?php
defined('BASEPATH') or exit('No direct script access allowed');
class JournalEntry extends MY_Controller
{
    private $indexPage = "journal_entry/index";
    private $form = "journal_entry/form";
    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Journal Entry";
        $this->data['headData']->controller = "journalEntry";
    }

    public function index()
    {
        $this->data['tableHeader'] = getAccountDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows(){
        $result = $this->journalEntry->getDTRows($this->input->post());
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $row->controller = "journalEntry";
            $sendData[] = getJournalEntryData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addJournalEntry()
    {
        $this->data['ref_id'] = '';
        $this->data['trans_prefix'] = $this->transModel->getTransPrefix(17);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(17);
        $this->data['partyData'] = $this->party->getPartyList();
        $this->load->view($this->form, $this->data);
    }

    public function saveJournalEntry()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = 'Entry is required.';
        
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $firstCrKey = array_search("CR",$data['cr_dr'],true);
            $firstDrKey = array_search("DR",$data['cr_dr'],true);

            
            $trans_no = (empty($data['id']))?$this->transModel->nextTransNo(17):$data['trans_no'];
            $trans_prefix = (empty($data['id']))?$this->transModel->getTransPrefix(17):$data['trans_prefix'];

            $masterData = [
                'id' => $data['id'],
                'entry_type' => $data['entry_type'],
                'trans_no' => $trans_no,
                'trans_prefix' => $trans_prefix,
                'trans_number' => getPrefixNumber($trans_prefix,$trans_no),
                'trans_date' => $data['trans_date'],
                'vou_acc_id' => $data['item_id'][$firstDrKey],
                'opp_acc_id' => $data['item_id'][$firstCrKey],
                'party_id' => $data['item_id'][$firstCrKey],
                'total_amount' => $data['debit_amount'][$firstDrKey],	
				'taxable_amount' => $data['debit_amount'][$firstDrKey],
                'net_amount' => $data['debit_amount'][$firstDrKey],
                'vou_name_s' => getVoucherNameShort($data['entry_type']),
				'vou_name_l' => getVoucherNameLong($data['entry_type']),
                'ledger_eff' => 1,
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'acc_id' => $data['item_id'],
                'acc_name' => $data['item_name'],   
                'credit_amount' => $data['credit_amount'],
                'debit_amount' => $data['debit_amount'],
                'cr_dr' => $data['cr_dr'],
                'item_remark' => $data['item_remark']
            ];

            $this->printJson($this->journalEntry->save($masterData,$itemData));
        endif;
    }

    public function edit($id){
        $this->data['dataRow'] = $this->journalEntry->editJournal($id);
        $this->data['partyData'] = $this->party->getPartyList();
        $this->load->view($this->form, $this->data);
    }

    public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
		else:
			$this->printJson($this->journalEntry->delete($id));
		endif;
	}
}
