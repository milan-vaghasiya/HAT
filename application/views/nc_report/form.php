<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="prod_type" value="<?= (!empty($dataRow->prod_type)) ? $dataRow->prod_type : "2"; ?>" />
            <input type="hidden" name="m_ct" id="m_ct" value="<?= (!empty($dataRow->m_ct)) ? $dataRow->m_ct : ""; ?>" />


            <div class="col-md-2 form-group">
                <label for="job_card_id">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select req">
                    <?php
                    if (empty($dataRow->job_card_id)) :
                        echo '<option value="">Select Job Card No.</option>';
                    endif;
                    foreach ($jobCardData as $row) :
                        $selected = (!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id) ? "selected" : "";
                        $disabled = (!empty($dataRow->job_card_id) && $dataRow->job_card_id != $row->id) ? "disabled" : "";
                        echo '<option value="' . $row->id . '" data-part_code="' . $row->item_code . '" data-job_date="' . $row->job_date . '" data-part_id="' . $row->product_id . '"="' . $row->item_code . '" ' . $selected . ' ' . $disabled . '>' . getPrefixNumber($row->job_prefix, $row->job_no) . '  [' . $row->item_code . ']</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="part_code" id="part_code" value="">
                <input type="hidden" id="part_id" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="log_date">Date</label>
                <input type="date" name="log_date" id="log_date" class="form-control req" max="<?= date("Y-m-d") ?>" value="<?= (!empty($dataRow->log_date)) ? date('Y-m-d', strtotime($dataRow->log_date)) : date("Y-m-d"); ?>" required>
            </div>
            <div class="col-md-3 form-group">
                <label for="process_id">Process Name</label>
                <select name="process_id" id="process_id" class="form-control single-select req">
                    <?php if (empty($dataRow->processOpt)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                            echo $dataRow->processOpt;
                                                                                                        } ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="operator_id">Inspector</label>
                <select name="operator_id" id="operator_id" class="form-control single-select">
                    <option value="">Select Inspector</option>
                    <?php
                    foreach ($operatorList as $row) :
                        $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="inspection_type">Inspection Type</label>
                <select name="inspection_type" id="inspection_type" class="form-control single-select">
                    <option value="">Select Inspection Type</option>
                    <?php
                    foreach ($inspectionTypeList as $row) :
                        $selected = (!empty($dataRow->inspection_type) && $dataRow->inspection_type == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->inspection_type . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select">
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
                <label for="production_time">Inspection Time(In Min.)</label>
                <input type="text" name="production_time" id="production_time" class="form-control numericOnly partCount " min="0" value="<?= (!empty($dataRow->production_time)) ? floatVal($dataRow->production_time) : "0" ?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="ok_qty">OK Qty.</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control numericOnly req qtyCal" min="0" value="<?= (!empty($dataRow->ok_qty)) ? floatVal($dataRow->ok_qty) : "0" ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="production_qty">Inspection Qty.</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly partCount  req" min="0" value="<?= (!empty($dataRow->production_qty)) ? floatVal($dataRow->production_qty) : "0" ?>" readonly>
            </div>

            <hr style="width:100%">
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
                <label for="rej_from">Rejection From</label>
                <select name="rej_from" id="rej_from" class="form-control single-select req">
                    <?php if (empty($dataRow->rejOption)) { ?> <option value="">Select Process</option> <?php } else {
                                                                                                        echo $dataRow->rejOption;
                                                                                                    } ?>
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
                                <th style="width:15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="rejectionReasonData">
                            <?php
                            $rwTable = "";
                            if (!empty($rejRwData)) :
                                $i = 0;
                                $j = 0;

                                foreach ($rejRwData as $row) :
                                    if ($row->manag_type == 1) :
                                        // $row->trans_id = $row->id;
                                        // $row->rej_qty = $row->qty;
                                        // $row->rej_reason = $row->reason;
                                        // $row->rejection_reason = $row->reason_name;
                                        // $row->rej_stage = $row->belongs_to;
                                        // $row->rej_stage_name = $row->belongs_to_name;
                                        // $row->rej_from = $row->vendor_id;
                                        // $row->rej_party_name = $row->vendor_name;
                                        // $row->rej_remark = $row->remark;
                                        //print_r(json_encode($row).'****'.date('d-m-Y H:i:s'));
                                        // echo "<script>AddRowRejection(" . json_encode($row) . ");</script>";
                            ?>
                                        <tr>
                                            <td><?= $i + 1 ?>
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][trans_id]" value="<?= $row->id ?>">
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_ref_id]" value="<?= $row->ref_id ?>">
                                                <input type="hidden" name="rejection_reason[<?= $i ?>][rej_type]" value="<?= $row->rej_type ?>">
                                            </td>
                                            <td><?= $row->qty ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_qty]" value="<?= $row->qty ?>"></td>
                                            <td><?= $row->reason_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_reason]" value="<?= $row->reason ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rejection_reason]" value="<?= $row->reason_name ?>"></td>
                                            <td><?= $row->belongs_to_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage]" value="<?= $row->belongs_to ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rej_stage_name]" value="<?= $row->belongs_to_name ?>"></td>
                                            <td><?= $row->vendor_name ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_from]" value="<?= $row->vendor_id ?>"> <input type="hidden" name="rejection_reason[<?= $i ?>][rej_party_name]" value="<?= $row->vendor_name ?>"></td>
                                            <td><?= $row->remark ?><input type="hidden" name="rejection_reason[<?= $i ?>][rej_remark]" value="<?= $row->remark ?>"></td>
                                            <td><button type="button" onclick="RemoveRejection(this)" style="margin-left:4px;" class="btn btn-outline-danger waves-effect waves-light text-center"><i class="ti-trash"></i></button></td>
                                        </tr>

                            <?php
                                        $i++;
                                    elseif ($row->manag_type == 2) :


                                        $row->trans_id = $row->id;
                                        $row->rw_qty = $row->qty;
                                        $row->rw_reason = $row->reason;
                                        $row->rework_reason = $row->reason_name;
                                        $row->rw_stage = $row->belongs_to;
                                        $row->rw_stage_name = $row->belongs_to_name;
                                        $row->rw_from = $row->vendor_id;
                                        $row->rw_party_name = $row->vendor_name;
                                        $row->rw_remark = $row->remark;
                                        // echo "<script> AddRowRework(" . json_encode($row) . ");</script>";
                                        $rowData = json_encode($row);
                                        $hidden = ($row->qty == 0) ? 'hidden' : '';
                                        $rwTable .= '<tr ' . $hidden . '>
                                    <td>' . (($row->qty > 0) ? $j + 1 : '') . '  <input type="hidden" name="rework_reason[' . $j . '][trans_id]" value="' . $row->id . '"></td>
                                    <td>' . $row->qty . '  <input type="hidden" name="rework_reason[' . $j . '][rw_qty]" value="' . $row->qty . '"></td>
                                    <td>' . $row->reason_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_reason]" value="' . $row->reason . '"> <input type="hidden" name="rework_reason[' . $j . '][rework_reason]" value="' . $row->reason_name . '"></td>
                                    <td>' . $row->belongs_to_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_stage]" value="' . $row->belongs_to . '"> <input type="hidden" name="rework_reason[' . $j . '][rw_stage_name]" value="' . $row->belongs_to_name . '"></td>
                                    <td>' . $row->vendor_name . '  <input type="hidden" name="rework_reason[' . $j . '][rw_from]" value="' . $row->vendor_id . '"> <input type="hidden" name="rework_reason[' . $j . '][rw_party_name]" value="' . $row->vendor_name . '"></td>
                                    <td>' . $row->remark . '  <input type="hidden" name="rework_reason[' . $j . '][rw_remark]" value="' . $row->remark . '"></td>
                                                                       ';
                                        $rwTable .= "
                                    <td class='text-center'>
                                     <!--<button type='button' onclick='convertToOKQty(" . $rowData . ",this)' style='margin-left:2px;' class='btn btn-outline-success waves-effect waves-light' datatip='Ok Qty'><i class='ti-check'></i></button>

                                     <button type='button' onclick='addRejQty(" . $rowData . ")' style='margin-left:2px;' class='btn btn-outline-warning waves-effect waves-light' datatip='Rejection Qty'><i class='ti-close'></i></button>-->

                                    <button type='button' onclick='RemoveRework(this)' style='margin-left:2px;' class='btn btn-outline-danger waves-effect waves-light'><i class='ti-trash'></i></button>

                                   

                                    </td>
                                </tr>";
                                        $j++;
                                    endif;

                                endforeach;
                            else :
                                echo '<tr id="noData">
                                    <td colspan="7" class="text-center">No data available in table</td>
                                 </tr>';
                            endif;
                            ?>

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
                            <?php
                            if (!empty($rwTable)) :
                                echo  $rwTable;
                            else :
                                echo '<tr id="noData">
                                <td colspan="7" class="text-center">No data available in table</td>
                            </tr>';
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</form>
<?php


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