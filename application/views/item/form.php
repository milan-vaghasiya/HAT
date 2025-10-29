<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />
            <input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:"0"?>" />

            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <?php 
                $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
            ?>
            <div class="col-md-8 form-group">
                <label for="item_name">Item Name</label>				
				<input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
						foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->category_code . '] ' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
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
                <select name="hsn_code" id="hsn_code" class="form-control single-select">
                    <option value="0">Select</option>
                    <?php
						foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn_code) ? "selected" : "";
                            echo '<option value="' . $row->hsn_code . '" ' . $selected . '>' . floatVal($row->hsn_code) . ' [' . $row->description . ']</option>';
                            //echo '<option value="' . $row->hsn_code . '" ' . $selected . '>' . $row->hsn_code . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
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
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" id="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="mfg_srno">Serial No.</label>
                <input type="text" name="mfg_srno" id="mfg_srno" class="form-control numericOnly" value="<?=(!empty($dataRow->mfg_srno))?$dataRow->mfg_srno:""?>" />
            </div>
            <div class="col-md-3 form-group lc">
                <label for="location">Location</label>
                <select name="location" id="location" class="form-control single-select"> 
                    <option value="">Select Location</option>
                    <?php
                        foreach($locationData as $lData):
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                $selected = (!empty($dataRow->location) && $dataRow->location == $row->id) ? "selected" : "";
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->location . '</option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="sub_group">Sub Group</label>
                <select name="sub_group" id="sub_group" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
						foreach ($subGroupList as $row) :
                            $selected = (!empty($dataRow->sub_group) && $dataRow->sub_group == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->title . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="price">Price</label>
                <input type="text" name="price" id="price" min="0" class="form-control floatOnly" value="<?=(!empty($dataRow->price))?$dataRow->price:""?>" />
            </div>	
            
			<div class="col-md-9 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>

            <div class="col-md-12 form-group">
                <hr><h5>Technical Specification</h5><hr style="margin-bottom:0px;">
            </div>

            <div class="col-md-3 form-group">
                <label for="wt_pcs">Weight/Pcs</label>
                <input type="text" name="wt_pcs" class="form-control floatOnly" value="<?= (!empty($dataRow->wt_pcs)) ? $dataRow->wt_pcs : "" ?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="min_qty">Min Qty</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="material_grade">Material Grade</label>
                <select name="material_grade" id="material_grade" class="form-control single-select itmmaterialtype">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                            echo '<option value="'.$row->material_grade.'" data-grade_id="'.$row->id.'" '.$selected.'>[' . $row->metal_code . '] '.$row->material_grade.'</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="material_grade_id" id="material_grade_id" class="form-control" value="<?=(!empty($dataRow->material_grade_id) ? $dataRow->material_grade_id : "")?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="batch_stock">Stock Type</label>
                <select name="batch_stock" id="batch_stock" class="form-control">
                    <option value="0" <?=(!empty($dataRow->batch_stock) && $dataRow->batch_stock == 0)?"selected":""?>>None</option>
                    <option value="1" <?=(!empty($dataRow->batch_stock) && $dataRow->batch_stock == 1)?"selected":""?>>Batch Wise</option>
                    <option value="2" <?=(!empty($dataRow->batch_stock) && $dataRow->batch_stock == 2)?"selected":""?>>Serial Wise</option>
                </select>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="no_of_corner">No. of Corner</label>
                <input type="text" name="no_of_corner" class="form-control" value="<?= (!empty($dataRow->no_of_corner)) ? $dataRow->no_of_corner : "" ?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="tool_life">Life</label>
                <input type="text" name="tool_life" class="form-control" value="<?= (!empty($dataRow->tool_life)) ? $dataRow->tool_life : "" ?>" />
            </div>
            
            <div class="col-md-4 form-group">
                <label for="unit_of_life">Unit of Life</label>
                <select name="unit_of_life" id="unit_of_life" class="form-control single-select">
                    <option value="0">--</option>
                    <?php
                        foreach($unitData as $row):
                            $selected = (!empty($dataRow->unit_of_life) && $dataRow->unit_of_life == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->unit_name.'] '.$row->description.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-8 form-group">
                <div class="input-group">
                    <label for="diameter" style="width:35%">Dia. (mm)</label>
                    <label for="length" style="width:35%">Length (mm)</label>
                    <label for="flute_length">Flute Length (mm)</label>
                </div>
                <div class="input-group">
                    <?php
                    $diameter ='';$length ='';$flute_length ='';
                    if(!empty($dataRow->size)){
                        $size = explode("X",$dataRow->size);
                        $diameter =!empty($size[0])?$size[0]:'';$length =!empty($size[1])?$size[1]:'';$flute_length =!empty($size[2])?$size[2]:'';
                    }
                    ?>
                    <input type="text" id="diameter" name="diameter" class="form-control floatOnly" value="<?=$diameter?>">
                    <input type="text" id="length" name="length" class="form-control floatOnly"  value="<?=$length?>">
                    <input type="text" id="flute_length" name="flute_length" class="form-control floatOnly"  value="<?=$flute_length?>">
                </div>
            </div>
            
            <div class="col-md-4 form-group">
                <label for="rev_specification">Specification</label>
                <input type="text" name="rev_specification" class="form-control" value="<?= (!empty($dataRow->rev_specification)) ? $dataRow->rev_specification : "" ?>" />
            </div>

        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $(document).on('change','.itmmaterialtype',function(){
            var material_grade = $(this).val();
            $("#material_grade").val(material_grade);
            
            var grade_id = $("#material_grade :selected").data('grade_id');
            $("#material_grade_id").val(grade_id);
        });
    });
</script>