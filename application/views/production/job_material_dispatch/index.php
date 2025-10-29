<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"> 
                                            <a href="<?=base_url("production/jobMaterialDispatch/index/0")?>" class=" btn waves-effect waves-light btn-outline-info <?=($status == 0)?"active":""?>" style="outline:0px">Material Request</a> 
                                        </li>
                                        <li class="nav-item"> 
                                            <a href="<?=base_url("production/jobMaterialDispatch/index/1")?>" class=" btn waves-effect waves-light btn-outline-success <?=($status == 1)?"active":""?>" style="outline:0px">Material Issued</a> 
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h4 class="card-title text-center">Job Material Issue</h4>
                            </div>
                            <div class="col-md-3">
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addDispatch" data-form_title="General Issue"><i class="fa fa-plus"></i> General Issue</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='jobMaterialDispatchTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-material-dispatch.js?v=<?= time() ?>"></script>
<script>
$(document).ready(function(){
    $(document).on('change', '#used_at', function() {
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

    $(document).on('keyup change','.batchQty',function(){
        var qtyArray = $(".batchQty").map(function () { return $(this).val(); }).get();
        var qtySum = 0;
        $.each(qtyArray, function () { qtySum += parseFloat(this) || 0; });
        $("#totalQty").html(qtySum.toFixed(2));
    });
});
</script>