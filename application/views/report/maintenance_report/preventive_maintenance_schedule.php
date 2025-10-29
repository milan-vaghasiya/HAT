<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-4">
								<div class="input-group">
									<select name="machine_id" id="machine_id" class="form-control single-select req" style="width:80%">
										<option value="">Select Machine</option>
										<?php 
										
											foreach($machineData as $row):
												if($row->prev_maint_req == 'Yes'):
													echo '<option value="'.$row->id.'" data-machine_code="'.$row->item_code.'"  data-machine_name="'.$row->item_name.'">['.$row->item_code.'] ' .$row->item_name.'</option>';
												endif;
											endforeach;
										?>
									</select>
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>      
							</div>      
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="3">Preventive Maintenance Report</th>
                                        <th >D/MNT/02 (00/01.01.16)</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th colspan="3" class="text-left" id="mc_name">Machine Name :-</th>
                                        <th  class="text-left" id="mc_code">Code No.:</th>
                                    </tr>
									<tr>
										<th  style="min-width:5px;">#</th>
										<th  class="text-center"> Activity Description</th>
                                        <th  style="min-width:150px;">Schedule</th>
                                        <th  style="min-width:150px;">Remark</th> 
									</tr>
								</thead>
								<tbody id="tbodyData">
								</tbody> 
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
		var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
		var machine_id = $('#machine_id').val();
		if($("#machine_id").val() == ""){$(".machine_id").html("Machine is required.");valid=0;}
		var sendData = {machine_id:machine_id,type:type};
		var machine_code = $('#machine_id :selected').data('machine_code');
		var machine_name = $('#machine_id :selected').data('machine_name');
		$("#mc_name").html("Machine Name : "+machine_name);
		$("#mc_code").html("Machine Code : "+machine_code);
		// console.log(sendData);
		if(valid)
		{
			if(type == 1){
				var url =  base_url + '/reports/maintenanceReport/getPreventiveMaintenance/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
				$.ajax({
					url: base_url + 'reports/maintenanceReport/getPreventiveMaintenance',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").dataTable().fnDestroy();
						$("#tbodyData").html(data.tbody);
						$("#tfootData").html(data.tfoot);
						 reportTable();
					}
				});
			}
		}
	});
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,2] }, 
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
    var printBtn = '<button class="btn btn-outline-primary loaddata" data-type="1" type="button"><span>PDF</span></button>';
    reportTable.buttons().container().append(printBtn);
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