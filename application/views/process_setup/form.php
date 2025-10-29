<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <!-- <div class="col-md-12 form-group">
                <label for="setup_end_time">End Time</label>
                <input type="datetime-local" name="setup_end_time" min="<?=strftime('%Y-%m-%dT%H:%M:%S',strtotime($dataRow->request_date))?>" max="<?=strftime('%Y-%m-%dT%H:%M:%S',time())?>" class="form-control req" value="<?=strftime('%Y-%m-%dT%H:%M:%S',time())?>" />
            </div> -->

            <div class="col-md-12 form-group">
                <label for="setter_note">Note</label>
                <textarea name="setter_note" class="form-control req" rows="4"><?=(!empty($dataRow->setter_note)) ? $dataRow->setter_note:"" ?></textarea>
            </div>
        </div>
    </div>
</form>