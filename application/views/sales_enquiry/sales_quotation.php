<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <label for="party_name">Customer Name : <span id="party_name"></span></label>
            </div>
            <div class="col-md-3">
                <label for="enquiry_no">Enquiry No. : <span id="enquiry_no"></span></label>
            </div>
            <div class="col-md-3">
                <label for="enquiry_date">Enquiry Date : <span id="enquiry_date"></span></label>
            </div>
        </div><hr>
		<div class="row">
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="enq_id" id="enq_id" value="" />
			<div class="col-md-3">
				<label for="trans_no">Quote No.</label>
				<div class="input-group mb-3">
					<input type="text" name="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
					<input type="text" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$nextTransNo?>" readonly />
				</div>
			</div>

			<div class="col-md-3">
				<label for="trans_date">Quotation Date</label>
				<input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />	
			</div>
			<div class="col-md-3 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="contact_no">Contact Number</label>
                <input type="text" name="contact_no" id="contact_no" class="form-control req" value="<?=(!empty($dataRow->contact_no))?$dataRow->contact_no:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="contact_email">Contact Email</label>
                <input type="text" name="contact_email" id="contact_email" class="form-control req" value="<?=(!empty($dataRow->contact_email))?$dataRow->contact_email:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="party_phone">Party Phone</label>
                <input type="text" name="party_phone" id="party_phone" class="form-control req" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="party_email">Party Email</label>
                <input type="text" name="party_email" id="party_email" class="form-control req" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""?>" />
            </div>
			<div class="col-md-3">
				<label for="ref_by">Referance By</label>
				<input type="text" id="ref_by" name="ref_by" class=" form-control" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>" />	
			</div>
			<div class="col-md-9">
				<label for="party_address">Address</label>
				<input type="text" id="party_address" name="party_address" class=" form-control" value="<?=(!empty($dataRow->party_address))?$dataRow->party_address:""?>" />	
			</div>
			<div class="col-md-3">
				<label for="party_pincode">Pincode</label>
				<input type="text" id="party_pincode" name="party_pincode" class=" form-control" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />	
			</div>
        </div>
        <hr>
        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:5%;">#</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody id="enquiryData">
                    <?php if(!empty($enquiryItems)): echo $enquiryItems; else:?>
                    <tr><td colspan="4" class="text-center">No data available in table</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>