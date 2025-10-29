<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" id="approval_type" name="approval_type" value="<?=(!empty($dataRow->approval_type))?$dataRow->approval_type:"1"; ?>" />
            <!-- <input type="hidden" name="emp_id" value="<?=$this->session->userdata('loginId')?>" /> -->
			
            <div class="col-md-12 form-group"><div class="error generalError"></div></div>

            <div class="col-md-8 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req leaveQuota">
                    <option value="">Select Employee</option>
                    <!--<option value="<?=$this->loginId?>">My Self</option>-->
                    <?php
                        foreach($empList as $row):
							$selected = (!empty($dataRow->emp_id) && $row->id == $dataRow->emp_id)?"selected":"";
							$emp_name = ($this->loginId == $row->id) ? "My Self" : $row->emp_name;
							echo '<option value="'.$row->id.'" '.$selected.' data-pla_id="'.$row->pla_id.'" data-fla_id="'.$row->fla_id.'">['.$row->emp_code.'] '.$emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="leave_type_id">Leave Type</label>
                <select name="leave_type_id" id="leave_type_id" class="form-control single-select leave_type_id req leaveQuota">
                    <option value="">Select Leave Type</option>
                    <?php
                        foreach($leaveType as $row):
                            $selected = (!empty($dataRow->leave_type_id) && $row->id == $dataRow->leave_type_id)?"selected":"";
                            echo '<option value="'.$row->id.'" data-type="'.$row->type.'" '.$selected.'>'.$row->leave_type.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="type_leave" id="type_leave" value="<?=(!empty($dataRow->type_leave))?$dataRow->type_leave:"";?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="start_date">Start Date</label> <?php //(!empty($dataRow->start_date))?$dataRow->start_date:date("Y-m-d")?>
                <input type="date" name="start_date" id="start_date" class="form-control countTotalDays leaveQuota req" value="<?=(!empty($dataRow->start_date))?date('Y-m-d', strtotime($dataRow->start_date)):date("Y-m-d")?>"  />
            </div>
            <?php $style='style="display:none;"'; $style2=''; $totallbl='Total Days';
            if(!empty($dataRow->type_leave) && $dataRow->type_leave == 'SL'){
                $style = ''; $style2='style="display:none;"'; $totallbl='Total Mins';
            } ?>
            
            <div class="col-md-3 form-group shortLeave" <?=$style?>>
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control countTotalHours req" value="<?=(!empty($dataRow->start_date))?date('H:i', strtotime($dataRow->start_date)):date("H:i")?>" />
            </div>
            <div class="col-md-3 form-group shortLeave" <?=$style?>>
                <label for="end_time">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control countTotalHours req" value="<?=(!empty($dataRow->end_date))?date('H:i', strtotime($dataRow->end_date)):date("H:i")?>" />
            </div>
            <div class="col-md-3 form-group shortLeave" <?=$style?>>
                
            </div>
            
            <div class="col-md-3 form-group leaveType" <?=$style2?>>
                <label for="start_section">Start Section </label>
                <select name="start_section" id="start_section" class="form-control single countTotalDays select req" >
                    <option value="">Select Start Section</option>
                    <option value="1" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 1)?"selected":""?>>Half Day(First)</option> 
                    <option value="2" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 2)?"selected":""?>>Half Day(Second)</option>
                    <option value="3" <?=(!empty($dataRow->start_section) && $dataRow->start_section == 3)?"selected":""?>>Full day</option>
                </select>
            </div>
			
            <div class="col-md-3 form-group leaveType" <?=$style2?>>
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control countTotalDays req" value="<?=(!empty($dataRow->end_date))?date('Y-m-d', strtotime($dataRow->end_date)):date("Y-m-d")?>" min="<?=(!empty($dataRow->end_date))?$dataRow->end_date:date("Y-m-d")?>"  />
            </div>
			
            <div class="col-md-3 form-group leaveType" <?=$style2?>>
                <label for="end_section">End Section </label>
                <select name="end_section" id="end_section" class="form-control countTotalDays endSection req" <?=(!empty($dataRow->leave_type_id) && $dataRow->leave_type_id == -1)? "disabled":""; ?>>
                    <option value="">Select End Section</option>
                    <option value="1" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 1)?"selected":""?>>First Half</option>
                     <option value="2" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 2)?"selected":""?>>Second Half</option> 
                    <option value="3" <?=(!empty($dataRow->end_section) && $dataRow->end_section == 3)?"selected":""?>>Full day</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label class="totaldays" for="total_days"><?=$totallbl?></label>
                <input type="text" name="total_days" id="total_days" class="form-control floatOnly req" value="<?=(!empty($dataRow->total_days))?floatval($dataRow->total_days):1; ?>" <?=(!empty($dataRow->leave_type_id) && $dataRow->leave_type_id == -1)? "":"readOnly"; ?> />
            </div>
            
            <div class="col-md-9 form-group" id="leave_reason">
                <label for="leave_reason">Reason</label>
                <input type="text" name="leave_reason" id="leave_reason" class="form-control req" value="<?=(!empty($dataRow->leave_reason))?$dataRow->leave_reason:""?>" />
            </div>
            
			<div class="col-md-12 form-group">
				<span class="badge badge-pill badge-primary max-leave font-14 font-medium"></span>
				<span class="badge badge-pill badge-danger used-leave font-14 font-medium"></span>
				<span class="badge badge-pill badge-success remain-leave font-14 font-medium"></span>
			</div>
        </div>
    </div>
</form>
