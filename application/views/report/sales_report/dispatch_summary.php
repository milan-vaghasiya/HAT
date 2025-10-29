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
							<div class="col-md-4">
								<select name="party_id" id="party_id" class="form-control single-select">
									<option value="">Select Customer</option>
									<?php
										foreach($partyData as $row):
											echo '<option value="'.$row->id.'">['.$row->party_code.'] '.$row->party_name.'</option>';
										endforeach;
									?>
								</select>
							</div>
							<div class="col-md-3 form-group">
								<select name="itemSelect" id="itemSelect" data-input_id="item_id" class="form-control jp_multiselect req" multiple="multiple"></select>
								<input type="hidden" name="item_id" id="item_id" value="" />
								<div class="error item_id"></div>
							</div>
                            <div class="col-md-2">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
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
                                        <th colspan="10">Dispatch Summary </th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">S.O. No.</th>
										<th style="min-width:100px;">Cust P.O. No.</th>
										<th style="min-width:100px;">Customer</th>
										<th style="min-width:100px;">Part</th>
										<th style="min-width:100px;">Inv./Ch. No.</th>
										<th style="min-width:100px;">Dispatch date</th>
										<th style="min-width:50px;">Quantity</th>
										<th style="min-width:50px;">Price</th>
										<th style="min-width:50px;">Total Amount</th>
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

	$(document).on('change','#party_id', function(e){
		var party_id = $(this).val();
		if(party_id){
			$.ajax({
                url: base_url + controller + '/getPartyItems',
                data: {party_id:party_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#itemSelect").html("");
					$("#itemSelect").html(data.partyItems);
					reInitMultiSelect();
                }
            });
		}
	});

    $(document).on('click','.loaddata',function(e){
		var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
		var party_id = $('#party_id').val();
		var item_id = $('#item_id').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#party_id").val() == ""){$(".party_id").html("Customer is required.");valid=0;}
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		var sendData = {party_id:party_id,item_id:item_id,from_date:from_date, to_date:to_date,type:type};
		if(valid)
		{
			if(type == 1){
				var url =  base_url + '/reports/salesReport/getDispatchSummary/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
				$.ajax({
					url: base_url + controller + '/getDispatchSummary',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").dataTable().fnDestroy();
						$("#tbodyData").html(data.tbody);
						$("#tfootData").html(data.tfoot);
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
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel'],
		"initComplete": function(settings, json) {$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");}
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
	return reportTable;
}
</script>