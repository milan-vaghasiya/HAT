<?php
class StoreModel extends MasterModel{
    private $locationMaster = "location_master";
    private $itemMaster = "item_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->locationMaster;
        $data['order_by']['store_type'] = 'DESC';
        $data['searchCol'][] = "store_name";
        $data['searchCol'][] = "location";
        $data['serachCol'][] = "remark";
		$columns =array('','','store_name','location','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getStoreNames(){
        $data['tableName'] = $this->locationMaster;
        $data['select'] = "DISTINCT(store_name)";
        return $this->rows($data);
    }
    
    public function getStoreLocationList($customQry=""){
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        if(!empty($customQry)){$squery['customWhere'][] = $customQry;}
        $storeList = $this->rows($squery);
        
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }
    
	public function getStoreLocationListWithoutProcess($customQry=""){
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        if(!empty($customQry)){$squery['customWhere'][] = $customQry;}
        $storeList = $this->rows($squery);
        
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['location_master.ref_id'] = 0;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }
    
    public function getStoreLocationListGRN(){
        $locationList = array();
        $squery['tableName'] = $this->locationMaster;
        $squery['select'] = "DISTINCT(store_name)";
        $storeList = $this->rows($squery);
        
        if(!empty($storeList))
        {
            $i=0;
            foreach($storeList as $store)
            {
                $locationList[$i]['store_name'] = $store->store_name;
                $data['tableName'] = $this->locationMaster;
                $data['where']['location_master.ref_id'] = 0;
                $data['where']['store_name'] = $store->store_name;
                $locationList[$i++]['location'] =  $this->rows($data);
            }
        }
        return $locationList;
    }

