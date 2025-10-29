<?php
class Dashboard extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dashboard";
	}
	
	public function index(){
		$this->data['monthListFY'] = $monthListFY = $this->getMonthListFY("M-y");
		$monthSeries = Array();
		if(!empty($monthListFY)){foreach($monthListFY as $row){$monthSeries[] = $row['label'];}}
		$this->data['monthSeries'] = (!empty($monthSeries)) ? "'" . implode ( "', '", $monthSeries ) . "'" : '';
		
		$this->data['soTransData'] = $this->salesOrder->getPendingSoTrans();
		$this->data['poCount'] = $this->purchaseOrder->getPendingPoCount();
		$this->data['purchase'] = $this->dashboard->getTodayPurchase();
		$this->data['sales'] = $this->dashboard->getTodaySales();
		$this->data['prodAnalysis'] = $this->dashboard->getProductionAnalysis();
		$this->data['topProducts'] = $this->dashboard->getTopProduct();
		$this->load->view('dashboard',$this->data);
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
				$lable = date('M-y',strtotime($this->start_year.'-'.$x.'-01'));
				$of = 'oq'.$x;
				$df = 'dq'.$x;
				$series[] = array('period'=>$lable,'oq'=>intval($salesStats->{$of}),'dq'=>intval($salesStats->{$df}));
			}
			for($x=1;$x<=3;$x++)
			{
				$lable = date('M-y',strtotime($this->end_year.'-'.$x.'-01'));
				$of = 'oq'.$x;
				$df = 'dq'.$x;
				$series[] = array('period'=>$lable,'oq'=>intval($salesStats->{$of}),'dq'=>intval($salesStats->{$df}));
			}
			
			echo json_encode($series);
		}
	}
	
	public function userGuide(){
		$this->load->view('documentation', $this->data);
	}

	// Created By JP @ 06.07.2023
	public function trackOrderByTransId()
    {
        $postData = $this->input->post();
		$html = '<h4 class="card-title" style="font-size:2rem;text-align:center;color:#A90000;">Sorry...!<br> No Data Found...!</h4>';
		$salesData = $this->salesOrder->getSoByTransId($postData);
		//print_r($salesData);exit;
		if(!empty($salesData))
		{
			$html = '<h4 class="card-title" style="font-size:1rem;">
						<span class="badge badge-pill badge-success float-right font-bold">WO.No. : '.$salesData->grn_data.'</span>
						['.$salesData->item_code.'] '.$salesData->item_name.'<br>
						<small style="color: #a1aab2;"><i class="fas fa-clock"></i> '.formatDate($salesData->trans_date).' | #'.$salesData->trans_number.'</small>
					</h4>';
		}
		$prodData = '';
		$prodData .= '	<div class="row" style="width:100%;">
							<span class="badge badge-primary font-bold col-md-3">Order Qty<br>'.floatVal($salesData->qty).'</span>
							<span class="badge badge-success font-bold col-md-3">Dispatched Qty<br>'.floatVal($salesData->dispatch_qty).'</span>
							<span class="badge badge-danger font-bold col-md-3">Pending Qty<br>'.floatVal(($salesData->qty - $salesData->dispatch_qty)).'</span>
							<span class="badge badge-warning text-white font-bold col-md-3">Stock Qty<br>'.floatVal($salesData->stock_qty).'</span>
						</div>';
		if(!empty($salesData))
		{
			$jobData = $this->jobcard->trackOrderByTransId($postData);
			//print_r($jobData);exit;
			if(!empty($jobData))
			{
				foreach($jobData as $row)
				{
					$prodQty = $row->fq + $row->rq;
					$pendingQty = $row->oq - $prodQty;
					$workDone = 0;
					if(!empty($row->oq) AND $row->oq>0){$workDone = round((($prodQty * 100)/$row->oq),2);}
					$prodData .= '<div class="steamline">
									<div class="sl-item">
										<div class="sl-left bg-success">'.$workDone.'%</div>
										<div class="sl-right">
											<div class="font-medium">'.$row->process_name.'</div>
											<div class="desc">
												<span class="badge badge-info font-bold">OQ : '.floatVal($row->oq).'</span>
												<span class="badge badge-success font-bold">FQ : '.floatVal($row->fq).'</span>
												<span class="badge badge-warning font-bold">RQ : '.floatVal($row->rq).'</span>
												<span class="badge badge-danger font-bold">PQ : '.floatVal($pendingQty).'</span>
											</div>
										</div>
									</div>
								</div>';
				}
			}
			else
			{
				$prodData .= '<br><h4 class="card-title" style="font-size:2rem;text-align:center;color:#A90000;">No Running<br>Production Found...!</h4>';
			}
		}
		$html .= $prodData;
        $this->printJson(['html'=>$html]);
    }

	
}
?>