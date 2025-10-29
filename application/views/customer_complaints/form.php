<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="so_number"  id="so_number" value="<?=(!empty($dataRow->so_number))?$dataRow->so_number:""; ?>" >
            <div class="col-md-6 form-group">
                <label for="trans_number">Complaint No.</label>
                <div class="input-group mb-3">
                    <input type="text" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix?>" readonly />
                    <input type="text" name="trans_no" class="form-control req" value="<?=(!empty($dataRow->trans_no)) ? $dataRow->trans_no : sprintf("%03d",$nextTransNo) ?>" readonly />	
                </div>								
            </div>

            <div class="col-md-6 form-group">
                <label for="trans_date">Complaint Date</label>
                <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" />	
			</div>

            <div class="col-md-6 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control single-select partyOptions req" >
                    <option value="">Select Party</option>
                    <?php
                        foreach($customerData as $row):
                            $selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id)?"selected":((!empty($customerData->party_id) && $customerData->party_id == $row->id)?"selected":"");
                            echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
                        endforeach;
                    ?>
                </select>									
            </div>

            <div class="col-md-6 form-group">
                <label for="so_id">Ref. of Complaint</label>
                <select name="so_id" id="so_id" class="form-control single-select req" >
                    <?php
                        if (!empty($html)) { echo $html;  } 
                        else {  ?> <option value="">Select Sales Order</option>  
                    <?php } ?>
                </select>	
                
                
            </div>

            <div class="col-md-6 form-group">
                <label for="item_id">Item Description</label>
                <select name="item_id" id="item_id" class="form-control single-select req" >
                    <?php
                        if (!empty($options)) { echo $options;  } 
                        else {  ?> <option value="">Select Item</option> 
                        <?php
                            if(!empty($itemData)){
                                foreach($itemData as $row){
                                    $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id)?'selected':'';
                                    ?>
                                    <option value="<?=$row->id?>" data-so_trans_id="0" <?=$selected?>><?=$row->item_name?></option>
                                    <?php
                                }
                            } 
                         } ?>
                </select>
                <input type="hidden" name="so_trans_id" id="so_trans_id" value="<?=(!empty($dataRow->so_trans_id))?$dataRow->so_trans_id:""?>" />								
            </div>

            <div class="col-md-6 form-group">
                <label for="product_returned">Product Returned</label>
                <select name="product_returned" id="product_returned" class="form-control single-select">
                    <option value="">Select Option</option>
                    <option value="1" <?= (!empty($dataRow) && $dataRow->product_returned == 1) ? "selected" : "" ?>>No</option>
                    <option value="2" <?= (!empty($dataRow) && $dataRow->product_returned == 2) ? "selected" : "" ?>>Yes</option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="complaint">Details of Complaint</label>
                <input type="text" name="complaint" class="form-control req" value="<?=(!empty($dataRow->complaint))?$dataRow->complaint:""?>" />
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){

	$(document).on('change', '#party_id', function () {
        var partyData = $("#party_id :selected").data('row');
        $("#party_name").val(partyData.party_name);
        var party_id = $(this).val();
        if (party_id) {
            $.ajax({
                url: base_url + 'customerComplaints' + '/getPObyParty',
                data: { party_id: party_id },
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $("#so_id").html(data.htmlData);
                    $("#so_id").comboSelect();
                }
            });
        }
    });

    $(document).on('change', '#so_id', function () {
        var so_number = $("#so_id").val();
        var  soNumber = $("#so_id :selected").data('so_number');
        $("#so_number").val(soNumber);
        $.ajax({
            url: base_url + 'customerComplaints' + '/getItemList',
            data: { so_number: so_number},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $("#item_id").html(data.options);
                $("#item_id").comboSelect();
            }
        });
    });

    $(document).on('change', '#item_id', function () {
        var  so_trans_id = $("#item_id :selected").data('so_trans_id');
        $("#so_trans_id").val(so_trans_id);
    });
});
</script> 
               