<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form id="outstandingForm">
                            <div class="row">
    							<div class="col-md-2">
    								<select name="os_type" id="os_type" class="form-control">
    									<option value="R">Recievable</option>
    									<option value="P">Payable</option>
    								</select>
    							</div> 
    							<div class="col-md-5">
    							    <div class="input-group">
                                        <input type="text" name="days_range[]" id="d1" class="form-control numericOnly days_range" value="30" style="max-width:25%;">
                                        <input type="text" name="days_range[]" id="d2" class="form-control numericOnly days_range" value="60" style="max-width:25%;">
                                        <input type="text" name="days_range[]" id="d3" class="form-control numericOnly days_range" value="90" style="max-width:25%;">
                                        <input type="text" name="days_range[]" id="d4" class="form-control numericOnly days_range" value="120" style="max-width:25%;">
                                    </div>
                                    <input type="hidden" name="report_type" id="report_type" value="2">
    							</div>
    							<!--<div class="col-md-2">
    								<select name="report_type" id="report_type" class="form-control">
    									<option value="1">Summary</option>
    									<option value="2">Agewise</option>
    								</select>
    							</div>     -->
    							<div class="col-md-2">   
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                    <div class="error fromDate"></div>
                                </div>     
                                <div class="col-md-3">  
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
                        </form>
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='commanTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center"><th colspan="6"><?=$pageHeader?></th></tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Account Name</th>
										<th style="min-width:50px;">City</th>
										<th style="min-width:50px;">Contact Person</th>
										<th style="min-width:50px;">Contact Number</th>
										<th style="min-width:50px;">Closing Balance</th>
									</tr>
								</thead>
								<tbody id="receivableData"></tbody>
								<tfoot class="thead-info tfoot">
								   <tr>
									   <th colspan="5" class="text-right">Total</th>
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
<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	//loadData();
    $(document).on('click','.loaddata',function(){loadData();});  
});

function loadData(pdf=""){
	$(".error").html("");
	var valid = 1;
	var from_date = $('#from_date').val();
	var to_date = $('#to_date').val();
	var os_type = $("#os_type").val();
	var report_type = $("#report_type").val();
	var days_range = $('input[name="days_range[]"]').map(function(){ if(parseFloat(this.value) > 0 ){return this.value;} }).get();
	if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
	if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
	if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
	var postData = {from_date:from_date, to_date:to_date,os_type:os_type,report_type:report_type,days_range:days_range};
	if(valid){
		if(pdf == "")
		{
			$.ajax({
				url: base_url + controller + '/getOutstanding',
				data: postData,
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#commanTable").DataTable().clear().destroy();
					$("#receivableData").html("");
					$("#theadData").html(data.thead);
					$("#receivableData").html(data.tbody);
					$(".tfoot").html(data.tfoot);
					$("#totalAmount").html(data.totalClBalance);
					var textAlign = [[1,3],[0,2,4],[5]];
					var rightCols = [5];
					if(days_range.length > 0)
					{
					    for(var i=6;i<=(days_range.length + 6);i++){rightCols.push(i);}
					}
					if(report_type == 2){textAlign = [[1,3],[0,2,4],rightCols];}

					displayTable('commanTable',JSON.stringify(textAlign));
				}
			});
		}
		else
		{
			var url = base_url + controller + '/getOutstanding/' + encodeURIComponent(window.btoa(JSON.stringify(postData)));
			console.log(url);
			window.open(url);
		}
	}
}
function displayTable(tableId,textAlign='',extraBtn = {}) {
	if(textAlign != ""){textAlign = JSON.parse(textAlign);}else{textAlign = {};}
	var textFormats = [];
	if(jQuery.isEmptyObject(textAlign))
	{
		textFormats = [
						{type: 'natural',targets: 0},
						{orderable: false,targets: "_all"},
						{orderable: false,targets: "_all"},
						{className: "text-center", "targets": "_all"}
					];
	}
	else
	{
		textFormats = [
		    {type: 'natural',targets: 0},
			{orderable: false,targets: "_all"},
			{orderable: false,targets: "_all"},
			{ className: "text-left", "targets": textAlign[0] },
			{ className: "text-center", "targets": textAlign[1] },  
			{ className: "text-right", "targets": textAlign[2] }
		];
	}
	var displayTable = $('#'+tableId).DataTable({
		responsive: true,
		"scrollY": '52vh',
		"scrollX": true,
		deferRender: true,
		scroller: true,
		destroy: true,
		// 'stateSave':false,
		"autoWidth" : false,
		order: [],
		"columnDefs": textFormats,
		pageLength: 25,
		language: {search: ""},
		lengthMenu: [
			[ 10, 20, 25, 50, 75, 100, 250,500 ],
			[ '10 rows', '20 rows', '25 rows', '50 rows', '75 rows', '100 rows','250 rows','500 rows' ]
		],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Pdf',action: function ( e, dt, node, config ) {loadData(1);}}],
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
	    "fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	displayTable.buttons().container().appendTo('#'+tableId+'_wrapper toolbar');
	$('.dataTables_filter .form-control-sm').css("width", "97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
	$('.dataTables_filter').css("text-align", "left");
	$('.dataTables_filter label').css("display", "block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
	setTimeout(function(){ displayTable.columns.adjust().draw();}, 10);
	$('.page-wrapper').resizer(function() {displayTable.columns.adjust().draw(); });
	return displayTable;
} 

</script>