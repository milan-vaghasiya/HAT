<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="menu_icon">Menu Icon</label>
                <input type="text" name="menu_icon" class="form-control req" value="<?=(!empty($dataRow->menu_icon))?$dataRow->menu_icon:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="menu_name">Menu Name</label>
                <input type="text" name="menu_name" class="form-control req" value="<?=(!empty($dataRow->menu_name))?$dataRow->menu_name:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="menu_seq">Menu Sequence</label>
                <input type="text" name="menu_seq" class="form-control req" value="<?=(!empty($dataRow->menu_seq))?$dataRow->menu_seq:""?>" />
            </div>

          <!--<div class="col-md-12 form-group">
                <label for="menu_seq">Menu Sequence <label>
                <input type="text" name="menu_seq" class="form-control req" value="<?=(!empty($dataRow->menu_seq))?$dataRow->menu_seq:"";?>"/>    
          </div>-->

           
            <div class="col-md-12 form-group">
                <label for="is_master">Is Master</label>
                <select name="is_master" id="is_master" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->is_master) && $dataRow->is_master == 0)?"selected":""?>>Yes</option>
                    <option value="1" <?=(!empty($dataRow->is_master) && $dataRow->is_master == 1)?"selected":""?>>No</option>
                </select>
            </div>
		
        </div>
    </div>
</form>