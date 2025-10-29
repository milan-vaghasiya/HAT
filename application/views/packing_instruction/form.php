<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:""?>" />
        
            <div class="col-md-12 form-group">
                <label for="item_id">Product</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select Product</option>
                    <?php 
                        foreach($itemData as $row):
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id==$row->item_id)?"selected":"";
                            echo "<option value='".$row->item_id."' data-refid='".$row->id."' ".$selected.">".$row->item_code.'-'.$row->item_name.' ('.getPrefixNumber($row->trans_prefix,$row->trans_no).") </option>";
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="dispatch_date">Dispatch Date</label>
                <input type="date" name="dispatch_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->dispatch_date))?$dataRow->dispatch_date:$maxDate;?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" >
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>
