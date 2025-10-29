<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="item_id" value="<?=(!empty($dataRow->id))?$dataRow->id:$itemId?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : 1; ?>" />

            <div class="col-md-12">
                <div class="table-responsive">
					<table id='reportTable' class="table table-bordered">
						<thead class="thead-info" id="theadData">
							<tr>
								<th>#</th>
								<th>Store</th>
								<th>Location</th>
								<th>Batch</th>
								<th>Current Stock</th>
							</tr>  
						</thead>
						<tbody id="tbodyData"></tbody>
					</table>
				</div>				
            </div>
        </div>
    </div>
</form>