<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
            <div class="col-md-4 form-group">
                <label for="nom_name">Name</label>
                <input type="text" id="nom_name" class="form-control req" placeholder="Name" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="nom_gender">Gender</label>
                <select id="nom_gender" class="form-control single-select">
                    <?php
                        foreach ($genderData as $value) :
                            echo '<option value="' . $value . '">' . $value . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="nom_relation">Relation</label>
                <input type="text" id="nom_relation" class="form-control req" placeholder="Relation" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="nom_dob">Date of birth</label>
                <input type="date" id="nom_dob" class="form-control req" placeholder="mm-dd-yyyy" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="nom_proportion">Proportion</label>
                <input type="text" id="nom_proportion" class="form-control" placeholder="Proportion" value="" />
            </div>
            <div class="col-md-4 form-group">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30" onclick="AddRow();"><i class="fa fa-plus"></i> Add Nomination</button>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table id="empNomtbl" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Relation</th>
                            <th>Date of birth</th>
                            <th>Proportion</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="empNomBody">
                        <?php
                        if (!empty($nomData)) :
                                $i = 1;
                                foreach ($nomData as $row) :
                                    echo '<tr>
                                                <td>' . $i++ . '</td>
                                                <td>
                                                    ' . $row->nom_name . '
                                                    <input type="hidden" name="nom_name[]" value="' . $row->nom_name . '">
                                                 </td>
                                                <td>
                                                    ' . $row->nom_gender . '
                                                    <input type="hidden" name="nom_gender[]" value="' . $row->nom_gender . '">
                                                    <input type="hidden" name="trans_id[]" value="' . $row->id . '">
                                                </td>
                                                <td>
                                                    ' . $row->nom_relation . '
                                                    <input type="hidden" name="nom_relation[]" value="' . $row->nom_relation . '">
                                                </td>
                                                <td>
                                                    ' . $row->nom_dob . '
                                                    <input type="hidden" name="nom_dob[]" value="' . $row->nom_dob . '">
                                                </td>
                                                <td>
                                                    ' . $row->nom_proportion . '
                                                    <input type="hidden" name="nom_proportion[]" value="' . $row->nom_proportion . '">
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
	if($("#nom_name").val() == "" || $("#nom_dob").val() == "" || $("#nom_relation").val() == ""){
		if($("#nom_name").val() == ""){
			$(".nom_name").html("Name is required.");
		}
		if($("#nom_dob").val() == ""){
			$(".nom_dob").html("Date of birth is required.");
		}
		if($("#nom_relation").val() == "" || $("#nom_relation").val() == 0){
			$(".nom_relation").html("Relation is required.");
		}
	}else{
		/* var nomNames = $("input[name='nom_name[]']").map(function(){return $(this).val();}).get();
		if($.inArray($("#nom_name").val(),nomNames) >= 0){
			$(".nom_name").html("Name already added.");
		}else{ */
			//Get the reference of the Table's TBODY element.
			$("#empNomtbl").dataTable().fnDestroy();
			var tblName = "empNomtbl";
			
			var tBody = $("#"+tblName+" > TBODY")[0];
			
			//Add Row.
			row = tBody.insertRow(-1);
			
			//Add index cell
			var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
			var cell = $(row.insertCell(-1));
			cell.html(countRow);
			
			cell = $(row.insertCell(-1));
			cell.html($("#nom_name").val() + '<input type="hidden" name="nom_name[]" value="'+$("#nom_name").val()+'">');
			
			cell = $(row.insertCell(-1));
			cell.html($("#nom_gender").val() + '<input type="hidden" name="nom_gender[]" value="'+$("#nom_gender").val()+'"><input type="hidden" name="trans_id[]" value="">');

			cell = $(row.insertCell(-1));
			cell.html($("#nom_relation").val() + '<input type="hidden" name="nom_relation[]" value="'+$("#nom_relation").val()+'">');
				
			cell = $(row.insertCell(-1));
			cell.html($("#nom_dob").val() + '<input type="hidden" name="nom_dob[]" value="'+$("#nom_dob").val()+'">');

            cell = $(row.insertCell(-1));
			cell.html($("#nom_proportion").val() + '<input type="hidden" name="nom_proportion[]" value="'+$("#nom_proportion").val()+'">');

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
	$("#empNomtbl").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#empNomtbl")[0];
	table.deleteRow(row[0].rowIndex);
	$('#empNomtbl tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};
</script>