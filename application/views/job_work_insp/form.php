<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Incoming Inspection</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="InInspection">
                            <div class="col-md-12"> 
                                <input type="hidden" name="id" value="<?=(!empty($inInspectData->inpect_id))?$inInspectData->inpect_id:""?>" />
                                <input type="hidden" name="grn_id" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_card_id:""?>" />
                                <input type="hidden" name="grn_trans_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
                                <input type="hidden" name="party_id" value="<?=(!empty($dataRow->vendor_id))?$dataRow->vendor_id:""?>" />
                                <input type="hidden" name="item_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>" />
                                <input type="hidden" name="trans_type" value="1" />

                                <div class="row">
									<div class="col-md-8 form-group">
                                        <label>Job No.: <?=(!empty($dataRow->job_no))? getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):"";?></label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label>Sampling Qty.</label>
                                        <input type="text" name="sampling_qty" id="sampling_qty" class="form-control floatOnly" value="<?=(!empty($inInspectData->sampling_qty))?$inInspectData->sampling_qty:0?>" />
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
											<thead class="thead-info">
												<tr style="text-align:center;">
													<th rowspan="2" style="width:5%;">#</th>
													<th rowspan="2">Parameter</th>
													<th rowspan="2">Specification</th>
													<th rowspan="2">Tolerance</th>
													<th rowspan="2">Psc/Sp. Characteristics</th>
													<th rowspan="2">Instrument Used</th>
													<th colspan="10">Observation on Samples</th>
													<th rowspan="2">Result</th>
                                                </tr>
                                                <tr style="text-align:center;">
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
													<th>6</th>
													<th>7</th>
													<th>8</th>
													<th>9</th>
													<th>10</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $tbodyData="";$i=1; 
                                                    if(!empty($paramData)):
                                                        foreach($paramData as $row):
                                                            $obj = New StdClass;
                                                            if(!empty($inInspectData)):
                                                                $obj = json_decode($inInspectData->observation_sample);
                                                            endif;
                                                            $tbodyData.= '<tr>
                                                                        <td style="text-align:center;">'.$i++.'</td>
                                                                        <td>'.$row->parameter.'</td>
                                                                        <td>'.$row->specification.'</td>
                                                                        <td>'.$row->lower_limit.'</td>
                                                                        <td>'.$row->upper_limit.'</td>
                                                                        <td>'.$row->measure_tech.'</td>';
                                                            for($c=0;$c<10;$c++):
                                                                if(!empty($obj->{$row->id})):
                                                                    $tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center" value="'.$obj->{$row->id}[$c].'"></td>';
                                                                else:
                                                                    $tbodyData .= '<td><input type="text" name="sample'.($c+1).'_'.$row->id.'" class="xl_input maxw-60 text-center" value=""></td>';
                                                                endif;
                                                            endfor;
                                                            if(!empty($obj->{$row->id})):
                                                                $tbodyData .= '<td><input type="text" name="result_'.$row->id.'" class="xl_input maxw-150 text-center" value="'.$obj->{$row->id}[10].'"></td></tr>';
                                                            else:
                                                                $tbodyData .= '<td><input type="text" name="result_'.$row->id.'" class="xl_input maxw-150 text-center" value=""></td></tr>';
                                                            endif;
                                                            
                                                        endforeach;
                                                    else:
                                                        $tbodyData.= '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
                                                    endif;
                                                    echo $tbodyData;
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInInspection('InInspection','saveInInspection');" ><i class="fa fa-check"></i> Save</button>
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
    function saveInInspection(formId,fnsave){
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
            location.href = base_url + 'jobWorkInspection';
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + 'jobWorkInspection';
        }
	});
}
</script>