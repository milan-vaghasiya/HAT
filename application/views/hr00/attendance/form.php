<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-6 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" class="form-control text-capitalize" placeholder="Emp Name" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
                <div class="error emp_name"></div>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="emp_contact">Phone No.</label>
                <input type="number" name="emp_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
                <div class="error emp_contact"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_alt_contact">Alternate Phone</label>
                <input type="number" name="emp_alt_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
                <div class="error emp_alt_contact"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="emp_gender">Gender</label>
                <select name="emp_gender" id="emp_gender" class="form-control single-select">
                    <option value="">Select Gender</option>
                    <?php
                        foreach($genderData as $key => $value):
                            $selected = (!empty($dataRow->emp_gender) && $key == $dataRow->emp_gender)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error emp_gender"></div>
            </div>
            
            <div class="col-md-3">
                <label for="emp_joining_date">Joining Date</label>
                <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />
                <div class="error emp_joining_date"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_dept_id">Department</label>
                <select name="emp_dept_id" id="emp_dept_id" class="form-control single-select">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptRows as $row):
                            $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error emp_dept_id"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="emp_role">Role</label>
                <select name="emp_role" id="emp_role" class="form-control single-select">
                    <option value="">Select Role</option>
                    <?php
                        foreach($roleData as $key => $value):
                            $selected = (!empty($dataRow->emp_role) && $key == $dataRow->emp_role)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error emp_role"></div>
            </div>
            
            <div class="col-md-12 form-group">
                <label for="emp_address">Address</label>
                <textarea name="emp_address" class="form-control" placeholder="Address" style="resize:none;" ><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
                <div class="error emp_address"></div>
            </div>

            <?php if(empty($dataRow->id)): ?>
                <div class="col-md-6 form-group">
                    <label for="emp_password">Password</label>
                    <div class="input-group"> 
                        <input type="password" name="emp_password" id="emp_password" class="form-control pswType" placeholder="Enter Password" value="">
                        <div class="input-group-append">
                            <button type="button" class="btn waves-effect waves-light btn-outline-primary pswHideShow"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="error emp_password"></div>
                </div>

                <div class="col-md-6 form-group">
                    <label for="emp_password_c">Confirm Password</label>
                    <input type="text" name="emp_password_c" id="emp_password_c" class="form-control" placeholder="Enter Confirm Password" value="">
                    <div class="error emp_password_c"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>