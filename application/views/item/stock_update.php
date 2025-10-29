<div class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <h5 class="text-dark"><span id="itemName"></span></h5>
        </div>
        <hr>
        <input type="hidden" id="item_id" value="" />
        <div class="col-md-4 form-group">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?=date("Y-m-d")?>" max="<?=date("Y-m-d")?>" />
        </div>
        <div class="col-md-5 form-group">
            <label for="qty">Quantity</label>
            <div class="input-group">
                <select name="type" id="type" class="form-control">
                    <option value="+">(+) Add</option>
                    <option value="-">(-) Reduce</option>
                </select>
                <input type="number" id="qty" class="form-control floatOnly req" />
            </div>            
        </div>
        <div class="col-md-3 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30 save-form saveStock" ><i class="fa fa-plus"></i> Add Stock</button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="stockTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="stockData">
                <?=$stockTransData?>
            </tbody>
        </table>
    </div>
</div>