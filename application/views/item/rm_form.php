<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />
            <input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->opening_qty))?$dataRow->opening_qty:"0"?>" />

        
            <div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
                <select name="item_code" id="item_code" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
						foreach ($fgCodeList as $row) :
                            $selected = (!empty($dataRow->item_code) && $dataRow->item_code == $row->item_code) ? "selected" : "";
                            echo '<option value="' . $row->item_code . '" data-item_id="' . $row->id . '"' . $selected . '>'.$row->item_code.' </option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <?php 
                $itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
            ?>
         
            <div class="col-md-3 form-group">
                <label for="rm_type">RM Type</label>
                <select name="rm_type" id="rm_type" class="form-control" >
                   
                    <option value="1" <?=(!empty($dataRow->rm_type) && $dataRow->rm_type == "1")?"selected":""?>>IC</option>
                    <option value="0" <?=(!empty($dataRow) && $dataRow->rm_type == "0")?"selected":""?>>RM</option>
                   
                </select>
            </div>
            <div class="col-md-6 form-group" id="item_div">
                <label for="item_name">Item Name</label>
				<input type="text" name="item_name" class="form-control req" value="<?=(!empty($dataRow->item_name)) ? $dataRow->item_name : ""?>" />
            </div>
            <div class="col-md-6 form-group" id="size_div" style="display:none;">
                <label for="item_name">Item Name</label>
                <div class="input-group">
                    <?php $itmGroup = (!empty($dataRow->item_image))?explode('☻',$dataRow->item_image):""; ?>
                    <input type="text" name="itmsize" id="insize" class="form-control" placeholder="Size" value="<?=(!empty($itmGroup[0]))?$itmGroup[0]:""?>" style="max-width:33%;" />
                    <input type="text" name="itmshape" id="insize" class="form-control noSpecialChar" placeholder="Shape" value="<?=(!empty($itmGroup[1]))?$itmGroup[1]:""?>" />
                    <input type="text" name="itmbartype" id="insize" class="form-control" placeholder="Bar Type" value="<?=(!empty($itmGroup[2]))?$itmGroup[2]:""?>" style="max-width:33%;" />
                </div>
				
            </div>

            <div class="col-md-3 form-group">
                <label for="part_no">Die</label>
                <select name="part_no" id="part_no" class="form-control single-select req">
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
            
            <div class="col-md-4 form-group">
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
            <div class="col-md-4 form-group">
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
            <div class="col-md-4 form-group">
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
            
			<div class="col-md-12 form-group">
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

        $(document).on('change','#item_code',function(){
            var item_code = $(this).val();
            var item_id = $("#item_code :selected").data('item_id');
            if(item_code){
                $.ajax({
                    url:base_url + controller + '/getDieListForSelect',
                    type:'post',
                    data:{item_code:item_code, item_id:item_id},
                    dataType:'json',
                    success:function(data){
                        $("#part_no").html("");
                        $("#part_no").html(data.options);
                        $("#part_no").comboSelect();
                    }
                });
            } else {
                $("#part_no").html("<option value=''>Select Item Name</option>");
                $("#part_no").comboSelect();
            }
        });

        $(document).on('change', '#rm_type', function() {
            
		var rm_type = $(this).val();
            
		if(rm_type == 1){
			$("#item_div").show();
            $("#size_div").hide();
            $("#item_div").addClass("col-md-6");
            $("#size_div").removeClass("col-md-6");
		}else{
			$("#item_div").removeClass("col-md-6");
			$("#size_div").addClass("col-md-6");
            $("#size_div").show();
            $("#item_div").hide();
		}
	});
    setTimeout(function(){ 
		$('#rm_type').trigger('change');
	}, 500);
});
    
</script>