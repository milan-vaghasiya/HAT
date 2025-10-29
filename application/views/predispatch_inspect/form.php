<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Final Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveFinalInspection">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>"/>
                                <div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="report_no">Report No.</label>
                                        <div class="input-group mb-3">
											<input type="text" name="report_prefix" class="form-control req" value="<?=(!empty($dataRow->report_prefix))?$dataRow->report_prefix:$report_prefix?>" readonly />
											<input type="text" name="report_no" class="form-control" value="<?=(!empty($dataRow->report_no))?$dataRow->report_no:$nextReportNo?>" readonly />
										</div>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="date">Inspection Date</label>
                                        <input type="date" name="date" id="date" class="form-control req" value="<?=(!empty($dataRow->date))?$dataRow->date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="party_id">Customer</label>
                                        <select name="party_id" id="party_id" class="form-control single-select">
                                            <option value="">Select Customer</option>
                                            <?php
                                                foreach($customerData as $row):
                                                    $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id)?"selected":"";
							                        echo '<option value="'.$row->id.'" '.$selected.'>['.$row->party_code.'] '.$row->party_name.'</option>';
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>
									<div class="col-md-4 form-group">
                                        <label for="item_id">Product</label>
                                        <select name="item_id" id="item_id" class="form-control single-select req">
                                            <?php     
                                                if(!empty($dataRow)){
                                                    echo $itemData;
                                                } else {
                                                    echo '<option value="">Select Product</option>';
                                                }                                 
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="po_no">P.O. No.</label>
                                        <select name="po_no" id="po_no" class="form-control single-select req">
                                            <?php     
                                                if(!empty($dataRow)){
                                                    echo $soData;
                                                } else {
                                                    echo '<option value="">Select P.O. No.</option>';
                                                }                                 
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="job_id">Batch No.</label>
                                        <select name="job_id" id="job_id" class="form-control single-select req">
                                            <?php     
                                                if(!empty($dataRow)){
                                                    echo $jobData;
                                                } else {
                                                    echo '<option value="">Select Batch No</option>';
                                                }                                 
                                            ?>										</select>
                                        <input type="hidden" name="batch_no" id="batch_no" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="drg_no">Drg. No.</label>
                                        <input type="text" name="drg_no" id="drg_no" class="form-control floatOnly" value="<?=(!empty($dataRow->drg_no))?$dataRow->drg_no:""?>" />
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="dispatch_qty">Inspection Qty.</label>
                                        <input type="text" name="dispatch_qty" id="dispatch_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->dispatch_qty))?$dataRow->dispatch_qty:0?>" />
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
										<table id="finaltbl" class="table table-bordered generalTable">
											<thead class="thead-info">
												<tr style="text-align:center;">
													<th rowspan="2" style="width:5%;">#</th>
													<th>Description</th>
													<th rowspan="2">Tol.</th>
													<th rowspan="2">Min. Value</th>
													<th rowspan="2">Max. Value</th>
													<th rowspan="2">Instrument</th>
													<th colspan="5">Sample</th>
													<th rowspan="2">Remarks</th>
                                                </tr>
                                                <tr style="text-align:center;">
													<th>Drg. Dimension</th>
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
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
                                                                for($c=0;$c<5;$c++):
                                                                    echo '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input text-center" value="'.((!empty($obj->{$row->id}[$c]))?$obj->{$row->id}[$c] : '').'"></td>';
                                                                endfor;
                                                                echo '<td><input type="text" name="remark_'.$row->id.'" class="xl_input text-center" value="'.((!empty($obj->{$row->id}[5])) ? $obj->{$row->id}[5] : '').'"></td></tr>';
                                                            endforeach;
                                                        else:
                                                            echo '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
                                                        endif;
                                                    else:
                                                        echo '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
                                                    endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

							<hr>
							<div class="col-md-12">
								<div class="row">
                                    <div class="col-md-2 form-group">
                                        <label for="dispatch_qty">Qty. Received</label>
                                        <input type="text" name="dispatch_qty" id="dispatch_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->dispatch_qty))?$dataRow->dispatch_qty:0?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="accepted_qty">Qty. Accepted</label>
                                        <input type="text" name="accepted_qty" id="accepted_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->accepted_qty))?$dataRow->accepted_qty:0?>" />
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="rejected_qty">Qty. Rejected</label>
                                        <input type="text" name="rejected_qty" id="rejected_qty" class="form-control floatOnly" value="<?=(!empty($dataRow->rejected_qty))?$dataRow->rejected_qty:0?>" />
                                    </div>
									<div class="col-md-3 form-group">
										<label for="prepare_id">Inspected By</label>
										<select name="prepare_id" id="prepare_id" class="form-control single-select">
											<option value="">Select Inspected By</option>
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
                                        <label for="authorize_id">Approved By</label>
                                        <select name="authorize_id" id="authorize_id" class="form-control single-select">
                                            <option value="">Select Approved By</option>
                                            <?php
                                                foreach ($employeeList as $row) :
                                                    $selected = (!empty($dataRow->authorize_id) && $dataRow->authorize_id == $row->id) ? "selected" : "";
                                                    $emp_name = (!empty($row->emp_code)) ? '['.$row->emp_code.'] '.$row->emp_name : $row->emp_name;
                                                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $emp_name . '</option>';
                                                endforeach;
                                            ?>
                                        </select>
								    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="reason">Reason</label>
                                        <input type="text" name="reason" id="reason" class="form-control" value="<?=(!empty($dataRow->reason))?$dataRow->reason:""?>" />
                                    </div>
								</div>
							</div>
						</form>
                    </div>

                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveFinal('saveFinalInspection');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
    $(document).on('change','#party_id', function(e){
		var party_id = $(this).val();
		if(party_id){
			$.ajax({
                url: base_url + controller + '/getItemsByParty',
                data: {party_id:party_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#party_id").comboSelect();
					$("#item_id").html("");
					$("#item_id").html(data.partyItems);
					$("#item_id").comboSelect();
                    $("#item_id").trigger('change');
                }
            });
		}
	});

    $(document).on('change','#item_id',function(){
		var item_id = $(this).val();
        if(item_id)
		{
			$.ajax({
				url: base_url + controller + '/getFinalInspection',
				data: {item_id:item_id},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#tbodyData").html(data.tbodyData);
					$("#item_id").comboSelect();
                    $("#party_id").comboSelect();
                    $("#po_no").html("");
                    $("#po_no").html(data.soOptions);
                    $("#po_no").comboSelect();
					$("#job_id").html("");
					$("#job_id").html(data.jobNo);
                    $("#job_id").comboSelect();
				}
			});
		}
    });
	
	$(document).on('change','#job_id',function(){
		var batch_no = $('#job_id :selected').data('batch_no');
		$('#batch_no').val(batch_no);
    });
});

function saveFinal(formId,fnsave){
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
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/preDispatchInspect';
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + '/preDispatchInspect';
        }
	});
}
</script>