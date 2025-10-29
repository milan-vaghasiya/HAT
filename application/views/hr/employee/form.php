<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-3 form-group">
                <label for="emp_code">Emp Code</label>
                <input type="text" name="emp_code" class="form-control req" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:""?>" <?=(!empty($dataRow->device_id)) ? "readonly" : "" ?> />
            </div>
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
            <div class="col-md-3 form-group">
                <label for="emp_contact">Contact No.</label>
                <input type="text" name="emp_contact" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_alt_contact">Alternate Contact</label>
                <input type="text" name="emp_alt_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
            </div>
            <div class="col-md-3 form-group">
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
            <div class="col-md-3">
                <label for="emp_joining_date">Joining Date</label>
                <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control req" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control single-select">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderData as $value):
                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <!--<div class="col-md-3 form-group">
                <label for="attendance_type">Attendance</label>
                <select name="attendance_type" id="attendance_type" class="form-control single-select req ">
                    <option value="1" <?=(!empty($dataRow->attendance_type) && $dataRow->attendance_type == "1")?"selected":""?>>Yes</option>
                    <option value="0" <?=(!empty($dataRow) && $dataRow->attendance_type == "0")?"selected":""?>>No</option>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="emp_category">Emp Category</label>
                <select name="emp_category" id="emp_category" class="form-control single-select req">
                    <option value="">Select Category</option>
                    <?php
                        /*foreach($categoryData as $row):
                            $selected = (!empty($dataRow->emp_category) && $row->id == $dataRow->emp_category)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->category.'</option>';
                        endforeach;*/
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_type">Employee Type</label>
                <select name="emp_type" id="emp_type" class="form-control single-select req " >
                    <option value="">Select Type</option>
                    <option value="1" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "1")?"selected":""?>>Permanent (Fix)</option>
                    <option value="2" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "2")?"selected":""?>>Permanent (Hourly)</option>
                    <option value="3" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "3")?"selected":""?>>Temporary</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="allowed_visitors">Allowed Visitors?</label>
                <select name="allowed_visitors" id="allowed_visitors" class="form-control single-select req " tabindex="-1">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->allowed_visitors == 0)?"selected":""?>>Not Allowed</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->allowed_visitors == 1)?"selected":""?>>Allowed</option>
                </select>
            </div>-->
            <div class="col-md-3 form-group">
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select req">
                    <option value="">Select Shift</option>
                    <?php
                        foreach($shiftData as $row):
                            $selected = (!empty($dataRow->shift_id) && $row->id == $dataRow->shift_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->shift_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="emp_experience">Exp. (In Months)</label>
                <input type="text" name="emp_experience" class="form-control numericOnly" value="<?=(!empty($dataRow->emp_experience))?$dataRow->emp_experience:""?>" />
            </div>
            <!--<div class="col-md-3 form-group">
                <label for="uan_no">UAN Number</label>
                <input type="text" name="uan_no" id="uan_no" class="form-control" value="<?=(!empty($dataRow->uan_no))?$dataRow->uan_no:""?>" />
            </div>-->
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
                <input type="hidden" id="designationTitle" name="designationTitle" value="" />
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_sys_desc_id">System Designation</label>
                <select name="sysdescSelect" id="sysdescSelect" data-input_id="emp_sys_desc_id" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        foreach($systemDesignation as $key=>$value) : 
							$selected= $emp_sys_desc_idArr = (!empty($dataRow->emp_sys_desc_id)) ? explode(',',$dataRow->emp_sys_desc_id) : array();
							if(!empty($dataRow->emp_sys_desc_id) && in_array($key,$emp_sys_desc_idArr)){$selected = "selected";}else{ $selected=''; }
							echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
						endforeach;
                    ?>
                    <input type="hidden" id="emp_sys_desc_id" name="emp_sys_desc_id" value="<?=(!empty($dataRow->emp_sys_desc_id))?$dataRow->emp_sys_desc_id:""?>" />
                </select>
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