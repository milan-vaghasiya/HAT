$(document).ready(function(){
    $(document).on('change','#ref_id',function(){
        var ref_id = $(this).val();
        $('.ref_id').html("");
        if(ref_id){
            $.ajax({
                url: base_url + controller + '/getCommercialPackingData',
                type:'post',
                data:{id:ref_id},
                dataType:'json',
                success:function(data){                    
                    $.each(data.masterData,function(key , value){
                        $("#customPackingForm #"+key).val(value);
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

	$(document).on('change','#export_type',function(){
		var exp_type = $(this).val();
		if(exp_type != ""){
			if(exp_type == "(Supply Meant For Export With Payment Of IGST)"){
				$("#gst_type").val(2);
				$("#gst_applicable").val(1);
			}else{
				$("#gst_type").val(3);
				$("#gst_applicable").val(0);
			}
		}else{
			$("#gst_type").val(2);
			$("#gst_applicable").val(1);
		}
	});

    $(document).on('click','.saveItem',function(){
        
        var fd = $('#customPackingItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) { formData[v.name] = v.value; });
		$(".item_alias").html("");
        if(formData.item_alias == ""){
            $(".item_alias").html("Product Name is required.");
		}else{
            AddRow(formData);
            $('#customPackingItemForm')[0].reset();
            if($(this).data('fn') == "save"){
                $("#row_index").val($('#customPackingItems tbody').find('tr').length);
            }else if($(this).data('fn') == "save_close"){
                $("#itemModel").modal('hide');
            }  
		}
    });
});

function AddRow(data) {
	$('table#customPackingItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "customPackingItems";
	
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
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_id]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_name]",value:data.item_name});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_desc]",value:data.item_desc});
	var itemAliasInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_alias]",value:data.item_alias});
	var transIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][id]",value:data.id});
	var refIdInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][ref_id]",value:data.ref_id});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_type]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][item_code]",value:data.item_code});
	cell = $(row.insertCell(-1));
	cell.html(data.item_alias);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
    cell.append(itemDescInput);
	cell.append(itemAliasInput);
	cell.append(transIdInput);
	cell.append(refIdInput);
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
	cell = $(row.insertCell(-1));
	cell.html(data.amount);
	cell.append(amountInput);
	cell.append(taxableAmountInput);
	
	var netAmtInput = $("<input/>",{type:"hidden",name:"item_data["+countRow+"][net_amount]",class:"net_amt",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
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
	$.each(data,function(key, value) { $("#customPackingItemForm #"+key).val(value); }); 		   
	$("#customPackingItemForm #row_index").val(row_index);
    $(".btn-save").hide();	
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#customPackingItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#customPackingItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#customPackingItems tbody tr:last').index() + 1;
	if(countTR == 0){		
		$("#tempItem").html('<tr id="noData"><td colspan="9" align="center">No data available in table</td></tr>');
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
    
    $("#total_net_weight").val(parseFloat(amountSum).toFixed(3));
    $(".total_net_weight").html(parseFloat(amountSum).toFixed(3));

    $("#total_gross_weight").val(parseFloat(netAmtSum).toFixed(3));
    $(".total_gross_weight").html(parseFloat(netAmtSum).toFixed(3));
}

function savePacking(formId){
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