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
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
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
                                    <tr class="text-center">
                                        <th colspan="3"><?=$pageHeader?></th>
                                        <th>OP. Balance</th>
                                        <th class="text-right" id="op_balance">0</th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">Date</th>
										<th style="min-width:80px;">Account Name</th>
										<th style="min-width:80px;">Receivables</th>
										<th style="min-width:50px;">Payables</th>
										<th style="min-width:50px;">Closing Amount</th>
									</tr>
								</thead>
								<tbody id="fundManagementData"></tbody>
								<tfoot class="thead-info">
								   <tr>
									   <th colspan="2" class="text-right">Total</th>
									   <th class="text-right" id="cr_balance">0.00</th>
									   <th class="text-right" id="dr_balance">0.00</th>
									   <th class="text-right" id="cl_balance">0.00</th>
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

<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	loadData();
    $(document).on('click','.loaddata',function(){
		loadData();
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
			url: base_url + controller + '/getFundManagementData',
			data: {from_date:from_date, to_date:to_date},
			type: "POST",
			dataType:'json',
			success:function(data){
				$("#commanTable").DataTable().clear().destroy();
                $("#op_balance").html(data.op_balance);
				$("#fundManagementData").html("");
				$("#fundManagementData").html(data.tbody);
                $("#cr_balance").html(data.cr_balance);
                $("#dr_balance").html(data.dr_balance);
                $("#cl_balance").html(data.cl_balance);
				jpReportTable('commanTable');
			}
		});
	}
}

</script>