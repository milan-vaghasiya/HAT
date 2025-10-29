<?php 
	$itmtp = (!empty($dataRow->item_type))?$dataRow->item_type:$item_type;  
	$itemName = '';//(!empty($dataRow->item_image))?explode('☻',$dataRow->item_image):'';  
?>
<form>
    <div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />
            <input type="hidden" name="sname" id="sname" value="<?=(!empty($itemName[0]))?$itemName[0]:''; ?>" />
            <input type="hidden" name="cname" id="cname" value="<?=(!empty($itemName[1]))?$itemName[1]:''; ?>" />
            <input type="hidden" name="bname" id="bname" value="<?=(!empty($itemName[2]))?$itemName[2]:''; ?>" />
            <input type="hidden" name="gname" id="gname" value="<?=(!empty($itemName[3]))?$itemName[3]:''; ?>" />
            <input type="hidden" name="thread_type" id="thread_type" value="<?=(!empty($dataRow->thread_type))?$dataRow->thread_type:"" ?>" />
			<?php if($itmtp == 3){ ?>
			<div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req generateCode">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-code="'.$row->category_code.'" data-cname="'.$row->category_name.'" ' . $selected . '>[' . $row->category_code . '] ' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-4 form-group">
                <label for="material_grade_id">Material Grade</label>
                <select name="material_grade_id" id="material_grade_id" class="form-control single-select generateCode">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade_id) && $dataRow->material_grade_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" data-code="'.$row->metal_code.'" data-material_grade="'.$row->material_grade.'" '.$selected.'>[' . $row->metal_code . '] '.$row->material_grade.'</option>';
                        endforeach;
                    ?>
                </select>
				<input type="hidden" name="material_grade" id="material_grade" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>">
            </div>
			<div class="col-md-4 form-group">
                <label for="make_brand">Bar Type</label>
				<select name="make_brand" id="make_brand" class="form-control single-select req generateCode">
                    <option value="0">Select</option>
                    <?php
                        foreach ($barTypeList as $row) :
                            $selected = (!empty($dataRow->make_brand) && $dataRow->make_brand == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-code="'.$row->code.'" data-bname="'.$row->title.'" ' . $selected . '>[' . $row->code . '] ' . $row->title . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-4 form-group">
                <label for="instrument_range">Sub Type</label>
				<select name="instrument_range" id="instrument_range" class="form-control single-select req generateCode">
                    <option value="0">Select</option>
                    <?php
                        foreach ($barSubTypeList as $row) :
                            $selected = (!empty($dataRow->instrument_range) && $dataRow->instrument_range == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-code="'.$row->code.'" data-sbname="'.$row->title.'" ' . $selected . '>[' . $row->code . '] ' . $row->title . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-4 form-group">
                <label for="size">Item Size</label>
                <select name="size" id="size" class="form-control single-select req generateCode">
                    <option value="0">Select</option>
                    <?php
                        foreach ($sizeList as $row) :
                            $selected = (!empty($dataRow->size) && $dataRow->size == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-code="'.$row->code.'" data-sname="'.$row->title.'" ' . $selected . '>[' . $row->code . '] ' . $row->title . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
			<div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" id="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" readonly />
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
                <input type="text" name="hsn_code" class="form-control" value="<?=(!empty($dataRow->hsn_code))?$dataRow->hsn_code:""?>" />
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
                <label for="min_qty">Min. Qty.</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>
			<!--<div class="col-md-12 form-group">
                <label for="other">Other Detail <small>(Display In Item Name)</small></label>
                <input type="other" name="other" class="form-control" value="<?= (!empty($dataRow->other)) ? $dataRow->other : "" ?>" />
            </div>-->
			<div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
			<?php } ?>
		</div>
    </div>
</form>

<script>
    $(document).ready(function(){
        $(document).on('change','#material_grade_id',function(){
            //var material_grade = $('#material_grade_idc').val();
            var material_grade = $("#material_grade_id :selected").data('material_grade') || '';
			$("#material_grade").val(material_grade);
			/*var mg=material_grade.split('] ');
			if(mg[1]){$("#material_grade").val(mg[1]);}
			else{$("#material_grade").val(material_grade);}*/
        });
		$(document).on('change','.generateCode',function(){
			var ccode = $("#category_id :selected").data('code') || '';
			var mcode = $("#material_grade_id :selected").data('code') || '';
			var bcode = $("#make_brand :selected").data('code') || '';
			var sbcode = $("#instrument_range :selected").data('code') || '';
			var scode = $("#size :selected").data('code') || '';
			var icode = ccode + mcode + bcode + sbcode + scode;
			$("#item_code").val(icode);
			
			var cname = $("#category_id :selected").data('cname') || '';
			$("#cname").val(cname);
			var gname = $("#material_grade_id :selected").data('material_grade') || '';
			$("#gname").val(gname);
			var bname = $("#make_brand :selected").data('bname') || '';
			$("#bname").val(bname);
			var sbname = $("#instrument_range :selected").data('sbname') || '';
			$("#thread_type").val(sbname);
			var sname = $("#size :selected").data('sname') || '';
			$("#sname").val(sname);
        });
    });
</script>