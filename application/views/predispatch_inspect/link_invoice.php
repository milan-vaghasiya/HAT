<form id="linkInv"> 
    <div class="col-md-12">
        <div class="row"> 
            <input type="hidden" name="id" id="id" value="<?=(!empty($id) ? $id : '')?>" />

            <div class="col-md-12 form-group">
                <label for="trans_child_id">Item Name</label> <span class="text-danger">*</span>
                <select name="trans_child_id[]" id="trans_child_id" data-input_id="emp_sys_desc_id" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        foreach($invItemList as $row) :
                            echo '<option value="'.$row->trans_child_id.'">['.$row->trans_number.'] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>   
                <div class="error item_id"></div>   
            </div> 
        </div> 
    </div> 
</form>