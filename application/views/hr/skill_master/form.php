<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-5 form-group">
                <label for='dept_id'>Department</label>
                <select name="dept_id" id="dept_id" class="form-control select2 req">
					<option value="">Select Department</option>
                    <?php
                    foreach ($deptData as $row) :
                        $selected = (!empty($dataRow->dept_id) && $dataRow->dept_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-5 form-group">
                <label for="designation_id">Designation</label>
                <select name="designation_id" id="designation_id" class="form-control select2 req">
                    <option value="">Select Designation</option>
                    <?php
                        foreach($descRows as $row):
                            $selected = (!empty($dataRow->designation_id) && $row->id == $dataRow->designation_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="req_per">Req. Per(%)</label>
                <input type="text" name="req_per" id="req_per" class="form-control numericOnly req" value="<?=(!empty($dataRow->req_per))?$dataRow->req_per:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="skill">Skill</label>
				<textarea id="skill" name="skill"  class="form-control" row="2"><?=(!empty($dataRow->skill))?$dataRow->skill:""?></textarea>
				<div class="error skill"></div>
			</div>
		</div>
	</div>	
</form>
            
