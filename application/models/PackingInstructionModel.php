<?php
 
class PackingInstructionModel extends MasterModel {
    private $packingMaster = "packing_master";
    private $transChild = "trans_child";

    public function getDTRows($data){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "packing_master.*,trans_child.item_name,trans_child.item_code";   
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_master.ref_id";
        $data['where']['packing_master.ref_id != '] = 0;
        $data['searchCol'][] = "DATE_FORMAT(packing_master.dispatch_date,'%d-%m-%Y')";
        $data['searchCol'][] = "trans_child.item_code";
        $data['searchCol'][] = "trans_child.item_name";
        $data['searchCol'][] = "packing_master.qty";
        $data['searchCol'][] = "packing_master.remark";
        $data['searchCol'][] = "packing_master.status";

		$columns =array('','','packing_master.dispatch_date','trans_child.item_code','trans_child.item_name','packing_master.qty','packing_master.remark','packing_master.status');

		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getPackingData($id){
        $data['tableName'] = $this->packingMaster; 
        $data['select'] = "packing_master.*,trans_child.item_name,trans_child.item_code";   
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_master.ref_id";
        $data['where']['packing_master.id'] = $id;
        return $this->row($data);
    }

    public function getItemList(){
        $data['tableName'] = $this->transChild; 
        $data['select'] = "trans_child.id, trans_child.item_id, trans_child.item_code, trans_child.item_name, trans_main.trans_prefix, trans_main.trans_no";
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 4;
        $data['where']['trans_child.trans_status'] = 0;
        return $this->rows($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->packingMaster,$data,"Paking Instruction");
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }
	
	/*  Create By : Avruti @29-11-2021 04:00 PM
    update by : 
    note : 
*/
    //---------------- API Code Start ------//

    public function getCount(){
        $data['tableName'] = $this->packingMaster;
        $data['where']['packing_master.ref_id != '] = 0;

        return $this->numRows($data);
    }

    public function getPackingInstructionList_api($limit, $start,$status){
        $data['tableName'] = $this->packingMaster;
        $data['select'] = "packing_master.*,trans_child.item_name,trans_child.item_code";   
        $data['leftJoin']['trans_child'] = "trans_child.id = packing_master.ref_id";
        $data['where']['packing_master.ref_id != '] = 0;

        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}

?>