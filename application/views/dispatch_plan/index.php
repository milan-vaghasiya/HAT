<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                 <ul class="nav nav-pills">
                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/")?>"  class="nav-link btn waves-effect waves-light btn-outline-info mr-1 active" >Orders</a> </li>

                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/plannedSo")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Planned S.O.</a> </li>
                                </ul>
                            </div>       
                            <div class="col-md-3">   
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
                                <div class="error fromDate"></div>
                            </div>     
                            <div class="col-md-4">  
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
                                    <tr class="text-center">
                                        <th colspan="17">Dispatch Plan </th>
                                    </tr>
									<tr>
                                        <th>   
                                            <input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkRequest" value=""><label for="masterSelect">ALL</label>
                                        </th>
										<th style="min-width:25px;">#</th>
										<th style="min-width:100px;">Delivery Date</th>
										<th style="min-width:80px;">SO NO</th>
										<th style="min-width:80px;">PO NO</th>
										<th style="min-width:80px;">PO Date</th>
										<th style="min-width:50px;">Party Code</th>
										<th style="min-width:100px;">Part No</th>
										<th style="min-width:100px;">Part Name</th>
										<th style="min-width:100px;">Part Size</th> 
										<th style="min-width:100px;">W.O. No.</th>
										<th style="min-width:80px;">Order Qty</th>
										<th style="min-width:50px;">Dispatch Qty</th>
										<th style="min-width:80px;">Plan Qty</th>
										<th style="min-width:50px;">Pending Qty</th>
                                        <th style="min-width:50px;">WIP Qty</th>
                                        <th style="min-width:50px;">RTD Qty</th>
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
<script>
$(document).ready(function(){
	reportTable();
    $(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if($("#to_date").val() < $("#from_date").val()){$(".toDate").html("Invalid Date.");valid=0;}
		if(valid)
		{
            $.ajax({
                url: base_url + controller + '/getDispatchPlan',
                data: {from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
                    $("#reportTable").dataTable().fnDestroy();
					$("#tbodyData").html(data.tbody);
					reportTable();
                }
            });
        }
    });   

    $(document).on('click', '.BulkRequest', function() {
        if ($(this).attr('id') == "masterSelect") {
            if ($(this).prop('checked') == true) {
                $(".bulkPlan").show();
                $("input[name='so_trans_id[]']").prop('checked', true);
            } else {
                $(".bulkPlan").hide();
                $("input[name='so_trans_id[]']").prop('checked', false);
            }
        } else {
            if ($("input[name='so_trans_id[]']").not(':checked').length != $("input[name='so_trans_id[]']").length) {
                $(".bulkPlan").show();
                $("#masterSelect").prop('checked', false);
            } else {
                $(".bulkPlan").hide();
            }

            if ($("input[name='so_trans_id[]']:checked").length == $("input[name='so_trans_id[]']").length) {
                $("#masterSelect").prop('checked', true);
                $(".bulkPlan").show();
            } else {
                $("#masterSelect").prop('checked', false);
            }
        }
    });

    $(document).on('click', '.bulkPlan', function() {
        var so_trans_id = [];
        $("input[name='so_trans_id[]']:checked").each(function() {
            so_trans_id.push(this.value);
        });
        var ids = so_trans_id.join(",");
        var  button = "both"; if (button == "" || button == null) {;  };
        var sendData = { id: ids };
        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/addPlan',   
            data: sendData,
        }).done(function(response){
            $("#modal-xl").modal();
            $('#modal-xl .modal-body').html('');
            $('#modal-xl .modal-title').html("Add Plan");
            $('#modal-xl .modal-body').html(response);
            $("#modal-xl"+" .modal-body form").attr('id','addPlan');
            $("#modal-xl  .modal-footer .btn-save").attr('onclick',"storePlan('addPlan','savePlan');");
            $("#modal-xl  .modal-footer .btn-close").attr('data-modal_id','create_challan');
            $("#modal-xl  .modal-footer .btn-close").show();
            $("#modal-xl  .modal-footer .btn-save").show();
            initModalSelect();
            $(".single-select").comboSelect();
            $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
            $("#modal-xl  .scrollable").perfectScrollbar({suppressScrollX: true});
            initMultiSelect();setPlaceHolder();
        });

    });

});


function storePlan(formId,fnsave,srposition=1){
	// var fd = $('#'+formId).serialize();
	
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$('#'+formId)[0].reset();$(".modal").modal('hide');
            $(".loaddata").trigger('click');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(srposition); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}


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
		"fnInitComplete":function(){$('.dataTables_scrollBody').perfectScrollbar();},
		"fnDrawCallback": function( oSettings ) {$('.dataTables_scrollBody').perfectScrollbar('destroy').perfectScrollbar();}
	});
	reportTable.buttons().container().appendTo( '#reportTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
    var bulkPlanBtn = '<button class="btn btn-outline-primary bulkPlan" tabindex="0" aria-controls="reportTable" type="button"><span>Bulk Plan</span></button>';
    reportTable.buttons().container().append(bulkPlanBtn);
    $(".bulkPlan").hide();
	return reportTable;
}
</script>