$(document).ready(function () {
	initMultiSelect();
	$(document).on('click', '.addJobStage', function () {
		var jobid = $('#jobID').val();
		var process_id = $('#stage_id').val();
		$(".stage_id").html("");
		if (jobid != "" && process_id != "") {
			$.ajax({
				type: "POST",
				url: base_url + controller + '/addJobStage',
				data: { id: jobid, process_id: process_id },
				dataType: 'json',
				success: function (data) {
					$('#stageRows').html(""); $('#stageRows').html(data.stageRows);
					$('#stage_id').html(""); $('#stage_id').html(data.pOptions); $('#stage_id').comboSelect();
				}
			});
		} else {
			$(".stage_id").html("Stage is required.");
		}
	});

	$(document).on('click', '.removeJobStage', function () {
		var jobid = $('#jobID').val();
		var process_id = $(this).data('pid');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to delete this Stage?',
			type: 'red',
			buttons: {
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function () {
						if (jobid != "" && process_id != "") {
							$.ajax({
								type: "POST",
								url: base_url + controller + '/removeJobStage',
								data: { id: jobid, process_id: process_id },
								dataType: 'json',
								success: function (data) {
									$('#stageRows').html(""); $('#stageRows').html(data.stageRows);
									$('#stage_id').html(""); $('#stage_id').html(data.pOptions); $('#stage_id').comboSelect();
								}
							});
						}
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function () {

					}
				}
			}
		});
	});

	$("#jobStages tbody").sortable({
		items: 'tr',
		cursor: 'pointer',
		axis: 'y',
		dropOnEmpty: false,
		helper: fixWidthHelper,
		start: function (e, ui) { ui.item.addClass("selected"); },
		stop: function (e, ui) {
			ui.item.removeClass("selected");
			var seq = 1;
			$(this).find("tr").each(function () { $(this).find("td").eq(2).html(seq + 1); seq++; });
		},
		update: function () {
			var ids = '';
			$(this).find("tr").each(function (index) { ids += $(this).attr("id") + ","; });
			var lastChar = ids.slice(-1);
			if (lastChar == ',') { ids = ids.slice(0, -1); }
			var jobid = $('#jobID').val();
			var rnstages = $('#rnstages').val();

			$.ajax({
				url: base_url + controller + '/updateJobProcessSequance',
				type: 'post',
				data: { id: jobid, process_id: ids, rnstages: rnstages },
				dataType: 'json',
				global: false,
				success: function (data) { }
			});
		}
	});

	$(document).on('click', ".requiredMaterial", function () {
		var item_id = $(this).data('product_id');
		var productName = $(this).data('product');
		var orderQty = $(this).data('qty');
		var process_id = $(this).data('process_id');
		var process_name = $(this).data('process_name');

		$.ajax({
			url: base_url + controller + '/getProcessWiseRequiredMaterial',
			data: { process_id: process_id, item_id: item_id, qty: orderQty },
			type: "POST",
			dataType: "json",
			success: function (data) {
				if (data.status == 0) {
					swal("Sorry...!", data.message, "error");
				}
				else {
					$("#productName").html(productName);
					$("#processName").html(process_name);
					$("#orderQty").html(orderQty);
					$("#requiredMaterialModal").modal();
					$("#requiredItems").html("");
					$("#requiredItems").html(data.result);
				}
			}
		});
	});

	$(document).on("click", '#addJobBom', function () {
		var form = $('#job_bom_data')[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/saveJobBomItem',
			data: fd,
			type: "POST",
			processData: false,
			contentType: false,
			dataType: "json",
		}).done(function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) { $("." + key).html(value); });
			} else if (data.status == 1) {
				$("#bom_item_id").val(""); $("#bom_item_id").comboSelect();
				$("#bom_qty").val('');
				$("#requiredItems").html("");
				$("#requiredItems").html(data.result);
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			} else {
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}

		});
	});

	$(document).on('click', ".productionTab", function () {
		location.reload();
	});
	$(document).on('click','.btn-close',function(){
		window.location.reload();
	});

	$(document).on("change", "#rejection_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url +  'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").comboSelect();
                }
            });
        } else {
            $("#rej_from").html("<option value=''>Select Rej. From</option>");
            $("#rej_from").comboSelect(); 
        }
    });

    $(document).on("change", "#rework_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + 'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rw_from").html("");
                    $("#rw_from").html(data.rejOption);
                    $("#rw_from").comboSelect();
                }
            });
        } else {
            $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").comboSelect();
        }
    });
	
	$(document).on('click', "#addReworkRow", function () {
        var rw_qty = $("#rw_qty").val();
        var rw_reason = $("#rw_reason :selected").val();
        var rw_reason_text = $("#rw_reason :selected").text();
		var rw_stage = $("#rw_stage :selected").val();
		var rw_stage_text = $("#rw_stage :selected").text();
		var rw_dimension_range = $("#rw_dimension_range :selected").val();
		var rw_dimension_range_text = $("#rw_dimension_range :selected").text();
		var rw_by = $("#rw_by :selected").val();
		var rw_by_text = $("#rw_by :selected").text();
		var rw_process_id = $("#rw_process_id :selected").val();
		var rw_process_id_text = $("#rw_process_id :selected").text();
        var rw_remark = $("#rw_remark").val();

        var valid = 1;

        $(".rw_qty").html("");
        if (parseFloat(rw_qty) <= 0 || rw_qty == '') {
            $(".rw_qty").html("Rework Qty is required.");
            valid = 0;
        }

        $(".rw_reason").html("");
        if (rw_reason == "") {
            $(".rw_reason").html("Rework Reason is required.");
            valid = 0;
        }

        $(".rw_stage").html("");
        if (rw_stage == "") {
            $(".rw_stage").html("Rework Stage is required.");
            valid = 0;
        }

        $(".rw_by").html("");
        if (rw_by == "") {
            $(".rw_by").html("Rework By is required.");
            valid = 0;
        }

		$(".rw_process_id").html("");
        if (rw_process_id == "") {
            $(".rw_process_id").html("Rework Process is required.");
            valid = 0;
        }

        if (valid == 1) {

            var postData = {
                rw_qty: rw_qty,
                rr_reason: rw_reason,
                rr_reason_text: rw_reason_text,
                rr_stage: rw_stage,
                rr_stage_text: rw_stage_text,
                rr_by: rw_by,
                rr_by_text: rw_by_text,
                rw_process_id: rw_process_id,
                rw_process_id_text: rw_process_id_text,
                dimension_range: rw_dimension_range,
                dimension_range_text: rw_dimension_range_text,
				remark : rw_remark
            };


            AddRowRework(postData);
			$("#rw_qty").val("");
			$("#rw_reason").val("");
			$("#rw_reason").comboSelect();
			$("#rw_stage").val("");
			$("#rw_stage").comboSelect();
			$("#rw_dimension_range").val("");
			$("#rw_dimension_range").comboSelect();
			$("#rw_by").val("");
			$("#rw_by").comboSelect();
			$("#rw_process_id").val("");
			$("#rw_process_id").comboSelect();
			$("#rw_remark").val("");
            $("#rw_qty").focus();

        }
    });


    $(document).on('click', "#addRejectionRow", function () {
        var rej_qty = $("#rej_qty").val();
        var rr_reason = $("#rr_reason :selected").val();
        var rr_reason_text = $("#rr_reason :selected").text();
		var rej_type = $("#rej_type :selected").val();
		var rej_type_text = $("#rej_type :selected").text();
		var rr_stage = $("#rr_stage :selected").val();
		var rr_stage_text = $("#rr_stage :selected").text();
		var dimension_range = $("#dimension_range :selected").val();
		var dimension_range_text = $("#dimension_range :selected").text();
		var rr_by = $("#rr_by :selected").val();
		var rr_by_text = $("#rr_by :selected").text();
		var r_remark = $("#r_remark").val();

        var valid = 1;

        $(".rej_qty").html("");
        if (parseFloat(rej_qty) <= 0 || rej_qty == '') {
            $(".rej_qty").html("Rejection Qty is required.");
            valid = 0;
        }

        $(".rr_reason").html("");
        if (rr_reason == "") {
            $(".rr_reason").html("Rejection Reason is required.");
            valid = 0;
        }

        $(".rej_type").html("");
        if (rej_type == "") {
            $(".rej_type").html("Rejection Type is required.");
            valid = 0;
        }

        $(".rr_stage").html("");
        if (rr_stage == "") {
            $(".rr_stage").html("Rejection Stage is required.");
            valid = 0;
        }

		$(".rr_by").html("");
        if (rr_stage == "") {
            $(".rr_by").html("Rejection By is required.");
            valid = 0;
        }

		var opr_mt = $("#opr_mt").val();
        if (valid == 1) {
            var postData = {
                rej_qty:rej_qty,
				rr_reason:rr_reason,
				rr_reason_text:rr_reason_text,
				rej_type:rej_type,
				rej_type_text:rej_type_text,
				rr_stage:rr_stage,
				rr_stage_text:rr_stage_text,
				dimension_range:dimension_range,
				dimension_range_text:dimension_range_text,
				rr_by:rr_by,
				rr_by_text:rr_by_text,
				r_remark:r_remark,
				opr_mt:opr_mt
            };
            AddRowRejection(postData);
            $("#rej_qty").val("");
            $("#rr_reason").val("");
            $("#rr_reason").comboSelect();
            $("#rej_type").val("");
            $("#rr_stage").val("");
            $("#rr_stage").comboSelect();
			$("#dimension_range").val("");
            $("#dimension_range").comboSelect();
			$("#rr_by").val("");
            $("#rr_by").comboSelect();
            $("#r_remark").val("");
            $("#rej_qty").focus();
        }
    });


    $(document).on('click', ".openMaterialReturnModal", function () {
		var modalId = $(this).data('modal_id');
		var processName = $(this).data('process_name');
		var pendingQty = $(this).data('pending_qty');
		var item_name = $(this).data('item_name');
		var item_id = $(this).data('item_id');
		var job_card_id = $(this).data('job_card_id');
		var wp_qty=$(this).data('wp_qty');
        var dispatch_id = $(this).data('dispatch_id');
        var id = $(this).data('id');
		$.ajax({
			type: "POST",
			url: base_url + controller + '/materialReturn',
			data: { id:id,job_card_id: job_card_id,item_id:item_id,processName:processName,pendingQty:pendingQty,item_name:item_name,wp_qty:wp_qty,dispatch_id:dispatch_id}
		}).done(function (response) {
			$("#" + modalId).modal({ show: true });
			$("#" + modalId + ' .modal-title').html('Return Material');
			$("#" + modalId + ' .modal-body').html("");
			$("#" + modalId + ' .modal-body').html(response);
			$("#" + modalId + " .modal-body form").attr('id','returnMaterialForm');
			$("#" + modalId + " .modal-footer .btn-close").show();
			$("#" + modalId + " .modal-footer .btn-save").hide();
			$(".single-select").comboSelect();

			$("#" + modalId + " .scrollable").perfectScrollbar({ suppressScrollX: true });
			setTimeout(function () { initMultiSelect(); setPlaceHolder(); }, 5);
		});
	});

    $(document).on('change','#ref_type',function(){
		var ref_type=$(this).val();
		var scrap_location=$("#scrap_store_id").val();
		if(ref_type == 10){
			$(".location").show();
			$(".batchNo").show();
			$("#location_id option").removeAttr("disabled");
			$("#location_id option[value='"+scrap_location+"']").attr("disabled","disabled");
			$("#location_id").comboSelect();
		}
		if(ref_type == 13){
			$(".location").show();
			$('.batchNo').hide();
			$("#location_id").val(scrap_location);
			$("#location_id option").attr("disabled","disabled");
			$("#location_id option[value='"+scrap_location+"']").removeAttr("disabled");
			$("#location_id").comboSelect();
			
		}
		
		if(ref_type==21){
			$('.location').hide();
			$('.batchNo').hide();
			$("#location_id").val("");
			$("#location_id").comboSelect();
			$("#batch_no").val("");
			$("#batch_no").comboSelect();
		}
	});
    
	$(document).on('change', '#send_to', function() {
		var used_at = $(this).val();
		$.ajax({
			type: "POST",
			url: base_url + controller + '/getHandoverData',
			data: {
				used_at: used_at
			},
			dataType: 'json',
		}).done(function(response) {
			$("#handover_to").html(response.handover);
			$("#handover_to").comboSelect();
		});
	});

	$(document).on("change", "#rr_stage", function() {
        
		var process_id = $(this).find(":selected").val();//data('process_id');
		var part_id = $("#product_id").val();
		var pfc_id = $(this).val();
		var rej_type = $("#rej_type").val();

		if (process_id) {
			var job_card_id = $("#job_card_id").val();
			$.ajax({
				url: base_url + 'production/primaryCFT/getRRByOptions',
				type: 'post',
				data: {
					process_id: process_id,
					part_id: part_id,
					job_card_id: job_card_id,
					pfc_id: pfc_id,
					rej_type: rej_type
				},
				dataType: 'json',
				success: function(data) {
					$("#rr_by").html("");
					$("#rr_by").html(data.rejOption);
					$("#rr_by").comboSelect();

					/* $("#dimension_range").html("");
					$("#dimension_range").html(data.dimOptions);
					$("#dimension_range").comboSelect(); */
				}
			});
		} else {

			$("#rr_by").html("<option value=''>Select Rej. From</option>");
			$("#rr_by").comboSelect();

			/* $("#dimension_range").html("<option value=''>Select</option>");
			$("#dimension_range").comboSelect(); */

		}
	});
	$(document).on("change", "#rej_type", function() {
        
		$("#rr_stage").val("");
		$("#rr_stage").comboSelect();
		$("#rr_by").val("");
		$("#rr_by").comboSelect();

		var rej_type = $("#rej_type").val();
		if(rej_type == 1){
			$('#opr_mt optgroup[label=Operation]').prop('disabled', false); 
			$('#opr_mt optgroup[label=Material]').prop('disabled', true); 
			
		}else{
			$('#opr_mt optgroup[label=Operation]').prop('disabled', true); 
			$('#opr_mt optgroup[label=Material]').prop('disabled', false); 
			
		}
		$("#opr_mt").comboSelect();
		
	});

	$(document).on("change", "#rw_stage", function() {
		//$("#rr_stage").change(function(){
		var process_id = $(this).find(":selected").val();//data('process_id');
		var part_id = $("#product_id").val();
		var pfc_id = $(this).val();
		if (process_id) {
			var job_card_id = $("#job_card_id").val();
			$.ajax({
				url: base_url + 'production/primaryCFT/getRRByOptions',
				type: 'post',
				data: {
					process_id: process_id,
					part_id: part_id,
					job_card_id: job_card_id,
					pfc_id: pfc_id,
				},
				dataType: 'json',
				success: function(data) {
					$("#rw_by").html("");
					$("#rw_by").html(data.rejOption);
					$("#rw_by").comboSelect();

					/* $("#rw_dimension_range").html("");
					$("#rw_dimension_range").html(data.dimOptions);
					$("#rw_dimension_range").comboSelect(); */
				}
			});
		} else {

			$("#rw_by").html("<option value=''>Select Rej. From</option>");
			$("#rw_by").comboSelect();

			/* $("#rw_dimension_range").html("<option value=''>Select</option>");
			$("#rw_dimension_range").comboSelect(); */

		}
	});
	

	$(document).on("keyup",".qtyCal", function(){
        var hold_qty = $("#hold_qty").val() || 0;

		var rejQtyArray = $(".rej_sum").map(function(){return $(this).val();}).get();
		var rej_qty = 0;
		$.each(rejQtyArray,function(){rej_qty += parseFloat(this) || 0;});
		$("#total_rej_qty").val(rej_qty);

		var rwQtyArray = $(".rw_sum").map(function(){return $(this).val();}).get();
		var rw_qty = 0;
		$.each(rwQtyArray,function(){rw_qty += parseFloat(this) || 0;});
		$("#total_rw_qty").val(rw_qty);
        
		var okQty=parseFloat($("#production_qty").val())-rej_qty-rw_qty-hold_qty;      
		$("#out_qty").val(okQty);
    });
});

