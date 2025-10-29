<?php $this->load->view('includes/header'); ?>
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
                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block save-form " onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>
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
                                                                <input type="hidden" name="emp_id[]" value="<?=$row->emp_id?>">
                                                                <input type="number" name="basic_salary[]" class="basic_salary floatOnly" value="<?= (!empty($row->basic_salary)) ? $row->basic_salary : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="hra[]" class="hra floatOnly" value="<?= (!empty($row->hra)) ? $row->hra : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="ta[]" class="ta floatOnly" value="<?= (!empty($row->ta)) ? $row->ta : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="da[]" class="da floatOnly" value="<?= (!empty($row->da)) ? $row->da : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="oa[]" class="oa floatOnly" value="<?= (!empty($row->oa)) ? $row->oa : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="bonus_amount[]" class="bonus_amount floatOnly" value="<?= (!empty($row->bonus_amount)) ? $row->bonus_amount : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="pf_amount[]" class="pf_amount floatOnly" value="<?= (!empty($row->pf_amount)) ? $row->pf_amount : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="prof_tax[]" class="prof_tax floatOnly" value="<?= (!empty($row->prof_tax)) ? $row->prof_tax : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="other_deduction[]" class="other_deduction floatOnly" value="<?= (!empty($row->other_deduction)) ? $row->other_deduction : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="present_days[]" class="present_days floatOnly" value="<?= (!empty($row->present_days)) ? $row->present_days : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="absent_days[]" class="absent_days floatOnly" value="<?= (!empty($row->absent_days)) ? $row->absent_days : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="net_salary[]" readonly value="<?= (!empty($row->net_salary)) ? $row->net_salary : "" ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="remark[]" class="remark" value="<?= (!empty($row->remark)) ? $row->remark : "" ?>">
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
function savePayRoll(formId){
	
	$(document).on('keyup change','.countSalary',function(){
		var basic_salary = $('#').val();
	});

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
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
</script>