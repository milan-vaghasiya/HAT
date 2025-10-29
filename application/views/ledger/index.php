<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Ledger</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addLedger" data-form_title="Add Ledger"><i class="fa fa-plus"></i> Add Ledger</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='ledgerTable' class="table table-bordered ssTable" data-url='/getDtRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script type="text/javascript">
$(document).ready(function(){
    $(document).on('change','#group_id',function(){        
        $("#group_name").val("");
        $("#group_code").val("");

        if($(this).val() != ""){
            var dataRow = $("#group_id :selected").data('row');
            console.log(dataRow);
            $("#group_name").val(dataRow.name);
            $("#group_code").val(dataRow.group_code);
        }
    });

    $(document).on('change','#is_gst_applicable',function(){
		var is_gst_applicable = $(this).val();
		if(is_gst_applicable == 1){
			$('.applicable').removeAttr( 'style' );
		} else {
            $('.applicable').attr('style', 'display: none');
        }
	});
});
</script>