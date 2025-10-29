<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-6 form-group">
                <label for="comapny_name">Comapny Name</label>
                <input type="text" name="comapny_name" class="form-control req" value="<?=(!empty($dataRow->comapny_name))?$dataRow->comapny_name:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="contact_number">Contact No.</label>
                <input type="number" name="contact_number" class="form-control numericOnly req" value="<?=(!empty($dataRow->contact_number))?$dataRow->contact_number:""?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="contact_number2">Contact No. 2</label>
                <input type="number" name="contact_number2" class="form-control numericOnly" value="<?=(!empty($dataRow->contact_number2))?$dataRow->contact_number2:""?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="email">Email</label>
                <input type="text" name="email" class="form-control" value="<?=(!empty($dataRow->email))?$dataRow->email:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="service">Service</label>
                <input type="text" name="service" class="form-control" value="<?=(!empty($dataRow->service))?$dataRow->service:""; ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="1"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>