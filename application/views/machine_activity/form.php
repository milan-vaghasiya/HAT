<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="activities">Activities</label>
                <input name="activities" class="form-control req" value="<?=(!empty($dataRow->activities))?$dataRow->activities:"";?>" />
            </div>
        </div>
    </div>
</form>