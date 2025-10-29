<?php $this->load->view('app/includes/header');?>
<style>label, input, select, .select2-container,.table,td,th{font-size:12px !important;}</style>
<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white">Log [<?=(getPrefixNumber($dataRow->job_prefix,$dataRow->job_no))?> - <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?>]</div>
	
</div>

<div class="extraHeader pe-0 ps-0">
    <table class=" table jptable-bordered" >
        <tbody>
            <tr class="in_process_id">
                <th class="text-center bg-light" >Process</th>
                <td class="text-left" >
                    <?= (!empty($dataRow->in_process_name)) ? $dataRow->in_process_name : "" ?> ->
                    <?= (!empty($dataRow->out_process_name)) ? $dataRow->out_process_name : "Store Location" ?>
                </td>
                <th class="text-center bg-light" >Qty.</th>
                <td class="text-left" id="pending_qty" ><?= (!empty($dataRow->pqty)) ? $dataRow->pqty : "" ?></td>
            </tr>
        </tbody>
    </table>
</div>

<div id="appCapsule" class=" extra-header-active full-height">
    <div class="card">
        <div class="card-body">
            <form  id="outWard">
                <!-- Comman Hidden Field -->
                <input type="hidden" name="from_entry" id="from_entry" value="1">
                <input type="hidden" id="entry_type" name="entry_type" value="<?= (!empty($dataRow->entry_type)) ? $dataRow->entry_type : 0 ?>">
                <input type="hidden" id="trans_type" name="trans_type" value="<?= (!empty($dataRow->trans_type)) ? $dataRow->trans_type : 0 ?>">
                <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : '' ?>">
                <input type="hidden" id="vendor_id" name="vendor_id" value="<?= (!empty($dataRow->vendor_id)) ? $dataRow->vendor_id : '' ?>">
                <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : "" ?>">
                <input type="hidden" name="product_id" id="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : "" ?>" />
                <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>">
                <input type="hidden" id="in_process_id" name="in_process_id" value="<?= (!empty($dataRow->in_process_id)) ? $dataRow->in_process_id : "0" ?>">
                <input type="hidden" id="out_process_id" name="out_process_id" value="<?= (!empty($dataRow->out_process_id)) ? $dataRow->out_process_id : "0" ?>">
                <!-- <input type="hidden" name="cycle_time" id="cycle_time" class="form-control floatOnly" value="<?= (!empty($cycle_time) ? $cycle_time : 0) ?>"> -->
                <input type="hidden" name="load_unload_time" id="load_unload_time" class="form-control floatOnly" value="0">
                <div class="error general_error col-md-12"></div>
                    <!-- Comman I/P From Logsheet and OK Movement Form -->
                <div class="row">
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="entry_date">Date</label>
                            <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= $maxDate ?>" min="<?= $startYearDate ?>" max="<?= $maxDate ?>">
                        </div>
                    </div>
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="production_qty">Prod. Qty</label>
                            <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly req qtyCal" value="">
                        </div>
                    </div>
                </div>
                <div class="row">   
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="out_qty">Ok Qty</label>
                            <input type="text" name="out_qty" id="out_qty" class="form-control numericOnly req " readonly value="">
                            <div class="error batch_stock_error"></div>
                        </div>  
                    </div>
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="hold_qty">Suspected Qty</label>
                            <input type="text" name="hold_qty" id="hold_qty" class="form-control qtyCal floatOnly">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="cycle_time">C.T. (Sec)</label>
                            <input type="text" name="cycle_time" id="cycle_time" class="form-control numericOnly " placeholder="Cycle Time" value="">
                            <div class="error cycle_time"></div> 
                        </div>   
                    </div>
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="start_time">Start Time</label>
                            <input type="time" name="start_time" id="start_time" class="form-control " value="">
                            <div class="error start_time"></div>
                        </div>
                    </div> 
                    <div class="col form-group basicBox  ">
                        <div class="input-wrapper">
                            <label for="production_time">Prod. Time (Min)</label>
                            <input type="text" name="production_time" id="production_time" class="form-control numericOnly " value="">
                            <div class="error production_time"></div>
                        </div>
                    </div>
                </div>
                <div class="row">

                        <div class="col form-group basicBox  ">
                            <div class="input-wrapper">
                                <label for="machine_id">Machine</label>
                                <select name="machine_id" id="machine_id" class="form-control select2 asignOperator">
                                    <option value="">Select Machine</option>
                                    <?php
                                    if (!empty($machineData)) {
                                        foreach ($machineData as $row) :
                                            $selected = (!empty($machine->machine_id) && $machine->machine_id ==$row->id)?'selected':'';
                                            $machineName = (!empty($row->item_code) ? '[' . $row->item_code . '] ' : "") . $row->item_name;
                                            echo '<option value="' . $row->id . '" '.$selected.'>' . $machineName . '</option>';
                                        endforeach;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="row">
                        <div class="col form-group basicBox  ">
                            <div class="input-wrapper">
                                <label for="shift_id">Shift</label>
                                <select name="shift_id" id="shift_id" class="form-control select2 asignOperator req">
                                    <option value="">Select Shift</option>
                                    <?php
                                    foreach ($shiftData as $row) :
                                        $selected = (!empty($dataRow->shift_id) && $dataRow->shift_id == $row->id) ? "selected" : "";
                                        $production_time = floatVal($row->production_hour) * 60;
                                        echo '<option value="' . $row->id . '" ' . $selected . ' data-production_time="' . $production_time . '">' . $row->shift_name . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                                <div class="error shift_id"></div>
                            </div>
                        </div>
                        <div class="col form-group basicBox  ">
                            <div class="input-wrapper">
                                <label for="operator_id">Operator</label>
                                <select name="operator_id" id="operator_id" class="form-control select2 req">
                                    <option value="">Select Operator</option>
                                    <?php
                                    foreach ($operatorList as $row) :
                                        $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                                <div class="error operator_id"></div>
                            </div>
                        </div>
                </div>

                    <?php
                        if($dataRow->trans_type == 1):
                    ?>
                
                <div class="row " >
                    <div class="col form-group  basicBox">
                        <div class="accordion" id="accordionExample1" >
                            <div class="accordion-item ">
                                <h2 class="accordion-header  bg-secondary">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rejection" aria-expanded="false">
                                    Rejection
                                    </button>
                                </h2>
                                <div id="rejection" class="accordion-collapse collapse" data-bs-parent="#accordionExample1" style="">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <input type="hidden" name="rej_qty" id="total_rej_qty" value="0">
                                            
                                            <div class="col form-group basicBox">
                                                <label for="rej_qty">Qty</label>
                                                <input type="text" id="rej_qty" class="form-control numericOnly qtyCal" value="">
                                                <div class="error rej_qty"></div>
                                            </div>

                                            <div class="col  form-group basicBox">
                                                <label for="rr_reason">Reason</label>
                                                <select id="rr_reason" class="form-control select2 req">
                                                    <option value="">Select Reason</option>
                                                    <?php
                                                    foreach ($rejectionComments as $row) :
                                                        $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                                                        echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';
                                                    endforeach;
                                                    ?>
                                                </select>
                                                <div class="error rr_reason"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col  form-group basicBox">
                                                <label for="rej_type">Type</label>
                                                <select id="rej_type" class="form-control req">
                                                    <option value="">Select type</option>
                                                    <option value="1">Machine</option>
                                                    <option value="2">Raw Material</option>
                                                </select>
                                                <div class="error rej_type"></div>
                                            </div>
                                        
                                            <div class="col form-group basicBox">
                                                <label for="opr_mt">OPR./MT. Type</label>
                                                <select id="opr_mt"  class="form-control req select2">
                                                    <option value="Other">Other</option>
                                                    <?php
                                                    foreach($this->OPR_MT_TYPE as $key=>$value){
                                                            ?> <optgroup label="<?=$key?>" >
                                                                <?php foreach($OPR_MT_TYPE[$key] as $opr=>$type){
                                                                    ?>  <option value="<?=$type?>"><?=$type?></option> <?php
                                                                } ?>
                                                            </optgroup> <?php
                                                    } ?>
                                                </select>
                                                <div class="error rej_type"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col form-group basicBox">
                                                <label for="rr_stage">Rejection Stage</label>
                                                <select id="rr_stage" class="form-control select2 req">
                                                    <?php if (empty($dataRow->stage)) { ?> 
                                                        <option value="">Select Stage</option> 
                                                    <?php } else {
                                                        echo $dataRow->stage;
                                                    } ?>
                                                </select>
                                                <div class="error rr_stage"></div>
                                            </div>

                                            <div class="col form-group basicBox">
                                                <label for="rr_by">Rejection By <span class="text-danger">*</span></label>
                                                <select id="rr_by" class="form-control select2 req">
                                                    <option value="">Select Rej. From</option>
                                                </select>
                                                <div class="error rr_by"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col form-group basicBox">
                                                <label for="r_remark">Variance</label>
                                                <div class="input-group-append">
                                                    <input type="text" id="r_remark" class="form-control req" value="" >
                                                    
                                                </div>
                                                
                                            </div>

                                        </div>
                                        <div class="row justify-content-end">
                                         
                                            <div class="col form-group basicBox">
                                                <button type="button" id="addRejectionRow" class="btn btn-outline-secondary btn-md me-1" ><i class="fa fa-plus"></i> ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col form-group basicBox">
                                                <div class="table-responsive">
                                                    <table id="rejectionReason" class="table table-bordered">
                                                        <thead class="bg-secondary">
                                                            <tr>
                                                                <th>#</th>
                                                                <th style="min-width:100px">Rej Qty.</th>
                                                                <th style="min-width:150px">Reason</th>
                                                                <th style="min-width:150px">Type</th>
                                                                <th style="min-width:150px">OPR./MT.</th>
                                                                <th style="min-width:150px">Stage</th>
                                                                <th style="min-width:200px">Rejection By</th>
                                                                <th style="min-width:200px">Variance</th>
                                                                <th style="min-width:50px">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="rejData">
                                                            <tr id="noData">
                                                                <td class="text-center" colspan="9">No data available in table</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header bg-info">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rework" aria-expanded="false">
                                        Rework
                                    </button>
                                </h2>
                                <div id="rework" class="accordion-collapse collapse" data-bs-parent="#accordionExample1" style="">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <input type="hidden" name="rw_qty" id="total_rw_qty" value="0">

                                            <div class="col form-group basicBox">
                                                <label for="rw_qty">Rework Qty</label>
                                                <input type="text" id="rw_qty" class="form-control req numericOnly qtyCal" value="">
                                                <div class="error rw_qty"></div>
                                            </div>

                                            <div class="col form-group basicBox">
                                                <label for="rw_reason">Rework Reason</label>
                                                <select id="rw_reason" class="form-control select2 req">
                                                    <option value="">Select Reason</option>
                                                    <?php
                                                        foreach ($reworkComment as $row) :
                                                            $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                                                            echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';
                                                        endforeach;
                                                    ?>
                                                </select>
                                                <div class="error rw_reason"></div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col form-group basicBox">
                                                <label for="rw_stage">Rework Stage</label>
                                                <select id="rw_stage" class="form-control select2 req">
                                                    <?php if (empty($dataRow->stage)) { ?> 
                                                        <option value="">Select Stage</option> 
                                                    <?php } else {
                                                        echo $dataRow->stage;
                                                    } ?>
                                                </select>
                                                <div class="error rw_stage"></div>
                                            </div>

                                            <div class="col form-group basicBox">
                                                <label for="rw_by">Rework By <span class="text-danger">*</span></label>
                                                <select id="rw_by" class="form-control select2 req">
                                                    <option value="">Select RW. From</option>
                                                </select>
                                                <div class="error rw_by"></div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col form-group basicBox">
                                                <label for="rw_process_id">Rework Process</label>
                                                <select  id="rw_process_id" class="form-control select2 req">
                                                    <?php echo $dataRow->reworkProcess; ?>
                                                </select>
                                                <div class="error rw_process_id"></div>
                                            </div>

                                            <div class="col  form-group basicBox">
                                                <label for="remark">Variance </label>
                                                <input type="text" id="rw_remark" class="form-control" value="">
                                                <div class="error rw_remark"></div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col form-group basicBox">
                                                <button type="button" id="addReworkRow" class="btn btn-outline-info  btn-md me-1"><i class="fa fa-plus"></i> ADD</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col form-group basicBox">
                                                <div class="table-responsive">
                                                    <table  id="reworkReason" class="table table-bordered">
                                                        <thead class="thead-info">
                                                            <tr>
                                                                <th >#</th>
                                                                <th style="min-width:100px">RW Qty.</th>
                                                                <th style="min-width:100px">Reason</th>
                                                                <th style="min-width:100px">Stage</th>
                                                                <th style="min-width:100px">Rework By</th>
                                                                <th style="min-width:100px">Process</th>
                                                                <th style="min-width:200px">Variance</th>
                                                                <th style="min-width:100px">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="rwData">
                                                            <tr id="noData">
                                                                <td class="text-center" colspan="8">No data available in table</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     
                </div>     
                <?php
                    endif;
                ?>

                    
                    <div class="row">            
                        <div class="col form-group basicBox">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control" value="">
                        </div>
                    </div>
                    <div class="row">      
                        <div class="col form-group basicBox">
                            <button type="button" class="btn btn-primary me-1 mb-1 save-form" onclick="saveOutward('outWard')" style="padding:5px 40px;"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col form-group basicBox">

                </div>
                    <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 15px;">Process Transaction :</h5>
                    <div class="table-responsive">
                        <table id='outwardTransTable' class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Send To</th>
                                    <th>Production Time</th>
                                    <!-- <th>Machine</th>
                                    <th>Operator</th>
                                    <th>Shift</th> -->
                                    <th>Out Qty.</th>
                                    <th>Rejection Qty.</th>
                                    <th>Rework Qty.</th>
                                    <th>Hold Qty.</th>
                                    <th>Remark</th>
                                    <th style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="outwardTransData">
                                <?php
                                    $html = "";
                                    $i = 1;
                                    if (!empty($outwardTrans)) :
                                        
                                        echo $outwardTrans;
                                    else :
                                ?>
                                    <td colspan="14" class="text-center">No Data Found.</td>
                                <?php
                                    endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>
<script>
$(document).ready(function () {
    
	$(document).on("change", "#rejection_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url +  'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").select2();
                }
            });
        } else {
            $("#rej_from").html("<option value=''>Select Rej. From</option>");
            $("#rej_from").select2(); 
        }
    });

    $(document).on("change", "#rework_stage", function () {
        var process_id = $(this).val();
        var part_id = $("#product_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + 'production/processMovement/getRejRWBy',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#rw_from").html("");
                    $("#rw_from").html(data.rejOption);
                    $("#rw_from").select2();
                }
            });
        } else {
            $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").select2();
        }
    });
	
	$(document).on('click', "#addReworkRow", function () {
        var rw_qty = $("#rw_qty").val();
        var rw_reason = $("#rw_reason :selected").val();
        var rw_reason_text = $("#rw_reason :selected").text();
		var rw_stage = $("#rw_stage :selected").val();
		var rw_stage_text = $("#rw_stage :selected").text();
		var rw_dimension_range = $("#rw_dimension_range :selected").val();
		var rw_dimension_range_text = $("#rw_dimension_range :selected").text();
		var rw_by = $("#rw_by :selected").val();
		var rw_by_text = $("#rw_by :selected").text();
		var rw_process_id = $("#rw_process_id :selected").val();
		var rw_process_id_text = $("#rw_process_id :selected").text();
        var rw_remark = $("#rw_remark").val();

        var valid = 1;

        $(".rw_qty").html("");
        if (parseFloat(rw_qty) <= 0 || rw_qty == '') {
            $(".rw_qty").html("Rework Qty is required.");
            valid = 0;
        }

        $(".rw_reason").html("");
        if (rw_reason == "") {
            $(".rw_reason").html("Rework Reason is required.");
            valid = 0;
        }

        $(".rw_stage").html("");
        if (rw_stage == "") {
            $(".rw_stage").html("Rework Stage is required.");
            valid = 0;
        }

        $(".rw_by").html("");
        if (rw_by == "") {
            $(".rw_by").html("Rework By is required.");
            valid = 0;
        }

		$(".rw_process_id").html("");
        if (rw_process_id == "") {
            $(".rw_process_id").html("Rework Process is required.");
            valid = 0;
        }

        if (valid == 1) {

            var postData = {
                rw_qty: rw_qty,
                rr_reason: rw_reason,
                rr_reason_text: rw_reason_text,
                rr_stage: rw_stage,
                rr_stage_text: rw_stage_text,
                rr_by: rw_by,
                rr_by_text: rw_by_text,
                rw_process_id: rw_process_id,
                rw_process_id_text: rw_process_id_text,
                dimension_range: rw_dimension_range,
                dimension_range_text: rw_dimension_range_text,
				remark : rw_remark
            };


            AddRowRework(postData);
			$("#rw_qty").val("");
			$("#rw_reason").val("");
			$("#rw_reason").select2();
			$("#rw_stage").val("");
			$("#rw_stage").select2();
			$("#rw_dimension_range").val("");
			$("#rw_dimension_range").select2();
			$("#rw_by").val("");
			$("#rw_by").select2();
			$("#rw_process_id").val("");
			$("#rw_process_id").select2();
			$("#rw_remark").val("");
            $("#rw_qty").focus();

        }
    });


    $(document).on('click', "#addRejectionRow", function () {
        var rej_qty = $("#rej_qty").val();
        var rr_reason = $("#rr_reason :selected").val();
        var rr_reason_text = $("#rr_reason :selected").text();
		var rej_type = $("#rej_type :selected").val();
		var rej_type_text = $("#rej_type :selected").text();
		var rr_stage = $("#rr_stage :selected").val();
		var rr_stage_text = $("#rr_stage :selected").text();
		var dimension_range = $("#dimension_range :selected").val();
		var dimension_range_text = $("#dimension_range :selected").text();
		var rr_by = $("#rr_by :selected").val();
		var rr_by_text = $("#rr_by :selected").text();
		var r_remark = $("#r_remark").val();

        var valid = 1;

        $(".rej_qty").html("");
        if (parseFloat(rej_qty) <= 0 || rej_qty == '') {
            $(".rej_qty").html("Rejection Qty is required.");
            valid = 0;
        }

        $(".rr_reason").html("");
        if (rr_reason == "") {
            $(".rr_reason").html("Rejection Reason is required.");
            valid = 0;
        }

        $(".rej_type").html("");
        if (rej_type == "") {
            $(".rej_type").html("Rejection Type is required.");
            valid = 0;
        }

        $(".rr_stage").html("");
        if (rr_stage == "") {
            $(".rr_stage").html("Rejection Stage is required.");
            valid = 0;
        }

		$(".rr_by").html("");
        if (rr_stage == "") {
            $(".rr_by").html("Rejection By is required.");
            valid = 0;
        }

		var opr_mt = $("#opr_mt").val();
        if (valid == 1) {
            var postData = {
                rej_qty:rej_qty,
				rr_reason:rr_reason,
				rr_reason_text:rr_reason_text,
				rej_type:rej_type,
				rej_type_text:rej_type_text,
				rr_stage:rr_stage,
				rr_stage_text:rr_stage_text,
				dimension_range:dimension_range,
				dimension_range_text:dimension_range_text,
				rr_by:rr_by,
				rr_by_text:rr_by_text,
				r_remark:r_remark,
				opr_mt:opr_mt
            };
            AddRowRejection(postData);
            $("#rej_qty").val("");
            $("#rr_reason").val("");
            $("#rr_reason").select2();
            $("#rej_type").val("");
            $("#rr_stage").val("");
            $("#rr_stage").select2();
			$("#dimension_range").val("");
            $("#dimension_range").select2();
			$("#rr_by").val("");
            $("#rr_by").select2();
            $("#r_remark").val("");
            $("#rej_qty").focus();
        }
    });


    $(document).on('click', ".openMaterialReturnModal", function () {
		var modalId = $(this).data('modal_id');
		var processName = $(this).data('process_name');
		var pendingQty = $(this).data('pending_qty');
		var item_name = $(this).data('item_name');
		var item_id = $(this).data('item_id');
		var job_card_id = $(this).data('job_card_id');
		var wp_qty=$(this).data('wp_qty');
        var dispatch_id = $(this).data('dispatch_id');
        var id = $(this).data('id');
		$.ajax({
			type: "POST",
			url: base_url + controller + '/materialReturn',
			data: { id:id,job_card_id: job_card_id,item_id:item_id,processName:processName,pendingQty:pendingQty,item_name:item_name,wp_qty:wp_qty,dispatch_id:dispatch_id}
		}).done(function (response) {
			$("#" + modalId).modal({ show: true });
			$("#" + modalId + ' .modal-title').html('Return Material');
			$("#" + modalId + ' .modal-body').html("");
			$("#" + modalId + ' .modal-body').html(response);
			$("#" + modalId + " .modal-body form").attr('id','returnMaterialForm');
			$("#" + modalId + " .modal-footer .btn-close").show();
			$("#" + modalId + " .modal-footer .btn-save").hide();
			$(".single-select").select2();

			$("#" + modalId + " .scrollable").perfectScrollbar({ suppressScrollX: true });
			setTimeout(function () { initMultiSelect(); setPlaceHolder(); }, 5);
		});
	});

    $(document).on('change','#ref_type',function(){
		var ref_type=$(this).val();
		var scrap_location=$("#scrap_store_id").val();
		if(ref_type == 10){
			$(".location").show();
			$(".batchNo").show();
			$("#location_id option").removeAttr("disabled");
			$("#location_id option[value='"+scrap_location+"']").attr("disabled","disabled");
			$("#location_id").select2();
		}
		if(ref_type == 13){
			$(".location").show();
			$('.batchNo').hide();
			$("#location_id").val(scrap_location);
			$("#location_id option").attr("disabled","disabled");
			$("#location_id option[value='"+scrap_location+"']").removeAttr("disabled");
			$("#location_id").select2();
			
		}
		
		if(ref_type==21){
			$('.location').hide();
			$('.batchNo').hide();
			$("#location_id").val("");
			$("#location_id").select2();
			$("#batch_no").val("");
			$("#batch_no").select2();
		}
	});
    
	$(document).on("change", "#rr_stage", function() {
        
		var process_id = $(this).find(":selected").val();//data('process_id');
		var part_id = $("#product_id").val();
		var pfc_id = $(this).val();
		var rej_type = $("#rej_type").val();

		if (process_id) {
			var job_card_id = $("#job_card_id").val();
			$.ajax({
				url: base_url + 'production/primaryCFT/getRRByOptions',
				type: 'post',
				data: {
					process_id: process_id,
					part_id: part_id,
					job_card_id: job_card_id,
					pfc_id: pfc_id,
					rej_type: rej_type
				},
				dataType: 'json',
				success: function(data) {
					$("#rr_by").html("");
					$("#rr_by").html(data.rejOption);
					$("#rr_by").select2();

					/* $("#dimension_range").html("");
					$("#dimension_range").html(data.dimOptions);
					$("#dimension_range").select2(); */
				}
			});
		} else {

			$("#rr_by").html("<option value=''>Select Rej. From</option>");
			$("#rr_by").select2();

			/* $("#dimension_range").html("<option value=''>Select</option>");
			$("#dimension_range").select2(); */

		}
	});
	$(document).on("change", "#rej_type", function() {
        
		$("#rr_stage").val("");
		$("#rr_stage").select2();
		$("#rr_by").val("");
		$("#rr_by").select2();

		var rej_type = $("#rej_type").val();
		if(rej_type == 1){
			$('#opr_mt optgroup[label=Operation]').prop('disabled', false); 
			$('#opr_mt optgroup[label=Material]').prop('disabled', true); 
			
		}else{
			$('#opr_mt optgroup[label=Operation]').prop('disabled', true); 
			$('#opr_mt optgroup[label=Material]').prop('disabled', false); 
			
		}
		$("#opr_mt").select2();
		
	});

	$(document).on("change", "#rw_stage", function() {
		//$("#rr_stage").change(function(){
		var process_id = $(this).find(":selected").val();//data('process_id');
		var part_id = $("#product_id").val();
		var pfc_id = $(this).val();
		if (process_id) {
			var job_card_id = $("#job_card_id").val();
			$.ajax({
				url: base_url + 'production/primaryCFT/getRRByOptions',
				type: 'post',
				data: {
					process_id: process_id,
					part_id: part_id,
					job_card_id: job_card_id,
					pfc_id: pfc_id,
				},
				dataType: 'json',
				success: function(data) {
					$("#rw_by").html("");
					$("#rw_by").html(data.rejOption);
					$("#rw_by").select2();

					
				}
			});
		} else {

			$("#rw_by").html("<option value=''>Select Rej. From</option>");
			$("#rw_by").select2();

			

		}
	});
	

	$(document).on("input",".qtyCal", function(){
        var hold_qty = $("#hold_qty").val() || 0;

		var rejQtyArray = $(".rej_sum").map(function(){return $(this).val();}).get();
		var rej_qty = 0;
		$.each(rejQtyArray,function(){rej_qty += parseFloat(this) || 0;});
		$("#total_rej_qty").val(rej_qty);

		var rwQtyArray = $(".rw_sum").map(function(){return $(this).val();}).get();
		var rw_qty = 0;
		$.each(rwQtyArray,function(){rw_qty += parseFloat(this) || 0;});
		$("#total_rw_qty").val(rw_qty);
        
		var okQty=parseFloat($("#production_qty").val())-rej_qty-rw_qty-hold_qty;      
		$("#out_qty").val(okQty);
    });
});

