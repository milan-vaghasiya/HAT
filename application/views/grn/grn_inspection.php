<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="">
            <div class="col-md-12 form-group">
                <label for="item_id">Product Name</label>
                <select name="item_id" id="item_id" class="form-control single-select req" >
                    <option value="">Select Product</option>
                    <?php
                        foreach($productData as $row):
                            echo '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
                
            </div>
            <div class="col-md-4 form-group">
                <label for="pending_qty">Pending Inspe. Qty.</label>
                <input type="text" id="pending_qty" class="form-control" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="type">Inspection Type</label>
                <select name="type" id="type" class="form-control req">
                    <option value="1">Ok</option>
                    <option value="2">Rejection</option>
                    <option value="3">Scrape</option>
                </select>
                
            </div>
            <div class="col-md-4 form-group">
                <label for="qty">Qty.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly req" placeholder="Enter Qty." value="0">
                
            </div>
        </div>
    </div>
</form>