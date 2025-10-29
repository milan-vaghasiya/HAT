<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/productionReportNew/jobProduction')?>" class="bg-info">Job Wise Production </a></li>
			<li><a href="<?=base_url('reports/productionReportNew/jobworkRegister')?>" class="bg-success">Jobwork Register (F ST 11 00/01.06.2020)</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/machineWise')?>" class="bg-dribbble">Machine Wise OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/operatorWiseOee')?>" class="bg-warning">Operator Wise OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/oeeRegister')?>" class="bg-facebook">OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/dailyOeeRegister')?>" class="bg-success">Daily OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/stageProduction')?>" class="bg-danger">Stage Wise Production</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/jobcardRegister')?>" class="bg-primary">Jobcard Register (F PL 09 00/01.06.2020)</a></li>
			<li><a href="<?=base_url('reports/productionReportNew/operatorMonitoring')?>" class="bg-info">Operator Monitoring</a></li>
			<!-- <li><a href="<?=base_url('reports/productionReportNew/operatorPerformance')?>" class="bg-warning">Operator Performance</a></li> -->
		    <li><a href="<?=base_url('reports/productionReportNew/productionBom')?>" class="bg-dribbble">Item Bom Report</a></li>  
		    <li><a href="<?=base_url('reports/productionReportNew/rmPlaning')?>" class="bg-facebook">RM Planing</a></li> 
		    <li><a href="<?=base_url('reports/productionReportNew/fgTracking')?>" class="bg-success">FG Tracking</a></li> 
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