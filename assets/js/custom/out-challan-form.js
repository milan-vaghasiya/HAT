$(document).ready(function(){

    var ref_id = $("#r_id").val();
    var party_id = $("#party_id").val();
    $(".party_id").html("");
    if(party_id != ""){
		$.ajax({
			url: base_url + controller + '/getDepartmentOrJobcard',
			type:'post',
			data:{ref_id:ref_id,party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#ref_id").html("");
				$("#ref_id").html(data.options);
				$("#ref_id").comboSelect();
			}
		});
    }
	// $(document).on("change","#party_id",function(){
    //     var party_id = $(this).val();
    //     $(".party_id").html("");
	// 	if(party_id == ""){
	// 		$(".party_id").html("Party Name is required.");
	// 	}else{            
	// 		$.ajax({
	// 			url: base_url + controller + '/getDepartmentOrJobcard',
	// 			type:'post',
	// 			data:{party_id:party_id,ref_id:""},
	// 			dataType:'json',
	// 			success:function(data){
	// 				$("#ref_id").html("");
	// 				$("#ref_id").html(data.options);
	// 				$("#ref_id").comboSelect();
	// 			}
	// 		});
	// 	}
	// });
	$(document).on('change keyup','#party_id',function(){
		$("#party_name").val($('#party_idc').val()); 
	});
    
	$(document).on('change','#item_id',function(){
		if($(this).val() == ""){
			$("#item_name").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
		}else{
			var itemData = $('#item_id :selected').data('row');
			$("#item_name").val(itemData.item_name);			
			$("#unit_name").val(itemData.unit_name);
			$("#unit_id").val(itemData.unit_id);
		}
		$("#batch_no").html('<option value="">Select Batch No.</option>');
		$("#batch_no").comboSelect();
		//$("#location_id").val("");
		//$("#location_id").select2();      		
	});
	$(document).on('keyup',"#item_idc",function(){
		$("#item_name").val($("#item_idc").val());
	});

	// $(document).on("change","#location_id",function(){
	// 	var itemId = $("#item_id").val();
    //     var location_id = $(this).val();
	// 	$(".location_id").html("");
	// 	$(".item_id").html("");
	// 	$("#batch_stock").val("");
		
	// 	if(itemId == "" || location_id == ""){
	// 		if(itemId == ""){
	// 			$(".item_id").html("Issue Item name is required.");
	// 		}
	// 		if(location_id == ""){
	// 			$(".location_id").html("Location is required.");
	// 		}
	// 	}else{
	// 		$.ajax({
	// 			url:base_url + controller + '/getBatchNo',
	// 			type:'post',
	// 			data:{item_id:itemId,location_id:location_id},
	// 			dataType:'json',
	// 			success:function(data){
	// 				$("#batch_no").html("");
	// 				$("#batch_no").html(data.options);
	// 				$("#batch_no").comboSelect();
	// 			}
	// 		});
	// 	}
	// });

    $(document).on('click','.saveItem',function(){
        var fd = $('#challanItemForm').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });

        $(".error").html("");
        if(formData.item_name == ""){
			$(".item_name").html("Item Name is required.");
		}else{
			/* var item_names = $("input[name='item_name[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_name,item_names) >= 0) {
				$(".item_name").html("Item already added.");
			}else { */
				if(formData.qty == "" || formData.qty == "0" ||  formData.location_id == "" ){
					if(formData.qty == "" || formData.qty == "0"){
						$(".qty").html("Qty is required.");
					}
					// if(formData.unit_id == ""){
					// 	$(".unit_id").html("Unit name is required.");	
					// }
                    if(formData.location_id == ""){
						$(".location_id").html("Location name is required.");	
					}
					// if(formData.batch_no == ""){
					// 	$(".batch_no").html("Batch No. is required.");	
					// }
				}else{
                    
					formData.qty = parseFloat(formData.qty).toFixed(2);
					
					AddRow(formData);
                    $('#challanItemForm')[0].reset();
                    if($(this).data('fn') == "save"){
                        $("#item_idc").focus();
                        $("#item_id").comboSelect(); 
						// $("#batch_no").html('<option value="">Select Batch No.</option>');
        				// $("#batch_no").comboSelect();
                       // $("#location_id").select2();                       
                    }else if($(this).data('fn') == "save_close"){
                        $("#itemModel").modal('hide');
						$("#item_id").comboSelect();
						// $("#batch_no").html('<option value="">Select Batch No.</option>');
        				// $("#batch_no").comboSelect();
                        //$("#location_id").select2();
                    }   
				}
			// }
		}
    });  
    
	$(document).on('click','.add-item',function(){
		$(".btn-close").show();
    	$(".btn-save").show();
	});
    
    $(document).on('click','.btn-efclose',function(){
        $('#challanItemForm')[0].reset();
		$("#item_id").comboSelect();
		$("#batch_no").html('<option value="">Select Batch No.</option>');
        $("#batch_no").comboSelect();
        //$("#location_id").select2();
		$('#challanItemForm .error').html('');	
    });		
});

function AddRow(data) {
	$('table#outChallanItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "outChallanItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var returnableInput = $("<input/>",{type:"hidden",name:"is_returnable[]",value:data.is_returnable});	
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});	
	// var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var locationErrorDiv = $("<div></div>",{class:"error location_id"+countRow});
	// var batchNoErrorDiv = $("<div></div>",{class:"error batch_no"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(returnableInput);
	cell.append(locationIdInput);
	// cell.append(batchNoInput);
	cell.append(locationErrorDiv);
	// cell.append(batchNoErrorDiv);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyErrorDiv = $("<div></div>",{class:"error qty"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);
	
	// var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	// var unitNameInput = $("<input/>",{type:"hidden",name:"unit_name[]",value:data.unit_name});
	// cell = $(row.insertCell(-1));
	// cell.html(data.unit_name);
	// cell.append(unitIdInput);
	// cell.append(unitNameInput);

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	
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
    $("#itemModel").modal();
    $(".btn-close").hide();
    $(".btn-save").hide();
	var batchNo = "";
    $.each(data,function(key, value) {
		if(key=="batch_no"){ batchNo = value; }else{$("#"+key).val(value);}
	}); 	
	$("#item_id").comboSelect();
    //$("#location_id").select2();

	var itemId = $("#item_id").val();
	var location_id = $("#location_id").val();
	$.ajax({
		url:base_url + controller + '/getBatchNo',
		type:'post',
		data:{item_id:itemId,location_id:location_id},
		dataType:'json',
		success:function(data){
			// $("#batch_no").html("");
			// $("#batch_no").html(data.options);	
			// $("#batch_no").val(batchNo);
			// $("#batch_no").comboSelect();				
		}
	});	

    Remove(button);
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#outChallanItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#outChallanItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#outChallanItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="7" align="center">No data available in table</td></tr>');
	}	
};

function saveOutChallan(formId){
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