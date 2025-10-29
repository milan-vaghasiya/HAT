<?php 
	$this->load->view('includes/header'); 	
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Employee Punches</h4>
                            </div> 
                            <div class="col-md-6">
								<div class="input-group mb-3">
								    <select name="punch_status" id="punch_status" class="form-control single-select req" style="width:50%;margin-bottom:0px;">
                                        <option value="">All Punches</option>
                                        <option value="1" selected>Missed Punch</option>
                                        <option value="2">Absent</option>
                                    </select>
									<input type="date" id="report_date" name="report_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> style="width:30%;margin-bottom:0px;">
									<button class="btn btn-info getAllPunches" type="button" style="width:15%;padding: 0.3rem 0px;border-top-left-radius:0px;border-bottom-left-radius:0px;">Go!</button>
								</div>
                            </div>                       
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
								<table id='attendanceSummaryTable' class="table table-striped table-bordered jdt">
									<thead class="thead-info">
										<tr class="clonTR">
											<th>Code</th>
											<th>Emp Name</th>
											<th>Department</th>
											<th>Shift</th>
											<th>Designation</th>
											<th>Punches</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody class="attendance-summary"></tbody>
								</table>
							</div>
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
	// setTimeout(function(){$('.getDailyAttendance').trigger('click') }, 10);
	attendanceSummaryTable();
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(1) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		$(this).html( '<input type="text" style="width:100%;"/>' );
	});

	/* $(document).on('click',".attendanceInfo",function(){
        var attendance_id = $(this).data('id');
        var emp_name = $(this).data('emp_name');
        var emp_id = $(this).data('emp_id');

		$('#emp_id').val($(this).data('emp_id'));
		$('.emp_name').html(emp_name);
		$('.infotitle').html($(this).data('infotitle'));
		$('.totalhour').html($(this).data('totalhour'));
		$('.punch_in').html($(this).data('punch_in'));
		$('.punch_out').html($(this).data('punch_out'));
		$('.overtime').html($(this).data('overtime'));
		$('#attendanceInfo').modal();
    }); */

	$(document).on('click',".getAllPunches",function(){
        var report_date = $("#report_date").val();
        var punch_status = $("#punch_status").val();
		$.ajax({ 
            type: "POST",   
            url: base_url + 'reports/attendanceReport/getAllPunches',   
            data: {report_date : report_date,punch_status : punch_status},
			dataType:"json",
        }).done(function(response){
            $('#attendanceSummaryTable').DataTable().clear().destroy();
			$('.attendance-summary').html(response.tbody);
			
			attendanceSummaryTable();
        });
    });

	$(document).on('click',".manualAttendance",function(e){
	    e.stopImmediatePropagation();
		var functionName = $(this).data("function");
		var modalId = $(this).data('modal_id');
		var button = $(this).data('button');
		var title = $(this).data('form_title');
		var emp_id = $(this).data('empid');
		var attendance_date = $(this).data('adate');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
		$.ajax({ 
			type: "POST",   
			url: base_url + 'hr/manualAttendance/' + functionName,    
			data: {emp_id:emp_id,attendance_date:attendance_date}
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
			$('#punch_date').val(attendance_date);
			$(".single-select").comboSelect();
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); $('.loadtrans').trigger('change'); }, 5);
		});
	});
});

/* function attendanceTable()
{
	var attendanceTable = $('#attendanceTable').DataTable( 
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
	attendanceTable.buttons().container().appendTo( '#attendanceTable_wrapper toolbar' );
	$('#attendanceTable_filter .form-control-sm').css("width","97%");
	$('#attendanceTable_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('#attendanceTable_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return attendanceTable;
} */

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
		buttons: [ 'pageLength', 'excel']
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