<?php
class FeedbackPointModel extends MasterModel{
    private $feedbackMaster = "cust_feedback_points";
    
	public function getDTRows($data){
        $data['tableName'] = $this->feedbackMaster;
        
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "parameter";
        
		$columns =array('','','parameter');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getFeedbackPoint($id){
        $data['tableName'] = $this->feedbackMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
    
    public function save($data){
		return $this->store($this->feedbackMaster,$data);
	}
    
    public function delete($id){
		
        return $this->trash($this->feedbackMaster,['id'=>$id]);
    }
}
?>