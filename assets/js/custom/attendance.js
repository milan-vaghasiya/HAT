$(document).ready(function(){
	//setTimeout(function(){$('.getDailyAttendance').trigger('click') }, 10);
	attendanceSummaryTable();
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead .clonTR' );
    $('.jdt thead tr:eq(1) th').each( function (i) {
        var title = $(this).text(); //placeholder="'+title+'"
		$(this).html( '<input type="text" style="width:100%;"/>' );
	});
	
	$(document).on('click',".attendanceInfo",function(){
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
    });
	
	$(document).on('click',".getDailyAttendance",function(){
        var report_date = $("#report_date").val();
		$.ajax({ 
            type: "POST",   
            url: base_url + controller + '/getDailyAttendance',   
            data: {report_date : report_date},
			dataType:"json",
        }).done(function(response){
            $('#attendanceSummaryTable').dataTable().fnDestroy();
			$('.attendance-summary').html(response.tbody);
			$('.totalEmpStat').html(response.totalEmp);
			$('.presentStat').html(response.present);
			$('.lateStat').html(response.late);
			$('.absentStat').html(response.absent);
			attendanceSummaryTable();
        });
    });
	
	$(document).on('click',".syncDeviceData",function(){
        var report_date = $("#report_date").val();
		$.ajax({ 
            type: "POST",   
            url: base_url + controller + '/syncDeviceData',   
            data: {report_date : report_date},
			dataType:"json",
        }).done(function(response){
			if(response.status==1){
				$('.lastSyncedAt').html(response.lastSyncedAt);
				toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}else{
				toastr.error(response.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
            
        });
    });
});
function attendanceTable()
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
}
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