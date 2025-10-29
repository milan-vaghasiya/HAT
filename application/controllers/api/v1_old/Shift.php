<?php
class Shift extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }
/* Create By : Avruti @29-11-2021 10:10 AM
     update by : 
     note :
*/
    public function shiftList(){
        $total_rows = $this->shiftModel->getCount();

        $config = array();
        $config["base_url"] = base_url() . "api/v1/shift/shiftList";
        $config["total_rows"] = $total_rows;
        $config["per_page"] = (isset($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $config["uri_segment"] = 5;

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $this->data['total_rows'] = $total_rows;
        $this->data["links"] = $this->pagination->create_api_links();
        $this->data['shiftList'] = $this->shiftModel->getShiftList_api($config["per_page"], $page);
        
        $this->printJson(['status'=>1,'message'=>'Record found','data'=>$this->data]);
    }
    public function addShift(){
        $this->printJson(['status'=>1,'message'=>'Record found']);

    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
        if(empty($data['shift_name']))
			$errorMessage['shift_name'] = "Shift Name is required.";
        if(empty($data['start_time']))
			$errorMessage['start_time'] = "Start Time is required.";
        if(empty($data['production_hour']))
			$errorMessage['production_hour'] = "Production Hour is required.";
        if(empty($data['lunch_hour']))
			$errorMessage['lunch_hour'] = "Lunch Hour is required.";

        $data['shift_hour'] = ($data['lunch_hour'] + $data['production_hour']);
        if($data['shift_hour'] > 24)
            $errorMessage['lunch_hour'] = "Invalid Hours.";
		
        // $data['end_time'] = date('H:i:s',strtotime($data['start_time']) + ($data['shift_hour'] * 3600));
		
		$data['end_time'] = addTimeToDate($data['start_time'],$data['shift_hour'],$type="H",$dateFormat='H:i:s');
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->loginId;
            $this->printJson($this->shiftModel->save($data));
        endif;
    }

    public function view(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->shiftModel->getShift($id);
        $this->printJson(['status'=>1,'message'=>'recoreds found','data'=>$this->data]);

    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->shiftModel->delete($id));
        endif;
    }
}
?>