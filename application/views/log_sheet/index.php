<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('logsheetTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Inhouse</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('logsheetTable',3);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Vendor</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Production Log Sheet</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addLog" data-form_title="Add Production Log"><i class="fa fa-plus"></i> Add Log</button>
                            </div>    
                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">                            
                            <table id='logsheetTable' class="table table-bordered ssTable ssTable-cf" data-ninput="[0,1]"  data-srowposition = "1" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on("change","#job_card_id", function(){
        var job_card_id = $(this).val();
        var part_code = $('#job_card_id :selected').data('part_code');
        var part_id = $('#job_card_id :selected').data('part_id');
        if(job_card_id){
            $.ajax({
                url:base_url + controller + '/getProcess',
				type: 'post',
				data:{job_card_id:job_card_id},
				dataType:'json',
				success:function(data){
					$("#process_id").html("");
					$("#process_id").html(data.options);
                    $("#process_id").comboSelect();
                    $("#log_date").attr('min',data.job_date);
                    $("#part_code").val(part_code);
                    $("#part_id").val(part_id);
				}
			});
        } else { $("#process_id").html("<option value=''>Select Process</option>"); $("#process_id").comboSelect(); $("#part_code").val(""); $("#part_id").val(""); }
    });

    $(document).on("change","#process_id", function(){
        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        if(process_id){
            /*$.ajax({
                url:base_url + controller + '/getMachine',
				type: 'post',
				data:{process_id:process_id},
				dataType:'json',
				success:function(data){
					$("#machine_id").html("");
					$("#machine_id").html(data.options);
                    $("#machine_id").comboSelect();
				}
			});
			
		    $.ajax({
                url: base_url + controller + '/getMasterCycleTime',
                type: 'post',
                data: {
                    process_id: process_id,
                    product_id: part_id
                },
                dataType: 'json',
                success: function(data) {

                    $("#m_ct").val("");
                    $("#m_ct").val(data.cycle_time);
                }
            });*/

            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + controller + '/getRejectionBelongs',
                type: 'post',
                data: {process_id: process_id, part_id: part_id, job_card_id: job_card_id, entry_type: 'LOG' },
                dataType: 'json',
                success: function(data) {
                    $("#rejection_stage").html("");
                    $("#rejection_stage").html(data.rejOption);
                    $("#rejection_stage").comboSelect();

                    $("#rework_stage").html("");
                    $("#rework_stage").html(data.rewOption);
                    $("#rework_stage").comboSelect();
                    
					$("#machine_id").html("");
					$("#machine_id").html(data.mcOptions);
                    $("#machine_id").comboSelect();
                    
                    $("#m_ct").val("");$("#m_ct").val(data.cycle_time);
                    
                    if ($('.mct').length === 0) {
                        $(".modal-header .modal-title").css('width','100%');
                        $(".modal-header .modal-title").append("<span class='mct text-primary float-right'>Master Cycle Time : " + data.cycle_time + " Seconds</span>");
                    }
                    else {
                        $(".mct").html("Master Cycle Time : " + data.cycle_time + " Seconds");
                    }
                }
            });
        } else { 
            $("#machine_id").html("<option value=''>Select Machine</option>"); $("#machine_id").comboSelect(); 
            $("#rejection_stage").html("<option value=''>Select Rej. From</option>"); $("#rejection_stage").comboSelect();
            $("#rework_stage").html("<option value=''>Select Rew. From</option>"); $("#rework_stage").comboSelect();
        }
    });

    $(document).on("keyup",".partCount", function(){
       var startPartCount=parseFloat($("#start_part_count").val());
       var prdQty=parseFloat($("#production_qty").val());
       $("#end_part_count").val(startPartCount+prdQty);
    });

    $(document).on("keyup",".qtyCal", function(){
        var rejSum = 0;
		$( ".rej_sum" ).each( function(){rejSum += parseFloat( $( this ).val() ) || 0;});
        var rwSum = 0;
		$( ".rw_sum" ).each( function(){rwSum += parseFloat( $( this ).val() ) || 0;});
        
		var okQty=parseFloat($("#production_qty").val())-rejSum-rwSum;
      
		$("#ok_qty").val(okQty);
    });

    $(document).on('click',"#addIdleRow",function(){
        var idle_time = $("#idle_time").val();
        var idle_reason_id = $("#idle_reason").val();
        var idle_reason_code = $("#idle_reason :selected").data('code');
        var idle_reason = $("#idle_reason :selected").data('reason');

        var valid = 1;

        $(".idle_time").html("");
        if(parseFloat(idle_time) <= 0){
            $(".idle_time").html("Idle Time is required.");valid=0;
        }

        $(".idle_reason").html("");
        if(idle_reason_id == ""){
            $(".idle_reason").html("Idle Reason is required.");valid=0;
        }

        if(valid == 1){
            var postData = {idle_time:idle_time,idle_reason_id:idle_reason_id,idle_reason_code:idle_reason_code,idle_reason:idle_reason};
            AddRowIdle(postData);
            $("#idle_time").val("0");
            $("#idle_reason").val("");
            $("#idle_reason").comboSelect();
            $("#idle_time").focus();
        }
    });
    //Created By Karmi@04/03/2022
    $(document).on('click',"#addReworkRow",function(){
        var rw_qty = $("#rw_qty").val();
        var rw_reason = $("#rw_reason :selected").val();
        var rw_from = $("#rw_from :selected").val();
        var rw_reason_code = $("#rw_reason :selected").data('code');
        var rework_reason = $("#rw_reason :selected").data('reason');
        var rw_party_name = $("#rw_from :selected").data('party_name');
        var rw_remark = $("#rw_remark").val();
        var rw_stage = $("#rework_stage").val();
        var rw_stage_name = $("#rework_stage :selected").data('process_name');
       

        var valid = 1;

        $(".rw_qty").html("");
        if(parseFloat(rw_qty) <= 0){
            $(".rw_qty").html("Rework Qty is required.");valid=0;
        }

        $(".rw_reason").html("");
        if(rw_reason == ""){
            $(".rw_reason").html("Rework Reason is required.");valid=0;
        }

        $(".rw_from").html("");
        if(rw_from == ""){
            $(".rw_from").html("Rework From is required.");valid=0;
        }

        $(".rework_stage").html("");
        if(rw_stage == ""){
            $(".rework_stage").html("Rework Belongs to is required.");valid=0;
        }

        if(valid == 1){
            var postData = {rw_qty:rw_qty,rw_reason:rw_reason,rw_from:rw_from,rw_reason_code:rw_reason_code,rework_reason:rework_reason,rw_remark:rw_remark,rw_party_name:rw_party_name,rw_stage: rw_stage,rw_stage_name:rw_stage_name};
            AddRowRework(postData);
            $("#rw_qty").val("0");
            $("#rw_reason").comboSelect();
            $("#rw_from").comboSelect();
            $("#rework_stage").comboSelect();
            $("#rw_remark").val("");
            $("#rw_qty").focus();

        }
    });
    //Created By Karmi@04/03/2022
    $(document).on('click',"#addRejectionRow",function(){
        var rej_qty = $("#rej_qty").val();
        var rej_reason = $("#rej_reason :selected").val();
        var rej_from = $("#rej_from :selected").val();
        var rej_reason_code = $("#rej_reason :selected").data('code');
        var rejection_reason = $("#rej_reason :selected").data('reason');
        var rej_party_name = $("#rej_from :selected").data('party_name');
        var rej_remark = $("#rej_remark").val();
        var rej_stage = $("#rejection_stage").val();
        var rej_stage_name = $("#rejection_stage :selected").data('process_name');

        var valid = 1;

        $(".rej_qty").html("");
        if(parseFloat(rej_qty) <= 0){
            $(".rej_qty").html("Rejection Qty is required.");valid=0;
        }

        $(".rej_reason").html("");
        if(rej_reason == ""){
            $(".rej_reason").html("Rejection Reason is required.");valid=0;
        }

        $(".rej_from").html("");
        if(rej_from == ""){
            $(".rej_from").html("Rejection From is required.");valid=0;
        }

        $(".rejection_stage").html("");
        if(rej_stage == ""){
            $(".rejection_stage").html("Rejection Belongs is required.");valid=0;
        }

        if(valid == 1){
            var postData = {rej_qty:rej_qty,rej_reason:rej_reason,rej_from:rej_from,rej_reason_code:rej_reason_code,rejection_reason:rejection_reason,rej_remark:rej_remark,rej_party_name:rej_party_name,rej_stage: rej_stage,rej_stage_name:rej_stage_name};
            AddRowRejection(postData);
            $("#rej_qty").val("0");
            $("#rej_reason").comboSelect();
            $("#rej_from").comboSelect();
            $("#rejection_stage").comboSelect();
            $("#rej_remark").val("");
            $("#rej_qty").focus();

        }
    });
    
    //Created By Mansee @ 11-03-2022
    $(document).on("change", "#shift_id", function() {
        var production_time = $('#shift_id :selected').data('production_time');
        $("#production_time").val(production_time);
    });
    
    $(document).on("change", "#rejection_stage", function() {
        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + controller + '/getJobWorkOrder',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function(data) {
                    $("#rej_from").html("");
                    $("#rej_from").html(data.rejOption);
                    $("#rej_from").comboSelect();

                  
                }
            });
        } else {
            $("#machine_id").html("<option value=''>Select Machine</option>");
            $("#machine_id").comboSelect();
            $("#rej_from").html("<option value=''>Select Rej. From</option>");
            $("#rej_from").comboSelect();
            $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").comboSelect();
        }
    });

    $(document).on("change", "#rework_stage", function() {
        var process_id = $(this).val();
        var part_id = $("#part_id").val();
        if (process_id) {
            var job_card_id = $("#job_card_id").val();
            $.ajax({
                url: base_url + controller + '/getJobWorkOrder',
                type: 'post',
                data: {
                    process_id: process_id,
                    part_id: part_id,
                    job_card_id: job_card_id
                },
                dataType: 'json',
                success: function(data) {
                  
                    $("#rw_from").html("");
                    $("#rw_from").html(data.rewOption);
                    $("#rw_from").comboSelect();
                }
            });
        } else {
           
            $("#rw_from").html("<option value=''>Select Rew. From</option>");
            $("#rw_from").comboSelect();
        }
    });
        
});
// For Idle Data
function AddRowIdle(data){
    $('table#idleReasons tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tblName = "idleReasons";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
    row = tBody.insertRow(-1);

    var index =  $('#'+tblName+' tbody tr:last').index();
    var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

    var idle_time_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_time]",value:data.idle_time});
    cell = $(row.insertCell(-1));
	cell.html(data.idle_time);
	cell.append(idle_time_input);
    cell.attr("style","width:20%;");

    var idle_reason_id_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason_id]",value:data.idle_reason_id});
    var idle_reason_code_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason_code]",value:data.idle_reason_code});
    var idle_reason_input = $("<input/>",{type:"hidden",name:"idle_reason["+index+"][idle_reason]",value:data.idle_reason});
    cell = $(row.insertCell(-1));
	cell.html("["+data.idle_reason_code+"] - "+data.idle_reason);
	cell.append(idle_reason_id_input);
	cell.append(idle_reason_code_input);
	cell.append(idle_reason_input);

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveIdle(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function RemoveIdle(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#idleReasons")[0];
	table.deleteRow(row[0].rowIndex);
	$('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#idleReasons tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#idleReasonData").html('<tr id="noData"><td colspan="4" class="text-center">No data available in table</td></tr>');
	}	
};

// For Rework Data
function AddRowRework(data){
    $('table#reworkReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tblName = "reworkReason";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
    row = tBody.insertRow(-1);

    var index =  $('#'+tblName+' tbody tr:last').index();
    var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

    var rework_qty_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rw_qty]",value:data.rw_qty,class:"rw_sum"});
    cell = $(row.insertCell(-1));
	cell.html(data.rw_qty);
	cell.append(rework_qty_input);
    cell.attr("style","width:20%;");

    var rw_reason_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rw_reason]",value:data.rw_reason});
    cell = $(row.insertCell(-1));
    cell.html(data.rework_reason);
	cell.append(rw_reason_input);
    cell.attr("style","width:20%;");

    var rw_stage_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_stage]",
        value: data.rw_stage
    });
    var rw_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rework_reason[" + index + "][rw_stage_name]",
        value: data.rw_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rw_stage_name);
    cell.append(rw_stage_input);
    cell.append(rw_stage_name_input);
    cell.attr("style", "width:20%;");
    
    var rw_from_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rw_from]",value:data.rw_from});
    var rw_party_name_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rw_party_name]",value:data.rw_party_name});
    cell = $(row.insertCell(-1));
	cell.html(data.rw_party_name);
	cell.append(rw_from_input);
	cell.append(rw_party_name_input);
    cell.attr("style","width:20%;");

    var rw_remark_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rw_remark]",value:data.rw_remark});
    var rework_reason_input = $("<input/>",{type:"hidden",name:"rework_reason["+index+"][rework_reason]",value:data.rework_reason});
    cell = $(row.insertCell(-1));
	cell.html(data.rw_remark);
	cell.append(rw_remark_input);
	cell.append(rework_reason_input);
    cell.attr("style","width:20%;");

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveRework(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class","text-center");
	cell.attr("style","width:10%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRework(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#reworkReason")[0];
	table.deleteRow(row[0].rowIndex);
	$('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#idleReasons tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
	}	
    $(".qtyCal").trigger('keyup');
};

