<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                                    <option value="">Select Job Card</option>
                                    <?php   
										foreach($jobData as $row): 
											echo '<option data-item_id="'.$row->product_id.'" value="'.$row->job_card_id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).' ['.$row->item_code.']</option>';
										endforeach; 
                                    ?>
                                </select>
                            </div>    
                            <div class="col-md-4 form-group">
                                <select name="process_id" id="process_id" class="form-control single-select req">
                                    <option value="">Select Process</option>
                                    <?php
                                        foreach($processData as $row):
                                            echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div> 
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append ml-2">
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
                                <table id="maintbl" class="table table-bordered generalTable">
                                    <thead class="thead-info" id="theadDataA">
                                        <tr class="bg-light">
                                            <th>Drg No.</th>
                                            <th>Setup No.</th>
                                            <th>Inspector Name</th>
                                            <th>Shift</th>
                                            <th>M/C No.</th>
                                            <th>Batch Code</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDataA"></tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table id="preDispatchtbl" class="table table-bordered generalTable" style="margin-top:2px;">
                                    <thead class="thead-info" id="theadDataB">
                                        <?php $sQty= (!empty($lineInspectData->sampling_qty))?$lineInspectData->sampling_qty:5?>
                                        <tr style="text-align:center;">
                                            <th rowspan="2" style="width:5%;">#</th>
                                            <th rowspan="2">Parameter</th>
                                            <th rowspan="2">Specification</th>
                                            <th rowspan="2">Lower Limit</th>
                                            <th rowspan="2">Uper Limit</th>
                                            <th rowspan="2">Instrument Used</th>
                                            <th colspan="<?= $sQty?>">Observation on Samples</th>
                                            <th rowspan="2">Result</th>
                                        </tr>
                                        <tr style="text-align:center;">
                                            <?php for($j=1;$j<=$sQty;$j++):?> 
                                                <th><?= $j ?></th>
                                            <?php endfor;?>    
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDataB"></tbody>
                                </table>
                            </div>
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
    $(document).on('click','.loaddata',function(e){
        $(".error").html("");
        var valid = 1;
        var job_card_id = $("#job_card_id").val();
        var process_id = $('#process_id').val();
        var item_id = $("#job_card_id :selected").data('item_id');
        var to_date = $('#to_date').val();
        if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
    
        if(valid)
        {
            $.ajax({
                url: base_url + controller + '/getInProcessInspectionData',
                data: {job_card_id:job_card_id, process_id:process_id, item_id:item_id, to_date:to_date},
                type: "POST",
                dataType:'json',
                success:function(data){
                    //$("#reportTable").dataTable().fnDestroy();
                    $("#tbodyDataA").html("");
                    $("#tbodyDataA").html(data.tbodyDataA);
                    
                    $("#tbodyDataB").html("");
                    $("#tbodyDataB").html(data.tbodyDataB);
                    $("#theadDataB").html("");
                    $("#theadDataB").html(data.theadDataB);
                }
            });
        }
    }); 
    $(document).on('change','#job_card_id',function(){
		var job_card_id = $(this).val();
        var product_id = ($("#job_card_id :selected").data('product_id')); 
        var process = ($("#job_card_id :selected").data('process'));
        $('#product_id').val(product_id);

		if(job_card_id){
			$.ajax({
				url:base_url + controller + '/getProcessList',
				type:'post',
				data:{job_card_id:job_card_id,product_id:product_id,process:process},
				dataType:'json',
				success:function(data){
					$("#process_id").html("");
					$("#process_id").html(data.options);
					$("#process_id").comboSelect();
				}
			});
		} else {
			$("#process_id").html("<option value=''>Select Process</option>");
			$("#process_id").comboSelect();
		}
    });
}); 
function reportTable()
{
	var reportTable = $('#maintbl').DataTable( 
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
	reportTable.buttons().container().appendTo( '#maintbl_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>