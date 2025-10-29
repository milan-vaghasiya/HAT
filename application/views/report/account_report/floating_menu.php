<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/accountingReport/salesRegisterReport')?>" class="bg-info">Sales Register</a></li>
			<li><a href="<?=base_url('reports/accountingReport/purchaseRegisterReport')?>" class="bg-success">Purchase Register</a></li>
			<li><a href="<?=base_url('reports/accountingReport/stockRegisterReport')?>" class="bg-warning">Stock Register</a></li>
			<li><a href="<?=base_url('reports/accountingReport/receivableReport')?>" class="bg-danger">Receivable</a></li>
			<li><a href="<?=base_url('reports/accountingReport/payableReport')?>" class="bg-info">Payable</a></li>
			<li><a href="<?=base_url('reports/accountingReport/bankBookReport')?>" class="bg-success">Bank Book</a></li>
			<li><a href="<?=base_url('reports/accountingReport/cashBookReport')?>" class="bg-warning">Cash Book</a></li>
			<li><a href="<?=base_url('reports/accountingReport/accountLedgerReport')?>" class="bg-danger">Account Ledger</a></li>
			<li><a href="<?=base_url('reports/accountingReport/debitNoteRegisterReport')?>" class="bg-info">Debit Note Register</a></li>
			<li><a href="<?=base_url('reports/accountingReport/creditNoteRegisterReport')?>" class="bg-success">Credit Note Register</a></li>
			<li><a href="<?=base_url('reports/accountingReport/salesReport')?>" class="bg-warning">Sales Report</a></li>
			<li><a href="<?=base_url('reports/accountingReport/purchaseReport')?>" class="bg-danger">Purchase Report</a></li>
			<li><a href="<?=base_url('reports/accountingReport/outstandingReport')?>" class="bg-info">Outstanding</a></li>
			<li><a href="<?=base_url('reports/accountingReport/gstr1Report')?>" class="bg-facebook">GSTR 1</a></li>
			<li><a href="<?=base_url('reports/accountingReport/gstr2Report')?>" class="bg-warning">GSTR 2</a></li>
			<li><a href="<?=base_url('reports/accountingReport/trailBalance')?>" class="bg-info">Trail Blanace</a></li>
			<li><a href="<?=base_url('reports/accountingReport/profitAndLoss')?>" class="bg-warning">Profit & Loss</a></li>
			<li><a href="<?=base_url('reports/accountingReport/balanceSheet')?>" class="bg-success">Balance Sheet</a></li>

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