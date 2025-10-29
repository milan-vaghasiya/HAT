<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-9">
								<ul class="nav nav-pills">    
									<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/1") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 1)?'active':''?>">In Stock</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/0") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 0)?'active':''?>">New Inward</a> </li>
									<li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/2") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 2)?'active':''?>">Issued</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/indexUsed/3") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 3)?'active':''?>">In Calibration</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/4") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 4)?'active':''?>">Rejected</a> </li>
									<li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/5") ?>" class="nav-link btn btn-sm nbr btn-outline-success <?=($status == 5)?'active':''?>">Due For Calibration</a> </li>
                                	<li class="nav-item"> <button type="button" class="btn waves-effect waves-light btn-outline-success float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addInstrument" data-form_title="Add Instrument"><i class="fa fa-plus"></i> Add Instrument</button> </li>
                         
                                </ul>
							</div>
                            <div class="col-md-3 text-right">
                                <h4 class="card-title">Instruments/Equipments</h4>
                            </div>                         
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='instrumentTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    initBulkChallanButton();
    $(document).on('click','.rejectGauge',function(){
        var id = $(this).data('id');
        var gauge_code = $(this).data('gauge_code');
        $(".error").html("");
		$("#rejectGaugeModal").modal();
		$("#gauge_code").html(gauge_code);
		$("#gauge_id").val(id);
    });
    
	$(document).on('click', '.BulkInstChallan', function() {
		if ($(this).attr('id') == "masterInstSelect") {
			if ($(this).prop('checked') == true) {
				$(".bulkChallan").show();
				$("input[name='ref_id[]']").prop('checked', true);
			} else {
				$(".bulkChallan").hide();
				$("input[name='ref_id[]']").prop('checked', false);
			}
		} else {
			if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
				$(".bulkChallan").show();
				$("#masterInstSelect").prop('checked', false);
			} else {
				$(".bulkChallan").hide();
			}

			if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
				$("#masterInstSelect").prop('checked', true);
				$(".bulkChallan").show();
			}
			else{$("#masterInstSelect").prop('checked', false);}
		}
	});
	
	$(document).on('click', '.bulkChallan', function() {
		var ref_id = [];
		$("input[name='ref_id[]']:checked").each(function() {
			ref_id.push(this.value);
		});
		var ids = ref_id.join("~");
		var send_data = {
			ids
		};
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to generate Challan?',
			type: 'red',
			buttons: {
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function() {
						window.open(base_url + 'qcChallan/createChallan/' + ids, '_self');
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function() {

					}
				}
			}
		});
	});
});

function inwardGauge(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	var sendData = {id:data.id,status:data.status};
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnedit,
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function saveRejectGauge(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveRejectGauge',
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
		    $("#gauge_id").val(""); $("#gauge_code").html(""); $(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function initBulkChallanButton() {
	var bulkChallanBtn = '<button class="btn btn-outline-primary bulkChallan" tabindex="0" aria-controls="instrumentTable" type="button"><span>Bulk Challan</span></button>';
	$("#instrumentTable_wrapper .dt-buttons").append(bulkChallanBtn);
	$(".bulkChallan").hide();
}
</script>