<form>
    <div class="row">
        <div class="col-md-12 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="entry_type" name="entry_type" value="<?= (!empty($entry_type) ? $entry_type : 4) ?>">
            <input type="hidden" id="operation_type" name="operation_type" value="<?= (!empty($operation_type) ? $operation_type : 4) ?>">
            <input type="hidden" id="job_trans_id" name="job_trans_id" value="<?= (!empty($dataRow->job_trans_id) ? $dataRow->job_trans_id : '') ?>">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
           
            <input type="hidden" id="ref_type" name="ref_type" value="<?= (!empty($dataRow->entry_type) ? $dataRow->entry_type : '') ?>">
        </div>
        <div class="col-md-12 form-group">
            <label for="qty">Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly" value="">
        </div>
        <div class="col-md-12 form-group">
            <label for="remark">Deviation Description</label>
            <input type="text" id="remark" name="remark" class="form-control req" value="">
        </div>
        <div class="col-md-12 form-group">
            <label for="rr_stage">Deviation Reason</label>
            <input type="text" id="rr_stage" name="rr_stage" class="form-control req" value="">
        </div>
        <div class="col-md-12 form-group">
            <label for="rw_process_id">Special Marking</label>
            <input type="text" id="rw_process_id" name="rw_process_id" class="form-control req" value="">
        </div>
    </div>
</form>