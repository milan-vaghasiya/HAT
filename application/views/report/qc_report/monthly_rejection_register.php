<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-5 form-group">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-3 form-group">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4 form-group">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-pdf="0" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                        <button type="button" class="btn waves-effect waves-light btn-warning float-right loaddata" data-pdf="1" title="Load Data">
									        <i class="fas fa-print"></i> PDF
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
									<tr>
                                        <th rowspan="2" style="min-width:50px;">#</th>
										<th rowspan="2" style="min-width:100px;">Date</th>
										<th rowspan="2" style="min-width:80px;">Part Name</th>
                                        <th rowspan="2" style="min-width:80px;">Metal</th>
										<th rowspan="2" style="min-width:80px;">OA. No.</th>
										<th rowspan="2" style="min-width:100px;">Total Qty.</th>
										<th rowspan="2" style="min-width:150px;">OK Nos.</th>
										<th rowspan="2" style="min-width:50px;">Total Rejection</th>
										<th colspan="4" class="text-center" style="min-width:50px;">Rejection </th>
										<th rowspan="2" style="min-width:50px;">Remarks</th>
                                    </tr>
                                    <tr>
                                        <th>Turning</th>
                                        <th>Milling</th>
                                        <th>Other</th>
                                        <th>CASTING/ MATERIAL /OUT SOURCE</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
                                    <tr>
                                        <th class="text-right" colspan="5">Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>      
                                    <tr>
                                        <th class="text-right" colspan="5">Net Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th colspan="3"></th>
                                        <th></th>
                                        <th></th>
                                    </tr> 
                                    <tr>
                                        <th class="text-right" colspan="5">REJECTION IN PERCENTAGE</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th colspan="3"></th>
                                        <th></th>
                                        <th></th>
                                    </tr>                            
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
        var is_pdf = $(this).data('pdf');

		if(from_date == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if(to_date == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getMonthlyRejectionRegister',
                    data: {from_date:from_date, to_date:to_date},
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#reportTable").dataTable().fnDestroy();
                        $("#tbodyData").html(data.tbody);
                        $("#tfootData").html(data.tfooter);
                        reportTable();
                    }
                });
            }else{
                window.open(base_url + controller + '/getMonthlyRejectionRegister/'+from_date+'/'+to_date,'_blank').focus();
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
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