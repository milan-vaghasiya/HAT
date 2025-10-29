$(document).ready(function(){
    var numberOfChecked = $('.termCheck:checkbox:checked').length;
    $(".termsCounter").html(numberOfChecked);

	$(document).on('change','#rate_per',function(e){
		$(".error").html("");
		var rate_per = $(this).val();
		var rate = $('#rate').val();
       
        if(rate_per == 1){

            if($('#qty').val() != 0 && $('#qty').val() != "")
            {
                var perpcs = rate * $('#qty').val();
                $("#amount").val(perpcs); 
            } else { $(".qty_pcs").html("Qty Pcs is required."); $("#amount").val(0); } 

        } else if(rate_per == 2) {

            if($('#qty_kg').val() != 0 && $('#qty_kg').val() != "")
            {
                var perkg = rate * $('#qty_kg').val();
                $("#amount").val(perkg);
            } else { $(".qty_kg").html("Qty kg is required."); $("#amount").val(0); } 

        } else {
            $("#amount").val(0); 
        }
    });
    
	$(document).on("click",".closeTerms",function(){
        $("#termModel").modal('hide');
    });
    
	$(document).on("click",".termCheck",function(){
        var id = $(this).data('rowid');
		var numberOfChecked = $('.termCheck:checkbox:checked').length;
		$(".termsCounter").html(numberOfChecked);
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#term_id"+id).attr('disabled','disabled');
            $("#term_title"+id).attr('disabled','disabled');
            $("#condition"+id).attr('disabled','disabled');
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#term_id"+id).removeAttr('disabled');
            $("#term_title"+id).removeAttr('disabled');
            $("#condition"+id).removeAttr('disabled');
        }
    });
   
    $(document).on('change',"#product_id",function(){
        var IsValid = 1;
        var product_id = $(this).val();
        var vendor_id = $('#vendor_id').val();
        if(vendor_id == ""){
            $("#processSelect").html("");
            $("#process_id").val("");
            reInitMultiSelect(); 
            
            $(".vendor_id").html("Vendor is required");
            IsValid = 0;
        }
        
        if(IsValid){
            $.ajax({
                url: base_url + controller + "/getVendorProcessList",
                type: "POST",
                data:{product_id:product_id, vendor_id:vendor_id},
                dataType:"json",
                success:function(data){
                    $("#processSelect").html(data.options);
                    $("#process_id").val("");
                    reInitMultiSelect();
                }
            });
        }
    });

    $(document).on('click',".approveJobWorkOrder",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Job Work Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approveJobWorkOrder',
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
									initTable();
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
	
	$(document).on('click','.changeOrderStatus',function(){
		var id = $(this).data('id');
		var status = $(this).data('val');
		var msg = "";
		if(status == 1){
			msg = "Close";
		} else {
			msg = "Reopen";
		}

		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+msg+' this Job Work Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/changeJobStatus',
							data: {id:id,val:status},
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
});