function fixWidthHelper(e, ui) {
	ui.children().each(function () {
		$(this).width($(this).width());
	});
	return ui;
}

function AddStageRow(data) {
	$('table#purchaseEnqItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "purchaseEnqItems";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	row = tBody.insertRow(-1);

	//Add index cell
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style", "width:5%;");

	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="item_name[]" value="' + data.item_name + '"><input type="hidden" name="trans_id[]" value="' + data.trans_id + '" /><input type="hidden" name="item_remark[]" value="' + data.item_remark + '">');
}


function removeBomItem(id, job_card_id) {
	var send_data = { id: id, job_card_id: job_card_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Bom Item?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function () {
					$.ajax({
						url: base_url + controller + '/deleteBomItem',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function (data) {
							if (data.status == 0) {
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else {
								$("#requiredItems").html("");
								$("#requiredItems").html(data.result);

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function () {

				}
			}
		}
	});
}

function processMovement(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id};
	if(data.approve_type){sendData = {id:data.id,approve_type:data.approve_type};}
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);

		if(data.btnSave == "other"){
			$("#"+data.modal_id+" .btn-save-other").attr('onclick',"saveMovement('"+data.form_id+"','"+fnsave+"');");
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveMovement('"+data.form_id+"','"+fnsave+"');");
		}
		
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
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
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function saveMovement(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'production/processMovement/' + fnsave,
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
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			$("#movementTransData").html(data.transHtml);

			var pending_qty = $("#pend_qty").val();
			var qty = $("#qty").val();
			var newPendQty = parseFloat(parseFloat(pending_qty) - parseFloat(qty));
			$("#pending_qty").html(newPendQty);
			$("#pend_qty").val(newPendQty);
			
			$("#send_to").val("0");
			$("#send_to").trigger('change');
			$("#qty").val("");
			$("#remark").val("");			
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			$("#movementTransData").html(data.transHtml);
		}				
	});
}

