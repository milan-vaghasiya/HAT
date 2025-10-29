$(document).ready(function(){
    $("#entry_type").trigger('change');
    $(document).on('click','.add-item',function(){
		setPlaceHolder();
        $("#itemModel").modal();
        $(".btn-close").show();
        $(".btn-save-close").show();
        $(".btn-save").show();
        
        $('#packingItemForm #id').val("");
        $("#packingItemForm #row_index").val("");
        $('#packingItemForm #trans_child_id').val("0");
        $('#packingItemForm #item_code').val("");
        $('#packingItemForm #ref_id').val("");
        $('#packingItemForm #packing_type').val("1");
        
        var packing_type = $('#entry_type').val(); 
        $(".itemList").html('<option value="" data-trans_child_id="0">Selec Product</option>');
        $(".itemList").comboSelect();
        $("#box_id").html('<option value="" data-qty_per_box="" data-ref_id="" data-wt_pcs="" data-size="">Packing Material</option>');
        $("#box_id").comboSelect();
        if(packing_type == "Export"){
            $("#is_final").val(0);
            $("#packing_type").val(1);
            $(".expField").show();
            $('.finalPacking').hide();
            $(".itemDiv").removeClass("col-md-12");
            $(".itemDiv").addClass("col-md-5");
            $("#qty_per_box").attr("readOnly",true);
            /* var status = $("#is_final").val();
            if(status == 0){
                $('.finalPacking').hide();
            }else{
                $('.finalPacking').show();
            } */
            $.ajax({
                url : base_url + controller + '/getSalesOrderNoListForPacking',
                type : 'post',
                data:{order_id:""},
                dataType:'json',
                success:function(data){
                    $("#trans_main_id").html("");
                    $("#trans_main_id").html(data.orderNoList);
                    $("#trans_main_id").comboSelect();
                }
            });
        }else{
            $("#is_final").val(1);
            $("#packing_type").val(0);
            $(".expField").hide();
            $(".itemDiv").removeClass("col-md-5");
            $(".itemDiv").addClass("col-md-12");
            $("#trans_main_id").html("");
            $("#trans_main_id").comboSelect();
            $("#qty_per_box").attr("readOnly",false);

            $.ajax({
                url : base_url + controller + '/getItemList',
                type : 'post',
                data:{order_id:0},
                dataType:'json',
                success:function(data){
                    $(".itemList").html("");
                    $(".itemList").html(data.itemList);
                    $(".itemList").comboSelect();
                }
            });
            $('.finalPacking').show();
        }
	});

    $(document).on('change','#entry_type',function(){
        var packing_type = $(this).val();
        $(".itemList").html('<option value="" data-trans_child_id="0">Selec Product</option>');
        $(".itemList").comboSelect();
        if(packing_type == "Export"){
            $("#is_final").val(0);
            $(".expField").show();
            $('.finalPacking').hide();
            $(".itemDiv").removeClass("col-md-12");
            $(".itemDiv").addClass("col-md-5");
            /* var status = $("#is_final").val();
            if(status == 0){
                $('.finalPacking').hide();
            }else{
                $('.finalPacking').show();
            } */
            $.ajax({
                url : base_url + controller + '/getSalesOrderNoListForPacking',
                type : 'post',
                data:{order_id:""},
                dataType:'json',
                success:function(data){
                    $("#trans_main_id").html("");
                    $("#trans_main_id").html(data.orderNoList);
                    $("#trans_main_id").comboSelect();
                }
            });
        }else{
            $("#is_final").val(1);
            $(".expField").hide();
            $(".itemDiv").removeClass("col-md-5");
            $(".itemDiv").addClass("col-md-12");
            $("#trans_main_id").html("");
            $("#trans_main_id").comboSelect();
            $.ajax({
                url : base_url + controller + '/getItemList',
                type : 'post',
                data:{order_id:0},
                dataType:'json',
                success:function(data){
                    $(".itemList").html("");
                    $(".itemList").html(data.itemList);
                    $(".itemList").comboSelect();
                }
            });
            $('.finalPacking').show();
        }
        
    });

    $(document).on('change','#packing_type',function(){
        var packing_type = $(this).val();
        var item_id = $("#item_id").val();

        $(".item_id").html("");
        if(item_id == ""){
            $(".item_id").html("Product is required.");
        }else{
            $.ajax({
                url:base_url + controller + '/getPackingMaterial',
                type:'post',
                data:{packing_type:packing_type,item_id:item_id,ref_id:"",box_id:""},
                dataType:'json',
                success:function(data){
                    $("#box_id").html("");
                    $("#box_id").html(data.material_options);
                    $("#box_id").comboSelect();
                }
            });
        }
    });

    $(document).on('change','#box_id',function(){
        var box_size = $("#box_id :selected").data('size');
        var ref_id = $("#box_id :selected").data('ref_id');
        var qty_per_box = $("#box_id :selected").data('qty_per_box');
        var wt_pcs = $("#box_id :selected").data('wt_pcs');
        $("#box_size").val(box_size);
        $("#ref_id").val(ref_id);
        $("#wt_pcs").val(wt_pcs);
        $("#qty_per_box").val(qty_per_box);

        if($("#entry_type").val() == "Export"){
            if(ref_id == -1){
                $("#qty_per_box").attr("readOnly",false);
            }else{
                $("#qty_per_box").attr("readOnly",true);
                $(".totalQtyNos").trigger('change');
            }
        }else{
            $("#qty_per_box").attr("readOnly",false);
        }
    });

    $(document).on('change','#trans_main_id',function(){
        var order_id = $(this).val();
        $.ajax({
            url : base_url + controller + '/getItemList',
            type : 'post',
            data:{order_id:order_id,item_id:""},
            dataType:'json',
            success:function(data){
                $(".itemList").html("");
                $(".itemList").html(data.itemList);
                $(".itemList").comboSelect();
                
                $("#trans_child_id").val("");
                $("#item_code").val("");
                $("#qty_per_box").val("0");
                $("#total_box").val("0");
                $("#total_qty").val("0");
                $("#box_size").val("");
                $("#ref_id").val("");
                $("#wt_pcs").val("");
                $("#box_id").val("");$("#box_id").comboSelect();
            }
        });
    });

    $(document).on('change','#is_final',function(){
        var status = $(this).val();
        if(status == 0){
            $('.finalPacking').hide();
            $("#batchData").html("");
        }else{
            $('.finalPacking').show();
            /* var id = $("#item_id :selected").val();
            $.ajax({
                url: base_url + controller + '/batchWiseItemStock',
                data: {item_id:id,trans_id:"",batch_data:""},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#batchData").html(data.batchData);
                }
            }); */
        }
    });

    $(document).on('change',"#item_id",function(){	
        var id = $(this).val();
        var trans_child_id = $(".itemList :selected").data('trans_child_id');
        $("#trans_child_id").val(trans_child_id);
        $("#item_code").val($("#item_idc").val());
        var status = $("#is_final :selected").val();
        if(status == "1" && $("#entry_type").val() != "Export"){
            if(id){
                $.ajax({
                    url: base_url + 'packing/batchWiseItemStock',
                    data: {item_id:id,trans_id:"",batch_data:""},
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $('#totalQty').html("0");
		                $("#packing_qty").val(0);
                        $("#batchData").html(data.batchData);
                    }
                });
            }
        }
        // $("#packing_type").val("");
        // $("#packing_type").comboSelect();
        $("#packing_type").trigger('change');
    });

    $(document).on('keyup change','.totalQtyNos',function(){
        var qty_per_box = $("#qty_per_box").val();
        var total_box = $("#total_box").val();
        
        qty_per_box = (qty_per_box != 0 || qty_per_box != "")?qty_per_box:0;
        total_box = (total_box != 0 || total_box != "")?total_box:0;
        
        var total_qty = parseFloat((parseFloat(qty_per_box) * parseFloat(total_box))).toFixed(3);
        $("#total_qty").val(total_qty);
    });

    $(document).on('keyup change',".batchQty",function(){	
        
		var oldpqty = 0;//$("#oldpqty").val();	
		var batchQtyArr = $(".batchQty").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        batchQtySum += parseFloat(oldpqty);
        
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#packing_qty").val(batchQtySum.toFixed(3));
		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".packing_qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");
            var sum = parseFloat(batchQtySum) - parseFloat(batchQty) + parseFloat(oldpqty);
			$('#totalQty').html(sum);
		    $("#qty").val(sum);
			$(".bQty"+id).val(0);
		}
	});

    $(document).on('click','.saveItem',function(){
        
        var fd = $('#packingItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            if(v.name != "batch_number[]" && v.name != "location[]" && v.name != "batch_qty[]"){
                formData[v.name] = v.value;
            }   
        });
        var batch_quantity = $("#packingItemForm input[name='batch_qty[]']").map(function(){return $(this).val();}).get();
		var batch_number = $("#packingItemForm input[name='batch_number[]']").map(function(){return $(this).val();}).get();
		var location = $("#packingItemForm input[name='location[]']").map(function(){return $(this).val();}).get();
        $("#packingItemForm .error").html("");
        var valid = 1;
        if($("#entry_type :selected").val() == "Export"){
            if(formData.trans_main_id == ""){
                $(".trans_main_id").html('Sales Order No is required.'); valid = 0;
            }
            if(formData.package_no == "" || formData.package_no == "Self Packing"){
                $("#package_no").val("");
                $(".package_no").html('Package No is required.'); valid = 0;
            }
            if(formData.wt_pcs == ""){
                $(".wt_pcs").html('Net Weight Per Pcs is required.'); valid = 0;
            }
    
            if(formData.packing_wt == ""){
                $(".packing_wt").html('Pcaking Weight is required.'); valid = 0;
            }
            
            if(formData.packing_type == ""){
                $(".packing_type").html('Packing Type is required.');
            }
        }else{
            formData.package_no = "Self Packing"; 
        }
        if(formData.box_id == ""){
            $(".box_id").html('Packing Material is required.'); valid = 0;
        }
        if(formData.item_id == ""){
            $(".item_id").html('Product is required.'); valid = 0;
        }
        if(formData.qty_per_box == ""){
            $(".qty_per_box").html('Qty Per Box is required.'); valid = 0;
        }
        if(formData.total_box == ""){
            $(".total_box").html('Total Box is required.'); valid = 0;
        }        
         if($("#is_final").val() == 1){
            /*if(formData.packing_qty == "" || parseFloat(formData.packing_qty) == 0){
                $(".packing_qty").html('Batch details is required.'); valid = 0;
            }
            if(parseFloat(formData.packing_qty) != 0 && parseFloat(formData.packing_qty) != parseFloat(formData.total_qty)){
                $(".packing_qty").html('Batch Qty and Total Qty do not match.'); valid = 0;
            }*/
        } 
        
        if(valid){
            
            var net_wt = 0; var gross_wt = 0;
            formData.wooden_wt = (parseFloat(formData.wooden_wt) > 0)?(parseFloat(formData.wooden_wt).toFixed(3)):0;
            formData.wt_pcs = (parseFloat(formData.wt_pcs) > 0)?(parseFloat(formData.wt_pcs).toFixed(3)):0;
            formData.packing_wt = (parseFloat(formData.packing_wt) > 0)?(parseFloat(formData.packing_wt ).toFixed(3)):0;
            net_wt = parseFloat(parseFloat(formData.total_qty) * parseFloat(formData.wt_pcs)).toFixed(3);            
            gross_wt = parseFloat(parseFloat(net_wt) + parseFloat(formData.packing_wt) + parseFloat(formData.wooden_wt)).toFixed(3);
            formData.net_wt = net_wt;
            formData.gross_wt = gross_wt;
            var batch_data = {};
            if(batch_quantity){
                var i=0;
                $.each(batch_quantity,function(key,bt_qty){
                    if(parseFloat(bt_qty) > 0){
                        batch_data[i] = {batch_qty:bt_qty,batch_no:batch_number[key],location_id:location[key]};
                        i++;
                    }                    
                });
            }
            formData.batch_data = JSON.stringify(batch_data);
            AddRow(formData); 
       
            $("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
            $("#packing_qty").val(0);
            $("#row_index").val('');
            $("#totalQty").html(0);
            if($(this).data('fn') == "save"){
                $("#packingItemForm .single-select").comboSelect();
                $("#box_id").val("");
                $("#total_box").val("");
                $("#total_qty").val("");
                $("#wt_pcs").val("");
                $("#wooden_wt").val("");
                $("#box_size").val("");
            }else if($(this).data('fn') == "save_close"){
                $('#packingItemForm #id').val("");
                $("#packingItemForm #row_index").val("");
                $('#packingItemForm #trans_child_id').val("0");
                $('#packingItemForm #item_code').val("");
                $('#packingItemForm #ref_id').val("");
                $('#packingItemForm #packing_type').val("1");
                
                $('#packingItemForm')[0].reset();
                $("#packingItemForm .single-select").comboSelect();
                $("#itemModel").modal('hide');
            } 
        }
    }); 

    $(document).on('click','.btn-close',function(){
        $('#packingItemForm #id').val("");
        $("#packingItemForm #row_index").val("");
        $('#packingItemForm #trans_child_id').val("0");
        $('#packingItemForm #item_code').val("");
        $('#packingItemForm #ref_id').val("");
        $('#packingItemForm #packing_type').val("1");
        
        $('#packingItemForm')[0].reset();
        $("#packingItemForm .single-select").comboSelect();
        $("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
        $("#packingItemForm .error").html("");
    });
});

