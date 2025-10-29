<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="unit_id" value="27" />

            <div class="col-md-8 form-group">
                <label for="item_name">Instrument Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-cat_name="'.$row->category_name.'" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="item_code">Code No.</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="instrument_range">Range (mm)</label>
                <input type="text" name="instrument_range" class="form-control" value="<?=(!empty($dataRow->instrument_range))?$dataRow->instrument_range:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="least_count">Least Count</label>
                <input type="text" name="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                        foreach($gstPercentage as $row):
                            $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate'])?"selected":"";
                            echo '<option value="'.$row['rate'].'" '.$selected.'>'.$row['val'].'</option>';
                        endforeach;
                    ?>
                </select>
            </div>    
			<div class="col-md-4 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control single-select req" >
                    <option value="Yes" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "Yes")?"selected":""?>>Yes</option>
                    <option value="No" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "No")?"selected":""?>>No</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="cal_freq">Cal. Frequency <small>(Months)</small></label>
                <input type="text" name="cal_freq" class="form-control floatOnly req" value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_reminder">Plan Before <small>(Days)</small></label>
                <input type="text" name="cal_reminder" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>