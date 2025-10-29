$(document).ready(function(){

	$(document).on('click','.createDeliveryChallan',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/salesOrder/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Challan');
					$("#party_so").attr('action',base_url + 'deliveryChallan/createChallan');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Challan');
					$("#partyName").html(party_name);
					$("#party_name_so").val(party_name);
					$("#party_id_so").val(party_id);
					$("#orderData").html("");
					$("#orderData").html(data.htmlData);
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

	$(document).on('click','#grnItems',function(){
		$(".item_id").html("");
		if($("#item_id").val() == ""){
			$(".item_id").html("Product name is required.");
		}else{
			$("#grnItemModel").modal();

			var party_id = $("#party_id").val();
			$.ajax({
				url: base_url + controller + '/getCustomerGrnNo',
				data: {party_id:party_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#grn_id").html(data.options);
					$("#grn_id").comboSelect();
				}
			});

			var grnData = $("#grn_data").val();
			if(grnData != ""){
				grnData = JSON.parse( grnData );
				$.each(grnData,function(i, v) {
					addGrnItemRow(JSON.parse(v));
				});
			}
		}		
	});

	$(document).on('change','#grn_id',function(){
		var grn_id = $(this).val();
		if(grn_id != ""){
			$.ajax({
				url: base_url + controller + '/getGrnItems',
				data: {grn_id:grn_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#grn_item_id").html(data.options);
					$("#grn_item_id").comboSelect();
				}
			});
		}
	});

	$(document).on('click','.addGrnItem',function(){
		var grn_id = $("#grn_id").val();
		var grn_no = $("#grn_idc").val();
		var grn_item_id = $("#grn_item_id").val();
		var grn_item_name = $("#grn_item_idc").val();
		var grn_qty = $("#grn_qty").val();
		var grn_remaining_qty = $("#grn_item_id :selected").data("remaining_qty");
		var grn_trans_id = $("#grn_item_id :selected").data("grn_trans_id");

		$('.error').html("");
		if(grn_id == "" || grn_item_id == "" || grn_qty == ""){
			if(grn_id == ""){
				$(".grn_id").html("Grn No is required.");
			}
			if(grn_item_id == ""){
				$(".grn_item_id").html("Item name is required.");
			}
			if(grn_qty == ""){
				$(".grn_qty").html("Qty is required.");
			}
		}else{
			if(parseFloat(grn_qty) > parseFloat(grn_remaining_qty)){
				$(".grn_qty").html("Invalid Qty.");
			}else{
				var postData = {grn_id:grn_id,grn_no:grn_no,grn_trans_id:grn_trans_id,grn_item_id:grn_item_id,grn_item_name:grn_item_name,grn_qty:grn_qty};
				addGrnItemRow(postData);
				$("#grn_id").val("");$("#grn_id").comboSelect();
				$("#grn_item_id").val("");$("#grn_item_id").comboSelect();
				$("#grn_qty").val("0");
			}
		}
	});

	$(document).on('click','.saveGrnItems',function(){
		var fd = $('#grnItemForm').serializeArray();
        var formData = {};
		$.each(fd,function(i, v) {
            formData[i] = v.value;
        });
		$("#grn_data").val(JSON.stringify(formData));		
	});

	// $(document).on("change keyup","#party_id",function(){
	// 	$("#party_name").val($("#party_idc").val());
	// });
	$(document).on("change keyup","#party_id",function(){
		if($(this).val() != ""){
			$("#party_name").val($("#party_idc").val());
			var partyData = $(this).find(":selected").data('row');	
			var gstin = partyData.gstin;		
			var stateCode = "";
			if(gstin != ""){
				stateCode = gstin.substr(0, 2);
				if(stateCode == 24 || stateCode == "24"){gst_type= 1;}else{gst_type= 2;}
			}			
			$("#party_name").val(partyData.party_name);
			$("#party_state_code").val(stateCode);
		}else{
			$("#party_name").val("");
			$("#party_state_code").val("");
		}
	});

    $(document).on('change keyup','#item_id',function(){
        $("#item_name").val($("#item_idc").val());        
    });
	$(document).on('change','#item_id',function(){
		var item_id = $(this).val();
		var batchQtySum = 0;
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#qty").val(batchQtySum.toFixed(3));
		if(item_id == ""){
			$("#item_type").val("");
			$("#item_code").val("");
			$("#item_name").val("");
			$("#item_desc").val("");
			$("#hsn_code").val("");
			$("#gst_per").val("");
			$("#price").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
			$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
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

			$.ajax({
				url: base_url + 'store/batchWiseItemStock',
				data: {item_id:item_id,trans_id:"",batch_no:"",location_id:"",batch_qty:""},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#batchData").html(data.batchData);
				}
			});
		}	
	});

	$(document).on('keyup change',".batchQty",function(){		
		var batchQtyArr = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#qty").val(batchQtySum.toFixed(3));

		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");
		}
	});

    $(document).on("change","#batch_no",function(){
		$('.stockQty').html($(this).find(":selected").data('stock'));
		$('#stockQty').val($(this).find(":selected").data('stock'));
	});
	
	$(document).on("change","#location_id",function(){
		var itemId = $("#item_id").val();
        var location_id = $(this).val();
		$(".location_id").html("");
		$(".item_id").html("");
		$("#batch_stock").val("");
		
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				$(".item_id").html("Issue Item name is required.");
			}
			if(location_id == ""){
				$(".location_id").html("Location is required.");
			}
		}else{
			$.ajax({
				url:base_url + controller + '/getBatchNo',
				type:'post',
				data:{item_id:itemId,location_id:location_id},
				dataType:'json',
				success:function(data){
					$("#batch_no").html("");
					$("#batch_no").html(data.options);
					$("#batch_no").comboSelect();
				}
			});
		}
	});
	
    $(document).on('click','.saveItem',function(){
       
        var fd = $('#challanItemForm').serializeArray();
		
        var formData = {};
        $.each(fd,function(i, v) {
			if(v.name != "batch_quantity[]" && v.name != "batch_number[]" && v.name != "location[]" && v.name != "packing_transid[]" && v.name != "tc_number[]" && v.name != "ref_batch[]"){
				formData[v.name] = v.value;
			}
		});
		
		var batch_qty = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batch_no = $("input[name='batch_number[]']").map(function(){return $(this).val();}).get();
		var tc_no = $("input[name='tc_number[]']").map(function(){return $(this).val();}).get();
		var ref_batch = $("input[name='ref_batch[]']").map(function(){return $(this).val();}).get();
		var location_id = $("input[name='location[]']").map(function(){return $(this).val();}).get();
		var packing_trans_id = $("input[name='packing_transid[]']").map(function(){return $(this).val();}).get();

		var i=0;formData.batch_qty = [];formData.batch_no = []; formData.tc_no = []; formData.ref_batch = [];
		formData.location_id = [];formData.packing_trans_id = [];
		$.each(batch_qty,function(key,value){
			if(parseFloat(value) > 0){
				formData.batch_qty[i] = value;
				formData.batch_no[i] = batch_no[key];
				formData.tc_no[i] = tc_no[key];
				formData.ref_batch[i] = ref_batch[key];
				formData.location_id[i] = location_id[key];
				formData.packing_trans_id[i] = packing_trans_id[key];
				i++;
			}
		});
		
        $(".item_id").html("");$(".qty").html("");$(".location_id").html("");
		$(".scrape_qty").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required.");
		}else{
			if(formData.qty == "" || formData.qty == "0"){
				$(".qty").html("Qty is required.");					
			}else{
				var trans_id = formData.trans_id;var stck = parseFloat(formData.stockQty);
				if(trans_id != "" || parseFloat(trans_id) > 0)
				{
					stck = parseFloat(formData.oldQty) + parseFloat(formData.stockQty);
				}
				
				if(parseFloat(stck) < parseFloat(formData.qty) ){$(".qty").html("Stock not available");} 
				else
				{						
					formData.qty = parseFloat(formData.qty).toFixed(2);
					AddRow(formData);
					$('#challanItemForm')[0].reset();
					$("#item_id").comboSelect();
					$("#challanItemForm .error").html("");
					$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
					if($(this).data('fn') == "save"){ 
						$("#item_id").comboSelect(); 
						$("#item_idc").focus(); 
						// $("#row_index").val($('#invoiceItems tbody').find('tr').length);
					}
					else if($(this).data('fn') == "save_close"){ 
						$("#itemModel").modal('hide');
						$("#item_id").comboSelect(); 
					}
				}
			}
		}
    }); 

	$(document).on('click','.add-item',function(){
        $('#challanItemForm')[0].reset();
		var party_id = $('#party_id').val();
		$(".party_id").html("");	
		if(party_id){
			$("#itemModel").modal();
			$(".btn-close").show();
			$(".btn-save").show();			
			$("#row_index").val("");
		}
		else{$(".party_id").html("Party name is required.");$(".modal").modal('hide');}
	});
	
	$(document).on('change','#order_id',function(){
		var order_id = $(this).val();
		$("#item_id").html("");
		if(party_id)
		{
			$.ajax({
				url:base_url + controller + '/getPendingOrderItems',
				type:'post',
				data:{order_id:order_id},
				dataType:'json',
				success:function(data){
					$("#item_id").html(data.orderItems);
					$("#item_id").comboSelect();
				}
			});
		}
		else{$(".order_id").html("Sales Order is required.");}
	});

    $(document).on('click','.btn-close',function(){
        $('#challanItemForm')[0].reset();
        $("#item_id").comboSelect();
		$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
        $("#challanItemForm .error").html("");
        $(".stockQty").html("0");
		$("#grnItemTableData").html('<tr id="noData"><td class="text-center" colspan="5">No data available in table</td></tr>');
    });

	//Done By Karmi @15/04/2022
	$(document).on("change","#order_type",function(){
		var orderType = $(this).val();
		$("#grnDiv").hide();
		if(orderType == 2){
			$("#soDiv").hide();
			$("#grnDiv").show();
		}else{
			$("#soDiv").show();
			$("#grnDiv").hide();
		}
	});

	//Done By Karmi @15/04/2022
	$(document).on('click','.createDCFromGRN',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/grn/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#grnListModal").modal();
					$("#exampleModalLabel1").html('Create Challan');
					$("#party_grn").attr('action',base_url + 'deliveryChallan/createChallanfromGrn');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Challan');
					$("#partyNameDC").html(party_name);
					$("#party_name_dc").val(party_name);
					$("#party_id_dc").val(party_id);
					$("#grnData").html("");
					$("#grnData").html(data.htmlData);
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});
});

