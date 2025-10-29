<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-2 form-group">
                <label for="trans_number">Debit No.</label>
                <div class="input-group mb-3">
                    <input type="text" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix?>" readonly />
                    <input type="text" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : sprintf("%03d",$nextTransNo) ?>" readonly />	
                </div>								
            </div>

            <div class="col-md-2 form-group">
                <label for="trans_date">Date</label>
                <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" />	
			</div>

            <div class="col-md-3 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control single-select req" >
                    <option value="">Select Party</option>
                    <?php
                        foreach($customerData as $row):
                            $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id)?"selected":((!empty($customerData->party_id) && $customerData->party_id == $row->id)?"selected":"");
                            echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
                        endforeach;
                    ?>
                </select>									
            </div>

            <div class="col-md-2 form-group">
                <label for="inv_id">Invoice</label>
                <select name="inv_id" id="inv_id" class="form-control single-select req" >
                    <?php
                        if (!empty($html)) { echo $html;  } 
                        else {  ?> <option value="">Select Invoice</option>  
                    <?php } ?>
                </select>	
                
                
            </div>

            <div class="col-md-3 form-group">
                <label for="item_id">Item Description</label>
                <select name="item_id" id="item_id" class="form-control single-select req" >
                    <?php
                        if (!empty($options)) { echo $options;  } 
                        else {  ?> <option value="">Select Item</option> 
                        <?php
                            if(!empty($itemData)){
                                foreach($itemData as $row){
                                    $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'';
                                    ?>
                                    <option value="<?=$row->id?>" data-so_trans_id="0" <?=$selected?>><?=$row->item_name?></option>
                                    <?php
                                }
                            } 
                         } ?>
                </select>
                <input type="hidden" name="so_trans_id" id="so_trans_id" value="<?=(!empty($dataRow->so_trans_id))?$dataRow->so_trans_id:""?>" />								
                <input type="hidden" name="inv_child_id" id="inv_child_id" value="<?=(!empty($dataRow->inv_child_id))?$dataRow->inv_child_id:""?>" />								
            </div>
            
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" id="remark" name="remark" class=" form-control"  value="<?=(!empty($dataRow->remark))?$dataRow->trans_date:''?>" />	
			</div>
            
        </div>
        <hr>
        <div class="row form-group" id="batchDiv">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Batch No.</th>
                            <th>TC No.</th>
                            <th>Dispatched Qty.</th>
                            <th>Rework Qty.</th>
                        </tr>
                    </thead>
                    <tbody id="batchData">
                        <tr>
                            <td colspan="5" class="text-center">No data available in table</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>

    $(document).ready(function(){  
        $(document).on('keyup change',".batchQty",function(){		
           
            var id = $(this).data('rowid');
            var cl_stock = $(this).data('cl_stock');
            var batchQty = $(this).val();
            $(".batch_qty"+id).html("");
            $(".qty").html();
            if(parseFloat(batchQty) > parseFloat(cl_stock)){
                $(".batch_qty"+id).html("Stock not avalible.");
                $(this).val("");
            }
        });
	});
</script>

  