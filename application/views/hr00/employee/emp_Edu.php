<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
            <div class="col-md-6 form-group">
                <label for="course">Course</label>
                <input type="text" id="course" class="form-control req" placeholder="Course" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="university">University/Board</label>
                <input type="text" id="university" class="form-control" placeholder="University/Board" value="" />
            </div>
            <div class="col-md-6 form-group">
                <label for="passing_year">Passing Year</label>
                <input type="text" id="passing_year" class="form-control req" placeholder="Passing Year" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="grade">Per./Grade</label>
                <input type="text" id="grade" class="form-control req" placeholder="Per./Grade" value="" />
            </div>
            <div class="col-md-3 form-group">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30" onclick="AddRow();"><i class="fa fa-plus"></i> Add Education</button>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table id="empEdutbl" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Course</th>
                            <th>University/Board</th>
                            <th>Passing Year</th>
                            <th>Per./Grade</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="empEduBody">
                        <?php
                        if (!empty($eduData)) :
                                $i = 1;
                                foreach ($eduData as $row) :
                                    echo '<tr>
                                                <td>' . $i++ . '</td>
                                                <td>
                                                    ' . $row->course . '
                                                    <input type="hidden" name="course[]" value="' . $row->course . '">
                                                 </td>
                                                <td>
                                                    ' . $row->university . '
                                                    <input type="hidden" name="university[]" value="' . $row->university . '">
                                                    <input type="hidden" name="trans_id[]" value="' . $row->id . '">
                                                </td>
                                                <td>
                                                    ' . $row->passing_year . '
                                                    <input type="hidden" name="passing_year[]" value="' . $row->passing_year . '">
                                                </td>
                                                <td>
                                                    ' . $row->grade . '
                                                    <input type="hidden" name="grade[]" value="' . $row->grade . '">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                                                </td>
                                            </tr>';
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>

function AddRow() {
    $(".error").html("");
	if($("#course").val() == "" || $("#passing_year").val() == "" || $("#grade").val() == ""){
		if($("#course").val() == ""){
			$(".course").html("Course is required.");
		}
		if($("#passing_year").val() == ""){
			$(".passing_year").html("Passing Year is required.");
		}
		if($("#grade").val() == "" || $("#grade").val() == 0){
			$(".grade").html("Grade is required.");
		}
	}else{
		/* var nomNames = $("input[name='nom_name[]']").map(function(){return $(this).val();}).get();
		if($.inArray($("#nom_name").val(),nomNames) >= 0){
			$(".nom_name").html("Name already added.");
		}else{ */
			//Get the reference of the Table's TBODY element.
			$("#empEdutbl").dataTable().fnDestroy();
			var tblName = "empEdutbl";
			
			var tBody = $("#"+tblName+" > TBODY")[0];
			
			//Add Row.
			row = tBody.insertRow(-1);
			
			//Add index cell
			var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
			var cell = $(row.insertCell(-1));
			cell.html(countRow);
			
			cell = $(row.insertCell(-1));
			cell.html($("#course").val() + '<input type="hidden" name="course[]" value="'+$("#course").val()+'">');
			
			cell = $(row.insertCell(-1));
			cell.html($("#university").val() + '<input type="hidden" name="university[]" value="'+$("#university").val()+'"><input type="hidden" name="trans_id[]" value="">');

			cell = $(row.insertCell(-1));
			cell.html($("#passing_year").val() + '<input type="hidden" name="passing_year[]" value="'+$("#passing_year").val()+'">');
				
			cell = $(row.insertCell(-1));
			cell.html($("#grade").val() + '<input type="hidden" name="grade[]" value="'+$("#grade").val()+'">');

			//Add Button cell.
			cell = $(row.insertCell(-1));
			var btnRemove = $('<button><i class="ti-trash"></i></button>');
			btnRemove.attr("type", "button");
			btnRemove.attr("onclick", "Remove(this);");
			btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
			cell.append(btnRemove);
			cell.attr("class","text-center");
		/* } */
	}
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	$("#empEdutbl").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#empEdutbl")[0];
	table.deleteRow(row[0].rowIndex);
	$('#empEdutbl tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};
</script>