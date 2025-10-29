<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-4">
								<h4 class="card-title pageHeader"><?= $pageHeader ?></h4>
							</div>
							<div class="col-md-3">
                                <select name="partySelect" id="partySelect" data-input_id="party_id" class="form-control jp_multiselect_all" multiple="multiple">
									
									<?php
									foreach ($partyData as $row) :
										echo '<option value="' . $row->id . '">[' . $row->party_code . '] ' . $row->party_name . '</option>';
									endforeach;
									?>
								</select>
								<input type="hidden" name="party_id" id="party_id" value="" /> 
								<div class="error party_id"></div>
							</div>
							<div class="col-md-2">
								<input type="date" name="from_date" id="from_date" class="form-control" value="<?= date('Y-m-01') ?>" />
								<div class="error fromDate"></div>
							</div>
							<div class="col-md-3">
								<div class="input-group">
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>" />
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">

							<table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th colspan="7">Enquiry Monitoring </th>
									</tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:100px;">Total Enquiry</th>
										<th style="min-width:100px;">Quotation Send</th>
										<th style="min-width:100px;">Pending Quotation</th>
										<th style="min-width:50px;">Sales Order Confirm</th>
										<th style="min-width:50px;">Pending Enquiry</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
								</tfoot>
							</table>

							<table id='detailReportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th class="text-center" colspan="13">Enquiry Monitoring </th>
									</tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:100px;">Enquiry Date</th>
										<th style="min-width:100px;">Enquiry No.</th>
										<th style="min-width:100px;">Quotation Date</th>
										<th style="min-width:100px;">Quotation No.</th>
										<th style="min-width:50px;">Quoted</th>
										<th style="min-width:50px;">Un Quote</th>
										<th clas="text-right" style="min-width:100px;">Quotation Amount</th>
										<th style="min-width:100px;">Day</th>
										<th style="min-width:100px;">Sales order Date</th>
										<th style="min-width:100px;">Sales order No.</th>
										<th style="min-width:100px;">Day/Month</th>
									</tr>
								</thead>
								<tbody id="detailTbodyData"></tbody>
								<tfoot id="detailTfootData">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
									<th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
								</tfoot>
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
		$("#detailReportTable").hide();

		$('.jp_multiselect_all').multiselect({
			allSelectedText: 'All',
			maxHeight: 200,
			includeSelectAllOption: true,
			buttonWidth: '100%'
		}).multiselect('selectAll', true).multiselect('updateButtonText');
		$('.form-check-input').addClass('filled-in');
		$('.multiselect-filter i').removeClass('fas');
		$('.multiselect-filter i').removeClass('fa-sm');
		$('.multiselect-filter i').addClass('fa');
		$('.multiselect-container.dropdown-menu').addClass('scrollable');
		$('.multiselect-container.dropdown-menu').css('max-height','200px');
		$('.scrollable').perfectScrollbar({wheelPropagation: !0});
	
		$(document).on('click', '.loaddata', function(e) {
		var type = $(this).data('type');
			$(".error").html("");
			var valid = 1;
			var from_date = $('#from_date').val();
			var to_date = $('#to_date').val();
			var party_id = $('#partySelect').val(); 
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
			var sendData = {from_date: from_date,to_date: to_date,party_id: party_id,type:type}
			if (valid) {
				
			if(type == 1){
				var url =  base_url + '/reports/salesReport/getEnquiryMonitoring/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
					$.ajax({
						url: base_url + controller + '/getEnquiryMonitoring',
						data: sendData,
						type: "POST",
						dataType: 'json',
						success: function(data) {
							if ($('#partySelect').val() == '') {
								$("#detailReportTable").dataTable().fnDestroy();
								$("#reportTable").show();
								
								$("#detailReportTable").hide();
								$("#reportTable").dataTable().fnDestroy();
								$("#tbodyData").html(data.tbody);
								$("#tfootData").html(data.tfoot);
								reportTable();
							} else {
								$("#reportTable").dataTable().fnDestroy();
								$("#reportTable").hide();
								$("#detailReportTable").show();
							
								$("#detailReportTable").dataTable().fnDestroy();
								
								$("#detailTbodyData").html(data.tbody);
								$("#detailTfootData").html(data.tfoot);
								detailReportTable();
							}
						}
					});
				}
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
			buttons: ['pageLength', 'excel'],
			"initComplete": function(settings, json) {
				$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");
			}
		});
		var printBtn = '<button class="btn btn-outline-primary loaddata" data-type="1" type="button"><span>PDF</span></button>';
		reportTable.buttons().container().append(printBtn);
		reportTable.buttons().container().appendTo('#reportTable_wrapper toolbar');
		$('.dataTables_filter .form-control-sm').css("width", "97%");
		$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
		$('.dataTables_filter').css("text-align", "left");
		$('.dataTables_filter label').css("display", "block");
		$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
		$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
		return reportTable;
	}

	function detailReportTable() {
		var reportTable = $('#detailReportTable').DataTable({
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
					targets: [0, 1]
				},
				{
					className: "text-right",
					targets: [8]
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
			buttons: ['pageLength', 'excel'],
			"initComplete": function(settings, json) {
				$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");
			}
		});
		var printBtn = '<button class="btn btn-outline-primary loaddata" data-type="1" type="button"><span>PDF</span></button>';
		reportTable.buttons().container().append(printBtn);
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