<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="prod_type" value="<?= (!empty($dataRow->prod_type)) ? $dataRow->prod_type : "1"; ?>" />
            <input type="hidden" name="m_ct" id="m_ct" value="<?= (!empty($dataRow->m_ct)) ? $dataRow->m_ct : ""; ?>" />


            <div class="col-md-3 form-group">
                <label for="job_card_id">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select req">
                    <?php
                    if (empty($dataRow->job_card_id)) :
                        echo '<option value="">Select Job Card No.</option>';
                    endif;
                    foreach ($jobCardData as $row) :
                        if($row->order_status != 0){
                            $selected = (!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id) ? "selected" : "";
                            $disabled = (!empty($dataRow->job_card_id) && $dataRow->job_card_id != $row->id) ? "disabled" : "";
                            echo '<option value="' . $row->id . '" data-part_code="' . $row->item_code . '" data-job_date="' . $row->job_date . '" data-part_id="' . $row->product_id . '"="' . $row->item_code . '" ' . $selected . ' ' . $disabled . '>' . getPrefixNumber($row->job_prefix, $row->job_no) . '  [' . $row->item_code . ']</option>';
                        }
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="part_code" id="part_code" value="">
                <input type="hidden" id="part_id" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="log_date">Date</label>
                <input type="date" name="log_date" id="log_date" class="form-control req" min="<?=$startYearDate?>" max="<?=$maxDate?>"  value="<?= (!empty($dataRow->log_date)) ? date('Y-m-d', strtotime($dataRow->log_date)) : $maxDate; ?>" required>
            </div>
            <div class="col-md-3 form-group">
                <label for="process_id">Process Name</label>
                <select name="process_id" id="process_id" class="form-control single-select req">
                    <?php if (empty($dataRow->processOpt)) { ?> <option value="">Select Process</option> <?php } else {
                        echo $dataRow->processOpt;
                    } ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="cycle_time">Cycle Time (In Sec.) </label>
                <input type="text" name="cycle_time" id="cycle_time" class="form-control req floatOnly" value="<?= (!empty($dataRow->cycle_time)) ? $dataRow->cycle_time : ''; ?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="load_unload_time">L./U.L. Time (In Sec.) </label>
                <input type="text" name="load_unload_time" id="load_unload_time" class="form-control req floatOnly" value="<?= (!empty($dataRow->load_unload_time)) ? $dataRow->load_unload_time : ''; ?>">
            </div>
            <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select req">
                    <?php if (empty($dataRow->machineOpt)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                            echo $dataRow->machineOpt;
                                                                                                        } ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select11">
                    <option value="">Select Shift</option>
                    <?php
                    foreach ($shiftData as $row) :
                        $selected = (!empty($dataRow->shift_id) && $dataRow->shift_id == $row->id) ? "selected" : "";
                        $production_time = $row->production_hour * 60;
                        echo '<option value="' . $row->id . '" ' . $selected . ' data-production_time="' . $production_time . '">' . $row->shift_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="operator_id">Operator</label>
                <select name="operator_id" id="operator_id" class="form-control single-select">
                    <option value="">Select Operator</option>
                    <?php
                    foreach ($operatorList as $row) :
                        $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="production_time">Production Time(In Min.)</label>
                <input type="text" name="production_time" id="production_time" class="form-control numericOnly partCount " min="0" value="<?= (!empty($dataRow->production_time)) ? floatVal($dataRow->production_time) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="start_part_count">Start Part Count</label>
                <input type="text" name="start_part_count" id="start_part_count" class="form-control numericOnly partCount req" min="0" value="<?= (!empty($dataRow->start_part_count)) ? floatVal($dataRow->start_part_count) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="production_qty">Production Qty.</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly partCount qtyCal  req" min="0" value="<?= (!empty($dataRow->production_qty)) ? floatVal($dataRow->production_qty) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="end_part_count">End Part Count</label>
                <input type="text" name="end_part_count" id="end_part_count" class="form-control numericOnly req" min="0" value="<?= (!empty($dataRow->end_part_count)) ? floatVal($dataRow->end_part_count) : "0" ?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="ok_qty">OK Qty.</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req" readonly min="0" value="<?= (!empty($dataRow->ok_qty)) ? floatVal($dataRow->ok_qty) : "0" ?>">
            </div>

            <!-- <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="rej_qty">Rejected Qty.</label>
                <input type="text" name="rej_qty" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
            </div>
            <div class="col-md-3 form-group">
                <label for="rej_reason">Rejection Reason</label>
                <select name="rej_reason" id="rej_reason" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                    foreach ($rejectionComments as $row) :
                        $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                        echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                    endforeach;
                    ?>
                </select>
            </div>
           
            <div class="col-md-3 form-group">
                <label for="rejection_stage">Rejection Belong To</label>
                <select id="rejection_stage" class="form-control single-select req">
                    <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                    echo $dataRow->stage;
                                                                                                } ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="rej_from">Rejection From <span class="text-danger">*</span></label>
                <select name="rej_from" id="rej_from" class="form-control single-select req">
                    <option value="">Select Rej. From</option>
                </select>
            </div>
            <div class="col-md-11 form-group">
                <label for="rej_remark">Rejection Remark</label>
                <input type="text" name="rej_remark" id="rej_remark" class="form-control" value="">
            </div>
            <div class="col-md-1 form-group">
                <label for="">&nbsp;</label>
                <button type="button" id="addRejectionRow" class="btn btn-outline-info btn-block ">Add</button>
            </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table id="rejectionReason" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Rejection Qty.</th>
                                <th>Rejection Reason</th>
                                <th>Rejection Belong To</th>
                                <th>Rejection From</th>
                                <th>Rejection Remark</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="rejectionReasonData">
                            <tr id="noData">
                                <td colspan="7" class="text-center">No data available in table</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="rw_qty">Rework Qty.</label>
                <input type="text" name="rw_qty" id="rw_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
            </div>
            <div class="col-md-3 form-group">
                <label for="rw_reason">Rework Reason</label>
                <select name="rw_reason" id="rw_reason" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                    foreach ($reworkComments as $row) :
                        $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                        echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="rework_stage">Rework Belong To</label>
                <select id="rework_stage" class="form-control single-select req">
                    <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                    echo $dataRow->stage;
                                                                                                } ?>

                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="rw_from">Rework From</label>
                <select name="rw_from" id="rw_from" class="form-control single-select req">
                    <?php if (empty($dataRow->rewOption)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                        echo $dataRow->rewOption;
                                                                                                    } ?>
                </select>
            </div>

            <div class="col-md-11 form-group">
                <label for="rw_remark">Rework Remark</label>
                <input type="text" name="rw_remark" id="rw_remark" class="form-control" value="">
            </div>
            <div class="col-md-1 form-group">
                <label for="">&nbsp;</label>
                <button type="button" id="addReworkRow" class="btn btn-outline-info btn-block ">ADD</button>
            </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table id="reworkReason" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Rework Qty.</th>
                                <th>Rework Reason</th>
                                <th>Rework Belong To</th>
                                <th>Rework From</th>
                                <th>Rework Remark</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="reworkReasonData">
                            <tr id="noData">
                                <td colspan="7" class="text-center">No data available in table</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> -->
            <hr style="width: 100%;">
            <div class="col-md-3 form-group">
                <label for="idle_time">Idle Time (In Min.)</label>
                <input type="text" id="idle_time" class="form-control numericOnly" value="0" />
            </div>
            <div class="col-md-4 form-group ">
                <label for="idle_reason">Idle Reason</label>
                <select id="idle_reason" class="form-control single-select req">
                    <option value="">Select Reason</option>
                    <?php
                    foreach ($idleReasonList as $row) :
                        $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                        echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" id="addIdleRow" class="btn btn-outline-info btn-block "><i class="fa fa-plus"></i> ADD</button>
            </div>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table id="idleReasons" class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th style="width:20%;">Idle Time (In Min.)</th>
                                <th>Idle Reason</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="idleReasonData">
                            <tr id="noData">
                                <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
if (!empty($dataRow->idle_reason)) :
    $idle_reason = json_decode($dataRow->idle_reason);
    if (!empty($idle_reason)) :
        foreach ($idle_reason as $row) :
            echo "<script>AddRowIdle(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;

if (!empty($dataRow->rej_reason)) :
    $rej_reason = json_decode($dataRow->rej_reason);
    if (!empty($rej_reason)) :
        foreach ($rej_reason as $row) :
            echo "<script>AddRowRejection(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;

if (!empty($dataRow->rw_reason)) :
    $rw_reason = json_decode($dataRow->rw_reason);
    if (!empty($rw_reason)) :
        foreach ($rw_reason as $row) :
            echo "<script>AddRowRework(" . json_encode($row) . ");</script>";
        endforeach;
    endif;
endif;
?>