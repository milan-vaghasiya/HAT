<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/qualityReport/batchHistory')?>" class="bg-info">Batch Wise History</a></li>
			<li><a href="<?=base_url('reports/qualityReport/batchTracability')?>" class="bg-success">Batch Tracability</a></li>
			<li><a href="<?=base_url('reports/qualityReport/supplierRating')?>" class="bg-warning">Supplier Rating (F PU 06 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/qualityReport/vendorRating')?>" class="bg-dribbble">Vendor Rating (F PL 03 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/qualityReport/measuringThread')?>" class="bg-facebook">Measuring Thread (D QA 03 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/qualityReport/measuringInstrument')?>" class="bg-danger">Measuring Instrument (D QA 02 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/qualityReport/rejectionReworkMonitoring')?>" class="bg-info">Rejection & Rework Monitoring Report</a></li>
			<li><a href="<?=base_url('reports/qualityReport/rejectionMonitoring')?>" class="bg-info">Rejection Monitoring Report</a></li>
			<li><a href="<?=base_url('reports/qualityReport/ncReport')?>" class="bg-success">Final Inspection NC Report</a></li>
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