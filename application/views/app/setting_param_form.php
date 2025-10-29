<?php $this->load->view('app/includes/header');?>

<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white">Setting Parameter</div>
	
</div>
<div class="extraHeader pe-0 ps-0 text-center">
    <ul class="nav nav-tabs lined" role="tablist">
        <li class="nav-item"><?=(getPrefixNumber($dataRow->job_prefix,$dataRow->job_no))?> - <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?> </li>
    </ul>
    
</div>

<div id="appCapsule" class=" extra-header-active full-height">
    <div class="card">
        <div class="card-body">
            <form id="sttingParameter">
                <div class="col form-group boxed"> 
                     <div class="row">
                        <input type="hidden" name="from_entry" id="from_entry" value="1">
                        <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=$job_approval_id?>">
                        <input type="hidden" name="id" id="id" value="">
                    
                        <div class="col form-group boxed">
                            <label for="entry_date">Date</label>
                            <input type="date" name="entry_date" id="entry_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>"  >
                        </div>
                        <div class="col form-group boxed">
                            <label for="tool_no">Tool No.</label>
                            <input type="text" name="tool_no" id="tool_no" class="form-control numericOnly req" value="" />
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col form-group boxed">
                            <label for="insert_id">Insert</label>
                            <select name="insert_id" id="insert_id" class="form-control select2 req">
                                <option value="">Select Insert</option>
                                <?php
                                    if(!empty($insertData)){
                                        foreach($insertData as $row){
                                            ?><option value="<?=$row->id?>" data-insert_name="<?=$row->item_name?>"><?=$row->item_name?></option><?php
                                        }
                                    }
                                ?>
                            </select>
                            <input type="hidden" name="insert_name" id="insert_name">
                        </div>
                        <div class="col form-group boxed">
                            <label for="corner_radius">Corner Redius</label>
                            <input type="text" name="corner_radius" id="corner_radius" class="form-control floatOnly " value="" />
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col form-group boxed">
                            <label for="grade">Grade</label>
                            <input type="text" name="grade" id="grade" class="form-control " value="" />
                        </div>
                        <div class="col form-group boxed">
                            <label for="make">Make</label>
                            <input type="text" name="make" id="make" class="form-control " value="" />
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col form-group boxed">
                            <label for="cutting_speed">Cutting Speed / RPM </label>
                            <input type="text" name="cutting_speed" id="cutting_speed" class="form-control numericOnly " value="" />
                        </div>
                        <div class="col form-group boxed">
                            <label for="feed">Feed</label>
                            <input type="text" name="feed" id="feed" class="form-control floatOnly " value="" />
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col form-group boxed">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control" value="" />
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col form-group boxed">
                            <button type="button" class="btn btn-outline-success me-1 mb-1" onclick="saveSttingParameter('sttingParameter');"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div class="col form-group boxed">
                    <label for="">Transactions : </label>
                    <div class="table-responsive">
                        <table id='storeLocationTransTable' class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th class="text-center" style="min-width:50px">#</th>
                                    <th class="text-center" style="min-width:100px">Date</th>
                                    <th class="text-center" style="min-width:100px">Tool No.</th>
                                    <th class="text-center" style="min-width:200px">Insert</th>
                                    <th class="text-center" style="min-width:100px">Corner Redius</th>
                                    <th class="text-center" style="min-width:50px">Grade</th>
                                    <th class="text-center" style="min-width:100px">Make</th>
                                    <th class="text-center" style="min-width:200px">Cutting Speed / RPM</th>
                                    <th class="text-center" style="min-width:50px">Feed</th>
                                    <th class="text-center" style="min-width:200px">Remark</th>
                                    <th class="text-center" style="min-width:50px">Action</th>
                                </tr>
                            </thead>
                            <tbody id="settingParamData">
                                <?=$htmlData?>
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
$(document).ready(function(){
    $(document).on('change','#insert_id',function(){
        var insert_name = $("#insert_id :selected").data("insert_name");
        $("#insert_name").val(insert_name);
    });

});

function editSettingParam(data,button){ 
    $.each(data,function(key, value) {
        $("#"+key).val(value);
    }); 
    $("#insert_id").select2();
}
function saveSttingParameter(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url  +'production/jobcard/saveSettingParameter',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
             $("#settingParamData").html(data.htmlData);
			$('#'+formId)[0].reset();
			$("#id").val("");
            $("#DialogIconedSuccess .modal-body").html(data.message);
            $("#DialogIconedSuccess").modal('show');
            $(".select2").select2();
		}else{
			$("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');
		}				
	});
}

function removeSettingParam(id,job_approval_id,name = 'Record') {
	var send_data = { id: id,job_approval_id:job_approval_id  };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Record?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function() {
					$.ajax({
						url: base_url + 'production/jobcard/deleteSettingParam',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function(data) {
							if (data.status == 0) {
								$("#DialogIconedDanger .modal-body").html(data.message);
								$("#DialogIconedDanger").modal('show');
							} else {
                                $("#DialogIconedSuccess .modal-body").html("Record Deleted Successfully");
                                $("#DialogIconedSuccess").modal('show');
								$("#settingParamData").html(data.htmlData);
                                $(".select2").select2();
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function() {

				}
			}
		}
	});
}

</script>