<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />

            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <?php 
                $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
            ?>
            <div class="<?=($itmtp == 3)?'col-md-5':'col-md-8'?> form-group">
                <label for="item_name">Item Name</label>
				<?php if($itmtp == 3): ?>
                <div class="input-group">
                    <?php
                        $itmGroup = (!empty($dataRow->item_image))?explode('~@',$dataRow->item_image):"";
                    ?>
                    <input type="text" name="itmsize" id="insize" class="form-control" placeholder="Size" value="<?=(!empty($itmGroup[0]))?$itmGroup[0]:""?>" style="max-width:33%;" />
                    <input type="text" name="itmshape" id="insize" class="form-control noSpecialChar" placeholder="Shape" value="<?=(!empty($itmGroup[1]))?$itmGroup[1]:""?>" />
                    <input type="text" name="itmbartype" id="insize" class="form-control" placeholder="Bar Type" value="<?=(!empty($itmGroup[2]))?$itmGroup[2]:""?>" style="max-width:33%;" />
                    <!-- <input type="text" name="itmmaterialtype" id="insize" class="form-control noSpecialChar" placeholder="Material Type" value="<?=(!empty($itmGroup[3]))?$itmGroup[3]:""?>" /> -->
					<input type="hidden" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
                </div>
				<?php else: ?>
					<input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
				<?php endif; ?>                
            </div>
            <?php if($itmtp == 3): ?>
            <div class="col-md-3 form-group">
                <label for="itmmaterialtype">Material Grade</label>
                <select name="itmmaterialtype" id="itmmaterialtype" class="form-control single-select itmmaterialtype">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                            echo '<option value="'.$row->material_grade.'" data-grade_id="'.$row->id.'" '.$selected.'>'.$row->material_grade.' '.$row->standard.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="material_grade_id" id="material_grade_id" class="form-control" value="<?=(!empty($dataRow->material_grade_id) ? $dataRow->material_grade_id : "")?>" />
            </div>
            <?php endif; ?>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <!--<div class="col-md-3 form-group">
                <label for="family_id">Family Group</label>
                <select name="family_id" id="family_id" class="form-control single-select">
                    <option value="0">Select</option>
                    <?php
                        /*foreach ($familyGroup as $row) :
                            $selected = (!empty($dataRow->family_id) && $dataRow->family_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->family_name . '</option>';
                        endforeach;*/
                    ?>
                </select>
            </div>-->
            <div class="col-md-3 form-group">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                        foreach($unitData as $row):
                            $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->unit_name.'] '.$row->description.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <input type="text" name="hsn_code" class="form-control" value="<?=(!empty($dataRow->hsn_code))?$dataRow->hsn_code:""?>" />
            </div>
            <!-- <div class="col-md-6 form-group">
                <label for="rm_type">Item Type</label>
                <select name="rm_type" id="rm_type" class="form-control">
                    <option value="0" <?=(!empty($dataRow->rm_type) && $dataRow->rm_type == 0)?"selected":""?>>Consumable</option>
                    <option value="1" <?=(!empty($dataRow->rm_type) && $dataRow->rm_type == 1)?"selected":""?>>Raw Material</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="opening_qty">Opening Qty</label>
                <input type="number" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:""?>" />
            </div> -->
            <input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:"0"?>" />
            <div class="col-md-3 form-group">
                <label for="gst_per">GST %.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                        foreach($gstPercentage as $row):
                            $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate'])?"selected":"";
                            echo '<option value="'.$row['rate'].'" '.$selected.'>'.$row['val'].'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" min="0" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="min_qty">Min. Qty.</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="max_qty">Max. Qty.</label>
                <input type="text" name="max_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="wt_pcs">Weight Per Pcs.</label>
                <input type="text" name="wt_pcs" class="form-control floatOnly" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" class="form-control" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>
            
            <?php if($itmtp != 3): ?>
    			<div class="col-md-3 form-group">
                    <label for="material_grade">Grade</label>
                    <input type="text" name="material_grade" class="form-control" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>" />
                </div>
            <?php else: ?>
                <input type="hidden" name="material_grade" id="material_grade" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            <?php endif; ?>
            
			<div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $(document).on('change','.itmmaterialtype',function(){
            var material_grade = $(this).val();
            $("#material_grade").val(material_grade);
            
            var grade_id = $("#itmmaterialtype :selected").data('grade_id'); console.log(grade_id);
            $("#material_grade_id").val(grade_id);
        });
    });
</script>