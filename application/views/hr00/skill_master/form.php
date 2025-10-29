<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
                <label for='dept_id'>Department Name</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
					<option value="">Select Department</option>
                    <?php
                    foreach ($deptData as $row) :
                        $selected = (!empty($dataRow->dept_id) && $dataRow->dept_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="skill">Skill</label>
				<input type="text" id="skill" name="skill"  class="form-control req" value="<?=(!empty($dataRow->skill))?$dataRow->skill:""?>">				
			</div>
		</div>
	</div>	
</form>
            
