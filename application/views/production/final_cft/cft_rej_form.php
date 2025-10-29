<form>
    <div class="row">
        <div class="col-md-4 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="entry_type" name="entry_type" value="3">
            <input type="hidden" id="operation_type" name="operation_type" value="<?= (!empty($operation_type) ? $operation_type : 1) ?>">
            <input type="hidden" id="job_trans_id" name="job_trans_id" value="<?= (!empty($dataRow->job_trans_id) ? $dataRow->job_trans_id : '') ?>">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
            <input type="hidden" id="ref_type" name="ref_type" value="<?= (!empty($dataRow->entry_type) ? $dataRow->entry_type : '') ?>">

            <input type="hidden" id="process_id" value="<?= (!empty($dataRow->process_id) ? $dataRow->process_id : '') ?>">
            <input type="hidden" id="part_id" value="<?= (!empty($dataRow->product_id) ? $dataRow->product_id : '') ?>">
            <label for="qty">Rej Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly">
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_reason">Rejection Reason</label>
            <select id="rr_reason" name="rr_reason" class="form-control single-select req">
                <option value="">Select Reason</option>
                <?php
                foreach ($rejectionComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rej_type">Rejection Type</label>
            <select id="rej_type" name="rej_type" class="form-control req">
                <option value="">Select type</option>
                <option value="1">Machine</option>
                <option value="2">Raw Material</option>
            </select>
            <div class="error rej_type"></div>
        </div>
        <div class="col-md-4 form-group">
            <label for="opr_mt">OPR./MT. Type</label>
            <select id="opr_mt" name="opr_mt"  class="form-control req single-select">
                <option value="Other">Other</option>
                <?php
                    foreach($this->OPR_MT_TYPE as $key=>$value){
                        ?> <optgroup label="<?=$key?>" >
                            <?php foreach($OPR_MT_TYPE[$key] as $opr=>$type){
                                ?>  <option value="<?=$type?>"><?=$type?></option> <?php
                            } ?>
                        </optgroup> <?php
                    } ?>
            </select>
            <div class="error rej_type"></div>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_stage">Rejection Stage</label>
            <select id="rr_stage" name="rr_stage" class="form-control single-select req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>
            </select>
        </div>
        <div class="col-md-4 form-group" <?= (!empty($this->controlPlanEnable)?'':'hidden')?>>
            <label for="dimension_range"> Dimension</label>
            <select id="dimension_range" name="dimension_range" class="form-control single-select">
                <option value="">Select Dimension</option>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_by">Rejection By <span class="text-danger">*</span></label>
            <select id="rr_by" name="rr_by" class="form-control single-select req">
                <option value="">Select Rej. From</option>
            </select>
        </div>
        <div class="col-md-12 form-group">
            <label for="remark">Variance</label>
            <input type="text" id="remark" name="remark" class="form-control req" value="">
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
                        pfc_id:pfc_id
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

        $(document).on("change", "#rej_type", function() {
        
            $("#rr_stage").val("");
            $("#rr_stage").comboSelect();
            $("#rr_by").val("");
            $("#rr_by").comboSelect();

            var rej_type = $("#rej_type").val();
            if(rej_type == 1){
                $('#opr_mt optgroup[label=Operation]').prop('disabled', false); 
                $('#opr_mt optgroup[label=Material]').prop('disabled', true); 
                
            }else{
                $('#opr_mt optgroup[label=Operation]').prop('disabled', true); 
                $('#opr_mt optgroup[label=Material]').prop('disabled', false); 
                
            }
            $("#opr_mt").comboSelect();
            
        });
    });
</script>