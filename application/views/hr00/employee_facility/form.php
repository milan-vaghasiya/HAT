<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='ficility_type' class="control-label">Facility</label>
				<input type="text" id="ficility_type" name="ficility_type" class="form-control req" value="<?=(!empty($dataRow->ficility_type))?$dataRow->ficility_type:""?>">				
			</div>

			<div class="col-md-12 form-group">
                <label for="is_returnable">Returnable</label>
                <select name="is_returnable" id="is_returnable" class="form-control req">
                   <option value="0" <?=empty($dataRow->is_returnable)?'selected':''?>>No</option>
                   <option value="1" <?=(!empty($dataRow->is_returnable) && $dataRow->is_returnable == 1)?'selected':''?>>Yes</option>
                </select>
                <div class="error is_returnable"></div>
            </div>
			            
		</div>
	</div>	
</form>