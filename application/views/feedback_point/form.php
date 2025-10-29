<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="parameter">Feedback Point</label>
                <input type="text" name="parameter" class="form-control req" value="<?=(!empty($dataRow->parameter))?$dataRow->parameter:""?>" />
            </div>
        </div>
    </div>
</form>