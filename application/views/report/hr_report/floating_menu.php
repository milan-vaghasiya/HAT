<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
		    <?php if($this->loginId == -1 or $this->loginId == 1){ ?>
    			<li><a href="<?=base_url('reports/hrReport/empReport')?>" class="bg-info">Employee Report</a></li>
    			<!--<li><a href="<?=base_url('reports/attendanceReport/mismatchPunch')?>" class="bg-warning">Missed Punch Report</a></li>-->
    			<li><a href="<?=base_url('reports/attendanceReport/monthlyAttendance')?>" class="bg-success">Monthly Attendance</a></li>
			<?php } ?>
			<!--<li><a href="<?=base_url('reports/hrReport/monthlyAttendanceSummary')?>" class="bg-primary">Monthly Summary Old</a></li>-->
			<li><a href="<?=base_url('reports/hrReport/empRecruitmentForm')?>" class="bg-info" target="_blank">Employee Recruitment Form</a></li>
			<li><a href="<?=base_url('reports/attendanceReport/monthlyAttendanceSummary')?>" class="bg-warning">Monthly Summary</a></li>
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