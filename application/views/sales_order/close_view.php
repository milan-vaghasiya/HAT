<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
            <input type="hidden" name="trans_status" value="2" />

            <div class="col-md-12 form-group">
                <label for="close_reason">Reason</label>
                <textarea name="close_reason" id="close_reason" class="form-control req" placeholder="Close Reason"><?=(!empty($dataRow->close_reason))?$dataRow->close_reason:""?></textarea>
				<div class="error close_reason"></div>
            </div>
        </div>
    </div>
</form>