function saveOutward(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
            $("#pending_qty").html(data.pending_qty);

			$("#out_qty").val("");
			$("#cycle_time").val("");
			$("#production_time").val("");
			$("#remark").val("");
			$("#wp_qty").val("");
			$("#machine_id").val("");
			$("#shift_id").val("");
			$("#operator_id").val("");

            $("#machine_id").select2();
			$("#shift_id").select2();
			$("#operator_id").select2();
            $("#rejectionReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
            $("#reworkReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
			$("#outwardTransData").html(data.outwardTrans);
            $("#DialogIconedSuccess .modal-body").html(data.message);
            $("#DialogIconedSuccess").modal('show');
           	
		}else{
            $("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');		}				
	});
}


function trashOutward(id,functionName,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/'+functionName,
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
                                $("#DialogIconedDanger .modal-body").html(data.message);
                                $("#DialogIconedDanger").modal('show');
							}
							else{
                                $("#DialogIconedSuccess .modal-body").html(data.message);
                                $("#DialogIconedSuccess").modal('show');
								$("#pending_qty").html(data.pending_qty);
								$("#outwardTransData").html(data.outwardTrans);
                                $(".select2").select2();
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}
// For Rework Data
function AddRowRework(data) {

    $('table#reworkReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
    var tblName = "reworkReason";

    var tBody = $("#" + tblName + " > TBODY")[0];


    row = tBody.insertRow(-1);
    var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rework_qty_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][qty]",
        value: data.rw_qty,
        class: "rw_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_qty);
    cell.append(rework_qty_input);
    cell.attr("style", "width:20%;");

    var rr_reason_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_reason]",
        value: data.rr_reason
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_reason_text);
    cell.append(rr_reason_input);
    cell.attr("style", "width:20%;");

    var rr_stage_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_stage]",
        value: data.rr_stage
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_stage_text);
    cell.append(rr_stage_input);
    cell.attr("style", "width:20%;");

    var rr_by_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rr_by]",
        value: data.rr_by
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_by_text);
    cell.append(rr_by_input);

    var rw_process_id_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_process_id]",
        value: data.rw_process_id
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_process_id_text);
    cell.append(rw_process_id_input);
    cell.attr("style", "width:20%;");

    var dimension_range_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][dimension_range]",
        value: data.dimension_range
    });
    var remark_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][remark]",
        value: data.remark
    });
    cell = $(row.insertCell(-1));
    cell.html(data.remark);
    cell.append(dimension_range_input);
    cell.append(remark_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));
    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRework(this);");
    btnRemove.attr("style", "margin-left:2px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:5%;");

    $(".qtyCal").trigger('input');
}

