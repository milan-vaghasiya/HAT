<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"> Rejection Review</h4>
                            </div>
                            <div class="col-md-6">
                                <select name="job_id" id="job_id" class="form-control single-select" style="width:50%;float:right;" >
                                    <option value="">Select Job Card</option>
                                    <?php
                                        foreach($jobCardList as $row):
                                            echo '<option value="'.$row->id.'">['.$row->item_code.'] '.getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='finalInspectionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
    $(document).on('change',"#job_id",function(){
        var job_id = $(this).val();
        $('.ssTable').dataTable().fnDestroy();
        var tableOptions = {pageLength: 25,'stateSave':false};
        var tableHeaders = {'theads':'','textAlign':textAlign};
        var dataSet = {job_id:job_id};
        ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
    });
});

function inspection(data){
    var button = data.button;
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnSave = data.fnSave;if(fnSave == "" || fnSave == null){fnSave="save";}
	var postData = {id:data.id,product_name:data.product_name,pending_qty:data.pending_qty,job_card_id:data.job_card_id,product_id:data.product_id,rejection_type_id:data.rejection_type_id,job_inward_id:data.job_inward_id,operator_id:data.operator_id,machine_id:data.machine_id};
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: postData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnSave+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
        inspectionDataTable();
	});
}
function inspectionDataTable(){
    var inspectionTable = $('#inspectionTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	inspectionTable.buttons().container().appendTo( '#inspectionTable_wrapper .col-md-6:eq(0)' );
	return inspectionTable;
}

function inspectionSave(){
    var rejection_id = $("#rejection_id").val();
    var pending_qty = $("#pending_qty").val();
    var job_card_id = $("#job_card_id").val();
    var product_id = $("#product_id").val();
    var rejection_type_id = $("#rejection_type_id").val();
    var job_inward_id = $("#job_inward_id").val();
    var operator_id = $("#operator_id").val();
    var machine_id = $("#machine_id").val();
    var ok_qty = $("#ok_qty").val();
    var ud_qty = $("#ud_qty").val();
    var scrape_qty = $("#scrape_qty").val();
    var remark = $("#remark").val();

    $(".ok_qty").html("");
    if(ok_qty == "" && ok_qty == "0.000" && isNaN(ok_qty) && ud_qty == "" && ud_qty == "0.000" && isNaN(ud_qty) && scrape_qty == "" && scrape_qty == "0.000" && isNaN(scrape_qty)){
        $(".ok_qty").html("Qty is required.");
    }else{
        var total_qty = parseFloat(parseFloat(ok_qty) + parseFloat(ud_qty) + parseFloat(scrape_qty)).toFixed(3);
        if(parseFloat(total_qty) > parseFloat(pending_qty)){
            $(".ok_qty").html("Invalid Qty.");
        }else{
            var postData = {rejection_id:rejection_id,ok_qty:ok_qty,ud_qty:ud_qty,scrape_qty:scrape_qty,remark:remark,job_card_id:job_card_id,product_id:product_id,rejection_type_id:rejection_type_id,job_inward_id:job_inward_id,operator_id:operator_id,machine_id:machine_id};
            $.ajax({
                url : base_url + controller + '/save',
                type: 'post',
                data : postData,
                dataType : 'json',
                success:function(data){
                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

                    pending_qty = parseFloat(parseFloat(pending_qty) - parseFloat(total_qty)).toFixed(3);
                    $("#pending_qty").val(pending_qty);
                    $("#ProductPendingQty").html(pending_qty);

                    $("#ok_qty").val(0);
                    $("#ud_qty").val(0);
                    $("#scrape_qty").val(0);
                    $("#remark").val("");

                    $("#inspectionData").html("");
                    $("#inspectionTable").dataTable().fnDestroy();
                    $("#inspectionData").html(data.htmlData);
                    inspectionDataTable();

                    var job_id = $("#job_id").val();
                    $('.ssTable').dataTable().fnDestroy();
                    var tableOptions = {pageLength: 25,'stateSave':false};
                    var tableHeaders = {'theads':'','textAlign':textAlign};
                    var dataSet = {job_id:job_id};
                    ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
                }
            });
        }
    }
}

function trashInspection(id,qty,name="Record"){
    var rejection_id = $("#rejection_id").val();

	var send_data = { id:id,rejection_id:rejection_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/delete',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });

								var pending_qty = $("#pending_qty").val();
								pending_qty = parseFloat(parseFloat(pending_qty) + parseFloat(qty)).toFixed(3);
                                $("#pending_qty").val(pending_qty);
                                $("#ProductPendingQty").html(pending_qty);

                                $("#inspectionData").html("");
                                $("#inspectionTable").dataTable().fnDestroy();
                                $("#inspectionData").html(data.htmlData);
                                inspectionDataTable();
							}
                            var job_id = $("#job_id").val();
                            $('.ssTable').dataTable().fnDestroy();
                            var tableOptions = {pageLength: 25,'stateSave':false};
                            var tableHeaders = {'theads':'','textAlign':textAlign};
                            var dataSet = {job_id:job_id};
                            ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}
</script>