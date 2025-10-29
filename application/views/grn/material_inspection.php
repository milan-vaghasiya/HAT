<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
							<div class="col-md-4">
								<ul class="nav nav-pills">
									<li class="nav-item"> <button onclick="statusInspectTab(0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
									<li class="nav-item"> <button onclick="statusInspectTab(1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
								</ul>      
							</div>    
							<div class="col-md-4">
								<h4 class="card-title text-center">Raw Material Receiving <br>Inspection</h4>
							</div>       
						</div>                          
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseInvoiceMaterialInspectionTable' class="table table-bordered ssTable" data-url='/purchaseMaterialInspectionList'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="inspectionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Material Inspection</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="inspectedMaterial">
                    <div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<label for="">GRN No. : </label>
								<input type="text" id="grnNo" class="form-control" value="" readonly />
								<input type="hidden" name="grn_id" id="grn_id" value="" />
							</div>
							<div class="col-md-3">
								<label for="">GRN Date</label>
								<input type="text" id="grnDate" class="form-control" value="" readonly />
							</div>
							<div class="col-md-6">
								<label for="">Item Name</label>
								<input type="text" id="itemName" class="form-control" value="" readonly />
							</div>
						</div>
					</div>
					<hr>
					<div class="col-md-12">
						<div class="row">
							<div class="table-responsive">
								<table class="table table-bordered align-items-center">
									<thead class="thead-info">
										<tr class="text-center">
											<th style="width:5%;">#</th>
											<th>Batch No.</th>
											<th style="width:22%">Received Qty</th>
											<th style="width:22%">Status</th>
                                            <th style="width:22%">Short Qty</th>
										</tr>
									</thead>
									<tbody id="recivedItems">
										<tr>
											<td class="text-center" colspan="5">No data available in table</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </form>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="inspectedMaterialSave('inspectedMaterial');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approveInspectionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header info">
				<h4 class="modal-title">Approve Inspection</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<input type="hidden" id="id">
			<div class="modal-body" id="inspectionDataBody"></div>
            <div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="saveApproveRemarks()"><i class="fa fa-check"></i> Approve</button>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/purchase-material-inspection.js?v=<?= time() ?>"></script>
<script>
	function statusInspectTab(status) {
		$("#purchaseInvoiceMaterialInspectionTable").attr("data-url", '/purchaseMaterialInspectionList/' + status);
		ssTable.state.clear();
		initTable();
	}

	$(document).ready(function() {
		$(document).on('click', ".approveInspection", function() {
			var id = $(this).data('id');
			var val = $(this).data('val');
			var msg = $(this).data('msg');
			$('#id').val(id);

			$('#approveInspectionModel').modal();
			$.ajax({
				url: base_url + controller + '/getInspectionData',
				data: {id: id},
				type: "POST",
				dataType: "json",
				success: function(data) {
					$('#inspectionDataBody').html(data);
				}
			});
		});
		
		$(document).on('click',".rejectInspection",function(){
			var id = $(this).data('id');
			var val = $(this).data('val');
			var msg= $(this).data('msg');
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure want to '+ msg +' this Inspection?',
				type: 'red',
				buttons: {   
					ok: {
						text: "ok!",
						btnClass: 'btn waves-effect waves-light btn-outline-success',
						keys: ['enter'],
						action: function(){
							$.ajax({
								url: base_url + controller + '/approveInspection',
								data: {id:id,val:val,msg:msg},
								type: "POST",
								dataType:"json",
								success:function(data)
								{
									if(data.status==0)
									{
										toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
									}
									else
									{
										initTable(1); 
										toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
									}
								}
							});
						}
					},
					cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function(){
		
						}
					}
				}
			});
		});
	});

	function saveApproveRemarks() {
		var approval_remarks = $("#approval_remarks").val();
		var id = $("#id").val();
		$.ajax({
			url: base_url + controller + '/approveInspection',
			data: {
				id: id,
				val: '1',
				msg: 'Approve',
				approval_remarks: approval_remarks
			},
			type: "POST",
			dataType: "json",
			success: function(data) {
				if (data.status == 0) {
					toastr.error(data.message, 'Sorry...!', {
						"showMethod": "slideDown",
						"hideMethod": "slideUp",
						"closeButton": true,
						positionClass: 'toastr toast-bottom-center',
						containerId: 'toast-bottom-center',
						"progressBar": true
					});
				} else {
					initTable(1); $(".modal").modal('hide'); $("#approval_remarks").val("");
					toastr.success(data.message, 'Success', {
						"showMethod": "slideDown",
						"hideMethod": "slideUp",
						"closeButton": true,
						positionClass: 'toastr toast-bottom-center',
						containerId: 'toast-bottom-center',
						"progressBar": true
					});
					//window.location.reload();
				}
			}
		});
	}
	
	function tcInspe(data){
    	var button = data.button;if(button == "" || button == null){button="both";};
    	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
    	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
    	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
    	var sendData = {grn_trans_id:data.grn_trans_id,grn_id:data.grn_id,type:data.type};
    	if(data.approve_type){sendData = {grn_trans_id:data.grn_trans_id,approve_type:data.approve_type};}
    	$.ajax({ 
    		type: "POST",   
    		url: base_url + controller + '/' + fnEdit,   
    		data: sendData,
    	}).done(function(response){
    		$("#"+data.modal_id).modal();
    		$("#"+data.modal_id+' .modal-title').html(data.title);
    		$("#"+data.modal_id+' .modal-body').html(response);
    		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
    		//$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
    		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
    		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
    		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
    		if(button == "close"){
    			$("#"+data.modal_id+" .modal-footer .btn-close").show();
    			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
    			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
    		}else if(button == "save"){
    			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
    			$("#"+data.modal_id+" .modal-footer .btn-save").show();
                $("#"+data.modalId+" .modal-footer .btn-save-close").show();
    		}else{
    			$("#"+data.modal_id+" .modal-footer .btn-close").show();
    			$("#"+data.modal_id+" .modal-footer .btn-save").show();
                $("#"+data.modalId+" .modal-footer .btn-save-close").show();
    		}
    		//initModalSelect();
    		$(".single-select").comboSelect();
    		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
    		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
    		initMultiSelect();setPlaceHolder();
    	});
    }


</script>