<!-- ============================================================== -->
<!-- Header -->
<!-- ============================================================== -->
    <?php $this->load->view('includes/header'); ?>
    <!--<link href="<?=base_url()?>assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/js/pages/chartist/chartist-init.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/extra-libs/c3/c3.min.css">-->
    <!-- This Page CSS -->
    <link href="<?=base_url()?>assets/libs/morris.js/morris.css" rel="stylesheet">
	<style>
		svg.ct-chart-bar, svg.ct-chart-line{
			overflow: visible;
		}
		.ct-label.ct-label.ct-horizontal.ct-end {
		  position: relative;
		  justify-content: flex-end;
		  text-align: right;
		  transform-origin: 100% 0;
		  transform: translate(-100%) rotate(-45deg);
		  white-space:nowrap;
		}
	</style>
	
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="row">
					<div class="col-lg-3">
						<div class="card bg-orange text-white">
							<div class="card-body">
								<div id="cc1" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6  icon-Receipt-2 text-white" title="Pending Purchase Order"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending Purchase Order</h4>
													<h5><?=!empty($poCount)?$poCount->total_po:0?></h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-success text-white">
							<div class="card-body">
								<div id="myCarousel22" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Receipt-3 text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Pending Sales Order</h4>
													<h5><?=!empty($soTransData)?count($soTransData):0?></h5>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-cyan text-white">
							<div class="card-body">
								<div id="myCarousel45" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6 icon-Shopping-Basket text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Today's Purchase</h4>
													<h5><?=!empty($purchase->purchase_amount)?numberFormatIndia($purchase->purchase_amount):'0'?></h5>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card bg-dark text-white">
							<div class="card-body">
								<div id="myCarousel33" class="carousel slide" data-ride="carousel">
									<div class="carousel-inner">
										<div class="carousel-item flex-column active">
											<div class="d-flex no-block align-items-center">
												<a href="JavaScript: void(0);"><i class="display-6  fas fa-rupee-sign text-white" title="BTC"></i></a>
												<div class="m-l-15 m-t-10">
													<h4 class="font-medium m-b-0">Today's Sales</h4>
													<h5><?=!empty($sales->sales_amount)?numberFormatIndia($sales->sales_amount):'0'?></h5>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>				
				
				<div class="row">
					<div class="col-lg-4">
						<div class="card">
							<div class="card-header">
                                <div class="row card-title">
                                    <div class="col-lg-12">
										<div class="input-group">
											<select name="so_trans_id" id="so_trans_id" class="form-control single-select" style="width:85%;" >
												<option value="">Select Order</option>
												<?php
													foreach($soTransData as $row):
														echo "<option value='".$row->id."' >".$row->trans_number." [".$row->grn_data." | ".formatDate($row->trans_date)."]</option>";
													endforeach;
												?>
											</select>
											<button class="btn btn-info trackOrder" type="button" style="width:15%;">Go!</button>
										</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="padding:1rem;">
                                <div class="sales_track scrollable text-center" style="height:300px;display:none;">
									<h5 class="text-primary">Track Sales Order</h5>
									<h5 class="card-subtitle">Here you will get process wise production status</h5>
									<img src="<?=base_url()?>assets/images/background/track_bg1.jpg" style = "width:90%;">
								</div>
								<div class="sales_track scrollable" id="trackData" style="height:350px;">
									<div style="text-align:center;"><img src="<?=base_url('assets/images/track_order.png')?>" style="height:280px;"></div>
								</div>
                            </div>
						</div>
                    </div>
					<!-- Production Analysis -->
					<div class="col-lg-4">
						<div class="card">
							<div class="card-header" style="height:60px">
								<h4 class="card-title">Production Analysis</h4>
                            </div>
                            <div class="card-body" style="padding:1rem;">
								<div class="sales_track scrollable"style="height:350px;">
									<div class="feed-widget scrollable ps-container ps-theme-default ps-active-y" style="height:450px;" data-ps-id="832a08fa-adff-eb5e-1055-45f56608de95">
										<ul class="list-style-none feed-body m-0 p-b-20">
											<li class="feed-item border-bottom ">
												<a   style="width:50%">  <b>Process Name</b> </a>
												<span class="ml-auto font-medium" style="width:30%">Pend. Production</span>
												<span class="ml-auto font-medium" style="width:30%" >Pend. Movement</span>
											</li>
											
												<?php
												if($prodAnalysis){
													foreach($prodAnalysis as $row){
															?>
															<li class="feed-item border-bottom ">
																<a   style="width:50%" >  <b><?=$row->process_name?></b> </a>
																<span class="ml-auto " style="width:30%">  <?=floatval($row->pend_prod_qty)?></span>
																<span class="ml-auto " style="width:30%"> <?=floatval($row->pend_move_qty)?></span>
															</li>
															<?php
													}
												}
												?>
											
											</ul>
									</div>
								</div>
                            </div>
						</div>
                    </div>
					<!-- End -->
					<!-- Top 10 Product -->
					<div class="col-lg-4">
						<div class="card">
							<div class="card-header" style="height:60px">
								<div class=" card-title">
									<h4 class="card-title">Top 10 Selling Products</h4>
                                </div>
                            </div>
                            <div class="card-body" style="padding:1rem;">
								<div class="sales_track scrollable" style="height:350px;">
									<div class="feed-widget scrollable ps-container ps-theme-default ps-active-y" style="height:450px;" data-ps-id="832a08fa-adff-eb5e-1055-45f56608de95">
									<?php
									if(!empty($topProducts)){
										?>
										<ul class="list-style-none feed-body m-0 p-b-20">
											<?php
											foreach($topProducts as $row){
												?>
												<li class="feed-item">
													<div class="feed-icon bg-info">
														<i class="ti-shopping-cart"></i>
													</div>
													<a  > 
														<?=$row->item_code?><br>
														<span class="ml-auto font-12 text-muted"><?=$row->item_name?></span>
													</a>
													<span class="ml-auto font-medium" "><?=numberFormatIndia($row->sales_amount)?></span>
												</li>
												<?php
											}
											?>
											
										</ul>
										<?php
									}
									?>
										
									</div>
								</div>
                            </div>
						</div>
                    </div>
					<!-- End -->

                  
                </div>
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-header">
                                <div class="row card-title">
                                    <div class="col-lg-6"><h4>Order & Sales</h4></div>
									<div class="col-lg-6 text-right">
										<h5><i class="fa fa-circle m-r-5 text-info"></i>Order
										<i class="fa fa-circle m-r-5 text-inverse"></i>Sales</h5>
									</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="order_chart" style="height: 350px;"></div>
                            </div>
                        </div>
                    </div>
				</div>
				<!--
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card earning-widget">
                            <div class="card-body">
                                <h4 class="m-b-0">Sellers</h4>
                            </div>
                            <div class="border-top scrollable" style="height:365px;">
                                <table class="table v-middle no-border">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;">
                                                <img src="assets/images/users/user_default.png" width="30" class="rounded-circle" alt="logo">
                                            </td>
                                            <td>Andrew Simon</td>
                                            <td align="right" class="font-medium fs-15">$2300</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Credit vs Debit</h4>
                                <div id="stacked-column"></div>
                            </div>
                        </div>
                    </div>
                </div>
				-->
            </div>
        </div>
