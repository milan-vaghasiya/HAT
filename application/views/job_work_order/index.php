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
                                    <li class="nav-item"> <button onclick="statusTab('jobOrderTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('jobOrderTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('jobOrderTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Short Close</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <h4 class="card-title">Job Work Order</h4>
                            </div>
                            <div class="col-md-4">
							    <?php if($shortYear == "22-23"): ?>
                                    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addOrder" data-form_title="Add Job Work Order"><i class="fa fa-plus"></i> Add Order</button>
                                <?php endif; ?>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='jobOrderTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="viewJWOModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <!-- <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">JOB WORK ORDER</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div> -->
            <form id="party_so" method="post" action="">
                <input type="hidden" id="jobworkid" value="">
                <div class="modal-body"  >
                    <div class="col-md-12" id="jwoView"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="saveApprove();"><i class="fa fa-check"></i> Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/job-work-order.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
	$(document).on('change','#rate_per',function(e){
		$(".error").html("");
		var rate_per = $(this).val();
		var rate = $('#rate').val();
       
        if(rate_per == 1){

            if($('#qty').val() != 0 && $('#qty').val() != "")
            {
                var perpcs = rate * $('#qty').val();
                $("#amount").val(perpcs); 
            } else { $(".qty_pcs").html("Qty Pcs is required."); $("#amount").val(0); } 

        } else if(rate_per == 2) {

            if($('#qty_kg').val() != 0 && $('#qty_kg').val() != "")
            {
                var perkg = rate * $('#qty_kg').val();
                $("#amount").val(perkg);
            } else { $(".qty_kg").html("Qty kg is required."); $("#amount").val(0); } 

        } else {
            $("#amount").val(0); 
        }
    });

    $(document).on('change',"#product_id",function(){
        var IsValid = 1;
        var product_id = $(this).val();
        var vendor_id = $('#vendor_id').val();
        if(vendor_id == ""){
            $("#processSelect").html("");
            $("#process_id").val("");
            reInitMultiSelect(); 
            
            $(".vendor_id").html("Vendor is required");
            IsValid = 0;
        }
        
        if(IsValid){
            $.ajax({
                url: base_url + controller + "/getVendorProcessList",
                type: "POST",
                data:{product_id:product_id, vendor_id:vendor_id},
                dataType:"json",
                success:function(data){
                    $("#processSelect").html(data.options);
                    $("#process_id").val("");
                    reInitMultiSelect();
                }
            });
        }
    });
    
    $(document).on('click',".approveJobWorkOrderView",function(){
        $('#viewJWOModal').modal();
        var id = $(this).data('id');
        $('#jobworkid').val(id);
        $.ajax({
            url:base_url + controller + '/jobWorkOrderView',
            type:'post',
            data:{id:id},
            dataType:'json',
            global:false,
            success:function(data)
            {
                $('#jwoView').html(data.pdfData)
            }
        });
    });
    
    $(document).on('change','#item_type',function(){
		var item_type = $(this).val();
		if(item_type){
			$.ajax({
				url:base_url + controller + '/getItemListForSelect',
				type:'post',
				data:{item_type:item_type},
				dataType:'json',
				success:function(data){
					$("#product_id").html("");
					$("#product_id").html(data.options);
					$("#product_id").comboSelect();
				}
			});
		} else {
			$("#product_id").html("<option value=''>Select Item Name</option>");
			$("#product_id").comboSelect();
		}
    });
});

function saveApprove() {
    var id = $("#jobworkid").val();
    $.ajax({
        url: base_url + controller + '/approveJobWorkOrder',
        data: {
            id: id,
            val: '1',
            msg: 'Approve'
        },
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (data.status == 0) {
                toastr.error(data.message, 'Sorry...!', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
            } else {
                initTable();$(".modal").modal('hide');
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                
            }
        }
    });
}
</script>