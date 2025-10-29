<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
						<div class="row">
                            <div class="col-md-4 form-group">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-3 form-group">
                                <select name="itemSelect" id="itemSelect" data-input_id="item_id" class="form-control jp_multiselect_all" multiple="multiple">
                                    
                                    <?php
                                        foreach ($itemDataList as $row) :
                                         $selected = (!empty($itemDataList) && (in_array($row->id, $itemDataList))) ? "selected" : "";
                                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->item_code . '</option>';
                                        endforeach;
                                    ?>
                                </select>
                            	<input type="hidden" name="item_id" id="item_id" value="<?=(!empty($itemDataList->item_id))? implode(',' , $itemDataList->item_id) : ""?>" /> 
                            	<div class="error item_id"></div>
                            </div>  
                            <div class="col-md-2 form-group">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-d')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-3 form-group">  
                                <div class="input-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
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
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
                                        <th style="min-width:50px;">#</th>
										<th style="min-width:100px;">Date</th>
										<th style="min-width:80px;">Part No</th>
										<th style="min-width:80px;">Setup No.</th>
										<th style="min-width:100px;">Shift</th>
										<th style="min-width:150px;">Machine No.</th>
										<th style="min-width:50px;">Operator Name</th>
										<th style="min-width:50px;">Batch Code</th>
                                        <th style="min-width:100px;">Rework Qty.</th>
										<th style="min-width:50px;">Reason of Rework</th>
										<th style="min-width:50px;">Rework Remark</th>
										<th style="min-width:50px;">Defect Belong To</th>
										<th style="min-width:50px;">Rework From</th>
										<th style="min-width:50px;">Rejection Qty.</th>
										<th style="min-width:50px;">Reason of Rejection</th>
										<th style="min-width:50px;">Rejection Remarks</th>
										<th style="min-width:50px;">Defect Belong To</th>
										<th style="min-width:50px;">Rejection From</th>
										<th style="min-width:50px;">Rejection Cost</th>
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
									<th></th>
                                    <th></th>
                                    <th></th>
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
	
	$('.jp_multiselect_all').multiselect({
        allSelectedText: 'All',
        maxHeight: 200,
        includeSelectAllOption: true,
        buttonWidth: '100%'
    }).multiselect('selectAll', true).multiselect('updateButtonText');
    $('.form-check-input').addClass('filled-in');
	$('.multiselect-filter i').removeClass('fas');
	$('.multiselect-filter i').removeClass('fa-sm');
	$('.multiselect-filter i').addClass('fa');
	$('.multiselect-container.dropdown-menu').addClass('scrollable');
	$('.multiselect-container.dropdown-menu').css('max-height','200px');
	$('.scrollable').perfectScrollbar({wheelPropagation: !0});

    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
        //var item_id = $('#item_id').val();
        var item_id = $('#itemSelect').val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if(item_id.length === 0){$(".item_id").html("Part is required.");valid=0;}
		if(from_date == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if(to_date == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getRejectionMonitoring',
                data: {item_id:item_id,from_date:from_date, to_date:to_date},
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
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {$(".loaddata").trigger('click');}}]
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