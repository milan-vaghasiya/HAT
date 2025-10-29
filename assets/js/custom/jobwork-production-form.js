$(document).ready(function(){
	//initMultiSelect();
    productionTable();
    OutwardTable();
	IdleTable();

	$("#operator_id").comboSelect('destroy');$("#operator_id").hide();$(".operatorDiv").hide();
	$("#machine_id").comboSelect('destroy');$("#machine_id").hide();$(".machineDiv").hide();
	$("#shift_id").comboSelect('destroy');$("#shift_id").hide();$(".shiftDiv").hide();
    $(".ptime").hide();
    $(".outWpcs").removeClass('col-md-2');$(".outWpcs").addClass('col-md-3');
	$(".outTotalWeight").removeClass('col-md-2');$(".outTotalWeight").addClass('col-md-3');
    $(".getIdleTime").hide();
	$("#popup_title").html("");
	$(".inProcessIdDiv").hide();
	$("#reworkRow").hide();

	$(document).on('change',"#rejection_stage",function(){
		var type = $(this).val();
		/* if(type == "-1"){
			$("#rejection_reason").val("-1");
			$("#rejection_reason option").attr("disabled","disabled");			
			$("#rejection_reason option[value='-1']").removeAttr("disabled");
			$("#rejection_reason").comboSelect();
		}else{
			$("#rejection_reason option").removeAttr("disabled");
			$("#rejection_reason").val("");
			$("#rejection_reason").comboSelect();
		} */

		$.ajax({
			url:base_url + controller + "/rejectionReason",
			type:'post',
			data:{stage_id:type},
			dataType:'json',
			success:function(data){
				$("#rejection_reason").html("");
				$("#rejection_reason").html(data.rrOptions);
				$("#rejection_reason").comboSelect();
			}
		});
	});

    $(document).on('change',"#process_id",function(){
        var process_id = $(this).val();
        var job_id = $("#job_id").val();
        $.ajax({
            url:base_url + "productions/getProcessWiseProduction",
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
    });

    $(document).on("click",".getForward",function(){
		$(".challanNoDiv").show();
		$(".remarkDiv").removeClass('col-md-10');$(".remarkDiv").addClass('col-md-6');
		var dataRow = $(this).data('row');
		var name = dataRow.product_name;
		var ProcessName = dataRow.process_name;
		var PendingQty = dataRow.pending_qty;
		$("#ProductItemName").html("");$("#ProductItemName").html(name);
		$("#ProductProcessName").html("");$("#ProductProcessName").html(ProcessName);
		$("#ProductPendingQty").html("");$("#PendingQty").val(parseFloat(PendingQty).toFixed(3));$("#ProductPendingQty").html(parseFloat(PendingQty).toFixed(3));
		
		var job_card_id = dataRow.job_card_id;
		var in_process_id = dataRow.in_process_id;
		var product_id = dataRow.product_id;	
		var ref_id = dataRow.ref_id;

		$("#job_card_id").val(job_card_id);
		$("#in_process_id").val(in_process_id);
		$("#product_id").val(product_id);		
		$("#ref_id").val(ref_id);
		$("#issue_batch_no").val(dataRow.issue_batch_no);
		$("#issue_material_qty").val(dataRow.issue_material_qty);
		$("#material_used_id").val(dataRow.material_used_id);
		$("#outEntryDate").attr('min',dataRow.minDate);
		
		$("#outEntryDate").val('');$("#rejEntryDate").val('');$("#rewEntryDate").val('');$("#scrapeEntryDate").val('');$("#production_time").val('00:00');
		$('.single-select').comboSelect();
		var today = (new Date()).toISOString().split('T')[0];
		$("#outEntryDate").val(today);$("#rejEntryDate").val(today);$("#rewEntryDate").val(today);$("#scrapeEntryDate").val(today);

		$("#operator_id").comboSelect('destroy');$("#operator_id").hide();$(".operatorDiv").hide();
	    $("#machine_id").comboSelect('destroy');$("#machine_id").hide();$(".machineDiv").hide();
	    $("#shift_id").comboSelect('destroy');$("#shift_id").hide();$(".shiftDiv").hide();
        $(".ptime").hide();

		
		$(".challanNoCol").show();
		$("#forward #OutwordTab").trigger('click');
		$(".inputmask-hhmm").inputmask("99:99");
	});

    $(document).on("click",".getOutWordQty",function(){
				
        var ref_id = $("#ref_id").val();
        var in_process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
        var page_process_id = $("#process_id").val();
		$("#production_time").val('00:00');
		
        $.ajax({
            url:base_url + 'productions/getOutwordTrans',
            type:'post',
            data:{ref_id:ref_id,process_id:in_process_id,job_card_id:job_card_id,page_process_id:page_process_id},
            dataType:'json',
            success:function(data)
            {
				$("#rejection_stage").html("");
				$("#rejection_stage").html(data.processOptions);
				$("#rejection_stage").comboSelect();
			    
			    $("#rejection_reason").html("");
				$("#rejection_reason").html(data.rrOptions);
				$("#rejection_reason").comboSelect();

				/* $("#rework_process").html("");
				$("#rework_process").html(data.process);
				reInitMultiSelect(); */

                $("#outwardQtyData").html("");
                $("#outwardTable").dataTable().fnDestroy();
                $("#outwardQtyData").html(data.sendData);				
                OutwardTable();
				$(".challanNoCol").show();

                initTable();
            }
        });
    });

    $(document).on('keyup change',".countWeightOut",function(){
		var qty = $("#outQty").val();
		var wQty = $("#outWpcs").val();
		var totalWeight = $("#outTotalWeight").val();
		var col = $(this).data('col');
		$('.outQty').html("");
		if(qty == "" || isNaN(qty)){
			$('.outQty').html("Qty. is required.");
			$("#outWpcs").val(0);
			$("#outTotalWeight").val(0);
		}else{
			var total = 0;
			if(col == "total_weight"){
				if(totalWeight == "" || isNaN(totalWeight)){
					$("#outWpcs").val(0);
				}else{
					total = parseFloat((parseFloat(totalWeight) / parseFloat(qty))).toFixed(3);
					$("#outWpcs").val(total);
				}
			}else if(col == "w_pcs"){
				if(wQty == "" || isNaN(wQty)){
					$("#outTotalWeight").val(0);
				}else{
					total = parseFloat((parseFloat(wQty) * parseFloat(qty))).toFixed(3);
					$("#outTotalWeight").val(total);
				}
			}
		}
	});

    /* $(document).on("keyup","#outQty",function(){
				
        var outQty = $(this).val();
        var product_id = $("#product_id").val();
        var process_id = $("#in_process_id").val();
        if(outQty > 0)
		{
			$.ajax({
				url:base_url + 'productions/getProductProcessRow',
				type:'post',
				data:{product_id:product_id,process_id:process_id,outQty:outQty},
				dataType:'json',
				global:false,
				success:function(data)
				{
					$('#cycle_time').val(data.ppData.cycle_time);
					$('.totalPT').html("As per Standard Time (" + data.ppData.cycle_time + " Per Piece), Production Time : " + data.ppData.ptLabel);
				}
			});
		}
    }); */
	
	
	$(document).on("click",".getIdleTime",function(){
		
        var process_id = $("#in_process_id").val();		
        var job_card_id = $("#job_card_id").val();
		var page_process_id = $("#process_id").val();

		$.ajax({
			url:base_url + 'productions/getIdleTime',
			type:'post',
			data:{process_id:process_id,job_card_id:job_card_id,page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
				$("#idleData").html("");
				$("#idleTable").dataTable().fnDestroy();
				$("#idleData").html(data.idletblData);
				IdleTable();

			    $("#idle_reason").html("");
				$("#idle_reason").html(data.idleReason);
				$("#idle_reason").comboSelect();
			}
		});
	});

	$(document).on("click","#muDetail",function(){var htmlContent = $(this).data('htmlcontent');Swal.fire({html: htmlContent,showConfirmButton: false});});

});

function productionTable(){
    var table = $('#productionTable').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel']
	});
	table.buttons().container().appendTo( '#productionTable_wrapper .col-md-6:eq(0)' );
}

