$(document).ready(function(){
	productionTable();
    
	$(document).on('change',"#material_used_id",function(){
	    console.log($(this).val());
		if($(this).val() != ""){
			$("#batch_no").val("");$("#batch_no").val($(this).find(":selected").data('batch_no'));
			$("#issue_qty").val("");$("#issue_qty").val($(this).find(":selected").data('issue_qty'));
			$("#used_qty").val("");$("#used_qty").val($(this).find(":selected").data('used_qty'));
			$("#wp_qty").val("");$("#wp_qty").val($(this).find(":selected").data('wp_qty'));

			var outQty = parseFloat($('#out_qty').val());
			$(".out_qty").html("");$("#req_qty").val("");var req_qty = 0;var wp_qty = 0;var stockQty = 0;
			if(outQty=="" || outQty==null || isNaN(outQty)){
				$(".out_qty").html("Out Qty. is required.");
				$(this).val("");
				//$(this).select2({ dropdownParent: $('.model-select2').parent() });
			}
			else
			{
				wp_qty = parseFloat($("#wp_qty").val());
				req_qty = parseFloat(outQty) * parseFloat(wp_qty);
				stockQty = parseFloat($("#issue_qty").val()) - parseFloat($("#used_qty").val());
				
				if(stockQty < req_qty){$(".batch_no").html("Stock Not Available");}
				
				$("#req_qty").val(req_qty);
				$(".batchMaterial").html("Stock Qty : " + stockQty);
				$(".reqMaterial").html("Required Qty : " + req_qty);
			}
		}else{
			$(".batchMaterial").html("");
			$(".reqMaterial").html("");
		}
    });
    
    $(document).on('keyup change',"#out_qty",function(){
		$("#material_used_id").val("");
		//$("#material_used_id").select2({ dropdownParent: $("#material_used_id").parent() });
		$(".batchMaterial").html("");
		$(".reqMaterial").html("");
	});

	/*$(document).on('change',"#process_id",function(){
        getProcessWiseData();
    });*/

	$(document).on('change',"#vendor_id",function(){
		var vendor_id = $(this).val();
		var product_id = $("#product_id").val();
		var process_id = $("#out_process_id").val();
		var job_card_id = $("#outWard #job_card_id").val();
		if(vendor_id == "0"){
			$("#job_order_id").html('<option value="">Select Job Order No.</option>');
			$("#job_order_id").comboSelect();
			//$("#jobProcessSelect").html("");
			$("#job_process_ids").val("");
			$("#job_process_ids").comboSelect();
			//reInitMultiSelect();
		}else{
			$.ajax({
				url: base_url + 'production_v2/processApproval/getJobWorkOrderNoList',
				type:'post',
				data:{vendor_id:vendor_id,product_id:product_id,process_id:process_id},
				dataType:'json',
				success:function(data){
					$("#job_order_id").html("");
					$("#job_order_id").html(data.options);
					$("#job_order_id").comboSelect();
				}
			});

			$.ajax({
				url: base_url + 'production_v2/processApproval/getJobWorkOrderProcessList',
				type:'post',
				data:{job_order_id:"",process_id:process_id,job_card_id:job_card_id,vendor_id:vendor_id},
				dataType:'json',
				success:function(data){
					/* $("#job_process_ids").val(data.job_process);
					$("#jobProcessSelect").html("");
					$("#jobProcessSelect").html(data.options);
					reInitMultiSelect(); */

					$("#job_process_ids").html("");
					$("#job_process_ids").html(data.options);
					$("#job_process_ids").comboSelect();
				}
			});
		}
	});

	$(document).on('change','#job_order_id',function(){
		var job_order_id = $(this).val();
		var vendor_id = $("#vendor_id").val();
		var process_id = $("#out_process_id").val();
		var job_card_id = $("#outWard #job_card_id").val();
		
		if(vendor_id == "0"){
			$("#job_order_id").html('<option value="">Select Job Order No.</option>');
			$("#job_order_id").comboSelect();
			//$("#jobProcessSelect").html("");
			$("#job_process_ids").val("");
			$("#job_process_ids").comboSelect();
			//reInitMultiSelect();
		}else{
			$.ajax({
				url: base_url + 'production_v2/processApproval/getJobWorkOrderProcessList',
				type:'post',
				data:{job_order_id:job_order_id,process_id:process_id,job_card_id:job_card_id},
				dataType:'json',
				success:function(data){
					/* $("#job_process_ids").val(data.job_process);
					$("#jobProcessSelect").html("");
					$("#jobProcessSelect").html(data.options);
					reInitMultiSelect(); */

					$("#job_process_ids").html("");
					$("#job_process_ids").html(data.options);
					$("#job_process_ids").comboSelect();
				}
			});
		}
	});

	$(document).on('click','.btn-close',function(){
		window.location.reload();
	});
	
	$(document).on('click','.close',function(){
		window.location.reload();
	});
});

