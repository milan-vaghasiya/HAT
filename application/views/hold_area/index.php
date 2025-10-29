<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('holdAreaTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('holdAreaTable',2);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Holding Area</h4>
                            </div>


                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='holdAreaTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {


        $(document).on('change', "#vendor_id", function() {
            var vendor_id = $(this).val();
            var product_id = $("#product_id").val();
            var process_id = $("#out_process_id").val();
            var job_card_id = $("#outWard #job_card_id").val();
            if (vendor_id == "0") {
                $("#job_order_id").html('<option value="">Select Job Order No.</option>');
                $("#job_order_id").comboSelect();
                //$("#jobProcessSelect").html("");
                $("#job_process_ids").val("");
                $("#job_process_ids").comboSelect();
                //reInitMultiSelect();
            } else {
                $.ajax({
                    url: base_url + 'production_v2/processApproval/getJobWorkOrderNoList',
                    type: 'post',
                    data: {
                        vendor_id: vendor_id,
                        product_id: product_id,
                        process_id: process_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#job_order_id").html("");
                        $("#job_order_id").html(data.options);
                        $("#job_order_id").comboSelect();
                    }
                });

                $.ajax({
                    url: base_url + 'production_v2/processApproval/getJobWorkOrderProcessList',
                    type: 'post',
                    data: {
                        job_order_id: "",
                        process_id: process_id,
                        job_card_id: job_card_id,
                        vendor_id: vendor_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        /* $("#job_process_ids").val(data.job_process);
                        $("#jobProcessSelect").html("");
                        $("#jobProcessSelect").html(data.options);
                        reInitMultiSelect(); */

                        $("#job_process_ids").html("");
                        $("#job_process_ids").html(data.options);
                        $("#job_process_ids").comboSelect();
                    }
                });
            }
        });

        $(document).on('change', '#job_order_id', function() {
            var job_order_id = $(this).val();
            var vendor_id = $("#vendor_id").val();
            var process_id = $("#out_process_id").val();
            var job_card_id = $("#outWard #job_card_id").val();

            if (vendor_id == "0") {
                $("#job_order_id").html('<option value="">Select Job Order No.</option>');
                $("#job_order_id").comboSelect();
                //$("#jobProcessSelect").html("");
                $("#job_process_ids").val("");
                $("#job_process_ids").comboSelect();
                //reInitMultiSelect();
            } else {
                $.ajax({
                    url: base_url + 'production_v2/processApproval/getJobWorkOrderProcessList',
                    type: 'post',
                    data: {
                        job_order_id: job_order_id,
                        process_id: process_id,
                        job_card_id: job_card_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        /* $("#job_process_ids").val(data.job_process);
                        $("#jobProcessSelect").html("");
                        $("#jobProcessSelect").html(data.options);
                        reInitMultiSelect(); */

                        $("#job_process_ids").html("");
                        $("#job_process_ids").html(data.options);
                        $("#job_process_ids").comboSelect();
                    }
                });
            }
        });

        $(document).on('click', '.btn-close', function() {
            initTable();
        });

        $(document).on('click', '.close', function() {
            initTable();
        });

        /** Created By Mansee @ 23-03-2022 */
        $(document).on("keyup", ".qtyCal", function() {
            var rejSum = 0;
            $(".rej_sum").each(function() {
                rejSum += parseFloat($(this).val()) || 0;
            });


            var okQty = parseFloat($("#production_qty").val()) - rejSum;

            $("#out_qty").val(okQty);
        });

        /** Created By Mansee @ 23-03-2022 */
        $(document).on('click', "#addRejectionRow", function() {
            var rej_qty = $("#rej_qty").val();
            var rej_reason = $("#rej_reason :selected").val();
            var rej_from = $("#rej_from :selected").val();
            var rej_reason_code = $("#rej_reason :selected").data('code');
            var rejection_reason = $("#rej_reason :selected").data('reason');
            var rej_party_name = $("#rej_from :selected").data('party_name');
            var rej_remark = $("#rej_remark").val();
            var rej_stage = $("#rejection_stage").val();
            var rej_stage_name = $("#rejection_stage :selected").data('process_name');

            var valid = 1;

            $(".rej_qty").html("");
            if (parseFloat(rej_qty) <= 0) {
                $(".rej_qty").html("Rejection Qty is required.");
                valid = 0;
            }

            $(".rej_reason").html("");
            if (rej_reason == "") {
                $(".rej_reason").html("Rejection Reason is required.");
                valid = 0;
            }

            $(".rejection_stage").html("");
            if (rej_stage == "") {
                $(".rejection_stage").html("Rejection Belongs To is required.");
                valid = 0;
            }

            if (valid == 1) {
                var postData = {
                    rej_qty: rej_qty,
                    rej_reason: rej_reason,
                    rej_from: rej_from,
                    rej_reason_code: rej_reason_code,
                    rejection_reason: rejection_reason,
                    rej_remark: rej_remark,
                    rej_party_name: rej_party_name,
                    rej_stage: rej_stage,
                    rej_stage_name: rej_stage_name
                };
                AddRowRejection(postData);
                $("#rej_qty").val("0");
                $("#rej_reason").comboSelect();
                $("#rej_from").comboSelect();
                $("#rejection_stage").comboSelect();
                $("#rej_remark").val("");
                $("#rej_qty").focus();

            }
        });
        /** Created By Mansee @ 23-03-2022 */
        $(document).on("change", "#rejection_stage", function() {
            var process_id = $(this).val();
            var part_id = $("#product_id").val();
            if (process_id) {
                var job_card_id = $("#job_card_id").val();
                $.ajax({
                    url: base_url + 'logSheet/getJobWorkOrder',
                    type: 'post',
                    data: {
                        process_id: process_id,
                        part_id: part_id,
                        job_card_id: job_card_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#rej_from").html("");
                        $("#rej_from").html(data.rejOption);
                        $("#rej_from").comboSelect();


                    }
                });
            } else {
                $("#machine_id").html("<option value=''>Select Machine</option>");
                $("#machine_id").comboSelect();
                $("#rej_from").html("<option value=''>Select Rej. From</option>");
                $("#rej_from").comboSelect();
                $("#rw_from").html("<option value=''>Select Rew. From</option>");
                $("#rw_from").comboSelect();
            }
        });
    });

    function outward(data) {
        var button = data.button;
        console.log(data);
        $.ajax({
            type: "POST",
            url: base_url + 'production_v2/processApproval/processApproved',
            data: {
                id: data.id,
                from_entry_type: data.entry_type,
                trans_ref_id: data.trans_ref_id,
                pending_qty: data.pending_qty
            }
        }).done(function(response) {
            $("#" + data.modal_id).modal();
            $("#" + data.modal_id + ' .modal-title').html(data.title);
            $("#" + data.modal_id + ' .modal-body').html(response);
            $("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
            $("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "');");
            if (data.button == "close") {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").hide();
            } else if (data.button == "save") {
                $("#" + data.modal_id + " .modal-footer .btn-close").hide();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            } else {
                $("#" + data.modal_id + " .modal-footer .btn-close").show();
                $("#" + data.modal_id + " .modal-footer .btn-save").show();
            }
            $(".single-select").comboSelect();
            setPlaceHolder();
            initMultiSelect();
        });
    }

    function saveOutward(formId) {
        var fd = $('#' + formId).serialize();
        $.ajax({
            url: base_url + 'production_v2/processApproval/save',
            data: fd,
            type: "POST",
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".error").html("");
                $.each(data.message, function(key, value) {
                    $("." + key).html(value);
                });
            } else if (data.status == 1) {
                $("#pqty").val(data.pending_qty);
                $("#pending_qty").html(data.pending_qty);

                $(".batchMaterial").html("");
                $(".reqMaterial").html("");
                $("#out_qty").val("");
                $("#in_qty_kg").val("");
                $("#setup_status").val(1);
                $("#setup_status").comboSelect();
                $("#setter_id").val("");
                $("#setter_id").comboSelect();
                $("#machine_id").val("");
                $("#machine_id").comboSelect();
                $("#material_used_id").val(""); //$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
                $("#vendor_id").val("0");
                $("#vendor_id").comboSelect();
                $("#job_order_id").val("");
                $("#job_order_id").comboSelect();
                //$("#job_process_ids").val("");$("#jobProcessSelect").html("");reInitMultiSelect();
                $("#job_process_ids").html("");
                $("#job_process_ids").comboSelect();
                $("#remark").val("");

                $("#material_used_id").html("");
                $("#material_used_id").html(data.materialBatch);
                //$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
                $("#batch_no").val("");
                $("#issue_qty").val("0");
                $("#used_qty").val("0");
                $("#req_qty").val("0");
                $("#wp_qty").val("0");

                $("#outwardTransData").html(data.outwardTrans);
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
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
    /** Created By Mansee @ 23-03-2022 */
    function AddRowRejection(data) {
        $('table#rejectionReason tr#noData').remove();

        //Get the reference of the Table's TBODY element.
        var tblName = "rejectionReason";

        var tBody = $("#" + tblName + " > TBODY")[0];
        row = tBody.insertRow(-1);

        var index = $('#' + tblName + ' tbody tr:last').index();
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        cell.attr("style", "width:5%;");

        var rejection_qty_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_qty]",
            value: data.rej_qty,
            class: "rej_sum"
        });
        cell = $(row.insertCell(-1));
        cell.html(data.rej_qty);
        cell.append(rejection_qty_input);
        cell.attr("style", "width:20%;");

        var rej_reason_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_reason]",
            value: data.rej_reason
        });
        cell = $(row.insertCell(-1));
        cell.html(data.rejection_reason);
        cell.append(rej_reason_input);
        cell.attr("style", "width:20%;");

        var rej_stage_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_stage]",
            value: data.rej_stage
        });
        var rej_stage_name_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_stage_name]",
            value: data.rej_stage_name
        });
        cell = $(row.insertCell(-1));
        cell.html(data.rej_stage_name);
        cell.append(rej_stage_input);
        cell.append(rej_stage_name_input);
        cell.attr("style", "width:20%;");

        var rej_from_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_from]",
            value: data.rej_from
        });
        var rej_party_name_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_party_name]",
            value: data.rej_party_name
        });
        cell = $(row.insertCell(-1));
        cell.html(data.rej_party_name);
        cell.append(rej_from_input);
        cell.append(rej_party_name_input);
        cell.attr("style", "width:20%;");

        var rej_remark_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rej_remark]",
            value: data.rej_remark
        });
        var rejection_reason_input = $("<input/>", {
            type: "hidden",
            name: "rejection_reason[" + index + "][rejection_reason]",
            value: data.rejection_reason
        });
        cell = $(row.insertCell(-1));
        cell.html(data.rej_remark);
        cell.append(rej_remark_input);
        cell.append(rejection_reason_input);
        cell.attr("style", "width:20%;");

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "RemoveRejection(this);");
        btnRemove.attr("style", "margin-left:4px;");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);
        cell.attr("class", "text-center");
        cell.attr("style", "width:10%;");

        $(".qtyCal").trigger('keyup');
    }

    function RemoveRejection(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#rejectionReason")[0];
        table.deleteRow(row[0].rowIndex);
        $('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        var countTR = $('#idleReasons tbody tr:last').index() + 1;
        if (countTR == 0) {
            $("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
        }
        $(".qtyCal").trigger('keyup');
    };

    function trashOutward(id, name = 'Record') {
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
                            url: base_url + 'production_v2/processApproval/delete',
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
                                    $("#material_used_id").html("");
                                    $("#material_used_id").html(data.materialBatch);
                                    //$("#material_used_id").select2({ dropdownParent: $('.model-select2').parent() });
                                    $("#batch_no").val("");
                                    $("#issue_qty").val("0");
                                    $("#used_qty").val("0");
                                    $("#req_qty").val("0");
                                    $("#wp_qty").val("0");

                                    $("#pqty").val(data.pending_qty);
                                    $("#pending_qty").html(data.pending_qty);
                                    $("#outwardTransData").html(data.outwardTrans);
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