<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tbody>
        <tr class="in_process_id">
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->job_card_id)) ? $dataRow->job_number : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Product</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Process</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->in_process_name)) ? $dataRow->in_process_name : "" ?> ->
                <?= (!empty($dataRow->out_process_name)) ? $dataRow->out_process_name : "Store Location" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Qty.</th>
            <th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?= (!empty($dataRow->pqty)) ? $dataRow->pqty : "" ?></th>
        </tr>
    </tbody>
</table>
<form style="padding-top:35px;">
    <!-- Comman Hidden Field -->
    <div class="col-md-12">
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
    </div>
    <div class="col-md-12">

        <div class="error general_error col-md-12"></div>
        <!-- Comman I/P From Logsheet and OK Movement Form -->
        <div class="row">
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-3'?> form-group">
                <label for="entry_date">Date</label>
                <!-- <input type="text" class="form-control" value="<?=formatDate($maxDate)?>" readonly> -->
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?= $maxDate ?>" min="<?= $startYearDate ?>" max="<?= $maxDate ?>">
            </div>
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-3'?> form-group">
                <label for="production_qty">Production Qty</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly req qtyCal" value="">
            </div>
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-3'?> form-group">
                <label for="out_qty">Ok Qty</label>
                <input type="text" name="out_qty" id="out_qty" class="form-control numericOnly req " readonly value="">
                <div class="error batch_stock_error"></div>
            </div>

            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-3'?> form-group" <?=($dataRow->trans_type == 2)?'hidden':''?>>
                <label for="hold_qty">Suspected Qty</label>
                <input type="text" name="hold_qty" id="hold_qty" class="form-control qtyCal floatOnly">
            </div>
            
        </div>
        
        <!-- <hr style="width:100%"> Logsheet I/P -->
        <div class="row" <?= (!empty($masterOption->op_mc_shift)  && $masterOption->op_mc_shift == 2) ? "hidden" : "" ?>>
            <div class="col-md-2 form-group">
                <label for="cycle_time">Cycle Time (InSec.)</label>
                <input type="text" name="cycle_time" id="cycle_time" class="form-control numericOnly " value="">
                <div class="error cycle_time"></div>
            </div>
            <div class="col-md-2 form-group">
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control " value="">
                <div class="error start_time"></div>
            </div>
            <div class="col-md-2 form-group">
                <label for="production_time">Prod. Time (In Min.)</label>
                <input type="text" name="production_time" id="production_time" class="form-control numericOnly " value="">
                <div class="error production_time"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select asignOperator">
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
            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select11 asignOperator req">
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
            <div class="col-md-3 form-group">
                <label for="operator_id">Operator</label>
                <select name="operator_id" id="operator_id" class="form-control single-select req">
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

        <?php
            if($dataRow->trans_type == 1):
        ?>
            <div class="row form-group">
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary btn-block" title="Click Me" data-toggle="collapse" href="#rejection" role="button" aria-expanded="false" aria-controls="rejection"> Rejection Details</button>
                </div>
                <div class="col-md-10">
                    <hr>
                </div>
            </div>
            
            <section class="collapse multi-collapse" id="rejection">
                <div class="row">
                    <input type="hidden" name="rej_qty" id="total_rej_qty" value="0">
                    
                    <div class="col-md-2 form-group">
                        <label for="rej_qty">Rejection Qty</label>
                        <input type="text" id="rej_qty" class="form-control numericOnly qtyCal" value="">
                        <div class="error rej_qty"></div>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="rr_reason">Rejection Reason</label>
                        <select id="rr_reason" class="form-control single-select req">
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

                    <div class="col-md-2 form-group">
                        <label for="rej_type">Rejection Type</label>
                        <select id="rej_type" class="form-control req">
                            <option value="">Select type</option>
                            <option value="1">Machine</option>
                            <option value="2">Raw Material</option>
                        </select>
                        <div class="error rej_type"></div>
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="opr_mt">OPR./MT. Type</label>
                        <select id="opr_mt"  class="form-control req single-select">
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
                    <div class="col-md-3 form-group">
                        <label for="rr_stage">Rejection Stage</label>
                        <select id="rr_stage" class="form-control single-select req">
                            <?php if (empty($dataRow->stage)) { ?> 
                                <option value="">Select Stage</option> 
                            <?php } else {
                                echo $dataRow->stage;
                            } ?>
                        </select>
                        <div class="error rr_stage"></div>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="rr_by">Rejection By <span class="text-danger">*</span></label>
                        <select id="rr_by" class="form-control single-select req">
                            <option value="">Select Rej. From</option>
                        </select>
                        <div class="error rr_by"></div>
                    </div>

                    <div class="col-md-7 form-group">
                        <label for="remark">Variance</label>
                        <input type="text" id="r_remark" class="form-control req" value="">
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="">&nbsp;</label>
                        <button type="button" id="addRejectionRow" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> ADD</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <div class="table-responsive">
                            <table id="rejectionReason" class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th>#</th>
                                        <th>Rej Qty.</th>
                                        <th>Reason</th>
                                        <th>Type</th>
                                        <th>OPR./MT.</th>
                                        <th>Stage</th>
                                        <th>Rejection By</th>
                                        <th>Variance</th>
                                        <th>Action</th>
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
            </section>

            <div class="row form-group">
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary btn-block" title="Click Me" data-toggle="collapse" href="#rework" role="button" aria-expanded="false" aria-controls="rework"> Rework Details</button>
                </div>
                <div class="col-md-10">
                    <hr>
                </div>
            </div>
            
            <section class="collapse multi-collapse" id="rework">
                <div class="row">
                    <input type="hidden" name="rw_qty" id="total_rw_qty" value="0">

                    <div class="col-md-2 form-group">
                        <label for="rw_qty">Rework Qty</label>
                        <input type="text" id="rw_qty" class="form-control req numericOnly qtyCal" value="">
                        <div class="error rw_qty"></div>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="rw_reason">Rework Reason</label>
                        <select id="rw_reason" class="form-control single-select req">
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

                    <div class="col-md-2 form-group">
                        <label for="rw_stage">Rework Stage</label>
                        <select id="rw_stage" class="form-control single-select req">
                            <?php if (empty($dataRow->stage)) { ?> 
                                <option value="">Select Stage</option> 
                            <?php } else {
                                echo $dataRow->stage;
                            } ?>
                        </select>
                        <div class="error rw_stage"></div>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="rw_by">Rework By <span class="text-danger">*</span></label>
                        <select id="rw_by" class="form-control single-select req">
                            <option value="">Select RW. From</option>
                        </select>
                        <div class="error rw_by"></div>
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="rw_process_id">Rework Process</label>
                        <select  id="rw_process_id" class="form-control single-select req">
                            <?php echo $dataRow->reworkProcess; ?>
                        </select>
                        <div class="error rw_process_id"></div>
                    </div>

                    <div class="col-md-10 form-group">
                        <label for="remark">Variance </label>
                        <input type="text" id="rw_remark" class="form-control" value="">
                        <div class="error rw_remark"></div>
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="">&nbsp;</label>
                        <button type="button" id="addReworkRow" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> ADD</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 form-group">
                        <div class="table-responsive">
                            <table  id="reworkReason" class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th>#</th>
                                        <th>RW Qty.</th>
                                        <th>Reason</th>
                                        <th>Stage</th>
                                        <th>Rejection By</th>
                                        <th>Process</th>
                                        <th>Variance</th>
                                        <th>Action</th>
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
            </section>

            <hr>
        <?php
            endif;
        ?>

        
        <div class="row">            
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-success btn-block float-right save-form" onclick="saveOutward('outWard')" style="padding:5px 40px;"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
        
    </div>
</form>

<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered jpExcelTable">
                <thead>
                    <tr>
                        <th style="min-width:20px">#</th>
                        <th style="min-width:100px">Date</th>
                        <th>Cycle Time</th>
                        <th>Start Time</th>
                        <th>Production Time</th>
                        <th>Machine</th>
                        <th>Operator</th>
                        <th>Shift</th>
                        <th>OK Qty.</th>
                        <th>Rejection Qty.</th>
                        <th>Rework Qty.</th>
                        <th>Suspected  Qty.</th>
                        <th>Remark</th>
                        <th style="width:50px;">Action</th>
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