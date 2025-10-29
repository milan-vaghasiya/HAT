<?php
class MasterDetailModel extends MasterModel
{
    private $master_detail = "master_detail";

    public function getDTRows($data,$type){
        $data['tableName'] = $this->master_detail;
        if($data['type'] == 1){$data['where']['type'] = 1;}
        if($data['type'] == 2){$data['where']['type'] = 2;}
        if($data['type'] == 3){$data['where']['type'] = 3;}
        $data['where']['type'] = $type;
		$data['searchCol'][] = "code";
        $data['searchCol'][] = "title";
		$columns =array('','','code','title');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getMasterDetail($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->master_detail;
        return $this->row($data);
    }

    public function getMasterDetailList(){
        $data['tableName'] = $this->master_detail;
        $data['where']['type'] = 1;
        return $this->rows($data);
    }

    public function getMasterDetailsByCategory($postData = []){
        $data['tableName'] = $this->master_detail;
        if(!empty($postData['type'])){$data['where']['type'] = $postData['type'];}
        if(!empty($postData['category_id'])){$data['customWhere'][''] = " FIND_IN_SET('".$postData['category_id']."', 'ref_id')";}
        return $this->rows($data);
    }

    public function save($data){
		return $this->store($this->master_detail,$data);
	}

    public function delete($id){		
        return $this->trash($this->master_detail,['id'=>$id]);
    }	
}
?>