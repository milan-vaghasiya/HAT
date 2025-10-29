<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="punch_type" value="3" />
            <div class="col-md-12 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <option value="<?=$loginID?>" <?=(!empty($dataRow->emp_id) && $loginID == $dataRow->emp_id)?"selected":"";?>>My Self</option>
                    <?php
                        foreach($empList as $row):
							if($loginID != $row->id):
								$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
							endif;
                        endforeach;
                    ?>
                </select>
            </div>    
            
            <div class="col-md-6 form-group">
                <label for="punch_date">Attendance Date</label>
                <input type="date" name="punch_date" id="punch_date" class="form-control req" value="<?=(!empty($dataRow->punch_date))?date('Y-m-d',strtotime($dataRow->punch_date)):date("Y-m-d")?>" max="<?php date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="xtype">Action</label>
                <select name="xtype" id="xtype" class="form-control single-select req">
                    <option value="1" <?=(!empty($dataRow->xtype) AND $dataRow->xtype == 1)?'selected':''?>>Add/Increase</option>
                    <option value="-1"<?=(!empty($dataRow->xtype) AND $dataRow->xtype == -1)?'selected':''?>>Minus/Decrease</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="ex_hours">Extra Hours</label>
                <input type="text" name="ex_hours" id="ex_hours" class="form-control numericOnly req" value="<?=(!empty($dataRow->ex_hours))?($dataRow->ex_hours):""?>" />
            </div>
            
            <div class="col-md-6 form-group">
                <label for="ex_mins">Extra minutes</label>
                <input type="text" name="ex_mins" id="ex_mins" class="form-control numericOnly req" value="<?=(!empty($dataRow->ex_mins))?($dataRow->ex_mins):""?>" />
            </div>
		
            <div class="col-md-12 form-group">
                <label for="remark">Reason</label>
                <textarea rows="2" name="remark" class="form-control req" placeholder="Reason" ><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
         
        </div>
    </div>
</form>
