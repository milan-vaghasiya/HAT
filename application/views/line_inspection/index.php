<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="lineInspectionTabStatus('LineInspectionTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="lineInspectionTabStatus('LineInspectionTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Line Inspection</h4>
                            </div>
                            <div class="col-md-2">
                                <select id="page_job_id" class="single-select float-right">
                                    <option value="">All Jobcard</option>
                                    <?php
                                        foreach($jobNoList as $row):
                                            echo '<option value="'.$row->id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).' ['.$row->item_code.']</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="page_process_id" class="single-select float-right">
                                    <option value="">All Process</option>
                                    <?php
                                        foreach($processList as $row):
                                            echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div> 
                        </div>                                          
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='LineInspectionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<!-- <script src="<?php echo base_url();?>assets/js/custom/production-form.js?v=<?=time()?>"></script> -->
<script>
$(document).ready(function(){
    inspectionTransTable();
    
    initBulkInspectionButton();
	$(document).on('click','.BulkLineInspection',function(){
		if($(this).attr('id') == "masterSelect"){
			if($(this).prop('checked')==true){
				$(".bulkInspection").show();
				$("input[name='ref_id[]']").prop('checked',true);
			}else{
				$(".bulkInspection").hide();
				$("input[name='ref_id[]']").prop('checked',false);
			}	
		}else{
			if($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length){
				$(".bulkInspection").show();
				$("#masterSelect").prop('checked',false);
			}else{
				$(".bulkInspection").hide();
			}

			if($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length){
				$("#masterSelect").prop('checked',true);
				$(".bulkInspection").show();
			}
		}	
	});

	$(document).on('click','.bulkInspection',function(){
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});

		var send_data = { ref_id:ref_id };
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to Bulk Line Inspection?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/bulkLineInspection',
							data: send_data,
							type: "POST",
							dataType:"json",
							success:function(data)
							{
								if(data.status==0){									
									toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}else{
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
								initTable(1,{job_card_id:$("#page_job_id").val(),process_id:$("#page_process_id").val()});
							    initBulkInspectionButton();
								$(".BulkLineInspection").prop('checked',false);
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
    
    $(document).on('change',"#page_job_id",function(){
        $('.ssTable').dataTable().fnDestroy();
        var tableOptions = {pageLength: 25,'stateSave':false};
        var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
        var dataSet = {job_card_id:$("#page_job_id").val(),process_id:$("#page_process_id").val()};
        ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
        initBulkInspectionButton();
    });

    $(document).on('change',"#page_process_id",function(){
        $('.ssTable').dataTable().fnDestroy();
        var tableOptions = {pageLength: 25,'stateSave':false};
        var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
        var dataSet = {job_card_id:$("#page_job_id").val(),process_id:$("#page_process_id").val()};
        ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
        initBulkInspectionButton();
    });   

    $(document).on('change',"#rejection_stage1",function(){
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

    $(document).on('keyup change',".countWeightOut",function(){
		var col = $(this).data('col');
		calculateWeight(col);
	});

	$(document).on('keyup change','.countOutQty',function(){
		var productionOutQty = $("#production_out_qty").val();
		var productionRejQty = $("#production_rejection_qty").val();
		var productionRewQty = $("#production_rework_qty").val();

		productionOutQty = (productionOutQty > 0)?productionOutQty:0;
		productionRejQty = (productionRejQty > 0)?productionRejQty:0;
		productionRewQty = (productionRewQty > 0)?productionRewQty:0;
		var totalOutQty = parseFloat(parseFloat(productionOutQty) + parseFloat(productionRejQty) + parseFloat(productionRewQty)).toFixed(3);

		var rejQty = $("#rejection_qty").val();
		var rewQty = $("#rework_qty").val();

		rejQty = (rejQty > 0)?rejQty:0;
		rewQty = (rewQty > 0)?rewQty:0;
		var rejRewTotal = parseFloat(parseFloat(rejQty) + parseFloat(rewQty)).toFixed(3);

		var okQty = parseFloat(parseFloat(totalOutQty) - parseFloat(rejRewTotal)).toFixed(3);
		$("#out_qty").val(okQty);
		calculateWeight("w_pcs");
	});
});

function calculateWeight(col){
	var qty = $("#out_qty").val();
	var wQty = $("#w_pcs").val();
	var totalWeight = $("#total_weight").val();
	//var col = $(this).data('col');
	$('.out_qty').html("");
	if(qty == "" || isNaN(qty)){
		$('.out_qty').html("Qty. is required.");
		$("#w_pcs").val(0);
		$("#total_weight").val(0);
	}else{
		var total = 0;
		if(col == "total_weight"){
			if(totalWeight == "" || isNaN(totalWeight)){
				$("#w_pcs").val(0);
			}else{
				total = parseFloat((parseFloat(totalWeight) / parseFloat(qty))).toFixed(3);
				$("#w_pcs").val(total);
			}
		}else if(col == "w_pcs"){
			if(wQty == "" || isNaN(wQty)){
				$("#total_weight").val(0);
			}else{
				total = parseFloat((parseFloat(wQty) * parseFloat(qty))).toFixed(3);
				$("#total_weight").val(total);
			}
		}
	}
}

function lineInspection(postData){
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/lineInspection',   
		data: postData
	}).done(function(response){
		$("#"+postData.modal_id).modal();
        
		$("#"+postData.modal_id+' .modal-title').html(postData.title);
		$("#"+postData.modal_id+' .modal-body').html(response);
		$("#"+postData.modal_id+" .modal-body form").attr('id',postData.form_id);
		$("#"+postData.modal_id+" .modal-footer .btn-save").attr('onclick',"saveInsTrans('"+postData.form_id+"');");
		if(postData.button == "close"){
			$("#"+postData.modal_id+" .modal-footer .btn-close").show();
			$("#"+postData.modal_id+" .modal-footer .btn-save").hide();
		}else if(postData.button == "save"){
			$("#"+postData.modal_id+" .modal-footer .btn-close").hide();
			$("#"+postData.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+postData.modal_id+" .modal-footer .btn-close").show();
			$("#"+postData.modal_id+" .modal-footer .btn-save").hide();
		}
		$(".single-select").comboSelect();
		setPlaceHolder();
		initMultiSelect();
        inspectionTransTable();
	});
}

function inspectionTransTable(){
	var inspectionTrans = $('#inspectionTrans').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	inspectionTrans.buttons().container().appendTo( '#inspectionTrans_wrapper .col-md-6:eq(0)' );
	return inspectionTrans;
}

function saveInsTrans(form){
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
			var outQty = parseFloat(parseFloat($("#out_qty").val()) + parseFloat($("#rejection_qty").val()) + parseFloat($("#rework_qty").val())).toFixed(3);
			var pendingQty = $("#pending_qty").val();

			var currentPenQty = parseFloat(parseFloat(pendingQty) - parseFloat(outQty)).toFixed(3);
			$("#pending_qty").val(currentPenQty);
            $("#ProductPendingQty").html(currentPenQty);

			$("#out_qty").val("0");
			$("#ud_qty").val("0");
			$("#w_pcs").val("0");
			$("#total_weight").val("0");
			$("#rejection_qty").val("0");
			$("#rejection_stage").val("");$("#rejection_stage").comboSelect();
			$("#rejection_reason").val("");$("#rejection_reason").comboSelect();
			$("#rework_qty").val("0");
			$("#rework_process_id").val("");$("#rework_process_id").comboSelect();
			//$("#rework_process").val("");reInitMultiSelect();
			$("#remark").val("");
			$("#rejection_remark").val("");
			$("#rework_reason").val(""); $("#rework_reason").comboSelect();
			$("#rework_remark").val("");  

			$("#inspectionTransData").html("");
            $("#inspectionTrans").dataTable().fnDestroy();
            $("#inspectionTransData").html(data.htmlData);
            inspectionTransTable();

			initTable(1,{job_card_id:$("#page_job_id").val(),process_id:$("#page_process_id").val()});
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
		initBulkInspectionButton();
	});
}

