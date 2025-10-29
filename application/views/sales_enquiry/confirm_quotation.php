<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12"><label for="party_name">Customer Name : <span id="party_name"></span></label></div>
            <div class="col-md-6"><label for="enquiry_no">Quotation No. : <span id="quote_no"></span></label></div>
            <div class="col-md-6"><label for="enquiry_date">Quotation Date : <span id="quotation_date"></span></label></div>
			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="quote_id" id="quote_id" value="" />
			<input type="hidden" name="customer_id" id="customer_id" value="" />
			<div class="col-md-4">
				<label for="confirm_date">Confirm Date</label>
				<input type="date" id="confirm_date" name="confirm_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->confirm_date))?$dataRow->confirm_date:date("Y-m-d")?>" />	
			</div>
        </div><hr>
        <div class="error item_name_error"></div>
        <div class="table-responsive">
            <table class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:5%;">#</th>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Quoted Price</th>
                        <th>Confirm Price</th>
                    </tr>
                </thead>
                <tbody id="enquiryData">
                    <?php if(!empty($quotationItems)): echo $quotationItems; else:?>
                    <tr><td colspan="4" class="text-center">No data available in table</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>