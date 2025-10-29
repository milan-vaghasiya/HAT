<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : $item_id; ?>" />

            <div class="col-md-3 form-group">
                <label for="instrument_id">Instrument</label>
                <select name="instrument_id" id="instrument_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($itemData as $row) :
                            $selected = (!empty($dataRow->instrument_id) && $dataRow->instrument_id == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" '.$selected.'>['.$row->item_code.']'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="cal_date">Calibration Date</label>
                <input type="date" name="cal_date" id="cal_date" class="form-control req" value="<?= date("Y-m-d") ?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="cal_by">Calibration By</label>
                <select name="cal_by" id="cal_by" class="form-control single-select">
					<option value="">Select</option>
                    <?php
						if(!empty($dataRow->cal_by)):
							if($dataRow->cal_by == "Inhouse"):
								echo '<option value="Inhouse" selected>Inhouse</option><option value="Outside">Outside</option>';
							else:
								echo '<option value="Inhouse">Inhouse</option><option value="Outside" selected>Outside</option>';
							endif;
						else:
							echo '<option value="Inhouse">Inhouse</option><option value="Outside">Outside</option>';
						endif;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group vndrList">
                <label for="cal_agency">Calibration Agency</label>
                <select  name="cal_agency" id="cal_agency" class="form-control single-select">
                    <option value="">Select Agency</option>
                    <?php
                    if(!empty($supVndrList)){
                        foreach($supVndrList as $row){
                            ?><option value="<?=$row->id?>"><?=$row->party_name?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="cal_certi_no">Certificate No.</label>
                <input type="text" name="cal_certi_no" id="cal_certi_no" class="form-control" value="">
            </div>
            <div class="col-md-3 form-group">
                <label for="certificate_file">Certificate File</label>
                <input type="file" name="certificate_file" class="form-control-file" />
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th >Parameter</th>
                                <th class="text-center">GO</th>
                                <th class="text-center">NOGO</th>
                                <th class="text-center">Remark</th>
                            </tr>
                           
                        </thead>
                        <tbody>
                            <tr>
                                <th>DURING CAL.</th>
                                <td><input type="text" name="during_go" class="form-control floatOnly text-center"></td>
                                <td><input type="text" name="during_nogo" class="form-control floatOnly text-center"></td>
                                <td><input type="text" name="during_remark" class="form-control floatOnly text-center"></td>
                            </tr>
                            <tr>
                                <th>AFTER CAL./REPAIR</th>
                                <td><input type="text" name="after_go" class="form-control floatOnly text-center"></td>
                                <td><input type="text" name="after_nogo" class="form-control floatOnly text-center"></td>
                                <td><input type="text" name="after_remark" class="form-control floatOnly text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <tr>
                        <th colspan="4" class="text-center">Specification</th>
                    </tr>
                    <tr>
                        <th style="width:25%;">Size (Go): </th><td style="width:25%;"><?=$dataRow->drawing_no?></td>
                        <th style="width:25%;">Acceptance Criteria (Go): </th><td style="width:25%;"><?=$dataRow->drawing_file?></td>
                    </tr>
                    <tr>
                        <th style="width:25%;">Size (No Go) : </th><td style="width:25%;"><?=$dataRow->part_no?></td>
                        <th style="width:25%;">Acceptance Criteria (No Go): </th><td style="width:25%;"><?=$dataRow->rev_no?></td>
                    </tr>	
                </table>
            </div>
            <div class="col-md-12 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="storeCalibration('calibration','saveCalibration');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="table-responsive">
        <table id="disctbl" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;" rowspan="2">#</th>
                    <th rowspan="2">Calibration Date</th>
                    <th rowspan="2">Calibration By</th>
                    <th rowspan="2">Calibration Agency</th>
                    <th rowspan="2">Parameter</th>
                    <th colspan="2">Actual Size</th>
                    <th rowspan="2">Remark</th>
                    <th rowspan="2">Certificate No.</th>
                    <th rowspan="2">Certificate File</th>
                    <th rowspan="2" class="text-center" style="width:10%;">Action</th>
                </tr>
                <tr>
                    <th>GO</th>
                    <th>NOGO</th>
                </tr>
            </thead>
            <tbody id="calibrationBody">
                <?php
                    if (!empty($calData)) :
                        $i=1;
                        foreach ($calData as $row) :
                            $itemId = (!empty($dataRow->item_id)) ? $dataRow->item_id : $item_id;
                            $deleteParam = $row->id.','.$itemId.",'Calibration'";
                            echo '<tr>
                                    <td rowspan="2">'.$i.'</td>
                                    <td rowspan="2">'.formatDate($row->cal_date).'</td>
                                    <td rowspan="2">'.$row->cal_by.'</td>
                                    <td rowspan="2">'.$row->cal_agency.'</td>
                                    <td>DURING CAL.</td>
                                    <td>'.$row->during_go.'</td>
                                    <td>'.$row->during_nogo.'</td>
                                    <td>'.$row->during_remark.'</td>
                                    <td rowspan="2">'.$row->cal_certi_no.'</td>
                                    <td rowspan="2">'.((!empty($row->certificate_file))?'<a href="'.base_url('assets/uploads/instrument/'.$row->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                    <td class="text-center" rowspan="2">';
                                        echo '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashCalibration('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>';
                                    echo '</td>
                                </tr>
                                <tr>
                                    <td>AFTER CAL./REPAIR</td>
                                    <td>'.$row->after_go.'</td>
                                    <td>'.$row->after_nogo.'</td>
                                    <td>'.$row->after_remark.'</td>
                                </tr>'; $i++;
                        endforeach;
                    else:
                        echo '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function(){
    $(".vndrList").hide();
    $(document).on('change',"#cal_by",function(){
       var cal_by = $(this).val();
       if(cal_by == 'Outside'){ $(".vndrList").show(); }else{ $(".vndrList").hide(); }
    });
});
function storeCalibration(formId,fnsave,srposition=1){
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
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#calibrationBody").html("");
            $("#calibrationBody").html(data.tbodyData);
            $("#item_id").val(data.partyId);
            $("#instrument_id").val("");
            $("#cal_date").val("");
            $("#cal_by").val("");
            $("#cal_agency").val("");
            $("#cal_certi_no").val("");
            $("#certificate_file").val("");
        }else{
			initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function trashCalibration(id,item_id,name='Record'){
	var send_data = { id:id,item_id:item_id };
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
						url: base_url + controller + '/deleteCalibration',
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
							    $("#calibrationBody").html("");
                                $("#calibrationBody").html(data.tbodyData);
                                $("#item_id").val(data.partyId);
                                $("#instrument_id").val("");
                                $("#cal_date").val("");
                                $("#cal_by").val("");
                                $("#cal_agency").val("");
                                $("#cal_certi_no").val("");
                                $("#certificate_file").val("");
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