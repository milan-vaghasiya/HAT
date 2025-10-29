<form>
    <div class="col-md-12">
        <div class="row">
            <div id="hiddenInputs">
                <input type="hidden" name="id" id="id" value="<?=$salaryData->id?>">                
                <input type="hidden" name="row_index" id="row_index" value="<?=$salaryData->sr_no?>">
                <input type="hidden" name="emp_id" id="emp_id" value="<?=$salaryData->emp_id?>">
                <input type="hidden" name="emp_code" id="emp_code" value="<?=$salaryData->emp_code?>">
                <input type="hidden" name="emp_type" id="emp_type" value="<?=$salaryData->emp_type?>">
                <input type="hidden" name="salary_basis" id="salary_basis" value="<?=$salaryData->salary_basis?>">
                <input type="hidden" name="salary_code" id="salary_code" value="<?=$salaryData->salary_code?>">
                <input type="hidden" name="wage" id="wage" value="<?=$salaryData->wage?>">
                <input type="hidden" name="r_hr" id="r_hr" value="<?=$salaryData->r_hr?>"> 
                <input type="hidden" name="pf_applicable" id="pf_applicable" value="<?=$salaryData->pf_applicable?>"> 
                <input type="hidden" name="pf_per" id="pf_per" value="<?=$salaryData->pf_per?>"> 
                <input type="hidden" name="earning_data" id="earning_data" value="<?=json_encode($salaryData->earning_data)?>"> 
                <input type="hidden" name="deduction_data" id="deduction_data" value="<?=json_encode($salaryData->deduction_data)?>"> 
                
                <input type="hidden" name="advance_deduction" id="advance_deduction" value="<?=$salaryData->advance_deduction?>">
                <input type="hidden" name="emi_amount" id="emi_amount" value="<?=$salaryData->emi_amount?>">
            </div>

            <div class="col-md-9 form-group">
                <label for="emp_name">Employee Name</label>
                <input type="text" name="emp_name" id="emp_name" class="form-control" value="<?=$salaryData->emp_name?>" readonly>
            </div>
			
			<div class="col-md-3 form-group">
				<label for="wage">Wage</label>
				<input type="text" class="form-control" value="<?=$salaryData->wage?>" readonly>
			</div>
			
            <div class="col-md-4 form-group">
                <label for="total_wh">TWH</label>
                <input type="text" name="total_wh" id="total_wh" class="form-control" value="<?=floatVal($salaryData->total_wh)?>" readonly>
            </div>			
            <div class="col-md-4 form-group">
                <label for="total_wh">WH</label>
                <input type="text" name="wh" id="wh" class="form-control" value="<?=floatVal($salaryData->wh)?>" readonly>
            </div>
            <div class="col-md-4 form-group">
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
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr><th colspan="2">Earnings</th></tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($salaryData->earning_data)):
                                    foreach($salaryData->earning_data as $row):
                                        $row = (object) $row;
										$readonly = (empty($row->editable)) ? "readonly" : "";
                                        echo '<tr>
												<td>'.$row->head_name.'</td>
												<td style="width:200px;">
													<input type="text" name="'.$row->field_name.'" class="form-control numericOnly calculateSalary earnings" value="'.$row->amount.'" '.$readonly.'>
												</td>
											</tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="2">No data available for Earnings</td></tr>';
                                endif;
                            ?>
                        </tbody>
                        <thead class="thead-info">
                            <tr>
                                <th>Gross Salary</th>
                                <th style="width:200px;">
                                    <input type="text" name="total_earning" id="total_earning" value="<?=$salaryData->total_earning?>" class="form-control" readonly>
                                </th>
                            </tr>
                            <tr><th colspan="2">Deductions</th></tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($salaryData->deduction_data)):
                                    foreach($salaryData->deduction_data as $key => $row):
                                        $row = (object) $row;
										$readonly = (empty($row->editable)) ? "readonly" : "";
                                        echo '<tr>
												<td>'.$row->head_name.'</td>
												<td style="width:200px;">
													<input type="text" name="'.$row->field_name.'" class="form-control numericOnly calculateSalary deductions" value="'.$row->amount.'" '.$readonly.'>
												</td>
											</tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="2">No data available for Deductions</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr><th colspan="2">Advance Salary</th></tr>
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
                                            <td style="width:200px;">
                                                <input type="text" name="advance_data['.$key.'][amount]" class="form-control numericOnly calculateSalary deductions advanceSalary" value="'.$row->amount.'" >
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="2">No data available</td></tr>';
                                endif;
                            ?>
                        </tbody>
                    </table>

                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr><th colspan="2">Loan No.</th></tr>
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
                                            <td style="width:200px;">
                                                <input type="text" name="loan_data['.$key.'][amount]" class="form-control numericOnly calculateSalary deductions emiAmounts" data-id="'.$row->id.'" value="'.$row->amount.'" '.(($row->payment_mode != "CS")?"readonly":"").'>
                                                <div class="error org_emi_amount_'.$row->id.'"></div>
                                            </td>
                                        </tr>';
                                    endforeach;
                                else:
                                    echo '<tr><td class="text-center" colspan="2">No data available</td></tr>';
                                endif;
                            ?>
                        </tbody>
                        <tfoot class="thead-info">
                            <tr>
                                <th>Gross Deduction</th>
                                <th style="width:200px;">
                                    <input type="text" name="total_deduction" id="total_deduction" class="form-control" value="<?=$salaryData->total_deduction?>" readonly>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <table class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th>Net Amount</th>
                                <th style="width:200px;">
                                    <input type="text" name="net_salary" id="net_salary" class="form-control" value="<?=$salaryData->net_salary?>" readonly>                
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>