function AddRow(data) {
	$('table#packingItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "packingItems";
	
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
	
    var transIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][id]",value:data.id});	
    var boxSizeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][box_size]",value:data.box_size});
    var transChildIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][trans_child_id]",value:data.trans_child_id});
    var itemCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_code]",value:data.item_code});
    var transMainIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][trans_main_id]",value:data.trans_main_id});
    var packageNoInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][package_no]",value:data.package_no});
    var boxIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][box_id]",value:data.box_id});
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_id]",value:data.item_id});
	var qtyPerBoxInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][qty_per_box]",value:data.qty_per_box});
	var totalBoxInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][total_box]",value:data.total_box});
	var totalQtyInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][total_qty]",value:data.total_qty});
	var wtPcsInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][wt_pcs]",value:data.wt_pcs});
	var packingWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][packing_wt]",value:data.packing_wt});
	var woodenWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][wooden_wt]",value:data.wooden_wt});
	var packingQtyInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][packing_qty]",value:data.packing_qty});
	var netWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][net_wt]",value:data.net_wt});
	var grossWtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][gross_wt]",value:data.gross_wt});
	var batchDataInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][batch_data]",value:data.batch_data});
    var refIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][ref_id]",value:data.ref_id});
    var packingTypeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][packing_type]",value:data.packing_type});
	
    //----------------------
    cell = $(row.insertCell(-1));
	cell.html(data.package_no);
    cell.append(transIdInput);
	cell.append(packageNoInput);
	cell.append(transMainIdInput);
	cell.append("<div class='error batch_data_"+countRow+"'></div>");

    cell = $(row.insertCell(-1));
	cell.html(data.box_size);
	cell.append(boxSizeInput);
	cell.append(boxIdInput);
	cell.append(refIdInput);
	cell.append(packingTypeInput);
    cell.append("<div class='error box_id_"+countRow+"'></div>");
    
    cell = $(row.insertCell(-1));
	cell.html(data.item_code);
	cell.append(itemCodeInput);
	cell.append(itemIdInput);
	cell.append(transChildIdInput);
	
	
	cell = $(row.insertCell(-1));
	cell.html(data.qty_per_box);
	cell.append(qtyPerBoxInput);
	
	
	cell = $(row.insertCell(-1));
	cell.html(data.total_box);
	cell.append(totalBoxInput);	
    cell.append("<div class='error total_box_"+countRow+"'></div>");

	cell = $(row.insertCell(-1));
	cell.html(data.total_qty);
    cell.append(totalQtyInput);
    cell.append(packingQtyInput);
    cell.append(batchDataInput);
    cell.append("<div class='error batch_qty_"+countRow+"'></div>");

    cell = $(row.insertCell(-1));
	cell.html(data.wt_pcs);
    cell.append(wtPcsInput);

    cell = $(row.insertCell(-1));
	cell.html(data.net_wt);
    cell.append(netWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.packing_wt);
    cell.append(packingWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.wooden_wt);
    cell.append(woodenWtInput);

    cell = $(row.insertCell(-1));
	cell.html(data.gross_wt);
    cell.append(grossWtInput);
	
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
	/* claculateColumn(); */
};

