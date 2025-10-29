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
                            <div class="col-md-7">  
                                <div class="input-group">
                                    <select id="party_id" name="party_id" class="form-control single-select req" style="width:50%;">
        								<option value="">Select ALL Customer</option>
                                        <?php
    										foreach($customerList as $row):
    											if(!empty($row->party_code)){echo '<option value="'.$row->id.'">['.$row->party_code.'] '.$row->party_name.'</option>';}
    										endforeach;  
                                        ?>
                                    </select>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" style="width:15%;" />
                                    <div class="error fromDate"></div>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" style="width:15%;" />
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect btn-block waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
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
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr class="text-center">
										<th colspan="15"><h4>CUSTOMER ORDER MONITORING REGISTER</h4></th>
										<th colspan="3">F/MKT/03 (00/01.01.16)</th>
									</tr>
									<tr>
										<th rowspan="2">#</th>
										<th rowspan="2">OA Date</th>
										<th rowspan="2">P.O. Date</th>
										<th rowspan="2">P.O. No</th>
										<th rowspan="2">Customer Code</th>
										<th rowspan="2">Part</th>
										<th rowspan="2">W.O. No.</th>
										<th rowspan="2">Metal</th>
										<th rowspan="2">Actual Order Qty.</th>
										<th rowspan="2">Exp. Delivery Date</th>
										<th rowspan="2">RM Size</th>
										<th rowspan="2">Wtg./Pc</th>
										<th rowspan="2">Reqd. Wtg.</th>
										<th colspan="3">Disp. Details</th>
										<th rowspan="2">Total</th>
										<th rowspan="2">Disp. Wtg</th>
									</tr>
									<tr>
										<th>Actual Delivery Date</th>
										<th>Actual Delivered Qty.</th>
										<th>Total Qty. Delivered</th>										
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
<?=$floatingMenu?>
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var party_id = $('#party_id').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		var sendData = {from_date:from_date, to_date:to_date,party_id:party_id,type:type};
		if(valid)
		{
			if(type == 1){
				var url =  base_url + '/reports/salesReport/getOrderMonitor/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
				$.ajax({
					url: base_url + controller + '/getOrderMonitor',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").dataTable().fnDestroy();
						$("#theadData").html(data.thead);
						$("#tbodyData").html(data.tbody);
						reportTable();
					}
				});
			}
		}
    });   
});
function reportTable()
{
	var reportTable = $('#reportTable').DataTable( 
	{
		responsive: true,
		//scrollY: '55vh',
        //scrollCollapse: true,
		//"scrollX": true,
		//"scrollX": true,
		//"scrollCollapse":true,
		//'stateSave':true,
		//"autoWidth" : false,
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {}}]
		// $('.loaddata').trigger('click');
	});
	var printBtn = '<button class="btn btn-outline-primary loaddata" data-type="1" type="button"><span>PDF</span></button>';
    reportTable.buttons().container().append(printBtn);
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	$('.page-wrapper').resizer(function() {reportTable.columns.adjust().draw(); });
	return reportTable;
}
</script>