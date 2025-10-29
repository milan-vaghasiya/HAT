<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="ctc_id" value="<?=$ctc_id?>" />
            <div class="col-md-4 form-group">
                <label for="head_name">Head Name</label>
                <input type="text" name="head_name" id="head_name" class="form-control req" value="">
            </div>
            <div class="col-md-4 form-group"> <!-- 1 = Earnings, -1 = Deduction -->
                <label for="type">Type</label> 
                <select name="type" id="type" class="form-control single-select req">
                    <?php 
                        if(!empty($typeArray)):
                            foreach($typeArray as $key=>$value):
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="parent_head">Parent Head</label>
                <select name="parent_head" id="parent_head" class="form-control single-select req">
                    <?php 
                        if(!empty($parentheadArray)):
                            foreach($parentheadArray as $key=>$value):
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_method">Cal. Method</label>
                <select name="cal_method" id="cal_method" class="form-control single-select req">
                    <?php 
                        if(!empty($calMethodArray)):
                            foreach($calMethodArray as $key=>$value):
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_value">Cal. Value</label> 
                <input type="text" name="cal_value" id="cal_value" class="form-control floatOnly req" value="">
            </div>
            <div class="col-md-4 form-group">
                <label for="min_val">Min. Value</label> 
                <input type="text" name="min_val" id="min_val" class="form-control floatOnly" value="">
            </div>
            <div class="col-md-8 form-group">
                <label for="cal_on">Cal. On</label> 
                <select name="cal_on" id="cal_on" class="form-control single-select">
                    <option value=""> Select</option>  
                    <?php 
                        if(!empty($calOnArray)):
                            foreach($calOnArray as $key=>$value):
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <button type="button" class="btn btn-success btn-save float-right mt-30 btn-block" onclick="saveSalaryStructure('salaryHeads','saveSalaryStructure');">Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="table-responsive">
    <table id="salarytbl" class="table table-bordered align-items-center">
        <thead class="thead-info">
            <tr>
                <th style="width:5%;">#</th>
                <th class="text-center">Head Name</th>
                <th class="text-center">Type</th>        
                <th class="text-center">Parent Head</th>                
                <th class="text-center">Cal. Method</th>
                <th class="text-center">Cal. Value</th>
                <th class="text-center">Min. Value</th>     
                <th class="text-center" style="width:10%;">Action</th>
            </tr>
        </thead>
        <tbody id="salaryBody">
            <?php echo $salaryData['salaryBody']; ?>
        </tbody>
    </table>
</div>

<script>
function saveSalaryStructure(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/' + fnsave,
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
			//initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            
            $("#salaryBody").html(data.salaryBody);
            $("#head_name").val("");
            $("#type").val(1);
            $("#cal_type").val(1);
            $("#parent_head").val(1);
            $(".single-select").comboSelect();
        }else{
			//initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function deleteSalaryStructure(id,name='Record'){
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
						url: base_url + controller + '/deleteSalaryStructure',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								//initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#salaryBody").html(data.salaryBody);
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