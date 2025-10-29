<form>
    <div class="col-md-12">
        <div class="row">
              <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
          <div class="col-md-6 form-group">
                <label for="party_id">Party Name</label>
                <select name="party_id" id="party_id" class="form-control single-select req ">
                    <option value="0">Select Party Name</option>
                    <?php
                        foreach($partyData as $row):
                            $selected = (!empty($dataRow->party_id) && $row->id == $dataRow->party_id)?"selected":"";
                            echo '<option value="'.$row->id.'"'.  $selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
              <div class="col-md-6 form-group">
                <label for="invoice_no">Ref. No.</label>
                <select id="invoiceSelect" data-input_id="invoice_no" class="form-control jp_multiselect req" multiple="multiple">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->invoice_no == 0)?"selected":"";?>>Advance</option>;
                    <?php 
                        foreach($invData as $inv):
                            $selected = '';
                            if(!empty($dataRow->invoice_no)){
                                if (in_array($inv->id,explode(',',$dataRow->invoice_no))) {
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="'.$inv->id.'" '.$selected.'>'.getPrefixNumber($inv->trans_prefix,$inv->trans_no).'</option>';
                        endforeach; ?>
                </select>
                 <input type="hidden" name="invoice_no" id="invoice_no" value="<?=(!empty($dataRow->invoice_no))?$dataRow->invoice_no:"" ?>" />
            </div>
              <div class="col-md-6 form-group">
                <label for="tran_mode">Mode</label>
                <select name="tran_mode" id="tran_mode" class="form-control single-select req">
                    <option value="Cash" <?=(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Cash")?"selected":""?>>Cash</option>
                    <option value="Cheque" <?=(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Cheque")?"selected":""?>>Cheque</option>
                    <option value="Card" <?=(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Card")?"selected":""?>>Card</option>
                    <option value="Internet Banking" <?=(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Internet Banking")?"selected":""?>>Internet Banking</option>
                </select>
            </div>
             <div class="col-md-6 form-group">
                <label for="bank_ledger_id">Cash/Bank Name</label>
                <select name="bank_ledger_id" id="bank_ledger_id" class="form-control single-select">
                    <option value="1" <?=(!empty($dataRow->bank_ledger_id) && $dataRow->bank_ledger_id == "1")?"selected":""?>>Cash In Hand</option>
                    <option value="2" <?=(!empty($dataRow->bank_ledger_id) && $dataRow->bank_ledger_id == "2")?"selected":""?>>HDFC Bank Limited</option>
                </select>
            </div>

            <?php if(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Cheque"):?>
                <div class="col-md-6 form-group" id="chDate" style="display: block;">
            <?php else:?>
                <div class="col-md-6 form-group" id="chDate" style="display: none;">
            <?php endif;?>
                <label for="ch_date">Cheque Date</label>
                <input type="date" name="ch_date" id="ch_date" class="form-control req" value="<?=(!empty($dataRow->ch_date))?$dataRow->ch_date:"";?>">
            </div>
            <?php if(!empty($dataRow->tran_mode) && $dataRow->tran_mode == "Cheque"):?>
                <div class="col-md-6 form-group" id="chNo" style="display: block;">
            <?php else:?>
                <div class="col-md-6 form-group" id="chNo" style="display: none;">
            <?php endif;?>
                <label for="ch_no">Cheque No.</label>
                <input type="text" name="ch_no" id="ch_no" class="form-control req" value="<?=(!empty($dataRow->ch_no))?$dataRow->ch_no:"";?>">
            </div>
            
             <div class="col-md-4 form-group">
                <label for="tran_amount">Amount</label>
                <input type="text" name="tran_amount" id="tran_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->tran_amount))?$dataRow->tran_amount:"0";?>">
            </div>
             <div class="col-md-4 form-group">
                <label for="adj_amount">Adjustment Amount</label>
                <input type="text" name="adj_amount" id="adj_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->adj_amount))?$dataRow->adj_amount:"0";?>">
            </div>
             <div class="col-md-4 form-group">
                <label for="total_amount">Total Amount</label>
                <input type="text" name="total_amount" id="total_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->total_amount))?$dataRow->total_amount:"0";?>" readonly>
            </div>
              <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>