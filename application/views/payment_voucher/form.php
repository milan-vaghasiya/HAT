<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-4 form-group">

                <label for="trans_no">Voucher No.</label>
                <div class="input-group mb-3">
                    <input type="text" name="trans_prefix" id="trans_prefix" class="form-control req" value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" readonly />
                    <input type="text" name="trans_no" id="trans_no" class="form-control col-md-4" value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $nextTransNo ?>" readonly />
                </div>

            </div>

            <div class="col-md-4 form-group">
                <label for="trans_date">Voucher Date</label>
                <input type="date" class="form-control" name="trans_date" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="entry_type">Voucher Type</label>
                <select name="entry_type" id="entry_type" class="form-control single-select" >
                    <option value="15" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 15)?"selected":"" ;?>>Receive</option>
                    <option value="16" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 16)?"selected":"" ;?>>Paid</option>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label>Doc. No.</label>
                <input type="text" class="form-control" id="doc_no" name="doc_no" value="<?= (!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""; ?>">
            </div>

            <div class="col-md-4 form-group">
                <label>Doc. Date</label>
                <input type="date" class="form-control" id="doc_date" name="doc_date" value="<?= (!empty($dataRow->doc_date)) ? $dataRow->doc_date : date("Y-m-d"); ?>">
            </div>

            <div class="col-md-4 form-group">
                <label>Party Name</label>
                <select name="opp_acc_id" id="opp_acc_id" class="form-control single-select">
                    <option value="">Select Party</option>
                    <?php
                        foreach($partyData as $row):
                            $selected = ($row->id == $dataRow->opp_acc_id) ? "selected":"";                            
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';                            
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label>Ledger Name</label>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control single-select">
                <option value="">Select Ledger</option>
                    <?php
                        foreach($ledgerData as $row):
                            $selected = ($row->id == $dataRow->vou_acc_id) ? "selected":"";                            
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label>Referance</label>
                <small class="float-right">Amount : <span id="ref_amt"><?=(!empty($ref_amount))?$ref_amount:"0.00"?></span></small>
                <select id="ref_data" data-input_id="ref_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?=(!empty($optionsHtml))?$optionsHtml:""?>
                </select>
                <input type="hidden" name="ref_id" id="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : "" ?>" />
                <input type="hidden" name="paid_amount" id="paid_amount" value="<?= (!empty($dataRow->paid_amount)) ? $dataRow->paid_amount : "0" ?>">
                <div class="error ref_id"></div>
            </div>

            <div class="col-md-4 form-group">
                <label>Payment Mode</label>
                <select name="trans_mode" id="trans_mode" class="form-control single-select">
                    <option value="">Select Payment Mode</option>
                    <?php
                        foreach($paymentMode as $row):
                            $selected = (!empty($dataRow->trans_mode) && $row == $dataRow->trans_mode) ? "selected":"";
                            echo '<option value="'.$row.'" '.$selected.'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-4 form-group">
                <label>Amount</label>
                <input type="number" name="net_amount" id="net_amount" class="form-control" value="<?= (!empty($dataRow->net_amount)) ? $dataRow->net_amount : ""; ?>">
            </div>

            <div class="col-md-8 form-group">
                <label>Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : ""; ?>">
            </div>
         <div>
    </div>

</form>

