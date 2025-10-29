<?php
    $type = (!empty($dataRow->type))?$dataRow->type:$type;
?>

<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="<?=$type?>" />
            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:""?>" />
            </div>
        </div>
    </div>
</form>