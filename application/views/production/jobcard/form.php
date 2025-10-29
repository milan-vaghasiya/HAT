<form>
	<div class="col-md-12">
		<div class="row">
			<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
			<input type="hidden" name="error_msg" id="error_msg" value="">
			<input type="hidden" id="delivery_date" name="delivery_date" class="form-control" min="<?= (!empty($dataRow->job_date)) ? $dataRow->job_date : date("Y-m-d") ?>" value="<?= (!empty($dataRow->delivery_date)) ? $dataRow->delivery_date : date("Y-m-d") ?>" />
			<input type="hidden" name="job_prefix" value="<?=(!empty($dataRow->job_prefix))?$dataRow->job_prefix:""?>">
			<input type="hidden" name="job_no" value="<?=(!empty($dataRow->job_no))?$dataRow->job_no:""?>">
			<input type="hidden" name="is_npd" value="<?=(!empty($dataRow->is_npd))?$dataRow->is_npd:"0"?>">
			<input type="hidden" name="used_at" id="used_at" value="">
			<input type="hidden" name="handover_to" id="handover_to" value="">

			<div class="col-md-2 form-group">
				<label for="job_date">Job Card Date</label>
				<input type="date" id="job_date" name="job_date" class="form-control req" value="<?= (!empty($dataRow->job_date)) ? $dataRow->job_date : $maxDate ?>" min="<?= $startYearDate ?>" max="<?= $maxDate ?>" />
			</div>
			<div class="col-md-2 form-group">
				<label for="job_category">Job Work Type</label>
				<select name="job_category" id="job_category" class="form-control req">
					<option value="0" data-job_no="<?= (!empty($dataRow)) ? $dataRow->job_prefix. $dataRow->job_no : $jobPrefix. $jobNo ?>" <?= (!empty($dataRow) && $dataRow->job_category == 0) ? "selected" : "" ?> <?= (!empty($dataRow->sales_order_id) && $dataRow->job_category != 0) ? "disabled='disabled'" : "" ?>>Manufacturing</option>
					<option value="1" data-job_no="<?= (!empty($dataRow)) ? $dataRow->job_prefix. $dataRow->job_no : $jobwPrefix. $jobwNo ?>" <?= (!empty($dataRow) && $dataRow->job_category == 1) ? "selected" : "" ?> <?= (!empty($dataRow->sales_order_id) && $dataRow->job_category != 1) ? "disabled='disabled'" : "" ?>>Job Work</option>
				</select>
			</div>
			<div class="col-md-2 form-group">
				<label for="job_no">Job Card No.</label>
				<input type="text" id="job_no" class="form-control req" value="<?= (!empty($dataRow)) ? ($dataRow->job_prefix . $dataRow->job_no) : ($jobPrefix.'-'. $jobNo) ?>" readonly />
			</div>
			<div class="col-md-3 form-group">
				<label for="party_id">Customer</label>
				<select name="party_id" id="party_id" class="form-control single-select req" autocomplete="off">
					<option value="">Select Customer</option>
					<option value="0" <?= (!empty($dataRow->id) && $dataRow->party_id == 0) ? "selected" : "" ?>>Self Stock</option>
					<?php
    					foreach ($customerData as $row) :
    						$selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->party_id) ? "selected" : "";
    							echo '<option value="' . $row->party_id . '" ' . $selected . '>'.(!empty($row->party_code)?'[ '.$row->party_code.' ] ':'').$row->party_name.' </option>';
    					endforeach;
					?>
				</select>
			</div>
			<div class="col-md-3 form-group">
				<label for="sales_order_id">Sales Order No.</label>
				<select name="sales_order_id" id="sales_order_id" class="form-control single-select">
					<option value="">Select Order No.</option>
					<?php
					if (!empty($dataRow)) :
						foreach ($customerSalesOrder as $row) :
							$selected = (!empty($dataRow->sales_order_id) && $dataRow->sales_order_id == $row->id) ? "selected" : "";
							echo '<option value="' . $row->id . '" ' . $selected . '>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</option>';
						endforeach;
					endif;
					?>
				</select>
			</div>

			<div class="col-md-3 form-group">
				<label for="item_id">Product Code</label>
				<select name="product_id" id="item_id" class="form-control single-select1 select2 req" autocomplete="off">
					
					<?php
					if (!empty($dataRow->id)) :
						echo $productData['htmlData'];
					else :
						echo '<option value="" data-delivery_date="'.date("Y-m-d").'" data-trans_id="0" data-order_type="0">Select Product</option>';
						/*foreach ($productData as $row) :
							$selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : "";
				            $item_name = (!empty($row->item_code)? "[".$row->item_code."] ".$row->item_name:$row->item_name);
							echo '<option value="' . $row->id . '" data-delivery_date="' . date("Y-m-d") . '" data-trans_id="0" data-order_type="0" ' . $selected . '>' . $item_name . '</option>';
						endforeach;*/
					endif;
					?>
				</select>
				<input type="hidden" name="so_trans_id" id="so_trans_id" value="<?=(!empty($dataRow->so_trans_id))?$dataRow->so_trans_id:"0"?>">
			</div>
			<div class="col-md-2 form-group">
				<label for="qty">Quatity</label>
				<input type="text" name="qty" id="qty" class="form-control numericOnly countWeight req" min="0" placeholder="Enter Qty." value="<?= (!empty($dataRow->qty)) ? $dataRow->qty : "" ?>" />
			</div>
			<div class="col-md-7 form-group">
				<label for="remark">Remark</label>
				<input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
			</div>

			<div class="col-md-12 form-group" id="processDiv">
				<label for="process">Select Process</label>
				<div id="processData">
					<?php
					if(!empty($dataRow->process)) :
						echo $productProcessAndRaw['htmlData'];
					endif;
					?>
				</div>
				<div class="error process"></div>
			</div>
			<div class="error error_msg"></div>
		</div>
		<hr>
		
		<div class="row">
			<div class="col-md-12">
				<h4>Raw Material : </h4>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<table  class="table table-bordered align-items-center">
							<thead class="thead-info">
								<tr>
									<th></th>
									<th>Item Name</th>
									<th>Used As</th>
									<th>Bom Qty (PCS)</th>
									<th>Required Qty (PCS)</th>
									<th>Batch No</th>
								</tr>
							</thead>
							<tbody id="requestItems">
								<?=!empty($productProcessAndRaw['BomTable'])?$productProcessAndRaw['BomTable']:''?>
							</tbody>
						<?php
							
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>