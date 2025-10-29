$(document).ready(function(){

    $(document).on('click','.loaddata',function(e){
		var valid = 1;
		var id = $('#machine_id').val();
		var month = $('#month').val();
		if($("#month").val() == ""){$(".month").html("Month is required.");valid=0;}
        var sendData = {id:id,month:month};
		if(valid)
		{            
            $.ajax({
                url: base_url + controller + '/monthWiseMachineReport',
                data: sendData,
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#theadData2").html("");
                    $("#theadData2").html(data.thead);
                    $("#tbodyData2").html("");
                    $("#tbodyData2").html(data.tbody);
                    $("#id").val("");
                    $("#id").val(data.id);
                }
            });		
        }
    }); 
});
function saveLog(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
    
	$.ajax({
		url: base_url + controller + '/saveMachineMaintanLog',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location.reload();   
            $(".chk-col-success").removeAttr("checked");
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}