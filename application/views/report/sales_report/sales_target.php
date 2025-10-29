<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                        	<div class="col-md-7">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div> 
                            <div class="col-md-3 form-group">
								<input type= "hidden"  name="sales_executive" id="sales_executive" value="">
								<select name="month" id="month" class="form-control single-select">
									<option value="">Month</option>
									<?php   
										foreach($monthData as $row): 
											echo '<option value="'.$row['val'].'">'.$row['label'].'</option>';
										endforeach; 
									?>
								</select>
								<div class="error month"></div>
							</div>
                            <div class="col-md-2 form-group">  
                                <button type="button" class="btn waves-effect waves-light btn-success btn-block loaddata" title="Load Data"><i class="fas fa-sync-alt"></i> Load</button>
                            </div> 	             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="targetDataForm">
							<div class="hiddenInputs"></div>
							<div class="table-responsive">
								<table id='reportTable' class="table table-bordered jpDataTable">
									<thead class="thead-info">
										<tr>
											<th style="width:5%;">#</th>
											<th>Customer Name</th>
											<th>Sales Executive</th>
											<th>Business Target</th>
											<th>Order Received</th>
											<th>Invoice Generated</th>
											<th>Performance</th>
										</tr>
									</thead>
									<tbody class="salesTargetData"></tbody>
									<tfoot id="tfootData">
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
                        </form>
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
	$(document).on("click",".loaddata",function(){
        var sales_executive = $("#sales_executive").val();
        var month = $("#month").val();

		$.ajax({
			url:base_url + controller + '/getTargetRows',
			type:'post',
			data:{sales_executive:sales_executive,month:month},
			dataType:'json',
			success:function(data)
			{
				if(data.status===0){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else {
					$("#salesTargetData").html("");$(".hiddenInputs").html("");
					$('#reportTable').DataTable().clear().destroy();
					$(".salesTargetData").html(data.targetData);
					$("#tfootData").html(data.tfoot);
					$(".hiddenInputs").html(data.hiddenInputs);
					reportTable();
				}
				
			}
		});
	}); 
});

function reportTable() {
		var reportTable = $('#reportTable').DataTable({
			responsive: true,
			scrollY: '55vh',
			scrollCollapse: true,
			"scrollX": true,
			"scrollCollapse": true,
			//'stateSave':true,
			"autoWidth": false,
			order: [],
			"columnDefs": [{
					type: 'natural',
					targets: 0
				},
				{
					orderable: false,
					targets: "_all"
				},
				{
					className: "text-center",
					targets: [0, 1]
				},
				{
					className: "text-center",
					"targets": "_all"
				}
			],
			pageLength: 25,
			language: {
				search: ""
			},
			lengthMenu: [
				[10, 25, 50, 100, -1],
				['10 rows', '25 rows', '50 rows', '100 rows', 'Show all']
			],
			dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" + "<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			buttons: ['pageLength', 'excel'],
			"initComplete": function(settings, json) {
				$('body').find('.dataTables_scrollBody').addClass("ps-scrollbar");
			}
		});
		reportTable.buttons().container().appendTo('#reportTable_wrapper toolbar');
		$('.dataTables_filter .form-control-sm').css("width", "97%");
		$('.dataTables_filter .form-control-sm').attr("placeholder", "Search.....");
		$('.dataTables_filter').css("text-align", "left");
		$('.dataTables_filter label').css("display", "block");
		$('.btn-group>.btn:first-child').css("border-top-right-radius", "0");
		$('.btn-group>.btn:first-child').css("border-bottom-right-radius", "0");
		return reportTable;
	}

</script>