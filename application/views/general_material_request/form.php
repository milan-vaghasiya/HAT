<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="req_no" id="req_no" value="<?=(!empty($dataRow->req_no))?$dataRow->req_no:$req_no?>" />
            <input type="hidden" name="issue_type" id="issue_type" value="<?=(!empty($dataRow->issue_type))?$dataRow->issue_type:2?>" />
            <input type="hidden" name="material_type" id="material_type" value="2" />          


            <div class="col-md-6 form-group">
                <label for="req_item_id">Request Item Name (Consumable)</label>
                <select name="req_item_id" id="req_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php    
                        $stock = "";                  
                        foreach($itemData as $row):
                            $selected = "";
                            if(empty($dataRow)):
                                if($row->item_type == 2):
                                    echo '<option value="'.$row->id.'" data-stock="'.floatVal($row->qty).' '.$row->unit_name.'" '.$selected.'>'.$row->item_name.'</option>';  
                                endif;  
                            else:
                                $selected = ($dataRow->req_item_id == $row->id)?"selected":"";
                                $stock =  ($dataRow->req_item_id == $row->id)?floatVal($row->qty).' '.$row->unit_name:"";  
                                if($row->item_type == $dataRow->material_type):             
									echo '<option value="'.$row->id.'" data-stock="'.floatVal($row->qty).' '.$row->unit_name.'" '.$selected.'>'.$row->item_name.'</option>';     
								endif;                       
                            endif;                          
                        endforeach;
                    ?>
                </select>      
            </div>
            <div class="col-md-6 form-group">
                <label for="req_date">Request Date</label>
                <input type="date" name="req_date" id="req_date" class="form-control req" value="<?=(!empty($dataRow->id))?$dataRow->req_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" >
            </div>
            
            
            
            <div class="col-md-4 form-group">
                <input type="hidden" name="process_id" id="process_id" value="" />
                <label for="dispatch_date">Required Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control req" value="<?=(!empty($dataRow->dispatch_date))?$dataRow->dispatch_date:date("Y-m-d")?>" >  
            </div>
            
           
            <div class="col-md-4 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" placeholder="Item Stock Qty." class="form-control" value="" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="number" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow->req_qty))?floatVal($dataRow->req_qty):""?>">
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_item"></div>
                <div class="table-responsive ">
                    <table id="reqItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <tr id="noData">
                                <td class="text-center" colspan="6">No Data Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    if (!empty($dataRow->trans_data)) :
                        if (!empty($dataRow->trans_data)) :
                            foreach ($dataRow->trans_data as $row) :
                                echo '<script>var postData={id:"",batch_no:"' . $row->batch_no . '",qty:"' . abs($row->qty) . '",location_id:"' . $row->location_id . '",location_name:"[ ' . $row->store_name . ' ] ' . $row->location . '",item_id:"' . $row->item_id . '",item_name:"' . $row->item_name . '"}; addRow(postData); $("#count_item").val(parseFloat($("#count_item").val()) + 1);</script>';
                            endforeach;
                        endif;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){    

    $(document).on("change", "#req_item_id", function () {
		
		var itemId = $(this).val();
        $("#stock_qty").val("");$("#req_qty").val(0);
		
		if (itemId == "") {
			if (itemId == "") { }

		} else {
			$.ajax({
				url: base_url + controller + '/getItemStock',
				type: 'post',
				data: {
					item_id: itemId
				},
				dataType: 'json',
				success: function (data) {
                    //console.log(data);
					$("#stock_qty").html("");
					$("#stock_qty").val(data);
					
				}
			});
		}
	});

    $(document).on('click', '.addRow', function () {
		
		var stock = $("#stock_qty").val();
		var req_qty = $("#req_qty").val();
		
		if (req_qty == "" || req_qty == "0" || req_qty == "0.000") {
			$(".req_qty").html("Qty. is required.");
			
		} else 
        {
            var reqQty = $("#req_qty").val();
            var item_id = $("#req_item_id").val();
            var item_name = $("#req_item_id option:selected").text();
            var post = {
                id: "",
                req_qty: req_qty,
                item_id: item_id,
                item_name: item_name
            };
            addRow(post);                
            $("#req_item_id").comboSelect();  
		}
	});

});

function addRow(data) {
	$('table#reqItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "reqItems";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	row = tBody.insertRow(-1);

	//Add index cell
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="item_id[]" value="' + data.item_id + '">');


	cell = $(row.insertCell(-1));
	cell.html(data.req_qty + '<input type="hidden" name="req_qty[]" value="' + data.req_qty + '">');

	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");

}

function Remove(button) {

	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#reqItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#reqItems tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#reqItems tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}

	
};
</script>