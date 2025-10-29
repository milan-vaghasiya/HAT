<div class="col-md-12">
	<form>
        <div class="row">
            <div class="col-md-12">
                <div class="error gerenal_error"></div>
            </div>
            <input type="hidden" name="item_id" class="item_id" value="" />
            <input type="hidden" name="process_id" id="process_id" value="0">
            <input type="hidden" name="id" id="id" value="">
            <div class="col-md-4">
                <label for="kit_item_id">Raw Material Item</label>
                <select id="kit_item_id" name="kit_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($rawMaterial as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="kit_item_qty">Quantity</label>
                <input type="text" name="kit_item_qty" id="kit_item_qty" class="form-control floatOnly req" value="" min="0" />
            </div>
			<div class="col-md-3">
                <label for="used_as">Used As</label>
                <select id="used_as" name="used_as" class="form-control single-select req">
                    <option value="1">Main</option>
                    <option value="2">Alternate</option>
				</select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="saveProductKit('addProductKitItems');" ><i class="fa fa-plus"></i> Add Item</button>
            </div>
        </div>
	</form>
	<hr>
	<div class="row">
		<div class="table-responsive">
		<table id="productKit" class="table table-bordered align-items-center">
			<thead class="thead-info">
				<tr>
					<th style="width:5%;">#</th>
					<th>Item Name</th>
					<th>Qty</th>
					<th>Used AS</th>
					<th class="text-center" style="width:10%;">Action</th>
				</tr>
			</thead>
			<tbody id="kitItems">
				<?php
					if(!empty($productKitData)):
						$i=1;
						foreach($productKitData as $row):
							echo '<tr>
									<td>'.$i++.'</td>
									<td>
										'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'
									</td>
									<td>
										'.$row->qty.'
									</td>
									<td>
										'.(($row->used_as == 1)?'Main':'Alternate').'
									</td>
									<td class="text-center">
										<button type="button" onclick="deleteProductKit('.$row->id.','.$row->item_id.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
									</td>
								</tr>';
						endforeach;
					endif;
				?>
			</tbody>
		</table>
	</div>
	</div>
</div>

<script>
	
	function deleteProductKit(id,item_id){
		var send_data = { id:id,item_id:item_id };
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to delete this Record?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/deleteProductKit',
							data: send_data,
							type: "POST",
							dataType:"json",
							success:function(data)
							{
								if(data.status==0)
								{
									toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
								else
								{
									initTable(0); initMultiSelect();
									$('#kitItems').html("");
									$('#kitItems').html(data.tbody);
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
								}
							}
						});
					}
				},
				cancel: {
					btnClass: 'btn waves-effect waves-light btn-outline-secondary',
					action: function(){

					}
				}
			}
		});
	}
</script>