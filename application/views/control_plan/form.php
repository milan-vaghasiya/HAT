<div class="col-md-12">
    <form id="getInspectionParameter">
        <div class="row">
            <input type="hidden" name="id" id="id" class="id" value="" />
            <input type="hidden" name="item_id" id="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-3 form-group">
                <label for="process_id">Process</label>
                <select name="process_id" id="process_id" class="form-control single-select req">
                    <option value="">Select Process</option>
                    <?php
                        foreach($processData as $row):
                            echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error process_id"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="drg_diameter">Drg. Diameter</label>
                <input type="text" name="drg_diameter" id="drg_diameter" class="form-control req">
            </div>
            <div class="col-md-3 form-group">
                <label for="specification">Specification</label>
                <input type="text" name="specification" id="specification" class="form-control req" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="inst_used">Instrument</label>
                <select name="inst_used" id="inst_used" class="form-control single-select">
                    <option value="">Select Instrument</option>
                    <?php
                     	foreach($instruments as $row):
                            $selected = (!empty($dataRow->inst_used) && $dataRow->inst_used == $row->category_name)?"selected":"";
                            echo '<option value="'.$row->category_name.'" '.$selected.'>'.$row->category_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error inst_used"></div>
            </div>
            <div class="col-md-2 form-group">
                <label for="min_value">Min. Value</label>
                <input type="text" name="min_value" id="min_value" class="form-control">
            </div>
            <div class="col-md-2 form-group">
                <label for="max_value">Max. Value</label>
                <input type="text" name="max_value" id="max_value" class="form-control">
            </div>
           
            <div class="col-md-2 form-group">
                <label for="is_line">Is Line</label>
                <select name="is_line" id="is_line" class="form-control single-select">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_line == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_line == 1)?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="is_final">Is Final</label>
                <select name="is_final" id="is_final" class="form-control single-select ">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_final == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_final == 1)?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="sequence">Sequence</label>
                <input type="text" name="sequence" id="sequence" class="form-control numericOnly req" value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="rev_no">Rev No</label>
                <div class="input-group">
                    <input type="text" name="rev_no" id="rev_no" class="form-control mr-2" value="<?=!empty($itemData->rev_no)?$itemData->rev_no:''?>" readonly/>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save" onclick="saveInspectionParameter('getInspectionParameter','saveInspectionParameter');"><i class="fa fa-plus"></i>Add</button>
                </div>
            </div>
           
        </div>
    </form>
    <hr>
    <div class="row  justify-content-end"> 
        <a href="<?= base_url($headData->controller . '/createInspectionExcel/' . $item_id.'/' ) ?>" class="btn btn-labeled btn-info bg-info-dark mr-2" target="_blank">
            <i class="fa fa-download"></i>&nbsp;&nbsp;
            <span class="btn-label">Download Excel&nbsp;&nbsp;<i class="fa fa-file-excel"></i></span>
        </a>
        <input type="file" name="insp_excel" id="insp_excel" class="form-control-file float-left col-md-3" />
        <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark ml-2 importExcel  " type="button">
            <i class="fa fa-upload"></i>&nbsp;
            <span class="btn-label">Upload Excel &nbsp;<i class="fa fa-file-excel"></i></span>
        </a>
        <h6 class="col-md-12 msg text-primary text-center mt-1">
        </h6>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="inspection" class="table table-bordered align-items-center fhTable">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Process</th>
                        <th>Drg. Diameter</th>
                        <th>Specification</th>
                        <th>Min. Value</th>
                        <th>Max. Value</th>
                        <th>Instrument</th>
                        <th>Is Line</th>
                        <th>Is Final</th>
                     
                        <th>Sequence</th>
                        <th>Rev No</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="inspectionBody" class="scroll-tbody scrollable maxvh-60">
                    <?=$paramData?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
});
function saveInspectionParameter(formId,fnsave){
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
			initTable(0); 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#inspectionBody").html(data.tbodyData);
            $("#id").val("");
            $("#specification").val("");
            $("#drg_diameter").val("");
            $("#sequence").val("");
            $("#min_value").val("");
            $("#max_value").val("");
        }else{
			initTable(0);  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function deleteInspectionParameter(id,item_id,param_type,name='Record'){
	var send_data = { id:id, item_id:item_id,param_type:param_type };
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
						url: base_url + controller + '/deleteInspectionParameter',
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
								initTable(0); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#inspectionBody").html(data.tbodyData);
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

function editInspectionParameter(id,button){
    if(id){
            $.ajax({
                url: base_url + controller + '/editInspectionParameter',
                data: {id:id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $.each(data,function(key, value) {
                        $("#"+key).val(value);
                    }); 
                    $(".single-select").comboSelect();
                    var row = $(button).closest("TR");  
                    var table = $("#inspection")[0];
                    table.deleteRow(row[0].rowIndex);
                    $('#inspectionBody tbody tr td:nth-child(1)').each(function(idx, ele) {
                        ele.textContent = idx + 1;
                    });
                }
            });
        }
}
</script>