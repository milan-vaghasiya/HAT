<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>       
                            <div class="col-md-2 pl-0">   
                                <select class="form-control select2" data-placeholder="Party ALL" id="party">
                                    <option value=""></option>
                                    <option value="0">ALL</option>
                                    <?php
                                    foreach($partyList as $partyRow) { ?>
                                        <option value="<?php echo $partyRow->id; ?>"><?php echo $partyRow->party_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>  
                            <div class="col-md-2 pl-0">   
                            <select class="form-control select2" data-placeholder="Product ALL" id="product">
                                    <option value=""></option>
                                    <option value="0">ALL</option>
                                    <?php
                                        foreach($productList as $prdRow){ ?>
                                            <option value="<?php echo $prdRow->id; ?>"><?php echo $prdRow->item_name; ?></option>
                                    <?php } ?>
                                </select>                            
                            </div>  
                            <div class="col-md-2 pl-0">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>  
                            <div class="col-md-2 pl-0">   
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
                            </div>
                            <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
                                <i class="fas fa-sync-alt"></i> Load
                            </button>
                            <div class="error toDate"></div>     
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:80px;">Invoice Date</th>
										<th style="min-width:50px;">Invoice No</th>
										<th style="min-width:100px;">Party Name</th>
										<th style="min-width:100px;">Taxable Amount</th>
										<th style="min-width:50px;">GST</th>
										<th style="min-width:80px;">Discount</th>
										<th style="min-width:50px;">Net Amount</th>
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
		var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
        var party=$("#party").val();
        var product=$("#product").val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
        var sendData = {from_date:from_date, to_date:to_date,product:product,party:party,type:type};
		if(valid)
		{
            if(type == 1){
				var url =  base_url + '/reports/salesReport/getMonthlySalesData/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
                $.ajax({
                    url: base_url + controller + '/getMonthlySalesData',
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {}}]
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