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
                                    <li class="nav-item"> <button onclick="statusTab('instrumentTable',0);" class="nav-link btn btn-outline-info active" data-toggle="tab" aria-expanded="false">ALL Instruments</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('instrumentTable',1);" class="nav-link btn btn-outline-success" data-toggle="tab" aria-expanded="false">Due For Calibration</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Instruments/Equipments</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addInstrument" data-form_title="Add Instrument"><i class="fa fa-plus"></i> Add Instrument</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='instrumentTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>