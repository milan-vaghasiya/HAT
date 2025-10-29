<?php
class SalesQuotationModel extends MasterModel{
    private $salesEnquiryMaster = "sales_enquiry";
    private $salesEnquiryTrans = "sales_enquiry_transaction";
    private $salesQuotation = "sales_quotation";
    private $salesQuotationTrans = "sales_quote_transaction";
    private $itemMaster = "item_master";
    private $partyMaster = "party_master";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty, trans_child.price, trans_child.org_price, trans_child.cod_date,trans_child.confirm_by,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by, trans_main.quote_rev_no, trans_main.from_entry_type, trans_main.ref_id';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 2;
        $data['where']['trans_child.trans_status != '] = 2;
		$data['group_by'][]='trans_child.trans_main_id';

		if($data['status'] == 1) { 
			$data['where']['trans_child.confirm_by != '] = 0; 
			$data['where']['trans_child.cod_date >= '] = $this->startYearDate;
			$data['where']['trans_child.cod_date <= '] = $this->endYearDate;
		} 
        else { $data['where']['trans_child.confirm_by'] = 0; }

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
		$data['searchCol'][] = "CONCAT('/',trans_main.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "DATE_FORMAT(trans_child.cod_date,'%d-%m-%Y')";
        $data['searchCol'][] = "";

		$columns =array('','','trans_main.trans_no','trans_main.trans_date','trans_main.party_name','DATE_FORMAT(trans_child.cod_date,"%d-%m-%Y")','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function itemSearch(){
        $data['tableName'] = 'item_master';
		$data['select'] = 'item_name';
		$data['where']['item_type'] = 1;
		$result = $this->rows($data);
		$searchResult = array();
		foreach($result as $row){$searchResult[] = $row->item_name;}
		return  $searchResult;
    }

