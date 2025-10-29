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
                                <div class="input-group">
                                    <select name="batch_no" id="batch_no" class="form-control single-select" style="width:70%">
                                        <option value="">Select Batch No.</option>
                                        <?php
    										foreach($batchData as $row):
    										    if(!empty($row->batch_no)):
    											    echo '<option value="'.trim($row->batch_no).'">'.trim($row->batch_no).'</option>';
    											endif;
    										endforeach;  
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
    								        <i class="fas fa-sync-alt"></i> Load
    							        </button>
                                    </div>
                                </div>
                                <div class="error batch_no"></div>
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
										<th>Item Name</th>
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
	<?php if(!empty($itemId)) { ?>
		setTimeout(function(){ $('#batch_no').val(<?=$itemId?>);$('#batch_no').comboSelect();$('#batch_no').trigger('change'); }, 50);		
	<?php } ?>
	
	$(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var batch_no = $("#batch_no").val();
		if($("#batch_no").val() == ""){$(".batch_no").html("Batch No. is required.");valid=0;}
		
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getBatchHistory',
				data: {batch_no:batch_no},
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