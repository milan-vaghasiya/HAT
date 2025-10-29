<form>
    <div class="col-md-12">
        <div class="row">
            <div id="hiddenInputs">
                <input type="hidden" name="id" id="id" value="<?=$salaryData->id?>">                
                <input type="hidden" name="row_index" id="row_index" value="<?=$salaryData->sr_no?>">
                <input type='hidden' name='structure_id' id="structure_id" value="<?=$salaryData->structure_id?>">
                <input type="hidden" name="emp_id" id="emp_id" value="<?=$salaryData->emp_id?>">
                <input type="hidden" name="emp_code" id="emp_code" value="<?=$salaryData->emp_code?>">
                <input type="hidden" name="emp_type" id="emp_type" value="<?=$salaryData->emp_type?>">
                <input type="hidden" name="salary_basis" id="salary_basis" value="<?=$salaryData->salary_basis?>">
                <input type="hidden" name="salary_code" id="salary_code" value="<?=$salaryData->salary_code?>">
                <input type="hidden" name="wage" id="wage" value="<?=$salaryData->wage?>">
                <input type="hidden" name="r_hr" id="r_hr" value="<?=$salaryData->r_hr?>"> 
                <input type="hidden" name="pf_applicable" id="pf_applicable" value="<?=$salaryData->pf_applicable?>"> 
                
                <input type="hidden" name="advance_deduction" id="advance_deduction" value="<?=$salaryData->advance_deduction?>">
                <input type="hidden" name="org_advance_deduction" id="org_advance_deduction" value="<?=$salaryData->org_advance_deduction?>">
                <input type="hidden" name="emi_amount" id="emi_amount" value="<?=$salaryData->emi_amount?>">
                <input type="hidden" name="org_emi_amount" id="org_emi_amount" value="<?=$salaryData->org_emi_amount?>">
                <input type="hidden" name="sal_diff" id="sal_diff" value="<?=$salaryData->sal_diff?>">
            </div>

            <div class="<?=($salaryData->salary_basis == "H")?"col-md-6":"col-md-8"?> form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" id="emp_name" class="form-control" value="<?=$salaryData->emp_name?>" readonly>
            </div>

            <?php
                if($salaryData->salary_basis == "H"):
            ?>
                <div class="col-md-3 form-group">
                    <label for="wage">Wage</label>
                    <input type="text" class="form-control" value="<?=$salaryData->wage?>" readonly>
                </div>
            <?php
                endif;
            ?>

            <div class="col-md-3 form-group" style="<?=($salaryData->salary_basis != "H")?"display:none;":""?>">
                <label for="total_wh">TWH</label>
                <input type="text" name="total_wh" id="total_wh" class="form-control" value="<?=floatVal($salaryData->total_wh)?>" readonly>
            </div>

            <div class="col-md-4 form-group" style="<?=($salaryData->salary_basis == "H")?"display:none;":""?>">
                <label for="tot">TOT</label>
                <input type="text" name="tot" id="tot" class="form-control" value="<?=floatVal($salaryData->tot)?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="working_days">Working Days</label>
                <input type="text" name="working_days" id="working_days" class="form-control" value="<?=floatVal($salaryData->working_days)?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="present_days">Present Days</label>
                <input type="text" name="present_days" id="present_days" class="form-control" value="<?=floatVal($salaryData->present_days)?>" readonly>
            </div>

            <div class="col-md-4 form-group">
                <label for="absent_days">Absent Days</label>
                <input type="text" name="absent_days" id="absent_days" class="form-control" value="<?=floatVal($salaryData->absent_days)?>" readonly>
            </div>

            <hr>

            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-borderd">
                        <thead class="thead-info">
                            <tr>
                                <th>Earnings</th>
                                <th style="width:150px;">Actual Amount</th>
                                <th style="width:150px;">On Paper Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($salaryData->earning_data)):
                                    foreach($salaryData->earning_data as $key => $row):
                                        $row = (object) $row;
                                        echo '<tr>
                                            <td>
                                                '.$row->head_name.'
                                                <input type="hidden" name="earning_data['.$key.'][head_name]" value="'.$row->head_name.'">
                                                <input type="hidden" name="earning_data['.$key.'][system_code]" value="'.$row->system_code.'">
                                                <input type="hidden" name="earning_data['.$key.'][cal_method]" value="'.$row->cal_method.'" id="'.($row->system_code).'_cal_method_'.$key.'">
                                                <input type="hidden" name="earning_data['.$key.'][cal_value]" value="'.$row->cal_value.'" id="'.($row->system_code).'_cal_value_'.$key.'">
                                            </td>
                                            <td>
                                                <input type="text" name="earning_data['.$key.'][org_amount]" class="form-control numericOnly calculateSalary org_'.($row->system_code).' org_earnings" value="'.$row->org_amount.'" '.((!in_array($row->system_code,["ca","sp"] ))?"readonly":"").'>
                                            </td>
                                            <td>
                                                <input type="text" name="earning_data['.$key.'][amount]" class="form-control numericOnly calculateSalary '.($row->system_code).' earnings" value="'.$row->amount.'" '.((!in_array($row->system_code,["ca","sp"] ))?"readonly":"").'>
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="3">No data available for Earnings</td></tr>';
                                endif;
                            ?>
                        </tbody>
                        <thead class="thead-info">
                            <tr>
                                <th>Gross Salary</th>
                                <th>
                                    <input type="text" name="org_total_earning" id="org_total_earning" value="<?=$salaryData->org_total_earning?>" class="form-control" readonly>
                                </th>
                                <th>
                                    <input type="text" name="total_earning" id="total_earning" value="<?=$salaryData->total_earning?>" class="form-control" readonly>
                                </th>
                            </tr>
                            <tr>
                                <th>Deductions</th>
                                <th style="width:150px;">Actual Amount</th>
                                <th style="width:150px;">On Paper Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($salaryData->deduction_data)):
                                    foreach($salaryData->deduction_data as $key => $row):
                                        $row = (object) $row;
                                        $readonly = (!in_array($row->system_code,['ccl','ccd']))?"readonly":"";
                                        echo '<tr>
                                            <td>
                                                '.$row->head_name.'
                                                <input type="hidden" name="deduction_data['.$key.'][head_name]" value="'.$row->head_name.'">
                                                <input type="hidden" name="deduction_data['.$key.'][system_code]" value="'.$row->system_code.'">
                                                <input type="hidden" name="deduction_data['.$key.'][cal_method]" value="'.$row->cal_method.'" id="'.($row->system_code).'_cal_method_'.$key.'">
                                                <input type="hidden" name="deduction_data['.$key.'][cal_value]" value="'.$row->cal_value.'" id="'.($row->system_code).'_cal_value_'.$key.'">
                                            </td>
                                            <td>
                                                <input type="text" name="deduction_data['.$key.'][org_amount]" class="form-control numericOnly calculateSalary org_'.($row->system_code).' org_deductions" data-key="'.$key.'" value="'.$row->org_amount.'" '.$readonly.'>
                                            </td>
                                            <td>
                                                <input type="text" name="deduction_data['.$key.'][amount]" class="form-control numericOnly calculateSalary '.($row->system_code).' deductions" data-key="'.$key.'" value="'.$row->amount.'" '.$readonly.'>
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="3">No data available for Deductions</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>
                    <table class="table table-borderd">
                        <thead class="thead-info">
                            <tr>
                                <th>Advance Salary</th>
                                <th style="width:150px;">Amount</th>
                                <th style="width:150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if(!empty($salaryData->advance_data)):
                                    $i=1;
                                    foreach($salaryData->advance_data as $key => $row):
                                        $row = (object)$row;
                                        
                                        echo '<tr>
                                            <td>
                                                '.date("d-m-Y",strtotime($row->entry_date)).'
                                                <input type="hidden" name="advance_data['.$key.'][id]" value="'.$row->id.'">
                                                <input type="hidden" name="advance_data['.$key.'][entry_date]" value="'.$row->entry_date.'">
                                                <input type="hidden" name="advance_data['.$key.'][payment_mode]" value="'.$row->payment_mode.'">
                                            </td>
                                            <td>
                                                <input type="text" name="advance_data['.$key.'][org_amount]" class="form-control numericOnly calculateSalary org_deductions orgAdvanceSalary" value="'.$row->org_amount.'" '.(($row->payment_mode != "CS")?"readonly":"").'>
                                            </td>
                                            <td>
                                                <input type="text" name="advance_data['.$key.'][amount]" class="form-control numericOnly calculateSalary deductions advanceSalary" value="'.$row->amount.'" '.(($row->payment_mode == "CS")?"readonly":"").'>
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="3">No data available</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>

                    <table class="table table-borderd">
                        <thead class="thead-info">
                            <tr>
                                <th>Loan No.</th>
                                <th style="width:150px;">Emi Amount</th>
                                <th style="width:150px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                if(!empty($salaryData->loan_data)):
                                    foreach($salaryData->loan_data as $key => $row):
                                        $row = (object)$row;
                                        echo '<tr>
                                            <td>
                                                '.$row->loan_no.' <br><small>(Pending Amount : <span id="pendingAmount'.$row->id.'">'.($row->loan_amount - ($row->amount + $row->org_amount)).'</span>)</small>
                                                <input type="hidden" name="loan_data['.$key.'][id]" value="'.$row->id.'">
                                                <input type="hidden" name="loan_data['.$key.'][payment_mode]" value="'.$row->payment_mode.'">
                                                <input type="hidden" name="loan_data['.$key.'][loan_no]" value="'.$row->loan_no.'">                                                
                                            </td>
                                            <td>
                                                <input type="text" name="loan_data['.$key.'][org_amount]" class="form-control numericOnly calculateSalary org_deductions orgEmiAmounts" data-id="'.$row->id.'" value="'.$row->org_amount.'" '.(($row->payment_mode != "CS")?"readonly":"").'>
                                                <div class="error org_emi_amount_'.$row->id.'"></div>
                                            </td>
                                            <td>
                                                <input type="text" name="loan_data['.$key.'][amount]" class="form-control numericOnly calculateSalary deductions emiAmounts" data-id="'.$row->id.'" value="'.$row->amount.'" '.(($row->payment_mode == "CS")?"readonly":"").'>
                                                <div class="error emi_amount_'.$row->id.'"></div>

                                                <input type="hidden" name="loan_data['.$key.'][loan_amount]" id="loan_amount_'.$row->id.'" value="'.$row->loan_amount.'">
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="3">No data available</td></tr>';
                                endif;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th>Gross Deduction</th>
                                <th>
                                    <input type="text" name="org_total_deduction" id="org_total_deduction" class="form-control" value="<?=$salaryData->org_total_deduction?>" readonly>
                                </th>
                                <th>
                                    <input type="text" name="total_deduction" id="total_deduction" class="form-control" value="<?=$salaryData->total_deduction?>" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table table-borderd">
                        <thead class="thead-info">
                            <tr>
                                <th>Net Amount</th>
                                <th  style="width:150px;">
                                    <input type="text" name="actual_sal" id="actual_sal" class="form-control" value="<?=$salaryData->actual_sal?>" readonly>                
                                </th>
                                <th  style="width:150px;">
                                    <input type="text" name="net_salary" id="net_salary" class="form-control" value="<?=$salaryData->net_salary?>" readonly>
                                </th>
                            </tr>
                            <tr>
                                <th>Salary Diff</th>
                                <th colspan="2">
                                    <input type="text" name="sal_diff" id="sal_diff" class="form-control" value="<?=$salaryData->sal_diff?>" readonly>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>