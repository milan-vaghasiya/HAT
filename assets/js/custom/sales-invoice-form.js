$(document).ready(function(){
	claculateColumn(); 
	gstType();
	gstApplicable();

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

	$(document).on('click','.createSalesInvoice',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		var inv_id = $('#inv_id').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/salesOrder/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Invoice');
					$("#party_so").attr('action',base_url + 'salesInvoice/createInvoice');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
					$("#partyName").html(party_name);
					$("#party_name_so").val(party_name);
					$("#party_id_so").val(party_id);
					$("#sales_id_so").val(inv_id);
					$("#orderData").html("");
					$("#orderData").html(data.htmlData);
					orderTable();
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

	$(document).on('click','.createSalesInvoiceOnCustomInv',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		var inv_id = $('#inv_id').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/customInvoice/getPartyCustomInvocie',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#customInvModal").modal();
					$("#cumInvModalLabel").html('Create Invoice');
					$("#party_cum_inv").attr('action',base_url + 'salesInvoice/createInvoiceOnCustomInv');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
					$("#partyNameCumInv").html(party_name);
					$("#party_name_cum").val(party_name);
					$("#party_id_cum").val(party_id);
					$("#sales_id_cum").val(inv_id);
					$("#cumInvData").html("");
					$("#cumInvData").html(data.htmlData);
					orderTable('cumInvTable');
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

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
		
		$("#sales_type").trigger('change');
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
	
	/**Updated BY Mansee @ 29-12-2021 121-133*/
	$(document).on('change keyup','#party_id',function(){ 
		$("#party_name").val("");
		$("#gstin").html("");
		$("#currency").val("");
		$("#inrrate").val("");
		if($(this).val() != ""){
			var partyData = $(this).find(":selected").data('row');
			var gstArr = (partyData.json_data != '') ? JSON.parse(partyData.json_data) : '';
			if (gstArr != '') {
				$("#gstin").append("<option value='' data-pincode='' data-address=''>Select GSTIN</option>");
				var option = "";
				$.each(gstArr, function (key, row) {
					$("#gstin").append("<option value='" + key + "' data-pincode='" + row.delivery_pincode + "' data-address='" + row.delivery_address + "'>" + key + "</option>");
				});

			} else {

				$("#gstin").append("<option value='' data-pincode='' data-address=''>Select GSTIN</option>");

			}
			$("#party_name").val(partyData.party_name);
			$("#gstin").trigger('change');
			$("#currency").val(partyData.currency);
			$("#inrrate").val(partyData.inrrate);
		}
		
    });

	$(document).on("change", "#gstin", function(){
		var stateCode = "";
		var gstin = $(this).val();
		console.log(gstin);
		var gst_type= 1; 
		if(gstin != "" && gstin != null){
			stateCode = gstin.substr(0, 2);
			if(stateCode == 24 || stateCode == "24"){gst_type= 1;}else{gst_type= 2;}
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}			
		}else{
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}	
			$("#party_state_code").val("");
		}
		$("#party_state_code").val(stateCode);

		if($("#sales_type").val() == 2){			
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(2);
			}else{
				$("#gst_type").val(3);
			}
		}
		gstApplicable();
	});
	
	$("#cum_inv_div").hide();
	$(".expDiv").hide();
	$(".expInv").show();
	var sales_tp = $("#sales_type :selected").val();
	if(sales_tp == 2){
		$("#so_div").hide();
		$("#cum_inv_div").show();
		$(".expDiv").show();
		$(".expInv").hide();
	}else{
		$("#cum_inv_div").hide();
		$("#so_div").show();
		$(".expDiv").hide();
		$(".expInv").show();
	}
	$(document).on('change','#sales_type',function(){
	    var sales_type = $(this).val();	
		if(sales_type == 2){
			$("#so_div").hide();
			$("#cum_inv_div").show();
			$(".expDiv").show();
			$(".expInv").hide();
			var gst_type= 2;			
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}
		}else{
			$("#cum_inv_div").hide();
			$("#so_div").show();
			$(".expDiv").hide();
			$(".expInv").show();
			var gst_type= 1;
			if($("#party_id").val() != ""){
				var partyData = $("#party_id").find(":selected").data('row');	
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
				if($("#gst_applicable").val() == 1){
					$("#gst_type").val(gst_type);
				}else{
					$("#gst_type").val(3);
				}
			}		
		}
		//gstType();
		gstApplicable();
        
        if($("#inv_id").val() == ""){
    		$.ajax({
    			url:base_url+controller+'/getInvoiceNo',
    			type:'post',
    			data:{sales_type:sales_type},
    			dataType:'json',
    			success:function(data){
    				$("#inv_prefix").val(data.trans_prefix);
    				$("#inv_no").val(data.nextTransNo);
    				$("#entry_type").val(data.entry_type);
    			}
    		});
        }
	});

	/* var freightAmt = ($("#freight").val() == "")?"0.00":parseFloat($("#freight").val()).toFixed(2);
	$("#freight_amt").val(freightAmt);
	var freightGst = ($("#freight_gst").val() == "")?"0.00":parseFloat($("#freight_gst").val()).toFixed(2);
	$(".freight_amt").html(parseFloat(freightAmt) + parseFloat(freightGst));
	
	$(document).on('keyup click',"#freight",function(){
		var freightAmt = ($(this).val() == "")?"0.00":parseFloat($(this).val()).toFixed(2);
		$("#freight_amt").val(freightAmt);
		claculateColumn();
		var freightGst = ($("#freight_gst").val() == "")?"0.00":parseFloat($("#freight_gst").val()).toFixed(2);
		$(".freight_amt").html(parseFloat(freightAmt) + parseFloat(freightGst));
		console.log(parseFloat(freightAmt) + " + " + parseFloat(freightGst));
	}); */

	$(document).on('change keyup','#item_id',function(){
        $("#item_name").val($('#item_idc').val());
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
			$("#gst_per").val("");$("#gst_per").comboSelect();
			$("#price").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
			$("#batchData").html('<tr><td class="text-center" colspan="6">No Data Found.</td></tr>'); 
		}else{
			var itemData = $('#item_id :selected').data('row');
		
			$("#item_type").val(itemData.item_type);
			$("#item_code").val(itemData.item_code);
			$("#item_name").val(itemData.item_name);
			$("#item_desc").val(itemData.description);
			$("#hsn_code").val(itemData.hsn_code);
			$("#gst_per").val(parseFloat(itemData.gst_per).toFixed(0));$("#gst_per").comboSelect();
			$("#price").val(itemData.price);
			$("#unit_name").val(itemData.unit_name);
			$("#unit_id").val(itemData.unit_id);

			$("#qty").attr('readonly','readonly');
			$("#batchDiv").show();
			
			$.ajax({
				url: base_url + 'store/batchWiseItemStock',
				data: {item_id:item_id,trans_id:"",batch_no:"",location_id:"",batch_qty:"",tc_no:""}, 
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

	$(document).on("change","#batch_no",function(){
		$('.stockQty').html($(this).find(":selected").data('stock'));
		$('#stockQty').val($(this).find(":selected").data('stock'));
	});

	$(document).on('keyup change','#inrrate',function(){
		var inr_rate = $(this).val();
		inr_rate = (parseFloat(inr_rate) > 0)?inr_rate:0;
		var rowCount = $('#invoiceItems tbody tr').length;
		//console.log(rowCount);
		for(var i=0;i<rowCount;i++){
			if($("#invoiceItems tbody tr:eq("+i+") input[name='org_price[]']").val()){				
				formData = {};				

				formData.item_id = $("#invoiceItems tbody tr:eq("+i+") input[name='item_id[]']").val();
				formData.item_name = $("#invoiceItems tbody tr:eq("+i+") input[name='item_name[]']").val();
				formData.trans_id = $("#invoiceItems tbody tr:eq("+i+") input[name='trans_id[]']").val();
				formData.from_entry_type = $("#invoiceItems tbody tr:eq("+i+") input[name='from_entry_type[]']").val();
				formData.ref_id = $("#invoiceItems tbody tr:eq("+i+") input[name='ref_id[]']").val();
				formData.item_type = $("#invoiceItems tbody tr:eq("+i+") input[name='item_type[]']").val();
				formData.item_code = $("#invoiceItems tbody tr:eq("+i+") input[name='item_code[]']").val();
				formData.item_desc = $("#invoiceItems tbody tr:eq("+i+") input[name='item_desc[]']").val();
				formData.item_desc = $("#invoiceItems tbody tr:eq("+i+") input[name='item_desc[]']").val();
				formData.gst_per = $("#invoiceItems tbody tr:eq("+i+") input[name='gst_per[]']").val();
				formData.location_id = $("#invoiceItems tbody tr:eq("+i+") input[name='location_id[]']").val();
				formData.batch_qty = $("#invoiceItems tbody tr:eq("+i+") input[name='batch_qty[]']").val();
				formData.batch_no = $("#invoiceItems tbody tr:eq("+i+") input[name='batch_no[]']").val();
				formData.tc_no = $("#invoiceItems tbody tr:eq("+i+") input[name='tc_no[]']").val();
				formData.ref_batch = $("#invoiceItems tbody tr:eq("+i+") input[name='ref_batch[]']").val();
				formData.stock_eff = $("#invoiceItems tbody tr:eq("+i+") input[name='stock_eff[]']").val();
				formData.hsn_code = $("#invoiceItems tbody tr:eq("+i+") input[name='hsn_code[]']").val();
				formData.qty = $("#invoiceItems tbody tr:eq("+i+") input[name='qty[]']").val();
				formData.unit_id = $("#invoiceItems tbody tr:eq("+i+") input[name='unit_id[]']").val();
				formData.unit_name = $("#invoiceItems tbody tr:eq("+i+") input[name='unit_name[]']").val();
				formData.unit_name = $("#invoiceItems tbody tr:eq("+i+") input[name='unit_name[]']").val();

				var org_price = $("#invoiceItems tbody tr:eq("+i+") input[name='org_price[]']").val();
				formData.org_price = (org_price != "" || parseFloat(org_price) > 0)?org_price:0;
				formData.price = parseFloat(parseFloat(org_price) * parseFloat(inr_rate)).toFixed(2);

				formData.disc_per = $("#invoiceItems tbody tr:eq("+i+") input[name='disc_per[]']").val();
				formData.item_remark = $("#invoiceItems tbody tr:eq("+i+") input[name='item_remark[]']").val();
				
				
				var cgst_amt = 0;var sgst_amt = 0;var igst_amt = 0;
				var cgst_per = 0;var sgst_per = 0;var igst_per = 0;
				var amount = 0;var disc_amt = 0;var total = 0;var net_amount = 0;
				if(formData.disc_per == "" || parseFloat(formData.disc_per) == "0"){
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
				formData.row_index = ""+i+"";		
				//console.log(formData);
				AddRow(formData); 				
			}
		}
		//console.log($("#invoiceItems tbody tr:eq("+key+") input[name='org_price[]']").val());
	});

    $(document).on('click','.saveItem',function(){
        
        var fd = $('#invoiceItemForm').serializeArray();

        var formData = {};
        $.each(fd,function(i, v) {
			if(v.name != "batch_quantity[]" && v.name != "batch_number[]" && v.name != "location[]" && v.name != "packing_transid[]" && v.name != "tc_number[]" && v.name != "ref_batch[]"){
				formData[v.name] = v.value;
			}
		});
		
		if(formData.stock_eff == 1){
			var batch_qty = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
			var batch_no = $("input[name='batch_number[]']").map(function(){return $(this).val();}).get();
			var tc_no = $("input[name='tc_number[]']").map(function(){return $(this).val();}).get();
			var refBatch = $("input[name='ref_batch[]']").map(function(){return $(this).val();}).get();
			var location_id = $("input[name='location[]']").map(function(){return $(this).val();}).get();
			var packing_trans_id = $("input[name='packing_transid[]']").map(function(){return $(this).val();}).get();

			var i=0;formData.batch_qty = [];formData.batch_no = []; formData.tc_no = [];formData.ref_batch = [];
			formData.location_id = [];formData.packing_trans_id = [];
			$.each(batch_qty,function(key,value){
				if(parseFloat(value) > 0){
					formData.batch_qty[i] = value;
					formData.batch_no[i] = batch_no[key];
					formData.tc_no[i] = tc_no[key];
					formData.ref_batch[i] = refBatch[key];
					formData.location_id[i] = location_id[key];
					formData.packing_trans_id[i] = packing_trans_id[key];
					i++;
				}
			});
		}else{
			formData.batch_qty = [];formData.batch_no = []; formData.tc_no = []; formData.ref_batch = [];
			formData.location_id = [];formData.packing_trans_id = [];
		}

        $(".item_id").html("");
		$(".qty").html("");
        $(".unit_id").html("");
        if(formData.item_id == ""){
			$(".item_id").html("Item Name is required.");
		}else{
			var item_ids = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_id,item_ids) >= 0  && formData.row_index == "") {
				$(".item_id").html("Item already added.");
			}else {
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
                    AddRow(formData); 
					$('#invoiceItemForm')[0].reset();
					$("#batchData").html('<tr><td class="text-center" colspan="6">No Data Found.</td></tr>');
                    if($(this).data('fn') == "save"){
                        $("#item_id").comboSelect();
                        $("#item_idc").focus();
                    }else if($(this).data('fn') == "save_close"){
                        $("#item_id").comboSelect();
                        $("#itemModel").modal('hide');
                    } 
					
				}
			}
		}
    }); 

	$(document).on('click','.add-item',function(){
		var party_id = $('#party_id').val();	
		$(".party_id").html("");	
		$("#row_index").val("");
		if(party_id){
			$.ajax({ 
				type: "POST",   
				url: base_url + controller + '/getPartyItems',   
				data: {party_id:party_id},
				dataType:'json',
			}).done(function(response){
				$("#item_id").html(response.partyItems);
				$("#item_id").comboSelect();
				setPlaceHolder();
				$("#itemModel").modal();
				$(".btn-close").show();
				$(".btn-save").show();
				
			});			
		}else{$(".party_id").html("Party name is required.");$(".modal").modal('hide');}
	});
	
    $(document).on('click','.btn-close',function(){
        $('#invoiceItemForm')[0].reset();
        $("#item_id").comboSelect();
        $("#batchData").html('<tr><td class="text-center" colspan="6">No Data Found.</td></tr>'); 
        $("#invoiceItemForm .error").html("");
    });

	$(document).on("change","#apply_round",function(){
		claculateColumn();
	});

	$(document).on('keyup','.calculateSummary',function(){
		calculateSummary();
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
	var formEnteryTypeInput = $("<input/>",{type:"hidden",name:"from_entry_type[]",value:data.from_entry_type});
	var refIdInput = $("<input/>",{type:"hidden",name:"ref_id[]",value:data.ref_id});
	var soRefIdInput = $("<input/>",{type:"hidden",name:"so_ref_id[]",value:data.so_ref_id});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_desc[]",value:data.item_desc});	
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per});
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
	var batchQtyInput = $("<input/>",{type:"hidden",name:"batch_qty[]",value:data.batch_qty});
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var tcNoInput = $("<input/>",{type:"hidden",name:"tc_no[]",value:data.tc_no});
	var refBatchInput = $("<input/>",{type:"hidden",name:"ref_batch[]",value:data.ref_batch});
	var packingTransIdInput = $("<input/>",{type:"hidden",name:"packing_trans_id[]",value:data.packing_trans_id}); 
	var stockEffInput = $("<input/>",{type:"hidden",name:"stock_eff[]",value:data.stock_eff});
	var bacthErrorDiv = $("<div></div>",{class:"error batch_no"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(formEnteryTypeInput);
	cell.append(refIdInput);
	cell.append(soRefIdInput);
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(itemDescInput);
	cell.append(gstPerInput);
	cell.append(locationIdInput);
	cell.append(batchQtyInput);
	cell.append(batchNoInput);
	cell.append(tcNoInput);
	cell.append(refBatchInput);
	cell.append(packingTransIdInput);
	cell.append(stockEffInput);
	cell.append(bacthErrorDiv);

	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyErrorDiv = $("<div></div>",{class:"error qty"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);
	
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	var unitNameInput = $("<input/>",{type:"hidden",name:"unit_name[]",value:data.unit_name});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);
	
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	var orgPriceInput = $("<input/>",{type:"hidden",name:"org_price[]",value:data.org_price});
	var priceErrorDiv = $("<div></div>",{class:"error price"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(orgPriceInput);
	cell.append(priceErrorDiv);
	
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
	
	gstApplicable();
	//claculateColumn();
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#invoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#invoiceItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#invoiceItems tbody tr:last').index() + 1;
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
		else{ $("#"+key).val(value); }
    }); 
    $("#item_id").comboSelect();

	if(data.stock_eff == 1){
		$("#qty").attr('readonly','readonly');
		$("#batchDiv").show();
		$.ajax({
			url: base_url + 'store/batchWiseItemStock',
			data: {item_id:data.item_id,trans_id:data.trans_id,batch_no:data.batch_no,location_id:data.location_id,batch_qty:data.batch_qty,tc_no:data.tc_no}, 
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#batchData").html(data.batchData);
			}
		});
	}else{
		$("#batchDiv").hide();
		$("#qty").removeAttr('readonly');
	}	
    $("#row_index").val(row_index);	
}

/* function claculateColumn()
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

	var frAmt = parseFloat($("#freight_amt").val());
	var frGst = parseFloat(parseFloat(frAmt * 18)/100).toFixed(2);
	var totalFrAmt = parseFloat(frAmt) + parseFloat(frGst);
	$("#freight_gst").val(frGst);
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum + totalFrAmt).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			//if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			//else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);} 
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
		var amount = parseFloat(netAmtSum + totalFrAmt).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			//if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			//else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}

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
} */

function claculateColumn(){	

	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	$("#taxable_amount").val(amountSum.toFixed(2));	
	calculateSummary();	
	
}

function calculateSummary(){
	$(".calculateSummary").each(function(){
		var row = $(this).data('row');

		var map_code = row.map_code;
		var amtField = $("#"+map_code+"_amt");
		var netAmountField = $("#"+map_code+"_amount");
		var perField = $("#"+map_code+"_per");		
		var sm_type = amtField.data('sm_type');

		if(sm_type == "exp"){
			if(row.position == "1"){
				var itemGstArray = $("input[name='gst_per[]']").map(function(){return $(this).val();}).get();
				var gstPer = [];
				$.each(itemGstArray,function(){gstPer.push(parseFloat(this))});
				var maxGstPer = Math.max.apply(Math, gstPer);
				maxGstPer = (maxGstPer != "")?maxGstPer:0;

				if(row.calc_type == "1"){
					var amount = (amtField.val() != "")?amtField.val():0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount))/100).toFixed(2);
				}else{
					var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
					var per = (perField.val() != "")?perField.val():0;
					
					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount))/100).toFixed(2);		
				}
				
				$("#other_"+map_code+"_amount").val(gstAmount);

			}else{
				if(row.calc_type == "1"){
					var amount = (amtField.val() != "")?amtField.val():0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
				}else{
					var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
					var per = (perField.val() != "")?perField.val():0;
					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);				
					netAmountField.val(amount);
				}
			}
		}

		if(sm_type == "tax"){
			if(row.calculation_type == 1){
				var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
				var per = (perField.val() != "")?perField.val():0;
				var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}else{
				var qtyArray = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
				var qtySum = 0;
				$.each(qtyArray,function(){qtySum += parseFloat(this) || 0;});

				var per = (perField.val() != "")?perField.val():0;
				var amount = parseFloat(parseFloat(qtySum) * parseFloat(per)).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}			
		}

		
	});

	calculateSummaryAmount();
}

