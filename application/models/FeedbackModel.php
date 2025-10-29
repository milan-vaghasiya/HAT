<?php
class FeedbackModel extends MasterModel{
    
    private $feedBack = "customer_feedback";
    private $feedBackTrans = "cust_feedback_trans";
    private $feedBackPoint = "cust_feedback_points";
    
        public function nextTransNo($trans_prefix){
        $data['tableName'] = $this->feedBack;
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['where']['created_at >= '] = $this->startYearDate;
        $data['where']['created_at <= '] = $this->endYearDate;
        $data['where']['trans_prefix'] = $trans_prefix;
		$trans_no = $this->specificRow($data)->trans_no;
		//print_r($this->db->last_query());exit;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
    }

    public function getDTRows($data){
        $data['tableName'] = $this->feedBack;
        $data['select'] = "customer_feedback.*,party_master.party_name";
        $data['leftJoin']['party_master'] = "party_master.id = customer_feedback.party_id";
        if($data['status']==0){ $data['customWhere'][] = "(customer_feedback.feedback_by IS NULL OR customer_feedback.feedback_by = '')"; }
        if($data['status']==1){ $data['customWhere'][] = "(customer_feedback.feedback_by IS NOT NULL OR customer_feedback.feedback_by != '')"; }

        $data['searchCol'][] = "customer_feedback.trans_no";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "customer_feedback.survey_from";
        $data['searchCol'][] = "customer_feedback.survey_to";
        $data['searchCol'][] = "customer_feedback.feedback_by";
        $data['searchCol'][] = "customer_feedback.feedback_at";
        
		$columns =array('','','customer_feedback.trans_no','party_master.party_name','customer_feedback.survey_from','customer_feedback.survey_to','customer_feedback.feedback_by','customer_feedback.feedback_at');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
    
    public function getFeedbackPoint(){
        $data['tableName'] = $this->feedBackPoint;
        return $this->rows($data);
    }

    public function getFeedback($id){
        $data['tableName'] = $this->feedBack;
        $data['select'] = "customer_feedback.*,party_master.party_name,party_master.party_address";
        $data['leftJoin']['party_master'] = "party_master.id = customer_feedback.party_id";
        $data['where']['customer_feedback.id'] = $id;
        $result = $this->row($data);
        
        $result->transData = $this->getFeedbackTrans($result->id);
        return $result;
    }
    
    public function getFeedbackTrans($feedback_id){
        $data['tableName'] = $this->feedBackTrans;
        $data['where']['feedback_id'] = $feedback_id;
        return $this->rows($data);
    }
    
    public function getFeedbackParam($id){
        $data['tableName'] = $this->feedBackPoint;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        try{
            $this->db->trans_begin();
         
            $param_id = $data['param_id']; unset($data['param_id']);
            //$partyData = $this->party->getParty($data['party_id']);
            
            if(!empty($data['id'])):
                $this->trash($this->feedBackTrans,['feedback_id'=>$data['id']]);
            else:
                $data['created_at'] = date("Y-m-d H:i:s");
                $data['trans_prefix'] = n2y(date('Y')).n2m(date('m'));
                $data['trans_no'] = $this->nextTransNo($data['trans_prefix']);
            endif;
            $result = $this->store($this->feedBack,$data,'Generate Demand');
            $data['id'] = (empty($data['id']))?$result['insert_id']:$data['id'];

            $param_id = explode(',',$param_id);
            foreach($param_id as $value):
                $paramData = $this->getFeedbackParam($value);
                $transData = [
                    'id' => '',
                    'feedback_id' => $data['id'],
                    'param_id' => $value,
                    'parameter' => $paramData->parameter,
                    'created_by' => $data['created_by'],
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->feedBackTrans,$transData);
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

    public function delete($id){
        $this->trash($this->feedBackTrans,['feedback_id'=>$id]);
        return $this->trash($this->feedBack,['id'=>$id]);
    }   
}