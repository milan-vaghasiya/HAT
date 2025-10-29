<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-4 form-group">
                <label for="item_code">Machine No.</label>
                <input type="text" name="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:"";?>" />
            </div>
            <div class="col-md-8 form-group">
                <label for="item_name">Description/Name</label>
                <input name="item_name" id="item_name" class="form-control" style="resize:none;" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="make_brand">Make/Brand</label>
                <input type="text" name="make_brand" id="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" class="form-control" value="<?=(!empty($dataRow->model))?$dataRow->model:""?>">
            </div>
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
            <div class="col-md-3 form-group">
                <label for="gst_per">GST Per.</label>
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
                <label for="size">Capacity</label>
                <input type="text" name="size" id="size" class="form-control" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>">
            </div>
            <!--<div class="col-md-3 form-group">-->
            <!--    <label for="install_year">Installation Year</label>-->
            <!--    <input type="text" name="install_year" id="install_year" class="form-control" value="<?=(!empty($dataRow->install_year))?$dataRow->install_year:""?>">-->
            <!--</div>-->
            <div class="col-md-3 form-group">
                <label for="installation_date">Installation Date</label>
                <input type="date" id="installation_date" name="installation_date" class="form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->installation_date))?$dataRow->installation_date:date("Y-m-d")?>" />	
            </div>
            <div class="col-md-3 form-group">
                <label for="part_no">Serial No.</label>
                <input type="text" name="part_no" class="form-control" value="<?=(!empty($dataRow->part_no))?$dataRow->part_no:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="machine_hrcost">Hourly Cost</label>
                <input type="text" name="machine_hrcost" class="form-control floatOnly" value="<?=(!empty($dataRow->machine_hrcost))?$dataRow->machine_hrcost:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="prev_maint_req">Pre. Maintanance?</label>
                <select name="prev_maint_req" id="prev_maint_req" class="form-control" >
                    <option value="No" <?=(!empty($dataRow->prev_maint_req) && $dataRow->prev_maint_req == "No")?"selected":""?>>No</option>
                    <option value="Yes" <?=(!empty($dataRow->prev_maint_req) && $dataRow->prev_maint_req == "Yes")?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-5 form-group">
                <label for="location">Department</label>
                <select name="location" id="location" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptData as $row):
                            $selected = (!empty($dataRow->location) && $dataRow->location == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="process_id">Process Name</label>
                <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                        foreach ($processData as $row) :
                            $selected = '';
                            if(!empty($dataRow->process_id)){
                                if (in_array($row->id,explode(',',$dataRow->process_id))) {
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:"" ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Notes</label>
                <textarea name="description" id="description" class="form-control" style="resize:none;"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('change','#location',function(e){
		var dept_id = $(this).val();
		if(dept_id)
		{
			$.ajax({
				url: base_url + controller + '/getProcessData',
				data: {dept_id:dept_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					if(data.status===0){
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
					} else {
						$("#processSelect").html(data.option);
						reInitMultiSelect();
					}
				}
			});
		}
	});
});
</script>