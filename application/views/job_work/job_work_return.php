<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow['job_card_id']))?$dataRow['job_card_id']:""?>">
            <input type="hidden" name="job_trans_id" id="job_trans_id" value="<?=(!empty($dataRow['job_trans_id']))?$dataRow['job_trans_id']:""?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($dataRow['job_approval_id']))?$dataRow['job_approval_id']:""?>">
            <input type="hidden" name="pending_qty" id="pending_qty" value="<?=(!empty($dataRow['pending_qty']))?$dataRow['pending_qty']:"0"?>">

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
                <input type="date" name="entry_date" id="entry_date" class="form-control req" min="<?=(!empty($dataRow['minDate']))?$dataRow['minDate']:$maxDate?>" max="<?=$maxDate?>" value="<?=$maxDate?>"> 
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly req" min="0" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="total_weight">Total Weight</label>
                <input type="number" name="total_weight" id="total_weight" class="form-control floatOnly" min="0" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="return_process_id">Process Name</label>
                <select name="return_process_id" id="return_process_id" class="form-control single-select req">
                    <?php
                        if(!empty($dataRow['job_process_ids'])):
                            $processList = explode(",",$dataRow['job_process_ids']);
                            foreach($processList as $key=>$value):
                                $processData = $this->process->getProcess($value);
                                echo '<option value="'. $processData->id.'">'.$processData->process_name.'</option>';
                            endforeach;
                        else:
                            echo '<option value="">Select Process</option>';
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-8 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
        </div>
    </div>
</form>