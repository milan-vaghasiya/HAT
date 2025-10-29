<?php
class Notification extends MY_Controller{   

    public function index(){
        $this->load->view('notification');
    }

    public function send(){
        $data = $this->input->post();

        $notifyData['notificationMsg'] = (!empty($data['notificationMsg']))?$data['notificationMsg']:"Notification test successfull.";
        $notifyData['notificationTitle'] = (!empty($data['notificationTitle']))?$data['notificationTitle']:"Test Notification";
        $notifyData['payload'] = ['callBack' => base_url('notification')];
        $notifyData['controller'] = "'salesOrder'";
        /* 
        * Send New Entry Notification set action = W
        * Send Update Entry Notification set action = M
        * Send Delete Entry Notification set action = D
        */
        $notifyData['action'] = "W";

        $result = $this->masterModel->notify($notifyData);
        $this->printJson($result);
    }
}
?>