$(document).ready(function(){
    $(document).on('click','.enquiryConfirmed',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var enq_id = $(this).data('id');
		var partyName = $(this).data('party');
		var enquiry_no = $(this).data('enqno');
		var enquiry_date = $(this).data('enqdate');	

        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {enq_id:enq_id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"enquiryConfirmedSave('"+formId+"');");
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
			$(".single-select").comboSelect();

            $("#party_name").html(partyName);
            $("#enquiry_no").html(enquiry_no);
            $("#enquiry_date").html(enquiry_date);
            $("#enq_id").val(enq_id);

            $('.floatOnly').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    });

    $(document).on("click",".itemCheck",function(){
        var id = $(this).data('rowid');
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#item_name"+id).attr('disabled','disabled');
            $("#qty"+id).attr('disabled','disabled');
            $("#rate"+id).attr('disabled','disabled');
            $("#gst_per"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#item_name"+id).removeAttr('disabled');
            $("#qty"+id).removeAttr('disabled');
            $("#rate"+id).removeAttr('disabled');
            $("#gst_per"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
        }
    });
    
});

function enquiryConfirmedSave(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/enquiryConfirmed',
		data:fd,
		type: "POST",
		dataType:"json",
    }).done(function(data){
        if(data.status===0){
            $(".error").html("");
            $.each( data.message, function( key, value ) {
                $("."+key).html(value);
            });
        }else{
            initTable(); $(".modal").modal('hide');
            toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }		
	});
}

function closeEnquiry(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to close this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/closeEnquiry',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
                                toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
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
}

function reopenEnquiry(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to reopen this '+name+'?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/reopenEnquiry',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
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
}