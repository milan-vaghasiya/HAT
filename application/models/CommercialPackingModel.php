<?php
class CommercialPackingModel extends MasterModel{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $packingMaster = "packing_master";
    private $packingTrans = "packing_transaction";

    public function getDTRows($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = "trans_main.*,packing_master.trans_number as packing_no";
        $data['leftJoin']['packing_master'] = "packing_master.id = trans_main.ref_id";
        $data['customWhere'][] = 'trans_main.entry_type IN ('.$data['entry_type'].')';
        if($data['status'] == 0):
            $data['where']['trans_main.trans_status'] = $data['status'];
        else:
            $data['where']['trans_main.trans_status'] = $data['status'];
            $data['where']['trans_main.trans_date >= '] = $this->startYearDate;
            $data['where']['trans_main.trans_date <= '] = $this->endYearDate;
        endif;

        $data['order_by']['trans_main.trans_no'] = "DESC";
        
        $data['searchCol'][] = "packing_master.trans_number";
        $data['searchCol'][] = "trans_main.trans_number";
        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "trans_main.party_name";
        $data['searchCol'][] = "trans_main.net_amount";

        $columns =array('','','trans_main.trans_number','packing_master.trans_number','trans_main.doc_no','','trans_main.party_name','','trans_main.net_amount');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getPackingNoList($party_id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_master.id,packing_master.trans_number";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['trans_main'] = "trans_main.id = packing_transaction.trans_main_id";
        $queryData['where']['packing_transaction.trans_main_id != '] = 0;
        //$queryData['where']['packing_master.packing_status'] = 0;
        $queryData['where']['packing_master.entry_type'] = "Export";
        $queryData['where']['trans_main.party_id'] = $party_id;
        $queryData['group_by'][] = "trans_number";
        return $this->rows($queryData);
    }

    public function getPackingItemList($packing_id){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "packing_transaction.id,packing_transaction.item_id,packing_transaction.total_qty as qty,packing_transaction.wt_pcs as price,packing_transaction.net_wt as amount,packing_transaction.gross_wt as net_amount,packing_transaction.wooden_wt as taxable_amount,item_master.item_type,item_master.item_code,item_master.item_name,item_master.description,item_master.item_alias,hsn_master.hsn_code,hsn_master.description as hsn_desc";
        $queryData['leftJoin']['packing_master'] = "packing_master.id = packing_transaction.packing_id";
        $queryData['leftJoin']['item_master'] = "item_master.id = packing_transaction.item_id";
        $queryData['leftJoin']['hsn_master'] = "item_master.hsn_code = hsn_master.hsn_code";
        $queryData['where']['packing_transaction.packing_id'] = $packing_id;
        $queryData['where']['packing_master.entry_type'] = "Export";
        $queryData['where_in']['packing_transaction.com_pck_status'] = [0,1];
        return $this->rows($queryData);
    }

    public function getCustomerPOData($packing_ids){
        $queryData = array();
        $queryData['tableName'] = $this->packingTrans;
        $queryData['select'] = "trans_main.id,trans_main.doc_no,trans_main.doc_date";
        $queryData['leftJoin']['trans_main'] = "packing_transaction.trans_main_id = trans_main.id";
        $queryData['where_in']['packing_transaction.id'] = $packing_ids;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($masterData,$itemData){
        try{
            $this->db->trans_begin();
            if(!empty($masterData['id'])):
                $transData = $this->getCommercialPackingItems($masterData['id']);
                foreach($transData as $row):
                    $this->edit($this->packingTrans,['id'=>$row->ref_id],['com_pck_status'=>0]);
                endforeach;
                $this->trash($this->transChild,['trans_main_id'=>$masterData['id']]);                
            else:
                $masterData['trans_date'] = date("Y-m-d");
                $masterData['trans_prefix'] = $this->transModel->getTransPrefix(19);
                $masterData['trans_no'] = $this->transModel->nextTransNo(19);
                $masterData['trans_number'] = getPrefixNumber($masterData['trans_prefix'],$masterData['trans_no']);
            endif;

            $result = $this->store($this->transMain,$masterData,'Commercial Packing');
            $result['url'] = base_url('commercialPacking');
            $masterData['id'] = (empty($masterData['id']))?$result['insert_id']:$masterData['id'];

            foreach($itemData as $row):
                $row['created_by'] = $masterData['created_by'];
                $row['trans_main_id'] = $masterData['id'];
                $row['entry_type'] = $masterData['entry_type'];
                $row['is_delete'] = 0;
                $this->store($this->transChild,$row);
                $this->edit($this->packingTrans,['id'=>$row['ref_id']],['com_pck_status'=>1]);
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

    public function getCommercialPackingData($id,$is_pdf = 0){
        $queryData = array();
        $queryData['tableName'] = $this->transMain;
        $queryData['select'] = "trans_main.*,party_master.party_address as buyer_address";
        $queryData['leftJoin']['party_master'] = "trans_main.party_id = party_master.id";
        $queryData['where']['trans_main.id'] = $id;
        $result = $this->row($queryData);
        if(!empty($result->extra_fields)):
            $jsonData = json_decode($result->extra_fields);
            foreach($jsonData as $key=>$value):
                $result->{$key} = $value;
            endforeach;
        endif;

        if($is_pdf == 1):
            $result->itemData = $this->getCommercialPackingItemsForPdf($id);
        else:
            $result->itemData = $this->getCommercialPackingItems($id);
        endif;
        return $result;
    }

    public function getCommercialPackingItems($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.*,soi.price as so_price,packing_transaction.com_pck_status as packing_status,LPAD(packing_transaction.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo";
        $queryData['leftJoin']['packing_transaction'] = "packing_transaction.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "packing_transaction.trans_child_id = soi.id";
        $queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where']['trans_child.trans_main_id'] = $id;
        $result = $this->rows($queryData);
        return $result;
    }
    
    public function getCommercialPackingItemsForPdf($id){
        $queryData = array();
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_id,trans_child.item_code,trans_child.item_name,trans_child.item_desc,trans_child.item_alias,trans_child.hsn_code,trans_child.hsn_desc,SUM(trans_child.qty) as qty,AVG(trans_child.price) as price,SUM(trans_child.amount) as amount,SUM(trans_child.net_amount) as net_amount,soi.price as so_price,packing_transaction.com_pck_status as packing_status,LPAD(packing_transaction.package_no, 2, '0') as package_no,item_master.part_no as PartNo,item_master.drawing_no as DrgNo,item_master.rev_no as RevNo";
        $queryData['leftJoin']['packing_transaction'] = "packing_transaction.id = trans_child.ref_id";
        $queryData['leftJoin']['trans_child as soi'] = "packing_transaction.trans_child_id = soi.id";
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

            $transData = $this->getCommercialPackingItems($id);
            foreach($transData as $row):
                $this->edit($this->packingTrans,['id'=>$row->ref_id],['com_pck_status'=>0]);
            endforeach;

            $this->trash($this->transChild,['trans_main_id'=>$id]);
            $result = $this->trash($this->transMain,['id'=>$id],'Commercial Packing');

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