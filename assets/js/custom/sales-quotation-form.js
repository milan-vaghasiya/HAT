$(document).ready(function(){
	$(".discCol").hide();
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

	$(document).on('change','#item_id',function(){
		if($(this).val() == ""){
			$("#item_type").val("");
			$("#item_code").val("");
			$("#item_name").val("");
			$("#item_desc").val("");
			$("#hsn_code").val("");
			$("#gst_per").val("");
			$("#price").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
			$("#unit_id").comboSelect();
		}else{
			var itemData = $('#item_id :selected').data('row');
		
			$("#item_type").val(itemData.item_type);
			$("#item_code").val(itemData.item_code);
			$("#item_name").val(itemData.item_name);
			$("#item_desc").val(itemData.description);
			$("#hsn_code").val(itemData.hsn_code);
			$("#gst_per").val(itemData.gst_per);
			$("#price").val(itemData.price);
			$("#unit_name").val(itemData.unit_name);
			$("#unit_id").val(itemData.unit_id);
			$("#unit_id").comboSelect();
		}	
	});
	//$(document).on('keyup change',"#item_idc",function(){
	//	$("#item_name").val($("#item_idc").val());
	//});

	claculateColumn();
	gstType();
	gstApplicable();

	$(document).on("change","#gst_applicable",function(){
		var gstType = $("#gst_type").val();
		var gstApplicable = $(this).val();
		if(gstApplicable == 1){
			if($("#party_id").val() != ""){
				var partyData = $("#party_id").find(":selected").data('row');
				var gstin = partyData.gstin;		
				var stateCode = "";
				if(gstin != ""){
					stateCode = gstin.substr(0, 2);
					if(stateCode == 24 || stateCode == "24"){gstType= 1;}else{gstType= 2;}
				}else{
					gstType = 1;
				}
			}else{
				gstType = 1;
			}

			if(gstApplicable == 1){
				$("#gst_type").val(gstType);
			}else{
				$("#gst_type").val(3);
			}
			if(gstType == 1){ 
				$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else if(gstType == 2){
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else{
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
				$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			}
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
		claculateColumn();
	});
	
	$(document).on("change","#gst_type",function(){
		var gstType = $(this).val();
		var gstApplicable = $("#gst_applicable").val();
		if(gstApplicable == 1){
			if(gstType == 1){ 
				$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else if(gstType == 2){
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else{
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
				$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			}
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
		claculateColumn();
	});

    $(document).on('change keyup','#unit_id',function(){
        $("#unit_name").val($('#unit_idc').val());
    });

    $(document).on('change keyup','#party_id',function()
	{
		var gst_type= 1;
		if($(this).val() != "" || $(this).val() != "0"){
			var partyData = $(this).find(":selected").data('row');
			$("#contact_person").val(partyData.contact_person);
			$("#contact_no").val(partyData.party_mobile);
			$("#contact_email").val(partyData.contact_email);
			$("#party_phone").val(partyData.party_phone);
			$("#party_email").val(partyData.party_email);
			$("#party_address").val(partyData.party_address);
			$("#party_pincode").val(partyData.party_pincode);
			$("#sales_executive").val(partyData.sales_executive);
			$("#party_name").val(partyData.party_name); 

			var gstin = partyData.gstin;		
			var stateCode = "";
			if(gstin != ""){
				stateCode = gstin.substr(0, 2);
				if(stateCode == 24 || stateCode == "24"){gst_type= 1;}else{gst_type= 2;}
			}
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}
		}else{
			$("#contact_person").val("");
			$("#contact_no").val("");
			$("#contact_email").val("");
			$("#party_phone").val("");
			$("#party_email").val("");
			$("#party_address").val("");
			$("#party_pincode").val("");
			$("#sales_executive").val("");
			$("#party_name").val(""); 

			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}
		}		
		$("#sales_executive").comboSelect();
		gstApplicable();
	});
    $(document).on('change keyup','#party_idc',function(){$("#party_name").val($('#party_idc').val()); });

    $(document).on('click','.saveItem',function(){
        var fd = $('#quotationItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });
		console.log(formData);
        $(".item_name").html("");
		$(".qty").html("");
        $(".unit_id").html("");
		$(".price").html("");
        if(formData.item_name == ""){
			$(".item_name").html("Item Name is required.");
		}else{
			var item_names = $("input[name='item_name[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_name,item_names) >= 0 && formData.row_index == "") {
				$(".item_name").html("Item already added.");
			}else {
				if(formData.qty == "" || formData.qty == "0" || formData.price == "" || formData.price == "0" || formData.unit_id == ""){
					if(formData.qty == "" || formData.qty == "0"){
						$(".qty").html("Qty is required.");
					}
					if(formData.price == "" || formData.price == "0"){
						$(".price").html("Price is required.");
					}
					if(formData.unit_id == ""){
						$(".unit_id").html("Unit name is required.");	
					}
				}else{
                    var amount = 0;var total = 0;var disc_amt = 0;var igst_amt = 0;
					var cgst_amt = 0;var sgst_amt = 0;var net_amount = 0;var cgst_per = 0;var sgst_per = 0; var igst_per = 0;
					formData.disc_per = 0;
					if(formData.disc_per == "" && formData.disc_per == "0"){
						amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
					}else{
						total = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
						disc_amt = parseFloat((total * parseFloat(formData.disc_per))/100).toFixed(2);
						amount = parseFloat(total - disc_amt).toFixed(2);
					}
					
					cgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
					sgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
					
					cgst_amt = parseFloat((cgst_per * amount )/100).toFixed(2);
					sgst_amt = parseFloat((sgst_per * amount )/100).toFixed(2);
					
					igst_per = parseFloat(formData.gst_per).toFixed(2);
					igst_amt = parseFloat((igst_per * amount )/100).toFixed(2);
					
					net_amount = parseFloat(parseFloat(amount) + parseFloat(igst_amt)).toFixed(2);

                    formData.gst_type = $('#gst_type').val();
					formData.qty = parseFloat(formData.qty).toFixed(2);
					formData.cgst_per = cgst_per;
                    formData.cgst_amt = cgst_amt;
                    formData.sgst_per = sgst_per;
                    formData.sgst_amt = sgst_amt;
                    formData.igst_per = igst_per;
                    formData.igst_amt = igst_amt;
                    formData.disc_amt = disc_amt;
                    formData.amount = amount;
                    formData.net_amount = net_amount;
					console.log(formData);
					AddRow(formData);
                    $('#quotationItemForm')[0].reset();					
                    if($(this).data('fn') == "save"){
                        $("#item_idc").focus();
                        //$("#item_id").comboSelect();
						$("#item_id").select2();
                        $("#unit_id").comboSelect();
						$("#row_index").val($('#salesEnqItems tbody').find('tr').length);
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        //$("#item_id").comboSelect();
						$("#item_id").select2();
						$("#unit_id").comboSelect();
                    }   

					/* formData.qty = parseFloat(formData.qty).toFixed(2);
					
					AddRow(formData);
                    $('#quotationItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_idc").focus();
                        $("#item_id").comboSelect();
                        $("#unit_id").comboSelect();                        
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        $("#item_id").comboSelect();
						$("#unit_id").comboSelect();
                    }    */
				}
			}
		}
    });  
    
	$(document).on('click','.add-item',function(){
        $('#quotationItemForm')[0].reset();
	    $("#trans_id").val("");
		$("#row_index").val($('#salesEnqItems tbody').find('tr').length);
		$(".btn-close").show();
    	$(".btn-save").show();
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#quotationItemForm')[0].reset();
        //$("#item_id").comboSelect();
		$("#item_id").select2();
        $("#unit_id").comboSelect();
		$('#quotationItemForm .error').html('');	
    });	$("#item_name").attr("autocomplete","off");
	
	$(document).on('change',"#challan_no",function(){
		var apply = $(this).val(); var charge = $(this).find(':selected').data("charge");
		if(apply == 1){
			$("#dev_charge").attr('readonly','readonly');
			$("#dev_charge").val(0);
		} else {
			$("#dev_charge").removeAttr('readonly');
			$("#dev_charge").val(charge);
		}
	});

	$(document).on("change","#apply_round",function(){
		claculateColumn();
	});
	
});

function gstType(){
	var gstType = $("#gst_type").val();
	var gstApplicable = $("#gst_applicable").val();
	if(gstApplicable == 1){
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		$("#gst_type").val(3);
	}
	claculateColumn();
}

function gstApplicable(){
	var gstType = $("#gst_type").val();
	var gstApplicable = $("#gst_applicable").val();
	if(gstApplicable == 1){
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		}
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		$("#gst_type").val(3);
	}
	claculateColumn();
}

function AddRow(data) {
	$('table#salesEnqItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "salesEnqItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
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
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var formEnteryTypeInput = $("<input/>",{type:"hidden",name:"from_entry_type[]",value:data.from_entry_type});
	var refIdInput = $("<input/>",{type:"hidden",name:"ref_id[]",value:data.ref_id});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_desc[]",value:data.item_desc});
	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per});
	cell = $(row.insertCell(-1));
	cell.html("[ "+data.item_code+" ] "+data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(formEnteryTypeInput);
	cell.append(refIdInput);
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(itemDescInput);
	cell.append(hsnCodeInput);
	cell.append(gstPerInput);
	
	var drgRevNokInput = $("<input/>",{type:"hidden",name:"drg_rev_no[]",value:data.drg_rev_no});
	cell = $(row.insertCell(-1));
	cell.html(data.drg_rev_no);
	cell.append(drgRevNokInput);
	
	var revNoInput = $("<input/>",{type:"hidden",name:"rev_no[]",value:data.rev_no});
	cell = $(row.insertCell(-1));
	cell.html(data.rev_no);
	cell.append(revNoInput);

	var partNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});	
	cell = $(row.insertCell(-1));
	cell.html(data.batch_no);
	cell.append(partNoInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	var unitNameInput = $("<input/>",{type:"hidden",name:"unit_name[]",value:data.unit_name});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);
	
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);

	var cgstPerInput = $("<input/>",{type:"hidden",name:"cgst[]",value:data.cgst_per});
	var cgstAmtInput = $("<input/>",{type:"hidden",name:"cgst_amt[]",value:data.cgst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.cgst_amt+ '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	cell.attr("class","cgstCol");
	
	var sgstPerInput = $("<input/>",{type:"hidden",name:"sgst[]",value:data.sgst_per});
	var sgstAmtInput = $("<input/>",{type:"hidden",name:"sgst_amt[]",value:data.sgst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.sgst_amt+ '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	cell.attr("class","sgstCol");

	var igstPerInput = $("<input/>",{type:"hidden",name:"igst[]",value:data.igst_per});
	var igstAmtInput = $("<input/>",{type:"hidden",name:"igst_amt[]",value:data.igst_amt});
	cell = $(row.insertCell(-1));
	cell.html(data.igst_amt + '(' + data.igst_per + '%)');
	cell.append(igstPerInput);
	cell.append(igstAmtInput);
	cell.attr("class","igstCol");
	
	var discPerInput = $("<input/>",{type:"hidden",name:"disc_per[]",value:data.disc_per});
	var discAmtInput = $("<input/>",{type:"hidden",name:"disc_amt[]",value:data.disc_amt});
    cell = $(row.insertCell(-1));
	cell.html(data.disc_amt + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);
	cell.attr("class","discCol");
	
	var amountInput = $("<input/>",{type:"hidden",name:"amount[]",value:data.amount});
	cell = $(row.insertCell(-1));
	cell.html(data.amount);
	cell.append(amountInput);
	cell.attr("class","amountCol");
	
	var netAmtInput = $("<input/>",{type:"hidden",name:"net_amount[]",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class","netAmtCol");

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	var prodDisInput = $("<input/>",{type:"hidden",name:"grn_data[]",value:data.grn_data});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	cell.append(prodDisInput);
	
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

	if(data.gst_type == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(data.gst_type == 2){
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
	var item_id = "";
	var item_name = "";
    $.each(data,function(key, value) {
		if(key == "item_id"){item_id = value;} 
		if(key == "item_name"){item_name = value;} 
		$("#"+key).val(value);
	}); 
    //$("#item_id").comboSelect();
	$("#item_id").select2();
	$("#unit_id").comboSelect();
	if(item_id == "" || item_id == "0"){
		$("#item_idc").val(item_name);
	}
    $("#row_index").val(row_index);	
    //Remove(button);
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#salesEnqItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#salesEnqItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#salesEnqItems tbody tr:last').index() + 1;
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
			/* if(decimal>=50){
				if($('#apply_round').val()==0){roundOff=(100-decimal)/100;}
				netAmount = parseFloat(amount) + parseFloat(roundOff);
			}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);} */
			if(decimal>=50){
				if($('#apply_round').val()==0){roundOff=(100-decimal)/100;}
				netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{
				if($('#apply_round').val()==0){roundOff=(decimal-(decimal*2))/100;} 
				netAmount = parseFloat(amount) + parseFloat(roundOff);
			}
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
			if(decimal>=50){
				if($('#apply_round').val()==0){roundOff=(100-decimal)/100;}
				netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{
				if($('#apply_round').val()==0){roundOff=(decimal-(decimal*2))/100;} 
				netAmount = parseFloat(amount) + parseFloat(roundOff);
			}
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

	$(".discCol").hide();
}

function saveEnquiry(formId){
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