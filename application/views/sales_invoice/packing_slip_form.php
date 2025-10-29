<form id="packingForm" method="post" action="<?=base_url($headData->controller.'/invoice_pdf')?>" target="_blank">
    <div class="modal-body">
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <label>Wooden Box No.</label>
                    <input type="text" name="wooden_box_no" id="wooden_box_no" class="form-control req">
                </div>

                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <label>Item</label>
                    <select name="item_id" id="item_id" class="form-control select2 partyOptions req">
                        <option value="">Select Item</option>
                        <?php
                            foreach($itemData as $row){
                                $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?"selected":((!empty($orderData->item_id) && $orderData->item_id == $row->id)?"selected":"");
                                echo "<option value='".$row->item_id."' ".$selected.">".$row->item_name." [".$row->item_code."]</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <label>Qty</label>
                    <input type="text" name="qty" id="qty" class="form-control floatOnly req">
                </div>

                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-outline-success waves-effect add-item"><i class="fa fa-plus"></i> Add Item</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#item_id').select2({
            dropdownParent: $('#modal-xl')
        });
    });
</script>