<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-12">
								<div class="input-group">
									<select id="state_code" class="form-control single-select" style="width: 15%;">
											<option value="">All State</option>
											<option value="1">IntraState</option>
											<option value="2">InterState</option>
									</select>
									<select name="sales_type" id="sales_type" class="form-control single-select sales_type" style="width:18%">
										<option value="">Select All</option>
										<option value="1">Manufacturing (Domestics)</option>
										<option value="2">Manufacturing (Export)</option>
										<option value="3">Jobwork (Domestics)</option>
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
							<table id='salesTable' class="table table-bordered jpDataTable colSearch" data-srowposition="2">
								<thead class="thead-info text-center" id="theadData">
									<tr>
										<th colspan="14">Sales</th>
									</tr>
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="3">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Total Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<!-- <th>GSTIN</th>
										<th>Customer Name</th> -->
										<th>State Code</th>
										<th>State Name</th>
										<th>Invoice No.</th>
										<th>Invoice Date</th>
										<th>Invoice Value</th>
										<!-- <th>Total Tax(%)</th>
										<th>Taxable Amount</th>	 -->
										<th>CGST</th>
										<th>SGST</th>
										<th>IGST</th>
										<th>CESS</th>
										<th>Total Tax</th>									
									</tr>
								</thead>
								<tbody id="salesRegisterData"></tbody>
								<tfoot id="footerData"></tfoot>
							</table>
						</div>
						<br>
						<div class="table-responsive">
							<table id='salesReturn' class="table table-bordered jpDataTable colSearch" data-srowposition="2">
								<thead class="thead-info text-center " id="theadData">
									<tr class="">
										<th colspan="14">Sales Return</th>
									</tr>
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="3">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Total Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<!-- <th>GSTIN</th>
										<th>Customer Name</th> -->
										<th>State Code</th>
										<th>State Name</th>
										<th>Invoice No.</th>
										<th>Invoice Date</th>
										<th>Invoice Value</th>
										<!-- <th>Total Tax(%)</th>
										<th>Taxable Amount</th>	 -->
										<th>CGST</th>
										<th>SGST</th>
										<th>IGST</th>
										<th>CESS</th>
										<th>Total Tax</th>									
									</tr>
								</thead>
								<tbody id="salesReturnRegisterData"></tbody>
								<tfoot id="footerReturnData"></tfoot>

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
		var sales_type = $('#sales_type').val();
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
			sales_type: sales_type
		};
		
		if (valid) {

			if (file_type == "VIEW") {
				$.ajax({
					url: base_url + controller + '/getGstr1ReportData',
					data: postData,
					type: "POST",
					dataType: 'json',
					success: function(data) {
						var tamt = (data.taxable_amount != null) ? inrFormat(data.taxable_amount) : 0;
						var namt = (data.net_amount != null) ? inrFormat(data.net_amount) : 0;
						$("#salesTable").DataTable().clear().destroy();
						$("#salesRegisterData").html("");
						$("#salesRegisterData").html(data.tbody);
						$("#footerData").html(data.tfoot);
						jpReportTable('salesTable');
						// jpDataTable('salesTable');

						$("#salesReturn").DataTable().clear().destroy();
						$("#salesReturnRegisterData").html("");
						$("#salesReturnRegisterData").html(data.tbodyReturn);
						$("#footerReturnData").html(data.tfootReturn);
						jpReportTable('salesReturn');
						// jpDataTable('salesReturn');
					}
				});
			}
			if (file_type == "EXCEL") {
				console.log(postData);
				var url = base_url + controller + '/getGstr1ReportData/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
				console.log(url);
				window.open(url);
			}
		}
	}


</script>