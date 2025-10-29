<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>  
							<div class="col-md-6">
								<button type="button" class="btn waves-effect waves-light btn-success float-right mr-2 loaddata" title="Load Data">
									<i class="fas fa-sync-alt"></i> Load
								</button>
                                <select name="vendor_id" id="vendor_id" class="form-control single-select float-right" style="width: 80%;">
                                    <option value="">Select Vendor</option>
                                    <?php   
										foreach($vendorList as $row): 
											echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
										endforeach; 
                                    ?>
                                </select>
							</div>
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered jdt" >
								<thead class="thead-info" id="theadData">
									<!--<tr>
										<th colspan="20" style="text-align: center;">JOB WORK OUTWARD-INWARD REGISTER</th>
										<th colspan="3" style="text-align: center;">F ST 11/01.06.2020</th>
									</tr>
									<tr>
										<th colspan="23">Vendor Name: <span id="vendor_name"></span></th>	
									</tr>-->
									<tr>
										<th colspan="12" style="text-align: center;">Outward Details</th>
										<th colspan="8" style="text-align: center;">Inward Details</th>
										<th colspan="3" style="text-align: center;">F ST 11 00/01.06.2020</th>
									</tr>
									<tr class="text-center clonTR">
										<th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;">Job Order<br>No.</th>
										<th style="min-width:50px;">JJI Challan<br>No.</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:190px;">Material Disc.</th>
										<th style="min-width:180px;">Process</th>
										<th style="min-width:80px;">Qty.</th>
										<th style="min-width:50px;">UOM</th>
										<th style="min-width:100px;">Batch/Heat No.</th>
										<th style="min-width:50px;">Bag/<br>Caret</th>
										<th style="min-width:100px;">Remark</th>
										
										<th style="min-width:100px;">Date</th>
										<th style="min-width:100px;">Part No.</th>
										<th style="min-width:100px;">JJI Challan No.</th>
										<th style="min-width:50px;">Challan No.</th>
										<th style="min-width:80px;">Qty.</th>
										<th style="min-width:50px;">UOM</th>
										<th style="min-width:80px;">Balance Qty.</th>
										<th style="min-width:80px;">Rej./Under Dev.</th>
										<th style="min-width:100px;">Batch/Heat Code.</th>
										<th style="min-width:50px;">Bag/<br>Caret</th>
										<th style="min-width:100px;">Remark</th>
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
	$('.jdt thead .clonTR').clone(true).insertAfter( '.jdt thead tr:eq(1)' );
	var lastIndex = -1;
    $('.jdt thead tr:eq(2) th').each( function (index,value) {
        var title = $(this).text(); //placeholder="'+title+'"
		if(index == lastIndex){$(this).html( '' );}else{$(this).html( '<input type="text" style="width:100%;"/>' );}
	});
	$(document).on('click','.loaddata',function(e){
		$('#vendor_name').text($('#vendor_idc').val());
		var vendor_id = $('#vendor_id').val();
		if(vendor_id)
		{
			$.ajax({
				url: base_url + controller + '/getJobworkRegister',
				data: {vendor_id:vendor_id},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tblData);
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
							// { orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		order:[],
		orderCellsTop: true,
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel','colvis']
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	
	//Datatable Column Filter
    $('.jdt thead tr:eq(2) th').each( function (i) {
		$( 'input', this ).on( 'keyup change', function () {
			if ( reportTable.column(i).search() !== this.value ) {reportTable.column(i).search( this.value ).draw();}
		});
	} );
}
</script>