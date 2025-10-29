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
            <div class="col-md-4 form-group">
                <label for="salary_duration">Salary Duration</label> <!-- M = Monthly , H = Hourly -->
                <select name="salary_duration" id="salary_duration" class="form-control single-select req">
                    <option value="M" <?=(!empty($dataRow->salary_duration) && $dataRow->salary_duration == "M")?"selected":""; ?>>Monthly</option>
                    <option value="H" <?=(!empty($dataRow->salary_duration) && $dataRow->salary_duration == "H")?"selected":""; ?>>Hourly</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="eh_ids">Earning Heads</label>
                <select id="ehIds" data-input_id="eh_ids" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                    foreach ($earningHead as $row) :
                        $selected=(!empty($dataRow->eh_ids) && in_array($row->id, explode(',',$dataRow->eh_ids)))?"selected":''; 
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->head_name . '</option>';
                    endforeach;
                    ?>
                </select>
				<input type="hidden" name="eh_ids" id="eh_ids" value="" />
                <input type="hidden" name="system_eh_ids" id="system_eh_ids" value="<?=$systemEarningHeadIds?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="dh_ids">Deduction Heads</label>
                <select id="dhIds" data-input_id="dh_ids" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                    foreach ($deductionHead as $row):
                        $selected=(!empty($dataRow->dh_ids) && in_array($row->id, explode(',',$dataRow->dh_ids)))?"selected":''; 
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->head_name . '</option>';
                    endforeach;
                    ?>
                </select>
				<input type="hidden" name="dh_ids" id="dh_ids" value="" />
                <input type="hidden" name="system_dh_ids" id="system_dh_ids" value="<?=$systemDeductionHeadIds?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="min_wages">Min. Wages</label>
                <input type="text" name="min_wages" id="min_wages" class="form-control floatOnly" value="<?=(!empty($dataRow->min_wages))?$dataRow->min_wages:""; ?>">
            </div>            
            <div class="col-md-4 form-group">
                <label for="gratuity_days">Gratuity Days</label>
                <input type="text" name="gratuity_days" id="gratuity_days" class="form-control numericOnly req" value="<?=(!empty($dataRow->gratuity_days))?$dataRow->gratuity_days:""; ?>">
            </div>
            <div class="col-md-4 form-group">
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


