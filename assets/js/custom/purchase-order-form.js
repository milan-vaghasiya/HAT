$(document).ready(function(){

	var numberOfChecked = $('.termCheck:checkbox:checked').length;
	$("#termsCounter").html(numberOfChecked);
	$(document).on("click",".termCheck",function(){
        var id = $(this).data('rowid');
		var numberOfChecked = $('.termCheck:checkbox:checked').length;
		$("#termsCounter").html(numberOfChecked);
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#term_id"+id).attr('disabled','disabled');
            $("#term_title"+id).attr('disabled','disabled');
            $("#condition"+id).attr('disabled','disabled');
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#term_id"+id).removeAttr('disabled');
            $("#term_title"+id).removeAttr('disabled');
            $("#condition"+id).removeAttr('disabled');
        }
    });
    
    $(document).on("change keyup","#party_id",function(){
		var gst_type= 1;
		if($(this).val() != ""){
			var partyData = $(this).find(":selected").data('row');	
			var gstin = partyData.gstin;
			
			var stateCode = "";
			if(gstin != ""){
				stateCode = gstin.substr(0, 2);
				if(stateCode == 24 || stateCode == "24"){ gst_type = 1; }else{ gst_type = 2; }
			}
			$("#gst_type").val(gst_type);
			$("#party_name").val($("#party_idc").val());
			$("#gst_type").trigger('change');
		}
	});
    
	
// 	$(document).on('change keyup','#fgitem_id',function(){$("#fgitem_name").val($('#fgitem_id :selected').text());});
//  $(document).on('keyup','#fgitem_idc',function(){ $("#fgitem_name").val($(this).val());});

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

    $(document).on("change","#item_id",function(){
		var itemId = $(this).val();
		$(".item_id").html("");
		if(itemId == ""){
			$(".item_id").html("Please Select Item.");
		}else{
			var itemData = $("#item_id :selected").data('row');
			$("#item_type").val(itemData.item_type);
			$("#item_code").val(itemData.item_code);
			$("#item_name").val(itemData.item_name);
			$("#item_desc").val(itemData.description);
			$("#hsn_code").val(itemData.hsn_code);
			$("#item_gst").val(itemData.gst_per);
			$("#price").val(itemData.price);
			$("#unit_name").val(itemData.unit_name);
			$("#unit_id").val(itemData.unit_id);		
			$.ajax({
				url:base_url + controller + '/getLastPrice',
				type:'post',
				data:{item_id:itemId},
				dataType:'json',
				success:function(data){
				
					if(data!=null)
					{
					$("#price").val(data.price);
					}
				}
			});	
		}
	});

    $(document).on('change keyup','#item_id',function(){
        $("#item_name").val($('#item_idc').val());
    });

    // var freightAmt = ($("#freight").val() == "")?"0.00":parseFloat($("#freight").val()).toFixed(2);
    // var packingAmt = ($("#packing").val() == "")?"0.00":parseFloat($("#packing").val()).toFixed(2);
	// $("#freight_amt").val(freightAmt);$(".freight_amt").html(freightAmt);
	// $("#packing_amt").val(packingAmt);$(".packing_amt").html(packingAmt);
	
	$(document).on('keyup change',"#freight",function(){
		var freightAmt = ($(this).val() == "")? 0 :parseFloat($(this).val()).toFixed(2);
		var frgst = parseFloat( parseFloat(freightAmt) * 0.18).toFixed(2);
		var framt = parseFloat(parseFloat(freightAmt) +  parseFloat(frgst)).toFixed(2);
		$("#freight_amt").val(freightAmt);
		$(".freight_amt").html(framt);
		claculateColumn();
	});

	$(document).on('keyup change',"#packing",function(){
		var packingAmt = ($(this).val() == "")?"0.00":parseFloat($(this).val()).toFixed(2);
		var pckgst = parseFloat( parseFloat(packingAmt) * 0.18).toFixed(2);
		var pckamt = parseFloat(parseFloat(packingAmt) +  parseFloat(pckgst)).toFixed(2);
		$("#packing_charge").val(packingAmt);
		$(".packing_amt").html(pckamt);
		claculateColumn();
	});

    $(document).on('click','.saveItem',function(){
        var fd = $('#orderItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });
        $(".item_id").html("");
		$(".qty").html("");
		$(".price").html("");
        var Valid=1;
		if(formData.item_id == ""){
			$(".item_id").html("Item Name is required.."); Valid=0;
		}
		if(formData.item_type == "" || formData.item_type == "0"){
			$(".item_type").html("Item Category is required."); Valid=0;
		}
		if(formData.rate_unit == 2){
			if(formData.qty_kg == "" || formData.qty_kg == "0"){
				$(".qty_kg").html("Qty K.G is required."); Valid=0;
			}
		}

		if(Valid){
// 			var itemIds = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
// 			if ($.inArray(formData.item_id,itemIds) >= 0) {
// 				$(".item_id").html("Item already added.");
// 			}else {
				if(formData.qty == "" || formData.qty == "0" || formData.price == "" || formData.price == "0"){
					if(formData.qty == "" || formData.qty == "0"){
						$(".qty").html("Qty is required.");
					}
					if(formData.price == "" || formData.price == "0"){
						$(".price").html("Price is required.");
					}
				}else{
					var amount = 0;var total = 0;var disc_amt = 0;var igst_amt = 0;
					var cgst_amt = 0;var sgst_amt = 0;var net_amount = 0;var cgst_per = 0;var sgst_per = 0; var igst_per = 0;
					if(formData.disc_per == "" && formData.disc_per == "0"){
						if(formData.rate_unit == 1){ amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);}
						else{ amount = parseFloat(parseFloat(formData.qty_kg) * parseFloat(formData.price)).toFixed(2); }
					}else{
						if(formData.rate_unit == 1){ total = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2); }
						else{ total = parseFloat(parseFloat(formData.qty_kg) * parseFloat(formData.price)).toFixed(2); }
						
						disc_amt = parseFloat((total * parseFloat(formData.disc_per))/100).toFixed(2);
						amount = parseFloat(total - disc_amt).toFixed(2);
					}
					
					cgst_per = parseFloat(parseFloat(formData.item_gst)/2).toFixed(2);
					sgst_per = parseFloat(parseFloat(formData.item_gst)/2).toFixed(2);
					
					cgst_amt = parseFloat((cgst_per * amount )/100).toFixed(2);
					sgst_amt = parseFloat((sgst_per * amount )/100).toFixed(2);
					
					igst_per = parseFloat(formData.item_gst).toFixed(2);
					igst_amt = parseFloat((igst_per * amount )/100).toFixed(2);
					
					net_amount = parseFloat(parseFloat(amount) + parseFloat(igst_amt)).toFixed(2);

                    formData.gst_type = $('#gst_type').val();
					formData.qty = parseFloat(formData.qty).toFixed(2);
					formData.qty_kg = parseFloat(formData.qty_kg).toFixed(2);
					formData.cgst_per = cgst_per;
                    formData.cgst_amt = cgst_amt;
                    formData.sgst_per = sgst_per;
                    formData.sgst_amt = sgst_amt;
                    formData.igst_per = igst_per;
                    formData.igst_amt = igst_amt;
                    formData.disc_amt = disc_amt;
                    formData.amount = amount;
                    formData.net_amount = net_amount;
					
					AddRow(formData);
                    $('#orderItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_id").focus();
                        $("#item_id").comboSelect();
						reInitMultiSelect();
						$("#row_index").val($('#purchaseItems tbody').find('tr').length);
						//$("#fgitem_id").comboSelect();
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        $("#item_id").comboSelect();
						reInitMultiSelect();
						//$("#fgitem_id").comboSelect();
                    }   
				}
			//}
		}
    });   

	$(document).on('click','.add-item',function(){
		$(".btn-close").show();
    	$(".btn-save").show();
		$("#trans_id").val("");
		$("#item_id").val("");
        $("#item_id").comboSelect();
        $('#orderItemForm')[0].reset();
		$("#row_index").val($('#purchaseItems tbody').find('tr').length);
	});

    $(document).on('click','.btn-close',function(){
        $('#orderItemForm')[0].reset();
        //$("#unit_id").comboSelect();
        $("#item_id").comboSelect();
		$('#orderItemForm .error').html("");
    });

	$(document).on('change','#item_type',function(){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
				url:base_url + controller + '/getItemListForSelect',
				type:'post',
				data:{item_type:item_type},
				dataType:'json',
				success:function(data){
					$("#item_id").html("");
					$("#item_id").html(data.options);
					$("#item_id").comboSelect();
				}
			});
		} else {
			$("#item_id").html("<option value=''>Select Item Name</option>");
			$("#item_id").comboSelect();
		}
    });
});

