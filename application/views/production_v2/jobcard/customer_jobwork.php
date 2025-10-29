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
                                    <li class="nav-item"> <button onclick="statusCustomerJobTab('customerJobcardTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusCustomerJobTab('customerJobcardTable',4);" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false"> NPD </button> </li>
                                    <li class="nav-item"> <button onclick="statusCustomerJobTab('customerJobcardTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusCustomerJobTab('customerJobcardTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Short Close</button> </li>
                                    <li class="nav-item"> <button onclick="statusCustomerJobTab('customerJobcardTable',3);" class=" btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-toggle="tab" aria-expanded="false">On-Hold</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Job Work Customer (New)</h4>
                            </div>
                            <div class="col-md-4">
							    <?php if($shortYear == "22-23"): ?>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addJobcard" data-form_title="Add Job"><i class="fa fa-plus"></i> Add Job</button>
                                <?php endif; ?>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='customerJobcardTable' class="table table-bordered ssTable" data-url='/customerJobWorkList'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="material-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Material Request</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success save-form" onclick="materialRequest('materialRequest','saveMaterialRequest');"><i class="fa fa-check"></i> Send</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="lastActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document" style="min-width: 90%;">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Last Activity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12"><b>Job No : <span id="jobNo"></span></b></div>
                <div class="col-md-12">
                    <div class="error general"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-info">
                                <tr class="text-center">
                                    <th style="width:5%;">#</th>
                                    <th style="width:10%;">Date</th>
                                    <th>Entry Type</th>
                                    <th>OK Qty.</th>
                                    <th>UD Qty.</th>
                                    <th>Rej. Qty.</th>
                                    <th>Rew. Qty.</th>
                                    <th>Prod. Time</th>
                                    <th>Shift</th>
                                    <th>Operator</th>
                                    <th>Machine</th>
                                    <th>Inserted By</th>
                                    <th>Activity</th>
                                </tr>
                            </thead>
                            <tbody id="activityData">
                                <tr>
                                    <td class="text-center" colspan="10">No Data Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/production_v2/job-card.js?v=<?=time()?>"></script>
<script>
function statusCustomerJobTab(tableId,status){
    $("#"+tableId).attr("data-url",'/customerJobWorkList/'+status);
    ssTable.state.clear();initTable();
}
</script>