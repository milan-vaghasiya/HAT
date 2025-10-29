<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <!-- <div class="col-md-4">
								<ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('jobWorkInspectionTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('jobWorkInspectionTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div> -->        
							<div class="col-md-9">
                                <h4 class="card-title">Incoming Inspection Report</h4>
                            </div>
                            <div class="col-md-3">
                                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                                    <option value="">Select All Job Card</option>
                                    <?php 
                                        foreach($jobData as $row):
                                            echo '<option value="'.$row->id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='jobWorkInspectionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
    var job_card_id = $("#job_card_id").val();
    $("#jobWorkInspectionTable").attr("data-url",'/getDTRows/'+job_card_id);
    initTable();
    $(document).on('change',"#job_card_id",function(){
        var job_card_id = $(this).val();
        $("#jobWorkInspectionTable").attr("data-url",'/getDTRows/'+job_card_id);
        initTable();
    });
});
</script>