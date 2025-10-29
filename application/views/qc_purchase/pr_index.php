<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"> 
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">PURCHASE REQUEST (QC)</h4>
                            </div>
                            <div class="col-md-6">
                                <!--<button type="button" class="btn waves-effect waves-light btn-outline-primary float-right permission-write addNew" data-button="both" data-modal_id="modal-lg" data-function="addGauge" data-form_title="Add Gauge"><i class="fa fa-plus"></i> Add Gauge</button>-->
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right permission-write addNew" data-button="both" data-modal_id="modal-md" data-function="addPurchaseRequest" data-form_title="Add Purchase Request"><i class="fa fa-plus"></i> Add Purchase Request</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='qcprTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>