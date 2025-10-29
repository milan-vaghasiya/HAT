<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="unit_id" value="27" />

            <div class="col-md-8 form-group">
                <label for="size">Gauge Size</label>
                <input type="text" name="size" class="form-control req" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="item_code">Code No.</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-cat_name="'.$row->category_name.'" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
				<input type="hidden" name="cat_name" id="cat_name" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="drawing_no" >Size (Go-NoGo)</label>
                <div class="input-group range">
                    <input type="text" name="drawing_no" id="drawing_no" value="<?=(!empty($dataRow->drawing_no))?$dataRow->drawing_no:""?>" class="form-control floatOnly" placeholder="Go"   />
                    <input type="text" name="part_no" id="part_no" class="form-control floatOnly" value="<?=(!empty($dataRow->part_no))?$dataRow->part_no:""?>"  placeholder="No Go" />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="drawing_file">Accept. Criteria(Go-NoGo)</label>
                <div class="input-group range">
                    <input type="text" name="drawing_file" id="drawing_file" value="<?=(!empty($dataRow->drawing_file))?$dataRow->drawing_file:""?>" class="form-control floatOnly" placeholder="Go"   />
                    <input type="text" name="rev_no" id="rev_no" class="form-control floatOnly" value="<?=(!empty($dataRow->rev_no))?$dataRow->rev_no:""?>"  placeholder="No Go" />
                </div>
            </div>
        
            <div class="col-md-4 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
			<div class="col-md-4 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control single-select req" >
                    <option value="Yes" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "Yes")?"selected":""?>>Yes</option>
                    <option value="No" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "No")?"selected":""?>>No</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_freq">Cal. Frequency <small>(Months)</small></label>
                <input type="text" name="cal_freq" class="form-control floatOnly req" value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="cal_reminder">Reminder <small>(Days)</small></label>
                <input type="text" name="cal_reminder" class="form-control floatOnly req" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
            </div>
            <div class="col-md-4 form-group">
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
            <div class="col-md-4 form-group">
                <label for="location">Location</label>
                <input type="text" name="location" class="form-control" value="<?=(!empty($dataRow->location))?$dataRow->location:""?>" />
            </div> 

            <div class="col-md-4 form-group threadType">
                <label for="thread_type">Thread Type</label>
                <select name="thread_type" id="thread_type" class="form-control single-select">
					<option value="">Select</option>
                    <?php
						foreach ($threadType as $thread_type) :
							$selected = (!empty($dataRow->thread_type) && $dataRow->thread_type == $thread_type) ? "selected" : "";
							echo '<option value="' . $thread_type . '" ' . $selected . '>' . $thread_type . '</option>';
						endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	<?php if(!empty($dataRow->category_name) && containsWord($dataRow->category_name, 'thread')){ ?>
		$(".threadType").show();$("#thread_type").comboSelect();
	<?php }else{ ?>
		$(".threadType").hide();
	<?php } ?>
		
	$(document).on('change',"#category_id",function(){
		var category = $(this).find(":selected").data('cat_name');$('#cat_name').val(category);
		if (category.toLowerCase().indexOf("thread")!=-1){$(".threadType").show();$("#thread_type").comboSelect();}
		else{$(".threadType").hide();}
	});
})
</script>