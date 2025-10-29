<div class="col-md-12">
    <h5 class="text-dark"><span id="itemName"></span></h5>
</div>
<hr>
<form>
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="item_id" id="item_id" value="" />
        <input type="hidden" name="ref_type" value="-1" />
        <div class="col-md-4 form-group">
            <label for="location_id">Store Location</label>
            <select name="location_id" id="location_id" class="form-control model-select2 req">
                <option value="">Select Location</option>
                <?php
                    foreach($locationData as $lData):
                        echo '<optgroup label="'.$lData['store_name'].'">';
                        foreach($lData['location'] as $row):
                            echo '<option value="'.$row->id.'">'.$row->location.' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="batch_no">Batch No.</label>
            <input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
        </div>
        <div class="col-md-2 form-group">
            <label for="qty">Quantity</label>
            <input type="number" name="qty" id="qty" class="form-control floatOnly req" />           
        </div>
        <div class="col-md-3 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30 save-form" onclick="saveOpening(this.form);"><i class="fa fa-plus"></i> Add Stock</button>
        </div>
    </div>
</form>

<hr>
<div class="col-md-12">
    <div class="table-responsive">
        <table id="openingStockTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Store Location</th>
                    <th>Batch No.</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="openingStockData">
                <?=$openingStockData['htmlData']?>
            </tbody>
        </table>
    </div>
</div>