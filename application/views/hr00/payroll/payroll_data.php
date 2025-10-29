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
                                                        <th style="width:100px;">Leave Loss</th>
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
                                                            <td><?=$row->basic_salary?> </td>
                                                            <td><?=$row->hra?> </td>
                                                            <td><?=$row->ta?> </td>
                                                            <td><?=$row->da?> </td>
                                                            <td><?=$row->oa?> </td>
                                                            <td><?=$row->bonus_amount?> </td>
                                                            <td><?=$row->pf_amount?> </td>
                                                            <td><?=$row->prof_tax?> </td>
                                                            <td><?=$row->other_deduction?> </td>
                                                            <td><?=$row->present_days?> </td>
                                                            <td><?=$row->absent_days?> </td>
                                                            <td><?=$row->leave_loss?> </td>
                                                            <td><?=$row->net_salary?> </td>
                                                            <td><?=$row->remark?> </td>
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