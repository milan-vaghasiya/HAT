<div class="col-md-12">
    <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
        <tr class="">
            <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
            <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=$dataRow['postData']['product_name']?></th>
            <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Process</th>
            <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductProcessName"><?=$dataRow['postData']['process_name']?></th>
            <th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
            <th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"><?=($dataRow['inwardData']->in_qty - $dataRow['inwardData']->inspected_qty)?></th>
        </tr>
    </table>
</div>
<form>
    <input type="hidden" name="ref_id" id="ref_id" value="<?=$dataRow['postData']['ref_id']?>" />
    <input type="hidden" name="product_id" id="product_id" value="<?=$dataRow['postData']['product_id']?>" />
    <input type="hidden" name="process_id" id="in_process_id" value="<?=$dataRow['postData']['process_id']?>" />
    <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$dataRow['postData']['job_card_id']?>" />
    <input type="hidden" id="pending_qty" value="<?=($dataRow['inwardData']->in_qty - $dataRow['inwardData']->inspected_qty)?>" />
    <input type="hidden" name="cycle_time" id="cycle_time" value="" />
    <input type="hidden" name="ud_qty" id="ud_qty" class="form-control numericOnly" min="0" value="0" />
    <input type="hidden" id="production_out_qty" value="<?=(!empty($dataRow['inwardData']->out_qty))?$dataRow['inwardData']->out_qty:0?>">

    <div class="col-md-12 ">					
        <div class="row">	
            <div class="col-md-12 error out_form_error"></div>
            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" min="<?=(!empty($dataRow['postData']['minDate']))?$dataRow['postData']['minDate']:$maxDate?>" max="<?=$maxDate?>" value="<?=$maxDate?>" required >
            </div>
            <div class="col-md-3 form-group">
                <label for="out_qty">Ok Qty.</label>
                <input type="number" name="out_qty" id="out_qty" class="form-control numericOnly countWeightOut req" placeholder="Enter Quantity" data-col="w_pcs" value="<?=(!empty($dataRow['inwardData']->out_qty))?$dataRow['inwardData']->out_qty:0?>" min="0" readonly />                
            </div>
            <div class="col-md-3 form-group">
                <label for="w_pcs">Weight/Pcs.</label>
                <input type="number" name="w_pcs" id="w_pcs" class="form-control floatOnly countWeightOut" min="0" data-col="w_pcs" value="<?=(!empty($dataRow['inwardData']->w_pcs))?$dataRow['inwardData']->w_pcs:0?>" />
                
            </div>
            <div class="col-md-3 form-group">
                <label for="total_weight">Total Weight</label>
                <input type="number" name="total_weight" id="total_weight" class="form-control floatOnly countWeightOut" min="0" data-col="total_weight" value="<?=(!empty($dataRow['inwardData']->total_weight))?$dataRow['inwardData']->total_weight:0?>" />	
            </div>
            <!-- <div class="col-md-2 form-group ptime">
                <label for="production_time">Prod. Time(HH:MM)</label>
                <input type="text" name="production_time" id="production_time" class="form-control inputmask-hhmm ptime" value="00:00">
            </div> -->            
        </div>

        <div class="row">
            <div class="col-md-2 form-group">
                <label for="rejection_qty">Rejected Qty.</label>
                <input type="number" name="rejection_qty" id="rejection_qty" class="form-control numericOnly countOutQty req" placeholder="Enter Quantity" data-col="w_pcs" value="<?=(!empty($dataRow['inwardData']->rejection_qty))?$dataRow['inwardData']->rejection_qty:0?>" min="0" />

                <input type="hidden" id="production_rejection_qty" value="<?=(!empty($dataRow['inwardData']->rejection_qty))?$dataRow['inwardData']->rejection_qty:0?>">
            </div>            
            <div class="col-md-2 form-group">
                <label for="rejection_reason">Rejection Reason</label>
                <select name="rejection_reason" id="rejection_reason" class="form-control single-select req">
                    <?=$dataRow['rrOptions']?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="rejection_stage">Rejection Belong To</label>
                <select name="rejection_stage" id="rejection_stage" class="form-control single-select req">
                    <?=$dataRow['processOptions']?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="rejection_remark">Rejection Remark</label>
                <input type="text" name="rejection_remark" id="rejection_remark" class="form-control  " value="<?=(!empty($dataRow['inwardData']->rejection_remark))?$dataRow['inwardData']->rejection_remark:""?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="rework_qty">Rework Qty.</label>
                <input type="number" name="rework_qty" id="rework_qty" class="form-control numericOnly countOutQty req" placeholder="Enter Quantity" data-col="w_pcs" value="<?=(!empty($dataRow['inwardData']->rework_qty))?$dataRow['inwardData']->rework_qty:0?>" min="0" />

                <input type="hidden" id="production_rework_qty" value="<?=(!empty($dataRow['inwardData']->rework_qty))?$dataRow['inwardData']->rework_qty:0?>">
            </div>            	
            <div class="col-md-2 form-group">
                <label for="rework_reason">Rework Reason</label>
                <select name="rework_reason" id="rework_reason" class="form-control single-select req">
                    <?=$dataRow['reworkOptions']?>
                </select>
            </div>        
            <div class="col-md-2 form-group">
                <label for="rework_process_id">Rework Belong To <span class="text-danger">*</span></label>
                <!-- <select id="rework_process" data-input_id="rework_process_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?=$dataRow['rewProcess']?>
                </select>
                
                <input type="hidden" name="rework_process_id" id="rework_process_id" class="req" value="<?=$dataRow['selectedRewProcess']?>" /> -->
                <select name="rework_process_id" id="rework_process_id" class="form-control single-select req">
                    <?=$dataRow['rewProcess']?>
                </select>
                <div class="error rework_process_id"></div>
            </div>    
            <div class="col-md-6 form-group">
                <label for="rework_remark">Rework Remark</label>
                <input type="text" name="rework_remark" id="rework_remark" class="form-control" value="<?=(!empty($dataRow['inwardData']->rework_remark))?$dataRow['inwardData']->rework_remark:""?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 form-group remarkDiv">
                <label for="remark" style="width:100%;">Remark<strong class="error totalPT text-primary float-right"></strong></label>
                <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow['inwardData']->remark))?$dataRow['inwardData']->remark:""?>">
            </div>
            <div class="col-md-2 form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-primary waves-effect waves-light btn-block save-form" onclick="saveInsTrans(this.form);"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>

<div class="col-md-12 mt-10">
    <div class="error item_stock mb-3"></div>
    <div class="table-responsive">
        <table id="inspectionTrans" class="table table-bordered align-items-center" style="width: 100%;">
            <thead class="thead-info">
                <tr class="text-center">
                    <th style="width:5%;">#</th>
                    <th style="width:15%;">Date</th>
                    <th>OK Qty.</th>
                    <th>UD Qty.</th>
                    <th>Rej. Qty.</th>
                    <th>Rew. Qty.</th>
                    <th>Prod. Time</th>
                    <th>Shift</th>
                    <th>Operator</th>
                    <th>Machine</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="inspectionTransData">
                <?=$dataRow['transData']['htmlData']?>
            </tbody>
        </table>
    </div>
</div>
    
