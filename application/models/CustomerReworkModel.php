<?php
class CustomerReworkModel extends MasterModel{
  
    private $customer_rework = "customer_rework";
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $stockTrans = "stock_transaction";
    
    public function getDTRows($data){ 
        $data['tableName'] = $this->customer_rework;
        $data['select'] = "customer_rework.*,trans_main.trans_number AS inv_no,party_master.party_name,item_master.item_name,item_master.full_name";
        $data['leftJoin']['trans_main'] = "trans_main.id = customer_rework.inv_id";
        $data['leftJoin']['party_master'] = "party_master.id = customer_rework.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = customer_rework.item_id";
        $data['where']['customer_rework.status'] = $data['status'];
        
      
        $data['order_by']['customer_rework.trans_date'] = "DESC";

        $data['searchCol'][] = "";
        $data['searchCol'][] = "customer_rework.trans_number";
        $data['searchCol'][] = "DATE_FORMAT(customer_rework.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "customer_rework.qty";
        $data['searchCol'][] = "customer_rework.remark";

        
        $columns =array('','',"customer_rework.trans_number",'customer_rework.trans_date','party_master.party_name','trans_main.trans_number','item_master.item_name','customer_rework.qty',"customer_rework.remark");

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getSalesInvoiceList($party_id){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.*';
        $data['leftJoin']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
        $data['where']['(trans_child.qty-trans_child.rework_qty) > ']=0;
        $data['where']['party_id'] = $party_id;
        $data['where_in']['trans_main.entry_type'] = [6,7,8];
        $data['group_by'][] = 'trans_child.trans_main_id';
        return $this->rows($data);      
    }

    public function salesTransactions($id){
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,item_master.full_name,item_master.drawing_no,item_master.material_grade'; 
        $queryData['leftJoin']['item_master'] = 'item_master.id = trans_child.item_id';
        $queryData['where']['trans_main_id'] = $id;
        $queryData['where']['(trans_child.qty-trans_child.rework_qty) > ']=0;
        return $this->rows($queryData);
    }

    public function getNextTransNo(){
        $data['tableName'] = $this->customer_rework;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['YEAR(trans_date)'] = date("Y");
        $data['where']['MONTH(trans_date)'] = date("m");
        $maxNo = $this->specificRow($data)->trans_no;
        $nextNo = (!empty($maxNo)) ? ($maxNo + 1) : 1;
        return $nextNo;
    }

    public function getItemWiseBatchDetail($data){
        $queryData['tableName'] = "stock_transaction";
        $queryData['select'] = "stock_transaction.*";
        $queryData['where']['stock_transaction.trans_type'] = 2;
        if(!empty($data['item_id'])): 
            $queryData['where']['stock_transaction.item_id'] = $data['item_id'];           
        endif;   
        if(!empty($data['ref_type'])):
            $queryData['where']['stock_transaction.ref_type'] = $data['ref_type'];
        endif;

        if(!empty($data['ref_id'])):
            $queryData['where']['stock_transaction.ref_id'] = $data['ref_id'];
        endif;
        if(!empty($data['trans_ref_id'])):
            $queryData['where']['stock_transaction.trans_ref_id'] = $data['trans_ref_id'];
        endif;      
       return $this->rows($queryData);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
            if(empty($data['id'])){
                $data['trans_no'] = $this->customerRework->getNextTransNo();
                $data['trans_prefix'] = "DBR-" . n2y(date('Y')).n2m(date('m'));
                $data['trans_number']=$data['trans_prefix'].sprintf("%03d",$data['trans_no']);
            }
            foreach($data['batch_quantity'] as $key=>$batch_qty){
                if($batch_qty > 0){
                    $reworkData = [
                        'id'=>'',
                        'trans_no'=>$data['trans_no'],
                        'trans_prefix'=>$data['trans_prefix'],
                        'trans_number'=>$data['trans_number'],
                        'trans_date'=>$data['trans_date'],
                        'party_id'=>$data['party_id'],
                        'inv_id'=>$data['inv_id'],
                        'item_id'=>$data['item_id'],
                        'inv_child_id'=>$data['inv_child_id'],
                        'so_trans_id'=>$data['so_trans_id'],
                        'remark'=>$data['remark'],
                        'batch_no'=>$data['batch_number'][$key],
                        'tc_no'=>$data['tc_number'][$key],
                        'location_id'=>$data['location'][$key],
                        'material_grade'=>$data['ref_batch'][$key],
                        'qty'=>$batch_qty,
                        'created_by'=>$batch_qty,
                    ];
                    $result = $this->store($this->customer_rework,$reworkData);
                    $setData = Array();
                    $setData['tableName'] = 'trans_child';
                    $setData['where']['id'] = $data['inv_child_id'];
                    $setData['set']['rework_qty'] = 'rework_qty, + '.$batch_qty;
                    $this->setValue($setData);
                }
                

            }
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
	}

    public function getReworkData($postData){
        $data['tableName'] = $this->customer_rework;
        $data['select'] = "customer_rework.*,trans_main.trans_number AS inv_no,party_master.party_name,item_master.item_name,item_master.full_name";
        $data['leftJoin']['trans_main'] = "trans_main.id = customer_rework.inv_id";
        $data['leftJoin']['party_master'] = "party_master.id = customer_rework.party_id";
        $data['leftJoin']['item_master'] = "item_master.id = customer_rework.item_id";
        if(!empty($postData['id'])){ $data['where']['customer_rework.id'] = $postData['id'];}
        if(!empty($postData['trans_number'])){ $data['where']['customer_rework.trans_number'] = $postData['trans_number'];}
        if(isset($postData['single_row'])){
            return $this->row($data);
        }else{
            return $this->rows($data);}
        }

    public function delete($id){
        try{
            $this->db->trans_begin();
            $transData = $this->getReworkData(['id'=>$id,'single_row'=>1]);
            $dbtData = $this->getReworkData(['trans_number'=>$transData->trans_number]);
            foreach($dbtData as $row){
                $result = $this->trash($this->customer_rework,['id'=>$row->id]);
                $setData = Array();
                $setData['tableName'] = 'trans_child';
                $setData['where']['id'] =$row->inv_child_id;
                $setData['set']['rework_qty'] = 'rework_qty, - '.$row->qty;
                $this->setValue($setData);
            }
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
		
    }

    
    public function acceptRework($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->customer_rework,['id'=>$data['id'],'status'=>1]);
            
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
             return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }
		
    }

    public function saveRwRejQty($data){
        try{
            $this->db->trans_begin();
            $result = $this->store($this->customer_rework,['id'=>$data['id'],'ok_qty'=>$data['ok_qty'],'rej_qty'=>$data['rej_qty'],'status'=>2]);
            if(!empty($data['ok_qty']) && $data['ok_qty'] > 0){
                $stockEffect = [
                    'id'=>'',
                    'location_id' => $data['location_id'],
                    'ref_type'=>35,
                    'trans_type'=>1,
                    'qty'=>$data['ok_qty'],
                    'item_id'=>$data['item_id'],
                    'ref_id'=>$data['id'],
                    'ref_no'=>$data['trans_number'],
                    'batch_no'=>$data['batch_no'],				
                    'ref_batch'=>$data['material_grade'],				
                    'ref_date'=>date("Y-m-d"),				
                    'tc_no'=>$data['tc_no'],
    
                ];
                $this->store($this->stockTrans,$stockEffect);
            }
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