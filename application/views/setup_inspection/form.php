<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-12 form-group">
                <label for="setup_status">Inspection Status</label>
                <select name="setup_status" id="setup_status" class="form-control single-select req">
                    <option value="">Select Status</option>
                    <?php
                        foreach($inspectionStatus as $key=>$value):
                            $selected = (!empty($dataRow->setup_status) && $dataRow->setup_status == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <!-- <div class="col-md-12 form-group">
                <label for="inspection_date">Inspection Date</label>
                <input type="datetime-local" name="inspection_date" min="<?=strftime('%Y-%m-%dT%H:%M:%S',strtotime($dataRow->setup_end_time))?>" max="<?=strftime('%Y-%m-%dT%H:%M:%S',time())?>"  class="form-control req" value="<?=strftime('%Y-%m-%dT%H:%M:%S',time())?>" />
            </div> -->
            <div class="col-md-12 form-group">
                <label for="qci_note">Qci. Note</label>
                <textarea name="qci_note" class="form-control req" rows="4"><?=(!empty($dataRow->qci_note)) ? $dataRow->qci_note:"" ?></textarea>
            </div>
            <div class="col-md-12 form-group">
                <label for="attachment">Attachment</label>
                <div data-repeater-list="repeater-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="attachment" id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>