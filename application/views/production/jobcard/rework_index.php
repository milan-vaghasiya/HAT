<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="reworkStatusTab('reworkTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="reworkStatusTab('reworkTable',1);" class=" btn waves-effect waves-light btn-outline-warning" style="outline:0px" data-toggle="tab" aria-expanded="false"> Completed </button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title ">Rework</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reworkTable' class="table table-bordered ssTable" data-url='/getReworkDTRow'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    
function reworkStatusTab(tableId,status,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getReworkDTRow/'+status);
    ssTable.state.clear();initTable(srnoPosition);
}
</script>