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
                                    <li class="nav-item"> <button onclick="statusTab('gaugeTable',0);" class="nav-link btn btn-outline-info active" data-toggle="tab" aria-expanded="false">ALL GAUGES</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('gaugeTable',1);" class="nav-link btn btn-outline-success" data-toggle="tab" aria-expanded="false">Due For Calibration</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">GAUGES</h4>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <select id="category_id_filter" class="form-control single-select" style="width:50%">
                                        <option value="">Select All</option>
                                        <?php
                                            foreach ($categoryList as $row) :
                                                $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                                                echo '<option value="' . $row->id . '" data-cat_name="'.$row->category_name.'" ' . $selected . '>' . $row->category_name . '</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right permission-write addNew" data-button="both" data-modal_id="modal-lg" data-function="addGauge" data-form_title="Add Gauge"><i class="fa fa-plus"></i> Add Gauge</button>
                                </div>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='gaugeTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	//$(document).on('change','#category_id_filter',function(){initGaugeTable($(this).val());});
	$(document).on("change","#category_id_filter",function(){
		$("#gaugeTable").attr("data-url",'/getDTRows/0/'+$(this).val());
		initTable();
	});
});

function initGaugeTable(category_id){;
    $('.ssTable').dataTable().fnDestroy();
    var tableOptions = {pageLength: 25,'stateSave':false};
    var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
    var dataSet = {category_id:category_id};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
}
</script>