<?php $this->load->view('app/includes/header');?>


<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white"><?=(getPrefixNumber($dataRow->job_prefix,$dataRow->job_no))?></div>
	
</div>
<div class="extraHeader pe-0 ps-0">
    <ul class="nav nav-tabs lined" role="tablist">
        <li class="nav-item"> <a href="<?= base_url('production/jobcard/printDetailedRouteCardNew/'.$dataRow->id)?>" class="headerButton  " target="_blank"><i class="fa fa-print"></i>  Print Blank</a> </li>
        <li class="nav-item" ><a href="<?= base_url('production/jobcard/printDetailedRouteCard/'.$dataRow->id)?>" class="headerButton  " target="_blank"><i class="fa fa-print"></i>  Print </a> </li>
    </ul>
</div>

<!-- Start Main Content -->
<div id="appCapsule" class="extra-header-active full-height">
    <input type="hidden" id="job_card_id" name="" id="<?=$dataRow->id?>">
    <?php
        if (!empty($dataRow->processData)) :
            $i = 1;
            foreach ($dataRow->processData as $row) : ?>
                <div class="card">
                    <div class="card-header">
                        <div class="chip chip-media">
                            <i class="chip-icon bg-primary">
                                <ion-icon name="radio-button-on-outline"></ion-icon>
                            </i>
                            <span class="chip-label"><a href="#" class="text-bold-500 text-dark"><?= $row->process_name ?></a></span>
                        </div>
                        <div class="card-button dropdown">
                        <?php
                            $button = "";
                            if (!empty($row->process_approvel_data) && $dataRow->job_order_status != 3) :
                                $approvalData = $row->process_approvel_data;
                                

                                

                                /* Accept Button */
                                $acceptParam = "{'id' : ".$approvalData->id.", 'modal_id' : 'acceptInward','pending_qty':".$row->unaccepted_qty."}";
                                if($row->unaccepted_qty > 0):	
                                    $button .= ' <a class="dropdown-item" href="javascript:void(0); " onclick="acceptInward('.$acceptParam.')">
                                                    <ion-icon name="checkmark-circle-outline" role="img" class="md hydrated" aria-label="pencil outline"></ion-icon>Accept Inward
                                                </a>';
                                endif;	

                                /* Production Log Button */
                                $outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";
                                if(!empty($approvalData->in_process_id)):
                                    $button .= ' <a class="dropdown-item" href="'.base_url($headData->controller.'/processLog/'.$approvalData->id).'" >
                                                    <ion-icon name="document-outline" role="img" class="md hydrated" aria-label="pencil outline">
                                                    </ion-icon>Production Log
                                                </a>';
                                endif;

                                /* Movement Button */
                                $moveParam = "{'id' : ".$approvalData->id.", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnedit' : 'processMovement','btnSave':'other'}";
                                if($approvalData->ok_qty  > 0 && !empty($approvalData->out_process_id)):		
                                    $button .= ' <a class="dropdown-item" href="'.base_url($headData->controller.'/processMovement/'.$approvalData->id).'" >
                                                    <ion-icon name="paper-plane-outline" role="img" class="md hydrated" aria-label="pencil outline"></ion-icon>Move to Next Process
                                                </a>';
                                endif;

                                /* Store Location Button */
                                $storeLocationParam = "{'id' : " . $approvalData->job_card_id . ",'transid' : " . $approvalData->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";
                                if($approvalData->out_process_id == 0 && $row->ok_qty> 0):
                                    $button .= ' <a class="dropdown-item" href="'.base_url($headData->controller.'/storeLocation/'.$approvalData->job_card_id.'/'.$approvalData->id).'" >
                                                    <ion-icon name="paper-plane-outline" role="img" class="md hydrated" aria-label="pencil outline"></ion-icon>Store Location
                                                </a>';
                                endif;
                                
                                /* Idle Time Button */
                                if(!empty($approvalData->in_process_id)):
                                    $idleTimeParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'idleTime', 'title' : 'Idle Time','button' : 'close'}";
                                    $button .= ' <a class="dropdown-item" href="'.base_url($headData->controller.'/idleTime/'.$approvalData->id).'">
                                                    <ion-icon name="cog-outline" role="img" class="md hydrated" aria-label="pencil outline"></ion-icon>Idle Time
                                                </a>';
                                endif;

                                /*** Setting Parameter Button  */

                                /* Store Location Button */
                                $settingParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'sttingParameter', 'title' : 'Setting Parameter','button' : 'close','fnEdit':'addSettingParameter'}";
                                if($approvalData->in_process_id > 0):
                                    $button .= ' <a class="dropdown-item" href="'.base_url($headData->controller.'/addSettingParameter/'.$approvalData->id).'">
                                                    <ion-icon name="build" role="img" class="md hydrated" aria-label="pencil outline"></ion-icon>Setting Parameter
                                                </a>';
                                endif;
                            endif;
                            ?>
                            <button type="button" class="btn btn-link btn-icon" data-bs-toggle="dropdown">
                                <ion-icon name="ellipsis-horizontal" role="img" class="md hydrated" aria-label="ellipsis horizontal"></ion-icon>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <?=$button?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table jptable-bordered ">
                                <thead>
                                    <?php
                                    if(empty($row->process_id)){
                                        ?>
                                        <th colspan="9" class="text-left bg-light">Pending Material</th>
                                        <?php
                                    }else{
                                    ?>
                                    <tr class="bg-light">
                                        <th class="text-left" style="min-width:50px">Vendor</th>
                                        <td colspan="7"><?=$row->vendor?></td>
                                    </tr>
                                    <tr class="bg-light">
                                       
                                        <th  style="min-width:50px">P.In</th>
                                        <th  style="min-width:50px">In</th>
                                        <th  style="min-width:50px">Ok </th>	
                                        <th  style="min-width:80px">P. Inhouse</th>	
                                        <th  style="min-width:100px">P. Outsource</th>
                                        <th  style="min-width:100px">P. Movement</th>
                                        <th  style="min-width:50px">Rej</th>
                                        <th  style="min-width:50px">RW</th>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </thead>
                                <tbody>

                                    <?php
                                    if(empty($row->process_id)){
                                        ?>
                                        <th colspan="9" class="text-left"><?=(!empty($rmData))?number_format(($rmData->received_qty-($rmData->used_qty+$rmData->return_qty)),3).' '.$rmData->unit_name:0?></th>
                                        <?php
                                    }else{
                                    ?>
                                    <tr>
                                       
                                        <td><?=$row->unaccepted_qty?></td>
                                        <td><?=$row->accepted_qty?></td>
                                        <td><?=$row->ok_qty?></td>
                                        <td><?=$row->inhouse_prod_pending?></td>
                                        <td><?=$row->vendor_prod_pending?></td>
                                        <td><?=$row->pending_movement?></td>																			
                                        <td><?=$row->total_rejection_qty?></td>
                                        <td><?=$row->total_rework_qty?></td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
    <?php   endforeach;
        endif;
        ?>
		
	
</div>
<!-- End Main Content -->

<div class="modal fade action-sheet" id="aceptForm" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Accept Inward</h5>
			</div>
			<div class="modal-body">
				<div class="action-sheet-content">
					<form id="acceptedInwatd">
                        <div  class="float-right">	
                                <span class="float-right">Unaccepted Qty. : <span id="pending_act_qty">0</span></span>
                        </div>
                        <input type="hidden" name="job_approval_id" id="job_approval_id">
						<input type="hidden" name="trans_type" value="1">
						<div class="form-group basic">
							<label class="label">Quantity </label>
                           
							<div class="input-group">
                                <input type="text" name="in_qty" id="in_qty" class="form-control floatOnly" value="">
							</div>
							<!-- <div class="input-info">Enter the ID of the  bill you want to add.</div> -->
						</div>
					</form>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-danger btn-close save-form" data-bs-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="saveAcceptedQty('acceptedInwatd');"><i class="fa fa-check"></i> Save</button>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>

<script>
$(document).ready(function(){
	var qsRegex;
	var isoOptions = {
		itemSelector: '.listItem',
		layoutMode: 'fitRows',
		filter: function() {return qsRegex ? $(this).text().match( qsRegex ) : true;}
	};
	// init isotope
	var $grid = $('.list-grid').isotope( isoOptions );
	var $qs = $('.quicksearch').keyup( debounce( function() {qsRegex = new RegExp( $qs.val(), 'gi' );$grid.isotope();}, 200 ) );

//$(document).on('keyup',".quicksearch",function(){console.log($(this).val());});
});

function searchItems(ele){
	console.log($(ele).val());
}

function debounce( fn, threshold ) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
	clearTimeout( timeout );
	var args = arguments;
	var _this = this;
	function delayed() {fn.apply( _this, args );}
	timeout = setTimeout( delayed, threshold );
  };
}

function acceptInward(data){
    $("#aceptForm").modal('toggle');
	$("#aceptForm  #job_approval_id").val(data.id);
	$("#aceptForm  #pending_act_qty").html(data.pending_qty);
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
            $("#aceptForm").modal('hide');	
            // toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });	
            window.location.reload();
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

</script>