$(document).ready(function(){
	$(document).on('click','.createDeliveryChallan',function(){		
		var party_id = $(this).data('id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyOrders',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();
				$("#exampleModalLabel1").html('Create Challan');
				$("#party_so").attr('action',base_url + 'deliveryChallan/createChallan');
				$("#btn-create").html('<i class="fa fa-check"></i> Create Challan');
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#orderData").html("");
				$("#orderData").html(data.htmlData);
			}
		});
	});

	$(document).on('click','.createSalesInvoice',function(){		
		var party_id = $(this).data('id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyOrders',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();
				$("#exampleModalLabel1").html('Create Invoice');
				$("#party_so").attr('action',base_url + 'salesInvoice/createInvoice');
				$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#orderData").html("");
				$("#orderData").html(data.htmlData);
			}
		});
	});

    $(document).on('click',".requiredMaterial",function(){
		var item_id = $(this).data('id');
		var productName = $(this).data('product');
		var orderQty = $(this).data('qty');

		$.ajax({
			url: base_url + controller + '/getRequiredMaterial',
			data: {item_id:item_id,qty:orderQty},
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					$("#productName").html(productName);
					$("#orderQty").html(orderQty);
					$("#requiredMaterialModal").modal();
					$("#requiredItems").html("");
					$("#requiredItems").html(data.result);
				}
			}
		});
	});

	$(document).on('click',".invComplete",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
		var msg=$(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/completeInv',
							data: {id:id,val:val,msg:msg},
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
                                    initTable(); 
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
	});
	
	$(document).on('click',".orderComplete",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
		var msg=$(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/completeOrder',
							data: {id:id,val:val,msg:msg},
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
									window.location.reload();
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
	});

	$(document).on('click',".completeOrderItem",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg=$(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Order Item?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/completeOrderItem',
							data: {id:id,val:val,msg:msg},
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
									window.location.reload();
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
	});

	$(document).on('click',".approveSOrder",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Sales Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approveSOrder',
							data: {id:id,val:val,msg:msg},
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
									window.location.reload();
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
	});
	
	$(document).on('click','.createItemList',function(){		
        var id = $(this).data('id');
        var party_name = $(this).data('party_name');

        $.ajax({
            url : base_url + controller + '/getItemList',
            type: 'post',
            data:{id:id},
            dataType:'json',
            success:function(data){
                $("#itemModal").modal();
                $("#partyNames").html(party_name);
                $("#party_name").val(party_name);
                $("#party_id").val(party_id);
                $("#itemData").html("");
                $("#itemData").html(data.htmlData);
            }
        });
    });
	
	$(document).on('change','#sales_type_filter',function(){initSOTable($(this).val());});
});

function initSOTable(sales_type){
    //var sales_type = $("#sales_type").val();
    $('.ssTable').dataTable().fnDestroy();
    var tableOptions = {pageLength: 25,'stateSave':false};
    var tableHeaders = {'theads':'','textAlign':textAlign,'srnoPosition':1};
    var dataSet = {sales_type:sales_type};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
}