function RemoveRework(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#reworkReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#reworkReason tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#reworkReason tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#reworkReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
    }

    $(".qtyCal").trigger('input');
};

// For Rejection Data
function AddRowRejection(data) {
    //Get the reference of the Table's TBODY element.
    var tblName = "rejectionReason";

    $('table#'+ tblName +' tr#noData').remove();    

    var tBody = $("#" + tblName + " > tbody")[0];
    row = tBody.insertRow(-1);

    var index = $('#' + tblName + ' tbody tr:last').index();
    var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
    cell.html(countRow);
    cell.attr("style", "width:5%;");

    var rejection_qty_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][qty]",
        value: data.rej_qty,
        class: "rej_sum"
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_qty);
    cell.append(rejection_qty_input);
    cell.attr("style", "width:20%;");

    var rej_reason_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_reason]",
        value: data.rr_reason
    });    
    cell = $(row.insertCell(-1));
    cell.html(data.rr_reason_text);
    cell.append(rej_reason_input);
    cell.attr("style", "width:20%;");

    var rej_type_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_type]",
        value: data.rej_type
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_type_text);
    cell.append(rej_type_input);
    cell.attr("style", "width:20%;");

    var opr_mt_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][opr_mt]",
        value: data.opr_mt
    });
    cell = $(row.insertCell(-1));
    cell.html(data.opr_mt);
    cell.append(opr_mt_input);

    var rr_stage_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_stage]",
        value: data.rr_stage
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_stage_text);
    cell.append(rr_stage_input);
    cell.attr("style", "width:20%;");

    var rr_by_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rr_by]",
        value: data.rr_by
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rr_by_text);
    cell.append(rr_by_input);
    cell.attr("style", "width:20%;");

    var rej_remark_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][remark]",
        value: data.r_remark
    });
    var dimension_range_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][dimension_range]",
        value: data.dimension_range
    });
    cell = $(row.insertCell(-1));
    cell.html(data.r_remark);
    cell.append(rej_remark_input);
    cell.append(dimension_range_input);
    cell.attr("style", "width:20%;");

    //Add Button cell.
    cell = $(row.insertCell(-1));


    var btnRemove = $('<button><i class="ti-trash"></i></button>');
    btnRemove.attr("type", "button");
    btnRemove.attr("onclick", "RemoveRejection(this);");
    btnRemove.attr("style", "margin-left:4px;");
    btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class", "text-center");
    cell.attr("style", "width:15%;");

    $(".qtyCal").trigger('input');
}

function RemoveRejection(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#rejectionReason")[0];
    table.deleteRow(row[0].rowIndex);
    $('#rejectionReason tbody tr td:nth-child(1)').each(function (idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#rejectionReason tbody tr:last').index() + 1;
    if (countTR == 0) {
        $("#rejectionReason tbody").html('<tr id="noData"><td colspan="8" class="text-center">No data available in table</td></tr>');
    }
    $(".qtyCal").trigger('input');
};

</script>