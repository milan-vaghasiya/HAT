<?php
class Production extends MY_Apicontroller{

    public function __construct() {
        parent:: __construct();        
    }

    public function saveProductionLogs(){
        $data = json_decode(file_get_contents('php://input'), true);
        $errorMessage = array();
        if(empty($data['machine_id']))
            $errorMessage['machine_id'] = "Machine ID is required.";
        if(empty($data['part_code']))
            $errorMessage['part_code'] = "Part Code is required.";
        if(empty($data['part_count']))
            $errorMessage['part_count'] = "Part Count is required.";
        if(empty($data['operator_id']))
            $errorMessage['operator_id'] = "Operator ID is required.";
        //if(empty($data['date']))
        //    $errorMessage['date'] = "Date is required.";
        //if(empty($data['time']))
        //    $errorMessage['time'] = "Time is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            //$data['log_date'] = date('Y-m-d h:i:s',strtotime($data['date'].' '.$data['time']));
            $data['start_time'] =  date('Y-m-d H:i:s',strtotime($data['start_date'].' '.$data['start_time']));
            $data['end_time'] = date('Y-m-d H:i:s',strtotime($data['end_date'].' '.$data['end_time']));
            $data['log_date'] = date('Y-m-d H:i:s',strtotime($data['start_time']));
            $data['id']="";
            $data['process_id'] = $data['part_job'];
            unset($data['start_date'],$data['end_date'],$data['part_job']);
            //print_r($data);
            $this->printJson($this->production->saveProductionLogs($data));
        endif;
    }
}
?>