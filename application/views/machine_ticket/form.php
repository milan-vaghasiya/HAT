<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
            <div class="col-md-6 form-group">
                <label for="trans_no">Trans. No.</label>
                <div class="input-group">
                    <input type="text" name="trans_prefix" id="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" />
                    <input type="text" name="trans_no" class="form-control" placeholder="Trans. No." value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : $nextTransNo ?>" readonly />
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($machineData as $row) :
                            $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" '.$selected.'>['.$row->item_code.'] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($deptData as $row) :
                            $selected = (!empty($dataRow->dept_id) && $dataRow->dept_id == $row->id) ? "selected" : "";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="problem_date">Problem Date</label>
                <input type="date" name="problem_date" class="form-control req" max="<?=$maxDate?>" min="<?=$startYearDate?>" value="<?=(!empty($dataRow->problem_date))?date('Y-m-d', strtotime($dataRow->problem_date)):$maxDate?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="problem_time">Problem Time</label>
                <input type="time" name="problem_time" class="form-control req" value="<?=(!empty($dataRow->problem_time)) ? $dataRow->problem_time:''?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="problem_title">Problem Title</label>
                <input type="text" name="problem_title" class="form-control req" placeholder="Problem Title" value="<?=(!empty($dataRow->problem_title))?$dataRow->problem_title:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="problem_detail">Problem Detail</label>
                <textarea name="problem_detail" id="problem_detail" class="form-control req" placeholder="Problem Detail"><?=(!empty($dataRow->problem_detail))?$dataRow->problem_detail:""?></textarea>
            </div>
        </div>
    </div>
</form>