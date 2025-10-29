$(document).ready(function(){
    $(document).on('click','.createInvoice',function(){
        var party_id = $(this).data('id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyChallans',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#challanModal").modal();
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#challanData").html("");
				$("#challanData").html(data.htmlData);
			}
		});
    });
    
    $(document).on('click','.createItemList',function(){		
        var id = $(this).data('id');
        var party_name = $(this).data('party_name');

        $.ajax({
            url : base_url + controller + '/getItemList',
            type: 'post',
            data:{id:id},
            dataType:'json',
            success:function(data){
                $("#itemModal").modal();
                $("#partyNames").html(party_name);
                $("#party_name").val(party_name);
                $("#party_id").val(party_id);
                $("#itemData").html("");
                $("#itemData").html(data.htmlData);
            }
        });
    });
});