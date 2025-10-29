<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-12 form-group">
                <label for="material_grade">Material Grade</label>
                <input type="text" name="material_grade" id="material_grade" class="form-control req" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="standard">Standard</label>
                <select name="standard" id="standard" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Standard</option>
                    <?php $i=1; foreach($standard as $row):
                        $selected = (!empty($dataRow->standard) && $dataRow->standard == $row->standard)?"selected":"";
                    ?>
                        <option value="<?=$row->standard?>" <?=$selected?>><?=$row->standard?></option>
                    <?php endforeach; ?>
                    ?>
                </select>
                <input type="hidden" id="standardName" name="standardName" value="<?= (!empty($dataRow->standard)) ? $dataRow->standard : ""; ?>" />
            </div>
            
            <div class="col-md-6 form-group">
                <label for="metal_code">Code</label>
                <input type="text" name="metal_code" id="metal_code" class="form-control numericOnly" value="<?= (!empty($dataRow->metal_code)) ? $dataRow->metal_code : "" ?>">
            </div>
			
            <div class="col-md-6 form-group">
                <label for="scrap_group">Scrap Group</label>
                <select name="scrap_group" id="scrap_group" class="form-control single-select req">
                    <option value="">Select Scrap Group</option>
                    <?php
                        foreach ($scrapData as $row) :
                            $selected = (!empty($dataRow->scrap_group) && $dataRow->scrap_group == $row->id) ? "selected" : "";
                            echo '<option value="'. $row->id .'" '.$selected.'>'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="color_code">Colour Code</label>
                <select name="color_code" id="color_code" class="form-control single-select">
                    <option value="">Select</option>
                    <?php   
                        foreach($colorList as $color):
                            $selected = (!empty($dataRow->color_code) && $dataRow->color_code == $color) ? "selected" : "";
                            echo '<option value="' . $color . '" ' . $selected . '>' . $color . '</option>';
                        endforeach;                                        
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','#standardc',function(){
        $('#standardName').val($(this).val());
    });
});
</script>