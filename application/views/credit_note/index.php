<?php $this->load->view('includes/header');
	//$etype = "6,7,8";
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-3">
                                <h4 class="card-title">Credit Note</h4>
							</div>
							<div class="col-md-9"> 
                                <div class="input-group ">
									<!-- <select name="entry_type" id="entry_type" class="form-control single-select entry_type" style="width:30%">
										<option value="">Select All</option>
										<option value="6">Manufacturing (Domestics)</option>
										<option value="8">Manufacturing (Export)</option>
										<option value="7">Jobwork (Domestics)</option>
									</select> -->
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									    <a href="<?=base_url($headData->controller."/addCreditNote")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write ml-2"><i class="fa fa-plus"></i> Add Credit Note</a>
                                    </div>
								</div>
							</div>                          
                        </div>   
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='creditNoteTable' class="table table-bordered ssTable" data-url='/getDTRows/'></table>
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
									<input type="checkbox" name="duplicate" id="duplicate" class="filled-in chk-col-success" value="0">
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

		$(document).on("change",".entry_type",function(){
			$("#creditNoteTable").attr("data-url",'/getDTRows/'+$("#entry_type").val());
    		ssTable.state.clear();initTable();
		});
		
		//CREATED By Karmi 14/12/2021
		$(document).on('click','.loaddata',function(){
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			initCreditTable(from_date,to_date);
		});
	});

	function closeModal(modalId)
	{
		$("#"+ modalId).modal('hide');
		
		<?php if(!empty($printID)): ?>
			window.location = base_url + controller;
		<?php endif; ?>
	}
	
	//CREATED By Karmi 14/12/2021
	function initCreditTable(from_date,to_date){
		$('.ssTable').dataTable().fnDestroy();
		var tableOptions = {pageLength: 25,'stateSave':false};
		var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
		var dataSet = {from_date:from_date, to_date:to_date}
		ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
	}
</script>
