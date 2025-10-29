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
                                    <li class="nav-item">
                                        <button onclick="statusTab('advanceSalaryTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> 
                                    </li>
                                    <li class="nav-item">
                                        <button onclick="statusTab('advanceSalaryTable',1);" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false">Sanctioned</button> 
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= base_url($headData->controller . "/indexPenalty") ?>" class="btn waves-effect waves-light btn-outline-primary">Penalty</a>
                                    </li>
                                    <li class="nav-item">
        								<a href="<?= base_url($headData->controller . "/indexFacility") ?>" class="btn waves-effect waves-light btn-outline-primary">Facility</a>
        							</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="card-title">Advance Salary</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-lg" data-function="addAdvance" data-form_title="Add Advance Salary"><i class="fa fa-plus"></i> Add Advance</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='advanceSalaryTable' class="table table-bordered ssTable" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>