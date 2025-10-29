<?php
class TransactionMainModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function nextTransNo($entry_type){
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['entry_type'] = $entry_type;
		$data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
        $data['tableName'] = $this->transMain;
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }
	
    public function getTransPrefix($entry_type){
		$prefix = 'TRANS/';
        switch($entry_type)
		{
			case 1: $prefix = 'SE/';break;
			case 2: $prefix = 'SQ/';break;
			case 3: $prefix = 'SQR/';break;
			case 4: $prefix = 'SO/';break;
			case 5: $prefix = 'SCH/';break;
			case 6: $prefix = 'GT/';break;
			case 7: $prefix = 'JW/';break;
			case 8: $prefix = 'EXP/';break;
			case 9: $prefix = 'PINV/';break;
			case 10: $prefix = 'COMINV/';break;
			case 11: $prefix = 'CUMINV/';break;
			case 13: $prefix = 'CRN/';break;
			case 14: $prefix = 'DRN/';break;
			case 15: $prefix = 'RV/';break;
			case 16: $prefix = 'PV/';break;
			case 17: $prefix = 'JV/';break;
			case 18: $prefix = 'GEXP/';break;
			case 19: $prefix = 'COMP/';break;
			case 20: $prefix = 'CUMP/';break;
			case 21: $prefix = 'AS/';break;
			case 22: $prefix = 'EL/';break;
			default : $prefix = 'TRANS/';break;
		}
		return $prefix.$this->shortYear.'/';
    }

	public function ledgerEffects($transMainData,$expenseData = array()){
		try{
			$this->deleteLedgerTrans($transMainData['id']);
			$this->deleteExpenseTrans($transMainData['id']);
			
			$partyData = $this->party->getParty($transMainData['opp_acc_id']);
			if(!empty($partyData)):
				$transLedgerData['currency'] = (!empty($partyData->currency))?$partyData->currency:"INR";
				$transLedgerData['inrrate'] = (!empty($partyData->inrrate) && $partyData->inrrate > 0)?$partyData->inrrate:1;
			endif;

			if(in_array($transMainData['entry_type'],[15,16])):
				$cord = getCrDrEff($transMainData['entry_type']);
				//Save Party Account Detail
				$transLedgerData = ['id'=>"",'entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['opp_type'],'remark'=>$transMainData['remark'],'trans_mode'=>$transMainData['trans_mode'],'created_by'=>$transMainData['created_by']];
				$this->storeTransLedger($transLedgerData);

				//Save BankCash Account Detail
				$transLedgerData['vou_acc_id'] = $transMainData['vou_acc_id'];
				$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
				$transLedgerData['c_or_d'] = $cord['vou_type'];
				$this->storeTransLedger($transLedgerData);
			endif;

			if(in_array($transMainData['entry_type'],[12,6,7,8,10,11,13,14,18])):
				if($transMainData['ledger_eff'] == 1):
					if(!empty($expenseData)):
						$expenseData['id'] = "";
						$expenseData['trans_main_id'] = $transMainData['id'];
						$this->store('trans_expense',$expenseData);
					endif;

					$cord = getCrDrEff($transMainData['entry_type']);
					//Save Party Account Detail
					$transLedgerData = ['id'=>'','entry_type'=>$transMainData['entry_type'],'trans_main_id'=>$transMainData['id'],'trans_date'=>$transMainData['trans_date'],'trans_number'=>$transMainData['trans_number'],'doc_date'=>$transMainData['doc_date'],'doc_no'=>$transMainData['doc_no'],'vou_acc_id'=>$transMainData['opp_acc_id'],'opp_acc_id'=>$transMainData['vou_acc_id'],'amount'=>$transMainData['net_amount'],'c_or_d'=>$cord['vou_type'],'remark'=>$transMainData['remark'],'created_by'=>$transMainData['created_by']];
					$this->storeTransLedger($transLedgerData);

					//Save Sale/Purc Account Detail
					if($transMainData['entry_type'] != 18):
						if(!isset($transMainData['sp_acc_id'])):
							$accType = getSystemCode($transMainData['entry_type'],true,$transMainData['gst_type']);
							if(!empty($accType)):
								$spAcc = $this->ledger->getLedgerOnSystemCode($accType);
								$transMainData['sp_acc_id'] = (!empty($spAcc))?$spAcc->id:0;
							else:
								$transMainData['sp_acc_id'] = 0;
							endif;
						endif;
						$transLedgerData['vou_acc_id'] = $transMainData['sp_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['taxable_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					else:
						$gstExpenseTrans = $this->gstExpense->gstExpenseTransaction($transMainData['id']);
						foreach($gstExpenseTrans as $row):
							$transLedgerData['vou_acc_id'] = $row->item_id;
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $row->taxable_amount;
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endforeach;
					endif;

					//Save Tax Account Detail
					if($transMainData['gst_type'] == 2):
						if($transMainData['igst_amount'] > 0):
							$transLedgerData['vou_acc_id'] = $transMainData['igst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['igst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endif;
					else:
						if($transMainData['cgst_amount'] > 0 && $transMainData['sgst_amount'] > 0):
							$transLedgerData['vou_acc_id'] = $transMainData['cgst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['cgst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);

							$transLedgerData['vou_acc_id'] = $transMainData['sgst_acc_id'];
							$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
							$transLedgerData['amount'] = $transMainData['sgst_amount'];
							$transLedgerData['c_or_d'] = $cord['opp_type'];
							$this->storeTransLedger($transLedgerData);
						endif;
					endif;


					if($transMainData['cess_amount'] > 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['cess_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['cess_qty_amount'] > 0):
						$transLedgerData['vou_acc_id'] = $transMainData['cess_qty_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['cess_qty_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['tcs_amount'] > 0):
						$transLedgerData['vou_acc_id'] = $transMainData['tcs_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['tcs_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					if($transMainData['tds_amount'] > 0):
						$transLedgerData['vou_acc_id'] = $transMainData['tds_acc_id'];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = $transMainData['tds_amount'];
						$transLedgerData['c_or_d'] = $cord['opp_type'];
						$this->storeTransLedger($transLedgerData);
					endif;

					//Save Expense Account Detail
					$expType = (in_array($transMainData['entry_type'],[12,14]))?1:2;
					$expenseMaster = $this->expenseMaster->getActiveExpenseList($expType);
					foreach($expenseMaster as $row):
						if(isset($expenseData[$row->map_code."_acc_id"]) && isset($expenseData[$row->map_code.'_amount'])):
							if($expenseData[$row->map_code.'_amount'] > 0 && $row->map_code != "roff"): 
								$transLedgerData['vou_acc_id'] = $expenseData[$row->map_code."_acc_id"];
								$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
								$transLedgerData['amount'] = $expenseData[$row->map_code.'_amount'];
								$transLedgerData['c_or_d'] = (in_array($transMainData['entry_type'],[12,13]))?(($row->add_or_deduct == 1)?"DR":"CR"):(($row->add_or_deduct == 1)?"CR":"DR");
								$this->storeTransLedger($transLedgerData);
							endif;
						endif;
					endforeach;

					//Save Round off Account Detail 
					if($transMainData['round_off_amount'] != 0): 
						$transLedgerData['vou_acc_id'] = $transMainData["round_off_acc_id"];
						$transLedgerData['opp_acc_id'] = $transMainData['opp_acc_id'];
						$transLedgerData['amount'] = abs($transMainData['round_off_amount']);
						$transLedgerData['c_or_d'] = (in_array($transMainData['entry_type'],[12,13]))?(($transMainData['round_off_amount'] > 0)?"DR":"CR"):(($transMainData['round_off_amount'] > 0)?"CR":"DR");
						$this->storeTransLedger($transLedgerData);
					endif;
				endif;
			endif;

			return true;
		}catch(\Exception $e){
			return false;
        }		
	}

	public function storeTransLedger($data){
		try{
			$data['p_or_m'] = ($data['c_or_d'] == "DR")?-1:1;
			$data['vou_name_s'] = getVoucherNameShort($data['entry_type']);
			$data['vou_name_l'] = getVoucherNameLong($data['entry_type']);
			$this->store("trans_ledger",$data);
			$this->updateAccountBalance($data['vou_acc_id'],( $data['p_or_m'] * $data['amount'] ));
			return true;
		}catch(\Exception $e){
			return false;
        }			
	}

	public function updateAccountBalance($acc_id,$amount){
		try{
			$setData = Array();
			$setData['tableName'] = "party_master";
			$setData['where']['id'] = $acc_id;
			$setData['set']['cl_balance'] = 'cl_balance, + '.$amount;
			$this->setValue($setData);
			return true;
		}catch(\Exception $e){
			return false;
        }
	}

	public function deleteLedgerTrans($trans_main_id){
		try{
			$queryData = array();
			$queryData['tableName'] = "trans_ledger";
			$queryData['where']['trans_main_id'] = $trans_main_id;
			$transLedgerData = $this->rows($queryData);

			if(!empty($transLedgerData)):
				foreach($transLedgerData as $row):
					$amount = $row->amount * $row->p_or_m * -1;
					$this->updateAccountBalance($row->vou_acc_id,$amount);				
				endforeach;
				$this->remove("trans_ledger",['trans_main_id'=>$trans_main_id]);
			endif;
			return true;
		}catch(\Exception $e){
			return false;
        }
	}

	public function deleteExpenseTrans($trans_main_id){
		try{
			$this->trash('trans_expense',['trans_main_id'=>$trans_main_id]);
			return true;
		}catch(\Exception $e){
			return false;
		}
	}
}
	
?>