<form autocomplete="off">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
             <div class="col-md-6">
                <label for="date"> Date</label>
                <input type="date" name="date" id="date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" class="form-control single-select req">
                    <option value="">Select Employee</option>
                    <?php
                        foreach($empData as $row):
                            $selected = (!empty($dataRow->sales_executive) && $row->id == $dataRow->sales_executive)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label>Ledger Name</label>
                <select name="vou_acc_id" id="vou_acc_id" class="form-control single-select">
                <option value="">Select Ledger</option>
                    <?php
                        foreach($ledgerData as $row):
                            $selected = ($row->id == $dataRow->party_id) ? "selected":"";                            
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="amount">Loan Amount</label>
                <input type="number" name="amount" id="amount" class="form-control numericOnly req calEMI" value="<?=(!empty($dataRow->net_amount))?$dataRow->net_amount:""?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="total_emi">Total EMI</label>
                <input type="number" name="total_emi" id="total_emi" class="form-control numericOnly req calEMI" value="<?=(!empty($dataRow->other_gst))?$dataRow->other_gst:""?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="emi_amount">EMI Amount</label>
                <input type="number" name="emi_amount" id="emi_amount" class="form-control req calEMI" value="<?=(!empty($dataRow->other_amount))?$dataRow->other_amount:""?>" />
            </div>
            
            <div class="col-md-12 form-group">
                <label for="reason">Reason</label>
                <textarea name="reason" class="form-control req" style="resize:none;" ><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
    
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change',".calEMI",function(){   
        
        if($(this).prop('id') == 'emi_amount'){$("#total_emi").val('');}
        if($(this).prop('id') == 'total_emi'){$("#emi_amount").val('');}
        
        var amount = parseFloat($("#amount").val());
		var total_emi = parseFloat($("#total_emi").val());
		var emi_amount = parseFloat($("#emi_amount").val());
        var emiAmt = 0;var totalEmi = 0;
        if((total_emi > 0 || emi_amount > 0) && amount > 0)
        {
            if(emi_amount > 0){total_emi = parseFloat((parseFloat(amount) / parseFloat(emi_amount)).toFixed());}
            if(total_emi > 0){emi_amount = parseFloat((parseFloat(amount) / parseFloat(total_emi))).toFixed(2); }
        }
        $("#total_emi").val(total_emi);
        $("#emi_amount").val(emi_amount);
    });

});

</script>