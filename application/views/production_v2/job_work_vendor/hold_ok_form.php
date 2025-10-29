<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tbody>
        <tr class="">
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->job_card_id)) ? $dataRow->job_prefix . $dataRow->job_no : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Product</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Process</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->in_process_name)) ? $dataRow->in_process_name : "" ?> ->
                <?= (!empty($dataRow->out_process_name)) ? $dataRow->out_process_name : "Store Location" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Qty.</th>
            <th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?= (!empty($dataRow->pqty)) ? $dataRow->pqty : "" ?></th>
        </tr>
    </tbody>
</table>
<form style="padding-top:35px;">
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>">
            <input type="hidden" name="vp_trans_id" id="vp_trans_id" value="<?= (!empty($dataRow->vp_trans_id)) ? $dataRow->vp_trans_id : 0 ?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->production_approval_id)) ? $dataRow->production_approval_id : "" ?>">
            <input type="hidden" name="trans_type" id="trans_type" value="">
            <input type="hidden" name="w_pcs" id="w_pcs" value="">
            <input type="hidden" name="total_weight" id="total_weight" value="<?= (!empty($dataRow->in_total_weight)?$dataRow->in_total_weight:'') ?>">
            <input type="hidden" name="material_request" value="1">


            <input type="hidden" name="job_card_no" id="job_card_no" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_prefix . $dataRow->job_no : "" ?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : "" ?>" />
            <input type="hidden" id="delivery_date" value="<?= (!empty($dataRow->delivery_date)) ? date("d-m-Y", strtotime($dataRow->delivery_date)) : "" ?>" />
            <input type="hidden" id="product_name" value="<?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?>" />
            <input type="hidden" name="product_id" id="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : "" ?>" />
            <input type="hidden" id="in_process_name" value="<?= (!empty($dataRow->in_process_name)) ? $dataRow->in_process_name : "" ?>" />
            <input type="hidden" name="in_process_id" id="in_process_id" value="<?= (!empty($dataRow->in_process_id)) ? $dataRow->in_process_id : "0" ?>">
            <input type="hidden" name="out_process_id" id="out_process_id" value="<?= (!empty($dataRow->out_process_id)) ? $dataRow->out_process_id : "" ?>" />
            <input type="hidden" name="in_qty" id="in_qty" value="<?= (!empty($dataRow->in_qty)) ? $dataRow->in_qty : "" ?>" />
            <input type="hidden" id="out_process_name" value="<?= (!empty($dataRow->out_process_name)) ? $dataRow->out_process_name : "" ?>" />
            <input type="hidden" id="pqty" value="<?= (!empty($dataRow->pqty)) ? $dataRow->pqty : "" ?>" readonly />
            <input type="hidden" name="trans_ref_id" id="trans_ref_id" value="<?= (!empty($dataRow->trans_ref_id)) ? $dataRow->trans_ref_id : "" ?>">
            <input type="hidden" name="from_entry_type" id="from_entry_type" value="2">

            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?= date("Y-m-d") ?>" min="<?= $startYearDate ?>" max="<?= $dataRow->entry_date ?>">
            </div>

            
            <div class="col-md-2 form-group">
                <label for="out_qty">OK Qty. (Pcs)</label>
                <input type="number" name="out_qty" id="out_qty" class="form-control numericOnly req"  value="<?=$dataRow->in_qty?>" readonly>
                <input type="hidden" name="production_qty" id="production_qty"  class="form-control numericOnly qtyCal req" value="<?=$dataRow->in_qty?>" readonly>

                <div class="error batch_stock_error"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="in_qty_kg">Out Qty. (Kg)</label>
                <input type="number" name="in_qty_kg" id="in_qty_kg" class="form-control floatOnly req1" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="vendor_id">Vendor Name</label>
                <select name="vendor_id" id="vendor_id" class="form-control single-select">
                    <option value="0">In House</option>
                    <?php
                    foreach ($vendorData as $row) :
                        echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="job_order_id">Job Order No.</label>
                <select name="job_order_id" id="job_order_id" class="form-control single-select">
                    <option value="">Select Job Order No.</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="job_process_ids">Job Order Process</label>
                <select name="job_process_ids" id="job_process_ids" class="form-control single=select">
                    <option value="">Select Process</option>
                </select>
                <div class="error job_process_ids"></div>
            </div>

            <div class="col-md-2 form-group">
                <label for="in_challan_no">Challan No.</label>
                <input type="text" name="in_challan_no" id="in_challan_no" class="form-control" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label for="charge_no">Charge No.</label>
                <input type="text" name="charge_no" id="charge_no" class="form-control" value="" />
            </div>

            <div class="col-md-8 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <?php
            if (!empty($dataRow->vp_trans_id)) {
            ?>
                <div class="col-md-2 form-group">
                    <label for="entry_type">Transaction</label>
                    <select name="entry_type" id="entry_type" class="form-control">
                        <option value="1">Move To Next Process</option>
                        <option value="2">Hold Area</option>
                    </select>
                </div>

            <?php
            } else {
            ?>
                <input type="hidden" name="entry_type" value="1">
            <?php
            }

            if (!empty($dataRow->vp_trans_id) || !empty($trans_ref_id)) {
            ?>
                <hr style="width:100%">
                <div class="col-md-3 form-group">
                    <label for="rej_qty">Rejected Qty.</label>
                    <input type="text" name="rej_qty" id="rej_qty" class="form-control numericOnly qtyCal req" value="" min="0" />
                </div>
                <div class="col-md-3 form-group">
                    <label for="rej_reason">Rejection Reason</label>
                    <select name="rej_reason" id="rej_reason" class="form-control single-select req">
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
                    <select name="rej_from" id="rej_from" class="form-control single-select req">
                        <option value="">Select Rej. From</option>
                    </select>
                </div>
                <div class="col-md-11 form-group">
                    <label for="rej_remark">Rejection Remark</label>
                    <input type="text" name="rej_remark" id="rej_remark" class="form-control" value="">
                </div>
                <div class="col-md-1 form-group">
                    <label for="">&nbsp;</label>
                    <button type="button" id="addRejectionRow" class="btn btn-outline-info btn-block ">Add</button>
                </div>
                <div class="col-md-12 form-group">
                    <div class="table-responsive">
                        <table id="rejectionReason" class="table table-bordered">
                            <thead class="thead-info">
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th>Rejection Qty.</th>
                                    <th>Rejection Reason</th>
                                    <th>Rejection Belong To</th>
                                    <th>Rejection From</th>
                                    <th>Rejection Remark</th>
                                    <th style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rejectionReasonData">
                                <tr id="noData">
                                    <td colspan="7" class="text-center">No data available in table</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
            }
            ?>

            <!--<div class="col-md-2 form-group">
				<label for="">&nbsp;</label>
				<button type="button" class="btn waves-effect waves-light btn-outline-success btn-block float-right save-form" onclick="saveOutward('outWard');"><i class="fa fa-check"></i> Save</button>
			</div>-->
        </div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :
            <button type="button" class="btn waves-effect waves-light btn-success float-right save-form" onclick="saveOutward('outWard');" style="padding:5px 40px;"><i class="fa fa-check"></i> Save</button>
        </h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Vendor</th>
                        <th>Out Qty.</th>
                        <th>Remark</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="outwardTransData">
                    <?php
                    $html = "";
                    $i = 1;
                    if (!empty($outwardTrans)) :
                        foreach ($outwardTrans as $row) :
                            $transDate = date("d-m-Y", strtotime($row->entry_date));
                            $transType = ($row->entry_type == 2 || $row->entry_type == 3) ? "Hold Area" : "Regular";
                            $deleteBtn = '';
                            if (empty($row->accepted_by)) :
                                $printBtn = '<a href="' . base_url('production_v2/jobcard/printProcessIdentification/' . $row->id) . '" target="_blank" class="btn btn-sm btn-outline-info waves-effect waves-light mr-2" title="Print"><i class="fas fa-print"></i></a>';
                                $deleteBtn = '<button type="button" onclick="trashOutward(' . $row->id . ');" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>';
                            endif;
                            $html .= '<tr>
                                        <td>' . $i++ . '</td>
                                        <td>' . $transDate . '</td>
                                        <td>' . $transType . '</td>
                                        <td>' . $row->vendor_name . '</td>
                                        <td>' . $row->in_qty . '</td>
                                        <td>' . $row->remark . '</td>
                                        <td class="text-center" style="width:10%;">' . $printBtn . $deleteBtn . '</td>
                                    </tr>';
                        endforeach;
                    else :
                        $html = '<td colspan="7" class="text-center">No Data Found.</td>';
                    endif;
                    echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>