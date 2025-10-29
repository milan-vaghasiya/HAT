$(document).ready(function(){
    $(document).on('change','#ref_id',function(){
        var ref_id = $(this).val();
        $('.ref_id').html("");
        if(ref_id){
            $.ajax({
                url: base_url + controller + '/getCustomPackingData',
                type:'post',
                data:{id:ref_id},
                dataType:'json',
                success:function(data){                    
                    $.each(data.masterData,function(key , value){
                        if(key != "export_type" /* || key != "applicable_prefrential_agreement" */)
                            $("#customInvoiceForm #"+key).val(value);

                        if(key == "export_type"){
							if(value == "(Supply Meant For Export With Payment Of IGST)"){
								$("#customInvoiceForm #export_type_name").val("IGST");
							}else{
								$("#customInvoiceForm #export_type_name").val("LUT");
							}
							$("#customInvoiceForm #export_type").val(value);
                            /* $("#customInvoiceForm #export_type").val(value);
                            $("#customInvoiceForm #export_type option").filter(function() {
                                return $(this).data().text === value
                            }).prop("disabled", false);  */                         
                        }

                        /* if(key == "applicable_prefrential_agreement"){
                            $("#customInvoiceForm #applicable_prefrential_agreement").val(value);
                            $("#customInvoiceForm #applicable_prefrential_agreement option").filter(function() {
                                return $(this).data().text === value
                            }).prop("disabled", false);
                        } */
                    });

                    if(data.itemData.length > 0){
                        $.each(data.itemData,function(key , row){
                            row.row_index = "";
                            AddRow(row);
                        });
                    }
                }
            });
        }else{
            $('.ref_id').html("Com. Pac. No is required.");
        }
    });

    $(document).on('click','.saveItem',function(){
        
        var fd = $('#customInvoiceItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
        $(".price").html("");
        if(formData.price == "" || formData.price == "0"){
            $(".price").html("Price is required.");
		}else{
            
			formData.amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
			formData.taxable_amount = formData.amount;
			formData.net_amount = formData.amount;
            AddRow(formData);
            $('#customInvoiceItemForm')[0].reset();
            if($(this).data('fn') == "save"){
                $("#row_index").val($('#customInvoiceItems tbody').find('tr').length);
            }else if($(this).data('fn') == "save_close"){
                $("#itemModel").modal('hide');
            }  
		}
    });

    $(document).on('keyup','#freight_amount',function(){ claculateColumn(); });
    $(document).on('keyup','#other_amount',function(){ claculateColumn(); });
});

function AddRow(data) {
	$('table#customInvoiceItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "customInvoiceItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	
	var ind = (data.row_index == "")?-1:data.row_index;
	row = tBody.insertRow(ind);
	
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
    var transIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][id]",value:data.id});
	var refIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][ref_id]",value:data.ref_id});
	var fromEntryTypeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][from_entry_type]",value:data.from_entry_type});
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_id]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_name]",value:data.item_name});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_desc]",value:data.item_desc});
	var itemAliasInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_alias]",value:data.item_alias});	
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_type]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_code]",value:data.item_code});
	cell = $(row.insertCell(-1));
	cell.html(data.item_alias);
    cell.append(transIdInput);
	cell.append(refIdInput);
	cell.append(fromEntryTypeInput);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
    cell.append(itemDescInput);
	cell.append(itemAliasInput);	
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	

	var hsnCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][hsn_code]",value:data.hsn_code});
    cell = $(row.insertCell(-1));
	cell.html(data.hsn_code );
	cell.append(hsnCodeInput);

    var hsnDescInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][hsn_desc]",value:data.hsn_desc});
    cell = $(row.insertCell(-1));
	cell.html(data.hsn_desc );
	cell.append(hsnDescInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][qty]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	
	var priceInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][price]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);	
	
    var amountInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][amount]",class:"amt",value:data.amount});
	var taxableAmountInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][taxable_amount]",value:data.taxable_amount});
	var netAmtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][net_amount]",class:"net_amt",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
    cell.append(amountInput);
	cell.append(taxableAmountInput);
	cell.append(netAmtInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light");

    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");

	claculateColumn();
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    console.log(data);
	$.each(data,function(key, value) { $("#customInvoiceItemForm #"+key).val(value); }); 		   
	$("#customInvoiceItemForm #row_index").val(row_index);
    $(".btn-save").hide();	
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#customInvoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#customInvoiceItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#customInvoiceItems tbody tr:last').index() + 1;
	if(countTR == 0){		
		$("#tempItem").html('<tr id="noData"><td colspan="8" align="center">No data available in table</td></tr>');
	}	
	claculateColumn();
};

function claculateColumn(){			
	var amountArray = $(".amt").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});

    var netAmtArray = $(".net_amt").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
    
    var freight_amount = $("#freight_amount").val();
    netAmtSum += (parseFloat(freight_amount) > 0)?parseFloat(freight_amount):0;

    var other_amount = $("#other_amount").val();
    netAmtSum += (parseFloat(other_amount) > 0)?parseFloat(other_amount):0;

    $("#total_amount").val(parseFloat(amountSum).toFixed(3));
    $("#net_amount").val(parseFloat(netAmtSum).toFixed(3));
}

function saveInvoice(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
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
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}