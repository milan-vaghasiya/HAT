$(document).ready(function(){
	
	var gstType = $("#gst_type").val();
	if(gstType == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(gstType == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	$(document).on("change","#gst_type",function(){
		var gstType = $(this).val();
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();
		}
		claculateColumn();
	});
	
	var freightAmt = ($("#freight").val() == "")?"0.00":parseFloat($("#freight").val()).toFixed(2);
	$("#freight_amt").val(freightAmt);
	$(".freight_amt").html(freightAmt);
	
	$(document).on('keyup click',"#freight",function(){
		var freightAmt = ($(this).val() == "")?"0.00":parseFloat($(this).val()).toFixed(2);
		$("#freight_amt").val(freightAmt);
		$(".freight_amt").html(freightAmt);
		claculateColumn();
	});
	
	$(document).on("change","#item_id",function(){
		var itemId = $(this).val();
		$(".item_id").html("");
		if(itemId == ""){
			$(".item_id").html("Please Select Item.");
		}else{
			$.ajax({
				url:base_url + controller + '/getItemData',
				type:'post',
				data:{itemId:itemId},
				dataType:'json',
				success:function(data){
					$("#unit_id").val(0);
					$("#unit_id").val(data.unit_id);
					$("#hsnCode").val("");
					$("#hsnCode").val(data.hsn_code);
					$("#itemGst").val("");
					$("#itemGst").val(data.gst_per);
					$("#unitName").val("");
					$("#unitName").val(data.unit_name);
					$("#price").val("");
					$("#price").val(data.rate);
				}
			});
		}
	});
	
	$(document).on("click",".AddItem",function(){
		var itemId = $("#item_id").val();
		var itemName = $("#item_idc").val();
		var hsnCode = $("#hsnCode").val();
		var itemGst = $("#itemGst").val();
		var unitId = $("#unit_id").val();
		var unitName = $("#unitName").val();
		var qty = $("#qty").val();
		var price = $("#price").val();
		var discPer = $("#disc_per").val();
		var gstType = $("#gst_type").val();
		var item_remark = $("#item_remark").val();

		$(".item_id").html("");
		$(".qty").html("");
		$(".rate").html("");
		if(itemId == ""){
			$(".item_id").html("Please select Item.");
		}else{
			var itemIds = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(itemId,itemIds) >= 0) {
				$(".item_id").html("Item already added.");
			}else {
				if(qty == "" || qty == "0" || price == "" || price == "0"){
					if(qty == "" || qty == "0"){
						$(".qty").html("Qty is required.");
					}
					if(price == "" || price == "0"){
						$(".rate").html("Price is required.");
					}
				}else{
					var amount = 0;var total = 0;var discAmt = 0;var igstAmt = 0;
					var cgstAmt = 0;var sgstAmt = 0;var netAmount = 0;var cgstPer = 0;var sgstPer = 0; var igstPer = 0;
					if(discPer == "" && discPer == "0"){
						amount = parseFloat(parseFloat(qty) * parseFloat(price)).toFixed(2);
					}else{
						total = parseFloat(parseFloat(qty) * parseFloat(price)).toFixed(2);
						discAmt = parseFloat((total * parseFloat(discPer))/100).toFixed(2);
						amount = parseFloat(total - discAmt).toFixed(2);
					}
					
					cgstPer = parseFloat(parseFloat(itemGst)/2).toFixed(2);
					sgstPer = parseFloat(parseFloat(itemGst)/2).toFixed(2);
					
					cgstAmt = parseFloat((cgstPer * amount )/100).toFixed(2);
					sgstAmt = parseFloat((sgstPer * amount )/100).toFixed(2);
					
					igstPer = parseFloat(itemGst).toFixed(2);
					igstAmt = parseFloat((igstPer * amount )/100).toFixed(2);
					
					netAmount = parseFloat(parseFloat(amount) + parseFloat(igstAmt)).toFixed(2);
					
					
					var data = {itemId:itemId,itemName:itemName,hsnCode:hsnCode,itemGst:itemGst,unitId:unitId,unitName:unitName,qty:parseFloat(qty).toFixed(2),price:price,discPer:discPer,gstType:gstType,amount:amount,discAmt:discAmt,igstAmt:igstAmt,cgstAmt:cgstAmt,sgstAmt:sgstAmt,netAmount:netAmount,cgstPer:cgstPer,sgstPer:sgstPer,igstPer:igstPer,item_remark:item_remark};
					
					AddRow(data);
					
					$("#item_id").val("");
					$("#item_idc").val("");
					$("#hsnCode").val("");
					$("#itemGst").val("");
					$("#unitName").val("");
					$("#qty").val("0");
					$("#price").val("0");
					$("#disc_per").val("0");
					$("#item_remark").val("");
					$("#item_id").focus();
				}
			}
		}
	});
	
});

