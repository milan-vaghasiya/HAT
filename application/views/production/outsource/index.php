<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('outsourceTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('outsourceTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <?php if ($shortYear == CURRENT_FYEAR) : ?>
                                            <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
                                                <i class="fa fa-plus"></i> Create Challan
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='outsourceTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" id="vendorChallanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <form id="vendorChallanForm">
                <input type="hidden" name="vendor_id" id="vendor_id" value="0" />
                <input type="hidden" name="challan_id" id="challan_id" value="0" />

                <div class="modal-header">
                    <div class="col-md-8">
                        <h4 class="modal-title">Create Challan For : <span id="vendorName"></span></h4>
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="trans_date" id="trans_date" class="form-control float-right req" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="process_id">Select Process</label>
                            <select name="process_id" id="process_id" class="form-control single-select req">
                                <option value="">All Process</option>
                                <?php
                                foreach ($processList as $row) :
                                    echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-8 form-group">
                            <label for="remark">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control" value="">
                        </div>
                        <div class="table-responsive">
                            <div class="error orderError"></div><br>
                            <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                                <thead class="thead-info">
                                    <tr class="text-center">
                                        <th class="text-center" style="width:5%;">#</th>
                                        <th class="text-center" style="width:10%;">Job No.</th>
                                        <th class="text-center" style="width:10%;">Job Date</th>
                                        <th class="text-center" style="width:20%;">Product</th>
                                        <th class="text-center" style="width:10%;">Ok Qty.</th>
                                        <th class="text-center" style="width:10%;">Pending Qty.</th>
                                        <th  style="width:10%;">Challan Qty.</th>
                                        <th style="width:25%;">Remark</th>
                                    </tr>
                                </thead>
                                <tbody id="outsourceTransData">
                                    <tr><td colspan="8" class="text-center">No data available in table</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="store('vendorChallanForm','save');"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>

<script>
    $(document).ready(function() {
        $(document).on('click', '.createVendorChallan', function(e) {
            $(".error").html("");
            var valid = 1;
            var item_id = $('#item_id').val();
            var party_id = $('#party_id').val();
            var party_name = $('#party_idc').val();
            if ($("#party_id").val() == "" || $("#party_id").val() == 0) {
                $(".party_id").html("Party is required.");
                valid = 0;
            }
            if (valid) {
                $.ajax({
                    url: base_url + controller + '/createOutsourceChallan',
                    type: 'post',
                    data: {
                        item_id: item_id,
                        party_id: party_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#vendorChallanModal").modal();
                        $("#exampleModalLabel1").html('Create Challan For : ' + party_name);
                        $("#vendor_id").val(party_id);
                        $("#vendorName").html(party_name);
                        $("#process_id").html("");
                        $("#process_id").html(data.processOption);
                        $("#process_id").comboSelect();
                        //$('#outsourceTransTable').DataTable().clear().destroy();
                        $("#outsourceTransData").html("");
                        // jpDataTable = jpDataTable('outsourceTransTable');
                        // setTimeout(function() {
                        //     jpDataTable.columns.adjust().draw();
                        // }, 200);
                    }
                });
            }
        });

        $(document).on('change', '#process_id', function(e) {
            $(".error").html("");
            var valid = 1;
            var vendor_id = $('#vendor_id').val();
            var process_id = $(this).val();
            if ($("#vendor_id").val() == "" || $("#vendor_id").val() == 0) {
                $(".vendor_id").html("Vendor is required.");
                valid = 0;
            }
            if (process_id == "" || process_id == 0) {
                $(".process_id").html("Process is required.");
                valid = 0;
            }
            if (valid) {
                $.ajax({
                    url: base_url + controller + '/getPendingOSTransaction',
                    type: 'post',
                    data: {
                        vendor_id: vendor_id,
                        process_id: process_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        //$('#outsourceTransTable').DataTable().clear().destroy();
                        $("#outsourceTransData").html("");
                        $("#outsourceTransData").html(data.transData);
                        // jpDataTable = jpDataTable('outsourceTransTable');
                        // setTimeout(function() {
                        //     jpDataTable.columns.adjust().draw();
                        // }, 200);
                    }
                });
            }
        });

        $(document).on("click", ".challanCheck", function() {
            var id = $(this).data('rowid');
            $(".error").html("");
            if (this.checked) {
                $("#ch_qty" + id).removeAttr('disabled');
                $("#trans_remark" + id).removeAttr('disabled');
            } else {
                $("#ch_qty" + id).attr('disabled', 'disabled');
                $("#trans_remark" + id).attr('disabled', 'disabled');
            }
        });

        $(document).on("keyup", ".challanQty", function() {
            var id = $(this).data('rowid');
            var ch_qty = $("#ch_qty" + id).val();
            var out_qty = $("#out_qty" + id).val();
            if (parseFloat(ch_qty) > parseFloat(out_qty)) {
                $("#ch_qty" + id).val('0');
            }
        });
    });

    function AddRow() {
        var valid = 1;
        $(".error").html("");

        if ($("#item_id").val() == "") {
            $(".item_id").html("Packing Material is required.");
            valid = 0;
        }
        if ($("#out_qty").val() == "" || $("#out_qty").val() == 0) {
            $(".out_qty").html("qty is required.");
            valid = 0;
        }

        if (valid) {
            $(".item_id").html("");
            $(".out_qty").html("");
            //Get the reference of the Table's TBODY element.
            $("#packingBom").dataTable().fnDestroy();
            var tblName = "packingBom";

            var tBody = $("#" + tblName + " > TBODY")[0];

            //Add Row.
            row = tBody.insertRow(-1);

            //Add index cell
            var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
            var cell = $(row.insertCell(-1));
            cell.html(countRow);

            cell = $(row.insertCell(-1));
            cell.html($("#item_idc").val() + '<input type="hidden" name="item_id[]" value="' + $("#item_id").val() + '">');


            var out_qtyErrorDiv = $("<div></div>", {
                class: "error out_qty" + countRow
            });
            cell = $(row.insertCell(-1));
            cell.html($("#out_qty").val() + '<input type="hidden" name="out_qty[]" value="' + $("#out_qty").val() + '">');
            cell.append(out_qtyErrorDiv);

            //Add Button cell.
            cell = $(row.insertCell(-1));
            var btnRemove = $('<button><i class="ti-trash"></i></button>');
            btnRemove.attr("type", "button");
            btnRemove.attr("onclick", "Remove(this);");
            btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
            cell.append(btnRemove);
            cell.attr("class", "text-center");
            $("#item_id").val("");
            $("#item_idc").val("");
            $("#out_qty").val("");
        }
    };

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#packingBom")[0];
        table.deleteRow(row[0].rowIndex);
        $('#packingBom tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
    };

    function vendorMaterialReturn(data){
        var button = data.button;
        $.ajax({ 
            type: "POST",   
            url: base_url +controller+ '/vendorInward',   
            data: {id:data.job_approval_id,job_trans_id:data.job_trans_id}
        }).done(function(response){
            $("#"+data.modal_id).modal();
            $("#"+data.modal_id+' .modal-title').html(data.title);
            $("#"+data.modal_id+' .modal-body').html(response);
            $("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
            $("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"');");
            if(data.button == "close"){
                $("#"+data.modal_id+" .modal-footer .btn-close").show();
                $("#"+data.modal_id+" .modal-footer .btn-save").hide();
            }else if(data.button == "save"){
                $("#"+data.modal_id+" .modal-footer .btn-close").hide();
                $("#"+data.modal_id+" .modal-footer .btn-save").show();
            }else{
                $("#"+data.modal_id+" .modal-footer .btn-close").show();
                $("#"+data.modal_id+" .modal-footer .btn-save").show();
            }
            $(".single-select").comboSelect();
            setPlaceHolder();
            initMultiSelect();
        });
    }
</script>