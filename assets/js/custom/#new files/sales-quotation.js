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
            url: base_url + 'salesQuotation/' + functionName,   
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
            $("#drawing_no"+id).attr('disabled','disabled');
            $("#rev_no"+id).attr('disabled','disabled');

        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#item_name"+id).removeAttr('disabled');
            $("#qty"+id).removeAttr('disabled');
            $("#price"+id).removeAttr('disabled');
            $("#currency"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
            $("#drawing_no"+id).removeAttr('disabled');
            $("#rev_no"+id).removeAttr('disabled');
        }
    });

	$(document).on('click','.confirmQuotation',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var quote_id = $(this).data('id');
		var customerId = $(this).data('customer_id');
		var partyName = $(this).data('party');
		var quote_no = $(this).data('quote_no');
		var quotation_date = $(this).data('quotation_date');
		
        $.ajax({ 
            type: "POST",   
            url: base_url + 'salesQuotation/' + functionName,   
            data: {quote_id:quote_id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-dialog").css('max-width','40%');
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveConfirmQuotation('"+formId+"');");
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

            $("#customer_id").val(customerId);
            $("#party_name").html(partyName);
            $("#quote_no").html(quote_no);
            $("#quotation_date").html(quotation_date);
            $("#id").val(quote_id);$("#quote_id").val(quote_id);
            $(".modal-lg").attr("style","max-width: 70% !important;");
            $('.floatOnly').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });
        });
    });
	
	$(document).on('click','.addFolloUp',function(){
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        var id = $(this).data('id');
		
        $.ajax({ 
            type: "POST",   
            url: base_url + 'salesQuotation/' + functionName,   
            data: {id:id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-dialog").css('max-width','40%');
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"saveFollowUp('"+formId+"');");
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
            $(".modal-lg").attr("style","max-width: 70% !important;");
            setTimeout(function(){ setPlaceHolder(); }, 5);
        });
    });

    $(document).on("click",".itemCheckCQ",function(){
        var id = $(this).data('rowid');
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#qty"+id).attr('disabled','disabled');
            $("#unit_id"+id).attr('disabled','disabled');
            $("#price"+id).attr('disabled','disabled');
            $("#item_id"+id).attr('disabled','disabled');
            $("#automotive"+id).attr('disabled','disabled');
            $("#item_name"+id).attr('disabled','disabled');
            $("#inq_trans_id"+id).attr('disabled','disabled');
            $("#confirm_price"+id).attr('disabled','disabled');
            $("#trans_id"+id).attr('disabled','disabled');
            $("#drg_rev_no"+id).attr('disabled','disabled');
            $("#rev_no"+id).attr('disabled','disabled');

        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#qty"+id).removeAttr('disabled');
            $("#price"+id).removeAttr('disabled');
            $("#unit_id"+id).removeAttr('disabled');
            $("#automotive"+id).removeAttr('disabled');
            $("#item_id"+id).removeAttr('disabled');
            $("#item_name"+id).removeAttr('disabled');
            $("#inq_trans_id"+id).removeAttr('disabled');
            $("#confirm_price"+id).removeAttr('disabled');
            $("#trans_id"+id).removeAttr('disabled');
            $("#drg_rev_no"+id).removeAttr('disabled');
            $("#rev_no"+id).removeAttr('disabled');
        }
    });
    
    $(document).on('click',".approveQuotation",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Sales Quotation?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approveQuotation',
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
	
	$(document).on("click",".getFeasibleData",function(){
		var trans_main_id = $(this).data('trans_main_id'); 
		var status = $(this).data('status'); 
		var enq_no = $(this).data('enq_no'); 
		if(trans_main_id){
            $.ajax({
                url:base_url + controller + "/getFeasibleData",
                type:'post',
                data:{trans_main_id:trans_main_id,status:status,enq_no:enq_no},
                dataType:'json',
                success:function(data){
                    $("#lastActivityModal").modal();
					$("#enqNo").html(enq_no);
					$("#activityData").html(data.tbody);
                }
            });
        }
	});

    $(document).on("click",".getRegretedData",function(){
		var trans_main_id = $(this).data('trans_main_id'); 
		var id = $(this).data('id'); 
		if(trans_main_id){
            $.confirm({
                title: 'Confirm!',
                content: 'Are you sure want to reopen this enquiry?',
                type: 'green',
                buttons: {   
                    ok: {
                        text: "ok!",
                        btnClass: 'btn waves-effect waves-light btn-outline-success',
                        keys: ['enter'],
                        action: function(){
                            $.ajax({
                                url:base_url + controller + "/getRegretedData",
                                type:'post',
                                data:{trans_main_id:trans_main_id,id:id},
                                dataType:'json',
                                success:function(data){
                                    if(data.status===0){
                                        $(".error").html("");
                                        $.each( data.message, function( key, value ) {$("."+key).html(value);});
                                    }else if(data.status==1){
                                        initTable(1); $("#lastActivityModal").modal('hide');
                                        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                    }else{
                                        initTable(1); $("#lastActivityModal").modal('hide');
                                        toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
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
	});
	
	$(document).on('click','.createSalesQuotation',function(){		
        var id = $(this).data('id');
        var sq_no = $(this).data('sq_no');
        $.ajax({
            url : base_url + controller + '/viewRevisionQuotation',
            type: 'post',
            data:{id:id},
            dataType:'json',
            success:function(data){
                $("#orderModal").modal();
                $("#exampleModalLabel1").html('Quotation Revision');
                $("#sq_no").html(sq_no);
                $("#orderData").html(data);
            }
        });
    });
});

function saveQuotation(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'salesQuotation/saveQuotation',
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

function saveConfirmQuotation(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'salesQuotation/saveConfirmQuotation',
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

function addFRow(){
    $(".error").html(""); var valid=1;
    if($("#trans_date").val() == ""){
        $(".trans_date").html("Date is required."); valid=0;
    }
    if($("#sales_executive").val() == ""){
        $(".sales_executive").html("Sales Executive is required."); valid=0;
    }
    if($("#f_note").val() == "" || $("#f_note").val() == 0){
        $(".f_note").html("Note is required."); valid=0;
    }
    if(valid){
        //Get the reference of the Table's TBODY element.
        $("#followup").dataTable().fnDestroy();
        var tblName = "followup";
        
        var tBody = $("#"+tblName+" > TBODY")[0];
        
        //Add Row.
        row = tBody.insertRow(-1);
        
        //Add index cell
        var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        
        var dt = moment($("#trans_date").val());
        cell = $(row.insertCell(-1));
        cell.html(dt.format('DD-MM-YYYY') + '<input type="hidden" name="trans_date[]" value="'+$("#trans_date").val()+'">');
        
        cell = $(row.insertCell(-1));
        cell.html($("#sales_executivec").val() + '<input type="hidden" name="sales_executive[]" value="'+$("#sales_executive").val()+'"><input type="hidden" name="sales_executiveName[]" value="'+$("#sales_executivec").val()+'">');

        cell = $(row.insertCell(-1));
        cell.html($("#f_note").val() + '<input type="hidden" name="f_note[]" value="'+$("#f_note").val()+'">');

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);
        cell.attr("class","text-center");

        $("#sales_executive").val(""); $("#sales_executive").comboSelect(); $("#f_note").val("");
	}
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	$("#followup").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#followup")[0];
	table.deleteRow(row[0].rowIndex);
	$('#followup tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};

function saveFollowUp(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'salesQuotation/saveFollowUp',
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