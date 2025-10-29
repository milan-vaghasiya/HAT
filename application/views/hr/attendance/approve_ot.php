<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title">Approve OT</h4>
                            </div>
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="date" name="attendance_date" id="attendance_date" class="form-control" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loadData" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                            </div>
						</div>                                         
                    </div>
                    <div class="card-body" style="min-height:50vh;">
                        <div class="table-responsive">
							<table id="empOtTable" class="table table-bordered jpDataTable">
								<thead class="thead-info">
									<tr>
										<th>Action</th>
										<th>#</th>
										<th>Emp Code</th>
										<th>Emp Name</th>
										<!--<th>Department</th>-->
										<th>Shift</th>
										<th>Date</th>
										<!--<th>Day</th>
										<th>Status</th>
										<th>WH</th>
										<th>Lunch</th>
										<th>Ex. Hrs</th>
										<th>TWH</th>-->
										<th>OT</th>
										<th>AOT</th>
										<th>Adj. From</th>
										<th>Adj. To</th>
										<th>All Punches</th>
									</tr>
								</thead>
								<tbody id="empOtTabledata">
								
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
<script>
$(document).ready(function(){
    reportTable('empOtTable');
    
    $(document).on('click','.loadData',function(){
        $(".error").html("");
		var valid = 1;    
		var attendance_date = $('#attendance_date').val();
		if(attendance_date == ""){$(".fromDate").html("Date is required.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getEmployeeAttendanceData',
				data: {from_date:attendance_date,to_date:attendance_date},
				type: "POST",
				dataType:'json',
				success:function(data){
				    $('#empOtTable').DataTable().clear().destroy();
					$("#empOtTabledata").html(data.tbody);
					reportTable('empOtTable');
				}
			});
		}
    });
});

function approveOT(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnEdit = data.fnedit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var savebtn_text = data.savebtn_text;if(savebtn_text == "" || savebtn_text == null){savebtn_text="Save";}
	
	var sendData = {id:data.id,ot:data.ot};
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: sendData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		//$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveApproveOT('"+data.form_id+"','"+fnsave+"');");
		$("#"+data.modal_id+" .modal-footer .btn-save-close").attr('onclick',"store('"+data.form_id+"','"+fnsave+"','save_close');");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.form_id);
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
			$("#"+data.modalId+" .modal-footer .btn-save-close").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
            $("#"+data.modalId+" .modal-footer .btn-save-close").show();
		}
		initModalSelect();
		$(".single-select").comboSelect();
		$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
	});
}

function saveApproveOT(formId,fnsave){
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
    		$('#'+formId)[0].reset();$(".modal").modal('hide');$(".loadData").trigger('click');
    		toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
    	}else{
    		toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
    	}
	});
}

function reportTable(tableId){
	var reportTable = $('#'+tableId).DataTable({
    		responsive: true,
    		//'stateSave':true,
    		"autoWidth" : false,
    		// order:[],
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
    		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    		buttons: [ 'pageLength', 'excel','colvis']
	});
	reportTable.buttons().container().appendTo( '#'+tableId+'_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
}

</script>