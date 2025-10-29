<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-3 form-group float-left">
                                <h4 class="card-title  pageHeader"><?=$pageHeader?></h4>
                            </div>

                            <div class="col-md-3 form-group">
                                <select id="job_id" class="form-control single-select">
                                    <option value="">Select Job No.</option>
                                    <?php
                                        foreach($jobcardData as $row):
                                            echo '<option value="'.$row->id.'">'.$row->job_number.((!empty($row->item_code))?" [".$row->item_code."]":"").'</option>';
                                        endforeach;
                                    ?>
                                </select>
                                <div class="error job_id"></div>
                            </div>

                            <div class="col-md-2 form-group">
                                <select id="process_id" class="form-control single-select">
                                    <option value="">Select Process</option>
                                </select>
                                <div class="error process_id"></div>
                            </div>

                            <div class="col-md-2 form-group">
                                <select id="machine_id" class="form-control single-select">
                                    <option value="0">Select Process</option>
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <div class="input-group">
                                    <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-pdf="0" title="Load Data">
                                        <i class="fas fa-sync-alt"></i> Load
                                    </button>

                                    <button type="button" class="btn waves-effect waves-light btn-warning float-right loaddata" data-pdf="1" title="PDF">
                                        <i class="fas fa-print"></i> PDF
                                    </button>
                                </div>
                            </div>      
						</div>                            
                    </div>
                    
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
							<?php
							$colspan=(!empty($idleReasonList)?count($idleReasonList):0);
							?>
                            <table id='reportTable' class="table table-bordered colSearch jpDataTable" data-ninput="[0]"  data-srowposition = "1">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;" >Shift</th>
										<th style="min-width:80px;">Operator</th>
										<th style="min-width:80px;">Cycle time (in sec.)<br><small>(A)</small></th>
										<th style="min-width:80px;">Load-Unload Time (in sec.)<br><small>(B)</small></th> 
										<th style="min-width:80px;">Total Production (In Nos.)<br><small>(C)</small></th> 
										<th style="min-width:80px;">Rej. Qty</th> 
										<th style="min-width:80px;">R/w Qty.</th> 
										
										<th style="min-width:100px;">Planned Pro.Time<br /><small>(In Min.)</small><br><small>(E)</small></th>
										<th style="min-width:100px;">Run Time<br /><small>(In Min.)</small><br><small>(F)</small></th>
										<th style="min-width:100px;">Plan Qty.<br></th>
										<th style="min-width:100px;">Ok Qty.<br><small>(G)</small></th>
										<th style="min-width:100px;">Load Unload time (In Minutes per shift) <br><small> (H)</small>  </th>
                                        <th style="min-width:100px;">Idle Time<br><small>(D)</small></th>
										<?php
										if(!empty($idleReasonList))
										{
											foreach($idleReasonList as $row)
											{
												?>
												<th style="min-width:30px;"><?=$row->code?></th>
												<?php
											}
										}
										?>
										<th style="min-width:100px;">Availability <br><small>(I=(F/E)*100)</small></th>
										<th style="min-width:100px;">Overall Performance<br><small>(J=(A+B/60)*C)/E)</small></th>
										<th style="min-width:100px;">Performance <br><small>(K=(((A+B)*C)/(E-D))/60)</small> </th>
										<th style="min-width:100px;">Quality Rate <br><small>(L=(G/C)*100)</small></th>
										<th style="min-width:100px;">OEE <br><small>(I*K*L)/100</small></th>
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
	//  reportTable();
	jpDataTableFilter('reportTable');

    $(document).on('change','#job_id',function(){
        var job_id = $(this).val();
        if(job_id == ""){
            $("#process_id").html('<option value="">Select Process</option>');
            $("#process_id").comboSelect();
            $("#machine_id").html('<option value="">Select Machine</option>');
            $("#machine_id").comboSelect();
        }else{
            $.ajax({
                url: base_url + controller + '/getProcessListOnJobCard',
                type:'post',
                data:{job_id:job_id},
                dataType:'json',
                success:function(data){
                    $("#process_id").html(data.process_list);
                    $("#process_id").comboSelect();
                    $("#machine_id").html('<option value="">Select Machine</option>');
                    $("#machine_id").comboSelect();
                }
            });
        }
    });

    $(document).on('change','#process_id',function(){
        var process_id = $(this).val();
        if(job_id == ""){
            $("#machine_id").html('<option value="">Select Machine</option>');
            $("#machine_id").comboSelect();
        }else{
            $.ajax({
                url: base_url + controller + '/getProcessWiseMachineList',
                type:'post',
                data:{process_id:process_id},
                dataType:'json',
                success:function(data){
                    $("#machine_id").html(data.machine_list);
                    $("#machine_id").comboSelect();
                }
            });
        }
    });

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
        
		var job_id = $('#job_id').val();
		var process_id = $('#process_id').val();
		var machine_id = $('#machine_id').val();
        var is_pdf = $(this).data('pdf');
	
		if($("#job_id").val() == ""){$(".job_id").html("Job No. is required.");valid=0;}
		if($("#process_id").val() == ""){$(".process_id").html("Process is required.");valid=0;}
		if(valid){
            if(is_pdf == 0){
                $.ajax({
                    url: base_url + controller + '/getProductionMonitoringData',//getOeeData',
                    data: {job_id:job_id, process_id:process_id,machine_id:machine_id,is_pdf:is_pdf},
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $('#reportTable').DataTable().clear().destroy();
                        $("#tbodyData").html(data.tbody);
                        jpDataTableFilter('reportTable');
                    }
                });
            }else{
                window.open(base_url + controller + '/getProductionMonitoringData/'+job_id+'/'+process_id+'/'+machine_id, '_blank').focus();
            }            
        }
    });   
});
/*function reportTable()
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger("click");}}]
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}*/
</script>