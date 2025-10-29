$(document).ready(function(){
    receiveTable();	
    $(document).on('click','.returnItem',function(){
        $("#returnItem").modal();
        var dataRow = $(this).data('row');
        $("#ref_id").val(dataRow.ref_id);
        $("#ref_no").val(dataRow.ref_no);
        $("#location_id").val(dataRow.location_id);
        $("#item_id").val(dataRow.item_id);
        $("#ProductItemName").html(dataRow.item_name);
        $("#ProductPendingQty").html(dataRow.pending_qty);

        $.ajax({
            url: base_url + controller + "/getReceiveItemTrans",
            type:'post',
            data:{item_id:dataRow.item_id,challan_trans_id:dataRow.ref_id,trans_type:1},
            dataType:"json",
            success:function(data){
                $("#receiveItemTable").dataTable().fnDestroy();
                $("#receiveItemTableData").html("");				
				$("#receiveItemTableData").html(data.resultHtml);
				receiveTable();
            }
        });
    });
});

function saveReceiveItem(frm){
    var fd = new FormData(frm);
    var qty = $("#qty").val();
    $.ajax({
		url: base_url + controller + '/saveReceiveItem',
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
			$("#qty").val("");
            $("#receiveItemTable").dataTable().fnDestroy();
            $("#receiveItemTableData").html("");				
            $("#receiveItemTableData").html(data.resultHtml);
            receiveTable();
            var pending_qty = 0;
            pending_qty = parseFloat(parseFloat($("#ProductPendingQty").html()) - parseFloat(qty)).toFixed(3);
            $("#ProductPendingQty").html(pending_qty);
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{			
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function trashReceiveItem(id,qty,name='Record'){
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
						url: base_url + controller + '/deleteReceiveItem',
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

								$("#receiveItemTable").dataTable().fnDestroy();
                                $("#receiveItemTableData").html("");				
                                $("#receiveItemTableData").html(data.resultHtml);
                                receiveTable();

                                var pending_qty = 0;
                                pending_qty = parseFloat(parseFloat($("#ProductPendingQty").html()) + parseFloat(qty)).toFixed(3);
                                $("#ProductPendingQty").html(pending_qty);
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

function receiveTable(){
	var receiveTable = $('#receiveItemTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'pageLength','copy', 'excel']
	});
	receiveTable.buttons().container().appendTo( '#receiveItemTable_wrapper .col-md-6:eq(0)' );
	return receiveTable;
};