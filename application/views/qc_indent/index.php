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
									<li class="nav-item"> <button onclick="tabStatus('qcRequestTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
									<li class="nav-item"> <button onclick="tabStatus('qcRequestTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
									<li class="nav-item"> <button onclick="tabStatus('qcRequestTable',3);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Close</button> </li>
								</ul>
							</div>
							<div class="col-md-4">
								<h4 class="card-title text-center">QC Indent</h4>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table id='qcRequestTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Create Purchase Order</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_so" method="post" action="">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="error general"></div>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">Part Name</th>
										<th class="text-center">Qty.</th>
									</tr>
								</thead>
								<tbody id="orderData">
									<tr>
										<td class="text-center" colspan="3">No Data Found</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Challan</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
	$(document).ready(function() {
		initBulkInspectionButton();
		$(document).on('click', ".approvePreq", function() {
			var id = $(this).data('id');
			var val = $(this).data('val');
			var msg = $(this).data('msg');
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure want to ' + msg + ' this Purchase Request?',
				type: 'green',
				buttons: {
					ok: {
						text: "ok!",
						btnClass: 'btn waves-effect waves-light btn-outline-success',
						keys: ['enter'],
						action: function() {
							$.ajax({
								url: base_url + controller + '/approvePreq',
								data: {
									id: id,
									val: val,
									msg: msg
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
										initTable();
										toastr.success(data.message, 'Success', {
											"showMethod": "slideDown",
											"hideMethod": "slideUp",
											"closeButton": true,
											positionClass: 'toastr toast-bottom-center',
											containerId: 'toast-bottom-center',
											"progressBar": true
										});
									}
								}
							});
						}
					},
					cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function() {

						}
					}
				}
			});
		});

		$(document).on('click', ".closePreq", function() {
			var id = $(this).data('id');
			var val = $(this).data('val');
			var msg = $(this).data('msg');
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure want to ' + msg + ' this Purchase Request?',
				type: 'red',
				buttons: {
					ok: {
						text: "ok!",
						btnClass: 'btn waves-effect waves-light btn-outline-success',
						keys: ['enter'],
						action: function() {
							$.ajax({
								url: base_url + controller + '/closePreq',
								data: {
									id: id,
									val: val,
									msg: msg
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
										initTable();
										toastr.success(data.message, 'Success', {
											"showMethod": "slideDown",
											"hideMethod": "slideUp",
											"closeButton": true,
											positionClass: 'toastr toast-bottom-center',
											containerId: 'toast-bottom-center',
											"progressBar": true
										});
									}
								}
							});
						}
					},
					cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function() {

						}
					}
				}
			});
		});

		$(document).on('click', '.createPurchaseOrder', function() {
			$.ajax({
				url: base_url + controller + '/getPurchaseOrder',
				type: 'post',
				data: {},
				dataType: 'json',
				success: function(data) {
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Purchase Order');
					$("#party_so").attr('action', base_url + 'purchaseOrder/createOrder');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Purchase Order');
					$("#orderData").html("");
					$("#orderData").html(data.htmlData);
				}
			});
		});
		
		$(document).on('click', '.BulkQcRequest', function() {
			if ($(this).attr('id') == "masterQcSelect") {
				if ($(this).prop('checked') == true) {
					$(".bulkPO").show();
					$("input[name='ref_id[]']").prop('checked', true);
				} else {
					$(".bulkPO").hide();
					$("input[name='ref_id[]']").prop('checked', false);
				}
			} else {
				if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
					$(".bulkPO").show();
					$("#masterQcSelect").prop('checked', false);
				} else {
					$(".bulkPO").hide();
				}

				if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
					$("#masterQcSelect").prop('checked', true);
					$(".bulkPO").show();
				}
				else{$("#masterQcSelect").prop('checked', false);}
			}
		});
		

		$(document).on('click', '.bulkPO', function() {
			var ref_id = [];
			$("input[name='ref_id[]']:checked").each(function() {
				ref_id.push(this.value);
			});
			var ids = ref_id.join("~");
			var send_data = {
				ids
			};
			$.confirm({
				title: 'Confirm!',
				content: 'Are you sure want to generate PO?',
				type: 'red',
				buttons: {
					ok: {
						text: "ok!",
						btnClass: 'btn waves-effect waves-light btn-outline-success',
						keys: ['enter'],
						action: function() {
							window.open(base_url + 'qcPurchase/addPOFromRequest/' + ids, '_self');

						}
					},
					cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function() {

						}
					}
				}
			});
		});
	});

	function inspectionTransTable() {
		var inspectionTrans = $('#qcRequestTable').DataTable({
			lengthChange: false,
			responsive: true,
			'stateSave': true,
			retrieve: true,
			buttons: ['pageLength', 'copy', 'excel']
		});
		inspectionTrans.buttons().container().appendTo('#qcRequestTable_wrapper .col-md-6:eq(0)');
		return inspectionTrans;
	}

	function initBulkInspectionButton() {
		var bulkPOBtn = '<button class="btn btn-outline-primary bulkPO" tabindex="0" aria-controls="qcRequestTable" type="button"><span>Bulk PO</span></button>';
		$("#qcRequestTable_wrapper .dt-buttons").append(bulkPOBtn);
		$(".bulkPO").hide();
	}

	function tabStatus(tableId, status) {
		$("#" + tableId).attr("data-url", '/getDTRows/' + status);
		ssTable.state.clear();
		initTable();
	}
</script>