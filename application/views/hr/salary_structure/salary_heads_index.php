<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-4">
								<a href="<?=base_url("hr/salaryStructure")?>" class="btn btn-outline-primary waves-effect waves-light">CTC Format</a>
								<a href="<?=base_url("hr/salaryStructure/heads")?>" class="btn btn-outline-primary waves-effect waves-light">Salary Heads</a>
							</div>
                            <div class="col-md-4 text-center">
                                <h4 class="card-title">Salary Heads</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-md" data-function="addSalaryHead" data-fnsave="saveSalaryHead" data-form_title="Add Salary Head"><i class="fa fa-plus"></i> Add Salary Head</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='salaryHeadTable' class="table table-bordered ssTable bt-switch1" data-url="/getSalaryHeadDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>