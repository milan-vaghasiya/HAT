<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="category_name">Category Name</label>
                <input type="text" name="category_name" class="form-control req" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
            </div>
            
            <!--<div class="col-md-12 form-group">
                <label for="category_type">Category Type</label>
                <select name="category_type" id="category_type" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->category_type) && $dataRow->category_type == 0)?"selected":""?>>Non Finish Good</option>
                    <option value="1" <?=(!empty($dataRow->category_type) && $dataRow->category_type == 1)?"selected":""?>>Finish Good</option>
                </select>
            </div>-->
            <div class="col-md-6 form-group">
                <label for="category_code">Code</label>
                <input type="text" name="category_code" class="form-control req" value="<?=(!empty($dataRow->category_code))?$dataRow->category_code:""?>" />
            </div>
			<div class="col-md-6 form-group">
                <label for="is_return">Is Returnable?</label>
                <select name="is_return" id="is_return" class="form-control">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_return == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_return == 1)?"selected":""?>>Yes</option>
                </select>
            </div>
			<div class="col-md-12 form-group">
                <label for="category_type">Item Group</label>
                <select name="category_type" id="category_type" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($itemGroup as $row) :
                            $selected = (!empty($dataRow->category_type) && $dataRow->category_type == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->group_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="3" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>