<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/salesReport/orderMonitor')?>" class="bg-info">Customer Order Monitoring (F MK 05 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/salesReport/dispatchPlan')?>" class="bg-success">Dispatch Plan (F PL 10 00/01.07.2021)</a></li>
			<li><a href="<?=base_url('reports/salesReport/dispatchPlanSummary')?>" class="bg-primary">Monthly Order Summary</a></li>
			<li><a href="<?=base_url('reports/salesReport/orderSummary')?>" class="bg-warning">Sales Order Summary</a></li>
			<li><a href="<?=base_url('reports/salesReport/dispatchSummary')?>" class="bg-dribbble">Customer wise Dispatch Report</a></li>			
			<!--<li><a href="<?=base_url('reports/salesReport/itemHistory')?>" class="bg-facebook">Item History</a></li>-->
			<li><a href="<?=base_url('reports/salesReport/salesEnquiry')?>" class="bg-danger">Regreated Enquiry</a></li>
		    <li><a href="<?=base_url('reports/salesReport/monthlySales')?>" class="bg-success">Monthly Sales</a></li>
			<!--<li><a href="<?=base_url('reports/salesReport/packingReport')?>" class="bg-info">Packing Report</a></li>-->
			<li><a href="<?=base_url('reports/salesReport/enquiryMonitoring')?>" class="bg-success">Enquiry v/s order</a></li>			
			<li><a href="<?=base_url('reports/salesReport/salesTarget')?>" class="bg-primary">Target v/s Sales</a></li>		
			<li><a href="<?=base_url('reports/salesReport/customerEnquiryRegister')?>" class="bg-danger">Customer Enquiry Register (F MK 01 (00/01.06.20))</a></li>
		</ul>
	</div>
</div>
<script>
$(document).ready(function(){
	
	$(document).on('click','.floatingButton',
		function(e){
			e.preventDefault();
			$(this).toggleClass('open');
			if($(this).children('.fa').hasClass('fa-plus'))
			{
				$(this).children('.fa').removeClass('fa-plus');
				$(this).children('.fa').addClass('fa-times');
			} 
			else if ($(this).children('.fa').hasClass('fa-times')) 
			{
				$(this).children('.fa').removeClass('fa-times');
				$(this).children('.fa').addClass('fa-plus');
			}
			$('.floatingMenu').stop().slideToggle();
		}
	);
	$(this).on('click', function(e) {
		var container = $(".floatingButton");

		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && $('.floatingButtonWrap').has(e.target).length === 0) 
		{
			if(container.hasClass('open'))
			{ 
				container.removeClass('open'); 
			}
			if (container.children('.fa').hasClass('fa-times')) 
			{
				container.children('.fa').removeClass('fa-times');
				container.children('.fa').addClass('fa-plus');
			}
			$('.floatingMenu').hide();
		}
	});
});
</script>