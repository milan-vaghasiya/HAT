<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('empLoanTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('empLoanTable',1);" class="nav-link btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Approved</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('empLoanTable',2);" class="nav-link btn waves-effect waves-light btn-outline-info" data-toggle="tab" aria-expanded="false">Senctioned</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-lg" data-function="addLoan" data-form_title="Add Employee Loan"><i class="fa fa-plus"></i> Add Loan</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='empLoanTable' class="table table-bordered ssTable" data-url="/getDTRows"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
   
</script>