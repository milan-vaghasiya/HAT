<form id="materialRequest">
    <div class="error general_error"></div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4 mt-3 form-group">
                <label for="used_at">Dispatch To</label>
                <select name="used_at" id="used_at" class="form-control">
                    <option value="0">Inhouse</option>
                    <option value="1">Vendor</option>
                </select>
                <div class="error used_at"></div>
            </div>

            <div class="col-md-4 mt-3 form-group">
                <label for="handover_to">Dispatch Location</label>
                <select name="handover_to" id="handover_to" class="form-control single-select">
                    <option value="">Select</option>
                    <?php
                        if (!empty($machineList)):
                            foreach ($machineList as $row):
                                echo '<option value="'.$row->id.'">[' . $row->item_code . '] ' . $row->item_name .'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
                <div class="error handover_to"></div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table id="requestItems" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Item Name</th>
                            <th>Store Location</th>
                            <th>Heat/Batch No.</th>
                            <th>Allocated Qty.</th>
                            <th>Pending Req. Qty.</th>
                            <th>Req. Qty.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($allocatedMaterial)) :
                            $i = 1;
                            foreach ($allocatedMaterial as $row) :
                        ?>
                            <tr class="text-center">
                                <td>
                                    <?= $i ?>
                                </td>
                                <td class="text-left">
                                    <?= $row->item_name ?>
                                    <input type="hidden" name="item[<?=$i?>][id]" value="">
                                    <input type="hidden" name="item[<?=$i?>][log_type]" value="1">
                                    <input type="hidden" name="item[<?=$i?>][reqn_type]" value="3">
                                    <input type="hidden" name="item[<?=$i?>][req_from]" value="<?=$job_id?>">
                                    <input type="hidden" name="item[<?=$i?>][req_item_id]" value="<?=$row->item_id?>">
                                    <input type="hidden" name="item[<?=$i?>][from_ref]" value="<?=$row->id?>">
                                    <input type="hidden" name="item[<?=$i?>][job_bom_id]" value="<?=$row->trans_ref_id?>">
                                </td>
                                <td>
                                    <?="[ ".$row->store_name." ] ".$row->location?>
                                    <input type="hidden" name="item[<?=$i?>][location_id]" value="<?=$row->location_id?>">
                                </td>
                                <td>                                  
                                    <?=$row->batch_no?>
                                    <input type="hidden" name="item[<?=$i?>][batch_no]" value="<?=$row->batch_no?>">
                                </td>
                                <td>
                                    <?=$row->qty?>
                                    <input type="hidden" name="item[<?=$i?>][job_allocated_stock]" value="<?=$row->qty?>">
                                </td>
                                <td>
                                    <?=$row->stock_qty?>
                                    <input type="hidden" name="item[<?=$i?>][pending_qty]" value="<?=$row->stock_qty?>">
                                </td>
                                <td>
                                    <input type="number" name="item[<?=$i?>][req_qty]" class="form-control floatOnly" value="0">
                                    <div class="error request_qty_<?= $i ?>"></div>
                                </td>
                            </tr>
                        <?php
                                    $i++;
                                endforeach;
                            else :
                        ?>
                            <tr>
                                <td colspan="6" class="text-center">No Data Found.</td>
                            </tr>
                        <?php
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>