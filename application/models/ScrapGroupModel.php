<?php
class ScrapGroupModel extends MasterModel{
    private $item_master = "item_master";

    public function getDTRows($data){
        $data['tableName'] = $this->item_master;
        $data['select'] = "item_master.*,item_category.category_name,unit_master.unit_name";
        $data['leftJoin']['item_category'] = "item_category.id  = item_master.category_id";
        $data['leftJoin']['unit_master'] = "unit_master.id  = item_master.unit_id";
        $data['where']['item_master.item_type = '] = "10";
        $data['searchCol'][] = "item_name";
        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "unit_master.unit_name";
		$columns =array('','','item_name','item_category.category_name','unit_master.unit_name');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getScrapGroup($id){
        $data['tableName'] = $this->item_master;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
		return $this->store($this->item_master,$data);
	}

    public function delete($id){
		
        return $this->trash($this->item_master,['id'=>$id]);
    }
	
}