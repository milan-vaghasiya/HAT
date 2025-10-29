$(document).ready(function(){
	
	$(".bt-switch").bootstrapSwitch();
	
    $('body').on('switchChange.bootstrapSwitch','.bt-switch',function () {
		var id = $(this).data('id');
		var value = $(this).data('val');
		var row_id = $(this).data('row_id');
		$.ajax({
			url: base_url + controller + '/activeInactive',
			type:'post',
			data:{id:id,value:value},
			dataType:'json',
			success:function(data){
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					if(value == 1){
						$("#activeInactive"+row_id).prop("checked",true);
						$("#activeInactive"+row_id).data('val',"0");
					}else{
						$("#activeInactive"+row_id).prop("checked",false);
						$("#activeInactive"+row_id).data('val',"1");
					}
				}
			}
		});
	});
	
	
	/* $(document).on('change','#emp_dept_id',function(){
		var id = $(this).val();
		if(id == ""){
			$("#emp_designation").html('<option value="">Select Designation</option>');
			$(".single-select").comboSelect();
		}else{
			$.ajax({
				url: base_url + controller + '/getDesignation',
				type:'post',
				data:{id:id},
				dataType:'json',
				success:function(data){
					if(data.status==0)
					{
						swal("Sorry...!", data.message, "error");
					}
					else
					{
						$("#emp_designation").html(data.result);
						$(".single-select").comboSelect();
						$("#emp_designation").focus();
					}
				}
			});
		}
	}); */
});

//Created By Karmi @13/01/2022
function changeEmpPsw(id,name='Password'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Change Employee Password?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/changeEmpPsw',
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
								initTable(); initMultiSelect();
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