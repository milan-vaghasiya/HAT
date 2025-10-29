<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="item_id" id="item_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : $item_id; ?>" />

            <div class="col-md-4 form-group">
                <label for="cal_date">Calibration Date</label>
                <input type="date" name="cal_date" id="cal_date" class="form-control floatOnly req" value="<?= date("Y-m-d") ?>">
            </div>
            <div class="col-md-4 form-group">
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
           
            <div class="col-md-4 form-group">
                <label for="cal_agency">Calibration Agency</label>
                <input type="text" name="cal_agency" id="cal_agency" class="form-control " value="">
            </div>
            <div class="col-md-5 form-group">
                <label for="cal_certi_no">Certificate No.</label>
                <input type="text" name="cal_certi_no" id="cal_certi_no" class="form-control" value="">
            </div>
            <div class="col-md-5 form-group">
                <label for="certificate_file">Certificate File</label>
                <input type="file" name="certificate_file" class="form-control-file" />
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right mt-30" onclick="storeCalibration('calibration','saveCalibration');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<div class="row">
        <div class="table-responsive">
            <table id="disctbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Calibration Date</th>
                        <th>Calibration By</th>
                        <th>Calibration Agency</th>
                        <th>Certificate No.</th>
                        <th>Certificate File</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="discBody">
                    <?php
                        if (!empty($calData)) :
                            $i=1;
                            foreach ($calData as $row) :
                                $itemId = (!empty($dataRow->item_id)) ? $dataRow->item_id : $item_id;
                                $deleteParam = $row->id.','.$itemId.",'Calibration'";
                                echo '<tr>
                                        <td>'.$i.'</td>
                                        <td>'.formatDate($row->cal_date).'</td>
                                        <td>'.$row->cal_by.'</td>
                                        <td>'.$row->cal_agency.'</td>
                                        <td>'.$row->cal_certi_no.'</td>
                                        <td>'.((!empty($row->certificate_file))?'<a href="'.base_url('assets/uploads/gauges/'.$row->certificate_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                        <td class="text-center">';
                                            echo '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="trashCalibration('.$deleteParam.');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>';
                                        echo '</td>
                                    </tr>'; $i++;
                            endforeach;
                        else:
                            echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<script>

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
            $("#discBody").html(data.tbodyData);
            $("#item_id").val(data.partyId);
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
                                $("#discBody").html(data.tbodyData);
                                $("#item_id").val(data.partyId);
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

