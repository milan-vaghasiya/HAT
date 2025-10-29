<?php 
class PaymentVoucherModel extends MasterModel{
	private $transMain = "trans_main";

	public function getDtRows($data,$type=null){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.*,vacc.party_name as vou_acc_name,opacc.party_name as opp_acc_name";
		$data['leftJoin']['party_master opacc'] = 'opacc.id = trans_main.opp_acc_id';
		$data['leftJoin']['party_master vacc'] = 'vacc.id = trans_main.vou_acc_id';
		$data['where']['trans_main.trans_date >= '] = $this->startYearDate;
        $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
		$data['where_in']['entry_type'] = "15,16";
		
		if($data['status'] == 0):
			$data['customWhere'][] = '(trans_main.ref_id = "" OR (trans_main.ref_id != "" AND trans_main.net_amount = trans_main.paid_amount))';
		else:
			$data['customWhere'][] = 'trans_main.ref_id != "" AND trans_main.net_amount != paid_amount';
		endif;
		
		$data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "opacc.party_name";
        $data['searchCol'][] = "trans_main.net_amount";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.doc_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.remark";
		
		$columns = array('','','trans_number','trans_date','','opacc.party_name','trans_main.net_amount','trans_main.doc_no','trans_main.doc_date','trans_main.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
	}

    public function save($data){
		try{
			$this->db->trans_begin();
			
			/* if(!empty($data['ref_id'])):
			    $invData = $this->getVoucher($data['ref_id']);
			    
			    if($data['net_amount'] > $invData->net_amount):
			        return ['status'=>0,'message'=>['net_amount'=>"Invalid Amount"]];    
			    endif;
			endif; */
			
			if(empty($data['id'])):
    			// Get Latest Trans Number
    			$data['trans_prefix'] = $this->transModel->getTransPrefix($data['entry_type']);
                $data['trans_no'] = $this->transModel->nextTransNo($data['entry_type']);
            endif;
            
            if(!empty($data['id'])):
                $voucherData = $this->getVoucher($data['id']);
                
                if(!empty($voucherData->ref_id)):
					$oldRef = json_decode($voucherData->extra_fields);
					foreach($oldRef as $row):
						$setData = array();
						$setData['tableName'] = $this->transMain;
						$setData['where']['id'] = $row->trans_main_id;
						$setData['set']['paid_amount'] = 'paid_amount, - '.$row->ad_amount;
						$this->setValue($setData);
					endforeach;
					$this->store($this->transMain,['id'=>$data['id'],'extra_fields'=>"",'paid_amount'=>0]);
                endif;
            endif;
            
            if(!empty($data['ref_id'])):
				$refIds = explode(",",$data['ref_id']);
				$refData = array();$totalAmount = $data['net_amount'];
				foreach($refIds as $ref_id):
					$queryData = array();
					$queryData['tableName'] = $this->transMain;
					$queryData['select'] = "(net_amount - paid_amount) as due_amount";
					$queryData['where']['id'] = $ref_id;
					$invData = $this->row($queryData);

					$totalAmount -= $invData->due_amount;
					$adAmount = 0;
					if($totalAmount > 0):
						$adAmount = $invData->due_amount;
					elseif($totalAmount < 0):
						$adAmount = $invData->due_amount + $totalAmount;
					else:
						$adAmount = $invData->due_amount;
					endif;

					$setData = array();
					$setData['tableName'] = $this->transMain;
					$setData['where']['id'] = $ref_id;
					$setData['set']['paid_amount'] = 'paid_amount, + '.$adAmount;
					$this->setValue($setData); 

					$refData[] = ['trans_main_id'=>$ref_id,'ad_amount'=>$adAmount];

					if($totalAmount < 0):break;endif;
				endforeach; 
				 
				$data['extra_fields'] = json_encode($refData);
				$data['ref_id'] = implode(",",array_column($refData,'trans_main_id'));
				$data['paid_amount'] = array_sum(array_column($refData,'ad_amount'));
            endif;			
            
            $paymentId = $data['id'];
			$data['trans_number'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
			$data['doc_date'] = (!empty($data['doc_date']))?$data['doc_date']:null;

			$result = $this->store($this->transMain,$data,'Voucher');
			$data['id'] = (empty($data['id']))?$result['insert_id']:$data['id'];	

			$this->transModel->ledgerEffects($data);
			
			/* Send Notification */
			$pvNo = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
			$notifyData['notificationTitle'] = (empty($paymentId))?"New Payment Voucher":"Update Payment Voucher";
			$notifyData['notificationMsg'] = (empty($paymentId))?"New Payment Voucher Generated. Vou. No. : ".$pvNo:"Payment Voucher updated. Vou No. : ".$pvNo;
			$notifyData['payload'] = ['callBack' => base_url('paymentVoucher')];
			$notifyData['controller'] = "'paymentVoucher'";
			$notifyData['action'] = (empty($paymentId))?"W":"M";
			$this->notify($notifyData);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getVoucher($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->transMain;
        return $this->row($data);
    }  

	public function delete($id){
		try{
			$this->db->trans_begin();
			$voucherData = $this->getVoucher($id);
			
			if(!empty($voucherData->ref_id)):
				$oldRef = json_decode($voucherData->extra_fields);
				foreach($oldRef as $row):
					$setData = array();
					$setData['tableName'] = $this->transMain;
					$setData['where']['id'] = $row->trans_main_id;
					$setData['set']['paid_amount'] = 'paid_amount, - '.$row->ad_amount;
					$this->setValue($setData);
				endforeach;
			endif;

			$result= $this->trash($this->transMain,['id'=>$id],'PaymentVoucher');
			$this->transModel->deleteLedgerTrans($id);
			
			/* Send Notification */
			$pvNo = getPrefixNumber($voucherData->trans_prefix,$voucherData->trans_no);
			$notifyData['notificationTitle'] = "Delete Payment Voucher";
			$notifyData['notificationMsg'] = "Payment Voucher deleted. Vou No. : ".$pvNo;
			$notifyData['payload'] = ['callBack' => base_url('paymentVoucher')];
			$notifyData['controller'] = "'paymentVoucher'";
			$notifyData['action'] = "D";
			$this->notify($notifyData);

			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

	public function getRefInvoiceList($data){
		$queryData = array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "id,trans_no,trans_prefix,trans_number,net_amount,paid_amount,(net_amount - paid_amount) as due_amount";
		if($data['entry_type'] == 15):
			$queryData['where_in']['entry_type'] = [6,7,8,14];
		else:
			$queryData['where_in']['entry_type'] = [12,13,18];
		endif;
		$queryData['where']['party_id'] = $data['party_id'];
		if(empty($data['ref_id'])):
			$queryData['where']['(net_amount - paid_amount) >'] = 0;
		else:
			$queryData['customWhere'][] = "((net_amount - paid_amount) > 0 OR id IN (".$data['ref_id']."))";
		endif;
		return $this->rows($queryData);
	}
	
	/*public function migrateReceiptNo(){
	    try{
			$this->db->trans_begin();
			
			$queryData['tableName'] = $this->transMain;
    		$queryData['select'] = "trans_main.*";
    		//$queryData['where_in']['entry_type'] = "15,16";
    		$queryData['where']['entry_type'] = 16;
    		$queryData['order_by']['id'] = "ASC";
    		$result = $this->rows($queryData);
    		
    		$i=1;
    		foreach($result as $row):
    		    $data = array();
    		    $data['id'] = $row->id;
    		    $data['trans_prefix'] = $this->transModel->getTransPrefix($row->entry_type);
                $data['trans_no'] = $i;
                //print_r($data);
                $this->store($this->transMain,$data);
                $i++;
    		endforeach;
			
			$result = "Receipt no migration success.";
			if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
		}catch(\Exception $e){
            $this->db->trans_rollback();
			return "somthing is wrong. Error : ".$e->getMessage();
        }
	}*/
}
?>