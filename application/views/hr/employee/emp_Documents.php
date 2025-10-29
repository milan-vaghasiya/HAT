
<div class="col-md-12">
    <form id="getEmpDocuments" enctype="multipart/form-data">
        <div class="row">
        <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id ?>" />

            <div class="col-md-4 form-group">
                <label for="doc_name">Document Name</label>
                <input type="text" name="doc_name" id="doc_name" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="doc_no">Document No.</label>
                <input type="text" name="doc_no" id="doc_no" class="form-control req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="doc_type">Document Type</label>
                <select name="doc_type" id="doc_type" class="form-control req">
                    <option value="">Select Document Type </option>
                    <option value="1">Extra Document</option>
                    <option value="2">Aadhar Card</option>
                    <option value="3">Basic Rules</option>
                </select>
            </div>
            <div class="col-md-8 form-group">
                <label for="doc_file">Document File</label>
                <div class="input-group">
                    <input type="file" name="doc_file" id="doc_file" class="form-control-file" style="width:80%;"/>
                </div>                
            </div>
            <div class="col-md-4 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save mt-30" onclick="saveEmpDocuments('getEmpDocuments','saveEmpDocumentsParam');"><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="inspection" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th class="text-center">Document Name</th>
                        <th class="text-center">Document No.</th>                        
                        <th class="text-center">Document Type</th>
                        <th class="text-center">Document File</th>
                        <th class="text-center" style="width:10%;">Action</th>
                        
                    </tr>
                </thead>
                <tbody id="inspectionBody">
                    <?php
                        if(!empty($docData)):
                            $i=1;
                            foreach($docData as $row):
                                echo '<tr>
                                            <td class="text-center">'.$i++.'</td>
                                            <td class="text-center">'.$row->doc_name.'</td>
                                            <td class="text-center">'.$row->doc_no.'</td>
                                            <td class="text-center">'.$row->doc_type_name.'</td>
                                            <td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                            <td class="text-center">
                                                <button type="button" onclick="trashPreInspection('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        else:
                            echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function saveEmpDocuments(formId,fnsave){
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
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#inspectionBody").html(data.tbodyData);
            $("#doc_name").val("");
            $("#doc_no").val("");
            $("#doc_type").val("");
            $("#doc_file").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function trashPreInspection(id,emp_id,name='Record'){
	var send_data = { id:id, emp_id:emp_id };
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
						url: base_url + controller + '/deletePreInspection',
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
								initTable(); 
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
</script>