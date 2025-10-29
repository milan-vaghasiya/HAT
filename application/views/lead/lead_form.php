<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="party_category" id="party_category" value="1" />
            <input type="hidden" name="party_type" id="party_type" value="2" />
                
            <div class="col-md-4 form-group">
                <label for="party_name">Company Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="party_code">Party Code</label>
                <input type="text" name="party_code" class="form-control" value="<?=(!empty($dataRow->party_code)) ? $dataRow->party_code : "" ?>" />
            </div>
            <div class="col-md-5 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="party_mobile">Contact No.</label>
                <input type="number" name="party_mobile" class="form-control numericOnly req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_phone">Party Phone</label>
                <input type="number" name="party_phone" class="form-control numericOnly" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="business_budget">Business Budget/Year</label>
                <input type="text" name="business_budget" class="form-control floatOnly" value="<?=(!empty($dataRow->business_budget))?$dataRow->business_budget:""?>" />
            </div>            
            <div class="col-md-4 form-group">
                <label for="country_id">Select Country</label>
                <select name="country_id" id="country_id" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Country</option>
                    <?php $i=1; foreach($countryData as $Country):
                        $selected = (!empty($dataRow->country_id) && $dataRow->country_id == $Country->id)?"selected":"";
                    ?>
                        <option value="<?=$Country->id?>" <?=$selected?>><?=$Country->name?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label for="state_id">Select State</label>
                <select name="state_id" id="state_id" class="form-control single-select req" tabindex="-1">
                    <?php if(empty($dataRow->id)): ?>
                        <option value="">Select State</option>
                    <?php else: echo $dataRow->state; endif;?>
                </select>
                <input type="hidden" id="statename" name="statename" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="city_id">Select City</label>
                <select name="city_id" id="city_id" class="form-control single-select req" tabindex="-1">
                    <?php if(empty($dataRow->id)): ?>
                        <option value="">Select City</option>
                    <?php else: echo $dataRow->city; endif;?>
                </select>
				
                <input type="hidden" id="ctname" name="ctname" value="" />
            </div>
            
            <div class="col-md-9 form-group">
                <label for="party_address">Address</label>
                <textarea name="party_address" class="form-control req" rows="1"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_pincode">Pincode</label>
                <input type="text" name="party_pincode" class="form-control req" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','#city_idc',function(){
        $('#ctname').val($(this).val());
    });
    $(document).on('keyup','#state_idc',function(){
        $('#statename').val($(this).val());
    });
});
</script>