function AddRow(data) {
//	$('table#purchaseItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
 	var tblName = "purchaseItems";
	
 	var tBody = $("#"+tblName+" > TBODY")[0];
	
// 	//Add Row.
// 	row = tBody.insertRow(-1);
	
// 	//Add index cell
// 	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
// 	var cell = $(row.insertCell(-1));
// 	cell.html(countRow);
// 	cell.attr("style","width:5%;");	


    if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	
	var ind = (data.row_index == "")?-1:data.row_index;
	row = tBody.insertRow(ind);
	
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>", {type:"hidden",name:"item_id[]",value: data.item_id});
	var transIdInput = $("<input/>", {type:"hidden",name:"trans_id[]",value:data.trans_id});
	var itemTypeInput = $("<input/>", {type:"hidden",name:"item_type[]",value: data.item_type});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);	
	cell.append(itemIdInput);	
	cell.append(transIdInput);
	cell.append(itemTypeInput);

	var deliveryDateInput = $("<input/>",{type:"hidden",name:"delivery_date[]",value:data.delivery_date});
	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	cell = $(row.insertCell(-1));
	cell.html(data.delivery_date);	
	cell.append(deliveryDateInput);
	cell.append(hsnCodeInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);	
	cell.append(qtyInput);

	var qtyKgInput = $("<input/>",{type:"hidden",name:"qty_kg[]",value:data.qty_kg});
	cell = $(row.insertCell(-1));
	cell.html(data.qty_kg);	
	cell.append(qtyKgInput);
	
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	var fgItemIdInput = $("<input/>",{type:"hidden",name:"fgitem_id[]",value:data.fgitem_id});
	var fgItemNameInput = $("<input/>",{type:"hidden",name:"fgitem_name[]",value:data.fgitem_name});
	var wo_noInput = $("<input/>",{type:"hidden",name:"wo_no[]",value:data.wo_no});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);	
	cell.append(unitIdInput);	
	cell.append(fgItemIdInput);	
	cell.append(fgItemNameInput);
	cell.append(wo_noInput);
		
	
	var rateUnitInput = $("<input/>",{type:"hidden",name:"rate_unit[]",value:data.rate_unit});
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(rateUnitInput);
	
	var cgstPerInput = $("<input/>",{type:"hidden",name:"cgst[]",value:data.cgst_per});
	var cgstAmtInput = $("<input/>",{type:"hidden",name:"cgst_amt[]",value:data.cgst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amt+ '(' + data.cgst_per + '%)');
	cell.attr("class","cgstCol");	
	cell.append(cgstPerInput);	
	cell.append(cgstAmtInput);
	
	var sgstPerInput = $("<input/>",{type:"hidden",name:"sgst[]",value:data.sgst_per});
	var sgstAmtInput = $("<input/>",{type:"hidden",name:"sgst_amt[]",value:data.sgst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amt+ '(' + data.sgst_per + '%)');
	cell.attr("class","sgstCol");	
	cell.append(sgstPerInput);	
	cell.append(sgstAmtInput);

	var igstPerInput = $("<input/>",{type:"hidden",name:"igst[]",value:data.igst_per});
	var igstAmtInput = $("<input/>",{type:"hidden",name:"igst_amt[]",value:data.igst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amt + '(' + data.igst_per + '%)');
	cell.attr("class","igstCol");	
	cell.append(igstPerInput);	
	cell.append(igstAmtInput);
	
	var discPerInput = $("<input/>",{type:"hidden",name:"disc_per[]",value:data.disc_per});
	var discAmtInput = $("<input/>",{type:"hidden",name:"disc_amt[]",value:data.disc_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.disc_amt + '(' + data.disc_per + '%)');	
	cell.append(discPerInput);
	cell.append(discAmtInput);
	
	var amountInput = $("<input/>",{type:"hidden",name:"amount[]",value:data.amount});
	cell = $(row.insertCell(-1));
	cell.html(data.amount);
	cell.attr("class","amountCol");	
	cell.append(amountInput);
	
	var remarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	var netAmountInput = $("<input/>",{type:"hidden",name:"net_amount[]",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.attr("class","netAmtCol");	
	cell.append(netAmountInput);
	cell.append(remarkInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
	if($("#gst_type").val() == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if($("#gst_type").val() == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	claculateColumn();
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
	
    var fnm = ""; var item_id = 0;
    $.each(data,function(key, value) {
		if(key == "item_id"){ item_id=value; }
		$("#"+key).val(value); 
	}); 
	
	$("#item_id").comboSelect();
	$("#item_type").comboSelect();
	$.ajax({
		url:base_url + '/grn/setFGItems',
		method:"POST",
		data:{fgitem_id:$("#fgitem_id").val()},
		dataType:"json",
		success:function(response){$("#fgSelect").html(response.fgOpt);reInitMultiSelect();}
	});	  

	var item_type = $("#item_type :selected").val(); 
	$.ajax({
		url:base_url + controller + '/getItemListForSelect',
		type:'post',
		data:{item_type:item_type,item_id:item_id},
		dataType:'json',
		success:function(data){
			$("#item_id").html("");
			$("#item_id").html(data.options);
			$("#item_id").comboSelect();
		}
	});
	$("#row_index").val(row_index);	
    //Remove(button);
}

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
			$("#tempItem").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
		}else if($("#gst_type").val() == 2){
			$("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
		}else{
			$("#tempItem").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
		}
	}	
	claculateColumn();
};

function claculateColumn(){
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
	
	var frgst = parseFloat( parseFloat($("#freight_amt").val()) * 0.18).toFixed(2);
	var framt = parseFloat(parseFloat($("#freight_amt").val()) + parseFloat(frgst)).toFixed(2);
	var pckgst = parseFloat( parseFloat($("#packing_charge").val()) * 0.18).toFixed(2);
	var pckamt = parseFloat(parseFloat($("#packing_charge").val()) + parseFloat(pckgst)).toFixed(2);
	
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum + parseFloat(framt) + parseFloat(pckamt)).toFixed(2);
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
		var amount = parseFloat(netAmtSum + parseFloat(framt) + parseFloat(pckamt)).toFixed(2);
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

function saveOrder(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}