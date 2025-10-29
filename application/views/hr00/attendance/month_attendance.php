<?php 
	$this->load->view('includes/header'); 
	$today = new DateTime();
	$today->modify('first day of this month');$first_day = date('Y-m-d');
	$today->modify('last day of this month');$last_day = date("t",strtotime($today->format('Y-m-d')));
	$monthArr = ['April'=>'04','May'=>'05','June'=>'06','July'=>'07','August'=>'08','September'=>'09','October'=>'10','November'=>'11','December'=>'12','January'=>'01','February'=>'02','March'=>'03'];
	
	$printString = '';
	for($r=1;$r<=5;$r++)
	{
		for($c=1;$c<=$r;$c++)
		{
			$printString .= ($c + (($r + ($r-1)) * $c) - 1)." ";
		}
		$printString .= '<br>';
	}
	
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-7">
                                <h4 class="card-title">Monthly Attendance</h4>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
									<select name="month" id="month" class="form-control single-select" style="width:50%;margin-bottom:0px;">
										<?php
											foreach($monthArr as $key=>$value):
												$selected = (date('m') == $value)?"selected":"";
												echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
											endforeach;
										?>
									</select>
									<button type="button" class="btn waves-effect waves-light btn-warning float-right" title="Load Data" style="padding: 0.3rem 0px;border-radius:0px;width:25%;" onclick="loadAttendanceSheet();"><i class="fa fa-sync"></i> Load</button>
									<button type="button" class="btn waves-effect waves-light btn-primary float-right" title="Load Data" style="padding: 0.3rem 0px;width:25%;border-top-left-radius:0px;border-bottom-left-radius:0px;" onclick="printMonthlyAttendance('pdf');"><i class="fa fa-print"></i> Print</a>
								</div>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='attendanceTable' class="table table-striped table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>Employee</th>
										<?php for($d=1;$d<=$last_day;$d++){echo '<th>'.$d.'</th>';} ?>
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
<style>
.punch-det {
    background-color: #f9f9f9;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    margin-bottom: 20px;
    padding: 10px 5px;
}
</style>
<div class="modal fade" id="attendanceInfo" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Attendance Info</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
				<div class="col-md-12"><h5 class="emp_name"></h5></div>
                <div class="col-md-12" style="padding:20px;border:1px solid #e3e3e3; border-radius:4px;">
					<div class="row">
						<div class="col-md-12"><h3 class="text-primary"><span class="infotitle"></span><span class="float-right totalhour"></span></h3></div>
						<input type="hidden" name="emp_id" id="emp_id" value="">
					</div>
					<div class="row punch-det">
						<div class="col-md-6"><h6>Punch In</h6></div>
						<div class="col-md-6 text-right font-bold punch_in"></div>
					</div>
					<div class="row punch-det">
						<div class="col-md-6"><h6>Punch Out</h6></div>
						<div class="col-md-6 text-right font-bold punch_out"></div>
					</div>
					<div class="row punch-det">
						<div class="col-md-6"><h6>Overtime</h6></div>
						<div class="col-md-6 text-right font-bold overtime"></div>
					</div>
				</div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <!--<button type="button" class="btn waves-effect waves-light btn-outline-success btn-save" onclick="updateAttendance();"><i class="fa fa-check"></i> Update Record</button>-->
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/month-attendance.js?v=<?=time()?>"></script>