<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="8" />

            <div class="col-md-4 form-group">
                <label for="item_code">Die No.</label>
                <input type="text" name="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="item_name">Pin Dia.</label>
                <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="size">Ball Size</label>
                <input type="text" name="size" class="form-control req" value="<?= (!empty($dataRow->size)) ? $dataRow->size : "" ?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label  for="fg_id">Product</label>
                <select id="dieSelect" data-input_id="fg_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                        foreach ($itemData as $row) :
                            $selected = '';
                            if(!empty($dataRow->fg_id)){
                                if (in_array($row->id,explode(',',$dataRow->fg_id))) {
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_code . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="fg_id" id="fg_id" value="<?=(!empty($dataRow->fg_id))?$dataRow->fg_id:"" ?>" />
            </div>
        
			
            <div class="col-md-4 form-group">
                <label for="wt_pcs">Casting Weight</label>
                <input type="text" name="wt_pcs" class="form-control floatOnly" value="<?= (!empty($dataRow->wt_pcs)) ? $dataRow->wt_pcs : "" ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="part_no">Cavity</label>
                <input type="text" name="part_no" class="form-control" value="<?= (!empty($dataRow->part_no)) ? $dataRow->part_no : "" ?>" />
            </div> 
            <div class="col-md-6 form-group">
                <label for="item_name">Die Diamention</label>
                <div class="input-group">
					<?php
                        $dieDime = (!empty($dataRow->item_image))?explode(' X ',$dataRow->item_image):"";
                    ?>
                    <input type="text" name="die_od" id="die_od" class="form-control" placeholder="OD" value="<?=(!empty($dieDime[0]))?$dieDime[0]:""?>" style="width:33%;" />
                    <input type="text" name="die_id" id="die_id" class="form-control" placeholder="ID" value="<?=(!empty($dieDime[1]))?$dieDime[1]:""?>" style="width:34%;" />
                    <input type="text" name="die_length" id="die_length" class="form-control" placeholder="Length" value="<?=(!empty($dieDime[2]))?$dieDime[2]:""?>" style="width:33%;" />
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label for="item_name">Casting Diamention</label>
					<?php
                        $castDime = (!empty($dataRow->material_grade))?explode(' X ',$dataRow->material_grade):"";
                    ?>
                <div class="input-group">
                    <input type="text" name="cast_od" id="cast_od" class="form-control" placeholder="OD" value="<?=(!empty($castDime[0]))?$castDime[0]:""?>" style="width:33%;" />
                    <input type="text" name="cast_id" id="cast_id" class="form-control" placeholder="ID" value="<?=(!empty($castDime[1]))?$castDime[1]:""?>" style="width:34%;" />
                    <input type="text" name="cast_length" id="cast_length" class="form-control" placeholder="Length" value="<?=(!empty($castDime[2]))?$castDime[2]:""?>" style="width:33%;" />
                </div>
            </div>
			
        </div>
    </div>
</form>