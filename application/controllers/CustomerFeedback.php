<?php
class CustomerFeedback extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('LoginModel','login_model');
	}
	
	public function index(){
		$this->load->view('cust_feedback_form');
	}
	
	public function getFeedback($id=0){
	    $this->data['id']= $id;
	    $this->data['feedbackData']= $this->login_model->getFeedback($id);
	    $this->data['param']= $this->login_model->getFeedbackParams($id);
		$this->load->view('cust_feedback_form',$this->data);
	}
	
	public function saveFeedback(){
        $data = $this->input->post();
        
        $errorMessage = array();
        if(empty($data['feedback_by']))
			$errorMessage['feedback_by'] = "Feedback Filled By is required.";
			
        if(!empty($errorMessage)):
            print json_encode(['status'=>0,'message'=>$errorMessage]); exit;
        else:
            $ftransData = Array();$totalRate = 0;$rateCount= 0 ;$i=1;
            
            if(!empty($data['ftrans_id']))
            {
                foreach($data['ftrans_id'] as $ftid)
                {
                    $rate = (!empty($data['rating']['p'.$i])) ? $data['rating']['p'.$i] : 0;
                    $ftransData[$ftid] = $rate;
                    $totalRate += $rate;
                    if(!empty($data['rating']['p'.$i])){$rateCount++;}
                    $i++;
                }
            }
            if(empty($rateCount)){
                $errorMessage['feedback_grade'] = "Feedback  is required.";
                print json_encode(['status'=>0,'message'=>$errorMessage]); exit;
            }
            $data['grade'] = ceil($totalRate / $rateCount) ;
            $data['ftransData'] = $ftransData;
            print json_encode($this->login_model->saveFeedback($data)); exit;
        endif;
    }
}
?>