function addGrnItemRow(data){
	$('table#grnItemTable tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "grnItemTable";
	
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

	var grnDataInput = $("<input/>",{type:"hidden",name:"grn_data_row[]",value:JSON.stringify(data)});
	cell = $(row.insertCell(-1));
	cell.html(data.grn_no);
	cell.append(grnDataInput);
	
	cell = $(row.insertCell(-1));
	cell.html(data.grn_item_name);   
	    
	cell = $(row.insertCell(-1));
	cell.html(data.grn_qty);	

	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveGrnRow(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:15%;");
}

function RemoveGrnRow(button){
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#grnItemTable")[0];
	table.deleteRow(row[0].rowIndex);
	$('#grnItemTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#grnItemTable tbody tr:last').index() + 1;
	if(countTR == 0){
        $("#grnItemTableData").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}
}

function AddRow(data) { 
	$('table#invoiceItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "invoiceItems";
	
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
	var formEntryTypeInput = $("<input/>",{type:"hidden",name:"from_entry_type[]",value:data.from_entry_type});    
	var refIdInput = $("<input/>",{type:"hidden",name:"ref_id[]",value:data.ref_id});    
	var stockEffInput = $("<input/>",{type:"hidden",name:"stock_eff[]",value:data.stock_eff});    
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});    
	var unitNameInput = $("<input/>",{type:"hidden",name:"unit_name[]",value:data.unit_name});    
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});    
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code}); 	
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_desc[]",value:data.item_desc}); 	
	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code}); 	
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per}); 	
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price}); 	
	var oldQtyInpdate = $("<input/>",{type:"hidden",name:"oldQty[]",value:data.oldQty}); 
	var batchQtyInput = $("<input/>",{type:"hidden",name:"batch_qty[]",value:data.batch_qty}); 
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no}); 
	var tcNoInput = $("<input/>",{type:"hidden",name:"tc_no[]",value:data.tc_no}); 
	var refBatchInput = $("<input/>",{type:"hidden",name:"ref_batch[]",value:data.ref_batch}); 
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id}); 
	var packingTransIdInput = $("<input/>",{type:"hidden",name:"packing_trans_id[]",value:data.packing_trans_id}); 
	var batchErrorDiv = $("<div></div>",{class:"error batch"+countRow});
	cell = $(row.insertCell(-1));
	cell.html('['+data.item_code+'] '+data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(formEntryTypeInput);
	cell.append(refIdInput);
	cell.append(stockEffInput);
	cell.append(unitIdInput);
	cell.append(unitNameInput);
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(itemDescInput);
	cell.append(hsnCodeInput);
	cell.append(gstPerInput);
	cell.append(priceInput);
	cell.append(oldQtyInpdate);
	cell.append(batchQtyInput);
	cell.append(batchNoInput);
	cell.append(tcNoInput);
	cell.append(refBatchInput);
	cell.append(locationIdInput);
	cell.append(packingTransIdInput);
	cell.append(batchErrorDiv);
	
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyErrorDiv = $("<div></div>",{class:"error qty"+countRow});
	
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);

	//Updated By Karmi for GRN TO DC
	var rejQtyInput = $("<input/>",{type:"hidden",name:"rej_qty[]",value:data.rej_qty});
	var rejQtyErrorDiv = $("<div></div>",{class:"error rej_qty"+countRow});
	var grnDataInput = $("<input/>",{type:"hidden",name:"grn_data[]",value:data.grn_data});
	cell = $(row.insertCell(-1));
	cell.html(data.grn_data);
	cell.append(rejQtyInput);
	cell.append(rejQtyErrorDiv);
	cell.append(grnDataInput);

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	
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
	cell.attr("style","width:15%;");
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#invoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#invoiceItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
    $('#invoiceItems tbody tr td:nth-child(3) div').each(function(idx, ele) {
		let newIdx = idx + 1;
		$(this).attr('class','error');
		$(this).addClass('qty'+newIdx);
    });
	var countTR = $('#invoiceItems tbody tr:last').index() + 1;
	if(countTR == 0){
        $("#tempItem").html('<tr id="noData"><td colspan="6" align="center">No data available in table</td></tr>');
	}
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
    $("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
	var batchNo = ""; var locationId = ""; var batchQty = ""; var tcNo = ""; var refBatch = "";
    $.each(data,function(key, value) {
		if(key=="batch_no"){ batchNo = value; }
		else if(key=="location_id"){ locationId = value; }
		else if(key=="batch_qty"){ batchQty = value; }
		else if(key=="tc_no"){ tcNo = value; }
		else if(key=="ref_batch"){ refBatch = value; }
		else{$("#"+key).val(value);}
    }); 
    $("#item_id").comboSelect();
	
	$.ajax({
		url: base_url + 'store/batchWiseItemStock',
		data: {item_id:data.item_id,trans_id:data.trans_id,batch_no:data.batch_no,location_id:data.location_id,batch_qty:data.batch_qty},
		type: "POST",
		dataType:'json',
		success:function(data){
			$("#batchData").html(data.batchData);
			/* var batchQtyArr = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
			var batchQtySum = 0;
			$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
			$('#totalQty').html("");
			$('#totalQty').html(batchQtySum.toFixed(3)); */
			// $("#qty").val(batchQtySum.toFixed(3));
		}
	});	

    $("#row_index").val(row_index);	
}

function saveChallan(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
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
            window.location = base_url + controller;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
