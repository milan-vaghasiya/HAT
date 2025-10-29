<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="type" value="2" />
            <div class="col-md-12 form-group">
                <label for="code">Idle Code</label>
                <input name="code" class="form-control req" value="<?=(!empty($dataRow->code))?$dataRow->code:"";?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Idle Reason</label>
                <textarea name="remark" class="form-control req"><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>