function trashMovement(id,qty){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Record?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/deleteMovement',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0 || data.status==2){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								$("#movementTransData").html(data.transHtml);

								var pending_qty = $("#pend_qty").val();
								var newPendQty = parseFloat(parseFloat(pending_qty) + parseFloat(qty));
								$("#pending_qty").html(newPendQty);
								$("#pend_qty").val(newPendQty);

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

function acceptInward(data){
	$("#"+data.modal_id).modal();
	$("#"+data.modal_id+ " #job_approval_id").val(data.id);
	$("#"+data.modal_id+ " #pending_act_qty").html(data.pending_qty);
	setPlaceHolder();
}

function saveAcceptedQty(formId){
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + 'production/processMovement/saveAcceptedQty',
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
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			window.location.reload();
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function outward(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/processApproved',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+data.fnsave+"');");
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
		url: base_url + 'production/processMovement/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#pending_qty").html(data.pending_qty);

			$("#out_qty").val("");
			$("#cycle_time").val("");
			$("#production_time").val("");
			$("#remark").val("");
			$("#wp_qty").val("");
			$("#machine_id").val("");
			$("#shift_id").val("");
			$("#operator_id").val("");

            $("#machine_id").comboSelect();
			$("#shift_id").comboSelect();
			$("#operator_id").comboSelect();
            $("#rejectionReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
            $("#reworkReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
			$("#outwardTransData").html(data.outwardTrans);

			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashOutward(id,functionName,name='Record'){
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
						url: base_url + 'production/processMovement/'+functionName,
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

function storeLocation(data){
	var button = data.button;
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/storeLocation',   
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
		url: base_url + 'production/processMovement/saveStoreLocation',
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
			$('#'+formId)[0].reset();
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

// For Rework Data
function AddRowRework(data) {

    $('table#reworkReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "reworkReason";

    var tBody = $("#" + tblName + " > TBODY")[0];


    row = tBody.insertRow(-1);
	var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rework_qty_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][qty]",
        value: data.rw_qty,
        class: "rw_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_qty);
    cell.append(rework_qty_input);
    cell.attr("style", "width:20%;");

    var rr_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_reason]",
        value: data.rr_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_reason_text);
    cell.append(rr_reason_input);
    cell.attr("style", "width:20%;");

    var rr_stage_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_stage]",
        value: data.rr_stage
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_stage_text);
    cell.append(rr_stage_input);
    cell.attr("style", "width:20%;");

    var rr_by_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_by]",
        value: data.rr_by
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_by_text);
    cell.append(rr_by_input);

    var rw_process_id_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_process_id]",
        value: data.rw_process_id
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_process_id_text);
    cell.append(rw_process_id_input);
    cell.attr("style", "width:20%;");

	var dimension_range_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][dimension_range]",
        value: data.dimension_range
    });
	var remark_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][remark]",
        value: data.remark
    });
    cell = $(row.insertCell(-1));
    cell.html(data.remark);
    cell.append(dimension_range_input);
    cell.append(remark_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));
    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRework(this);");
    btnRemove.attr("style", "margin-left:2px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:5%;");

	$(".qtyCal").trigger('keyup');
}

