<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="action_taken">Details of  Action Taken  / CA Report  No.</label>
                <input type="text" name="action_taken" class="form-control req" value="<?=(!empty($dataRow->action_taken))?$dataRow->action_taken:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="ref_feedback">Reference of feed back to CustomerReference of feed back to Customer</label>
                <input type="text" name="ref_feedback" class="form-control" value="<?=(!empty($dataRow->ref_feedback))?$dataRow->ref_feedback:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remarks</label>
                <input type="text" name="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" />
            </div>
        </div>
    </div>
</form>


               