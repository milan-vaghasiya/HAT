<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
								<h4 class="card-title">Change Job Stage</h4>
							</div>
							
						</div>
					</div>
					<div class="card-body">
                        <form id="changeJobStage">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="job_card_id">Job Number</label>
                                    <div class="input-group-append">
                                        <select class="form-control single-select" id="job_card_id" name="job_card_id" style="width:80%">
                                            <option value="">Select Job Number</option>
                                            <?php
                                            if(!empty($jobList)){
                                                foreach($jobList as $row){
                                                    ?>
                                                    <option value="<?=$row->id?>" data-job_process='<?=$row->process?>'><?=$row->job_number?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <button type="button" class="btn btn-info waves-effect waves-light loadProcess"  style="width:20%">Load</button>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="stage_id">Production Stages</label>
                                    <div class="input-group-append">
                                        <select name="stage_id" id="stage_id" data-input_id="process_id1" class="form-control single-select" style="width:80%">
                                            <option value="">Select Stage</option>
                                        </select>
                                        <button type="button" class="btn btn-success waves-effect add-process btn-block addJobStage" style="width:20%">+ Add</a>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    
                                    
                                    
                                </div>									
                                <div class="col-md-3 form-group">
                                    <label>&nbsp;</label>
                                    
                                </div>
                                <hr>
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <div class="error stage_error"></div>
                                        <table id="jobStages" class="table excel_table table-bordered">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:10%;text-align:center;">#</th>
                                                    <th style="width:65%;">Process Name</th>
                                                    <th style="width:10%;">Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stageRows">
                                                <?php
                                                echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                            
                        
                    </div>

                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveStage('changeJobStage');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller.'/changeJobStage')?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>



<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function () {
        $(document).on('click', ".loadProcess", function () {
            var job_card_id = $("#job_card_id").val();
            
            $("#stage_id").html("");
            $("#stage_id").comboSelect();
            $("#stageRows").html("");
            if(job_card_id){
                var job_process = $('#job_card_id :selected').data('job_process');
                $.ajax({
                    url: base_url + controller + '/getJobStages',
                    data: { job_card_id: job_card_id,job_process:job_process},
                    type: "POST",
                    dataType: "json",
                    success: function (data) {
                        if (data.status == 0) {
                            swal("Sorry...!", data.message, "error");
                        }
                        else {
                            $("#stage_id").html(data.processOptions);
                            $("#stage_id").comboSelect();
                            $("#stageRows").html(data.stageRows);
                        }
                    }
                });
            }
            
        });

        $("#jobStages tbody").sortable({
            items: 'tr',
            cursor: 'pointer',
            axis: 'y',
            dropOnEmpty: false,
            helper: fixWidthHelper,
            start: function (e, ui) { ui.item.addClass("selected"); },
            stop: function (e, ui) {
                ui.item.removeClass("selected");
                var seq = 0;
                $(this).find("tr").each(function () { $(this).find("td").eq(0).html(seq + 1); seq++; });
            },
            update: function () {
                var ids = '';
                $(this).find("tr").each(function (index) { ids += $(this).attr("id") + ","; });
                var lastChar = ids.slice(-1);
                if (lastChar == ',') { ids = ids.slice(0, -1); }
            

                
            }
        });

        $(document).on('click', '.addJobStage', function () {
            var jobid = $('#job_card_id').val();
            var process_id = $('#stage_id').val();
            $(".stage_id").html("");
            if (jobid != "" && process_id != "") {
                $.ajax({
                    type: "POST",
                    url: base_url + controller + '/addJobStage',
                    data: { job_card_id: jobid, process_id: process_id },
                    dataType: 'json',
                    success: function (data) {
                        $('#stageRows').html(""); $('#stageRows').html(data.stageRows);
                        $('#stage_id').html(""); $('#stage_id').html(data.processOptions); $('#stage_id').comboSelect();
                    }
                });
            } else {
                $(".stage_id").html("Stage is required.");
            }
        });

	});

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function saveStage(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/saveJobProcessSequence',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location.reload();
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function removeJobStage(button){
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#jobStages")[0];
	table.deleteRow(row[0].rowIndex);
	$('#jobStages tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#jobStages tbody tr:last').index() + 1;
	if(countTR == 0){
        $("#jobStages").html('<tr id="noData"><td colspan="3" align="center">No data available in table</td></tr>');
	}
}

</script>