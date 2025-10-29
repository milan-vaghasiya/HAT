<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:$item_id; ?>"/>
            
            <div class="col-md-3 form-group">
                <label for="date"> Date </label>
                <input type="date" name="date" class="form-control req" value="<?=(!empty($dataRow->date))?$dataRow->date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="change_reason">Change Reason</label>
                <input type="text" name="change_reason" class="form-control req" value="<?=(!empty($dataRow->change_reason))?$dataRow->change_reason:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="description">Description</label>
                <input type="text" name="description" class="form-control req" value="<?=(!empty($dataRow->description))?$dataRow->description:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="new_rev_no">Revision No/Date</label>
                <input type="text" name="new_rev_no" class="form-control req" value="<?=(!empty($dataRow->new_rev_no))?$dataRow->new_rev_no:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="new_specs">Specification</label>
                <input type="text" name="new_specs" class="form-control req" value="<?=(!empty($dataRow->new_specs))?$dataRow->new_specs:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="feasibility_status">Feasibility Change</label>
                <select name="feasibility_status" id="feasibility_status" class="form-control req">
                    <option value="No" <?=(!empty($dataRow->feasibility_status) && $dataRow->feasibility_status == 'No')?"selected":""?>>No</option>
                    <option value="Yes" <?=(!empty($dataRow->feasibility_status) && $dataRow->feasibility_status == 'Yes')?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="feasibilty_remark">Feasibilty Remark</label>
                <input type="text" name="feasibilty_remark" class="form-control req" value="<?=(!empty($dataRow->feasibilty_remark))?$dataRow->feasibilty_remark:""?>" />    
            </div>
            <div class="col-md-3 form-group">
                <label for="fg_stock">Fg Stock</label>
                <input type="text" name="fg_stock" class="form-control req" value="<?=(!empty($dataRow->fg_stock))?$dataRow->fg_stock:""?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="cost_effect">Effect Of Cost</label>
                <select name="cost_effect" id="cost_effect" class="form-control req">
                    <option value="No" <?=(!empty($dataRow->cost_effect) && $dataRow->cost_effect == 'No')?"selected":""?>>No</option>
                    <option value="Yes" <?=(!empty($dataRow->cost_effect) && $dataRow->cost_effect == 'Yes')?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="cost_remark">Cost Remark</label>
                <input type="text" name="cost_remark" class="form-control req" value="<?=(!empty($dataRow->cost_remark))?$dataRow->cost_remark:""?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="auth_required">Cft Auth. Req.</label>
                <select name="auth_required" id="auth_required" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->auth_required) && $dataRow->auth_required == '0')?"selected":""?>>Not Required</option>
                    <option value="1" <?=(!empty($dataRow->auth_required) && $dataRow->auth_required == '1')?"selected":""?>>Required</option>
                </select>
            </div>
        </div>
    </div>
</form>  