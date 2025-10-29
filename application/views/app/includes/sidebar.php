<div class="modal fade panelbox panelbox-left" id="sidebarPanel" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				<!-- profile box -->
				<div class="profileBox bg-light p-1">
					<div class="image-wrapper">
						<img src="<?= base_url(); ?>assets/app/img/users/user_default.png" alt="image" class="imaged  w36">
					</div>
					<div class="in">
						<strong><?= $this->session->userdata('emp_name') ?></strong>
						<!-- <div class="text-muted"><?= $this->session->userdata('emp_code') ?></div> -->
					</div>
					<a href="#" class="btn btn-link btn-icon sidebar-close" data-bs-dismiss="modal">
						<ion-icon name="close-outline"></ion-icon>
					</a>
				</div>
				<ul class="listview flush transparent no-line image-listview">
					<li>
						<a href="<?= base_url('app/dashboard'); ?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="pie-chart-outline"></ion-icon></div>
							<div class="in">Dashboard</div>
						</a>
					</li>
					<?php 
					if(in_array($this->userRole,[-1,1])){
					?>
					<li>
						<a  href="<?= base_url('app/salesOrder'); ?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="document-text-outline"></ion-icon></div>
							<div class="in">Sales Order</div>
						</a>
					</li>
					<li>
						<a href="<?=base_url('app/purchaseOrder');?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="cart-outline"></ion-icon></div>
							<div class="in">Purchase Order</div>
						</a>
					</li>
					<?php
					}
					?>
					<li>
						<a href="<?=base_url('app/jobTracking/jobCard');?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="card-outline"></ion-icon></div>
							<div class="in">Jobcard</div>
						</a>
					</li>
					
				</ul>
				<!-- * menu -->

				<!-- others -->
				<!-- <div class="listview-title mt-1">Others</div> -->
				<!-- <ul class="listview flush transparent no-line image-listview">
					<li>
						<a href="<?= base_url('dashboard/stockRegister'); ?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="server-outline"></ion-icon></div>
							<div class="in">Stock Register</div>
						</a>
					</li>
					<li>
						<a href="<?= base_url('dashboard/accountLedger'); ?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="logo-usd"></ion-icon></div>
							<div class="in">Accout Register</div>
						</a>
					</li>

					<li>
						<a href="<?= base_url('logout') ?>" class="item">
							<div class="icon-box bg-primary"><ion-icon name="log-out-outline"></ion-icon></div>
							<div class="in">Log out</div>
						</a>
					</li>
				</ul> -->
			</div>
		</div>
	</div>
</div>