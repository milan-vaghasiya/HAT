<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-6 form-group">
                <label for="item_id">RM Item Name</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select Raw Material</option>
					<?php
						foreach($rmData as $row):
							$selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';
						endforeach;
					?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="location_id">RM Location</label>
                <select name="location_id" id="location_id" class="form-control model-select1 req">
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
            </div>
            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" class="form-control req" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""; ?>" />
                <input type="hidden" name="ref_date" class="form-control" value="<?=(!empty($dataRow->ref_date))?$dataRow->ref_date:date("Y-m-d") ?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""; ?>" />
            </div>
           
        </div>
    </div>
</form>

<!-- <script>
$(document).ready(function(){
    $(document).on('change','#rm_item_id',function(){
		var rm_name = $("#rm_item_idc").val();
        $("#rm_name").val(rm_name);
    });

    $('.model-select1').select2({ dropdownParent: $('.model-select1').parent() });
});
</script> -->