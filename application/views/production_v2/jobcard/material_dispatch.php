<form id="materialDispatch">
    <input type="hidden" name="job_id" value="<?=$job_id?>">
    <div class="error general_error"></div>
    <div class="table-responsive">
        <table id="dispatchItems" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Store</th>
                    <th>Lot No.</th>
                    <th>Weight/Pcs</th>
                    <th>Issue Qty.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($disptachData)):
                        $i=1;
                        foreach($disptachData as $row):
                ?>
                        <tr>
                            <td><?=$i?></td>
                            <td>
                                <?=$row->item_name?>
                                <input type="hidden" name="bom_item_id[]" value="<?=$row->ref_item_id?>">
                                <input type="hidden" name="material_type[]" value="<?=$row->rm_type?>">
                            </td>
                            <td>
                                <select name="location_id[]" id="location_id" class="form-control single-select">
									<option value="">Select Location</option>
                                    <?php
										foreach($row->locationData as $lData):
											echo '<optgroup label="'.$lData['store_name'].'">';
											foreach($lData['location'] as $ld):
												echo '<option value="'.$ld->id.'">'.$ld->location.' </option>';
											endforeach;
											echo '</optgroup>';
                                        endforeach;
									?>
                                </select>
                                <div class="error location_id<?=$i?>"></div>
                            </td>
                            <td>
                                <?php 
                                    if(!empty($row->rm_type)):
                                ?>
                                <select name="lot_trans_id[]" id="lot_trans_id" class="form-control single-select">
                                    <option value="">Select Lot No.</option>
                                    <?php
                                        if($row->opening_qty != "0.000"):
                                            echo '<option value="-1">[ Opening Qty : '.$row->opening_qty.' ]</option>';
                                        endif;
                                        foreach($row->lot_data as $lotRow):
                                            echo '<option value="'.$lotRow->id.'">'.$lotRow->lot_no.' [ Stock Qty. : '.$lotRow->remaining_qty.' ]</option>';
                                        endforeach;
                                    ?>
                                </select>
                                <div class="error lot_trans_id<?=$i?>"></div>
                                <?php
                                    else:
                                        echo '<input type="hidden" name="lot_trans_id[]" value="0">';
                                    endif;
                                ?>
                            </td>
                            <td>
                                <input type="text" name="bom_qty[]" id="bom_qty" class="form-control" value="<?=(!empty($row->qty))?$row->qty:0?>" readonly />
                            </td>
                            <th>
                                <input type="number" name="dispatch_qty[]" id="dispatch_qty" class="form-control floatOnly" value="<?=(!empty($row->dispatch_qty))?$row->dispatch_qty:0?>">
                                <div class="error dispatch_qty<?=$i?>"></div>
                            </th>
                        </tr>
                <?php
                            $i++;
                        endforeach;
                    else:
                ?>
                    <tr>
                        <td colspan="5" class="text-center">No Data Found.</td>
                    </tr>
                <?php
                    endif;
                ?>
            </tbody>
        </table>
    </div>
</form>
            