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
						<hr>
						<div class="row">
						    <div class="col-md-3">
								<select name="sales_type" id="sales_type" class="form-control single-select">
									<option value="">Select ALL</option>
									<option value="1">Manufacturing (Domestics)</option>
									<option value="2">Manufacturing (Export)</option>
									<option value="3">Jobwork (Domestics)</option>
								</select>
							</div>
							<div class="col-md-4">
								<select name="party_id" id="party_id" class="form-control single-select">
									<option value="">Select ALL</option>
									<?php
										foreach($partyData as $row):
											echo '<option value="'.$row->id.'">['.$row->party_code.'] '.$row->party_name.'</option>';
										endforeach;
									?>
								</select>
							</div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
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
                                    <tr class="text-center">
                                        <th colspan="9">Monthly Order Summary </th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:100px;">Part</th>
										<th style="min-width:100px;">Cust. PO. No.</th>
										<th style="min-width:100px;">SO. No.</th>
										<th style="min-width:100px;">Delivery date</th>
										<th class="text-right" style="min-width:50px;">Quantity</th>
										<th class="text-right" style="min-width:50px;">Price</th>
										<th class="text-right" style="min-width:50px;">Total Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
                                <tfoot id="tfootData">
                                    <tr class="thead-info">
                                        <th colspan="5">Total</th>
                                        <th class="text-right">0</th>
                                        <th></th>
                                        <th class="text-right">0</th>
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

	// $(document).on('change','#party_id', function(e){
	// 	var party_id = $(this).val();
	// 	if(party_id){
	// 		$.ajax({
    //             url: base_url + controller + '/getPartyItems',
    //             data: {party_id:party_id},
	// 			type: "POST",
	// 			dataType:'json',
	// 			success:function(data){
	// 				$("#item_id").html("");
	// 				$("#item_id").html(data.partyItems);
	// 				$("#item_id").comboSelect();
    //             }
    //         });
	// 	}
	// });

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var party_id = $('#party_id').val();
		var sales_type = $('#sales_type').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		//if($("#party_id").val() == ""){$(".party_id").html("Customer is required.");valid=0;}
		//if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getDispatchPlanSummary',
                data: {party_id:party_id,sales_type:sales_type,from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					$("#theadData").html(data.thead);
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
		buttons: [ 'pageLength', 'excel'],
		"initComplete": function(settings, json) {$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");}
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