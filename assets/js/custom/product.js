$(document).ready(function(){

	// $(document).on('keyup','#item_code',function(){
        
    //     var jjicode = addLeadingZero(parseInt($('#item_code').val()),7);
    //     jjicode = jjicode.toString();
    //     var l = jjicode.length;
    //     if(l > 7){jjicode = jjicode.substring(0, l-1);}
    //     $('#item_code').val(jjicode);
    // });
    // $('#item_code').focus(function() { $(this).select(); } );
    
    $(document).on('change',"#party_id",function(){
        var party_code = $(this).find(":selected").data('party_code');$('#party_code').val(party_code);
        var last_part = $(this).find(":selected").data('last_part');$('.last_part').html("(Last Code : "+last_part+")");
    });
	
    $(document).on('click',".setProductProcess",function(){
        var id = $(this).data('id');
        var itemName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {id:id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title + " [ Product : "+itemName+" ]");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick','saveProductProcess("'+formId+'");');    
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
            $("#item_id_p").val(id);            
        });
    });

    $(document).on('click','.viewItemProcess',function(){
        var id = $(this).data('id');
        var itemName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {id:id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title + " [ Product : "+itemName+" ]");
            $("#"+modalId+' .modal-body').html(response);
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
            $("#itemProcess tbody").sortable({
                items: 'tr',
                cursor: 'pointer',
                axis: 'y',
                dropOnEmpty: false,
                helper: fixWidthHelper,
                start: function (e, ui) {
                    ui.item.addClass("selected");
                },
                stop: function (e, ui) {
                    ui.item.removeClass("selected");
                    $(this).find("tr").each(function (index) {
                        $(this).find("td").eq(2).html(index+1);
                    });
                },
                update: function () 
                {
                    var ids='';
                    $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
                    var lastChar = ids.slice(-1);
                    if (lastChar == ',') {ids = ids.slice(0, -1);}
                    
                    $.ajax({
                        url: base_url + controller + '/updateProductProcessSequance',
                        type:'post',
                        data:{id:ids},
                        dataType:'json',
                        global:false,
                        success:function(data){}
                    });
                }
            });             
        });		
	});     	   
    
    $(document).on('click',".productKit",function(){
        var id = $(this).data('id');
        var itemName = $(this).data('product_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;
        var printbtn='';
        if($(this).hasClass('printbtn')){
            printbtn = '<a class="btn btn-outline-success btn-edit btn-print" href="'+base_url+'productOption/printMaterialBom/'+id+'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
        }

        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {id:id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title + " [ Product : "+itemName+" ]");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick','saveProductKit("'+formId+'");');    
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
                $("#"+modalId+" .modal-footer .btn-edit").hide();
            } 
            // $("#"+modalId+" .modal-footer").remove(printbtn);
            $(".btn-print").remove();
            $("#"+modalId+" .modal-footer").append(printbtn);
            $(".item_id").val(id);  
            $(".modal-lg").attr("style","max-width: 70% !important;");
			$(".single-select").comboSelect();
            kitTable();setPlaceHolder();
        });
    });
});

function addLeadingZero(value,max) {
  //return (value.length < max) ? "0" + value : value;
  str = value.toString();
  return str.length < max ? addLeadingZero("0" + str, max) : str;
}

function fixWidthHelper(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
}

function kitTable(){
	var kitTable = $('#productKit').DataTable( {
		lengthChange: false,
		"paging":false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'excel' ]
	});
	kitTable.buttons().container().appendTo( '#productKit_wrapper .col-md-6:eq(0)' );
	return kitTable;
}

function saveProductProcess(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveProductProcess',
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
			initTable(0); $('#'+formId)[0].reset();$(".modal").modal('hide'); 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(0); $('#'+formId)[0].reset();$(".modal").modal('hide'); 
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

//kit items
function AddKitRow() {
	var valid = 1;
	$(".error").html("");
	var unt = $("#kit_item_id").find(":selected").data('unit_id');
	if($("#process_id").val() == ""){$(".gerenal_error").html("Product Process not set...Please set first product process.");valid = 0;}
	if($("#kit_item_id").val() == ""){$(".kit_item_id").html("Item is required.");valid = 0;}
	if($("#kit_item_qty").val() == "" || $("#kit_item_qty").val() == 0){$(".kit_item_qty").html("Quantity is required.");valid = 0;}
	if(unt == 27){if(isFloat($("#kit_item_qty").val())){$(".kit_item_qty").html("Invalid Qty");valid = 0;}else{$("#kit_item_qty").val(parseInt($("#kit_item_qty").val()));}}
	if(valid)
	{
		var ids = $(".processItem"+$("#process_id").val()).map(function(){return $(this).val();}).get();
		var processIds = $("input[name='process_id[]']").map(function(){return $(this).val();}).get();
		if($.inArray($("#kit_item_id").val(),ids) >= 0 && $.inArray($("#process_id").val(),processIds) >= 0){
			$(".kit_item_id").html("Item already added.");
		}else{
			$(".kit_item_id").html("");
			$(".kit_item_qty").html("");
			//Get the reference of the Table's TBODY element.
			$("#productKit").dataTable().fnDestroy();
			var tblName = "productKit";
			
			var tBody = $("#"+tblName+" > TBODY")[0];
			
			//Add Row.
			row = tBody.insertRow(-1);
			
			//Add index cell
			var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
			var cell = $(row.insertCell(-1));
			cell.html(countRow);
			
			/* cell = $(row.insertCell(-1));
			cell.html($("#process_idc").val() + '<input type="hidden" name="process_id[]" value="'+$("#process_id").val()+'">'); */
			
			cell = $(row.insertCell(-1));
			cell.html($("#kit_item_idc").val() + '<input type="hidden" name="ref_item_id[]" class="processItem'+$("#process_id").val()+'" value="'+$("#kit_item_id").val()+'"><input type="hidden" name="id[]" value=""><input type="hidden" name="process_id[]" value="'+$("#process_id").val()+'">');

			cell = $(row.insertCell(-1));
			cell.html($("#kit_item_qty").val() + '<input type="hidden" name="qty[]" value="'+$("#kit_item_qty").val()+'">');
				
			//Add Button cell.
			cell = $(row.insertCell(-1));
			var btnRemove = $('<button><i class="ti-trash"></i></button>');
			btnRemove.attr("type", "button");
			btnRemove.attr("onclick", "Remove(this);");
			btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
			cell.append(btnRemove);
			cell.attr("class","text-center");
			kitTable();
			/* $("#process_idc").val("");
			$("#process_id").val(""); */
			$("#kit_item_idc").val("");
			$("#kit_item_id").val("");
			$("#kit_item_qty").val("");
		}
	}
};

function RemoveKit(button) {
	//Determine the reference of the Row using the Button.
	$("#productKit").dataTable().fnDestroy();
	var row = $(button).closest("TR");
	var table = $("#productKit")[0];
	table.deleteRow(row[0].rowIndex);
	$('#productKit tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	kitTable();
};

function saveProductKit(formId){
    var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/saveProductKit',
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
			$('#kitItems').html("");
			$('#kitItems').html(data.tbody);
			initTable(0); $('#'+formId)[0].reset();//$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(0); $('#'+formId)[0].reset();//$(".modal").modal('hide'); 
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}