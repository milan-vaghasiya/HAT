<?php $this->load->view('app/includes/header'); ?>
<?php $this->load->view('app/includes/topbar'); ?>

<!-- Start Main Content -->
<div id="appCapsule" class="bg-light">
    <!-- <div class="section mt-2 mb-3">
        <div class="section-title">Sales Vs. Dispatch</div>
        <div class="card">
            <div class="card-body">
                <div id="chartOrderDispatch"></div>
            </div>
        </div>
    </div> -->
	<?php 
	if(in_array($this->userRole,[-1,1])){
		?>
		<div class="section">
			<div class="row mt-2">
				<div class="col-6">
					<div class="stat-box bg-warning">
						<div class="title">Pending PO</div>
						<div class="value"><?=!empty($poCount)?$poCount->total_po:0?></div>
					</div>
				</div>
				<div class="col-6">
					<div class="stat-box bg-danger">
						<div class="title">Pending SO</div>
						<div class="value"><?=!empty($soTransData)?count($soTransData):0?></div>
					</div>
				</div>
			</div>
			<div class="row mt-2">
				<div class="col-6">
					<div class="stat-box bg-secondary">
						<div class="title">Today's Purchase</div>
						<div class="value "><?=!empty($purchase->purchase_amount)?numberFormatIndia($purchase->purchase_amount):'0'?></div>
					</div>
				</div>
				<div class="col-6">
					<div class="stat-box bg-info">                                                                                             
						<div class="title">Today's Sales</div>
						<div class="value "><?=!empty($sales->sales_amount)?numberFormatIndia($sales->sales_amount):'0'?></div>
					</div>
				</div>
			</div>
		</div>
	
	
	<!-- * Exchange Action Sheet -->
	<div class="section pt-1 mb-2 bg-light">
		<div class="wallet-card">
			<div class="wallet-footer">
			
				<div class="item">
					<a href="<?=base_url('app/salesOrder');?>">
						<div class="icon-wrapper">
							<i class=" far fa-clipboard"></i>
						</div>
						<strong>Sales Order</strong>
					</a>
				</div>
				<div class="item">
					<a href="<?=base_url('app/purchaseOrder');?>" >
						<div class="icon-wrapper bg-danger">
							<ion-icon name="cart"></ion-icon>
						</div>
						<strong>Purchase Order</strong>
					</a>
				</div>
				
				<div class="item">
					<a href="<?=base_url('app/jobTracking/jobCard');?>">
						<div class="icon-wrapper bg-success">
							<ion-icon name="card-outline"></ion-icon>					
						</div>
						<strong>Job Card</strong>
					</a>
				</div>
				
				<!-- <div class="item">
					<a href="<?=base_url('app/jobTracking');?>">
						<div class="icon-wrapper bg-success">
						<ion-icon name="analytics-outline"></ion-icon>
						</div>
						<strong>Job Tracking</strong>
					</a>
				</div> -->
			</div>
		</div>
	</div>
	<?php
	}
	?>
	<div class="section   mb-2 bg-light">
		<div class="section-title">Production Analysis</div>
		<div class="card">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">Process Name</th>
							<th scope="col">Pend. Production</th>
							<th scope="col">Pend. Movement</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($prodAnalysis){
								foreach($prodAnalysis as $row){
										?>
										<tr>
											<th scope="row"><?=$row->process_name?></th>
											<td><?=floatval($row->pend_prod_qty)?></td>
											<td><?=floatval($row->pend_move_qty)?></td>
										</tr>
										<?php
								}
							}
						?>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>

<!-- End Main Content -->


<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<?php $this->load->view('app/includes/add_to_home'); ?>
<?php $this->load->view('app/includes/footer'); ?>

<!--
<script src="<?=base_url()?>assets/app/js/plugins/apexcharts/apexcharts.min.js"></script>
<script>
    $(document).ready(function(){ getSalesChartData(); });
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
					var options = 
                    {
            			  series: [{
                			  name: 'Orders',
                			  type: 'column',
                			  //data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30,40]
                			  data: response.orderData
                			}, {
                			  name: 'Dispatch',
                			  type: 'area',
                			  //data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43,28]
                			  data: response.dispatchData
                			}],
            			  chart: {
            			  height: 350,
            			  type: 'line',
            			  stacked: false,
            			},
            			stroke: {
            			  width: [0, 2, 5],
            			  curve: 'smooth'
            			},
            			plotOptions: {
            			  bar: {
            				columnWidth: '50%'
            			  }
            			},
            			
            			fill: {
            			  opacity: [0.85, 0.25, 1],
            			  gradient: {
            				inverseColors: false,
            				shade: 'light',
            				type: "vertical",
            				opacityFrom: 0.85,
            				opacityTo: 0.55,
            				stops: [0, 100, 100, 100]
            			  }
            			},
            			labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            			markers: {
            			  size: 0
            			},
            			xaxis: {
            			  type: 'text'
            			},
            			yaxis: {
            			  title: {
            				text: 'Amount (Thousand)',
            			  },
            			  min: 0
            			},
            			tooltip: {
            			  shared: true,
            			  intersect: false,
            			  y: {
            				formatter: function (y) {
            				  if (typeof y !== "undefined") {
            					return y.toFixed(0) + " points";
            				  }
            				  return y;
            			
            				}
            			  }
            			}
            		};
            
            		var chartOrderDispatch = new ApexCharts(document.querySelector("#chartOrderDispatch"), options);
            		chartOrderDispatch.render();
				}
		});
	}


    </script>
    
    -->