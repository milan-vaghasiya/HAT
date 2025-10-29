<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-4">
                <label for="trans_no">Feddback No.</label>
                <div class="input-group">
                    <input type="text" name="trans_prefix" id="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix ?>" readonly>
                    <input type="text" name="trans_no" id="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?sprintf('%04d',$dataRow->trans_no):sprintf('%04d',$trans_no); ?>" readonly>
                </div>
            </div>
            <div class="col-md-8 form-group">
                <label for="party_id">Customer</label>
                <select name="party_id" id="party_id" class="form-control single-select req">
                    <option value="">Select Customer</option>
                    <?php
                        foreach($partyData as $row):
                            $selected = (!empty($row->id) && $row->id == $dataRow->party_id)?'selected':'';
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="survey_from">Survey From</label>
                <input type="date" name="survey_from" id="survey_from" class="form-control" value="<?=(!empty($dataRow->survey_from))?$dataRow->survey_from:date('Y-m-d') ?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="survey_to">Survey To</label>
                <input type="date" name="survey_to" id="survey_to" class="form-control" value="<?=(!empty($dataRow->survey_to))?$dataRow->survey_to:date('Y-m-d') ?>">
            </div>
            <div class="col-md-12">
                <label for="param_id">Feedback Parameters</label>
                <select name="paramSelect" id="paramSelect" data-input_id="param_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                    foreach ($feedbackPoint as $row) :
                        $selected = (!empty($dataRow->param_id) && (in_array($row->id,explode(',', $dataRow->param_id)))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->parameter . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="param_id" id="param_id" value="<?=(!empty($dataRow->param_id) ? $dataRow->param_id :"")?>" />
                <div class="error param_id"></div>
            </div>
        </div>
    </div>
</form>