$(document).ready(function(){
    $(document).on("change","#leave_type_id",function(){
        var leave_type = $("#leave_type_id :selected").data('type');
        $('#type_leave').val(leave_type);
		if(leave_type == 'SL'){
		    $('.leaveType').hide();
		    $('.shortLeave').show();
		    $(".shortLeave").removeAttr("style");
		    $(".totaldays").html('Total Mins <strong class="text-danger">*</strong>');
		}else{
		    $('.leaveType').show();
		    $('.shortLeave').hide();
		    $(".leaveType").removeAttr("style");
		    $(".totaldays").html('Total Days <strong class="text-danger">*</strong>');
		}
    });
    
    $(document).on("change keyup","#emp_id",function(){
        var pla_id = $('#emp_id :selected').data('pla_id');
        var approval_type = 1;
        if(pla_id !==""){approval_type = 2;}
        console.log(approval_type);
        $('#approval_type').val(approval_type);
    });
    $(document).on("change keyup",".leaveQuota",function(){
        var leave_type = $('#type_leave').val();
        var start_date = $('#start_date').val(); 
        if(start_date!='' && leave_type == 'SL'){
            var emp_id = $('#emp_id :selected').val();
    	    $.ajax({
    			url:base_url + controller + '/getLeaveQuota',
    			type:'post',
    			data:{emp_id:emp_id,start_date:start_date},
    			dataType:'json',
    			success:function(data){
    			    $('.max-leave').html('Max Leave: '+data.max_leave);
    			    $('.used-leave').html('Used Leave: '+data.used_leave);
    			    var remain = parseFloat(data.max_leave) - parseFloat(data.used_leave);
    			    $('.remain-leave').html('Remain Leave: '+remain);
    			}
    		});	
        }
    });
    
	$(document).on("change",".countTotalHours",function(){
		var diff = calcTimeDiffInHrs($("#start_time").val(),$("#end_time").val(),"M");
		$("#total_days").val(diff);
	});
	
	$(document).on("change",".countTotalDays",function(){
	    var startDate  = $('#start_date').val();
		var endDate    = $('#end_date').val();
	    var start_section = $('#start_section').val();
		var end_section = $('#end_section').val();
		
		//$('#end_date').attr('min',startDate);
		
		if(start_section == 1){endDate = startDate; $('#end_date').val(startDate); }
		
		if(startDate == endDate){
		    $('#end_section').html("");
		    $('#end_section').html('<option value="0">NA</option>');
		} else {
		    if(!($(this).hasClass('endSection')))
		    {
		        $('#end_section').html("");
		        $('#end_section').html('<option value="">Select End Section</option><option value="1">Half Day</option><option value="3">Full Day</option>');
		    }
		}
		
		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		
		var totalDay = parseFloat(diffInDays) + 1;
		if(start_section == 1 || start_section == 2){totalDay =  totalDay -  0.5;}
		if(end_section == 1 || end_section == 2){totalDay = totalDay -  0.5;}
		
		var type_leave = $('#type_leave').val();
		if(type_leave != 'SL'){
		    $("#total_days").val(totalDay);
		}
	});
	
});