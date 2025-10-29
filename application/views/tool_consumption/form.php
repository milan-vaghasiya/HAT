<form>
    <div class="col-md-12">
        <div class="row form-group">
            <input type="hidden" name="item_id" class="item_id" value="<?=$item_id?>" />
            <div class="col-md-4">
                <label for="ref_item_id">Consumable Item</label>
                <select id="ref_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($consumableData as $row):
                            echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error ref_item_id"></div>
            </div>
            <div class="col-md-2">
                <label for="tool_life">Tool Life</label>
                <input type="text" id="tool_life" class="form-control floatOnly req" placeholder="Tool Life" value="" min="0" />
                <div class="error tool_life"></div>
            </div>
            <div class="col-md-4">
                <label for="operation_id">Production Operation</label>
                <select name="operationSelect" id="operationSelect" data-input_id="operation_id" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        foreach ($operationData as $row) :  
                            $selected = (!empty($operation) && (in_array($row->id, $operation))) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-operation_name="'.$row->operation_name.'" ' . $selected . '>' . $row->operation_name . '</option>';
                        endforeach;
                    ?>
                </select>     
               <input type="hidden" name="operation_id" id="operation_id" value="<?=(!empty($operation) ? implode(',',$operation):"")?>" />
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light float-right mt-30 save-form" onclick="AddRow();" ><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
            <table id="toolConsumption" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <!-- <th>Process</th> -->
                        <th>Item Name</th>
                        <th>Tool Life</th>
                        <th>Operation</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="kitItems">
                    <?php
                        if(!empty($toolConsumptionData)):
                            $i=1;
                            foreach($toolConsumptionData as $row):
                                echo '<tr>
                                            <td>'.$i++.'</td>
                                            <td>
                                                '.$row->item_name.'
                                                <input type="hidden" name="ref_item_id[]" value="'.$row->ref_item_id.'">
                                                <input type="hidden" name="id[]" value="'.$row->id.'">
                                            </td>
                                            <td>
                                                '.$row->tool_life.'
                                                <input type="hidden" name="tool_life[]" value="'.$row->tool_life.'">
                                            </td>
                                            <td>
                                                '.$row->operation.'
                                                <input type="hidden" name="operation_id[]" value="'.$row->operation.'">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                                            </td>
                                        </tr>';
                            endforeach;
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        initMultiSelect();
    });
</script>  