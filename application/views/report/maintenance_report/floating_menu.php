<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/maintenanceReport/machineReport')?>" class="bg-info">Machine Report (D MT 01 00/01.06.20)</a></li>
			<li><a href="<?=base_url('machineTicket/maintenanceLogReport')?>" class="bg-warning">Maintenance Log Book Report</a></li>
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