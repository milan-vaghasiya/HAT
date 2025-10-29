<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
			<div class="col-md-6 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="father_name">Father/Husband Name</label>
                <input type="text" name="father_name" class="form-control" value="<?=(!empty($dataRow->father_name))?$dataRow->father_name:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_email">Email ID</label>
                <input type="text" name="emp_email" class="form-control" value="<?=(!empty($dataRow->emp_email))?$dataRow->emp_email:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="emp_contact">Phone No.</label>
                <input type="text" name="emp_contact" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="emp_alt_contact">Alternate Phone</label>
                <input type="text" name="emp_alt_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="marital_status">Marital Status</label>
                <select name="marital_status" id="marital_status" class="form-control " >
                    <option value="Married" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Married")?"selected":""?>>Married</option>
                    <option value="UnMarried" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "UnMarried")?"selected":""?>>UnMarried</option>
                    <option value="Widow" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Widow")?"selected":""?>>Widow</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="emp_birthdate">Date of Birth</label>
                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control req" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_education">Education</label>
                <input type="text" name="emp_education" class="form-control req" value="<?=(!empty($dataRow->emp_education))?$dataRow->emp_education:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control single-select req">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderData as $value):
                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-2 form-group">
                <label for="emp_experience">Exp. (In Months)</label>
                <input type="text" name="emp_experience" class="form-control numericOnly" value="<?=(!empty($dataRow->emp_experience))?$dataRow->emp_experience:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="emp_category">Emp Category</label>
                <select name="emp_category" id="emp_category" class="form-control single-select req">
                    <option value="">Select Category</option>
                    <?php
                        foreach($categoryData as $row):
                            $selected = (!empty($dataRow->emp_category) && $row->id == $dataRow->emp_category)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->category.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="emp_dept_id">Department</label>
                <select name="emp_dept_id" id="emp_dept_id" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptRows as $row):
                            $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 from-group">
                <label for="emp_designation">Designation</label>
                <select name="emp_designation" id="emp_designation" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Designation</option>
                    <?php
                        foreach($descRows as $row):
                            $selected = (!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                        endforeach;
                    ?>
                </select>
                <!--<input type="hidden" id="designationTitle" name="designationTitle" value="" />-->
            </div>
            <div class="col-md-6 form-group">
                <label for="emp_address">Address</label>
                <textarea name="emp_address" class="form-control" placeholder="Address" style="resize:none;" rows="2"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
            </div>

            <div class="col-md-6 form-group">
                <label for="permenant_address">Permenant Address</label>
                <textarea name="permenant_address" class="form-control" placeholder="Permenant Address" style="resize:none;" rows="2"><?=(!empty($dataRow->permenant_address))?$dataRow->permenant_address:""?></textarea>
            </div>

            
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('keyup','#emp_designationc',function(){
        $('#designationTitle').val($(this).val());
    });
});
</script>