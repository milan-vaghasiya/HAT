<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="machine_id" id="machine_id" value="" />
            <input type="hidden" name="ftype" value="activities" />
            <div class="col-md-5 form-group">
                <label for="">Machine Activities</label>
                <select name="act_id" id="act_id" class="form-control single-select req">
                    <option value="">Select Activity</option>
                    <?php
                    foreach ($activityData as $row) :
                        $selected = (!empty($dataRow->activity_id) && $row->id == $dataRow->activity_id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->activities . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="ch_frequancy">Frequancy</label>
                <select name="ch_frequancy" id="ch_frequancy" class="form-control req">
                    <option value="Daily" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 1) ? "selected" : "" ?>>Daily</option>
                    <option value="Week" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 6) ? "selected" : "" ?>>Weekly</option>
                    <option value="Monthly" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 5) ? "selected" : "" ?>>Monthly</option>
                    <option value="Quarterly" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 2) ? "selected" : "" ?>>Quarterly</option>
                    <option value="Half Yearly" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 3) ? "selected" : "" ?>>Half Yearly</option>
                    <option value="Yearly" <?= (!empty($dataRow->checking_frequancy) && $dataRow->checking_frequancy == 4) ? "selected" : "" ?>>Yearly</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-block btn-outline-success waves-effect float-right saveItem btn-save save-form" data-fn="save"><i class="fa fa-plus"></i> Add Activity</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 from-group">
                <div class="error activity_error"></div>
                <div class="table-responsive scrollable" style="max-height:250px;">
                    <table id="machineActivity" class="table table-striped table-borderless">
                        <thead class="table-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Activity Name</th>
                                <th>Frequancy</th>
                                <th class="text-center" style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tempActivity" class="temp_activity">
                            <?php
                            if (!empty($dataRow)) :
                                $i = 1;
                                foreach ($dataRow as $row) :
                            ?>
                                    <tr>
                                        <td style="width:5%;"> <?= $i ?> </td>
                                        <td>
                                            <?= $row->activities ?>
                                            <input type="hidden" name="activity_id[]" value="<?= $row->activity_id ?>">
                                            <input type="hidden" name="id[]" value="<?= $row->id ?>">
                                        </td>
                                        <td>
                                            <?= $row->checking_frequancy ?>
                                            <input type="hidden" name="checking_frequancy[]" value="<?= $row->checking_frequancy ?>">
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2 permission-remove"><i class="ti-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php $i++;
                                endforeach;
                            else : ?>
                                <tr id="noData">
                                    <td colspan="4" class="text-center">No data available in table</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        
        $(document).on('click', '.saveItem', function() {
            var fd = $('#machine_activity').serializeArray();
            var formData = {};
            $.each(fd, function(i, v) {
                formData[v.name] = v.value;
            });
            $(".error").html("");
            if (formData.act_id == "") {
                $(".act_id").html("Machine Activities is required.");
            } else {
                var activity_ids = $("input[name='activity_id[]']").map(function() {
                    return $(this).val();
                }).get();
                if ($.inArray(formData.act_id, activity_ids) >= 0) {
                    $(".act_id").html("Machine Activities already added.");
                } 
                else 
                {
                    formData.activity_name = $('#act_idc').val();
                    AddRow(formData);
                    $('#machine_activity')[0].reset();
                    if ($(this).data('fn') == "save") {
                        $("#act_id").comboSelect();
                        $("#ch_frequancy").comboSelect();
                    }
                }
            }
        });
    });

    function AddRow(data) {
        $('table#machineActivity tr#noData').remove();
        //Get the reference of the Table's TBODY element.
        var tblName = "machineActivity";

        var tBody = $("#" + tblName + " > TBODY")[0];

        //Add Row.
        row = tBody.insertRow(-1);

        //Add index cell
        var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        cell.attr("style", "width:5%;");

        cell = $(row.insertCell(-1));
        cell.html(data.activity_name + '<input type="hidden" name="activity_id[]" value="' + data.act_id + '"><input type="hidden" name="id[]" value="0">');

        cell = $(row.insertCell(-1));
        cell.html(data.ch_frequancy + '<input type="hidden" name="checking_frequancy[]" value="' + data.ch_frequancy + '">');

        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("style", "margin-left:4px;");
        btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light permision-remove");

        cell.append(btnRemove);
        cell.attr("class", "text-center");
        cell.attr("style", "width:10%;");
    };

    function Remove(button) {
        //Determine the reference of the Row using the Button.
        var row = $(button).closest("TR");
        var table = $("#machineActivity")[0];
        table.deleteRow(row[0].rowIndex);
        $('#machineActivity tbody tr td:nth-child(1)').each(function(idx, ele) {
            ele.textContent = idx + 1;
        });
        var countTR = $('#machineActivity tbody tr:last').index() + 1;
    };
</script>