<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:$p_id; ?>" />
            <div class="col-md-12 form-group">
                <label for="ch_status">Cheque Status</label>
                <select name="ch_status" id="ch_status" class="form-control single-select req">
                    <option value="">Select Status</option>
                    <option value="1" <?=(!empty($dataRow->ch_status) && $dataRow->ch_status == 1)?"selected":""?>>Pending</option>
                    <option value="2" <?=(!empty($dataRow->ch_status) && $dataRow->ch_status == 2)?"selected":""?>>Clear</option>
                    <option value="3" <?=(!empty($dataRow->ch_status) && $dataRow->ch_status == 3)?"selected":""?>>Return</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="ch_clear_date">Clear Date</label>
                <input type="date" name="ch_clear_date" id="ch_clear_date" class="form-control req" value="<?=(!empty($dataRow->ch_clear_date))?$dataRow->ch_clear_date:date('Y-m-d')?>" />
            </div>
              <div class="col-md-12 form-group">
                <label for="ch_reason">Remark</label>
                <textarea name="ch_reason" class="form-control req" rows="2"><?=(!empty($dataRow->ch_reason))?$dataRow->ch_reason:"";?></textarea>
            </div>
        </div>
    </div>
</form>