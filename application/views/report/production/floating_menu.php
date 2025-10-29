<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/productionReport/jobProduction')?>" class="bg-info">Job Wise Production </a></li>
			<li><a href="<?=base_url('reports/productionReport/jobworkRegister')?>" class="bg-success">Jobwork Register (F ST 11 00/01.06.2020)</a></li>
			<li><a href="<?=base_url('reports/productionReport/productionAnalysis')?>" class="bg-warning">Production Analysis</a></li>
			<li><a href="<?=base_url('reports/productionReport/machineWise')?>" class="bg-dribbble">Machine Wise OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReport/oeeRegister')?>" class="bg-facebook">General OEE Register</a></li>
			<li><a href="<?=base_url('reports/productionReport/stageProduction')?>" class="bg-danger">Stage Wise Production</a></li>
			<li><a href="<?=base_url('reports/productionReport/jobcardRegister')?>" class="bg-primary">Jobcard Register (F PL 09 00/01.06.2020)</a></li>
			<li><a href="<?=base_url('reports/productionReport/operatorMonitoring')?>" class="bg-info">Operator Monitoring</a></li>
			<li><a href="<?=base_url('reports/productionReport/operatorPerformance')?>" class="bg-warning">Operator Performance</a></li>
		    <li><a href="<?=base_url('reports/productionReport/productionBom')?>" class="bg-dribbble">Item Bom Report</a></li>  
		    <li><a href="<?=base_url('reports/productionReport/rmPlaning')?>" class="bg-facebook">RM Planing</a></li> 
		    <li><a href="<?=base_url('reports/productionReport/fgTracking')?>" class="bg-success">FG Tracking</a></li> 
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