<?php  $this->load->view('includes/footer');  ?>


<!--Morris JavaScript -->
<script src="<?=base_url()?>assets/libs/raphael/raphael.min.js"></script>
<script src="<?=base_url()?>assets/libs/morris.js/morris.min.js"></script>
<!--<script src="<?=base_url()?>assets/js/pages/morris/morris-data.js"></script>-->
<script src="<?=base_url()?>assets/js/pages/dashboards/dashboard3.js"></script>
<script>
	$(document).ready(function(){
		getSalesChartData();
	});
    $(document).on('click','.trackOrder',function(){		
		var so_trans_id = $('#so_trans_id').val();
		$.ajax({
				url:base_url + controller + '/trackOrderByTransId',
				type:'post',
				data:{so_trans_id:so_trans_id},
				dataType:'json',
				global:false,
				success:function(data)
				{
					$('#trackData').html(data.html)
				}
			});
	});
	function getSalesChartData()
	{
		$.ajax({
				url:base_url + controller + '/getSalesChartData',
				type:'post',
				data:{},
				dataType:'json',
				global:false,
				success:function(response)
				{
					var salesChart = Morris.Area({
													element: 'order_chart',
													data: response,
													lineColors: ['#55ce63', '#2962FF'],// '#2f3d4a'],
													xkey: 'period',
													ykeys: ['oq', 'dq'],
													labels: ['Order', 'Sales'],
													xLabelAngle: 50,
													pointSize: 0,
													lineWidth: 0,
													resize:true,
													fillOpacity: 0.8,
													behaveLikeLine: true,
													gridLineColor: '#e0e0e0',
													hideHover: 'auto',
													parseTime: false,
													resize: true
													
												});
				}
		});
	}
</script>