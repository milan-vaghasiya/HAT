<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"> <button onclick="statusTab('jobMaterialDispatchTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                        <li class="nav-item"> <button onclick="statusTab('jobMaterialDispatchTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                        <li class="nav-item"> <button onclick="statusTab('jobMaterialDispatchTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Close</button> </li>
                                    </ul>
                                    <select name="item_type" id="item_type" class="form-control float-right" style="width:24%;">
                                        <option value="">Select All</option>
                                        <option value="2">Consumable</option>
                                        <option value="3">Raw Material</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Job Material Issue</h4>
                            </div>
                            <div class="col-md-4">                            
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addDispatch" data-form_title="Material Issue"><i class="fa fa-plus"></i> Material Issue</button>-->
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write mr-2" data-button="both" data-modal_id="modal-lg" data-function="addPurchaseRequest" data-form_title="Purchase Request" data-fnsave="savePurchaseRequest"><i class="fa fa-plus"></i> Purchase Request</button>
                            </div>                              
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='jobMaterialDispatchTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/job-material-dispatch.js?v=<?=time()?>"></script>