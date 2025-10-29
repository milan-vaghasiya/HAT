<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <div class="col-md-6 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '> ['. $row->category_code.'] ' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error category_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="delivery_date">Required Date</label>
                <input type="date" name="delivery_date" class="form-control" value="<?=(!empty($dataRow->delivery_date))?$dataRow->delivery_date:date('Y-m-d')?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="make">Make</label>
                <input type="text" name="make" class="form-control" value="<?=(!empty($dataRow->make))?$dataRow->make:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" id="size" class="form-control req" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" class="form-control floatOnly" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />
            </div>
        </div>
    </div>
</form>