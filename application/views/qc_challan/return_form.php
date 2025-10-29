<div class="col-md-12">
    <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
        <tr class=""> 
            <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Item Name</th>
            <th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;"><?=$dataRow->item_name?></th>
        </tr>
    </table>
</div>
<hr>
<form>
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>" />
        <input type="hidden" name="challan_id" value="<?=(!empty($dataRow->challan_id))?$dataRow->challan_id:''?>" />
        <input type="hidden" name="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:''?>" />
        <input type="hidden" name="from_location" value="<?=(!empty($dataRow->from_location))?$dataRow->from_location:''?>" />

        <div class="col-md-3 form-group">
            <label for="receive_at">Receive Date</label>
            <input type="date" name="receive_at" id="receive_at" class="form-control req" value="<?=date("Y-m-d")?>">
        </div> 
        <div class="col-md-3 form-group">
            <label for="in_ch_no">In Challan No</label>
            <input type="text" name="in_ch_no" id="in_ch_no" class="form-control" value="">
        </div>

        <div class="col-md-4 form-group">
            <label for="to_location">Receive Location</label>
            <select name="to_location" id="to_location" class="form-control single-select">
                <option value="">Select Location</option>
                <?php
                    foreach ($locationList as $row) :
                        echo '<option value="' . $row->id . '">[' .$row->store_name. '] '.$row->location.'</option>';
                    endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-2 form-group">
            <label for="">&nbsp;</label>
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="saveReturnChallan(this.form)"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</form>    