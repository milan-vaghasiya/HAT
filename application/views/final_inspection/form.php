<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 form-group">
                <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
					<tr class="">
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=$inspectionData['product_name']?></th>
						
						<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
						<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"><?=$inspectionData['pending_qty']?></th>
					</tr>
				</table>
            </div>
            <div class="col-md-4 form-group">
                <label for="ok_qty">OK Qty.</label>
                <input type="number" name="ok_qty" id="ok_qty" class="form-control floatOnly" min="0" value="">
                <input type="hidden" name="rejection_id" id="rejection_id" value="<?=$inspectionData['id']?>">
                <input type="hidden" name="pending_qty" id="pending_qty" value="<?=$inspectionData['pending_qty']?>">
                <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$inspectionData['job_card_id']?>">
                <input type="hidden" name="product_id" id="product_id" value="<?=$inspectionData['product_id']?>">
                <input type="hidden" name="rejection_type_id" id="rejection_type_id" value="<?=$inspectionData['rejection_type_id']?>">
                <input type="hidden" name="job_inward_id" id="job_inward_id" value="<?=$inspectionData['job_inward_id']?>">
                <input type="hidden" name="operator_id" id="operator_id" value="<?=$inspectionData['operator_id']?>">
                <input type="hidden" name="machine_id" id="machine_id" value="<?=$inspectionData['machine_id']?>">
            </div>
            <div class="col-md-4 form-group">
                <label for="ud_qty">UD Qty.</label>
                <input type="number" name="ud_qty" id="ud_qty" class="form-control floatOnly" min="0" value="">
            </div>
            <div class="col-md-4 form-group">
                <label for="scrape_qty">Scrape Qty.</label>
                <input type="number" name="scrape_qty" id="scrape_qty" class="form-control floatOnly" min="0" value="">
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-outline-success waves-effect btn-block save-form" onclick="inspectionSave();"><i class="fas fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12 mt-10">
    <div class="table-responsive">
        <table id="inspectionTable" class="table table-bordered align-items-center" style="width: 100%;">
            <thead class="thead-info">
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>OK Qty.</th>
                    <th>UD Qty</th>
                    <th>Scrape Qty.</th>
                    <th>Remark</th>
                    <th class="text-center" style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody id="inspectionData">
                <?=$inspectionTrans['htmlData']?>
            </tbody>
        </table>
    </div>
</div>