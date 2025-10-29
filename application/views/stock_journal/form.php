<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-6 form-group">
                <label for="rm_item_id">RM Item Name</label>
                <select name="rm_item_id" id="rm_item_id" class="form-control single-select req">
                    <option value="">Select Raw Material</option>
					<?php
						foreach($rmData as $row):
							$selected = (!empty($dataRow->rm_item_id) && $dataRow->rm_item_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';
						endforeach;
					?>
                </select>
                <input type="hidden" name="rm_name" id="rm_name" value="<?=(!empty($dataRow->rm_name))?$dataRow->rm_name:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="rm_location_id">RM Location</label>
                <select name="rm_location_id" id="rm_location_id" class="form-control model-select1 req">
                    <option value="">Select RM Location</option>
                    <?php
                        foreach($locationData as $lData):
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                echo '<option value="'.$row->id.'">'.$row->location.' </option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>          
                <input type="hidden" name="rm_batch_no" value="SJ">  
            </div>
            <div class="col-md-2 form-group">
                <label for="rm_qty">RM Qty.</label>
                <input type="text" name="rm_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->rm_qty))?$dataRow->rm_qty:""; ?>" />
            </div>
            
            <div class="col-md-6 form-group">
                <label for="fg_item_id">FG Item Name</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control single-select req">
                    <option value="">Select Finish Goods</option>
					<?php
						foreach($fgData as $row):
							$selected = (!empty($dataRow->fg_item_id) && $dataRow->fg_item_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_code.'</option>';
						endforeach;
					?>
                </select>
                <input type="hidden" name="fg_name" id="fg_name" value="<?=(!empty($dataRow->fg_name))?$dataRow->fg_name:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="fg_location_id">FG Location</label>
                <select name="fg_location_id" id="fg_location_id" class="form-control model-select2 req">
                    <option value="">Select FG Location</option>
                    <?php
                        foreach($locationData as $lData):
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                echo '<option value="'.$row->id.'">'.$row->location.' </option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>          
                <input type="hidden" name="fg_batch_no" value="SJ">  
            </div>
            <div class="col-md-2 form-group">
                <label for="fg_qty">FG Qty.</label>
                <input type="text" name="fg_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->fg_qty))?$dataRow->fg_qty:""; ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="date">Date</label>
                <input type="date" name="date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->date))?$dataRow->date:$maxDate; ?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
            </div>
            <div class="col-md-8 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="1"><?=(!empty($dataRow->remark))?$dataRow->remark:""; ?></textarea>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('change','#rm_item_id',function(){
		var rm_name = $("#rm_item_idc").val();
        $("#rm_name").val(rm_name);
    });

    $(document).on('change','#fg_item_id',function(){
		var fg_name = $("#fg_item_idc").val();
        $("#fg_name").val(fg_name);
    });

    $('.model-select1').select2({ dropdownParent: $('.model-select1').parent() });
    $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
});
</script>