// For Rejection Data
function AddRowRejection(data){
    //console.log(data.rej_qty);
    $('table#rejectionReason tr#noData').remove();

    //Get the reference of the Table's TBODY element.
	var tblName = "rejectionReason";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
    row = tBody.insertRow(-1);

    var index =  $('#'+tblName+' tbody tr:last').index();
    var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
    var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");

    var rejection_qty_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rej_qty]",value:data.rej_qty,class:"rej_sum"});
    cell = $(row.insertCell(-1));
	cell.html(data.rej_qty);
	cell.append(rejection_qty_input);
    cell.attr("style","width:20%;");

    var rej_reason_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rej_reason]",value:data.rej_reason});
    cell = $(row.insertCell(-1));
    cell.html(data.rejection_reason);
	cell.append(rej_reason_input);
    cell.attr("style","width:20%;");

    var rej_stage_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage]",
        value: data.rej_stage
    });
    var rej_stage_name_input = $("<input/>", {
        type: "hidden",
        name: "rejection_reason[" + index + "][rej_stage_name]",
        value: data.rej_stage_name
    });
    cell = $(row.insertCell(-1));
    cell.html(data.rej_stage_name);
    cell.append(rej_stage_input);
    cell.append(rej_stage_name_input);
    cell.attr("style", "width:20%;");
        
    var rej_from_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rej_from]",value:data.rej_from});
    var rej_party_name_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rej_party_name]",value:data.rej_party_name});
    cell = $(row.insertCell(-1));
	cell.html(data.rej_party_name);
	cell.append(rej_from_input);
	cell.append(rej_party_name_input);
    cell.attr("style","width:20%;");

    var rej_remark_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rej_remark]",value:data.rej_remark});
    var rejection_reason_input = $("<input/>",{type:"hidden",name:"rejection_reason["+index+"][rejection_reason]",value:data.rejection_reason});
	cell = $(row.insertCell(-1));
	cell.html(data.rej_remark);
	cell.append(rej_remark_input);
	cell.append(rejection_reason_input);
    cell.attr("style","width:20%;");

    //Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "RemoveRejection(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
    cell.append(btnRemove);
    cell.attr("class","text-center");
	cell.attr("style","width:10%;");

    $(".qtyCal").trigger('keyup');
}

function RemoveRejection(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#rejectionReason")[0];
	table.deleteRow(row[0].rowIndex);
	$('#idleReasons tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#idleReasons tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#idleReasonData").html('<tr id="noData"><td colspan="6" class="text-center">No data available in table</td></tr>');
	}	
    $(".qtyCal").trigger('keyup');
};
</script>