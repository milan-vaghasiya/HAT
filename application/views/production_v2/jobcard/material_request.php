<form id="materialRequest">
<?php
        /** Updated By Mansee @ 19-02-22 */
    ?>
    <input type="hidden" name="job_id" value="<?= $job_id ?>">
    <div class="error general_error"></div>
    <div class="row">
        <div class="col-md-6 form-group"><label>Request To Store</label></div>
        <div class="col-md-3 form-group">
            <select id="machine_id" name="machine_id" class="form-control single-select">
                <option value="">Select Machine</option>
                <?php
                foreach ($machineData as $row) :
                    echo '<option value="' . $row->id . '">[ ' . $row->item_code . ' ] ' . $row->item_name . '</option>';
                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <input type="date" id="req_date" name="req_date" class="form-control" placeholder="dd-mm-yyyy" min="<?= (!empty($jobCardData->job_date)) ? $jobCardData->job_date : $maxDate ?>" value="<?= (!empty($dataRow->req_date)) ? $dataRow->req_date : $maxDate ?>" max="<?=$maxDate?>" />
        </div>
    </div>
    <div class="table-responsive">
        <table id="requestItems" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%">#</th>
                    <th>Item Name</th>
                    <th>Weight/Pcs</th>
                    <th>Store Location</th>
                    <th>Heat/Batch No.</th>
                    <th>Stock Qty.</th>
                    <th>Req. Qty.</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $mismatch = 0;
                if (!empty($disptachData)) :
                    $i = 1;
                    foreach ($disptachData as $row) :
                        //$kitItemKey = array_search($row->ref_item_id,array_column($kitData,'ref_item_id'));
                        //if(empty($mismatch)):
                        //    if($row->ref_item_id != $kitData[$kitItemKey]->ref_item_id || $row->qty != $kitData[$kitItemKey]->qty):
                        //        $mismatch = 1;
                        //    endif;
                        //endif;
                ?>
                        <tr class="text-center">
                            <td>
                                <?= $i ?>
                            </td>
                            <td class="text-left">
                                <?= $row->item_name ?>
                                <input type="hidden" name="bom_item_id[]" value="<?= $row->ref_item_id ?>">
                                <input type="hidden" name="material_type[]" value="<?= $row->item_type ?>">
                            </td>
                            <!-- <td><?= $row->stock_qty ?> <small><?= $row->unit_name ?></small></td> -->
                            <td>
                                <input type="text" name="bom_qty[]" id="bom_qty" class="form-control" value="<?= (!empty($row->qty)) ? $row->qty : 0 ?>" readonly />
                            </td>
                            <td>

                                <select id="location_id<?=$row->id?>" name="location_id[]" class="location form-control single-select1 model-select2 " >
                                    <option value="" data-store_name="">Select Location</option>
                                    <?php
                                    foreach ($locationData as $lData) :
                                        echo '<optgroup label="' . $lData['store_name'] . '">';
                                        foreach ($lData['location'] as $lopt) :
                                            echo '<option value="' . $lopt->id . '" data-store_name="' . $lData['store_name'] . '" data-row_id="' . $row->id . '" data-item_id="' . $row->ref_item_id . '">' . $lopt->location . ' </option>';
                                        endforeach;
                                        echo '</optgroup>';
                                    endforeach;
                                    ?>
                                </select>

                            </td>
                            <td>

                                <select id="batch_no<?=$row->id?>" name="batch_no[]" onclick="getBatchWiseStock(<?=$row->id?>)" class=" form-control  ">
                                    <option value="">Select Batch No.</option>
                                </select>

                            </td>
                            <td>
                                <input type="text" id="batch_stock<?=$row->id?>" class="form-control" value="" readonly />
                            </td>
                            <td>
                                <input type="number" name="request_qty[]" id="request_qty" class="form-control floatOnly" value="<?= (!empty($row->request_qty)) ? $row->request_qty : 0 ?>">
                                <div class="error request_qty<?= $i ?>"></div>
                            </td>
                        </tr>
                    <?php
                        $i++;
                    endforeach;
                else :
                    ?>
                    <tr>
                        <td colspan="5" class="text-center">No Data Found.</td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>

        <input type="hidden" id="mismatch_data" value="<?= $mismatch ?>">
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });

        $(document).on("change", ".location", function() {
            var itemId =$(this).find(":selected").data('item_id');;
            var row_id = $(this).find(":selected").data('row_id');;
            var location_id = $(this).val();
            $("#batch_stock"+row_id).val("");
            console.log(itemId);
            console.log(location_id);
            if (itemId == "" || location_id == "") {
                if (itemId == "") {
                    $(".bom_item_id").html("Issue Item name is required.");
                }
                if (location_id == "") {
                    // $(".location_id").html("Location is required.");
                }
            } else {
                $.ajax({
                    url: base_url + controller + '/getBatchNo',
                    type: 'post',
                    data: {
                        item_id: itemId,
                        location_id: location_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#batch_no"+row_id).html("");
                        $("#batch_no"+row_id).html(data.options);
                        //$("#batch_no").comboSelect();
                        // $("#batch_no"+row_id).select2();
                    }
                });
            }
        });


    });

    function getBatchWiseStock(id)
    {
        $("this :selected").data('stock')
            $("#batch_stock"+id).val("");
            $("#batch_stock"+id).val($("#batch_no"+id+" :selected").data('stock'));
    }
</script>