function OutwardTable(){
	var OutQtyTable = $('#outwardTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	OutQtyTable.buttons().container().appendTo( '#outwardTable_wrapper .col-md-6:eq(0)' );
	return OutQtyTable;
}

function acceptJob(id,name='Job Process'){
    var process_id = $("#process_id").val();
        var job_id = $("#job_id").val();
	var send_data = { id:id,process_id:process_id,job_id:job_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to accept this '+name+'?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'productions/acceptJob',
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
}

function saveOutQty(){
	$(".error").html("");
	var valid = 1;
    if($("#outEntryDate").val() == ""){
        $(".outEntryDate").html("Date is required.");valid=0;
    }
    if($("#outQty").val() == "" && $("#rejQty").val() == "" && $("#rewQty").val() == "" || $("#outQty").val() == "0" && $("#rejQty").val() == "0" && $("#rewQty").val() == "0"){
        $(".out_form_error").html("OK Qty or Reject Qty or Rework Qty is required.");
		valid=0;
    }
	
    /* if($("#outQty").val() != "" && $("#outQty").val() != "0"){
        if($("#production_time").val() != "00:00"){
            var pt = $("#production_time").val();
            var ptime = pt.split(':');
            if(ptime[0] == ""){ptime[0]='00';}
            if(ptime[1] == ""){ptime[1]='00';}
            if(ptime[0] == "00" && ptime[1] == "00"){$(".production_time").html("Production Time is required.");valid=0;}
            if(ptime[0] > 10){$(".production_time").html("Invalid Time");valid=0;
            }else{
                if(ptime[0] == 10 && ptime[1] > 30){$(".production_time").html("Invalid Time");valid=0;}
            }
        }
    } */

    if($("#rejQty").val() != "" && $("#rejQty").val() != "0"){
        if($("#rejection_stage").val() == ""){ 
            $(".rejection_stage").html("Rejection Stage is required.");valid=0;
        } 
        if($("#rejection_reason").val() == ""){
            $(".rejection_reason").html("Rejection Reason is required.");valid=0;
        }
    }

    if($("#rewQty").val() != "" && $("#rewQty").val() != "0"){
        if($("#rework_process_id").val() == ""){
            $(".rework_process").html("Rework Process is required.");valid=0;
        }
    }
	
	if(valid){

        var page_process_id = $("#process_id").val();
        var entry_type = 2;
		var ref_id = $("#ref_id").val();
		var product_id = $("#product_id").val();
		var process_id = $("#in_process_id").val();
		var job_card_id = $("#job_card_id").val();
        var issue_batch_no = $("#issue_batch_no").val();	
        var issue_material_qty = $("#issue_material_qty").val();
        var material_used_id = $("#material_used_id").val();        
        var cycle_time = $("#cycle_time").val();
        
        var operator_id = $("#operator_id").val();
		var machine_id = $("#machine_id").val();
        var shift_id = $("#shift_id").val();
		
		var entry_date = $("#outEntryDate").val();
		var new_process_id = $("#new_in_process_id").val();
        var out_qty = $("#outQty").val();	
        var ud_qty = $("#udQty").val();			
		var w_pcs = $("#outWpcs").val();
		var total_weight = $("#outTotalWeight").val();	
		var production_time = $("#production_time").val();
        var rejection_qty = $("#rejQty").val();
        var rejection_stage = $("#rejection_stage").val();
        var rejection_reason = $("#rejection_reason").val();
        var rework_qty = $("#rewQty").val();
        var rework_process_id = $("#rework_process_id").val();
		var challan_no = $("#outChallanNo").val();
		var charge_no = $("#outChargeNo").val();			
        var remark = $("#outRemark").val();          

		var postData = {id:"",page_process_id:page_process_id,entry_type:entry_type,ref_id:ref_id,product_id:product_id,process_id:process_id,job_card_id:job_card_id,issue_batch_no:issue_batch_no,issue_material_qty:issue_material_qty,material_used_id:material_used_id,cycle_time:cycle_time,operator_id:operator_id,machine_id:machine_id,shift_id:shift_id,entry_date:entry_date,out_qty:out_qty,ud_qty:ud_qty,w_pcs:w_pcs,total_weight:total_weight,production_time:production_time,rejection_qty:rejection_qty,rejection_stage:rejection_stage,rejection_reason:rejection_reason,rework_qty:rework_qty,rework_process_id:rework_process_id,challan_no:challan_no,charge_no:charge_no,remark:remark,new_process_id:new_process_id};
		$.ajax({
			url:base_url + 'productions/saveProductionTrans',
			type:'post',
			data:postData,
			dataType:'json',
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) 
					{
						$("."+key).html(value);
						// if(key == 'muDetail'){$('.muDetail').html(value);}
					});
				}
				else
				{
					toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					var PendingQty = $("#PendingQty").val();
					var newPendingQty = parseFloat(parseFloat(PendingQty) - parseFloat(parseFloat(out_qty) + parseFloat(rejection_qty) + parseFloat(rework_qty))).toFixed(3);
					$("#PendingQty").val(newPendingQty);
					$("#ProductPendingQty").html(newPendingQty);

					$("#outQty").val(0);
					$("#udQty").val(0);
					$("#outWpcs").val(0);
					$("#outTotalWeight").val(0);
					$("#production_time").val("");
					$("#rejQty").val(0);
					$("#rejection_stage").val(""); $("#rejection_stage").comboSelect();
					$("#rejection_reason").val(""); $("#rejection_reason").comboSelect();
					/* $("#rewQty").val(0);
					$("#rework_process").val("");
					$("#rework_process_id").val("");reInitMultiSelect(); */
					$("#outRemark").val("");
					$("#outChallanNo").val("");
					$("#outChargeNo").val("");

					$("#outwardQtyData").html("");
					$("#outwardTable").dataTable().fnDestroy();
					$("#outwardQtyData").html(data.sendData);
					$(".challanNoCol").show();  
					OutwardTable();					                  
				}
                initTable();
			}
		});
	}
};

