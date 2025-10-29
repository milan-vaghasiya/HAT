<?php $this->load->view('includes/header'); ?>
<style>
	/*#theadData th,#theadData td{width:auto !important;}*/
	/* Ensure that the demo table scrolls 
    .dataTable th, .dataTable td { white-space: nowrap; }*/
    
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-8 form-group">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>   
                            <div class="col-md-4 form-group">  
                                <div class="input-group">
                                    <input type="date" name="log_date" id="log_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-pdf="0" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                        <button type="button" class="btn waves-effect waves-light btn-warning float-right loaddata" data-pdf="1" title="Load Data">
									        <i class="fas fa-print"></i> PDF
								        </button>
                                    </div>
                                </div>
                                <div class="error log_date"></div>
                            </div>               
                        </div>                                        
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th>Operator Name</th>
                                        <th>M/C NO.</th>
                                        <th>Day/ Night</th>
                                        <th>Part Name</th>
                                        <th>Metal</th>
                                        <th>WO No</th>
                                        <th>Set up</th>
                                        <th>Cycle time<br>(Sec.)</th>
                                        <th>Total time<br>(Min.)</th>
                                        <th>Qty</th>
                                        <th>Before weight</th>
                                        <th>After weight</th>
                                        <th>Rejection qty.</th>
                                        <th>Rejection reason</th>
                                        <th>Rework qty.</th>
                                        <th>Rework reason</th>
                                        <th>Down time</th>
                                        <th>Down time reason</th>
                                        <th>Effciency (%)</th>
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var log_date = $('#log_date').val();
        var is_pdf = $(this).data('pdf');
		if(log_date == ""){$(".log_date").html("Date is required.");valid=0;}
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getDailyProductionLogSheet',
                    data: {log_date:log_date},
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").dataTable().fnDestroy();
                        $("#tbodyData").html(data.tbody);
                        reportTable();
                    }
                });
            }else{
                window.open(base_url + controller + '/getDailyProductionLogSheet/'+log_date,'_blank').focus();
            }            
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('.page-wrapper').resizer(function() {reportTable.columns.adjust().draw(); });
	return reportTable;
}
</script>