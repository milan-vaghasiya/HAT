<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <div class="col-md-6 form-group">
                <label for="item_id">Item_name</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" readonly>
                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no_r" class="form-control" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="dispatch_qty">Issue Qty</label>
                <input type="text" class="form-control" value="<?=(!empty($dataRow->dispatch_qty))?$dataRow->dispatch_qty:""?>" readonly>
            </div>

            <div class="col-md-3 form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id_r" class="form-control select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '">' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="return_qty">Return Qty</label>
                <input type="text" name="return_qty" id="return_qty" class="form-control floatOnly" value="">
            </div>

            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
        </div>
    </div>
</form>