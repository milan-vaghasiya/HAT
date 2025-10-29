<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Tools Issue</h4>
                            </div>
                            <div class="col-md-6">                            
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addDispatch" data-form_title="Tools Issue"><i class="fa fa-plus"></i> Tools Issue</button>
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write mr-2" data-button="both" data-modal_id="modal-md" data-function="addPurchaseRequest" data-form_title="Purchase Request" data-fnsave="savePurchaseRequest"><i class="fa fa-plus"></i> Purchase Request</button> -->
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='toolsIssueTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/tools-issue.js?v=<?=time()?>"></script>