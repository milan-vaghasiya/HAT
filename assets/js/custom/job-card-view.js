$(document).ready(function(){
	initMultiSelect();
	returnTable();
	$(document).on('click','.addJobStage',function(){
		var jobid = $('#jobID').val();
		var process_id = $('#stage_id').val();
		$(".stage_id").html("");
		if(jobid !="" && process_id != ""){
			$.ajax({ 
				type: "POST",   
				url: base_url + controller + '/addJobStage',   
				data: {id:jobid,process_id:process_id},
				dataType:'json',
				success:function(data){
					$('#stageRows').html("");$('#stageRows').html(data.stageRows);
					$('#stage_id').html("");$('#stage_id').html(data.pOptions);$('#stage_id').comboSelect();
				}
			});
		}else{
			$(".stage_id").html("Stage is required.");
		}
	});
	
	$(document).on('click','.removeJobStage',function(){
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
					action: function(){
						if(jobid !="" && process_id != "")
						{
							$.ajax({ 
								type: "POST",   
								url: base_url + controller + '/removeJobStage',   
								data: {id:jobid,process_id:process_id},
								dataType:'json',
								success:function(data){
									$('#stageRows').html("");$('#stageRows').html(data.stageRows);
									$('#stage_id').html("");$('#stage_id').html(data.pOptions);$('#stage_id').comboSelect();
								}
							});
						}
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
	
	$("#jobStages tbody").sortable({
		items: 'tr',
		cursor: 'pointer',
		axis: 'y',
		dropOnEmpty: false,
		helper: fixWidthHelper,
		start: function (e, ui) {ui.item.addClass("selected");},
		stop: function (e, ui) {
			ui.item.removeClass("selected");
			var seq = 1;
			$(this).find("tr").each(function() {$(this).find("td").eq(2).html(seq+1); seq++;});
		},
		update: function () 
		{
			var ids='';
			$(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
			var lastChar = ids.slice(-1);
			if (lastChar == ',') {ids = ids.slice(0, -1);}
			var jobid = $('#jobID').val();
			var rnstages=$('#rnstages').val();
			
			$.ajax({
				url: base_url + controller + '/updateJobProcessSequance',
				type:'post',
				data: {id:jobid,process_id:ids,rnstages:rnstages},
				dataType:'json',
				global:false,
				success:function(data){}
			});
		}
	});

    $(document).on('click',".requiredMaterial",function(){
		var item_id = $(this).data('product_id');
		var productName = $(this).data('product');
		var orderQty = $(this).data('qty');
        var process_id = $(this).data('process_id');
        var process_name = $(this).data('process_name');

		$.ajax({
			url: base_url + controller + '/getProcessWiseRequiredMaterial',
			data: {process_id:process_id,item_id:item_id,qty:orderQty},
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
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

	returnTable();

	$(document).on("click",".getForward",function(){
		$("#challanNoDiv").hide();
		$(".remarkDiv").removeClass('col-md-7');$(".remarkDiv").addClass('col-md-10');
		var name = $(this).data('product_name');
		var ProcessName = $(this).data('process_name');
		var PendingQty = $(this).data('pending_qty');
		var item_name = $(this).data('item_name');
		var item_id = $(this).data('item_id');
		$("#ProductItemName").html("");$("#ProductItemName").html(name);
		$("#ProductProcessName").html("");$("#ProductProcessName").html(ProcessName);
		$("#ProductPendingQty").html("");$("#PendingQty").val(parseFloat(PendingQty).toFixed(3));$("#ProductPendingQty").html(parseFloat(PendingQty).toFixed(3));
		$("#item_name_r").val("");$("#item_name_r").val(item_name);
		$("#item_id_r").val("");$("#item_id_r").val(item_id);
		
		var job_card_id = $(this).data('job_card_id');
		var in_process_id = $(this).data('in_process_id');
		var product_id = $(this).data('product_id');		
		var ref_id = $(this).data('ref_id');
		var page_process_id = $(this).data('in_process_id');

		$("#job_card_id").val(job_card_id);
		$("#in_process_id").val(in_process_id);
		$("#rproduct_id").val(product_id);		
		$("#ref_id").val(ref_id);

		$.ajax({
			url : base_url + controller + '/getStoreLocation',
			type: 'post',
			data : {},
			dataType : 'json',
			success:function(data){
				$("#location_id_r").html("");
				$("#location_id_r").html(data.options);
				$("#location_id_r").comboSelect();
			}
		});

		$.ajax({
			url : base_url + controller + '/getBatchNoForReturnMaterial',
			type: 'post',
			data : {job_id:job_card_id,item_id:item_id},
			dataType : 'json',
			success:function(data){
				$("#batch_no_r").html("");
				$("#batch_no_r").html(data.options);
				//$("#batch_no_r").comboSelect();
			}
		});
		
		$.ajax({
			url:base_url + 'productions/getReturnOrScrapeTrans',
			type:'post',
			data:{ref_id:ref_id,process_id:in_process_id,job_card_id:job_card_id,type:"1",page_process_id:page_process_id},
			dataType:'json',
			success:function(data)
			{
				$("#returnData").html("");
				$("#returnTable").dataTable().fnDestroy();
				$("#returnData").html(data.resultHtml);
				$(".operatorCol").hide();
				returnTable();
			}
		});
	});	

	$(document).on("click",'#addJobBom',function(){
		var form = $('#job_bom_data')[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/saveJobBomItem',
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
				$("#bom_item_id").val("");$("#bom_item_id").comboSelect();
				$("#bom_qty").val('');
				$("#requiredItems").html("");
				$("#requiredItems").html(data.result);
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}else{
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
					
		});
	});

	$(document).on('click',".addScrap",function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var job_card_id=$(this).data('job_card_id');
		var scrap_qty=$(this).data('scrap_qty');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {job_card_id:job_card_id,scrap_qty:scrap_qty}
        }).done(function(response){
            $("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveProductionScrape('"+formId+"','"+fnsave+"');");
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
        });
	});
});

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function AddStageRow(data) {
	$('table#purchaseEnqItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "purchaseEnqItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	cell = $(row.insertCell(-1));
	cell.html(data.item_name + '<input type="hidden" name="item_name[]" value="'+data.item_name+'"><input type="hidden" name="trans_id[]" value="'+data.trans_id+'" /><input type="hidden" name="item_remark[]" value="'+data.item_remark+'">');
}

function returnTable(){
	var returnTable = $('#returnTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	returnTable.buttons().container().appendTo( '#returnTable_wrapper .col-md-6:eq(0)' );
	return returnTable;
};

function requiredMaterialData(job_id){
	$.ajax({
		url : base_url + controller + '/getRequiredMaterialData',
		type: 'post',
		data : {job_id:job_id},
		dataType : 'json',
		success:function(data){
			$("#requiredItems").html("");
			$("#requiredItems").html(data.result);
		}
	});
}

function returnSave(){

	var ref_id = $("#ref_id").val();
    var product_id = $("#rproduct_id").val();
    var process_id = $("#in_process_id").val();
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#in_process_id").val();
	
	var trans_type = $("#trans_type_r").val();
	var item_id = $("#item_id_r").val();
	var qty = $("#qty_r").val();
	var remark = $("#remark_r").val();
	var location_id = $("#location_id_r").val();
	var batch_no = $("#batch_no_r").val();

	$(".qty_r").html('');
	$(".location_id_r").html("");
	$(".batch_no_r").html("");

	if(qty == 0 || qty == "" || isNaN(qty) || location_id == "" || batch_no == ""){
		if(qty == 0 || qty == "" || isNaN(qty)){
			$(".qty_r").html('Qty. is required.');
		}
		if(location_id == ""){
			$(".location_id_r").html("Store Location is required.");
		}
		if(batch_no == ""){
			$(".batch_no_r").html("Batch No. is required.");
		}
	}else{
		var postData = {id:"",ref_id:ref_id,product_id:product_id,process_id:process_id,job_card_id:job_card_id,page_process_id:page_process_id,trans_type:trans_type,item_id:item_id,qty:qty,remark:remark,operator_id:"0",machine_id:"0",location_id:location_id,batch_no:batch_no};

		$.ajax({
			url: base_url + 'productions/returnOrScrapeSave',
			data:postData,
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else if(data.status==1)
				{
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

					//requiredMaterialData(job_card_id);

					var PendingQty = $("#PendingQty").val();
					var newPendingQty = parseFloat(parseFloat(PendingQty) - parseFloat(qty)).toFixed(3);
					$("#PendingQty").val(newPendingQty);
					$("#ProductPendingQty").html(newPendingQty);

					var obj = data.result;
					$("#remark_r").val("");
					$("#qty_r").val("");
					$("#location_id_r").val("");
					$("#location_id_r").comboSelect();
					$("#batch_no_r").val("");
					$("#batch_no_r").comboSelect();
	
					$("#returnData").html("");
					$("#returnTable").dataTable().fnDestroy();
					$("#returnData").html(obj.resultHtml);
					$(".operatorCol").hide();
					returnTable();
				}
				else
				{
					toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
			}
		});
	}	
};

function deleteReturn(id,qty,name='Record'){
    var job_card_id = $("#job_card_id").val();	
    var page_process_id = $("#in_process_id").val();

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
						url: base_url + 'productions/deleteRetuenOrScrapeItem',
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
								//requiredMaterialData(job_card_id);

								var PendingQty = $("#PendingQty").val();
								var newPendingQty = parseFloat(parseFloat(PendingQty) + parseFloat(qty)).toFixed(3);
								$("#PendingQty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                var obj = data.result;
                                $("#returnData").html("");
                                $("#returnTable").dataTable().fnDestroy();
                                $("#returnData").html(obj.resultHtml);
								$(".operatorCol").hide();
                                returnTable();

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

function removeBomItem(id,job_card_id){
	var send_data = { id:id,job_card_id:job_card_id};
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Bom Item?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteBomItem',
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
                action: function(){

				}
            }
		}
	});
}

/**
 * Created By Mansee @ 10-12-2021
 */
 
function saveProductionScrape(formId,fnsave) {	
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/saveProductionScrape',
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
			$(".addScrap").hide();
			$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			
		}else{
			$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});

}