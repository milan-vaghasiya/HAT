$(document).ready(function(){
    //packingBom(); 
    
    $(document).on('change','#packing_type',function(){
        var packing_type = $(this).val();
        $(".itemList").html('<option value="" data-trans_child_id="0">Selec Product</option>');
        $(".itemList").comboSelect();

        if(packing_type == "Export"){
            $("#status").val(0);
            $(".expField").show();
            var status = $("#status").val();
            if(status == 0){
                $('.finalPacking').hide();
                $('.itemDiv').removeClass("col-md-12");
                $('.itemDiv').addClass("col-md-9");
                $(".tentativeQtyDiv").show();
            }else{
                $('.finalPacking').show();
                $('.itemDiv').removeClass("col-md-9");
                $('.itemDiv').addClass("col-md-12");
                $(".tentativeQtyDiv").hide();
            }

            $.ajax({
                url : base_url + controller + '/getSalesOrderNoListForPacking',
                type : 'get',
                dataType:'json',
                success:function(data){
                    $("#trans_main_id").html("");
                    $("#trans_main_id").html(data.orderNoList);
                    $("#trans_main_id").comboSelect();
                }
            });
        }else{
            $("#status").val(1);
            $(".expField").hide();
            $("#trans_main_id").html("");
            $("#trans_main_id").comboSelect();

            $.ajax({
                url : base_url + controller + '/getItemList',
                type : 'post',
                data:{order_id:0},
                dataType:'json',
                success:function(data){
                    $(".itemList").html("");
                    $(".itemList").html(data.itemList);
                    $(".itemList").comboSelect();
                }
            });

            $('.finalPacking').show();
            $('.itemDiv').removeClass("col-md-9");
            $('.itemDiv').addClass("col-md-12");
            $(".tentativeQtyDiv").hide();
            $(".tentative_qty").attr("readonly","readonly");
        }
        
    });

    $(document).on('change','#trans_main_id',function(){
        var order_id = $(this).val();

        $.ajax({
            url : base_url + controller + '/getItemList',
            type : 'post',
            data:{order_id:order_id},
            dataType:'json',
            success:function(data){
                $(".itemList").html("");
                $(".itemList").html(data.itemList);
                $(".itemList").comboSelect();
            }
        });
    });

    $(document).on('change','#status',function(){
        var status = $(this).val();
        if(status == 0){
            $('.finalPacking').hide();
            $('.itemDiv').removeClass("col-md-12");
            $('.itemDiv').addClass("col-md-9");
            $(".tentativeQtyDiv").show();
            $(".tentative_qty").removeAttr("readonly");
        }else{
            $('.finalPacking').show();
            $('.itemDiv').removeClass("col-md-9");
            $('.itemDiv').addClass("col-md-12");
            $(".tentativeQtyDiv").hide();
            $(".tentative_qty").attr("readonly","readonly");

            var id = $("#item_id :selected").val();
            $.ajax({
                url: base_url + 'packing/batchWiseItemStock',
                data: {item_id:id,trans_id:"",batch_no:"",location_id:"",batch_qty:""},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#batchData").html(data.batchData);
                }
            });
        }
    });

    $(document).on('keyup change',".batchQty",function(){	
        
		var oldpqty = $("#oldpqty").val();	
		var batchQtyArr = $("input[name='batch_qty[]']").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
        batchQtySum += parseFloat(oldpqty);
		$('#totalQty').html("");
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#packing_qty").val(batchQtySum.toFixed(3));

		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".packing_qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");

            var sum = parseFloat(batchQtySum) - parseFloat(batchQty) + parseFloat(oldpqty);
			$('#totalQty').html(sum);
		    $("#qty").val(sum);
			$(".bQty"+id).val(0);
		}
	});

    $(document).on('change',"#item_id",function(){	
        var id = $(this).val();

        var trans_child_id = $(".itemList :selected").data('trans_child_id');
        console.log(trans_child_id);
        $("#trans_child_id").val(trans_child_id);

        var status = $("#status :selected").val();
        if(status == "1"){
            if(id){
                $.ajax({
                    url: base_url + 'packing/batchWiseItemStock',
                    data: {item_id:id,trans_id:"",batch_no:"",location_id:"",batch_qty:""},
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#batchData").html(data.batchData);
                    }
                });
            }
        }        
    });
});


function AddRow() {
    var valid = 1;
	$(".error").html(""); 
    var status = $("#status :selected").val();
    if($("#box_id").val() == ""){$(".box_id").html("Packing Material is required.");valid = 0;}
	if($("#noof_box").val() == "" || $("#noof_box").val() == 0){$(".noof_box").html("No. Of Box is required.");valid = 0;}
	if($("#capacity").val() == "" || $("#capacity").val() == 0){$(".capacity").html("Capacity is required.");valid = 0;}
	if(status ==1){
        if($("#packing_qty").val() == "" || $("#packing_qty").val() == 0){$(".packing_qty").html("Total Qty is required.");valid = 0;}
    }else{
        if($("#tantetive_qty").val() == "" || $("#tantetive_qty").val() == 0){$(".tantetive_qty").html("Packing Qty is required.");valid = 0;}
    }
    
	
    var noofbox = $("#noof_box").val();
    var capacity = $("#capacity").val();
    var pQty = (status ==1)?$("#packing_qty").val():$("#tantetive_qty").val();
    var tQty = parseFloat(noofbox) * parseFloat(capacity);
    //if(parseFloat(pQty) != parseFloat(tQty)){ $(".capacity").html("Qty is mismatch.");valid = 0; }

	if(valid)
	{
        $(".box_id").html("");
        $(".capacity").html("");
        //Get the reference of the Table's TBODY element.
        // $("#packingBom").dataTable().fnDestroy();
        // var tblName = "packingBom";
        
        // var tBody = $("#"+tblName+" > TBODY")[0];
        
        // //Add Row.
        // row = tBody.insertRow(-1);
        
        // //Add index cell
        // var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        // var cell = $(row.insertCell(-1));
        // cell.html(countRow);

        $('table#packingBom tr#noData').remove();
        //Get the reference of the Table's TBODY element.
        var tblName = "packingBom";
        
        var tBody = $("#"+tblName+" > TBODY")[0];
        
        //Add Row.
        row = tBody.insertRow(-1);
        
        //Add index cell
        var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        cell.attr("style","width:5%;");	
        
        cell = $(row.insertCell(-1));
        cell.html($("#box_idc").val() + '<input type="hidden" name="box_id[]" value="'+$("#box_id").val()+'">');

        cell = $(row.insertCell(-1));
        cell.html($("#noof_box").val() + '<input type="hidden" name="noof_box[]" value="'+$("#noof_box").val()+'">');

        var capacityErrorDiv = $("<div></div>",{class:"error capacity"+countRow});
        cell = $(row.insertCell(-1));
        cell.html($("#capacity").val() + '<input type="hidden" name="capacity[]" value="'+$("#capacity").val()+'">');
	    cell.append(capacityErrorDiv);

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger");
        cell.append(btnRemove);
        cell.attr("class","text-center");
        //packingBom();
        $("#box_id").val("");
        $("#box_idc").val("");
        $("#noof_box").val("");
        $("#capacity").val("");
	}
};

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingBom")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingBom tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};

function packingBom(){
    var table = $('#packingBom').DataTable( {
		lengthChange: false,
		responsive: true,
		ordering: true,
		//'stateSave':true,
        'pageLength': 25,
		buttons: ['pageLength', 'copy', 'excel' ]
	});
	table.buttons().container().appendTo( '#packingBom_wrapper .col-md-6:eq(0)' );
}