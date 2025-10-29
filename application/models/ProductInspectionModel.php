<?php
class ProductInspectionModel extends MasterModel{
    private $productInspection = "product_inspection";
    private $itemMaster = "item_master";

    public function getDTRows($data,$type=0){
        $data['tableName'] = $this->productInspection;
        $data['select'] = "product_inspection.*,item_master.item_name,unit_master.unit_name";
        $data['join']['item_master'] = "item_master.id = product_inspection.item_id";
        $data['join']['unit_master'] = "unit_master.id = item_master.unit_id";

        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "DATE_FORMAT(product_inspection.inspection_date,'%d-%m-%Y')";
        $data['searchCol'][] = "unit_master.unit_name";
        $data['searchCol'][] = "product_inspection.qty";

		$columns =array('','','product_inspection.type','product_inspection.inspection_date','item_master.item_name','product_inspection.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $itemData = $this->item->getItem($data['item_id']);

        if($data['type'] == 1):
            $pendingInspectionQty = $itemData->pending_inspection_qty - $data['qty'];
            $inspectedQty = $itemData->qty + $data['qty'];
            $this->store($this->itemMaster,['id'=>$data['item_id'],'pending_inspection_qty'=>$pendingInspectionQty,'qty'=>$inspectedQty]);
        elseif($data['type'] == 2):
            $pendingInspectionQty = $itemData->pending_inspection_qty - $data['qty'];
            $rejectQty = $itemData->reject_qty + $data['qty'];
            $this->store($this->itemMaster,['id'=>$data['item_id'],'pending_inspection_qty'=>$pendingInspectionQty,'reject_qty'=>$rejectQty]);
        else:
            $pendingInspectionQty = $itemData->pending_inspection_qty - $data['qty'];
            $scrapeQty = $itemData->scrape_qty + $data['qty'];
            $this->store($this->itemMaster,['id'=>$data['item_id'],'pending_inspection_qty'=>$pendingInspectionQty,'scrape_qty'=>$scrapeQty]);
        endif;
        $data['inspection_date'] = date("Y-m-d");
        $result = $this->store($this->productInspection,$data,'Product Inspection');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $data['tableName'] = $this->productInspection;
        $data['where']['id'] = $id;
        $inspectedData = $this->row($data);

        $itemData = $this->item->getItem($inspectedData->item_id);

        if($inspectedData->type == 1):
            $pendingInspectionQty = $itemData->pending_inspection_qty + $inspectedData->qty;
            $inspectedQty = $itemData->qty - $inspectedData->qty;
            $this->store($this->itemMaster,['id'=>$inspectedData->item_id,'pending_inspection_qty'=>$pendingInspectionQty,'qty'=>$inspectedQty]);
        elseif($inspectedData->type == 2):
            $pendingInspectionQty = $itemData->pending_inspection_qty + $inspectedData->qty;
            $rejectQty = $itemData->reject_qty - $inspectedData->qty;
            $this->store($this->itemMaster,['id'=>$inspectedData->item_id,'pending_inspection_qty'=>$pendingInspectionQty,'reject_qty'=>$rejectQty]);
        else:
            $pendingInspectionQty = $itemData->pending_inspection_qty + $inspectedData->qty;
            $scrapeQty = $itemData->scrape_qty - $inspectedData->qty;
            $this->store($this->itemMaster,['id'=>$inspectedData->item_id,'pending_inspection_qty'=>$pendingInspectionQty,'scrape_qty'=>$scrapeQty]);
        endif;

        $result = $this->trash($this->productInspection,['id'=>$id],'Product Inspection');
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