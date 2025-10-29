<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
                            <div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>              
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='commanTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center"><th colspan="12"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Account Name</th>
										<th style="min-width:80px;">Group Name</th>
										<th style="min-width:50px;">Opening Amount</th>
										<th style="min-width:50px;">Closing Amount</th>
									</tr>
								</thead>
								<tbody id="cashBookData"></tbody>
                                <tfoot class="thead-info">
								   <tr>
									   <th colspan="4" class="text-right">Total</th>
									   <th id="totalAmount">0.00</th>
								   </tr>
							   </tfoot>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="accountDetails" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content animated slideDown">
            <div class="modal-header">
			<h4 class="modal-title" id="exampleModalLabel1">Account Details</h4>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>			
			<div class="modal-body">
				<div class="col-md-12">
					<div class="row">
						<input type="hidden" id="acc_id" value="" />
						<div class="col-md-6">   
							<input type="date" id="accd_from_date" class="form-control" value="<?=$startDate?>" />
							<div class="error accd_from_date"></div>
						</div>     
						<div class="col-md-6">  
							<input type="date" id="accd_to_date" class="form-control" value="<?=$endDate?>" />
							<div class="error accd_to_date"></div>
						</div>  
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
				<button type="button" class="btn btn-success" onclick="loadAccountDetails();"><i class="fa fa-submit"></i> Submit</button>
			</div>			
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	loadData();
    $(document).on('click','.loaddata',function(){
		loadData();
	});  

	$(document).on('click',".getAccountData",function(){
		var acc_id = $(this).data('id');
		$("#acc_id").val("");
		$("#acc_id").val(acc_id);
	});
});

function loadData(){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	if(valid){
		$.ajax({
			url: base_url + controller + '/getCashBook',
			data: {from_date:from_date, to_date:to_date},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#commanTable").DataTable().clear().destroy();
				$("#cashBookData").html("");
				$("#cashBookData").html(data.tbody);
				$("#totalAmount").html(data.totalClBalance);
				jpReportTable('commanTable');
			}
		});
	}
}

function loadAccountDetails(){
	$(".error").html("");
	var valid = 1;
	var acc_id = $("#acc_id").val();
	var from_date = $('#accd_from_date').val();
	var to_date = $('#accd_to_date').val();
	if(from_date == ""){$(".accd_from_date").html("From Date is required.");valid=0;}
	if(to_date == ""){$(".accd_to_date").html("To Date is required.");valid=0;}
	if(to_date < from_date){$(".accd_to_date").html("Invalid Date.");valid=0;}
	if(valid){
		window.location.href = base_url + controller + "/ledgerDetail/"+acc_id+"/"+from_date+"/"+to_date;
	}
}
</script>