<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row"> 
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>SETUP APPROVAL CUM INPROCESS INSPECTION REPORT</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveLineInspection">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="report_type">Report Type</label>
                                        <select name="report_type" id="report_type" class="form-control req">
                                            <option value="1" <?=(!empty($dataRow->report_type) && $dataRow->report_type == 1)?"selected":""?>>In Process Inspection</option>
                                            <option value="2" <?=(!empty($dataRow->report_type) && $dataRow->report_type == 2)?"selected":""?>>Setup Approval</option>
                                           
                                        </select>
                                        
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="job_card_id">Job Card</label>
                                        <select name="job_card_id" id="job_card_id" class="form-control single-select req">
                                            <option value="">Select Job Card</option>
                                            <?php
                                                foreach($jobData as $row):
                                                    $selected = (!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id)?"selected":"";
							                        echo '<option value="'.$row->id.'" '.$selected.' data-product_id='.$row->product_id.' data-process='.$row->process.'>'.$row->job_number.' ['.$row->item_code.'] ['.$row->wo_no.']</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                        <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="insp_date">Date</label>
                                        <input type="date" name="insp_date" id="insp_date" class="form-control req" value="<?=(!empty($dataRow->insp_date))?$dataRow->insp_date:date('Y-m-d')?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="insp_time">Time</label>
                                        <input type="time" name="insp_time" id="insp_time" class="form-control req" value="<?=(!empty($dataRow->insp_time))?$dataRow->insp_time:date('h:s')?>" />
                                    </div>
									

                                    <div class="col-md-3 form-group">
                                        <label for="process_id">Process</label>
                                        <select name="process_id" id="process_id" class="form-control single-select req">
                                            <?php     
                                                if(!empty($dataRow)){
                                                    echo $processData;
                                                } else {
                                                    echo '<option value="">Select Process</option>';
                                                }                                 
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="operator_id">Operator</label>
                                        <select name="operator_id" id="operator_id" class="form-control single-select">
                                            <option value="">Select Operator</option>
                                            <?php
                                                foreach ($operatorList as $row) :
                                                    $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                                                    $emp_name = (!empty($row->emp_code)) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name;
													echo '<option value="' . $row->id . '" ' . $selected . '>' . $emp_name . '</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="machine_id">Machine</label>
                                        <select name="machine_id" id="machine_id" class="form-control single-select">
                                            <?php 
                                                if(!empty($dataRow)){
                                                    echo $machineData;
                                                } else {
                                                    echo '<option value="">Select Machine</option>';
                                                }   
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="vendor_id">Vendor</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control single-select">
                                            <option value="0">IN House</option>
                                            <?php
                                                foreach ($vendorList as $row) :
                                                    $selected = (!empty($dataRow->vendor_id) && $dataRow->vendor_id == $row->id) ? "selected" : "";
                                                    $party_name = (!empty($row->party_code)) ? '['.$row->party_code.'] '.$row->party_name : $row->party_name;
													echo '<option value="' . $row->id . '" ' . $selected . '>' . $party_name . '</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
									<div class="col-md-3 form-group">
                                        <label for="prepare_id">Prepare</label>
                                        <select name="prepare_id" id="prepare_id" class="form-control single-select">
                                            <option value="">Select Prepare</option>
                                            <?php
                                                foreach ($employeeList as $row) :
                                                    $selected = (!empty($dataRow->prepare_id) && $dataRow->prepare_id == $row->id) ? "selected" : "";
                                                    $emp_name = (!empty($row->emp_code)) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name;
													echo '<option value="' . $row->id . '" ' . $selected . '>' . $emp_name . '</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
									<div class="col-md-3 form-group">
                                        <label for="inspector_id">Inspector/Setter</label>
                                        <select name="inspector_id" id="inspector_id" class="form-control single-select">
                                            <option value="">Select Inspector/Setter</option>
                                            <?php
                                                foreach ($employeeList as $row) :
                                                    $selected = (!empty($dataRow->inspector_id) && $dataRow->inspector_id == $row->id) ? "selected" : "";
                                                    $emp_name = (!empty($row->emp_code)) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name;
													echo '<option value="' . $row->id . '" ' . $selected . '>' . $emp_name . '</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2 form-group sampleQtyDiv">
                                        <label for="sampling_qty">Sampling Qty</label>
                                        <input type="text" name="sampling_qty" id="sampling_qty" class="form-control numericOnly req" value="<?=(!empty($dataRow->sampling_qty))?$dataRow->sampling_qty:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group settingTimeDiv">
                                        <label for="setting_time">Setting (In Minute)</label>
                                        <input type="text" name="setting_time" id="setting_time" class="form-control numericOnly req" value="<?=(!empty($dataRow->setting_time))?$dataRow->setting_time:""?>"/>
                                    </div>
                                    <div class="col-md-7 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" class="form-control" value="<?=(!empty($dataRow->remark)) ? $dataRow->remark:"" ?>" />
                                    </div>  
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="error general"></div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="table-responsive">
										<table id="preDispatchtbl" class="table table-bordered generalTable">
											<thead class="thead-info" id="theadData">
                                               <tr style="text-align:center;">
													<th style="width:5%;">#</th>
													<th>Parameter</th>
													<th style="width:35%;">Specification</th>
													<th>Min. Value</th>
													<th>Max. Value</th>
													<th>Instrument</th>
													<th>Reading</th>
                                                </tr>
                                                <tr style="text-align:center;"> 
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                    if(!empty($dataRow)):
                                                        $obj = json_decode($dataRow->observe_samples); $i=1; 
                                                        if(!empty($paramData)):
                                                            foreach($paramData as $row):
                                                                $c=0;
                                                                echo '<tr style="text-align:center;">
                                                                            <td>'.$i++.'</td>
                                                                            <td>'.$row->drg_diameter.'</td>
                                                                            <td>'.$row->specification.'</td>
                                                                            <td>'.$row->min_value.'</td>
                                                                            <td>'.$row->max_value.'</td>
                                                                            <td>'.$row->inst_used.'</td>';
                                                                echo '<td><input type="text" name="remark_'.$row->id.'" class="xl_input text-center form-control" value="'.$obj->{$row->id}[0].'"></td></tr>';
                                                            endforeach;
                                                        else:
                                                            echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                                                        endif;
                                                    else:
                                                        echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                                                    endif;
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveLineInspection('saveLineInspection');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url('/controlPlan/lineInspection')?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    setTimeout(function(){$('#report_type').trigger('change') }, 10);
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

    $(document).on('change','#process_id',function(e){
		$(".error").html("");
		var valid = 1;
		var job_card_id = $('#job_card_id').val();
		var product_id = $('#product_id').val();
		var process_id = $('#process_id').val();

		if($("#job_card_id").val() == ""){$(".job_card_id").html("Job Card is required.");valid=0;}
		if($("#process_id").val() == ""){$(".process_id").html("Process is required.");valid=0;}
	
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getLineInspection',
                data: {product_id:product_id,process_id:process_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html("");
					$("#tbodyData").html(data.tbodyData);
					$("#theadData").html("");
					$("#theadData").html(data.theadData);
                }
            });
        }
    }); 
    
    $(document).on("change","#process_id", function(){
        var process_id = $(this).val();
        if(process_id){
            $.ajax({
                url: base_url + controller + '/getMachineByProcess',
                type: 'post',
                data: {process_id: process_id},
                dataType: 'json',
                success: function(data) {
                    $("#machine_id").html("");
                    $("#machine_id").html(data.mcOptions);
                    $("#machine_id").comboSelect();
                }
            });
        } else { 
            $("#machine_id").html("<option value=''>Select Machine</option>"); $("#machine_id").comboSelect(); 
        }
    });

    $(document).on('change','#report_type',function(){
		var report_type = $(this).val();
        if(report_type == 1){
            $(".sampleQtyDiv").show();
            $(".settingTimeDiv").hide();
        }else{
            $(".sampleQtyDiv").hide();
            $(".settingTimeDiv").show();
        }
    });
});

function saveLineInspection(formId,fnsave){
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
			initTable(0); $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/controlPlan/lineInspection';
        }else{
			initTable(0);  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/controlPlan/lineInspection';

        }
	});
}
</script>