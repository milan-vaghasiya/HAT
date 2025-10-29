<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?=(!empty($dataRow))?$dataRow['batch_no']:""?>" readonly />
                <input type="hidden" name="from_location_id" id="from_location_id" value="<?=(!empty($dataRow))?$dataRow['location_id']:""?>">
                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow))?$dataRow['item_id']:""?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" class="form-control" value="<?=(!empty($dataRow))?$dataRow['stock_qty']:""?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="to_location_id">Store Location</label>
                <select name="to_location_id" id="to_location_id" class="form-control model-select2 req">
                    <option value="">Select Location</option>
                    <?php
                        foreach($locationData as $lData):
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                if(!empty($dataRow) && $dataRow['location_id'] != $row->id && $row->id != 134 && $row->id != 208):
                                    $selected=(!empty($dataRow['transfer_location']) && $dataRow['transfer_location'] == $row->id)?'selected':'';
                                    $disabled=(!empty($dataRow['transfer_location']) && $dataRow['transfer_location'] != $row->id)?'disabled':'';
                                    echo '<option value="'.$row->id.'" '.$selected.' '.$disabled.'>'.$row->location.' </option>';
                                endif;
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="transfer_qty">Qty.</label>
                <input type="number" name="transfer_qty" id="transfer_qty" class="form-control floatOnly req" value="" />
            </div>
            <?php
            if(!empty($dataRow['transfer_location'])){
                ?>
                <div class="col-md-12 form-group">
                    <label for="transfer_reason">Reason</label>
                    <input type="text" name="transfer_reason" id="transfer_reason" class="form-control req" value="" />
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</form>