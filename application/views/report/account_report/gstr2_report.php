<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
						<div class="col-md-4"></div>
							<div class="col-md-8">
								<div class="input-group">
									<select id="state_code" class="form-control single-select" style="width: 25%;">
											<option value="">All State</option>
											<option value="1">IntraState</option>
											<option value="2">InterState</option>
									</select>
									<select id="party_id" class="form-control single-select" style="width: 25%;">
										<option value="">All Customer</option>
										<?php
											if(!empty($customerData)){
												foreach($customerData as $row){ ?>
													<option value="<?=$row->id?>"><?=$row->party_name?></option>
												<?php }
											}
										?>
									</select>
									<input type="date" name="from_date" id="from_date" class="form-control" value="<?= $startDate ?>" />
									<div class="error fromDate"></div>
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?= $endDate ?>" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right " onclick="loadData('VIEW')" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-warning float-right " onclick="loadData('EXCEL')" title="Load Data">
											<i class="fa fa-file-excel-o"></i> Excel
										</button>
									</div>
								</div>
								<div class="error toDate"></div>
							</div>
						</div>
					</div>
					<div class="card-body reportDiv" style="min-height:75vh">
						<div class="table-responsive">
							<table id='commanTable' class="table table-bordered">
								<thead class="thead-info text-center" id="theadData">
									<tr>
										<th colspan="15">Purchase</th>
									</tr>
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Supplier Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="4">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Total Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th>
										<th>State Name</th>
										<th>Invoice No.</th>
										<th>Original Invoice No.</th>
										<th>Invoice Date</th>
										<th>Invoice Value</th>
										<th>CGST</th>
										<th>SGST</th>
										<th>IGST</th>
										<th>CESS</th>
										<th>Total Tax</th>
									</tr>
								</thead>
								<tbody id="purchaseRegisterData"></tbody>
								<tfoot id="footerData" ></tfoot>

							</table>
						</div>
						<br>
						<div class="table-responsive">
							<table id='purchaseReturn' class="table table-bordered">
								<thead class="thead-info text-center " id="theadData">
									<tr class="">
										<th colspan="14">Purchase Return</th>
									</tr>
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Supplier Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="3">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Total Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th>
										<th>State Name</th>
										<th>Invoice No.</th>
										<th>Invoice Date</th>
										<th>Invoice Value</th>
										<th>CGST</th>
										<th>SGST</th>
										<th>IGST</th>
										<th>CESS</th>
										<th>Total Tax</th>
									</tr>
								</thead>
								<tbody id="purchaseReturnRegisterData"></tbody>

								<tfoot id="footerReturnData" class="text-right"></tfoot>
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
		// loadData();
		$(document).on('click', '.loaddata', function() {
			// loadData();
		});
	});

	function loadData(file_type = "") {
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var entry_type = $('#entry_type').val();
		//if($("#entry_type").val() == ""){$(".entry_type").html("Entry Type is required.");valid=0;}
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
		var state_code=$("#state_code").val();
		var party_id=$("#party_id").val();
		var postData = {
			from_date: from_date,
			to_date: to_date,
			file_type: file_type,
			state_code: state_code,
			party_id: party_id,
	

		};
		if (valid) {

			if (file_type == "VIEW") {
				$.ajax({
					url: base_url + controller + '/getGstr2ReportData',
					data: postData,
					type: "POST",
					dataType: 'json',
					success: function(data) {
						var tamt = (data.taxable_amount != null) ? inrFormat(data.taxable_amount) : 0;
						var namt = (data.net_amount != null) ? inrFormat(data.net_amount) : 0;
						$("#commanTable").DataTable().clear().destroy();
						$("#purchaseRegisterData").html("");
						$("#purchaseRegisterData").html(data.tbody);
						$("#footerData").html(data.tfoot);
						jpReportTable('commanTable');

						$("#purchaseReturn").DataTable().clear().destroy();
						$("#purchaseReturnRegisterData").html("");
						$("#purchaseReturnRegisterData").html(data.tbodyReturn);
						$("#footerReturnData").html(data.tfootReturn);
						jpReportTable('purchaseReturn');
					}
				});
			} 
			if (file_type == "EXCEL") {
				console.log(postData);
				var url = base_url + controller + '/getGstr2ReportData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
				console.log(url);
				window.open(url);
			}
		}
	}


	function jpReportTable(tableId) {
		var jpReportTable = $('#' + tableId).DataTable({
			responsive: true,
			"scrollY": '52vh',
			"scrollX": true,
			deferRender: true,
			scroller: true,
			destroy: true,
			// 'stateSave':false,
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
				[10, 20, 25, 50, 75, 100, 250, 500],
				['10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows', '250 rows', '500 rows']
			],
			dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: ['pageLength', 'excel', //{
				// text: 'Pdf',
				// action: function(e, dt, node, config) {
				// 	// loadData('pdf');
				// }
				//}
			],
			"fnInitComplete": function() {
				$('.dataTables_scrollBody').perfectScrollbar();
			},
			"fnDrawCallback": function(oSettings) {
				$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();
			}
		});
		jpReportTable.buttons().container().appendTo('#' + tableId + '_wrapper toolbar');
		$('.dataTables_filter .form-control-sm').css("width", "97%");
		$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
		$('.dataTables_filter').css("text-align", "left");
		$('.dataTables_filter label').css("display", "block");
		$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
		$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
		setTimeout(function() {
			jpReportTable.columns.adjust().draw();
		}, 10);
		$('.page-wrapper').resizer(function() {
			jpReportTable.columns.adjust().draw();
		});
		return jpReportTable;
	}
</script>