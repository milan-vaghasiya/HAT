<form id="packingForm" method="post">
    <input type="hidden" name="inv_id" value="<?= $invoice_id ?? '';?>">
    <input type="hidden" name="inv_trans_id" value="">
    <input type="hidden" name="total_qty" value="">

    <div class="modal-body p-0">
        <div class="col-md-12">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <label>Wooden Box No.</label>
                    <input type="text" name="wooden_box_no" id="wooden_box_no" class="form-control floatOnly req">
                </div>

                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <label>Item</label>
                    <select name="item_id" id="item_id" class="form-control select2 partyOptions req">
                        <option value="">Select Item</option>
                        <?php
                            foreach($itemData as $row){
                                echo "<option value='".$row->item_id."' data-inv_trans_id=".$row->id." data-total_qty=".$row->qty.">".$row->item_name." (<b>Qty:</b> ".$row->qty.")</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <label>Qty</label>
                    <input type="text" name="qty" id="qty" class="form-control floatOnly req">
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-outline-success waves-effect add-invoice-item"><i class="fa fa-plus"></i> Add Item</button>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4 packingSlipTable">
            <h4>Item Details :</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Wooden Box No.</th>
                            <th class="text-center">Item</th>
                            <th class="text-left">Qty</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="itemListing">
                        <tr>
                            <td class="text-center" colspan="5">No Data Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('[name="item_id"]').select2({
            dropdownParent: $('#modal-lg')
        });
        
        $(document).on('change','[name="item_id"]',function(){
            var inv_trans_id = $('[name="item_id"] option:selected').attr('data-inv_trans_id');
            var total_qty = $('[name="item_id"] option:selected').attr('data-total_qty');
            $('[name="inv_trans_id"]').val(inv_trans_id);
            $('[name="total_qty"]').val(total_qty);
        });

        $(document).on('click','.add-invoice-item',function(){
            var postData = $('#packingForm').serialize();            
            var inv_id = $('[name="inv_id"]').val();
            $.ajax({
				url : base_url + controller + '/savePackingSlip',
				type: 'POST',
				data:postData,
				dataType:'json',
				success:function(data){
                    if(data.status === 0){
                        $(".error").html("");
                        $.each(data.message, function(key, value) {
                            $("."+key).html(value);
                        });
                    }else if(data.status == 1){
                        $('.packingSlipTable').removeClass('d-none');
                        showHtml(inv_id);
                        $('input[type="text"]').val('');
                        $('[name="item_id"] option:first').prop('selected', true).trigger('change');
                        $('[name="item_id"]').select2({
                            dropdownParent: $('#modal-lg')
                        });

                    }else if(data.status == 2){
                        toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                    }
				}
			});
        });
    });

    function trashItem(id){
        var send_data = { id:id };
        var inv_id = $('[name="inv_id"]').val();
        
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to delete this item?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/deletePackingSlipItem',
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
                                    showHtml(inv_id);
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