<form id="rmProcess">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="ref_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="trans_ref_id" value="<?= (!empty($dataRow->item_id)) ? $dataRow->item_id : ""; ?>" />
            <input type="hidden" name="ref_batch" value="<?= (!empty($dataRow->ref_batch)) ? $dataRow->ref_batch : ""; ?>">
            <input type="hidden" name="ref_no" value="<?= (!empty($dataRow->ref_no)) ? $dataRow->ref_no : ""; ?>" />
            <div class="col-md-4 form-group">
                <label for="ref_date">Date</label>
                <input type="date" id="ref_date" name="ref_date" value="<?= date("Y-m-d") ?>" class="form-control">
            </div>
            <div class="col-md-4 form-group">
                <label for="return_item_id">Item Name</label>
                <select name="return_item_id" id="return_item_id" class="form-control single-select req">
                    <option value="">Select Item Name</option>
                    <?php
                    foreach ($itemList as $row) :
                        //$selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control " value="">
            </div>
            <div class="col-md-6 form-group">
                <label for="location_id">Store Location</label>
                <select id="location_id" name="location_id" class="form-control single-select1 model-select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            //$selected = (!empty($dataRow->location_id) && $dataRow->location_id == $row->id) ? "selected" : '';
                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" >' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form mt-30" onclick="storeReturnRm('rmProcess','saveReturnRm');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<div class=" col-md-12">
    <div class="table-responsive">
        <table id="disctbl" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Item Name</th>
                    <th>Qty</th>
                    <th>Location</th>
                    <th>Batch No.</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="discBody">
                <?php
                if (!empty($calData)) :
                    $i = 1;
                    foreach ($calData as $row) :
                        $deleteParam = $row->id . ",'RmProcess'";
                        echo '<tr>
                                <td>' . $i . '</td>
                                <td>' . $row->item_name . '</td>
                                <td>' . $row->qty . '</td>
                                <td>' . $row->location . '</td>
                                <td>' . $row->batch_no . '</td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashReturnRm(' . $row->id . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a>
                                </td>
                            </tr>'; $i++;
                    endforeach;
                else :
                    echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });
    });
    function storeReturnRm(formId, fnsave, srposition = 1) {
        setPlaceHolder();
        if (fnsave == "" || fnsave == null) {
            fnsave = "save";
        }
        var form = $('#' + formId)[0];
        var fd = new FormData(form);
        $.ajax({
            url: base_url + controller + '/' + fnsave,
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                $("#discBody").html(data.tbodyData);
                // $("#item_id").val(data.partyId);
                $("#item_id").val("");
                $("#qty").val("");
                $("#location_id").val("");
                $("#batch_no").val("");
            } else {
                initTable(srposition); //$('#'+formId)[0].reset(); //$(".modal").modal('hide');
                toastr.error(data.message, 'Error', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            }
        });
    }
    function trashReturnRm(id, name = 'Record') {
        var send_data = {
            id: id
        };
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this ' + name + '?',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function() {
                        $.ajax({
                            url: base_url + controller + '/deleteReturnRm',
                            data: send_data,
                            type: "POST",
                            dataType: "json",
                            success: function(data) {
                                if (data.status == 0) {
                                    toastr.error(data.message, 'Sorry...!', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                } else {
                                    $("#discBody").html(data.tbodyData);
                                    // $("#item_id").val(data.partyId);
                                    $("#item_id").val("");
                                    $("#qty").val("");
                                    $("#location_id").val("");
                                    $("#batch_no").val("");
                                    toastr.success(data.message, 'Success', {
                                        "showMethod": "slideDown",
                                        "hideMethod": "slideUp",
                                        "closeButton": true,
                                        positionClass: 'toastr toast-bottom-center',
                                        containerId: 'toast-bottom-center',
                                        "progressBar": true
                                    });
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function() {
                    }
                }
            }
        });
    }
</script>