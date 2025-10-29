<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 form-group">
                <div class="error generalError"></div>
            </div>

            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:$item_id?>" />
           
            <div class="col-md-8 form-group">
                <label for="bag_id">Bag</label>
                <select name="bag_id" id="bag_id" class="form-control single-select">
                    <option value="">Select Bag</option>
                    <?php
                        foreach ($bagData as $row) :
                                $selected = (!empty($dataRow->bag_id) && $dataRow->bag_id == $row->id) ? "selected" : "";
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="bag_capacity">Bag capacity</label>
                <input type="text" name="bag_capacity" class="form-control" value="<?=(!empty($dataRow->bag_capacity))?$dataRow->bag_capacity:""?>" />
            </div>

            <div class="col-md-8 form-group">
                <label for="box_id">Box</label>
                <select name="box_id" id="box_id" class="form-control single-select">
                    <option value="">Select Box</option>
                    <?php
                        foreach ($boxData as $row) :
                                $selected = (!empty($dataRow->box_id) && $dataRow->box_id == $row->id) ? "selected" : "";
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="box_capacity">Box capacity</label>
                <input type="text" name="box_capacity" class="form-control" value="<?=(!empty($dataRow->box_capacity))?$dataRow->box_capacity:""?>" />
            </div>

            <div class="col-md-8 form-group">
                <label for="palette_id">Palette</label>
                <select name="palette_id" id="palette_id" class="form-control single-select">
                    <option value="">Select Palette</option>
                    <?php
                        foreach ($paletteData as $row) :
                                $selected = (!empty($dataRow->palette_id) && $dataRow->palette_id == $row->id) ? "selected" : "";
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="palette_capacity">Palette capacity</label>
                <input type="text" name="palette_capacity" class="form-control" value="<?=(!empty($dataRow->palette_capacity))?$dataRow->palette_capacity:""?>" />
            </div>
        </div>
    </div>
</form>