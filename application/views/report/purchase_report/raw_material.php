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
                                        <th colspan="5">Raw Material</th>
                                        <th colspan="3">D ST 02 (00/01-06-20)</th>
                                    </tr>
									<tr class="text-center">
										<th rowspan="2">#</th>
										<th rowspan="2">Grade</th>
										<th colspan="3">Specification</th>
										<th rowspan="2">Std. Ref.</th>
										<th rowspan="2">Min. Stock Qty.</th>
										<th rowspan="2">Remark</th>
									</tr>
									<tr class="text-center">
										<th>Dimensional</th>
										<th>Chemical</th>
										<th>Other</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=1; 
									foreach($rawMaterialData as $row):
										echo '<tr>
											<td>'.$i++.'</td>
											<td>'.$row->material_grade.'</td>
											<td>'.$row->item_name.'</td>
											<td>'.$row->cal_agency.'</td>
											<td>'.$row->other.'</td>
											<td>'.$row->part_no.'</td>
											<td>'.$row->min_qty.'</td>
											<td>'.$row->description.'</td>
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
							{ className: "text-left", targets: [0,2] }, 
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