<form>
    <div class="row">
        <div class="col-md-4 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="entry_type" name="entry_type" value="<?= (!empty($entry_type) ? $entry_type : 2) ?>">
            <input type="hidden" id="operation_type" name="operation_type" value="<?= (!empty($operation_type) ? $operation_type : 1) ?>">
            <input type="hidden" id="job_trans_id" name="job_trans_id" value="<?= (!empty($dataRow->job_trans_id) ? $dataRow->job_trans_id : '') ?>">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
            <input type="hidden" id="ref_type" name="ref_type" value="<?= (!empty($dataRow->entry_type) ? $dataRow->entry_type : '') ?>">

            <input type="hidden" id="process_id" value="<?= (!empty($dataRow->process_id) ? $dataRow->process_id : '') ?>">
            <input type="hidden" id="part_id" value="<?= (!empty($dataRow->product_id) ? $dataRow->product_id : '') ?>">
            <label for="qty">Rework Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly">
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_reason">Rework Reason</label>
            <select id="rr_reason" name="rr_reason" class="form-control single-select req">
                <option value="">Select Reason</option>
                <?php
                foreach ($reworkComment as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-4 form-group">
            <label for="rr_stage">Rework Stage</label>
            <select id="rr_stage" name="rr_stage" class="form-control single-select req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>
            </select>
        </div>
        <div class="col-md-4 form-group"  <?= (!empty($this->controlPlanEnable)?'':'hidden')?>>
            <label for="dimension_range"> Dimension</label>
            <select id="dimension_range" name="dimension_range" class="form-control single-select">
                <option value="">Select Dimension</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_by">Rework By <span class="text-danger">*</span></label>
            <select id="rr_by" name="rr_by" class="form-control single-select req">
                <option value="">Select Rej. From</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_stage">Rework Process</label>
            <select  id="rw_process_id" name="rw_process_id" class="form-control single-select req">
                <?php echo $dataRow->reworkProcess; ?>
            </select>
            <!-- <input type="hidden" id="rw_process_id" name="rw_process_id"> -->
        </div>
        <div class="col-md-12 form-group">
            <label for="remark">Variance </label>
            <input type="text" id="remark" name="remark" class="form-control" value="">
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
		$(document).on("change", "#rr_stage", function() {
            //$("#rr_stage").change(function(){
            var process_id = $(this).find(":selected").data('process_id');
            var part_id = $("#part_id").val();
            var pfc_id = $(this).val();

            if (process_id) {
                var job_card_id = $("#job_card_id").val();
                $.ajax({
                    url: base_url + controller + '/getRRByOptions',
                    type: 'post',
                    data: {
                        process_id: process_id,
                        part_id: part_id,
                        job_card_id: job_card_id,
                        pfc_id: pfc_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#rr_by").html("");
                        $("#rr_by").html(data.rejOption);
                        $("#rr_by").comboSelect();

                        $("#dimension_range").html("");
                        $("#dimension_range").html(data.dimOptions);
                        $("#dimension_range").comboSelect();
                    }
                });
            } else {

                $("#rr_by").html("<option value=''>Select Rej. From</option>");
                $("#rr_by").comboSelect();

                $("#dimension_range").html("<option value=''>Select</option>");
                $("#dimension_range").comboSelect();

            }
        });
    });
</script>