$(document).ready(function(){

	$(document).on('click',".itemOpeningStock",function(){
        var id = $(this).data('id');
        var itemName = $(this).data('item_name');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;

        $.ajax({ 
            type: "POST",   
            url: base_url + 'items/' + functionName,   
            data: {id:id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"');");
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
            $("#item_id").val('');
            $("#item_id").val(id);
            $("#itemName").html("");
            $("#itemName").html("Item Name : "+itemName);
            openingStockTable();
            $(".modal-lg").attr("style","max-width: 70% !important;");
			$(".single-select").comboSelect();setPlaceHolder();
			$(".select2").select2();
			$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
        });
    });	

    $(document).on('click',".itemStockUpdate",function(){
        var id = $(this).data('id');
        var itemName = $(this).data('item_name');
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
			$("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store('"+formId+"');");
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
            $("#item_id").val('');
            $("#item_id").val(id);
            $("#itemName").html("");
            $("#itemName").html("[ Item Name : "+itemName+" ]");
            stockTable();
            $(".modal-lg").attr("style","max-width: 70% !important;");
			$(".single-select").comboSelect();setPlaceHolder();
        });
    });	

    $(document).on("click",".saveStock",function(){
		var item_id = $("#item_id").val();
		var trans_date = $("#trans_date").val();
		var type = $("#type").val();
		var qty = $("#qty").val();
		$(".error").html("");
		
		if(qty == 0 || qty == ""){$(".qty").html("Quantity is required.");$("#qty").focus();}
		if(trans_date == null || trans_date == ""){$(".trans_date").html("Date is required.");$("#trans_date").focus();}
		else{
			$.ajax({
			url:base_url + controller +'/saveStockTrans',
			type:'post',
			data:{item_id:item_id,trans_date:trans_date,qty:qty,type:type},
			dataType:'json',
			success:function(data)
			{
				if(data.status===0){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else if(data.status==1){
					$("#commanTable1").dataTable().fnDestroy();
					$("#stockData").html("");
					if(data.length  != 0 ){$("#stockData").html(data.stockData);	}
					initTable();stockTable();
					$("#qty").val("");$("#qty").focus();
				}else{
					toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
				
			}
		});
		}
	});
});
function openingStockTable(){
	var openingStockTable = $('#openingStockTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'copy', 'excel', 'pdf', 'print' ]
	});
	openingStockTable.buttons().container().appendTo( '#openingStockTable_wrapper .col-md-6:eq(0)' );
	return openingStockTable;
}

function saveOpening(frm){
	setPlaceHolder();
	var fnSave="saveOpeningStock";
	var fd = new FormData(frm);
	$.ajax({
		url: base_url + 'items/' + fnSave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			initTable();
			$("#openingStockTable").dataTable().fnDestroy();
			$("#openingStockData").html("");
			$("#openingStockData").html(data.transData);
			openingStockTable();
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable();
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function deleteOpeningStock(id){
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Opening Stock?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url:base_url + 'items/deleteOpeningStockTrans',
						type:'post',
						data:{id:id},
						dataType:'json',
						success:function(data)
						{
                            if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								$("#openingStockTable").dataTable().fnDestroy();
								$("#openingStockData").html("");
								$("#openingStockData").html(data.transData);
								openingStockTable();
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


function stockTable(){
	var stockTable = $('#stockTable').DataTable( {
		lengthChange: false,
		responsive: true,
		'stateSave':true,
		retrieve: true,
		buttons: [ 'copy', 'excel', 'pdf', 'print' ]
	});
	stockTable.buttons().container().appendTo( '#stockTable_wrapper .col-md-6:eq(0)' );
	return stockTable;
}

function deleteStock(id){
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this Stock?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url:base_url + controller +'/deleteStockTrans',
						type:'post',
						data:{id:id},
						dataType:'json',
						success:function(data)
						{
							$("#commanTable1").dataTable().fnDestroy();
							$("#stockData").html("");	
							if(data.length  != 0 ){
								$("#stockData").html(data);	
							}
                            initTable();
							stockTable();
							$("#qty").focus();
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