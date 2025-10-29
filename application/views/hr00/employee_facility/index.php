<?php $this->load->view('includes/header'); ?>
<style>
	.select2-selection{
		height: auto !important;
		padding: 5px;
	}
	#itemProcess tbody tr:hover{cursor:pointer;}
	.selected
	{
		background-color: #666;
		color: #ffffff !important;
		width:100%;
	}
	.selected td
	{
		background-color: #666;
		color: #ffffff !important;
	}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Employee Facility</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-md" data-function="addEmployeeFacility" data-form_title="Add Employee Facility"><i class="fa fa-plus"></i> Add Employee Facility</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='facilityTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
