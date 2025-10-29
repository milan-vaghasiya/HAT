<table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
	<tr class="">
		<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Row Material</th>
		<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?= $reqMaterial->item_name ?></th>

		<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Material Grade</th>
		<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="material_grade"><?= $reqMaterial->material_grade ?></th>
	</tr>
</table>

<form id="scrap_form">
	<div class="col-md-12 mt-3">
		<input type="hidden" id="ref_id" name="ref_id" value="<?= $job_card_id ?>" />
		<input type="hidden" id="ref_type" name="ref_type" value="18" />
		<input type="hidden" id="trans_type" name="trans_type" value="1" />
		<input type="hidden" id="id" name="id" value="" />

	</div>
	<div class="col-md-12 col-6">
		<div class="row">



			<div class="col-md-6 form-group">
				<label for="ref_date">Date</label>
				<input type="date" id="ref_date" name="ref_date" class="form-control" value="<?= date("Y-m-d") ?>"  max="<?=date("Y-m-d")?>" readonly />

			</div>
			<div class="col-md-6 form-group">
				<label for="qty">Scrap Qty</label>
				<input type="tezt" id="qty" name="qty" class="form-control" value="<?= $totalScrapQty ?>" readonly />

			</div>
			<div class="col-md-6 form-group">
				<label for="location_id">Store Location</label>
				<select id="location_id" name="location_id" class="form-control single-select1 model-select2  req">
					<option value="">Select Location</option>
					<?php
					foreach ($locationList as $lData) {
					?>

						<optgroup label="<?= $lData['store_name'] ?>">
							<?php
							foreach ($lData['location'] as $row) {
							?>
								<option value="<?= $row->id ?>"><?= $row->location ?></option>
							<?php
							}

							?>
						</optgroup>
					<?php
					}
					?>
				</select>
			</div>

			<div class="col-md-6 form-group">
			<span class="dropdown float-right">
			<a class="text-primary addNewMaster float-right" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addItem/3" data-controller="items" data-class_name="itemOptions" data-form_title="Add Row Material">+ Row Material</a>
				</span>
				<label for="item_id">Item</label>
				<select id="item_id" name="item_id" class="form-control single-select itemOptions req">
					<option value="">Select Item Name</option>
					<?php
					foreach ($rawMaterial as $row) :
						echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
					endforeach;
					?>
				</select>
			</div>

		</div>

	</div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>

<script>
	$(document).ready(function() {
		$('.model-select2').select2({
			dropdownParent: $('.model-select2').parent()
		});
	});
</script>