$(document).ready(function(){
	$(document).on('change','#material_type',function(){
		var material_type = $(this).val();
		$.ajax({
			url: base_url + controller + "/getItemOptions",
			type: "post",
			data : {material_type:material_type},
			dataType:"json",
			success:function(response){
				$("#item_id").html("");
				$("#item_id").html(response.item_options);
				$("#item_id").comboSelect();

				$("#batch_no").html('<option value="">Select Batch No.</option>');
				//$("#batch_no").comboSelect();
				$("#batch_no").select2();
				$("#dispatch_qty").val("");
				$("#batch_stock").val("");
				$("#batch_qty").val("");
			}
		});		
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
					$("#batch_no").select2();
				}
			});
		}
	});

    $(document).on("change","#item_id",function(){
        var itemId = $(this).val();
        var location_id = $("#location_id").val();
		$(".location_id").html("");
		$(".item_id").html("");
		$("#batch_stock").val("");
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				//$(".item_id").html("Issue Item name is required.");
			}
			if(location_id == ""){
				//$(".location_id").html("Location is required.");
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
			    	$("#batch_no").select2();
					//$("#batch_no").comboSelect();
				}
			});
			$("#tempItem").html('<tr id="noData"><td class="text-center" colspan="5">No Data Found</td></tr>');
		}
    });

	$(document).on('change',"#batch_no",function(){
		$("#batch_stock").val("");
		$("#batch_stock").val($("#batch_no :selected").data('stock'));
	});
	
	$(document).on('change','#ref_id',function(){
		$('.general_batch_no').html('');
		if($(this).val() > 0){
			$('.general_batch_no').html('Please remove first all batch.');
			$("#ref_id").val("");
			$("#ref_id").comboSelect();
		}
	});

	$(document).on('click','.addRow',function(){
		var location_id = $("#location_id").val();
		var store_name = $("#location_id :selected").data('store_name');
		var location = $("#location_id :selected").text();
		var location_name = "[ "+store_name+" ] "+location;
		var batch_no = $("#batch_no").val();
		var stock = $("#batch_stock").val();
		var qty = $("#batch_qty").val();
		var count_item = $("#count_item").val();
		var ref_id = $("#ref_id").val();
		
		$(".location_id").html("");
		$(".batch_no").html("");
		$(".batch_qty").html("");
		$('.general_batch_no').html("");
		/*if(ref_id > 0 && count_item > 0 || ref_id != "" && count_item > 0){
			$('.general_batch_no').html('You cannot add more than one batch.');
		}else{*/
    		if(location_id == "" || batch_no == "" || qty == "" || qty == "0" || qty == "0.000"){
    			if(location_id == ""){
    				$(".location_id").html("Location is required.");
    			}
    			if(batch_no == ""){
    				$(".batch_no").html("Batch No. is required.");
    			}
    			if(qty == "" || qty == "0" || qty == "0.000"){
    				$(".batch_qty").html("Qty. is required.");
    			}
    		}else{
    			var batchNos = $("input[name='batch_no[]']").map(function(){return $(this).val();}).get();
    			/* if($.inArray(batch_no,batchNos) >= 0){
    				$(".batch_no").html("Batch No. already added.");
    			}else {  */
    				if(parseFloat(qty) > parseFloat(stock)){
    					$(".batch_qty").html("Stock not avalible.");
    				}else{
    					var qtySum = 0;
    					$(".qtyTotal").each(function(){
    						qtySum += parseFloat($(this).val());
    					});
    					qtySum += parseFloat(qty);
    					var pendingQty = $("#pending_qty").val();
    					var reqQty = $("#req_qty").val();
    					if(parseFloat(reqQty) != 0 && parseFloat(qtySum).toFixed(3) > parseFloat(reqQty).toFixed(3)){
    						$(".batch_qty").html("Invalid Issue qty.");
    					}else{
    						var post = {id:"",batch_no:batch_no,qty:qty,location_id:location_id,location_name:location_name};						
    						addRow(post);
    						$("#count_item").val(parseFloat(count_item) + 1);
    						if(parseFloat(reqQty) != 0){
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
		//}
	});
	
	$(document).on('change','#item_type',function(){initDispatchTable($(this).val());});

	//Created By Meghavi 01-12-2021
	$(document).on('click','.changeOrderStatus',function(){
		var id = $(this).data('id');
		var md_status = $(this).data('val');
	
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to Close this Job Material Request ?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/closeMaterialRequest',
							data: {id:id,md_status:md_status},
							type: "POST",
							dataType:"json",
							success:function(data)
							{
								if(data.status==0)
								{
									toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
								else
								{
									initTable(1); 
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
							}
						});
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function(){
	
					}
				}
			}
		});
	});	
});

function initDispatchTable(item_type){
	$('.ssTable').DataTable().clear().destroy();
    var tableOptions = {pageLength: 25,'stateSave':false};
    var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
    var dataSet = {material_type:item_type};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
}

function addRow(data){
	$('table#issueItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "issueItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.location_name + '<input type="hidden" name="location_id[]" value="'+data.location_id+'">');

	cell = $(row.insertCell(-1));
	cell.html(data.batch_no + '<input type="hidden" name="batch_no[]" value="'+data.batch_no+'"><input type="hidden" name="trans_id[]" value="'+data.id+'" />');

	cell = $(row.insertCell(-1));
	cell.html(data.qty + '<input type="hidden" class="qtyTotal" name="batch_qty[]" value="'+data.qty+'">');

	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this,'"+data.qty+"');");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
}

function Remove(button,qty) {
	var qtySum = 0;
	$(".qtyTotal").each(function(){
		qtySum += parseFloat($(this).val());
	});
	qtySum -= parseFloat(qty);
	var pendingQty = $("#pending_qty").val();
	var reqQty = $("#req_qty").val();
	if(parseFloat(reqQty) != 0){
		$("#pending_qty").val(parseFloat(parseFloat(pendingQty) + parseFloat(qty)).toFixed(3));
	}
	$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));
	$("#count_item").val(parseFloat($("#count_item").val()) - 1);

    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#issueItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#issueItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#issueItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="5" align="center">No data available in table</td></tr>');
	}	
};

function dispatch(data){
	var button = "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/dispatch',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();		
		initMultiSelect();setPlaceHolder();
	});
}

function request(id){
    $.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to send purchase request?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
	                $.ajax({
	                	url: base_url + 'jobMaterialDispatch/purchaseRequest',
	                	data:{
                            'id':'',
                            'dispatch_id':id
                        },
	                	type: "POST",
	                	dataType:"json",
	                }).done(function(data){
	                	if(data.status===0){
	                		$(".error").html("");
	                		$.each( data.message, function( key, value ) {
	                			$("."+key).html(value);
	                		});
	                	}else if(data.status==1){
	                		initTable(1); $(".modal").modal('hide');
	                		toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
	                	}else{
	                		initTable(1); $(".modal").modal('hide');
	                        toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
	                	}
                    
	                });
                }
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function consumption(data){
	var button = "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/consumption',   
		data: {product_id:data.id,ref_id:data.ref_id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
				
	});
}
