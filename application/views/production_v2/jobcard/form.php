<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
			<div class="col-md-4 form-group">
				<label for="job_no">Job Card No.</label>
				<input type="text" id="job_no" class="form-control req" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):getPrefixNumber($jobPrefix,$jobNo)?>" readonly />
			</div>
			<input type="hidden" name="heat_no" class="form-control" value="">			
			<div class="col-md-4 form-group">
				<label for="party_id">Customer</label>
				<select name="party_id" id="party_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Customer</option>
					<option value="0" <?=(!empty($dataRow->id) && $dataRow->party_id == 0)?"selected":""?>>Self Stock</option>
					<?php 
						foreach($customerData as $row):
							$selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->party_id)?"selected":"";
							echo '<option value="'.$row->party_id.'" '.$selected.'>'.$row->party_code.'</option>';
						endforeach;
					?>
				</select>				
			</div>
			<div class="col-md-4 form-group">
				<label for="sales_order_id">Sales Order No.</label>
				<select name="sales_order_id" id="sales_order_id" class="form-control single-select">
					<option value="">Select Order No.</option>
					<?php
						if(!empty($dataRow)):
							foreach($customerSalesOrder as $row):
								$selected = (!empty($dataRow->sales_order_id) && $dataRow->sales_order_id == $row->id)?"selected":"";
								echo '<option value="'.$row->id.'" '.$selected.'>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</option>';
							endforeach;
						endif;
					?>
				</select>
			</div>
			<div class="col-md-4 form-group">
				<label for="job_date">Job Card Date</label>
				<input type="date" id="job_date" name="job_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->job_date))?$dataRow->job_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
			</div>
			<div class="col-md-4 form-group">
				<label for="item_id">Product Code</label>
				<select name="product_id" id="item_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Product</option>
					<?php 
						if(!empty($dataRow->id)):
							echo $productData['htmlData'];
						else:
							foreach($productData as $row):
								$selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?"selected":"";
								echo '<option value="'.$row->id.'" data-delivery_date="'.date("Y-m-d").'" data-order_type="0" '.$selected.'>'.$row->item_code.'</option>';
							endforeach;
						endif;
					?>
				</select>				
			</div>
			
			<div class="col-md-4 form-group">
				<label for="job_category">Job Work Type</label>
				<select name="job_category" id="job_category" class="form-control req">
					<option value="0" data-job_no="<?=(!empty($dataRow))?getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):getPrefixNumber($jobPrefix,$jobNo)?>" <?=(!empty($dataRow) && $dataRow->job_category == 0)?"selected":""?> <?=(!empty($dataRow->sales_order_id) && $dataRow->job_category!=0)?"disabled='disabled'":""?>>Manufacturing</option>
					<option value="1" data-job_no="<?=(!empty($dataRow))?getPrefixNumber($dataRow->job_prefix,$dataRow->job_no):getPrefixNumber($jobwPrefix,$jobwNo)?>" <?=(!empty($dataRow) && $dataRow->job_category == 1)?"selected":""?> <?=(!empty($dataRow->sales_order_id) && $dataRow->job_category!=1)?"disabled='disabled'":""?>>Job Work</option>
				</select>
			</div>
			<input type="hidden" name="pre_disp_inspection" id="pre_disp_inspection" value="0">
			<div class="col-md-4 form-group">
				<label for="qty">Quatity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />				
			</div>

			<div class="col-md-4 form-group">
				<label for="delivery_date">Delivery Date</label>
				<input type="date" id="delivery_date" name="delivery_date" class="form-control" placeholder="dd-mm-yyyy" min="<?=(!empty($dataRow->job_date))?$dataRow->job_date:date("Y-m-d")?>" value="<?=(!empty($dataRow->delivery_date))?$dataRow->delivery_date:date("Y-m-d")?>" />
			</div>
			
			<div class="col-md-4 form-group">
				<label for="is_npd">Is NPD?</label>
				<select name="is_npd" id="is_npd" class="form-control">
					<option value="0" <?=(!empty($dataRow->id) && $dataRow->is_npd == 0)?"selected":""?>>No</option>
					<option value="1" <?=(!empty($dataRow->id) && $dataRow->is_npd == 1)?"selected":""?>>Yes</option>
				</select>
			</div>
			
			<div class="col-md-12 mt-3 form-group" id="processDiv">
				<label for="process">Select Process</label>
				<div id="processData">
					<?php
						if(!empty($dataRow->process)):
							echo $productProcessAndRaw['htmlData'];
						endif;
					?>
				</div>
				<div class="error process"></div>
			</div>
			<div class="col-md-12 mt-3 form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" />
			</div>
        </div>
    </div>
</form>
