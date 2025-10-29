<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>" />

            
            <div class="col-md-6 form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" value="<?=(!empty($dataRow->bank_name))?$dataRow->bank_name:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="account_no">Account No</label>
                <input type="text" name="account_no" class="form-control" value="<?=(!empty($dataRow->account_no))?$dataRow->account_no:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="ifsc_code">Ifsc Code</label>
                <input type="text" name="ifsc_code" class="form-control" value="<?=(!empty($dataRow->ifsc_code))?$dataRow->ifsc_code:""?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="salary_basis">Salary Basis</label>
                <select name="salary_basis" id="salary_basis" class="form-control req">
                    <option value="M" <?=(!empty($dataRow->salary_basis) && $dataRow->salary_basis == 'M')?"selected":""?>>Monthly</option>
                    <option value="H" <?=(!empty($dataRow->salary_basis) && $dataRow->salary_basis == 'H')?"selected":""?>>Hourly</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="travel_by">Travel By</label>
                <select name="travel_by" id="travel_by" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->travel_by) && $dataRow->travel_by == '0')?"selected":""?>>Self</option>
                    <option value="1" <?=(!empty($dataRow->travel_by) && $dataRow->travel_by == '1')?"selected":""?>>Company</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="rent_paid_type">Rent Paid Type</label>
                <select name="rent_paid_type" id="rent_paid_type" class="form-control req">
                    <option value="0" <?=(!empty($dataRow->rent_paid_type) && $dataRow->rent_paid_type == '0')?"selected":""?>>By Emp</option>
                    <option value="1" <?=(!empty($dataRow->rent_paid_type) && $dataRow->rent_paid_type == '1')?"selected":""?>>By Company</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="fare_per_day">Fare Per Day</label>
                <input type="number" name="fare_per_day" class="form-control" value="<?=(!empty($dataRow->fare_per_day))?$dataRow->fare_per_day:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pf_no">Pf No</label>
                <input type="text" name="pf_no" class="form-control" value="<?=(!empty($dataRow->pf_no))?$dataRow->pf_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="basic_salary">Wage/Basic Salary</label>
                <input type="text" name="basic_salary" id="basic_salary" class="form-control ptCount floatOnly req" value="<?=(!empty($dataRow->basic_salary))?floatVal($dataRow->basic_salary):"0"?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pf">PF(%)</label>
                <input type="text" name="pf" id="pf" class="form-control ptCount floatOnly" value="<?=(!empty($dataRow->pf))?floatVal($dataRow->pf):"0"?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="da">Dearness Allowance</label>
                <input type="text" name="da" id="da" class="form-control ptCount floatOnly" value="<?=(!empty($dataRow->da))?floatVal($dataRow->da):"0"?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="hra">HRA(%)</label>
                <input type="text" name="hra" id="hra" class="form-control ptCount floatOnly" value="<?=(!empty($dataRow->hra))?floatVal($dataRow->hra):"0"?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="ta">Special Allowances</label>
                <input type="text" name="ta" id="ta" class="form-control ptCount floatOnly" value="<?=(!empty($dataRow->ta))?floatVal($dataRow->ta):"0"?>" />    
            </div>
            <div class="col-md-4 form-group">
                <label for="other_deduction">Other Deduction</label>
                <input type="text" name="other_deduction" id="other_deduction" class="form-control ptCount floatOnly" value="<?=(!empty($dataRow->other_deduction))?floatVal($dataRow->other_deduction):"0"?>" />    
            </div>
            <!-- <div class="col-md-3 form-group">
                <label for="oa">Other Allowance</label>
                <input type="number" name="oa" class="form-control floatOnly" value="<?=(!empty($dataRow->oa))?floatVal($dataRow->oa):"0"?>" />    
            </div> -->
       
            <div class="col-md-4 form-group">
                <label for="prof_tax">Professional Tax</label>
                <input type="text" name="prof_tax" id="prof_tax" class="form-control floatOnly" value="<?=(!empty($dataRow->prof_tax))?floatVal($dataRow->prof_tax):"0"?>" readonly />    
            </div>
            <div class="col-md-4 form-group">
                <label for="hra_amount">HRA Amount</label>
                <input type="text" name="hra_amount" id="hra_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->hra_amount))?floatVal($dataRow->hra_amount):"0"?>" readonly />    
            </div>
            <div class="col-md-4 form-group">
                <label for="pf_amount">PF Amount</label>
                <input type="text" name="pf_amount" id="pf_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->pf_amount))?floatVal($dataRow->pf_amount):"0"?>" readonly />    
            </div>
            <div class="col-md-4 form-group">
                <label for="net_pay">Net Payable</label>
                <input type="text" name="net_pay" id="net_pay" class="form-control floatOnly" value="<?=(!empty($dataRow->net_pay))?floatVal($dataRow->net_pay):"0"?>" readonly />    
            </div>
        </div>
    </div>
</form>  
<script>
    $(document).ready(function(){
	    $(document).on('keyup change','.ptCount',function(){
            var basicSalary = $('#basic_salary').val();
            var da = $('#da').val();
            var ta = $('#ta').val();
            var other = $('#other_deduction').val();
            var hra = $('#hra').val();
            var pf = $('#pf').val();

            //Total Salary
            var totalSalary = parseFloat(basicSalary) + parseFloat(da) + parseFloat(ta) - parseFloat(other);
            
            //Pf Ammount / HRA
            var pf_amount = ((parseFloat(totalSalary) * parseFloat(pf)) / 100); 
            var hra_amount = ((parseFloat(totalSalary) * parseFloat(hra)) / 100);

            //Total Payble
            var netSalary = (parseFloat(totalSalary) - parseFloat(pf_amount) + parseFloat(hra_amount));

            $('#pf_amount').val(pf_amount);
            $('#hra_amount').val(hra_amount);

            // prof tax
            var prof_tax = 0;
            if(netSalary == 8000){ $('#prof_tax').val(100); prof_tax = 100; }
            else if(netSalary >= 8001 && netSalary <= 9999){ $('#prof_tax').val(150); prof_tax = 150; } 
            else if(netSalary >= 10000){ $('#prof_tax').val(200); prof_tax = 200; } 
            else { $('#prof_tax').val(0); prof_tax = 0; }
            
            var totalnetPay = parseFloat(netSalary) - parseFloat(prof_tax);
            $('#net_pay').val(totalnetPay);
        });
    });
</script>