$(document).ready(function(){
    
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
	$(document).on('keyup',"#item_idc",function(){
		$("#item_name").val($("#item_idc").val());
	});

    $(document).on('change keyup','#unit_id',function(){
        $("#unit_name").val($('#unit_idc').val());
    });

    $(document).on('change keyup','#party_id',function(){
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
		}		
	});
    $(document).on('change keyup','#party_idc',function(){$("#party_name").val($('#party_idc').val()); });

    $(document).on('click','.saveItem',function(){
        var fd = $('#enquiryItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });
        $(".item_name").html("");
		$(".qty").html("");
        $(".unit_id").html("");
		$(".item_name").html("");
        if(formData.item_name == ""){
			$(".item_name").html("Item Name is required.");
		}else{
			var item_names = $("input[name='item_name[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_name,item_names) >= 0 && formData.row_index == "") {
				$(".item_name").html("Item already added.");
			}else {
				if(formData.qty == "" || formData.qty == "0" || formData.unit_id == "" || formData.feasible == "No" && formData.item_remark == ""){
					if(formData.qty == "" || formData.qty == "0"){
						$(".qty").html("Qty is required.");
					}
					if(formData.unit_id == ""){
						$(".unit_id").html("Unit name is required.");	
					}
					if(formData.feasible == "No" && formData.item_remark == ""){
						$(".item_remark").html("Reason is required.");	
					}
				}else{
                    
					formData.qty = parseFloat(formData.qty).toFixed(2);
					
					AddRow(formData);
                    $('#enquiryItemForm')[0].reset();
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
				}
			}
		}
    });  
    
	$(document).on('click','.add-item',function(){
        $('#enquiryItemForm')[0].reset();
		$("#trans_id").val("");
		$("#row_index").val($('#salesEnqItems tbody').find('tr').length);
		$(".btn-close").show();
    	$(".btn-save").show();
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#enquiryItemForm')[0].reset();
        $("#unit_id").comboSelect();
		//$("#item_id").comboSelect();
		$("#item_id").select2();
		$('#enquiryItemForm .error').html('');	
    });	$("#item_name").attr("autocomplete","off");
	
	/* $('#item_name').typeahead({
		source: function(query, result)
		{
			$.ajax({
				url:base_url + controller + '/itemSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){result($.map(data, function(item){return item;}));}
			});
		}
	 }); */
});

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
	
// 	alert($('#'+tblName+' tbody tr:last').index());
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
// 	alert(countRow);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var automotiveInput = $("<input/>",{type:"hidden",name:"automotive[]",value:data.automotive});
	var formEntryTypeInput = $("<input/>",{type:"hidden",name:"from_entry_type[]",value:data.from_entry_type});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_desc[]",value:data.item_desc});
	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per});
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html("[ "+data.item_code+" ] "+data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(automotiveInput);
	cell.append(formEntryTypeInput);
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(itemDescInput);
	cell.append(hsnCodeInput);
	cell.append(gstPerInput);
	cell.append(priceInput);
	
	
	var drgRevNokInput = $("<input/>",{type:"hidden",name:"drg_rev_no[]",value:data.drg_rev_no});
	var revNoInput = $("<input/>",{type:"hidden",name:"rev_no[]",value:data.rev_no});
	var partNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	
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


	var feasiblekInput = $("<input/>",{type:"hidden",name:"feasible[]",value:data.feasible});
    cell = $(row.insertCell(-1));
	cell.html(data.feasible);
	cell.append(feasiblekInput);

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
	
    $("#unit_id").comboSelect();
	//$("#item_id").comboSelect();
	$("#item_id").select2();
	$("#item_remark").comboSelect();
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
		$("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
	}	
};

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