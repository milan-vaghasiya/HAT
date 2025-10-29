<?php
class Dashboard extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "app/dashboard";
	}
	
	public function index(){
		$this->data['headData']->menu_id = 0;
		$this->data['leadData'] = '';
		$this->data['leadView'] ='';
		$this->data['monthListFY'] = $monthListFY = $this->getMonthListFY("M-y");
		$monthSeries = Array();
		if(!empty($monthListFY)){foreach($monthListFY as $row){$monthSeries[] = $row['label'];}}
		$this->data['monthSeries'] = (!empty($monthSeries)) ? "'" . implode ( "', '", $monthSeries ) . "'" : '';
		$this->data['soTransData'] = $this->salesOrder->getPendingSoTrans();
		$this->data['poCount'] = $this->purchaseOrder->getPendingPoCount();
		$this->data['purchase'] = $this->dashboard->getTodayPurchase();
		$this->data['sales'] = $this->dashboard->getTodaySales();
		$this->data['prodAnalysis'] = $this->dashboard->getProductionAnalysis();
		$this->load->view('app/dashboard',$this->data);
	}
	
	public function getSalesChartData(){
		$this->data['monthListFY'] = $monthListFY = $this->getMonthListFY("M-y");
		$monthSeries = Array();
		if(!empty($monthListFY)){foreach($monthListFY as $row){$monthSeries[] = $row['label'];}}
		$this->data['monthSeries'] = (!empty($monthSeries)) ? "'" . implode ( "', '", $monthSeries ) . "'" : '';
		
		$this->data['soTransData'] = $this->salesOrder->getPendingSoTrans();
		
		$orderStats=[];$dispatchStats=[];$series = [];
		$salesStats = $this->salesReportModel->getSalesStats();
		if(!empty($salesStats))
		{
			for($x=4;$x<=12;$x++)
			{
				$lable = date('M-y',strtotime($this->startYear.'-'.$x.'-01'));
				$of = 'oq'.$x;
				$df = 'dq'.$x;
				//$series[] = array('period'=>$lable,'oq'=>intval($salesStats->{$of}),'dq'=>intval($salesStats->{$df}));
				$orderStats[] = intval(($salesStats->{$of}/1000));
				$dispatchStats[] = intval(($salesStats->{$df}/1000));
			}
			for($x=1;$x<=3;$x++)
			{
				$lable = date('M-y',strtotime($this->endYear.'-'.$x.'-01'));
				$of = 'oq'.$x;
				$df = 'dq'.$x;
				//$series[] = array('period'=>$lable,'oq'=>intval($salesStats->{$of}),'dq'=>intval($salesStats->{$df}));
				$orderStats[] = intval(($salesStats->{$of}/1000));
				$dispatchStats[] = intval(($salesStats->{$df}/1000));
			}
			$series['orderData'] = $orderStats;
			$series['dispatchData'] = $dispatchStats;
			echo json_encode($series);
		}
	}

	public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $id = $this->session->userdata('loginId');
			$result =  $this->dashboard->changePassword($id,$data);
			$this->printJson($result);
		endif;
    }
}
?>