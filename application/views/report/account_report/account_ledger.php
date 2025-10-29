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
                        <div class="table-responsive" style="width: 100%;">
                            <table id='commanTable' class="table table-bordered" style="width:100%;">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
										<th colspan="7"><?=$pageHeader?></th>
									</tr>
									<tr>
										<th>#</th>
										<th>Account Name</th>
										<th>Group Name</th>
										<th>Opening Amount</th>
										<th>Credit Amount</th>
										<th>Debit Amount</th>
										<th>Closing Amount</th>
									</tr>
								</thead>
								<tbody id="ledgerSummary">
								</tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<!-- <div class="modal fade" id="accountDetails" data-backdrop="static" data-keyboard="false">
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
</div> -->

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

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	var postData = {from_date:from_date,to_date:to_date,pdf:pdf};
	if(valid){
		if(pdf == "")
		{
			$.ajax({
				url: base_url + controller + '/getAccountLedger',
				data: postData,
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#commanTable").DataTable().clear().destroy();
					$("#ledgerSummary").html("");
					$("#ledgerSummary").html(data.tbody);
					jpReportTable('commanTable');
				}
			});
		}else
		{
			console.log(postData);
			var url = base_url + controller + '/getAccountLedger/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			console.log(url);
			window.open(url);
		}

	}
}

function jpReportTable(tableId) {
	var jpReportTable = $('#'+tableId).DataTable({
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		order: [],
		"columnDefs": [
		    {type: 'natural',targets: 0},
			{orderable: false,targets: "_all"},
			{className: "text-center",targets: [0, 1]},
			{className: "text-center","targets": "_all"}
		],
		pageLength: 25,
		language: {search: ""},
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: ['pageLength', 'excel',{text: 'Pdf',action: function ( e, dt, node, config ) {loadData('pdf');}}],
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
	    "fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	jpReportTable.buttons().container().appendTo('#'+tableId+'_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	setTimeout(function(){ jpReportTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() {jpReportTable.columns.adjust().draw(); });
	return jpReportTable;
}
</script>