function RemoveRework(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#reworkReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#reworkReason tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#reworkReason tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#reworkReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
    }

    $(".qtyCal").trigger('keyup');
};

// For Rejection Data
function AddRowRejection(data) {
	//Get the reference of the Table's TBODY element.
    var tblName = "rejectionReason";

    $('table#'+ tblName +' tr#noData').remove();    

    var tBody = $("#" + tblName + " > tbody")[0];
    row = tBody.insertRow(-1);

    var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_qty);
    cell.append(rejection_qty_input);
    cell.attr("style", "width:20%;");

    var rej_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_reason]",
        value: data.rr_reason
    });    
    cell = $(row.insertCell(-1));
    cell.html(data.rr_reason_text);
    cell.append(rej_reason_input);
    cell.attr("style", "width:20%;");

    var rej_type_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_type]",
        value: data.rej_type
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_type_text);
    cell.append(rej_type_input);
    cell.attr("style", "width:20%;");

	var opr_mt_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][opr_mt]",
        value: data.opr_mt
    });
    cell = $(row.insertCell(-1));
    cell.html(data.opr_mt);
    cell.append(opr_mt_input);

    var rr_stage_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_stage]",
        value: data.rr_stage
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_stage_text);
    cell.append(rr_stage_input);
    cell.attr("style", "width:20%;");

	var rr_by_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_by]",
        value: data.rr_by
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_by_text);
    cell.append(rr_by_input);
    cell.attr("style", "width:20%;");

    var rej_remark_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][remark]",
        value: data.r_remark
    });
	var dimension_range_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][dimension_range]",
        value: data.dimension_range
    });
    cell = $(row.insertCell(-1));
    cell.html(data.r_remark);
    cell.append(rej_remark_input);
    cell.append(dimension_range_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));


    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRejection(this);");
    btnRemove.attr("style", "margin-left:4px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:15%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRejection(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#rejectionReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#rejectionReason tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#rejectionReason tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#rejectionReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('keyup');
};

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
						url: base_url + 'production/processMovement/deleteStoreLocationTrans',
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

