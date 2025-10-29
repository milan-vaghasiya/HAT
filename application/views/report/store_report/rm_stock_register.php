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
                                    <select id="item_id" name="item_id" class="form-control single-select req" style="width:50%;">
                                        <option value="">Select Item</option>
                                        <?php
    										foreach($itemList as $row):
    											echo '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
    										endforeach;  
                                        ?>
                                    </select>
                                    <select name="month" id="month" class="form-control single-select req" style="width:30%;">
                                        <option value="">Select Month</option>
                                        <?php
                                            $startDate = new DateTime($this->startYearDate);
                                            $endDate = new DateTime($this->endYearDate);
                                            
                                            $interval = new DateInterval('P1M'); // 1 month interval
                                            $period = new DatePeriod($startDate, $interval, $endDate);
                                            
                                            foreach ($period as $date):
                                                echo '<option value="'.$date->format('01-m-Y').'">'.$date->format('F - Y').'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect btn-block waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error item_id"></div>
                                <div class="error month"></div>
                            </div>                  
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <!-- <tr>
                                        <th colspan="5" class="text-left" ></th>
                                        <th class="text-center"></th>
                                    </tr> -->
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-right">Opening</th>
                                        <th class="text-right">Recd. Qty</th>
                                        <th class="text-right">Issue Qty</th>
                                        <th class="text-right">Closing Qty</th>
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
    $("#item_id").val(312);
    $("#item_id").comboSelect();
    
	reportTable();
    $(document).on('click','.loaddata',function(e){
		var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
		var month = $('#month').val();
		var item_id = $('#item_id').val();
		if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}
		if($("#item_id").val() == ""){$(".item_id").html("Item Name is required.");valid=0;}

		var sendData = {month:month, item_id:item_id};
		if(valid)
		{
			if(type == 1){
				var url =  base_url + '/reports/storeReport/getRmStockRegisterData/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
			}else{
				$.ajax({
					url: base_url + controller + '/getRmStockRegisterData',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#reportTable").DataTable().clear().destroy();
						$("#theadData").html("");
						$("#theadData").html(data.thead);
						$("#tbodyData").html("");
						$("#tbodyData").html(data.tbody);
						reportTable();
					}
				});
			}
		}
    });   
});

function reportTable(){
	var reportTable = $('#reportTable').DataTable({
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$('.loaddata').trigger('click');}}]
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