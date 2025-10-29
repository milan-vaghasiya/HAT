<?php 
	$this->load->view('includes/header'); 
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03'];
	
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8"><h4 class="card-title">Attendance</h4>
								<small class="text-success font-bold">Last Synced at <span class="lastSyncedAt"><?=$lastSyncedAt?></span></small></div>
                            <div class="col-md-4">
								<div class="input-group mb-3">
									<?php if($this->loginId == -1 or $this->loginId == 1){ ?> <!--<button href="#" class="btn btn-light-green pulse syncDevicePunches"><i class="fas fa-sync"></i> Sync</button> --><?php } ?>
									<button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="importAttendance" data-fnsave="importEmpAttendance" data-form_title="Import Emp Attendance"><i class="fa fa-plus"></i> Import Excel</button>
									<input type="date" id="report_date" name="report_date" class="form-control" value="<?=date("Y-m-d")?>" max=<?=date('Y-m-d')?> >
									<div class="input-group-append">
										<button class="btn btn-info getDailyAttendance" type="button">Go!</button>
									</div>
								</div>
                            </div>                       
                        </div>                                         
                    </div>
                    <div class="card-body">
						<div class="row">
							<div class="col-sm-12 col-md-3">
								<div class="card bg-info">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="align-self-center display-6"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Employee</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 totalEmpStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-success">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Present</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 presentStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-warning">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Late</h4>
												<span>Arrived</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 lateStat"></h2>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="card bg-danger">
									<div class="card-body text-white">
										<div class="d-flex flex-row">
											<div class="display-6 align-self-center"><i class="ti-user"></i></div>
											<div class="p-10 align-self-center">
												<h4 class="m-b-0">Total</h4>
												<span>Absent</span>
											</div>
											<div class="ml-auto align-self-center">
												<h2 class="font-medium m-b-0 ">
												    <a href="javascript:" class="absentStat text-white getAbsentReport" target="_blank"></a>
												</h2>
												
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Column -->
						</div>
                        <div class="row">
                            <div class="col-md-12">
								<div class="table-responsive">
									<table id='attendanceSummaryTable' class="table table-striped table-bordered jdt">
										<thead class="thead-info">
											<tr class="clonTR">
												<th>#</th>
												<th>Code</th>
												<th>Emp Name</th>
												<th>Department</th>
												<th>Designation</th>
												<th>Shift</th>
												<th>Status</th>
												<th>Punch Time</th>
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
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/month-attendance.js?v=<?=time()?>"></script>
<script>
$(document).on('click','.getAbsentReport',function(){
    var report_date = $('#report_date').val();
    window.open(base_url + 'reports/attendanceReport/getAbsentReport/' + report_date, '_blank');
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
							{ orderable: false, targets: "_all" }
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