function trashInspection(id,out_qty,name='Record'){
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
						url: base_url + controller + '/delete',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								var pendingQty = $("#pending_qty").val();
								var newPendingQty = parseFloat(parseFloat(pendingQty) + parseFloat(out_qty)).toFixed(3);
								newPendingQty = (parseFloat(newPendingQty) >= 0)?newPendingQty:0;
								$("#pending_qty").val(newPendingQty);
								$("#ProductPendingQty").html(newPendingQty);

                                $("#inspectionTransData").html("");
                                $("#inspectionTrans").dataTable().fnDestroy();
                                $("#inspectionTransData").html(data.htmlData);
                                inspectionTransTable();

								initTable(1,{job_card_id:$("#page_job_id").val(),process_id:$("#page_process_id").val()});
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							initBulkInspectionButton();
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

function lineInspectionTabStatus(tableId,status){
    $("#"+tableId).attr("data-url",'/getDTRows/'+status);
    ssTable.state.clear();initTable();
    initBulkInspectionButton();
}

function initBulkInspectionButton(){
	var bulkInspectionBtn = '<button class="btn btn-outline-primary bulkInspection" tabindex="0" aria-controls="LineInspectionTable" type="button"><span>Bulk Inspection</span></button>';
	$("#LineInspectionTable_wrapper .dt-buttons").append(bulkInspectionBtn);
	$(".bulkInspection").hide();
}
</script>