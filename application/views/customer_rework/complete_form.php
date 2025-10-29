<form>
        <div class="col-lg-12 col-xlg-12 col-md-12">
            <table class="table table-bordered-dark">
                <tr>
                    <th>Debit no.</th>
                    <td><?= ($dataRow->trans_number) ?></td>
                    <th>Invoice No </th>
                    <td><?= $dataRow->inv_no ?></td>
                    <th>Customer </th>
                    <td><?= $dataRow->party_name ?></td>
                </tr>
                <tr>
                    <th>Product</th>
                    <td><?= (!empty($dataRow->full_name) ? $dataRow->full_name : '' ) ?></td>
                    <th>Quantity </th>
                    <td><?= floatVal($dataRow->qty) ?></td>
                    <th>Remark </th>
                    <td><?= $dataRow->remark ?></td>
                </tr>
            </table>
        </div>
    <div class="col-md-12">
        
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="trans_number" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:""; ?>" />
            <input type="hidden" name="qty" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""; ?>" />
            <input type="hidden" name="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""; ?>" />
            <input type="hidden" name="batch_no" value="<?=(!empty($dataRow->batch_no))?$dataRow->batch_no:""; ?>" />
            <input type="hidden" name="location_id" value="<?=(!empty($dataRow->location_id))?$dataRow->location_id:""; ?>" />
            <input type="hidden" name="tc_no" value="<?=(!empty($dataRow->tc_no))?$dataRow->tc_no:""; ?>" />
            <input type="hidden" name="material_grade" value="<?=(!empty($dataRow->material_grade))?$dataRow->material_grade:""; ?>" />
            
            <div class="col-md-3 form-group">
                <label for="ok_qty">Ok Qty</label>
                <input type="text" id="ok_qty" name="ok_qty" class=" form-control req"  value="" />	
			</div>
            <div class="col-md-3 form-group">
                <label for="rej_qty">Rej. Qty</label>
                <input type="text" id="rej_qty" name="rej_qty" class=" form-control req"  value="" />	
			</div>
            <div class="col-md-6 form-group">
                <label for="rej_reason">Rej. Reason</label>
                <input type="text" id="rej_reason" name="rej_reason" class=" form-control req"  value="" />	
			</div>
        </div>
        
    </div>
</form>

  