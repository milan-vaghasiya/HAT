$(document).ready(function () {
	calculateCRDR();
	$(document).on('change keyup', '#item_id', function () {
		$("#item_name").val($('#item_idc').val());
	});

	$(document).on('click', '.saveItem', function () {

		var fd = $('#invoiceItemForm').serializeArray();
		var formData = {};
		$.each(fd, function (i, v) {
			if (v.name != "batch_number[]" && v.name != "location[]" && v.name != "batch_quantity[]") {
				formData[v.name] = v.value;
			}
		});
		$(".item_id").html("");
		$(".qty").html("");
		$(".unit_id").html("");
		if (formData.item_id == "") {
			$(".item_id").html("Ledger is required.");
		} else {
			var item_ids = $("input[name='item_id[]']").map(function () { return $(this).val(); }).get();
			if ($.inArray(formData.item_id, item_ids) >= 0 && formData.row_index == "") {
				$(".item_id").html("Ledger already added.");
			} else {
				if (formData.cr_dr == "" || formData.price == "" || formData.price == "0") {
					if (formData.cr_dr == "") {
						$(".cr_dr").html("CR DR is required.");
					}
					if (formData.price == "" || formData.price == "0") {
						$(".price").html("Amount is required.");
					}
				} else {
					var amount = formData.price;
					formData.credit_amount = (formData.cr_dr == 'CR') ? amount : 0;
					formData.debit_amount = (formData.cr_dr == 'DR') ? amount : 0;
					AddRow(formData);
					$(".error").html('');
					$('#invoiceItemForm')[0].reset();
					$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
					if ($(this).data('fn') == "save") {
						$("#item_idc").focus();
					} else if ($(this).data('fn') == "save_close") {
						$("#itemModel").modal('hide');
					}
				}
			}
		}
	});

	$(document).on('click', '.add-item', function () {
		$("#row_index").val("");
		$(".error").html('');
		$("#itemModel").modal();
		$("#item_id").comboSelect();
		$(".btn-close").show();
		$(".btn-save").show();
	});


	$(document).on('click', '.btn-close', function () {
		$('#invoiceItemForm')[0].reset();
		//$("#item_id").comboSelect();
		$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
		$("#invoiceItemForm .error").html("");
	});
	
});



function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#invoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#invoiceItems tbody tr td:nth-child(1)').each(function (idx, ele) {
		ele.textContent = idx + 1;
	});
	var countTR = $('#invoiceItems tbody tr:last').index() + 1;
	if (countTR == 0) {
		$("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');		
	}

	calculateCRDR();
};


function Edit(data, button) {
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
	$(".btn-close").hide();
	$(".btn-save").hide();
	var fnm = "";
	$.each(data, function (key, value) { $("#" + key).val(value); });
	$("#row_index").val(row_index);
	$("#item_id").comboSelect();
	//Remove(button);
}



function saveJournalEntry(formId) {

	var total_dr_amt = $("#total_dr_amount").html();
	var total_cr_amt = $("#total_cr_amount").html();
	$(".total_cr_dr_amt").html("");
	console.log(total_cr_amt);
	console.log(total_dr_amt);
	if (total_dr_amt != total_cr_amt || total_cr_amt == 0 || total_dr_amt == 0) {
		$(".total_cr_dr_amt").html("Please Equal to CR. Or DR. Amount");
	}
	else {
		var fd = $('#' + formId)[0];
		var formData = new FormData(fd);
		$.ajax({
			url: base_url + controller + '/saveJournalEntry',
			data: formData,
			processData: false,
			contentType: false,
			type: "POST",
			dataType: "json",
		}).done(function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) {
					$("." + key).html(value);
				});
			} else if (data.status == 1) {
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				window.location = data.url;
			} else {
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		});
	}

}

function AddRow(data) {
	$('table#invoiceItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "invoiceItems";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	if (data.row_index != "") {
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#" + tblName + " tbody tr:eq(" + trRow + ")").remove();
	}
	var ind = (data.row_index == "") ? -1 : data.row_index;
	row = tBody.insertRow(ind);

	//Add index cell
	var countRow = (data.row_index == "") ? ($('#' + tblName + ' tbody tr:last').index() + 1) : (parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	var itemIdInput = $("<input/>", { type: "hidden", name: "item_id[]", value: data.item_id });
	var itemNameInput = $("<input/>", { type: "hidden", name: "item_name[]", value: data.item_name });
	var priceInput = $("<input/>", { type: "hidden", name: "price[]", value: data.price });
	var transIdInput = $("<input/>", { type: "hidden", name: "trans_id[]", value: data.trans_id });


	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(priceInput);
	cell.append(transIdInput);
	var crDrInput = $("<input/>", { type: "hidden", name: "cr_dr[]", value: data.cr_dr });
	var creditInput = $("<input/>", { type: "hidden", name: "credit_amount[]", value: data.credit_amount });
	var priceErrorDiv = $("<div></div>", { class: "error price" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.credit_amount);
	cell.append(creditInput);
	cell.append(crDrInput);
	cell.append(priceErrorDiv);

	var debitInput = $("<input/>", { type: "hidden", name: "debit_amount[]", value: data.debit_amount });
	var priceErrorDiv = $("<div></div>", { class: "error price" + countRow });
	cell = $(row.insertCell(-1));
	cell.html(data.debit_amount);
	cell.append(debitInput);
	cell.append(priceErrorDiv);

	var itemRemarkInput = $("<input/>", { type: "hidden", name: "item_remark[]", value: data.item_remark });
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);

	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("style", "margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
	btnEdit.attr("type", "button");
	btnEdit.attr("onclick", "Edit(" + JSON.stringify(data) + ",this);");
	btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

	cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class", "text-center");
	cell.attr("style", "width:10%;");


	calculateCRDR();
};

function calculateCRDR() {
	var creditAmountArray = $("input[name='credit_amount[]']").map(function () { return $(this).val(); }).get();
	var total_cr_amount = 0;
	$.each(creditAmountArray, function () { total_cr_amount += parseFloat(this) || 0; });

	var debitAmountArray = $("input[name='debit_amount[]']").map(function () { return $(this).val(); }).get();
	var total_dr_amount = 0;
	$.each(debitAmountArray, function () { total_dr_amount += parseFloat(this) || 0; });


	$("#total_cr_amount").html(total_cr_amount.toFixed(2));
	$("#total_dr_amount").html(total_dr_amount.toFixed(2));
}
