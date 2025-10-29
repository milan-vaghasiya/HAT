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
                                        <th colspan="7"><?=$pageHeader?></th>
                                        <th colspan="3">F/QC/06 (01/01.01.16)</th>
                                    </tr>
								
									<tr class="text-center">
										<th rowspan="2">#</th>
                                        <th rowspan="2">Description of Measuring Device</th>
										<th rowspan="2">Location</th>
										<th rowspan="2">Code No.</th>
										<th rowspan="2">Size</th>
										<th rowspan="2">Make</th>
										<th rowspan="2">Calibration Frequency</th>
										<th colspan="3">calibration</th>
										
									</tr>
									<tr class="text-center">
										<th>Date</th>
										<th>Due Date</th>
										<th>Result</th>
									</tr>
									
								</thead>
								 <tbody>
									<?php $i=1; 
									foreach($calibrationData as $row):
										$size = (!empty($row->size)) ? $row->size : '' ;
										echo '<tr>
											<td>'.$i++.'</td>
											<td>'.$row->item_name.'</td>
											<td>'.$row->location_name.'</td>
											<td>'.$row->mfg_sr.'</td>
											<td>'.$size.'</td>
											<td>'.$row->make_brand.'</td>
											<td>'.$row->cal_freq.'</td>
											<td>'.$row->last_cal_date.'</td>
											<td>'.$row->next_cal_date.'</td>
											<td></td>
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

	$(document).on('click','.loadPdf',function(e){
		$(".error").html("");
		var valid = 1;
		if(valid)
		{
        	window.open(base_url + controller + '/printCalibrationStatus').focus();
        }
    });

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
	var printBtn = '<button class="btn btn-outline-primary loadPdf" type="button"><span>PDF</span></button>';
    reportTable.buttons().container().append(printBtn);
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
