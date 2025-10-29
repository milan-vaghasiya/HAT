<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="is_delete" id="is_delete" value="<?=(!empty($dataRow->is_delete))?$dataRow->is_delete:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="emp_joining_date">Date</label>
                <input type="date" name="emp_joining_date" class="form-control" value="<?=($dataRow->is_delete==2)?'hidden':date("Y-m-d")?>" <?=($dataRow->is_delete==2)?'hidden':''?> />
                <input type="date" name="emp_relieve_date" class="form-control" value="<?=($dataRow->is_delete==0)?'hidden':date("Y-m-d")?>" <?=($dataRow->is_delete==0)?'hidden':''?> />
            </div>

            <div class="col-md-12 form-group" <?=($dataRow->is_delete==0)?'hidden':''?>>
                <label for="reason">Reason</label>
                <input type="text" name="reason" class="form-control" value="" />
            </div>
           
        </div>
    </div>
</form>  