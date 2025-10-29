<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>

<div class="col-md-12">
    <div class="row">
        <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
        <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>" />
        <div class="col-md-5 form-group">
            <label for="emp_dept_id">Department</label>
            <select name="emp_dept_id" id="emp_dept_id" class="form-control single-select req">
                <option value="">Select Department</option>
                <?php
                    foreach($deptRows as $row):
                        $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                    endforeach;
                ?>
            </select>
            <div class="error emp_dept_id"></div>
        </div>
        <div class="col-md-5 from-group">
            <label for="emp_designation">Designation</label>
            <select name="emp_designation" id="emp_designation" class="form-control single-select req" tabindex="-1">
                <option value="">Select Designation</option>
                <?php
                    foreach($descRows as $row):
                        $selected = (!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation)?"selected":"";
                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                    endforeach;
                ?>
            </select>            
            <div class="error emp_designation"></div>
        </div>
        <div class="col-md-2 form-group">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-success waves-effect add-leave btn-block" onclick="addLeaveAuthority()"><i class="fa fa-plus"></i> Add</a>
        </div>
    </div>
</div>

<div class="col-md-12 mt-3">
    <div class="row">
        <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Leave Hierarchy</i></h6>
        <div class="error general"></div>
        <table id="leaveHierarchy" class="table excel_table table-bordered">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Priority</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="leaveBody">
            <?php
                if (!empty($leaveData)) :
                    $i=1;
                    foreach ($leaveData as $row) :
						$deleteParam = $row->id.','.$row->emp_id;
						$deleteButton = '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" onclick="deleteLeaveAuthority('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                        echo '<tr id="'.$row->id.'">
                            <td class="text-center">'.$i++.'</td>
                            <td>'.$row->name.'</td>
                            <td>'.$row->title.'</td>
                            <td class="text-center">'.$row->priority.'</td>
                            <td>'.$deleteButton.'</td>
                        </tr>';
                    endforeach;
                else :
                    echo '<tr><td colspan="5" class="text-center">No Data Found.</td></tr>';
                endif;
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function addLeaveAuthority(){
		$(".error").html("");
		var valid = 1;
        var id = $('#id').val();
        var emp_id = $('#emp_id').val();
        var dept_id = $('#emp_dept_id').val();
        var desi_id = $('#emp_designation').val();
        
		if($("#emp_dept_id").val() == ""){$(".emp_dept_id").html("Department is required.");valid=0;}
		if($("#emp_designation").val() == ""){$(".emp_designation").html("Designation is required.");valid=0;}
        if(valid){
            $.ajax({ 
                type: "post",   
                url: base_url + controller + '/saveLeaveAuthority',
                data: {id:id,emp_id:emp_id, dept_id:dept_id, desi_id:desi_id},
                dataType:'json',
                success:function(data){
                    if(data.status==0)
                    {
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
                    }
                    else
                    {
                        $("#leaveBody").html(data.leaveHtml);
                        $('.single-select').comboSelect();setPlaceHolder();
                    }
                }
            });
        }
    }
	
	function deleteLeaveAuthority(id,emp_id){
		var send_data = { id:id,emp_id:emp_id };
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to delete this Record?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/deleteLeaveAuthority',
							data: send_data,
							type: "POST",
							dataType:"json",
							success:function(data)
							{
								if(data.status===0){
									$(".error").html("");
									$.each( data.message, function( key, value ) {$("."+key).html(value);});
								}else{$("#leaveBody").html(data.leaveHtml);}
								$('.single-select').comboSelect();setPlaceHolder();
								
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
