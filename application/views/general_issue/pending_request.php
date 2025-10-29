<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
								<a href="<?= base_url($headData->controller . "/index") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write active">Pending Request</a>
								<a href="<?= base_url($headData->controller . "/materialIssue") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write ">Issue Material</a>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">General Issue</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="issueMaterial" data-form_title="General Issue"><i class="fa fa-plus"></i> General Issue</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='generalIssueTable' class="table table-bordered ssTable" data-url='/getGeneralPendingRequestData'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url(); ?>assets/js/custom/general-material-issue.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function() {
    });
</script>