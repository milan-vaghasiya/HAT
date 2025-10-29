<?php $this->load->view('includes/header'); ?>
<style>
	.countSalary{width:100px;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Payroll Entry</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePayRoll">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="dept_id">Department</label>
                                    <select name="dept_id" id="dept_id" class="form-control single-select req">
                                        <option value="0">ALL Department</option>
                                        <?php
                                            foreach($deptRows as $row):
                                                $selected = (!empty($salaryData) && $salaryData[0]->dept_id == $row->id)?"selected":"";
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error dept_id"></div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control single-select req">
                                        <option value="">Select Month</option>
                                        <?php
                                            foreach($monthList as $row):
                                                $selected = (!empty($salaryData) && $salaryData[0]->month == $row)?"selected":((!empty($month) && $row == $month)?"selected":"");
                                                echo '<option value="'.$row.'" '.$selected.'>'.date("F-Y",strtotime($row)).'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error month"></div>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="ledger_id">Select Ledger</label>
                                    <select name="ledger_id" id="ledger_id" class="form-control single-select req" tabindex="-1">
                                        <option value="1" selected>CASH IN HAND</option>
                                    </select>
                                    <div class="error ledger_id"></div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn waves-effect waves-light btn-success btn-block loadSalaryData"  > Load</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="empSalary" class="table table-striped jpExcelTable">
                                                <thead class="thead-info" id="empSalaryHead">
                                                    <tr>
                                                        <th style="width:30px;">Emp Code</th>
                                                        <th>Employee Name</th>
                                                        <th style="width:100px;">Total Days</th>
                                                        <th style="width:100px;">Present <br> Days</th>
                                                        <th style="width:100px;">Absent <br> Days</th>
                                                        <?php
                                                            $headCount = 12;
                                                            if(!empty($salaryData)):    
                                                                foreach($earningHeads as $row):
                                                                    echo '<th>'.$row->head_name.'</th>';
                                                                    $headCount++;
                                                                endforeach;
                                                            endif;
                                                        ?>
                                                        <th style="width:100px;">Gross Salary</th>
                                                        <?php
                                                            if(!empty($salaryData)):   
                                                                foreach($deductionHeads as $row):
                                                                    echo '<th>'.$row->head_name.'</th>';
                                                                    $headCount++;
                                                                endforeach;
                                                            endif;
                                                        ?>
                                                        <th style="width:100px;">Advance</th>
                                                        <th style="width:100px;">Loan</th>
                                                        <th style="width:100px;">Net Salary</th>
                                                        <th style="width:100px;">Actual Salary</th>
                                                        <th style="width:100px;">Difference</th>
                                                        <th style="width:60px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="empSalaryData">
                                                    <?php
                                                        $sr_no = 1;
                                                        if(!empty($salaryData)):
                                                            $html = '';$etd = '';$betd = '';$dtd = '';$bdtd = '';
                                                            foreach($salaryData as $row):
                                                                $empEarnData = json_decode($row->earning_data);
                                                                $etd = '';
                                                                foreach($earningHeads as $erow):
                                                                    $amount = (!empty($empEarnData->{$erow->id}->amount))?$empEarnData->{$erow->id}->amount:0;
                                                                    $etd .= '<td>'.$amount.'</td>';
                                                                endforeach;

                                                                $dtd = '';
                                                                $empDeductData = json_decode($row->deduction_data);
                                                                foreach($deductionHeads as $drow):
                                                                    $amount = (!empty($empDeductData->{$drow->id}->amount))?$empDeductData->{$drow->id}->amount:0;
                                                                    $dtd .= '<td>'.$amount.'</td>';
                                                                endforeach;

                                                                $adsData = (!empty($row->advance_data))?json_decode($row->advance_data):array();
                                                                $a=0;$adsHtml='';
                                                                foreach($adsData as $adsRow):
                                                                    $adsHtml .= "<input type='hidden' name='salary_data[".$sr_no."][advance_data][".$a."][id]' value='".$adsRow->id."'>";
                                                                    $adsHtml .= "<input type='hidden' name='salary_data[".$sr_no."][advance_data][".$a."][entry_date]' value='".$adsRow->entry_date."'>";
                                                                    $adsHtml .= "<input type='hidden' name='salary_data[".$sr_no."][advance_data][".$a."][payment_mode]' value='".$adsRow->payment_mode."'>";
                                                                    $adsHtml .= "<input type='hidden' name='salary_data[".$sr_no."][advance_data][".$a."][amount]' value='".$adsRow->amount."'>";
                                                                    $adsHtml .= "<input type='hidden' name='salary_data[".$sr_no."][advance_data][".$a."][org_amount]' value='".$adsRow->org_amount."'>";
                                                                    $a++;
                                                                endforeach;

                                                                $l=0;$loanEmi=0;$pendingLoan=0;$emiAmount=0;$loanHtml = '';
                                                                $loanData = (!empty($row->loan_data))?json_decode($row->loan_data):array();
                                                                if(!empty($loanData)):
                                                                    foreach($loanData as $loanRow):
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][id]' value='".$loanRow->id."'>";
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][payment_mode]' value='".$loanRow->payment_mode."'>";
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][loan_no]' value='".$loanRow->loan_no."'>";
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][amount]' value='".$loanRow->amount."'>";
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][org_amount]' value='".$loanRow->org_amount."'>";
                                                                        $loanHtml .= "<input type='hidden' name='salary_data[".$sr_no."][loan_data][".$l."][loan_amount]' value='".$loanRow->loan_amount."'>";
                                                                        $l++;
                                                                    endforeach;
                                                                endif;

                                                                $salaryCode = '"'.$row->salary_code.'"';
                                                                $editButton = "<button type='button' class='btn btn-outline-warning' title='Edit' onclick='Edit(".$sr_no.",".$salaryCode.");'><i class='ti-pencil-alt'></i></button>";
                                                                $html .= "<tr id='".$sr_no."'>
                                                                    <td>".$row->emp_code."</td>
                                                                    <td>
                                                                        ".$row->emp_name."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][id]' value='".$row->id."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][structure_id]' value='".$row->structure_id."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][emp_id]' value='".$row->emp_id."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][emp_name]' value='".$row->emp_name."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][emp_type]' value='".$row->emp_type."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][pf_applicable]' value='".$row->pf_applicable."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][salary_code]' value='".$row->salary_code."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][salary_basis]' value='".$row->salary_basis."'>
                                                                    </td>
                                                                    <td>
                                                                        ".$row->working_days."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][total_wh]' value='".$row->total_wh."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][tot]' value='".$row->tot."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][wage]' value='".$row->wage."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][r_hr]' value='".$row->r_hr."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][actual_sal]' value='".$row->actual_sal."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][sal_diff]' value='".$row->sal_diff."'>
                                                                    </td>                                                                    
                                                                    <td>
                                                                        ".$row->present_days."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][present_days]' value='".$row->present_days."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][working_days]' value='".$row->working_days."'>
                                                                    </td>
                                                                    <td>
                                                                        ".$row->absent_days."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][absent_days]' value='".$row->absent_days."'>
                                                                    </td>
                                                                    ".((!empty($etd))?$etd:$betd)."
                                                                    <td>
                                                                        ".$row->total_earning."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][total_earning]' value='".$row->total_earning."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][org_total_earning]' value='".$row->org_total_earning."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][earning_data]' value='".$row->earning_data."'>
                                                                    </td>
                                                                    ".((!empty($dtd))?$dtd:$bdtd)."
                                                                    <td>".$row->advance_deduction."</td>
                                                                    <td>".$row->emi_amount."</td>
                                                                    <td>
                                                                        ".$row->net_salary."
                                                                        <input type='hidden' name='salary_data[".$sr_no."][net_salary]' value='".$row->net_salary."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][total_deduction]' value='".$row->total_deduction."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][org_total_deduction]' value='".$row->org_total_deduction."'>
                                                                        <input type='hidden' name='salary_data[".$sr_no."][deduction_data]' value='".$row->deduction_data."'>";
                                                                        $html .= "<input type='hidden' name='salary_data[".$sr_no."][advance_deduction]' value='".$row->advance_deduction."'><input type='hidden' name='salary_data[".$sr_no."][org_advance_deduction]' value='".$row->org_advance_deduction."'>".$adsHtml;
                                                                        $html .= "<input type='hidden' name='salary_data[".$sr_no."][emi_amount]' value='".$row->emi_amount."'><input type='hidden' name='salary_data[".$sr_no."][org_emi_amount]' value='".$row->org_emi_amount."'>".$loanHtml;
                                                                    $html .= "</td>
                                                                    <td>".$row->actual_sal."</td>
                                                                    <td>".$row->sal_diff."</td>
                                                                    <td>".$editButton."</td>
                                                                </tr>";

                                                                echo $html;
                                                                $html="";
                                                                $sr_no++;
                                                            endforeach;
                                                        /*else:
                                                            echo "<tr>
                                                                <td id='noData' class='text-center' colspan='".$headCount."'>No data available in table</td>
                                                            </tr>";*/
                                                        endif;
                                                    ?>                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-2 float-right form-group">
                            <button type="button" class=" btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
    viewDataTable("empSalary");
    $(document).on('click','.loadSalaryData',function(){
       
        var dept_id = $("#dept_id :selected").val();
        var format_id = $("#format_id :selected").val();
        var month = $("#month :selected").val();
        var valid = 1;

        if(dept_id == ""){ $(".dept_id").html("Department is required."); valid = 0; }
        if(format_id == ""){ $(".format_id").html("CTC Format is required."); valid = 0; }
        if(month == ""){ $(".month").html("Month is required."); valid = 0; }

        if(valid == 1){
            $.ajax({
                url:base_url + controller + '/getEmployeeSalaryData',
                type: 'post',
                data : {dept_id:dept_id, format_id:format_id, month:month,view:0},
                dataType:'json',
                success:function(data){
                    $('#empSalary').DataTable().clear().destroy();
                    $("#empSalaryHead").html("");
                    $("#empSalaryHead").html(data.emp_salary_head);
                    $("#empSalaryData").html("");
                    $("#empSalaryData").html(data.emp_salary_html); 
                    viewDataTable("empSalary");
                }
            });
        }
    });

    /* $(document).on('click','.saveItem',function(){
        var fd = $('#invoiceItemForm')[0];
        var formData = new FormData(fd);
        $.ajax({
            url: base_url + controller + '/getEmpSalaryJson',
            data:formData,
            processData: false,
            contentType: false,
            type: "POST",
            dataType:"json",
        }).done(function(data){
            AddRow(data.jsonData);
            $("#itemModel").modal('hide');
        });
    }); */

    $(document).on('keyup change','.orgEmiAmounts, .emiAmounts',function(){
        var id = $(this).data('id');
        var amount = $(this).val() || 0;
        var loan_amount = $("#loan_amount_"+id).val() || 0;
        $(".error").html("");

        var loan_pending_amount = parseFloat(parseFloat(loan_amount) - parseFloat(amount)).toFixed(0);
        if(parseFloat(loan_pending_amount) < 0){
            if($(this).hasClass(".orgEmiAmounts")){
                $(".org_emi_amount_"+id).html("Invalid EMI Amount.");
            }else{
                $(".emi_amount_"+id).html("Invalid EMI Amount.");
            }  
            $(this).val(0);
            $("#pendingAmount"+id).html(loan_amount);
        }else{
            $("#pendingAmount"+id).html(loan_pending_amount);
        }        
    });

    $(document).on('keyup change','.calculateSalary',function(){
        var earningAmount = 0;
        $("#editEmployeeSalary .earnings").each(function(){earningAmount += parseFloat($(this).val()) || 0;});
        $("#editEmployeeSalary #total_earning").val(earningAmount.toFixed(0));

        if($("#editEmployeeSalary #pf_applicable").val() == 1){
            var hraAmount = $("#editEmployeeSalary #hra_amount").val() || 0;
            var pf_cal_value = $("#editEmployeeSalary #pf_per").val() || 0;

            var pfValuation = parseFloat(parseFloat(earningAmount) - parseFloat(hraAmount)).toFixed(0);
            if(pfValuation >= 15000){
                var pfAmount = parseFloat((15000 * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }else{
                var pfAmount = parseFloat((pfValuation * parseFloat(pf_cal_value)) / 100).toFixed(0);
            }
            $("#editEmployeeSalary #pf_amount").val(pfAmount);
        }else{
            $("#editEmployeeSalary #pf_amount").val(0);
        }

        if(parseFloat(earningAmount) >= 12000){
            var pt_amount = 200;
			$("#editEmployeeSalary #pt_amount").val(pt_amount);
        }else{
			$("#editEmployeeSalary #pt_amount").val(0);
        }

        var advanceAmountArray = $("#editEmployeeSalary .advanceSalary").map(function(){return $(this).val();}).get();
        var advanceAmount = 0;
        $.each(advanceAmountArray,function(){advanceAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #advance_deduction").val(advanceAmount.toFixed(0));

        var emiAmountArray = $("#editEmployeeSalary .orgEmiAmounts").map(function(){return $(this).val();}).get();
        var emiAmount = 0;
        $.each(emiAmountArray,function(){emiAmount += parseFloat(this) || 0;});
        $("#editEmployeeSalary #emi_amount").val(emiAmount.toFixed(0));
		        		
		var deductionAmount = 0;
        $("#editEmployeeSalary .deductions").each(function(){deductionAmount += parseFloat($(this).val()) || 0;});
        $("#editEmployeeSalary #total_deduction").val(deductionAmount.toFixed(0));
		
        var netSalary = 0;
        netSalary = parseFloat(parseFloat(earningAmount) - parseFloat(deductionAmount)).toFixed(0);
        $("#editEmployeeSalary #net_salary").val(netSalary);
    });
});

function Edit(row_index = "",salaryCode = ""){
    var formData = $("#empSalaryData #"+row_index+" :input").serializeArray();
    formData.push({ name: "key_value", value: row_index });
    formData.push({ name: "month", value: $("#month :selected").val() });
    formData.push({ name: "dept_id", value: $("#dept_id :selected").val() });
    formData.push({ name: "format_id", value: $("#format_id :selected").val() });

    var modal_id = "modal-md";
    $.ajax({ 
		url: base_url + controller + "/editEmployeeSalaryData",
        type: "POST",		
		data: formData,
	}).done(function(response){
        $("#"+modal_id).modal();
		$("#"+modal_id+' .modal-title').html("Update Employee Salary ["+salaryCode+"]");
        $("#"+modal_id+' .modal-body').html('');
		$("#"+modal_id+' .modal-body').html(response);
		$("#"+modal_id+" .modal-body form").attr('id',"editEmployeeSalary");
		$("#"+modal_id+" .modal-footer .btn-save").attr('onclick',"saveEmpSalary('editEmployeeSalary','saveEmployeeSalaryData');");
		$("#"+modal_id+" .modal-footer .btn-close").attr('data-modal_id',"editEmployeeSalary");

		$("#"+modal_id+" .modal-footer .btn-close").show();
        $("#"+modal_id+" .modal-footer .btn-save").show();
        $("#"+modal_id+" .modal-footer .btn-save-close").hide();
		
		$("#"+modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initModalSelect();initMultiSelect();setPlaceHolder();
	});
}

function saveEmpSalary(formId,fnSave){
    var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/' + fnSave,
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
        var row_index = $("#row_index").val();
        $('#'+formId)[0].reset();$(".modal").modal('hide');
        $('#empSalary').DataTable().destroy();
        $("#"+row_index).html(data.salary_data);
        viewDataTable("empSalary");
	});
}

function savePayRoll(formId){
	
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = base_url + controller;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function viewDataTable(tableId){
	var table = $('#'+tableId).DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':false,
		retrieve: true,
		paging: false,
		//buttons: [ {text: 'PDF',action: function ( e, dt, node, config ) {exportData('pdf');}}, {text: 'Excel 1',action: function ( e, dt, node, config ) {exportData('excel');}},{text: 'Excel 2',action: function ( e, dt, node, config ) {exportData('excel2');}}]
	});
	table.buttons().container().appendTo( '#'+tableId+'_wrapper .col-md-6:eq(0)' );
	return table;
};
</script>