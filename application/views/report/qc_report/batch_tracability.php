<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
							<div class="col-md-4">
                                <select name="item_id" id="item_id" class="form-control single-select">
                                    <option value="">Select Item</option>
									<?php
										foreach($itemData as $row):
											echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
										endforeach;  
                                    ?>
                                </select>
								<div class="error ItemId"></div>
                            </div>   
                            <div class="col-md-3">
                                <select name="batch_no" id="batch_no" class="form-control single-select">
                                    <option value="">Select Batch No.</option>
                                    
                                </select>
								<div class="error BatchNo"></div>
                            </div>                 
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Date</th>
										<th>Ref. No.</th>
										<th>Transaction Type</th>
										<th>Reference</th>
										<th>In Qty.</th>
										<th>Out Qty.</th>
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

	$(document).on('change','#item_id',function(e){
		$("#reportTable").dataTable().fnDestroy();
		$("#tbodyData").html("");
		$("#tfootData").html("");
		reportTable();
		var item_id = $(this).val();
		if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getBatchList',
				data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#batch_no").html(data.itemList);
					$("#batch_no").comboSelect();
				}
			});
		}
	});

	$(document).on('change','#batch_no',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var batch_no = $(this).val();

		if($("#item_id").val() == ""){$(".ItemId").html("Item is required.");valid=0;}
		if($("#batch_no").val() == ""){$(".BatchNo").html("Batch No is required.");valid=0;}

		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getBatchTracability',
				data: {batch_no:batch_no,item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbodyData);
					$("#tfootData").html(data.tfootData);
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