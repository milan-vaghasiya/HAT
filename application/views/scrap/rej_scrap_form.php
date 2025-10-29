<form id="scrap_form">

	<div class="col-md-12 col-6">
		<div class="row">
			<div class="col-md-6 form-group">
				<label for="ref_date">Date</label>
				<input type="date" id="ref_date" name="ref_date" class="form-control" value="<?= date("Y-m-d") ?>" max="<?= date("Y-m-d") ?>" readonly />

			</div>
			<div class="col-md-6 form-group">
				<label for="job_card_id">Jobcard</label>
				<input type="hidden" id="item_id" name="item_id">
				<select id="job_card_id" name="job_card_id" class="form-control single-select1 model-select2  req">
					<option value="">Select Jobcard</option>
					<?php foreach ($locationList as $row) {
						if ($row->qty > 0) {
					?>
							<option value="<?= $row->job_card_id ?>" data-item_id="<?= $row->product_id ?>"><?= getPrefixNumber($row->job_prefix, $row->job_no). ' [ ' . $row->item_name . ' ]' ?></option>
					<?php }
					} ?>
				</select>
			</div>
			<div class="col-md-12 form-group">
				<div class="error general_error"></div>
				<div class="error qty"></div>
				<div class="scrollable" style="height:60vh;">
					<table id='reportTable' class="table table-bordered ">
						<thead class="thead-info" id="theadData">
							<tr>
								<th>#</th>
								<th>Batch</th>
								<th>Process</th>
								<th>Rej Reason</th>
								<th>Rej Stage</th>
								<th>Rej Belongs To</th>
								<th>Rej Qty</th>
								<th>Scrap Qty</th>
								<th>OK Qty</th>
							</tr>
						</thead>
						<tbody id="batchData">
							<tr>
								<td class="text-center" colspan="9">No Data Found.</td>
							</tr>
						</tbody>

					</table>
				</div>
			</div>
		</div>

	</div>
</form>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>

<script>
	$(document).ready(function() {
		$('.model-select2').select2({
			dropdownParent: $('.model-select2').parent()
		});

		$("#job_card_id").change(function() {
			var location_id = $(this).val();
			var item_id = $("#job_card_id :selected").data('item_id');

			$("#item_id").val(item_id);
			$("#batchData").html("");
			if (location_id != '') {
				$.ajax({
					url: base_url + controller + '/getRejectionBatchList',
					type: 'post',
					data: {
						location_id: location_id
					},
					dataType: 'json',
					success: function(data) {

						$("#batchData").html("");
						$("#batchData").html(data.tbody);
					}
				});
			}
		});
		$(document).on('keyup change', ".batchQty", function() {

			var id = $(this).data('rowid');
			var pending_qty = $(this).data('pending_qty');
			var scrap_qty = $("#scrapQty"+id).val();
			var ok_qty = $("#okQty"+id).val();
			$(".batch_qty" + id).html("");
			var totalQty=parseFloat(scrap_qty)+parseFloat(ok_qty);
			if (totalQty > parseFloat(pending_qty)) {
				$(this).val(0);
			}
		});
	});
</script>