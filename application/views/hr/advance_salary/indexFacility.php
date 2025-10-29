<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
								<a href="<?= base_url($headData->controller . "/index") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write">Advance</a>
								<a href="<?= base_url($headData->controller . "/indexPenalty") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write">Penalty</a>
								<a href="<?= base_url($headData->controller . "/indexFacility") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write active">Facility</a>
                            </div>
                            <div class="col-md-4 text-center">Facility</div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-fnsave="saveFacility" data-modal_id="modal-lg" data-function="addFacility" data-form_title="Add Facility"><i class="fa fa-plus"></i> Add Facility</button>
                            </div>                              
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='advanceSalaryTable' class="table table-bordered ssTable" data-url="/getDTRowsForFacility/0/<?= $type?>"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>