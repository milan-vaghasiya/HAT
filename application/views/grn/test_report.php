<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="grn_id" id="grn_id" value="<?=(!empty($dataRow->grn_id))?$dataRow->grn_id:$grn_id; ?>"/>
            
            <div class="col-md-4 form-group">
                <label for="name_of_agency">Name Of Agency</label>
                <input type="text" name="name_of_agency" class="form-control req" value="<?=(!empty($dataRow->name_of_agency))?$dataRow->name_of_agency:""?>" />
            </div>
            <div class="col-md-8 form-group">
                <label for="test_description">Test Description</label>
                <input type="text" name="test_description" class="form-control req" value="<?=(!empty($dataRow->test_description))?$dataRow->test_description:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="sample_qty">Sample Qty</label>
                <input type="number" name="sample_qty" class="form-control floatOnly req" value="<?=(!empty($dataRow->sample_qty))?floatVal($dataRow->sample_qty):""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="test_report_no">Test Report No</label>
                <input type="text" name="test_report_no" class="form-control" value="<?=(!empty($dataRow->test_report_no))?$dataRow->test_report_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="test_remark">Test Remark</label>
                <input type="text" name="test_remark" class="form-control" value="<?=(!empty($dataRow->test_remark))?$dataRow->test_remark:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="test_result">Test Result</label>
                <input type="text" name="test_result" class="form-control" value="<?=(!empty($dataRow->test_result))?$dataRow->test_result:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="inspector_name">Inspector Name</label>
                <input type="text" name="inspector_name" class="form-control" value="<?=(!empty($dataRow->inspector_name))?$dataRow->inspector_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="mill_tc">Mill TC</label>
                <input type="text" name="mill_tc" class="form-control" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""?>" />
            </div>
        </div>
    </div>
</form>  