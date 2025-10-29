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
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('paymentVoucherTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Regular</button> 
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('paymentVoucherTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false"> Advance </button> 
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="card-title">Payment Voucher</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew " data-button="both" data-modal_id="modal-lg" data-function="addPaymentVoucher" data-form_title="Add Payment "><i class="fa fa-plus"></i> Add Voucher</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='paymentVoucherTable' class="table table-bordered ssTable" data-url='/getDtRows'></table>
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
	

	$(document).on("change","#entry_type",function(){       
        var entry_type = $("#entry_type").val();
        $(".entry_type").html("");
        if(entry_type != ''){
		    $.ajax({
				url : base_url + controller + '/getTransNo',
				type: 'post',
				data:{entry_type:entry_type},
				dataType:'json',
				success:function(data){                    
                    $("#trans_prefix").val(data.trans.trans_prefix);
                    $("#trans_no").val(data.trans.nextTransNo);
				}
			}); 
        }else{
            $(".entry_type").html("Entry Type is required.");
        }
    });

    $(document).on('change',"#opp_acc_id",function(){
        $("#ref_id").val("");
        $("#ref_data").html('');reInitMultiSelect();
        $(".entry_type").html("");
        $(".opp_acc_id").html("");
        var entry_type = $("#entry_type").val();
        var party_id = $(this).val();
        if(entry_type != '' && party_id != ''){
		    $.ajax({
				url : base_url + controller + '/getReference',
				type: 'post',
				data:{entry_type:entry_type,party_id:party_id},
				dataType:'json',
				success:function(data){  
                    $("#ref_id").val("");                  
                    $("#ref_data").html(data.referenceData);
                    reInitMultiSelect();
                    $("#ref_amt").html(0.00);
                    $("#paid_amount").val(0);
				}
			}); 
        }else{
            if(entry_type == ""){
                $(".entry_type").html("Entry Type is required.");
            }
            if(party_id == ""){
                $(".opp_acc_id").html("Party Name is required.");
            }
        }
        
    });

    $(document).on('click change','#ref_data',function(){
        $("#paid_amount").val(0);
        var due_amount = 0.00;
        $('#ref_data :selected').each(function(){
            due_amount += parseFloat($(this).data('due_amount'));
        });

        $("#ref_amt").html(due_amount.toFixed(2));
        $("#paid_amount").val(due_amount.toFixed(2));
    });
});
</script>