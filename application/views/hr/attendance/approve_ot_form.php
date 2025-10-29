<form>
    <div class="col-md-12">
        <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
        <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:""?>">
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" class="form-control floatOnly" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""?>" readonly>
            </div>
             <div class="col-md-4 form-group">
                <label for="attendance_date">Attendance Date</label>
                <input type="text" class="form-control" name="attendance_date" value="<?=(!empty($dataRow->attendance_date))?formatDate($dataRow->attendance_date):""?>" readonly>
            </div>
            <div class="col-md-4 form-group">
                <label for="actual_ot">Actual OT (H:m)</label>
                <input type="text" name="actual_ot" id="actual_ot" class="form-control floatOnly" value="<?=(!empty($dataRow->actual_ot))?formatSeconds($dataRow->actual_ot,'H:i'):""?>" readonly>
            </div>
            <div class="col-md-4 form-group">
                <label for="ot_mins">Approve OT (H:m)</label>
                <input type="text" name="ot_mins" id="ot_mins" class="form-control floatOnly" value="<?=(!empty($dataRow->ot_mins))?formatSeconds($dataRow->ot_mins,'H:i'):formatSeconds($dataRow->actual_ot,'H:i')?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptList as $row):
                            if(!empty($dataRow->dept_id))
                            {
                                $selected = (!empty($dataRow->dept_id) && $row->id == $dataRow->dept_id)?"selected":"";
                            }
                            else
                            {
                                $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                            }
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="ot_type">OT Type</label>
                <select name="ot_type" id="ot_type" class="form-control single-select req " tabindex="-1">
                    <option value="1" <?=(!empty($dataRow) && $dataRow->ot_type == 1)?"selected":""?>>Regular OT</option>
                    <option value="2" <?=(!empty($dataRow) && $dataRow->ot_type == 2)?"selected":""?>>Adjust OT</option>
                </select>
            </div>
            <?php
                $adjust_date='';
                if(!empty($dataRow->adjust_to)){$at = explode('@',$dataRow->adjust_to);$adjust_date = $at[1];}
            ?>
            <div class="col-md-4">
                <label for="adjust_date">Adjust Date</label>
                <input type="date" name="adjust_date" id="adjust_date" class="form-control req" value="<?=(!empty($adjust_date))?$adjust_date:''?>" min="<?=(!empty($adjust_date))?$adjust_date:''?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>