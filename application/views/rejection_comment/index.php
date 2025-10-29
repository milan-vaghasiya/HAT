<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Rejection/Rework Reason</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addRejectionComment" data-form_title="Add Rejection/Rework Reason"><i class="fa fa-plus"></i> Add Reason</button>
                            </div>                              
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='rejectionTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$type?>'></table>
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
    $(document).on('change',"#type",function(){
        if($(this).val() == 1){ $("#subTypeDiv").show(); }
        else{ $("#subTypeDiv").hide(); }
    });
});
</script>