    public function save($masterData,$itemData,$is_revision){
		try{
            $this->db->trans_begin();
        
            if ($this->checkDuplicateQuote($masterData['trans_prefix'], $masterData['trans_no'],$masterData['entry_type'], $masterData['id']) > 0) :
                $errorMessage['quote_no'] = "SQ. No. is duplicate.";
                return ['status' => 0, 'message' => $errorMessage];
            else :
                
                $quoteId = $masterData['id'];
        		
        		$custData['party_name'] = $masterData['party_name'];$custData['contact_person'] = $masterData['contact_person'];
        		$custData['party_mobile'] = $masterData['contact_no'];$custData['contact_email'] = $masterData['contact_email'];
        		$custData['party_phone'] = $masterData['party_phone'];$custData['party_email'] = $masterData['party_email'];
        		$custData['party_address'] = $masterData['party_address'];$custData['party_pincode'] = $masterData['party_pincode'];
        		$custData['currency'] = $masterData['lr_no'];
        		if(empty($masterData['party_id'])):
        			$masterData['party_id'] = $this->saveLead($masterData['party_name']);
        		else:
        			$custData['id'] = $masterData['party_id'];
        			$custData['lead_status'] = 3;
        			$customerSave = $this->store('party_master',$custData);
        		endif;
                
                // Update Enquiry
                if ($masterData['from_entry_type'] == 1) {
					$salesEnqData = [
						'id' => $masterData['ref_id'],
						'party_id' => $masterData['party_id'],
						'party_name' => $masterData['party_name'],
					];
					$this->store($this->transMain, $salesEnqData);
				}
				
        		unset($masterData['contact_person'],$masterData['contact_no'],$masterData['contact_email'],$masterData['party_email'],$masterData['party_phone'],$masterData['party_address'],$masterData['party_pincode']);
        		
        		if(empty($quoteId)):	         
                    //Get Latest Trans Number
                    $masterData['trans_prefix'] = $this->transModel->getTransPrefix(2);
                    $masterData['trans_no'] = $this->transModel->nextTransNo(2);
                    
                    //save Sales Quotation Data
        			$saleQuotationSave = $this->store($this->transMain,$masterData);
        			$quoteId = $saleQuotationSave['insert_id'];  
        			
        			//if(!empty($masterData['ref_id'])):
        			//	$this->store($this->transMain,['id'=>$masterData['ref_id'],'trans_status'=>1]);
        			//endif;
        			$is_revision = 1;
        			
        			$result = ['status'=>1,'message'=>'Sales Quotation saved successfully.','url'=>base_url("salesQuotation")];
        		else:
        			$this->store($this->transMain,$masterData);			
        			$data = array();
        			$data['select'] = "id";
        			$data['where']['trans_main_id'] = $quoteId;
        			$data['tableName'] = $this->transChild;
        			$squoteIdArray = $this->rows($data);
        			
        			foreach($squoteIdArray as $key=>$value):
        				if(!in_array($value->id,$itemData['id'])):		
        					$this->trash($this->transChild,['id'=>$value->id]);
        				endif;
        			endforeach;
        			
        			$result = ['status'=>1,'message'=>'Sales Quotation updated successfully.','url'=>base_url("salesQuotation")];		
        			
        		endif;
        
        		foreach($itemData['item_name'] as $key=>$value):
        	
        			$itmQuery['tableName'] = $this->itemMaster;
        			$itmQuery['where']['item_name'] = $value;
        			$itmMaster = $this->row($itmQuery);
        			$item_id = $itemData['item_id'][$key];
        			if(!empty($itmMaster)){$item_id = $itmMaster->id;}
        			$transData = [
        							'id' => $itemData['id'][$key],
        							'entry_type' => $masterData['entry_type'],
                                    'currency' => $masterData['currency'],
                                    'inrrate' => $masterData['inrrate'],
        							'from_entry_type' => $itemData['from_entry_type'][$key],
        							'ref_id' => $itemData['ref_id'][$key],
        							'trans_main_id' => $quoteId,
        							'item_id' => $item_id,
        							'item_name' => $value,
        							'item_type' => $itemData['item_type'][$key],
        							'item_code' => $itemData['item_code'][$key],
        							'item_desc' => $itemData['item_desc'][$key],
        							'hsn_code' => $itemData['hsn_code'][$key],
        							'qty' => $itemData['qty'][$key],
        							'unit_id' => $itemData['unit_id'][$key],
        							'unit_name' => $itemData['unit_name'][$key],
        							'price' => $itemData['price'][$key],
        							'item_remark' => $itemData['item_remark'][$key],
        							'drg_rev_no' => $itemData['drg_rev_no'][$key],
        							'rev_no' => $itemData['rev_no'][$key],
        							'batch_no' => $itemData['batch_no'][$key],
        							'grn_data' => $itemData['grn_data'][$key],
        							'amount' => $itemData['amount'][$key],
        							'taxable_amount' => $itemData['taxable_amount'][$key],
        							'gst_per' => $itemData['gst_per'][$key],
        							'gst_amount' => $itemData['igst_amount'][$key],
        							'igst_per' => $itemData['igst_per'][$key],
        							'igst_amount' => $itemData['igst_amount'][$key],
        							'cgst_per' => $itemData['cgst_per'][$key],
        							'cgst_amount' => $itemData['cgst_amount'][$key],
        							'sgst_per' => $itemData['sgst_per'][$key],    
        							'sgst_amount' => $itemData['sgst_amount'][$key],
        							'disc_per' => $itemData['disc_per'][$key],
        							'disc_amount' => $itemData['disc_amount'][$key],
        							'net_amount' => $itemData['net_amount'][$key],
        							'created_by' => $masterData['created_by']
        						];
        			$this->store($this->transChild,$transData);
        			if(!empty($itemData['ref_id'][$key])):
        				$this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
        			endif;
        		endforeach;
    		
        		// Update Enquiry Trans Status
        		if(!empty($masterData['ref_id'])):
            		$enqdata = array();
            		$enqdata['select'] = "id,trans_status";
            		$enqdata['where']['trans_main_id'] = $masterData['ref_id'];
            		$enqdata['where']['trans_status'] = 0;
            		$enqdata['where']['feasible'] = 'Yes';
            		$enqdata['tableName'] = $this->transChild;
            		$quoteEnquiry = $this->rows($enqdata);
        		    if(empty($quoteEnquiry)):
        				$this->store($this->transMain,['id'=>$masterData['ref_id'],'trans_status'=>1]);
        			endif;
        		endif;
        		
        		/*** Revision Quotation ***/
        		if($is_revision != 0):
        			$this->saveReviseQuote($quoteId);
        		endif;
				  /* Send Notification */
				  $sqNo = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
				  $notifyData['notificationTitle'] = (empty($masterData['id']))?"New Sales Quotation":"Update Sales Quotation";
				  $notifyData['notificationMsg'] = (empty($masterData['id']))?"New Sales Quotation Generated. SQ. No. : ".$sqNo:"Sales Quotation updated. SQ No. : ".$sqNo;
				  $notifyData['payload'] = ['callBack' => base_url('salesQuotation')];
				  $notifyData['controller'] = "'salesQuotation'";
				  $notifyData['action'] = (empty($masterData['id']))?"W":"M";
				  $this->notify($notifyData);
		    endif;
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }
    
    public function checkDuplicateQuote($trans_prefix,$trans_no,$entry_type,$id = ""){
		$data['tableName'] = $this->transMain;
		$data['where']['trans_prefix']  = $trans_prefix;
		$data['where']['trans_no']  = $trans_no;
		$data['where']['trans_date >= '] = $this->startYearDate;
        $data['where']['trans_date <= '] = $this->endYearDate;
		$data['where']['entry_type']  = $entry_type;
		if(!empty($id)){$data['where']['id != '] = $id;}
		return $this->numRows($data);
    }
    
    public function saveReviseQuote($quoteId){
		try{
            $this->db->trans_begin();
            $queryData['tableName'] = $this->transMain;
    		$queryData['where']['trans_main.id'] = $quoteId;
    		$quoteData = (array) $this->row($queryData);
    		
    		$quoteData['ref_id'] = $quoteData['id'];
    		$quoteData['id'] = "";
    		$quoteData['from_entry_type'] = $quoteData['entry_type'];
    		$quoteData['entry_type'] = 3;
    		$quoteData['trans_prefix'] = $this->transModel->getTransPrefix(3);
    		$quoteData['trans_no'] = $this->transModel->nextTransNo(3);
    
    		$saleQuotationSave = $this->store($this->transMain,$quoteData);
    		$rquoteId = $saleQuotationSave['insert_id']; 
    		
    		
    		$itemData = $this->getTransChild($quoteId);
            foreach($itemData as $row):
    			$transData = (array) $row;
    			$transData['id'] = "";
    			$transData['entry_type'] = 3;
    			$transData['from_entry_type'] = $quoteData['from_entry_type'];
    			$transData['trans_main_id'] = $rquoteId;
    			
    			$this->store($this->transChild,$transData);
    		endforeach;
        
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $saleQuotationSave;
		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function save1($masterData,$itemData){
        
        $quoteId = $masterData['id'];
		
		$custData['party_name'] = $masterData['party_name'];$custData['contact_person'] = $masterData['contact_person'];
		$custData['party_mobile'] = $masterData['contact_no'];$custData['contact_email'] = $masterData['contact_email'];
		$custData['party_phone'] = $masterData['party_phone'];$custData['party_email'] = $masterData['party_email'];
		$custData['party_address'] = $masterData['party_address'];$custData['party_pincode'] = $masterData['party_pincode'];
		if(empty($masterData['party_id'])):
			$masterData['party_id'] = $this->saveLead($masterData['party_name']);
		else:
            $custData['id'] = $masterData['party_id'];
            $custData['lead_status'] = 3;
			$customerSave = $this->store('party_master',$custData);
		endif;

        unset($masterData['contact_person'],$masterData['contact_no'],$masterData['contact_email'],$masterData['party_email'],$masterData['party_phone'],$masterData['party_address'],$masterData['party_pincode']);

		if(empty($quoteId)):	            
            //save Sales Enquiry data
            $saleQuotationSave = $this->store($this->transMain,$masterData);
            $quoteId = $saleQuotationSave['insert_id'];  
            
            if(!empty($masterData['ref_id'])):
                $this->store($this->transMain,['id'=>$masterData['ref_id'],'trans_status'=>1]);
            endif;
            
            $result = ['status'=>1,'message'=>'Sales Quotation saved successfully.','url'=>base_url("salesQuotation")];        
        else:
            $this->store($this->transMain,$masterData);
            
            $data = array();
            $data['select'] = "id";
            $data['where']['trans_main_id'] = $quoteId;
            $data['tableName'] = $this->transChild;
            $squoteIdArray = $this->rows($data);
            
            foreach($squoteIdArray as $key=>$value):
                if(!in_array($value->id,$itemData['id'])):		
                    $this->trash($this->transChild,['id'=>$value->id]);
                endif;
            endforeach;
            
            $result = ['status'=>1,'message'=>'Sales Quotation updated successfully.','url'=>base_url("salesQuotation")];
        endif;

        foreach($itemData['item_name'] as $key=>$value):
	
            $itmQuery['tableName'] = $this->itemMaster;
            $itmQuery['where']['item_name'] = $value;
            $itmMaster = $this->row($itmQuery);
            $item_id = $itemData['item_id'][$key];
            if(!empty($itmMaster)){$item_id = $itmMaster->id;}
            $transData = [
                            'id' => $itemData['id'][$key],
                            'entry_type' => $masterData['entry_type'],
                            'from_entry_type' => $itemData['from_entry_type'][$key],
                            'ref_id' => $itemData['ref_id'][$key],
                            'trans_main_id' => $quoteId,
                            'item_id' => $item_id,
                            'item_name' => $value,
							'item_type' => $itemData['item_type'][$key],
							'item_code' => $itemData['item_code'][$key],
							'item_desc' => $itemData['item_desc'][$key],
							'hsn_code' => $itemData['hsn_code'][$key],
							'gst_per' => $itemData['gst_per'][$key],
                            'qty' => $itemData['qty'][$key],
                            'unit_id' => $itemData['unit_id'][$key],
                            'unit_name' => $itemData['unit_name'][$key],
                            'price' => $itemData['price'][$key],
                            'item_remark' => $itemData['item_remark'][$key],
                			'drg_rev_no' => $itemData['drg_rev_no'][$key],
							'rev_no' => $itemData['rev_no'][$key],
							'batch_no' => $itemData['batch_no'][$key],
							'grn_data' => $itemData['grn_data'][$key],
                            'created_by' => $masterData['created_by']
                        ];
            $this->store($this->transChild,$transData);
            if(!empty($itemData['ref_id'][$key])):
                $this->store($this->transChild,['id'=>$itemData['ref_id'][$key],'trans_status'=>1]);
            endif;
        endforeach;

        return $result;
    }

    public function saveLead($party_name){
		try{
            $this->db->trans_begin();
		if(!empty($party_name)):
			$customerSave = $this->store('party_master',['id'=>'','party_category'=>1,'party_name'=>$party_name,'party_type'=>2,'lead_status'=>3]);
			$result = $customerSave['insert_id'];
		else:
			$result = 0;
		endif;
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}

    public function getSalesQuotation($id){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.*,party_master.party_name,party_master.contact_person,party_master.party_mobile,party_master.contact_email,party_master.party_phone, party_master.party_email, party_master.party_address,party_master.party_pincode";		
		$data['join']['party_master'] = 'party_master.id = trans_main.party_id';
		$data['where']['trans_main.id'] = $id;
		$result = $this->row($data);		
		$result->itemData = $this->getTransChild($id);
		return $result;
	} 

    public function getTransChild($id){
		$data['tableName'] = $this->transChild;
		$data['select'] = "trans_child.*";
        $data['where']['trans_child.trans_main_id'] = $id;
		$result = $this->rows($data);
		return $result;
	}

    public function deleteQuotation($id){
		try{
            $this->db->trans_begin();
        $transMainData = $this->getSalesQuotation($id);
        if(!empty($transMainData->ref_id)):
            $this->store($this->transMain,['id'=>$transMainData->ref_id,'trans_status'=>0]);
            $setData = Array();
			$setData['tableName'] = $this->transChild;
			$setData['where']['trans_main_id'] = $transMainData->ref_id;
			$setData['set']['trans_status'] = 'trans_status, = '.(0);
			$qryresult = $this->setValue($setData);
        endif;

        //enquiry transation delete
		$where['trans_main_id'] = $id;
		$this->trash($this->transChild,$where);
        
        //enquiry master delete
		$result = $this->trash($this->transMain,['id'=>$id],'Sales Quotation');
		
			/* Send Notification */
			$sqNo = getPrefixNumber($transMainData->trans_prefix,$transMainData->trans_no);
			$notifyData['notificationTitle'] = "Delete Sales Quotation";
			$notifyData['notificationMsg'] = "Sales Quotation deleted. SQ No. : ".$sqNo;
			$notifyData['payload'] = ['callBack' => base_url('salesQuotation')];
			$notifyData['controller'] = "'salesQuotation'";
			$notifyData['action'] = "D";
			$this->notify($notifyData);

		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
    }

    public function getQuotationItems($quote_id){
		
		$qdata['tableName'] = $this->transChild;
        $qdata['select'] = 'trans_child.*';
        $qdata['where']['trans_child.entry_type'] = 2;
        $qdata['where']['trans_child.trans_main_id'] = $quote_id;
		$quoteItems = $this->rows($qdata);

		if(!empty($quoteItems)):
			$i=1; $html="";
			foreach($quoteItems as $row):
				if(empty($row->confirm_by)):
					$checked = "";
					$disabled = "disabled";
					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheckCQ" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' />
								<label for="md_checkbox'.$i.'" style="margin-bottom:0px;"></label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" class="form-control" value="'.$row->item_id.'" '.$disabled.' />
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
								<input type="hidden" name="inq_trans_id[]" id="inq_trans_id'.$i.'" class="form-control" value="'.$row->ref_id.'" '.$disabled.' />
								<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" class="form-control" value="'.$row->unit_id.'" '.$disabled.' />
								<input type="hidden" name="automotive[]" id="automotive'.$i.'" class="form-control" value="'.$row->automotive.'" '.$disabled.' />
							</td>
							<td>
								'.floatVal($row->qty).' <small>('.$row->unit_name.')</small>
								<input type="hidden" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								'.$row->price.'
								<input type="hidden" name="price[]" id="price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->price.'" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="confirm_price[]" id="confirm_price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="0" min="0" '.$disabled.' />
								<div class="error confirm_price'.$row->id.'"></div>
							</td>			
							<td>
								<input type="text" name="drg_rev_no[]" id="drg_rev_no'.$i.'" class="form-control" value="'.$row->drg_rev_no.'" '.$disabled.' />
							</td>
							<td>
								<input type="text" name="rev_no[]" id="rev_no'.$i.'" class="form-control" value="'.$row->rev_no.'" '.$disabled.' />
							</td>						
						</tr>';				
				else:
										
					$checked = "checked";
					$disabled = "disabled";

					$html .= '<tr>
							<td class="text-center">
								<input type="checkbox" id="md_checkbox'.$i.'" class="filled-in chk-col-success itemCheck" data-rowid="'.$i.'" check="'.$checked.'" '.$checked.' '.$disabled.' />
								<label for="md_checkbox'.$i.'" style="margin-bottom:0px;"></label>
							</td>
							<td>
								'.$row->item_name.'
								<input type="hidden" name="item_id[]" id="item_id'.$i.'" class="form-control" value="'.$row->item_id.'" '.$disabled.' />
								<input type="hidden" name="item_name[]" id="item_name'.$i.'" class="form-control" value="'.$row->item_name.'" '.$disabled.' />
								<input type="hidden" name="trans_id[]" id="trans_id'.$i.'" class="form-control" value="'.$row->id.'" '.$disabled.' />
								<input type="hidden" name="inq_trans_id[]" id="inq_trans_id'.$i.'" class="form-control" value="'.$row->ref_id.'" '.$disabled.' />
								<input type="hidden" name="unit_id[]" id="unit_id'.$i.'" class="form-control" value="'.$row->unit_id.'" '.$disabled.' />
								<input type="hidden" name="automotive[]" id="automotive'.$i.'" class="form-control" value="'.$row->automotive.'" '.$disabled.' />
								<input type="hidden" name="drg_rev_no[]" id="drg_rev_no'.$i.'" class="form-control" value="'.$row->drg_rev_no.'" '.$disabled.' />
								<input type="hidden" name="rev_no[]" id="rev_no'.$i.'" class="form-control" value="'.$row->rev_no.'" '.$disabled.' />
							</td>
							<td>
								'.$row->qty.'
								<input type="hidden" name="qty[]" id="qty'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->qty.'" min="0" '.$disabled.' />
								<div class="error qty'.$row->id.'"></div>
							</td>
							<td>
								'.$row->price.'
								<input type="hidden" name="price[]" id="price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->price.'" min="0" '.$disabled.' />
								<div class="error price'.$row->id.'"></div>
							</td>
							<td>
								<input type="number" name="confirm_price[]" id="confirm_price'.$i.'" class="form-control floatOnly" data-id="'.$i.'" value="'.$row->org_price.'" min="0" '.$disabled.' />
								<div class="error confirm_price'.$row->id.'"></div>
							</td>
							<td>
								<input type="text" name="drg_rev_no[]" id="drg_rev_no'.$i.'" class="form-control" value="'.$row->drg_rev_no.'" '.$disabled.' />
							</td>
							<td>
								<input type="text" name="rev_no[]" id="rev_no'.$i.'" class="form-control" value="'.$row->rev_no.'" '.$disabled.' />
							</td>
						</tr>';

				endif;$i++;
			endforeach;
		else:
			$html = '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
		endif;
		return $html;
	}

    public function saveConfirmQuotation($data){
		try{
            $this->db->trans_begin();
			
		//save sales Quotation items
		foreach($data['trans_id'] as $key=>$value):
			$itmId = $data['item_id'][$key];
			$itmData = Array();
			/*** Store New Confirmed Item to Item Master ***/
			if(empty($data['item_id'][$key])):
				$itmData['id']="";
				$itmData['item_name']=$data['item_name'][$key];
				$itmData['price']=$data['confirm_price'][$key];
				$itmData['unit_id']=$data['unit_id'][$key];
				$itmData['automotive']=$data['automotive'][$key];
				$itmData['party_id']=$data['customer_id'];
				$itmData['drawing_no']=$data['drg_rev_no'][$key];
				$itmData['rev_no']=$data['rev_no'][$key];
				$itmData['item_type'] = 1;
				$newItem = $this->store($this->itemMaster,$itmData);
				if(!empty($newItem['insert_id'])){$itmId = $newItem['insert_id'];}
			endif;
			/*** Update Quotation Transaction with Confirmed Parameters  ***/
			$transData = [
							'id' =>  $value,
							'item_id' =>  $itmId,
							'org_price' => $data['confirm_price'][$key],
							'drg_rev_no' => $data['drg_rev_no'][$key],
							'rev_no' => $data['rev_no'][$key],
							'cod_date' => formatDate($data['confirm_date'],'Y-m-d'),
							'confirm_by' => $data['confirm_by']
						];
			$this->store($this->transChild,$transData);
		endforeach;
		
		$customerSave = $this->store('party_master',['id'=>$data['customer_id'],'party_type'=>1,'lead_status'=>6]);		
		
		$queryData = array();
        $queryData['where']['trans_main_id'] = $data['id'];
        $queryData['where']['confirm_by'] = 0;
        $queryData['where']['entry_type'] = 2;
        $queryData['resultType'][] = "numRows";
		$queryData['tableName'] = $this->transChild;
		$quotationItems = $this->rows($queryData);
		if(count($quotationItems) <=0 ):
			$this->store($this->transMain,['id'=>$data['id'],'trans_status' => 1]);
		endif;

		$result = ['status'=>1,'message'=>'Sales Quotation Confirmed Successfully.'];
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}

	/** For Print Quotation **/
    public function getSalesQuotationForPrint($id){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.id,trans_main.entry_type,trans_main.from_entry_type,trans_main.ref_id,trans_main.lr_no,trans_main.trans_prefix,trans_main.trans_no, trans_main.trans_date, trans_main.party_id,trans_main.ref_by, trans_main.sales_executive, trans_main.terms_conditions, trans_main.remark, trans_main.total_amount,trans_main.igst_amount,trans_main.freight_amount,trans_main.packing_amount,trans_main.round_off_amount, trans_main.taxable_amount,trans_main.net_amount, trans_main.quote_rev_no, trans_main.doc_date, trans_main.challan_no, trans_main.net_weight, party_master.party_name, party_master.contact_person, party_master.party_mobile,party_master.contact_email,party_master.party_phone, party_master.party_email, party_master.party_address,party_master.party_pincode,tm.trans_prefix as ref_prefix,tm.trans_no as ref_no,tm.trans_date as ref_date";		
		$data['join']['party_master'] = 'party_master.id = trans_main.party_id';
		$data['leftJoin']['trans_main as tm'] = 'tm.id = trans_main.ref_id';
		$data['where']['trans_main.id'] = $id;
		$result = $this->row($data);		
		$result->itemData = $this->getTransChild($id);
		return $result;
	}

	public function saveFollowUp($data){
		try{
            $this->db->trans_begin();
		if(empty($data['trans_status'])):
			unset($data['trans_status']);
		endif; 
		$result = $this->store($this->transMain,$data,'Follow Up');
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}

	public function approveQuotation($data){
		$this->store($this->transMain, ['id'=> $data['id'], 'is_approve' => $data['val'], 'approve_date'=>date('Y-m-d')]);
        return ['status' => 1, 'message' => 'Sales Quotation '.$data['msg'].' successfully.'];
	}
	
	public function getSalesQuotationList($id){
		$data['tableName'] = $this->transMain;
		$data['select'] = "trans_main.id,trans_main.from_entry_type,trans_main.ref_id,trans_main.lr_no,trans_main.trans_prefix,trans_main.trans_no, trans_main.trans_date, trans_main.party_id,trans_main.ref_by, trans_main.sales_executive, trans_main.terms_conditions, trans_main.remark, trans_main.total_amount,trans_main.igst_amount,trans_main.freight_amount,trans_main.packing_amount,trans_main.round_off_amount, trans_main.taxable_amount,trans_main.net_amount, trans_main.quote_rev_no, trans_main.doc_date, trans_main.challan_no, trans_main.net_weight, party_master.party_name, party_master.contact_person, party_master.party_mobile,party_master.contact_email,party_master.party_phone, party_master.party_email, party_master.party_address,party_master.party_pincode,tm.trans_prefix as ref_prefix,tm.trans_no as ref_no,tm.trans_date as ref_date";		
		$data['join']['party_master'] = 'party_master.id = trans_main.party_id';
		$data['leftJoin']['trans_main as tm'] = 'tm.id = trans_main.ref_id';
		$data['where']['trans_main.ref_id'] = $id;
		$result = $this->rows($data);	
		return $result;
	} 
	
	//Created By Karmi @26/03/2022
	public function getQuoNOByRefid($transIds){
        $data['tableName'] = $this->transMain;        
        $data['select'] = "trans_main.id,trans_main.trans_prefix,trans_main.trans_no,trans_child.trans_main_id";
        $data['leftJoin']['trans_child'] = "trans_main.id = trans_child.trans_main_id";
        $data['where_in']['trans_child.trans_main_id'] = $transIds;
        $data['group_by'][] = 'trans_child.trans_main_id';
        return $this->rows($data);
    }
	public function getSalesQuatationTransactions($id){  
        $data['tableName'] = $this->transChild;    
        $data['select'] = 'trans_child.*,item_master.rev_no as itemRevNO , item_master.drawing_no as itemDrgNo, item_master.part_no as partNo';
        $data['join']['item_master'] = 'item_master.id = trans_child.item_id';
        $data['where']['entry_type'] = 2;
		//$data['where']['confirm_by'] = 0;
        $data['where']['trans_main_id'] = $id;
        return $this->rows($data);
    }
	public function getPartyOrders($id){
        $queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*,";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';       
        $queryData['where']['trans_child.confirm_by != '] = 0;
        $queryData['where']['trans_main.entry_type'] = 2;
        $queryData['where']['trans_main.party_id'] = $id;
        $queryData['group_by'][] = 'trans_child.trans_main_id';
        $resultData = $this->rows($queryData);
        
        $html="";
        if(!empty($resultData)):
            $i=1;
            foreach($resultData as $row):
                
                $partCode = array(); $qty = array();
                $partData = $this->getSalesQuatationTransactions($row->id);
                foreach($partData as $part):
                    $partCode[] = $part->item_code; 
                    $qty[] = $part->qty; 
                endforeach;
                $part_code = implode(",<br> ",$partCode); $part_qty = implode(",<br> ",$qty);
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="ref_id[]" class="filled-in chk-col-success" value="'.$row->id.'"  ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                            <td class="text-center">'.formatDate($row->trans_date).'</td>
                            <td class="text-center">'.$part_code.'</td>
                            <td class="text-center">'.$part_qty.'</td>
                          </tr>';
                $i++;
            endforeach;
        // else:
        //     $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$resultData];
    }
	
	/*  Create By : Avruti @29-11-2021 01:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
		$data['tableName'] = $this->transChild;
		$data['where']['trans_child.entry_type'] = 2;
        return $this->numRows($data);
    }

    public function getSalesQuotationList_api($limit, $start,$status){
		$data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty, trans_child.price, trans_child.org_price, trans_child.cod_date,trans_child.confirm_by,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by, trans_main.quote_rev_no';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 2;
        $data['where']['trans_child.trans_status != '] = 2;
		$data['group_by'][]='trans_child.trans_main_id';

		if($status == 1) { $data['where']['trans_main.trans_status'] = 2; } 
        else { $data['where']['trans_main.trans_status != '] = 2; }

        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>