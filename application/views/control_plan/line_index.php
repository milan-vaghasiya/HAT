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
                                    <li class="nav-item"> <button onclick="tabChange('lineInspectionTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Inprocess Inspection</button> </li>
                                    <li class="nav-item"> <button onclick="tabChange('lineInspectionTable',2);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Setup Approval</button> </li>
                                </ul>
                            </div>        
                            <div class="col-md-6">
                                <a href="<?=base_url($headData->controller."/addLineInspection")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add SAR/IPR</a>
                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='lineInspectionTable' class="table table-bordered ssTable" data-url='/getLineDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    function tabChange(tableId,status){
        $("#"+tableId).attr("data-url",'/getLineDTRows/'+status);
        ssTable.state.clear();initTable();
    }
</script>