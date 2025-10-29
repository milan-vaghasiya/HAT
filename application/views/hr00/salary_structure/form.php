<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-9 form-group">
                <label for="format_name">Format Name</label>
                <input type="text" name="format_name" id="format_name" class="form-control req" value="<?=(!empty($dataRow->format_name))?$dataRow->format_name:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="effect_from">Effect From</label>
                <input type="date" name="effect_from" id="effect_from" class="form-control req" value="<?=(!empty($dataRow->effect_from))?$dataRow->effect_from:date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="salary_duration">Salary Duration</label> <!-- M = Monthly , H = Hourly -->
                <select name="salary_duration" id="salary_duration" class="form-control single-select req">
                    <option value="M" <?=(!empty($dataRow->salary_duration) && $dataRow->salary_duration == "M")?"selected":""; ?>>Monthly</option>
                    <option value="H" <?=(!empty($dataRow->salary_duration) && $dataRow->salary_duration == "H")?"selected":""; ?>>Hourly</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="min_wages">Min. Wages</label>
                <input type="text" name="min_wages" id="min_wages" class="form-control floatOnly" value="<?=(!empty($dataRow->min_wages))?$dataRow->min_wages:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="basic_da">BASIC+DA(<small>% OF CTC</small>)</label>
                <input type="text" name="basic_da" id="basic_da" class="form-control floatOnly req" value="<?=(!empty($dataRow->basic_da))?$dataRow->basic_da:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="hra">HRA(<small>% Of BASIC+DA</small>)</label>
                <input type="text" name="hra" id="hra" class="form-control floatOnly" value="<?=(!empty($dataRow->hra))?$dataRow->hra:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="pf_status">PF Ftatus</label> <!-- 0 = Not Applicable, 1 = Applicable-->
                <select name="pf_status" id="pf_status" class="form-control single-select req">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->pf_status == "0")?"selected":""; ?>>Not Applicable</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->pf_status == "1")?"selected":""; ?>>Applicable</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="pf_per">PF(%)</label>
                <input type="text" name="pf_per" id="pf_per" class="form-control floatOnly" value="<?=(!empty($dataRow->pf_per))?$dataRow->pf_per:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="gratuity_days">Gratuity Days</label>
                <input type="text" name="gratuity_days" id="gratuity_days" class="form-control numericOnly req" value="<?=(!empty($dataRow->gratuity_days))?$dataRow->gratuity_days:""; ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="gratuity_per">Gratuity(%)</label>
                <input type="text" name="gratuity_per" id="gratuity_per" class="form-control floatOnly" value="<?=(!empty($dataRow->gratuity_per))?$dataRow->gratuity_per:""; ?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>">
            </div>
            
        </div>
    </div>
</form>