    public function getStoreLocation($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->locationMaster;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            $data['store_name'] = trim($data['store_name']);
            $data['location'] = trim($data['location']);
            if($this->checkDuplicate($data['store_name'],$data['location'],$data['id']) > 0):
                $errorMessage['location'] = "Location is duplicate.";
                $result = ['status'=>0,'message'=>$errorMessage];
            else:
                $result = $this->store($this->locationMaster,$data,'Store');
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

    public function checkDuplicate($storename,$location,$id=""){
        $data['tableName'] = $this->locationMaster;
        $data['where']['store_name'] = $storename;
        $data['where']['location'] = $location;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $result = $this->trash($this->locationMaster,['id'=>$id],'Store');
            if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function getItemWiseStock($data){	
		
		$itmData = $this->item->getItem($data['item_id']);
		
		$tbody = '';
        $i=1;$cStock=0;
		
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "location_master.store_name,location_master.location,stock_transaction.location_id,stock_transaction.batch_no,SUM(stock_transaction.qty) as qty";
        $queryData['leftJoin']['location_master'] = "location_master.id = stock_transaction.location_id";
        $queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        $queryData['where']['stock_transaction.location_id != '] = $this->MIS_PLC_STORE->id;
        $queryData['having'][] = 'SUM(stock_transaction.qty) <> 0';
        $queryData['order_by']['stock_transaction.id'] = "asc";
        $queryData['group_by'][] = "stock_transaction.location_id,stock_transaction.batch_no";
        $result = $this->rows($queryData);

        foreach($result as $row):
            $stfParam = "{'location_id':".$row->location_id.",'item_id':".$data['item_id'].",'stock_qty':".floatVal($row->qty).",'batch_no':'".$row->batch_no."','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
            $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer('.$stfParam.');"><i class="ti-control-shuffle" ></i></a>';
            $actionBtn = '';//getActionButton($stfBtn);
            $tbody .= '<tr>';
                $tbody .= '<td class="text-center">'.$actionBtn.'</td>';
                $tbody .= '<td class="text-center">'.$i++.'</td>';
                $tbody .= '<td>'.$row->store_name.'</td>';
                $tbody .= '<td>'.$row->location.'</td>';
                $tbody .= '<td>'.$row->batch_no.'</td>';
                $tbody .= '<td>'.floatVal($row->qty).'</td>';
            $tbody .= '</tr>';
            $cStock+=$row->qty;
        endforeach;

		/* $locationData = $this->store->getStoreLocationList();
        if(!empty($locationData))
		{
			foreach($locationData as $lData)
			{
				// $tbody = '<tr><th colspan="5">'.$lData['store_name'].'</th></tr>';
				foreach($lData['location'] as $batch):
					$queryData['tableName'] = "stock_transaction";
					$queryData['select'] = "SUM(qty) as qty,batch_no";
					$queryData['where']['item_id'] = $data['item_id'];
					$queryData['where']['location_id'] = $batch->id;
					$queryData['order_by']['id'] = "asc";
					$queryData['group_by'][] = "batch_no";
					$result = $this->rows($queryData);
					if(!empty($result))
					{
						foreach($result as $row)
						{
                            if(floatVal($row->qty) != 0):
                                $stfParam = "{'location_id':".$batch->id.",'item_id':".$data['item_id'].",'stock_qty':".floatVal($row->qty).",'batch_no':'".$row->batch_no."','modal_id' : 'modal-md', 'form_id' : 'stockTransfer', 'title' : 'Stock Transfer','fnSave' : 'saveStockTransfer'}";
                                $stfBtn = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Stock Transfer" flow="down" onclick="stockTransfer('.$stfParam.');"><i class="ti-control-shuffle" ></i></a>';
                                //if($batch->id == $this->PROD_STORE->id){$stfBtn = '';}
                                $actionBtn = getActionButton($stfBtn);
                                $tbody .= '<tr>';
                                    $tbody .= '<td class="text-center">'.$actionBtn.'</td>';
                                    $tbody .= '<td class="text-center">'.$i++.'</td>';
                                    $tbody .= '<td>'.$lData['store_name'].'</td>';
                                    $tbody .= '<td>'.$batch->location.'</td>';
                                    $tbody .= '<td>'.$row->batch_no.'</td>';
                                    $tbody .= '<td>'.floatVal($row->qty).'</td>';
                                $tbody .= '</tr>';
                                $cStock+=$row->qty;
                            endif;
						}
					}
				endforeach;
			}
		} */
		
		$thead = '<tr><th colspan="5">Product : ('.$itmData->item_code.') '.$itmData->item_name.'</th><th class="text-center">'.$cStock.'</th></tr>
					<tr>
                        <th style="width:5%;">Action</th>
						<th>#</th>
						<th style="text-align:left !important;">Store</th>
						<th>Location</th>
						<th>Batch</th>
						<th>Current Stock</th>
					</tr>';
        return ['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody];
    }

    // Get Sngle Item Stock From Stock Transaction
    public function getItemCurrentStock($item_id,$location_id=""){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
        if(!empty($location_id)){$queryData['where']['location_id'] = $location_id;}
        return $this->row($queryData);
    }
    
    // Created By JP @ 09082022 11:15 AM
    public function getItemStockGeneral($postData){
        if(!empty($postData['item_id']))
        {
            $queryData['tableName'] = "stock_transaction";
            $queryData['select'] = "SUM(qty) as qty";
            $queryData['where']['item_id'] = $postData['item_id'];
            if(!empty($postData['location_id'])){$queryData['where']['location_id'] = $postData['location_id'];}
            if(!empty($postData['batch_no'])){$queryData['where']['batch_no'] = $postData['batch_no'];}
			if(!empty($postData['tc_no'])){$queryData['where']['tc_no'] = $postData['tc_no'];}
            if(!empty($postData['ref_batch'])){$queryData['where']['ref_batch'] = $postData['ref_batch'];}
            return $this->row($queryData);
        }
    }

    public function checkBatchWiseStock($data){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['location_id'] = $data['from_location_id'];
        $queryData['where']['batch_no'] = $data['batch_no'];        
        if(!empty($data['ref_type']))
            $queryData['where']['ref_type'] = $data['ref_type'];
        $queryData['where']['is_delete'] = 0;
        return $this->row($queryData);
    }

    public function saveStockTransfer($data){
        try{
            $this->db->trans_begin();
            $fromTrans = [
                'id' => "",
                "location_id" => $data['from_location_id'],
                "batch_no" => $data['batch_no'],
                "trans_type" => 2,
                "item_id" => $data['item_id'],
                "qty" => "-".$data['transfer_qty'],
                "ref_type" => "9",
                "ref_id" => $data['from_location_id'],
                "ref_date" => date("Y-m-d"),
                "created_by" => $data['created_by'],
                "created_at" => date('Y-m-d H:i:s')
            ];
            $this->store('stock_transaction',$fromTrans);
    
            $toTrans = [
                'id' => "",
                "location_id" => $data['to_location_id'],
                "batch_no" => $data['batch_no'],
                "trans_type" => 1,
                "item_id" => $data['item_id'],
                "qty" => $data['transfer_qty'],
                "ref_type" => "9",
                "ref_id" => $data['from_location_id'],
                "ref_date" => date("Y-m-d"),
                "ref_batch"=>(!empty($data['transfer_reason'])?$data['transfer_reason']:''),
                "created_by" => $data['created_by'],
                "created_at" => date('Y-m-d H:i:s')
            ];
            $this->store('stock_transaction',$toTrans);
    
            $result = ['status'=>1,'message'=>"Stock Transfer successfully."];
            if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    	}	
    }

    public function getItemStock($item_id){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        $queryData['where']['item_id'] = $item_id;
        return $this->row($queryData);
    }

    public function getItemStockRTD($item_id,$item_type=''){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "SUM(qty) as qty";
        if(!empty($item_type) AND $item_type == 1){$queryData['where']['location_id'] = $this->RTD_STORE->id;}
        if(!empty($item_type) AND $item_type == 3){$queryData['where']['stock_effect'] = 1;}
        $queryData['where']['item_id'] = $item_id;
        $result = $this->row($queryData);
        //print_r($this->db->last_query());
        return $result;
    }
    
    /** Created BY Mansee @ 05-04-2022 */
    public function getProcessStoreLocationList(){
        $data['tableName'] = $this->locationMaster;
        $data['where']['location_master.ref_id >'] = 0;
        return  $this->rows($data);
    }
	
    public function getStockDTRows($data){ 
        $data['tableName'] = 'stock_transaction';
        $data['select'] = 'stock_transaction.*,SUM(stock_transaction.qty) as current_stock,location_master.store_name,location_master.location,item_master.item_name,item_master.item_code';
        $data['join']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['join']['item_master'] = 'item_master.id = stock_transaction.item_id';
        $data['where']['item_master.item_type'] = $data['item_type'];
        $data['where']['location_master.ref_id'] = 0;
        $data['customWhere'][] = ' location_master.store_type NOT IN(7,8)';
        $data['group_by'][] = 'stock_transaction.item_id';
        $data['group_by'][] = 'stock_transaction.location_id';
        $data['group_by'][] = 'stock_transaction.batch_no';

        $data['searchCol'][] = 'item_master.item_name';
        $data['searchCol'][] = 'location_master.location';
        $data['searchCol'][] = 'stock_transaction.batch_no';
        $data['searchCol'][] = 'stock_transaction.qty';
        
		$columns =array('','item_master.item_name','location_master.location','stock_transaction.batch_no','stock_transaction.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    /* Use Only Delivery Challan, Sales Invoice and Credit Note*/
    public function batchWiseItemStock($data){
        $item_id = $data['item_id'];$location_id="(".$this->RTD_STORE->id.",".$this->SCRAP_STORE->id.")";

		if(!empty($data['batch_no']) && !empty($data['trans_id'])):
            $where = "WHERE ((st.batch_no IN ('".implode("','",$data['batch_no'])."') AND st.location_id IN (".implode(",",$data['location_id']).")) OR  st.qty > 0)";
        else:
            $where = "WHERE st.qty > 0";
        endif;

        $result = $this->db->query("SELECT st.* FROM (
            SELECT SUM(stock_transaction.qty) AS qty, stock_transaction.batch_no, stock_transaction.tc_no, stock_transaction.ref_batch, stock_transaction.trans_ref_id, stock_transaction.location_id, location_master.store_name, location_master.location
            FROM stock_transaction 
            LEFT JOIN location_master ON location_master.id = stock_transaction.location_id
            WHERE stock_transaction.item_id = $item_id
            AND stock_transaction.location_id IN $location_id
            AND stock_transaction.is_delete = 0
            GROUP BY stock_transaction.batch_no, stock_transaction.location_id, stock_transaction.tc_no, stock_transaction.ref_batch
            ORDER BY stock_transaction.id ASC
        ) as st $where")->result();        
       
        $i=1;$tbody="";
        if(!empty($result)):
            $batch_no = array();$batch_qty = array();$location_id = array();
            $batch_no = (!is_array($data['batch_no']))?explode(",",$data['batch_no']):$data['batch_no'];
            $batch_qty = (!is_array($data['batch_qty']))?explode(",",$data['batch_qty']):$data['batch_qty'];
            $location_id = (!is_array($data['location_id']))?explode(",",$data['location_id']):$data['location_id'];

            foreach($result as $row):                
                if($row->qty > 0 || !empty($batch_no) && in_array($row->batch_no,$batch_no)):
                    if(!empty($batch_no) && in_array($row->batch_no,$batch_no) && in_array($row->location_id,$location_id)):
                        $qty = 0;
                        $qty = $batch_qty[array_search($row->batch_no,$batch_no)];
                        $cl_stock = (!empty($data['trans_id']))?floatVal($row->qty + $qty):floatVal($row->qty);
                    else:
                        $qty = "0";
                        $cl_stock = floatVal($row->qty);
                    endif;                                
                    
                    $tbody .= '<tr>';
                        $tbody .= '<td class="text-center">'.$i.'</td>';
                        $tbody .= '<td>['.$row->store_name.'] '.$row->location.'</td>';
                        $tbody .= '<td>'.$row->batch_no.'</td>';
						$tbody .= '<td>'.$row->tc_no.(!empty($row->ref_batch)? ' / '.$row->ref_batch:'').'</td>';
                        $tbody .= '<td>'.floatVal($row->qty).'</td>';
                        $tbody .= '<td>
                            <input type="number" name="batch_quantity[]" class="form-control batchQty" data-rowid="'.$i.'" data-cl_stock="'.$cl_stock.'" min="0" value="'.$qty.'" />
                            <input type="hidden" name="ref_batch[]" id="ref_batch'.$i.'" value="'.$row->ref_batch.'" />
							<input type="hidden" name="tc_number[]" id="tc_no'.$i.'" value="'.$row->tc_no.'" />
							<input type="hidden" name="batch_number[]" id="batch_number'.$i.'" value="'.$row->batch_no.'" />
                            <input type="hidden" name="location[]" id="location'.$i.'" value="'.$row->location_id.'" />
                            <input type="hidden" name="packing_transid[]" value="'.$row->trans_ref_id.'">
                            <div class="error batch_qty'.$i.'"></div>
                        </td>';
                    $tbody .= '</tr>';
                    $i++;
                endif;
            endforeach;
        else:
            $tbody = '<tr><td class="text-center" colspan="6">No Data Found.</td></tr>';
        endif;

        return ['status'=>1,'batchData'=>$tbody];
    }

    public function getItemStockBatchWise($data)
    {
        $stock_effect = (isset($data['stock_effect']))?$data['stock_effect']:1;

        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "stock_transaction.item_id,stock_transaction.tc_no, item_master.item_code, item_master.item_name, item_master.full_name as item_full_name, SUM(stock_transaction.qty) as qty, stock_transaction.batch_no, stock_transaction.ref_batch, stock_transaction.location_id, lm.location, lm.store_name";
		
		$queryData['leftJoin']['location_master as lm'] = "lm.id=stock_transaction.location_id";
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";

        $queryData['where']['stock_transaction.stock_effect'] = $stock_effect;

        if(!empty($data['item_id'])): 
            $queryData['where']['stock_transaction.item_id'] = $data['item_id'];           
        endif;

        if(!empty($data['location_id'])):
            $queryData['where']['stock_transaction.location_id'] = $data['location_id'];
        endif;

        if(!empty($data['batch_no'])):
            $queryData['where']['stock_transaction.batch_no'] = $data['batch_no'];
        endif;      
        if(!empty($data['tc_no'])):
            $queryData['where']['stock_transaction.tc_no'] = $data['tc_no'];
        endif;    
        if(!empty($data['ref_type'])):
            $queryData['where']['stock_transaction.ref_type'] = $data['ref_type'];
        endif;

        if(!empty($data['ref_id'])):
            $queryData['where']['stock_transaction.ref_id'] = $data['ref_id'];
        endif;
        
        if(!empty($data['stock_required'])):
            $queryData['having'][] = 'SUM(stock_transaction.qty) > 0';
        endif;

		$queryData['where']['lm.ref_id'] = 0;

        if(isset($data['group_by'])){
            $queryData['group_by'][] = $data['group_by'];
        }else{
            $queryData['group_by'][] = "stock_transaction.location_id";
            $queryData['group_by'][] = "stock_transaction.batch_no";
            $queryData['group_by'][] = "stock_transaction.item_id";
            $queryData['order_by']['lm.location'] = "ASC";
        }
        

        if(isset($data['single_row']) && $data['single_row'] == 1):
            $stockData = $this->row($queryData);
        else:
		    $stockData = $this->rows($queryData);
        endif;
        return $stockData;
    }
		
    public function getNextStoreLevel($ref_id){
        $data['tableName'] = $this->locationMaster;
        $data['where']['ref_id'] = $ref_id;
        return $this->rows($data);
    }

	/*  Create By : Avruti @27-11-2021 2:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->locationMaster;
        return $this->numRows($data);
    }

    public function getStoreLocationList_api($limit, $start){
        $data['tableName'] = $this->locationMaster;
        $data['order_by']['store_type'] = 'DESC';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
    
    
}
?>