function Edit(data,button){   
    $('#packingItemForm #id').val("");
    $("#packingItemForm #row_index").val("");
    $('#packingItemForm #trans_child_id').val("0");
    $('#packingItemForm #item_code').val("");
    $('#packingItemForm #ref_id').val("");
    $('#packingItemForm #packing_type').val("1");
        
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    var batchData = ''; var item_id = ''; var trans_main_id = "";var box_id = "";
	$.each(data,function(key, value) { 
        if(key == "batch_data"){
            batchData=value;
        }else if(key == "item_id"){
            item_id = value;
        }else if(key == "trans_main_id"){
            trans_main_id = value;
        }else if(key == "box_id"){
            box_id = value;
        }else{
            $("#packingItemForm #"+key).val(value);
        } 
    }); 		   
	$("#packingItemForm #row_index").val(row_index);
    $(".btn-save").hide();

    $(".itemDiv").removeClass("col-md-5");
    $(".itemDiv").addClass("col-md-12");

    var status = $("#is_final").val();
    
    $("#qty_per_box").attr("readOnly",false);
    if($("#entry_type").val() == "Export"){   
        $('.finalPacking').hide();  
        $(".itemDiv").removeClass("col-md-12");
        $(".itemDiv").addClass("col-md-5");  
        
        if(data.ref_id == -1){
            $("#qty_per_box").attr("readOnly",false);
        }else{
            $("#qty_per_box").attr("readOnly",true);
        }
        /* if(status == 0){
            $('.finalPacking').hide();
        }else{
            $('.finalPacking').show();
        } */
        $.ajax({
            url : base_url + controller + '/getSalesOrderNoListForPacking',
            type : 'post',
            data:{order_id:trans_main_id},
            dataType:'json',
            success:function(data){
                $("#trans_main_id").html("");
                $("#trans_main_id").html(data.orderNoList);
                $("#trans_main_id").comboSelect();
            }
        });
    } 

    var order_id = trans_main_id;
    $.ajax({
        url : base_url + controller + '/getItemList',
        type : 'post',
        data:{order_id:order_id,item_id:item_id},
        dataType:'json',
        success:function(data){
            $(".itemList").html("");
            $(".itemList").html(data.itemList);
            $(".itemList").comboSelect();
            
            var trans_child_id = $(".itemList :selected").data('trans_child_id');
            $("#trans_child_id").val(trans_child_id);
        }
    });

    

    if(status == "1" && $("#entry_type").val() != "Export"){
        var id = item_id;
        if(id){
            $("#batchData").html('<tr><td class="text-center bg-light" colspan="5">Loading...</td></tr>');
            batchData = JSON.parse(batchData);
            $.ajax({
                url: base_url + 'packing/batchWiseItemStock',
                data: {item_id:id,trans_id:$("#packingItemForm #id").val(),batch_data:batchData},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $('#totalQty').html("0");
                    $("#packing_qty").val(0);
                    $("#batchData").html(data.batchData);
                    $(".batchQty").trigger('change');
                }
            });
        }
    }

    $.ajax({
        url:base_url + controller + '/getPackingMaterial',
        type:'post',
        data:{packing_type:data.packing_type,item_id:data.item_id,ref_id:data.ref_id,box_id:data.box_id},
        dataType:'json',
        success:function(data){
            $("#box_id").html("");
            $("#box_id").html(data.material_options);
            $("#box_id").comboSelect();
        }
    });

    $("#packingItemForm .single-select").comboSelect();
    
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#packingItems tbody tr:last').index() + 1;
	if(countTR == 0){		
		$("#tempItem").html('<tr id="noData"><td colspan="13" align="center">No data available in table</td></tr>');
	}	
	/* claculateColumn(); */
};

function savePacking(formId){
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