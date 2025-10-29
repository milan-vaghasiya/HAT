<?php

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class Auth extends CI_Controller{

    public function __construct(){
		parent::__construct();
        $this->load->library('fcm');
        $this->load->model('masterModel');
        $this->load->model('LoginModel','loginModel');
        $this->load->model('NotificationModel','notification');
	}

    public function check(){
        $data = $this->input->post();
        echo json_encode($this->loginModel->checkApiAuth($data));
    }

    public function isVerified(){
        $data = $this->input->post();
        $data['fyear'] = 3;
        echo json_encode($this->loginModel->verification($data));
    }

    public function logout(){
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        echo json_encode($this->loginModel->appLogout($headData->loginId));
    }

    public function forgotPassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['user_name']))
            $errorMessage['user_name'] = "Login ID is required.";

        if(!empty($errorMessage)):
            echo json_encode(['status'=>0,'message'=>$errorMessage]);
        else:
            echo json_encode($this->loginModel->forgotPassword($data));
        endif;
    }

    public function updateNewPassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['password']))
            $errorMessage['password'] = "Password is required.";

        if(!empty($errorMessage)):
            echo json_encode(['status'=>0,'message'=>$errorMessage]);
        else:
            echo json_encode($this->loginModel->updateNewPassword($data));
        endif;
    }

    public function yearListing(){
        $yearList = $this->db->get('financial_year')->result();
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        $cyKey = array_search(1,array_column($yearList,'is_active'));
        $dataRow = array();
        foreach($yearList as $key=>$row):
            if($cyKey >= $key):
                $selected = ($headData->financialYear == $row->financial_year)?1:0;
                $dataRow[] = [
                    'financial_year' => $row->financial_year,
                    'short_year' => $row->year,
                    'selected' => $selected
                ];
            endif;
        endforeach;
        echo json_encode(['status'=>1,'message'=>'Recored found.','data'=>['financialYearList'=>$dataRow]]);
    }

    public function setFinancialYear(){
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
		$year = $this->input->post('financial_year');
		$result = $this->loginModel->setAppFinancialYear($year,$headData);
		echo json_encode(['status'=>1,'message'=>'Financial Year changed successfully.','data'=>['headData'=>$result]]);
	}
}
?>