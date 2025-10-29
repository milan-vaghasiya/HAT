<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-4 form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" name="bank_name" class="form-control req" value="<?=(!empty($dataRow->bank_name))?$dataRow->bank_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="branch_name">Branch Name</label>
                <input type="text" name="branch_name" class="form-control req" value="<?=(!empty($dataRow->branch_name))?$dataRow->branch_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="ifsc_code">IFSC Code</label>
                <input type="number" name="ifsc_code" id="ifsc_code" class="form-control floatOnly"  value="<?=(!empty($dataRow->ifsc_code))?$dataRow->ifsc_code:""?>" >
            </div>
           
            <div class="col-md-12 form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control"><?=(!empty($dataRow->address))?$dataRow->address:""?></textarea>
            </div>
        </div>
    </div>
</form>