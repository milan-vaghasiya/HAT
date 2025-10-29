<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="material_type" id="material_type" value="<?=(!empty($dataRow->material_type))?$dataRow->material_type:""?>" />
            <input type="hidden" name="job_card_id" id="job_card_id" value="0" />
            <input type="hidden" name="prtype" id="prtype" value="1" />

            <div class="col-md-4 form-group">
                <label for="req_date">Request Date</label>
                <input type="date" name="req_date" id="req_date" class="form-control req" min="<?=$startYearDate?>" max="<?=$maxDate?>" value="<?=$maxDate?>">
            </div>
            <div class="col-md-8 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
            <hr style="width:100%;">
            <div class="col-md-6 form-group req">
                <label for="req_item_id">Item Name</label>
                <select name="req_item_id" id="req_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php 
                        foreach($itemData as $row):
                            echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">['.$row->item_code.'] '.$row->item_name.' </option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="number" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow))?(($dataRow->req_qty != "0.000")?$dataRow->req_qty:$dataRow->req_qty):""?>">                
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right mt-30" onclick="AddRow();"><i class="fa fa-check"></i> Add</button>
            </div>
        </div>
        <hr style="width:100%;">
        <div class="row">
            <div class="table-responsive">
                <table id="requesttbl" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Request Item</th>
                            <th>Request Qty.</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="requestBody">
                        <?php
                        /* if (!empty($eduData)) :
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
                            endif; */
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('change','#req_item_id', function(){
        $('#material_type').val($(this).find(":selected").data('item_type'));
    });
});

function AddRow() {
    $(".error").html(""); var isValid = 1;
    if($("#req_item_id").val() == ""){
        $(".req_item_id").html("Item Name is required."); isValid = 0;
    }
    if($("#req_qty").val() == ""){
        $(".req_qty").html("Request Qty. is required."); isValid = 0;
    }
    
    if(isValid){

			//Get the reference of the Table's TBODY element.
			$("#requesttbl").dataTable().fnDestroy();
			var tblName = "requesttbl";
			var tBody = $("#"+tblName+" > TBODY")[0];
			
			//Add Row.
			row = tBody.insertRow(-1);
			
			//Add index cell
			var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
			var cell = $(row.insertCell(-1));
			cell.html(countRow);
			
            cell = $(row.insertCell(-1));
			cell.html($("#req_item_idc").val() + '<input type="hidden" name="req_item_id[]" value="'+$("#req_item_id").val()+'"><input type="hidden" name="req_item_name[]" value="'+$("#req_item_idc").val()+'">');

			cell = $(row.insertCell(-1));
			cell.html($("#req_qty").val() + '<input type="hidden" name="req_qty[]" value="'+$("#req_qty").val()+'">');

			//Add Button cell.
			cell = $(row.insertCell(-1));
			var btnRemove = $('<button><i class="ti-trash"></i></button>');
			btnRemove.attr("type", "button");
			btnRemove.attr("onclick", "Remove(this);");
			btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
			cell.append(btnRemove);
			cell.attr("class","text-center");
            $("#req_item_id").val('');
            $("#req_item_idc").val('');
            $("#req_qty").val('');
	}
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	$("#requesttbl").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#requesttbl")[0];
	table.deleteRow(row[0].rowIndex);
	$('#requesttbl tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};
</script>