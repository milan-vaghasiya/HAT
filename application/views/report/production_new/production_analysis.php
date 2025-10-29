<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>     
                            <div class="col-md-6">                                
                                <button type="button" class="btn waves-effect waves-light btn-success float-right mr-2 loaddata" title="Load Data">
									<i class="fas fa-sync-alt"></i> Load
								</button>

                                <input type="date" name="to_date" id="to_date" class="form-control float-right mr-2" value="<?=date('Y-m-d')?>" style="width: 40%;" />

                                <input type="date" name="from_date" id="from_date" class="form-control float-right mr-2" value="<?=date('Y-m-01')?>" style="width: 40%;" />
                                <div class="error fromDate"></div>
                                <div class="error toDate"></div>
                            </div>          
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered jdt">
								<thead class="thead-info" id="theadData">
									<tr>
										<th colspan="13" style="text-align: center;">Production Analysis</th>
									</tr>
									<tr class="clonTR">
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;">M/C No.</th>
										<th style="min-width:80px;">Shift</th>
										<th style="min-width:100px;">Operator Name</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:50px;">Runtime</th>
										<th style="min-width:150px;">Setup</th>
										<th style="min-width:80px;">Cycle Time(m:s)</th>
										<th style="min-width:50px;">Actual Prod. Qty.</th>
										<th style="min-width:50px;">Rework Qty.</th>
										<th style="min-width:50px;">Rejection Qty.</th>
										<th style="min-width:50px;">Rejection Ratio</th>
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
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead tr:eq(1)' );
	var lastIndex = -1;
    $('.jdt thead tr:eq(2) th').each( function (index,value) {
        var title = $(this).text(); //placeholder="'+title+'"
		if(index == lastIndex){$(this).html( '' );}else{$(this).html( '<input type="text" style="width:100%;"/>' );}
	});
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getProductionAnalysis',
                data: {from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					// $("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
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
		// order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							// { orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		order:[],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel','colvis']
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	
	//Datatable Column Filter
     $('.jdt thead tr:eq(2) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( reportTable.column(i).search() !== this.value ) {reportTable.column(i).search( this.value ).draw();}
		});
	} );
}
</script>