<?php
class InChallanModel extends MasterModel{
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
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.doc_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$data['where']['in_out_challan.challan_type'] = 1;
        $data['where']['in_out_challan.challan_date >= '] = $this->startYearDate;
        $data['where']['in_out_challan.challan_date <= '] = $this->endYearDate;

        $data['searchCol'][] = "CONCAT('/',in_out_challan.doc_no)";
        $data['searchCol'][] = "DATE_FORMAT(in_out_challan.challan_date,'%d-%m-%Y')";
        $data['searchCol'][] = "in_out_challan.party_name";
        $data['searchCol'][] = "in_out_challan_trans.item_name";
        $data['searchCol'][] = "in_out_challan_trans.qty";

		$columns =array('','','in_out_challan.doc_no','in_out_challan.challan_date','in_out_challan.party_name','in_out_challan_trans.item_name','in_out_challan_trans.qty','in_out_challan_trans.item_remark');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(empty($masterData['id'])):
                $masterData['challan_no'] = $this->nextTransNo(1);
                $inChallan = $this->store($this->transMain,$masterData);
                $mainId = $inChallan['insert_id'];
                $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("inChallan")];
            else:
                $this->store($this->transMain,$masterData);
                $mainId = $masterData['id'];
                $challanItems = $this->getInChallanTrans($mainId);
                $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("inChallan")];
            endif;

            foreach($itemData['item_name'] as $key=>$value):
        
                $transData = [
                    'id' => $itemData['id'][$key],
                    'in_out_ch_id' => $mainId,
                    'item_name' => $value, 
                    'qty' => $itemData['qty'][$key],               
                    'is_returnable' => $itemData['is_returnable'][$key],         
                    'item_remark' => $itemData['item_remark'][$key],
                    'created_by' => $masterData['created_by']
                ];
                /** Insert Record in Delivery Transaction **/
                $saveTrans = $this->store($this->transChild,$transData);
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

    public function getInChallanTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['in_out_ch_id'] = $id;
        return $this->rows($queryData);
    }

    public function getInChallanTransRow($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['id'] = $id;
        return $this->row($queryData);
    }

    public function getInChallan($id){
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getInChallanTrans($id);
        return $challanData;
    }

    public function deleteChallan($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getInChallanTrans($id);
            foreach($transData as $row):    
                $this->trash($this->transChild,['id'=>$row->id]);
            endforeach;
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $this->trash($this->transMain,['id'=>$id],'Challan');
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }

    public function getCustomerSalesOrder($party_id){
        $data['tableName'] = "trans_main";
        $data['select'] = "trans_main.id,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date";
        $data['where']['party_id'] = $party_id;
        $data['where']['trans_status'] = 0;
        $data['where']['entry_type'] = 4;
        return $this->rows($data);
    }

    public function getReturnItemTrans00($data){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['where']['ref_id'] = $data['ref_id'];
        $queryData['where']['item_id'] = $data['item_id'];
        $queryData['where']['trans_type'] = $data['trans_type'];
        $queryData['where']['ref_type'] = 11;
        $queryData['where']['qty < '] = 0;
        $itemTrans = $this->rows($queryData);

        $htmlData = "";
        if(!empty($itemTrans)):
            $i=1;
            
            foreach($itemTrans as $row):
                $deleteBtn = '<button type="button" onclick="trashReturnItem('.$row->id.','.abs($row->qty).');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $htmlData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->ref_date)).'</td>
                    <td>'.$row->batch_no.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        endif;

        return ['status'=>1,'result'=>$itemTrans,'resultHtml'=>$htmlData];
    }

    public function getReturnItemTrans($data){ 
        $queryData['tableName'] = $this->instrumentsReturn;
        $queryData['where']['challan_trans_id'] = $data['challan_trans_id'];
        $itemTrans = $this->rows($queryData);

        $htmlData = "";
        if(!empty($itemTrans)):
            $i=1;
            foreach($itemTrans as $row):
                $deleteBtn = '<button type="button" onclick="trashReturnItem('.$row->id.','.$row->return_qty.');" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>';
                $htmlData .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.date("d-m-Y",strtotime($row->return_date)).'</td>
                    <td>'.$row->return_qty.'</td>
                    <td>'.$deleteBtn.'</td>
                </tr>';
            endforeach;
        endif;
        return ['status'=>1,'result'=>$itemTrans,'resultHtml'=>$htmlData];
    }

    public function saveReturnItem($data){    
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
        $setData['tableName'] = $this->transChild;
        $setData['where']['id'] = $data['ref_id'];
        $setData['set']['return_qty'] = 'return_qty, + '.$data['qty'];
        $this->setValue($setData);

        $data['qty'] = "-".$data['qty'];
        $result['resultHtml'] = $this->getReturnItemTrans($data)['resultHtml'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];

    }	
    }

    public function deleteReturnItem($id){
        try{
            $this->db->trans_begin();
            $queryData['tableName'] = $this->instrumentsReturn;
            $queryData['where']['id'] = $id;
            $transRow = $this->row($queryData);  

            /** Update Item Stock **/
            $setData = Array();
            $setData['tableName'] = $this->instrumentsReturn;
            $setData['where']['id'] = $transRow->challan_trans_id;
            $setData['set']['return_qty'] = 'return_qty, - '.abs($transRow->return_qty);
            $this->setValue($setData);

            $setData = Array();
            $setData['tableName'] = $this->transChild;
            $setData['where']['id'] = $transRow->challan_trans_id;
            $setData['set']['return_qty'] = 'return_qty, - '.abs($transRow->return_qty);
            $this->setValue($setData);

            $result = $this->trash($this->instrumentsReturn,['id'=>$id],"Record");
            $result['resultHtml'] = $this->getReturnItemTrans(['challan_trans_id'=>$transRow->challan_trans_id])['resultHtml'];
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

    public function getInChallanList_api($limit, $start,$type=0){
        $data['tableName'] = $this->transChild;
        $data['select'] = 'in_out_challan_trans.*,in_out_challan.id as trans_main_id,in_out_challan.challan_prefix,in_out_challan.challan_no,in_out_challan.doc_no,in_out_challan.challan_type,in_out_challan.challan_date,in_out_challan.party_id,in_out_challan.party_name';
        $data['join']['in_out_challan'] = "in_out_challan.id = in_out_challan_trans.in_out_ch_id";
		$data['where']['in_out_challan.challan_type'] = 1;
		
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>