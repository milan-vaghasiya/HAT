<?php $this->load->view('app/includes/header');?>

<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white">Movement [<?=(getPrefixNumber($approvalData->job_prefix,$approvalData->job_no))?> - <?= (!empty($approvalData->product_code)) ? $approvalData->product_code : "" ?>]</div>
	
</div>

<div class="extraHeader pe-0 ps-0">
    <table class=" table jptable-bordered" >
        <tbody>
            <tr class="in_process_id">
                <th class="text-center bg-light" >Process</th>
                <td class="text-left" >
                    <?= (!empty($approvalData->in_process_name)) ? $approvalData->in_process_name : "" ?> ->
                    <?= (!empty($approvalData->out_process_name)) ? $approvalData->out_process_name : "Store Location" ?>
                </td>
                <th class="text-center bg-light" >Qty.</th>
                <td class="text-left" id="pending_qty"  ><?= (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "" ?></td>            
            </tr>
        </tbody>
    </table>
</div>
<div id="appCapsule" class=" extra-header-active full-height">
    <div class="card">
        <div class="card-body">
            <form  id="movementForm">
                <div class="row">
                    <input type="hidden" name="from_entry" id="from_entry" value="1">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=$approvalData->id?>">
                    <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$approvalData->job_card_id?>">
                    <input type="hidden" id="pend_qty" value="<?= (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "" ?>">

                    <div class="col form-group boxed ">
                        <label for="entry_date">Date</label>
                        <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
                    </div>
                </div>
                <div class="row">

                    <div class="col form-group boxed ">
                        <label for="send_to">Send To</label>
                        <select name="send_to" id="send_to" class="form-control select2">
                            <option value="0" <?=($send_to == 0)?"selected":""?>>In House</option>
                            <option value="1" <?=($send_to == 1)?"selected":""?>>Vendor</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed ">
                        <label for="handover_to">Machine/Vendor</label>
                        <select name="handover_to" id="handover_to" class="form-control select2">
                            <?=$handover_to?>
                        </select>
                    </div>
                </div>
                <div class="row">

                    <div class="col form-group boxed ">
                        <label for="qty">Qty.</label>
                        <input type="text" name="qty" id="qty" class="form-control floatOnly" value="">
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed " <?=($approvalData->trans_type == 2)?'hidden':''?>>
                        <label for="batch_no">Heat No</label>
                        <select name="batch_no" id="batch_no" class="form-control select2">
                            <option value="">Select Heat No</option>
                            <?php
                                if(!empty($heatData)){
                                    foreach($heatData as $row){
                                        ?><option value="<?=$row->tc_no?>"><?=$row->tc_no.' [Pending Material : '.($row->in_qty-$row->out_qty).']'?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed ">
                        <label for="remark">Remark</label>
                        <input type="text" name="remark" id="remark" class="form-control" value="">
                    </div>
                </div>
                <div class="row">

                    <div class="col form-group boxed ">
                        <button type="button" class="btn btn-primary me-1 mb-1 save-form" onclick="saveMovement('movementForm','saveProcessMovement')"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>        
            </form>

            <div class="row">
                <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Movement :
                </h5>

                <div class="col form-group boxed">
                    <div class="table-responsive">                    
                        <table id='outwardTransTable' class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th class="text-center" style="width:5%;">#</th>
                                    <th>Date</th>
                                    <th>Send To</th>
                                    <th>Machine/Vendor</th>
                                    <th>Qty.</th>
                                    <th>Heat No.</th>
                                    <th>Remark</th>                        
                                    <th class="text-center" style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="movementTransData">
                                <?php
                                    if(!empty($transHtml)):
                                        echo $transHtml;
                                    else:
                                ?>
                                    <tr><td colspan="8" class="text-center">No Data Found.</td></tr>
                                <?php
                                    endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>
<script>
    $(document).ready(function () {
        
        $(document).on('change', '#send_to', function() {
            var used_at = $(this).val();
            $.ajax({
                type: "POST",
                url: base_url  + 'production/processMovement/getHandoverData',
                data: {
                    used_at: used_at
                },
                dataType: 'json',
            }).done(function(response) {
                $("#handover_to").html(response.handover);
                $("#handover_to").select2();
            });
        });
    });

    
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
            $("#DialogIconedSuccess .modal-body").html(data.message);
            $("#DialogIconedSuccess").modal('show');
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
            $(".select2").select2();
		}else{			
            $("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');
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
                                $("#DialogIconedDanger .modal-body").html(data.message);
                                $("#DialogIconedDanger").modal('show');
							}else{
								$("#movementTransData").html(data.transHtml);

								var pending_qty = $("#pend_qty").val();
								var newPendQty = parseFloat(parseFloat(pending_qty) + parseFloat(qty));
								$("#pending_qty").html(newPendQty);
								$("#pend_qty").val(newPendQty);
                                $(".select2").select2();

                                $("#DialogIconedSuccess .modal-body").html(data.message);
                                $("#DialogIconedSuccess").modal('show');
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
</script>