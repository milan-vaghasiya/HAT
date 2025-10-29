<style>
    .ui-sortable-handle {
        cursor: move;
    }

    .ui-sortable-handle:hover {
        background-color: #daeafa;
        border-color: #9fc9f3;
        cursor: move;
    }
</style>

<div class="col-md-12">
    <div class="row">
        <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
        <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
    </div>
    <div class="row">
        <div class="col-md-4 form-group">
            <label for="transfer_from">Transfer From</label>
            <input type="date" name="transfer_from" id="transfer_from" class="form-control" value="<?=date("Y-m-d",strtotime('2022-02-01'))?>" />
        </div>
        <div class="col-md-3 form-group">
            <label for="emp_code">Old Emp Code</label>
            <input type="text" name="emp_code" id="emp_code" class="form-control" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:""?>" readonly />
        </div>
        <div class="col-md-3 form-group">
            <label for="new_emp_code">New Code</label>
            <input type="text" name="new_emp_code" id="new_emp_code" class="form-control req" value="" />
        </div>
        <div class="col-md-2 form-group">
            <button type="button" class="btn btn-block btn-danger waves-effect waves-light mt-30" onclick="transferEmpCode();"><i class="fas fa-exchange-alt"></i> Transfer</button>
        </div>
    </div>
    
    <div class="col-md-12 mt-3">
        <div class="row">

            <table id="deviceList" class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Device</th>
                        <th>Location</th>

                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="leaveBody">
                    <?php
                    $deviceArray = explode(',', $dataRow->device_id);
                    if (!empty($deviceList)) :
                        $i = 1;
                        foreach ($deviceList as $row) :
                            $deviceButton="";
                            if (in_array($row->id, $deviceArray)) {
                                $deviceButton = '<a class="btn btn-outline-danger btn-sm btn-delete" href="javascript:void(0)" data-device_srno="' . $row->device_srno . '" onclick="removeEmployeeInDevice(' . $row->id . ',' . $emp_id . ');" datatip="Add In Device" flow="down"><i class="fa fa-trash"></i></a>';
                                $deviceButton ='<labe class="text-facebook">Already Added In Device</label>';

                            } else {
                                $deviceParm = $row->device_srno . ',' . $emp_id;
                                $deviceButton = '<a class="btn btn-outline-info btn-sm btn-delete" href="javascript:void(0)" data-device_srno="' . $row->device_srno . '" onclick="addEmpInDevice(' . $row->id . ',' . $emp_id . ');" datatip="Add In Device" flow="down"><i class="fa fa-desktop"></i></a>';
    
                            }
                            echo '<tr id="' . $row->id . '">
                            <td class="text-center">' . $i++ . '</td>
                            <td>' . $row->device_srno . '</td>
                            <td>' . $row->device_location . '</td>
                            <td style="width:30%">' . $deviceButton . '</td>
                        </tr>';
                        endforeach;
                    else :
                        echo '<tr><td colspan="5" class="text-center">No Data Found.</td></tr>';
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function addEmpInDevice(id, emp_id) {

            var send_data = {
                id: id,
                emp_id: emp_id
            };
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to add in device ?',
                type: 'red',
                buttons: {
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function() {
                            $.ajax({
                                url: base_url + controller + '/saveEmployeeInDevice',
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
                                        initTable();
                                        initMultiSelect();
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
        
        function removeEmployeeInDevice(id, emp_id) {

            var send_data = {
                id: id,
                emp_id: emp_id
            };
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to remove from device ?',
                type: 'red',
                buttons: {
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function() {
                            $.ajax({
                                url: base_url + controller + '/removeEmployeeInDevice',
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
                                        initTable();
                                        initMultiSelect();
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
        
        function transferEmpCode() {
            
            var emp_id = $('#emp_id').val();
            var transfer_from = $('#transfer_from').val();
            var old_emp_code = $('#emp_code').val();
            var new_emp_code = $('#new_emp_code').val();
            
            var send_data = {emp_id: emp_id,transfer_from: transfer_from,old_emp_code: old_emp_code,new_emp_code: new_emp_code };
            
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to transfer Employee Code ?',
                type: 'red',
                buttons: {
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function() {
                            $.ajax({
                                url: base_url + controller + '/transferEmpCode',
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
                                        initTable();
                                        initMultiSelect();
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