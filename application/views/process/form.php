<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="process_name">Process Name</label>
                <input type="text" name="process_name" class="form-control req" value="<?=(!empty($dataRow->process_name))?$dataRow->process_name:"";?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="dept_id">Department</label>
                <select id="mul_dept_id"  data-input_id="dept_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                        foreach($deptRows as $row):
                            $selected = "";
                            if (!empty($dataRow->dept_id) && in_array($row->id,explode(',',$dataRow->dept_id))) {
                                $selected = "selected";
                            }
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="dept_id" id="dept_id" value="<?=(!empty($dataRow->dept_id)?$dataRow->dept_id:'')?>">
				<div class="error dept_id"></div>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>