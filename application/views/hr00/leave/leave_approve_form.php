<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="total_days" id="total_days" value="<?=(!empty($dataRow->total_days))?$dataRow->total_days:0; ?>" />
            <input type="hidden" name="emp_id" value="<?=$this->session->userdata('loginId')?>" />
			
            <div class="col-md-12 form-group"><div class="error generalError"></div></div>

            <div class="col-md-4">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?=(!empty($dataRow->start_date))?$dataRow->start_date:date("Y-m-d")?>" min="<?=(!empty($dataRow->start_date))?$dataRow->start_date:date("Y-m-d")?>" />
                <div class="error start_date"></div>
            </div>
			
            <div class="col-md-4">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?=(!empty($dataRow->end_date))?$dataRow->end_date:date("Y-m-d")?>" min="<?=(!empty($dataRow->start_date))?$dataRow->start_date:date("Y-m-d")?>" />
                <div class="error end_date"></div>
            </div>
			
			<div class="col-md-4 form-group">
                <label for="leave_type_id">Leave Type</label>
                <select name="leave_type_id" id="leave_type_id" class="form-control single-select leave_type_id">
                    <option value="">Select Leave Type</option>
                    <?php
                        foreach($leaveType as $row):
                            $selected = (!empty($dataRow->leave_type_id) && $row->id == $dataRow->leave_type_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->leave_type.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error leave_type_id"></div>
            </div>
            
			<div class="col-md-12 form-group text-center"><h6 class="leave-days block font-14 font-medium bg-cyan text-white"></h6></div>
            <div class="col-md-12 form-group">
                <label for="leave_reason">Reason</label>
                <textarea rows="2" name="leave_reason" class="form-control" placeholder="Reason" ><?=(!empty($dataRow->leave_reason))?$dataRow->leave_reason:""?></textarea>
                <div class="error leave_reason"></div>
            </div>
			<div class="col-md-12 form-group">
				<span class="badge badge-pill badge-primary max-leave font-14 font-medium"></span>
				<span class="badge badge-pill badge-danger used-leave font-14 font-medium"></span>
				<span class="badge badge-pill badge-success remain-leave font-14 font-medium"></span>
			</div>
        </div>
    </div>
</form>
