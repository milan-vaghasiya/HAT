<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="policy_name">Policy Name</label>
                <input type="text" name="policy_name" class="form-control req" value="<?=(!empty($dataRow->policy_name))?$dataRow->policy_name:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="early_in">Late In Mins.</label>
                <input type="text" name="early_in" class="form-control floatOnly req" value="<?=(!empty($dataRow->early_in))?$dataRow->early_in:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="no_early_in">Maximum Late In</label>
                <input type="text" name="no_early_in" class="form-control numericOnly req" value="<?=(!empty($dataRow->no_early_in))?$dataRow->no_early_in:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="early_out">Early Out Mins.</label>
                <input type="text" name="early_out" class="form-control floatOnly req" value="<?=(!empty($dataRow->early_out))?$dataRow->early_out:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="no_early_out">Maximum Early Out</label>
                <input type="text" name="no_early_out" class="form-control numericOnly req" value="<?=(!empty($dataRow->no_early_out))?$dataRow->no_early_out:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="short_leave_hour">Short Leave Hour</label>
                <input type="text" name="short_leave_hour" class="form-control floatOnly req" value="<?=(!empty($dataRow->short_leave_hour))?$dataRow->short_leave_hour:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="no_short_leave">Maximum Short Leave</label>
                <input type="text" name="no_short_leave" class="form-control numericOnly req" value="<?=(!empty($dataRow->no_short_leave))?$dataRow->no_short_leave:""; ?>" />
            </div>
        </div>
    </div>
</form>