function AddRow(data) {
	$('table#purchaseItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "purchaseItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	cell = $(row.insertCell(-1));
	cell.html(data.itemName + '<input type="hidden" name="item_id[]" value="'+data.itemId+'"><input type="hidden" name="order_trans_id[]" value="" />');

	cell = $(row.insertCell(-1));
	cell.html(data.hsnCode + '<input type="hidden" name="hsn_code[]" value="'+data.hsnCode+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" name="qty[]" value="'+data.qty+'"><div class="error qty'+countRow+'"></div>');
	
	cell = $(row.insertCell(-1));
	cell.html(data.unitName + '<input type="hidden" name="unit_id[]" value="'+data.unitId+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.price + '<input type="hidden" name="srate[]" value="'+data.price+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.cgstAmt+ '(' + data.cgstPer + '%) <input type="hidden" name="cgst_amt[]" value="'+data.cgstAmt+'"><input type="hidden" name="cgst[]" value="'+data.cgstPer+'">');
	cell.attr("class","cgstCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.sgstAmt+ '(' + data.sgstPer + '%) <input type="hidden" name="sgst_amt[]" value="'+data.sgstAmt+'"><input type="hidden" name="sgst[]" value="'+data.cgstPer+'">');
	cell.attr("class","sgstCol");

	cell = $(row.insertCell(-1));
	cell.html(data.igstAmt + '(' + data.itemGst + '%) <input type="hidden" name="igst_amt[]" value="'+data.igstAmt+'"><input type="hidden" name="igst[]" value="'+data.igstPer+'">');
	cell.attr("class","igstCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.discAmt + '(' + data.discPer + '%) <input type="hidden" name="disc_per[]" value="'+data.discPer+'"><input type="hidden" name="disc_amt[]" value="'+data.discAmt+'">');
	
	cell = $(row.insertCell(-1));
	cell.html(data.amount + '<input type="hidden" name="amount[]" value="'+data.amount+'">');
	cell.attr("class","amountCol");
	
	cell = $(row.insertCell(-1));
	cell.html(data.netAmount + '<input type="hidden" name="net_amount[]" value="'+data.netAmount+'">');
	cell.attr("class","netAmtCol");

	cell = $(row.insertCell(-1));
	cell.html(data.item_remark + '<input type="hidden" name="item_remark[]" value="'+data.item_remark+'">');
	cell.attr("style","width:15%;");
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="feather icon-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
	if(data.gstType == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(data.gstType == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	claculateColumn();
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#purchaseItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#purchaseItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#purchaseItems tbody tr:last').index() + 1;
	if(countTR == 0){
		if($("#gst_type").val() == 1){
			$("#tempItem").html('<tr id="noData"><td colspan="12" align="center">No data available in table</td></tr>');
		}else if($("#gst_type").val() == 2){
			$("#tempItem").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
		}else{
			$("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
		}
	}
	
	claculateColumn();
};

function claculateColumn()
{
	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	
	var netAmtArray = $("input[name='net_amount[]']").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
			
	var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();;
    var igstAmtSum = 0;
	$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
	$('#igst_amt_total').val("");
	$('#igst_amt_total').val(igstAmtSum.toFixed(2));
	
	var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();;
    var cgstAmtSum = 0;
	$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
	$('#cgst_amt_total').val("");
	$('#cgst_amt_total').val(cgstAmtSum.toFixed(2));
	
	var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();;
    var sgstAmtSum = 0;
	$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
	$('#sgst_amt_total').val("");
	$('#sgst_amt_total').val(sgstAmtSum.toFixed(2));
	
	var discAmtArr = $("input[name='disc_amt[]']").map(function(){return $(this).val();}).get();;
    var discAmtSum = 0;
	$.each(discAmtArr,function(){discAmtSum += parseFloat(this) || 0;});
	$('#disc_amt_total').val("");
	$('#disc_amt_total').val(discAmtSum.toFixed(2));
	
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum + parseFloat($("#freight_amt").val())).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(amountSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}else{
		var amount = parseFloat(netAmtSum + parseFloat($("#freight_amt").val())).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(netAmtSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}
}

function commanSave1(frm)
{
	var fd = new FormData(frm);
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		processData: false,
		contentType: false,
		dataType:"json",
		success:function(data)
		{
			if(data.status===0)
			{
				$(".error").html("");
				$.each( data.message, function( key, value ) {
					$("."+key).html(value);
				});
			}
			else
			{
				swal({
					title: popupTitle,
					text: data.message,
					icon: "success",
					buttons: {
					  confirm: {
						text: "OK",
						value: true,
						visible: true,
						className: "",
						closeModal: true
					  }
					}
				}).then((willDelete) => {
					window.location = data.url;
				});
			}
		}
	});
}