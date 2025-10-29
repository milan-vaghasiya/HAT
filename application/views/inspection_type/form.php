<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="inspection_type">Inspection Type</label>
                <input type="text" name="inspection_type" class="form-control req" value="<?=(!empty($dataRow->inspection_type))?$dataRow->inspection_type:""?>" />
            </div>
          
        </div>
    </div>
</form>