<?php
class CommercialInvoiceModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,packing_master.trans_number as packing_no";
        $data['leftJoin']['packing_master'] = "packing_master.id = trans_main.ref_by";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = $data['status'];
        else:
            $data['where']['trans_main.trans_status'] = $data['status'];
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_no'] = "DESC";
        
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_number','packing_master.trans_number','trans_main.doc_no','','trans_main.party_name','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getCommercialPackingNoList(){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "id,trans_number,ref_by,trans_status";
        $queryData['where']['entry_type'] = 19;
        $queryData['where']['trans_status'] = 0;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(!empty($masterData['id'])):
                $this->trash($this->transChild,['trans_main_id'=>$masterData['id']]);
            else:
                $masterData['trans_date'] = date("Y-m-d");
                $masterData['trans_prefix'] = $this->transModel->getTransPrefix(10);
                $masterData['trans_no'] = $this->transModel->nextTransNo(10);
                $masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
            endif;

            $result = $this->store($this->transMain,$masterData,'Commercial Invoice');
            $result['url'] = base_url('commercialInvoice');
            $masterData['id'] = (empty($masterData['id']))?$result['insert_id']:$masterData['id'];

            foreach($itemData as $row):
                $row['created_by'] = $masterData['created_by'];
                $row['trans_main_id'] = $masterData['id'];
                $row['entry_type'] = $masterData['entry_type'];
                $row['from_entry_type'] = $masterData['from_entry_type'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);
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

    public function getCommercialInvocieData($id,$is_pdf = 0){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $result = $this->row($queryData);
        if(!empty($result->extra_fields)):
            $jsonData = json_decode($result->extra_fields);
            foreach($jsonData as $key=>$value):
                $result->{$key} = $value;
            endforeach;
        endif;
        
        if($is_pdf == 1):
            $result->itemData = $this->getCommercialInvoiceItemsForPdf($id);
        else:
            $result->itemData = $this->getCommercialInvoiceItems($id);
        endif;
        return $result;
    }

    public function getCommercialInvoiceItems($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,packing_transaction.com_pck_status as packing_status,LPAD(packing_transaction.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo";        
        $queryData['leftJoin']['trans_child as compck'] = "trans_child.ref_id = compck.id";
        $queryData['leftJoin']['packing_transaction'] = "packing_transaction.id = compck.ref_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }

    public function getCommercialInvoiceItemsForPdf($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_desc,trans_child.item_alias,trans_child.hsn_code,trans_child.hsn_desc,SUM(trans_child.qty) as qty,AVG(trans_child.price) as price,SUM(trans_child.amount) as amount,SUM(trans_child.net_amount) as net_amount,packing_transaction.com_pck_status as packing_status,LPAD(packing_transaction.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo";        
        $queryData['leftJoin']['trans_child as compck'] = "trans_child.ref_id = compck.id";
        $queryData['leftJoin']['packing_transaction'] = "packing_transaction.id = compck.ref_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $queryData['group_by'][] = "trans_child.item_id";
        $queryData['order_by']['trans_child.id'] = "ASC";
        $result = $this->rows($queryData);
        return $result;
    }

    public function delete($id){
        try{
            $this->db->trans_begin();

            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $result = $this->trash($this->transMain,['id'=>$id],'Commercial Invocie');

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