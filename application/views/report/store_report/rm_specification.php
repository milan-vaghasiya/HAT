<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="18">RAW MATERIAL SPECIFICATION </th>
                                        <th colspan="2">D/PUR/02 (00/01.01.16)</th>
                                    </tr>
									<tr>
                                        <th rowspan="2" style="min-width:5px;">Sr. No.</th>
                                        <th rowspan="2" style="min-width:150px;">Material</th>
                                        <th rowspan="2" style="min-width:150px;">Standard </th>
                                        <th colspan="11" class="text-center">Chamical Composition</th>
                                        <th colspan="4" class="text-center">Mechanical Properties </th>
                                        <th rowspan="2" style="min-width:150px;">Hardness</th>
                                        <th rowspan="2" style="min-width:150px;">Remark</th>
                                    </tr>
                                    <tr>
                                        <th style="min-width:35px;"></th>
                                        <th style="min-width:35px;">C%</th>
                                        <th style="min-width:35px;">Mn%</th>
                                        <th style="min-width:35px;">P%</th>
                                        <th style="min-width:35px;">S%</th>
                                        <th style="min-width:35px;">Si%</th>
                                        <th style="min-width:35px;">Cr%</th>
                                        <th style="min-width:35px;">Ni%</th>
                                        <th style="min-width:35px;">Mo%</th>
                                        <th style="min-width:35px;">N%</th>
                                        <th style="min-width:35px;">Other</th>
                                        <th style="min-width:35px;">TS (Mpa) Min.</th>
                                        <th style="min-width:35px;">YS (Mpa) Min.</th>
                                        <th>Elong (%) Min.</th>
                                        <th>RA (%) Min.</th>
                                    </tr>
								</thead>
								<tbody>
									<?php 
										echo $tbody;
									?>
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {}}]
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