<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-4 form-group">
								<h4 class="card-title text-left pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-3 form-group ">
								<select name="emp_id" id="emp_id" class="form-control single-select">
									<option value="">Select All</option>
									<?php
									foreach ($empData as $row) :
										echo '<option value="' . $row->id . '">' . $row->emp_name . '</option>';
									endforeach;
									?>
								</select>
								<div class="error emp_id"></div>
							</div>
							<div class="col-md-2 form-group">
								<select name="process_id" id="process_id" class="form-control single-select">
									<option value="">Select All</option>
									<?php
									foreach ($processList as $row) :
										echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
									endforeach;
									?>
								</select>
							</div>
							<div class="col-md-3 form-group">
								<div class="input-group">
									<input type="date" name="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error date"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">

							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th style="min-width:50px;" rowspan="2">#</th>
										<th style="min-width:100px;" rowspan="2">Date</th>
										<th style="min-width:80px;" rowspan="2">Shift</th>
										<th style="min-width:80px;" rowspan="2">Operator</th>
										<th style="min-width:80px;" rowspan="2">Process</th>
										<th style="min-width:80px;" rowspan="2">Item</th>
										<th style="min-width:100px;" rowspan="2">Planned Pro.Time<br /><small>(In Min.)</small></th>
										<th style="min-width:100px;" rowspan="2">Cycle Time<br /><small>(In Sec.)</small></th>
										<th style="min-width:100px;" rowspan="2">Plan Qty.</th>
										<th style="min-width:100px;" rowspan="2">Production Qty.</th>
										<th style="min-width:100px;" rowspan="2">Ok Qty.</th>
										<th style="min-width:100px;" colspan="3" class="text-center">Pending Decision</th>
										<th style="min-width:100px;" rowspan="2">Rej Qty.</th>
										<th style="min-width:100px;" rowspan="2">Performance</th>
										<th style="min-width:100px;" rowspan="2">Quality Ratio</th>
										<th style="min-width:100px;" rowspan="2">OEE</th>
									</tr>
									<tr>
										<th>Rej Qty</th>
										<th>RW Qty</th>
										<th>Suspected Qty</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
						</div>
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
			var operator_id = $('#emp_id').val();
			var process_id = $('#process_id').val();
			
			var date = $('#date').val();
			
			if ($("#date").val() == "") {
				$(".date").html("Date is required.");
				valid = 0;
			}
			if (valid) {
				$.ajax({
					url: base_url + controller + '/getOperatorWiseProduction',
					data: {
						operator_id: operator_id,
						process_id: process_id,
						date: date
					},
					type: "POST",
					dataType: 'json',
					success: function(data) {
						$("#reportTable").dataTable().fnDestroy();
						$("#theadData").html(data.thead);
						$("#tbodyData").html(data.tbody);
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
					className: "text-left",
					targets: [0, 1]
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
					loadAttendanceSheet();
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