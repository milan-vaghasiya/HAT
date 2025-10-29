<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

            <div class="col-md-4 form-group">
                <label for="dispatch_date">Issue Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" min="<?=(!empty($dataRow))?$dataRow->issue_date:$startYearDate?>" max="<?=$maxDate?>" value="<?=(!empty($dataRow))?$dataRow->issue_date:$maxDate?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="material_type">Material Type</label>
                <input type="text" class="form-control" value="Consumable" readonly />
                <input type="hidden" name="material_type" id="material_type" value="2" />             
            </div>

            <div class="col-md-4 form-group">
                <label for="collected_by">Material Collected By</label>
                <input type="text" name="collected_by" id="collected_by" placeholder="Enter Person Name" class="form-control"  value="" />
            </div>

            <div class="col-md-3 form-group">
                <label for="">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                    <option value="">Select Job Card No.</option>
                    <option value="-1">General Issue</option>
                    <?php
                        foreach($jobCardData as $row):
                            $selected = "";
                            if(!empty($dataRow)):
                                echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->job_prefix.$row->job_no.'</option>';
                            else:
                                //if($row->order_status != 2):
                                    echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.$row->job_prefix.$row->job_no.'</option>';
                                //endif;
                            endif;
                        endforeach;
                    ?>
                </select>                
            </div>    
            
            <div class="col-md-3 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptData as $row):
                            $selected = ($row->id == 9)?'selected':'';
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>   

            <div class="col-md-3 form-group">
                <label for="process_id">Process Name</label>
                <select name="process_id" id="process_id" class="form-control">
                    <option value="">Select Process Name</option>
                    <?php
                        /* foreach($processList as $row):
                            $selected = "";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->process_name.'</option>';
                        endforeach; */
                    ?>
                </select>
            </div>            

            <div class="col-md-3 form-group">
                <label for="dispatch_item_id">Issue Item Name</label>
                <select name="dispatch_item_id" id="dispatch_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php                    
                        foreach($itemData as $row):
                            if($row->item_type == 2):
                                echo '<option value="'.$row->id.'" >'.$row->item_name.'</option>';  
                            endif;                        
                        endforeach;
                    ?>
                </select>                
            </div>

            <input type="hidden" name="dispatch_qty" id="dispatch_qty" value="<?=(!empty($dataRow->total_qty))?$dataRow->total_qty:0?>">
            
            <div class="col-md-3 form-group lc">
                <label for="location_id">Store Location</label>
                <select id="location_id" class="form-control single-select1 model-select2 req">
                    <option value=""  data-store_name="">Select Location</option>
                    <?php
                        foreach($locationData as $lData):                            
                            echo '<optgroup label="'.$lData['store_name'].'">';
                            foreach($lData['location'] as $row):
                                $selected = ($row->id == 6) ? 'selected' : '' ;
                                echo '<option value="'.$row->id.'" data-store_name="'.$lData['store_name'].'" '.$selected.'>'.$row->location.' </option>';
                            endforeach;
                            echo '</optgroup>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="batch_no">Heat/Batch No.</label>
                <select id="batch_no" class="form-control single-select req">
                    <option value="">Select Batch No.</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_stock">Stock Qty.</label>
                <input type="text" id="batch_stock" class="form-control" value="" readonly />
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_qty">Issue Qty.</label>
                <input type="numbet" id="batch_qty" class="form-control floatOnly req" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_error"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-striped table-borderless">
                        <thead class="thead-info">       
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item Name</th>
                                <th>Part Code</th>
                                <th>Department</th>
                                <th>Material Collected By</th>
                                <th>Location</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <tr id="noData">
                                <td class="text-center" colspan="8">No Data Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                        if(!empty($dataRow)):
                            if(!empty($batchTrans)):
                                foreach($batchTrans as $row):
                                    echo '<script> var postData = {id:'.$row->trans_id.',dispatch_date:"'.$row->dispatch_date.'",material_type:"'.$row->material_type.'",collected_by:"'.$row->collected_by.'",job_card_id:"'.$row->job_card_id.'",process_id:"'.$row->process_id.'",dispatch_item_id:"'.$row->dispatch_item_id.'",dispatch_item_name:"'.$row->item_name.'",location_id:"'.$row->location_id.'",dept_id:"'.$row->dept_id.'",dept_idc:"'.$row->dept_name.'",collected_by:"'.$row->collected_by.'",location_name:"[ '.$row->store_name.' ]'.$row->location.'",batch_no:"'.$row->batch_no.'",qty:"'.$row->qty.'",job_card_idc:"'.$row->part_code.'"}; addRow(postData);</script>';
                                endforeach;
                            endif;
                        endif;
                    ?>
                </div>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});

	$(document).on('change','#dept_id',function(e){
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
                        $("#process_id").html(data.option);
					}
				}
			});
		}
	});
});
</script>