<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="approved_date">Approved Date </label>
                <input type="date" name="approved_date" class="form-control req" value="<?=(!empty($dataRow->approved_date))?$dataRow->approved_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="approved_by">Approved By</label>
                <input type="text" name="approved_by" class="form-control req" value="<?=(!empty($dataRow->approved_by))?$dataRow->approved_by:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="approved_base">Approved Base</label>
                <input type="text" name="approved_base" class="form-control req" value="<?=(!empty($dataRow->approved_base))?$dataRow->approved_base:""?>" />
            </div>
        </div>
    </div>
</form>