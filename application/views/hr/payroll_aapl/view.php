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
                        <h4><u>Payroll View</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePayRoll">
                            <div class="row">
                                <div class="col-md-2 form-group">
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
                                <div class="col-md-2 form-group">
                                    <label for="format_id">CTC Format</label>
                                    <select name="format_id" id="format_id" class="form-control single-select req " >
                                        <option value="">Select Type</option>
                                        <?php
                                            foreach($ctcFormat as $row):
                                                $selected = (!empty($salaryData) && $salaryData[0]->format_id == $row->id)?"selected":"";
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->format_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="error format_id"></div>
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
                                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block loadSalaryData"  > Load</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="empSalary" class="table table-striped jpExcelTable jpDataTable ">
                                                <thead class="thead-info" id="empSalaryHead">
                                                    <tr>
                                                        <th style="width:30px;">#</th>
                                                        <th>Employee Name</th>
                                                        <th style="width:100px;">Total Days</th>
                                                        <th style="width:100px;">Present <br> Days</th>
                                                        <th style="width:100px;">Absent <br> Days</th>
                                                        <th style="width:100px;">Gross Salary</th>
                                                        <th style="width:100px;">Advance</th>
                                                        <th style="width:100px;">Loan</th>
                                                        <th style="width:100px;">Net Salary</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="empSalaryData">
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
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Go Back</a>
                            <!--<button type="button" class=" btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="savePayRoll('savePayRoll');" ><i class="fa fa-check"></i> Save</button>-->
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
                data : {dept_id:dept_id, format_id:format_id, month:month,view:1},
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
});

function exportData(file_type="pdf"){
    var dept_id = $("#dept_id :selected").val();
    var format_id = $("#format_id :selected").val();
    var month = $("#month :selected").val();
    var valid = 1;

    if(dept_id == ""){ $(".dept_id").html("Department is required."); valid = 0; }
    if(format_id == ""){ $(".format_id").html("CTC Format is required."); valid = 0; }
    if(month == ""){ $(".month").html("Month is required."); valid = 0; }
    
    if(valid == 1){
        if(file_type == 'excel2'){
            window.open(base_url + controller + '/getEmployeeActualSalaryData/' + dept_id + '/' + format_id + '/' + month + '/' + file_type, '_blank').focus();
        }else{
            window.open(base_url + controller + '/getEmployeeSalaryData/' + dept_id + '/' + format_id + '/' + month + '/' + file_type, '_blank').focus();
        }
    }
}

function viewDataTable(tableId){
	var table = $('#'+tableId).DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':false,
		retrieve: true,
		buttons: [ 'pageLength', {text: 'PDF',action: function ( e, dt, node, config ) {exportData('pdf');}}, {text: 'Excel 1',action: function ( e, dt, node, config ) {exportData('excel');}},{text: 'Excel 2',action: function ( e, dt, node, config ) {exportData('excel2');}}]
	});
	table.buttons().container().appendTo( '#'+tableId+'_wrapper .col-md-6:eq(0)' );
	return table;
};
</script>
