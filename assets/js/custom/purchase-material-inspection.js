$(document).ready(function(){
    $(document).on('click',".getInspectedMaterial",function(){
        var trans_id = $(this).data('trans_id');
        var grn_id = $(this).data('grn_id');
        var grn_no = $(this).data('grn_no');
        var grn_date = $(this).data('grn_date');
        var item_name = $(this).data('item_name');

        $("#grn_id").val(grn_id);
        $("#grnNo").val(grn_no);
        $("#grnDate").val(grn_date);
        $("#itemName").val(item_name);
        $.ajax({
            url:base_url + controller + '/getInspectedMaterial',
                type:'post',
                data:{id:trans_id},
                dataType:'json',
                success:function(data){
                    $("#recivedItems").html("");
                    $("#recivedItems").html(data);
                    
                }
        });
    });
});
function inspectedMaterialSave(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/inspectedMaterialSave',
		data:fd,
		type: "POST",
		dataType:"json",
		success:function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
                initTable(); $("#inspectionModel").modal('hide');
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}
	});
}