<?php
class QcChallanModel extends MasterModel{
    private $qcChallan = "qc_challan";
    private $qcChallanTrans = "qc_challan_trans";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";

    public function nextTransNo($entry_type){
        $data['tableName'] = $this->qcChallan;
        $data['select'] = "MAX(trans_no) as trans_no";
		$trans_no = $this->specificRow($data)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;        
		return $nextTransNo;
    } 
    
    public function getDTRows($data){
        $data['tableName'] = $this->qcChallanTrans;
        $data['select'] = 'qc_challan_trans.*,qc_challan.trans_prefix,qc_challan.trans_no,qc_challan.challan_type,qc_challan.trans_date,qc_challan.party_id,qc_instruments.item_name,qc_instruments.item_code,(CASE WHEN qc_challan.challan_type != 1 THEN party_master.party_name ELSE department_master.name END) as party_name';
        $data['join']['qc_challan'] = "qc_challan.id = qc_challan_trans.challan_id";
        $data['join']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $data['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $data['leftJoin']['department_master'] = "department_master.id = qc_challan.party_id";
        $data['where']['qc_challan_trans.trans_status'] = 0;
        if(!empty($data['item_type'])){ $data['where']['qc_instruments.item_type'] = $data['item_type']; }
        if(!empty($data['status']) && $data['status'] == 2){
            $data['where_in']['qc_challan.challan_type'] = '1,2';
        }else{
            $data['where']['qc_challan.challan_type'] = '3';
        }

        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "CONCAT('/',qc_challan.trans_no)";
        $data['searchCol'][] = "DATE_FORMAT(qc_challan.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "qc_challan.challan_type";
        $data['searchCol'][] = "department_master.name";
        $data['searchCol'][] = "qc_instruments.item_name";
        $data['searchCol'][] = "qc_challan_trans.item_remark";

		$columns =array('','','qc_challan.trans_no','qc_challan.trans_date','qc_challan.challan_type','department_master.name','qc_instruments.item_name','qc_challan_trans.item_remark');
		
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}	
		return $this->pagingRows($data);
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            
            if(empty($masterData['id'])):
                $masterData['trans_no'] = $this->nextTransNo(1);
                $inChallan = $this->store($this->qcChallan,$masterData);
                $mainId = $inChallan['insert_id'];
                $result = ['status'=>1,'message'=>'Challan Saved Successfully.','url'=>base_url("qcChallan")];
            else:
                $this->store($this->qcChallan,$masterData);
                $mainId = $masterData['id'];
                $challanItems = $this->getQcChallanTrans($mainId);
                
                foreach($challanItems as $row):
                    if(!in_array($row->id,$itemData['id'])):
                        $this->trash($this->qcChallanTrans,['id'=>$row->id]);
                        $this->edit('qc_instruments', ['id'=>$row->item_id], ['status'=>1]);
                    endif;
                endforeach;
    
                $result = ['status'=>1,'message'=>'Challan updated Successfully.','url'=>base_url("qcChallan")];
            endif;
    
            foreach($itemData['item_id'] as $key=>$value):
                $item = $this->qcInstrument->getItem($value);
                $transData = [
                    'id' => $itemData['id'][$key],
                    'challan_id' => $mainId,
                    'item_id' => $value,
                    'from_location' => $item->location_id,
                    'batch_no' => $itemData['batch_no'][$key],                
                    'item_remark' => $itemData['item_remark'][$key],
                    'entry_type' => $itemData['entry_type'],
                    'created_by' => $itemData['created_by']
                ];
                /** Insert Record in Delivery Transaction **/
                $saveTrans = $this->store($this->qcChallanTrans,$transData);
                
                if($masterData['challan_type'] != 3):
                    $this->edit('qc_instruments', ['id'=>$value], ['status'=>2,'is_fresh'=>2]);
                else:
                    $this->edit('qc_instruments', ['id'=>$value], ['status'=>3,'is_fresh'=>2]);
                endif;
                
                $setData = Array();
                $setData['tableName'] = $this->qcChallan;
                $setData['where']['id'] = $mainId;
                $setData['set']['qty'] = 'qty, + 1 ';
                $this->setValue($setData);
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

    public function getQcChallan($id){
        $queryData['tableName'] = $this->qcChallan;
        $queryData['where']['id'] = $id;
        $challanData = $this->row($queryData);
        $challanData->itemData = $this->getQcChallanTrans($id);
        return $challanData;
    }

    public function getQcChallanTrans($id){
        $queryData = array();
        $queryData['tableName'] = $this->qcChallanTrans;
        $queryData['select'] = 'qc_challan_trans.*,qc_instruments.item_name,qc_instruments.item_code';
        $queryData['join']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $queryData['where']['challan_id'] = $id;
        return $this->rows($queryData);
    }
    
    public function getQcChallanTransRow($id){
        $queryData = array();
        $queryData['tableName'] = $this->qcChallanTrans;
        $queryData['select'] = 'qc_challan_trans.*,qc_challan.party_id,qc_instruments.item_name,qc_instruments.item_code,party_master.party_name';
        $queryData['join']['qc_challan'] = "qc_challan.id = qc_challan_trans.challan_id";
        $queryData['join']['qc_instruments'] = "qc_challan_trans.item_id = qc_instruments.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = qc_challan.party_id";
        $queryData['where']['qc_challan_trans.id'] = $id;
        return $this->row($queryData);
    }

    public function getInstrumentForChallan($id){
        $data['tableName'] = 'qc_instruments';
        $data['where_in']['id'] = str_replace("~", ",", $id);
        return $this->rows($data);
    }

    public function deleteChallan($id){
        $transData = $this->getQcChallanTrans($id);
        foreach($transData as $row):    
            $this->trash($this->qcChallanTrans,['id'=>$row->id]);
            $this->edit('qc_instruments', ['id'=>$row->item_id], ['status'=>1]);
        endforeach;
        return $this->trash($this->qcChallan,['id'=>$id],'Challan');
    }

    public function saveReturnChallan($data){
        try{
            $this->db->trans_begin();
            
                //$this->edit('qc_instruments', ['id'=>$data['item_id']], ['status'=>1]);

                $update = [
                    'receive_by'=>$data['receive_by'],
                    'receive_at'=>$data['receive_at'],
                    'to_location'=>$data['to_location'],
                    'in_ch_no'=>$data['in_ch_no'],
                    //'trans_status'=>1
                ];
                $this->edit($this->qcChallanTrans, ['id'=>$data['id']], $update);
                
                // $setData = Array();
                // $setData['tableName'] = $this->qcChallan;
                // $setData['where']['id'] = $data['challan_id'];
                // $setData['set']['receive_qty'] = 'receive_qty, + 1 ';
                // $this->setValue($setData);
    
                $result = ['status'=>1,'message'=>'Challan Return Successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	 
    }

    public function confirmChallan($data){
        try{
            $this->db->trans_begin();
            
                $this->edit('qc_instruments', ['id'=>$data['item_id']], ['status'=>1]);

                $update = [
                    //'receive_by'=>$data['receive_by'],
                    //'receive_at'=>$data['receive_at'],
                    //'to_location'=>$data['to_location'],
                    //'in_ch_no'=>$data['in_ch_no'],
                    'trans_status'=>1
                ];
                $this->edit($this->qcChallanTrans, ['id'=>$data['id']], ['trans_status'=>1]);
                
                $setData = Array();
                $setData['tableName'] = $this->qcChallan;
                $setData['where']['id'] = $data['challan_id'];
                $setData['set']['receive_qty'] = 'receive_qty, + 1 ';
                $this->setValue($setData);
    
                $result = ['status'=>1,'message'=>'Challan Confirm Successfully.'];
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	 
    }
}
?>