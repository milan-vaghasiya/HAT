<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-3 form-group">
                                <h4 class="card-title text-left pageHeader"><?=$pageHeader?></h4>
                            </div>     
	
                            <div class="col-md-4 form-group">
                                <select name="item_id" id="item_id" class="form-control single-select">
                                    <option value="">Select FininshGood </option>
                                    <?php   
										foreach($itemData as $row): 
											echo '<option value="'.$row->id.'">'.$row->item_code.'</option>';
										endforeach; 
                                    ?>
                                </select>
								<div class="error item_id"></div>
                            </div>
							<div class="col-md-4 form-group">
								<select name="ref_item_id" id="ref_item_id" class="form-control single-select">
									<option value="">Select Bom Item</option>
									<?php   
										foreach($refItemData as $row): 
											echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
										endforeach; 
                                    ?>
								</select>
								<div class="error ref_item_id"></div>
							</div>
                            
                        </div>                                 
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Item Name</th>
										<th style="min-width:80px;">Qty</th>
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
	<?php if(!empty($itemId)) { ?>
		setTimeout(function(){ $('#item_id').val(<?=$itemId?>);$('#item_id').comboSelect();$('#item_id').trigger('change'); }, 50);		
	<?php } ?>

	$(document).on('change','#item_id',function(e){
		var item_id = $(this).val();
		if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getItemBomData',
				data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
					$("#theadData").html(data.thead);
					$("#tbodyData").html(data.tbody);
					reportTable();
				}
			});
		}
	});

	$(document).on('change','#ref_item_id',function(e){
		var ref_item_id = $(this).val();
		if(ref_item_id)
		{
			$.ajax({
				url: base_url + controller + '/getProductionBomData',
				data: {ref_item_id:ref_item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
					$("#theadData").html(data.thead);
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