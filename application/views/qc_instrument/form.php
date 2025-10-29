<form>
    <div class="col-md-12">
        <div class="error item_name"></div>
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="status" value="<?=(!empty($dataRow->status))?$dataRow->status:$status?>" />
            <input type="hidden" name="unit_id" value="25" />
            <input type="hidden" name="item_code" id="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ?$dataRow->item_code   : ""; ?>" style="letter-spacing:1px;" />
            <input type="hidden" name="cat_code" id="cat_code" value="<?= (!empty($dataRow->cat_code)) ? $dataRow->cat_code : ""; ?>" />
            <input type="hidden" name="cat_name" id="cat_name" value="<?= (!empty($dataRow->category_name)) ?$dataRow->category_name : ""; ?>" />
           

            <?php if(!empty($dataRow->id)): ?>
                <div class="col-md-3 form-group">
                    <label for="category_id">Category</label>
                    <input type="text" id="category_name" value="<?=(!empty($dataRow->category_name))?'['.$dataRow->cat_code.'] '.$dataRow->category_name:""?>" class="form-control req" readOnly>
                    <input type="hidden" name="category_id" id="category_id" value="<?=(!empty($dataRow->category_id))?$dataRow->category_id:""?>" />
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
                <label for="gauge_type">Inst. Type</label>
                <select name="gauge_type" id="gauge_type" class="form-control single-select req">
                    <option value="3" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)?"selected":""?>>Range</option>
                    <option value="4" <?=(!empty($dataRow->gauge_type) && $dataRow->gauge_type == 4)?"selected":""?>>Other</option>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="size">Range (mm)</label>
                    <div class="input-group range">
                        <?php
                            $minRange = '';$maxRange = '';
                            if(!empty($dataRow->size) && (!empty($dataRow->gauge_type) && $dataRow->gauge_type == 3)){
                                $range = explode("-",$dataRow->size);
                                $minRange = $range[0];$maxRange = $range[1];
                            }
                        ?>
                        <input type="text" name="min_range" id="min_range" value="<?= $minRange?>" class="form-control floatOnly" placeholder="Min"   />
                        <input type="text" name="max_range" id="max_range" class="form-control floatOnly" value="<?= $maxRange?>"  placeholder="Max" />
                    </div>
                    
                    <input type="text" name="size" id="size" class="form-control other" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" style="display:none;" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="least_count">Least Count</label>
                <input type="text" name="least_count" id="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="make_brand">Make</label>
                <input type="text" name="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>" />
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

            <div class="col-md-3 form-group">
                <label for="permissible_error">Permissible Error</label>
                <input type="text" name="permissible_error" class="form-control" value="<?=(!empty($dataRow->permissible_error))?$dataRow->permissible_error:""?>" />
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
			<?php if(empty($dataRow->id) OR empty($dataRow->last_cal_date)): ?>
				<div class="col-md-3 form-group threadType">
					<label for="last_cal_date">Cali. Date</label>
					<input type="date" name="last_cal_date" id="last_cal_date" class="form-control" value="<?=(!empty($dataRow->last_cal_date))?$dataRow->last_cal_date:date('Y-m-d')?>" />  
				</div>
			<?php else: ?>
				<input type="hidden" name="last_cal_date" id="last_cal_date" value="<?=(!empty($dataRow->last_cal_date))?$dataRow->last_cal_date:date('Y-m-d')?>" />  
			<?php endif; ?>
            <div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <textarea name="description" id="description" class="form-control"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        setTimeout(function(){ $('#gauge_type').trigger('change'); }, 5);
        
		$(document).on('change',"#location_id",function(){
			var location_name = $(this).find(":selected").data('location_name');
			$('#location_name').val(location_name);
		});
        
        $("#category_id").on('change',function(){
            var cat_code = $(this).find(":selected").data('cat_code');
            var cat_name = $(this).find(":selected").data('cat_name');
            $('#cat_code').val(cat_code);
            $('#cat_name').val(cat_name);
        });
        
        $("#gauge_type").on('change',function(){
            var inst_type = $(this).val();
            if(inst_type == 3){
                $('.range').show();
                $('.other').hide();
            }else{
                $('.range').hide();
                $('.other').show();
            }
        });

        $(".generateCode").on('click',function(){
            var cat_code = $("#category_id").find(":selected").data('cat_code');
            var category_id = $("#category_id").val();
            var min_range = $("#min_range").val();
            var max_range = $("#max_range").val();
            var least_count = $("#least_count").val();
            var valid = 1;
            if(cat_code == ''){
                $(".category_id").html("Category code is required");
                valid = 0;
            }
            if(max_range == ''){
                $(".max_range").html("Enter Range");
                valid = 0;
            }
            if(min_range == ''){
                $(".min_range").html("Enter Range");
                valid = 0;
            }
            if(least_count == ''){
                $(".least_count").html("Enter Least Count");
                valid = 0;
            }

            if(valid){
                $.ajax({
                    url:base_url + controller + "/getInstrumentCode",
                    type:'post',
                    data:{category_id:category_id,cat_code:cat_code,max_range:max_range,min_range:min_range,least_count:least_count},
                    dataType:'json',
                    success:function(data){
                        $("#item_code").val(data.item_code);
                        $("#store_id").val(data.part_no);
                        $("#cat_code").val(cat_code);
                    }
                });
            }
        });
    });
</script>