<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-12 form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control single-select req" >
                    <option value="">Select Type</option>
                    <option value="3" <?=(!empty($dataRow->type) && $dataRow->type == "3")?"selected":""?>>Item Feasibility</option>
                    <option value="4" <?=(!empty($dataRow->type) && $dataRow->type == "4")?"selected":""?>>Customer Feedback</option>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Rejected Reason</label>
                <textarea name="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>