<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-5 form-group float-left">
								<h4 class="card-title pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-3">
							    <input type="date" name="fromDate" id="fromDate" class="form-control" value="<?= date('Y-m-01') ?>" />
							    <div class="error fromDate"></div>
							</div>
							<div class="col-md-4">
								<div class="input-group float-right">
									<input type="date" name="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</div>
						</div>
						<hr>
						<div class="row justify-content-end">

							<div class="col-md-3 form-group">

							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<?php
							$colspan = (!empty($idleReasonList) ? count($idleReasonList) : 0);
							?>
							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th style="min-width:50px;" rowspan="2">#</th>
										<th style="min-width:100px;" rowspan="2">Incharge</th>
										<th style="min-width:100px;" rowspan="2">Availability </th>
										<th style="min-width:100px;" rowspan="2">Overall Performance</th>
										<th style="min-width:100px;" rowspan="2">Performance</th>
										<th style="min-width:100px;" rowspan="2">Quality Rate</th>
										<th style="min-width:100px;" rowspan="2">OEE</th>
										<th style="min-width:100px;" rowspan="2">Loss Hours</th>
										<th style="min-width:100px;" rowspan="2">Loss Hours In INR</th>
										<th style="min-width:80px;" rowspan="2">Load-Unload Time</th>
										<th style="min-width:100px;" colspan="<?= $colspan ?>">Reason For Idle Time In Hours</th>
										<th style="min-width:80px;" rowspan="2">Total Production (In Nos.)</th>

									</tr>
									<tr>
										<?php
										if (!empty($idleReasonList)) {
											foreach ($idleReasonList as $row) {
										?>
												<th style="min-width:30px;"><?= $row->code ?></th>
										<?php
											}
										}
										?>
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
            var fromDate = $('#fromDate').val();
			var date = $('#date').val();

            if ($("#fromDate").val() == "") {
				$(".fromDate").html("From Date is required.");
				valid = 0;
			}
			if ($("#date").val() == "") {
				$(".date").html(" Date is required.");
				valid = 0;
			}
			if (valid) {
				$.ajax({
					url: base_url + controller + '/getDailyOeeData',
					data: {
						fromDate:fromDate,date: date
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
					$(".loaddata").trigger("click");
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