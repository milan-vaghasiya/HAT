$(document).ready(function(){
	$(document).on('change',"#item_id",function(){
		var id = $(this).val();
		if(id != ""){
			$.ajax({
				url:base_url + controller + '/getItemData',
				data:{id:id},
				type:'post',
				dataType:'json',
				success:function(data){
					$("#pending_qty").val(data.pending_inspection_qty);
				}
			});
		}else{
			$("#pending_qty").val(0);
		}
	});
});