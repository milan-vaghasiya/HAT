$(document).ready(function(){
	$("#party_idc").attr('autocomplete','off');
	$("#item_idc").attr('autocomplete','off');

	$(document).on('change',"#job_category",function(){
		var job_no = $("#job_category :selected").data('job_no');
		$("#job_no").val(job_no);
	});

	$(document).on('click','.btn-request',function(){
		var functionName = $(this).data("function");
		var id = $(this).data('id');
		$.ajax({ 
            type: "GET",   
            url: base_url + controller + '/' + functionName,   
            data: {id:id}
        }).done(function(response){
			$("#material-request").modal();
			$("#material-request .modal-body").html(response);
			$("#material-request .scrollable").perfectScrollbar({suppressScrollX: true});
			setPlaceHolder();
			$(".single-select").comboSelect();
        });
	});

    $(document).on('change',"#order_date",function(){
        $("#delivery_date").val($(this).val());
        $("#delivery_date").attr('min',$(this).val());
    });

	$(document).on('click','.materialReceived',function(){
		var id = $(this).data('id');
		var status = $(this).data('val');

		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to received this Job Material?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/materialReceived',
							data: {id:id,mr_status:status},
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
									initTable(); 
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

	$(document).on('click','.changeOrderStatus',function(){
		var id = $(this).data('id');
		var status = $(this).data('val');
		var msg = "";
		if(status == 1){
			msg = "Start";
		}else if(status == 3){
			msg = "Hold";
		}else if(status == 2){
			msg = "Restart";
		}else if(status == 5){
			msg = "Close";
		}else if(status == 4){
			msg = "Reopen";
		}

		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+msg+' this Job Card?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/changeJobStatus',
							data: {id:id,order_status:status},
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
									initTable(); 
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

	$(document).on('change','#party_id',function(){
		var id = $(this).val();
        $("#sales_order_id").val("");
		$("#sales_order_id").comboSelect();
		$("#item_id").html('<option value="">Select Product</option>');
		$("#item_id").comboSelect();
		$("#qty").val('0');
		$("#job_category option[value='0']").removeAttr("disabled");
		$("#job_category option[value='1']").removeAttr("disabled");

		$.ajax({
			url:base_url + controller + "/customerSalesOrderList",
			type:'post',
			data:{party_id:id},
			dataType:'json',
			success:function(data){
				$("#sales_order_id").html(data.options);
				$("#sales_order_id").comboSelect();
			}
		});
		if(id == 0){
			$.ajax({
				url:base_url + controller + "/getProductList",
				type:'post',
				data:{sales_order_id:0,product_id:""},
				dataType:'json',
				success:function(data){
					$("#item_id").html(data.htmlData);
					$("#item_id").comboSelect();
					$("#processDiv").hide();
					$("#processData").html("");
				}
			});
		}
	});

    $(document).on('change','#sales_order_id',function(){
        var id = $(this).val();
		$("#qty").val('0');
		$("#job_category option[value='0']").removeAttr("disabled");
		$("#job_category option[value='1']").removeAttr("disabled");

		$.ajax({
			url:base_url + controller + "/getProductList",
			type:'post',
			data:{sales_order_id:id,product_id:""},
			dataType:'json',
			success:function(data){
				$("#item_id").html(data.htmlData);
				$("#item_id").comboSelect();
				$("#processDiv").hide();
                $("#processData").html("");
				if(data.trans_date != ''){$("#job_date").attr('min',data.trans_date);}
			}
		});
    });
    
    $(document).on("change","#item_id",function(){
		var item_id = $(this).val();
		var deliveryDate = $("#item_id :selected").data('delivery_date');
		var jobType = $("#item_id :selected").data('order_type');
		//alert('DD : '+deliveryDate+' JT : '+jobType);
		$("#delivery_date").val(deliveryDate);
		$("#job_category").val(jobType);
		$("#qty").val('0');
		
		if(jobType == 0){
			$("#job_category option[value='0']").removeAttr("disabled");
			$("#job_category option[value='1']").attr("disabled","disabled");
		}else{
			$("#job_category option[value='1']").removeAttr("disabled");
			$("#job_category option[value='0']").attr("disabled","disabled");
		}
		
		var job_no = $("#job_category :selected").data('job_no');
		$("#job_no").val(job_no);

        $(".item_id").html("");
        if(item_id == ""){
            $("#processDiv").hide();
            $(".item_id").html("Please select product name.");
        }else{
            $.ajax({
                url:base_url + controller + "/getProductProcess",
                type:'post',
                data:{product_id:item_id},
                dataType:'json',
                success:function(data){
                    $("#processDiv").show();
                    $("#processData").html(data.htmlData);
                }
            });
        }
	});
	
	$(document).on("click",".viewLastActivity",function(){
		var trans_id = $(this).data('trans_id'); 
		var job_no = $(this).data('job_no'); 
		if(trans_id){
            $.ajax({
                url:base_url + controller + "/getLastActivitLog",
                type:'post',
                data:{trans_id:trans_id},
                dataType:'json',
                success:function(data){
                    $("#lastActivityModal").modal();
					$("#jobNo").html(job_no);
					$("#activityData").html(data.tbody);
                }
            });
        }
	});
	
	$(document).on("click",".saveJobQty",function(){
		var job_card_id = $("#job_card_id").val();
		var log_date = $("#log_date").val();
		var log_type = $("#log_type").val();
		var qty = $("#qty").val();
		$(".error").html("");
		
		var IsValid = 1;
		if(qty == 0 || qty == ""){$(".qty").html("Quantity is required."); IsValid = 0;}
		if(log_date == null || log_date == ""){$(".log_date").html("Date is required."); IsValid = 0;}

		if(IsValid){
			$.ajax({
			url:base_url + controller +'/saveJobQty',
			type:'post',
			data:{id:'',job_card_id:job_card_id,log_date:log_date,qty:qty,log_type:log_type},
			dataType:'json',
			success:function(data)
			{
				if(data.status===0){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else if(data.status==1){
					$("#joblogData").html("");
					$("#joblogData").html(data.tbody);
					$("#qty").val(""); initTable(1);
					toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": false, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}else{
					toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
				
			}
		});
		}
	});
});

function requiredTest(data){
	var button = "";
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/getRequiredTest',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveRequiredTest('"+data.form_id+"');");
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
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
	});
}

function saveRequiredTest(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveRequiredTest',
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
			initTable(); $(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(); $(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function materialRequest(formId,fnSave){
	var mismatchData = $("#"+formId+" #mismatch_data").val();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	if(mismatchData == 1){
		$.confirm({
			title: 'Confirm!',
			content: 'Job Bom and Product Bom are mismatch. Are you sure want to send material request?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/' + fnSave,
							data:fd,
							type: "POST",
							processData:false,
							contentType:false,
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
	}else{
		$.ajax({
			url: base_url + controller + '/' + fnSave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
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
}

function trashJobUpdateQty(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteJobUpdateQty',
						data: send_data,
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
								$("#joblogData").html("");
								$("#joblogData").html(data.tbody);
								initTable(1); initMultiSelect();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": false, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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
}