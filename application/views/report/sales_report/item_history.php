<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>     
                            <div class="col-md-2">
								<select name="location_id" id="location_id" class="form-control single-select">
									<option value="">Select ALL</option>
									<?php
										foreach($locationData as $row):
											echo '<option value="'.$row->id.'">'.$row->location.'</option>';
										endforeach;
									?>
								</select>
							</div>
							<div class="col-md-2">
								<select name="item_type" id="item_type" class="form-control single-select">
									<option value="">Select Item Type</option>
									<?php
										foreach($itemTypeData as $row):
											echo '<option value="'.$row->id.'">'.$row->group_name.'</option>';
										endforeach;
									?>
								</select>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<select name="item_id" id="item_id" class="form-control select2" style="width:70%;">
										<option value="">Select Item</option>
									</select>
									<div class="input-group-append">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
								</div>
							</div>
						</div>                                   
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="7">Item History </th>
                                    </tr>
									<tr>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Trans. Type</th>
										<th style="min-width:100px;">Ref. No.</th>
										<th style="min-width:50px;">Trans. Date</th>
										<th style="min-width:50px;">Inward</th>
										<th style="min-width:50px;">Outward</th>
										<th style="min-width:50px;">Balance</th>
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

	$(document).on('change','#item_type', function(e){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
                url: base_url + controller + '/getItemList',
                data: {item_type:item_type},
				type: "POST",
				dataType:'json',
				success:function(data){
					$("#item_id").html("");
					$("#item_id").html(data.itemData);
					$("#item_id").select2();
                }
            });
		}
	});

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var item_id = $('#item_id').val();
		var location_id = $('#location_id :selected').val();
		if($("#item_id").val() == ""){$(".item_id").html("Item is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getItemHistory',
                data: {item_id:item_id,location_id:location_id},
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