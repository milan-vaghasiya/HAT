$(document).ready(function(){
	$(document).on('change','#material_type',function(){
		var material_type = $(this).val();
		$.ajax({
			url: base_url + controller + "/getItemOptions",
			type: "post",
			data : {material_type:material_type},
			dataType:"json",
			success:function(response){
				$("#dispatch_item_id").html("");
				$("#dispatch_item_id").html(response.item_options);
				$("#dispatch_item_id").comboSelect();

				$("#batch_no").html('<option value="">Select Batch No.</option>');
				$("#batch_no").comboSelect();
				$("#dispatch_qty").val("");
				$("#batch_stock").val("");
				$("#batch_qty").val("");
			}
		});		
	});

	$(document).on("change","#location_id",function(){
		var itemId = $("#dispatch_item_id").val();
        var location_id = $(this).val();
		$(".location_id").html("");
		$(".dispatch_item_id").html("");
		$("#batch_stock").val("");
		
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				$(".dispatch_item_id").html("Issue Item name is required.");
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

    $(document).on("change","#dispatch_item_id",function(){
        var itemId = $(this).val();
        var location_id = $("#location_id").val();
		$(".location_id").html("");
		$(".dispatch_item_id").html("");
		$("#batch_stock").val("");
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				//$(".dispatch_item_id").html("Issue Item name is required.");
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
					$("#batch_no").comboSelect();
					setTimeout(function(){ $("#batch_no").trigger('change'); }, 10);
				}
			});
		}
    });

	$(document).on('change',"#batch_no",function(){
		$("#batch_stock").val("");
		$("#batch_stock").val($("#batch_no :selected").data('stock'));
	});

	$(document).on('click','.addRow',function(){
		var dispatch_date = $("#dispatch_date").val();
		var material_type = $("#material_type").val();
		var collected_by = $("#collected_by").val();
		var job_card_id = $("#job_card_id").val();
		var job_card_idc = $("#job_card_idc").val();
		var dept_id = $("#dept_id").val();
		var dept_idc = $("#dept_idc").val();
		var process_id = $("#process_id").val();
		var dispatch_item_id = $("#dispatch_item_id").val();
		var dispatch_item_name = $("#dispatch_item_idc").val();
		var dispatch_qty = $("#dispatch_qty").val();
		var location_id = $("#location_id").val();
		var store_name = $("#location_id :selected").data('store_name');
		var location = $("#location_id :selected").text();
		var location_name = "[ "+store_name+" ] "+location;
		var batch_no = $("#batch_no").val();
		var stock = $("#batch_stock").val();
		var qty = $("#batch_qty").val();
		
		$(".location_id").html("");
		$(".batch_no").html("");
		$(".batch_qty").html("");
		if(dispatch_item_id == "" || location_id == "" || batch_no == "" || qty == "" || qty == "0" || qty == "0.000"){
			if(dispatch_item_id == ""){
				$(".dispatch_item_id").html("Item Name is required.");
			}
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

			var qtySum = 0;
			$(".qtyTotal").each(function(){
				qtySum += parseFloat($(this).text());
			});
			qtySum += parseFloat(qty);

			if(parseFloat(qty) > parseFloat(stock)){
				$(".batch_qty").html("Stock not avalible.");
			}else{				

				var post = {id:"",dispatch_date:dispatch_date,material_type:material_type,collected_by:collected_by,job_card_id:job_card_id,job_card_idc:job_card_idc,dept_id:dept_id,dept_idc:dept_idc,process_id:process_id,dispatch_item_id:dispatch_item_id,dispatch_item_name:dispatch_item_name,dispatch_qty:dispatch_qty,location_id:location_id,location_name:location_name,batch_no:batch_no,qty:qty};						
				addRow(post);
				$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));
				$("#collected_by").val("");
				$("#job_card_id").val("");$("#job_card_id").comboSelect();				
				//$("#dept_id").val(""); 
				$("#dept_id").comboSelect();
				$("#process_id").val(""); //$("#process_id").comboSelect();
				$("#dispatch_item_id").val("");$("#dispatch_item_id").comboSelect();
				// $("#location_id").val("");$("#location_id").select2({dropdownParent: $('.model-select2').parent()});
				$("#batch_no").val("");$("#batch_no").comboSelect();				
				$("#batch_stock").val("");
				$("#batch_qty").val("");
			}
		}
	});
});

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

	var itemDataInput = $("<input />",{type:"hidden",name:"item_data[]",value:JSON.stringify(data)});
	cell = $(row.insertCell(-1));
	cell.html(data.dispatch_item_name);
	cell.append(itemDataInput);
	
	cell = $(row.insertCell(-1));
	cell.html(data.job_card_idc);

	cell = $(row.insertCell(-1));
	cell.html(data.dept_idc);

	cell = $(row.insertCell(-1));
	cell.html(data.collected_by);

	cell = $(row.insertCell(-1));
	cell.html(data.location_name);

	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.attr('class','qtyTotal');

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
		qtySum += parseFloat($(this).text());
	});
	qtySum -= parseFloat(qty);
	$("#dispatch_qty").val(parseFloat(qtySum).toFixed(3));

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
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
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
	                		initTable(); $(".modal").modal('hide');
	                		toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
	                	}else{
	                		initTable(); $(".modal").modal('hide');
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
		data: {product_id:data.id,job_card_id:data.job_card_id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
				
	});
}
