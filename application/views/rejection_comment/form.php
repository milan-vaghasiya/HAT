<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <!-- <input type="hidden" name="type" value="1" /> -->
            <div class="col-md-12 form-group">
                <label for="remark">Rejection Reason</label>
                <textarea name="remark" class="form-control req" placeholder="Rejection Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
                <div class="error remark"></div>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Type</label>
                <select name="type" id="type" class="form-control single-select">
                    <option value="1" <?=(!empty($dataRow->type) && $dataRow->type == 1)?"selected":""?>>Rejection</option>
                    <option value="4" <?=(!empty($dataRow->type) && $dataRow->type == 4)?"selected":""?>>Rework</option>                    
                </select>
            </div>
            <div id="subTypeDiv" class="col-md-12 form-group" style="<?=(!empty($dataRow->type) && $dataRow->type == 4)?"display:none;":""?>">
                <label for="sub_type">Sub Type</label>
                <select name="sub_type" id="sub_type" class="form-control single-select">
                    <option value="">Select Sub Type</option>
                    <?php
                        foreach($subTypes as $key => $type):
                            $selected = (!empty($dataRow->sub_type) && $dataRow->sub_type == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$type.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>