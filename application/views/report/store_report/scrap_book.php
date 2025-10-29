<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-3">
								<h4 class="card-title pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-9">
								<div class="input-group">
								    <input type="date" name="from_date" id="from_date" class="form-control" value="<?= date('Y-m-d') ?>" style="width:12%;" />
								    <input type="date" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>" style="width:12%;" />
								    <select id="scrap_group" data-input_id="scrap_group" class="form-control single-select" style="width:25%;">
										<option value="ALL">ALL Scrap Group</option>
										<?php
										foreach ($scrapGroup as $row) :
											echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
										endforeach;
										?>
									</select>
									<!-- <select id="material_grade" data-input_id="material_grade" class="form-control single-select" style="width:25%;">
										<option value="ALL">ALL Material Grade</option>
										<?php
										// foreach ($materialGrade as $row) :
										// 	echo '<option value="' . $row->material_grade . '">' . $row->material_grade . '</option>';
										// endforeach;
										?>
									</select> -->
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error material_grade"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Item Name</th>
										<th>Scrap Qty</th>
										<th>Price</th>
										<th>Valuation</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot class="thead-info">
									<tr>
										<th colspan="2" class="text-center">Total</th>
										<th id="total_qty">0</th>
										<th id="avg_price">0</th>
										<th id="net_valuation">0</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>					
				</div>
			</div>
		</div>
	</div>


	<?php $this->load->view('includes/footer'); ?>
	<?= $floatingMenu ?>
	<script>
		$(document).ready(function() {
			reportTable();
			$(document).on('click', '.loaddata', function(e) {
				$(".error").html("");
				var valid = 1;
				var from_date = $('#from_date').val();
				var to_date = $('#to_date').val();
				// var material_grade = $('#material_grade').val();
				var scrap_group=$("#scrap_group").val();
				if ($("#from_date").val() == "") {
					$(".fromDate").html("From Date is required.");
					valid = 0;
				}
				if ($("#to_date").val() == "") {
					$(".toDate").html("To Date is required.");
					valid = 0;
				}
				if ($("#to_date").val() < $("#from_date").val()) {
					$(".toDate").html("Invalid Date.");
					valid = 0;
				}
				if (valid) {
					$.ajax({
						url: base_url + controller + '/getScrapReport',
						data: {
							from_date: from_date,
							to_date: to_date,
							scrap_group:scrap_group
						},
						type: "POST",
						dataType: 'json',
						success: function(data) {
							$("#reportTable").dataTable().fnDestroy();
							$("#tbodyData").html(data.tbody);
							$("#avg_price").html(data.avg_price);
							$("#net_valuation").html(data.net_valuation);
							$("#total_qty").html(data.total_qty);
							reportTable();
						}
					});
				}
			});
		});

		function reportTable() {
			var reportTable = $('#reportTable').DataTable({
				responsive: true,
				scrollY: '55vh',
				scrollCollapse: true,
				"scrollX": true,
				"scrollCollapse": true,
				//'stateSave':true,
				"autoWidth": false,
				order: [],
				"columnDefs": [{
						type: 'natural',
						targets: 0
					},
					{
						orderable: false,
						targets: "_all"
					},
					{
						className: "text-center",
						targets: [0, 2]
					},
					{
						className: "text-center",
						"targets": "_all"
					}
				],
				pageLength: 25,
				language: {
					search: ""
				},
				lengthMenu: [
					[10, 25, 50, 100, -1],
					['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
				],
				dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
				buttons: ['pageLength', 'excel', {
					text: 'Refresh',
					action: function(e, dt, node, config) {
						$(".loaddata").trigger('click');
					}
				}]
			});
			reportTable.buttons().container().appendTo('#reportTable_wrapper toolbar');
			$('.dataTables_filter .form-control-sm').css("width", "97%");
			$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
			$('.dataTables_filter').css("text-align", "left");
			$('.dataTables_filter label').css("display", "block");
			$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
			$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
			return reportTable;
		}
	</script>