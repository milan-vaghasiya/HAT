<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">

                            <div class="col-md-4">
                                <h4 class="card-title">Rework Management </h4>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
                                    <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
                                    <input type="hidden" name="process_id" id="process_id" value="<?= (!empty($dataRow->process_id) ? $dataRow->process_id : '') ?>">
                                    <input type="hidden" id="part_id" value="">

                                    <div class="col-md-12 form-group">
                                        <div class="table-responsive">
                                            <table id="commanTable" class="table table-bordered">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Rework Qty.</th>
                                                        <th>Rework Reason</th>
                                                        <th>Rework Belong To</th>
                                                        <th>Rework From</th>
                                                        <th>Rework Remark</th>
                                                        <!-- <th>Ok Qty</th> -->
                                                        <th style="width:10%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="reworkReasonData">
                                                    <?php
                                                    // print_r($rejRwData);
                                                    if (!empty($rejRwData)) :
                                                        $j = 1;
                                                        $k = 0;
                                                        foreach ($rejRwData as $row) :
                                                            $hidden = ($row->qty == 0) ? 'hidden' : '';
                                                            
                                                            $rowData = json_encode($row);

                                                            echo '<tr ' . $hidden . '>
                                                                    <td>' . (($row->qty > 0) ? $j++ : '') . '  </td>
                                                                    <td id="rw_qty_html' . $row->id . '">' . $row->qty . '  </td>
                                                                    <td>' . $row->reason_name . ' </td>
                                                                    <td>' . $row->belongs_to_name . '</td>
                                                                    <td>' . $row->vendor_name . ' </td>
                                                                    <td>' . $row->remark . '</td>
                                                                   
                                                                    ';
                                                            echo  "
                                                                    <td class='text-center'>
                                                                    <button type='button' onclick='convertToOKQty(" . $rowData . ",this)' style='margin-left:2px;' class='btn btn-outline-success waves-effect waves-light' datatip='Ok Qty'><i class='ti-check'></i></button>
                                                                    <button type='button' onclick='addRejQty(" . $rowData . ")' style='margin-left:2px;' class='btn btn-outline-warning waves-effect waves-light' datatip='Rejection Qty'><i class='ti-close'></i></button>
                                                                    </td>
                                                                    </tr>";
                                                            // $j++;

                                                        endforeach;
                                                    else :
                                                        echo '<tr id="noData">
                                                                <td colspan="7" class="text-center">No data available in table</td>
                                                            </tr>';
                                                    endif;
                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <div class="modal fade" id="reworkToOKQtyModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content animated slideDown">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label for="accept_in_qty">OK Qty</label>
                                                    <input type="hidden" id="row_index">
                                                    <input type="hidden" id="rowData">
                                                    <input type="text" id="reworkOk_qty" class="form-control req numericOnly" value="0">
                                                    <input type="hidden" id="button">
                                                </div>
                                                <div class="col-md-12 form-group">
                                                    <label for="remark">Rejection Remark</label>
                                                    <input type="text" id="remark" class="form-control" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeModal"><i class="fa fa-times"></i> Close</button>
                                            <button type="button" class="btn waves-effect waves-light btn-outline-success acceptJob" onclick="saveReworkOkQty()"><i class="fa fa-check"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="reworkToRejQtyModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content animated slideDown">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="exampleModalLabel1" style="width:100%;">Rejection</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="error general_error col-md-12"></div>
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label for="rej_qty">Rejected Qty.</label>
                                                    <input type="text" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
                                                    <input type="hidden" id="rej_ref_id" class="form-control numericOnly qtyCal req" value="0" min="0" />
                                                    <input type="hidden" id="rej_type" class="form-control numericOnly qtyCal req" value="0" min="0" />
                                                    <input type="hidden" id="rowData">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="rej_reason">Rejection Reason</label>
                                                    <select id="rej_reason" class="form-control single-select req">
                                                        <option value="">Select Reason</option>
                                                        <?php
                                                        foreach ($rejectionComments as $row) :
                                                            $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                                                            echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 form-group">
                                                    <label for="rejection_stage">Rejection Belong To</label>
                                                    <select id="rejection_stage" class="form-control single-select req">
                                                        <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                                                        echo $dataRow->stage;
                                                                                                                                    } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="rej_from">Rejection From <span class="text-danger">*</span></label>
                                                    <select id="rej_from" class="form-control single-select req">
                                                        <option value="">Select Rej. From</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 form-group">
                                                    <label for="rej_remark">Rejection Remark</label>
                                                    <input type="text" id="rej_remark" class="form-control" value="">
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeModal"><i class="fa fa-times"></i> Close</button>
                                            <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveRejectionQty()"><i class="fa fa-check"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script src="<?php echo base_url(); ?>assets/js/custom/rejection-log.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function() {
        $(document).on("click", ".closeModal", function() {
            $('#reworkToOKQtyModel').modal('hide');
            $('#reworkToRejQtyModel').modal('hide');
        });
        initDataTable();
    });
</script>