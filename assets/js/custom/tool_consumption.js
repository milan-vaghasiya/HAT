$(document).ready(function() {
	$(document).on("change","#sub_group",function(){
		var sub_group = $(this).val();
		$.ajax({
			url: base_url + controller + '/getGroupWiseItem',
			data: {sub_group:sub_group},
			type: "POST",
			dataType:'json',
			success:function(data){
				$(".toolingItem").html('');
				$(".toolingItem").html(data.option);
				$(".toolingItem").comboSelect();
			}
		});
	});
});

function saveToolConsumption(formId){	
	setPlaceHolder();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/saveToolConsumption',
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
			$('#toolBody').html('');
			$('#toolBody').html(data.tbody);
			initTable(0);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			$("#process_id").val("");
			$("#sub_group").val("");
			$("#ref_item_id").val("");
			$("#req_qty").val("");
			$("#used_for").val("");
			$("#tool_unit").val("");
			$("#remark").val("");
			$(".single-select").comboSelect();
		}else{
			initTable(0);
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function deleteToolConsumption(id,item_id){
	var send_data = { id:id,item_id:item_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Record?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteToolConsumption',
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
								initTable(0); initMultiSelect();
								$('#toolBody').html("");
								$('#toolBody').html(data.tbody);
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
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