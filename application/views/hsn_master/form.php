<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />   
            <div class="col-md-6 form-group">
                <label for="hsn_code">HSN Code</label>
                <input type="text" name="hsn_code" class="form-control numericOnly req" value="<?= (!empty($dataRow->hsn_code)) ? $dataRow->hsn_code : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select req">
                    <?php
                    foreach ($gstPercentage as $row) :
                        $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate']) ? "selected" : "";
                        echo '<option value="' . $row['rate'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            
             <div class="col-md-12 form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control " rows="3"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
            
           
        </div>
    </div>
</form>
