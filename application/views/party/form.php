<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:$party_category; ?>" />
			<input type="hidden" name="disc_per" class="form-control floatOnly" value="<?=(!empty($dataRow->disc_per))?$dataRow->disc_per:""?>" />

            <div class="col-md-4 form-group">
                <label for="party_name">Company Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>

            <?php 
				$pcategory = (!empty($dataRow->party_category))?$dataRow->party_category:$party_category;
			?>
            <div class="col-md-2 form-group">
                <label for="party_code">Party Code</label>
                <input type="text" name="party_code" class="form-control" value="<?=(!empty($dataRow->party_code)) ? $dataRow->party_code : "" ?>" />
            </div>


            <div class="col-md-2 form-group">
                <label for="party_phone">Party Phone</label>
                <input type="number" name="party_phone" class="form-control numericOnly" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_type">Party Type</label>
                <select name="party_type" id="party_type" class="form-control">
                    <option value="1" <?=(!empty($dataRow->party_type) && $dataRow->party_type == 1)?"selected":""?>>Current</option>
                    <option value="2" <?=(!empty($dataRow->party_type) && $dataRow->party_type == 2)?"selected":""?>>Lead</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="vendor_code">Vendor Code</label>
                <input type="text" name="vendor_code" class="form-control" value="<?=(!empty($dataRow->vendor_code)) ? $dataRow->vendor_code : "" ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>

            <div class="col-md-2 form-group">
                <label for="party_mobile">Contact No.</label>
                <input type="text" name="party_mobile" class="form-control req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div>
			<?php 
				$pcategory = (!empty($dataRow->party_category))?$dataRow->party_category:$party_category;
				if($pcategory == 3){ 
			?>
			<div class="col-md-3 form-group">
                <label for="supplied_types">Supplied Types</label>
                <select name="supplied_types" id="supplied_types" class="form-control single-select req" >
					<option value="">Supplied Types</option>
					<?php
						foreach($suppliedTypes as $types):
							$selected = (!empty($dataRow->supplied_types) && $dataRow->supplied_types == $types)?"selected":"";
							echo '<option value="'.$types.'" '.$selected.'>'.str_replace(',',' & ',$types).'</option>';
						endforeach;
					?>
				</select>
				<input type="hidden" name="vendor_type" id="vendor_type" value="Manufacture" />
            </div>
			<?php }if($pcategory == 1){ ?>
            
            <div class="col-md-3 form-group">
                <label for="sales_executive">Sales Executive</label>
                <select name="sales_executive" id="sales_executive" class="form-control single-select" >
					<option value="">Sales Executive</option>
					<?php
						foreach($salesExecutives as $row):
							$selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?"selected":"";
							echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
						endforeach;
					?>
				</select>
				<input type="hidden" name="supplied_types" id="supplied_types" value="Goods" />
				<input type="hidden" name="vendor_type" id="vendor_type" value="Manufacture" />
            </div>
			<?php } if($pcategory == 2){ ?>
            <div class="col-md-3 form-group">
                <label for="vendor_type">Vendor Type</label>
                <select name="vendor_type" id="vendor_type" class="form-control single-select" >
					<?php
						foreach($vendorTypes as $row):
							$selected = (!empty($dataRow->vendor_type) && $dataRow->vendor_type == $row)?"selected":"";
							echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
						endforeach;
					?>
				</select>
				<input type="hidden" name="supplied_types" id="supplied_types" value="Goods" />
            </div>
			<?php } ?>
			<div class="col-md-3 form-group">
                <label for="party_pan">Party PAN</label>
                <input type="text" name="party_pan" class="form-control" value="<?=(!empty($dataRow->party_pan))?$dataRow->party_pan:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="gstin">Party GSTIN</label>
                <input type="text" name="gstin" class="form-control req" value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""; ?>" />
            </div>	
            <div class="col-md-2 form-group">
                <label for="credit_days">Credit Days</label>
                <input type="number" name="credit_days" class="form-control numericOnly" value="<?=(!empty($dataRow->credit_days))?$dataRow->credit_days:""?>" />
            </div>		
            

            <div class="col-md-2 form-group">
                <label for="business_budget">Bus. Budget/Year</label>
                <input type="text" name="business_budget" class="form-control floatOnly" value="<?=(!empty($dataRow->business_budget))?$dataRow->business_budget:""?>" />
            </div>
			
            <div class="col-md-2 form-group">
                <label for="currency">Currency</label>
                <select name="currency" id="currency" class="form-control single-select" tabindex="-1">
                    <option value="">Select Currency</option>
                    <?php $i=1; foreach($currencyData as $row):
                        $selected = (!empty($dataRow->currency) && $dataRow->currency == $row->currency)?"selected":"";
						if(empty($dataRow->currency) && $row->currency == "INR"){$selected = "selected";}
                    ?>
                        <option value="<?=$row->currency?>" <?=$selected?>><?=$row->currency?> [<?=$row->code2000?> - <?=$row->currency_name?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="opening_balance">Opening Balance</label>
                <div class="input-group">
                    <select name="balance_type" id="balance_type" class="form-control" >
                        <option value="1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "1")?"selected":""?>>Credit</option>
                        <option value="-1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "-1")?"selected":""?>>Debit</option>
                    </select>
                    <input type="number" name="opening_balance" class="form-control floatOnly" value="<?=(!empty($dataRow->opening_balance))?abs($dataRow->opening_balance):""?>" />
                </div>
            </div>
            
            <div class="col-md-3 form-group">
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

            <div class="col-md-3 form-group">
                <label for="state_id">Select State</label>
                <select name="state_id" id="state_id" class="form-control single-select req" tabindex="-1">
                    <?php if(empty($dataRow->id)): ?>
                        <option value="">Select State</option>
                    <?php else: echo $dataRow->state; endif;?>
                </select>
                <input type="hidden" id="statename" name="statename" value="" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="city_id">Select City</label>
                <select name="city_id" id="city_id" class="form-control single-select req" tabindex="-1">
                    <?php if(empty($dataRow->id)): ?>
                        <option value="">Select City</option>
                    <?php else: echo $dataRow->city; endif;?>
                </select>
                <input type="hidden" id="ctname" name="ctname" value="" />
            </div>
            <?php if($pcategory == 2){ ?>
            <div class="col-md-3 form-group">
                <label for="processSelect">Production Process</label>
                <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                    foreach ($processDataList as $row) :
                        $selected = (!empty($dataRow->process_id) && (in_array($row->id,explode(',', $dataRow->process_id)))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id) ? $dataRow->process_id :"")?>" />
            </div> <?php } ?>
            <div class="col-md-<?=($pcategory == 2)?"6":"9"?> form-group">
                <label for="party_address">Address</label>
                <textarea name="party_address" class="form-control" rows="1"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_pincode">Address Pincode</label>
                <input type="text" name="party_pincode" class="form-control req" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />
            </div>
            
            <div class="col-md-9 form-group">
                <label for="delivery_address">Delivery Address</label>
                <textarea name="delivery_address" class="form-control" rows="1"><?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="delivery_pincode">Delivery Pincode</label>
                <input type="text" name="delivery_pincode" class="form-control" value="<?=(!empty($dataRow->delivery_pincode))?$dataRow->delivery_pincode:""?>" />
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
    
    $(document).on('keyup','#partyCode',function(){
        var jjicode = addLeadingZero(parseInt($('#partyCode').val()),4);
        jjicode = jjicode.toString();
        var l = jjicode.length;
        if(l > 4){jjicode = jjicode.substring(0, l-1);}
        $('#partyCode').val(jjicode);
    });
    $('#partyCode').focus(function() { $(this).select(); } );
});

function addLeadingZero(value,max) {
  str = value.toString();
  return str.length < max ? addLeadingZero("0" + str, max) : str;
}
</script>