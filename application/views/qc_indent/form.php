<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : "" ?>" />
            <input type="hidden" name="log_no" id="log_no" value="<?= (!empty($dataRow->log_no)) ? $dataRow->log_no : $indentNo ?>" />
            <input type="hidden" name="log_type" id="log_type" value="<?= (!empty($dataRow->log_type)) ? $dataRow->log_type : 3 ?>" />
            <input type="hidden" name="auth_detail" id="auth_detail" value="<?= (!empty($dataRow->auth_detail)) ? $dataRow->auth_detail : "" ?>" />
            <input type="hidden" name="req_date" id="req_date" value="<?= (!empty($dataRow->req_date)) ? $dataRow->req_date : date("Y-m-d H:i:s") ?>" />
            <input type="hidden" name="approve_type" id="approve_type" value="<?= (!empty($approve_type)) ? $approve_type : '' ?>" />
            <input type="hidden" id="inm" value="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : '' ?>" />
            <input type="hidden" id="iid" value="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : '' ?>" />
            <input type="hidden" id="req_type" name="reqn_type" value="<?=(!empty($dataRow->reqn_type))?$dataRow->reqn_type:1?>">
            <input type="hidden" id="req_from" name="req_from" value="<?=(!empty($dataRow->req_from))?$dataRow->req_from:0?>">
            
            <div class="col-md-3 form-group">
                <label for="item_type">Item Group</label>
                <select id="item_type" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                    foreach ($itemTypeList as $row) :
                        $selected = (!empty($dataRow->item_type) && $dataRow->item_type == $row->id) ? 'selected' : '';
                        $disabled = (!empty($dataRow->item_type) && $dataRow->item_type != $row->id) ? 'disabled' : '';
                        echo '<option value="' . $row->id . '" ' . $selected . ' '.$disabled.'>' . $row->category_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select id="category_id" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                    foreach ($categoryList as $row) :
                        $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? 'selected' : '';
                        $disabled = (!empty($dataRow->category_id) && $dataRow->category_id != $row->id) ? 'disabled' : '';
                        echo '<option value="' . $row->id . '" ' . $selected . ' '.$disabled.'>' . $row->category_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="family_id">Family Group</label>
                <select id="family_id" class="form-control single-select">
                    <option value="">Select ALL</option>
                    <?php
                    foreach ($familyGroup as $row) :
                        $selected = (!empty($dataRow->family_id) && $dataRow->family_id == $row->id) ? 'selected' : '';
                        $disabled = (!empty($dataRow->family_id) && $dataRow->family_id != $row->id) ? 'disabled' : '';
                        echo '<option value="' . $row->id . '" ' . $selected . ' '.$disabled.'>' . $row->family_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group req">
                <label for="req_item_id">Full Name</label>
                <select name="req_item_id" id="req_item_id" class="form-control large-select2 req" data-item_type="" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="items/getDynamicItemList">
                    <option value="">Select Item</option>
                    <?php
                    // if (!empty($itemData)) {
                    //     foreach ($itemData as $row) {
                    //         $selected = (!empty($dataRow->req_item_id) && $dataRow->req_item_id == $row->id) ? 'selected' : '';
                    //         $disabled = (!empty($dataRow->req_item_id) && $dataRow->req_item_id != $row->id) ? 'disabled' : '';
                    // ?>
                    <!--          <option value="<?= $row->id ?>" data-row='<?= json_encode($row) ?>' <?= $selected ?>><?= $row->item_name ?></option> -->
                    // <?php
                    //     }
                    // }
                    ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="unit_name">Unit</label>
                <input type="text" id="unit_name" class="form-control" value="<?= (!empty($dataRow->unit_name)) ? $dataRow->unit_name : "" ?>" readonly>
                <input type="hidden" id="unit_id" value="<?= (!empty($dataRow->unit_id)) ? $dataRow->unit_id : "" ?>" />
            </div>
            
            <div class="col-md-3 form-group req">
                <label for="min_qty">Min Qty</label>
                <input type="text" id="min_qty" class="form-control" readonly value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>">
            </div>
            <div class="col-md-3 form-group req">
                <label for="max_qty">Max Qty</label>
                <input type="text" id="max_qty" class="form-control" readonly value="<?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?>">
            </div>
            <div class="col-md-3 form-group req">
                <label for="lead_time">Lead Time (In Days)</label>
                <input type="text" id="lead_time" class="form-control" readonly value="<?= (!empty($dataRow->lead_time)) ? $dataRow->lead_time : "" ?>">
            </div>
            
            <div class="col-md-2">
                <label for="urgency">Urgency</label>
                <select name="urgency" id="urgency" class="form-control single-select ">
                    <option value="0" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 0) ? 'selected' : ''; ?>>Low</option>
                    <option value="1" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 1) ? 'selected' : ''; ?>>Medium</option>
                    <option value="2" <?= (!empty($dataRow->urgency) && $dataRow->urgency == 2) ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            
            <div class="col-md-2 form-group req">
                <label for="req_type">Required Type</label>
                <select name="req_type" id="req_type" class="form-control single-select ">
                    <option value="1" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 1) ? 'selected' : ''; ?>>Fresh</option>
                    <option value="2" <?= (!empty($dataRow->req_type) && $dataRow->req_type == 2) ? 'selected' : ''; ?>>Used</option>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="planning_type">Planning Type</label>
                <select name="planning_type" id="planning_type" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                    foreach ($planningType as $row) :
                        $selected = (!empty($dataRow->planning_type) && $dataRow->planning_type == $row->id) ? 'selected' : '';
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->planning_type . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <!--
            <div class="col-md-3">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select ">
                    <option value="">Select Machine</option>
                    <?php
                    /*foreach ($fgNMcData as $row) :
                        if ($row->item_type == 5) {
                            $selected = (!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . " data-item_type='" . $row->item_type . "' data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
                        }
                    endforeach;*/
                    ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="fg_item_id">Where to Use</label>
                <select name="fg_item_id" id="fg_item_id" class="form-control single-select ">
                    <option value="">Select Finished Item</option>
                    <?php
                    /*foreach ($fgNMcData as $row) :
                        if ($row->item_type == 1) {
                            $selected = (!empty($dataRow->fg_item_id) && $dataRow->fg_item_id == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . " data-item_type='" . $row->item_type . "' data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . " </option>";
                        }
                    endforeach;*/
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="used_at">Used at</label>
                <select name="used_at" id="used_at" class="form-control single-select">
                    <option value="0" <?= (!empty($dataRow->used_at) && $dataRow->used_at == 0) ? 'selected' : '' ?>>In House</option>
                    <option value="1" <?= (!empty($dataRow->used_at) && $dataRow->used_at == 1) ? 'selected' : '' ?>>Vendor</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="handover_to">Whom to Handover</label>
                <select name="handover_to" id="handover_to" class="form-control single-select">
                    <option value="">Select</option>
                    <?php
                    /*if (!empty($dataRow->used_at) && $dataRow->used_at == 1) :
                        foreach ($partyData as $row) :
                            $selected = (!empty($dataRow->handover_to) && $dataRow->handover_to == $row->id) ? 'selected' : '';
                            echo "<option value='" . $row->id . "' " . $selected . "  data-row='" . json_encode($row) . "'>[" . $row->party_code . "] " . $row->party_name . " </option>";
                        endforeach;
                    else :
                        foreach ($empData as $row) :
                            $selected = (!empty($dataRow->handover_to) && $dataRow->handover_to == $row->id || !empty($loginId) && $loginId == $row->id) ? 'selected' : '';
                            // $selected = (empty($dataRow->handover_to) && !empty($loginId) && $loginId == $row->id) ? 'selected' : '';
                            ?>
                            <option value="<?=$row->id?>" <?=$selected?> data-row='<?=json_encode($row)?>'><?="[" . $row->emp_code . "] " . $row->emp_name ?></option>
                            <?php
                            // echo "<option value='" . $row->id . "' " . $selected . "  data-row='".  . "'>[" . $row->emp_code . "] " . $row->emp_name . " </option>";
                        endforeach;
                    endif;*/
                    ?>
                </select>
            </div>
            -->
            
            
            <div class="col-md-2 form-group">
                <label for="req_qty">Indent Qty.</label>
                <input type="number" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?= (!empty($dataRow->req_qty)) ? (($dataRow->req_qty != "0.000") ? $dataRow->req_qty : $dataRow->req_qty) : "" ?>">
                <input type="hidden" id="schedule_qty" name="schedule_qty" value="<?= !empty($dataRow->schedule_qty) ? $dataRow->schedule_qty : '' ?>">
                <input type="hidden" id="schedule_date" name="schedule_date" value="<?= !empty($dataRow->schedule_date) ? $dataRow->schedule_date : '' ?>">

            </div>
            <div class="col-md-2 req form-group">
                <label for="delivery_date">Required Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control req" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : date("Y-m-d") ?>">
            </div>
            <div class="col-md-1 req form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-block btn-info addSchedule">Add</button>
            </div>
            <!--
            <div class="col-md-2 form-group">
                <label for="is_returnable">Returnable</label>
                <select name="is_returnable" id="is_returnable" class="form-control">
                    <option value="0" <?= (!empty($dataRow->is_returnable) && $dataRow->is_returnable == 0) ? "selected" : "" ?>>No</option>
                    <option value="1" <?= (!empty($dataRow->is_returnable) && $dataRow->is_returnable == 1) ? "selected" : "" ?>>Yes</option>
                </select>
            </div>
            -->
            <div class="col-md-12">
                <div id="scheduleIndentDetail" class="row">
                    <?php
                    if (!empty($dataRow->schedule_qty)) {
                        $qtyArray = explode(",", $dataRow->schedule_qty);
                        $dateArray = explode(",", $dataRow->schedule_date);
                        for ($i = 0; $i < count($qtyArray); $i++) {
                    ?>
                            <div class="col-md-3" id="tbl<?= $i ?>">
                                <table class="table table-bordered ">
                                    <tr class="text-center">
                                        <td><?= $qtyArray[$i] ?></td>
                                        <td><?= $dateArray[$i] ?></td>
                                        <td><a href="javascript:void(0)" onclick="removeSchedule(<?= $i ?>)"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                </table>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>

            </div>
            <div class="col-md-12" id="authDetail">
                <?php
                if (!empty($stockData)) {
                    echo $stockData['authDetail'];
                }

                ?>
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
            <div class="col-md-2 form-group req">
                <label for="item_image">&nbsp;</label>
                <a href="javascript:void(0)" id="item_image" class="btn btn-info btn-block" target="_blank">Item Image</a>
                <!--<img src="" id="item_image" class="img-responsive" style="  width: 100%; height: 100%x;object-fit: cover;">-->
            </div>
        </div>


        <hr style="width:100%;">
        <div class="row">
            <div class="table-responsive">
                <table id="stockTbl" class="table jp-table align-items-center text-center">
                    <thead class="lightbg">
                        <tr>
                            <th>Current Stock</th>
                            <th>WIP Stock</th>
                            <th>Allocated Stock</th>
                            <th>Pending Purchase<br>Order Stock</th>
                            <th>Pending Requisition</th>
                            <th>Pending Indent Stock</th>
                            <th>Pending Indent for<br>Approval Stock</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="stockBody">
                        <?php
                        if (!empty($dataRow->id)) {
                        ?>
                            <tr>
                                <td id="current_stock"><?= $dataRow->current_stock . $dataRow->unit_name ?> <input type="hidden" name="current_stock" id="current_stock" value="<?= (!empty($dataRow->current_stock) ? $dataRow->current_stock : 0) ?>"></td>
                                <td id="wip_stock"><input type="hidden" name="wip_stock" value="<?= $dataRow->wip_stock ?>"><?= $dataRow->wip_stock . $dataRow->unit_name ?></td>
                                <td id="job_allocated_stock"><input type="hidden" name="job_allocated_stock" value="<?= $dataRow->job_allocated_stock ?>"><?= $dataRow->job_allocated_stock . $dataRow->unit_name ?></td>
                                <td id="pending_po_stock"><input type="hidden" name="pending_po_stock" value="<?= $dataRow->pending_po_stock ?>"><?= $dataRow->pending_po_stock . $dataRow->unit_name ?></td>
                                <td id="pending_req_stock"><input type="hidden" name="pending_req_stock" value="<?= $dataRow->pending_req_stock ?>"><?= $dataRow->pending_req_stock . $dataRow->unit_name ?></td>
                                <td id="pending_indent_stock"><input type="hidden" name="pending_indent_stock" value="<?= $dataRow->pending_indent_stock ?>"><?= $dataRow->pending_indent_stock . $dataRow->unit_name ?></td>
                                <td id="pending_indent_apr_stk"><input type="hidden" name="pending_indent_apr_stk" value="<?= $dataRow->pending_indent_apr_stk ?>"><?= $dataRow->pending_indent_apr_stk . $dataRow->unit_name ?></td>
                                <td><?= ($dataRow->current_stock + $dataRow->wip_stock + $dataRow->pending_po_stock + $dataRow->pending_req_stock + $dataRow->pending_indent_stock + $dataRow->pending_indent_apr_stk + $dataRow->job_allocated_stock) . $dataRow->unit_name ?></td>
                            </tr>
                        <?php
                        } else if(!empty($stockData['html'])) {
                            echo $stockData['html'];
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <hr style="width:100%;">

    </div>
</form>
<script>
    $(document).ready(function() {
        $('.model-select2').select2({
            dropdownParent: $('.model-select2').parent()
        });

        // setTimeout(function(){ 
        //   $("#req_item_id").trigger('change');
        // }, 1500);
        $(document).on('shown.bs.modal', function () 
        {
            let dataSet = {};
            var iid = $('#iid').val();//alert(iid);
            setTimeout(function(){
                if(iid)
                {
                    <?php if (!empty($dataRow)) { ?>
                        var jsonRow = '<?php echo htmlspecialchars(json_encode($dataRow), ENT_QUOTES, 'UTF-8'); ?>';
                        dataSet = {id: iid, text: $('#inm').val(),row: jsonRow}; 
                    <?php } else { ?> dataSet = {};  <?php } ?>
                }
                getDynamicItemList(dataSet);
            },600);
        });
        $(document).on('change', '#req_item_id', function() {
            
            var item_id = $(this).val();
            // var itemData = $(this).find(":selected").data('row');
            <?php if (!empty($dataRow->req_item_id)) { ?>
                    var itemData = $(this).find(":selected").data('row');
                    if (!itemData) {
                        itemData = JSON.parse($(this).select2('data')[0]['row']);
                    }
                <?php } else { ?>
                    var itemData = JSON.parse($(this).select2('data')[0]['row']);
                <?php } ?>
            $("#auth_detail").val("");
            $("#auth_detail").val(itemData.auth_detail);

            if (!itemData.item_image) {
                itemData.item_image = 'no-photo.png';
            }
            $("#item_image").attr("href", base_url + '/assets/uploads/items/' + itemData.item_image);

            $.ajax({
                    type: "POST",
                    url: base_url + '/sendPR/getItemStockData',
                    data: {
                        item_id: item_id
                    },
                    dataType: 'json',
                }).done(function(response) {
                    $("#stockBody").html("");
                    $("#stockBody").html(response.html);
                    $("#authDetail").html(response.authDetail);
                });

        });

        $(document).on('change', '#item_type', function() {
            $("#req_item_id").attr('data-item_type', $(this).val());

            $.ajax({
                type: "POST",
                url: base_url + controller + '/getCategoryData',
                data: {
                    item_type: $(this).val()
                },
                dataType: 'json',
            }).done(function(response) {
                $("#category_id").html("");
                $("#category_id").html(response.options);
                $("#category_id").comboSelect();
            });
        });
        $(document).on('change', '#category_id', function() {
            $("#req_item_id").attr('data-category_id', $(this).val());
        });
        $(document).on('change', '#family_id', function() {
            $("#req_item_id").attr('data-family_id', $(this).val());
        });

        $(document).on('change', '#used_at', function() {
            var used_at = $(this).val();
            $.ajax({
                type: "POST",
                url: base_url + controller + '/getHandoverData',
                data: {
                    used_at: used_at
                },
                dataType: 'json',
            }).done(function(response) {
                $("#handover_to").html(response.handover);
                $("#handover_to").comboSelect();
            });
        });


        $(document).on('click', '.addSchedule', function() {
            var schedule_qty = $("#req_qty").val();
            var schedule_date = $("#delivery_date").val();

            var is_valid = 1;
            $(".error").html("");
            if (schedule_qty == '' || schedule_qty == 0) {
                $(".req_qty").html("Qty is Required");
                is_valid = 0;
            }
            if (schedule_date == '' || schedule_date == 0) {
                $(".delivery_date").html("Date is Required");
                is_valid = 0;
            }

            if (is_valid) {
                var qty = $("#schedule_qty").val();
                var sDate = $("#schedule_date").val();

                qtyComa = '';
                var i = 0;
                if (qty != '') {
                    qtyComa = ',';
                    var qtyArray = qty.split(',');
                    i = qtyArray.length;
                }
                console.log(i);
                qty += qtyComa + schedule_qty;
                sDate += qtyComa + schedule_date;

                $("#schedule_qty").val(qty);
                $("#schedule_date").val(sDate);

                var htmlDetail = '<div class="col-md-3" id="tbl' + i + '"><table class="table table-bordered "><tr class="text-center"><td>' + schedule_qty + '</td><td>' + schedule_date + '</td><td><a href="javascript:void(0)" onclick="removeSchedule(' + i + ')"><i class="fa fa-trash"></i></a></td></tr></table></div>';
                $("#scheduleIndentDetail").append(htmlDetail);


                $("#req_qty").val("");
                $("#delivery_date").val("");
            }
        });
    });

    function AddRow() {
        $(".error").html("");
        var isValid = 1;
        if ($("#req_item_id").val() == "") {
            $(".req_item_id").html("Item Name is required.");
            isValid = 0;
        }
        if ($("#req_qty").val() == "") {
            $(".req_qty").html("Request Qty. is required.");
            isValid = 0;
        }

        if (isValid) {

            //Get the reference of the Table's TBODY element.
            $("#requesttbl").dataTable().fnDestroy();
            var tblName = "requesttbl";
            var tBody = $("#" + tblName + " > TBODY")[0];

            //Add Row.
            row = tBody.insertRow(-1);

            //Add index cell
            var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
            var cell = $(row.insertCell(-1));
            cell.html(countRow);

            cell = $(row.insertCell(-1));
            cell.html($("#req_item_idc").val() + '<input type="hidden" name="req_item_id[]" value="' + $("#req_item_id").val() + '"><input type="hidden" name="req_item_name[]" value="' + $("#req_item_idc").val() + '">');

            cell = $(row.insertCell(-1));
            cell.html($("#req_qty").val() + '<input type="hidden" name="req_qty[]" value="' + $("#req_qty").val() + '">');
            cell.append('<input type="hidden" name="planning_type[]" value="' + $("#planning_type").val() + '">');
            cell.append('<input type="hidden" name="delivery_date[]" value="' + $("#delivery_date").val() + '">');
            cell.append('<input type="hidden" name="description[]" value="' + $("#description").val() + '">');
            cell.append('<input type="hidden" name="item_dtl_description[]" value="' + $("#item_dtl_description").val() + '">');
            cell.append('<input type="hidden" name="current_stock[]" value="' + $("#current_stock").html() + '">');
            cell.append('<input type="hidden" name="wip_stock[]" value="' + $("#wip_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_po_stock[]" value="' + $("#pending_po_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_indent_stock[]" value="' + $("#pending_indent_stock").html() + '">');
            cell.append('<input type="hidden" name="pending_indent_apr_stk[]" value="' + $("#pending_indent_apr_stk").html() + '">');
            cell.append('<input type="hidden" name="remark[]" value="' + $("#remark").val() + '">');
            //Add Button cell.
            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="ti-trash"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light btn-sm");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            $("#req_item_id").val('');
            $("#req_item_idc").val('');
            $("#req_qty").val('');
        }
    };

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        $("#requesttbl").dataTable().fnDestroy();
        var row = $(button).closest("TR");
        var table = $("#requesttbl")[0];
        table.deleteRow(row[0].rowIndex);
        $('#requesttbl tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
    };

    function removeSchedule(index) {
        $("#tbl" + index).remove();
        var qtyArray = $("#schedule_qty").val().split(",");
        var dateArray = $("#schedule_date").val().split(",");
        qtyArray.splice(index, 1);
        dateArray.splice(index, 1);
        qtyArray.toString();
        $("#schedule_qty").val(qtyArray);
        $("#scheduleIndentDetail").html("");
        for (var i = 0; i < qtyArray.length; i++) {
            var htmlDetail = '<div class="col-md-3" id="tbl' + i + '"><table class="table table-bordered "><tr class="text-center"><td>' + qtyArray[i] + '</td><td>' + dateArray[i] + '</td><td><a href="javascript:void(0)" onclick="removeSchedule(' + i + ')"><i class="fa fa-trash"></i></a></td></tr></table></div>';
            $("#scheduleIndentDetail").append(htmlDetail);
        }
        dateArray.toString();
        $("#schedule_date").val(dateArray);

    }
</script>