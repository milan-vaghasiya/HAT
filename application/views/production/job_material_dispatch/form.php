<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="job_bom_id" id="job_bom_id" value="<?=(!empty($dataRow->job_bom_id))?$dataRow->job_bom_id:""?>" />

            <div class="col-md-3 form-group">
                <label for="">Job Card No.</label>
                <select name="job_card_id" id="job_card_id" class="form-control single-select">
                    <option value="">Select Job Card No.</option>
                    <?php
                        foreach($jobCardData as $row):
                            $selected = (!empty($dataRow->job_card_id) && $dataRow->job_card_id == $row->id)?"selected":"";
                            if(!empty($dataRow)):
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_number.'</option>';
                            else:
                                if($row->order_status != 2):
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->job_number.'</option>';
                                endif;
                            endif;
                        endforeach;
                    ?>
                </select>                
            </div>    
 
            <div class="col-md-3 form-group">
                <label for="dispatch_item_id">Issue Item Name</label>
                <select name="dispatch_item_id" id="dispatch_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php                    
                        foreach($itemData as $row):
                            $selected = (!empty($dataRow->ref_item_id) && $dataRow->ref_item_id == $row->id)?"selected":(($dataRow->ref_item_id == $row->id)?"selected":"disabled");  
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->item_name.'</option>';     
                        endforeach;
                    ?>
                </select>                
            </div>
            <div class="col-md-3 form-group">
                <label for="dispatch_date">Issue Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" min="<?=(!empty($dataRow))?$dataRow->job_date:$this->startYearDate?>" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="text" id="req_qty" class="form-control" value="<?=(!empty($dataRow->req_qty))?$dataRow->req_qty:0?>" readonly />
            </div>

          
            <div class="col-md-3 form-group lc">
                <label for="location_id">Store Location</label>
                <select id="location_id" name="location_id" class="form-control single-select req">
                    <option value=""  data-store_name="">Select Location</option>
                    <?php
                         if(!empty($locationData)):
                            foreach($locationData as $key=>$option): 
                                echo '<optgroup label="'.$key.'">';
                                    foreach($option as $val):
                                        echo '<option value="'.$val->location_id.'" data-store_name="'.$val->store_name.'">'.$val->location.'</option>';
                                    endforeach; 
                                echo '</optgroup>';
                            endforeach; 
                        endif;
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="batch_no">Heat/Batch No.</label>
                <select id="batch_no" name="batch_no" class="form-control single-select req">
                    <option value="">Select Batch No.</option>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_stock">Stock Qty.</label>
                <input type="text" id="batch_stock" class="form-control" value="" readonly />
                <input type="hidden" id="tc_no" name="tc_no" class="form-control" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label for="qty">Issue Qty.</label>
                <input type="text" id="qty" name="qty" class="form-control floatOnly req" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block " onclick="saveMaterialIssue('dispatchMaterial')"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Issue Date</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>T.C. No.</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyData">
                        <?php
                            if(!empty($batchTrans)):
                                echo $batchTrans;
                            endif;
                        ?>
                        </tbody>
                    </table>
                   
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});
});
</script>