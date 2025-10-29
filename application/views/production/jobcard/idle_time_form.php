<form>
    <div class="row">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="entry_type" id="entry_type" value="8">
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_card_id:""?>">
        <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
        <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>">
        <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->in_process_id))?$dataRow->in_process_id:""?>">

        <div class="col-md-12"><div class="error general_error"></div></div>
        <div class="col-md-4 form-group">
            <label for="entry_date">Date</label>
            <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
        </div>        

        <div class="col-md-4 form-group">
            <label for="production_time">Idle Time (in minutes)</label>
            <input type="text" name="production_time" id="production_time" class="form-control numericOnly" value="">
        </div>

        <div class="col-md-4 form-group">
            <label for="rr_reason">Reason</label>
            <select name="rr_reason" id="rr_reason" class="form-control single-select">
                <option value="">Select Reason</option>
                <?php
                    foreach($idleReason as $row):
                        echo '<option value="'.$row->id.'">'.((!empty($row->code))?"[".$row->code."] ":"").$row->remark.'</option>';
                    endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-4 form-group">
            <label for="machine_id">Machine</label>
            <select name="machine_id" id="machine_id" class="form-control single-select">
                <option value="">Select Machine</option>
                <?php
                    foreach($machineList as $row):
                        echo '<option value="'.$row->id.'">'.((!empty($row->item_code))?"[".$row->item_code."] ":"").$row->item_name.'</option>';
                    endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-4 form-group">
            <label for="shift_id">Shift</label>
            <select name="shift_id" id="shift_id" class="form-control single-select">
                <option value="">Select Shift</option>
                <?php
                    foreach($shiftData as $row):
                        echo '<option value="' . $row->id . '">' . $row->shift_name . '</option>';
                    endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-4 form-group">
            <label for="operator_id">Operator</label>
            <select name="operator_id" id="operator_id" class="form-control single-select">
                <option value="">Select Operator</option>
                <?php
                    foreach ($operatorList as $row) :
                        echo '<option value="' . $row->id . '">[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-10 form-group">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" value="">
        </div>

        <div class="col-md-2 form-group">
            <label for="">&nbsp;</label>
            <button type="button" class="btn btn-outline-success btn-block" onclick="saveIdleTime('idleTime');"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Idle Time (In Min.)</th>
                        <th>Machine</th>
                        <th>Shift</th>
                        <th>Operator</th>
                        <th>Reason</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="idleTimeTransData">
                    <?=$transHtml?>
                </tbody>
            </table>
        </div>
    </div>
</div>