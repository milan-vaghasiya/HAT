<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-6 form-group">
                <label for="store_name">Store Name</label>
                <select name="store_name" id="store_name" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Store</option>
                    <?php $i=1; foreach($storeNames as $row):
                        $selected = (!empty($dataRow->store_name) && $dataRow->store_name == $row->store_name)?"selected":"";
                    ?>
                        <option value="<?=$row->store_name?>" <?=$selected?>><?=$row->store_name?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="storename" name="storename" value="" />
            </div>

            <div class="col-md-6 form-group">
                <label for="location">Location</label>
                <input type="text" name="location" class="form-control req" value="<?=(!empty($dataRow->location))?$dataRow->location:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" rows="2" class="form-control"></textarea>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','#store_namec',function(){
        $('#storename').val($(this).val());
    });
});
</script>