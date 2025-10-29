$(document).ready(function(){
    // setPlaceHolder();
    $("#item_type_name").val($('#item_type :selected').text());
    
    $(document).on('change keyup','#supplier_id',function(){$("#supplier_name").val($('#supplier_idc').val()); });
    $(document).on('change keyup','#supplier_idc',function(){$("#supplier_name").val($('#supplier_idc').val());});

    $(document).on('change keyup','#unit_id',function(){ $("#unit_name").val($('#unit_idc').val());});

    $(document).on('change keyup','#item_type',function(){$("#item_type_name").val($('#item_type :selected').text());});
    $(document).on('keyup','#item_typec',function(){ $("#item_type_name").val($(this).val());});
    $(document).on('change keyup','#fgitem_id',function(){$("#fgitem_name").val($('#fgitem_id :selected').text());});
    $(document).on('keyup','#fgitem_idc',function(){ $("#fgitem_name").val($(this).val());});

    $(document).on('click','.saveItem',function(){
        var fd = $('#enquiryItemForm').serializeArray();
        var formObject = {};
        $.each(fd,function(i, v) {
            formObject[v.name] = v.value;
        });
        $(".item_name").html("");
		$(".qty").html("");
        $(".unit_id").html("");
        if(formObject.item_name == ""){
			$(".item_name").html("Item Name is required..");
		}else{
			var item_names = $("input[name='item_name[]']").map(function(){return $(this).val();}).get();
// 			if ($.inArray(formObject.item_name,item_names) >= 0) {
// 				$(".item_name").html("Item already added.");
// 			}else {
				if(formObject.qty == "" || formObject.qty == "0" || isNaN(formObject.qty)){
					$(".qty").html("Qty is required.");
				}else if(formObject.unit_id == "" || formObject.unit_id == "0" || isNaN(formObject.unit_id)){
					$(".unit_id").html("Unit is required.");
				}else{								
					AddRow(formObject);					
					$('#enquiryItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_name").focus();
                        $("#unit_id").comboSelect();
                        $("#fgitem_id").comboSelect();
						$("#row_index").val($('#purchaseEnqItems tbody').find('tr').length);
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
                        $("#unit_id").comboSelect();
                        $("#fgitem_id").comboSelect();
                    }                    
				}
// 			}
		}
    });  

    $(document).on('click','.add-item',function(){
		$(".btn-close").show();
    	$(".btn-save").show();
		$('#enquiryItemForm')[0].reset();
		$("#row_index").val($('#purchaseEnqItems tbody').find('tr').length);
	});    
    
    $(document).on('click','.btn-close',function(){
        $('#enquiryItemForm')[0].reset();
        $("#unit_id").comboSelect();
        $('#enquiryItemForm .error').html("");
    });

	$('#item_name').typeahead({
		source: function(query, result)
		{
			$.ajax({
				url:base_url + controller + '/itemSearch',
				method:"POST",
				global:false,
				data:{query:query},
				dataType:"json",
				success:function(data){result($.map(data, function(item){return item;}));}
			});
		}
	 });
	 
});

function AddRow(data) {
    //$('table#purchaseEnqItems tr#noData').remove();
    //Get the reference of the Table's TBODY element.
    var tblName = "purchaseEnqItems";
    
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
    
    var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
    var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
    var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
    cell = $(row.insertCell(-1));
    cell.html(data.item_name);
    cell.append(itemNameInput);
    cell.append(transIdInput);
    cell.append(itemRemarkInput);
    
    var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
    cell = $(row.insertCell(-1));
    cell.html(data.item_type_name);
    cell.append(itemTypeInput);
    
    var fgItemIdInput = $("<input/>",{type:"hidden",name:"fgitem_id[]",value:data.fgitem_id});
    var fgItemNameInput = $("<input/>",{type:"hidden",name:"fgitem_name[]",value:data.fgitem_name});
    cell = $(row.insertCell(-1));
    cell.html(data.fgitem_name);
    cell.append(fgItemIdInput);
    cell.append(fgItemNameInput);
    
    var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
    cell = $(row.insertCell(-1));
    cell.html(data.qty);
    cell.append(qtyInput);
    
    var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
    cell = $(row.insertCell(-1));
    cell.html(data.unit_name);        
    cell.append(unitIdInput);
    
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
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
	$("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
    var fnm = "";
    $.each(data,function(key, value) {
        $("#"+key).val(value);
        if(key == "fgitem_id"){
            fnm = $('#fgitem_id :selected').text();
        }
    }); 
    $("#fgitem_name").val(fnm);
    $("#unit_id").comboSelect();
    $("#fgitem_id").comboSelect();
	$("#row_index").val(row_index);	
    //Remove(button);
}

function Remove(button) {
    //Determine the reference of the Row using the Button.
    var row = $(button).closest("TR");
    var table = $("#purchaseEnqItems")[0];
    table.deleteRow(row[0].rowIndex);
    $('#purchaseEnqItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
    var countTR = $('#purchaseEnqItems tbody tr:last').index() + 1;
    if(countTR == 0){
        $("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
    }
};

function saveEnquiry(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
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