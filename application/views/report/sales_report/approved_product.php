<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title pageHeader">LIST OF CUSTOMER APPROVED  PRODUCT & DRAWING</h4>
                            </div>
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:80%;">
                                        <option value="">Select All Customer</option>
                                        <?php
                                            foreach($partyData as $row):
                                                echo '<option value="'.$row->id.'">['.$row->party_code.'] '.$row->party_name.'</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data" data-type="0"><i class="fas fa-sync-alt"></i> Load</button>
                                    </div>
                                </div>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='custProductTable' class="table table-bordered">                          
								<thead class="thead-info" id="theadData">
                                <tr class="text-center">
                                        <th colspan="11">LIST OF CUSTOMER APPROVED  PRODUCT & DRAWING</th>
                                        <th colspan="2">D/MKT/02 (00/01.01.16)</th>
                                </tr>
									<tr>
										<th rowspan="2">Sr. No.</th>
										<th rowspan="2">Product Description</th>
										<th rowspan="2">Part Name</th>
										<th rowspan="2">Drawing No.</th>
										<th colspan="2">Revision</th>
										<th rowspan="2">Received Date</th>
										<th rowspan="2">RM Size</th>
										<th rowspan="2">RM Weight</th>
										<th rowspan="2">Finish Weight</th>
										<th rowspan="2">Material Grade</th>
										<th rowspan="2">Distributed To</th>
										<th rowspan="2">Remarks</th>
									</tr>
                                    <tr>
                                        <th>No.</th>
                                        <th>Date</th>
                                    </tr>
								</thead>
								<tbody id="tbodyData">		
								</tbody>
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
		var valid = 1;
		var party_id = $('#party_id').val();
        var sendData = {party_id:party_id,type:type};
		if(valid)
		{
            if(type == 1){
				var url =  base_url + '/reports/salesReport/getlistOfCustomersApprovedProductDrawing/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
				$.ajax({
					url: base_url + '/reports/salesReport/getlistOfCustomersApprovedProductDrawing',
					data: sendData,
					type: "POST",
					dataType:'json',
					success:function(data){
						$("#custProductTable").dataTable().fnDestroy();
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
	var reportTable = $('#custProductTable').DataTable( 
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',data:0,action: function ( e, dt, node, config ) {}}]
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