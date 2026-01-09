<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />
            <input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:"0"?>" />

            <div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
				<input type="text" name="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code)) ? $dataRow->item_code : ""?>" />
            </div>

            <div class="col-md-9 form-group" id="item_div">
                <label for="item_name">Item Name</label>
				<input type="text" name="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name)) ? $dataRow->item_name : ""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="fg_id">Finish goods</label>
                <select name="fg_id" id="fg_id" class="form-control single-select req">
                    <option value="0">Select Item</option>
                    <?php
						foreach ($fgCodeList as $row) :
                            $selected = (!empty($dataRow->fg_id) && $dataRow->fg_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>'.(!empty($row->item_code) ? '['.$row->item_code.'] '.$row->item_name : '').' </option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <?php 
                $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
            ?>

            <div class="col-md-3 form-group">
                <label for="part_no">Die</label>
                <select name="part_no" id="part_no" class="form-control single-select">
                    <option value="0">Select Die</option>
                    <?php
						echo $dieOptions;
                    ?>
                </select>
            </div>
            <?php if($itmtp == 3): ?>
            <div class="col-md-3 form-group">
                <label for="material_grade">Material Grade</label>
                <select name="material_grade" id="material_grade" class="form-control single-select itmmaterialtype">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade_id) && $dataRow->material_grade_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->material_grade." ".$row->standard.'" data-grade_id="'.$row->id.'" '.$selected.'>[' . $row->metal_code . '] '.$row->material_grade.' - '.$row->standard.'</option>';
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
                             echo '<option value="' . $row->id . '" ' . $selected . '>'.(!empty($row->category_code)?'[ '.$row->category_code.' ] ':'').$row->category_name.' </option>';
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
            
            <div class="col-md-6 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select">
                    <option value="">Select HSN Code</option>
                    <?php
                        foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn_code) ? "selected" : "";
                            echo '<option value="' . floatVal($row->hsn_code) . '" ' . $selected . '>' . floatVal($row->hsn_code) . ' [' . $row->description . ']</option>';
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
                <label for="wt_pcs">Weight Per Pcs.</label>
                <input type="text" name="wt_pcs" class="form-control floatOnly" value="<?=(!empty($dataRow->wt_pcs))?$dataRow->wt_pcs:""?>" />
            </div>
			
            
            <?php if($itmtp != 3): ?>
    			<div class="col-md-3 form-group">
                    <label for="material_grade">Grade</label>
                    <input type="text" name="material_grade" class="form-control" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>" />
                </div>
            <?php else: ?>
                <input type="hidden" name="material_grade" id="material_grade" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            <?php endif; ?>
            
			<div class="col-md-9 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function(){
      
        $(document).on('change','#material_grade',function(){
            var material_grade = $(this).val();
            $("#material_grade").val(material_grade);
            
            var grade_id = $("#material_grade :selected").data('grade_id'); console.log(grade_id);
            $("#material_grade_id").val(grade_id);
        });

        $(document).on('change','#fg_id',function(){
            var fg_id = $(this).val();
            var part_no = '<?= !empty($dataRow->part_no) ? $dataRow->part_no : '';?>';
            if(fg_id){
                $.ajax({
                    url:base_url + controller + '/getDieListForSelect',
                    type:'post',
                    data:{item_id:fg_id,part_no:part_no},
                    dataType:'json',
                    success:function(data){
                        $("#part_no").html("");
                        $("#part_no").html(data.options);
                        $("#part_no").comboSelect();
                    }
                });
            } else {
                $("#part_no").html("<option value=''>Select Die</option>");
                $("#part_no").comboSelect();
            }
        });
        if($('#id').val() != ''){
            $('#fg_id').trigger('change');
        }
	});
    
</script>