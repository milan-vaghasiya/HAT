<div class="col-md-12">
    <!-- Created By Mansee @ 27-12-2021 -->
    <form>
        <div class="row">
            <input type="hidden" name="party_id" id="party_id" value="<?= (!empty($dataRow->party_id)) ? $dataRow->party_id : $party_id; ?>" />
            <div class="col-md-4 form-group">
                <label for="gstin">GST</label>
                <input type="text" name="gstin" id="gstin" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="party_address">Party Address</label>
                <input type="text" name="party_address" id="party_address" class="form-control  req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="dparty_incode">Party Pincode</label>
                <input type="text" name="party_pincode" id="party_pincode" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="delivery_address">Delivery Address</label>
                <input type="text" name="delivery_address" id="delivery_address" class="form-control  req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="delivery_pincode">Delivery Pincode</label>
                <input type="text" name="delivery_pincode" id="delivery_pincode" class="form-control req" value="" />
            </div>
            <div class="col-md-8 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right mt-4" onclick="storeGst('gstDetail','saveGst');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </form>
    <!-- <div class="row">
        <div class="col-md-12 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form float-right" onclick="storeGst('gstDetail','saveGst');"><i class="fa fa-check"></i> Save</button>
        </div>
    </div> -->
    <div class="row">
        <div class="table-responsive">
            <table id="disctbl" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>GST</th>
                        <th>Party Address</th>
                        <th>Party Pincode</th>
                        <th>Delivery Address</th>
                        <th>Delivery Pincode</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="discBody">
                    <?php
                    if (!empty($json_data)) :
                        $i = 1;


                        foreach ($json_data as $key => $row) :
                            echo '<tr>
                                        <td>' .  $i++ . '</td>
                                        <td>' . $key . '</td>
                                        <td>' . $row->party_address . '</td>
                                        <td>' . $row->party_pincode . '</td>
                                        <td>' . $row->delivery_address . '</td>
                                        <td>' . $row->delivery_pincode . '</td>
                                        <td class="text-center">
                                        <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\');"><i class="ti-trash"></i></a> 
                                        </td>
                                    </tr> ';
                        endforeach;
                    else :
                        echo '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function storeGst(formId, fnsave, srposition = 1) {
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
                $("#party_id").val(data.partyId);
                $("#gstin").val("");
                $("#delivery_address").val("");
                $("#delivery_pincode").val("");
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

    function trashGst(gstin) {
        var partyId = $("#party_id").val();
        var send_data = {
            id: partyId,
            gstin: gstin
        };
        console.log(send_data);
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
                            url: base_url + controller + '/deleteGst',
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
                                    $("#party_id").val(data.partyId);
                                    $("#gstin").val("");
                                    $("#delivery_address").val("");
                                    $("#delivery_pincode").val("");
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