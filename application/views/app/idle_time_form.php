<?php $this->load->view('app/includes/header');?>

<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white">Idle Time</div>
	
</div>
<div class="extraHeader pe-0 ps-0 text-center">
    <ul class="nav nav-tabs lined" role="tablist">
        <li class="nav-item"><?=(getPrefixNumber($dataRow->job_prefix,$dataRow->job_no))?> - <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?> </li>
    </ul>
    
</div>

<div id="appCapsule" class=" extra-header-active full-height">
    <div class="card">
        <div class="card-body">
            <form id="idleTime">
                <div class="row">
                    <input type="hidden" name="from_entry" id="from_entry" value="1">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="entry_type" id="entry_type" value="8">
                    <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_card_id:""?>">
                    <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                    <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>">
                    <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->in_process_id))?$dataRow->in_process_id:""?>">

                    <div class="error general_error"></div>
                    <div class="col form-group boxed">
                        <label for="entry_date">Date</label>
                        <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
                    </div>        

                    <div class="col form-group boxed">
                        <label for="production_time">Idle Time (in minutes)</label>
                        <input type="text" name="production_time" id="production_time" class="form-control numericOnly" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <label for="rr_reason">Reason</label>
                        <select name="rr_reason" id="rr_reason" class="form-control select2">
                            <option value="">Select Reason</option>
                            <?php
                                foreach($idleReason as $row):
                                    echo '<option value="'.$row->id.'">'.((!empty($row->code))?"[".$row->code."] ":"").$row->remark.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col form-group boxed">
                        <label for="machine_id">Machine</label>
                        <select name="machine_id" id="machine_id" class="form-control select2">
                            <option value="">Select Machine</option>
                            <?php
                                foreach($machineList as $row):
                                    echo '<option value="'.$row->id.'">'.((!empty($row->item_code))?"[".$row->item_code."] ":"").$row->item_name.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <label for="shift_id">Shift</label>
                        <select name="shift_id" id="shift_id" class="form-control select2">
                            <option value="">Select Shift</option>
                            <?php
                                foreach($shiftData as $row):
                                    echo '<option value="' . $row->id . '">' . $row->shift_name . '</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="col form-group boxed">
                        <label for="operator_id">Operator</label>
                        <select name="operator_id" id="operator_id" class="form-control select2">
                            <option value="">Select Operator</option>
                            <?php
                                foreach ($operatorList as $row) :
                                    echo '<option value="' . $row->id . '">[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">

                    <div class="col form-group boxed">
                        <label for="remark">Remark</label>
                        <input type="text" name="remark" id="remark" class="form-control" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <button type="button" class="btn btn-outline-success me-1 mb-1" onclick="saveIdleTime('idleTime');"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </form>

            <hr>

            <div class="row">
                <div class="col form-group boxed">
                    <div class="table-responsive">
                        <table class="table jptable-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th>#</th>
                                    <th style="min-width:100px">Date</th>
                                    <th style="min-width:130px">Idle Time (In Min.)</th>
                                    <th style="min-width:200px">Machine</th>
                                    <th style="min-width:200px">Shift</th>
                                    <th style="min-width:200px">Operator</th>
                                    <th style="min-width:200px">Reason</th>
                                    <th style="min-width:200px">Remark</th>
                                    <th style="min-width:50px">Action</th>
                                </tr>
                            </thead>
                            <tbody id="idleTimeTransData">
                                <?=$transHtml?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>
<script>
    
function saveIdleTime(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveIdleTime',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){

		
			$("#idleTimeTransData").html(data.transHtml);
            $("#DialogIconedSuccess .modal-body").html(data.message);
            $("#DialogIconedSuccess").modal('show');
            $(".select2").select2();
            // var job_card_id = $("#job_card_id").val();
            // window.location = base_url + controller+'/jobDetail/'+job_card_id;	
            $("#idleTimeTransData").html(data.transHtml);

		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}


function trashIdleTime(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/deleteIdleTime',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data){
							if(data.status==0){
								$("#DialogIconedDanger .modal-body").html(data.message);
								$("#DialogIconedDanger").modal('show');
							}else{
								$("#idleTimeTransData").html(data.transHtml);
								$("#DialogIconedSuccess .modal-body").html(data.message);
                                $("#DialogIconedSuccess").modal('show');
                                $(".select2").select2();
									
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}
</script>