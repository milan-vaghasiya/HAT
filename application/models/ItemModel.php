<?php
class ItemModel extends MasterModel{
    private $itemMaster = "item_master";
	private $stockTrans = "item_stock_trans";
	private $itemKit = "item_kit";
	private $productProcess = "product_process";
	private $processMaster = "process_master";
    private $unitMaster = "unit_master";
    private $itemCategory = "item_category";
    private $openingStockTrans = "stock_transaction";
    private $productionOperation = "production_operation";
    private $inspectionParam = "inspection_param";
    private $fgRevision = "fg_revisions";
    private $familyGroup = "family_group";
    private $calibration = "calibration";
	private $production_output = 'production_output';
	private $product_dimensions = 'product_dimensions';
    private $productDimensions = "product_dimensions";

    public function getDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code,st.stock_qty";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['leftJoin']['(SELECT SUM(qty) as stock_qty, item_id FROM stock_transaction WHERE is_delete = 0 AND stock_effect = 1 AND location_id !='.$this->SCRAP_STORE->id.' GROUP BY item_id) as st'] = "st.item_id = item_master.id";
        $data['where']['item_master.item_type'] = $type;
        
        $columns = Array();
        if($type==1)
		{ 
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
            $data['searchCol'][] = "item_master.item_name";
            $data['searchCol'][] = "item_master.size";
    		$data['searchCol'][] = "item_master.part_no";
            $data['searchCol'][] = "item_master.hsn_code";
            $data['searchCol'][] = "party_master.party_code";
    		$data['searchCol'][] = "item_master.drawing_no";
    		$data['searchCol'][] = "item_master.rev_no";
            $data['searchCol'][] = "item_master.price";
            //$data['searchCol'][] = "item_master.opening_qty";
    
    		$columns =array('','','item_master.item_code','item_master.item_name','item_master.part_no','item_master.hsn_code','party_master.party_code','item_master.drawing_no',"item_master.rev_no","item_master.price");
		}
		elseif($type==4)
		{ 
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
            $data['searchCol'][] = "item_master.item_name";
    		$data['searchCol'][] = "item_category.category_name";
            $data['searchCol'][] = "item_master.opening_qty";
            $data['searchCol'][] = "item_master.qty";
            $data['searchCol'][] = "";
    
    		$columns =array('','','item_master.item_name','item_category.category_name','item_master.opening_qty','item_master.qty','');
		}
		elseif($type==3 OR $type==2)
		{ 
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
            $data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_category.category_name";
            $data['searchCol'][] = "item_master.hsn_code";
            //$data['searchCol'][] = "item_master.opening_qty";
            //$data['searchCol'][] = "item_master.qty";
            //$data['searchCol'][] = "";
    
    		$columns =array('','','item_master.item_code','item_master.item_name','item_category.category_name','item_master.hsn_code');
		}else if($type ==11){
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_name";
			$data['searchCol'][] = "item_master.item_code";
			$data['searchCol'][] = "item_master.hsn_code";
		    $columns =array('','','item_master.item_name','item_master.item_code','item_master.hsn_code');
		}
		else
		{ 
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "item_master.item_code";
            $data['searchCol'][] = "item_master.item_name";
    		$data['searchCol'][] = "item_master.part_no";
            $data['searchCol'][] = "item_master.hsn_code";
            $data['searchCol'][] = "party_master.party_code";
    		$data['searchCol'][] = "item_master.drawing_no";
    		$data['searchCol'][] = "item_master.rev_no";
            $data['searchCol'][] = "item_master.price";
            $data['searchCol'][] = "item_master.opening_qty";
            $data['searchCol'][] = "item_master.qty";
            $data['searchCol'][] = "";
    
    		$columns =array('','','item_master.item_code','item_master.item_name','item_master.part_no','item_master.hsn_code','party_master.party_code','item_master.drawing_no',"item_master.rev_no","item_master.price",'item_master.opening_qty','item_master.qty','');
		}
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getProdOptDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['where']['item_master.item_type'] = $type;
		
        $columns = array();
        if($type == 1){
        	$data['searchCol'][] = "";
            $data['searchCol'][] = "item_master.item_name";
            $data['searchCol'][] = "item_master.item_code";
        	$data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
            $data['searchCol'][] = "";
    		
        	$columns =array('','item_master.item_name','item_master.item_code','','','','');
        } else {
            $data['searchCol'][] = "";
            $data['searchCol'][] = "item_master.item_name";
        	$data['searchCol'][] = "";
    		
        	$columns =array('','item_master.item_name','');
        }
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
    