function trashOutward(id,out_qty,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();

	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id };
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
						url: base_url + 'productions/deleteProductionTrans',
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
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								var PendingQty = $("#PendingQty").val();
								var newPendingQty = parseFloat(parseFloat(PendingQty) + parseFloat(out_qty)).toFixed(3);
								$("#PendingQty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                $("#outwardQtyData").html("");
                                $("#outwardTable").dataTable().fnDestroy();
                                $("#outwardQtyData").html(data.sendData);
                                OutwardTable(); 
								$(".challanNoCol").show();
							}
                            initTable();
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

function IdleTable(){
	var idleTable = $('#idleTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	idleTable.buttons().container().appendTo( '#idleTable_wrapper .col-md-6:eq(0)' );
	return idleTable;
}

function saveIdleTime() {
	
	$(".error").html("");
	var valid = 1;
	if($("#shift_id").val() == ""){$(".shift_id").html("Shift is required.");valid=0;}
	if($("#operator_id").val() == ""){$(".operator_id").html("Operator Name is required.");valid=0;}
	if($("#machine_id").val() == ""){$(".machine_id").html("Machine is required.");valid=0;}
	if($("#idleEntryDate").val() == ""){$(".idleEntryDate").html("Date is required.");valid=0;}
	if($("#idle_reason").val() == ""){$(".idle_reason").html("Idle Reason is required.");valid=0;}
	if($("#idle_time").val() == ""){$(".idle_time").html("Idle Time is required.");valid=0;}
	else
	{
		var pt = $("#idle_time").val();
		var ptime = pt.split(':');
		if(ptime[0] == ""){ptime[0]='00';}if(ptime[1] == ""){ptime[1]='00';}
		if(ptime[0] > 10){$(".idle_time").html("Invalid Time");valid=0;}
		else{if(ptime[0] == 10 && ptime[1] > 30){$(".idle_time").html("Invalid Time");valid=0;}}
	}
	if(valid)
	{
		var process_id = $("#in_process_id").val();
		var job_card_id = $("#job_card_id").val();	
        var shift_id = $("#shift_id").val();
		var operator_id = $("#operator_id").val();
		var machine_id = $("#machine_id").val();
		var entry_date = $("#idleEntryDate").val();
		var idle_reason = $("#idle_reason").val();
		var idle_time = $("#idle_time").val();		
		var page_process_id = $("#process_id").val();

		var postData = {process_id:process_id,job_card_id:job_card_id,shift_id:shift_id,operator_id:operator_id,machine_id:machine_id,entry_date:entry_date,idle_reason:idle_reason,idle_time:idle_time,page_process_id:page_process_id};
		$.ajax({
			url:base_url + 'productions/saveIdleTime',
			type:'post',
			data:postData,
			dataType:'json',
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) 
					{
						$("."+key).html(value);
					});
				}
				else
				{
					toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					$("#idleData").html("");
					$("#idleTable").dataTable().fnDestroy();
					$("#idleData").html(data.sendData);
					IdleTable();
				}
				$("#idle_time").val("00:00");
				$("#idle_reason").val("");
				$("#idle_reason").comboSelect();
			}
		});
	}
};

function trashIdleTime(id,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#process_id").val();
    var process_id = $("#in_process_id").val();
	var send_data = { id:id,job_card_id:job_card_id,page_process_id:page_process_id,process_id:process_id};
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
						url: base_url + 'productions/deleteIdleTime',
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
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                                //var obj = data.result;

                                $("#idleData").html("");
								$("#idleTable").dataTable().fnDestroy();
                                $("#idleData").html(data.sendData);
								IdleTable();

								$("#idle_reason").html("");
								$("#idle_reason").html(data.idleReason);
								$("#idle_reason").comboSelect();
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