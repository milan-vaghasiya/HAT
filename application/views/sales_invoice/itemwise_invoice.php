<?php 
    $this->load->view('includes/header');
	$etype = "6,7,8";
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-3">
                                <div class="input-group ">
									<a href="<?=base_url($headData->controller)?>" class="btn btn-outline-info float-right" title="Load Data">Bill Wise</a>
									<a href="<?=base_url($headData->controller."/itemwiseInvoice")?>" class="btn btn-info float-right" title="Load Data">Item Wise</a>
								</div>
							</div>
							<div class="col-md-7">
                                <div class="input-group ">
									<select name="sales_type" id="sales_type" class="form-control single-select sales_type" style="width:30%">
										<option value="">Select All</option>
										<option value="1">Manufacturing (Domestics)</option>
										<option value="2">Manufacturing (Export)</option>
										<option value="3">Jobwork (Domestics)</option>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-d')?>" min="<?=$startYearDate?>" max="<?=$maxDate?>"  />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" min="<?=$startYearDate?>" max="<?=$maxDate?>"  />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data"><i class="fas fa-sync-alt"></i> Load</button>
                                    </div>
								</div>
							</div>
							<div class="col-md-2"> 
                                <a href="<?=base_url($headData->controller."/addInvoice")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write ml-2"><i class="fa fa-plus"></i> Add Invoice</a>
							</div>                          
                        </div>   
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='salesInvoiceTable' class="table table-bordered dtTable dtTable-cf" data-ninput='[0,1]' data-url='/getItemWiseDTRows/'.<?=$etype?>>
								<thead class="thead-info">
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Invoice No.</th>
                                        <th class="text-center">Invoice Date</th>
                                        <th class="text-left">Customer Name</th>
                                        <th class="text-left">Item Name</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Description</th>
                                        <th class="text-center">HSN/SAC</th>
                                        <th class="text-center">GST <small>%</small></th>
                                        <th class="text-center">Qty.</th>
                                        <th class="text-center">UOM</th>
                                        <th class="text-center">Rate<br><small></small></th>
                                        <th class="text-center">Amount<br><small></small></th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
  
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/invoice_pdf')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
							<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="original" id="original" class="filled-in chk-col-success" value="1" checked>
									<label for="original">Original</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="duplicate" id="duplicate" class="filled-in chk-col-success" value="1" checked>
									<label for="duplicate">Duplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="triplicate" id="triplicate" class="filled-in chk-col-success" value="0">
									<label for="triplicate">Triplicate</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="header_footer" id="header_footer" class="filled-in chk-col-success" value="1" checked>
									<label for="header_footer">Header/Footer</label>
								</div>
							</div>
							<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
								<label>No. of Extra Copy</label>
								<input type="text" name="extra_copy" id="extra_copy" class="form-control" value="0">
								<input type="hidden" name="printsid" id="printsid" value="0">
								<label class="error_extra_copy text-danger"></label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function(){
	    
		setTimeout(function(){initInvTable();},100);
		$(document).on('click','.createItemList',function(){		
			var id = $(this).data('id');
			var party_name = $(this).data('party_name');

			$.ajax({
				url : base_url + controller + '/getItemList',
				type: 'post',
				data:{id:id},
				dataType:'json',
				success:function(data){
					$("#itemModal").modal();
					$("#partyName").html(party_name);
					$("#party_name").val(party_name);
					$("#party_id").val(party_id);
					$("#itemData").html("");
					$("#itemData").html(data.htmlData);
				}
			});
		});

		<?php if(!empty($printID)): ?>
			$("#printModel").attr('action',base_url + controller + '/invoice_pdf');
			$("#printsid").val(<?=$printID?>);
			$("#print_dialog").modal();
		<?php endif; ?>
	
	
		$(document).on("click",".printInvoice",function(){
			$("#printModel").attr('action',base_url + controller + '/invoice_pdf');
			$("#printsid").val($(this).data('id'));
			$("#print_dialog").modal();
		});

		$(document).on("change","#sales_type",function(){
			$("#salesInvoiceTable").attr("data-url",'/getItemWiseDTRows/'+$(this).val());
			initInvTable();
		});
		
		$(document).on('click','.loaddata',function(){initInvTable();});
		$(document).on('change','#party_id',function(){initInvTable();});
	});

	function closeModal(modalId)
	{
		$("#"+ modalId).modal('hide');
		
		<?php if(!empty($printID)): ?>
			window.location = base_url + controller;
		<?php endif; ?>
	}
	function dtTablePdf(){initInvTable('pdf')}
	function initInvTable(pdf=""){
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var sales_type = $('#sales_type').val();
		var party_id="";
		if($('#party_id').val()){party_id = $('#party_id').val();}
		if(pdf == "")
		{
    		$('.dtTable').DataTable().clear().destroy();
    		var dataSet = {from_date:from_date, to_date:to_date,party_id:party_id}
		    defaultDtTable($('.dtTable'),dataSet);
		}
		var ctb= getInvoiceSummary(sales_type,from_date,to_date,party_id,pdf);
	}
	function getInvoiceSummary(sales_type,from_date,to_date,party_id,pdf){
		
		if(sales_type == ""){sales_type = '';}
		var postData = {sales_type:sales_type,from_date:from_date, to_date:to_date,party_id,pdf:pdf};
		
		if(pdf == "")
		{
		    var ctb = '';
			$.ajax({
				url : base_url + controller + '/getInvoiceSummarybillWise',
				type: 'post',
				data:postData,
				dataType:'json',
				success:function(data){
					var tamt = (data.taxable_amount != null) ? inrFormat(data.taxable_amount) : 0;
					var namt = (data.net_amount != null) ? inrFormat(data.net_amount) : 0;
					ctb = '<span class="inv-head-select">'+data.custOption+'</span><span class="inv-head-stat">Taxable : '+tamt+'</span><span class="inv-head-stat">Net Sales : '+namt+'</span>';
					//copt = ;
					if(ctb != ""){$('div.ctbtr').html(ctb);}$('#party_id').comboSelect()
					//if(data.custOption != ""){$('div.cstfilter').html(data.custOption);$('#party_id').comboSelect();}
					//console.log('copt = '+data.custOption);
				}
			});
		}
		else
		{
			var url = base_url + controller + '/getInvoiceSummarybillWise/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			console.log(url);
			window.open(url);
		}
	}
</script>
