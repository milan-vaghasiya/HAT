<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="packing_id" id="packing_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->packing_no))?$dataRow->packing_no:""?>">
            <input type="hidden" name="trans_prefix" value="<?=(!empty($dataRow->packing_prefix))?$dataRow->packing_prefix:""?>">
            <input type="hidden" name="trans_number" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:""?>">

            <div class="col-md-3 form-group">
                <label for="req_date">Request Date </label>
                <input type="date" name="req_date" id="req_date" class="form-control " value="<?=date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="item_id">Item Name </label>
                <select name="item_id" id="item_id" class="form-control single-select itemList req">
                    <option value="" >Selec Item Name</option>
                    <?php
                        if(!empty($itemData)):
                            foreach($itemData as $row):
                                echo '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
                            endforeach;
                        endif
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" placeholder="Item Stock Qty." class="form-control" value="" readonly />
            </div>

            <div class="col-md-6 form-group">
                <label for="trans_child_id">Sales Order</label>
                <select name="trans_child_id" id="trans_child_id" class="form-control single-select req">
                    <option value="">Select Sales Order</option>
                </select>
                <input type="hidden" name="trans_main_id" id="trans_main_id" value=""/>
            </div>
            <div class="col-md-3 form-group">
                <label for="request_qty"> Qty</label>
                <input type="text" name="request_qty" id="request_qty" class="form-control floatOnly req" value="" />    
            </div>
            <div class="col-md-3 form-group">
                <label for="delivery_date">Delivery Date </label>
                <input type="date" id="delivery_date" name="delivery_date" class="form-control req" value="<?=date("Y-m-d")?>" />
            </div>
            <div class="col-md-10 form-group">
                <label for="remark"> Remark</label>
                <input type="text" name="remark" class="form-control " value="" />    
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary mt-30" onclick="AddRow();"><i class="fas fa-plus"></i> Add</button>
            </div>
            <hr style="width:100%;">
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table id="pReqtbl" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:15%;">Item Name</th>
                                <th style="width:15%;">Sales Order</th>
                                <th style="width:10%;">Qty</th>
                                <th style="width:5%;">Delivery Date</th>
                                <th class="text-center" style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                        <?php
                        $i=1;
                        if (!empty($dataRow->itemData)) :
                            foreach ($dataRow->itemData as $row) :
                            ?>
                                <tr>
                                    <td style="width:5%;">
                                        <?= $i++ ?>
                                    </td>
                                    <td>
                                        <?=htmlentities($row->item_name)?>
                                        <input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
                                        <input type="hidden" name="item_name[]" value="<?= htmlentities($row->item_name) ?>">
                                        <input type="hidden" name="item_code[]" value="<?= $row->item_code ?>">
                                        <input type="hidden" name="item_alias[]" value="<?= $row->item_alias ?>">
                                        <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
                                    </td>
                                    <td>
                                        <?= getPrefixNumber($row->trans_prefix,$row->trans_no)  ?>
                                        <input type="hidden" name="trans_child_id[]" value="<?= $row->trans_child_id ?>">
                                        <input type="hidden" name="trans_main_id[]" value="<?= $row->trans_main_id ?>">
                                    </td>
                                    <td>
                                        <?= $row->request_qty ?>
                                        <input type="hidden" name="request_qty[]" value="<?= $row->request_qty ?>">
                                    </td>
                                    <td>
                                        <?= $row->delivery_date ?>
                                        <input type="hidden" name="delivery_date[]" value="<?= $row->delivery_date ?>">
                                    </td>
									<td class="text-center">
                                        <button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>  

