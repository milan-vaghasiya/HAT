<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/storeReport/issueRegister')?>" class="bg-info">Issue Register (F ST 04 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/storeReport/stockRegister')?>" class="bg-success">Stock (Consumable) Register (F ST 05 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/storeReport/stockRegisterRawMaterial')?>" class="bg-facebook">Stock (Raw Material) Register (F ST 02 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/storeReport/inventoryMonitor')?>" class="bg-warning">Inventory Monitoring (F ST 08 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/storeReport/consumableReport')?>" class="bg-danger">Consumable Report (D ST 01 00/01.06.20)</a></li>
			<li><a href="<?=base_url('reports/storeReport/fgStockReport')?>" class="bg-primary">Stock Statement FG (F ST 06 00/01.06.20)</a></li>
            <li><a href="<?=base_url('reports/storeReport/toolissueRegister')?>" class="bg-info">Tool Issue Register</a></li>		
			<!-- Updated By Mansee @ 09-12-2021 -->
			<li><a href="<?=base_url('reports/storeReport/scrapBook')?>" class="bg-success">Scrap Book</a></li>	
			<li><a href="<?=base_url('reports/storeReport/itemHistory')?>" class="bg-dribbble">Item History</a></li>	
			<li><a href="<?=base_url('reports/storeReport/storeWiseStockReport')?>" class="bg-info">Store Wise Stock Statement</a></li>
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