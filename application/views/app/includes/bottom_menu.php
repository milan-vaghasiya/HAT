<div class="appBottomMenu">
	

	<a href="<?=base_url('app/dashboard');?>" class="item <?=($this->data['headData']->controller == 'app/dashboard')?'active':''?>">
		<div class="col">
			<ion-icon name="pie-chart-outline"></ion-icon>
			<strong>Dashboard </strong>
		</div>
	</a>
	<?php 
	if(in_array($this->userRole,[-1,1])){
	?>
	<a href="<?=base_url('app/salesOrder');?>" class="item <?=(($this->data['headData']->controller == 'app/salesOrder') && $this->data['bottomMenuName'] == 'salesOrder')?'active':''?>">
		<div class="col">
			<ion-icon name="document-text-outline"></ion-icon>
			<strong>Sales Order</strong>
		</div>
	</a>

	<a href="<?=base_url('app/purchaseOrder');?>" class="item <?=(($this->data['headData']->controller == 'app/purchaseOrder') && $this->data['bottomMenuName'] == 'purchaseOrder')?'active':''?>">
		<div class="col">
			<ion-icon name="cart-outline"></ion-icon>
			<strong>Purchase Order</strong>
		</div>
	</a>
	<?php
	}
	?>
	<a href="<?=base_url('app/jobTracking/jobCard');?>" class="item <?=(($this->data['headData']->controller == 'app/jobTracking') && $this->data['bottomMenuName'] == 'jobCard')?'active':''?>">
		<div class="col">
		<ion-icon name="card-outline"></ion-icon>			
		<strong>Jobcard</strong>
		</div>
	</a>

</div>
