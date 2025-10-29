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
                                <div class="col-md-5 form-group">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control req">
                                        <option value="2021-04-30">April-2021</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <label for="ledger_id">Select Ledger</label>
                                    <select name="ledger_id" id="ledger_id" class="form-control single-select" tabindex="-1">
                                        <option value="1">CASH IN HAND</option>
                                    </select>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="purchaseItems" class="table table-striped table-borderless">
                                                <thead class="thead-info">
                                                    <tr>
                                                        <th style="width:30px;">#</th>
                                                        <th>Employee Name</th>
                                                        <th style="width:5%;">Basic Salary</th>
                                                        <th style="width:100px;">HRA</th>
                                                        <th style="width:100px;">TA</th>
                                                        <th style="width:100px;">DA</th>
                                                        <th style="width:100px;">OA</th>
                                                        <th style="width:100px;">Bonus Amount</th>
                                                        <th style="width:100px;">Pf Amount</th>
                                                        <th style="width:100px;">Prof. Tax</th>
                                                        <th style="width:100px;">Other Deduction</th>
                                                        <th style="width:100px;">Present Days</th>
                                                        <th style="width:100px;">Absent Days</th>
                                                        <th style="width:100px;">Net Salary</th>
                                                        <th style="width:100px;">Remark</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i = 1;
                                                    foreach ($empData as $row) : ?>
                                                        <tr>
                                                            <td><?= $i++ ?></td>
                                                            <td>
                                                                <?= $row->emp_name ?>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="id[]" value="<?=(!empty($dataRow->id)) ? $dataRow->id : ""?>">
                                                                <input type="hidden" name="emp_id[]" value="<?=$row->emp_id?>">
                                                                <input type="number" name="basic_salary[]" id="basic_salary<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="basic_salary floatOnly countSalary" value="<?= (!empty($row->basic_salary)) ? $row->basic_salary : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="hra[]" id="hra<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="hra floatOnly countSalary" value="<?= (!empty($row->hra)) ? $row->hra : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="ta[]" id="ta<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="ta floatOnly countSalary" value="<?= (!empty($row->ta)) ? $row->ta : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="da[]" id="da<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="da floatOnly countSalary" value="<?= (!empty($row->da)) ? $row->da : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="oa[]" id="oa<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="oa floatOnly countSalary" value="<?= (!empty($row->oa)) ? $row->oa : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="bonus_amount[]" id="bonus_amount<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="bonus_amount floatOnly countSalary" value="<?= (!empty($row->bonus_amount)) ? $row->bonus_amount : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="pf_amount[]" id="pf_amount<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="pf_amount floatOnly countSalary" value="<?= (!empty($row->pf_amount)) ? $row->pf_amount : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="prof_tax[]" id="prof_tax<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="prof_tax floatOnly countSalary" value="<?= (!empty($row->prof_tax)) ? $row->prof_tax : 200 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="other_deduction[]" id="other_deduction<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="other_deduction floatOnly countSalary" value="<?= (!empty($row->other_deduction)) ? $row->other_deduction : 0 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="present_days[]" class="present_days floatOnly countSalary" value="<?= (!empty($row->present_days)) ? $row->present_days : 28 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="absent_days[]" id="absent_days<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="absent_days floatOnly countSalary" value="<?= (!empty($row->absent_days)) ? $row->absent_days : 2 ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="net_salary[]" id="net_salary<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" class="net_salary" readonly value="<?= (!empty($row->net_salary)) ? $row->net_salary : 0 ?>">
																<input type="hidden" name="leave_loss[]" id="net_salary<?=$row->emp_id?>" data-emp_id="<?=$row->emp_id?>" value="<?=(!empty($dataRow->leave_loss)) ? $dataRow->leave_loss : 0?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="remark[]"  class="remark" value="<?= (!empty($row->remark)) ? $row->remark : "" ?>">
                                                            </td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>

<script>
$(document).ready(function(){
	$(document).on('keyup change','.countSalary',function(){
		var emp_id = $(this).data('emp_id');
		var basic_salary = parseFloat($('#basic_salary'+emp_id).val());
		var hra = parseFloat($('#hra'+emp_id).val());
		var ta = parseFloat($('#ta'+emp_id).val());
		var da = parseFloat($('#da'+emp_id).val());
		var oa = parseFloat($('#oa'+emp_id).val());
		var bonus_amount = parseFloat($('#bonus_amount'+emp_id).val());
		var pf_amount = parseFloat($('#pf_amount'+emp_id).val());
		var prof_tax = parseFloat($('#prof_tax'+emp_id).val());
		var other_deduction = parseFloat($('#other_deduction'+emp_id).val());
		var absent_days = parseFloat($('#absent_days'+emp_id).val());
		var leave_loss = parseFloat((basic_salary / 30) * absent_days).toFixed(0);
		var net_salary = basic_salary + hra + ta + da + oa + bonus_amount;
		net_salary = net_salary - pf_amount - prof_tax - other_deduction - leave_loss;
		$('#leave_loss'+emp_id).val(leave_loss);
		$('#net_salary'+emp_id).val(net_salary);
	});
});
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
</script>