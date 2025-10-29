<?php
class InstrumentModel extends MasterModel{
    private $itemMaster = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->itemMaster;
        return $this->pagingRows($data);
    }

    public function getItem($id){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = 'item_master.id,item_master.item_code,item_master.unit_id,item_master.size,item_master.category_id,item_master.location, item_master.make_brand, item_master.cal_required,item_master.cal_agency,item_master.cal_freq,item_master.cal_reminder,item_master.last_cal_date,item_master.thread_type, item_master.description, item_master.item_name,item_master.instrument_range,item_master.least_count,item_master.permissible_error, item_master.item_image,item_category.category_name,item_master.drawing_no,item_master.part_no,item_master.drawing_file,item_master.rev_no';
        $data['leftJoin']['item_category'] = 'item_category.id = item_master.category_id';
        $data['where']['item_master.id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
    		$result = $this->store($this->itemMaster,$data);
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
    		$itemData = $this->getItem($id);
            $result= $this->trash($this->itemMaster,['id'=>$id]);
            if ($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
        }	
    }
	
	/*  Create By : Avruti @27-11-2021 5:45 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount($type){        
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_type'] = $type;
        return $this->numRows($data);
    }

    public function getGaugesList_api($limit, $start,$type=0){
        $data['tableName'] = $this->itemMaster;

		$data['where']['item_type'] = $type;
		
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}