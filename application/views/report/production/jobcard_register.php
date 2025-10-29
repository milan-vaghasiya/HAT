<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>             
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="11">Job Card Register</th>
                                        <th colspan="3">F PL 09 (00/01.06.2020)</th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">PRC No.</th>
										<th style="min-width:80px;">Issue Date</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:80px;">Challan No.<small>(Vendor)</small></th>
										<th style="min-width:80px;">Batch Code</th>
										<th style="min-width:50px;">Weight</th>
										<th style="min-width:50px;">Quantity</th>
										<th style="min-width:50px;">Ok Qty</th>
										<th style="min-width:50px;">Rejection Qty</th>
										<th style="min-width:50px;">Short Qty</th>
										<th style="min-width:80px;">Issued By</th>
										<th style="min-width:100px;">Remark</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1; 
									foreach($jobCardData as $row):
										$cname = !empty($row->party_code)?$row->party_code:"Self Stock";
										echo '<tr>
											<td>'.$i++.'</td>
											<td>'.getPrefixNumber($row->job_prefix,$row->job_no).'</td>
											<td>'.formatDate($row->job_date).'</td>
											<td>'.$row->item_code.'</td>
											<td>'.$cname.'</td>
											<td>'.$row->challan_no.'</td>
											<td>'.$row->batch_no.'</td>
											<td>'.$row->total_weight.'</td>
											<td>'.floatVal($row->qty).'</td>
											<td>'.floatVal($row->total_out_qty).'</td>
											<td>'.floatVal($row->total_reject_qty).'</td>
											<td>'.floatVal($row->qty - $row->total_out_qty).'</td>
											<td>'.$row->emp_name.'</td>
											<td>'.$row->remark.'</td>
										</tr>';
									endforeach; ?>
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>