function saveMaterialReturn() {
	var fd = $('#returnMaterialForm').serialize();
	$.ajax({
		url: base_url + controller + '/saveMaterialReturn',
		data: fd,
		type: "POST",
		dataType: "json",
		success: function (data) {
			if (data.status === 0) {
				$(".error").html("");
				$.each(data.message, function (key, value) {
					$("." + key).html(value);
				});
			}
			else if (data.status == 1) {
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

				// $('#returnMaterialForm')[0].reset();

				var obj = data.result;
				$("#qty_rs").val("");
				$("#qty").val("");
				$("#returnScrapData").html("");
				$("#returnScrapData").html(obj.resultHtml);
                $("#pendingQty").html(data.pending_qty);
			}
			else {
				toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}
	});
};

function deleteMaterialReturn(id, qty, name = 'Record') {
	var job_card_id = $("#job_card_id").val();
	var page_process_id = $("#in_process_id").val();

	var send_data = { id: id, job_card_id: job_card_id, page_process_id: page_process_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this ' + name + '?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function () {
					$.ajax({
						url: base_url +controller+'/deleteMaterialReturn',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function (data) {
							if (data.status == 0) {
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else {
								$("#pendingQty").html(data.pending_qty);
								var obj = data.result;
								$("#returnScrapData").html("");;
								$("#returnScrapData").html(obj.resultHtml);

								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function () {

				}
			}
		}
	});
}


function saveRework(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveRework',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#pending_qty").html(data.pending_qty);

			$("#out_qty").val("");
			$("#remark").val("");
			$("#wp_qty").val("");
			$("#vendor_id").val("");
			$("#machine_id").val("");
			$("#shift_id").val("");
			$("#operator_id").val("");

            $("#vendor_id").comboSelect;
            $("#machine_id").comboSelect;
			$("#shift_id").comboSelect;
			$("#operator_id").comboSelect;
            $("#rejectionReasonData").html("");
			$("#outwardTransData").html(data.outwardTrans);

			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function idleTime(data){
	$.ajax({ 
		type: "POST",   
		url: base_url + 'production/processMovement/idleTime',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		//$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+data.fnsave+"');");
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

function saveIdleTime(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveIdleTime',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){

			$("#machine_id").val("");
			$("#machine_id").comboSelect();
			$("#production_time").val("");
			$("#rr_reason").val("");
			$("#rr_reason").comboSelect();
			$("#shift_id").val("");
			$("#shift_id").comboSelect();
			$("#operator_id").val("");
			$("#operator_id").comboSelect();
			$("#remark").val("");
			$("#idleTimeTransData").html(data.transHtml);

			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashIdleTime(id,name='Record'){
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
						url: base_url + 'production/processMovement/deleteIdleTime',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data){
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								$("#idleTimeTransData").html(data.transHtml);
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

function saveCutWeight(){
	var job_bom_id = $("#job_bom_id").val();
	var cut_weight = $("#cut_weight").val();
	
	var valid=1;
	if(parseFloat(cut_weight) <= 0 || cut_weight ==''){$(".cut_weight").html("Enter Cut Weigt");valid=0;}
	if(job_bom_id ==''){$(".cut_weight").html("Enter Raw material in BOM");valid=0;}

	if(valid){
		var sendData = {job_bom_id:job_bom_id,cut_weight:cut_weight}
		$.ajax({
			url: base_url + 'production/processMovement/saveCutWeight',
			data:sendData,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else if(data.status==1){			
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				window.location.reload();
			}else{			
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}				
		});
	}
	
}

function saveSttingParameter(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url +controller +'/saveSettingParameter',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $("#settingParamData").html(data.htmlData);
			$('#'+formId)[0].reset();
			$("#id").val("");
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function removeSettingParam(id,job_approval_id,name = 'Record') {
	var send_data = { id: id,job_approval_id:job_approval_id  };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Record?',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function() {
					$.ajax({
						url: base_url + controller + '/deleteSettingParam',
						data: send_data,
						type: "POST",
						dataType: "json",
						success: function(data) {
							if (data.status == 0) {
								toastr.error(data.message, 'Sorry...!', {
									"showMethod": "slideDown",
									"hideMethod": "slideUp",
									"closeButton": true,
									positionClass: 'toastr toast-bottom-center',
									containerId: 'toast-bottom-center',
									"progressBar": true
								});
							} else {
								initTable(0);
								toastr.success(data.message, 'Success', {
									"showMethod": "slideDown",
									"hideMethod": "slideUp",
									"closeButton": true,
									positionClass: 'toastr toast-bottom-center',
									containerId: 'toast-bottom-center',
									"progressBar": true
								});
								$("#settingParamData").html(data.htmlData);
							}
						}
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function() {

				}
			}
		}
	});
}