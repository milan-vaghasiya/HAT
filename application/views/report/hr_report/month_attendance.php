<?php 
	$this->load->view('includes/header'); 	
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April-'.$start_year=>'01-04-'.$start_year,'May-'.$start_year=>'01-05-'.$start_year,
	'June-'.$start_year=>'01-06-'.$start_year,'July-'.$start_year=>'01-07-'.$start_year,'August-'.$start_year=>'01-08-'.$start_year,'September-'.$start_year=>'01-09-'.$start_year,'October-'.$start_year=>'01-10-'.$start_year,'November-'.$start_year=>'01-11-'.$start_year,'December-'.$start_year=>'01-12-'.$start_year,'January-'.$end_year=>'01-01-'.$end_year,'February-'.$end_year=>'01-02-'.$end_year,'March-'.$end_year=>'01-03-'.$end_year];	
		
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title">Monthly Attendance</h4>
                            </div>
							<div class="col-md-9">
                                <div class="input-group">
                                    <select name="dept_id" id="dept_id" class="form-control single-select req" style="width:30%;margin-bottom:0px;">
                                        <!--<option value="">All Department</option>-->
                                        <?php
                                            foreach($deptRows as $row):
                                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
									<select name="month" id="month" class="form-control single-select" style="width:20%;margin-bottom:0px;">
										<?php
											foreach($monthArr as $key=>$value):
												$selected = (date('m') == $value)?"selected":"";
												echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
											endforeach;
										?>
									</select>
									<button type="button" class="btn waves-effect waves-light btn-github float-right" datatip="View Report" flow="down" style="padding: 0.3rem 0px;border-radius:0px;width:10%;" onclick="getHourlyReport('view');"><i class="fa fa-eye"></i> View</button>
									<button type="button" class="btn waves-effect waves-light btn-danger float-right" datatip="PDF V1" flow="down" style="padding: 0.3rem 0px;width:10%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="getHourlyReport('pdf');"><i class="fa fa-file-pdf"></i> PDF V1</a>
									<button type="button" class="btn waves-effect waves-light btn-success float-right" datatip="EXCEL" flow="down" style="padding: 0.3rem 0px;width:10%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="getHourlyReport('excel');"><i class="fa fa-file-excel"></i> Excel</a>
									<button type="button" class="btn waves-effect waves-light btn-youtube float-right" datatip="PDF V2" flow="down" style="padding: 0.3rem 0px;width:10%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="printMonthlyAttendance('pdf');"><i class="fa fa-file-pdf"></i> PDF V2</button>
									<button type="button" class="btn waves-effect waves-light btn-facebook float-right" datatip="EXCEL V2" flow="down" style="padding: 0.3rem 0px;width:10%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="printMonthlyAttendance('excel');"><i class="fa fa-file-excel"></i> Excel V2</button>
								</div>
                            </div>                     
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='attendanceTable' class="table table-striped table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>Emp Code</th><th>Employee</th>
										<?php for($d=1;$d<=$last_day;$d++){echo '<th>'.$d.'</th>';} ?>
										<th>Total</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/month-attendance.js?v=<?=time()?>"></script>
<?=$floatingMenu?>
<script>
    $(document).ready(function(){
	attendanceSummaryTable();
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(1) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		$(this).html( '<input type="text" style="width:100%;"/>' );
	});
	$(document).on('click',".getDailyAttendance",function(){
        var report_date = $("#report_date").val();
		$.ajax({ 
            type: "POST",   
            url: base_url + 'reports/hrReport/getMismatchPunch',   
            data: {report_date : report_date},
			dataType:"json",
        }).done(function(response){
            $('#attendanceSummaryTable').dataTable().fnDestroy();
			$('.attendance-summary').html(response.tbody);
			
			attendanceSummaryTable();
        });
    });
	$(document).on('click',".manualAttendance",function(){
		var functionName = $(this).data("function");
		var modalId = $(this).data('modal_id');
		var button = $(this).data('button');
		var title = $(this).data('form_title');
		var emp_id = $(this).data('empid');
		var attendance_date = $(this).data('adate');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
		$.ajax({ 
			type: "GET",   
			url: base_url + 'hr/manualAttendance/' + functionName,   
			data: {}
		}).done(function(response){
			$("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
			$("#"+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeWithController('"+formId+"','"+fnsave+"','hr/manualAttendance/');");
			if(button == "close"){
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").hide();
			}else if(button == "save"){
				$("#"+modalId+" .modal-footer .btn-close").hide();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}else{
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}
			$('#emp_id').val(emp_id);
			$('#attendance_date').val(attendance_date);
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
		});
	});
});
function attendanceSummaryTable()
{
	var attendanceSummaryTable = $('#attendanceSummaryTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	attendanceSummaryTable.buttons().container().appendTo( '#attendanceSummaryTable_wrapper toolbar' );
	$('#attendanceSummaryTable_filter .form-control-sm').css("width","97%");
	$('#attendanceSummaryTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#attendanceSummaryTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");

	//Datatable Column Filter
    $('.jdt thead tr:eq(1) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( attendanceSummaryTable.column(i).search() !== this.value ) {attendanceSummaryTable.column(i).search( this.value ).draw();}
		});
	} );
	return attendanceSummaryTable;
}
</script>