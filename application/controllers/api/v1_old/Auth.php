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
        $this->load->model('LoginModel','loginModel');
	}

    public function check(){
        $data = $this->input->post();
        echo json_encode($this->loginModel->checkApiAuth($data));
    }

    public function isVerified(){
        $data = $this->input->post();
        $data['fyear'] = 2;
        echo json_encode($this->loginModel->verification($data));
    }

    public function logout(){
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        echo json_encode($this->loginModel->appLogout($headData->loginId));
    }
}
?>