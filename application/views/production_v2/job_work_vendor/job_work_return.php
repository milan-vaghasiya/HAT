<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow['id']))?$dataRow['id']:""?>">
            <input type="hidden" name="pending_qty" id="pending_qty" value="<?=(!empty($dataRow['pending_qty']))?$dataRow['pending_qty']:"0"?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow['job_card_id']))?$dataRow['job_card_id']:"0"?>">
            <input type="hidden" name="production_approval_id" id="pending_qty" value="<?=(!empty($dataRow['production_approval_id']))?$dataRow['production_approval_id']:"0"?>">
            <input type="hidden" name="production_trans_id" id="production_trans_id" value="<?=(!empty($dataRow['production_trans_id']))?$dataRow['production_trans_id']:"0"?>">
            <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow['process_id']))?$dataRow['process_id']:"0"?>">
            <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow['product_id']))?$dataRow['product_id']:"0"?>">

            <div class="col-md-12 form-group">
                <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
					<tr class="">
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=(!empty($dataRow['product_name']))?$dataRow['product_name']:""?></th>
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Process</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductProcessName"><?=(!empty($dataRow['process_name']))?$dataRow['process_name']:""?></th>
						<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
						<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"><?=(!empty($dataRow['pending_qty']))?$dataRow['pending_qty']:"0"?></th>
					</tr>
				</table>
            </div>

            <div class="col-md-4 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?=$maxDate?>"  min="<?=$startYearDate?>" max="<?=$maxDate?>"> 
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly req" min="0" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="total_weight">Total Weight</label>
                <input type="number" name="total_weight" id="total_weight" class="form-control floatOnly" min="0" value="" />
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30" onclick="jobWorkReturnSave('jobWorkReturnSave','jobWorkReturnSave');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <h4>Transaction Details :</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:10%;">#</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Qty</th>
                        <th>Remark</th>
                        <th class="text-center" style="width:20%;">Action</th>
                    </tr>
                </thead>
                <tbody id="transHtmlData">
                    <?=$transHtml?>
                </tbody>
            </table>
        </div>
    </div>
    

</div>
