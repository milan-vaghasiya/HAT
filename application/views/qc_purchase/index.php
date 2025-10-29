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
                                    <li class="nav-item"> <button onclick="statusTab('qcPurchaseTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info active pendingBtn" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('qcPurchaseTable',1);" class="nav-link btn waves-effect waves-light btn-outline-success" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">QC Purchase Order</h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/addQCPurchase")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Order</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='qcPurchaseTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="qcOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Receive Goods [<b>P.O. No.: <span id="purchase_no"></span></b>]</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="receivePurchase">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="grn_date">Receive Date</label>
                            <input type="date" name="grn_date" id="grn_date" class="form-control" value="<?=date('Y-m-d')?>" />
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="in_challan_no">Challan Number</label>
                            <input type="text" name="in_challan_no" id="in_challan_no" class="form-control" value="" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Product</th>
                                        <th class="text-center">Order Qty</th>
                                        <th class="text-center">Pending Qty</th>
                                        <th class="text-center">Receive Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="qcOrderData">
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success" onclick="saveReceiveGoods('receivePurchase');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    
    $(document).on('click','.purchaseReceive',function(){
        var po_id = $(this).data('po_id');

		$.ajax({
			url : base_url + controller + '/getPurchaseOrderForReceive',
			type: 'post',
			data:{po_id:po_id},
			dataType:'json',
			success:function(data){
				$("#qcOrderModal").modal();
				$("#in_challan_no").val("");
				$("#purchase_no").html(data.po_no);
				$("#qcOrderData").html("");
				$("#qcOrderData").html(data.htmlData);
			}
		});
    });
});

function saveReceiveGoods(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/purchaseRecive',
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
		    $("#in_challan_no").val("");$("#qcOrderData").html("");$(".modal").modal('hide');
		    $(".pendingBtn").trigger('click');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}
</script>