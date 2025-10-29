$(document).ready(function(){
    $(document).on('change','#material_type',function(){
        var type = $(this).val();
        $.ajax({
            url: base_url + controller + '/getItemOptions',
            type:'post',
            data:{type:type},
            dataType:'json',
            success:function(data){
                $("#req_item_id").html("");
                $("#req_item_id").html(data.options);
                $("#req_item_id").comboSelect();
                $("#req_qty").val(0);
            }
        });
    });

    $(document).on('change',"#req_item_id",function(){
        $("#stock_qty").val("");$("#req_qty").val(0);
        $("#stock_qty").val($("#req_item_id :selected").data('stock'));
    });

    $(document).on('click', '.addRow', function () {
		var id = $("#id").val();
		var qty = $("#req_qty").val();
		var item_id = $("#req_item_id").val();
		var item_name = $("#req_item_id option:selected").text();

		$(".req_item_id").html("");
		$(".req_qty").html("");
		$(".stock_qty").html("");
		$('.remark').html("");
		var item_ids = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
		if ($.inArray(item_id,item_ids) >= 0) {
			$(".req_item_id").html("Item already added.");
		}
        else
        {
			if (item_id == "" || req_qty == "" || req_qty == "0" || req_qty == "0.000") {
				if (item_id == "") {
					$(".req_item_id").html("Item is required.");
				}
                if (req_qty == "" || req_qty == "0" || req_qty == "0.000") {
					$(".req_qty").html("Qty. is required.");
				}
			} else {
                var post = {
                    id: id,							
                    qty: qty,							
                    item_id: item_id,
                    item_name: item_name,
					trans_id: ''

                };
                addRow(post);
                $("#req_item_id").comboSelect();
                $(".req_item_id").html("");
                $(".req_qty").html("");
                $(".stock_qty").html("");
                $('.remark').html("");            
            }
				
				
		}		
	});

});

function addRow(data) { //console.log(data);
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
	cell.html(data.item_name + '<input type="hidden" name="trans_id[]" value="' + data.trans_id + '"><input type="hidden" name="req_item_id[]" value="' + data.item_id + '"><input type="hidden" name="req_item_name[]" value="'+$("#req_item_idc").val()+'">');

	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" class="qtyTotal" name="req_qty[]" value="' + data.qty + '">');

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