function calculateSummaryAmount(){
	var gst_type = $("#gst_type").val();

	$('#cgst_amount').val("0");
	$('#sgst_amount').val("0");
	if(gst_type == 1){
		var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();
		var cgstAmtSum = 0;
		$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
		$('#cgst_amount').val(parseFloat(cgstAmtSum).toFixed(2));
		
		var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();
		var sgstAmtSum = 0;
		$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
		$('#sgst_amount').val(parseFloat(sgstAmtSum).toFixed(2));
	}
	
	$('#igst_amount').val("0");
	if(gst_type == 2){
		var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();
		var igstAmtSum = 0;
		$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
		$('#igst_amount').val(parseFloat(igstAmtSum).toFixed(2));
	}
	
	var otherGstAmtArray = $(".otherGstAmount").map(function(){return $(this).val();}).get();
	var otherGstAmtSum = 0;
	$.each(otherGstAmtArray,function(){otherGstAmtSum += parseFloat(this) || 0;});

	var cgstAmt = 0;
	var sgstAmt = 0;
	var igstAmt = 0;
	if(gst_type == 1){
		cgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		sgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		$("#cgst_amount").val(parseFloat(parseFloat($("#cgst_amount").val()) + parseFloat(cgstAmt)).toFixed(2));
		$("#sgst_amount").val(parseFloat(parseFloat($("#sgst_amount").val()) + parseFloat((sgstAmt))).toFixed(2));						
	}else if(gst_type == 2){
		igstAmt = otherGstAmtSum;
		$("#igst_amount").val(parseFloat(parseFloat($("#igst_amount").val()) + parseFloat((igstAmt))).toFixed(2));
	}	

	var summaryAmtArray = $(".summaryAmount").map(function(){return $(this).val();}).get();
	var summaryAmtSum = 0;
	$.each(summaryAmtArray,function(){summaryAmtSum += parseFloat(this) || 0;});	

	if($("#roff_amount").length > 0){
		var totalAmount = parseFloat(summaryAmtSum).toFixed(2);
		var decimal = totalAmount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		if (decimal !== 0) {
			if (decimal >= 50) {
				if ($('#apply_round').val() == "0") { roundOff = (100 - decimal) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			}else {
				if ($('#apply_round').val() == "0") { roundOff = (decimal - (decimal * 2)) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			}
			$("#roff_amount").val(parseFloat(roundOff).toFixed(2));
		}
		$("#net_inv_amount").val(netAmount);
	}else{
		$("#net_inv_amount").val(summaryAmtSum.toFixed(2));
	}
}

function saveInvoice(formId){
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

// Created By : Karmi 15/12/2021
function orderTable(table_id = "orderTable"){
	var orderTable = $('#'+table_id).DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:100,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [] //[ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	orderTable.buttons().container().appendTo( '#orderTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return orderTable;
}