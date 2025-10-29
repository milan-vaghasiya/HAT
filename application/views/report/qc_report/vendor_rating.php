<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
							<div class="col-md-2">
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
								<div class="error fromDate"></div>
							</div> 
							<div class="col-md-2">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
								<div class="error toDate"></div>
						    </div>   
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%">
                                        <option value="">Select Vendor</option>
                                        <?php
    										foreach($vendorData as $row):
    											echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
    										endforeach;  
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
    								        <i class="fas fa-sync-alt"></i> Load
    							        </button>
                                    </div>
                                </div>
                                <div class="error party_id"></div>
                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">.
									<tr class="text-center">
										<th colspan="10">VENDOR RATING REPORT</th>
										<th colspan="2">F PL 03 (00/01.06.20)</th>
									</tr>
									<tr>
										<th colspan="4">Vendor's Name :</th>
										<th colspan="4">Period :</th>
										<th colspan="4">Date :</th>
									</tr>
									<tr class="text-center">
										<th rowspan="3" style="min-width:50px;">Sr No.</th>
										<th rowspan="3" style="min-width:100px;">Item Description</th>
										<th rowspan="3" style="min-width:50px;">Quantity Supplied</th>
										<th rowspan="3" style="min-width:50px;">Inspected Qty.<br />(N)</th>
										<th colspan="3">Quality Rating</th>
										<th colspan="3">Delivery Rating</th>
										<th rowspan="3" style="min-width:100px;">Remark</th>
									</tr>
									<tr class="text-center">
										<th colspan="3">Quantity</th>
										<th colspan="3">Quantity Received</th>
									</tr>
									<tr class="text-center">
										<th>Accepted<br>(Q1)</th>
										<th>Accept.U/D<br>(Q2)</th>
										<th>Rejected<br>(Q3)</th>
										<th>Intime<br>(T1)</th>
										<th>Late upto 1 week<br>(T2)</th>
										<th>Late beyond week<br>(T3)</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData"></tfoot>
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
		var party_id = $("#party_id").val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		
		if($("#party_id").val() == ""){$(".party_id").html("Vendor is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/',
				data: {party_id:party_id, from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
					if(data.status===0){
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
					} else {
						$("#reportTable").dataTable().fnDestroy();
						$("#tbodyData").html(data.tbodyData);
						$("#tfootData").html(data.tfootData);
						reportTable();
					}
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