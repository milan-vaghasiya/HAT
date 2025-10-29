<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
           <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:4; ?>" /> 
           <input type="hidden" name="group_name" id="group_name" value="<?=(!empty($dataRow->group_name))?$dataRow->group_name:""?>">
           <input type="hidden" name="group_code" id="group_code" value="<?=(!empty($dataRow->group_code))?$dataRow->group_code:""?>">

            <div class="col-md-6 form-group">
                <label for="party_name">Ladger Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="group_id">Group Name</label>
                <select name="group_id" id="group_id" class="form-control single-select req">
                    <option value="">Select Group</option>
                    <?php
                        foreach($grpData as $row):
                            $selected = (!empty($dataRow->group_id) && $row->id == $dataRow->group_id)?"selected":"";
                            echo "<option value='".$row->id."' data-row='".json_encode($row)."' ".$selected.">".$row->name."</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="opening_balance">Opening Balance</label>
                <div class="input-group">
                    <select name="balance_type" id="balance_type" class="form-control" style="width: 40%;">
                        <option value="1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "1")?"selected":""?>>CR</option>
                        <option value="-1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "-1")?"selected":""?>>DR</option>
                    </select>
                    <input type="number" name="opening_balance" class="form-control floatOnly" style="width: 60%;" value="<?=(!empty($dataRow->opening_balance))?abs($dataRow->opening_balance):""?>" />
                </div>
            </div>

            <div class="col-md-6 form-group">
                <label for="is_gst_applicable">Gst Applicable</label>
                <select name="is_gst_applicable" id="is_gst_applicable" class="form-control req" >
                    <option value="0" <?=(!empty($dataRow->is_gst_applicable) && $dataRow->is_gst_applicable == 0)?"selected":""?>>No</option>
                    <option value="1" <?=(!empty($dataRow->is_gst_applicable) && $dataRow->is_gst_applicable == 1)?"selected":""?>>Yes</option>
                </select>
            </div>


            <div class="col-md-4 form-group applicable" <?=(!empty($dataRow->is_gst_applicable))?'':'style="display:none"'; ?>>
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                        foreach($gstPercentage as $row):
                            $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate'])?"selected":"";
                            echo '<option value="'.$row['rate'].'" '.$selected.'>'.$row['val'].'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group applicable" <?=(!empty($dataRow->is_gst_applicable))?'':'style="display:none"'; ?>>
                <label for="cess_per">Cess Per.</label>
                <input type="number" name="cess_per" class="form-control numericOnly" value="<?=(!empty($dataRow->cess_per))?$dataRow->cess_per:""?>" />
            </div>

            <div class="col-md-4 form-group applicable" <?=(!empty($dataRow->is_gst_applicable))?'':'style="display:none"'; ?>>
                <label for="hsn_code">Hsn Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select req">
                    <option value="">Select HSN</option>
                    <?php
                        foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && trim($dataRow->hsn_code) == trim($row->hsn_code)) ? "selected" : "";
                            echo '<option value="' . floatVal($row->hsn_code) . '" ' . $selected . '>' . floatVal($row->hsn_code) . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>

        </div>
    </div>
</form>
