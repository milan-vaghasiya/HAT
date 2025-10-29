<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Salary Structure</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row"> 
                                <div class="col-lg-12 col-xlg-9 col-md-9">
                                    <div class="card">
                                        <!-- Tabs -->
                                        <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="pills-salary" aria-selected="true">Salary Heads</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-ctc-tab" data-toggle="pill" href="#ctc" role="tab" aria-controls="pills-ctc" aria-selected="false">CTC Format</a>
                                            </li>
                                        </ul>
                                        
                                         <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="salary" role="tabpanel" aria-labelledby="pills-salary-tab">
                                                <div class="card-body">
                                                    <form id="salaryStructure">
                                                        <div class="row">
                                                            <input type="hidden" name="id" value="" />
                                                            <div class="col-md-4 form-group">
                                                                <label for="head_name">Head Name</label>
                                                                <input type="text" name="head_name" id="head_name" class="form-control req" value="">
                                                            </div>
                                                            <div class="col-md-2 form-group"> <!-- 1 = Earnings, -1 = Deduction -->
                                                                <label for="type">Type</label> 
                                                                <select name="type" id="type" class="form-control single-select req">
                                                                    <option value="1">Earnings</option>
                                                                    <option value="-1">Deduction</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 form-group"> <!-- 1 = Basic, 2= HRA, 3 = PF, 4 = Speacial -->
                                                                <label for="cal_type">Cal. Type</label>
                                                                <select name="cal_type" id="cal_type" class="form-control single-select req">
                                                                    <option value="1">Basic</option>
                                                                    <option value="2">HRA</option>
                                                                    <option value="3">PF</option>
                                                                    <option value="4">Speacial</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="parent_head">Parent Head</label> <!-- 	1= Gross Earning, 2 = General Earning, 3 = Gross Deduction , 4 = General Deduction -->
                                                                <select name="parent_head" id="parent_head" class="form-control single-select req">
                                                                    <option value="1">Gross Earning</option>
                                                                    <option value="2">General Earning</option>
                                                                    <option value="3">Gross Deduction</option>
                                                                    <option value="4">General Deduction</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-1 form-group">
                                                                <button type="button" class="btn btn-success btn-save float-right mt-30 btn-block" onclick="saveSalaryStructure('salaryStructure','saveSalaryStructure');">Save</button>
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
                                                                    <th class="text-center">Cal. Type</th>
                                                                    <th class="text-center">Parent Head</th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="salaryBody">
                                                                <?php echo $salaryData['salaryBody']; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                             <div class="tab-pane fade" id="ctc" role="tabpanel" aria-labelledby="pills-ctc-tab">
                                                <div class="card-body">
                                                    <!-- <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="card border-left border-orange">
                                                                <div class="card-body">
                                                                    <div class="d-flex no-block align-items-center">
                                                                        <div>
                                                                            <span class="text-orange display-6"><i class="icon-Bank"></i></span>
                                                                        </div>
                                                                        <div class="ml-auto">
                                                                            <h2>290</h2>
                                                                            <h6 class="text-orange">New Customers</h6>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <form id="ctcStructure">
                                                        <div class="row">
                                                            <input type="hidden" name="id" value="" />
                                                            <div class="col-md-3 form-group">
                                                                <label for="format_name">Format Name</label>
                                                                <input type="text" name="format_name" id="format_name" class="form-control req" value="">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="effect_from">Effect From</label>
                                                                <input type="date" name="effect_from" id="effect_from" class="form-control req" value="<?= date('Y-m-d') ?>">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="remark">Remark</label>
                                                                <input type="text" name="remark" id="remark" class="form-control" value="">
                                                            </div>
                                                            
                                                            <hr style="width:100%">

                                                            <div class="col-md-2 form-group">
                                                                <label for="salary_head">Salary Head</label> 
                                                                <select name="salary_head" id="salary_head" class="form-control single-select req">
                                                                    <?php 
                                                                        if(!empty($salaryHead)):
                                                                            echo '<option value="">Select Salary Head</option>';
                                                                            foreach($salaryHead as $row):
                                                                                echo '<option value="'.$row->id.'">'.$row->head_name.'</option>';
                                                                            endforeach;
                                                                        else:
                                                                            echo '<option value="">Select Salary Head</option>';
                                                                        endif;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2 form-group">
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
                                                            <div class="col-md-2 form-group">
                                                                <label for="cal_value">Cal. Value</label> 
                                                                <input type="text" name="cal_value" id="cal_value" class="form-control floatOnly req" value="">
                                                            </div>
                                                            <div class="col-md-2 form-group">
                                                                <label for="min_value">Min. Value</label> 
                                                                <input type="text" name="min_value" id="min_value" class="form-control floatOnly" value="">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="cal_on">Cal. On</label> 
                                                                <select name="calonSelect" id="calonSelect" data-input_id="cal_on" class="form-control jp_multiselect" multiple="multiple">
                                                                    <?php 
                                                                        if(!empty($salaryHead)):
                                                                            foreach($salaryHead as $row):
                                                                                echo '<option value="'.$row->id.'">'.$row->head_name.'</option>';
                                                                            endforeach;
                                                                        else:
                                                                            echo '<option value="">Select Salary Head</option>';
                                                                        endif;
                                                                    ?>
                                                                </select>
                                                                <input type="hidden" name="cal_on" id="cal_on" value="">
                                                            </div>

                                                            <div class="col-md-1 form-group">
                                                                <button type="button" class="btn btn-success btn-save float-right mt-30 btn-block" onclick="saveCtc('ctcStructure','saveCtc');"> Add</button>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <hr style="width:100%">

                                                    <div class="table-responsive">
                                                        <table id="ctctbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th class="text-center">Format Name</th>   
                                                                    <th class="text-center">Effect From</th>                  
                                                                    <th class="text-center">Salary Head</th>
                                                                    <th class="text-center">Cal. Method</th>
                                                                    <th class="text-center">Cal. Value</th>
                                                                    <th class="text-center">Min. Value</th>
                                                                    <th class="text-center">Cal. On</th>
                                                                    <th class="text-center">Remark</th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ctcBody">
                                                                <?php echo $ctcData['ctcBody']; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

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

function saveCtc(formId,fnsave){
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
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            
            $("#ctcBody").html(data.ctcBody);
            $("#salary_head").val("");
            $("#cal_method").val(1);
            $("#cal_value").val("");
            $("#min_value").val("");
            $("#cal_on").val("");
            $(".single-select").comboSelect();
            reInitMultiSelect();
        }else{ 
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function deleteCtc(id,name='Record'){
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
						url: base_url + controller + '/deleteCtc',
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
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#ctcBody").html(data.ctcBody);
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