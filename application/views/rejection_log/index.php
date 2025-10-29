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
                                    <li class="nav-item"> <button onclick="statusTab('rejectionlogTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Inhouse</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('rejectionlogTable',3);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Vendor</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('rejectionlogTable',2);" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">NC Report</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Rejection Log </h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addLog" data-form_title="Add Rejection Log"><i class="fa fa-plus"></i> Add Rejection Log</button>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='rejectionlogTable' class="table table-bordered ssTable ssTable-cf" data-ninput="[0,1]"  data-srowposition = "1" data-url='/getDTRows' ></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url(); ?>assets/js/custom/rejection-log.js?v=<?= time() ?>"></script>