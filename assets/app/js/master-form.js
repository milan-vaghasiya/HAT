$(document).ready(function(){
    $(document).on('click',".addNewMaster",function(){		
        var functionName = $(this).data("function");
        var controllerName = $(this).data('controller');
        var appendClassName = $(this).data('class_name');
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName.split('/')[0];
		var fnSave = $(this).data("fnSave");if(fnSave == "" || fnSave == null){fnSave="save";}
        var party_id = "";
        if(functionName == "addProduct/1"){
            party_id = $("#party_id").val();
        }
		
        $.ajax({ 
            type: "GET",   
            url: base_url + controllerName + '/' + functionName,   
            data: {}
        }).done(function(response){
            $("#"+modalId).modal('show');
            //$("#"+modalId).attr("class","modal fade master-form");
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body .action-sheet-content').html("");
            $("#"+modalId+' .modal-body .action-sheet-content ').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeMaster('"+formId+"','"+controllerName+"','"+fnSave+"','"+appendClassName+"','"+modalId+"');");
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
            if(controller == "purchaseEnquiry" && functionName == "addParty/3"){
                $("#party_type").val(2);
            }
            if(functionName == "addProduct/1"){
                $("#"+formId+" #party_id").val(party_id);
                // $("#"+formId+" #party_id").comboSelect();
            }
			// $("#"+modalId+" .modal-body .single-select").comboSelect();
			$("#processDiv").hide();
			// $("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			// initMultiSelect();setPlaceHolder();
        });
    });	
});


function storeMaster(formId,controllerName,fnSave,className,modalId){
	setPlaceHolder();
	if(fnSave == "" || fnSave == null){fnSave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
    var optionText = "";
    if(className == "partyOptions"){
        optionText = capitalizeFirstLetter(jQuery('#'+formId+' input[name="party_name"]').val());
    }
    if(className == "itemOptions"){
        var itemCode = jQuery('#'+formId+' input[name="item_code"]').val();
        var itemName = capitalizeFirstLetter(jQuery('#'+formId+' input[name="item_name"]').val());
        optionText = "[ "+itemCode+" ] "+itemName;
    }
	$.ajax({
		url: base_url + controllerName + '/' + fnSave,
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
            
			$('#'+modalId).modal('hide');    
            $('#'+modalId+'-body').html("");  
            $("#DialogIconedSuccess .modal-body").html(data.message);
			$("#DialogIconedSuccess").modal('show');         
			// toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            if(className == "itemOptions"){
                $.ajax({
                    url:base_url + controllerName + '/getItemData',
                    type:'post',
                    data:{itemId:data.insert_id},
                    dataType:'json',
                    success:function(itemData){
                        $("."+className).append("<option data-row='"+JSON.stringify(itemData)+"' value='"+data.insert_id+"' selected>"+optionText+"</option>");
                        // $("."+className).comboSelect();

                        $("#item_type").val(itemData.item_type);
                        $("#item_code").val(itemData.item_code);
                        $("#item_name").val(itemData.item_name);
                        $("#item_desc").val(itemData.description);
                        $("#hsn_code").val(itemData.hsn_code);
                        $("#gst_per").val(itemData.gst_per);
                        $("#item_gst").val(itemData.gst_per);
                        $("#price").val(itemData.price);
                        $("#unit_name").val(itemData.unit_name);
                        $("#unit_id").val(itemData.unit_id);
                    }
                });
            }
            if(className == "partyOptions"){
                $.ajax({
                    url:base_url + 'parties/partyDetails',
                    type:'post',
                    data:{id:data.insert_id},
                    dataType:'json',
                    success:function(partyData){
                        
                        $("."+className).append("<option data-row='"+JSON.stringify(partyData)+"' value='"+data.insert_id+"' selected>"+optionText+"</option>");
                        // $("."+className).comboSelect();

                        var gstin = partyData.gstin;
                        var gst_type= 1;
                        var stateCode = "";
                        if(gstin != ""){ 
                            stateCode=gstin.substr(0, 2); 
                            if(stateCode == 24 || stateCode == "24"){
                                gst_type= 1;
                            }else{
                                gst_type= 2;
                            }
                        }
                        $("#gst_type").val(gst_type);
                        $("#party_name").val(partyData.party_name);
                        $("#supplier_name").val(partyData.party_name);
                        $("#party_state_code").val(stateCode);
                    }
                });
            }
		}else{
			$(".master-form").modal({show:false});
            $("#DialogIconedDanger .modal-body").html(data.message);
			$("#DialogIconedDanger").modal('show');
		} 				
	});
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}