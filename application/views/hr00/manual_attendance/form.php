<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="punch_type" value="2" />

            <div class="col-md-6 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req loadtrans">
                    <option value="">Select Employee</option>
                    <option value="<?=$loginID?>" <?=(!empty($dataRow->emp_id) && $loginID == $dataRow->emp_id)?"selected":"";?>>My Self</option>
                    <?php
                        foreach($empList as $row):
							if($loginID != $row->id):
								$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
							endif;
                        endforeach;
                    ?>
                </select>
            </div> 
            <div class="col-md-3 form-group">
                <label for="punch_date">Attendance Date</label>
                <input type="date" name="punch_date" id="punch_date" class="form-control req changeDate" value="<?=(!empty($dataRow->punch_date))?formatDate($dataRow->punch_date, 'Y-m-d'):date("Y-m-d")?>" max="<?php date("Y-m-d")?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="punch_in">Punch Time</label>
                <input type="time" name="punch_in" id="punch_in" class="form-control req" value="" />
            </div>
            
            <div class="col-md-10 form-group">
                <label for="remark">Reason</label>
                <input type="text" name="remark" id="remark" class="form-control req" value="" />
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn btn-outline-success btn-save save-form mt-30 float-right" onclick="storeManualAttendance('addManualAttendance','save');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12 form-group">
        <div class="table-responsive">
            <table id="attenData" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;" rowspan="2">#</th>
                        <th>Shift</th>
                        <th>Punch Date & Time</th>
                        <th>Punch Type</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="attenBody">
                    <?php 
                        if(!empty($punchData['tbody'])):
                            echo $punchData['tbody'];
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $(document).on('change',".loadtrans, .changeDate",function(e){
        e.stopImmediatePropagation();
        var emp_id = $("#emp_id").val();
        var punch_date = $("#punch_date").val();
        $.ajax({ 
            type: "POST",   
            url: base_url + 'hr/manualAttendance/getEmpPunchData',   
            data: {emp_id:emp_id, punch_date:punch_date},
			dataType:"json",
        }).done(function(response){
            $('#attenBody').html("");
			$('#attenBody').html(response.tbody);
        });
    });
});

function storeManualAttendance(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){ fnsave="save"; }
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'hr/manualAttendance/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){ 
            $('#attenBody').html("");
			$('#attenBody').html(data.tbody);
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }else{
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}		
	});
}

function trashPunch(id,emp_id,name='Record'){
    var emp_id = $('#emp_id').val();
	var punch_date = $('#punch_date').val();
	var send_data = { id:id, emp_id:emp_id, punch_date:punch_date };
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
						url: base_url + 'hr/manualAttendance/deletePunch',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{ console.log(data.tbody);
                                $('#attenBody').html("");
			                    $('#attenBody').html(data.tbody);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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