<script>
$(document).ready(function(){
    $(document).on('change',"#trans_child_id",function(){ 
        var delivery_date = $("#trans_child_id :selected").data('delivery_date');
        if(delivery_date =='' || delivery_date == null){
            var today = new Date().toISOString().split('T')[0];
            $("#delivery_date").val(today);
        }else{
            $("#delivery_date").val(delivery_date);
        }
        var trans_main_id = $("#trans_child_id :selected").data('trans_main_id');
        $("#trans_main_id").val(trans_main_id);
    });

    $(document).on('change','#item_id',function(){
        var item_id = $(this).val();
        $("#stock_qty").val("");
        $.ajax({
            url : base_url + controller + '/getSOList',
            type : 'post',
            data:{item_id:item_id},
            dataType:'json',
            success:function(data){
                $("#trans_child_id").html("");
                $("#trans_child_id").html(data.options);
                $("#trans_child_id").comboSelect();
                $("#stock_qty").val(data.stock_qty);
            }
        });
    });
});

function AddRow() {  

    var id = $("#id").val();
    var item_id = $("#item_id").val();
    var item_name = $("#item_id option:selected").text();
    var trans_no = $("#trans_child_id option:selected").text();
    var trans_child_id = $("#trans_child_id").val();
    var trans_main_id = $("#trans_main_id").val();
    var request_qty = $("#request_qty").val();
    var delivery_date = $("#delivery_date").val();
    var item_code = $("#trans_child_id :selected").data('item_code');
    var item_alias = $("#trans_child_id :selected").data('item_alias');

    $(".item_id").html("");
    $(".trans_child_id").html("");
    $(".request_qty").html("");
    $(".delivery_date").html("");
	var IsValid = 1;
    if (item_id == "") {
        $(".item_id").html("Item is required."); IsValid = 0;
    }
    if (request_qty == "" || request_qty == "0" || request_qty == "0.000") {
        $(".request_qty").html("Qty. is required."); IsValid = 0;
    }
    if(IsValid) {
        var itemIds = $("input[name='trans_child_id[]']").map(function(){return $(this).val();}).get();
        if ($.inArray(trans_child_id,itemIds) >= 0) {
            $(".trans_child_id").html("Item already added.");
        }else {
            var data = {
                id: id,								
                item_id: item_id,
                item_name: item_name,
                trans_child_id: trans_child_id,
                trans_main_id:trans_main_id,
                request_qty: request_qty,
                delivery_date: delivery_date,
                trans_no:trans_no,
                item_code:item_code,
                item_alias:item_alias,
                trans_id: ''
            };
            $("#item_id").comboSelect();
            $("#trans_child_id").comboSelect();
            $(".item_id").html("");
            $(".trans_child_id").html("");
            $(".request_qty").html("");
            $(".delivery_date").html("");         
                

            $('table#pReqtbl tr#noData').remove();
            //Get the reference of the Table's TBODY element.
            var tblName = "pReqtbl";

            var tBody = $("#" + tblName + " > TBODY")[0];

            //Add Row.
            row = tBody.insertRow(-1);

            //Add index cell
            var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
            var cell = $(row.insertCell(-1));
            cell.html(countRow);
            cell.attr("style", "width:5%;");

            cell = $(row.insertCell(-1));
            cell.html(data.item_name + '<input type="hidden" name="trans_id[]" value="' + data.trans_id + '"><input type="hidden" name="item_id[]" value="' + data.item_id + '"><input type="hidden" name="item_name[]" value="'+$("#item_idc").val()+'"><input type="hidden" name="item_code[]" value="'+data.item_code+'"><input type="hidden" name="item_alias[]" value="'+data.item_alias+'">');
            
            cell = $(row.insertCell(-1));
            cell.html(data.trans_no + '<input type="hidden"  name="trans_child_id[]" value="' + data.trans_child_id + '"><input type="hidden"  name="trans_main_id[]" value="' + data.trans_main_id + '">');

            cell = $(row.insertCell(-1));
            cell.html(data.request_qty + '<input type="hidden"  name="request_qty[]" value="' + data.request_qty + '">');

            cell = $(row.insertCell(-1));
            cell.html(data.delivery_date + '<input type="hidden"  name="delivery_date[]" value="' + data.delivery_date + '">');

            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="ti-trash"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("style", "margin-left:4px;");
            btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            cell.attr("style", "width:10%;");
        }
    }
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#pReqtbl")[0];
	table.deleteRow(row[0].rowIndex);
	$('#pReqtbl tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#pReqtbl tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
    }
};
</script>