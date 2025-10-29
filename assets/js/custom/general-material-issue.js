$(document).ready(function () {
    $("#dispatch_item_id").trigger("change");
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});

    $(document).on("change", "#dispatch_item_id", function () {
		console.log()
		var itemId = $(this).val();
		var req_id = $(this).find(":selected").data('req_id');
		
		$(".location_id").html("");
		$(".dispatch_item_id").html("");
		$("#batch_stock").val("");
		if (itemId == "") {
			if (itemId == "") { }

		} else {
			$.ajax({
				url: base_url + controller + '/getItemLocationList',
				type: 'post',
				data: {
					item_id: itemId
				},
				dataType: 'json',
				success: function (data) {
					$("#location_id").html("");
					$("#location_id").html(data.options);
					$("#id").val(req_id);
					$("#location_id").select2();
					//$("#batch_no").comboSelect();
				}
			});
		}
	});

	$(document).on("change", "#location_id", function () {
		var itemId = $("#dispatch_item_id").val();
		var location_id = $(this).val();
		$(".location_id").html("");
		$(".dispatch_item_id").html("");
		$("#batch_stock").val("");

		if (itemId == "" || location_id == "") {
			if (itemId == "") {
				$(".dispatch_item_id").html("Issue Item name is required.");
			}
			if (location_id == "") {
				$(".location_id").html("Location is required.");
			}
		} else {
			$.ajax({
				url: base_url + controller + '/getBatchNo',
				type: 'post',
				data: {
					item_id: itemId,
					location_id: location_id
				},
				dataType: 'json',
				success: function (data) {
					$("#batch_no").html("");
					$("#batch_no").html(data.options);
					//$("#batch_no").comboSelect();
					$("#batch_no").select2();
				}
			});
		}
	});
	$(document).on('change', "#batch_no", function () {
		$("#batch_stock").val("");
		$("#batch_stock").val($("#batch_no :selected").data('stock'));
	});

    $(document).on('click', '.addRow', function () {
		var id = $("#id").val();
		var location_id = $("#location_id").val();
		var store_name = $("#location_id :selected").data('store_name');
		var location = $("#location_id :selected").text();
		var location_name = "[ " + store_name + " ] " + location;
		var batch_no = $("#batch_no").val();
		var stock = $("#batch_stock").val();
		var qty = $("#batch_qty").val();
		var count_item = $("#count_item").val();
		var job_card_id = $("#job_card_id").val();
		var item_id = $("#dispatch_item_id").val();
		var item_name = $("#dispatch_item_id option:selected").text();

		$(".location_id").html("");
		$(".batch_no").html("");
		$(".batch_qty").html("");
		$('.general_batch_no').html("");
		var item_ids = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
		console.log(item_ids);
		if ($.inArray(item_id,item_ids) >= 0) {
			$(".dispatch_item_id").html("Item already added.");
		}else{
			if (location_id == "" || batch_no == "" || qty == "" || qty == "0" || qty == "0.000") {
				if (location_id == "") {
					$(".location_id").html("Location is required.");
				}

				if (batch_no == "") {
					$(".batch_no").html("Batch No. is required.");
				}
				if (qty == "" || qty == "0" || qty == "0.000") {
					$(".batch_qty").html("Qty. is required.");
				}
			} else {
				var batchNos = $("input[name='batch_no[]']").map(function () {
					return $(this).val();
				}).get();
				/* if($.inArray(batch_no,batchNos) >= 0){
					$(".batch_no").html("Batch No. already added.");
				}else {  */
				if (parseFloat(qty) > parseFloat(stock)) {
					$(".batch_qty").html("Stock not avalible.");
				} else {
					var qtySum = 0;
					$(".qtyTotal").each(function () {
						qtySum += parseFloat($(this).val());
					});
					qtySum += parseFloat(qty);
					var pendingQty = $("#pending_qty").val();
					var reqQty = $("#req_qty").val();
					if (parseFloat(reqQty) != 0 && parseFloat(qtySum).toFixed(3) > parseFloat(reqQty).toFixed(3)) {
						$(".batch_qty").html("Invalid Issue qty.");
					} else {
						var item_id = $("#dispatch_item_id").val();
						var item_name = $("#dispatch_item_id option:selected").text();
						var post = {
							id: id,
							batch_no: batch_no,
							qty: qty,
							location_id: location_id,
							location_name: location_name,
							item_id: item_id,
							item_name: item_name
						};
						addRow(post);
						$("#count_item").val(parseFloat(count_item) + 1);
						if (parseFloat(reqQty) != 0) {
							$("#pending_qty").val(parseFloat(parseFloat(pendingQty) - parseFloat(qty)).toFixed(3));
						}
						$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));
						$("#batch_no").val("");
						//$("#batch_no").comboSelect();
						$("#batch_no").select2();
						$("#batch_stock").val("");
						$("#batch_qty").val("");
					}
				}
				// }
			}
		}
	});
	
});

function addRow(data) {
	$('table#issueItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "issueItems";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	row = tBody.insertRow(-1);

	//Add index cell
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="id[]" value="' + data.id + '"><input type="hidden" name="ref_id[]" value=""><input type="hidden" name="item_id[]" value="' + data.item_id + '">');

	cell = $(row.insertCell(-1));
	cell.html(data.location_name + '<input type="hidden" name="location_id[]" value="' + data.location_id + '">');


	cell = $(row.insertCell(-1));
	cell.html(data.batch_no + '<input type="hidden" name="batch_no[]" value="' + data.batch_no + '"><input type="hidden" name="trans_id[]" value="' + data.id + '" />');

	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" class="qtyTotal" name="batch_qty[]" value="' + data.qty + '">');

	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this,'" + data.qty + "');");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");
}

function Remove(button, qty) {
	var qtySum = 0;
	$(".qtyTotal").each(function () {
		qtySum += parseFloat($(this).val());
	});
	qtySum -= parseFloat(qty);
	var pendingQty = $("#pending_qty").val();
	var reqQty = $("#req_qty").val();
	if (parseFloat(reqQty) != 0) {
		$("#pending_qty").val(parseFloat(parseFloat(pendingQty) + parseFloat(qty)).toFixed(3));
	}
	$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));
	$("#count_item").val(parseFloat($("#count_item").val()) - 1);

	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#issueItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#issueItems tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#issueItems tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}

	
};