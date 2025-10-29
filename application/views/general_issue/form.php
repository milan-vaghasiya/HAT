<form>
    <?php
    /** Updated By Mansee @ 19-02-22 */
    ?>
    <div class="col-md-12">
        <div class="row">
            <!-- <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" /> -->

            <input type="hidden" id="count_item" value="0">

            <div class="col-md-2 form-group">
                <label for="dispatch_date">Issue Date</label>
                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" min="<?= (!empty($dataRow)) ? $dataRow->req_date : $this->startYearDate ?>" max="<?=$maxDate?>"  value="<?= (!empty($dataRow->dispatch_date)) ? $dataRow->dispatch_date : $maxDate ?>">
            </div>
            <div class="col-md-2 form-group">
                <label for="collected_by">Material Collected By</label>
                <select name="collected_by" id="collected_by" class="form-control single-select">
                    <option value="">Select Employee</option>
                    <?php
                    $selected = '';
                    foreach ($empData as $row) :
                        $selected = (!empty($dataRow->collected_by) && $dataRow->collected_by == $row->id) ? "selected" :((!empty($requested_id) && $requested_id == $row->id) ? "selected" :"") ;
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 from-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="from-control single-select">
                    <option value="">Select Department</option>
                    <?php
                    $selected = '';
                    foreach ($deptData as $row) :
                        $selected = (!empty($dataRow->dept_id) && $dataRow->dept_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
            <hr style="width:100%">
            <div class="col-md-3 form-group">
                <label for="dispatch_item_id">Issue Item Name</label>
                <select name="dispatch_item_id" id="dispatch_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php
                    foreach ($itemData as $row) :
                        $selected = (!empty($dataRow->req_item_id) && $dataRow->req_item_id == $row->id) ? "selected" : "";
                        if(!empty($row->req_id)):
                            echo '<option value="' . $row->id . '" data-req_id="' . $row->req_id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        else:
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_name . '</option>';
                        endif;
                    endforeach;
                    ?>
                </select>
            </div>



            <div class="col-md-2 form-group lc">
                <label for="location_id">Store Location</label>
                <select id="location_id" class="form-control single-select1 model-select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            $selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : '';
                            $disabled = (!empty($dataRow->location_id) && $dataRow->location_id != $row->id) ? "disabled" : '';

                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" ' . $selected . ' ' . $disabled . '>' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" id="id" name="id" value="0">
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_no">Heat/Batch No.</label>
                <select id="batch_no" class="form-control select2 single-select11 req">
                    <option value="">Select Batch No.</option>
                    <?php
                    if (!empty($batchData)) :
                        foreach ($batchData as $row) :
                            if ($row->qty > 0) :
                                $selected = (!empty($dataRow->batch_no) && $dataRow->batch_no == $row->batch_no) ? "selected" : 'disabled';

                                echo '<option value="' . $row->batch_no . '" data-stock="' . $row->qty . '" ' . $selected . '>' . $row->batch_no . '</option>';
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_stock">Stock Qty.</label>
                <input type="text" id="batch_stock" class="form-control" value="" readonly />
            </div>

            <div class="col-md-2 form-group">
                <label for="batch_qty">Issue Qty.</label>
                <input type="numbet" id="batch_qty" class="form-control floatOnly req" value="<?= !empty($dataRow->req_qty) ? $dataRow->req_qty : '' ?>" />
            </div>

            <div class="col-md-1 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>

            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table id="issueItems" class="table table-striped table-borderless">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Qty.</th>
                                <th style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempItem">
                            <tr id="noData">
                                <td class="text-center" colspan="6">No Data Found</td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    if (!empty($dataRow->trans_data)) :
                        if (!empty($dataRow->trans_data)) :
                            foreach ($dataRow->trans_data as $row) :
                                echo '<script>var postData={id:"'.$row->ref_id.'",ref_id:"",batch_no:"' . $row->batch_no . '",qty:"' . abs($row->dispatch_qty) . '",location_id:"' . $row->location_id . '",location_name:"[ ' . $row->store_name . ' ] ' . $row->location . '",item_id:"' . $row->item_id . '",item_name:"' . $row->item_name . '"}; addRow(postData); $("#count_item").val(parseFloat($("#count_item").val()) + 1);</script>';
                            endforeach;
                        endif;
                    endif;
                    ?>
                </div>
            </div>


        </div>
    </div>
</form>
<script>
    $(document).ready(function () {
	    $("#dispatch_item_id").trigger("change");
    });

</script>
