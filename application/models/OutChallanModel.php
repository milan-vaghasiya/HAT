<?php
class OutChallanModel extends MasterModel{
    private $transMain = "in_out_challan";
    private $transChild = "in_out_challan_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $instrumentsReturn = "instruments_return";

    public function nextTransNo($entry_type){
        $data['select'] = "MAX(challan_no) as challan_no";
        $data['where']['challan_type'] = $entry_type;
        $data['where']['challan_date >= '] = $this->startYearDate;
        $data['where']['challan_date <= '] = $this->endYearDate;
        $data['tableName'] = $this->transMain;
		$trans_no = $this->specificRow($data)->challan_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    }
    
    
	public function getDTRows($data){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name,employee_master.emp_code as collected_code,employee_master.emp_name as collected_name,process_master.process_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
        $data['leftJoin']['employee_master'] = "employee_master.id = in_out_challan_trans.collected_by";
        $data['leftJoin']['process_master'] = "process_master.id = in_out_challan_trans.process_id";
		$data['where']['in_out_challan.challan_type'] = 2;
        $data['where']['in_out_challan.challan_date >= '] = $this->startYearDate;
        $data['where']['in_out_challan.challan_date <= '] = $this->endYearDate;

        $data['searchCol'][] = "CONCAT('/',in_out_challan.challan_no)";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "in_out_challan.party_name";
        $data['searchCol'][] = "in_out_challan_trans.item_name";
        $data['searchCol'][] = "in_out_challan_trans.qty";

		$columns =array('','','in_out_challan.challan_no','in_out_challan.challan_date','in_out_challan.party_name','in_out_challan_trans.item_name','in_out_challan_trans.qty','in_out_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function challanTransRow($trans_id){
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $trans_id;
        return $this->row($queryData);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
        if(empty($masterData['id'])):
            $masterData['challan_no'] = $this->nextTransNo(2);
            $outChallan = $this->store($this->transMain,$masterData);
            $mainId = $outChallan['insert_id'];
            $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("outChallan")];
        else:
            $this->store($this->transMain,$masterData);
            $mainId = $masterData['id'];
            $challanItems = $this->getOutChallanTrans($mainId);
            foreach($challanItems as $row):
                /** Update Item Stock **/
                $setData = Array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->item_id;
                $setData['set']['qty'] = 'qty, + '.$row->qty;
                $this->setValue($setData);

                /** Remove Stock Transaction **/
                $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>12]);

                if(!in_array($row->id,$itemData['id'])):
                    $this->trash($this->transChild,['id'=>$row->id]);
                endif;
            endforeach;

            $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("outChallan")];
        endif;

        foreach($itemData['item_id'] as $key=>$value):
       
            $transData = [
                'id' => $itemData['id'][$key],
                'in_out_ch_id' => $mainId,
                'item_id' => $value,
                'item_name' => $itemData['item_name'][$key], 
                'collected_by' => $itemData['collected_by'][$key],
                'process_id' => $itemData['process_id'][$key],
                'qty' => $itemData['qty'][$key],
                // 'unit_id' => $itemData['unit_id'][$key],
                // 'unit_name' => $itemData['unit_name'][$key],
                'is_returnable' => $itemData['is_returnable'][$key],
                'location_id' => $itemData['location_id'][$key],
                // 'batch_no' => $itemData['batch_no'][$key],                
                'item_remark' => $itemData['item_remark'][$key],
                'created_by' => $itemData['created_by']
            ];
            /** Insert Record in Delivery Transaction **/
            $saveTrans = $this->store($this->transChild,$transData);
            $refID = (empty($itemData['id'][$key]))?$saveTrans['insert_id']:$itemData['id'][$key];            

            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $itemData['item_id'][$key];
            $setData['set']['qty'] = 'qty, - '.$itemData['qty'][$key];
            $this->setValue($setData);

            /*** UPDATE STOCK TRANSACTION DATA ***/
            $stockQueryData['id']="";
            $stockQueryData['location_id']=$itemData['location_id'][$key];
            if(!empty($itemData['batch_no'][$key])){$stockQueryData['batch_no'] = $itemData['batch_no'][$key];}
            $stockQueryData['trans_type']=2;
            $stockQueryData['item_id']=$itemData['item_id'][$key];
            $stockQueryData['qty'] = "-".$itemData['qty'][$key];
            $stockQueryData['ref_type']=12;
            $stockQueryData['ref_id']=$refID;
            $stockQueryData['ref_no']=getPrefixNumber($masterData['challan_prefix'],$masterData['challan_no']);
            $stockQueryData['ref_date']=$masterData['challan_date'];
            $stockQueryData['created_by']=$this->loginID;
            $this->store($this->stockTrans,$stockQueryData);
        endforeach;

        
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getOutChallanTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "in_out_challan_trans.*,employee_master.emp_code as collected_code,employee_master.emp_name as collected_name,process_master.process_name";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = in_out_challan_trans.collected_by";
        $queryData['leftJoin']['process_master'] = "process_master.id = in_out_challan_trans.process_id";
        $queryData['where']['in_out_ch_id'] = $id;
        return $this->rows($queryData);
    }

    public function getOutChallanTransRow($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getOutChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getOutChallanTrans($id);
        return $challanData;
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();
        $transData = $this->getOutChallanTrans($id);
        foreach($transData as $row):    
            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $row->item_id;
            $setData['set']['qty'] = 'qty, + '.$row->qty;
            $this->setValue($setData);

            /** Remove Stock Transaction **/
            $this->remove($this->stockTrans,['ref_id'=>$row->id,'trans_type'=>2,'ref_type'=>12]);
            $this->trash($this->transChild,['id'=>$row->id]);
        endforeach;
        $result = $this->trash($this->transMain,['id'=>$id],'Challan');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getReceiveItemTrans($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['trans_type'] = $data['trans_type'];
        $queryData['where']['ref_type'] = 12;
        $queryData['where']['qty > '] = 0;
        $itemTrans = $this->rows($queryData);

        $htmlData = "";
        if(!empty($itemTrans)):
            $i=1;
            
            foreach($itemTrans as $row):
                $deleteBtn = '<button type="button" onclick="trashReceiveItem('.$row->id.','.abs($row->qty).');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $htmlData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->ref_date)).'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        endif;

        return ['status'=>1,'result'=>$itemTrans,'resultHtml'=>$htmlData];
    }

    public function saveReceiveItem($data){   
        try{
            $this->db->trans_begin();  
            $stockReturnData = [
                'id'=>'',
                'challan_trans_id'=>$data['challan_trans_id'],
                'challan_type'=> 1,
                'return_date'=>$data['ref_date'],
                'return_qty'=>$data['qty'],
                'created_by'=>$data['created_by']
            ];
            $result = $this->store($this->instrumentsReturn,$stockReturnData);

           /** Update Item Stock **/
           $setData = Array();
           $setData['tableName'] = $this->itemMaster;
           $setData['where']['id'] = $data['item_id'];
           $setData['set']['qty'] = 'qty, + '.$data['qty'];
           $this->setValue($setData);
    
           unset($data['challan_trans_id']);
           $setData = Array();
           $setData['tableName'] = $this->transChild;
           $setData['where']['id'] = $data['ref_id'];
           $setData['set']['return_qty'] = 'return_qty, + '.$data['qty'];
           $this->setValue($setData);
    
           $result = $this->store($this->stockTrans,$data,"Record");
           $result['resultHtml'] = $this->getReceiveItemTrans($data)['resultHtml'];
           
           if ($this->db->trans_status() !== FALSE):
               $this->db->trans_commit();
               return $result;
           endif;
       }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
       }	
   }

    public function deleteReceiveItem($id){
        try{
            $this->db->trans_begin();
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['id'] = $id;
        $transRow = $this->row($queryData);

        /** Update Item Stock **/
        $this->edit($this->instrumentsReturn,['ref_id'=>$transRow->id],['is_delete'=>1]);
        $setData = Array();
        $setData['tableName'] = $this->itemMaster;
        $setData['where']['id'] = $transRow->item_id;
        $setData['set']['qty'] = 'qty, - '.abs($transRow->qty);
        $this->setValue($setData);

        $setData = Array();
        $setData['tableName'] = $this->transChild;
        $setData['where']['id'] = $transRow->ref_id;
        $setData['set']['return_qty'] = 'return_qty, - '.abs($transRow->qty);
        $this->setValue($setData);

        $result = $this->remove($this->stockTrans,['id'=>$id],"Record");
        $result['resultHtml'] = $this->getReceiveItemTrans(['ref_id'=>$transRow->ref_id,'item_id'=>$transRow->item_id,'trans_type'=>$transRow->trans_type])['resultHtml'];
        
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @29-11-2021 10:10 AM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){        
        $data['tableName'] = $this->transChild;
        return $this->numRows($data);
    }

    public function getOutChallanList_api($limit, $start,$type=0){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$data['where']['in_out_challan.challan_type'] = 2;
        
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>