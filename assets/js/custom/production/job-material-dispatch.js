$(document).ready(function(){
	$(document).on('change','#job_card_id',function(){
		var job_card_id = $(this).val();
		$.ajax({
			url: base_url + controller + "/getJobBomItems",
			type: "post",
			data : {job_card_id:job_card_id},
			dataType:"json",
			success:function(response){
				$("#dispatch_item_id").html("");
				$("#dispatch_item_id").html(response.options);
				$("#dispatch_item_id").comboSelect();

				
			}
		});		
	});

	$(document).on("change","#location_id",function(){
		var itemId = $("#dispatch_item_id :selected").val();
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

    $(document).on("change","#dispatch_item_id",function(){
        var itemId = $(this).val();
		$(".location_id").html("");
		$(".item_id").html("");
		$("#batch_stock").val("");
		$("#location_id").html("");
		$("#location_id").comboSelect();
		$("#batch_no").html("");
		$("#batch_no").comboSelect();

		if(itemId){
			var job_bom_id = $("#dispatch_item_id :selected").data('job_bom_id');
			var req_qty = $("#dispatch_item_id :selected").data('req_qty');
			var issue_qty = $("#dispatch_item_id :selected").data('issue_qty');
			$("#req_qty").val(req_qty);
			$("#pending_qty").val(parseFloat(req_qty)-parseFloat(issue_qty));
			$("#job_bom_id").val(job_bom_id);
			$.ajax({
				url:base_url + controller + '/getLocation',
				type:'post',
				data:{item_id:itemId},
				dataType:'json',
				success:function(data){
					$("#location_id").html("");
					$("#location_id").html(data.options);
					$("#location_id").comboSelect();
				}
			});
			$("#tempItem").html('<tr id="noData"><td class="text-center" colspan="5">No Data Found</td></tr>');
		}
    });

	$(document).on('change',"#batch_no",function(){
		$("#batch_stock").val("");
		$("#batch_stock").val($("#batch_no :selected").data('stock'));
		$("#tc_no").val($("#batch_no :selected").data('tc_no'));
	});

});

function dispatch(data){
	var button = "close";
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

function saveMaterialIssue(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(0); 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#tbodyData").html(data.tbodyData);
            $("#id").val("");
            $("#location_id").val("");
            $("#batch_no").val("");
            $("#stock_qty").val("");
            $("#qty").val("");
			$("#location_id").comboSelect();
			$("#batch_no").comboSelect();
        }else{
			initTable(0);  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function trashMaterial(id,job_bom_id,job_card_id) {
	var send_data = { id:id, job_bom_id:job_bom_id,job_card_id:job_card_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Material?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/delete',
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
								initTable(0); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#tbodyData").html(data.tbodyData);
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
};
