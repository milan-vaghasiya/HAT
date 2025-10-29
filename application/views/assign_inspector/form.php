<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=$dataRow->id?>">
            <div class="col-md-6 form-group">
                <label for="request_date">Req. Date</label>
                <input type="text" id="request_date" class="form-control" value="<?=date("d-m-Y",strtotime($dataRow->request_date))?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="job_card_id">Job Card No.</label>
                <input type="text" id="job_card_id" class="form-control" value="<?=getPrefixNumber($dataRow->job_prefix,$dataRow->job_no)?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="item_code">Product</label>
                <input type="text" id="item_code" class="form-control" value="<?=$dataRow->item_code?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="process_id">Process Name</label>
                <input type="text" id="process_id" class="form-control" value="<?=$dataRow->process_name?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="machine_id">Machine No.</label>
                <input type="text" id="machine_id" class="form-control" value="<?="[ ".$dataRow->machine_code." ] ".$dataRow->machine_name?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="setter_id">Setter Name</label>
                <input type="text" id="setter_id" class="form-control" value="<?=$dataRow->setter_name?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="assign_date">Assign Date</label>
                <input type="date" name="assign_date" id="assign_date" class="form-control" min="<?=date("Y-m-d",strtotime($dataRow->request_date))?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" value="<?=(!empty($dataRow->assign_date))?$dataRow->assign_date:$maxDate?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="qci_id">Inspector Name</label>
                <select name="qci_id" id="qci_id" class="form-control single-select req">
                    <option value="">Select Inspector</option>
                    <?php
                        foreach($employeeData as $row):
                            $selected = (!empty($dataRow->qci_id) && $dataRow->qci_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>[ '.$row->emp_code.' ] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>