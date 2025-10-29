<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="sub_menu_seq">Sub Menu Sequence</label>
                <input type="text" name="sub_menu_seq" class="form-control req" value="<?=(!empty($dataRow->sub_menu_seq))?$dataRow->sub_menu_seq:""?>" />
            </div>

			<div class="col-md-12 form-group">
                <label for="sub_menu_icon">Sub Menu Icon</label>
                <input type="text" name="sub_menu_icon" class="form-control req" value="<?=(!empty($dataRow->sub_menu_icon))?$dataRow->sub_menu_icon:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="sub_menu_name">Sub Menu Name</label>
                <input type="text" name="sub_menu_name" class="form-control req" value="<?=(!empty($dataRow->sub_menu_name))?$dataRow->sub_menu_name:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="sub_controller_name">Sub Controller Name</label>
                <input type="text" name="sub_controller_name" class="form-control req" value="<?=(!empty($dataRow->sub_controller_name))?$dataRow->sub_controller_name:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="menu_id">Menu Id</label>
                <select name="menu_id" id="menu_id" class="form-control single-select" >
					<option value="">Menu Id</option>
					<?php
						foreach($menuRow as $row):
							$selected = (!empty($dataRow->menu_id) && $dataRow->menu_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->menu_name.'</option>';
						endforeach;
					?>
				</select>
				
            </div>

            <div class="col-md-6 form-group">
                <label for="is_report">Is Report</label>
                <select name="is_report" id="is_report" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->is_report) && $dataRow->is_report == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow->is_report) && $dataRow->is_report == 1)?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="is_system">Is System</label>
                <select name="is_system" id="is_system" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->is_system) && $dataRow->is_system == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow->is_system) && $dataRow->is_system == 1)?"selected":""?>>Yes</option>
                </select>
            </div>
		
        </div>
    </div>
</form>