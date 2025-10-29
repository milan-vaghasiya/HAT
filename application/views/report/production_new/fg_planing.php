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
                            <div class="col-md-4">
                                <select name="fg_item_id" id="fg_item_id" class="form-control single-select">
                                    <option value="">Select Finish Good</option>
                                    <?php   
										foreach($itemDataList as $row): 
											echo '<option value="'.$row->id.'"> '.$row->item_code.'</option>';
										endforeach; 
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 form-group">  
									<div class="input-group">
										<input type="text" name="fg_qty"  id="fg_qty" class="form-control" placeholder="Enter Qty" value="" />
                                        
										<div class="input-group-append ml-2">
											<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
												<i class="fas fa-sync-alt"></i> Load
											</button>
										</div>
									</div>
                                    <div class="error fg_qty_error"></div>
									
								</div>                              
                        </div>                                         
                    </div>
                    
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Item</th>
										<th>Qty/Pcs</th>
										<th>Total Req. Qty.</th>
										<th>Stock</th>
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
        
		var fg_item_id = $('#fg_item_id').val();
		var fg_qty = $('#fg_qty').val();
	
		if($("#fg_item_id").val() == ""){$(".fg_item_id").html("Item is required.");valid=0;}
		if($("#fg_qty").val() == ""){$(".fg_qty_error").html("Qty is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getFGPlaning',
                data: {fg_item_id:fg_item_id, fg_qty:fg_qty},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
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