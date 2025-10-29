<table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
    <tr class="">
        <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Material Name</th>
        <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="materialName"><?= $dataRow['item_name'] ?></th>
        <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border:0px;">Pending</th>
        <th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="pendingQty"><?= round($dataRow['pendingQty'] ,3)?></th>
    </tr>
</table>
<form>
    <div class="col-md-12 mt-3">
        <input type="hidden" name="item_id" id="item_id" value="<?= $dataRow['item_id'] ?>" />
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?= $dataRow['job_card_id'] ?>" />
        <input type="hidden" name="wp_qty" id="wp_qty" value="<?=$dataRow['wp_qty']?>">
    </div>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="trans_type" id="trans_type" value="1">
            <div class="col-md-4 form-group">
                <label for="ref_type">Return Type</label>
                <select name="ref_type" id="ref_type" class="form-control">
                    <option value="">Select Return Type</option>
                    <option value="10">Material Return</option>
                    <option value="18">Scrap</option>
                    <option value="27">Used In Job</option>
                </select>
            </div>
            <div class="col-md-4 form-group location">
                <label for="location_id">Store Location</label>
                <input type="hidden" id="scrap_store_id" value="<?= $this->SCRAP_STORE->id ?>">
                <select id="location_id" name="location_id" class="form-control single-select req">
                    <option value="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '">' . $row->location . ' </option>';
                        endforeach;
                        $options .= '</optgroup>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group batchNo">
                <label for="batch_no">Batch No.</label>
                <select id="batch_no" class="form-control single-select req" name="batch_no">
                    <?= $batchData ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly req pendingQtykg" placeholder="Enter Quantity" value="0" min="0" />
            </div>
            <div class="col-md-4 form-group pendingPcs">
                <label for="pending_qty_pcs">Qty Pcs</label>
                <input type="number" name="pending_qty_pcs" id="pending_qty_pcs" class="form-control floatOnly req" placeholder="Enter Quantity" value="0" min="0" readonly />
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary waves-effect waves-light btn-block" onclick="returnScrapSave();"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-10">
                <div class="error item_stock mb-3"></div>
                <div class="table-responsive">
                    <table id="returnTable" class="table table-bordered align-items-center" style="width:100%;">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item Name</th>
                                <th>Qty</th>
                                <th class="text-center" style="width:10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="returnScrapData">
                            <?= $transData ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $(document).on('keyup', '.pendingQtykg', function() {
            if($("#ref_type").val() == 10){
                var wp_qty=$("#wp_qty").val();
                var qty_kg=$(this).val();
                var pending_pcs=parseFloat(qty_kg)/parseFloat(wp_qty);
                $("#pending_qty_pcs").val(pending_pcs.toFixed());
            }
        });
    });
</script>