function productionTable(){
    var table = $('#productionTable').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel', 'colvis' ]
	});
	table.buttons().container().appendTo( '#productionTable_wrapper .col-md-6:eq(0)' );
}

function outward(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production_v2/processApproval/processApproved',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		setPlaceHolder();
		initMultiSelect();
	});
}

function saveOutward(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production_v2/processApproval/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#pqty").val(data.pending_qty);
			$("#pending_qty").html(data.pending_qty);

			$(".batchMaterial").html("");
			$(".reqMaterial").html("");
			$("#out_qty").val("");
			$("#in_qty_kg").val("");
			$("#setup_status").val(1);$("#setup_status").comboSelect();
			$("#setter_id").val("");$("#setter_id").comboSelect();
			$("#machine_id").val("");$("#machine_id").comboSelect();
			$("#material_used_id").val("");//$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
			$("#vendor_id").val("0");$("#vendor_id").comboSelect();
			$("#job_order_id").val("");$("#job_order_id").comboSelect();
			//$("#job_process_ids").val("");$("#jobProcessSelect").html("");reInitMultiSelect();
			$("#job_process_ids").html("");$("#job_process_ids").comboSelect();
			$("#remark").val("");

			$("#material_used_id").html("");
			$("#material_used_id").html(data.materialBatch);
			//$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
			$("#batch_no").val("");
			$("#issue_qty").val("0");			
			$("#used_qty").val("0");
			$("#req_qty").val("0");
			$("#wp_qty").val("0");

			$("#outwardTransData").html(data.outwardTrans);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashOutward(id,name='Record'){
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
						url: base_url + 'production_v2/processApproval/delete',
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
								$("#material_used_id").html("");
								$("#material_used_id").html(data.materialBatch);
								//$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
								$("#batch_no").val("");
								$("#issue_qty").val("0");			
								$("#used_qty").val("0");
								$("#req_qty").val("0");
								$("#wp_qty").val("0");

								$("#pqty").val(data.pending_qty);
								$("#pending_qty").html(data.pending_qty);
								$("#outwardTransData").html(data.outwardTrans);
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
}

/* function getProcessWiseData(){
	var process_id = $("#process_id").val();
	var job_id = $("#job_id").val();
	$.ajax({
		url:base_url + "processApproval/getProcessWiseApprovalData",
		type:'post',
		data:{process_id:process_id,job_id:job_id},
		dataType:'json',
		success:function(data){
			$("#productionData").html("");
			$("#productionTable").dataTable().fnDestroy();
			$("#productionData").html(data.html);
			productionTable();
		}
	});
} */

function storeLocation(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production_v2/processApproval/storeLocation',   
		data: {id:data.id,transid:data.transid}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		//$(".select2").select2();
		setPlaceHolder();
		initMultiSelect();
	});
}

function saveStoreLocation(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production_v2/processApproval/saveStoreLocation',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $("#storeLocationData").html(data.htmlData);
			$("#unstoredQty").html(data.unstored_qty);
			//getProcessWiseData();
			$('#'+formId)[0].reset();
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashStockTrans(id,name='Record'){
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
						url: base_url + 'production_v2/processApproval/deleteStoreLocationTrans',
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
								initTable(); $("#storeLocationData").html(data.htmlData);
								$("#unstoredQty").html(data.unstored_qty);
								//getProcessWiseData();
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
}