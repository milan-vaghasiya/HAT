$(document).ready(function(){
    $(document).on('click','.sendQuotation',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var enq_id = $(this).data('id');
		var partyName = $(this).data('party');
		var enquiry_no = $(this).data('enqno');
		var enquiry_date = $(this).data('enqdate');	
		var ref_by = $(this).data('ref_by');	
		
        $.ajax({ 
            type: "POST",   
            url: base_url + 'salesEnquiry/' + functionName,   
            data: {enq_id:enq_id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveQuotation('"+formId+"');");
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
            $("#customer_name").val(partyName);
            $("#ref_by").val(ref_by);
            $("#enquiry_no").html(enquiry_no);
            $("#enquiry_date").html(enquiry_date);
            $("#enq_id").val(enq_id);
            $(".modal-lg").attr("style","max-width: 70% !important;");
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
            $("#price"+id).attr('disabled','disabled');
            $("#currency"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');

        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#item_name"+id).removeAttr('disabled');
            $("#qty"+id).removeAttr('disabled');
            $("#price"+id).removeAttr('disabled');
            $("#currency"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
        }
    });

    $(document).on('keyup change',".countItem1",function(){
		var id = $(this).data('id');
        var gstPer = $("#gst_per"+id).val();
        var qty = $("#qty"+id).val();
        var price = $("#price"+id).val();

        if(gstPer == "" || gstPer == "0" || gstPer == "0.00" || isNaN(gstPer)){
            gstPer = 0;
        }
        if(qty == "" || qty == "0" || qty == "0.000" || isNaN(qty)){
            qty = 0;
        }
        if(price == "" || price == "0" || price == "0.00" || isNaN(price)){
            price = 0;
        }

        var amount = 0;var totalAmount = 0;var igstAmt = 0; var igstPer = 0;
        var cgstAmt = 0;var sgstAmt = 0;var cgstPer = 0;var sgstPer = 0;

        amount = parseFloat(parseFloat(qty) * parseFloat(price)).toFixed(2);

        cgstPer = parseFloat(parseFloat(gstPer)/2).toFixed(2);
        sgstPer = parseFloat(parseFloat(gstPer)/2).toFixed(2);
        
        cgstAmt = parseFloat((cgstPer * amount )/100).toFixed(2);
        sgstAmt = parseFloat((sgstPer * amount )/100).toFixed(2);
        
        igstPer = parseFloat(gstPer).toFixed(2);
        igstAmt = parseFloat((igstPer * amount )/100).toFixed(2);
        
        totalAmount = parseFloat(parseFloat(amount) + parseFloat(igstAmt)).toFixed(2);

        $("#t_amount"+id).val(totalAmount);
        $("#amount"+id).val(amount);
        $("#igst_amount"+id).val(igstAmt);
        $("#cgst_amount"+id).val(cgstAmt);
        $("#sgst_amount"+id).val(sgstAmt);
        $("#total_amount"+id).val(totalAmount);					

        claculateColumn();
	});
    
});

function saveQuotation(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'salesEnquiry/saveQuotation',
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

function claculateColumn()
{
	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	
	var netAmtArray = $("input[name='total_amount[]']").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
			
	var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();;
    var igstAmtSum = 0;
	$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
	$('#igst_amt_total').val("");
	$('#igst_amt_total').val(igstAmtSum.toFixed(2));
	
	var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();;
    var cgstAmtSum = 0;
	$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
	$('#cgst_amt_total').val("");
	$('#cgst_amt_total').val(cgstAmtSum.toFixed(2));
	
	var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();;
    var sgstAmtSum = 0;
	$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
	$('#sgst_amt_total').val("");
	$('#sgst_amt_total').val(sgstAmtSum.toFixed(2));
	
	/* var discAmtArr = $("input[name='disc_amt[]']").map(function(){return $(this).val();}).get();;
    var discAmtSum = 0;
	$.each(discAmtArr,function(){discAmtSum += parseFloat(this) || 0;});
	$('#disc_amt_total').val("");
	$('#disc_amt_total').val(discAmtSum.toFixed(2)); */
	
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(amountSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}else{
		var amount = parseFloat(netAmtSum).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
		}
		$(".subTotal").html("");
		$(".subTotal").html(netAmtSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}
}