	// Created By Meghavi @12/01/2023
	public function getToolDieDTRows($data){
        $data['tableName'] = $this->itemMaster;  
		$data['select'] = "item_master.*,GROUP_CONCAT(fg_master.item_code SEPARATOR ', ') as fg_code,GROUP_CONCAT(CONCAT(fg_master.item_code,' - ',fg_master.make_brand) SEPARATOR '<hr>') as fg_dia";
        $data['leftJoin']['item_master as fg_master'] = "FIND_IN_SET(fg_master.id, item_master.fg_id) > 0";
        $data['where']['item_master.item_type'] = 8;
		$data['group_by'][] = 'item_master.id';
        
    	$data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
    	$data['searchCol'][] = "item_master.make_brand";
        $data['searchCol'][] = "item_master.size";
        $data['searchCol'][] = "item_master.wt_pcs";
        $data['searchCol'][] = "item_master.part_no";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "item_master.item_code";
		
    	$columns =array('','item_master.item_code','item_master.make_brand','item_master.size','item_master.wt_pcs','item_master.part_no','party_master.party_name,item_master.item_code');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
	
    public function getStockDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['where']['item_master.item_type'] = $type;
        $data['order_by']['item_master.qty'] = 'ASC';
        
    	$data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
    	$data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "item_master.hsn_code";
        $data['searchCol'][] = "item_master.opening_qty";
        $data['searchCol'][] = "item_master.qty";
		
    	$columns =array('','item_master.item_code','item_master.item_name','item_master.hsn_code','item_master.opening_qty','item_master.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function getItemList($type=0){
		$data['tableName'] = $this->itemMaster;
	    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.family_id,item_master.item_alias,unit_master.unit_name,material_master.color_code,item_master.material_grade,material_master.material_grade as mtr_grade,item_master.size";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['material_master'] = "material_master.id=item_master.material_grade_id";

		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
	// Created By Meghavi @29/09/2023 
	public function getDieListForSelect($fgId){
	    $data['where']['item_type'] = 8;
	    $data['customWhere'][] = 'find_in_set("'.$fgId.'", fg_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}

	public function getItemDataFromItemCode($item_code){		
		$data['tableName'] = 'item_master';
		$data['where']['item_code'] = $item_code;
		$data['where']['item_type'] = 1;
		return $this->row($data);
	}
	
    public function getItemLists($type="0"){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.family_id,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";

		if(!empty($type) and $type != "0")
			$data['where_in']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
    public function getLastPartCode($party_id=0){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = 'MAX(item_master.item_code) as last_part';
		$data['where']['party_id'] = $party_id;
		return $this->row($data);
	}
	
	public function getItemListForSelect($type=0){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,unit_master.unit_name,material_master.color_code";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['material_master'] = "material_master.material_grade = item_master.material_grade";

		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}

	public function locationWiseBatchStock($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as qty,batch_no";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		$data['order_by']['id'] = "asc";
		$data['group_by'][] = "batch_no";
		return $this->rows($data);
	}

    public function getItem($id,$select=''){
        $data['tableName'] = $this->itemMaster;
        if(!empty($select)){$data['select'] = $select;}
        $data['where']['id'] = $id;
        return $this->row($data);
    }
	
    public function getItemBySelect($id,$select){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = $select;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function itemUnits(){
        $data['tableName'] = $this->unitMaster;
		return $this->rows($data);
	}

	public function itemUnit($id){
        $data['tableName'] = $this->unitMaster;
		$data['where']['id'] = $id;
		return $this->row($data);
	}

	public function getOpeningRawMaterialList(){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
        $data['join']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['item_master.item_type'] = 1;
		$data['where']['item_master.opening_remaining_qty != '] = "0.000";
		return $this->rows($data);
	}

    public function save($data){ 
		try{ 
            $this->db->trans_begin();
			$process = array();$itmId = 0;
			$msg = ($data['item_type'] == 0)?"Item":"Part";
			if($this->checkDuplicate($data['item_code'],$data['item_type'],$data['id']) > 0):
				$errorMessage['item_code'] =  $msg." Code is duplicate.";
				return ['status'=>0,'message'=>$errorMessage];
			else:
				if(!empty($data['process_id'])):
					$process = explode(',',$data['process_id']);
				endif;
				unset($data['process_id']);
				
				$mgsName = ($data['item_type'] == 0)?"Item":"Product";
				$result = $this->store($this->itemMaster,$data,$mgsName);
				$itmId = (empty($data['id'])) ? $result['insert_id'] : $data['id'];

				if(!empty($process) AND !empty($itmId)):
					$ppData = ["item_id"=>$itmId,"process"=>$process,"created_by"=>$data['created_by']];
					$ppResult = $this->saveProductProcess($ppData);
				endif;
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

    public function checkDuplicate($code,$type,$id=""){
        
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_code'] = $code;
        $data['where']['item_type'] = $type;
        if(!empty($id))
            $data['where']['id !='] = $id;

        $result = $this->numRows($data);
        return $result;
    }

    public function checkDuplicateName($name,$type,$id=""){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_name'] = $name;
        $data['where']['item_type'] = $type;
        if(!empty($id))
            $data['where']['id !='] = $id;

        $result = $this->numRows($data);
        return $result;
    }

    public function delete($id){
		try{
            $this->db->trans_begin();
			$itemData = $this->getItem($id);
			$mgsName = ($itemData->item_type == 0)?"Item":"Product";
			$result = $this->trash($this->itemMaster,['id'=>$id],$mgsName);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
    }

    public function getStockTrans($id){
		$data['where']['item_id'] = $id;
		$data['order_by']['trans_date'] = 'desc';
        $data['tableName'] = $this->stockTrans;
		$stockTrans = $this->rows($data);
		
		if(!empty($stockTrans)):
			$html = "";$i=1;
			foreach($stockTrans as $row):
				$typeName = ($row->type == "+")?"Add":"Reduce";
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.date('d-m-Y',strtotime($row->trans_date)).'</td>
							<td>('.$row->type.') '.$typeName.'</td>
							<td>'.$row->qty.'</td>
							<td class="text-center"><div class="btn-group"><a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light" onclick="deleteStock('.$row->id.');" ><i class="ti-trash"></i></a></div></td>
						 </tr>';
			endforeach;
			$result = $html;
		else:
			$result = "";
		endif;
		return $result;
	}

    public function saveStockTrans($data){
		try{
            $this->db->trans_begin();
			$data['id'] = "";
			$data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
			$data['created_by'] = $this->session->userdata('loginId');
			$this->store($this->stockTrans,$data,"");
			
			$itemData = $this->getItem($data['item_id']);
			if($data['type'] == "+"):
				$qty = $itemData->qty + $data['qty'];
			else:
				$qty = $itemData->qty - $data['qty'];
			endif;

			$this->edit($this->itemMaster,['id'=>$data['item_id']],['qty'=>$qty]);		
			$result = $this->getStockTrans($data['item_id']);
			if($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	
	public function getCategoryList($type=0,$item_type=0){
        $data['tableName'] = $this->itemCategory;
		$data['where']['category_type'] = $type;
		if(!empty($item_type)){$data['where_in']['item_type'] = $item_type;}
        return $this->rows($data);
    }
    
	public function getItemGroup(){
        $data['tableName'] = 'item_group';
        return $this->rows($data);
    }
    
    public function getItemGroupById($id){
        $data['tableName'] = 'item_group';
		$data['where']['id'] = $id;
        return $this->row($data);
    }
    
	public function deleteStockTrans($id){
		try{
            $this->db->trans_begin();
        $data['tableName'] = $this->stockTrans;
        $data['where']['id'] = $id;
		$transData = $this->row($data);		
		$this->trash($this->stockTrans,['id'=>$id],'Stock');
		
		$itemData = $this->getItem($transData->item_id);
		if($transData->type == "+"):
			$qty = $itemData->qty - $transData->qty;
		else:
			$qty = $itemData->qty + $transData->qty;
		endif;
		
        $this->edit($this->itemMaster,['id'=>$transData->item_id],['qty'=>$qty]);		
		$result = $this->getStockTrans($transData->item_id);
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}

	public function getProductProcessForSelect($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->rows($data);
		$process = array();
		if($result){foreach($result as $row){$process[] = $row->process_id;}}
		return $process;
	}
	
	public function getProductOperationForSelect($id){
		$data['select'] = "operation";
		$data['where']['id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->row($data);
		return $result->operation;
	}
	
	public function getProductProcess($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		return $this->rows($data);
	}

	public function saveProductProcess($data){
		try{
            $this->db->trans_begin();
    		$queryData['select'] = "process_id,id,sequence";
    		$queryData['where']['item_id'] = $data['item_id'];
    		$queryData['tableName'] = $this->productProcess;
    		$process_ids =  $this->rows($queryData);
    
    		$process = '';
    		if(!empty($data['process_id'])):
    			$process = explode(',',$data['process_id']);
    		endif;
    		$z=0;
    		foreach($process_ids as $key=>$value):
    			if(!in_array($value->process_id,$process)):
    			
    				$upProcess['tableName'] = $this->productProcess;
    				$upProcess['where']['item_id']=$data['item_id'];
    				$upProcess['where']['sequence > ']=($value->sequence - $z++);
    				$upProcess['where']['is_delete']=0;
    				$upProcess['set']['sequence']='sequence, - 1';
    				$q = $this->setValue($upProcess);
    				$this->remove($this->productProcess,['id'=>$value->id],'');
    			endif;
    		endforeach;
    		foreach($process as $key=>$value):			
    			if(!in_array($value,array_column($process_ids,'process_id'))):
    				$queryData = array();
    				$queryData['select'] = "MAX(sequence) as value";
    				$queryData['where']['item_id'] = $data['item_id'];
    				$queryData['where']['is_delete'] = 0;
    				$queryData['tableName'] = $this->productProcess;
    				$sequence = $this->specificRow($queryData)->value;
    				
    				$productProcessData = [
    					'id'=>"",
    					'item_id'=>$data['item_id'],
    					'process_id'=>$value,
    					'sequence'=>(!empty($sequence))?($sequence + 1):1,
    					'created_by' => $this->session->userdata('loginId')
    				];
    				$this->store($this->productProcess,$productProcessData,'');
    			endif;
    		endforeach;
    
    
    		$result = ['status'=>1,'message'=>'Product process saved successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

	public function saveProductProcessCycleTime($data){
		try{
            $this->db->trans_begin();
    		foreach($data['id'] as $key=>$value):
    			if(!empty($data['cycle_time'][$key])):	
    			    if(empty($value)):
    				    $productProcessData = ['id'=>$value,'cycle_time'=>$data['cycle_time'][$key],'created_by'=>$data['loginId']];
    				else:
    				    $productProcessData = ['id'=>$value,'cycle_time'=>$data['cycle_time'][$key],'updated_by'=>$data['loginId']];
    				endif;
    				$this->store($this->productProcess,$productProcessData,'');
    			endif;
    		endforeach;
    
    		$result = ['status'=>1,'message'=>'Cycle Time Updated successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

	public function getItemProcess($id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $id;
		$data['order_by']['product_process.sequence'] = "ASC";
		return $this->rows($data);
	}

	public function getProductProcessBySequence($product_id,$sequence){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		$data['where']['product_process.sequence'] = $sequence;
		return $this->row($data);
	}

	public function updateProductProcessSequance($data){
		try{
            $this->db->trans_begin();
    		$ids = explode(',', $data['id']);
    		$i=1;
    		foreach($ids as $pp_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->productProcess,['id'=>$pp_id],$seqData);
    		endforeach;
    
    // 		$queryData['tableName'] = $this->productProcess;
    // 		$queryData['where']['id'] = $ids[0];
    // 		$queryData['order_by']['sequence'] = "ASC";		
    // 		$productProcessRow = $this->row($queryData);
    // 		$this->edit($this->itemKit,['item_id'=>$productProcessRow->item_id],['process_id'=>$productProcessRow->process_id]);
    		
    		$result = ['status'=>1,'message'=>'Process Sequence updated successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

	public function getProductKitData($id){
		$data['tableName'] = $this->itemKit;
		$data['select'] = "item_kit.*,item_master.item_name,item_master.item_code,item_master.item_name as full_name,process_master.process_name,unit_master.unit_name,item_master.item_type";
		$data['join']['item_master'] = "item_master.id = item_kit.ref_item_id AND item_master.is_delete = 0";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_master.is_delete'] = 0;
		return $this->rows($data);
	}

	public function getProductKitOnProcessData($id,$processId){
		$data['select'] = "item_kit.*,item_master.item_name,item_master.item_name as full_name";
		$data['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_kit.process_id'] = $processId;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

	public function saveProductKit($data){
		try{
            $this->db->trans_begin();
			/*$kitData = $this->getProductKitData($data['item_id']);
			foreach($data['ref_item_id'] as $key=>$value):
				if(empty($data['id'][$key])):
					$itemKitData = ['id'=>"",'item_id'=>$data['item_id'],'ref_item_id'=>$value,'qty'=>$data['qty'][$key],'process_id'=>0];
					$this->store($this->itemKit,$itemKitData);
				else:
					$where['process_id'] = $data['process_id'][$key];
					$where['item_id'] = $data['item_id'];
					$where['id'] = $data['id'][$key];
					$this->edit($this->itemKit,$where,['qty'=>$data['qty'][$key]]);
				endif;
			endforeach;
			
			if(!empty($kitData)):
				foreach($kitData as $key=>$value):
					if(!in_array($value->id,$data['id'])){
						$this->trash($this->itemKit,['id'=>$value->id],'');
					}
				endforeach;
			endif;*/
			
			$itemKitData = [
				'id'=>$data['id'],
				'item_id'=>$data['item_id'],
				'ref_item_id'=>$data['kit_item_id'],
				'qty'=>$data['kit_item_qty'],
				'used_as'=>$data['used_as']
			];
			$this->store($this->itemKit,$itemKitData);
			$result = ['status'=>1,'message'=>'Product Kit Item saved successfully.'];
			
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}
	
	public function checkDuplicateKitItem($item_id,$ref_item_id,$id=""){
        
        $data['tableName'] = $this->itemKit;
        $data['where']['item_id'] = $item_id;
        $data['where']['ref_item_id'] = $ref_item_id;
        if(!empty($id))
            $data['where']['id !='] = $id;

        $result = $this->numRows($data);
        return $result;
    }
	
	public function deleteProductKit($id){
		$this->trash($this->itemKit,['id'=>$id],'');
	}

	public function getProductWiseProcessList($product_id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "process_master.id,process_master.process_name";
		$data['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		return $this->rows($data);
	}
	
	public function getItemOpeningTrans($id){
		$queryData['tableName'] = $this->openingStockTrans;
		$queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
		$queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$queryData['where']['stock_transaction.ref_type'] = "-1";
		$queryData['where']['stock_transaction.ref_id'] = 0;
		$queryData['where']['stock_transaction.trans_type'] = 1;
		$queryData['where']['stock_transaction.item_id']  = $id;
		$openingStockTrans = $this->rows($queryData);

		$html = '';
		if(!empty($openingStockTrans)):
			$i=1;
			foreach($openingStockTrans as $row):
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>[ '.$row->store_name.' ] '.$row->location.'</td>
							<td>'.$row->batch_no.'</td>
							<td>'.$row->qty.'</td>
							<td class="text-center">
								<div class="btn-group">
									<a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light" onclick="deleteOpeningStock('.$row->id.');" ><i class="ti-trash"></i></a>
								</div>
							</td>
						</tr>';
			endforeach;
		endif;
		return ['status'=>1,'htmlData'=>$html,'result'=>$openingStockTrans];
	}

	public function saveOpeningStock($data){
		try{
            $this->db->trans_begin();
    	    if(empty($data['batch_no'])){
			    unset($data['batch_no']);
    	    }
    		if(empty($data['tc_no'])){
    			unset($data['tc_no']);
    	    }
    	    
    		$this->store($this->openingStockTrans,$data);
    
    		$setData = Array();
    		$setData['tableName'] = $this->itemMaster;
    		$setData['where']['id'] = $data['item_id'];
    		$setData['set']['qty'] = 'qty, + '.$data['qty'];
    		$setData['set']['opening_qty'] = 'opening_qty, + '.$data['qty'];
    		$this->setValue($setData);
    
    		$result = ['status'=>1,'message'=>'Opening Stock saved successfully.','transData'=>$this->getItemOpeningTrans($data['item_id'])['htmlData']];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	public function deleteOpeningStockTrans($id){
		try{
            $this->db->trans_begin();
		$queryData['tableName'] = $this->openingStockTrans;
		$queryData['where']['id'] = $id;
		$transData = $this->row($queryData);

		$setData = Array();
		$setData['tableName'] = $this->itemMaster;
		$setData['where']['id'] = $transData->item_id;
		$setData['set']['qty'] = 'qty, - '.$transData->qty;
		$setData['set']['opening_qty'] = 'opening_qty, - '.$transData->qty;
		$this->setValue($setData);

		$this->remove($this->openingStockTrans,['id'=>$id],"Opening Stock");

		$result = ['status'=>1,'message'=>'Opening Stock deleted successfully.','transData'=>$this->getItemOpeningTrans($transData->item_id)['htmlData']];
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}
	
	public function getProcessWiseMachine($processId){
	    $data['where']['item_type'] = 5;
	    $data['customWhere'][] = 'find_in_set("'.$processId.'", process_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}
	
	public function getBatchNoCurrentStock($item_id,$location_id,$batch_no=""){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as stock_qty";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		if(!empty($batch_no)){ $data['where']['batch_no'] = $batch_no; }
		return $this->row($data);
	}

	/* Updated By :- Sweta @08-08-2023 */
	public function saveToolConsumption($data){		
	    try{
            $this->db->trans_begin();
		    
		    $result = $this->store('tool_consumption',$data,'');
		    
		    if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }	
	}

	/* Updated By :- Sweta @08-08-2023 */
	public function getToolConsumption($id){
		$data['tableName'] = "tool_consumption";		
		$data['select'] = "tool_consumption.*,item_master.item_code,item_master.item_name,process_master.process_name,master_detail.title as material_type,qc_instruments.item_code as inst_code,qc_instruments.item_name as inst_name,product_process.cycle_time,product_process.finished_weight";		
		$data['leftJoin']['item_master'] = "item_master.id = tool_consumption.ref_item_id";
		$data['leftJoin']['qc_instruments'] = "qc_instruments.id = tool_consumption.ref_item_id";
		$data['leftJoin']['master_detail'] = "item_master.sub_group = master_detail.id";
		$data['leftJoin']['process_master'] = "process_master.id = tool_consumption.process_id";
		$data['leftJoin']['product_process'] = "product_process.process_id = tool_consumption.process_id AND product_process.item_id = tool_consumption.item_id";
		$data['where']['tool_consumption.item_id'] = $id;
		$data['order_by']['product_process.sequence'] = 'ASC';
		//$data['order_by']['tool_consumption.process_id'] = 'ASC';
		$result = $this->rows($data);
		return $result;
	}

	public function getToolConsumptionOperation($operations){
		$data['tableName'] = "production_operation";
		$data['where_in']['id'] = $operations;
		return $this->rows($data);
	}
	
    public function getProductOperation($id){
        $data['where']['item_id'] = $id;
        $data['tableName'] = $this->productProcess;
        $result = $this->rows($data);
// 		print_r($result);
		$operations = Array();
		if(!empty($result)):
			foreach($result as $row)
			{
				if(!empty($row->operation)){
					$ops = explode(',',$row->operation);
					foreach($ops as $op){$operations[] = $op;}
				}
			}
		endif;
		$ops_id = array_unique($operations);$response = Array();
		if(!empty($ops_id)):
			$qData['tableName'] = $this->productionOperation;
			$qData['where_in']['id'] = implode(',',$ops_id);
			$response = $this->rows($qData);
		endif;
		return $response;
    }

	public function saveProductOperation($data){
		try{
            $this->db->trans_begin();
    		
    		$this->store($this->productProcess,['id'=>$data['id'],'operation'=>$data['operation']]);
    		$result = ['status'=>1,'message'=>'Process Operation Updated successfully.'];
    		
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}

    public function getPartyItems($party_id){
		$queryData['tableName'] = $this->itemMaster;
	    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.item_alias,item_master.size,item_master.drawing_no,item_master.rev_no,unit_master.unit_name";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		//$queryData['customWhere'][] = "((item_master.item_type = 1 AND item_master.party_id = ".$party_id.") OR item_master.item_type = 10)";
		$queryData['where']['item_master.item_type'] = 1;
        $queryData['where']['item_master.party_id'] = $party_id;
        $itemData = $this->rows($queryData);
        
        $partyItems='<option value="">Select Product Name</option>';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        return ['status'=>1,'partyItems'=>$partyItems,'itemData'=>$itemData];
    }

    public function getPartyItemList($party_id=0){
		
		$queryData['tableName'] = $this->itemMaster;
	    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.item_alias,unit_master.unit_name";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		if(!empty($party_id)){$queryData['where']['party_id'] = $party_id;}
        $itemData = $this->rows($queryData);
        
        return $itemData;
    }

	public function checkProductOptionStatus($id){
		$result = new StdClass;$result->bom=0;$result->process=0;$result->cycleTime=0;$result->tool=0; $result->finishedWeight=0; $result->inspection=0; $result->incoming_inspection=0;
		$queryData = Array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$queryData['where']['item_kit.item_id'] = $id;
		$queryData['where']['item_master.is_delete'] = 0;
		$bomData = $this->rows($queryData);
		$result->bom=count($bomData);
		
		/*$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['select']='count(*) as process, (CASE WHEN finished_weight != "00:00:00" THEN )';
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process=count($processData);*/
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process=count($processData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['finished_weight != '] = '0.000';
		$fwData = $this->rows($queryData);
		$result->finishedWeight=count($fwData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['cycle_time != '] = '00:00:00';
		$ctData = $this->rows($queryData);
		$result->cycleTime=count($ctData);
		
		$queryData = Array();
		$queryData['tableName'] = 'tool_consumption';
		$queryData['where']['item_id'] = $id;
		$toolData = $this->rows($queryData);
		$result->tool=count($toolData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->inspectionParam;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['param_type'] = 0;
		$toolData = $this->rows($queryData);
		$result->inspection=count($toolData);

		$queryData = Array();
		$queryData['tableName'] = $this->inspectionParam;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['param_type'] = 1;
		$incomingData = $this->rows($queryData);
		$result->incoming_inspection=count($incomingData);
		
		return $result;
	}
	
	public function getlastUpdatedProcess($id){
		$queryData['tableName'] = $this->productProcess;
		$queryData['select'] = 'employee_master.emp_name,product_process.updated_at';
		$queryData['leftJoin']['employee_master'] = 'product_process.updated_by = employee_master.id';
		$queryData['where']['item_id'] = $id;
		$queryData['order_by']['updated_at'] = 'DESC';
		return $this->row($queryData);
	}
	
	//CREATED BY MEGHAVI @23/06/2023
	public function getPreInspectionParam($item_id,$param_type="0"){
		$data['tableName'] = $this->inspectionParam;
		$data['select'] = "inspection_param.*,item_category.category_name,item_master.item_name";
		$data['leftJoin']['item_category'] = "item_category.id = inspection_param.measure_tech";
		$data['leftJoin']['item_master'] = "item_master.id = inspection_param.item_id";
		$data['where']['param_type']=$param_type;
		$data['where']['item_id']=$item_id;
		$data['group_by'][] = "inspection_param.id";
		return $this->rows($data);
	}
	
	public function getPreDispatchInspParam($item_id){
		$data['tableName'] = $this->productDimensions;
		$data['select'] = "product_dimensions.*,item_master.item_name,item_master.item_code";	
		$data['leftJoin']['item_master'] = "item_master.id = product_dimensions.item_id";
		$data['where']['product_dimensions.item_id'] = $item_id;
		$data['where']['product_dimensions.is_final'] = 1;
		return $this->rows($data);
	}

    //Created At 25/4/22
	public function getInspParamByRoute($item_id,$inspection_route=""){
		$data['tableName'] = $this->inspectionParam;
		$data['where']['item_id']=$item_id;
		if(!empty($inspection_route)){$data['where']['inspection_route']=$inspection_route;}
		return $this->rows($data);
	}
	
	public function savePreInspectionParam($data){
		try{
            $this->db->trans_begin();
		$result = $this->store($this->inspectionParam,$data,'Inspection Parameter');
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}

	public function checkDuplicateParam($parameter,$id=""){
        $data['tableName'] = $this->inspectionParam;
        $data['where']['parameter'] = $parameter;
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

	public function deletePreInspection($id){
		try{
            $this->db->trans_begin();
        $result = $this->trash($this->inspectionParam,['id'=>$id],"Record");
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	}	
	}
	
	public function getFgRevision($item_id){
        $data['where']['item_id'] = $item_id;
        $data['tableName'] = $this->fgRevision;
        return $this->row($data);
    }

	public function saveFgRevision($data){
		try{
            $this->db->trans_begin();
		
            $result=$this->store($this->fgRevision,$data,'product');
            $itemData =[
             'rev_no'=>$data['new_rev_no'],
             'rev_specification'=>$data['new_specs'],
             'id'=>$data['item_id']
            ];
            
            return $this->store($this->itemMaster,$itemData,'');
            if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
            endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }
    
    public function saveProductFinishedWeight($data){
		try{
            $this->db->trans_begin();
    		if(empty($data['id'])):
    		    $productWeightData = ['id'=>$data['id'],'finished_weight'=>$data['finished_weight'],'created_by'=>$data['loginId']];
    		else:
    		    $productWeightData = ['id'=>$data['id'],'finished_weight'=>$data['finished_weight'],'updated_by'=>$data['loginId']];
    		endif;
			
    		$this->store($this->productProcess,$productWeightData,'');
    		
    		$result = ['status'=>1,'message'=>'Process Finished Weight Updated successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
	}
	
	public function getProductProcessData($item_id,$process_id){
		$data['select'] = "product_process.*";
		$data['where']['item_id'] = $item_id;
		$data['where']['process_id'] = $process_id;
		$data['tableName'] = $this->productProcess;
		$result = $this->row($data);
		return $result;
	}
	
	public function getfamilyGroupList(){
        $data['tableName'] = $this->familyGroup;
        $result = $this->rows($data);
        return $result;
    }
	
	//Created By Karmi @31/12/2021
	public function getMaterialBomPrintData($id){
		$data['tableName'] = $this->itemKit;
		$data['select'] = "item_kit.*,item_master.item_name,item_master.item_name as full_name,process_master.process_name,unit_master.unit_name,itm.item_name as product_name,itm.item_code as product_code";
		$data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['item_master as itm'] = "itm.id = item_kit.item_id";
		$data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$result = $this->rows($data);
		return $result;
	}
	
	public function saveImportRM($data){
		return $this->edit($this->itemMaster,['item_name'=>$data['item_name']],['cal_agency'=>$data['cal_agency'],'other'=>$data['other'],'part_no'=>$data['part_no'],'min_qty'=>$data['min_qty'],'description'=>$data['description']]);
	}
	
	/** Created By Mansee @ 08-03-2022 */
	public function itemWiseStock($item_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "location_master.*";
		$data['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";		
		$data['where']['item_id'] = $item_id;
		$data['order_by']['id'] = "asc";
		$data['group_by'][] = "location_id";
		return $this->rows($data);
	}
	
	/*Created By : Avruti @21-3-2022 */
	public function getCalibrationList($item_id,$batch_no=""){
		$data['tableName'] = $this->calibration;
		$data['select'] = "calibration.*,employee_master.emp_name";
        $data['leftJoin']['employee_master'] = "employee_master.id = calibration.created_by";
		$data['where']['item_id'] = $item_id;
		if(!empty($batch_no)){ $data['where']['calibration.batch_no'] = $batch_no; }
		return $this->rows($data);
	}

	public function saveCalibration($data){
		$this->store($this->itemMaster,['id'=>$data['item_id'],'last_cal_date'=>$data['cal_date'],'cal_agency'=>$data['cal_by'],'next_cal_date'=>$data['next_cal_date']],'Instruments');
		unset($data['next_cal_date']);
		return $this->store($this->calibration,$data,'Calibration');
   	}

   	public function deleteCalibration($id,$item_id){
		$result = $this->trash($this->calibration,['id'=>$id],"Record");
		$calData = $this->getCalibrationData($item_id);
		$itemData = $this->item->getItem($item_id);  
		$cal_agency = (!empty($calData))?$calData->cal_by:NULL; 
		$cal_date = (!empty($calData))?$calData->cal_date:NULL; 
		$next_cal = (!empty($cal_date))? date('Y-m-d', strtotime($cal_date . "+".$itemData->cal_freq." months") ) : NULL;
		$this->store($this->itemMaster,['id'=>$item_id,'cal_agency'=>$cal_agency, 'last_cal_date'=>$cal_date,'next_cal_date'=>$next_cal],'Instruments');
		return $result;
	}

	public function getCalibrationData($item_id){
        $data['tableName'] = $this->calibration;
        $data['where']['item_id'] = $item_id;
        $data['order_by']['id'] = "DESC";
        $data['limit'] = 1;
        return $this->row($data);
	}
	
	//created By Karmi @13/06/2022
	public function getProductProcesswithCycleTime($id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['item_id'] = $id;
        $data['order_by']['product_process.sequence'] = "ASC";
		return $this->rows($data);
	}
	
	/*  Create By : Meghavi @13-08-2022
        note : Costing
    */
	public function getCostingDTRows($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
        $data['where']['item_master.item_type'] = 1;
        
        $columns = array();
    	$data['searchCol'][] = "";
        $data['searchCol'][] = "item_master.item_code";
    	$data['searchCol'][] = "";
        $data['searchCol'][] = "";
		
    	$columns =array('','item_master.item_name','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

	public function saveCosting($data){
		try{
            $this->db->trans_begin();
			foreach($data['id'] as $key=>$value):
				if($data['costing'][$key] > 0):	
					$costingData = [
						'id'=>$value,
						'costing'=>$data['costing'][$key]
					];
					$this->store($this->productProcess,$costingData,'');
				endif;
			endforeach;

			$result = ['status'=>1,'message'=>'Costing Data Updated successfully.'];
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
		}	
	}

	public function checkCostingStatus($id){
		$result = new StdClass;$result->process=0; $result->cstng=0; 	
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process=count($processData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['costing !='] = '00:00:00';
		$fwData = $this->rows($queryData);
		$result->cstng=count($fwData);
		
		return $result;
	}
	
	//Created By Avruti @14/08/2022
	public function getPreInspectionParamForDeviation($id){
		$data['where']['id']=$id;
		$data['tableName'] = $this->inspectionParam;
		return $this->row($data);
	}
	
	//Created By Avruti @08/09/2022
	public function getPreInspectionData($item_id,$type){
		$data['tableName'] = $this->inspectionParam;
		$data['select'] = "inspection_param.*";
		$data['where']['inspection_param.item_id'] = $item_id;
		$data['where']['inspection_param.test_type'] = $type;
		$result = $this->rows($data);
        return $result;
	}

	public function getPrdProcessDataProductProcessWise($data){
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $data['item_id'];
		$queryData['where']['process_id'] = $data['process_id'];
		return  $this->row($queryData);
	}

	public function getMachineTypeWiseMachine($typeof_machine=''){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['where']['item_type'] = 5;
		//$customeWhere='';
		return  $this->rows($queryData);
	}

	public function getProductOutputData($id){
		$data['tableName'] = $this->production_output;
		$data['select'] = "production_output.*,item_master.item_name,item_master.full_name";
		$data['leftJoin']['item_master'] = "item_master.id = production_output.output_item_id";
		$data['where']['production_output.item_id'] = $id;
		return $this->rows($data);
	}
	
	public function getItemDataList($postData){
		$data['tableName'] = $this->itemMaster;
	    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price,item_master.unit_id,item_master.qty,item_master.family_id,item_master.item_alias,unit_master.unit_name,material_master.color_code";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['leftJoin']['material_master'] = "material_master.material_grade=item_master.material_grade";

		if(!empty($postData['category_id'])){ $data['where']['item_master.category_id'] = $postData['category_id']; }
		return $this->rows($data);
	}

	/* Created By :- Sweta @08-08-2023 */
	public function getGroupWiseItem($sub_group){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.item_name as full_name,item_master.category_id";
		$data['where']['item_master.item_type'] = 2;
		$data['where']['item_master.sub_group'] = $sub_group;
		return $this->rows($data);
	}

	/* Created By :- Sweta @08-08-2023 */
	public function deleteToolConsumption($id){		
	    try{
            $this->db->trans_begin();
		    
		    $result = $this->trash('tool_consumption',['id'=>$id],'');
		    		    
		    if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
	    }
	}
	
	public function getItemForCalPrint($item_id){
        $data['tableName'] = $this->calibration; 
		$data['select'] = "calibration.*,item_master.item_name,item_master.item_code,item_master.next_cal_date,item_master.last_cal_date,item_master.least_count";
        $data['leftJoin']['item_master'] = "item_master.id = calibration.instrument_id";
        $data['where']['calibration.item_id'] = $item_id;
        $data['order_by']['calibration.id'] = "DESC";
        $data['limit'] = 1;
        return $this->row($data);
	}

	/*  Create By : Avruti @27-11-2021 12:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getItemList_api($data){
		$queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,unit_master.unit_name,item_category.category_name,party_master.party_code";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";

		if(!empty($data['type']))
        	$queryData['where']['item_master.item_type'] = $data['type'];

        $queryData['length'] = $data['limit'];
        $queryData['start'] = $data['start'];
		
        return $this->rows($queryData);
    }

    //------ API Code End -------//
	
}
?>