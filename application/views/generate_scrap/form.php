<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="trans_type" id="trans_type" value="1">
            <input type="hidden" name="ref_type" id="ref_type" value="17">
            <input type="hidden" name="location_id" id="location_id" value="<?=$this->SCRAP_STORE->id?>">
            <input type="hidden" name="batch_no" id="batch_no" value="General Batch">
            <div class="col-md-3 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control" value="<?=(!empty($dataRow->ref_date))?$dataRow->ref_date:date("Y-m-d")?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($itemList as $row):
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>