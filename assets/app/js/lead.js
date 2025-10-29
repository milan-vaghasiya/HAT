$(document).ready(function(){
	$(document).on('click','.leadAction',function(){
		var lead_id = $(this).data("id");
		var functionName = $(this).data("function");
		var fnSave = $(this).data("fnsave");
		var modalId = $(this).data('modal_id');
		var title = $(this).data('form_title');
		var entry_type = $(this).data('entry_type');
		var formId = functionName;
		if(fnSave == "" || fnSave == null){fnSave="save";}
		$.ajax({
			type: "POST",
			url: base_url + controller + '/' + functionName,
			data: {lead_id:lead_id,entry_type:entry_type}
		}).done(function(response){
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body .action-sheet-content').html("");
			$("#"+modalId+' .modal-body .action-sheet-content').html(response);
			$("#"+modalId+" .modal-body .action-sheet-content form").attr('id',formId);
			// $("#lead_id").val(lead_id);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveFollowup('"+formId+"','"+fnSave+"');");
			$("#"+modalId+" .modal-footer .btn-close").show();
			$("#"+modalId+" .modal-footer .btn-save").show();
			// $(".single-select").comboSelect();
			// $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setPlaceHolder();
		});
	});
	$(document).on('click','.closeApplointment',function(){
		var id = $(this).data("id");
		var functionName = $(this).data("function");
		var fnSave = $(this).data("fnsave");
		var modalId = $(this).data('modal_id');
		var title = $(this).data('form_title');
		var formId = functionName;
		if(fnSave == "" || fnSave == null){fnSave="save";}
		$.ajax({
			type: "POST",
			url: base_url + controller + '/closeApplointment',
			data: {id:id}
		}).done(function(response){
			$("#"+modalId).modal('show');
			$("#"+modalId+' .modal-body .action-sheet-content').html("");
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveFollowup('"+formId+"','"+fnSave+"');");
			$("#"+modalId+" .modal-footer .btn-close").show();
			$("#"+modalId+" .modal-footer .btn-save").show();
			
			// setPlaceHolder();
		});
	});

});

function trashLead(id,fnSave='delete',name='Record'){
var send_data = { id:id };
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
					url: base_url + controller + '/' + fnSave,
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
							if(fnSave!='delete'){$(".modal").modal('hide');}$('.reloadLeads').trigger('click');
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}
					}
				});
			}
		},
		cancel: {  btnClass: 'btn waves-effect waves-light btn-outline-secondary',action: function(){} }
	}
});
}

function addLead(data){
   
	$.ajax({ 
		type: "POST",   
		url: base_url + 'app/lead/addLead',   
		data: {}
	}).done(function(response){
		$("#"+data.modal_id+' .modal-body .action-sheet-content').html("");
		$("#"+data.modal_id+' .modal-title').html('Add Approach');
		$("#"+data.modal_id+' .modal-body .action-sheet-content').html(response);
		$("#"+data.modal_id+" .modal-body .action-sheet-content form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveLead('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		// $(".single-select").comboSelect();
		setPlaceHolder();
		// initMultiSelect();
	});
}


function editLead(data){
   
	$.ajax({ 
		type: "POST",   
		url: base_url + 'app/lead/edit',   
		data: {id:data.id}
	}).done(function(response){
		$("#"+data.modal_id+' .modal-body .action-sheet-content').html("");
		$("#"+data.modal_id+' .modal-title').html('Update Approach');
		$("#"+data.modal_id+' .modal-body .action-sheet-content').html(response);
		$("#"+data.modal_id+" .modal-body .action-sheet-content form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"saveLead('"+data.form_id+"');");
		if(data.button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(data.button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		// $(".single-select").comboSelect();
		setPlaceHolder();
		// initMultiSelect();
	});
}


function saveLead(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + '/lead/saveLead',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#DialogIconedSuccess .modal-body").html(data.message);
			$("#DialogIconedSuccess").modal('show');
			window.location.reload();
		}else{
			$("#DialogIconedDanger .modal-body").html(data.message);
			$("#DialogIconedDanger").modal('show');
		}				
	});
}

function saveFollowup(formId,fnSave){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/' + fnSave,
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		$(".error").html("");
		if(data.status===0){
			
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			$("#DialogIconedSuccess .modal-body").html(data.message);
			$("#DialogIconedSuccess").modal('show');
			window.location.reload();
		}else{
			$("#DialogIconedDanger .modal-body").html(data.message);
			$("#DialogIconedDanger").modal('show');
		}		
	});
}

