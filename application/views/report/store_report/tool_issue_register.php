<!-- MEGHAVI -->
<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                        <div class="col-md-12">
                                <h4 class="card-title pageHeader text-center"><?=$pageHeader?></h4>
                            </div>  
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="dept_id">Department</label>
                                <select name="dept_id" id="dept_id" class="form-control single-select">
                                 <option value="">ALL</option>
                                    <?php
                                        foreach($deptData as $row):
                                          $selected = "";
                                             echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                     endforeach;
                                      ?>
                                </select>
                                <div class="error dept_id"></div>
                            </div>      
							<div class="col-md-3 form-group">
								<label for="">Job Card No.</label>
								<select name="job_card_id" id="job_card_id" class="form-control single-select">
									 <option value="">ALL</option>
									 <option value="-1">General Issue</option>
									<?php
										foreach($jobCardData as $row):
											echo '<option value="'.$row->id.'" '.$selected.'>['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
										endforeach;
									?>
								</select>       
                                <div class="error job_card_id"></div>         
                            </div>         
                            <div class="col-md-2 form-group"> 
                            <label for="">From Date</label>  
                                <input type="date" name="from_date" id="from_date" class="form-control" max="<?=date('Y-m-01')?>" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4 form-group">  
                                <label for="">To Date</label>
								<div class="input-group-append">
									<input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="max-width:70%" />
									<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
										<i class="fas fa-sync-alt"></i> Load
									</button>
								</div>  
                                <div class="error toDate"></div>
                            </div>                                            
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
								    <tr class="text-center" id="theadData">
                                        <th colspan="6">Tool Issue Register</th>
                                        <th colspan="2">F PR 13 (00/01.06.2020)</th>
                                    </tr>
									<tr>  
										<th>#</th>
										<th>Date</th>
										<th>Product</th>
										<th>Department</th>
										<th>Job Card</th>
										<th>QTY</th>
										<th>Price</th>
										<th>Amount</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
								<tfoot id="tfootData">
								    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
								</tfoot>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var dept_id = $('#dept_id').val();
		var job_card_id = $('#job_card_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		//if($("#dept_id").val() == ""){$(".dept_id").html("Department is required.");valid=0;}
		//if($("#job_card_id").val() == ""){$(".job_card_id").html("Job Card is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getToolIssueRegister',
                data: {dept_id:dept_id, job_card_id:job_card_id, from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
                    $("#theadData").html(data.thead)
					$("#tbodyData").html(data.tbody);
					$("#tfootData").html(data.tfoot);
					reportTable();
                }
            });
        }
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,2] }, 
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
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return reportTable;
}
</script>


