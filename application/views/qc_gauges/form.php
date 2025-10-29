<form>
    <div class="col-md-12">
        <div class="error item_name"></div>
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status?>" />
            <input type="hidden" name="unit_id" value="25" />
            <input type="hidden" name="item_code" id="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ?$dataRow->item_code   : ""; ?>" style="letter-spacing:1px;" />
            
            <?php if(!empty($dataRow->id)): ?>
                <div class="col-md-3 form-group">
                    <label for="category_id">Category</label>
                    <input type="text" id="category_name" value="<?=(!empty($dataRow->category_name))?'['.$dataRow->cat_code.'] '.$dataRow->category_name:""?>" class="form-control req" readOnly>
                    <input type="hidden" name="category_id" id="category_id" value="<?=(!empty($dataRow->category_id))?$dataRow->category_id:""?>" />
                    <input type="hidden" name="cat_name" id="cat_name" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
                    <input type="hidden" name="cat_code" id="cat_code" value="<?=(!empty($dataRow->cat_code))?$dataRow->cat_code:""?>" />
                    <div class="error category_id"></div>
                </div>
            <?php else: ?>    
                <div class="col-md-3 form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control single-select req">
                        <option value="">Select Category</option>
                        <?php
                            foreach ($categoryList as $row) :
                                $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                                echo '<option value="'. $row->id .'" '.$selected.'>'.((!empty($row->category_code))?'['.$row->category_code.'] '.$row->category_name:$row->category_name).'</option>';
                            endforeach;
                        ?>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="col-md-3 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" id="size" class="form-control req" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="cal_required">Cal. Required</label>
                <select name="cal_required" id="cal_required" class="form-control single-select req" >
                    <option value="YES" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "YES")?"selected":""?>>YES</option>
                    <option value="NO" <?=(!empty($dataRow->cal_required) && $dataRow->cal_required == "NO")?"selected":""?>>NO</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <div class="input-group">
                    <label for="cal_freq" style="width: 50%;">Freq. <small>(Months)</small></label>
                    <label for="cal_reminder">Reminder <small>(Days)</small></label>
                </div>
                <div class="input-group">
                    <input type="text" name="cal_freq" class="form-control floatOnly"  value="<?=(!empty($dataRow->cal_freq))?$dataRow->cal_freq:""?>" />
                    <input type="text" name="cal_reminder" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder))?$dataRow->cal_reminder:""?>" />
                </div>
            </div>
            <div class="col-md-6 form-group">
                <div class="input-group">
                    <label for="cal_freq_nos" style="width: 50%;">Freq. <small>(Volume)</small></label>
                    <label for="cal_reminder_nos">Reminder <small>(Volume)</small></label>
                </div>
                <div class="input-group">
                    <input type="text" name="cal_freq_nos" class="form-control floatOnly"  value="<?=(!empty($dataRow->cal_freq_nos))?$dataRow->cal_freq_nos:""?>" />
                    <input type="text" name="cal_reminder_nos" class="form-control floatOnly" value="<?=(!empty($dataRow->cal_reminder_nos))?$dataRow->cal_reminder_nos:""?>" />
                </div>
            </div>
            <div class="col-md-3 form-group">
                <label for="lead_time">Cal. Lead Time <small>(Days)</small></label>
                <input type="text" name="lead_time" class="form-control" value="<?=(!empty($dataRow->lead_time))?$dataRow->lead_time:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="mfg_sr">MFG. Sr No.</label>
                <input type="text" name="mfg_sr" class="form-control" value="<?=(!empty($dataRow->mfg_sr))?$dataRow->mfg_sr:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="location_id">Location</label>
                <select name="location_id" id="location_id" class="form-control single-select req">
                    <option value="">Select Location</option>
                    <?php
                        foreach($locationList as $lData):
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
								$selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : "";
                                echo '<option value="'.$row->id.'" data-location_name="'.$row->location.'" '.$selected.'>'.$row->location.' </option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="location_name" id="location_name" class="form-control req" value="<?=(!empty($dataRow->location_name))?$dataRow->location_name:""?>" />
            </div>
            <div class="col-md-3 form-group threadType">
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
			<?php if(empty($dataRow->id) OR empty($dataRow->last_cal_date)): ?>
				<div class="col-md-3 form-group">
					<label for="last_cal_date">Cali. Date</label>
					<input type="date" name="last_cal_date" id="last_cal_date" class="form-control" value="<?=(!empty($dataRow->last_cal_date))?$dataRow->last_cal_date:date('Y-m-d')?>" />  
				</div>
			<?php else: ?>
				<input type="hidden" name="last_cal_date" id="last_cal_date" value="<?=(!empty($dataRow->last_cal_date))?$dataRow->last_cal_date:date('Y-m-d')?>" />  
			<?php endif; ?>
            <div class="col-md-12 form-group remark">
                <label for="description">Remark</label>
                <input type="text" name="description" id="description" class="form-control" value="<?=(!empty($dataRow->description))?$dataRow->description:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	<?php if(!empty($dataRow->category_name) && containsWord($dataRow->category_name, 'thread')){ ?>
		$(".threadType").show();$("#thread_type").comboSelect();
        //$(".remark").attr("class","col-md-9 form-group remark");
	<?php }else{ ?>
		$(".threadType").hide();
        //$(".remark").attr("class","col-md-12 form-group remark");
	<?php } ?>
	
	$(document).on('change',"#location_id",function(){
		var location_name = $(this).find(":selected").data('location_name');
		$('#location_name').val(location_name);
	});
		
	$(document).on('change',"#category_id",function(){
		var category = $(this).find(":selected").data('cat_name');$('#cat_name').val(category);
		if (category.toLowerCase().indexOf("thread")!=-1){$(".threadType").show();$("#thread_type").comboSelect();$(".remark").attr("class","col-md-9 form-group remark");}
		else{$(".threadType").hide(); $(".remark").attr("class","col-md-12 form-group remark"); }

        var cat_code = $(this).find(":selected").data('cat_code');
        $('#cat_code').val(cat_code);
	});

    $(".generateCode").on('click',function(){
            var cat_code = $("#category_id").find(":selected").data('cat_code');
            var category_id = $("#category_id").val();
            var size = $("#size").val();
            var valid = 1;
            if(cat_code == ''){
                $(".category_id").html("Category code is required");
                valid = 0;
            }
            if(size == ''){
                $(".size").html("Enter Size");
                valid = 0;
            }

            if(valid){
                $.ajax({
                    url:base_url + controller + "/getGaugeCode",
                    type:'post',
                    data:{category_id:category_id,size:size,cat_code:cat_code},
                    dataType:'json',
                    success:function(data){
                        $("#item_code").val(data.item_code);
                        $("#store_id").val(data.part_no);
                        $("#cat_code").val(cat_code);
                    }
                });
            }
        });
})
</script>