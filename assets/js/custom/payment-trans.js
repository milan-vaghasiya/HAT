$(document).ready(function(){
    $(document).on('change',"#party_id",function(){
		var party_id = $(this).val();
		if(party_id){
			$.ajax({
				url: base_url + controller + '/getPartyRefNo',
				type:'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#invoiceSelect").html("");
					$("#invoiceSelect").html(data.options);
	                reInitMultiSelect();
				}
			});
		}
	});
    
    $(document).on('change',"#tran_mode",function(){
		var tran_mode = $(this).val();
        if(tran_mode == "Cheque"){
            $("#chDate").css("display", "block");
            $("#chNo").css("display", "block");
        } else {
            $("#chDate").css("display", "none");
            $("#chNo").css("display", "none");
        }
    });

    $(document).on('keyup change',"#tran_amount",function(){calcAmt();});
	$(document).on('keyup change',"#adj_amount",function(){calcAmt();});
});

function calcAmt()
{
	var tran_amount=$("#tran_amount").val();var adj_amount=$("#adj_amount").val();
	var total_amount = parseFloat(tran_amount) + parseFloat(adj_amount);
	$("#total_amount").val(total_amount);
}